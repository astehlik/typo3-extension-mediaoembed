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
 * oohembed.com
 */
class ImportFromOhhembedTask extends \TYPO3\CMS\Extbase\Scheduler\Task {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $db;

	protected $ohhembedServicesUrl = 'http://oohembed.com/static/endpoints.json';

	/**
	 * Initializes the database connection
	 */
	public function __construct() {
		parent::__construct();
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Function executed from the Scheduler.
	 * Goes to sleep ;-)
	 *
	 * @return boolean
	 */
	public function execute() {

		$json = \TYPO3\CMS\Core\Utility\GeneralUtility::getURL($this->ohhembedServicesUrl);

		$services = json_decode($json, TRUE);

		foreach ($services as $serviceData) {

			$endpoint = $this->db->escapeStrForLike($serviceData['endpoint_url'], 'tx_mediaoembed_provider');
			$endpoint = $this->db->fullQuoteStr('%' . $endpoint . '%', 'tx_mediaoembed_provider');

			$names = explode(' ', $serviceData['title']);
			$namequery = '1=0';
			foreach ($names as $name) {
				$name = $this->db->escapeStrForLike($name, 'tx_mediaoembed_provider');
				$name = $this->db->fullQuoteStr('%' . $name . '%', 'tx_mediaoembed_provider');
				$namequery .= ' OR name LIKE ' . $name;
			}

			$result = $this->db->exec_SELECTquery(
				'*',
				'tx_mediaoembed_provider',
				'(' . $namequery . ') OR endpoint LIKE ' . $endpoint
			);

			if ($result === FALSE) {
				continue;
			}

			$providerExists = intval($this->db->sql_num_rows($result));

			if ($providerExists) {

				$currentProviderData = $this->db->sql_fetch_assoc($result);
				if (!empty($currentProviderData['endpoint'])) {
					continue;
				}

				$updateArray = array(
					'tstamp' => time(),
					'endpoint' => $serviceData['endpoint_url'],
				);

				$this->db->exec_UPDATEquery(
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

				$this->db->exec_INSERTquery(
					'tx_mediaoembed_provider',
					$insertArray
				);
			}
		}

		return TRUE;
	}
}

?>