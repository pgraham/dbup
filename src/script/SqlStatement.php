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
namespace zpt\dbup\script;

use zpt\db\DatabaseConnection;

/**
 * Interface for SQL statements that are part of an {@link SqlScript}
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
interface SqlStatement
{

	/**
	 * Execute the statement against the specified database.
	 *
	 * @param DatabaseConnection $db
	 *   Connection to the database against which the statement should be applied.
	 * @param SqlScriptState $state
	 *   The current state of the the SQL script of which the statement is a part.
	 * @return zpt\db\QueryResult
	 *   Return the result of executing the statement
	 */
	public function execute(DatabaseConnection $db, SqlScriptState $state);

}
