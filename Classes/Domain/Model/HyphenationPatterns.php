<?php
/*******************************************************************************
 * Copyright notice
 * (c) 2013 Jost Baron <j.baron@netzkoenig.de>
 * All rights reserved
 * 
 * This file is part of the TYPO3 extension "nkhyphenation".
 *
 * The TYPO3 extension "nkhyphenation" is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * The TYPO3 extension "nkhyphenation" is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the TYPO3 extension "nkhyphenation".  If not, see
 * <http://www.gnu.org/licenses/>.
 ******************************************************************************/

namespace Netzkoenig\Nkhyphenation\Domain\Model;

use Netzkoenig\Nkhyphenation\Exception\HyphenationException;
use Netzkoenig\Nkhyphenation\Utility\HyphenatorJSPatternProvider;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Contains a set of hyphenation patterns. The patterns are stored in a file,
 * but are also stored as serialized trie in the database, in order to make
 * their retrieval fast.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class HyphenationPatterns extends AbstractEntity
{
    /**
     * Title of this pattern set.
     * @var string
     */
    protected $title;

    /**
     * Characters that may make up a word in this language.
     * @var string
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
    protected $patternfile;
    
    /**
     * @var string The pattern file format.
     */
    protected $patternfileformat;

    /**
     * Check if data from the cache was already fetched.
     * @var boolean
     */
    protected $dataFromCacheFetched = FALSE;
    
    /**
     * Trie of the hyphenation patterns.
     * @var array
     */
    protected $trie;
    
    /**
     * Data from the pattern file that are not patterns, for example word
     * characters, leftmin and rightmin data.
     * @type array
     */
    protected $dataFromFile;

    /**
     * @return \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface The cache
     * frontend for the tries.
     */
    protected function getCache()
    {
        return $GLOBALS['typo3CacheManager']->getCache('nkhyphenation_cache');
    }
    
    /**
     * @return string The cache identifier for the trie of this pattern set.
     */
    protected function getCacheIdentifier(): string
    {
        return 'hyphenationPatterns-' . $this->getUid();
    }

    /**
     * Fetch the data from the patternfile that is cached.
     */
    protected function makeSureCachedDataIsAvailable(): void
    {
        if ($this->dataFromCacheFetched) {
            return;
        } else {
            $cacheInstance = $this->getCache();

            if (!$cacheInstance->has($this->getCacheIdentifier())) {
                $this->buildCache();
            }

            $cacheEntry = $cacheInstance->get($this->getCacheIdentifier());

            $this->trie         = $cacheEntry['trie'];
            $this->dataFromFile = $cacheEntry['dataFromFile'];

            $this->dataFromCacheFetched = true;
        }
    }
    
    /**
     * Writes the current trie to the cache.
     */
    protected function updateCache(): void
    {
        
        $cacheEntry = array(
            'dataFromFile' => $this->dataFromFile,
            'trie'         => $this->trie
        );

        $trieCacheInstance = $this->getCache();
        $trieCacheInstance->set($this->getCacheIdentifier(), $cacheEntry);
    }

    /**
     * Returns the title of the record.
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Sets the title of the record.
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * Returns the word characters (the ones that define what a word is).
     *
     * @return string[]
     *
     * @throws HyphenationException
     */
    public function getWordcharacters(): array
    {
        $this->makeSureCachedDataIsAvailable();

        $this->wordcharacters = $this->sanitizeWordCharacters($this->wordcharacters);
        
        if (isset($this->dataFromFile['wordcharacters'])) {
            $fileWordcharacters = $this->dataFromFile['wordcharacters'];
        } else {
            $fileWordcharacters = [];
        }
        
        return \array_unique(array_merge($this->wordcharacters, $fileWordcharacters));
    }

    /**
     * Sets the word characters to use.
     *
     * @param string[]|string|null $wordCharacters
     */
    public function setWordcharacters($wordCharacters): void
    {
        $this->wordcharacters = $this->sanitizeWordCharacters($wordCharacters);
    }
    
    /**
     * 
     * @param string $wordcharacters
     * @return string[]|string|null[]
     * @throws HyphenationException If the
     * parameter is neither null, an array nor a string.
     */
    public function sanitizeWordCharacters($wordcharacters): array
    {
        if (\is_array($wordcharacters)) {
            return $wordcharacters;
        } else if (\is_string($wordcharacters)) {
            return \preg_split('//u', $wordcharacters, -1, PREG_SPLIT_NO_EMPTY);
        } else if (\is_null($wordcharacters)) {
            return [];
        } else {
            throw new HyphenationException(
                    'The list of word characters must be a string or an array,'
                    . ' but got \'' . gettype($wordcharacters) . '\' instead.',
                    1384634628
            );
        }
    }

    /**
     * Returns the hyphen to use, as UTF-8 string with decoded entities.
     * @return string
     */
    public function getHyphen(): string
    {
        return html_entity_decode($this->hyphen, ENT_COMPAT | ENT_HTML401, 'UTF-8');
    }

    /**
     * Set the hyphen to use.
     * @param string $hyphen
     */
    public function setHyphen(string $hyphen)
    {
        $this->hyphen = $hyphen;
    }

    /**
     * Returns the minimal number of characters in a word that must occur before
     * a hyphen. The value from the pattern record takes precedence over any
     * value from a pattern file.
     *
     * @return int
     */
    public function getLeftmin(): int
    {
        $this->makeSureCachedDataIsAvailable();
        
        return is_null($this->leftmin) ? $this->dataFromFile['leftmin'] : $this->leftmin;
    }

    /**
     * Sets the minimal number of characters in a word that must occur before a
     * hyphen.
     *
     * @param int $leftmin
     */
    public function setLeftmin(int $leftmin): void
    {
        $this->makeSureCachedDataIsAvailable();
        
        $this->leftmin = $leftmin;
    }

    /**
     * Returns the minimal number of characters in a word that must occur after
     * a hyphen. The value from the pattern record takes precedence over any
     * value from a pattern file.
     *
     * @return int
     */
    public function getRightmin(): int
    {
        return is_null($this->rightmin) ? $this->dataFromFile['rightmin'] : $this->rightmin;
    }

    /**
     * Sets the minimal number of characters in a word that must occur after a
     * hyphen.
     *
     * @param int $rightmin
     */
    public function setRightmin(int $rightmin): void
    {
        $this->rightmin = $rightmin;
    }

    /**
     * Returns the system language this patternset is for.
     *
     * @return int
     */
    public function getSystemLanguage(): int
    {
        return $this->systemLanguage;
    }

    /**
     * Sets the system language this patternset ist for.
     *
     * @param int $systemLanguage
     */
    public function setSystemLanguage(int $systemLanguage): void
    {
        $this->systemLanguage = $systemLanguage;
    }
    
    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference The pattern file.
     */
    public function getPatternfile(): FileReference
    {
        return $this->patternfile;
    }
    
    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $patternfile The new
     * pattern file.
     */
    public function setPatternfile(FileReference $patternfile)
    {
        $this->patternfile = $patternfile;
    }
    
    /**
     * @return string The pattern file format.
     */
    public function getPatternfileformat(): string
    {
        return $this->patternfileformat;
    }
    
    /**
     * @param string $patternfileFormat The new pattern file format.
     */
    public function setPatternfileformat(string $patternfileFormat): void
    {
        $this->patternfileformat = $patternfileFormat;
    }

    /**
     * Returns the hyphenation-TRIE.
     *
     * @return array
     */
    public function getTrie(): array
    {
        return $this->trie;
    }

    /**
     * Empties the trie.
     * @return void
     */
    public function resetTrie(): void
    {
        
        $this->trie = array();
        $this->updateCache();
    }

    /**
     * Inserts a pattern into a hyphenation trie.
     *
     * @param string $pattern The pattern to insert.
     * @param boolean $updateCache Defines if the cached trie should be
     * updated. This is useful if many patterns will be inserted
     * one after another, since in that case there is no need to update the
     * cache after each pattern. If you set this to false, make sure you update
     * the cache manually afterwards or set the parameter to true when inserting
     * the last pattern.
     *
     * @return void
     *
     * @license The code of this method is heavily inspired (but not simply
     * ported) by a code piece from Hyphenator.js. The code there is in turn a
     * modified version of code from hypher.js by Bram Stein, 2011.
     */
    public function insertPatternIntoTrie(string $pattern, bool $updateCache = true): void
    {
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
        
        if ($updateCache) {
            $this->updateCache();
        }
    }
    
    /**
     * Fill this pattern-object from a patternProvider.
     *
     * @param \Netzkoenig\Nkhyphenation\Utility\AbstractPatternProvider $patternProvider
     *
     * @return void
     */
    protected function readPatternFile($patternProvider): void
    {
        $patternList = $patternProvider->getPatternList();
        
        foreach ($patternList as $pattern) {
            // Don't update the cache, do that once at the end of this method
            $this->insertPatternIntoTrie($pattern, FALSE);
        }
        
        $this->dataFromFile = [
            'leftmin'        => $patternProvider->getMinCharactersBeforeFirstHyphen(),
            'rightmin'       => $patternProvider->getMinCharactersAfterLastHyphen(),
            'wordcharacters' => $patternProvider->getWordCharacterList(),
        ];
        
        // Update the cache, needed since not done above.
        $this->updateCache();
    }
    
    /**
     * Builds the trie from the current pattern file.
     */
    public function buildCache(): void
    {
        if (!\is_null($this->patternfile)) {
            $patternfileContent = $this->patternfile->getOriginalResource()->getContents();
            
            switch ($this->patternfileformat) {
                case 'hyphenatorjs':
                    $patternprovider = GeneralUtility::makeInstance(
                        HyphenatorJSPatternProvider::class,
                        $patternfileContent
                    );
                    break;
                default:
                    throw new Exception('Unknown pattern file format.', 1385210987);
            }

            $this->resetTrie();
            $this->readPatternFile($patternprovider);
        }
        else {
            $this->resetTrie();
            $this->trie = [];
            $this->updateCache();
        }
    }

    /**
     * Hyphenation of a single word.
     *
     * @param string $word The word to hyphenate.
     *
     * @return string The word with hyphens inserted.
     *
     * @license The code of this method is heavily inspired (but not a simple
     * port) of a code piece from Hyphenator.js. The code there is in turn a
     * modified version of code from hypher.js by Bram Stein, 2011.
     */
    public function hyphenateWord(string $word): string
    {
        $characters = \preg_split('//u', \mb_strtolower('.' . $word . '.', 'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);
        $points = \array_fill(0, \count($characters), 0);

        $numberCharacters = count($characters);
        for ($i = 0; $i < $numberCharacters; $i++) {

            // Start from the root of the TRIE
            $currentTrieNode = $this->getTrie();
            
            for ($j = $i; $j < $numberCharacters; $j++) {

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
                    $nodePointsLength = count($nodePoints);
                    for ($k = 0; $k < $nodePointsLength; $k++) {
                        $points[$i + $k] = max($points[$i + $k], $nodePoints[$k]);
                    }
                }
            }
        }

        $result = [];
        $part = '';

        // Get the original characters to build the result. The $characters
        // array had strtolower applied to it.
        $originalCharacters = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
        
        for ($i = 1; $i < $numberCharacters - 1; $i++) {
            if (   (($points[$i] % 2) === 1)
                && ($this->getLeftmin() < $i)
                && ($i < ($numberCharacters - $this->getRightmin()))
               ) {

                \array_push($result, $part);
                $part = $originalCharacters[$i-1];
            }
            else {
                $part .= $originalCharacters[$i-1];
            }
        }

        // Push the last part.
        \array_push($result, $part);

        return \implode($this->getHyphen(), $result);
    }

    /**
     * Hyphenates a text.
     *
     * @param string $text The text to hyphenate.
     * @return string
     *
     * @license The code of this method is heavily inspired (but not a simple
     * port) of a code piece from Hyphenator.js. The code there is in turn a
     * modified version of code from hypher.js by Bram Stein, 2011.
     */
    public function hyphenation($text, $preserveHTMLTags = true): string
    {
        if ($preserveHTMLTags) {
            // Load the text into a DOMDocument, hyphenate and replace all test
            // nodes recursively and then print the resulting markup DOM. Watch
            // the encoding - convert the text to ASCII first, with HTML
            // entities for all characters outside the range of 7 bit ASCII.
            // Look here for details: https://stackoverflow.com/questions/11309194/php-domdocument-failing-to-handle-utf-8-characters
            $asciiText = \mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8');
            
            $domDocument = new \DOMDocument();
            $domDocument->loadHTML('<div>' . $asciiText . '</div>');
            
            // Walk through all text nodes:
            $xPath = new \DOMXPath($domDocument);
            $textNodes = $xPath->query('//text()');
            
            foreach ($textNodes as $textNode) {
                // Hyphenate the text node content, don't preserve HTML tags
                // there this time.
                $hyphenatedText = $this->hyphenation($textNode->nodeValue, FALSE);

                // Replace text node with a new node with hyphenated text.
                $hyphenatedNode = $domDocument->createTextNode($hyphenatedText);
                $textNode->parentNode->replaceChild($hyphenatedNode, $textNode);
            }
            
            // Generate the hyphenated HTML fragment
            $result = $domDocument->saveHTML($xPath->query('/html/body/div')->item(0));
            $result = \mb_substr($result, 5);
            $result = \mb_substr($result, 0, -6);
            
            unset($textNodes);
            unset($domDocument);
            unset($xPath);
            
            return $result;
        }

        // Characters that are part of a word: \u200C is a zero-width space,
        // \u00AD is the soft-hyphen &shy;
        $unicodeWordCharacters = \preg_split('//u', \json_decode('"\u200C\u00AD"'), -1, PREG_SPLIT_NO_EMPTY);

        $wordCharacters = $this->getWordcharacters();
        $wordCharacters = \array_merge($wordCharacters, $unicodeWordCharacters);

        $wordSplittingRegex = '/((?:' . \implode('|', $wordCharacters) . ')+)/iu';

        $thisInstance = $this;

        return \preg_replace_callback(
                $wordSplittingRegex,
                function($matches) use ($thisInstance) {
                    return $thisInstance->hyphenateWord($matches[1]);
                },
                $text
        );
    }
}
