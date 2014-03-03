<?php
/**
 * =============================================================================
 * Copyright (c) 2012, Philip Graham
 * All rights reserved.
 *
 * This file is part of dbUp and is licensed by the Copyright holder under the
 * 3-clause BSD License.	The full text of the license can be found in the
 * LICENSE.txt file included in the root directory of this distribution or at
 * the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace zpt\dbup\test\integration;

require_once __DIR__ . '/../setup.php';

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;

use zpt\dbup\FsVersionParser;

/**
 * This class tests the FsVersionParser class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class FsVersionParserTest extends TestCase {

	public function tearDown() {
		M::close();
	}

	public function testVersionParsing() {
		$alterDir = __DIR__ . '/sql';

		$fsVersionParser = new FsVersionParser();
		$versions = $fsVersionParser->parseVersions($alterDir);

		$numVersions = 0;
		foreach ($versions as $idx => $version) {
			$numVersions++;

			$this->assertEquals(1, $idx, "Unexpected index $idx");

			$this->assertArrayHasKey('pre', $version);
			$this->assertArrayHasKey('post', $version);
			$this->assertArrayHasKey('alter', $version);

			$this->assertEquals("$alterDir/pre-alter-000001.php", $version['pre']);
			$this->assertEquals("$alterDir/post-alter-000001.php", $version['post']);
			$this->assertEquals("$alterDir/alter-000001.sql", $version['alter']);
		}

		$this->assertEquals(1, $numVersions,
			"Only expected 1 database version. $numVersions found");
	}
}
