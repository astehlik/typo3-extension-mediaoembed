<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Provider;

class Endpoint
{
    /**
     * @var bool
     */
    private $isRegex;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $urlSchemes;

    public function __construct(string $name, string $url, bool $isRegex)
    {
        $this->name = $name;
        $this->url = $url;
        $this->isRegex = $isRegex;
        $this->urlSchemes = [];
    }

    public function addUrlScheme(string $urlScheme)
    {
        $this->urlSchemes[] = $urlScheme;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUrlConfigKey(): string
    {
        return $this->isRegex() ? 'urlRegexes' : 'urlSchemes';
    }

    public function getUrlSchemes(): array
    {
        return $this->urlSchemes;
    }

    public function isRegex(): bool
    {
        return $this->isRegex;
    }
}
