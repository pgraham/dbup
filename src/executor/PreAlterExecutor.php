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
 * Interface for classes with execute pre-alter initizalization PHP scripts.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
interface PreAlterExecutor {

  /**
   * Execute the pre-alter script at the given path.
   *
	 * @param DatabaseConection $db
   *   Database connection to which the script is applied
   * @param string $path Path to the pre-alter script
   * @param Initially empty StdClass instance that can be populated with any
   *   data that should be passed to an associated post-alter script.
   */
  public function executePreAlter(DatabaseConnection $db, $path, $data);

}
