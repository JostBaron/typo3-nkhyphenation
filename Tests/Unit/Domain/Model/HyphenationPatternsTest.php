<?php

/**
 * Description of HyphenationPatternsTest
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class Tx_Nkhyphenation_Tests_Unit_Domain_Model_HyphenationPatternsTest
        extends Tx_Extbase_Tests_Unit_BaseTestCase {

    /**
     * The hyphenation patters object to test.
     * @var Tx_Nkhyphenation_Domain_Model_HyphenationPatterns
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
     */
    public function patternIsInsertedIntoEmptyTree() {
        $this->hyphenationPatterns->_call('insertPatternIntoTrie', 'a4d_');

        $expectedResult = array(
            'a' => array(
                'd' => array(
                    '_' => array(
                        'points' => [0, 4, 0, 0],
                    ),
                ),
            ),
        );

        $result = $this->hyphenationPatterns->_get('trie');
        $this->assertEquals($expectedResult, $result, 'Expected: ' . print_r($expectedResult, true) . ', actual: ' . print_r($result, true));
    }

    /**
     * @test
     */
    public function additionalPatternDoesNotOverwrite() {
        $this->hyphenationPatterns->_call('insertPatternIntoTrie', 'a4d_');
        $this->hyphenationPatterns->_call('insertPatternIntoTrie', 'ad2e_');

        $expectedResult = array(
            'a' => array(
                'd' => array(
                    '_' => array(
                        'points' => [0, 4, 0, 0],
                    ),
                    'e' => array(
                        '_' => array(
                            'points' => [0, 0, 2, 0, 0],
                        ),
                    ),
                ),
            ),
        );

        $result = $this->hyphenationPatterns->_get('trie');
        $this->assertEquals($expectedResult, $result, 'Expected: ' . print_r($expectedResult, true) . ', actual: ' . print_r($result, true));
    }

    /**
     * @test
     */
    public function titleCanBeSet() {
        $title = 'This is a title.';
        $this->hyphenationPatterns->setTitle($title);
        $this->assertEquals($title, $this->hyphenationPatterns->getTitle());
    }

    /**
     * @test
     */
    public function specialCharactersCanBeSet() {
        $specialCharacters = 'öas|ſ«¢„€łł¶€ŧ←ø↓←ł¹²³';
        $this->hyphenationPatterns->setSpecialCharacters($specialCharacters);
        $this->assertEquals($specialCharacters, $this->hyphenationPatterns->getSpecialCharacters());
    }

    /**
     * @test
     */
    public function hyphenCanBeSet() {
        $hyphen = '---x---';
        $this->hyphenationPatterns->setHyphen($hyphen);
        $this->assertEquals($hyphen, $this->hyphenationPatterns->getHyphen());
    }

    /**
     * @test
     */
    public function leftminCanBeSet() {
        $leftmin = 100;
        $this->hyphenationPatterns->setLeftmin($leftmin);
        $this->assertEquals($leftmin, $this->hyphenationPatterns->getLeftmin());
    }

    /**
     * @test
     */
    public function rightminCanBeSet() {
        $rightmin = 100;
        $this->hyphenationPatterns->setRightmin($rightmin);
        $this->assertEquals($rightmin, $this->hyphenationPatterns->getRightmin());
    }

    /**
     * @test
     */
    public function systemLanguageCanBeSet() {
        $systemLanguage = 100;
        $this->hyphenationPatterns->setSystemLanguage($systemLanguage);
        $this->assertEquals($systemLanguage, $this->hyphenationPatterns->getSystemLanguage());
    }
}

?>