<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Domain\Model\Content;

class ContentTest extends TestCase
{
    /**
     * @var Content
     */
    private $content;

    protected function setUp(): void
    {
        $this->content = new Content(10, 'https://the.media.url', 12, 43, false, '12:2');
    }

    public function testGetAspectRatio(): void
    {
        self::assertSame('12:2', $this->content->getAspectRatio());
    }

    public function testGetMaxHeight(): void
    {
        self::assertSame(12, $this->content->getMaxHeight());
    }

    public function testGetMaxWidth(): void
    {
        self::assertSame(43, $this->content->getMaxWidth());
    }

    public function testGetUid(): void
    {
        self::assertSame(10, $this->content->getUid());
    }

    public function testGetUrl(): void
    {
        self::assertSame('https://the.media.url', $this->content->getUrl());
    }

    public function testShouldPlayRelated(): void
    {
        self::assertFalse($this->content->shouldPlayRelated());
    }
}
