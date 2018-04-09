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
 * Update class for the install tool that creates the columns in the
 * tt_content table that are required for migrating the media elements
 * to the new format
 */
class CreateRequiredColumnsUpdate extends AbstractUpdate
{
    const RENDER_TYPE = 'tx_mediaoembed';

    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $db;

    /**
     * @var NULL|\TYPO3\CMS\Install\Service\SqlSchemaMigrationService
     */
    protected $installToolSqlParser = null;

    /**
     * Title of this update that is displayed in the install tool
     *
     * @var string
     */
    protected $title = 'mediaoembed - Create required columns';

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
        $description = 'Creates the columns in the tt_content table that are required for the new version'
            . ' of mediaoembed. Please make sure you execute this update before executing the content'
            . ' element migration update!';
        $result = false;

        // First check necessary database update
        $updateStatements = $this->getUpdateStatements();
        if (empty($updateStatements) || $this->checkValidStatements($updateStatements)) {
            // Check for repository database table
            $databaseTables = $this->db->admin_get_tables();
            if (!isset($databaseTables['tt_content'])) {
                $result = true;
            }
        } else {
            $result = true;
        }

        return $result;
    }

    /**
     * As experimented in TYPO3 v8, TYPO3 transforms the instruction from ext_tables.sql from:
     *
     * "hidden tinyint(3) unsigned DEFAULT '0' NOT NULL"
     * -> into:
     * "smallint(5) unsigned NOT NULL default '0'"
     *
     * If so, we want to inform TYPO3, there is nothing to do in the Install Wizard and we keep the database as it is.
     * Otherwise, we enter an infinite "loop" where the Install Wizard is changing the table structure towards "smallint" and the
     * "Compare current database with specification" is changing back towards "tinyint"
     *
     * @param array $updateStatements
     * @return bool
     */
    public function checkValidStatements(array $updateStatements) : bool
    {
        if (isset($updateStatements['change_currentValue']) && \is_array($updateStatements['change_currentValue'])) {
            foreach ($updateStatements['change_currentValue'] as $statement) {
                if (!preg_match('/smallint(5) unsigned NOT NULL default/', $statement)) {
                    return true;
                }
            }
        }
        return false;
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
        $hasError = false;

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
     * @return \TYPO3\CMS\Install\Service\SqlSchemaMigrationService
     */
    protected function getInstallToolSqlParser()
    {
        if ($this->installToolSqlParser === null) {
            $this->installToolSqlParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                'TYPO3\\CMS\\Install\\Service\\SqlSchemaMigrationService'
            );
        }

        return $this->installToolSqlParser;
    }

    /**
     * Gets all create, add and change queries from ext_tables.sql
     *
     * @return array
     */
    protected function getUpdateStatements()
    {
        $updateStatements = [];

        // Get all necessary statements for ext_tables.sql file
        $rawDefinitions = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl(
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mediaoembed') . '/ext_tables.sql'
        );
        $fieldDefinitionsFromFile = $this->getInstallToolSqlParser()->getFieldDefinitions_fileContent($rawDefinitions);
        if (count($fieldDefinitionsFromFile)) {
            $fieldDefinitionsFromCurrentDatabase = $this->getInstallToolSqlParser()->getFieldDefinitions_database();
            $diff = $this->getInstallToolSqlParser()->getDatabaseExtra(
                $fieldDefinitionsFromFile,
                $fieldDefinitionsFromCurrentDatabase
            );
            $updateStatements = $this->getInstallToolSqlParser()->getUpdateSuggestions($diff);
        }

        return $updateStatements;
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
