<?php
/*******************************************************************************
 * Copyright notice
 * (c) 2013 Jost Baron <j.baron@netzkoenig.de>
 * All rights reserved
 * 
 * This file is part of the TYPO3 extension "nkhyphenation".
 *
 * The TYPO3 extension "nkhyphenation" is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * The TYPO3 extension "nkhyphenation" is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the TYPO3 extension "nkhyphenation".  If not, see
 * <http://www.gnu.org/licenses/>.
 ******************************************************************************/

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    'tx_nkhyphenation_domain_model_hyphenationpatterns'
);
$GLOBALS['TCA']['tx_nkhyphenation_domain_model_hyphenationpatterns'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:nkhyphenation/Resources/Private/Language/locallang_db.xml:hyphenationpatterns_recordlabel',
        'label' => 'title',
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => 1,
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' =>
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/Tca/hyphenationpatterns.php',
        'searchFields' => 'uid,title',
    ),
);

// Help texts for the backend forms.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_nkhyphenation_domain_model_hyphenationpatterns',
    'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_hyphenationpatterns_csh.xml'
);

// Register static templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript',
    'Hyphenation - basic settings'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/csc-6.0',
    'Hyphenation for CSS Styled Content 6.0'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/csc-6.1',
    'Hyphenation for CSS Styled Content 6.1'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/csc-6.2',
    'Hyphenation for CSS Styled Content 6.2'
);
