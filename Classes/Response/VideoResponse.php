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

use Sto\Mediaoembed\Response\Behavior\AspectRatioTrait;
use Sto\Mediaoembed\Response\Contract\AspectRatioAwareResponseInterface;

/**
 * This type is used for representing playable videos.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 * If a provider wishes the consumer to just provide a thumbnail, rather than an
 * embeddable player, they should instead return a photo response type.
 */
class VideoResponse extends GenericResponse implements AspectRatioAwareResponseInterface, HtmlAwareResponseInterface
{
    use AspectRatioTrait;

    /**
     * The height in pixels required to display the HTML.
     * This value is required.
     */
    protected int $height = 0;

    /**
     * The HTML required to embed a video player.
     * The HTML should have no padding or margins.
     * Consumers may wish to load the HTML in an off-domain iframe to avoid
     * XSS vulnerabilities.
     * This value is required.
     */
    protected string $html;

    /**
     * The width in pixels required to display the HTML.
     * This value is required.
     */
    protected int $width = 0;

    /**
     * Getter for the height in pixels required to display the HTML.
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Getter for the HTML required to embed a video player.
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * Getter for the width in pixels required to display the HTML.
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Initializes the response parameters that are specific for this
     * resource type.
     */
    public function initializeTypeSpecificResponseData(): void
    {
        $this->html = $this->responseDataArray['html'];
        $this->width = (int)($this->responseDataArray['width'] ?? 0);
        $this->height = (int)($this->responseDataArray['height'] ?? 0);
    }

    public function setHtml(string $html): void
    {
        $this->html = $html;
    }
}
