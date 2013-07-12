<?php

/**
 * A hyphenator. Once constructed it is able to hyphenate a given text.
 * This is basically a object oriented version of phpHyphenator 1.5, which is
 * available here: http://phphyphenator.yellowgreen.de/ .
 * As I cannot find out an author, there are no credits to the author here. I'm
 * sorry for that.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class Tx_Nkhyphenation_Utility_Hyphenator {

    /**
     * The language this hyphenator is for.
     * @var string
     */
    protected $language;

    /**
     * The patterns to use in the hyphenation.
     * @var array
     */
    protected $patterns;

    /**
     * The dictionary to use.
     * @var array
     */
    protected $dictionary;

    /**
     * The string to use as hyphen.
     * @var string
     */
    protected $hyphen;

    /**
     * Minimal number of characters in a word before a line break may be
     * inserted.
     * @var int
     */
    protected $leftmin;

    /**
     * Minimal number of characters in a word that must be left after a line
     * break is inserted.
     * @var int
     */
    protected $rightmin;

    /**
     * Array of the tags whose content must not be hyphenated.
     * @var array
     */
    protected $excludetags;

    /**
     * Creates a Hyphenator.
     * @param string $language The language this hyphenator uses. Required.
     * @param array $arguments Other arguments for the hyphenator. Possible
     *   arguments are:
     *    * 'patternfile':  Path to pattern-file. Defaults to
     *                      'EXT:nkhyphenation/Resources/Private/patterns/' . $language . '.php'
     *    * 'dictionary':   Path to the dictionary file to use. Defaults to no file.
     *    * 'hyphen':       The string to use as hyphen. Defaults to &shy;
     *    * 'leftmin':      Minimal number of characters before a hyphen may be
     *                      set. Defaults to 2.
     *    * 'rightmin':     Minimal number of characters a word must have left
     *                      after a hyphen. Defaults to 2.
     *    * 'charmin':      No idea what this does. Defaults to 2.
     *    * 'charmax':      No idea what this does. Defaults to 10.
     *    * 'excludetags':  Tags whose content may not be hyphenated. Must be an
     *                      array. Defaults to array("code", "pre", "script",
     *                      "style").
     * @validate $language Text, StringLength(minimum = 2)
     */
    public function __construct($language, $arguments = array()) {

        $oldEncoding = mb_internal_encoding();
        mb_internal_encoding('UTF-8');

        // Required parameter - check that it is given is done by validator.
        $this->language = $language;

        // Set the rest of the parameters, before processing the patterns and
        // dictionary. The processing needs this values.
        $this->hyphen = isset($arguments['hypen']) ? $arguments['hyphen'] : '&shy;';
        $this->leftmin = isset($arguments['leftmin']) ? $arguments['leftmin'] : 2;
        $this->rightmin = isset($arguments['rightmin']) ? $arguments['rightmin'] : 2;
        $this->charmin = isset($arguments['minchars']) ? $arguments['minchars'] : 2;
        $this->charmax = isset($arguments['maxchars']) ? $arguments['maxchars'] : 10;
        $this->excludetags = isset($arguments['excludetags']) ? $arguments['excludetags'] : array('code', 'pre', 'script', 'style');

        // Load patterns
        $patternfile = isset($arguments['patternfile']) ? $arguments['patternfile'] : 'EXT:nkhyphenation/Resources/Private/patterns/' . $language . '.php';
        $patternfile = t3lib_div::getFileAbsFileName($patternfile);

        if ((false !== $patternfile) && file_exists($patternfile)) {
            $patterns = file($patternfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            $this->patterns = array();
            foreach ($patterns as $pattern) {
                $this->patterns[preg_replace('/[0-9]/', '', $pattern)] = $pattern;
            }
        } else {
            throw new Exception(
            'Could not open pattern file. Given path was: \'' .
            $patternfile . '\'', 1372623873);
        }

        // Load dictionary
        if (isset($arguments['dictionary'])) {
            $dictionaryPath = t3lib_div::getFileAbsFileName($arguments['dictionary']);

            if ((false !== $dictionaryPath) && file_exists($dictionaryPath)) {
                $raw_dictionary = file($dictionaryPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            } else {
                throw new Exception(
                'Could not open dictionary file. Given path was: \'' .
                $dictionaryPath . '\'', 1372623873);
            }

            foreach($raw_dictionary as $entry) {
				$this->dictionary[str_replace('/', '', mb_strtolower($entry))] = str_replace('/', $this->hyphen, $entry);
			}
        } else {
            $this->dictionary = array();
        }

        mb_internal_encoding($oldEncoding);
    }

    /**
     * Hyphenates a text.
     * @param string $text
     * @return string
     */
    public function hyphenate($text) {
        $oldEncoding = mb_internal_encoding();
        mb_internal_encoding('UTF-8');
        return utf8_decode($this->hyphenation(utf8_encode($text)));
        mb_internal_encoding($oldEncoding);
    }

    /**
     * Splits a string into an array
     * @param string $string
     * @return array
     */
    private function mb_split_chars($string) {
        $strlen = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, 0, 1, 'utf-8');
            $string = mb_substr($string, 1, $strlen, 'utf-8');
            $strlen = mb_strlen($string);
        }
        return $array;
    }

    /**
     * Hyphenation of a single word.
     * @param string $word The word to hyphenate.
     * @param Tx_Nkhyphenation_Domain_Model_HyphenationPatterns $patterns
     *        The hyphenation patterns to use.
     * @return string The word with hyphens inserted.
     */
    protected function hyphenateWord($word, $patterns) {

        $characters = str_split(strtolower('_' . $word . '_'));
        $points = array_fill(0, count($characters),  0);

        for ($i = 0; $i < count($characters); $i++) {

            // Start from the root of the TRIE
            $currentTrieNode = $patterns->getTrie();
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
     * @param Tx_Nkhyphenation_Domain_Model_HyphenationPatterns The patterns to
     *        use.
     * @return string
     */
    protected function hyphenation($text, $patterns) {

        // Characters that are part of a word: \u200C is a zero-width space,
        // \u00AD is the soft-hyphen &shy;
        $unicodeWordCharacters = json_decode('"\u200C\u00AD"');
        $wordSplittingRegex = '/([' . 'a-zA-Z0-9@\-' . $patterns->getSpecialCharacters() . $unicodeWordCharacters . ']+)/u';

        if ('' !== $patterns->getSpecialCharacters()) {
            error_log($text);
            error_log('special characters: ' . $patterns->getSpecialCharacters());

            $matches = array();
            preg_match_all($wordSplittingRegex, $text, $matches);
            error_log(print_r($matches, true));
        }

        // For each word, call the hyphenation function.
        preg_replace_callback(
                $wordSplittingRegex,
                function($matches) use ($patterns) {
                    return $this->hyphenateWord($matches[1], $patterns);
                },
                $text
        );
    }
}

?>