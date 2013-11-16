<?php

namespace Netzkoenig\Nkhyphenation\Domain\Repository;

/**
 * Repository for hyphenation patterns.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class HyphenationPatternsRepository
        extends Tx_Extbase_Persistence_Repository {

    public function initializeObject() {
        $querySettings = $this->objectManager->create('TYPO3\\CMS\\Extbase\\Persistence\\Typo3QuerySettings');
        $querySettings->setRespectStoragePage(FALSE);
        $querySettings->setRespectSysLanguage(FALSE);
        $this->setDefaultQuerySettings($querySettings);
    }

}
