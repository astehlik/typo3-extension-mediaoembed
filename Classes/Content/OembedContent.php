<?php
namespace Sto\Mediaoembed\Content;

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
 */
class OembedContent extends \TYPO3\CMS\Frontend\ContentObject\AbstractContentObject {

	/**
	 * Current TypoScript / Flexform configuration
	 *
	 * @var Configuration
	 */
	protected $configuration;

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\MediaContentObject
	 */
	protected $parentContent;

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

	/**
	 * @var RegisterData
	 */
	protected $registerData;

	/**
	 * Injects the parent content object
	 *
	 * @param \TYPO3\CMS\Frontend\ContentObject\MediaContentObject $parentContent
	 */
	public function injectParentContent($parentContent) {
		$this->parentContent = $parentContent;
	}

	/**
	 * Initializes the provider resolver
	 */
	protected function initializeProviderResolver() {
		$this->providerResolver = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sto\\Mediaoembed\\Request\\ProviderResolver');
		$this->providerResolver->injectCObj($this->cObj);
		$this->providerResolver->injectConfiguration($this->configuration);
	}

	/**
	 * Initializes the request builder
	 */
	protected function initializeRequestBuilder() {
		$this->requestBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sto\\Mediaoembed\\Request\\RequestBuilder');
		$this->requestBuilder->injectConfiguration($this->configuration);
	}

	/**
	 * Initializes the response builder
	 */
	protected function initializeResponseBuilder() {
		$this->responseBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sto\\Mediaoembed\\Response\\ResponseBuilder');
	}

	/**
	 * Renders the oembed media item
	 *
	 * @param array $conf Current TypoScript / Flexform configuration
	 * @return string
	 */
	public function render($conf = array()) {

		$this->configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sto\\Mediaoembed\\Content\\Configuration', $conf);
		$this->registerData = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sto\\Mediaoembed\\Content\\RegisterData', $this->configuration);

		try {
			$this->getEmbedDataFromProvider();
			return $this->setRegisterAndRenderCobj();
		}
		catch(\Sto\Mediaoembed\Exception\OEmbedException $exception) {
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
	 * @return string
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
}
?>