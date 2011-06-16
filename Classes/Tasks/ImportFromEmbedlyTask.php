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
 * embed.ly
 *
 * @package mediaoembed
 * @subpackage Tasks
 * @version $Id:$
 */
class tx_Mediaoembed_Tasks_ImportFromEmbedlyTask extends tx_scheduler_Task {

	protected $embedlyServicesUrl = 'http://api.embed.ly/1/services';

	protected $uidEmbedlyProvider = 1;

	/**
	 * Function executed from the Scheduler.
	 * Goes to sleep ;-)
	 *
	 * @return	void
	 */
	public function execute() {

		$json = t3lib_div::getURL($this->embedlyServicesUrl);

		$services = json_decode($json, TRUE);

		$embedlyService[] = array(
			'name' => 'embedly',
			'displayname' => 'embed.ly',
			'about' => 'One API to Rule them All. The Embedly API allows developers to embed videos, images and rich media from 212 services through one API.',
		);

		$services = array_merge($embedlyService, $services);

		$sorting = 1;

		foreach ($services as $serviceData) {

			$currentProviderResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tx_mediaoembed_provider',
				'embedly_shortname=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($serviceData['name'], 'tx_mediaoembed_provider')
			);

			if ($currentProviderResult === FALSE) {
				throw new RuntimeException('Error while getting provider data from database.', 1303848921);
			}

			$urlSchemes = $serviceData['regex'];

			$updateArray = array(
				'name' => $serviceData['displayname'],
				'description' => $serviceData['about'],
				'sorting' => $sorting,
			);

			if ($GLOBALS['TYPO3_DB']->sql_num_rows($currentProviderResult)) {

				$currentProviderData = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($currentProviderResult);

				if ($currentProviderData['is_generic']) {
					continue;
				}

				if (!t3lib_div::inList($currentProviderData['use_generic_providers'], $this->uidEmbedlyProvider)) {
					$genericEndpointArray = t3lib_div::intExplode(',', $currentProviderData['use_generic_providers'], TRUE);
					$genericEndpointArray[] = $this->uidEmbedlyProvider;
					$updateArray['use_generic_providers'] = implode(',', $genericEndpointArray);
				}

				$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
					'tx_mediaoembed_provider',
					'uid=' . intval($currentProviderData['uid']),
					$updateArray
				);

			} else {

				$insertArray = $updateArray;
				$insertArray['embedly_shortname'] = $serviceData['name'];
				$insertArray['crdate'] = time();
				$insertArray['tstamp'] = time();
				$insertArray['use_generic_providers'] = $this->uidEmbedlyProvider;
				$insertArray['url_schemes'] = implode(LF, $urlSchemes);

				$GLOBALS['TYPO3_DB']->exec_INSERTquery(
					'tx_mediaoembed_provider',
					$insertArray
				);
			}

			$sorting += 512;
		}

		return TRUE;
	}
}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mediaoembed/Classes/Tasks/ImportFromEmbedlyTask.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mediaoembed/Classes/Tasks/ImportFromEmbedlyTask.php']);
}

?>