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
 * This class encapsulates the state of a currently executing {@link SqlScript}.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SqlScriptState
{

	private $vars = [];

	public function assignVariable($name, $value) {
		$this->vars[$name] = $value;
	}

	public function __get($name) {
		if (isset($this->vars[$name])) {
			return $this->vars[$name];
		}
		return null;
	}

}
