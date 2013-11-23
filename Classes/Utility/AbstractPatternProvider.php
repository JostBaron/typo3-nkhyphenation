<?php

namespace Netzkoenig\Nkhyphenation\Utility;

/**
 * Abstract pattern provider - provides hyphenation patterns and related stuff
 * from one source.
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
abstract class AbstractPatternProvider {
    
    /**
     * @return array A list of patterns to use, order doesn't matter.
     */
    public function getPatternList();

    /**
     * @return array The list of word characters, each as a one-letter string.
     */
    public function getWordCharacterList();
    
    /**
     * @return int The minimal number of characters that must occur before the
     * first hyphen.
     */
    public function getMinCharactersBeforeFirstHyphen();
    
    /**
     * @return int The minimal number of characters that must occur after the
     * last hyphen.
     */
    public function getMinCharactersAfterLastHyphen();
}
