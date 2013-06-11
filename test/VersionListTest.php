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
namespace zpt\dbup;

require_once __DIR__ . '/test-common.php';

use \PHPUnit_Framework_TestCase as TestCase;
use \zpt\dbup\VersionList;

/**
 * Tests for the VersionList class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class VersionListTest extends TestCase {

  public function testVersionList() {
    $versionList = new VersionList();
  }
}
