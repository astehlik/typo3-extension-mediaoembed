<?php

declare(strict_types=1);

use Sto\Mediaoembed\Content\ConfigurationFactory;
use Sto\Mediaoembed\Response\ResponseBuilder;
use Sto\Mediaoembed\Service\AspectRatioCalculator;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $configurator->services()
        ->defaults()->autowire()->autoconfigure()->public()

        ->set(ConfigurationFactory::class, ConfigurationFactory::class)
        ->load('Sto\\Mediaoembed\\Controller\\', __DIR__ . '/../Classes/Controller/')
        ->load('Sto\\Mediaoembed\\Domain\\Repository\\', __DIR__ . '/../Classes/Domain/Repository/')
        ->load('Sto\\Mediaoembed\\Request\\HttpClient\\', __DIR__ . '/../Classes/Request/HttpClient/')
        ->load('Sto\\Mediaoembed\\Request\\RequestHandler\\', __DIR__ . '/../Classes/Request/RequestHandler/')
        ->load('Sto\\Mediaoembed\\Response\\Processor\\', __DIR__ . '/../Classes/Response/Processor/')
        ->set(ResponseBuilder::class, ResponseBuilder::class)
        ->load('Sto\\Mediaoembed\\Service\\', __DIR__ . '/../Classes/Service/')
        ->set(AspectRatioCalculatorInterface::class, AspectRatioCalculator::class)
        ->load('Sto\\Mediaoembed\\ViewHelpers\\', __DIR__ . '/../Classes/ViewHelpers/');
};
