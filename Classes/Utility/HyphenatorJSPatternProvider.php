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

namespace Netzkoenig\Nkhyphenation\Utility;

/**
 * Pattern provider for hyphenator.js - provides hyphenation patterns from
 * Hyphenator.js pattern files.
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class HyphenatorJSPatternProvider {
    
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

        // Now replace all object keys with themself in a quoted version.
        $regex = '/((?:,|\{)\s*)([^:\s\'\"]+)(\s*:\s*)([^,\{]*|(?:\"[^\"\n]*\"))/u';
        $jsonInput = preg_replace($regex, '$1"$2"$3$4', $jsonInput);        

        // And replace all single quotes by double quotes (assuming they don't
        // belong to the data...
        $jsonInput = preg_replace("/'/u", '"', $jsonInput);
        
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
