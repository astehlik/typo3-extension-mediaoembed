<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Domain\Model\Content;

class ContentTest extends TestCase
{
    private Content $contentElement;

    protected function setUp(): void
    {
        $this->contentElement = new Content(10, 'https://the.media.url', 12, 43, false, '12:2');
    }

    public function testGetAspectRatio(): void
    {
        self::assertSame('12:2', $this->contentElement->getAspectRatio());
    }

    public function testGetMaxHeight(): void
    {
        self::assertSame(12, $this->contentElement->getMaxHeight());
    }

    public function testGetMaxWidth(): void
    {
        self::assertSame(43, $this->contentElement->getMaxWidth());
    }

    public function testGetUid(): void
    {
        self::assertSame(10, $this->contentElement->getUid());
    }

    public function testGetUrl(): void
    {
        self::assertSame('https://the.media.url', $this->contentElement->getUrl());
    }

    public function testShouldPlayRelated(): void
    {
        self::assertFalse($this->contentElement->shouldPlayRelated());
    }
}
