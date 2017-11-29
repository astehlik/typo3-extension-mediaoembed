<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Domain\Model;

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
 * A mediaoembed tt_content record.
 */
class Content
{
    /**
     * @var int
     */
    protected $maxHeight;

    /**
     * @var int
     */
    protected $maxWidth;

    /**
     * @var string
     */
    protected $url;

    /**
     * @return int
     */
    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    /**
     * @return int
     */
    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setMaxHeight(int $maxHeight)
    {
        $this->maxHeight = $maxHeight;
    }

    public function setMaxWidth(int $maxWidth)
    {
        $this->maxWidth = $maxWidth;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }
}
