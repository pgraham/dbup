<?php
/**
 * =============================================================================
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of dbUp and is licensed by the Copyright holder under the
 * 3-clause BSD License.	The full text of the license can be found in the
 * LICENSE.txt file included in the root directory of this distribution or at
 * the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace zpt\dbup\exception;

use Exception;

/**
 * Exception class for errors that occur while applying database updates.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseUpdateExecutionException extends DatabaseUpdateException
{

	private $version;
	private $phase;

	public function __construct($version, $phase, Exception $cause = null) {
		$this->version = $version;
		$this->phase = $phase;

		$msg = "Database updating failed on version $version in the $phase phase.";
		parent::__construct($msg, 0, $cause);
	}

	public function getVersion() {
		return $this->version;
	}

	public function getPhase() {
		return $this->phase;
	}
}
