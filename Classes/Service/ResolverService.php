<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use Psr\Container\ContainerInterface;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Domain\Model\ResolverResult;
use Sto\Mediaoembed\Domain\Repository\ProviderRepository;
use Sto\Mediaoembed\Exception\InvalidUrlException;
use Sto\Mediaoembed\Exception\NoMatchingProviderException;
use Sto\Mediaoembed\Exception\ProviderRequestException;
use Sto\Mediaoembed\Exception\ProviderResolveFailedException;
use Sto\Mediaoembed\Exception\RequestException;
use Sto\Mediaoembed\Exception\RequestHandler\RequestHandlerClassDoesNotExistsException;
use Sto\Mediaoembed\Exception\RequestHandler\RequestHandlerClassInvalidException;
use Sto\Mediaoembed\Request\ProviderResolver;
use Sto\Mediaoembed\Request\RequestHandler\HttpRequestHandler;
use Sto\Mediaoembed\Request\RequestHandler\RequestHandlerInterface;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Response\HtmlAwareResponseInterface;
use Sto\Mediaoembed\Response\Processor\HtmlResponseProcessorInterface;
use Sto\Mediaoembed\Response\Processor\ResponseProcessorInterface;
use Sto\Mediaoembed\Response\ResponseBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ResolverService
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly ProviderRepository $providerRepository,
        private readonly ResponseBuilder $responseBuilder
    ) {
    }

    public function resolve(Configuration $configuration): ResolverResult
    {
        $response = null;

        $url = $configuration->getMediaUrl();
        $this->checkIfUrlIsValid($url);

        $providers = $this->providerRepository->getFromConfig($configuration);
        $providerResolver = new ProviderResolver($providers);

        $providerExceptions = [];

        while ($provider = $this->getNextMatchingProvider($providerResolver, $url)) {
            try {
                $responseData = $this->getResponseDataForProvider($provider, $configuration);
                $response = $this->responseBuilder->buildResponse($responseData, $configuration);
                $this->processResponse($provider, $response);
                break;
            } catch (RequestException $exception) {
                $providerExceptions[] = new ProviderRequestException(
                    $provider,
                    $exception
                );
                $response = null;
            }
        }

        if ($response === null) {
            throw new ProviderResolveFailedException(...$providerExceptions);
        }

        return new ResolverResult($response, $provider);
    }

    private function checkIfUrlIsValid(string $url): void
    {
        $isValid = true;

        if (empty($url)) {
            $isValid = false;
        }

        if ($isValid && !GeneralUtility::isValidUrl($url)) {
            $isValid = false;
        }

        if (!$isValid) {
            throw new InvalidUrlException($url);
        }
    }

    private function getNextMatchingProvider(ProviderResolver $providerResolver, string $url): ?Provider
    {
        try {
            return $providerResolver->getNextMatchingProvider($url);
        } catch (NoMatchingProviderException $e) {
            return null;
        }
    }

    private function getRequestHandlerForProvider(Provider $provider): RequestHandlerInterface
    {
        $requestHandlerClass = $provider->getRequestHandlerClass();

        if (!$requestHandlerClass) {
            return $this->container->get(HttpRequestHandler::class);
        }

        if (!class_exists($requestHandlerClass)) {
            throw new RequestHandlerClassDoesNotExistsException($provider);
        }

        $requestHandler = $this->container->get($requestHandlerClass);
        if (!$requestHandler instanceof RequestHandlerInterface) {
            throw new RequestHandlerClassInvalidException($provider);
        }

        return $requestHandler;
    }

    private function getResponseDataForProvider(Provider $provider, Configuration $configuration): array
    {
        $requestHandler = $this->getRequestHandlerForProvider($provider);
        return $requestHandler->handle($provider, $configuration);
    }

    private function processResponse(Provider $provider, GenericResponse $response): void
    {
        foreach ($provider->getProcessors() as $processorClass) {
            /** @var ResponseProcessorInterface $processor */
            $processor = $this->container->get($processorClass);
            $processor->processResponse($response);
        }

        $this->processResponseWithHtml($response);
    }

    private function processResponseWithHtml(GenericResponse $response): void
    {
        if (!$response instanceof HtmlAwareResponseInterface) {
            return;
        }

        foreach ($response->getConfiguration()->getProcessorsForHtml() as $htmlProcessorClass) {
            /** @var HtmlResponseProcessorInterface $processor */
            $processor = $this->container->get($htmlProcessorClass);
            $processor->processHtmlResponse($response);
        }
    }
}
