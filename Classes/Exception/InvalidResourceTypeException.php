<?php
namespace Sto\Mediaoembed\Exception;

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
 * This Excpetion will be thrown when the server returns an invalid or unknown resource type.
 */
class InvalidResourceTypeException extends OEmbedException {

	/**
	 * Initializes the Exception with a default message and a default code (1303403046).
	 *
	 * @param string $resourceType
	 */
	public function __construct($resourceType) {
		$message = 'The server returned an invalid resource type: %s';
		$message = sprintf($message, htmlspecialchars($resourceType));
		parent::__construct($message, 1303403046);
	}
}