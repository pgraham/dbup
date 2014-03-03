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
 * Exception class for exceptions that occur while preparing to apply a series
 * of updates. This includes version parsing and current version retrieval.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseUpdateFinalizationException extends DatabaseUpdateException
{

	public function __construct($msg, Exception $cause) {
		parent::__construct($msg, 0, $cause);
	}
}
