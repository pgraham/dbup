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

use \Exception;

/**
 * Exception class for errors that occur while apply database updates.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseUpdateException extends Exception {

  private $_version;
  private $_phose;

  public function __construct($version, $phase, $cause = null) {
    parent::__construct("Database updating failed on $version in the $phase phase.", 0, $cause);
    $this->_version = $version;
    $this->_phase = $phase;
  }

  public function getVersion() {
    return $this->_version;
  }

  public function getPhase() {
    return $this->_phase;
  }
}
