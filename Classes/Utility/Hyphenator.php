<?php

/**
 * A hyphenator for a patternset. Once constructed, it allows to hyphenate
 * words and texts as defined by the given pattern set.
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class Tx_Nkhyphenation_Utility_Hyphenator {

    /**
     * The patterns to use.
     * @var Tx_Nkhyphenation_Domain_Model_HyphenationPatterns
     */
    protected $patterns;

    /**
     * Builds a new hyphenator with the given patterns.
     * @param Tx_Nkhyphenation_Domain_Model_HyphenationPatterns $patterns
     * The patterns to use.
     */
    public function __construct($patterns) {
        $this->patterns = $patterns;
    }

    /**
     * Hyphenation of a single word.
     * @param string $word The word to hyphenate.
     * @return string The word with hyphens inserted.
     */
    public function hyphenateWord($word) {

        $characters = str_split(strtolower('_' . $word . '_'));
        $points = array_fill(0, count($characters),  0);

        for ($i = 0; $i < count($characters); $i++) {

            // Start from the root of the TRIE
            $currentTrieNode = $this->patterns->getTrie();
            for ($j = $i; $j < count($characters); $j++) {

                // The character currently inspected
                $character = $characters[$j];

                // Check if we can walk down the trie fourther with the
                // next letter. If not, break the loop.
                if (!array_key_exists($character, $currentTrieNode)) {
                    break;
                }

                $currentTrieNode = $currentTrieNode[$character];
                if (array_key_exists('points', $currentTrieNode)) {
                    $nodePoints = $currentTrieNode['points'];

                    for ($k = 0; $k < count($nodePoints); $k++) {
                        $points[$i + $k] = max($points[$i + $k], $nodePoints[$k]);
                    }
                }
            }
        }

        $result = array();
        $part = '';

        for ($i = 1; $i < count($characters) - 1; $i++) {
            if (($points[$i] % 2) === 1) {
                array_push($result, $part);
                $part = $characters[$i];
            }
            else {
                $part .= $characters[$i];
            }
        }

        // Push the last part.
        array_push($result, $part);

        return implode('-', $result);
    }

    /**
     * Hyphenates a text.
     * @param string $text The text to hyphenate.
     * @return string
     */
    public function hyphenation($text) {

        // Characters that are part of a word: \u200C is a zero-width space,
        // \u00AD is the soft-hyphen &shy;
        $unicodeWordCharacters = json_decode('"\u200C\u00AD"');
        $wordSplittingRegex = '/([' . 'a-zA-Z0-9@\-' . $this->patterns->getSpecialCharacters() . $unicodeWordCharacters . ']+)/u';

        preg_replace_callback(
                $wordSplittingRegex,
                function($matches) {
                    return $this->hyphenateWord($matches[1]);
                },
                $text
        );
    }
}

?>