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
        $this->registerArgument('patternsUid', 'int', 'Uid of the patterns record to use.', TRUE);
    }

    /**
     * Actually do the hyphenation.
     * @param string $content The content to hyphenate.
     */
    public function render() {
        
        $patternsUid = $this->arguments['patternsUid'];
        $patterns = $this->hyphenationPatternRepository->findByUid($patternsUid);
        
        $content = $this->renderChildren();
        
        return $patterns->hyphenation($content);
    }
}
