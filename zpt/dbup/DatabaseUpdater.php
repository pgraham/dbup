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
use \PDO;
use \StdClass;

/**
 * This class applies a series of updates to a database.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseUpdater {

  private $versionParser;
  private $preAlterExecutor;
  private $alterExecutor;
  private $postAlterExecutor;
  private $curVerRetriever;

  public function update(PDO $db, $alterDir) {
    $this->ensureDependencies();

    $db->beginTransaction();

    $versions = $this->versionParser->parseVersions($alterDir);

    $curVersion = $this->curVerRetriever->getVersion($db);
    if ($curVersion === null) {
      $base = $this->versionParser->parseBase($alterDir);
      if ($base !== null) {
        $this->alterExecutor->executeAlter($base, $db);
      }
    }

    $versions = array();
    foreach ($versions as $version => $scripts) {

      $data = new StdClass();

      if ($version > $curVersion) {
        if (isset($scripts['pre'])) {
          try {
            $this->preAlterExecutor->executePreAlter(
              $scripts['pre'],
              $db,
              $data
            );
          } catch (Exception $e) {
            $db->rollback();
            throw new DatabaseUpdateException($version, 'pre', $e);
          }
        }

        if (isset($scripts['alter'])) {
          try {
            $this->alterExecutor->executeAlter(
              $scripts['alter'],
              $db
            );
          } catch (Exception $e) {
            $db->rollback();
            throw new DatabaseUpdateException($version, 'alter', $e);
          }
        }

        if (isset($scripts['post'])) {
          try {
            $this->postAlterExecutor->executePostAlter(
              $scripts['post'],
              $db,
              $data
            );
          } catch (Exception $e) {
            $db->rollback();
            throw new DatabaseUpdateException($version, 'post', $e);
          }
        }
      }
    }

    $db->commit();
  }

  public function setAlterExecutor(AlterExecutor $alterExecutor) {
    $this->alterExecutor = $alterExecutor;
  }

  public function setCurrentVersionRetrievalScheme(
    CurrentVersionRetrievalScheme $curVerRetriever
  ) {
    $this->curVerRetriever = $curVerRetriever;
  }

  public function setPostAlterExecutor(PostAlterExecutor $postAlterExecutor) {
    $this->postAlterExecutor = $postAlterExecutor;
  }

  public function setPreAlterExecutor(PreAlterExecutor $preAlterExecutor) {
    $this->preAlterExecutor = $preAlterExecutor;
  }

  public function setVersionParser(VersionParser $versionParser) {
    $this->versionParser = $versionParser;
  }

  private function ensureDependencies() {

    if ($this->preAlterExecutor === null || $this->postAlterExecutor === null) {
      $inclExecutor = new PhpIncludeExecutor();

      if ($this->preAlterExecutor === null) {
        $this->preAlterExecutor = $inclExecutor;
      }

      if ($this->postAlterExecutor === null) {
        $this->postAlterExecutor = $inclExecutor;
      }
    }

    if ($this->alterExecutor === null) {
      $this->alterExecutor = new BatchSqlExecutor();
    }

    if ($this->versionParser === null) {
      $this->versionParser = new FsVersionParser();
    }

    if ($this->curVerRetriever === null) {
      $this->curVerRetriever = new DefaultCurrentVersionRetrievalScheme();
    }
  }

}
