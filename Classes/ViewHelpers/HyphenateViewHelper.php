<?php

declare(strict_types=1);

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

namespace Netzkoenig\Nkhyphenation\ViewHelpers;

use Netzkoenig\Nkhyphenation\Domain\Repository\HyphenationPatternsRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class HyphenateViewHelper extends AbstractViewHelper
{
    /**
     * The hyphenation pattern repository.
     *
     * @var HyphenationPatternsRepository
     */
    protected static $hyphenationPatternRepository;
    
    /**
     * Registers the arguments.
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'language',
            'int',
            'Language of the hyphenated content.',
            true
        );
        $this->registerArgument(
            'preserveHtmlTags',
            'boolean',
            'Defines if HTML tags should be preseved.',
            false,
            true
        );
    }

    /**
     * Actually do the hyphenation.
     *
     * @param string $content The content to hyphenate.
     */
    public function render()
    {
        static::renderStatic(
            $this->arguments,
            function () {
                return $this->renderChildren();
            },
            $this->renderingContext
        );
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $patterns = static::getHyphenationPatternRepository()->findOneBySystemLanguage($arguments['language']);

        $content = $renderChildrenClosure();

        if (!\is_null($patterns)) {
            return $patterns->hyphenation($content, $arguments['preserveHtmlTags']);
        } else {
            return $content;
        }
    }

    protected static function getHyphenationPatternRepository(): HyphenationPatternsRepository
    {
        if (null === static::$hyphenationPatternRepository) {
            /** @var ObjectManagerInterface $objectManager */
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            static::$hyphenationPatternRepository = $objectManager->get(HyphenationPatternsRepository::class);
        }

        return static::$hyphenationPatternRepository;
    }
}
