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

use zpt\dbup\script\SqlScript;
use PHPUnit_Framework_TestCase as TestCase;

class SqlScriptTest extends TestCase
{

	public function testScriptConstruction() {
		$script = new SqlScript();

		$this->assertInstanceOf('Iterator', $script);
		$this->assertInstanceOf('Countable', $script);
	}

	public function testScriptCountable() {
		$script = new SqlScript([ 'CREATE TABLE config ( key TEXT, value TEXT )' ]);

		$this->assertCount(1, $script);

		$script = new SqlScript([
			'CREATE TABLE config ( key TEXT, value TEXT )',
			"INSERT INTO config VALUES ('key1', 'value1')"
		]);

		$this->assertCount(2, $script);
	}

	public function testScriptIterable() {
		$script = new SqlScript([
			'CREATE TABLE config ( key TEXT, value TEXT )',
			"INSERT INTO config VALUES ('key1', 'value1')"
		]);

		foreach ($script as $idx => $line) {
			if ($idx === 0) {
				$this->assertEquals(
					'CREATE TABLE config ( key TEXT, value TEXT )',
					$line
				);
			} elseif ($idx === 1) {
				$this->assertEquals(
					"INSERT INTO config VALUES ('key1', 'value1')",
					$line
				);
			} else {
				$this->fail("Unexpected iterator index $idx");
			}
		}
	}
}
