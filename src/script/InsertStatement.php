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

use zpt\db\DatabaseConnection;
use InvalidArgumentException;

/**
 * This class encapsulates an INSERT SQL statement which assigns a value to
 * a script variable.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class InsertStatement extends BaseSqlStatement implements SqlStatement
{

	private $varName;
	private $sql;
	private $tableName;

	public function __construct($stmt) {
		$sqlStart = strpos($stmt, 'INSERT');
		if ($sqlStart === 0 || $sqlStart === false) {
			throw new InvalidArgumentException(
				"Given statement does not assign an insert id to a variable: $stmt"
			);
		}
		$this->sql = substr($stmt, $sqlStart);

		if (preg_match('/INSERT\s+INTO\s+(\S+)/i', $this->sql, $matches)) {
			$this->tableName = $matches[1];
		} else {
			throw new InvalidArgumentException(
				"Unable to determine INSERT statement table name: $stmt"
			);
		}

		$assignOpPos = strpos($stmt, ':=');
		$this->varName = trim(substr($stmt, 0, $assignOpPos));

		parent::__construct($stmt);
	}

	public function execute(DatabaseConnection $db, SqlScriptState $state) {
		$result = parent::execute($db, $state);

		$driver = $db->getInfo()->getDriver();
		if ($driver === 'pgsql') {
			$sequenceName = "{$this->tableName}_id_seq";
			$insertId = $result->getInsertId($sequenceName);
			echo "\n***\n\nSEQUENCE NAME: $sequenceName, INSERT ID: $insertId\n\n***\n\n";
		} else {
			$insertId = $result->getInsertId();
		}
		$state->assignVariable($this->varName, $insertId);
		return $result;
	}

	public function getSql($dbDriver) {
		return $this->sql;
	}
}
