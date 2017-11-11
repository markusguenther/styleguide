<?php

namespace TYPO3\CMS\Styleguide\TcaDataGenerator;

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
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class contains helper methods to locate uids or pids of specific records
 * in the system.
 */
class RecordFinder
{
    /**
     * Returns a uid list of existing styleguide demo top level pages.
     * These are pages with pid=0 and tx_styleguide_containsdemo set to 'tx_styleguide'.
     * This can be multiple pages if "create" button was clicked multiple times without "delete" in between.
     *
     * @return array
     */
    public function findUidsOfStyleguideEntryPages()
    {
        /** @var DatabaseConnection $connection */
        $connection = GeneralUtility::makeInstance(DatabaseConnection::class);
        /** @var PageRepository $pageRepository */
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);

        $whereConstraints = [
            'pid=0',
            'tx_styleguide_containsdemo=\'tx_styleguide\'',
            $pageRepository->enableFields('pages')
        ];
        $rows = $connection->exec_SELECTgetRows('uid', 'pages', implode(' AND ', $whereConstraints));
        $uids = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $uids[] = (int)$row['uid'];
            }
        }
        return $uids;
    }

    /**
     * "Main" tables have a single page they are located on with their possible children.
     * The methods find this page by getting the highest uid of a page where field
     * tx_styleguide_containsdemo is set to given table name.
     *
     * @param string $tableName
     * @return int
     * @throws Exception
     */
    public function findPidOfMainTableRecord($tableName)
    {
        /** @var DatabaseConnection $connection */
        $connection = GeneralUtility::makeInstance(DatabaseConnection::class);
        /** @var PageRepository $pageRepository */
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);

        $whereClause = 'tx_styleguide_containsdemo=' . $tableName . ' AND ' . $pageRepository->enableFields('pages');
        $row = $connection->exec_SELECTgetRows('uid', 'pages', $whereClause, '', 'pid DESC');
        if (count($row) !== 1) {
            throw new Exception(
                'Found no page for main table ' . $tableName,
                1457690656
            );
        }
        return (int)$row['uid'];
    }

    /**
     * Find uids of styleguide demo sys_language`s
     *
     * @return array List of uids
     */
    public function findUidsOfDemoLanguages()
    {
        /** @var DatabaseConnection $connection */
        $connection = GeneralUtility::makeInstance(DatabaseConnection::class);
        /** @var PageRepository $pageRepository */
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);

        $whereClause = 'tx_styleguide_isdemorecord=1 AND ' . $pageRepository->enableFields('sys_language');
        $rows = $connection->exec_SELECTgetRows('uid', 'sys_language', $whereClause);

        $result = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $result[] = $row['uid'];
            }
        }
        return $result;
    }

    /**
     * Find uids of styleguide demo be_groups
     *
     * @return array List of uids
     */
    public function findUidsOfDemoBeGroups()
    {
        /** @var DatabaseConnection $connection */
        $connection = GeneralUtility::makeInstance(DatabaseConnection::class);
        /** @var PageRepository $pageRepository */
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);

        $whereClause = 'tx_styleguide_isdemorecord=1 AND ' . $pageRepository->enableFields('be_groups');
        $rows = $connection->exec_SELECTgetRows('uid', 'be_groups', $whereClause);

        $result = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $result[] = $row['uid'];
            }
        }
        return $result;
    }

    /**
     * Find uids of styleguide demo be_users
     *
     * @return array List of uids
     */
    public function findUidsOfDemoBeUsers()
    {
        /** @var DatabaseConnection $connection */
        $connection = GeneralUtility::makeInstance(DatabaseConnection::class);
        /** @var PageRepository $pageRepository */
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);

        $whereClause = 'tx_styleguide_isdemorecord=1 AND ' . $pageRepository->enableFields('be_users');
        $rows = $connection->exec_SELECTgetRows('uid', 'be_users', $whereClause);

        $result = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $result[] = $row['uid'];
            }
        }
        return $result;
    }

    /**
     * Find uids of styleguide static data records
     *
     * @return array List of uids
     */
    public function findUidsOfStaticdata()
    {
        $pageUid = $this->findPidOfMainTableRecord('tx_styleguide_staticdata');
        /** @var DatabaseConnection $connection */
        $connection = GeneralUtility::makeInstance(DatabaseConnection::class);
        /** @var PageRepository $pageRepository */
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);

        $whereClause = 'pid=' . $pageUid . 'AND ' . $pageRepository->enableFields('tx_styleguide_staticdata');
        $rows = $connection->exec_SELECTgetRows('uid', 'tx_styleguide_staticdata', $whereClause);

        $result = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $result[] = $row['uid'];
            }
        }
        return $result;
    }

    /**
     * Find the object representation of the demo images in fileadmin/styleguide
     *
     * @return \TYPO3\CMS\Core\Resource\File[]
     */
    public function findDemoFileObjects()
    {
        /** @var StorageRepository $storageRepository */
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        $storage = $storageRepository->findByUid(1);
        $folder = $storage->getRootLevelFolder();
        $folder = $folder->getSubfolder('styleguide');
        return $folder->getFiles();
    }

    /**
     * Find the demo folder
     *
     * @return Folder
     */
    public function findDemoFolderObject()
    {
        /** @var StorageRepository $storageRepository */
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        $storage = $storageRepository->findByUid(1);
        $folder = $storage->getRootLevelFolder();
        return $folder->getSubfolder('styleguide');
    }
}
