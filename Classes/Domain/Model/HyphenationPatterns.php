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
     * Additional characters that may occur in word. The characters a-z, A-Z,
     * 0-9, @, - and \u200C, \u00AD are word characters by default. The
     * characters given here are additional to them. In a german pattern set,
     * this would probably be set to 'äöüß'.
     * @var string
     */
    protected $specialCharacters;

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
     * Returns the special characters (the ones that do NOT make a word
     * boundary).
     * @return string
     */
    public function getSpecialCharacters() {
        return $this->specialCharacters;
    }

    /**
     * Sets the special characters to use.
     * @param string $specialCharacters
     */
    public function setSpecialCharacters($specialCharacters) {
        $this->specialCharacters = $specialCharacters;
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
        
        $this->setSpecialCharacters($patternProvider->getWordCharacterList());
    }
}
