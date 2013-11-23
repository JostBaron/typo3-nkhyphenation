<?php

namespace Netzkoenig\Nkhyphenation\Tests\Unit\Utility;

/**
 * @author jost
 */
class HyphenatorJSPatternProviderTest 
        extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {
    
    /**
     * @test
     * @dataProvider checkResultDataProvider
     */
    function checkResult(
            $input,
            $expectedMinLeft,
            $expectedMinRight,
            $expectedPatterns,
            $expectedWordCharacters
        ) {
        
        $patternProvider = $this->getAccessibleMock(
            'Netzkoenig\\Nkhyphenation\\Utility\\HyphenatorJSPatternProvider',
            array('dummy'),
            array(
                $input
            )
        );
        
        $this->assertEquals($expectedMinLeft, $patternProvider->getMinCharactersBeforeFirstHyphen());
        $this->assertEquals($expectedMinRight, $patternProvider->getMinCharactersAfterLastHyphen());
        
        // Sort the arrays (expected and actual) to make comparisons easy.
        $actualPatternList = $patternProvider->getPatternList();
        $actualWordCharacterList = $patternProvider->getWordCharacterList();
        
        sort($actualPatternList);
        sort($actualWordCharacterList);
        
        sort($expectedPatterns);
        sort($expectedWordCharacters);
        
        $this->assertEquals($expectedPatterns, $actualPatternList);
        $this->assertEquals($expectedWordCharacters, $actualWordCharacterList);
    }
    
    function checkResultDataProvider() {
        $defaultWordCharacters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@-_';
        $defaultWordCharacters = preg_split('//u', $defaultWordCharacters, -1, PREG_SPLIT_NO_EMPTY);

        return array(
            'Multiple patterns per length' => array(
<<<'EOD'
// For questions about the Bengali hyphenation patterns
// ask Santhosh Thottingal (santhosh dot thottingal at gmail dot com)
Hyphenator.languages['bn'] = {
        leftmin : 2,
        rightmin : 2,
        patterns : {
                2 : "a2c43b",
                3 : "a3ac5a"
        }
};
EOD
                ,
                2,
                2,
                array('a2', 'c4', '3b', 'a3a', 'c5a'),
                $defaultWordCharacters
            ),
            'One pattern per length' => array(
<<<'EOD'
Hyphenator.languages['sad']={leftmin:1,rightmin:4,patterns:{2:"a2",3:"a3g"}};
EOD
                ,
                1,
                4,
                array('a2', 'a3g'),
                $defaultWordCharacters
            ),
            'Special characters given' => array(
<<<'EOD'
Hyphenator.languages['sad']={leftmin:1,rightmin:4,patterns:{2:"a2",3:"a3g"}, specialChars: "äöüÄÜÖßſ"};
EOD
                ,
                1,
                4,
                array('a2', 'a3g'),
                array_merge($defaultWordCharacters, array('ä', 'ö', 'ü', 'Ä', 'Ü', 'Ö', 'ß', 'ſ'))
            ),
        );
    }
}
