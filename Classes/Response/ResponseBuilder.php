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
 * This type is used for representing static photos.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 *
 * @package mediaoembed
 * @subpackage Response
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Response_ResponseBuilder {

	/**
	 * The response that will be build by the response builder
	 *
	 * @var Tx_Mediaoembed_Response_GenericResponse
	 */
	protected $response;

	/**
	 * Builds a response object using the reponse data returned
	 * from the provider.
	 *
	 * @param string $responseData Raw response data from the provider
	 * @return Tx_Mediaoembed_Response_GenericResponse An instance of a response
	 */
	public function buildResponse($responseData) {


		$parsedResponseData = json_decode($responseData, TRUE);

		if ($parsedResponseData === NULL) {
			throw new Tx_Mediaoembed_Exception_InvalidResponseException($responseData);
		}

		$this->createResponseByType($parsedResponseData['type']);

		$this->response->initializeResponseData($parsedResponseData);

		return $this->response;
	}

	/**
	 * Creates an instance of a non abstract response for the
	 * given response type.
	 *
	 * @param string $type
	 * @return void
	 */
	protected function createResponseByType($type) {

		switch ($type) {
			case 'link':
				$this->response = t3lib_div::makeInstance('Tx_Mediaoembed_Response_LinkResponse');
				break;
			case 'photo':
				$this->response = t3lib_div::makeInstance('Tx_Mediaoembed_Response_PhotoResponse');
				break;
			case 'rich':
				$this->response = t3lib_div::makeInstance('Tx_Mediaoembed_Response_RichResponse');
				break;
			case 'video':
				$this->response = t3lib_div::makeInstance('Tx_Mediaoembed_Response_VideoResponse');
				break;
			default:
				$this->response = t3lib_div::makeInstance('Tx_Mediaoembed_Response_GenericResponse');
				break;
		}
	}
}
?>