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
use \StdClass;

/**
 * This class applies a series of updates to a database.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseUpdater {

  private $_db;
  private $_curVersion;
  private $_alterDir;

  private $_versionParser;
  private $_preAlterExecutor;
  private $_alterExecutor;
  private $_postAlterExecutor;

  public function __construct($db, $curVersion, $alterDir)
  {
    $this->_db = $db;
    $this->_curVersion = $curVersion;
    $this->_alterDir = $alterDir;
  }

  public function run() {
    $versions = $this->_versionParser->parseVersions($this->_alterDir);

    $this->_db->beginTransaction();

    foreach ($versions as $version => $scripts) {

      $data = new StdClass();

      if ($version > $this->_curVersion) {
        if (isset($scripts['pre'])) {
          try {
            $this->_preAlterExecutor->execute($scripts['pre'], $this->_db,
              $data);
          } catch (Exception $e) {
            $this->_db->rollback();
            throw new DatabaseUpdateException($version, 'pre', $e);
          }
        }

        if (isset($scripts['alter'])) {
          try {
            $this->_alterExecutor->execute($scripts['alter'], $this->_db);
          } catch (Exception $e) {
            $this->_db->rollback();
            throw new DatabaseUpdateException($version, 'alter', $e);
          }
        }

        if (isset($scripts['post'])) {
          try {
            $this->_postAlterExecutor->execute($scripts['post'], $this->_db,
              $data);
          } catch (Exception $e) {
            $this->_db->rollback();
            throw new DatabaseUpdateException($version, 'post', $e);
          }
        }
      }
    }

    $this->_db->commit();
  }

  public function setAlterExecutor(AlterExecutor $alterExecutor) {
    $this->_alterExecutor = $alterExecutor;
  }

  public function setPostAlterExecutor(PostAlterExecutor $postAlterExecutor) {
    $this->_postAlterExecutor = $postAlterExecutor;
  }

  public function setPreAlterExecutor(PreAlterExecutor $preAlterExecutor) {
    $this->_preAlterExecutor = $preAlterExecutor;
  }

  public function setVersionParser(VersionParser $versionParser) {
    $this->_versionParser = $versionParser;
  }

}
