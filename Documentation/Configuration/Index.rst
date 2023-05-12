..  include:: /Includes.rst.txt

.. _configuration:

=============
Configuration
=============

.. _configuration-manage-providers:

Manage providers
================

Providers are managed via TypoScript in :typoscript:`plugin.tx_mediaoembed.settings.providers`.

There are two ways of configuring matching URLs of an provider: regular expressions or simple wildcard based schmemes.

Regular Expressions
-------------------

To make the handling of URLs simpler we use # as the regex delimer.

This is an example configuration using Regular Expressions:

.. code-block:: typoscript

    plugin.tx_mediaoembed.settings.providers {
        some_provider {
            endpoint = https://some-provider.tld/oembed/endpoint
            urlRegexes {
                10 = #https?://([a-z0-9-]+\.)?myprovider\.(org|de|dk)/embedvideo/.*#i
                20 = #https?://([a-z0-9-]+\.)?myprovider\.(org|de|dk)/embedimage/.*#i
            }
        }
    }

Wildcards
---------

A simpler approach is the usage of wildcards. In that case the config option is called urlSchemes
and not urlRegexes:

.. code-block:: typoscript

    plugin.tx_mediaoembed.settings.providers {
        some_provider {
            endpoint = https://some-provider.tld/oembed/endpoint
            urlSchemes {
                10 = https://*.myprovider.org/embedvideo/*
                20 = https://*.myprovider.org/embedimage/*
            }
        }
    }

It provides less flexibility than regex but it is easier to handle.

.. _configuration-rendering:

Rendering
=========

The Extension uses the default Extbase template mechanisms for rendering.

This means you can add your own template and partials paths to the configuration to overwrite
the templates that come with this Extension:

.. code-block:: typoscript

    plugin.tx_mediaoembed {
        view {
            templateRootPaths {
                0 = EXT:mediaoembed/Resources/Private/Templates/
                1 = EXT:mysitepackage/Resources/Private/Templates/
            }
            partialRootPaths {
                0 = EXT:mediaoembed/Resources/Private/Partials/
                1 = EXT:mysitepackage/Resources/Private/Partials/
            }
        }
    }

See also: :ref:`t3extbasebook:view`
