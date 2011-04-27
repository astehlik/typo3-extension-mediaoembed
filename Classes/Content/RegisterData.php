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
 * Stores all relevant data for the content REGISTER
 *
 * @package mediaoembed
 * @subpackage Content
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Content_RegisterData {

	/**
	 * The provider that was used for the request.
	 *
	 * @var Tx_Mediaoembed_Request_Provider
	 */
	protected $provider;

	/**
	 * The request that was send to get the embed code
	 *
	 * @var Tx_Mediaoembed_Request_HttpRequest
	 */
	protected $request;

	/**
	 * The response we got from the server
	 *
	 * @var Tx_Mediaoembed_Response_GenericResponse
	 */
	protected $response;

	/**
	 * Getter for the provider
	 *
	 * @return Tx_Mediaoembed_Request_Provider
	 */
	public function getProvider() {
		return $this->provider;
	}

	/**
	 * Getter for the request
	 *
	 * @return Tx_Mediaoembed_Request_HttpRequest
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * Getter for the response
	 *
	 * @return Tx_Mediaoembed_Response_GenericResponse
	 */
	public function getReponse() {
		return $this->response;
	}

	/**
	 * Setter for the provider
	 *
	 * @param Tx_Mediaoembed_Request_Provider $provider
	 * @return void
	 */
	public function setProvider($provider) {
		$this->provider = $provider;
	}

	/**
	 * Setter for the request
	 *
	 * @param Tx_Mediaoembed_Request_HttpRequest $request
	 * @return void
	 */
	public function setRequest($request) {
		$this->request = $request;
	}

	/**
	 * Setter for the response
	 *
	 * @param Tx_Mediaoembed_Response_GenericResponse $response
	 * @return void
	 */
	public function setReponse($response) {
		$this->response = $response;
	}





}