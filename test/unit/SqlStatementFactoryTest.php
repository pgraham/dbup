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
namespace zpt\dbup\test\unit;

require_once __DIR__ . '/../setup.php';

use zpt\dbup\script\SqlStatementFactory;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * This class tests the SqlStatementFactory class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SqlStatementFactoryTest extends TestCase
{

	public function testCreateSimple() {
		$factory = new SqlStatementFactory();

		$stmtSrc = "SELECT * FROM config;";
		$stmt = $factory->createFor($stmtSrc);

		$this->assertInstanceOf('zpt\dbup\script\SimpleSqlStatement', $stmt);
		$this->assertEquals($stmtSrc, $stmt->getSource());
		$this->assertEquals($stmtSrc, $stmt->getSql('sqlite'));
	}

	public function testCreateInsert() {
		$factory = new SqlStatementFactory();

		$stmtSrc = "insertId := INSERT INTO config (name) VALUES ('aName');";
		$stmt = $factory->createFor($stmtSrc);

		$this->assertInstanceOf('zpt\dbup\script\InsertStatement', $stmt);
		$this->assertEquals($stmtSrc, $stmt->getSource());

		$stmtSql = "INSERT INTO config (name) VALUES ('aName');";
		$this->assertEquals($stmtSql, $stmt->getSql('sqlite'));
	}

	public function testCreateTable() {
		$factory = new SqlStatementFactory();

		$stmtSrc = "CREATE TABLE t ( id INTEGER NOT NULL );";
		$stmt = $factory->createFor($stmtSrc);

		$this->assertInstanceOf('zpt\dbup\script\CreateTableStatement', $stmt);
		$this->assertEquals($stmtSrc, $stmt->getSource());
	}

	public function testAlterTable() {
		$factory = new SqlStatementFactory();

		$stmtSrc = 'ALTER TABLE t MODIFY COLUMN c varchar(128);';
		$stmt = $factory->createFor($stmtSrc);

		$this->assertInstanceOf('zpt\dbup\script\AlterTableStatement', $stmt);
	}

	public function testChangeColumnType() {
		$factory = new SqlStatementFactory();

		$stmts = [
			'ALTER TABLE t MODIFY c varchar(128);',
			'ALTER TABLE t MODIFY COLUMN c varchar(128);',
			'ALTER TABLE t ALTER COLUMN c TYPE varchar(128);',
			'ALTER TABLE t ALTER COLUMN c SET DATA TYPE varchar(128);'
		];

		foreach ($stmts as $stmtSrc) {
			$stmt = $factory->createFor($stmtSrc);
			$this->assertInstanceOf(
				'zpt\dbup\script\ChangeColumnTypeStatement',
				$stmt
			);

			$this->assertEquals('t', $stmt->getTable());
			$this->assertEquals('c', $stmt->getColumn());
			$this->assertEquals('varchar(128)', $stmt->getType());
		}
	}

}
