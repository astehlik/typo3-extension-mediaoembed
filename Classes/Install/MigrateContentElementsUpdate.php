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

use Sto\Mediaoembed\Install\Repository\UpdateRepositoryFactory;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * Update class for the install tool that migrates the old media content
 * types with render type tx_mediaoembed to the new external media
 * content element
 */
class MigrateContentElementsUpdate extends AbstractUpdate
{
    /**
     * Title of this update that is displayed in the install tool
     *
     * @var string
     */
    protected $title = 'mediaoembed - Migrate content elements';

    /**
     * @var FlexFormUpdateHandler
     */
    private $flexFormUpdateHandler;

    /**
     * Checks whether updates are required.
     *
     * @param string &$description : The description for the update
     * @return boolean Whether an update is required (TRUE) or not (FALSE)
     */
    public function checkForUpdate(&$description)
    {
        return $this->getFlexFormUpdateHandler()->checkForUpdate($description);
    }

    /**
     * Performs the accordant updates.
     *
     * @param array &$dbQueries : queries done in this update
     * @param mixed &$customMessages : custom messages
     * @return boolean Whether everything went smoothly or not
     */
    public function performUpdate(array &$dbQueries, &$customMessages)
    {
        return $this->getFlexFormUpdateHandler()->performUpdate($dbQueries, $customMessages);
    }

    private function getFlexFormUpdateHandler()
    {
        if ($this->flexFormUpdateHandler) {
            return $this->flexFormUpdateHandler;
        }

        $updateRepository = UpdateRepositoryFactory::getUpdateRepository();
        $this->flexFormUpdateHandler = new FlexFormUpdateHandler($updateRepository);
        return $this->flexFormUpdateHandler;
    }
}
