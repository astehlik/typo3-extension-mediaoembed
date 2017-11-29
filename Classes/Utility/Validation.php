<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Utility class for validating input
 */
class Validation
{
    /**
     * Gets a valid width or height value, or NULL if none was set.
     * Valid values are positive integers or empty values.
     *
     * @param int $value
     * @return int If valid width / height was set, or NULL if value was empty
     */
    public static function getValidWithHeightValue($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = intval($value);
        if ($value < 1) {
            throw new \InvalidArgumentException(
                'Invalid width or height value. Only positive integers are allowed.',
                1303846809
            );
        }

        return $value;
    }
}
