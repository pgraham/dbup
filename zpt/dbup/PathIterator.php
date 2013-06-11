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

use \Iterator;

/**
 * Interface for Factory objects that interate over a path.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
interface PathIterator extends Iterator {

  /**
   * Get the path and name components of the current item in the iteration.
   *
   * @return string
   */
  public function getPathname();

  /**
   * Get the path component of the current item in the iteration.
   *
   * @return string
   */
  public function getPath();

  /**
   * Get the name component of the current item in the iteration.
   *
   * @return string
   */
  public function getBasename();

}
