<?php
/*
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of DbUp. For the full copyright and license information
 * please view the LICENSE file that was distributed with this source code.
 *
 * If you did not receive this file as part of a distrubution that includes
 * a LICENSE file attributing then no further rights will be granted until the
 * author is contacted with information regarding the provenance of this file,
 * i.e. Who the FUCK is plagarising my work!
 */
namespace zpt\dbup;

use Iterator;

/**
 * This class encapsulates a list of database versions and the scripts that
 * incremental build a database.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class VersionList implements Iterator
{

	private $versions = [];

	public function addScript($type, $version, $path) {
		if (!isset($this->versions[$version])) {
			$this->versions[$version] = array();
		}

		$this->versions[$version][$type] = $path;
	}

	/*
	 * ------------------------------------------------------------------------
	 * Iterator implementation
	 * ------------------------------------------------------------------------
	 */

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
