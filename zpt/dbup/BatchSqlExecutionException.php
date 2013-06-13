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

use \Exception;
use \PDOException;

/**
 * Exception class wrapping a PDOException thrown while executing a batch SQL 
 * script.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class BatchSqlExecutionException extends Exception {

	private $scriptPath;
	private $lineNum;
	private $sql;

	public function __construct($scriptPath, $lineNum, $sql, PDOException $cause)
	{
		$this->scriptPath = $scriptPath;
		$this->lineNum = $lineNum;
		$this->sql = $sql;

		$msg = "$scriptPath:$lineNum: {$cause->getMessage()}";
		parent::__construct($msg, $cause->getCode(), $cause);
	}

	public function getScriptPath() {
		return $this->scriptPath;
	}

	public function getLineNum() {
		return $this->lineNum;
	}

	public function getSql() {
		return $this->sql;
	}
}
