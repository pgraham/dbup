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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use DirectoryIterator;

/**
 * This class parses database versions from the a file system path.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class FsVersionParser implements VersionParser, LoggerAwareInterface {
  use LoggerAwareTrait;

  const ALTER_REGEX = '/^alter-0*([1-9][0-9]*)\.sql$/';
  const PRE_ALTER_REGEX = '/^pre-alter-0*([1-9][0-9]*)\.php$/';
  const POST_ALTER_REGEX = '/^post-alter-0*([1-9][0-9]*)\.php$/';

  public function parseBase($path) {
    $baseScript = "$path/base.sql";
    if (file_exists($baseScript)) {
      return $baseScript;
    }
    return null;
  }

  public function parseVersions($path) {
    $iter = new DirectoryIterator($path);

    $versions = new VersionList();
    foreach ($iter as $file) {
      $version = null;
      $type = null;

      $pathName = $file->getPathname();
      $fName = $file->getBasename();
      $matches = array();
      if (preg_match(self::ALTER_REGEX, $fName, $matches)) {
        $type = 'alter';
      } else if (preg_match(self::PRE_ALTER_REGEX, $fName, $matches)) {
        $type = 'pre';
      } else if (preg_match(self::POST_ALTER_REGEX, $fName, $matches)) {
        $type = 'post';
      }

      if ($type !== null) {
        $version = (int) $matches[1];
        $versions->addScript($type, $version, $pathName);
      }
    }

    return $versions;
  }
}
