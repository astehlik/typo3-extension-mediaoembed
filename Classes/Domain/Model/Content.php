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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * A mediaoembed tt_content record.
 */
class Content extends AbstractEntity
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
    public function getMaxHeight()
    {
        return (int)$this->maxHeight;
    }

    /**
     * @return int
     */
    public function getMaxWidth()
    {
        return (int)$this->maxWidth;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
