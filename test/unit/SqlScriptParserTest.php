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

use zpt\dbup\script\SqlScriptParser;
use PHPUnit_Framework_TestCase as TestCase;

class SqlScriptParserTest extends TestCase
{

	public function testParseBasic() {
		$parser = new SqlScriptParser();

		$scriptSrc = 'CREATE TABLE config ( key TEXT, name TEXT );';
		$script = $parser->parse($scriptSrc);

		$this->assertInstanceOf('zpt\dbup\script\SqlScript', $script);
		$this->assertCount(1, $script);

		foreach ($script as $idx => $stmt) {
			$this->assertInstanceOf('zpt\dbup\script\SqlScriptStatement', $stmt);

			if ($idx === 0) {
				$this->assertEquals($scriptSrc, $stmt->getSource());
			} else {
				$this->fail("Unexpected script statement at index $idx: $stmt");
			}
		}
	}

	public function testParseMultipleStatments() {
		$parser = new SqlScriptParser();

		$scriptSrc = [
			'CREATE TABLE config ( key TEXT, name TEXT );',
			"INSERT INTO config VALUES ('akey', 'some_text')"
		];
		$script = $parser->parse(implode("\n", $scriptSrc));

		$this->assertCount(2, $script);

		foreach ($script as $idx => $stmt) {
			$this->assertInstanceOf('zpt\dbup\script\SqlScriptStatement', $stmt);

			if ($idx > 1) {
				$this->fail("Unexpected index $idx");
			}

			$this->assertEquals($scriptSrc[$idx], $stmt->getSource());
		}
	}

	public function testParseScriptWithComment() {
		$this->markTestIncomplete("Ensure that comments are ommitted");
	}

	public function testParseScriptWithTrailingComment() {
		$this->markTestIncomplete(
			"Ensure statement lines with trailing comments have the comment stripped."
			. " This includes multi line statements"
		);
	}

	public function testParseScriptWithBlankLines() {
		$parser = new SqlScriptParser();

		$scriptSrc = "CREATE TABLE config ( key TEXT, name TEXT );\n";
		$script = $parser->parse($scriptSrc);

		$this->assertCount(1, $script);

		$scriptSrc = implode("\n", [
			'CREATE TABLE config ( key TEXT, name TEXT );',
			'',
			'         ',
			'				',
			"INSERT INTO config VALUES ('akey', 'some_text')"
		]);

		$script = $parser->parse($scriptSrc);

		$this->assertCount(2, $script);
	}

	public function testParseScriptWithMultiLineStatements() {
		$parser = new SqlScriptParser();

		$createTableSrc = implode("\n", [
			'CREATE TABLE config (',
			'key TEXT,',
			'name TEXT',
			');'
		]);
		$scriptSrc = [
			$createTableSrc,
			"INSERT INTO config VALUES ('akey', 'some_text')"
		];
		$script = $parser->parse(implode("\n", $scriptSrc));

		$this->assertCount(2, $script);

		foreach ($script as $idx => $stmt) {
			$this->assertInstanceOf('zpt\dbup\script\SqlScriptStatement', $stmt);

			if ($idx > 1) {
				$this->fail("Unexpected index $idx");
			}

			$this->assertEquals($scriptSrc[$idx], $stmt->getSource());
		}
	}

	public function testParseInsertWIthIdVariableAssignment() {
		$parser = new SqlScriptParser();

		$scriptSrc = file_get_contents(__DIR__ . '/../scripts/test2-insertid.sql');
		$script = $parser->parse($scriptSrc);

		$this->assertCount(2, $script);

		foreach ($script as $idx => $stmt) {
			$this->assertInstanceOf('zpt\dbup\script\SqlScriptStatement', $stmt);

			if ($idx === 1) {
				$this->assertInstanceOf(
					'zpt\dbup\script\InsertSqlScriptStatement',
					$stmt
				);
			}
		}
	}
}
