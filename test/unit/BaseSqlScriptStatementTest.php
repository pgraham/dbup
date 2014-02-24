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

use zpt\dbup\script\SqlScriptState;

/**
 * This class tests the abstract {@link BaseSqlScriptStatement} class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class BaseSqlScriptStatementTest extends TestCase
{

	protected function tearDown() {
		M::close();
	}

	public function testBuildParams() {
		$ctorArgs = [
			"linkId := INSERT INTO my_table_link (my_table_id, link_val)"
			. "VALUES (:linkId, 'linked_value');",

			"INSERT INTO my_table_link (my_table_id, link_val)"
			. "VALUES (:linkId, 'linked_value');"
		];

		$stmt = M::mock('zpt\dbup\script\BaseSqlScriptStatement', $ctorArgs)
			->makePartial();

		$sqlState = new SqlScriptState();
		$sqlState->assignVariable('linkId', 1);
		$params = $stmt->buildParams($sqlState);

		$this->assertArrayHasKey('linkId', $params);
		$this->assertEquals(1, $params['linkId']);
	}
}
