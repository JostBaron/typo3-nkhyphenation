<?php

namespace Netzkoenig\Nkhyphenation\Domain\Model;

/**
 * Contains a set of hyphenation patterns. The patterns are stored in a file,
 * but are also stored as serialized trie in the database, in order to make
 * their retrieval fast.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class HyphenationPatterns
    extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

    /**
     * Title of this pattern set.
     * @var string
     */
    protected $title;

    /**
     * Characters that may make up a word in this language.
     * @var array
     */
    protected $wordCharacters;

    /**
     * The string to insert as hyphen.
     * @var string
     */
    protected $hyphen = '&shy;';

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
     * Reference to the system language that this patternset is for.
     * @var int
     */
    protected $systemLanguage;

    /**
     * Trie of the hyphenation patterns.
     * @var array
     */
    protected $trie = array();

    /**
     * The trie in serialized form for database storage.
     * @var string
     */
    protected $serializedTrie;

    /**
     * Returns the title of the record.
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the title of the record.
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Returns the word characters (the ones that define what a word is).
     * @return array
     */
    public function getWordCharacters() {
        return $this->wordCharacters;
    }

    /**
     * Sets the word characters to use.
     * @param mixed $wordCharacters
     */
    public function setWordCharacters($wordCharacters) {
        
        if (is_array($wordCharacters)) {
            $this->wordCharacters = $wordCharacters;
        }
        else if (is_string($wordCharacters)) {
            $this->wordCharacters = preg_split('//u', $wordCharacters, -1, PREG_SPLIT_NO_EMPTY);
        }
        else {
            throw new \Netzkoenig\Nkhyphenation\Exception\HyphenationException(
                    'The list of word characters must be a string or an array,'
                    . ' but got \'' . gettype($wordCharacters) . '\' instead.',
                    1384634628
            );
        }
    }

    /**
     * Returns the hyphen to use.
     * @return string
     */
    public function getHyphen() {
        return $this->hyphen;
    }

    /**
     * Set the hyphen to use.
     * @param string $hyphen
     */
    public function setHyphen($hyphen) {
        $this->hyphen = $hyphen;
    }

    /**
     * Returns the minimal number of characters in a word that must occur before
     * a hyphen.
     * @return int
     */
    public function getLeftmin() {
        return $this->leftmin;
    }

    /**
     * Sets the minimal number of characters in a word that must occur before a
     * hyphen.
     * @param int $leftmin
     */
    public function setLeftmin($leftmin) {
        $this->leftmin = $leftmin;
    }

    /**
     * Returns the minimal number of characters in a word that must occur after
     * a hyphen.
     * @return int
     */
    public function getRightmin() {
        return $this->rightmin;
    }

    /**
     * Sets the minimal number of characters in a word that must occur after a
     * hyphen.
     * @param int $rightmin
     */
    public function setRightmin($rightmin) {
        $this->rightmin = $rightmin;
    }

    /**
     * Returns the system language this patternset is for.
     * @return int
     */
    public function getSystemLanguage() {
        return $this->systemLanguage;
    }

    /**
     * Sets the system language this patternset ist for.
     * @param int $systemLanguage
     */
    public function setSystemLanguage($systemLanguage) {
        $this->systemLanguage = $systemLanguage;
    }

    /**
     * Returns the hyphenation-TRIE.
     * @return array
     */
    public function getTrie() {        
        return $this->trie;
    }

    /**
     * Empties the trie.
     * @return void
     */
    public function resetTrie() {
        $this->trie = array();
    }

    /**
     * Returns the serialized TRIE. Should only be called by the T3 persistence
     * manager.
     * @return string
     */
    public function getSerializedTrie() {
        return serialize($this->trie);
    }

    /**
     * Sets the serialized TRIE. Should only be called by the T3 persistence
     * manager.
     * @param string $serializedTrie
     */
    public function setSerializedTrie($serializedTrie) {
        $this->trie = unserialize($serializedTrie);
    }

    /**
     * Inserts a pattern into a hyphenation trie.
     * @param string $pattern The pattern to insert.
     * @return void
     * @license The code of this method is heavily inspired (but not simply
     * ported) by a code piece from Hyphenator.js. The code there is in turn a
     * modified version of code from hypher.js by Bram Stein, 2011.
     */
    public function insertPatternIntoTrie($pattern) {
        
        $characters = str_split(preg_replace('/\d/', '', $pattern));
        $points = preg_split('/[\D]/', $pattern);

        if (!isset($this->trie)) {
            $this->trie = array();
        }
        
        $currentTrie =& $this->trie;

        foreach ($characters as $character) {
            if (!array_key_exists($character, $currentTrie)) {
                $currentTrie[$character] = array();
            }

            $currentTrie =& $currentTrie[$character];
        }

        $currentTrie['points'] = array();
        foreach ($points as $point) {
            array_push($currentTrie['points'], ($point === '') ? 0 : intval($point));
        }
    }
    
    /**
     * Fill this pattern-object from a patternProvider.
     * @param \Netzkoenig\Nkhyphenation\Utility\AbstractPatternProvider $patternProvider
     */
    public function addPatterns($patternProvider) {
        
        foreach ($patternProvider->getPatternList() as $pattern) {
            $this->insertPatternIntoTrie($pattern);
        }
        
        $this->setLeftmin($patternProvider->getMinCharactersBeforeFirstHyphen());
        $this->setRightmin($patternProvider->getMinCharactersAfterLastHyphen());
        
        $this->setWordCharacters($patternProvider->getWordCharacterList());
    }

    /**
     * Hyphenation of a single word.
     * @param string $word The word to hyphenate.
     * @return string The word with hyphens inserted.
     * @license The code of this method is heavily inspired (but not a simple
     * port) of a code piece from Hyphenator.js. The code there is in turn a
     * modified version of code from hypher.js by Bram Stein, 2011.
     */
    public function hyphenateWord($word) {

        $characters = preg_split('//u', mb_strtolower('_' . $word . '_', 'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);
        $points = array_fill(0, count($characters),  0);

        for ($i = 0; $i < count($characters); $i++) {

            // Start from the root of the TRIE
            $currentTrieNode = $this->getTrie();
            
            for ($j = $i; $j < count($characters); $j++) {

                // The character currently inspected
                $character = $characters[$j];

                // Check if we can walk down the trie further with the
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
            if (   (($points[$i] % 2) === 1)
                && ($this->getLeftmin() < $i)
                && ($i < (count($characters) - $this->getRightmin()))
               ) {

                array_push($result, $part);
                $part = $characters[$i];
            }
            else {
                $part .= $characters[$i];
            }
        }

        // Push the last part.
        array_push($result, $part);

        return implode($this->getHyphen(), $result);
    }

    /**
     * Hyphenates a text.
     * @param string $text The text to hyphenate.
     * @return string
     * @license The code of this method is heavily inspired (but not a simple
     * port) of a code piece from Hyphenator.js. The code there is in turn a
     * modified version of code from hypher.js by Bram Stein, 2011.
     */
    public function hyphenation($text) {

        // Characters that are part of a word: \u200C is a zero-width space,
        // \u00AD is the soft-hyphen &shy;
        $unicodeWordCharacters = preg_split('//u', json_decode('"\u200C\u00AD"'), -1, PREG_SPLIT_NO_EMPTY);
        
        $wordCharacters = $this->getWordCharacters();
        $wordCharacters = array_merge($wordCharacters, $unicodeWordCharacters);

        $wordSplittingRegex = '/((?:' . implode('|', $wordCharacters) . ')+)/u';

        $thisInstance = $this;

        preg_replace_callback(
                $wordSplittingRegex,
                function($matches) use ($thisInstance) {
                    return $thisInstance->hyphenateWord($matches[1]);
                },
                $text
        );
    }
}
