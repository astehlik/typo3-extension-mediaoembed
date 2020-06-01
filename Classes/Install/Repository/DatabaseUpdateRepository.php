<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Install\Repository;

use TYPO3\CMS\Core\Database\DatabaseConnection;

class DatabaseUpdateRepository extends AbstractUpdateRepository implements UpdateRepository
{
    public function countOldRecords(): int
    {
        $connection = $this->getDatabaseConnection();
        $res = $connection->exec_SELECTquery(
            'uid',
            'tt_content',
            $this->getWhereForRecordsThatNeedUpgrading()
        );
        return (int)$connection->sql_num_rows($res);
    }

    public function executeUpdateQuery(int $contentUid, array $updateData, array &$dbQueries): string
    {
        $updateQuery = $this->getUpdateQuery($contentUid, $updateData);
        $dbQueries[] = $updateQuery;

        $this->getDatabaseConnection()->sql_query($updateQuery);
        return $this->getDatabaseConnection()->sql_error();
    }

    public function fetchResultRow($result)
    {
        return $this->getDatabaseConnection()->sql_fetch_assoc($result);
    }

    public function findAllRecordsThatNeedUpgrading()
    {
        return $this->getDatabaseConnection()->exec_SELECTquery(
            'uid, pi_flexform',
            'tt_content',
            $this->getWhereForRecordsThatNeedUpgrading()
        );
    }

    private function getDatabaseConnection(): DatabaseConnection
    {
        return $GLOBALS['TYPO3_DB'];
    }

    private function getUpdateQuery(int $contentUid, array $updateData): string
    {
        return $this->getDatabaseConnection()->UPDATEquery('tt_content', 'uid=' . $contentUid, $updateData);
    }
}
