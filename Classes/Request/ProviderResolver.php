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
 * Resolves a matching provider for the given URL
 * 
 * @package mediaoembed
 * @subpackage Request
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Request_ProviderResolver {
	
	/**
	 * The parent content object
	 */
	protected $cObj;
	
	/**
	 * Contains the current media URL
	 * 
	 * @var string
	 */
	protected $url;
	
	
	public function __construct($cObj) {
		$this->cObj = $cObj;
	}
	
	/**
	 * Returns the first provider whos url scheme matches the given
	 * URL.
	 * 
	 * @return array Provider data
	 * @throws Exception If URL is invalid or no matching provider was found.
	 */
	public function getMatchingProviderData($url) {
		$this->url = $url;
		$this->checkIfUrlIsValid();
		$providerUid = $this->getMatchingProviderUid();
		$providerData = $this->fetchProviderDataFromDatabase($providerUid);
		return $providerData;
	}
	
	/**
	 * Checks if the current URL is valid
	 * 
	 * @return void
	 * @throws Tx_Mediaoembed_Exception_InvalidUrlException if URL is invalid
	 */
	protected function checkIfUrlIsValid() {
		
		if (!t3lib_div::isValidUrl($this->url)) {
			throw new Tx_Mediaoembed_Exception_InvalidUrlException($this->url);
		}
	}
	
	/**
	 * Searches for a url scheme that matches the given url. If
	 * there is a result, the UID of the matching provider will be returned.
	 * Otherwise FALSE will be returned.
	 * 
	 * @return int UID of the provider or FALSE if none was found
	 * @throws Tx_Mediaoembed_Exception_NoMatchingProviderException if no matching provider was found
	 */
	protected function getMatchingProviderUid() {
		
		$providerUid = FALSE;
		
		$urlSchemeResult = $this->fetchSortedUrlSchemesFromDatabase();
		
		while ($urlSchemeData = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($urlSchemeResult)) {
			$urlScheme = preg_quote($urlSchemeData['url_scheme'], '/');
			$urlScheme = str_replace('\*', '.*', $urlScheme);
			if (preg_match('/' . $urlScheme . '/', $this->url)) {
				$providerUid = $urlSchemeData['provider_uid'];
				break;
			}
		}
		
		if ($providerUid === FALSE) {
			throw new Tx_Mediaoembed_Exception_NoMatchingProviderException($this->url);
		}
		
		return $providerUid;
	}
	
	/**
	 * Fetches all data for the provider from the database.
	 * 
	 * @return array Associative array with provider data or NULL
	 */
	protected function fetchProviderDataFromDatabase($providerUid) {
		
		$providerData = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'tx_mediaoembed_provider', 'uid = ' . intval($providerUid));
		
		if (!isset($providerData)) {
			throw new RuntimeException('Error retrieving provider data from database.', 1303399235);
		}
		
		return $providerData;
	}
	
	/**
	 * Fetches the regular expressions for the providers from the database.
	 * Respects the sorting of the providers and of the regular expressions.
	 * 
	 * @return pointer MySQL result pointer / DBAL object
	 * @throws RuntimeException If SQL query fails
	 */
	protected function fetchSortedUrlSchemesFromDatabase() {
	
		$providerRegexResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tx_mediaoembed_provider.uid as provider_uid, ' .
			'tx_mediaoembed_url_scheme.url_scheme as url_scheme',
			'tx_mediaoembed_url_scheme, tx_mediaoembed_provider',
			'tx_mediaoembed_url_scheme.provider = tx_mediaoembed_provider.uid' .
			$this->cObj->enableFields('tx_mediaoembed_provider') . 
			$this->cObj->enableFields('tx_mediaoembed_url_scheme'),
			'',
			'tx_mediaoembed_provider.sorting, tx_mediaoembed_url_scheme.sorting'
		);
		
		if ($providerRegexResult === FALSE) {
			throw new RuntimeException('Error retrieving regular expressions for providers from database.', 1303109998);
		}
		
		return $providerRegexResult;
	}
}
?>