<?php
/**
 * =============================================================================
 * Copyright (c) 2014, Philip Graham
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
 * This class parses and executes SQL scripts against a {@link
 * DatabaseConnection}.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SqlScriptExecutor
{

	private $db;

	/**
	 * Create a new script executor for the given {@link DatabaseConnection}
	 */
	public function __construct(DatabaseConnection $db) {
		$this->db = $db;
	}

	public function execute($filename) {
		$scriptSrc = file_get_contents($filename);
		$parser = new SqlScriptParser();
		$script = $parser->parse($scriptSrc);

		return $script->execute($this->db);
	}

}
