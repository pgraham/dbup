<?php
/*
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of DbUp. For the full copyright and license information
 * please view the LICENSE file that was distributed with this source code.
 */
namespace zpt\dbup\script;

/**
 * This class encapsulates an SQL statement to change the type of a table field.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ChangeColumnTypeStatement extends AlterTableStatement
{

	private $column;
	private $type;

	public function getColumn() {
		return $this->column;
	}

	public function setColumn($column) {
		$this->column = $column;
	}

	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function getSql($dbDriver) {
		$sql = "ALTER TABLE $this->table ";

		switch ($dbDriver) {
			case 'pgsql':
			$sql .= "ALTER COLUMN $this->column TYPE $this->type;";
			break;

			case 'mysql':
			$sql .= "MODIFY $this->column $this->type;";
			break;

			case 'sqlite':
			throw new RuntimeException(
				"Change a column's type is not support in SQLite"
			);
		}

		return $sql;
	}

}
