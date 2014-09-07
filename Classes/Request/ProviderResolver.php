<?php
namespace Sto\Mediaoembed\Request;

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
 */
class ProviderResolver {

	/**
	 * The parent content object
	 *
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $cObj;

	/**
	 * TypoScript / Flexform configuration
	 *
	 * @var \Sto\Mediaoembed\Content\Configuration
	 */
	protected $configuration;

	/**
	 * TYPO3 database connection
	 *
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $db;

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
	 * @return ProviderResolver
	 */
	public function __construct() {
		$this->db = $GLOBALS['TYPO3_DB'];
		$this->genericProviderCache = array();
	}

	/**
	 * Injector for the current cObj
	 *
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
		$this->cObj = $configurationManager->getContentObject();
	}

	/**
	 * Returns the next active provider whos url scheme matches the URL in
	 * the current configuration
	 *
	 * @return Provider The next matching provider
	 */
	public function getNextMatchingProvider() {

		$urlHasChanged = $this->initializeUrl();

		if ($urlHasChanged) {
			$this->fetchSortedProvidersFromDatabase();
		}

		$providerData = $this->getNextMatchingProviderData();
		$provider = $this->buildProvider($providerData);
		return $provider;
	}

	/**
	 * Checks, if the url in the configuration is the same as the current url.
	 * If it has changed the url will be validated and TRUE will be returned.
	 *
	 * @return boolean TRUE if URL has changed, FALSE if not
	 */
	protected function initializeUrl() {

		$newUrl = $this->configuration->getMediaUrl();

		if ($this->url === $newUrl) {
			return FALSE;
		}

		$this->url = $newUrl;
		$this->checkIfUrlIsValid();
		return TRUE;
	}

	/**
	 * Checks if the current URL is valid
	 *
	 * @return void
	 * @throws \Sto\Mediaoembed\Exception\InvalidUrlException
	 */
	protected function checkIfUrlIsValid() {

		$isValid = TRUE;

		if (empty($this->url)) {
			$isValid = FALSE;
		}

		if (!\TYPO3\CMS\Core\Utility\GeneralUtility::isValidUrl($this->url)) {
			$isValid = FALSE;
		}

		if (!$isValid) {
			throw new \Sto\Mediaoembed\Exception\InvalidUrlException($this->url);
		}
	}

	/**
	 * Fetches the regular expressions for the providers from the database.
	 * Respects the sorting of the providers and of the regular expressions.
	 *
	 * @return resource MySQL result pointer / DBAL object
	 */
	protected function fetchSortedProvidersFromDatabase() {

		$providerResult = $this->db->exec_SELECTquery(
			'*',
			'tx_mediaoembed_provider',
			'is_generic=0 ' . $this->cObj->enableFields('tx_mediaoembed_provider'),
			'sorting'
		);

		if ($providerResult === FALSE) {
			throw new \RuntimeException('Error retrieving url schemes of providers from database.', 1303109998);
		}

		$this->providerResult = $providerResult;
	}

	/**
	 * Searches for a url scheme that matches the given url. If
	 * there is a result, the data of the matching provider will be returned.
	 *
	 * @return array Database data of the provider
	 * @throws \Sto\Mediaoembed\Exception\NoMatchingProviderException
	 */
	protected function getNextMatchingProviderData() {

		$matchingProviderData = FALSE;

		while (($providerData = $this->db->sql_fetch_assoc($this->providerResult)) && ($matchingProviderData === FALSE)) {

			// We don't care about providers that don't have a url scheme
			if (empty($providerData['url_schemes'])) {
				continue;
			}

			$urlSchemes = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(LF, $providerData['url_schemes']);

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
			throw new \Sto\Mediaoembed\Exception\NoMatchingProviderException($this->url);
		}

		return $matchingProviderData;
	}

	/**
	 * Builds a provider including the attached generic providers.
	 *
	 * @param array $providerData Provider data fetched from the database
	 * @return Provider
	 */
	protected function buildProvider($providerData) {

		/**
		 * @var \Sto\Mediaoembed\Request\Provider $provider
		 */
		$provider = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sto\\Mediaoembed\\Request\\Provider', $providerData);

		if (empty($providerData['use_generic_providers'])) {
			return $provider;
		}

		$genericProviders = array();
		$genericProviderUidArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $providerData['use_generic_providers']);
		foreach ($genericProviderUidArray as $genericProviderUid) {
			$genericProvider = $this->buildGenericProvider($genericProviderUid);
			if (isset($genericProvider)) {
				$genericProviders[] = $genericProvider;
			}
		}

		$provider->setGenericProviders($genericProviders);
		return $provider;
	}

	/**
	 * Fetches the generic provider from the database (if enabled) and creates
	 * a new provider instance with the fetched data.
	 *
	 * @param int $genericProviderUid
	 * @return Provider
	 */
	protected function buildGenericProvider($genericProviderUid) {

		if (array_key_exists($genericProviderUid, $this->genericProviderCache)) {
			return $this->genericProviderCache[$genericProviderUid];
		}

		$genericProviderData = $this->fetchGenericProviderDataFromDatabase($genericProviderUid);
		$genericProvider = NULL;

		if (isset($genericProviderData)) {
			$genericProvider = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sto\\Mediaoembed\\Request\\Provider', $genericProviderData);
		}

		$this->genericProviderCache[$genericProviderUid] = $genericProvider;
		return $this->genericProviderCache[$genericProviderUid];
	}

	/**
	 * Fetches all data for the provider from the database.
	 *
	 * @param int $genericProviderUid
	 * @return array Associative array with provider data
	 */
	protected function fetchGenericProviderDataFromDatabase($genericProviderUid) {

		if (!isset($this->genericProviderStatement)) {
			$this->genericProviderStatement = $this->db->prepare_SELECTquery(
				'*',
				'tx_mediaoembed_provider',
				'is_generic = 1 AND uid = :genericProviderUid' .
				$this->cObj->enableFields('tx_mediaoembed_provider'),
				'',
				'sorting'
			);
		}

		$genericProviderResult = $this->genericProviderStatement->execute(array(':genericProviderUid' => $genericProviderUid));

		if ($genericProviderResult === FALSE) {
			throw new \RuntimeException('Error retrieving generic provider data from database.', 1303399235);
		}

		if (!$this->genericProviderStatement->rowCount()) {
			return NULL;
		}

		return $this->genericProviderStatement->fetch();
	}

	/**
	 * Initializes the current configuration
	 *
	 * @param \Sto\Mediaoembed\Content\Configuration $configuration
	 */
	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
	}
}