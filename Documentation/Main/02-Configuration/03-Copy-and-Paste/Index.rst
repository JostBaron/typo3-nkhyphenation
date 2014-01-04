.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../includes.txt

Copy & Paste
^^^^^^^^^^^^

If soft hyphens (&shy;, unicode 0x00AD) are used as hyphens, browsers tend to
copy them to the clipboard when doing a copy-operation on the website. When this
text is pasted again, some programs print every soft hyphen as a visible hyphen
character, which is rather ugly.

In order to mitigate this behavior, this extension provides a jQuery based
JavaScript that removes all soft hyphens from the copied text.

(If the jQuery dependency is a real problem, please let me know)

This script is loaded by default. If you don't want to load it, add the
following line to your TypoScript
::

    plugin.tx_nkhyphenation.settings.includeHyphenRemovalJS = 0

or don't include the basic static TypoScript template. The latter could have
repercussions when updating, so it's better to insert the above line.
