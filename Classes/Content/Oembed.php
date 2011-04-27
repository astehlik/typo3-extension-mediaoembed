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

	protected function initializeProviderResolver() {
		$this->providerResolver = t3lib_div::makeInstance('Tx_Mediaoembed_Request_ProviderResolver');
		$this->providerResolver->injectCObj($this->cObj);
		$this->providerResolver->injectConfiguration($this->configuration);
	}

	protected function initializeResponseBuilder() {
		$this->requestBuilder = t3lib_div::makeInstance('Tx_Mediaoembed_Request_RequestBuilder');
		$this->requestBuilder->injectConfiguration($this->configuration);
	}

	/**
	 * Renders the oembed media item
	 *
	 * @param array $conf Current TypoScript / Flexform configuration
	 */
	public function render($conf) {

		$this->configuration = t3lib_div::makeInstance('Tx_Mediaoembed_Content_Configuration', $conf);
		$this->registerData = t3lib_div::makeInstance('Tx_Mediaoembed_Content_RegisterData');

		try {
			$this->getEmbedDataFromProvider();
			return $this->setRegisterAndRenderCobj();
		}
		catch(Tx_Mediaoembed_Exception_OEmbedException $exception) {
			return 'Error: ' . $exception->getMessage();
		}

	}

	protected function getEmbedDataFromProvider() {

		$this->initializeProviderResolver();

		$requestSuccessful = FALSE;
		$provider = $this->providerResolver->getNextMatchingProviderData();
		while (!$requestSuccessful && ($provider !== FALSE)) {

			$request = $this->requestBuilder->buildNextRequest($provider);
			while (!$requestSuccessful && ($request !== FALSE)) {

				try {
					$responseData = $request->sendAndGetResponseData();
					$requestSuccessful = TRUE;
				} catch (Tx_Mediaoembed_Exception_RequestException $exception) {
					// @TODO record all exceptions and provide that information to the user
				}

				$request = $this->requestBuilder->buildNextRequest($provider);
			}

			$provider = $this->providerResolver->getNextMatchingProviderData();
		}

		$responseBuilder = t3lib_div::makeInstance('Tx_Mediaoembed_Response_ResponseBuilder');
		$response = $responseBuilder->buildResponse($responseData);

		if ($response->getType() === 'photo') {

			$imageData = t3lib_div::getURL($response['url']);

			$imageFilename = basename($response['url']);
			$imageFilename = preg_replace('/[^a-z0-9\._-]/i', '', $imageFilename);
			$imagePrefix = t3lib_div::md5int($imageData);
			$imageFilename = $imagePrefix . '_' . $imageFilename;
			$imagePathAndFilename = 'typo3temp/tx_mediaoembed/' . $imageFilename;

			t3lib_div::writeFileToTypo3tempDir(PATH_site . $imagePathAndFilename, $imageData);
			$response['url_tempimage'] = $imagePathAndFilename;
		}

		$dataForRegister['provider'] = $providerData;
		$dataForRegister['response'] = $response;
		$dataForRegister['request']['url'] = $mediaUrl;

		return $content;
	}

	/**
	 * Renders the renderItem and provides the oembed information in
	 * a register during the rendering process.
	 *
	 * @param array $dataForRegister
	 */
	protected function setRegisterAndRenderCobj($dataForRegister) {

		array_push($GLOBALS['TSFE']->registerStack, $GLOBALS['TSFE']->register);
		$GLOBALS['TSFE']->register['tx_mediaoembed'] = $dataForRegister;

		$content = $this->cObj->cObjGetSingle(
			$this->configuration->getRenderItem(),
			$this->configuration->getRenderItemConf()
		);

		$this->cObj->LOAD_REGISTER(array(), 'RESTORE_REGISTER');

		return $content;
	}

}