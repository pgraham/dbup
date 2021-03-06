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
 * Interface for classes which implement a scheme for storing a database's
 * current version.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
interface DatabaseVersionManager
{

	/**
	 * Retrieve the database version using the given connection. Connection MUST
	 * be connected to a database, not just a database engine. The returned
	 * version SHOULD be an integer.
	 *
	 * @param PDO $db
	 * @return integer
	 */
	public function getCurrentVersion(DatabaseConnection $db);

	/**
	 * Set the database's current version.
	 *
	 * @param DatabaseConnection $db
	 * @param mixed $version;
	 */
	public function setCurrentVersion(DatabaseConnection $db, $version);

}
