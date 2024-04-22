<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Response;

trait AspectRatioAwareResponseTrait
{
    /**
     * The height in pixels required to display the HTML.
     * This value is required.
     */
    protected int $height = 0;

    /**
     * The width in pixels required to display the HTML.
     * This value is required.
     */
    protected int $width = 0;

    /**
     * Returns the current aspect ratio.
     */
    public function getAspectRatio(): float
    {
        if ($this->getHeight() === 0) {
            return 0;
        }

        return $this->getWidth() / $this->getHeight();
    }

    /**
     * Getter for the height in pixels required to display the HTML.
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Getter for the width in pixels required to display the HTML.
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    private function initializeAspectRatioData(array $responseData): void
    {
        $this->width = (int)($responseData['width'] ?? 0);
        $this->height = (int)($responseData['height'] ?? 0);
    }
}
