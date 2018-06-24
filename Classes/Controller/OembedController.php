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
use Sto\Mediaoembed\Domain\Repository\ProviderRepository;
use Sto\Mediaoembed\Exception\InvalidUrlException;
use Sto\Mediaoembed\Exception\OEmbedException;
use Sto\Mediaoembed\Exception\RequestException;
use Sto\Mediaoembed\Request\HttpRequest;
use Sto\Mediaoembed\Request\ProviderResolver;
use Sto\Mediaoembed\Response\ResponseBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

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
        } catch (OEmbedException $exception) {
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
        $this->startRequestLoop();
    }

    /**
     * Checks if the current URL is valid
     *
     * @param string $url
     * @throws \Sto\Mediaoembed\Exception\InvalidUrlException
     */
    private function checkIfUrlIsValid(string $url)
    {
        $isValid = true;

        if (empty($url)) {
            $isValid = false;
        }

        if (!GeneralUtility::isValidUrl($url)) {
            $isValid = false;
        }

        if (!$isValid) {
            throw new InvalidUrlException($url);
        }
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

        while ($provider = $providerResolver->getNextMatchingProvider($url)) {
            $request = new HttpRequest($this->configuration, $provider->getEndpoint());

            try {
                $responseData = $request->sendAndGetResponseData();
                $response = $this->responseBuilder->buildResponse($responseData);
                break;
            } catch (RequestException $exception) {
                // @TODO record all exceptions and provide that information to the user
                $response = null;
            }
        }

        if ($response === null) {
            throw new RequestException(
                'No provider returned a valid result. Giving up.'
                . ' Please make sure the URL is valid and you have configured a provider that can handle it.'
            );
        }

        $this->view->assign('provider', $provider);
        $this->view->assign('request', $request);
        $this->view->assign('response', $response);
    }
}
