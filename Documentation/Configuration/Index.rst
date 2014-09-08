.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _configuration:

Configuration
=============

.. _configuration-rendering:

Rendering
---------

The rendering mechanism of this extension is very flexible since all the rendering is done with TypoScript.
Have a look at ``Configuration/TypoScript/setup.txt`` and it will probably remind you of the setup of
``css_styled_content``.

.. _configuration-rendering-accessing-data:

Accessing data
~~~~~~~~~~~~~~

But there is something special in the TypoScript configuration because we somehow need to access the
data of the the provider, the request and the response. This is why there is a new ``getText`` type available
called ``registerobj``. With this type you are able to access register data like you can do with the register type.

The new thing about ``registerobj`` is, that you can access object getters and array data. To do that simply
use the pipe character (|) like you know it from the GP type for example.

All relevant data for the oEmbed rendering is stored in a single register called tx_mediaoembed which is an
object of the type ``Sto\Mediaoembed\Content\RegisterData`` array that contains 4 child objects:

::

    Sto\Mediaoembed\Content\Configuration
    Sto\Mediaoembed\Request\Provider
    Sto\Mediaoembed\Request\HttpRequest
    Sto\Mediaoembed\Response\GenericResponse

With the new ``getText`` type you can access all public available getter Methods of these object.

For a complete documentation of all available methods please have a look in the code or use PHPDoc
to generate API documentation. In the future, the most important methods will also be documented here.

If a method returns an array you can traverse the array by using the pipe character.

Example
```````

.. code-block:: typoscript

    video = TEXT
    video.data = registerobj : tx_mediaoembed|response|html

The above TypoScript snippet will call these PHP methods to retrieve the data:

.. code-block:: php

    Sto\Mediaoembed\Content\RegisterData->getResponse()->getHtml()


.. _configuration-function-reference:

Function reference
------------------

TODO: Document the most important getter methods.

.. _configuration-extending-it:

Extending it
------------

TODO: Document how this extension can be extended.
