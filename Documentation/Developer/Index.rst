..  include:: /Includes.rst.txt

.. _developer:

=========
Developer
=========

This chapter describes the extension points the `mediaoembed` extension offers for
developers.

.. _developer-service-visibility:

Service Visibility
====================

Response processors, HTML response processors, request handlers and the HTTP client are
all looked up at runtime by their fully qualified class name, taken straight from
TypoScript, via `$container->get($fqcn)`. This only works if that class is registered as
a **public** service in your extension's dependency injection configuration — TYPO3's and
Symfony's default is **private**, which makes `container->get()` throw a
`ServiceNotFoundException`.

..  code-block:: yaml

    services:
      MySitePackage\ResponseProcessor\MyCustomProcessor:
        public: true

.. _developer-response-processors:

Response Processors
====================

`Sto\\Mediaoembed\\Response\\Processor\\ResponseProcessorInterface`

..  code-block:: php

    public function processResponse(GenericResponse $response);

Response processors are called once a provider has answered a request, right after the
raw oEmbed response has been turned into a `GenericResponse` object and before it is
handed to the view. They can inspect and modify the response, for example to rewrite
the embed HTML or to change response properties.

They are configured per provider, as a numerically sorted list, via
:typoscript:`plugin.tx_mediaoembed.settings.providers.<name>.processors`:

..  code-block:: typoscript

    plugin.tx_mediaoembed.settings.providers.youtube.processors {
        10 = Sto\Mediaoembed\Response\Processor\YouTube\NocookieProcessor
        20 = Sto\Mediaoembed\Response\Processor\YouTube\PlayRelatedProcessor
        30 = MySitePackage\ResponseProcessor\MyCustomProcessor
    }

See :ref:`configuration-manage-providers` for how providers are configured. Built-in
examples: `Sto\\Mediaoembed\\Response\\Processor\\YouTube\\NocookieProcessor` and
`Sto\\Mediaoembed\\Response\\Processor\\YouTube\\PlayRelatedProcessor`.

.. _developer-html-response-processors:

HTML Response Processors
==========================

`Sto\\Mediaoembed\\Response\\Processor\\HtmlResponseProcessorInterface`

..  code-block:: php

    public function processHtmlResponse(HtmlAwareResponseInterface $response);

Similar to response processors, but they run for every provider (instead of being tied
to one) and only if the response implements `HtmlAwareResponseInterface`, i.e. it
actually carries embed HTML. Use this when a processor should apply regardless of which
provider answered the request.

They are configured globally, as a numerically sorted list, via
:typoscript:`plugin.tx_mediaoembed.settings.responseProcessors.html`:

..  code-block:: typoscript

    plugin.tx_mediaoembed.settings.responseProcessors.html {
        10 = MySitePackage\ResponseProcessor\MyGlobalHtmlProcessor
    }

.. _developer-request-handlers:

Request Handlers
==================

`Sto\\Mediaoembed\\Request\\RequestHandler\\RequestHandlerInterface`

..  code-block:: php

    public function handle(Provider $provider, Configuration $configuration): array;

A request handler is responsible for actually talking to a provider and returning the
oEmbed-style response data as an array (`type`, `html`, `provider_name`, ...). If a
provider does not configure a request handler, the built-in
`Sto\\Mediaoembed\\Request\\RequestHandler\\HttpRequestHandler` is used, which performs a
regular oEmbed HTTP request against the provider's `endpoint`.

Implement this interface when a provider needs custom logic instead of a plain oEmbed
HTTP call, for example when the provider does not implement the oEmbed standard itself.
Configure it per provider via
:typoscript:`plugin.tx_mediaoembed.settings.providers.<name>.requestHandlerClass`, with
optional free-form settings passed through as
:typoscript:`plugin.tx_mediaoembed.settings.providers.<name>.requestHandlerSettings` and
available in the handler via `$provider->getRequestHandlerSettings()`:

..  code-block:: typoscript

    plugin.tx_mediaoembed.settings.providers.my_provider {
        endpoint = https://my-provider.tld
        requestHandlerClass = MySitePackage\RequestHandler\MyRequestHandler
        requestHandlerSettings {
            someOption = 42
        }
    }

Built-in example: `Sto\\Mediaoembed\\Request\\RequestHandler\\Panopto\\PanoptoRequestHandler`,
used by the bundled `panopto` provider.

.. _developer-http-client:

HTTP Client
============

`Sto\\Mediaoembed\\Request\\HttpClient\\HttpClientInterface`

..  code-block:: php

    /**
     * @throws HttpClientRequestException
     */
    public function executeGetRequest(string $requestUrl): string;

