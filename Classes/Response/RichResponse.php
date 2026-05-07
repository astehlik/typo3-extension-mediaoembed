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

use Sto\Mediaoembed\Response\Contract\AspectRatioAwareResponseInterface;
use Sto\Mediaoembed\Response\Trait\AspectRatioTrait;

/**
 * This type is used for rich HTML content that does not fall under one of
 * the other categories.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 */
class RichResponse extends GenericResponse implements AspectRatioAwareResponseInterface, HtmlAwareResponseInterface
{
    use AspectRatioTrait;

    /**
     * The height in pixels required to display the HTML.
     * This value is required.
     */
    protected int $height;

    /**
     * The HTML required to display the resource.
     * The HTML should have no padding or margins.
     * Consumers may wish to load the HTML in an off-domain iframe to avoid
     * XSS vulnerabilities.
     * The markup should be valid XHTML 1.0 Basic.
     * This value is required.
     */
    protected string $html;

    /**
     * The width in pixels required to display the HTML.
     * This value is required.
     */
    protected int $width;

    /**
     * Getter for the height in pixels required to display the HTML.
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Getter for the HTML required to display the resource.
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

        $this->width = (int)$this->responseDataArray['width'];
        $this->height = (int)$this->responseDataArray['height'];
    }

    public function setHtml(string $html): void
    {
        $this->html = $html;
    }
}
