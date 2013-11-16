<?php

namespace Netzkoenig\Nkhyphenation\Tests\Unit\Service;

/**
 * Unit tests for the class Hyphenator.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class HyphenationServiceTest
        extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

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
                'Netzkoenig\\Nkhyphenation\\Domain\\Model\\HyphenationPatterns',
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

        $hyphenator = $this->getAccessibleMock(
                'Netzkoenig\\Nkhyphenation\\Service\\HyphenationService',
                array('dummy'),
                array($this->hyphenationPatterns)
        );

        $result = $hyphenator->hyphenateWord($inputString);

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
                    'ma3wo'
                ),
                '-',
                'someword',
                'someword'
            ),
            'multiple matching patterns' => array(
                array(
                    'o1me',
                    'wo3rd'
                ),
                '-',
                'someword',
                'so-mewo-rd'
            ),
            'multiple matching patterns, overriding each other, highest level is even' => array(
                array(
                    'o1me',
                    'o4mew'
                ),
                '-',
                'someword',
                'someword'
            ),
            'multiple matching patterns, overriding each other, highest level is odd' => array(
                array(
                    'o2me',
                    'o3mew'
                ),
                '-',
                'someword',
                'so-meword'
            ),
            'multiple matching patterns, overriding each other, highest level is odd' => array(
                array(
                    'o2me',
                    'o3mew'
                ),
                '-',
                'someword',
                'so-meword'
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

        $hyphenator = $this->getAccessibleMock(
                'Netzkoenig\\Nkhyphenation\\Service\\HyphenationService',
                array('dummy'),
                array($this->hyphenationPatterns)
        );

        $result = $hyphenator->hyphenateWord($word);

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

        $hyphenator = $this->getAccessibleMock(
                'Netzkoenig\\Nkhyphenation\\Service\\HyphenationService',
                array('dummy'),
                array($this->hyphenationPatterns)
        );

        $this->assertEquals('some-this-is-a-hyphen-word', $hyphenator->hyphenateWord('someword'));
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
                'Netzkoenig\\Nkhyphenation\\Service\\HyphenationService',
                array('hyphenateWord'),
                array($this->hyphenationPatterns)
        );

        for ($i = 0; $i < count($expectedParts); $i++) {
            $hyphenator->expects($this->at($i))
                       ->method('hyphenateWord')
                       ->with($expectedParts[$i]);
        }

        if (count($expectedParts) === 0) {
            $hyphenator->expects($this->never())
                       ->method('hyphenateWord');
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
            'multiple words separated by non-word-characters' => array(
                '',
                'word1,word2;word3ħword4',
                array('word1', 'word2', 'word3', 'word4'),
            ),
        );
    }
}
