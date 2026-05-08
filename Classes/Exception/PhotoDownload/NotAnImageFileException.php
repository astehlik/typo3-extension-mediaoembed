<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Exception\PhotoDownload;

use Sto\Mediaoembed\Exception\OEmbedException;

class NotAnImageFileException extends OEmbedException
{
    public function __construct(string $url, string $mimeType)
    {
        $message = sprintf(
            'The file downloaded from %s does not seem to be an image file. Detected file type: %s',
            $url,
            $mimeType,
        );
        parent::__construct($message, 1564780686);
    }
}
