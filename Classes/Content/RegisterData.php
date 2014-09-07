<?php
namespace Sto\Mediaoembed\Content;

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
 */
class RegisterData {

	/**
	 * The configuration that was used during the request
	 *
	 * @var \Sto\Mediaoembed\Content\Configuration
	 * @inject
	 */
	protected $configuration;

	/**
	 * The provider that was used for the request.
	 *
	 * @var \Sto\Mediaoembed\Request\Provider
	 */
	protected $provider;

	/**
	 * The request that was send to get the embed code
	 *
	 * @var \Sto\Mediaoembed\Request\HttpRequest
	 */
	protected $request;

	/**
	 * The response we got from the server
	 *
	 * @var \Sto\Mediaoembed\Response\GenericResponse
	 */
	protected $response;

	/**
	 * Getter for the configuration
	 *
	 * @return Configuration $configuration
	 */
	public function getConfiguration() {
		return $this->configuration;
	}

	/**
	 * Getter for the provider
	 *
	 * @return \Sto\Mediaoembed\Request\Provider
	 */
	public function getProvider() {
		return $this->provider;
	}

	/**
	 * Setter for the provider
	 *
	 * @param \Sto\Mediaoembed\Request\Provider $provider
	 * @return void
	 */
	public function setProvider($provider) {
		$this->provider = $provider;
	}

	/**
	 * Getter for the request
	 *
	 * @return \Sto\Mediaoembed\Request\HttpRequest
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * Setter for the request
	 *
	 * @param \Sto\Mediaoembed\Request\HttpRequest $request
	 * @return void
	 */
	public function setRequest($request) {
		$this->request = $request;
	}

	/**
	 * Getter for the response
	 *
	 * @return \Sto\Mediaoembed\Response\GenericResponse
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * Setter for the response
	 *
	 * @param \Sto\Mediaoembed\Response\GenericResponse $response
	 * @return void
	 */
	public function setResponse($response) {
		$this->response = $response;
	}

}