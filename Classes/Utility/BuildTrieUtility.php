<?php

/**
 * Description of Tx_Nkhyphenation_Utility_BuildTrieUtility
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class Tx_Nkhyphenation_Utility_BuildTrieUtility {

    public static function buildTrieFromHyphenatorJsFile($filename) {
        error_log('Filename: ' . $filename);

        $filecontent = file_get_contents($filename);
        $filecontent = preg_replace('/^Hyphenator\.languages[^=]+=/', '', $filecontent);

        $patterns = json_decode($filecontent);

        error_log('File content: ' . print_r($patterns, true));

        $result = array();
        if (isset($patterns['leftmin'])) {
            $result['leftmin'] = $patterns['leftmin'];
        }
        if (isset($patterns['rightmin'])) {
            $result['rightmin'] = $patterns['rightmin'];
        }
        if (isset($patterns['specialChars'])) {
            $result['specialcharacters'] = $patterns['specialChars'];
        }

        if (isset($patterns['patterns'])) {
            $trie = array();

            foreach ($patterns['patterns'] as $length => $codedPatterns) {
                foreach (str_split($codedPatterns, $length) as $pattern) {
                    self::insertPatternIntoTrie($trie, $pattern);
                }
            }

            $result['serialized_trie'] = serialize($trie);
        }

        return $result;
    }
}
