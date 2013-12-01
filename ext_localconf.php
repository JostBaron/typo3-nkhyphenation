<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Register cache for the tries
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['nkhyphenation_cache'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['nkhyphenation_cache'] = array();
}

// Register hook for stdWrap
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['stdWrap'][] = 'EXT:nkhyphenation/Classes/Hooks/StdWrapHook.php:&Netzkoenig\\Nkhyphenation\\Hooks\\StdWrapHook';

// Register hook for the pagerenderer to include js file
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = 'EXT:nkhyphenation/Classes/Hooks/PageRendererHook.php:&Netzkoenig\\Nkhyphenation\\Hooks\\PageRendererHook->addJavaScript';