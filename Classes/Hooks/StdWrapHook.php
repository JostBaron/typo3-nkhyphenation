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

namespace Netzkoenig\Nkhyphenation\Hooks;

use Netzkoenig\Nkhyphenation\Domain\Repository\HyphenationPatternsRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectStdWrapHookInterface;

/**
 * Hook for stdWrap that adds hyphenation capabilities to stdWrap.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class StdWrapHook implements ContentObjectStdWrapHookInterface
{

    /**
     * Repository for hyphenation patterns.
     *
     * @var HyphenationPatternsRepository
     */
    protected $hyphenationPatternRepository = null;
    
    /**
     * Does nothing, only implemented to satisfy interface contract.
     * @param string $content The content to process.
     * @param array $configuration The TypoScript config of stdWrap.
     * @param ContentObjectRenderer $parentObject
     *
     * The parent rendering object.
     */
    public function stdWrapOverride(
        $content,
        array $configuration,
        ContentObjectRenderer &$parentObject
    ) {
        return $content;
    }
    
    /**
     * Processes the "hyphenateBefore" property.
     * @param string $content The content to process.
     * @param array $configuration The TypoScript config of stdWrap.
     * @param ContentObjectRenderer $parentObject
     * The parent rendering object.
     */
    public function stdWrapPreProcess(
        $content,
        array $configuration,
        ContentObjectRenderer &$parentObject
    ) {
        if (isset($configuration['hyphenateBefore.'])) {
            return $this->doHyphenation($content, $configuration['hyphenateBefore.'], $parentObject);
        } else {
            return $content;
        }
    }
    
    /**
     * Processes the "hyphenate" property.
     * @param string $content The content to process.
     * @param array $configuration The TypoScript config of stdWrap.
     * @param ContentObjectRenderer $parentObject
     * The parent rendering object.
     */
    public function stdWrapProcess(
        $content,
        array $configuration,
        ContentObjectRenderer &$parentObject
    ) {
        if (isset($configuration['hyphenate.'])) {
            return $this->doHyphenation($content, $configuration['hyphenate.'], $parentObject);
        } else {
            return $content;
        }
    }
    
    /**
     * Processes the "hyphenateAfter" property.
     * @param string $content The content to process.
     * @param array $configuration The TypoScript config of stdWrap.
     * @param ContentObjectRenderer $parentObject
     * The parent rendering object.
     */
    public function stdWrapPostProcess(
        $content,
        array $configuration,
        ContentObjectRenderer &$parentObject
    ) {
        if (isset($configuration['hyphenateAfter.'])) {
            return $this->doHyphenation($content, $configuration['hyphenateAfter.'], $parentObject);
        } else {
            return $content;
        }
    }
    
    /**
     * Processes one of the hyphenation properties. They are all build equal, so
     * only do the logic once.
     * @param string $content The content to process.
     * @param array $configuration The TypoScript config of stdWrap.
     * @param ContentObjectRenderer $parentObject
     * The parent rendering object.
     */
    public function doHyphenation(
        $content,
        array $configuration,
        ContentObjectRenderer &$parentObject
    ) {

        // Get the language (after some stdWrap processing)
        $languageValue = filter_var($configuration['language'], FILTER_VALIDATE_INT, array('min_range' => 0));
        
        if (isset($configuration['language.'])) {
            $languageProperties = $configuration['language.'];
            $languageStdWrapProperties = isset($languageProperties['stdWrap.']) ? $languageProperties['stdWrap.'] : array();
            
            $languageValue = $parentObject->stdWrap($languageValue, $languageStdWrapProperties);
        }
        
        // Find out if HTML tags should be preserved, do stdWrap processing for them.
        $preserveHtmlTags = isset($configuration['preserveHtmlTags']) ? $configuration['preserveHtmlTags'] : '1';
        
        if (isset($configuration['preserveHtmlTags.'])) {
            $preserveHtmlTagsProperties = $configuration['preserveHtmlTags.'];
            $preserveHtmlTagsStdWrapProperties = isset($preserveHtmlTagsProperties['stdWrap.']) ? $preserveHtmlTagsProperties['stdWrap.'] : array();
            
            $preserveHtmlTags = $parentObject->stdWrap($preserveHtmlTags, $preserveHtmlTagsStdWrapProperties);
        }
        
        $preserveHtmlTags = ('1' !== $preserveHtmlTags) ? FALSE : TRUE;
        
        // Fetch the correct pattern set and do the hyphenation.
        $hyphenationPatterns = $this->getHyphenationPatternRepository()->findOneBySystemLanguage($languageValue);
        
        if (!is_null($hyphenationPatterns)) {
            return $hyphenationPatterns->hyphenation($content, $preserveHtmlTags);
        }
        else {
            return $content;
        }
    }
    
    /**
     * Gets (and initializes, if necessary) the pattern repository.
     *
     * @return HyphenationPatternsRepository
     */
    protected function getHyphenationPatternRepository(): HyphenationPatternsRepository
    {
        if (is_null($this->hyphenationPatternRepository)) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $this->hyphenationPatternRepository = $objectManager->get(HyphenationPatternsRepository::class);
        }
        
        return $this->hyphenationPatternRepository;
    }
}
