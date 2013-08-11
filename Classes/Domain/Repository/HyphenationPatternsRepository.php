<?php

/**
 * Repository for hyphenation patterns.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class Tx_Nkhyphenation_Domain_Repository_HyphenationPatternsRepository
        extends Tx_Extbase_Persistence_Repository {

    public function initializeObject() {
        $querySettings = $this->objectManager->create('Tx_Extbase_Persistence_Typo3QuerySettings');
        $querySettings->setRespectStoragePage(FALSE);
        $querySettings->setRespectSysLanguage(FALSE);
        $this->setDefaultQuerySettings($querySettings);
    }

}

?>