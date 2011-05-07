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
 * @package mediaoembed
 * @subpackage Renderer
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Request_HttpRequest {

	/**
	 * The endpoint URL that should be contacted to get the embed
	 * information.
	 *
	 * @var string
	 */
	protected $endpoint;

	/**
	 * The required response format. When not specified, the provider can return
	 * any valid response format.
	 * When specified, the provider must return data in the request format,
	 * else return an error (see below for error codes).
	 * This value is optional.
	 *
	 * Important! At the moment, we only handle JSON formatted Responses.
	 *
	 * @var string
	 */
	protected $format = 'json';

	/**
	 * Injector for the configuration object
	 *
	 * @param Tx_Mediaoembed_Content_Configuration $configuration
	 */
	public function injectConfiguration($configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * Builds a request url and tries to read the embed information
	 * from the server. Result should be in json format.
	 *
	 * @return string json formatted result from server
	 */
	public function sendAndGetResponseData() {
		$parameters = $this->buildRequestParameterArray();
		$requestUrl = $this->buildRequestUrl($parameters);
		$responseData = $this->sendRequest($requestUrl);
		return $responseData;
	}

	/**
	 * Setter for the endpoint URL
	 *
	 * @param string $url
	 */
	public function setEndpoint($endpoint) {
		$this->endpoint = $endpoint;
	}

	/**
	 * Setter for the URL
	 *
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * Builds an array of parameters that should be attached to the
	 * endpoint url.
	 *
	 * @return array
	 */
	protected function buildRequestParameterArray() {

		$parameters = array();

		$maxwidth = $this->configuration->getMaxwidth();
		if (isset($maxwidth)) {
			$parameters['maxwidth'] = $maxwidth;
		}

		$maxheight = $this->configuration->getMaxheight();
		if (isset($maxheight)) {
			$parameters['maxheight'] = $maxheight;
		}

		if (isset($this->format)) {
			$parameters['format'] = $this->format;
		}
			// needs to be last parameter
		$parameters['url'] = $this->configuration->getMediaUrl();

		return $parameters;
	}

	/**
	 * Builds a request url for the current endpoint based on the
	 * given parameter array.
	 *
	 * If the endpoint URL contains a marker ###FORMAT### or {format}
	 * it will be replaced with the expected response data format.
	 *
	 * @param array $parameters
	 * @return string
	 */
	protected function buildRequestUrl($parameters) {

		if (strstr('?', $this->endpoint)) {
			$firstParameter = FALSE;
		}
		else {
			$firstParameter = TRUE;
		}

		$requestUrl = $this->endpoint;

		$requestUrl = t3lib_parsehtml::substituteMarker($requestUrl, '###FORMAT###', $this->format);
		$requestUrl = t3lib_parsehtml::substituteMarker($requestUrl, '{format}', $this->format);

		foreach ($parameters as $name => $value) {

			$name = urlencode($name);
			$value = urlencode($value);

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

	/**
	 * Tries to get the real error code from the $report array of
	 * t3lib_div::getURL()
	 *
	 * @param array $report report array of t3lib_div::getURL()
	 * @return string the error code
	 * @see t3lib_div::getURL()
	 */
	protected function getErrorCode($report) {

		$message = $report['message'];
		$errorCode = $report['error'];

		if (strstr($message, '404')) {
			$errorCode = '404';
		} else if (strstr($message, '501')) {
			$errorCode = '501';
		} else if (strstr($message, '401')) {
			$errorCode = '401';
		}

		return $errorCode;
	}

	/**
	 * Sends a request to the given URL and returns the reponse
	 * from the server.
	 *
	 * @return string response data
	 */
	protected function sendRequest($requestUrl) {

		$report = array();
		$responseData = t3lib_div::getURL($requestUrl, 0, FALSE, $report);

		if ($report['error'] !== 0) {
			switch ($this->getErrorCode($report)) {
				case 404;
					throw new Tx_Mediaoembed_Exception_HttpNotFoundException($this->url, $requestUrl);
					break;
				case 501:
					throw new Tx_Mediaoembed_Exception_HttpNotImplementedException($this->url, $this->format, $requestUrl);
					break;
				case 401:
					throw new Tx_Mediaoembed_Exception_UnauthorizedException($this->url, $requestUrl);
					break;
				default:
					throw new RuntimeException('An unknown error occured while contacting the provider: ' . $report['message'] . ' (' . $report['error'] . '). Please make sure CURL use is enabled in the install tool to get valid error codes.', 1303401545);
					break;
			}
		}

		return $responseData;
	}
}
?>