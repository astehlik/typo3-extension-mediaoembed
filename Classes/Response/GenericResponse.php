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
     */
    protected string $authorName;

    /**
     * A URL for the author/owner of the resource.
     * This value is optional.
     */
    protected string $authorUrl;

    /**
     * The suggested cache lifetime for this resource, in seconds.
     * Consumers may choose to use this value or not.
     * This value is optional.
     */
    protected int $cacheAge;

    /**
     * The name of the resource provider.
     * This value is optional.
     */
    protected string $providerName;

    /**
     * The url of the resource provider.
     * This value is optional.
     */
    protected string $providerUrl;

    /**
     * Array containing the json decoded data of the provider's response.
     */
    protected array $responseDataArray;

    /**
     * The height of the optional thumbnail.
     * If this paramater is present, thumbnail_url and thumbnail_width
     * must also be present.
     * This value is optional.
     */
    protected int $thumbnailHeight;

    /**
     * A URL to a thumbnail image representing the resource.
     * The thumbnail must respect any maxwidth and maxheight parameters.
     * If this paramater is present, thumbnail_width and thumbnail_height
     * must also be present.
     * Consumers may choose to use this value or not.
     * This value is optional.
     */
    protected string $thumbnailUrl;

    /**
     * The width of the optional thumbnail.
     * If this paramater is present, thumbnail_url and thumbnail_height
     * must also be present.
     * This value is optional.
     */
    protected int $thumbnailWidth;

    /**
     * A text title, describing the resource.
     * This value is optional.
     */
    protected string $title;

    /**
     * The resource type. Valid values, along with value-specific parameters, are described below.
     * This value is required.
     */
    protected string $type;

    /**
     * The oEmbed version number. This must be 1.0.
     * This value is required.
     */
    protected string $version;

    private Configuration $configuration;

    /**
     * Getter for the name of the author/owner of the resource.
     */
    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    /**
     * Getter for the URL for the author/owner of the resource.
     */
    public function getAuthorUrl(): string
    {
        return $this->authorUrl;
    }

    /**
     * Getter for the suggested cache lifetime for this resource, in seconds.
     */
    public function getCacheAge(): int
    {
        return $this->cacheAge;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * Getter for the name of the resource provider.
     */
    public function getProviderName(): string
    {
        return $this->providerName;
    }

    /**
     * Getter for the url of the resource provider.
     */
    public function getProviderUrl(): string
    {
        return $this->providerUrl;
    }

    /**
     * Getter for the the json decoded data of the provider's response.
     */
    public function getResponseDataArray(): array
    {
        return $this->responseDataArray;
    }

    /**
     * Getter for the height of the optional thumbnail.
     */
    public function getThumbnailHeight(): int
    {
        return $this->thumbnailHeight;
    }

    /**
     * Getter for the URL to a thumbnail image representing the resource.
     */
    public function getThumbnailUrl(): string
    {
        return $this->thumbnailUrl;
    }

    /**
     * Getter for the width of the optional thumbnail.
     */
    public function getThumbnailWidth(): int
    {
        return $this->thumbnailWidth;
    }

    /**
     * Getter for the text title, describing the resource.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Getter for the resource type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Getter for the resource type.
     */
    public function getTypePartialName(): string
    {
        return ucfirst($this->type);
    }

    /**
     * Getter for the oEmbed version number.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Initializes the response parameters that are valid for all
     * response types.
     *
     * @param array $responseData the parsed json response
     */
    public function initializeResponseData(array $responseData, Configuration $configuration): void
    {
        $this->responseDataArray = $responseData;
        $this->configuration = $configuration;
        $this->type = (string)$this->getValueFromResponseData('type');
        $this->version = (string)$this->getValueFromResponseData('version');
        $this->title = (string)$this->getValueFromResponseData('title');
        $this->authorName = (string)$this->getValueFromResponseData('author_name');
        $this->authorUrl = (string)$this->getValueFromResponseData('author_url');
        $this->providerName = (string)$this->getValueFromResponseData('provider_name');
        $this->providerUrl = (string)$this->getValueFromResponseData('provider_url');
        $this->cacheAge = (int)$this->getValueFromResponseData('cache_age');
        $this->thumbnailUrl = (string)$this->getValueFromResponseData('thumbnail_url');
        $this->thumbnailWidth = (int)$this->getValueFromResponseData('thumbnail_width');
        $this->thumbnailHeight = (int)$this->getValueFromResponseData('thumbnail_height');
        $this->initializeTypeSpecificResponseData();
    }

    /**
     * Initializes the response parameters that are specific for this
     * resource type.
     */
    public function initializeTypeSpecificResponseData(): void {}

    /**
     * Retrieves a value from the response array or returns NULL if the array key is not set.
     *
     * @return mixed
     */
    protected function getValueFromResponseData(string $key)
    {
        return $this->responseDataArray[$key] ?? null;
    }
}
