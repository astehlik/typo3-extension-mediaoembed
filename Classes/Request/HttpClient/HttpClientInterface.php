<?php

namespace Sto\Mediaoembed\Request\HttpClient;

use Sto\Mediaoembed\Exception\HttpClientRequestException;

interface HttpClientInterface
{
    /**
     * @param string $requestUrl
     * @return string
     * @throws HttpClientRequestException
     */
    public function executeGetRequest(string $requestUrl): string;
}
