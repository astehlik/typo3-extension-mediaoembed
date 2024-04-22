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

/**
 * This type is used for rich HTML content that does not fall under one of
 * the other categories.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 */
class RichResponse extends GenericResponse implements AspectRatioAwareResponseInterface, HtmlAwareResponseInterface
{
    use AspectRatioAwareResponseTrait;

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
     * Getter for the HTML required to display the resource.
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * Initializes the response parameters that are specific for this
     * resource type.
     */
    public function initializeTypeSpecificResponseData(): void
    {
        $this->html = $this->responseDataArray['html'];

        $this->initializeAspectRatioData($this->responseDataArray);
    }

    public function setHtml(string $html): void
    {
        $this->html = $html;
    }
}
