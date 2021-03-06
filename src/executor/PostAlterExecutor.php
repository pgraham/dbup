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
namespace zpt\dbup\executor;

use zpt\db\DatabaseConnection;

/**
 * Interface for classes which execute post-alter cleanup scripts.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
interface PostAlterExecutor {

  /**
   * Execute the post-alter script at the given path.
   *
	 * @param DatabaseConnection $db
	 *   DatabaseConnection object to which the script should be applied.
   * @param string $path The path to the post-alter script.
   * @param StdClass $data StdClass instance populated by the an associated
   *   pre-alter script.
   */
  public function executePostAlter(DatabaseConnection $db, $path, $data);

}
