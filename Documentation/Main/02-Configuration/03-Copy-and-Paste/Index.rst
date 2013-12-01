.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../includes.txt

Copy & Paste
^^^^^^^^^^^^

If soft hyphens (&shy;, unicode 0x00AD) are used as hyphens, browsers tend to
copy them as well when doing a copy-operation on the website. In order to cope
with this behavior, this extension provides a JavaScript file that removes all
soft hyphens from the copied text.

This script needs jQuery to be loaded.

If you don't want to load it, add the following line to your TypoScript
::

    plugin.tx_nkhyphenation.settings.includeHyphenRemovalJS = 0

or don't include the basic static TypoScript template.
