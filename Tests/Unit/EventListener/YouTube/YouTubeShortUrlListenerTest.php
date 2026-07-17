<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\EventListener\YouTube;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Sto\Mediaoembed\Event\BeforeMediaUrlResolvedEvent;
use Sto\Mediaoembed\EventListener\YouTube\YouTubeShortUrlListener;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

#[CoversClass(YouTubeShortUrlListener::class)]
final class YouTubeShortUrlListenerTest extends AbstractUnitTestCase
{
    private YouTubeShortUrlListener $listener;

    protected function setUp(): void
    {
        $this->listener = new YouTubeShortUrlListener();
    }

    public function testInvokeDoesNotChangeDisplayedUrl(): void
    {
        $shortUrl = 'https://www.youtube.com/shorts/abc123';
        $event = new BeforeMediaUrlResolvedEvent($shortUrl);
        ($this->listener)($event);
        $this->assertSame($shortUrl, $event->getUrl());
    }

    public function testInvokeKeepsNonShortUrlUnchanged(): void
    {
        $event = new BeforeMediaUrlResolvedEvent('https://www.youtube.com/watch?v=abc123');
        ($this->listener)($event);
        $this->assertSame('https://www.youtube.com/watch?v=abc123', $event->getRequestUrl());
    }

    public function testInvokeKeepsUnrelatedUrlUnchanged(): void
    {
        $event = new BeforeMediaUrlResolvedEvent('https://example.com/shorts/abc123');
        ($this->listener)($event);
        $this->assertSame('https://example.com/shorts/abc123', $event->getRequestUrl());
    }

    #[DataProvider('provideInvokeRewritesShortUrlToWatchUrlCases')]
    public function testInvokeRewritesShortUrlToWatchUrl(string $url, string $expectedUrl): void
    {
        $event = new BeforeMediaUrlResolvedEvent($url);
        ($this->listener)($event);
        $this->assertSame($expectedUrl, $event->getRequestUrl());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideInvokeRewritesShortUrlToWatchUrlCases(): iterable
    {
        yield 'plain domain' => [
            'https://youtube.com/shorts/abc123XYZ_-',
            'https://www.youtube.com/watch?v=abc123XYZ_-',
        ];
        yield 'www domain' => [
            'https://www.youtube.com/shorts/abc123',
            'https://www.youtube.com/watch?v=abc123',
        ];
        yield 'mobile domain' => [
            'https://m.youtube.com/shorts/abc123',
            'https://www.youtube.com/watch?v=abc123',
        ];
        yield 'http scheme' => [
            'http://www.youtube.com/shorts/abc123',
            'https://www.youtube.com/watch?v=abc123',
        ];
        yield 'trailing query string is dropped' => [
            'https://www.youtube.com/shorts/abc123?feature=share',
            'https://www.youtube.com/watch?v=abc123',
        ];
    }
}
