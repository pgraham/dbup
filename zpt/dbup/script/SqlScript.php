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

use \zpt\db\DatabaseConnection;

/**
 * This class encapsulates an SQL script using dbUp's own augmented SQL. For
 * more information on the additional features offered by dbUp refer to the
 * README.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SqlScript
{

	private $stmts;

	/**
	 * Create a new SqlScript containing the given list of
	 * {@link SqlScriptStatement}s.
	 *
	 * @param SqlScriptStatement[] $stmts
	 */
	public function __construct($stmts) {
		$this->stmts = $stmts;
	}

	public function execute(DatabaseConnection $db) {

		$state = new SqlScriptState();

		foreach ($this->stmts as $stmt) {
			try {
				$stmt->execute($db, $state);
			} catch (DatabaseException $e) {
				throw new BatchSqlExecutionException($stmt, $e);
			}
		}

	}

}
