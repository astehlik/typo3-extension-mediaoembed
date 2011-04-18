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
		
		$this->conf = $conf;
		$this->cObj = $parentContent->getCObj();
		
			// @TODO submit TYPO3 patch that we get the current content
			// that was possibly set by other providers!
		$currentContent = '';
		
			// we only render if it is our content type
		if ($renderType !== self::$renderType) {
			return $currentContent;
		}
		
		$mediaUrl = $conf['file'];
		$providerData = $this->getMatchingProviderData($mediaUrl);
		
		if (!isset($providerData)) {
			return 'No matching provider was found for the url ' . $mediaUrl;
		}
		
		if ($providerData === FALSE) {
			throw new RuntimeException('Could not retrieve provider data.', 1303076207);
		}
		
		$requestParamesters['url'] = $mediaUrl;
		$requestParamesters['format'] = 'json';
		if (isset($conf['width'])) {
			$requestParamesters['maxwidth'] = $conf['width'];
		}
		if (isset($conf['height'])) {
			$requestParamesters['maxheight'] = $conf['height'];
		}
		
		$requestUrl = $this->buildRequestUrl($providerData['endpoint'], $requestParamesters['format']);
		
		$report = array();
		$providerResult = t3lib_div::getURL($requestUrl, 0, FALSE, $report);
		if($providerResult === FALSE) {
			return 'Error getting data from provider: ' . $report['message'];
		}
		
		$this->providerResult = json_decode($providerResult);
		
		switch ($this->providerResult->type) {
			case 'photo':
				$html = $this->getHtmlForPhoto();
				break;
			case 'video':
				$html = $this->getHtmlForVideo();
				break;
			case 'link':
				$html = $this->getHtmlForLink();
				break;
			case 'rich':
				$html = $this->getHtmlForRich();
				break;
			default:
				$html = 'Provider returned an unknown content type: ' . $parsedProviderResult->type;
				break;
		}
		
		
		return 'Provider: ' . $providerData['name'];
	}
	
	protected function getHtmlForPhoto() {
	}
	
	protected function getHtmlForVideo() {
	}
	
	protected function getHtmlForVideo() {
	}
	
	protected function getHtmlForVideo() {
	}
	
	protected function buildRequestUrl($endpoint, $parameters) {
	
		if (strstr('?', $endpoint)) {
			$firstParameter = FALSE;
		}
		else {
			$firstParameter = TRUE;
		}
		
		$requestUrl = $endpoint;
		
		foreach ($parameters as $name => $value) {
		
			$name = urlencode($name);
			$value = urldecode($value);
			
			if (!$firstParameter) {
				$parameterGlue = '&';
			} else {
				$parameterGlue = '?';
				$firstParameter = FALSE;
			}
		
			$requestUrl .= $parameterGlue . $name . '=' . $value;
		}
		
		return $requestUrl;
	}
	
	protected function getMatchingProviderData($url) {
	
		$providerRegexResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tx_mediaoembed_provider.uid as provider_uid, ' .
			'tx_mediaoembed_provider_regex.regex as provider_regex',
			'tx_mediaoembed_provider_regex, tx_mediaoembed_provider',
			'tx_mediaoembed_provider_regex.provider = tx_mediaoembed_provider.uid' .
			$this->cObj->enableFields('tx_mediaoembed_provider') . 
			$this->cObj->enableFields('tx_mediaoembed_provider_regex'),
			'',
			'tx_mediaoembed_provider.sorting, tx_mediaoembed_provider_regex.sorting'
		);
		
		if ($providerRegexResult === FALSE) {
			throw new RuntimeException('Error retrieving regular expressions for providers from database.', 1303109998);
		}
		
		$providerData = NULL;
		
		while ($regexRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($providerRegexResult)) {
			$regex = preg_quote($regexRow['provider_regex'], '/');
			$regex = str_replace('\*', '.*', $regex);
			if (preg_match('/' . $regex . '/', $url)) {
				$providerData =  $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'tx_mediaoembed_provider', 'uid = ' . intval($regexRow['provider_uid']));
				break;
			}
		}
		
		return $providerData;
	}
}

?>