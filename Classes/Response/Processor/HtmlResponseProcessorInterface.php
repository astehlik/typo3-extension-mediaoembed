<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Response\Processor;

use Sto\Mediaoembed\Response\HtmlAwareResponseInterface;

interface HtmlResponseProcessorInterface
{
    public function processHtmlResponse(HtmlAwareResponseInterface $response);
}
