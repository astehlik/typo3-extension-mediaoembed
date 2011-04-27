<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009-2011 François Suter <francois@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Class "tx_scheduler_SleepTask" provides a task that sleeps for some time
 * This is useful for testing parallel executions
 *
 * @author		François Suter <francois@typo3.org>
 * @package		TYPO3
 * @subpackage	tx_scheduler
 */
class tx_Mediaoembed_Tasks_ImportFromOhhembedTask extends tx_scheduler_Task {

	protected $ohhembedServicesUrl = 'http://oohembed.com/static/endpoints.json';

	/**
	 * Function executed from the Scheduler.
	 * Goes to sleep ;-)
	 *
	 * @return	void
	 */
	public function execute() {

		$json = t3lib_div::getURL($this->ohhembedServicesUrl);

		$services = json_decode($json, TRUE);

		foreach ($services as $serviceData) {

			$endpoint = $GLOBALS['TYPO3_DB']->escapeStrForLike($serviceData['endpoint_url'], 'tx_mediaoembed_provider');
			$endpoint = $GLOBALS['TYPO3_DB']->fullQuoteStr('%' . $endpoint . '%', 'tx_mediaoembed_provider');

			$names = explode(' ', $serviceData['title']);
			$namequery = '1=0';
			foreach ($names as $name) {
				$name = $GLOBALS['TYPO3_DB']->escapeStrForLike($name);
				$name = $GLOBALS['TYPO3_DB']->fullQuoteStr('%' . $name . '%', 'tx_mediaoembed_provider');
				$namequery .= ' OR name LIKE ' . $name;
			}

			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tx_mediaoembed_provider',
				'(' . $namequery . ') OR endpoint LIKE ' . $endpoint
			);

			if ($result === FALSE) {
				continue;
			}

			$providerExists = intval($GLOBALS['TYPO3_DB']->sql_num_rows($result));

			if ($providerExists) {

				$currentProviderData = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
				if (!empty($currentProviderData['endpoint'])) {
					continue;
				}

				$updateArray = array(
					'tstamp' => time(),
					'endpoint' => $serviceData['endpoint_url'],
				);

				$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
					'tx_mediaoembed_provider',
					'uid=' . intval($currentProviderData['uid']),
					$updateArray
				);

			} else {

				$insertArray = array(
					'crdate' => time(),
					'tstamp' => time(),
					'name' => $serviceData['title'],
					'endpoint' => $serviceData['endpoint_url'],
					'url_schemes' => $serviceData['url'],
				);

				$GLOBALS['TYPO3_DB']->exec_INSERTquery(
					'tx_mediaoembed_provider',
					$insertArray
				);
			}
		}

		return TRUE;
	}
}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mediaoembed/Classes/Tasks/ImportFromOhhembedTask.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mediaoembed/Classes/Tasks/ImportFromOhhembedTask.php']);
}

?>