<?php
/*
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of DbUp. For the full copyright and license information
 * please view the LICENSE file that was distributed with this source code.
 */
namespace zpt\dbup\script;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * This class tests the {@link AlterTableStatement} class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ChangeColumnTypeStatementTest extends TestCase
{

	public function testChangePostgresColumn() {
		$stmt = new ChangeColumnTypeStatement('');
		$stmt->setTable('t');
		$stmt->setColumn('c');
		$stmt->setType('varchar(128)');

		$pgSql = $stmt->getSql('pgsql');
		$expected = 'ALTER TABLE t ALTER COLUMN c TYPE varchar(128);';
		$this->assertEquals($expected, $pgSql);
	}

	public function testChangeMysqlColumn() {
		$stmt = new ChangeColumnTypeStatement('');
		$stmt->setTable('t');
		$stmt->setColumn('c');
		$stmt->setType('varchar(128)');

		$mySql = $stmt->getSql('mysql');
		$expected = 'ALTER TABLE t MODIFY c varchar(128);';
		$this->assertEquals($expected, $mySql);
	}

}
