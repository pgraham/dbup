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

/**
 * This class parses individual SQL statements in order to create
 * SqlScriptStatement instances, deferentiated on the type of statement.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SqlScriptStatementFactory {

	private static $CREATE_TABLE_RE;

	/**
	 * Create an SqlScriptStatement.
	 *
	 * @param string $stmt
	 */
	public function create($stmt) {

		if (preg_match('/^CREATE TABLE\s*\(', $stmt)) {
			return new CreateTableStatement($stmt);
		} else {
			return new SqlStatement($stmt);
		}

	}
}
