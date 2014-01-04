<?php

namespace Netzkoenig\Nkhyphenation\Hooks;

/**
 * Hook for stdWrap that adds hyphenation capabilities to stdWrap.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class StdWrapHook implements \TYPO3\CMS\Frontend\ContentObject\ContentObjectStdWrapHookInterface {

    /**
     * Repository for hyphenation patterns.
     * @var \Netzkoenig\Nkhyphenation\Domain\Repository\HyphenationPatternsRepository
     */
    protected $hyphenationPatternRepository = NULL;
    
    /**
     * Does nothing, only implemented to satisfy interface contract.
     * @param string $content The content to process.
     * @param array $configuration The TypoScript config of stdWrap.
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $parentObject
     * The parent rendering object.
     */
    public function stdWrapOverride(
            $content,
            array $configuration,
            \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer &$parentObject
            ) {
        return $content;
    }
    
    /**
     * Processes the "hyphenateBefore" property.
     * @param string $content The content to process.
     * @param array $configuration The TypoScript config of stdWrap.
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $parentObject
     * The parent rendering object.
     */
    public function stdWrapPreProcess(
            $content,
            array $configuration,
            \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer &$parentObject
            ) {
        
        if (isset($configuration['hyphenateBefore.'])) {
            return $this->doHyphenation($content, $configuration['hyphenateBefore.'], $parentObject);
        }
        else {
            return $content;
        }
    }
    
    /**
     * Processes the "hyphenate" property.
     * @param string $content The content to process.
     * @param array $configuration The TypoScript config of stdWrap.
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $parentObject
     * The parent rendering object.
     */
    public function stdWrapProcess(
            $content,
            array $configuration,
            \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer &$parentObject
            ) {
        
        if (isset($configuration['hyphenate.'])) {
            return $this->doHyphenation($content, $configuration['hyphenate.'], $parentObject);
        }
        else {
            return $content;
        }
    }
    
    /**
     * Processes the "hyphenateAfter" property.
     * @param string $content The content to process.
     * @param array $configuration The TypoScript config of stdWrap.
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $parentObject
     * The parent rendering object.
     */
    public function stdWrapPostProcess(
            $content,
            array $configuration,
            \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer &$parentObject
            ) {
        
        if (isset($configuration['hyphenateAfter.'])) {
            return $this->doHyphenation($content, $configuration['hyphenateAfter.'], $parentObject);
        }
        else {
            return $content;
        }
    }
    
    /**
     * Processes one of the hyphenation properties. They are all build equal, so
     * only do the logic once.
     * @param string $content The content to process.
     * @param array $configuration The TypoScript config of stdWrap.
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $parentObject
     * The parent rendering object.
     */
    public function doHyphenation(
            $content,
            array $configuration,
            \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer &$parentObject) {

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
        
        $preserveHtmlTags = ('0' === $preserveHtmlTags) ? FALSE : TRUE;
        
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
     * @return \Netzkoenig\Nkhyphenation\Domain\Repository\HyphenationPatternsRepository
     */
    protected function getHyphenationPatternRepository() {
        
        if (is_null($this->hyphenationPatternRepository)) {
            $this->hyphenationPatternRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Netzkoenig\\Nkhyphenation\\Domain\\Repository\\HyphenationPatternsRepository');

            $querySettings = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
            $querySettings->setRespectStoragePage(FALSE);
            $querySettings->setRespectSysLanguage(FALSE);
            $this->hyphenationPatternRepository->setDefaultQuerySettings($querySettings);
        }
        
        return $this->hyphenationPatternRepository;
    }
}
