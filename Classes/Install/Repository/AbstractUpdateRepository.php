<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Install\Repository;

class AbstractUpdateRepository
{
    protected function getWhereForRecordsThatNeedUpgrading(): string
    {
        return 'CType=\'media\' AND pi_flexform LIKE \'%<field index="mmRenderType">%<value index="vDEF">'
            . UpdateRepository::RENDER_TYPE
            . '</value>%\'';
    }
}
