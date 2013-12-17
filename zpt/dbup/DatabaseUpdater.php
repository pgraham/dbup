<?php
/**
 * =============================================================================
 * Copyright (c) 2012, Philip Graham
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
namespace zpt\dbup;

use \Psr\Log\LoggerAwareInterface;
use \Psr\Log\LoggerInterface;
use \Psr\Log\NullLogger;
use \zpt\db\DatabaseConnection;
use \Exception;
use \StdClass;

/**
 * This class applies a series of updates to a database.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseUpdater implements LoggerAwareInterface
{

	private $logger;

	private $versionParser;
	private $preAlterExecutor;
	private $alterExecutor;
	private $postAlterExecutor;
	private $dbVerRetriever;

	public function update(DatabaseConnection $db, $alterDir) {
		$this->ensureDependencies();

		$this->logger->info('Updating database with alters in directory {dir}', [
			'dir' => $alterDir
		]);

		$db->beginTransaction();

		$versions = $this->versionParser->parseVersions($alterDir);

		$curVersion = $this->dbVerRetriever->getVersion($db);
		$this->logger->info('Current database version is {dbVersion}', [
			'dbVersion' => $curVersion === null ? 'unintialized' : $curVersion
		]);

		// If this is an uninitialized database apply the base schema if it exists.
		if ($curVersion === null) {
			$base = $this->versionParser->parseBase($alterDir);
			if ($base !== null) {
				$this->logger->info('Applying base schema');
				$this->alterExecutor->executeAlter($db, $base);
			}
		}

		$versions = array();
		foreach ($versions as $version => $scripts) {

			$data = new StdClass();

			if ($version > $curVersion) {
				$this->logger->info('Applying alter {alter}', [ 'alter' => $version ]);
				if (isset($scripts['pre'])) {
					try {
						$this->preAlterExecutor->executePreAlter(
							$db,
							$scripts['pre'],
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
							$db,
							$scripts['alter']
						);
					} catch (Exception $e) {
						$db->rollback();
						throw new DatabaseUpdateException($version, 'alter', $e);
					}
				}

				if (isset($scripts['post'])) {
					try {
						$this->postAlterExecutor->executePostAlter(
							$db,
							$scripts['post'],
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

	public function setDatabaseVersionRetrievalScheme(
		DatabaseVersionRetrievalScheme $dbVerRetriever
	) {
		$this->dbVerRetriever = $dbVerRetriever;
	}

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
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

		if ($this->logger === null) {
			$this->logger = new NullLogger();
		}

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

		if ($this->dbVerRetriever === null) {
			$this->dbVerRetriever = new DefaultDatabaseVersionRetrievalScheme();
		}
	}

}
