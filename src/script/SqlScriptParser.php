<?php
/**
 * =============================================================================
 * Copyright (c) 2014, Philip Graham
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
namespace zpt\dbup\script;

/**
 * Parse the lines of a given script into a state that is executable against
 * different database engines.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SqlScriptParser
{

	private $stmtFactory;

	public function __construct() {
		$this->stmtFactory = new SqlScriptStatementFactory();
	}

	public function parse($scriptSrc) {
		$lines = explode("\n", $scriptSrc);

		$stmts = [];
		$curStmt = [];

		foreach ($lines as $idx => $line) {
			if (trim($line) === '') {
				continue;
			}
			$curStmt[] = $line;

			if (preg_match('/;$/', $line)) {
				$stmts[] = $this->getStatement($curStmt);
				$curStmt = [];
			}
		}

		if (count($curStmt) > 0) {
			$stmts[] = $this->getStatement($curStmt);
		}

		return new SqlScript($stmts);
	}

	private function getStatement($curStmt) {
		$completeStmt = implode("\n", $curStmt);
	  return $this->stmtFactory->createFor($completeStmt);
	}

}
