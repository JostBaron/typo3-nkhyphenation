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
     * No idea what this does.
     * @var int
     */
    protected $charmin;

    /**
     * No idea what this does.
     * @var int
     */
    protected $charmax;

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
     * Word hyphenation.
     * @param string $word
     * @return string
     */
    private function word_hyphenation($word) {
        if (mb_strlen($word) < $this->charmin)
            return $word;
        if (mb_strpos($word, $this->hyphen) !== false)
            return $word;
        if (isset($this->dictionary[mb_strtolower($word)]))
            return $this->dictionary[mb_strtolower($word)];

        $text_word = '_' . $word . '_';
        $word_length = mb_strlen($text_word);
        $single_character = $this->mb_split_chars($text_word);
        $text_word = mb_strtolower($text_word);
        $hyphenated_word = array();
        $numb3rs = array('0' => true, '1' => true, '2' => true, '3' => true, '4' => true, '5' => true, '6' => true, '7' => true, '8' => true, '9' => true);

        for ($position = 0; $position <= ($word_length - $this->charmin); $position++) {
            $maxwins = min(($word_length - $position), $this->charmax);

            for ($win = $this->charmin; $win <= $maxwins; $win++) {

                if (isset($this->patterns[mb_substr($text_word, $position, $win)])) {
                    $pattern = $this->patterns[mb_substr($text_word, $position, $win)];
                    $digits = 1;
                    $pattern_length = mb_strlen($pattern);

                    for ($i = 0; $i < $pattern_length; $i++) {
                        $char = $pattern[$i];
                        if (isset($numb3rs[$char])) {
                            $zero = ($i == 0) ? $position - 1 : $position + $i - $digits;
                            if (!isset($hyphenated_word[$zero]) || $hyphenated_word[$zero] != $char)
                                $hyphenated_word[$zero] = $char;
                            $digits++;
                        }
                    }
                }
            }
        }

        $inserted = 0;
        for ($i = $this->leftmin; $i <= (mb_strlen($word) - $this->rightmin); $i++) {
            if (isset($hyphenated_word[$i]) && $hyphenated_word[$i] % 2 != 0) {
                array_splice($single_character, $i + $inserted + 1, 0, $this->hyphen);
                $inserted++;
            }
        }

        return implode('', array_slice($single_character, 1, -1));
    }

    /**
     * Hyphenates a text.
     * @param string $text
     * @return string
     */
    private function hyphenation($text) {
        $word = "";
        $tag = "";
        $tag_jump = 0;
        $output = array();
        $word_boundaries = "<>\t\n\r\0\x0B !\"§$%&/()=?….,;:-–_„”«»‘’'/\\‹›()[]{}*+´`^|©℗®™℠¹²³";
        $text = $text . " ";

        for ($i = 0; $i < mb_strlen($text); $i++) {
            $char = mb_substr($text, $i, 1);
            if (mb_strpos($word_boundaries, $char) === false && $tag == "") {
                $word .= $char;
            } else {
                if ($word != "") {
                    $output[] = $this->word_hyphenation($word);
                    $word = "";
                }
                if ($tag != "" || $char == "<")
                    $tag .= $char;
                if ($tag != "" && $char == ">") {
                    $tag_name = (mb_strpos($tag, " ")) ? mb_substr($tag, 1, mb_strpos($tag, " ") - 1) : mb_substr($tag, 1, mb_strpos($tag, ">") - 1);
                    if ($tag_jump == 0 && in_array(mb_strtolower($tag_name), $this->excludetags)) {
                        $tag_jump = 1;
                    } else if ($tag_jump == 0 || mb_strtolower(mb_substr($tag, -mb_strlen($tag_name) - 3)) == '</' . mb_strtolower($tag_name) . '>') {
                        $output[] = $tag;
                        $tag = '';
                        $tag_jump = 0;
                    }
                }
                if ($tag == "" && $char != "<" && $char != ">")
                    $output[] = $char;
            }
        }

        $text = join($output);
        return substr($text, 0, strlen($text) - 1);
    }
}

?>