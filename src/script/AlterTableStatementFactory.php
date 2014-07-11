<?php
/*
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of DbUp. For the full copyright and license information
 * please view the LICENSE file that was distributed with this source code.
 */
namespace zpt\dbup\script;

/**
 * This class parses ALTER TABLE statements to create instances of the
 * appropriate {@link AlterTableStatement} implementation.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class AlterTableStatementFactory
{

	const TABLE_NAME = '/^ALTER\s+TABLE\s+(\S+)\s+(.+)/i';

	const PG_COL_TYPE = '/^ALTER\s+COLUMN\s+(\S+)\s+(?:SET\s+DATA\s+)?TYPE\s+(\S+)/i';
	const MY_COL_TYPE = '/^MODIFY\s+(?:COLUMN\s+)?(\S+)\s+(\S+)/i';

	/**
	 * Create an {@link AlterTableStatement} implementation for the given `ALTER
	 * TABLE ...` SQL statement. This method assumes that the given string starts
	 * with ALTER TABLE.
	 *
	 * @param stringable $stmtSrc
	 * @return AlterTableStatement
	 */
	public function createFor($stmt) {
		if (preg_match(self::TABLE_NAME, $stmt, $matches)) {
			$tableName = $matches[1];
			$changeStmt = $matches[2];
		}

		if (
			preg_match(self::PG_COL_TYPE, $changeStmt, $matches) ||
			preg_match(self::MY_COL_TYPE, $changeStmt, $matches)
		) {
			$stmt = new ChangeColumnTypeStatement($stmt);
			$stmt->setColumn($matches[1]);
			$stmt->setType(rtrim($matches[2], ';'));
		} else {
			$stmt = new AlterTableStatement($stmt);
		}

		$stmt->setTable($tableName);
		$stmt->setAlter($changeStmt);

		return $stmt;
	}
}
