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
 * At the moment it is not possible for hooks to access the current
 * content object. This XCLASS provides a public getCObj method
 * so that the current content object will become accessible.
 *
 * @package mediaoembed
 * @subpackage Ux
 * @version $Id:$
 */
class ux_tslib_content_Media extends tslib_content_Media {

	/**
	 * Returns the current content object
	 *
	 * @return tslib_cObj
	 */
	public function getCObj() {
		return $this->cObj;
	}

}