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
    protected $wordcharacters;

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
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference The file containing the
     * patterns.
     */
    protected $patternfile = null;
    
    /**
     * @var string The pattern file format.
     */
    protected $patternfileformat;

    /**
     * Trie of the hyphenation patterns.
     * @var array
     */
    protected $trie = array();

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
    public function getWordcharacters() {
        return $this->wordcharacters;
    }

    /**
     * Sets the word characters to use.
     * @param mixed $wordCharacters
     */
    public function setWordcharacters($wordCharacters) {
        
        if (is_array($wordCharacters)) {
            $this->wordcharacters = $wordCharacters;
        }
        else if (is_string($wordCharacters)) {
            $this->wordcharacters = preg_split('//u', $wordCharacters, -1, PREG_SPLIT_NO_EMPTY);
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
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference The pattern file.
     */
    public function getPatternfile() {
        return $this->patternfile;
    }
    
    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $patternfile The new
     * pattern file.
     */
    public function setPatternfile(\TYPO3\CMS\Extbase\Domain\Model\FileReference $patternfile) {
        $this->patternfile = $patternfile;
    }
    
    /**
     * @return string The pattern file format.
     */
    public function getPatternfileformat() {
        return $this->patternfileformat;
    }
    
    /**
     * @param sring $patternfileFormat The new pattern file format.
     */
    public function setPatternfileformat($patternfileFormat) {
        $this->patternfileformat = $patternfileFormat;
    }
    
    /**
     * @return \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface The cache
     * frontend for the tries.
     */
    protected function getTrieCache() {
        return $GLOBALS['typo3CacheManager']->getCache('nkhyphenation_triecache');
    }
    
    /**
     * @return string The cache identifier for the trie of this pattern set.
     */
    protected function getTrieCacheIdentifier() {
        return 'hyphenationPatterns-' . $this->getUid();
    }
    
    /**
     * Writes the current trie to the cache.
     */
    protected function updateTrieCache() {
        $trieCacheInstance = $this->getTrieCache();
        $trieCacheInstance->set($this->getTrieCacheIdentifier(), $this->trie);
    }

    /**
     * Returns the hyphenation-TRIE.
     * @return array
     */
    public function getTrie() {
        
        $trieCacheInstance = $this->getTrieCache();
                
        if (!$trieCacheInstance->has($this->getTrieCacheIdentifier())) {
            $this->buildTrie();
        }
        
        return $this->trie;
    }

    /**
     * Empties the trie.
     * @return void
     */
    public function resetTrie() {
        
        $this->trie = array();
        $this->updateTrieCache();
    }

    /**
     * Inserts a pattern into a hyphenation trie.
     * @param string $pattern The pattern to insert.
     * @param boolean $updateCache Defines if the cached trie should be
     * updated. This is useful if many patterns will be inserted
     * one after another, since in that case there is no need to update the
     * cache after each pattern. If you set this to false, make sure you update
     * the cache manually afterwards or set the parameter to true when inserting
     * the last pattern.
     * @return void
     * @license The code of this method is heavily inspired (but not simply
     * ported) by a code piece from Hyphenator.js. The code there is in turn a
     * modified version of code from hypher.js by Bram Stein, 2011.
     */
    public function insertPatternIntoTrie($pattern, $updateCache = TRUE) {
        
        $characters = preg_split('//u', preg_replace('/\d/', '', $pattern), -1, PREG_SPLIT_NO_EMPTY);
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
        
        if (TRUE === $updateCache) {
            $this->updateTrieCache();
        }
    }
    
    /**
     * Fill this pattern-object from a patternProvider.
     * @param \Netzkoenig\Nkhyphenation\Utility\AbstractPatternProvider $patternProvider
     */
    protected function addPatterns($patternProvider) {
        
        $patternList = $patternProvider->getPatternList();
        
        foreach ($patternList as $pattern) {
            // Don't update the cache, do that once at the end of this method
            $this->insertPatternIntoTrie($pattern, FALSE);
        }
        
        // Update the cache, needed since not done above.
        $this->updateTrieCache();
        
        $this->setLeftmin($patternProvider->getMinCharactersBeforeFirstHyphen());
        $this->setRightmin($patternProvider->getMinCharactersAfterLastHyphen());
        
        $this->setWordcharacters($patternProvider->getWordCharacterList());
    }
    
    /**
     * Builds the trie from the current pattern file.
     */
    public function buildTrie() {
        
        if (!is_null($this->patternfile)) {
        
            $patternfileContent = $this->patternfile->getOriginalResource()->getContents();
            
            switch ($this->patternfileformat) {
                case 'hyphenatorjs':
                    $patternprovider = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                            '\\Netzkoenig\\Nkhyphenation\\Utility\\HyphenatorJSPatternProvider',
                            $patternfileContent
                    );
                    break;
                default:
                    throw new \TYPO3\CMS\Core\Exception('Unknown pattern file format.', 1385210987);
            }

            $this->resetTrie();
            $this->addPatterns($patternprovider);
        }
        else {
            $this->resetTrie();
            $this->trie = array();
            $this->updateTrieCache();
        }
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

        $characters = preg_split('//u', mb_strtolower('.' . $word . '.', 'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);
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

        // Get the original characters to build the result. The $characters
        // array had strtolower applied to it.
        $originalCharacters = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
        
        for ($i = 1; $i < count($characters) - 1; $i++) {
            if (   (($points[$i] % 2) === 1)
                && ($this->getLeftmin() < $i)
                && ($i < (count($characters) - $this->getRightmin()))
               ) {

                array_push($result, $part);
                $part = $originalCharacters[$i-1];
            }
            else {
                $part .= $originalCharacters[$i-1];
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

        $trie = $this->getTrie();
        
        // Characters that are part of a word: \u200C is a zero-width space,
        // \u00AD is the soft-hyphen &shy;
        $unicodeWordCharacters = preg_split('//u', json_decode('"\u200C\u00AD"'), -1, PREG_SPLIT_NO_EMPTY);
        
        $wordCharacters = $this->getWordcharacters();
        $wordCharacters = array_merge($wordCharacters, $unicodeWordCharacters);

        $wordSplittingRegex = '/((?:' . implode('|', $wordCharacters) . ')+)/u';

        $thisInstance = $this;

        return preg_replace_callback(
                $wordSplittingRegex,
                function($matches) use ($thisInstance) {
                    return $thisInstance->hyphenateWord($matches[1]);
                },
                $text
        );
    }
}
