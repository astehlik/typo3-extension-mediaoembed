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

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Service\PhotoDownloadService;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * This type is used for representing static photos.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 */
class ResponseBuilder implements SingletonInterface
{
    private PhotoDownloadService $photoDownloadService;

    public function __construct(PhotoDownloadService $photoDownloadService)
    {
        $this->photoDownloadService = $photoDownloadService;
    }

    /**
     * Builds a response object using the reponse data returned
     * from the provider.
     *
     * @param array $responseData Raw response data from the provider
     *
     * @return GenericResponse An instance of a response
     */
    public function buildResponse(array $responseData, Configuration $configuration): GenericResponse
    {
        return $this->createResponseByType($responseData, $configuration);
    }

    /**
     * Creates an instance of a non abstract response for the
     * given response type.
     */
    protected function createResponseByType(array $parsedResponseData, Configuration $configuration): GenericResponse
    {
        $finalResponseData = $parsedResponseData;

        $responseType = (string)$parsedResponseData['type'];

        return match ($responseType) {
            'link' => $this->createResponseLink($finalResponseData, $configuration),
            'photo' => $this->createResponsePhoto($finalResponseData, $configuration),
            'rich' => $this->createResponseRich($finalResponseData, $configuration),
            'video' => $this->createResponseVideo($finalResponseData, $configuration),
            default => $this->createResponseGeneric($finalResponseData, $configuration),
        };
    }

    protected function createResponseGeneric(array $responseData, Configuration $configuration): GenericResponse
    {
        return $this->createResponseWithData(GenericResponse::class, $responseData, $configuration);
    }

    protected function createResponseLink(array $responseData, Configuration $configuration): LinkResponse
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createResponseWithData(LinkResponse::class, $responseData, $configuration);
    }

    protected function createResponsePhoto(array $responseData, Configuration $configuration): PhotoResponse
    {
        $responseData['localFile'] = $this->photoDownloadService->downloadPhoto(
            $responseData['url'],
            $configuration
        );

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createResponseWithData(PhotoResponse::class, $responseData, $configuration);
    }

    protected function createResponseRich(array $responseData, Configuration $configuration): RichResponse
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createResponseWithData(RichResponse::class, $responseData, $configuration);
    }

    protected function createResponseVideo(array $responseData, Configuration $configuration): VideoResponse
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createResponseWithData(VideoResponse::class, $responseData, $configuration);
    }

    protected function createResponseWithData(
        string $responseClass,
        array $responseData,
        Configuration $configuration
    ): GenericResponse {
        /** @var GenericResponse $response */
        $response = new $responseClass();
        $response->initializeResponseData($responseData, $configuration);
        return $response;
    }
}
