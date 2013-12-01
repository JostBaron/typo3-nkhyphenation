.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../includes.txt

Configuration
^^^^^^^^^^^^^

.. contents::

Overview
""""""""

To use the extension, the following steps are necessary:

- Include the basic static template.
- Create a pattern set in TYPO3, and fill it with patterns.
- Use either the provided view helper or the stdWrap property to trigger
  hyphenation.
- To make hyphenation work with CSS Styled Content, include the static template
  for your version of CSC.

Creating a pattern set
""""""""""""""""""""""

To create a pattern set, you first need a collection of hyphenation patterns.
Currently only the pattern files from Hyphenator.js are supported. They may be
downloaded `here <http://code.google.com/p/hyphenator/source/browse/tags>`_.
Download the appropriate pattern set and save it to the fileadmin folder or some
other storage.

The go into the list module and create a new record of type
"Hyphenation Patterns". It does not matter which page you use to store the
record, as the storage PID is ignored for hyphenation patterns. The following
options are available:

.. t3-field-list-table::
 :header-rows: 1

 - :Field:
         Field:

   :Description:
         Description:

 - :Field:
         Hide

   :Description:
         Hide this pattern set.

 - :Field:
         Name of pattern set

   :Description:
         The name of this pattern set - only used to make it easier for humans
         to identify.

 - :Field:
         Language

   :Description:
         The language this pattern set is for.

 - :Field:
         Additional word characters

   :Description:
         Additional characters that may make up a word. These characters are
         used to split up the texts to be hyphenated into words. The characters
         given here are merged with the characters given in the pattern file, if
         any (not all formats provide word characters). The characters "soft
         hyphen" (unicode 0xAD) and "zero width spacer" (unicode 0x200C) are
         always considered to be word characters.

 - :Field:
         Hyphen string

   :Description:
         The string to insert as a hyphen. You may use HTML entities here, they
         are replaced by their corresponding UTF8-characters in the output.

 - :Field:
         Minimal number of characters before first hyphen (lmin)

   :Description:
         Minimal number of characters that must occur before the first hyphen.
         This value may also be given in the pattern file. If it is given both
         in the record and the pattern file, the value from the record takes
         precedence.

 - :Field:
         Minimal number of characters after lase hyphen (rmin)

   :Description:
         Same as lmin.

 - :Field:
         Hyphenation pattern file

   :Description:
         The file to read the hyphenation patterns from. Only files from
         Hyphenator.js (version >= 2.0.0) are supported at the moment.

 - :Field:
         Hyphenation pattern file format

   :Description:
         Format of the given pattern file. Determines which parse engine is
         used.

Applying hyphenation
""""""""""""""""""""

There are two ways to apply hyphenation:

- Use the stdWrap properties provided by this extension.

- Use the view helper provided by this extension.

ViewHelper
~~~~~~~~~~~

The view helper is in the namespace Netzkoenig\Nkhyphenation\ViewHelpers, the
namespace directive is this:
::

    {namespace nk=Netzkoenig\Nkhyphenation\ViewHelpers}

After that, you may use the view helper like  this:
::

    <nk:hyphenate language="1">text to hyphenate</nk:hyphenate>

This view helper takes the following arguments:

.. t3-field-list-table::
 :header-rows: 1

 - :Argument:
        Argument:

   :Type:
        Type:

   :Description:
        Description:

   :Default:
        Default:

 - :Argument:
        language

   :Type:
        Integer

   :Description:
        The language of the text to be hyphenated. Is used to determine the
        pattern set that is used.

   :Default:
        -

 - :Argument:
        preserveHtmlTags

   :Type:
        Boolean

   :Description:
        Whether HTML tags should be preserved or not.

   :Default:
        true


stdWrap-Property
~~~~~~~~~~~~~~~~

Generate the text you want to hyphenate, then stdWrap it with the
hyphenation property defined. The following properties are available:

.. t3-field-list-table::
 :header-rows: 1

 - :Property:
         Property:

   :Type:
         Type:

   :Description:
         Description:

   :Default:
         Default:

 - :Property:
         hyphenate.language

   :Type:
         int/stdWrap

   :Description:
         Id of the language the input is in - used to determine the pattern set
         to use.

 - :Property:
         hyphenate.preserveHtmlTags

   :Type:
         boolean/stdWrap

   :Description:
         Defines whether HTML tags should be respected or not.

   :Default:
         true (preserve tags)

 - :Property:
         hyphenateBefore

   :Description:
         Same as `hyphenate`, but applied as very first stdWrap processing step.

 - :Property:
         hyphenateAfter

   :Description:
         Same as `hyphenate`, but applied as very last stdWrap processing step
         (except debugging functions).

Hyphenation for CSS Styled Content
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

There are static templates that enable hyphenation for CSS Styled Content. The
used language is the language of the content element. Only the body texts of
content elements are processed, the headlines are generally left alone.
