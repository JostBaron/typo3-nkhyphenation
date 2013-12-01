.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../includes.txt

What does it do?
^^^^^^^^^^^^^^^^

This extension implements server side hyphenation for TYPO3.

**Features**

- Uses the well-known algorithm from Franklin Mark Liang, who invented it
  for use in TeX (http://www.tug.org/docs/liang/).

- Adds a stdWrap property to hyphenate text.

- Provides a view helper to hyphenate text.

- Respects HTML tags. This may be switched off for performance reasons.

- Can read Hyphenator.js files to get hyphenation patterns. More pattern file
  formats may be implemented, especially to read LaTeX pattern files.

- Provides a JavaScript file that removes hyphens when copying from the website.

- Provides a static template that enables hyphenation for CSS styled content.
