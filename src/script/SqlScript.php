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
use Countable;
use Iterator;
use OutOfBoundsException;

/**
 * This class encapsulates an SQL script using dbUp's own augmented SQL. For
 * more information on the additional features offered by dbUp refer to the
 * README.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SqlScript implements Countable, Iterator
{

	private $stmts = [];

	/**
	 * Create a new SqlScript containing the given list of
	 * {@link SqlScriptStatement}s.
	 *
	 * @param SqlScriptStatement[] $stmts
	 */
	public function __construct(array $stmts = []) {
		$this->stmts = $stmts;
	}

	public function execute(DatabaseConnection $db) {
		$state = new SqlScriptState();
		foreach ($this->stmts as $stmt) {
			$stmt->execute($db, $state);
		}

		// Return final state of SQL script so that variable values can be used in 
		// subsequent processing without requiring an addition query.
		return $state;
	}

	/*
	 * ---------------------------------------------------------------------------
	 * Countable
	 * ---------------------------------------------------------------------------
	 */

	public function count() {
		return count($this->stmts);
	}

	/*
	 * ---------------------------------------------------------------------------
	 * Iterator
	 * ---------------------------------------------------------------------------
	 */

	public function current() {
		return current($this->stmts);
	}

	public function key() {
		return key($this->stmts);
	}

	public function next() {
		return next($this->stmts);
	}

	public function rewind() {
		return reset($this->stmts);
	}

	public function valid() {
		return current($this->stmts) !== false;
	}
}
