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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * Update class for the install tool that migrates the old media content
 * types with render type tx_mediaoembed to the new external media
 * content element
 */
class MigrateContentElementsUpdate extends AbstractUpdate
{
    const RENDER_TYPE = 'tx_mediaoembed';

    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $db;

    /**
     * Title of this update that is displayed in the install tool
     *
     * @var string
     */
    protected $title = 'mediaoembed - Migrate content elements';

    /**
     * Initializes the database connection
     */
    public function __construct()
    {
        $this->db = $GLOBALS['TYPO3_DB'];
    }

    /**
     * Checks whether updates are required.
     *
     * @param string &$description : The description for the update
     * @return boolean Whether an update is required (TRUE) or not (FALSE)
     */
    public function checkForUpdate(&$description)
    {
        $description = 'All media content elements that use oEmbed as
		their render type will be migrated to "external media" content
		elements that are used in the current version of mediaoembed.';
        $res = $this->db->exec_SELECTquery(
            'uid',
            'tt_content',
            'CType=\'media\' AND pi_flexform LIKE \'%<field index="mmRenderType">%<value index="vDEF">'
            . self::RENDER_TYPE
            . '</value>%\''
        );
        $oldRecords = $this->db->sql_num_rows($res);
        if ($oldRecords) {
            $description .= '<br />There are currently <strong>' . $oldRecords . '</strong> records to update.<br />';
            return true;
        } else {
            return false;
        }
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
        $res = $this->db->exec_SELECTquery(
            'uid, pi_flexform',
            'tt_content',
            'CType=\'media\' AND pi_flexform LIKE \'%<field index="mmRenderType">%<value index="vDEF">'
            . self::RENDER_TYPE
            . '</value>%\''
        );
        $updateCounter = 0;
        $hasError = false;

        while ($row = $this->db->sql_fetch_assoc($res)) {
            $flexFormData = GeneralUtility::xml2array($row['pi_flexform']);

            if (!is_array($flexFormData)) {
                $customMessages .= sprintf(
                    'Skipping content element with uid %d because of XML parsing error: %s' . "\n",
                    $row['uid'],
                    $flexFormData
                );
                $hasError = true;
                continue;
            }

            if (!isset($flexFormData['data']['sVideo']['lDEF']['mmFile']['vDEF'])
                || empty($flexFormData['data']['sVideo']['lDEF']['mmFile']['vDEF'])
            ) {
                $customMessages .= sprintf(
                    'Skipping content element with uid %d because mmFile is not set' . "\n",
                    $row['uid']
                );
                $hasError = true;
                continue;
            }

            $flexFormGeneralData = $flexFormData['data']['sGeneral']['lDEF'];
            $mediaUrl = $flexFormData['data']['sVideo']['lDEF']['mmFile']['vDEF'];

            if ($flexFormGeneralData['mmRenderType']['vDEF'] !== self::RENDER_TYPE) {
                continue;
            }

            $updateData = [
                'CType' => 'mediaoembed_oembedmediarenderer',
                'pi_flexform' => '',
                'tx_mediaoembed_maxwidth' => isset($flexFormGeneralData['mmWidth']['vDEF']) ? intval(
                    $flexFormGeneralData['mmWidth']['vDEF']
                ) : 0,
                'tx_mediaoembed_maxheight' => isset($flexFormGeneralData['mmWidth']['vDEF']) ? intval(
                    $flexFormGeneralData['mmHeight']['vDEF']
                ) : 0,
                'tx_mediaoembed_url' => $mediaUrl,
            ];

            $updateQuery = $this->db->UPDATEquery('tt_content', 'uid=' . $row['uid'], $updateData);
            $this->db->sql_query($updateQuery);
            $hasError = ($hasError || $this->hasError($customMessages));
            $dbQueries[] = $updateQuery;
            $updateCounter++;
        }

        return !$hasError;
    }

    /**
     * @param mixed &$customMessages Custom messages
     *
     * @return boolean
     */
    protected function hasError(&$customMessages)
    {
        $hasError = false;
        if ($this->db->sql_error()) {
            $customMessages .= 'SQL-ERROR: ' . $this->db->sql_error() . "\n";
            $hasError = true;
        }

        return $hasError;
    }
}
