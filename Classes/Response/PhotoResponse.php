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

use TYPO3\CMS\Core\Resource\FileInterface;

/**
 * This type is used for representing static photos.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 */
class PhotoResponse extends GenericResponse
{
    /**
     * The height in pixels of the image specified in the url parameter.
     * This value is required.
     *
     * @var string
     */
    protected $height;

    /**
     * Path to the local version of the photo.
     *
     * @var FileInterface
     */
    protected $localFile;

    /**
     * The source URL of the image.
     * Consumers should be able to insert this URL into an <img> element.
     * Only HTTP and HTTPS URLs are valid.
     * This value is required.
     *
     * @var string
     */
    protected $url;

    /**
     * The width in pixels of the image specified in the url parameter.
     * This value is required.
     *
     * @var string
     */
    protected $width;

    /**
     * Initializes the response parameters that are specific for this
     * resource type.
     */
    public function initializeTypeSpecificResponseData()
    {
        $this->url = $this->responseDataArray['url'];

        $this->height = $this->responseDataArray['height'];
        $this->width = $this->responseDataArray['width'];

        $this->localFile = $this->responseDataArray['localFile'];
    }

    /**
     * Getter for the height in pixels of the image specified in the url parameter.
     *
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Getter for the path to a locally stored version of the image.
     *
     * @return FileInterface|null
     */
    public function getLocalFile()
    {
        return $this->localFile;
    }

    /**
     * Getter for the source URL of the image.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Getter for the width in pixels of the image specified in the url parameter.
     *
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }
}
