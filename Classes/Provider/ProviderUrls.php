<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Provider;

class ProviderUrls
{
    private array $urls;

    public function __construct()
    {
        $host = '{host}';
        $this->urls = [
            '#https?://((m|www)\\.)?youtube\\.com/watch.*#i' => [
                'https://www.youtube.com/oembed',
                true,
            ],
            '#https?://((m|www)\\.)?youtube\\.com/playlist.*#i' => [
                'https://www.youtube.com/oembed',
                true,
            ],
            '#https?://youtu\\.be/.*#i' => [
                'https://www.youtube.com/oembed',
                true,
            ],
            '#https?://(.+\\.)?vimeo\\.com/.*#i' => [
                'https://vimeo.com/api/oembed.{format}',
                true,
            ],
            '#https?://(www\\.)?dailymotion\\.com/.*#i' => [
                'https://www.dailymotion.com/services/oembed',
                true,
            ],
            '#https?://dai\\.ly/.*#i' => [
                'https://www.dailymotion.com/services/oembed',
                true,
            ],
            '#https?://(www\\.)?flickr\\.com/.*#i' => [
                'https://www.flickr.com/services/oembed/',
                true,
            ],
            '#https?://flic\\.kr/.*#i' => [
                'https://www.flickr.com/services/oembed/',
                true,
            ],
            '#https?://(.+\\.)?smugmug\\.com/.*#i' => [
                'https://api.smugmug.com/services/oembed/',
                true,
            ],
            '#https?://(www\\.)?hulu\\.com/watch/.*#i' => [
                'http://www.hulu.com/api/oembed.{format}',
                true,
            ],
            'http://i*.photobucket.com/albums/*' => [
                'http://api.photobucket.com/oembed',
                false,
            ],
            'http://gi*.photobucket.com/groups/*' => [
                'http://api.photobucket.com/oembed',
                false,
            ],
            '#https?://(www\\.)?scribd\\.com/doc/.*#i' => [
                'https://www.scribd.com/services/oembed',
                true,
            ],
            '#https?://wordpress\\.tv/.*#i' => [
                'https://wordpress.tv/oembed/',
                true,
            ],
            '#https?://(.+\\.)?polldaddy\\.com/.*#i' => [
                'https://polldaddy.com/oembed/',
                true,
            ],
            '#https?://poll\\.fm/.*#i' => [
                'https://polldaddy.com/oembed/',
                true,
            ],
            '#https?://(www\\.)?funnyordie\\.com/videos/.*#i' => [
                'http://www.funnyordie.com/oembed',
                true,
            ],
            '#https?://(www\\.)?twitter\\.com/\\w{1,15}/status(es)?/.*#i' => [
                'https://publish.twitter.com/oembed',
                true,
            ],
            '#https?://(www\\.)?twitter\\.com/\\w{1,15}$#i' => [
                'https://publish.twitter.com/oembed',
                true,
            ],
            '#https?://(www\\.)?twitter\\.com/\\w{1,15}/likes$#i' => [
                'https://publish.twitter.com/oembed',
                true,
            ],
            '#https?://(www\\.)?twitter\\.com/\\w{1,15}/lists/.*#i' => [
                'https://publish.twitter.com/oembed',
                true,
            ],
            '#https?://(www\\.)?twitter\\.com/\\w{1,15}/timelines/.*#i' => [
                'https://publish.twitter.com/oembed',
                true,
            ],
            '#https?://(www\\.)?twitter\\.com/i/moments/.*#i' => [
                'https://publish.twitter.com/oembed',
                true,
            ],
            '#https?://(www\\.)?soundcloud\\.com/.*#i' => [
                'https://soundcloud.com/oembed',
                true,
            ],
            '#https?://(.+?\\.)?slideshare\\.net/.*#i' => [
                'https://www.slideshare.net/api/oembed/2',
                true,
            ],
            '#https?://(www\\.)?instagr(\\.am|am\\.com)/p/.*#i' => [
                'https://api.instagram.com/oembed',
                true,
            ],
            '#https?://(open|play)\\.spotify\\.com/.*#i' => [
                'https://embed.spotify.com/oembed/',
                true,
            ],
            '#https?://(.+\\.)?imgur\\.com/.*#i' => [
                'https://api.imgur.com/oembed',
                true,
            ],
            '#https?://(www\\.)?meetu(\\.ps|p\\.com)/.*#i' => [
                'https://api.meetup.com/oembed',
                true,
            ],
            '#https?://(www\\.)?issuu\\.com/.+/docs/.+#i' => [
                'https://issuu.com/oembed_wp',
                true,
            ],
            '#https?://(www\\.)?collegehumor\\.com/video/.*#i' => [
                'https://www.collegehumor.com/oembed.{format}',
                true,
            ],
            '#https?://(www\\.)?mixcloud\\.com/.*#i' => [
                'https://www.mixcloud.com/oembed',
                true,
            ],
            '#https?://(www\\.|embed\\.)?ted\\.com/talks/.*#i' => [
                'https://www.ted.com/services/v1/oembed.{format}',
                true,
            ],
            '#https?://(www\\.)?(animoto|video214)\\.com/play/.*#i' => [
                'https://animoto.com/oembeds/create',
                true,
            ],
            '#https?://(.+)\\.tumblr\\.com/post/.*#i' => [
                'https://www.tumblr.com/oembed/1.0',
                true,
            ],
            '#https?://(www\\.)?kickstarter\\.com/projects/.*#i' => [
                'https://www.kickstarter.com/services/oembed',
                true,
            ],
            '#https?://kck\\.st/.*#i' => [
                'https://www.kickstarter.com/services/oembed',
                true,
            ],
            '#https?://cloudup\\.com/.*#i' => [
                'https://cloudup.com/oembed',
                true,
            ],
            '#https?://(www\\.)?reverbnation\\.com/.*#i' => [
                'https://www.reverbnation.com/oembed',
                true,
            ],
            '#https?://videopress\\.com/v/.*#' => [
                'https://public-api.wordpress.com/oembed/?for=' . $host,
                true,
            ],
            '#https?://(www\\.)?reddit\\.com/r/[^/]+/comments/.*#i' => [
                'https://www.reddit.com/oembed',
                true,
            ],
            '#https?://(www\\.)?speakerdeck\\.com/.*#i' => [
                'https://speakerdeck.com/oembed.{format}',
                true,
            ],
            '#https?://www\\.facebook\\.com/.*/posts/.*#i' => [
                'https://www.facebook.com/plugins/post/oembed.json/',
                true,
            ],
            '#https?://www\\.facebook\\.com/.*/activity/.*#i' => [
                'https://www.facebook.com/plugins/post/oembed.json/',
                true,
            ],
            '#https?://www\\.facebook\\.com/.*/photos/.*#i' => [
                'https://www.facebook.com/plugins/post/oembed.json/',
                true,
            ],
            '#https?://www\\.facebook\\.com/photo(s/|\\.php).*#i' => [
                'https://www.facebook.com/plugins/post/oembed.json/',
                true,
            ],
            '#https?://www\\.facebook\\.com/permalink\\.php.*#i' => [
                'https://www.facebook.com/plugins/post/oembed.json/',
                true,
            ],
            '#https?://www\\.facebook\\.com/media/.*#i' => [
                'https://www.facebook.com/plugins/post/oembed.json/',
                true,
            ],
            '#https?://www\\.facebook\\.com/questions/.*#i' => [
                'https://www.facebook.com/plugins/post/oembed.json/',
                true,
            ],
            '#https?://www\\.facebook\\.com/notes/.*#i' => [
                'https://www.facebook.com/plugins/post/oembed.json/',
                true,
            ],
            '#https?://www\\.facebook\\.com/.*/videos/.*#i' => [
                'https://www.facebook.com/plugins/video/oembed.json/',
                true,
            ],
            '#https?://www\\.facebook\\.com/video\\.php.*#i' => [
                'https://www.facebook.com/plugins/video/oembed.json/',
                true,
            ],
            '#https?://(www\\.)?screencast\\.com/.*#i' => [
                'https://api.screencast.com/external/oembed',
                true,
            ],
            '#https?://([a-z0-9-]+\\.)?amazon\\.(com|com\\.mx|com\\.br|ca)/.*#i' => [
                'https://read.amazon.com/kp/api/oembed',
                true,
            ],
            '#https?://([a-z0-9-]+\\.)?amazon\\.(co\\.uk|de|fr|it|es|in|nl|ru)/.*#i' => [
                'https://read.amazon.co.uk/kp/api/oembed',
                true,
            ],
            '#https?://([a-z0-9-]+\\.)?amazon\\.(co\\.jp|com\\.au)/.*#i' => [
                'https://read.amazon.com.au/kp/api/oembed',
                true,
            ],
            '#https?://([a-z0-9-]+\\.)?amazon\\.cn/.*#i' => [
                'https://read.amazon.cn/kp/api/oembed',
                true,
            ],
            '#https?://(www\\.)?a\\.co/.*#i' => [
                'https://read.amazon.com/kp/api/oembed',
                true,
            ],
            '#https?://(www\\.)?amzn\\.to/.*#i' => [
                'https://read.amazon.com/kp/api/oembed',
                true,
            ],
            '#https?://(www\\.)?amzn\\.eu/.*#i' => [
                'https://read.amazon.co.uk/kp/api/oembed',
                true,
            ],
            '#https?://(www\\.)?amzn\\.in/.*#i' => [
                'https://read.amazon.in/kp/api/oembed',
                true,
            ],
            '#https?://(www\\.)?amzn\\.asia/.*#i' => [
                'https://read.amazon.com.au/kp/api/oembed',
                true,
            ],
            '#https?://(www\\.)?z\\.cn/.*#i' => [
                'https://read.amazon.cn/kp/api/oembed',
                true,
            ],
            '#https?://www\\.someecards\\.com/.+-cards/.+#i' => [
                'https://www.someecards.com/v2/oembed/',
                true,
            ],
            '#https?://www\\.someecards\\.com/usercards/viewcard/.+#i' => [
                'https://www.someecards.com/v2/oembed/',
                true,
            ],
            '#https?://some\\.ly\\/.+#i' => [
                'https://www.someecards.com/v2/oembed/',
                true,
            ],
        ];
    }

    public function getUrls(): array
    {
        return $this->urls;
    }
}
