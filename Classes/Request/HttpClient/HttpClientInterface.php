<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\HttpClient;

use Sto\Mediaoembed\Exception\HttpClientRequestException;

interface HttpClientInterface
{
    /**
     * @throws HttpClientRequestException
     */
    public function executeGetRequest(string $requestUrl): string;
}
