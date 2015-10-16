<?php
namespace Sto\Mediaoembed\Response;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * This type is used for representing static photos.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 */
class ResponseBuilder {

	/**
	 * The response that will be build by the response builder
	 *
	 * @var GenericResponse
	 */
	protected $response;

	/**
	 * Builds a response object using the reponse data returned
	 * from the provider.
	 *
	 * @param string $responseData Raw response data from the provider
	 * @return GenericResponse An instance of a response
	 * @throws \Sto\Mediaoembed\Exception\InvalidResponseException
	 */
	public function buildResponse($responseData) {


		$parsedResponseData = json_decode($responseData, TRUE);

		if ($parsedResponseData === NULL) {
			throw new \Sto\Mediaoembed\Exception\InvalidResponseException($responseData);
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
				$this->response = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sto\\Mediaoembed\\Response\\LinkResponse');
				break;
			case 'photo':
				$this->response = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sto\\Mediaoembed\\Response\\PhotoResponse');
				break;
			case 'rich':
				$this->response = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sto\\Mediaoembed\\Response\\RichResponse');
				break;
			case 'video':
				$this->response = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sto\\Mediaoembed\\Response\\VideoResponse');
				break;
			default:
				$this->response = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sto\\Mediaoembed\\Response\\GenericResponse');
				break;
		}
	}
}