<?php
/**
 * =============================================================================
 * Copyright (c) 2012, Philip Graham
 * All rights reserved.
 *
 * This file is part of DbUp and is licensed by the Copyright holder under the
 * 3-clause BSD License.	The full text of the license can be found in the
 * LICENSE.txt file included in the root directory of this distribution or at
 * the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace zpt\dbup;

use Iterator;

/**
 * This class encapsulates a list of database versions and the scripts that
 * incremental build a database.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class VersionList implements Iterator {

	private $versions = array();

	public function addScript($type, $version, $path) {
		if (!isset($this->versions[$version])) {
			$this->versions[$version] = array();
		}

		$this->versions[$version][$type] = $path;
	}

	/* ------------------------------------------------------------------------
	 * Iterator implementation
	 * ------------------------------------------------------------------------ */

	public function current() {
		return current($this->versions);
	}

	public function key() {
		return key($this->versions);
	}

	public function next() {
		next($this->versions);
	}

	public function rewind() {
		reset($this->versions);
	}

	public function valid() {
		// The versions array should never contain the value false so this is safe.
		return current($this->versions) !== false;
	}

}
