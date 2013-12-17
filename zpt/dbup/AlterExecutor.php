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

use \zpt\db\DatabaseConnection;

/**
 * Interface for classes with execute database alter SQL files.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
interface AlterExecutor {

	/**
	 * Execute the specified alter script using the given database connection.
	 *
	 * @param DatabaseConnection $db
	 *   Connection to the database to which the alter script is to be applied.
	 * @param string $path
	 *   The path to the alter script to execute.
	 */
  public function executeAlter(DatabaseConnection $db, $path);

}
