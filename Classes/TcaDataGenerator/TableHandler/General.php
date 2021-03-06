<?php
declare(strict_types=1);
namespace TYPO3\CMS\Styleguide\TcaDataGenerator\TableHandler;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Styleguide\TcaDataGenerator\RecordData;
use TYPO3\CMS\Styleguide\TcaDataGenerator\RecordFinder;
use TYPO3\CMS\Styleguide\TcaDataGenerator\TableHandlerInterface;

/**
 * General table handler
 */
class General extends AbstractTableHandler implements TableHandlerInterface
{
    /**
     * Match always
     *
     * @param string $tableName
     * @return bool
     */
    public function match(string $tableName): bool
    {
        return true;
    }

    /**
     * Adds rows
     *
     * @param string $tableName
     * @return string
     */
    public function handle(string $tableName)
    {
        $database = $this->getDatabase();
        /** @var RecordFinder $recordFinder */
        $recordFinder = GeneralUtility::makeInstance(RecordFinder::class);
        /** @var RecordData $recordData */
        $recordData = GeneralUtility::makeInstance(RecordData::class);

        // First insert an empty row and get the uid of this row since
        // some fields need this uid for relations later.
        $fieldValues = [
            'pid' => $recordFinder->findPidOfMainTableRecord($tableName),
        ];
        $database->exec_INSERTquery($tableName, $fieldValues);
        $fieldValues['uid'] = $database->sql_insert_id();
        $fieldValues = $recordData->generate($tableName, $fieldValues);
        $database->exec_UPDATEquery(
            $tableName,
            'uid = ' . $fieldValues['uid'],
            $fieldValues
        );
    }
}
