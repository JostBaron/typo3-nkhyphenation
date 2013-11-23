<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Register cache for the tries
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['nkhyphenation_triecache'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['nkhyphenation_triecache'] = array();
}
