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
 * A oEmbed provider
 *
 * @package mediaoembed
 * @subpackage Request
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Request_Provider {

	/**
	 * Description of the provider
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Unique identifier used by embed.ly
	 *
	 * @var string
	 */
	protected $embedlyShortname;

	/**
	 * The endpoint url where we can send our request to.
	 *
	 * @var string
	 */
	protected $endpoint;

	/**
	 * Array containting the generic providers for this provider.
	 *
	 * @var array
	 */
	protected $genericProviders;

	/**
	 * A generic provider doesn't provide own content but generates
	 * the embed code for other content providers.
	 *
	 * @var boolean
	 */
	protected $isGeneric;

	/**
	 * Readable name of the provider
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The uid of the provider
	 *
	 * @var int
	 */
	protected $uid;

	/**
	 * The url schemes (seperated by linebreak) that can be used
	 * with this provider.
	 *
	 * @var string
	 */
	protected $urlSchemes;

	/**
	 * Constructor for the provider, uses the data array that was
	 * read from the database for initialization.
	 *
	 * @param array $providerData The associative array with provider data fetched from the database
	 */
	public function __construct ($providerData) {
		$this->description = $providerData['description'];
		$this->embedlyShortname = $providerData['embedly_shortname'];
		$this->endpoint = $providerData['endpoint'];
		$this->genericProviders = array();
		$this->isGeneric = $providerData['is_generic'];
		$this->name = $providerData['name'];
		$this->uid = $providerData['uid'];
		$this->urlSchemes = $providerData['url_schemes'];
	}

	/**
	 * Checks, if the given provider equals this provider.
	 *
	 * @param Tx_Mediaoembed_Request_Provider $provider
	 * @return boolean TRUE if provider is equal.
	 */
	public function equals($provider) {
		if ($provider instanceof Tx_Mediaoembed_Request_Provider) {
			if ($this->getUid() === $provider->getUid()) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Getter for all endpoints of this provider (native and generic)
	 *
	 * @return array Array containing all endpoint urls of this provider (native and generic).
	 */
	public function getAllEndpoints() {

		$endpoints = array();

		$nativeEndpoint = $this->getEndpoint();
		if (!empty($nativeEndpoint)) {
			$endpoints[] = $nativeEndpoint;
		}

		$endpoints = array_merge($endpoints, $this->getGenericEndpoints());

		return $endpoints;
	}

	/**
	 * Getter for the native endpoint of this provider.
	 *
	 * @return string
	 */
	public function getEndpoint() {
		return $this->endpoint;
	}


	/**
	 * Getter for the generic endpoints this provider should use.
	 *
	 * @return array
	 */
	public function getGenericEndpoints() {
		$genericEndpoints = array();
		$genericProviders = $this->getGenericProviders();
		foreach ($genericProviders as $genericProvider) {
			$genericEndpoints[] = $genericProvider->getEndpoint();
		}
		return $genericEndpoints;
	}

	/**
	 * Getter for the generic providers that should be used for this provider.
	 *
	 * @return array
	 */
	public function getGenericProviders() {
		return $this->genericEndpoints;
	}
	
	/**
	 * Setter for the generic providers (should only be called from the provider resolver.
	 * 
	 * @param array
	 */
	public function setGenericProviders($genericProviders) {
		$this->genericProviders = $genericProviders;
	}
}
?>