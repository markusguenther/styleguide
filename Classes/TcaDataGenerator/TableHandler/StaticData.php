<?php

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
use TYPO3\CMS\Styleguide\TcaDataGenerator\RecordFinder;
use TYPO3\CMS\Styleguide\TcaDataGenerator\TableHandlerInterface;

/**
 * Generate data for table tx_styleguide_staticdata
 */
class StaticData extends AbstractTableHandler implements TableHandlerInterface
{
    /**
     * @var string Table name to match
     */
    protected $tableName = 'tx_styleguide_staticdata';

    /**
     * Adds rows
     *
     * @param string $tableName
     * @return string
     */
    public function handle($tableName)
    {
        /** @var RecordFinder $recordFinder */
        $recordFinder = GeneralUtility::makeInstance(RecordFinder::class);
        /** @var DatabaseConnection $connection */
        $connection = GeneralUtility::makeInstance(DatabaseConnection::class);

        // tx_styleguide_staticdata is used in other TCA demo fields. We need some default
        // rows to later connect other fields to these rows.
        $pid = $recordFinder->findPidOfMainTableRecord('tx_styleguide_staticdata');
        $connection->exec_INSERTmultipleRows(
            'tx_styleguide_staticdata',
            [ 'pid', 'value_1' ],
            [
                [ 'pid' => $pid, 'value_1' => 'foo' ],
                [ 'pid' => $pid, 'value_1' => 'bar' ],
                [ 'pid' => $pid, 'value_1' => 'foofoo' ],
                [ 'pid' => $pid, 'value_1' => 'foobar' ],
            ]
        );
    }
}
