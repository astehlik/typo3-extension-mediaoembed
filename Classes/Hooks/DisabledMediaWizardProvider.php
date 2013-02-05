<?php
namespace Sto\Mediaoembed\Hooks;

/**
 * Always returns false when it is asked if it can handle the given URL
 */
class DisabledMediaWizardProvider implements \TYPO3\CMS\Frontend\MediaWizard\MediaWizardProviderInterface {


	/**
	 * Tells the calling party if we can handle the URL passed to the constructor
	 *
	 * @param string $url URL to be handled
	 * @return boolean
	 */
	public function canHandle($url) {
		return FALSE;
	}

	/**
	 * Rewrites a media provider URL into a canonized form that can be embedded
	 *
	 * @param string $url URL to be handled
	 * @return string Canonized URL that can be used to embedd
	 */
	public function rewriteUrl($url) {
		return $url;
	}
}
