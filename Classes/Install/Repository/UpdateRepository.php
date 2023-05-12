<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Install\Repository;

interface UpdateRepository
{
    public const RENDER_TYPE = 'tx_mediaoembed';

    public function countOldRecords(): int;

    public function executeUpdateQuery(int $contentUid, array $updateData, array &$dbQueries): string;

    public function fetchResultRow($result);

    public function findAllRecordsThatNeedUpgrading();
}
