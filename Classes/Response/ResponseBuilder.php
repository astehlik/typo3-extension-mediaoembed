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
use Sto\Mediaoembed\Service\PhotoDownloadService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * This type is used for representing static photos.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 */
class ResponseBuilder implements SingletonInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var PhotoDownloadService
     */
    private $photoDownloadService;

    public function __construct(ObjectManagerInterface $objectManager, PhotoDownloadService $photoDownloadService)
    {
        $this->objectManager = $objectManager;
        $this->photoDownloadService = $photoDownloadService;
    }

    /**
     * Builds a response object using the reponse data returned
     * from the provider.
     *
     * @param string $embedUrl The URL provided by the editor that is sent to the oEmbed endpoint.
     * @param string $responseData Raw response data from the provider
     * @return GenericResponse An instance of a response
     * @throws InvalidResponseException
     */
    public function buildResponse(string $embedUrl, string $responseData): GenericResponse
    {
        $parsedResponseData = json_decode($responseData, true);

        if ($parsedResponseData === null) {
            throw new InvalidResponseException($responseData);
        }

        $parsedResponseData['embedUrl'] = $embedUrl;

        return $this->createResponseByType($parsedResponseData);
    }

    /**
     * Creates an instance of a non abstract response for the
     * given response type.
     *
     * @param array $parsedResponseData
     * @return GenericResponse
     */
    protected function createResponseByType(array $parsedResponseData): GenericResponse
    {
        $finalResponseData = $parsedResponseData;

        switch ((string)$parsedResponseData['type']) {
            case 'link':
                $response = $this->objectManager->get(LinkResponse::class);
                break;
            case 'photo':
                $finalResponseData['localFile'] = $this->photoDownloadService->downloadPhoto(
                    $finalResponseData['embedUrl'],
                    $finalResponseData['url']
                );
                $response = $this->objectManager->get(PhotoResponse::class);
                break;
            case 'rich':
                $response = $this->objectManager->get(RichResponse::class);
                break;
            case 'video':
                $response = $this->objectManager->get(VideoResponse::class);
                break;
            default:
                $response = $this->objectManager->get(GenericResponse::class);
                break;
        }

        $response->initializeResponseData($finalResponseData);
        return $response;
    }
}
