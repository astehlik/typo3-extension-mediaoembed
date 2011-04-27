<?php

class Tx_Mediaoembed_Hooks_CmsMediaitems {

	/**
	 * The current content object
	 *
	 * @var tslib_cObj
	 */
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
	 * @param ux_tslib_content_Media $contentObject
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

		$content = t3lib_div::makeInstance('Tx_Mediaoembed_Content_OembedHandler', $parentContent->getCObj());
		$content->injectParentContent($parentContent);
		return $handler->$content($conf);
	}
}
?>