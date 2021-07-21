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
 * This type is used for representing playable videos.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 * If a provider wishes the consumer to just provide a thumbnail, rather than an
 * embeddable player, they should instead return a photo response type.
 */
class VideoResponse extends GenericResponse implements AspectRatioAwareResponseInterface, HtmlAwareResponseInterface
{
    const ASPECT_RATIO_16TO9 = '16to9';

    const ASPECT_RATIO_4TO3 = '4to3';

    /**
     * The height in pixels required to display the HTML.
     * This value is required.
     *
     * @var int
     */
    protected $height = 0;

    /**
     * The HTML required to embed a video player.
     * The HTML should have no padding or margins.
     * Consumers may wish to load the HTML in an off-domain iframe to avoid
     * XSS vulnerabilities.
     * This value is required.
     *
     * @var string
     */
    protected $html;

    /**
     * The width in pixels required to display the HTML.
     * This value is required.
     *
     * @var int
     */
    protected $width = 0;

    /**
     * Initializes the response parameters that are specific for this
     * resource type.
     */
    public function initializeTypeSpecificResponseData()
    {
        $this->html = $this->responseDataArray['html'];
        $this->width = (int)$this->responseDataArray['width'];
        $this->height = (int)$this->responseDataArray['height'];
    }

    /**
     * Returns the current aspect ratio.
     *
     * @return float
     */
    public function getAspectRatio(): float
    {
        if ($this->getHeight() === 0) {
            return 0;
        }

        return $this->getWidth() / $this->getHeight();
    }

    /**
     * Returns TRUE if the current aspect ratio looks like 16 to 9.
     *
     * @return bool
     */
    public function getAspectRatioIs16To9(): bool
    {
        return $this->getAspectRatioType() === static::ASPECT_RATIO_16TO9;
    }

    /**
     * Returns TRUE if the current aspect ratio looks like 4 to 3.
     *
     * @return bool
     */
    public function getAspectRatioIs4To3(): bool
    {
        return $this->getAspectRatioType() === static::ASPECT_RATIO_4TO3;
    }

    /**
     * Returns one of the ASPECT_RATIO_* constants depending on the current aspect ratio.
     *
     * @return string
     */
    public function getAspectRatioType(): string
    {
        $ratio4To3 = 4 / 3;
        $ratio16To9 = 16 / 9;
        $currentRatio = $this->getAspectRatio();

        $ratioDiff4To3 = $currentRatio - $ratio4To3;
        $ratioDiff16To9 = $currentRatio - $ratio16To9;

        if (abs($ratioDiff4To3) < abs($ratioDiff16To9)) {
            return static::ASPECT_RATIO_4TO3;
        }

        return static::ASPECT_RATIO_16TO9;
    }

    /**
     * Getter for the height in pixels required to display the HTML.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Getter for the HTML required to embed a video player.
     *
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * Getter for the width in pixels required to display the HTML.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    public function setHtml(string $html)
    {
        $this->html = $html;
    }
}
