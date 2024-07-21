<?php

declare(strict_types=1);

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

namespace Netzkoenig\Nkhyphenation\Hooks;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Includes JS and CSS into the page
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class PageRendererHook
{
    public function addJavaScript($params)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);

        $frameworkConfiguration = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'nkhyphenation'
        );
        
        $settings = $frameworkConfiguration['settings'];
        
        if (('FE' === TYPO3_MODE) && ('1' === $settings['includeHyphenRemovalJS'])) {

            $extPath = PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath('nkhyphenation'));
            $extPathWithAbsRefPrefix = $GLOBALS['TSFE']->absRefPrefix . $extPath;
            
            $scriptPath = $extPathWithAbsRefPrefix . 'Resources/Public/JavaScript/sanitizeCopiedText.js';
            
            $params['jsFiles'][$scriptPath] = [
                'type'       => 'text/javascript',
                'section'    => PageRenderer::PART_HEADER,
                'compress'   => true,
                'forceOnTop' => false,
                'allWrap'    => '',
            ];
        }
    }
}
