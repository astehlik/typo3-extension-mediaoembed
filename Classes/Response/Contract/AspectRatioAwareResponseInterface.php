<?php

namespace Sto\Mediaoembed\Response\Contract;

interface AspectRatioAwareResponseInterface
{
    public function getAspectRatio(): float;
}
