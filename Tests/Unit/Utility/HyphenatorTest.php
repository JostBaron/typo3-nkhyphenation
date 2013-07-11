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
     * Hyphenator to test.
     */
    protected $hyphenator;

    /**
     * Create a mocked up hypenation patterns object.
     * @return void
     */
    protected function setUp() {
        $this->hyphenationPatterns = $this->getAccessibleMock(
                'Tx_Nkhyphenation_Domain_Model_HyphenationPatterns',
                array('dummy')
        );

        $this->hyphenator = $this->getAccessibleMock(
                'Tx_Nkhyphenation_Utility_Hyphenator',
                array('dummy'),
                array(),
                '',
                false
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

        $result = $this->hyphenator->_call(
                'hyphenateWord',
                $inputString,
                $this->hyphenationPatterns->_call('getTrie')
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
        );
    }
}

?>