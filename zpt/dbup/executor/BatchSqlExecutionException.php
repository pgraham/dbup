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

use \zpt\db\exception\DatabaseException;
use \Exception;

/**
 * Exception class wrapping a {@link DatabaseException} thrown while executing 
 * a batch SQL script.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class BatchSqlExecutionException extends Exception {

	private $stmt;

	public function __construct(
		BatchSqlStatment $stmt,
		DatabaseException $cause
	) {

		$this->stmt = $stmt;

		$scriptPath = $stmt->getPath();
		$lineNum = $stmt->getLineNum();
		$msg = "$scriptPath:$lineNum: {$cause->getMessage()}";

		parent::__construct($msg, $cause->getCode(), $cause);
	}

	public function getScriptPath() {
		return $this->stmt->getPath();
	}

	public function getLineNum() {
		return $this->stmt->getLineNum();
	}

	public function getSql() {
		return $this->stmt->getSql();
	}
}
