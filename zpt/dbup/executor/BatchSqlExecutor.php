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
use \zpt\dbup\script\SqlScript;
use \zpt\dbup\script\SqlScriptStatementFactory;

/**
 * This class implements the AlterExecutor interface by breaking the script into
 * statements and executing the individually using PDO::exec(...);
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class BatchSqlExecutor implements AlterExecutor, LoggerAwareInterface {
	use LoggerAwareTrait;

	private $sqlStatementParser;
	private $sqlScriptStatementFactory;

	public function __construct() {
		$this->sqlStatementParser = new SqlStatementParser();
		$this->sqlScriptStatementFactory = new SqlScriptStatementFactory();
	}

	/**
	 * Execute the SQL statements found in the script against the provided
	 * database connection.
	 */
	public function executeAlter(DatabaseConnection $db, $path) {
		if (!file_exists($path)) {
			$msg = "Unable to execute script, it does not exist: $path";
			$this->logger->warning($msg);
			return;
		}

		$stmts = $this->sqlStatementParser->parse(file_get_contents($path));
		$scriptStmts = [];
		foreach ($stmts as $stmt) {
			$scriptStmts[] = $this->sqlScriptStatementFactory->create($stmt);
		}

		$sqlScript = new SqlScript($scriptStmts);
		$sqlScript->execute($db);
	}
}
