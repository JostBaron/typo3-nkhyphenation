<?php

namespace Netzkoenig\Nkhyphenation\Utility;

/**
 * Abstract pattern provider - provides hyphenation patterns and related stuff
 * from one source.
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
abstract class AbstractPatternProvider {
    
    /**
     * @return array A list of patterns to use, order doesn't matter. Returns
     * an empty array if no patterns are given (weird pattern file...).
     */
    public function getPatternList();

    /**
     * @return array The list of word characters, each as a one-letter string.
     * Returns an empty array if not word characters are given.
     */
    public function getWordCharacterList();
    
    /**
     * @return int The minimal number of characters that must occur before the
     * first hyphen. May return null to indicate that the value is not given.
     */
    public function getMinCharactersBeforeFirstHyphen();
    
    /**
     * @return int The minimal number of characters that must occur after the
     * last hyphen. May return null to indicate that the value is not given.
     */
    public function getMinCharactersAfterLastHyphen();
}
