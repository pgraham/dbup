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

/**
 * This class implements the AlterExecutor interface by breaking the script into 
 * statements and executing the individually using PDO::exec(...);
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class BatchSqlExecutor implements AlterExecutor {

	public function executeAlter($path, $db) {
		if (!file_exists($path)) {
			// TODO If available, log a warning using a PSR-3 logger instance
			return;
		}

		$sql = file_get_contents($path);
		$stmts = explode(";\n", $sql);
		foreach ($stmts as $stmt) {
			$db->exec($stmt);
		}
	}
}
