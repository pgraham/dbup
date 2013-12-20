<?php
/**
 * =============================================================================
 * Copyright (c) 2012, Philip Graham
 * All rights reserved.
 *
 * This file is part of dbUp and is licensed by the Copyright holder under the
 * 3-clause BSD License.  The full text of the license can be found in the
 * LICENSE.txt file included in the root directory of this distribution or at
 * the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace zpt\dbup\executor;

use \Psr\Log\LoggerAwareInterface;
use \Psr\Log\LoggerAwareTrait;
use \zpt\db\exception\DatabaseException;
use \zpt\db\DatabaseConnection;

/**
 * This class implements the AlterExecutor interface by breaking the script into
 * statements and executing the individually using PDO::exec(...);
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class BatchSqlExecutor implements AlterExecutor, LoggerAwareInterface {
	use LoggerAwareTrait;

	/**
	 * Execute the SQL statements found in the script against the provided
	 * database connection.
	 */
	public function executeAlter(DatabaseConnection $db, $path) {
		if (!file_exists($path)) {
			// TODO If available, log a warning using a PSR-3 logger instance
			return;
		}

		$stmts = $this->parseStmts($path);
		foreach ($stmts as $stmt) {
			$this->executeStmt($stmt);
		}
	}

	private function executeStmt($stmt) {
		$stmtSql = $stmt->getSql();
		try {
			$db->exec($stmtSql);
		} catch (DatabaseException $e) {
			throw new BatchSqlExecutionException($stmt, $e);
		}
	}

	private function parseStmts($path) {
		$sql = file_get_contents($path);
		$stmts = array();

		$lines = explode("\n", $sql);
		$curStmt = array();
		$stmtLine = null;
		foreach ($lines as $idx => $line) {
			if (trim($line) === '') {
				continue;
			}
			if (preg_match('/^\s*--/', $line)) {
				continue;
			}

			$lineNum = $idx + 1;

			// Remove any trailing comments from the line
			$line = preg_replace('/--.*$/', '', $line);

			$curStmt[] = $line;
			if ($stmtLine === null) {
				$stmtLine = $lineNum;
			}
			if (preg_match('/;$/', $line)) {
				$completeStmt = implode(' ', $curStmt);
				$stmts[] = new BatchSqlStatement($completeStmt, $path, $stmtLine);
				$curStmt = array();
				$stmtLine = null;
			}
		}

		return $stmts;
	}
}
