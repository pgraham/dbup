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
 * {@link SqlStatement} implementation for ALTER TABLE statements.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class AlterTableStatement extends BaseSqlStatement
	implements SqlStatement
{

	private $alter;
	protected $table;

	/**
	 * Getter for the portion of the statement that comes after the leading `ALTER
	 * TABLE <table-name> ...`.
	 *
	 * @return string
	 */
	public function getAlter() {
		return $this->alter;
	}

	public function setAlter($alter) {
		$this->alter = $alter;
	}

	public function getTable() {
		return $this->table;
	}

	public function setTable($table) {
		$this->table = $table;
	}

	public function getSql($dbDriver) {
		return "ALTER TABLE $this->table $this->alter";
	}

}
