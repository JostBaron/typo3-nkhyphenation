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

    /**
     * Inserts a pattern into a hyphenation trie.
     * @param array $trie The trie to insert the pattern into.
     * @param string $pattern The pattern to insert.
     * @return void
     * @license The code of this method is heavily inspired (but not simply
     * ported) by a code piece from Hyphenator.js. The code there is in turn a
     * modified version of code from hypher.js by Bram Stein, 2011.
     */
    protected static function insertPatternIntoTrie(&$trie, $pattern) {

        $characters = str_split(preg_replace('/\d/', '', $pattern));
        $points = preg_split('/[\D]/', $pattern);

        $currentTrie = $trie;

        foreach ($characters as $character) {
            if (!array_key_exists($character, $trie)) {
                $currentTrie[$character] = array();
            }

            $currentTrie =& $currentTrie[$character];
        }

        $currentTrie['points'] = array();
        foreach ($points as $point) {
            array_push($currentTrie['points'], ($point === '') ? 0 : intval($point));
        }
    }
}

?>