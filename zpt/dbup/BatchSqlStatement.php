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
 * This class encapsulates a single SQL statement that is part of a larger batch 
 * SQL script.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class BatchSqlStatement {

	private $sql;
	private $lineNum;

	public function __construct($sql, $lineNum) {
		$this->sql = $sql;
		$this->lineNum = $lineNum;
	}

	public function getSql() {
		return $this->sql;
	}

	public function getLineNum() {
		return $this->lineNum;
	}
}
