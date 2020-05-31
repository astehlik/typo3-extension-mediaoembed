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

    /**
     * @var string[]
     */
    private $responseTypes = [
        'link',
        'photo',
        'rich',
        'video',
    ];

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

        $responseType = (string)$parsedResponseData['type'];
        $createMethod = in_array($responseType, $this->responseTypes, true)
            ? 'createResponse' . ucfirst($responseType)
            : 'createResponseGeneric';

        /**
         * @uses createResponseGeneric()
         * @uses createResponseLink()
         * @uses createResponsePhoto()
         * @uses createResponseRich()
         * @uses createResponseVideo()
         */
        return $this->$createMethod($finalResponseData);
    }

    protected function createResponseGeneric(array $responseData): GenericResponse
    {
        return $this->createResponseWithData(GenericResponse::class, $responseData);
    }

    protected function createResponseLink(array $responseData): LinkResponse
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createResponseWithData(LinkResponse::class, $responseData);
    }

    protected function createResponsePhoto(array $responseData): PhotoResponse
    {
        $responseData['localFile'] = $this->photoDownloadService->downloadPhoto(
            $responseData['embedUrl'],
            $responseData['url']
        );
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createResponseWithData(PhotoResponse::class, $responseData);
    }

    protected function createResponseRich(array $responseData): RichResponse
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createResponseWithData(RichResponse::class, $responseData);
    }

    protected function createResponseVideo(array $responseData): VideoResponse
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createResponseWithData(VideoResponse::class, $responseData);
    }

    protected function createResponseWithData(string $responseClass, array $responseData): GenericResponse
    {
        /** @var GenericResponse $response */
        $response = $this->objectManager->get($responseClass);
        $response->initializeResponseData($responseData);
        return $response;
    }
}
