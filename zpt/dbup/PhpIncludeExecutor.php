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

use \Psr\Log\LoggerInterface;
use \Psr\Log\LoggerAwareInterface;

/**
 * This class implements both the PreAlterExecutor and PostAlterExecutor
 * interfaces by simply doing a PHP include of the pre/post alter scripts.
 * Any data returned from the pre alter will be passed to the post alter in the
 * global variable $DBUP_DATA.
 */
class PhpIncludeExecutor
	implements PreAlterExecutor, PostAlterExecutor, LoggerAwareInterface
{

	private $logger;

	/**
	 * Execute the specified pre-alter script.
	 *
	 * The given PDO connection will be made available to the included script as
	 *
	 *     $GLOBALS['DBUP_CONN'];
	 *
	 * The given data object will be made available to the included script as
	 *
	 *     $GLOBALS['DBUP_DATA'];
	 *
	 * The pre alter script can also return an array or object which will have
	 * it's keys/properties added to the data object.
	 */
	public function executePreAlter($path, $db, $data) {
		if (!file_exists($path)) {
			if ($this->logger !== null) {
				$msg = "Cannot execute pre-alter, file does not exist: $path";
				$this->logger->warning($msg);
			}
			return;
		}

		$GLOBALS['DBUP_CONN'] = $db;
		$GLOBALS['DBUP_DATA'] = $data;

		$retData = include $path;

		if (is_array($retData) || is_object($retData)) {
			foreach ($retData as $k => $v) {
				$data->$k = $v;
			}
		}
	}

	/**
	 * Execute the specified post-alter script.
	 *
	 * The given PDO connection and data object will be made available to the
	 * included script as
	 *
	 *     $GLOBALS['DBUP_CONN'];
	 *     $GLOBALS['DBUP_DATA'];
	 */
	public function executePostAlter($path, $db, $data) {
		if (!file_exists($path)) {
			// TODO If available, use a PSR-3 logger to output a warning
			return;
		}

		$GLOBALS['DBUP_CONN'] = $db;
		$GLOBALS['DBUP_DATA'] = $data;

		include $path;
	}

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

}
