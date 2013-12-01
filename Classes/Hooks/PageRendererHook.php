<?php

namespace Netzkoenig\Nkhyphenation\Hooks;


/**
 * Includes JS and CSS into the page
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class PageRendererHook {
    
    public function addJavaScript($params) {
        
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $configurationManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');

        $frameworkConfiguration = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'nkhyphenation'
        );
        
        $settings = $frameworkConfiguration['settings'];
        
        if (('FE' === TYPO3_MODE) && ('1' === $settings['includeHyphenRemovalJS'])) {

            $extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('nkhyphenation');
            $extPathWithAbsRefPrefix = $GLOBALS['TSFE']->absRefPrefix . $extPath;
            
            $scriptPath = $extPathWithAbsRefPrefix . 'Resources/Public/JavaScript/sanitizeCopiedText.js';
            
            $params['jsFiles'][$scriptPath] = array(
                'type'       => 'text/javascript',
                'section'    => \TYPO3\CMS\Core\Page\PageRenderer::PART_HEADER,
                'compress'   => TRUE,
                'forceOnTop' => FALSE,
                'allWrap'    => ''
            );
        }
    }
}
