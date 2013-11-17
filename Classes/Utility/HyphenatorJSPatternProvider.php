<?php

namespace Netzkoenig\Nkhyphenation\Utility;

/**
 * Pattern provider for hyphenator.js - provides hyphenation patterns from
 * Hyphenator.js pattern files.
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
abstract class HyphenatorJSPatternProvider {
    
    /**
     * List of patterns.
     * @var array
     */
    protected $patternList = array();
    
    /**
     * List of word characters.
     * @var array
     */
    protected $wordCharacterList = array();
    
    
    /**
     * Number of characters that must occur after the last hyphen
     * @var int
     */
    protected $minCharactersAfterLastHyphen;
    /**
     * Number of characters that must occur before the first hyphen
     * @var int
     */
    protected $minCharactersBeforeFirstHyphen;
    
    public function __construct($filecontent) {
        /*
         * The following is a bit hackish. In order not to use a complete
         * JS-Parser to read the input, try to transform the input to a valid
         * JSON string and parser that using built-in functions.
         */
        
        // First, strip the assignment
        $jsonInput = preg_replace('/^[^=]*=/u', '', $filecontent);

        // Remove trailing ';':
        $jsonInput = preg_replace('/\s*;\s*$/u', '', $jsonInput);
        
        $regex = '/((?:,|\{)\s*)([^:\s\'\"]+)(\s*:\s*)([^,\{]*|(?:\"[^\"\n]*\"))/u';
        $matches = array();
        preg_match($regex, $jsonInput, $matches);
        
        // Now replace all object keys with themself in a quoted version.
        $jsonInput = preg_replace($regex, '$1"$2"$3$4', $jsonInput);        
        
        // Decode file
        $parsedInput = json_decode($jsonInput, true);
        
        
        // Now fill the variables with the file contents.
        $this->minCharactersBeforeFirstHyphen = $parsedInput['leftmin'];
        $this->minCharactersAfterLastHyphen = $parsedInput['rightmin'];

        foreach ($parsedInput['patterns'] as $patternLength => $patterns) {
            $patternLength = intval($patternLength);
            $patternsWithLength = array();
            preg_match_all('/.{' . $patternLength . '}/u', $patterns, $patternsWithLength);
            
            // Add the patterns to the pattern list
            $this->patternList = array_merge($this->patternList, $patternsWithLength[0]);
        }

        // Default characters in Hyphenator.js are the ones matched by \w in JS
        // regexes, plus '@' and '-
        $characterList  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';
        $characterList .= '@-';
        // Add the special characters from this hyphenation pattern file:
        $characterList .= $parsedInput['specialChars'];
        $this->wordCharacterList = preg_split('//u', $characterList, -1, PREG_SPLIT_NO_EMPTY);
    }
    
    /**
     * @return array A list of patterns to use, order doesn't matter.
     */
    public function getPatternList() {
        return $this->patternList;
    }

    /**
     * @return array The list of word characters, each as a one-letter string.
     */
    public function getWordCharacterList() {
        return $this->wordCharacterList;
    }
    
    /**
     * @return int The minimal number of characters that must occur before the
     * first hyphen.
     */
    public function getMinCharactersBeforeFirstHyphen() {
        return $this->minCharactersBeforeFirstHyphen;
    }
    
    /**
     * @return int The minimal number of characters that must occur after the
     * last hyphen.
     */
    public function getMinCharactersAfterLastHyphen() {
        return $this->minCharactersAfterLastHyphen;
    }
}
