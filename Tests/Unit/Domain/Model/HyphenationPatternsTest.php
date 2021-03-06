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
     * Create a mocked hypenation patterns object.
     * @return void
     */
    protected function setUp() {
        $this->hyphenationPatterns = $this->getAccessibleMock(
                'Netzkoenig\\Nkhyphenation\\Domain\\Model\\HyphenationPatterns',
                array('dummy')
        );
        
        $this->hyphenationPatterns->resetTrie();
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
        $wordCharacters = 'öas|ſ«¢„€ł¶ŧ←ø↓¹²³';
        $this->hyphenationPatterns->setWordCharacters($wordCharacters);
        $this->assertEquals($wordCharacters, join('', $this->hyphenationPatterns->getWordCharacters()));
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
        $this->assertNotEquals(NULL, $this->hyphenationPatterns->getTrie());

        // Run the real test
        $this->hyphenationPatterns->resetTrie();
        $this->assertEquals(array(), $this->hyphenationPatterns->getTrie());
    }    

    /**
     * @test
     * @dataProvider patternsAreCorrectlyAppliedToSingleWordDataProvider
     * @return void
     */
    public function patternsAreCorrectlyAppliedToSingleWord(
            $patterns,
            $hyphen,
            $inputString,
            $expectedResult
        ) {

        foreach ($patterns as $pattern) {
            $this->hyphenationPatterns->_call('insertPatternIntoTrie', $pattern);
        }

        $this->hyphenationPatterns->setHyphen($hyphen);
        $this->hyphenationPatterns->setLeftmin(0);
        $this->hyphenationPatterns->setRightmin(0);

        $result = $this->hyphenationPatterns->hyphenateWord($inputString);

        $this->assertEquals($expectedResult, $result);
    }

    public function patternsAreCorrectlyAppliedToSingleWordDataProvider() {
        return array(
            'no pattern' => array(
                array(

                ),
                '-',
                'someword',
                'someword'
            ),
            'single matching pattern with odd level' => array(
                array(
                    'me1wo',
                ),
                '-',
                'someword',
                'some-word'
            ),
            'single matching pattern with even level' => array(
                array(
                    'me2wo',
                ),
                '-',
                'someword',
                'someword'
            ),
            'single non-matching pattern' => array(
                array(
                    'me2woe',
                ),
                '-',
                'someword',
                'someword'
            ),
            'multiple non-matching patterns' => array(
                array(
                    'me2woe',
                    'ma3wo',
                ),
                '-',
                'someword',
                'someword'
            ),
            'multiple matching patterns' => array(
                array(
                    'o1me',
                    'wo3rd',
                ),
                '-',
                'someword',
                'so-mewo-rd'
            ),
            'multiple matching patterns, overriding each other, highest level is even' => array(
                array(
                    'o1me',
                    'o4mew',
                ),
                '-',
                'someword',
                'someword'
            ),
            'multiple matching patterns, overriding each other, highest level is odd' => array(
                array(
                    'o2me',
                    'o3mew',
                ),
                '-',
                'someword',
                'so-meword'
            ),
            'Pattern with splitting point at end' => array(
                array(
                    'me1',
                ),
                '-',
                'someword',
                'some-word'
            ),
            'Pattern with splitting point at start' => array(
                array(
                    '9me',
                ),
                '-',
                'someword',
                'so-meword'
            ),
            'Pattern with multiple splitting points' => array(
                array(
                    '9me1',
                ),
                '-',
                'someword',
                'so-me-word'
            ),
            'Multiple patterns with multiple splitting points, no override' => array(
                array(
                    '9me1',
                    'ew5o',
                ),
                '-',
                'someword',
                'so-me-w-ord'
            ),
            'Multiple patterns with multiple splitting points, with override' => array(
                array(
                    '9me1',
                    'e2wo',
                ),
                '-',
                'someword',
                'so-meword'
            ),
            'Word start marker is respected' => array(
                array(
                    '.bl1a',
                ),
                '-',
                'blahblah',
                'bl-ahblah'
            ),
            'Word end marker is respected' => array(
                array(
                    'l3ah.',
                ),
                '-',
                'blahblah',
                'blahbl-ah'
            ),
            'Case of word is preserved' => array(
                array(
                    'som1eword'
                ),
                '-',
                'SomeWoRD',
                'Som-eWoRD'
            ),
        );
    }

    /**
     * @test
     * @dataProvider hyphenationRespectsMinimalCharactersDataProvider
     * @return void
     */
    public function hyphenationRespectsMinimalCharacters(
            $patterns,
            $minLeft,
            $minRight,
            $word,
            $expectedResult
            ) {

        foreach ($patterns as $pattern) {
            $this->hyphenationPatterns->_call('insertPatternIntoTrie', $pattern);
        }

        $this->hyphenationPatterns->setHyphen('-');
        $this->hyphenationPatterns->setLeftmin($minLeft);
        $this->hyphenationPatterns->setRightmin($minRight);

        $result = $this->hyphenationPatterns->hyphenateWord($word);

        $this->assertEquals($expectedResult, $result);
    }

    public function hyphenationRespectsMinimalCharactersDataProvider() {
        return array(
            'break in middle of word' => array(
                array('me5wo'),
                2,
                3,
                'someword',
                'some-word'
            ),
            'break before leftmin characters' => array(
                array('so1me'),
                3,
                3,
                'someword',
                'someword'
            ),
            'break after less than rightmin characters are left' => array(
                array('wo1rd'),
                0,
                3,
                'someword',
                'someword'
            ),
            'word split in middle' => array(
                array('some5word'),
                4,
                4,
                'someword',
                'some-word'
            ),
            'word to short' => array(
                array('some5word'),
                4,
                5,
                'someword',
                'someword'
            ),
            'break at leftmost possible position' => array(
                array('so3me'),
                2,
                0,
                'someword',
                'so-meword'
            ),
            'break at rightmost possible position' => array(
                array('wo5rd'),
                0,
                2,
                'someword',
                'somewo-rd'
            ),
        );
    }




    /**
     * @test
     */
    public function hyphenateWordRespectsSetHyphen() {
        $this->hyphenationPatterns->_call('insertPatternIntoTrie', 'me3w');
        $this->hyphenationPatterns->setHyphen('-this-is-a-hyphen-');

        $this->assertEquals('some-this-is-a-hyphen-word', $this->hyphenationPatterns->hyphenateWord('someword'));
    }




    /**
     * @test
     * @dataProvider textSplittedIntoCorrectWordsDataProvider
     * @return void
     */
    public function textSplittedIntoCorrectWords(
            $wordCharacters,
            $inputString,
            $expectedParts) {

        $this->hyphenationPatterns = $this->getAccessibleMock(
                'Netzkoenig\\Nkhyphenation\\Domain\\Model\\HyphenationPatterns',
                array('hyphenateWord')
        );

        $this->hyphenationPatterns->setWordCharacters($wordCharacters);

        $numberExpectedParts = count($expectedParts);
        for ($i = 0; $i < $numberExpectedParts; $i++) {
            $this->hyphenationPatterns->expects($this->at($i))
                                      ->method('hyphenateWord')
                                      ->with($expectedParts[$i]);
        }

        if (count($expectedParts) === 0) {
            $this->hyphenationPatterns->expects($this->never())
                                      ->method('hyphenateWord');
        }

        $this->hyphenationPatterns->_call(
                'hyphenation',
                $inputString
        );
    }

    public function textSplittedIntoCorrectWordsDataProvider() {
        $unicodeJoiner = json_decode('"\u200C"');
        $unicodeSoftHyphen = json_decode('"\u00AD"');
        
        $defaultWordCharacters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-';

        return array(
            'empty string' => array(
                $defaultWordCharacters,
                '',
                array(),
            ),
            'single word' => array(
                $defaultWordCharacters,
                'someword',
                array('someword'),
            ),
            'multiple words' => array(
                $defaultWordCharacters,
                'wordone wordtwo wordthree',
                array('wordone', 'wordtwo', 'wordthree'),
            ),
            'no special chars set' => array(
                $defaultWordCharacters,
                'wordäö',
                array('word'),
            ),
            'special chars set' => array(
                $defaultWordCharacters . 'äüö',
                'wordäö',
                array('wordäö'),
            ),
            'multiple words, some special chars set, but not all' => array(
                $defaultWordCharacters . 'öü',
                'wordäö',
                array('word', 'ö'),
            ),
            'hyphened word' => array(
                $defaultWordCharacters,
                'some-word',
                array('some-word'),
            ),
            'joiner in word' => array(
                $defaultWordCharacters,
                'some' . $unicodeJoiner . 'word',
                array('some' . $unicodeJoiner . 'word'),
            ),
            'soft hyphen in word' => array(
                $defaultWordCharacters,
                'some' . $unicodeSoftHyphen . 'word',
                array('some' . $unicodeSoftHyphen . 'word'),
            ),
            'multiple words separated by non-word-characters' => array(
                $defaultWordCharacters,
                'wordone,wordtwo;wordthreeħwordfour',
                array('wordone', 'wordtwo', 'wordthree', 'wordfour'),
            ),
            'One word with different cases' => array(
                'abcdefghijklmnopqrstuvwxyz',
                'WoRAD',
                array('WoRAD'),
            ),
            'Case sensitive splitting with cyrillic letters' => array(
                'дҠЛЛ̕',
                'ДҠЛл',
                array('ДҠЛл')
            ),
        );
    }


    /**
     * @test
     * @dataProvider hyphenationWithHTMLWorksDataProvider
     * @return void
     */
    public function hyphenationWithHTMLWorks(
            $inputString,
            $wordcharacters,
            $hyphenString,
            $leftMin,
            $rightMin,
            $patterns,
            $expectedResult) {

        foreach ($patterns as $pattern) {
            $this->hyphenationPatterns->_call('insertPatternIntoTrie', $pattern);
        }

        $this->hyphenationPatterns->setHyphen($hyphenString);
        $this->hyphenationPatterns->setWordcharacters($wordcharacters);
        $this->hyphenationPatterns->setLeftmin($leftMin);
        $this->hyphenationPatterns->setRightmin($rightMin);
        
        $result = $this->hyphenationPatterns->hyphenation($inputString, TRUE);
        
        $this->assertEquals($expectedResult, $result);
    }

    public function hyphenationWithHTMLWorksDataProvider() {
        
        $defaultWordCharacters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-';
        
        return array(
            'Single tag with potential hyphens only in text' => array(
                '<span>Dies ist ein Test</span>',
                $defaultWordCharacters,
                '-',
                2,
                2,
                array(
                    'i1e',
                    'e3st.',
                ),
                '<span>Di-es ist ein Te-st</span>',
            ),
            'Single tag with potential hyphen in tagname' => array(
                '<span>Dies ist ein Test</span>',
                $defaultWordCharacters,
                '-',
                2,
                2,
                array(
                    'sp1an',
                ),
                '<span>Dies ist ein Test</span>',
            ),
            'Tag as pattern' => array(
                '<strong>Dies ist ein Test</strong>',
                $defaultWordCharacters,
                '-',
                2,
                2,
                array(
                    '</stro5ng>',
                ),
                '<strong>Dies ist ein Test</strong>',
            ),
            'Text nodes on highest level' => array(
                'Dies <span>ist ein</span> Test... Test, Test',
                $defaultWordCharacters,
                '-',
                2,
                2,
                array(
                    'te1st',
                    '1ie',
                    'a3n',
                ),
                'Dies <span>ist ein</span> Te-st... Te-st, Te-st',
            ),
            'Entity as hyphen' => array(
                '<p>Dies ist ein Test</p>',
                $defaultWordCharacters,
                '&shy;',
                2,
                2,
                array(
                    'te1st',
                    '1ie',
                    'a3n',
                ),
                html_entity_decode('<p>Dies ist ein Te&shy;st</p>', ENT_COMPAT | ENT_HTML401, 'UTF-8'),
            ),
        );
    }
}
