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

use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * Update class for the install tool that migrates the old media content
 * types with render type tx_mediaoembed to the new external media
 * content element.
 */
class MigrateContentElementsUpdateLegacy extends AbstractUpdate
{
    use MigrateContentElementsUpdateTrait;

    /**
     * Title of this update that is displayed in the install tool.
     *
     * @var string
     */
    protected $title = 'mediaoembed - Migrate content elements';

    /**
     * Checks whether updates are required.
     *
     * @param string &$description : The description for the update
     *
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     */
    public function checkForUpdate(&$description)
    {
        $updateHandler = $this->getFlexFormUpdateHandler();

        $description = $updateHandler->getDescription();

        return $updateHandler->checkForUpdate();
    }

    public function getDescription(): string
    {
        return $this->getFlexFormUpdateHandler()->getDescription();
    }

    /**
     * Performs the accordant updates.
     *
     * @param array &$dbQueries : queries done in this update
     * @param mixed &$customMessages : custom messages
     *
     * @return bool Whether everything went smoothly or not
     */
    public function performUpdate(array &$dbQueries, &$customMessages)
    {
        return $this->getFlexFormUpdateHandler()->performUpdate($dbQueries, $customMessages);
    }
}
