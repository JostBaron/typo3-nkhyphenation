<?php

/**
 * Contains a set of hyphenation patterns. The patterns are stored in a file,
 * but are also stored as serialized trie in the database, in order to make
 * their retrieval fast.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class Tx_Nkhyphenation_Domain_Model_HyphenationPatterns
    extends Tx_Extbase_DomainObject_AbstractEntity {

    /**
     * Path to the hyphenation pattern file.
     * @var string
     */
    protected $patternfile;

    /**
     * Trie of the hyphenation patterns.
     * @var array
     */
    protected $trie = array();

    /**
     * Special characters that may be contained in a word.
     * @var string
     */
    protected $specialCharacters;

    /**
     * Builds a trie from a jsHyphenator pattern file.
     * @throws Exception
     */
    public function buildTrie() {
        if (!file_exists($this->patternfile)) {
            throw new Exception('Pattern file \'' . $this->patternfile . '\' does not exist.', 'nkhyphenation-1373491906');
        }

        $decodedPatternFile = json_decode(file_get_contents($this->patternfile), true);

        if (!is_array($decodedPatternFile['patterns'])) {
            throw new Exception('Pattern file \'' . $this->patternfile . '\' seems to be invalid.', 'nkhyphenation-1373491855');
        }

        foreach ($decodedPatternFile['patterns'] as $patternSize => $patterns) {

            // Check that lenght of $patterns is a multiple of $patternSize, so
            // there are no partial patterns here.
            if (0 !== (strlen($patterns) % intval($patternSize))) {
                throw new Exception('The patterns of length ' . $patternSize .
                                    ' don\'t have that length each in file \'' .
                                    $this->patternfile . '\'', 'nkhyphenation-1373492349');
            }

            $singlePatterns = str_split($patterns, $patternSize);

            foreach ($singlePatterns as $pattern) {
                $this->insertPatternIntoTrie($pattern);
            }
        }
    }

    /**
     * Inserts a pattern into the hyphenation trie.
     * @param string $pattern The pattern to insert.
     * @return void
     */
    protected function insertPatternIntoTrie($pattern) {
        $characters = str_split(preg_replace('/\d/', '', $pattern));
        $points = preg_split('/[\D]/', $pattern);

        $trie =& $this->trie;
        foreach ($characters as $character) {
            if (!array_key_exists($character, $trie)) {
                $trie[$character] = array();
            }

            $trie =& $trie[$character];
        }

        $trie['points'] = array();
        foreach ($points as $point) {
            array_push($trie['points'], ($point === '') ? 0 : intval($point));
        }
    }

    /**
     * Returns the hyphenation-TRIE.
     * @return array
     */
    public function getTrie() {
        return $this->trie;
    }

    /**
     * Returns the special characters (the ones that do NOT make a word
     * boundary).
     * @return string
     */
    public function getSpecialCharacters() {
        return $this->specialCharacters;
    }
}

?>