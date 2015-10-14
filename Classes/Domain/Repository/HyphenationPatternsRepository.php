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

namespace Netzkoenig\Nkhyphenation\Domain\Repository;

/**
 * Repository for hyphenation patterns.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class HyphenationPatternsRepository
        extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
     * Configuration manager to access TypoScript.
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
	 */
	protected $configurationManager;

    /**
     * Sets the default query settings.
     */
    public function initializeObject() {        
        $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $querySettings->setRespectStoragePage(FALSE);
        $querySettings->setRespectSysLanguage(FALSE);
        $this->setDefaultQuerySettings($querySettings);
        
//        $this->configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
    }
    
    /**
     * 
     * @param type $systemLanguage
     * @return Netzkoenig\Nkhyphenation\Domain\Model\HyphenationPatterns
     * Returns the hyphenation patterns to use for the given system language,
     * or NULL if no applicable patterns can be found.
     */
    public function findPatternsForSystemLanguage($systemLanguage) {
        $patternsFromTS = $this->getPatternRecordsFromTypoScript();
        
        return $this->findOneBySystemLanguage($systemLanguage);
    }
    
    protected function getPatternRecordsFromTypoScript() {

        var_dump($this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'nkhyphenation'
        ));
    }
}
