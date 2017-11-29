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
 * An oEmbed provider.
 */
class Provider extends AbstractEntity
{
    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $embedlyShortname;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var bool
     */
    protected $isGeneric;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $sorting;

    /**
     * @var string
     */
    protected $urlSchemes;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Sto\Mediaoembed\Domain\Model\Provider>
     * @lazy
     */
    protected $useGenericProviders;

    /**
     * Checks, if the given provider equals this provider.
     *
     * @param Provider $provider
     * @return boolean TRUE if provider is equal.
     */
    public function equals($provider)
    {
        if ($provider instanceof Provider) {
            if ($this->getUid() === $provider->getUid()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Getter for all endpoints of this provider (native and generic)
     *
     * @return array Array containing all endpoint urls of this provider (native and generic).
     */
    public function getAllEndpoints()
    {
        $endpoints = [];

        $nativeEndpoint = $this->getEndpoint();
        if (!empty($nativeEndpoint)) {
            $endpoints[] = $nativeEndpoint;
        }

        $endpoints = array_merge($endpoints, $this->getGenericEndpoints());

        return $endpoints;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getEmbedlyShortname()
    {
        return $this->embedlyShortname;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Getter for the generic endpoints this provider should use.
     *
     * @return array
     */
    public function getGenericEndpoints()
    {
        /** @var Provider $genericProvider */
        $genericEndpoints = [];
        $genericProviders = $this->getUseGenericProviders();
        foreach ($genericProviders as $genericProvider) {
            $genericEndpoints[] = $genericProvider->getEndpoint();
        }

        return $genericEndpoints;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @return string
     */
    public function getUrlSchemes()
    {
        return trim($this->urlSchemes);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Sto\Mediaoembed\Domain\Model\Provider>
     */
    public function getUseGenericProviders()
    {
        return $this->useGenericProviders;
    }

    /**
     * @return bool
     */
    public function isIsGeneric()
    {
        return $this->isGeneric;
    }
}
