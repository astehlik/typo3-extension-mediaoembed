<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Domain\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use Sto\Mediaoembed\Domain\Repository\ContentRepository;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

#[CoversClass(ContentRepository::class)]
final class ContentRepositoryTest extends AbstractUnitTestCase
{
    private ContentRepository $contentRepository;

    protected function setUp(): void
    {
        $this->contentRepository = new ContentRepository();
    }

    public function testCreateFromContentDataWithCompleteData(): void
    {
        $contentData = [
            'uid' => 123,
            'tx_mediaoembed_url' => 'https://example.com/video',
            'tx_mediaoembed_maxheight' => 480,
            'tx_mediaoembed_maxwidth' => 800,
            'tx_mediaoembed_play_related' => 0,
            'tx_mediaoembed_aspect_ratio' => '16:9',
        ];

        $content = $this->contentRepository->createFromContentData($contentData);

        $this->assertSame(123, $content->getUid());
        $this->assertSame('https://example.com/video', $content->getUrl());
        $this->assertSame(480, $content->getMaxHeight());
        $this->assertSame(800, $content->getMaxWidth());
        $this->assertFalse($content->shouldPlayRelated());
        $this->assertSame('16:9', $content->getAspectRatio());
    }

    public function testCreateFromContentDataWithEmptyData(): void
    {
        $contentData = [];

        $content = $this->contentRepository->createFromContentData($contentData);

        $this->assertSame(0, $content->getUid());
        $this->assertSame('', $content->getUrl());
        $this->assertSame(0, $content->getMaxHeight());
        $this->assertSame(0, $content->getMaxWidth());
        $this->assertTrue($content->shouldPlayRelated());
        $this->assertSame('', $content->getAspectRatio());
    }
}
