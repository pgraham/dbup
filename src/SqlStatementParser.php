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
namespace zpt\dbup;

/**
 * Parses the contents of an SQL script into individual statements.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SqlStatementParser
{

	public function parse($sql) {
		$stmts = array();

		$curStmt = array();
		$stmtLineNum = null;

		$lines = explode("\n", $sql);
		foreach ($lines as $idx => $line) {
			if (trim($line) === '') {
				continue;
			}
			if (preg_match('/^\s*--/', $line)) {
				continue;
			}

			$lineNum = $idx + 1;
			if ($stmtLineNum === null) {
				$stmtLineNum = $lineNum;
			}

			// Remove any trailing comments from the line
			$line = preg_replace('/--.*$/', '', $line);
			$curStmt[] = $line;

			if (preg_match('/;$/', $line)) {
				$completeStmt = implode(' ', $curStmt);
				$stmts[] = new SqlStatement($completeStmt, $path, $stmtLine);
				$curStmt = array();
				$stmtLineNull = null;
			}
		}

		return $stmts;
	}

}
