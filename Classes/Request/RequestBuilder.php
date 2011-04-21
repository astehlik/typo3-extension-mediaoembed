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
 * Builds a request object based on the (TypoScript) configuration
 * 
 * @package mediaoembed
 * @subpackage Request
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Request_RequestBuilder {
	
	/**
	 * Configuration (from FlexForm)
	 * 
	 * @var array
	 */
	protected $conf;
	
	/**
	 * Provider data from database
	 *
	 * @var array
	 */
	protected $providerData;
	
	/**
	 * Request object that is build by this request builder
	 *
	 * @var Tx_Mediaoembed_Request_HtmlRequest
	 */
	protected $request;
	
	/**
	 * Builds a request using the given configuration and the
	 * given provider data.
	 * 
	 * @param array $conf
	 * @param array $providerData
	 * @return Tx_Mediaoembed_Request_HtmlRequest
	 */
	public function buildRequest($conf, $providerData) {
		$this->conf = $conf;
		$this->providerData = $providerData;
		$this->initializeNewRequest();
		return $this->request;
	}
	
	/**
	 * Build a new request in the request property
	 * 
	 * @return void
	 */
	protected function initializeNewRequest() {
		$this->createNewRequest();
		$this->initializeUrl();
		$this->initializeMaxSize();
		$this->initializeEndpoint();
	}
	
	/**
	 * Creates a new request object
	 *
	 * @return void
	 */
	protected function createNewRequest() {
		$this->request = t3lib_div::makeInstance('Tx_Mediaoembed_Request_HttpRequest');
	}
	
	protected function initializeUrl() {
		$this->request->setUrl($this->conf['parameter.']['mmFile']);
	}
	
	/**
	 * Initializes maxwidth and maxheight properties
	 *
	 * @return void
	 */
	protected function initializeMaxSize() {
		
		if (isset($this->conf['width'])) {
			$this->request->setMaxwidth($this->conf['width']);
		}
		if (isset($this->conf['height'])) {
			$this->request->setMaxheight($this->conf['height']);
		}
	}
	
	/**
	 * Initializes the endpoint in the current request
	 * that was set in the provider data.
	 *
	 * @return void
	 */
	protected function initializeEndpoint() {
		$this->request->setEndpoint($this->providerData['endpoint']);
	}
	
}
?>