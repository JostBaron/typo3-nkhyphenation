<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tx_Nkhyphenation_Controller_HyphenationPatternsController
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class Tx_Nkhyphenation_Controller_HyphenationPatternsController
    extends Tx_Extbase_MVC_Controller_ActionController {

    /**
     * @var Tx_Nkhyphenation_Domain_Repository_HyphenationPatternsRepository
     */
    protected $hyphenationPatternsRepository;

    /**
     * @param Tx_Nkhyphenation_Domain_Repository_HyphenationPatternsRepository $repository
     */
    public function injectHyphenationPatternsRepository(
            Tx_Nkhyphenation_Domain_Repository_HyphenationPatternsRepository $repository) {

        $this->hyphenationPatternsRepository = $repository;
    }

    /**
     * Common initialization for all actions.
     */
    public function initializeAction() {

    }

    /**
     * Shows form for creating a new pattern set.
     */
    public function newAction() {
        // nothing to do ?!?
    }

    /**
     * Creates a new pattern set.
     * @param Tx_Nkhyphenation_Domain_Model_HyphenationPatterns $patterns
     */
    public function createAction(
            Tx_Nkhyphenation_Domain_Model_HyphenationPatterns $patterns) {

        $this->view->assign('patterns', $patterns);
    }

    /**
     * Shows form to edit the given pattern set.
     * @param Tx_Nkhyphenation_Domain_Model_HyphenationPatterns $patterns
     */
    public function editAction(
            Tx_Nkhyphenation_Domain_Model_HyphenationPatterns $patterns) {

        $this->view->assign('patterns', $patterns);
    }

    /**
     * Update given pattern set.
     * @param Tx_Nkhyphenation_Domain_Model_HyphenationPatterns $patterns
     */
    public function updateAction(
            Tx_Nkhyphenation_Domain_Model_HyphenationPatterns $patterns) {

        $this->view->assign('patterns', $patterns);
    }

    /**
     * Delete the given pattern set from the database.
     * @param Tx_Nkhyphenation_Domain_Model_HyphenationPatterns $patterns
     */
    public function deleteAction(
            Tx_Nkhyphenation_Domain_Model_HyphenationPatterns $patterns) {

        $this->view->assign('patterns', $patterns);
    }

    /**
     * Displays a single pattern set.
     * @param Tx_Nkhyphenation_Domain_Model_HyphenationPatterns $patterns
     */
    public function showAction(
            Tx_Nkhyphenation_Domain_Model_HyphenationPatterns $patterns) {

        $this->view->assign('patterns', $patterns);
    }

    /**
     * Lists all patterns sets.
     */
    public function listAction() {

        $allPatterns = $this->hyphenationPatternsRepository->findAll();

        $this->view->assign('allPatterns', $allPatterns);
    }
}

?>