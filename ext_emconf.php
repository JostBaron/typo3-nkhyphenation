<?php

########################################################################
# Extension Manager/Repository config file for ext "mak_stdwrapextended".
#
# Auto generated 05-10-2011 02:00
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Hyphenation',
    'description' => 'Adds soft hyphenation capabilities to TYPO3. Provides ' .
                     'a ViewHelper, a stdWrap property and some static ' .
                     'templates to enable hyphenation.',
    'category' => 'fe',
    'shy' => 0,
    'version' => '0.1.0',
    'priority' => '',
    'loadOrder' => '',
    'TYPO3_version' => '',
    'PHP_version' => '',
    'module' => '',
    'state' => 'alpha',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearcacheonload' => 1,
    'lockType' => '',
    'author' => 'Jost Baron',
    'author_email' => 'j.baron@netzkoenig.de',
    'author_company' => 'Netzkönig GbR',
    'CGLcompliance' => '',
    'CGLcompliance_note' => '',
    'constraints' => array(
        'dependencies' => array(
            'typo3' => '4.5.0-6.1.99',
            'php' => '5.2.0-0.0.0'
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
);
?>