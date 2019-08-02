<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Exception;

use Exception;

class PhotoDownloadException extends OEmbedException
{
    public function __construct(string $url, Exception $previous = null)
    {
        parent::__construct('Error downloading photo from ' . $url, 1564777848, $previous);
    }
}
