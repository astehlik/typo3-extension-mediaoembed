<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Install;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Update class for the install tool that migrates the old media content
 * types with render type tx_mediaoembed to the new external media
 * content element
 */
class MigrateContentElementsUpdate implements UpgradeWizardInterface
{
    use MigrateContentElementsUpdateTrait;

    public function getDescription(): string
    {
        return $this->getFlexFormUpdateHandler()->getDescription();
    }

    /**
     * Return the identifier for this wizard
     * This should be the same string as used in the ext_localconf class registration
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'tx_mediaoembed_migratecontentelements';
    }

    /**
     * Return the speaking name of this wizard
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'mediaoembed - Migrate content elements';
    }

    /**
     * Execute the update
     *
     * Called when a wizard reports that an update is necessary
     *
     * @return bool
     */
    public function executeUpdate(): bool
    {
        $dbQueries = [];
        $customMessages = '';
        return $this->getFlexFormUpdateHandler()->performUpdate($dbQueries, $customMessages);
    }

    /**
     * Is an update necessary?
     *
     * Is used to determine whether a wizard needs to be run.
     * Check if data for migration exists.
     *
     * @return bool
     */
    public function updateNecessary(): bool
    {
        return true;
        return $this->getFlexFormUpdateHandler()->checkForUpdate();
    }

    /**
     * Returns an array of class names of Prerequisite classes
     *
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [DatabaseUpdatedPrerequisite::class];
    }
}
