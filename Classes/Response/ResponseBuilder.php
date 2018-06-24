<?php
declare(strict_types=1);

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

use Sto\Mediaoembed\Exception\InvalidResponseException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This type is used for representing static photos.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 */
class ResponseBuilder implements SingletonInterface
{
    /**
     * Builds a response object using the reponse data returned
     * from the provider.
     *
     * @param string $responseData Raw response data from the provider
     * @return GenericResponse An instance of a response
     * @throws \Sto\Mediaoembed\Exception\InvalidResponseException
     */
    public function buildResponse(string $responseData): GenericResponse
    {
        $parsedResponseData = json_decode($responseData, true);

        if ($parsedResponseData === null) {
            throw new InvalidResponseException($responseData);
        }

        $response = $this->createResponseByType($parsedResponseData['type']);

        $response->initializeResponseData($parsedResponseData);

        return $response;
    }

    /**
     * Creates an instance of a non abstract response for the
     * given response type.
     *
     * @param string $type
     * @return \Sto\Mediaoembed\Response\GenericResponse
     */
    protected function createResponseByType(string $type): GenericResponse
    {
        switch ($type) {
            case 'link':
                $response = GeneralUtility::makeInstance(LinkResponse::class);
                break;
            case 'photo':
                $response = GeneralUtility::makeInstance(PhotoResponse::class);
                break;
            case 'rich':
                $response = GeneralUtility::makeInstance(RichResponse::class);
                break;
            case 'video':
                $response = GeneralUtility::makeInstance(VideoResponse::class);
                break;
            default:
                $response = GeneralUtility::makeInstance(GenericResponse::class);
                break;
        }
        return $response;
    }
}
