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

	private $lineNum;
	private $path;
	private $sql;

	/**
	 * Create a new statement object for the given sql.
	 *
	 * @param string $sql
	 *   The SQL statement encapsulated by this statement.
	 * @param string $path
	 *   The path to the alter file to which this statement belongs.
	 * @param int $lineNum
	 *   The line number of the alter file on which this statement appears.
	 */
	public function __construct($sql, $path, $lineNum) {
		$this->sql = $sql;
		$this->path = $path;
		$this->lineNum = $lineNum;
	}

	/**
	 * Get the line number of the alter file on which the encapsulated statement
	 * appears.
	 *
	 * @return string
	 */
	public function getLineNum() {
		return $this->lineNum;
	}

	/**
	 * Get the path to the alter file to which the encapsulated statement belongs.
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Get the encapsulated SQL statement.
	 *
	 * @return string
	 */
	public function getSql() {
		return $this->sql;
	}
}
