<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

interface AspectRatioCalculatorInterface
{
    public function calculateAspectRatio(string $aspectRatio): float;

    public function isValidAspectRatio(string $aspectRatio): bool;
}
