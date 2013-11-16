<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::allowTableOnStandardPages('tx_nkhyphenation_domain_model_hyphenationpatterns');
$TCA['tx_nkhyphenation_domain_model_hyphenationpatterns'] = array(
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
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/Tca/hyphenationpatterns.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/hyphenationpatterns.png',
        'searchFields' => 'uid,title',
    ),
);

// Help texts for the backend forms.
t3lib_extMgm::addLLrefForTCAdescr(
        'tx_nkhyphenation_domain_model_hyphenationpatterns', 'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_hyphenationpatterns_csh.xml');

// Register backend module.
if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        $_EXTKEY,
        'tools',        // Main area
        'hyphenation',  // Name of the module
        '',             // Position of the module
        array(          // Allowed controller action combinations
            'HyphenationPatterns' => 'list,show,edit,update,new,create'
        ),
        array(          // Additional configuration
            'access'    => 'user,group',
            'icon'      => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
            'labels'    => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_backend.xml',
        )
    );
}
