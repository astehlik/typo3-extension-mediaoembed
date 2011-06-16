<?php
//declare(ENCODING = 'utf-8');

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

/**
 * Content rendering for oembed media
 *
 * @package mediaoembed
 * @subpackage Content
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Content_Oembed extends tslib_content_Abstract {

	/**
	 * Current TypoScript / Flexform configuration
	 *
	 * @var Tx_Mediaoembed_Content_Configuration
	 */
	protected $configuration;

	/**
	 * @var tslib_content_Media
	 */
	protected $parentContent;

	/**
	 * The provider resolver tries to resolve the matching provider
	 * for the current media URL.
	 *
	 * @var Tx_Mediaoembed_Request_ProviderResolver
	 */
	protected $providerResolver;

	/**
	 * Request builder for creating a request to a given endpoint.
	 *
	 * @var Tx_Mediaoembed_Request_RequestBuilder
	 */
	protected $requestBuilder;

	/**
	 * Tries to build a reponse object using the reponse that came from the server.
	 *
	 * @var Tx_Mediaoembed_Response_ResponseBuilder
	 */
	protected $responseBuilder;

	/**
	 * @var Tx_Mediaoembed_Content_RegisterData
	 */
	protected $registerData;

	/**
	 * Injects the parent content object
	 *
	 * @param tslib_content_Media $parentContent
	 */
	public function injectParentContent($parentContent) {
		$this->parentContent = $parentContent;
	}

	/**
	 * Initializes the provider resolver
	 */
	protected function initializeProviderResolver() {
		$this->providerResolver = t3lib_div::makeInstance('Tx_Mediaoembed_Request_ProviderResolver');
		$this->providerResolver->injectCObj($this->cObj);
		$this->providerResolver->injectConfiguration($this->configuration);
	}

	/**
	 * Initializes the request builder
	 */
	protected function initializeRequestBuilder() {
		$this->requestBuilder = t3lib_div::makeInstance('Tx_Mediaoembed_Request_RequestBuilder');
		$this->requestBuilder->injectConfiguration($this->configuration);
	}

	/**
	 * Initializes the response builder
	 */
	protected function initializeResponseBuilder() {
		$this->responseBuilder = t3lib_div::makeInstance('Tx_Mediaoembed_Response_ResponseBuilder');
	}

	/**
	 * Renders the oembed media item
	 *
	 * @param array $conf Current TypoScript / Flexform configuration
	 */
	public function render($conf = array()) {

		$this->configuration = t3lib_div::makeInstance('Tx_Mediaoembed_Content_Configuration', $conf);
		$this->registerData = t3lib_div::makeInstance('Tx_Mediaoembed_Content_RegisterData', $this->configuration);

		try {
			$this->getEmbedDataFromProvider();
			return $this->setRegisterAndRenderCobj();
		}
		catch(Tx_Mediaoembed_Exception_OEmbedException $exception) {
			return 'Error: ' . $exception->getMessage();
		}

	}

	/**
	 * Build all data for the register using the embed code reponse
	 * of a matching provider.
	 */
	protected function getEmbedDataFromProvider() {

		$this->initializeProviderResolver();
		$this->initializeRequestBuilder();
		$this->initializeResponseBuilder();

		$this->startRequestLoop();
	}

	/**
	 * Renders the renderItem and provides the oembed information in
	 * a register during the rendering process.
	 *
	 * @param array $dataForRegister
	 */
	protected function setRegisterAndRenderCobj() {

		array_push($GLOBALS['TSFE']->registerStack, $GLOBALS['TSFE']->register);
		$GLOBALS['TSFE']->register['tx_mediaoembed'] = $this->registerData;

		$content = $this->cObj->cObjGetSingle(
			$this->configuration->getRenderItem(),
			$this->configuration->getRenderItemConfig()
		);

		$this->cObj->LOAD_REGISTER(array(), 'RESTORE_REGISTER');

		return $content;
	}

	/**
	 * Loops over all mathing providers and all their endpoint
	 * until the request was successful or no more providers / endpoints
	 * are available.
	 *
	 * @return Tx_Mediaoembed_Response_GenericResponse A response object initialized with the data the provider returned
	 * @throws Tx_Mediaoembed_Exception_RequestException If none of the requests returned a vaild result.
	 */
	protected function startRequestLoop() {

		$response = NULL;

		do {

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
				} catch (Tx_Mediaoembed_Exception_RequestException $exception) {
					// @TODO record all exceptions and provide that information to the user
					$response = NULL;
				}

				$request = $this->requestBuilder->buildNextRequest($provider);

			} while ($response === NULL);

		} while ($response === NULL);

		if ($response === NULL) {
			throw new Tx_Mediaoembed_Exception_RequestException('No provider returned a valid result. Giving up. Please make sure the URL is valid and you have configured a provider that can handle it.');
		}

		$this->registerData->setProvider($provider);
		$this->registerData->setRequest($request);
		$this->registerData->setResponse($response);

	}
}
?>