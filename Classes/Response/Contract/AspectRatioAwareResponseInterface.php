<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Response\Contract;

interface AspectRatioAwareResponseInterface
{
    public function getAspectRatio(): float;

    public function getHeight(): int;

    public function getWidth(): int;
}
