<?php
namespace Sto\Mediaoembed\Response;

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
 * This type is used for representing static photos.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 */
class PhotoResponse extends GenericResponse {
	/**
	 * The height in pixels of the image specified in the url parameter.
	 * This value is required.
	 *
	 * @var string
	 */
	protected $height;

	/**
	 * Path to the local version of the photo.
	 *
	 * @var string
	 */
	protected $localPath;

	/**
	 * The width in pixels of the image specified in the url parameter.
	 * This value is required.
	 *
	 * @var string
	 */
	protected $width;

	/**
     * The source URL of the image.
     * Consumers should be able to insert this URL into an <img> element.
     * Only HTTP and HTTPS URLs are valid.
     * This value is required.
     *
     * @var string
     */
	protected $url;

	/**
	 * Initializes the response parameters that are specific for this
	 * resource type.
	 */
	public function initializeTypeSpecificResponseData() {
		$this->url = $this->responseDataArray['url'];
		$this->width = $this->responseDataArray['width'];
		$this->height = $this->responseDataArray['height'];
	}

	/**
	 * Getter for the height in pixels of the image specified in the url parameter.
	 *
	 * @return string
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * Getter for the path to a locally stored version of the image.
	 *
	 * @return string
	 */
	public function getLocalPath() {

		if (!isset($this->localPath)) {
			$this->downloadPhoto();
		}
		return $this->localPath;
	}

	/**
	 * Getter for the source URL of the image.
	 *
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Getter for the width in pixels of the image specified in the url parameter.
	 *
	 * @return string
	 */
	public function getWidth() {
		return $this->width;
	}

	/**
	 * Downloads the photo from the server and stores it in the typo3temp folder.
	 *
	 * @return void
	 * TODO: Use _processed folder
	 */
	protected function downloadPhoto() {

		$imageData = \TYPO3\CMS\Core\Utility\GeneralUtility::getURL($this->getUrl());

		$imageFilename = basename($this->getUrl());
		$imageFilename = preg_replace('/[^a-z0-9\._-]/i', '', $imageFilename);
		$imagePrefix = \TYPO3\CMS\Core\Utility\GeneralUtility::md5int($imageData);
		$imageFilename = $imagePrefix . '_' . $imageFilename;
		$imagePathAndFilename = 'typo3temp/tx_mediaoembed/' . $imageFilename;

		\TYPO3\CMS\Core\Utility\GeneralUtility::writeFileToTypo3tempDir(PATH_site . $imagePathAndFilename, $imageData);
		$this->localPath = $imagePathAndFilename;
	}
}
?>