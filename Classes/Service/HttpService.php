<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class HttpService
{
    public function getUrl(string $uri): ResponseInterface
    {
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        return $requestFactory->request($uri);
    }
}
