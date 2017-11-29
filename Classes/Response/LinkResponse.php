<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Response;

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
 *
 * Responses of this type allow a provider to return any generic embed data
 * (such as title and author_name), without providing either the url or html
 * parameters.
 * The consumer may then link to the resource, using the URL specified in the
 * original request.
 */
class LinkResponse extends GenericResponse
{
}
