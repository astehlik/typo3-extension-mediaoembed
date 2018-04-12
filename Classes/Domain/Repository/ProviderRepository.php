<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Sto\Mediaoembed\Domain\Model\Provider;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository for mediaoembed tt_content elements.
 *
 * @method Provider findByUid($uid)
 * @method QueryResultInterface findByIsGeneric($isGeneric)
 */
class ProviderRepository extends Repository
{
    /**
     * Make sure we always ignore the storage page config.
     */
    public function initializeObject()
    {
        $this->defaultQuerySettings = $this->createQuery()->getQuerySettings();
        $this->defaultQuerySettings->setRespectStoragePage(false);
    }

    /**
     * Searches for a provider by the given UID. Only returns a result if the found provider is generic.
     *
     * @param int $uid
     * @return \Sto\Mediaoembed\Domain\Model\Provider|NULL
     */
    public function findGenericByUid($uid)
    {
        $provider = $this->findByUid($uid);
        if (!isset($provider)) {
            return null;
        }
        if (!$provider->isIsGeneric()) {
            return null;
        }
        return $provider;
    }
}
