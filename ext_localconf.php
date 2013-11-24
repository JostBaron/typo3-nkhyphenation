<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Register cache for the tries
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['nkhyphenation_triecache'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['nkhyphenation_triecache'] = array();
}

// Register hook for stdWrap
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['stdWrap'][] = 'EXT:nkhyphenation/Classes/Hooks/StdWrapHook.php:&Netzkoenig\\Nkhyphenation\\Hooks\\StdWrapHook';
