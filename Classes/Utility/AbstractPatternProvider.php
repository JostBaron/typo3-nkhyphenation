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
 * Abstract pattern provider - provides hyphenation patterns and related stuff
 * from one source.
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
interface AbstractPatternProvider
{
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
