.. include:: ../Includes.txt


.. _installation:

============
Installation
============

Import the extension in the extension manager.

Alternatively you can install it via composer:

.. code-block:: bash

    composer require de-swebhosting-typo3-extension/mediaoembed

Now enable it and import the static template "Media oEmbed" in you template. You can also import
the "Media oEmbed default providers" to import a set of default providers.

.. _admin-update:

Update
======

The database structure and the TYPO3 integration have changed very much between version 0.0.1 and 0.1.0! In the current
version a new content type is used for embedding external media called “External Media”. The media data is stored in
some new database fields in the tt_content table and not in the media FlexForm any more.

After updating the Extension please use the Update wizard in the Install tool to upgrade you existing media elements to
the new version. There are two steps you need to execute in the following order:

#. Create missing tables and fields (default update step by the TYPO3 Core)
#. mediaoembed - Migrate content elements

.. figure:: ../Images/ScreenshotInstallToolUpdates.png
   :width: 1000px
   :alt: Screenshot of the update steps in the install tool

   The update steps in the install tool

That's it. You can now use the new version of the mediaoembed Extension and all your existing contents are migrated.
