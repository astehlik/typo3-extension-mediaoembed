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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller for rendering oEmbed media
 */
class OembedController extends ActionController
{
    /**
     * Current TypoScript / Flexform configuration
     *
     * @var \Sto\Mediaoembed\Content\Configuration
     */
    protected $configuration;

    /**
     * @var \Sto\Mediaoembed\Domain\Repository\ContentRepository
     * */
    protected $contentRepository;

    /**
     * The provider resolver tries to resolve the matching provider
     * for the current media URL.
     *
     * @var \Sto\Mediaoembed\Request\ProviderResolver
     */
    protected $providerResolver;

    /**
     * Request builder for creating a request to a given endpoint.
     *
     * @var \Sto\Mediaoembed\Request\RequestBuilder
     */
    protected $requestBuilder;

    /**
     * Tries to build a reponse object using the reponse that came from the server.
     *
     * @var \Sto\Mediaoembed\Response\ResponseBuilder
     */
    protected $responseBuilder;

    public function injectContentRepository(\Sto\Mediaoembed\Domain\Repository\ContentRepository $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

    /**
     * Renders the external media
     *
     * @return string
     */
    public function renderMediaAction()
    {
        $this->configuration = $this->objectManager->get(\Sto\Mediaoembed\Content\Configuration::class);

        try {
            $this->getEmbedDataFromProvider();
            $this->view->assign('configuration', $this->configuration);
            $this->view->assign('isSSLRequest', GeneralUtility::getIndpEnv('TYPO3_SSL'));
            $result = $this->view->render();
        } catch (\Sto\Mediaoembed\Exception\OEmbedException $exception) {
            $result = 'Error: ' . $exception->getMessage();
        }

        return $result;
    }

    /**
     * Build all data for the register using the embed code reponse
     * of a matching provider.
     */
    protected function getEmbedDataFromProvider()
    {
        $this->providerResolver = $this->objectManager->get(\Sto\Mediaoembed\Request\ProviderResolver::class);
        $this->initializeRequestBuilder();
        $this->initializeResponseBuilder();

        $content = $this->contentRepository->buildFromContentObjectData(
            $this->configurationManager->getContentObject()->data
        );
        $this->startRequestLoop($content);
    }

    /**
     * Initializes the request builder
     */
    protected function initializeRequestBuilder()
    {
        $this->requestBuilder = $this->objectManager->get(\Sto\Mediaoembed\Request\RequestBuilder::class);
        $this->requestBuilder->setConfiguration($this->configuration);
    }

    /**
     * Initializes the response builder
     */
    protected function initializeResponseBuilder()
    {
        $this->responseBuilder = $this->objectManager->get(\Sto\Mediaoembed\Response\ResponseBuilder::class);
    }

    /**
     * Loops over all mathing providers and all their endpoint
     * until the request was successful or no more providers / endpoints
     * are available.
     *
     * @param \Sto\Mediaoembed\Domain\Model\Content $content
     * @throws \Sto\Mediaoembed\Exception\RequestException
     */
    protected function startRequestLoop($content)
    {
        $response = null;
        $request = null;

        do {
            $provider = $this->providerResolver->getNextMatchingProvider($content);

            if ($provider === false) {
                break;
            }

            do {
                $request = $this->requestBuilder->buildNextRequest($provider);

                if ($request === false) {
                    break;
                }

                try {
                    $responseData = $request->sendAndGetResponseData();
                    $response = $this->responseBuilder->buildResponse($responseData);
                } catch (\Sto\Mediaoembed\Exception\RequestException $exception) {
                    // @TODO record all exceptions and provide that information to the user
                    $response = null;
                }

                $request = $this->requestBuilder->buildNextRequest($provider);
            } while ($response === null);
        } while ($response === null);

        if ($response === null) {
            throw new \Sto\Mediaoembed\Exception\RequestException(
                'No provider returned a valid result. Giving up.'
                . ' Please make sure the URL is valid and you have configured a provider that can handle it.'
            );
        }

        $this->view->assign('provider', $provider);
        $this->view->assign('request', $request);
        $this->view->assign('response', $response);
    }
}
