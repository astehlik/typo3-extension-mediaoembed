<?php
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

use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository for mediaoembed tt_content elements.
 *
 * @method \Sto\Mediaoembed\Domain\Model\Provider findByUid($uid)
 * @method \TYPO3\CMS\Extbase\Persistence\QueryResultInterface findByIsGeneric($isGeneric)
 */
class ProviderRepository extends Repository {

	/**
	 * Make sure we always ignore the storage page config.
	 */
	public function initializeObject() {
		$this->defaultQuerySettings = $this->createQuery()->getQuerySettings();
		$this->defaultQuerySettings->setRespectStoragePage(FALSE);
	}

	/**
	 * Searches for a provider by the given UID. Only returns a result if the found provider is generic.
	 *
	 * @param int $uid
	 * @return \Sto\Mediaoembed\Domain\Model\Provider|NULL
	 */
	public function findGenericByUid($uid) {
		$provider = $this->findByUid($uid);
		if (!isset($provider)) {
			return NULL;
		}
		if (!$provider->isIsGeneric()) {
			return NULL;
		}
		return $provider;
	}
}