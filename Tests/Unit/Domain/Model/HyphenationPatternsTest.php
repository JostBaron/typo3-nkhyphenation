<?php

namespace Netzkoenig\Nkhyphenation\Tests\Unit\Domain\Model;

/**
 * Description of HyphenationPatternsTest
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class HyphenationPatternsTest
        extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

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
                'Netzkoenig\\Nkhyphenation\\Domain\\Model\\HyphenationPatterns',
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
                        'points' => array(0, 4, 0, 0),
                    ),
                ),
            ),
        );

        $this->assertEquals($expectedResult, $this->hyphenationPatterns->getTrie());
//        $this->assertEquals(serialize($expectedResult), $this->hyphenationPatterns->getSerializedTrie());
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
                        'points' => array(0, 4, 0, 0),
                    ),
                    'e' => array(
                        '_' => array(
                            'points' => array(0, 0, 2, 0, 0),
                        ),
                    ),
                ),
            ),
        );

        $this->assertEquals($expectedResult, $this->hyphenationPatterns->getTrie());
        $this->assertEquals(serialize($expectedResult), $this->hyphenationPatterns->getSerializedTrie());
    }

    /**
     * @test
     */
    public function splittingPointAtStart() {
        $this->hyphenationPatterns->_call('insertPatternIntoTrie', '9ad');

        $expectedResult = array(
            'a' => array(
                'd' => array(
                    'points' => array(9, 0, 0)
                ),
            ),
        );

        $this->assertEquals($expectedResult, $this->hyphenationPatterns->getTrie());
        $this->assertEquals(serialize($expectedResult), $this->hyphenationPatterns->getSerializedTrie());
    }

    /**
     * @test
     */
    public function splittingPointAtEnd() {
        $this->hyphenationPatterns->_call('insertPatternIntoTrie', 'ad1');

        $expectedResult = array(
            'a' => array(
                'd' => array(
                    'points' => array(0, 0, 1)
                ),
            ),
        );

        $this->assertEquals($expectedResult, $this->hyphenationPatterns->getTrie());
        $this->assertEquals(serialize($expectedResult), $this->hyphenationPatterns->getSerializedTrie());
    }
    
    /**
     * @test
     */
    public function multipleSplittingPoints() {
        $this->hyphenationPatterns->_call('insertPatternIntoTrie', 'a3d1');

        $expectedResult = array(
            'a' => array(
                'd' => array(
                    'points' => array(0, 3, 1)
                ),
            ),
        );

        $this->assertEquals($expectedResult, $this->hyphenationPatterns->getTrie());
        $this->assertEquals(serialize($expectedResult), $this->hyphenationPatterns->getSerializedTrie());
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
    public function wordCharactersCanBeSet() {
        $specialCharacters = 'öas|ſ«¢„€łł¶€ŧ←ø↓←ł¹²³';
        $this->hyphenationPatterns->setWordCharacters($specialCharacters);
        $this->assertEquals($specialCharacters, join('', $this->hyphenationPatterns->getWordCharacters()));
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

    /**
     * @test
     */
    public function trieIsResetCorrectly() {

        // Fill TRIE with some random data and assert it has been inserted
        // to make sure this test is not futile.
        $this->hyphenationPatterns->_call('insertPatternIntoTrie', 'ad2e_');
        $this->assertNotEquals(array(), $this->hyphenationPatterns->getTrie());
        $this->assertNotEquals(serialize(array()), $this->hyphenationPatterns->getSerializedTrie());

        // Run the real test
        $this->hyphenationPatterns->resetTrie();
        $this->assertEquals(array(), $this->hyphenationPatterns->getTrie());
        $this->assertEquals(serialize(array()), $this->hyphenationPatterns->getSerializedTrie());
    }
}
