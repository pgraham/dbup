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

use \zpt\db\DatabaseConnection;

/**
 * This class encapsulates common functionality among all SqlScriptStatement
 * implementations.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
abstract class BaseSqlStatement
{

	protected $stmt;
	protected $sql;
	protected $params;

	/**
	 * Construct the base functionality of an {@link SqlStatement} implementation.
	 *
	 * @param string $stmt
	 *   The SQL statement source as it appears in the SQL script
	 * @param string $sql
	 *   If the statement source contains additional syntax, the base SQL without
	 *   additional syntax can be provided.
	 */
	public function __construct($stmt) {
		$this->stmt = $stmt;
	}

	public function execute(DatabaseConnection $db, SqlScriptState $state) {
		$sql = $this->getSql($db->getInfo()->getDriver());
		$params = $this->parseParams($sql, $state);

		$stmt = $db->prepare($sql);
		return $stmt->execute($params);
	}

	public function getSource() {
		return $this->stmt;
	}

	public abstract function getSql($dbDriver);

	private function parse($sql, $state) {
		$paramNames = [];
		$params = [];

		if (preg_match_all('/:(\w+)/', $sql, $matches)) {
			$paramNames = $matches[1];
		}

		foreach ($paramNames as $paramName) {
			$params[$paramName] = $state->$paramName;
		}
		return $params;
	}

}
