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
 * {@link SqlStatment} for CREATE TABLE statements. These statements will
 * translate serial field types into the appropriate syntax for the database
 * against which the statement is being executed.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class CreateTableStatement extends BaseSqlStatement implements SqlStatement
{

	public function __construct($stmt) {
		parent::__construct($stmt);
	}

	public function getSql($dbDriver) {
		switch ($dbDriver) {
			case 'pgsql':
			$sql = preg_replace(
				'/integer\s+(NOT NULL\s+)?AUTO_INCREMENT/i',
				'SERIAL $1',
				$this->stmt
			);
			break;

			default:
			$sql = $this->stmt;
		}
		return $sql;
	}

}
