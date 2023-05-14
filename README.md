# mediaoembed TYPO3 Extension

[![.github/workflows/test.yml](https://github.com/astehlik/typo3-extension-mediaoembed/actions/workflows/test.yml/badge.svg)](https://github.com/astehlik/typo3-extension-mediaoembed/actions/workflows/test.yml)
[![Maintainability](https://api.codeclimate.com/v1/badges/e709f7de64e6002f1ee8/maintainability)](https://codeclimate.com/github/astehlik/typo3-extension-mediaoembed/maintainability)

This is a TYPO3 Extension for embedding third party content via oEmbed.

## Development

### Regenerate default provider config

1. Copy configuration from `WP_oEmbed` Wordpress class to constructor of `ProviderUrls` class.
2. Make sure all endpoint names are configured in the `ProviderEndpoints` class.
3. Regenerate the DefaultProviders Typoscript.

```bash
composer require typo3/minimal
php Resources/Private/Scripts/GenerateProviderData.php > Configuration/TypoScript/DefaultProviders/setup.txt
```
