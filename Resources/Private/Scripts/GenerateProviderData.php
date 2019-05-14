<?php

use Sto\Mediaoembed\Provider\EndpointCollector;
use Sto\Mediaoembed\Provider\ProviderEndpoints;
use Sto\Mediaoembed\Provider\ProviderTypoScriptRenderer;
use Sto\Mediaoembed\Provider\ProviderUrls;

require __DIR__ . '/../../../.Build/vendor/autoload.php';

$providerUrls = new ProviderUrls();
$providerEndpoints = new ProviderEndpoints();

$providerCommandController = new EndpointCollector($providerEndpoints, $providerUrls);
$endpoints = $providerCommandController->collectEndpoints();

$providerTypoScriptRenderer = new ProviderTypoScriptRenderer();
echo $providerTypoScriptRenderer->render($endpoints);
