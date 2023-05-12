<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Domain\Repository\ProviderRepository;
use Sto\Mediaoembed\Exception\InvalidUrlException;
use Sto\Mediaoembed\Exception\NoMatchingProviderException;
use Sto\Mediaoembed\Exception\OEmbedException;
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
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller for rendering oEmbed media
 */
class OembedController extends ActionController
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ProviderRepository
     */
    private $providerRepository;

    /**
     * @var ResponseBuilder
     */
    private $responseBuilder;

    public function injectConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function injectProviderRepository(ProviderRepository $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    public function injectResponseBuilder(ResponseBuilder $responseBuilder)
    {
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * Renders the external media
     *
     * @return string
     */
    public function renderMediaAction()
    {
        try {
            $this->getEmbedDataFromProvider();
            $this->view->assign('configuration', $this->configuration);
            $this->view->assign('isSSLRequest', GeneralUtility::getIndpEnv('TYPO3_SSL'));
            $result = $this->view->render();
        } catch (InvalidUrlException $invalidUrlException) {
            $result = $this->renderErrorMessage('error_message_invalid_url', [$invalidUrlException->getUrl()]);
        } catch (OEmbedException $exception) {
            $result = $this->renderErrorMessage('error_message_unknown', [$exception->getMessage()]);
        }

        return $result;
    }

    /**
     * Build all data for the register using the embed code reponse
     * of a matching provider.
     */
    protected function getEmbedDataFromProvider()
    {
        $this->startRequestLoop();
    }

    /**
     * Checks if the current URL is valid
     *
     * @param string $url
     * @throws InvalidUrlException
     */
    private function checkIfUrlIsValid(string $url)
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

    /**
     * @param ProviderResolver $providerResolver
     * @param string $url
     * @return Provider|null
     */
    private function getNextMatchingProvider(ProviderResolver $providerResolver, string $url)
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
            return $this->objectManager->get(HttpRequestHandler::class);
        }

        if (!class_exists($requestHandlerClass)) {
            throw new RequestHandlerClassDoesNotExistsException($provider);
        }

        $requestHandler = $this->objectManager->get($requestHandlerClass);
        if (!$requestHandler instanceof RequestHandlerInterface) {
            throw new RequestHandlerClassInvalidException($provider);
        }

        return $requestHandler;
    }

    private function getResponseDataForProvider(Provider $provider): array
    {
        $requestHandler = $this->getRequestHandlerForProvider($provider);
        return $requestHandler->handle($provider);
    }

    private function processResponse(Provider $provider, GenericResponse $response)
    {
        foreach ($provider->getProcessors() as $processorClass) {
            /** @var ResponseProcessorInterface $processor */
            $processor = $this->objectManager->get($processorClass);
            $processor->processResponse($response);
        }
        $this->processResponseWithHtml($response);
    }

    private function processResponseWithHtml(GenericResponse $response)
    {
        if (!$response instanceof HtmlAwareResponseInterface) {
            return;
        }

        foreach ($this->configuration->getProcessorsForHtml() as $htmlProcessorClass) {
            /** @var HtmlResponseProcessorInterface $processor */
            $processor = $this->objectManager->get($htmlProcessorClass);
            $processor->processHtmlResponse($response);
        }
    }

    private function renderErrorMessage(string $translationKey, array $arguments): string
    {
        $message = $this->translate($translationKey, $arguments);
        return '<div class="alert alert-warning">' . htmlspecialchars($message) . '</div>';
    }

    /**
     * @param Provider|null $provider
     * @return bool
     */
    private function shouldDisplayDirectLink($provider): bool
    {
        if (!$this->settings['view']['displayDirectLink']) {
            return false;
        }
        if (!$provider) {
            return true;
        }
        return $provider->shouldDirectLinkBeDisplayed();
    }

    /**
     * Loops over all mathing providers and all their endpoint
     * until the request was successful or no more providers / endpoints
     * are available.
     */
    private function startRequestLoop()
    {
        $response = null;
        $request = null;

        $url = $this->configuration->getMediaUrl();
        $this->checkIfUrlIsValid($url);

        $providerResolver = new ProviderResolver($this->providerRepository->findAll());

        $providerExceptions = [];

        while ($provider = $this->getNextMatchingProvider($providerResolver, $url)) {
            try {
                $responseData = $this->getResponseDataForProvider($provider);
                $response = $this->responseBuilder->buildResponse($url, $responseData);
                $this->processResponse($provider, $response);
                break;
            } catch (RequestException $exception) {
                $providerExceptions[] = [
                    'provider' => $provider,
                    'exception' => $exception,
                ];
                $response = null;
            }
        }

        if ($response === null) {
            $this->view->assign('hasErrors', true);
            $this->view->assign('providerExceptions', $providerExceptions);
            return;
        }

        $this->view->assign('request', $request);
        $this->view->assign('provider', $provider);
        $this->view->assign('displayDirectLink', $this->shouldDisplayDirectLink($provider));
        $this->view->assign('response', $response);
    }

    private function translate(string $key, $arguments = null)
    {
        return LocalizationUtility::translate($key, 'Mediaoembed', $arguments);
    }
}
