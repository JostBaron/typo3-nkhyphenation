<?php

namespace Netzkoenig\Nkhyphenation\Utility;

/**
 * Description of \Netzkoenig\Nkhyphenation\Utility\BuildTrieUtility
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class BuildTrieUtility {

    /**
     * Builds a trie from a Hyphenator.js file.
     * @param string $filecontent The content of the file.
     * @return array The trie.
     */
    public static function buildTrieFromHyphenatorJsFile($filecontent) {

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
