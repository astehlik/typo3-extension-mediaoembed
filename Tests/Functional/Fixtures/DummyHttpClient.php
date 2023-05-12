<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Functional\Fixtures;

use Sto\Mediaoembed\Exception\HttpClientRequestException;
use Sto\Mediaoembed\Request\HttpClient\HttpClientInterface;

class DummyHttpClient implements HttpClientInterface
{
    public function executeGetRequest(string $requestUrl): string
    {
        if (strpos(strtolower($requestUrl), 'youtube') !== false) {
            return file_get_contents(__DIR__ . '/Provider/YouTube.json');
        }

        throw new HttpClientRequestException('No dummy handling for URL available: ' . $requestUrl, 500);
    }
}
