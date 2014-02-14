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

// Register composer autoloader.
// -----------------------------------------------------------------------------

// Find composer vendor director. If this is a standalone installation it will
// be in the root directory of the package. If it is a dependency of another
// package this package will itself be in the vendor directory.

$dir = __DIR__ . '/..';
while (!file_exists($dir . DIRECTORY_SEPARATOR . 'vendor')) {
  if ($dir === '/') {
    throw new Exception("Composer install directory not found.");
  }
  $dir = dirname($dir);
}
$composerAutoloaderPath = implode(DIRECTORY_SEPARATOR, [
  $dir,
  'vendor',
  'autoload.php'
]);
require $composerAutoloaderPath;

/**
 * Create a mock iterator which expects to be iterated once using the given
 * array as data.
 */
function mockIteratorOver($a, $class = 'Iterator', $complete = true,
    $numElms = null)
{
  if ($complete === false && $numElms === null) {
    throw new Exception("Must provide a number of elements for an " .
      "incomplete iteration");
  }

  if ($complete) {
    $numElms = count($a);
  }

  $m = \Mockery::mock($class);

  // The iterator will receive a call to `rewind` with no arguments
  $m->shouldReceive('rewind')->withNoArgs()->once();

  // The iterator will receive a call to `valid` for each element in the array
  // and return true to indicate that that iteration should continue as well as
  // one last time returning false to indicate the end of the iteration
  $validVals = array();
  for ($i = 0; $i < $numElms; $i++) {
    $validVals[] = true;
  }
  if ($complete) {
    $validVals[] = false;
  }

  $exp = $m->shouldReceive('valid')->withNoArgs()->times(count($validVals));
  call_user_func_array(array($exp, 'andReturn'), $validVals);

  // The iterator will receive a call to current for each element in the given
  // array
  $currentVals = array_values($a);
  if (!$complete) {
    $currentVals = array_slice($currentVals, 0, $numElms);
  }
  $exp = $m->shouldReceive('current')->withNoArgs()->times($numElms);
  call_user_func_array(array($exp, 'andReturn'), $currentVals);

  // The iterator will receive a call to key for each element in the given array
  $keyVals = array_keys($a);
  if (!$complete) {
    $keyVals = array_slice($keyVals, 0, $numElms);
  }
  $exp = $m->shouldReceive('key')->withNoArgs()->times($numElms);
  call_user_func_array(array($exp, 'andReturn'), array_keys($a));

  // The iterator will be advanced for each element in the array
  $m->shouldReceive('next')->withNoArgs()->times(($complete ? $numElms : $numElms - 1));

  return $m;
}