Used by the default `HttpRequestHandler` to perform the actual GET request to a
provider's oEmbed endpoint. Unlike processors and request handlers this is configured
globally, not per provider, via :typoscript:`plugin.tx_mediaoembed.settings.httpClient`:

..  code-block:: typoscript

    plugin.tx_mediaoembed.settings.httpClient = MySitePackage\HttpClient\MyHttpClient

If nothing is configured, the built-in
`Sto\\Mediaoembed\\Request\\HttpClient\\GetUrlHttpClient` is used, which performs the
request via TYPO3's `RequestFactory`. Implementations should throw
`Sto\\Mediaoembed\\Exception\\HttpClientRequestException` on failure, with the HTTP status
code passed as the exception's code. `HttpRequest` (used by `HttpRequestHandler`) only
translates status codes `401`, `404` and `501` into typed exceptions that the surrounding
provider loop catches to try the next matching provider; any other code results in a
plain `RuntimeException` that is not caught anywhere and propagates as an unhandled
error.

.. _developer-viewhelpers:

Fluid ViewHelpers
===================

Both ViewHelpers below live in the `Sto\\Mediaoembed\\ViewHelpers` namespace, used as
`xmlns:mo="http://typo3.org/ns/Sto/Mediaoembed/ViewHelpers"` in the shipped templates.

`Sto\\Mediaoembed\\ViewHelpers\\EmbedViewHelper` (`<mo:embed>` in the shipped templates)
renders the actual embed container, including the responsive padding/aspect-ratio wrapper
and, if enabled, the consent placeholder (see :ref:`configuration-rendering`). It requires
the `configuration` and `response` variables that the extension's controller already
assigns to the view, so it is only usable from templates/partials that override the
shipped ones under `EXT:mediaoembed/Resources/Private/`, not from arbitrary custom
templates.

`Sto\\Mediaoembed\\ViewHelpers\\EmbedResponsivePaddingViewHelper`
(`<mo:embedResponsivePadding>`) takes the same `configuration` and `response` arguments
and renders the same responsive wrapper, but without consent support. It predates
`EmbedViewHelper` and is no longer used by the shipped templates; it is kept for templates
that still reference it directly.

.. _developer-events:

PSR-14 Events
=============

`mediaoembed` dispatches PSR-14 events at certain points during its request processing,
so listeners can hook into the extension's behavior without having to swap out a whole
class. See the `TYPO3 Explained documentation on PSR-14 events
<https://docs.typo3.org/permalink/typo3/cms-core:events>`__ for general information about
listening to and registering for PSR-14 events.

.. _developer-events-before-media-url-resolved:

BeforeMediaUrlResolvedEvent
---------------------------

`Sto\\Mediaoembed\\Event\\BeforeMediaUrlResolvedEvent`

Dispatched with the raw media URL taken from the content element, before it is used to
resolve a matching provider (see :ref:`configuration-manage-providers`).

The event carries two independent URLs, both initialized to the raw media URL from the
content element:

*   `url`, exposed via `getUrl()`/`setUrl(string $url)`, ends up in `Configuration::getMediaUrl()`
    and is what templates show to visitors (the direct link, the consent placeholder text).
*   `requestUrl`, exposed via `getRequestUrl()`/`setRequestUrl(string $requestUrl)`, ends up
    in `Configuration::getRequestMediaUrl()` and is what is used for provider resolving and
    for the request to the provider's endpoint.

Listeners can rewrite either or both independently. Typically only `requestUrl` is rewritten,
for example to translate an alternative or shortened URL format into one that matches a
provider's :typoscript:`urlRegexes` or :typoscript:`urlSchemes`, while `url` is left as entered
so visitors keep seeing the URL they recognize.

The extension itself uses this event to translate YouTube Shorts URLs
(`https://www.youtube.com/shorts/{id}`) into regular watch URLs
(`https://www.youtube.com/watch?v={id}`) so that they are still handled by the bundled
`youtube` provider. It only rewrites `requestUrl`, so the direct link and consent text still
show the original Shorts URL.

Example listener that rewrites a fictional shortened URL format:

..  code-block:: php

    <?php

    declare(strict_types=1);

    namespace MySitePackage\EventListener;

    use Sto\Mediaoembed\Event\BeforeMediaUrlResolvedEvent;
    use TYPO3\CMS\Core\Attribute\AsEventListener;

    #[AsEventListener(identifier: 'my-site-package/rewrite-short-url')]
    final class RewriteShortUrlListener
    {
        public function __invoke(BeforeMediaUrlResolvedEvent $event): void
        {
            if (!str_contains($event->getRequestUrl(), 'short.example.com/')) {
                return;
            }

            $event->setRequestUrl(
                str_replace('short.example.com/', 'example.com/videos/', $event->getRequestUrl()),
            );
        }
    }
