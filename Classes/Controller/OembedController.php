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

use Psr\Http\Message\ResponseInterface;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Content\ConfigurationFactory;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Exception\InvalidUrlException;
use Sto\Mediaoembed\Exception\OEmbedException;
use Sto\Mediaoembed\Exception\ProviderResolveFailedException;
use Sto\Mediaoembed\Service\ResolverService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Controller for rendering oEmbed media.
 */
class OembedController extends ActionController
{
    public function __construct(
        private readonly ConfigurationFactory $configurationFactory,
        private readonly ResolverService $responseResolver
    ) {
    }

    /**
     * Renders the external media.
     */
    public function renderMediaAction(): ResponseInterface
    {
        try {
            $configuration = $this->configurationFactory->createConfiguration(
                $this->getCurrentContentObject()->data,
                $this->settings
            );
            $this->getEmbedDataFromProvider($configuration);
            $this->view->assign('configuration', $configuration);
            $this->view->assign('isSSLRequest', GeneralUtility::getIndpEnv('TYPO3_SSL'));
            $result = $this->view->render();
        } catch (InvalidUrlException $invalidUrlException) {
            $result = $this->renderErrorMessage('error_message_invalid_url', [$invalidUrlException->getUrl()]);
        } catch (OEmbedException $exception) {
            $result = $this->renderErrorMessage('error_message_unknown', [$exception->getMessage()]);
        }

        return $this->htmlResponse($result);
    }

    /**
     * Build all data for the register using the embed code reponse
     * of a matching provider.
     */
    protected function getEmbedDataFromProvider(Configuration $configuration): void
    {
        $this->startRequestLoop($configuration);
    }

    private function getCurrentContentObject(): ContentObjectRenderer
    {
        return $this->request->getAttribute('currentContentObject');
    }

    private function renderErrorMessage(string $translationKey, array $arguments): string
    {
        $message = $this->translate($translationKey, $arguments);
        return '<div class="alert alert-warning">' . htmlspecialchars($message) . '</div>';
    }

    private function shouldDisplayDirectLink(?Provider $provider): bool
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
    private function startRequestLoop(Configuration $configuration): void
    {
        try {
            $resolverResult = $this->responseResolver->resolve($configuration);
        } catch (ProviderResolveFailedException $e) {
            $this->view->assign('hasErrors', true);
            $this->view->assign('providerExceptions', $e->getExceptions());
            return;
        }

        $this->view->assign('provider', $resolverResult->getProvider());
        $this->view->assign('displayDirectLink', $this->shouldDisplayDirectLink($resolverResult->getProvider()));
        $this->view->assign('response', $resolverResult->getResponse());
    }

    private function translate(string $key, $arguments = null): string
    {
        return (string)LocalizationUtility::translate($key, 'Mediaoembed', $arguments);
    }
}
