<?php

/**
 * This hook builds the TRIE for hyphenation.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class Tx_Nkhyphenation_Hooks_BuildTrie {

    function processDatamap_postProcessFieldArray ($status, $table, $id, &$fieldArray, &$object) {

        if (   ('tx_nkhyphenation_domain_model_hyphenationpatterns' === $table)
            && (($status === 'update') || ($status === 'new'))) {

            if (isset($fieldArray['patternfile'])) {
                error_log('Unmodified: ' . print_r($fieldArray, true));

                $patternfile = t3lib_div::getFileAbsFileName('uploads/tx_nkhyphenation/' . $fieldArray['patternfile']);
                $processedFields = Tx_Nkhyphenation_Utility_BuildTrieUtility::buildTrieFromHyphenatorJsFile($patternfile);
                
                error_log('Processing result: ' . print_r($processedFields, true));
                $fieldArray = array_merge($fieldArray, $processedFields);
                error_log('Merged: ' . print_r($fieldArray, true));
            }

        }
    }
}

?>