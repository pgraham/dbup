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

use \Psr\Log\LoggerAwareInterface;
use \Psr\Log\LoggerAwareTrait;
use \zpt\db\DatabaseConnection;

/**
 * Default DatabaseVersionRetrievalScheme implementation.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DefaultDatabaseVersionRetrievalScheme
	implements DatabaseVersionRetrievalScheme, LoggerAwareInterface
{
	use LoggerAwareTrait;

	private $column;
	private $table;

	public function __construct() {
		$this->column = 'version';
	}

	/**
	 * Retrieves the maximum value from the column `version` in a table named
	 * `alters`. The name of the column and table can be injected.
	 */
	public function getVersion(DatabaseConnection $db) {
		$stmt = $db->prepare("SELECT MAX($this->column) FROM $this->table");
		$stmt->execute();

		$version = $stmt->fetchColumn();
		if ($version === false) {
			$version = null;
		}

		return $version;
	}

	public function setColumn($column) {
		$this->column = $column;
	}

	public function setTable($table) {
		$this->table = $table;
	}

}
