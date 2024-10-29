<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\HttpClient;

use GuzzleHttp\Exception\RequestException;
use Sto\Mediaoembed\Exception\HttpClientRequestException;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GetUrlHttpClient implements HttpClientInterface
{
    public function executeGetRequest(string $requestUrl): string
    {
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);

        try {
            $response = $requestFactory->request($requestUrl);
        } catch (RequestException $exception) {
            throw new HttpClientRequestException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception,
            );
        }

        return $response->getBody()->getContents();
    }
}
