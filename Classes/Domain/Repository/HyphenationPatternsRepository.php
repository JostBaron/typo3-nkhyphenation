<?php

namespace Netzkoenig\Nkhyphenation\Domain\Repository;

/**
 * Repository for hyphenation patterns.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class HyphenationPatternsRepository
        extends \TYPO3\CMS\Extbase\Persistence\Repository {

    public function initializeObject() {        
        $querySettings = $this->objectManager->create('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $querySettings->setRespectStoragePage(FALSE);
        $querySettings->setRespectSysLanguage(FALSE);
        $this->setDefaultQuerySettings($querySettings);
    }
}
