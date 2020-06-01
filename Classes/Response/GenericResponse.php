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

/**
 * Responses can specify a resource type, such as photo or video.
 * Each type has specific parameters associated with it.
 * The parameters in this class are valid for all response types.
 */
class GenericResponse
{
    /**
     * The name of the author/owner of the resource.
     * This value is optional.
     *
     * @var string
     */
    protected $authorName;

    /**
     * A URL for the author/owner of the resource.
     * This value is optional.
     *
     * @var string
     */
    protected $authorUrl;

    /**
     * The suggested cache lifetime for this resource, in seconds.
     * Consumers may choose to use this value or not.
     * This value is optional.
     *
     * @var string
     */
    protected $cacheAge;

    /**
     * The name of the resource provider.
     * This value is optional.
     *
     * @var string
     */
    protected $providerName;

    /**
     * The url of the resource provider.
     * This value is optional.
     *
     * @var string
     */
    protected $providerUrl;

    /**
     * Array containing the json decoded data of the provider's response
     *
     * @var array
     */
    protected $responseDataArray;

    /**
     * The height of the optional thumbnail.
     * If this paramater is present, thumbnail_url and thumbnail_width
     * must also be present.
     * This value is optional.
     *
     * @var string
     */
    protected $thumbnailHeight;

    /**
     * A URL to a thumbnail image representing the resource.
     * The thumbnail must respect any maxwidth and maxheight parameters.
     * If this paramater is present, thumbnail_width and thumbnail_height
     * must also be present.
     * Consumers may choose to use this value or not.
     * This value is optional.
     *
     * @var string
     */
    protected $thumbnailUrl;

    /**
     * The width of the optional thumbnail.
     * If this paramater is present, thumbnail_url and thumbnail_height
     * must also be present.
     * This value is optional.
     *
     * @var string
     */
    protected $thumbnailWidth;

    /**
     * A text title, describing the resource.
     * This value is optional.
     *
     * @var string
     */
    protected $title;

    /**
     * The resource type. Valid values, along with value-specific parameters, are described below.
     * This value is required.
     *
     * @var string
     */
    protected $type;

    /**
     * The oEmbed version number. This must be 1.0.
     * This value is required.
     *
     * @var string
     */
    protected $version;

    /**
     * Initializes the response parameters that are valid for all
     * response types.
     *
     * @param array $responseData the parsed json response
     */
    public function initializeResponseData($responseData)
    {
        $this->responseDataArray = $responseData;
        $this->type = $this->getValueFromResponseData('type');
        $this->version = $this->getValueFromResponseData('version');
        $this->title = $this->getValueFromResponseData('title');
        $this->authorName = $this->getValueFromResponseData('author_name');
        $this->authorUrl = $this->getValueFromResponseData('author_url');
        $this->providerName = $this->getValueFromResponseData('provider_name');
        $this->providerUrl = $this->getValueFromResponseData('provider_url');
        $this->cacheAge = $this->getValueFromResponseData('cache_age');
        $this->thumbnailUrl = $this->getValueFromResponseData('thumbnail_url');
        $this->thumbnailWidth = $this->getValueFromResponseData('thumbnail_width');
        $this->thumbnailHeight = $this->getValueFromResponseData('thumbnail_height');
        $this->initializeTypeSpecificResponseData();
    }

    /**
     * Initializes the response parameters that are specific for this
     * resource type.
     */
    public function initializeTypeSpecificResponseData()
    {
    }

    /**
     * Getter for the name of the author/owner of the resource.
     *
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * Getter for the URL for the author/owner of the resource.
     *
     * @return string
     */
    public function getAuthorUrl()
    {
        return $this->authorUrl;
    }

    /**
     * Getter for the suggested cache lifetime for this resource, in seconds.
     *
     * @return string
     */
    public function getCacheAge()
    {
        return $this->cacheAge;
    }

    /**
     * Getter for the name of the resource provider.
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Getter for the url of the resource provider.
     *
     * @return string
     */
    public function getProviderUrl()
    {
        return $this->providerUrl;
    }

    /**
     * Getter for the the json decoded data of the provider's response
     *
     * @return array
     */
    public function getResponseDataArray()
    {
        return $this->responseDataArray;
    }

    /**
     * Getter for the height of the optional thumbnail.
     *
     * @return string
     */
    public function getThumbnailHeight()
    {
        return $this->thumbnailHeight;
    }

    /**
     * Getter for the URL to a thumbnail image representing the resource.
     *
     * @return string
     */
    public function getThumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

    /**
     * Getter for the width of the optional thumbnail.
     *
     * @return string
     */
    public function getThumbnailWidth()
    {
        return $this->thumbnailWidth;
    }

    /**
     * Getter for the text title, describing the resource.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Getter for the resource type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Getter for the resource type.
     *
     * @return string
     */
    public function getTypePartialName()
    {
        return ucfirst($this->type);
    }

    /**
     * Getter for the oEmbed version number.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Retrieves a value from the response array or returns NULL if the array key is not set.
     *
     * @param string $key
     * @return mixed
     */
    protected function getValueFromResponseData($key)
    {
        if (!array_key_exists($key, $this->responseDataArray)) {
            return null;
        }

        return $this->responseDataArray[$key];
    }
}
