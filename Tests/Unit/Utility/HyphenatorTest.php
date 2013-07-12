<?php

/**
 * Unit tests for the class Hyphenator.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class Tx_Nkhyphenation_Tests_Unit_Domain_Model_HyphenatorTest
        extends Tx_Extbase_Tests_Unit_BaseTestCase {

    /**
     * Used to build the pattern trie.
     */
    protected $hyphenationPatterns;

    /**
     * Create a mocked up hypenation patterns object.
     * @return void
     */
    protected function setUp() {
        $this->hyphenationPatterns = $this->getAccessibleMock(
                'Tx_Nkhyphenation_Domain_Model_HyphenationPatterns',
                array('dummy')
        );
    }

    /**
     * @test
     * @dataProvider patternsAreCorrectlyAppliedToSingleWordDataProvider
     * @return void
     */
    public function patternsAreCorrectlyAppliedToSingleWord(
            $patterns,
            $inputString,
            $expectedResult) {

        foreach ($patterns as $pattern) {
            $this->hyphenationPatterns->_call('insertPatternIntoTrie', $pattern);
        }

        $hyphenator = $this->getAccessibleMock(
                'Tx_Nkhyphenation_Utility_Hyphenator',
                array('dummy'),
                array($this->hyphenationPatterns)
        );

        $result = $hyphenator->_call(
                'hyphenateWord',
                $inputString
        );

        $this->assertEquals($expectedResult, $result);
    }

    public function patternsAreCorrectlyAppliedToSingleWordDataProvider() {
        return array(
            'no pattern' => array(
                array(

                ),
                'someword',
                'someword'
            ),
            'single matching pattern with odd level' => array(
                array(
                    'me1wo',
                ),
                'someword',
                'some-word'
            ),
            'single matching pattern with even level' => array(
                array(
                    'me2wo',
                ),
                'someword',
                'someword'
            ),
            'single non-matching pattern' => array(
                array(
                    'me2woe',
                ),
                'someword',
                'someword'
            ),
            'multiple non-matching patterns' => array(
                array(
                    'me2woe',
                    'ma3wo'
                ),
                'someword',
                'someword'
            ),
            'multiple matching patterns' => array(
                array(
                    'o1me',
                    'wo3rd'
                ),
                'someword',
                'so-mewo-rd'
            ),
            'multiple matching patterns, overriding each other, highest level is even' => array(
                array(
                    'o1me',
                    'o4mew'
                ),
                'someword',
                'someword'
            ),
            'multiple matching patterns, overriding each other, highest level is odd' => array(
                array(
                    'o2me',
                    'o3mew'
                ),
                'someword',
                'so-meword'
            ),
            'multiple matching patterns, overriding each other, highest level is odd' => array(
                array(
                    'o2me',
                    'o3mew'
                ),
                'someword',
                'so-meword'
            ),
        );
    }

    /**
     * @test
     * @dataProvider textSplittedIntoCorrectWordsDataProvider
     * @return void
     */
    public function textSplittedIntoCorrectWords(
            $specialCharacters,
            $inputString,
            $expectedParts) {

        $this->hyphenationPatterns->setSpecialCharacters($specialCharacters);

        $hyphenator = $this->getAccessibleMock(
                'Tx_Nkhyphenation_Utility_Hyphenator',
                array('hyphenateWord'),
                array($this->hyphenationPatterns)
        );

        for ($i = 0; $i < count($expectedParts); $i++) {
            $hyphenator->expects($this->at($i))
                       ->method('hyphenateWord')
                       ->with($expectedParts[$i]);
        }

        $hyphenator->_call(
                'hyphenation',
                $inputString
        );
    }

    public function textSplittedIntoCorrectWordsDataProvider() {
        $unicodeJoiner = json_decode('"\u200C"');
        $unicodeSoftHyphen = json_decode('"\u00AD"');

        return array(
            'empty string' => array(
                '',
                '',
                array(),
            ),
            'single word' => array(
                '',
                'someword',
                array('someword'),
            ),
            'multiple words' => array(
                '',
                'word1 word2 word3',
                array('word1', 'word2', 'word3'),
            ),
            'no special chars set' => array(
                '',
                'word1äö',
                array('word1'),
            ),
            'special chars set' => array(
                'äöü',
                'word1äö',
                array('word1äö'),
            ),
            'multiple words, some special chars set, but not all' => array(
                'öü',
                'word1äö',
                array('word1', 'ö'),
            ),
            'hyphened word' => array(
                '',
                'some-word',
                array('some-word'),
            ),
            'joiner in word' => array(
                '',
                'some' . $unicodeJoiner . 'word',
                array('some' . $unicodeJoiner . 'word'),
            ),
            'soft hyphen in word' => array(
                '',
                'some' . $unicodeSoftHyphen . 'word',
                array('some' . $unicodeSoftHyphen . 'word'),
            ),
        );
    }
}

?>