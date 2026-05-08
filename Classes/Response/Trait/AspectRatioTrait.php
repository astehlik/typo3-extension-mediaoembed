<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Response\Trait;

/**
 * Trait for response types that have width and height properties and support aspect ratio handling.
 */
trait AspectRatioTrait
{
    public const ASPECT_RATIO_16TO9 = '16to9';

    public const ASPECT_RATIO_4TO3 = '4to3';

    abstract public function getHeight(): int;

    abstract public function getWidth(): int;

    /**
     * Returns the current aspect ratio (width / height).
     */
    public function getAspectRatio(): float
    {
        $height = $this->getHeight();
        if ($height === 0) {
            return 0;
        }

        return $this->getWidth() / $height;
    }

    /**
     * Returns TRUE if the current aspect ratio looks like 16 to 9.
     */
    public function getAspectRatioIs16To9(): bool
    {
        return $this->getAspectRatioType() === static::ASPECT_RATIO_16TO9;
    }

    /**
     * Returns TRUE if the current aspect ratio looks like 4 to 3.
     */
    public function getAspectRatioIs4To3(): bool
    {
        return $this->getAspectRatioType() === static::ASPECT_RATIO_4TO3;
    }

    /**
     * Returns one of the ASPECT_RATIO_* constants depending on the current aspect ratio.
     */
    public function getAspectRatioType(): string
    {
        $ratio4To3 = 4 / 3;
        $ratio16To9 = 16 / 9;
        $currentRatio = $this->getAspectRatio();

        $ratioDiff4To3 = $currentRatio - $ratio4To3;
        $ratioDiff16To9 = $currentRatio - $ratio16To9;

        if (abs($ratioDiff4To3) < abs($ratioDiff16To9)) {
            return static::ASPECT_RATIO_4TO3;
        }

        return static::ASPECT_RATIO_16TO9;
    }
}
