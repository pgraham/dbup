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

use \DirectoryIterator;

/**
 * PathIterator implementation that wraps an SPL DirectoryIterator instance.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SplPathIterator implements PathIterator {

	private $dir;

	public function __construct($path) {
		$this->dir = new DirectoryIterator($path);
	}

	public function getPathname() {
		return $this->dir->getPathname();
	}

	public function getPath() {
		return $this->dir->getPath();
	}

	public function getBasename() {
		return $this->dir->getBasename();
	}

	public function current() {
		return $this->dir->current();
	}

	public function key() {
		return $this->dir->key();
	}

	public function next() {
		return $this->dir->next();
	}

	public function rewind() {
		return $this->dir->rewind();
	}

	public function valid() {
		return $this->dir->valid();
	}
}
