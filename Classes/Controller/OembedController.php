<?php
namespace Sto\Mediaoembed\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller for rendering oEmbed media
 */
class OembedController extends ActionController {

	/**
	 * Current TypoScript / Flexform configuration
	 *
	 * @var \Sto\Mediaoembed\Content\Configuration
	 */
	protected $configuration;

	/**
	 * The provider resolver tries to resolve the matching provider
	 * for the current media URL.
	 *
	 * @var \Sto\Mediaoembed\Request\ProviderResolver
	 */
	protected $providerResolver;

	/**
	 * @var \Sto\Mediaoembed\Content\RegisterData
	 */
	protected $registerData;

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

	/**
	 * Renders the external media
	 *
	 * @return string
	 */
	public function renderMediaAction() {

		$this->configuration = $this->objectManager->get('Sto\\Mediaoembed\\Content\\Configuration');
		$this->registerData = $this->objectManager->get('Sto\\Mediaoembed\\Content\\RegisterData');

		try {
			$this->getEmbedDataFromProvider();
			return $this->setRegisterAndRenderCobj();
		} catch (\Sto\Mediaoembed\Exception\OEmbedException $exception) {
			return 'Error: ' . $exception->getMessage();
		}
	}

	/**
	 * Build all data for the register using the embed code reponse
	 * of a matching provider.
	 */
	protected function getEmbedDataFromProvider() {

		$this->providerResolver = $this->objectManager->get('Sto\\Mediaoembed\\Request\\ProviderResolver');
		$this->providerResolver->setConfiguration($this->configuration);
		$this->initializeRequestBuilder();
		$this->initializeResponseBuilder();

		$this->startRequestLoop();
	}

	/**
	 * Initializes the request builder
	 */
	protected function initializeRequestBuilder() {
		$this->requestBuilder = $this->objectManager->get('Sto\\Mediaoembed\\Request\\RequestBuilder');
		$this->requestBuilder->setConfiguration($this->configuration);
	}

	/**
	 * Initializes the response builder
	 */
	protected function initializeResponseBuilder() {
		$this->responseBuilder = $this->objectManager->get('Sto\\Mediaoembed\\Response\\ResponseBuilder');
	}

	/**
	 * Loops over all mathing providers and all their endpoint
	 * until the request was successful or no more providers / endpoints
	 * are available.
	 *
	 * @throws \Sto\Mediaoembed\Exception\RequestException
	 * @return \Sto\Mediaoembed\Response\GenericResponse A response object initialized with the data the provider returned
	 */
	protected function startRequestLoop() {

		$response = NULL;
		$request = NULL;

		do {

			/**
			 * @var \Sto\Mediaoembed\Request\Provider $provider
			 */
			$provider = $this->providerResolver->getNextMatchingProvider();

			if ($provider === FALSE) {
				break;
			}

			do {

				$request = $this->requestBuilder->buildNextRequest($provider);

				if ($request === FALSE) {
					break;
				}

				try {
					$responseData = $request->sendAndGetResponseData();
					$response = $this->responseBuilder->buildResponse($responseData);
				} catch (\Sto\Mediaoembed\Exception\RequestException $exception) {
					// @TODO record all exceptions and provide that information to the user
					$response = NULL;
				}

				$request = $this->requestBuilder->buildNextRequest($provider);

			} while ($response === NULL);

		} while ($response === NULL);

		if ($response === NULL) {
			throw new \Sto\Mediaoembed\Exception\RequestException('No provider returned a valid result. Giving up. Please make sure the URL is valid and you have configured a provider that can handle it.');
		}

		$this->registerData->setProvider($provider);
		$this->registerData->setRequest($request);
		$this->registerData->setResponse($response);
	}

	/**
	 * Renders the renderItem and provides the oembed information in
	 * a register during the rendering process.
	 *
	 * @return string
	 */
	protected function setRegisterAndRenderCobj() {

		array_push($GLOBALS['TSFE']->registerStack, $GLOBALS['TSFE']->register);
		$GLOBALS['TSFE']->register['tx_mediaoembed'] = $this->registerData;

		$content = $this->configurationManager->getContentObject()->cObjGetSingle(
			$this->configuration->getRenderItem(),
			$this->configuration->getRenderItemConfig()
		);

		$this->configurationManager->getContentObject()->LOAD_REGISTER(array(), 'RESTORE_REGISTER');

		return $content;
	}
}