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
namespace zpt\dbup\test;

require_once __DIR__ . '/setup.php';

use PHPUnit_Framework_TestCase as TestCase;
use zpt\dbup\script\SqlScriptExecutor;
use zpt\db\DatabaseConnection;

class SqlScriptExecutionTest extends TestCase
{

	private $db;

	protected function setUp() {
		$this->db = new DatabaseConnection([
			'driver' => 'sqlite',
			'schema' => ':memory:'
		]);
	}

	public function testBasicScriptExecution() {
		$executor = new SqlScriptExecutor($this->db);

		$executor->execute(__DIR__ . '/scripts/test1.sql');

		$result = $this->db->query('SELECT * FROM config');
		$this->assertInstanceOf('zpt\db\QueryResult', $result);
	}

	public function testInsertIdVariable() {
		$executor = new SqlScriptExecutor($this->db);
		$values = $executor->execute(__DIR__ . '/scripts/test2-insertid.sql');

		$insertId = $values->insertId;
		$this->assertNotNull($insertId);

		$result = $this->db->query('SELECT * FROM my_table');

		$numRows = 0;
		foreach ($result as $idx => $row) {
			$numRows++;
			if ($idx > 0) {
				$this->fail("Unexpected index: $idx");
			}
			$this->assertEquals($insertId, $row['id']);
		}
		$this->assertEquals(1, $numRows);
	}

	public function testUseInsertId() {
		$executor = new SqlScriptExecutor($this->db);
		$values = $executor->execute(__DIR__ . '/scripts/test3-reuse-insertid.sql');

		$insertId = $values->insertId;
		$this->assertNotNull($insertId);

		$linkId = $values->linkId;
		$this->assertNotNull($linkId);
	}

}
