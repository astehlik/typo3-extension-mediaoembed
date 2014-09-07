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

use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * Update class for the install tool that creates the columns in the
 * tt_content table that are required for migrating the media elements
 * to the new format
 */
class CreateRequiredColumnsUpdate extends AbstractUpdate {

	const RENDER_TYPE = 'tx_mediaoembed';

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $db;

	/**
	 * @var NULL|\TYPO3\CMS\Install\Service\SqlSchemaMigrationService
	 */
	protected $installToolSqlParser = NULL;

	/**
	 * Title of this update that is displayed in the install tool
	 *
	 * @var string
	 */
	protected $title = 'mediaoembed - Create required columns';

	/**
	 * Initializes the database connection
	 */
	public function __construct() {
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Checks whether updates are required.
	 *
	 * @param string &$description : The description for the update
	 * @return boolean Whether an update is required (TRUE) or not (FALSE)
	 */
	public function checkForUpdate(&$description) {
		$description = 'Creates the columns in the tt_content table that are required for the new version of mediaoembed. Please make sure you execute this update before executing the content element migration update!';
		$result = FALSE;

		// First check necessary database update
		$updateStatements = $this->getUpdateStatements();
		if (empty($updateStatements)) {
			// Check for repository database table
			$databaseTables = $this->db->admin_get_tables();
			if (!isset($databaseTables['tt_content'])) {
				$result = TRUE;
			}
		} else {
			$result = TRUE;
		}

		return $result;
	}

	/**
	 * Gets all create, add and change queries from ext_tables.sql
	 *
	 * @return array
	 */
	protected function getUpdateStatements() {
		$updateStatements = array();

		// Get all necessary statements for ext_tables.sql file
		$rawDefinitions = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mediaoembed') . '/ext_tables.sql');
		$fieldDefinitionsFromFile = $this->getInstallToolSqlParser()->getFieldDefinitions_fileContent($rawDefinitions);
		if (count($fieldDefinitionsFromFile)) {
			$fieldDefinitionsFromCurrentDatabase = $this->getInstallToolSqlParser()->getFieldDefinitions_database();
			$diff = $this->getInstallToolSqlParser()->getDatabaseExtra($fieldDefinitionsFromFile, $fieldDefinitionsFromCurrentDatabase);
			$updateStatements = $this->getInstallToolSqlParser()->getUpdateSuggestions($diff);
		}

		return $updateStatements;
	}

	/**
	 * @return \TYPO3\CMS\Install\Service\SqlSchemaMigrationService
	 */
	protected function getInstallToolSqlParser() {
		if ($this->installToolSqlParser === NULL) {
			$this->installToolSqlParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Install\\Service\\SqlSchemaMigrationService');
		}

		return $this->installToolSqlParser;
	}

	/**
	 * Performs the accordant updates.
	 *
	 * @param array &$dbQueries : queries done in this update
	 * @param mixed &$customMessages : custom messages
	 * @return boolean Whether everything went smoothly or not
	 */
	public function performUpdate(array &$dbQueries, &$customMessages) {

		$hasError = FALSE;

		// First perform all create, add and change queries
		$updateStatements = $this->getUpdateStatements();
		foreach ((array)$updateStatements['add'] as $string) {
			$this->db->admin_query($string);
			$dbQueries[] = $string;
			$hasError = ($hasError || $this->hasError($customMessages));
		}
		foreach ((array)$updateStatements['change'] as $string) {
			$this->db->admin_query($string);
			$dbQueries[] = $string;
			$hasError = ($hasError || $this->hasError($customMessages));
		}
		foreach ((array)$updateStatements['create_table'] as $string) {
			$this->db->admin_query($string);
			$dbQueries[] = $string;
			$hasError = ($hasError || $this->hasError($customMessages));
		}

		return !$hasError;
	}

	/**
	 * @param mixed &$customMessages Custom messages
	 *
	 * @return boolean
	 */
	protected function hasError(&$customMessages) {
		$hasError = FALSE;
		if ($this->db->sql_error()) {
			$customMessages .= 'SQL-ERROR: ' . $this->db->sql_error() . "\n";
			$hasError = TRUE;
		}

		return $hasError;
	}
}