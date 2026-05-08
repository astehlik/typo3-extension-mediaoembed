<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class LocalizationService
{
    /**
     * @param non-empty-string $key
     */
    public function translate(string $key, array $arguments = []): string
    {
        return (string)LocalizationUtility::translate($key, 'Mediaoembed', $arguments);
    }
}
