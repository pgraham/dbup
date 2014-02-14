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
abstract class BaseSqlScriptStatement
{

	protected $stmt;
	protected $sql;
	protected $params;

	public function __construct($stmt, $sql = null) {
		$this->stmt = $stmt;

		if ($sql === null) {
			$sql = $stmt;
		}
		$this->sql = $sql;

		if (preg_match_all('/:(\w+)/', $this->sql, $matches)) {
			$this->params = $matches[1];
		}
	}

	public function buildParams(SqlScriptState $state) {
		$params = [];
		if (is_array($this->params)) {
			foreach ($this->params as $paramName) {
				$params[$paramName] = $state->$paramName;
			}
		}
		return $params;
	}

	public function doExecute(DatabaseConnection $db, SqlScriptState $state) {
		$stmt = $db->prepare($this->getSql());
		$stmt->execute($this->buildParams($state));
	}

	public function getSource() {
		return $this->stmt;
	}

	public function getSql() {
		return $this->sql;
	}

}
