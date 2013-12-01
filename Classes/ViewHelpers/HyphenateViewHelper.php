<?php

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
        $this->registerArgument('preserveHtmlTags', 'boolean', 'Defines if HTML tags should be preseved.', FALSE, true);
    }

    /**
     * Actually do the hyphenation.
     * @param string $content The content to hyphenate.
     */
    public function render() {
        
        $patterns = $this->hyphenationPatternRepository->findOneBySystemLanguage($this->arguments['language']);
        
        $content = $this->renderChildren();
        
        return $patterns->hyphenation($content, $this->arguments['preserveHtmlTags']);
    }
}
