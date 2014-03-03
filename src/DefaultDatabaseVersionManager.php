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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use zpt\db\exception\DatabaseException;
use zpt\db\DatabaseConnection;

/**
 * Default DatabaseVersionRetrievalScheme implementation.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DefaultDatabaseVersionManager
	implements DatabaseVersionManager, LoggerAwareInterface
{
	use LoggerAwareTrait;

	private $column;
	private $table;

	public function __construct() {
		$this->column = 'version';
		$this->table = 'alters';
	}

	/**
	 * Retrieves the maximum value from the column `version` in a table named
	 * `alters`. The name of the column and table can be injected.
	 */
	public function getCurrentVersion(DatabaseConnection $db) {

		try {

			$sql = "SELECT MAX($this->column) FROM $this->table";
			$stmt = $db->prepare($sql);
			$r = $stmt->execute();

			$version = $r->fetchColumn();
			if ($version === false) {
				$version = null;
			}
			return $version;

		} catch (DatabaseException $e) {
			if ($e->tableDoesNotExist()) {
				return null;
			} else {
				throw $e;
			}
		}

	}

	/**
	 * Inserts a row into the database version table with the given version.
	 */
	public function setCurrentVersion(DatabaseConnection $db, $version) {
		try {
			$this->executeSetCurrentVersion($db, $version);
		} catch (DatabaseException $e) {
			if ($e->tableDoesNotExist()) {
				// Attempt to create the table then re-issue the set version query
				$this->createAltersTable($db);
				$this->executeSetCurrentVersion($db, $version);
			} else {
				throw $e;
			}
		}
	}

	public function setColumn($column) {
		$this->column = $column;
	}

	public function setTable($table) {
		$this->table = $table;
	}

	private function createAltersTable($db) {
		$escapedTable = $db->getQueryAdapter()->escapeField($this->table);
		$escapedColumn = $db->getQueryAdapter()->escapeField($this->column);

		$db->exec("CREATE TABLE $escapedTable (
			$escapedColumn integer NOT NULL
		)");
	}

	private function executeSetCurrentVersion($db, $version) {
		$stmt = $db->prepare("INSERT INTO $this->table ($this->column)
			VALUES (:version)");
		$stmt->execute([ 'version' => $version ]);
	}

}
