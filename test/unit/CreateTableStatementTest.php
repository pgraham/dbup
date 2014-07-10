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
 * This class test the {@link CreateTableStatement} class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class CreateTableStatementTest extends TestCase
{

	public function testMysqlIntegerNotNullAutoIncrementToMysql() {
		$stmtSrc = 'CREATE TABLE t ( id integer NOT NULL AUTO_INCREMENT );';
		$stmt = new CreateTableStatement($stmtSrc);

		$mySql = $stmt->getSql('mysql');
		$this->assertEquals($stmtSrc, $mySql);
	}

	public function testMysqlIntegerNotNullAutoIncrementToPostgres() {
		$stmtSrc = 'CREATE TABLE t ( id integer NOT NULL AUTO_INCREMENT );';
		$stmt = new CreateTableStatement($stmtSrc);

		$pgSql = $stmt->getSql('pgsql');
		$pgSql = preg_replace('/\s+/', ' ', $pgSql);
		$this->assertEquals('CREATE TABLE t ( id SERIAL NOT NULL );', $pgSql);
	}

	public function testMysqlIntegerAutoIncrementToPostgres() {
		$stmtSrc = 'CREATE TABLE t ( id integer AUTO_INCREMENT );';
		$stmt = new CreateTableStatement($stmtSrc);

		$pgSql = $stmt->getSql('pgsql');
		$pgSql = preg_replace('/\s+/', ' ', $pgSql);
		$this->assertEquals('CREATE TABLE t ( id SERIAL );', $pgSql);
	}

}
