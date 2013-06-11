<?php
/**
 * =============================================================================
 * Copyright (c) 2012, Philip Graham
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

require_once __DIR__ . '/test-common.php';

use \zpt\dbup\FsVersionParser;
use \Mockery as M;
use \PHPUnit_Framework_TestCase as TestCase;

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

    $mockFile = M::mock();
    $mockFile
      ->shouldReceive("getPathname")
      ->times(3)
      ->andReturn($alterDir);
    $mockFile
      ->shouldReceive("getBasename")
      ->times(3)
      ->andReturn(
        "pre-alter-000001.php",
        "alter-000001.sql",
        "post-alter-000001.php"
      );
    $pathIterator = mockIteratorOver(array(
      $mockFile,
      $mockFile,
      $mockFile
    ), 'zpt\dbup\PathIterator');

    $pathIteratorFactory = M::mock('zpt\dbup\PathIteratorFactory');
    $pathIteratorFactory->shouldReceive('create')->once()->with($alterDir)
      ->andReturn($pathIterator);

    $fsVersionParser = new FsVersionParser();
    $fsVersionParser->setPathIteratorFactory($pathIteratorFactory);

    $versions = $fsVersionParser->parseVersions($alterDir);
    print_r($versions);
  }
}
