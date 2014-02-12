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

require_once __DIR__ . '/setup.php';

use \PHPUnit_Framework_TestCase as TestCase;
use \zpt\dbup\DatabaseUpdateException;
use \zpt\dbup\DatabaseUpdater;
use \zpt\util\PdoExt;
use \Exception;
use \Mockery as M;

/**
 * This class tests the DatabaseUpdater class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseUpdaterTest extends TestCase {

  public function tearDown() {
    M::close();
  }

  public function testOneVersionAllPhases() {
    $dbDir = __DIR__ . '/sql';

    // Mock dependencies and setup expectations
    // -------------------------------------------------------------------------
    $pdo = new PdoExt([
      'driver' => 'sqlite',
      'database' => ':memory:',
      'username' => null,
      'password' => null
    ]);
    $db = M::mock($pdo);
    $db->shouldReceive('beginTransaction')->withNoArgs()->once();
    $db->shouldReceive('commit')->withNoArgs()->once();
    $db->shouldReceive('rollback')->never();

    // Create the VersionList returned by the mock version parser
    $versions = mockIteratorOver(array(
      1 => array(
        'alter' => "$dbDir/alter-000001.sql",
        'pre' => "$dbDir/pre-alter-000001.php",
        'post' => "$dbDir/post-alter-000001.php"
      )
    ));

    $versionParser = M::mock('zpt\dbup\VersionParser')
      ->shouldReceive('parseVersions')
      ->with($dbDir)
      ->andReturn($versions)
      ->getMock();

    $preAlterExecutor = M::mock('zpt\dbup\PreAlterExecutor')
      ->shouldReceive('execute')
      ->with("$dbDir/pre-alter-000001.php", $db, anInstanceOf('stdClass'))
      ->getMock();

    $alterExecutor = M::mock('zpt\dbup\AlterExecutor')
      ->shouldReceive('execute')
      ->with("$dbDir/alter-000001.sql", $db)
      ->getMock();

    $postAlterExecutor = M::mock('zpt\dbup\PostAlterExecutor')
      ->shouldReceive('execute')
      ->with("$dbDir/post-alter-000001.php", $db, anInstanceOf('stdClass'))
      ->getMock();

    // Set object under test and its mocked dependencies
    // -------------------------------------------------------------------------
    $dbup = new DatabaseUpdater();
    $dbup->setVersionParser($versionParser);
    $dbup->setPreAlterExecutor($preAlterExecutor);
    $dbup->setAlterExecutor($alterExecutor);
    $dbup->setPostAlterExecutor($postAlterExecutor);

    // Run the update to exercise the object
    // -------------------------------------------------------------------------
    $dbup->update($db, $dbDir);
  }

  public function testExceptionInPrePhase() {
    $dbDir = __DIR__ . '/sql';

    // Mock dependencies and setup expectations
    // -------------------------------------------------------------------------
    $db = M::mock('db');
    $db->shouldReceive('beginTransaction')->withNoArgs()->once();
    $db->shouldReceive('rollback')->withNoArgs()->once();
    $db->shouldReceive('commit')->never();

    // Create the VersionList returned by the mock version parser
    $versions = mockIteratorOver(array(
      1 => array(
        'alter' => "$dbDir/alter-000001.sql",
        'pre' => "$dbDir/pre-alter-000001.php",
        'post' => "$dbDir/post-alter-000001.php"
      )
    ), 'Iterator', false, 1);

    $versionParser = M::mock('zpt\dbup\VersionParser')
      ->shouldReceive('parseVersions')
      ->with($dbDir)
      ->andReturn($versions)
      ->getMock();

    $preAlterExecutor = M::mock('zpt\dbup\PreAlterExecutor')
      ->shouldReceive('execute')
      ->with("$dbDir/pre-alter-000001.php", $db, anInstanceOf('stdClass'))
      ->andThrow(new Exception())
      ->getMock();

    $alterExecutor = M::mock('zpt\dbup\AlterExecutor')
      ->shouldReceive('execute')
      ->never()
      ->getMock();

    $postAlterExecutor = M::mock('zpt\dbup\PostAlterExecutor')
      ->shouldReceive('execute')
      ->never()
      ->getMock();

    // Set object under test and its mocked dependencies
    // -------------------------------------------------------------------------
    $dbup = new DatabaseUpdater();
    $dbup->setVersionParser($versionParser);
    $dbup->setPreAlterExecutor($preAlterExecutor);
    $dbup->setAlterExecutor($alterExecutor);
    $dbup->setPostAlterExecutor($postAlterExecutor);

    // Run the update to exercise the object
    // -------------------------------------------------------------------------
    try {
      $dbup->update($db, $dbDir);
      $this->fail('Expected DatabaseUpdateException has not been raised');
    } catch (DatabaseUpdateException $e) {
      // TODO Assert the DatabaseUpdateException
      $this->assertEquals(1, $e->getVersion());
      $this->assertEquals('pre', $e->getPhase());
    }
  }
}
