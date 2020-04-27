<?php

namespace Sto\Mediaoembed\Response\Processor;

use Sto\Mediaoembed\Response\GenericResponse;

interface ResponseProcessorInterface
{
    public function processResponse(GenericResponse $response): void;
}
