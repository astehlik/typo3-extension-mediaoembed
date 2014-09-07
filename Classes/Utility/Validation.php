<?php
namespace Sto\Mediaoembed\Utility;

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
 * Utility class for validating input
 */
class Validation {

	/**
	 * Gets a valid width or height value, or NULL if none was set.
	 * Valid values are positive integers or empty values.
	 *
	 * @param int $value
	 * @return int If valid width / height was set, or NULL if value was empty
	 */
	public static function getValidWithHeightValue($value) {

		if (empty($value)) {
			return NULL;
		}

		$value = intval($value);
		if ($value < 1) {
			throw new \InvalidArgumentException('Invalid width or height value. Only positive integers are allowed.', 1303846809);
		}

		return $value;
	}
}