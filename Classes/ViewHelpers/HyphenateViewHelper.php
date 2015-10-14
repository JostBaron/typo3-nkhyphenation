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

namespace Netzkoenig\Nkhyphenation\ViewHelpers;

/**
 * Description of HyphenationViewHelper
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class HyphenateViewHelper
        extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
    
    /**
     * The hyphenation pattern repository
     * @var \Netzkoenig\Nkhyphenation\Domain\Repository\HyphenationPatternsRepository
     * @inject
     */
    protected $hyphenationPatternRepository;
    
    /**
     * Registers the arguments.
     */
    public function initializeArguments() {
        $this->registerArgument('language', 'int', 'Language of the hyphenated content.', TRUE);
        $this->registerArgument('preserveHtmlTags', 'boolean', 'Defines if HTML tags should be preseved.', FALSE, TRUE);
    }

    /**
     * Actually do the hyphenation.
     * @param string $content The content to hyphenate.
     */
    public function render() {
        
        $patterns = $this->hyphenationPatternRepository->findPatternsForSystemLanguage($this->arguments['language']);
        
        $content = $this->renderChildren();
        
        if (!is_null($patterns)) {
            return $patterns->hyphenation($content, $this->arguments['preserveHtmlTags']);
        }
        else {
            return $content;
        }
    }
}
