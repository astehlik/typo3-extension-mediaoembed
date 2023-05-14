<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\StringUtility;

class UtilityService implements SingletonInterface
{
    public function getUniqueId(string $prefix): string
    {
        return StringUtility::getUniqueId($prefix);
    }
}
