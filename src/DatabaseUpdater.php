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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use zpt\db\DatabaseConnection;
use zpt\db\exception\DatabaseException;
use zpt\dbup\exception\DatabaseUpdateFinalizationException;
use zpt\dbup\exception\DatabaseUpdateInitializationException;
use zpt\dbup\exception\DatabaseUpdateExecutionException;
use zpt\dbup\executor\AlterExecutor;
use zpt\dbup\executor\BatchSqlExecutor;
use zpt\dbup\executor\PhpIncludeExecutor;
use zpt\dbup\executor\PostAlterExecutor;
use zpt\dbup\executor\PreAlterExecutor;
use Exception;
use StdClass;

/**
 * This class applies a series of updates to a database.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseUpdater implements LoggerAwareInterface
{
	use LoggerAwareTrait;


	private $versionParser;
	private $preAlterExecutor;
	private $alterExecutor;
	private $postAlterExecutor;
	private $dbVerManager;

	public function initialize(DatabaseConnection $db, $alterDir) {
		$this->ensureDependencies();
		$this->logger->info("[DBUP] Initializing for alters in $alterDir");

		$curVersion = $this->getCurrentVersion($db);

		if ($curVersion === null) {
			$db->beginTransaction();
			$this->doInitialize($db, $alterDir);
			$this->setCurrentVersion($db, 0);
			$db->commit();
		}
	}

	public function update(DatabaseConnection $db, $alterDir) {
		$this->ensureDependencies();

		$this->logger->info("Updating database with alters in $alterDir");

		$curVersion = $this->getCurrentVersion($db);

		$db->beginTransaction();

		// If this is an uninitialized database apply the base schema if it exists.
		if ($curVersion === null) {
			$this->doInitialize($db, $alterDir);
		}

		$versions = $this->versionParser->parseVersions($alterDir);
		foreach ($versions as $version => $scripts) {

			$data = new StdClass();

			if ($version > $curVersion) {
				$this->logger->info('Applying alter {alter}', [ 'alter' => $version ]);
				if (isset($scripts['pre'])) {
					$this->logger->debug('Running PHP pre-alter script');
					try {
						$this->preAlterExecutor->executePreAlter(
							$db,
							$scripts['pre'],
							$data
						);
					} catch (Exception $e) {
						$db->rollback();
						throw new DatabaseUpdateExecutionException($version, 'pre', $e);
					}
				}

				if (isset($scripts['alter'])) {
					$this->logger->debug('Apply SQL alter');
					try {
						$this->alterExecutor->executeAlter(
							$db,
							$scripts['alter']
						);
					} catch (Exception $e) {
						$db->rollback();
						throw new DatabaseUpdateExecutionException($version, 'alter', $e);
					}
				}

				if (isset($scripts['post'])) {
					$this->logger->debug('Running PHP post-alter script');
					try {
						$this->postAlterExecutor->executePostAlter(
							$db,
							$scripts['post'],
							$data
						);
					} catch (Exception $e) {
						$db->rollback();
						throw new DatabaseUpdateExecutionException($version, 'post', $e);
					}
				}

				$this->setCurrentVersion($db, $version);
			}
		}

		$db->commit();
	}

	/**
	 * Set the AlterExecutor. If not specified a {@link BatchSqlExecutor} will be
	 * used.
	 *
	 * @param AlterExecutor $alterExecutor
	 */
	public function setAlterExecutor(AlterExecutor $alterExecutor) {
		$this->alterExecutor = $alterExecutor;
	}

	/**
	 * Set the DatabaseVersionRetrievalScheme. If not specified a
	 * {@link DefaultDatabaseVersionRetrievalScheme} will be used.
	 *
	 * @param DatabaseVersionRetrievalScheme $dbVerRetriever
	 */
	public function setDatabaseVersionManager(
		DatabaseVersionManager $dbVerManager
	) {
		$this->dbVerManager = $dbVerManager;
	}

	/**
	 * Set the PostAlterExecutor. If not specified a {@link PhpIncludeExecutor}
	 * will be used.
	 *
	 * @param PostAlterExecutor $executor
	 */
	public function setPostAlterExecutor(PostAlterExecutor $executor) {
		$this->postAlterExecutor = $executor;
	}

	/**
	 * Set the PreAlterExecutor. If not specified a {@link PhpIncludeExecutor}
	 * will be used.
	 *
	 * @param PreAlterExecutor $executor
	 */
	public function setPreAlterExecutor(PreAlterExecutor $preAlterExecutor) {
		$this->preAlterExecutor = $preAlterExecutor;
	}

	/**
	 * Set the VersionParser. If not specified a {@link FsVersionParser} will be
	 * used.
	 *
	 * @param VersionParser $versionParser
	 */
	public function setVersionParser(VersionParser $versionParser) {
		$this->versionParser = $versionParser;
	}

	protected function doInitialize($db, $alterDir) {
		$base = $this->versionParser->parseBase($alterDir);
		if ($base !== null) {
			$this->logger->info('Applying base schema');
			$this->alterExecutor->executeAlter($db, $base);
		}
	}

	private function ensureDependencies() {

		if ($this->logger === null) {
			$this->logger = new NullLogger();
		}

		if ($this->preAlterExecutor === null || $this->postAlterExecutor === null) {
			$inclExecutor = new PhpIncludeExecutor();
			$inclExecutor->setLogger($this->logger);

			if ($this->preAlterExecutor === null) {
				$this->preAlterExecutor = $inclExecutor;
			}

			if ($this->postAlterExecutor === null) {
				$this->postAlterExecutor = $inclExecutor;
			}
		}

		if ($this->alterExecutor === null) {
			$this->alterExecutor = new BatchSqlExecutor();
			$this->alterExecutor->setLogger($this->logger);
		}

		if ($this->versionParser === null) {
			$this->versionParser = new FsVersionParser();
			$this->versionParser->setLogger($this->logger);
		}

		if ($this->dbVerManager === null) {
			$this->dbVerManager = new DefaultDatabaseVersionManager();
			$this->dbVerManager->setLogger($this->logger);
		}
	}

	private function getCurrentVersion($db) {
		try {
			$curVersion = $this->dbVerManager->getCurrentVersion($db);

			$curVersionMsg = $curVersion === null ? 'uninitialized' : $curVersion;
			$this->logger->debug("[DBUP] Current db version: $curVersionMsg");

			return $curVersion;
		} catch (DatabaseException $e) {
			throw new DatabaseUpdateInitializationException(
				"Unable to retrieve current database version", $e);
		}
	}

	private function setCurrentVersion($db, $version) {
		try {
			$this->dbVerManager->setCurrentVersion($db, $version);
		} catch (DatabaseException $e) {
			throw new DatabaseUpdateFinalizationException(
				"Error setting new database version", $e);
		}
	}
}
