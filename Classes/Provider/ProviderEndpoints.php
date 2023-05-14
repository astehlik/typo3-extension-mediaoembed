<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Provider;

class ProviderEndpoints
{
    private array $endpoints = [
        'https://www.youtube.com/oembed' => 'youtube',
        'https://vimeo.com/api/oembed.{format}' => 'vimeo',
        'https://www.dailymotion.com/services/oembed' => 'dailymotion',
        'https://www.flickr.com/services/oembed/' => 'flick',
        'https://api.smugmug.com/services/oembed/' => 'smugmug',
        'http://www.hulu.com/api/oembed.{format}' => 'hulu',
        'http://api.photobucket.com/oembed' => 'photobucket',
        'https://www.scribd.com/services/oembed' => 'scribd',
        'https://wordpress.tv/oembed/' => 'wordpresstv',
        'https://polldaddy.com/oembed/' => 'polldaddy',
        'http://www.funnyordie.com/oembed' => 'funnyordie',
        'https://publish.twitter.com/oembed' => 'twitter',
        'https://soundcloud.com/oembed' => 'soundcloud',
        'https://www.slideshare.net/api/oembed/2' => 'slideshare',
        'https://api.instagram.com/oembed' => 'instagram',
        'https://embed.spotify.com/oembed/' => 'spotify',
        'https://api.imgur.com/oembed' => 'imgur',
        'https://api.meetup.com/oembed' => 'meetup',
        'https://issuu.com/oembed_wp' => 'issuu',
        'https://www.collegehumor.com/oembed.{format}' => 'collegehumor',
        'https://www.mixcloud.com/oembed' => 'mixcloud',
        'https://www.ted.com/services/v1/oembed.{format}' => 'ted',
        'https://animoto.com/oembeds/create' => 'animoto',
        'https://www.tumblr.com/oembed/1.0' => 'tumblr',
        'https://www.kickstarter.com/services/oembed' => 'kickstarter',
        'https://cloudup.com/oembed' => 'cloudup',
        'https://www.reverbnation.com/oembed' => 'reverbnation',
        'https://public-api.wordpress.com/oembed/?for={host}' => 'wordpress_public_api',
        'https://www.reddit.com/oembed' => 'reddit',
        'https://speakerdeck.com/oembed.{format}' => 'speakerdeck',
        'https://www.facebook.com/plugins/post/oembed.json/' => 'facebook_post',
        'https://www.facebook.com/plugins/video/oembed.json/' => 'facebook_video',
        'https://api.screencast.com/external/oembed' => 'screencast',
        'https://read.amazon.com/kp/api/oembed' => 'amazon_read_us',
        'https://read.amazon.co.uk/kp/api/oembed' => 'amazon_read_uk',
        'https://read.amazon.com.au/kp/api/oembed' => 'amazon_read_au',
        'https://read.amazon.cn/kp/api/oembed' => 'amazon_read_cn',
        'https://read.amazon.in/kp/api/oembed' => 'amazon_read_in',
        'https://www.someecards.com/v2/oembed/' => 'someecards',
    ];

    public function getEndpoints(): array
    {
        return $this->endpoints;
    }
}
