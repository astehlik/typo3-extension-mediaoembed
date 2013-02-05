<?php
namespace Sto\Mediaoembed\Task;

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
 */
class ImportFromEmbedlyTask extends \TYPO3\CMS\Extbase\Scheduler\Task {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $embedlyServicesUrl = 'http://api.embed.ly/1/services';

	/**
	 * @var int
	 */
	protected $uidEmbedlyProvider = 1;

	/**
	 * Initializes the database connection
	 *
	 * @return ImportFromEmbedlyTask
	 */
	public function __construct() {
		parent::__construct();
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Function executed from the Scheduler.
	 *
	 * @return boolean
	 */
	public function execute() {

		$json = \TYPO3\CMS\Core\Utility\GeneralUtility::getURL($this->embedlyServicesUrl);

		$services = json_decode($json, TRUE);

		$embedlyService[] = array(
			'name' => 'embedly',
			'displayname' => 'embed.ly',
			'about' => 'One API to Rule them All. The Embedly API allows developers to embed videos, images and rich media from 212 services through one API.',
		);

		$services = array_merge($embedlyService, $services);

		$sorting = 1;

		foreach ($services as $serviceData) {

			$currentProviderResult = $this->db->exec_SELECTquery(
				'*',
				'tx_mediaoembed_provider',
				'embedly_shortname=' . $this->db->fullQuoteStr($serviceData['name'], 'tx_mediaoembed_provider')
			);

			if ($currentProviderResult === FALSE) {
				throw new \RuntimeException('Error while getting provider data from database.', 1303848921);
			}

			$urlSchemes = $serviceData['regex'];

			$updateArray = array(
				'name' => $serviceData['displayname'],
				'description' => $serviceData['about'],
				'sorting' => $sorting,
			);

			if ($this->db->sql_num_rows($currentProviderResult)) {

				$currentProviderData = $this->db->sql_fetch_assoc($currentProviderResult);

				if ($currentProviderData['is_generic']) {
					continue;
				}

				if (!\TYPO3\CMS\Core\Utility\GeneralUtility::inList($currentProviderData['use_generic_providers'], $this->uidEmbedlyProvider)) {
					$genericEndpointArray = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $currentProviderData['use_generic_providers'], TRUE);
					$genericEndpointArray[] = $this->uidEmbedlyProvider;
					$updateArray['use_generic_providers'] = implode(',', $genericEndpointArray);
				}

				$this->db->exec_UPDATEquery(
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

				$this->db->exec_INSERTquery(
					'tx_mediaoembed_provider',
					$insertArray
				);
			}

			$sorting += 512;
		}

		return TRUE;
	}
}

?>