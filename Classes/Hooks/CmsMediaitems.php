<?php

class Tx_Mediaoembed_Hooks_CmsMediaitems {
	
	protected $cObj;
	
	protected $providerResult;
	
	protected $conf;

	protected static $renderType = 'tx_mediaoembed';
	
	/**
	 * Addes oEmbed render type to list of render types
	 * 
	 * @param array $config The current flexform configuration
	 */
	public function customMediaRenderTypes($config) {
		
		$config['items'][] = array('oEmbed', self::$renderType, '');
		
		return $config;
	}
	
	/**
	 * Renders the embed code that was provided by oEmbed provider 
	 *
	 * @param string $renderType
	 * @param array $conf
	 * @param tslib_content_Media $contentObject
	 */
	public function customMediaRender($renderType, $conf, $parentContent) {
		
		//var_dump(Tx_Extbase_Utility_Extension::createAutoloadRegistryForExtension('mediaoembed', t3lib_extMgm::extPath('mediaoembed')));
		//return;
		
		$this->conf = $conf;
		$this->cObj = $parentContent->getCObj();
		
			// @TODO submit TYPO3 patch that we get the current content
			// that was possibly set by other providers!
		$currentContent = '';
		
			// we only render if it is our content type
		if ($renderType !== self::$renderType) {
			return $currentContent;
		}
		
		try {
			$mediaUrl = $conf['file'];
			$providerResolver = t3lib_div::makeInstance('Tx_Mediaoembed_Request_ProviderResolver', $this->cObj);
			$providerData = $providerResolver->getMatchingProviderData($mediaUrl);
			
			$requestBuilder = t3lib_div::makeInstance('Tx_Mediaoembed_Request_RequestBuilder');
			$request = $requestBuilder->buildRequest($conf, $providerData);
			
			$responseData = $request->sendAndGetResponseData();
			$responseBuilder = t3lib_div::makeInstance('Tx_Mediaoembed_Response_ResponseBuilder');
			$response = $responseBuilder->buildResponse($responseData);
			
			return $response->render();
			
		}
		catch(Tx_Mediaoembed_Exception_OEmbedException $exception) {
			return 'Error: ' . $exception->getMessage();
		}
	}
}
?>