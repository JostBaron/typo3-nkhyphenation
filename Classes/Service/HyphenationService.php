<?php

namespace Netzkoenig\Nkhyphenation\Service;

/**
 * A hyphenator for a patternset. Once constructed, it allows to hyphenate
 * words and texts as defined by the given pattern set.
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class HyphenationService {

    /**
     * The patterns to use.
     * @var Tx_Nkhyphenation_Domain_Model_HyphenationPatterns
     */
    protected $patterns;

    /**
     * Builds a new hyphenator with the given patterns.
     * @param \Netzkoenig\Nkhyphenation\Domain\Model\HyphenationPatterns $patterns
     * The patterns to use.
     */
    public function __construct($patterns) {
        $this->patterns = $patterns;
    }
}
