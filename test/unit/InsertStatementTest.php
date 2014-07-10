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

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;

use zpt\dbup\script\InsertStatement;

/**
 * This class tests the InsertStatement class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class InsertStatementTest extends TestCase
{

	protected function tearDown() {
		M::close();
	}

	public function testConstruction() {
		$stmtSrc = "insertId := INSERT INTO my_table (name) VALUES ('aName');";
		$stmt = new InsertStatement($stmtSrc);

		$this->assertEquals($stmtSrc, $stmt->getSource());
		$this->assertEquals(
			"INSERT INTO my_table (name) VALUES ('aName');",
			$stmt->getSql()
		);
	}

	public function testExecution() {
		$stmtSrc = "insertId := INSERT INTO my_table (name) VALUES ('aName');";
		$stmt = new InsertStatement($stmtSrc);

		$queryResult = M::mock('zpt\db\QueryResult');
		$queryResult
			->shouldReceive('getInsertId')
			->andReturn(1);

		$dbPrepStmt = M::mock('zpt\db\PreparedStatement');
		$dbPrepStmt
			->shouldReceive('execute')
			->andReturn($queryResult);

		$db = M::mock('zpt\db\DatabaseConnection');
		$db
			->shouldReceive('prepare')
			->with("INSERT INTO my_table (name) VALUES ('aName');")
			->andReturn($dbPrepStmt);

		$state = M::mock('zpt\dbup\script\SqlScriptState');
		$state
			->shouldReceive('assignVariable')
			->with('insertId', 1);


		$stmt->execute($db, $state);
	}

}
