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
 * {@link SqlStatment} for CREATE TABLE statements. These statements will
 * translate serial field types into the appropriate syntax for the database
 * against which the statement is being executed.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class CreateTableStatement extends BaseSqlStatement implements SqlStatement
{

	public function __construct($stmt) {
		parent::__construct($stmt);
	}

	public function getSql($dbDriver) {
		return $this->stmt;
	}

}
