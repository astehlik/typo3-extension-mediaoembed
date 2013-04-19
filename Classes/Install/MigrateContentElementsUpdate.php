<?php
namespace Sto\Mediaoembed\Install;

/*                                                                        *
 * This script belongs to the TYPO3 extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Update class for the install tool that migrates the old media content
 * types with render type tx_mediaoembed to the new external media
 * content element
 */
class MigrateContentElementsUpdate extends \TYPO3\CMS\Install\Updates\AbstractUpdate {

	const RENDER_TYPE = 'tx_mediaoembed';

	/**
	 * Title of this update that is displayed in the install tool
	 * @var string
	 */
	protected $title = 'mediaoembed - Migrate content elements';

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $db;

	/**
	 * Initializes the database connection
	 */
	public function __construct() {
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Checks whether updates are required.
	 *
	 * @param string &$description: The description for the update
	 * @return boolean Whether an update is required (TRUE) or not (FALSE)
	 */
	public function checkForUpdate(&$description) {
		$description = 'All media content elements that use oEmbed as
		their render type will be migrated to "external media" content
		elements that are used in the current version of mediaoembed.';
		$res = $this->db->exec_SELECTquery('uid', 'tt_content', 'CType=\'media\' AND pi_flexform LIKE \'%<field index="mmRenderType">%<value index="vDEF">' . self::RENDER_TYPE . '</value>%\'');
		return $this->db->sql_num_rows($res);
	}

	/**
	 * Performs the accordant updates.
	 *
	 * @param array &$dbQueries: queries done in this update
	 * @param mixed &$customMessages: custom messages
	 * @return boolean Whether everything went smoothly or not
	 */
	public function performUpdate(array &$dbQueries, &$customMessages) {

		$res = $this->db->exec_SELECTquery('uid,pi_flexform', 'tt_content', "CType='media'");
		$updateCounter = 0;

		while ($row = $this->db->sql_fetch_assoc($res)) {

			$flexFormData = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($row['pi_flexform']);

			if (!is_array($flexFormData)) {
				$customMessages = sprintf('<p>Skipping content element with uid %d because of XML parsing error: %s</p>', $row['uid'], $flexFormData);
				continue;
			}

			if (!isset($flexFormData['data']['sGeneral']['lDEF']['mmRenderType']['vDEF'])) {
				$customMessages = sprintf('<p>Skipping content element with uid %d because mmRenderType is not set</p>', $row['uid']);
				continue;
			}

			if (!isset($flexFormData['data']['sVideo']['lDEF']['mmFile']['vDEF'])
				|| empty($flexFormData['data']['sVideo']['lDEF']['mmFile']['vDEF'])) {
				$customMessages = sprintf('<p>Skipping content element with uid %d because mmFile is not set</p>', $row['uid']);
				continue;
			}

			$flexFormGeneralData = $flexFormData['data']['sGeneral']['lDEF'];
			$mediaUrl = $flexFormData['data']['sVideo']['lDEF']['mmFile']['vDEF'];

			if ($flexFormGeneralData['mmRenderType']['vDEF'] !== self::RENDER_TYPE) {
				continue;
			}

			$updateData = array(
				'CType' => 'mediaoembed_oembedmediarenderer',
				'pi_flexform' => '',
				'tx_mediaoembed_maxwidth' => isset($flexFormGeneralData['mmWidth']['vDEF']) ? intval($flexFormGeneralData['mmWidth']['vDEF']) : 0,
				'tx_mediaoembed_maxheight' => isset($flexFormGeneralData['mmWidth']['vDEF']) ? intval($flexFormGeneralData['mmHeight']['vDEF']) : 0,
				'tx_mediaoembed_url' => $mediaUrl,
			);

			$updateQuery = $this->db->UPDATEquery('tt_content', 'uid=' . $row['uid'], $updateData);
			$dbQueries[] = $updateQuery;
			$this->db->sql_query($updateQuery);
			$updateCounter++;
		}

		$customMessages = sprintf('<p>Migrated %d content elements</p>', $updateCounter);
		return TRUE;
	}
}