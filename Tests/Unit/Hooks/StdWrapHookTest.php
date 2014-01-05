<?php

namespace Netzkoenig\Nkhyphenation\Tests\Unit\Hooks;

/**
 * Test of the stdWrap hook.
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class StdWrapHookTest
        extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {
    
    protected $hookClass;
    protected $contentRenderObjectMock;
    protected $hyphenationPatternsRepositoryMock;
    protected $hyphenationPatternsMock;

    /**
     * Creates object under test and a mock.
     */
    protected function setUp() {
        
        // Create the mocks.
        $this->hookClass = $this->getAccessibleMock(
                'Netzkoenig\\Nkhyphenation\\Hooks\\StdWrapHook',
                array('getHyphenationPatternRepository')
        );
        
        $this->contentRenderObjectMock = $this->getAccessibleMock(
                'TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer',
                array('stdWrap')
        );
        
        $this->hyphenationPatternsRepositoryMock = $this->getAccessibleMock(
                'Netzkoenig\\Nkhyphenation\\Domain\\Repository\\HyphenationPatternsRepository',
                array('findOneBySystemLanguage')
        );
        
        $this->hyphenationPatternsMock = $this->getAccessibleMock(
                'Netzkoenig\\Nkhyphenation\\Domain\\Model\\HyphenationPatterns',
                array('hyphenation')
        );
        
        // Connect all the mocks.
        $this->hyphenationPatternsRepositoryMock->expects($this->any())
                                                ->method('findOneBySystemLanguage')
                                                ->will($this->returnValue($this->hyphenationPatternsMock));
        
        $this->hookClass->expects($this->any())
                        ->method('getHyphenationPatternRepository')
                        ->will($this->returnValue($this->hyphenationPatternsRepositoryMock));
    }
    
    /**
     * @test
     */
    public function stdWrapPreProcessTriggersHyphenationIfSet() {
        
        $this->hyphenationPatternsMock->expects($this->once())
                                      ->method('hyphenation')
                                      ->with(
                                              $this->equalTo('Testvalue'),
                                              $this->equalTo(TRUE)
                                        );

        $this->hookClass->stdWrapPreProcess(
                'Testvalue',
                array(
                    'hyphenateBefore.' => array(
                        'language' => '1',
                        'preserveHtmlTags' => '1',
                    )
                ),
                $this->contentRenderObjectMock
        );
    }
    
    /**
     * @test
     */
    public function stdWrapPreProcessDoesNotTriggerHyphenationIfNotSet() {
        
        $this->hyphenationPatternsMock->expects($this->never())
                                      ->method('hyphenation');

        $result = $this->hookClass->stdWrapPreProcess(
                        'Testvalue',
                        array(),
                        $this->contentRenderObjectMock
        );
        
        $this->assertEquals('Testvalue', $result);
    }
    
    /**
     * @test
     */
    public function stdWrapProcessTriggersHyphenationIfSet() {
        
        $this->hyphenationPatternsMock->expects($this->once())
                                      ->method('hyphenation')
                                      ->with(
                                              $this->equalTo('Testvalue'),
                                              $this->equalTo(TRUE)
                                        );

        $this->hookClass->stdWrapProcess(
                'Testvalue',
                array(
                    'hyphenate.' => array(
                        'language' => '1',
                        'preserveHtmlTags' => '1',
                    )
                ),
                $this->contentRenderObjectMock
        );
    }
    
    /**
     * @test
     */
    public function stdWrapProcessDoesNotTriggerHyphenationIfNotSet() {
        
        $this->hyphenationPatternsMock->expects($this->never())
                                      ->method('hyphenation');

        $result = $this->hookClass->stdWrapProcess(
                        'Testvalue',
                        array(
                            'hyphenateAfter.' => array(
                                'language' => '1',
                                'preserveHtmlTags' => '1',
                            )
                        ),
                        $this->contentRenderObjectMock
        );
        
        $this->assertEquals('Testvalue', $result);
    }
    
    /**
     * @test
     */
    public function stdWrapPostProcessTriggersHyphenationIfSet() {
        
        $this->hyphenationPatternsMock->expects($this->once())
                                      ->method('hyphenation')
                                      ->with(
                                              $this->equalTo('Testvalue'),
                                              $this->equalTo(TRUE)
                                        );

        $this->hookClass->stdWrapPostProcess(
                'Testvalue',
                array(
                    'hyphenateAfter.' => array(
                        'language' => '1',
                        'preserveHtmlTags' => '1',
                    )
                ),
                $this->contentRenderObjectMock
        );
    }
    
    /**
     * @test
     */
    public function stdWrapPostProcessDoesNotTriggerHyphenationIfNotSet() {
        
        $this->hyphenationPatternsMock->expects($this->never())
                                      ->method('hyphenation');

        $result = $this->hookClass->stdWrapPostProcess(
                        'Testvalue',
                        array(),
                        $this->contentRenderObjectMock
        );
        
        $this->assertEquals('Testvalue', $result);
    }
    
    /**
     * @test
     */
    public function stdWrapOverrideDoesNothingIfNoPropertySet() {
        
        $this->hyphenationPatternsMock->expects($this->never())
                                      ->method('hyphenation');

        $result = $this->hookClass->stdWrapOverride(
                                'Testvalue',
                                array(),
                                $this->contentRenderObjectMock
        );
        
        $this->assertEquals('Testvalue', $result);
    }
    
    /**
     * @test
     */
    public function stdWrapOverrideDoesNothingIfPropertySet() {
        
        $this->hyphenationPatternsMock->expects($this->never())
                                      ->method('hyphenation');

        $result = $this->hookClass->stdWrapOverride(
                                'Testvalue',
                                array(
                                    'hyphenateOverride.' => array(
                                        'language' => '1',
                                        'preserveHtmlTags' => '1',
                                    )
                                ),
                                $this->contentRenderObjectMock
        );
        
        $this->assertEquals('Testvalue', $result);
    }
    
    /**
     * @test
     */
    public function doHyphenationRespectsIfStdWrapNotUsed() {
        
        $this->hyphenationPatternsRepositoryMock->expects($this->once())
                                                ->method('findOneBySystemLanguage')
                                                ->with($this->equalTo('0'));

        $this->hyphenationPatternsMock->expects($this->once())
                                      ->method('hyphenation')
                                      ->with(
                                              $this->equalTo('Testvalue'),
                                              $this->equalTo(TRUE)
                                        );

        $this->hookClass->doHyphenation(
                'Testvalue',
                array(
                    'language' => '0',
                    'preserveHtmlTags' => '1'
                ),
                $this->contentRenderObjectMock
        );
    }
    
    /**
     * @test
     */
    public function doHyphenationRespectsStdWrapForLanguage() {
        
        $this->hyphenationPatternsRepositoryMock->expects($this->once())
                                                ->method('findOneBySystemLanguage')
                                                ->with($this->equalTo('foo0bar'));

        $this->contentRenderObjectMock->expects($this->once())
                                      ->method('stdWrap')
                                      ->with(
                                              $this->equalTo('0'),
                                              $this->equalTo(array('wrap' => 'foo|bar'))
                                        )
                                      ->will($this->returnValue('foo0bar'));

        $this->hookClass->doHyphenation(
                'Testvalue',
                array(
                    'language' => '0',
                    'language.' => array(
                        'stdWrap.' => array(
                            'wrap' => 'foo|bar',
                        ),
                    ),
                    'preserveHtmlTags' => '1'
                ),
                $this->contentRenderObjectMock
        );
    }
    
    /**
     * @test
     */
    public function doHyphenationRespectsStdWrapForPreserveHtmlTags() {
        
        $this->hyphenationPatternsMock->expects($this->once())
                                      ->method('hyphenation')
                                      ->with(
                                                $this->equalTo('Testvalue'),
                                                FALSE
                                        );

        $this->contentRenderObjectMock->expects($this->once())
                                      ->method('stdWrap')
                                      ->with(
                                              $this->equalTo('1'),
                                              $this->equalTo(array('wrap' => ''))
                                        )
                                      ->will($this->returnValue(''));

        $this->hookClass->doHyphenation(
                'Testvalue',
                array(
                    'language' => '0',
                    'preserveHtmlTags' => '1',
                    'preserveHtmlTags.' => array(
                        'stdWrap.' => array(
                            'wrap' => '',
                        ),
                    ),
                ),
                $this->contentRenderObjectMock
        );
    }
    
    /**
     * @test
     */
    public function stdWrapPreservesHtmlTagsByDefault() {
        
        $this->hyphenationPatternsMock->expects($this->once())
                                      ->method('hyphenation')
                                      ->with(
                                                $this->equalTo('Testvalue'),
                                                TRUE
                                        );

        $this->contentRenderObjectMock->expects($this->never())
                                      ->method('stdWrap');

        $this->hookClass->doHyphenation(
                'Testvalue',
                array(
                    'language' => '0',
                ),
                $this->contentRenderObjectMock
        );
    }
}
