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
use InvalidArgumentException;

/**
 * This class encapsulates an INSERT SQL statement which assigns a value to
 * a script variable.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class InsertStatement extends BaseSqlStatement implements SqlStatement
{

	private $varName;

	public function __construct($stmt) {
		$sqlStart = strpos($stmt, 'INSERT');
		if ($sqlStart === 0 || $sqlStart === false) {
			throw new InvalidArgumentException(
				"Given statement does not assign an insert id to a variable: $stmt"
			);
		}
		$sql = substr($stmt, $sqlStart);

		$assignOpPos = strpos($stmt, ':=');
		$this->varName = trim(substr($stmt, 0, $assignOpPos));

		parent::__construct($stmt, $sql);
	}

	public function execute(DatabaseConnection $db, SqlScriptState $state) {
		$stmt = $db->prepare($this->getSql());
		$result = $stmt->execute($this->buildParams($state));

		$state->assignVariable($this->varName, $result->getInsertId());
	}
}
