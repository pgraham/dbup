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
 * This class parses individual SQL statements in order to create
 * SqlScriptStatement instances, deferentiated on the type of statement.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SqlStatementFactory {

	private $alterTableStatmentFactory;

	public function __construct() {
		$this->alterTableStatementFactory = new AlterTableStatementFactory();
	}

	/**
	 * Create an SqlScriptStatement.
	 *
	 * @param string $stmt
	 */
	public function createFor($stmt) {
		$stmt = String($stmt);

		if ($stmt->startsWith('CREATE TABLE')) {
			return new CreateTableStatement($stmt);

		} else if ($stmt->startsWith('ALTER TABLE')) {
			return $this->alterTableStatementFactory->createFor($stmt);

		} else if (preg_match('/^\S+\s*:=\s*INSERT\s+/', $stmt)) {
			return new InsertStatement($stmt);

		}  else {
			return new SimpleSqlStatement($stmt);
		}
	}
}
