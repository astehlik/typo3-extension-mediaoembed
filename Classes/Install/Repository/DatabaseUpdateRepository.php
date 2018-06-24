<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Install\Repository;

class DatabaseUpdateRepository extends AbstractUpdateRepository implements UpdateRepository
{
    public function countOldRecords(): int
    {
        $db = $this->getDatabaseConnection();
        $res = $db->exec_SELECTquery(
            'uid',
            'tt_content',
            $this->getWhereForRecordsThatNeedUpgrading()
        );
        return (int)$db->sql_num_rows($res);
    }

    public function executeUpdateQuery(int $contentUid, array $updateData, array &$dbQueries): string
    {
        $updateQuery = $this->getUpdateQuery($contentUid, $updateData);
        $dbQueries[] = $updateQuery;

        $this->getDatabaseConnection()->query($updateQuery);
        return $this->getDatabaseConnection()->sql_error();
    }

    public function fetchResultRow($result): array
    {
        return $this->getDatabaseConnection()->sql_fetch_assoc($result);
    }

    public function findAllRecordsThatNeedUpgrading()
    {
        return $res = $this->getDatabaseConnection()->exec_SELECTquery(
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
