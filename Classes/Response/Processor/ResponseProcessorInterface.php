<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Response\Processor;

use Sto\Mediaoembed\Response\GenericResponse;

interface ResponseProcessorInterface
{
    public function processResponse(GenericResponse $response);
}
