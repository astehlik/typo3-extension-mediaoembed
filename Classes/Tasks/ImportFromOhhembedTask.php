<?php
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
 * This task can create / update providers with the data read from
 * oohembed.com
 *
 * @package mediaoembed
 * @subpackage Tasks
 * @version $Id:$
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