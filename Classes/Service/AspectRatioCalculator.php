<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use TYPO3\CMS\Core\Utility\MathUtility;

final class AspectRatioCalculator implements AspectRatioCalculatorInterface
{
    public function calculateAspectRatio(string $aspectRatio): float
    {
        if (!$this->isValidAspectRatio($aspectRatio)) {
            return 0.0;
        }
        list($width, $height) = $this->getAspectRatioParts($aspectRatio);
        return $width / $height;
    }

    public function isValidAspectRatio(string $aspectRatio): bool
    {
        if (!preg_match('/^\\d+:\\d+$/', $aspectRatio)) {
            return false;
        }

        list($width, $height) = $this->getAspectRatioParts($aspectRatio);
        return $width > 0 && $height > 0
            && MathUtility::canBeInterpretedAsInteger($width) && MathUtility::canBeInterpretedAsInteger($height);
    }

    private function getAspectRatioParts(string $aspectRatio): array
    {
        return explode(':', $aspectRatio);
    }
}
