<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Install\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use PDO;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DoctrineUpdateRepository extends AbstractUpdateRepository implements UpdateRepository
{
    public function countOldRecords(): int
    {
        $builder = $this->getDatabaseConnection()->createQueryBuilder();
        $builder->count('uid');
        $builder->from('tt_content');
        $builder->where($this->getWhereForRecordsThatNeedUpgrading());
        $result = $builder->execute();
        return (int)$result->fetchColumn(0);
    }

    public function executeUpdateQuery(int $contentUid, array $updateData, array &$dbQueries): string
    {
        $updateQuery = $this->getUpdateQuery($contentUid, $updateData);
        $dbQueries[] = $updateQuery->getSQL();

        try {
            $updateQuery->execute();
            return '';
        } catch (DBALException $exception) {
            return $exception->getMessage();
        }
    }

    public function fetchResultRow($result)
    {
        if (!$result instanceof \Doctrine\DBAL\Driver\Statement) {
            throw new \InvalidArgumentException('This method only supports doctrine results.');
        }

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function findAllRecordsThatNeedUpgrading()
    {
        $builder = $this->getDatabaseConnection()->createQueryBuilder();
        $builder->select('*');
        $builder->from('tt_content');
        $builder->where($this->getWhereForRecordsThatNeedUpgrading());
        return $builder->execute();
    }

    private function getDatabaseConnection(): Connection
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool->getConnectionForTable('tt_content');
    }

    private function getUpdateQuery(int $contentUid, array $updateData): QueryBuilder
    {
        $builder = $this->getDatabaseConnection()->createQueryBuilder();
        $builder->update('tt_content');
        $builder->where(
            $builder->expr()->eq(
                'uid',
                $builder->createNamedParameter($contentUid, PDO::PARAM_INT)
            )
        );

        foreach ($updateData as $column => $value) {
            $builder->set($column, $value);
        }

        return $builder;
    }
}
