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
	 *
	 * @var tslib_cObj
	 */
	protected $cObj;

	/**
	 * TypoScript / Flexform configuration
	 *
	 * @var Tx_Mediaoembed_Content_Configuration
	 */
	protected $configuration;

	/**
	 * A cache for generic providers, array keys are the UIDs of the providers.
	 *
	 * @var array
	 */
	protected $genericProviderCache;

	/**
	 * Statement for fetching a generic provider from the database.
	 *
	 * @param t3lib_db_PreparedStatement
	 */
	protected $genericProviderStatement;

	/**
	 * The SQL result of the provider query, should not contain
	 * all active, non generic providers.
	 *
	 * @var mixed
	 */
	protected $providerResult;

	/**
	 * Contains the current media URL
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * Builds the provider resolver an initializes the generic provider cache.
	 *
	 * @return Tx_Mediaoembed_Request_ProviderResolver
	 */
	public function __construct() {
		$this->genericProviderCache = array();
	}

	/**
	 * Injector for the current cObj
	 *
	 * @param tslib_cObj $cObj
	 */
	public function injectCObj($cObj) {
		$this->cObj = $cObj;
	}

	/**
	 * Injector for the TypoScript / Flexform configuration
	 *
	 * @param Tx_Mediaoembed_Content_Configuration $configuration
	 */
	public function injectConfiguration($configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * Checks, if the url in the configuration is the same as the current url.
	 * If it has changed the url will be validated and TRUE will be returned.
	 *
	 * @return boolean TRUE if URL has changed, FALSE if not
	 * @throws Tx_Mediaoembed_Exception_InvalidUrlException if URL is invalid
	 */
	protected function initializeUrl() {

		if ($this->url === $this->configuration->getMediaUrl()) {
			return FALSE;
		}

		$this->url = $url;
		$this->checkIfUrlIsValid();
		return TRUE;
	}

	/**
	 * Returns the next active provider whos url scheme matches the URL in
	 * the current configuration
	 *
	 * @return Tx_Mediaoembed_Request_Provider The next matching provider
	 * @throws Exception If URL is invalid or no matching provider was found.
	 */
	public function getNextMatchingProvider() {

		$urlHasChanged = $this->initializeUrl();

		if ($urlHasChanged) {
			$this->fetchSortedProvidersFromDatabase();
		}

		$providerData = $this->getNextMatchingProvider();
		$provider = $this->buildProvider($providerData);
		return $provider;
	}

	/**
	 * Fetches the generic provider from the database (if enabled) and creates
	 * a new provider instance with the fetched data.
	 *
	 * @return Tx_Mediaoembed_Request_Provider
	 */
	protected function buildGenericProvider($genericProviderUid) {

		if (array_key_exists($genericProviderUid, $this->genericProviderCache)) {
			return $this->genericProviderCache[$genericProviderUid];
		}

		$genericProviderData = $this->fetchGenericProviderDataFromDatabase($genericProviderUid);
		$genericProvider = NULL;
		
		if (isset($genericProviderData)) {
			$genericProvider = t3lib_div::makeInstance('Tx_Mediaoembed_Request_Provider', $genericProviderData);	
		}
		
		$this->genericProviderCache[$genericProviderUid] = $genericProvider;
		return $this->genericProviderCache[$genericProviderUid];
	}

	/**
	 * Builds a provider including the attached generic providers.
	 *
	 * @param array $providerData Provider data fetched from the database
	 * @return Tx_Mediaoembed_Request_Provider
	 */
	protected function buildProvider($providerData) {

		$provider = t3lib_div::makeInstance('Tx_Mediaoembed_Request_Provider', $providerData);

		if (empty($providerData['use_generic_providers'])) {
			return $provider;
		}

		$genericProviders = array();
		$genericProviderUidArray = t3lib_div::trimExplode(',', $this->genericEndpoints);
		foreach ($genericProviderUidArray as $genericProviderUid) {
			$genericProviders[] = $this->buildGenericProvider($genericProviderUid);
		}
		
		$provider->setGenericProviders($genericProviders);
		return $provider;
	}

	/**
	 * Checks if the current URL is valid
	 *
	 * @return void
	 * @throws Tx_Mediaoembed_Exception_InvalidUrlException if URL is invalid
	 */
	protected function checkIfUrlIsValid() {

		$isValid = TRUE;

		if (empty($this->url)) {
			$isValid = FALSE;
		}

		if (!t3lib_div::isValidUrl($this->url)) {
			$isValid = FALSE;
		}

		if (!$isValid) {
			throw new Tx_Mediaoembed_Exception_InvalidUrlException($this->url);
		}
	}

	/**
	 * Searches for a url scheme that matches the given url. If
	 * there is a result, the data of the matching provider will be returned.
	 *
	 * @return array Database data of the provider
	 * @throws Tx_Mediaoembed_Exception_NoMatchingProviderException if no matching provider was found
	 */
	protected function getNextMatchingProvider() {

		$matchingProviderData = FALSE;

		while (($providerData = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->providerResult)) && ($matchingProviderData === FALSE)) {

				// We don't care about providers that don't have a url scheme
			if (empty($providerData['url_schemes'])) {
				continue;
			}

			$urlSchemes = explode(LF, $providerData['url_schemes']);

			foreach ($urlSchemes as $urlScheme) {
				$urlScheme = preg_quote($urlScheme, '/');
				$urlScheme = str_replace('\*', '.*', $urlScheme);
				if (preg_match('/' . $urlScheme . '/', $this->url)) {
					$matchingProviderData = $providerData;
					break;
				}
			}
		}

		if ($matchingProviderData === FALSE) {
			throw new Tx_Mediaoembed_Exception_NoMatchingProviderException($this->url);
		}

		return $matchingProviderData;
	}

	/**
	 * Fetches all data for the provider from the database.
	 *
	 * @return array Associative array with provider data
	 * @throws RuntimeException if provider data can not be fetched
	 */
	protected function fetchGenericProviderDataFromDatabase($genericProviderUid) {

		if (!isset($this->genericProviderStatement)) {
			$this->genericProviderStatement = $GLOBALS['TYPO3_DB']->prepare_SELECTquery(
				'endpoint',
				'tx_mediaoembed_provider',
				'is_generic = 1 AND uid = :genericProviderUid' .
				$this->cObj->enableFields('tx_mediaoembed_provider'),
				'',
				'sorting'
			);
		}

		$genericProviderResult = $this->genericProviderStatement->execute(array(':genericProviderUid' => $genericProviderUid));

		if ($genericProviderResult === FALSE) {
			throw new RuntimeException('Error retrieving generic provider data from database.', 1303399235);
		}

		if ($GLOBALS['TYPO3_DB']->sql_num_rows($genericProviderResult)) {
			return NULL;
		}

		return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($genericProviderResult);
	}

	/**
	 * Fetches the regular expressions for the providers from the database.
	 * Respects the sorting of the providers and of the regular expressions.
	 *
	 * @return pointer MySQL result pointer / DBAL object
	 * @throws RuntimeException If SQL query fails
	 */
	protected function fetchSortedProvidersFromDatabase() {

		$providerResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_mediaoembed_provider',
			'is_generic=0 ' . $this->cObj->enableFields('tx_mediaoembed_provider'),
			'sorting'
		);

		if ($providerResult === FALSE) {
			throw new RuntimeException('Error retrieving url schemes of providers from database.', 1303109998);
		}

		$this->providerResult = $providerResult;
	}
}
?>