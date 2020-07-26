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

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$ll = 'LLL:EXT:nkhyphenation/Resources/Private/Language/locallang_db.xml:';

$GLOBALS['TCA']['tx_nkhyphenation_domain_model_hyphenationpatterns'] = array(
    'ctrl' => $GLOBALS['TCA']['tx_nkhyphenation_domain_model_hyphenationpatterns']['ctrl'],
    'columns' => [
        'pid' => [
            'label'   => 'pid',
            'config'  => [
                'type'     => 'passthrough',
            ],
        ],
        'tstamp' => [
            'label'   => 'tstamp',
            'config'  => [
                'type'     => 'passthrough',
            ],
        ],
        'crdate' => [
            'label'   => 'crdate',
            'config'  => [
                'type'     => 'passthrough',
            ],
        ],
        'cruser_id' => [
            'label'   => 'cruser_id',
            'config'  => [
                'type'     => 'passthrough',
            ],
        ],
        'system_language' => [
            'exclude' => 1,
            'label'  => 'LLL:EXT:cms/locallang_ttc.xml:sys_language_uid_formlabel',
            'config' => [
                'type'                => 'select',
                'foreign_table'       => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['Default', '0'],
                ]
            ],
        ],
        'hidden' => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => [
                'type'    => 'check',
                'default' => '0',
            ],
        ],
        'title' => [
            'exclude' => 1,
            'label'   => $ll . 'hyphenationpatterns_title_label',
            'config' => [
                'type'    => 'input',
                'default' => '',
                'eval'    => 'trim',
            ],
        ],
        'wordcharacters' => [
            'exclude' => 1,
            'label'   => $ll . 'hyphenationpatterns_wordcharacters_label',
            'config' => [
                'type'    => 'input',
                'default' => '',
            ],
        ],
        'hyphen' => [
            'exclude' => 1,
            'label'   => $ll . 'hyphenationpatterns_hyphen_label',
            'config' => [
                'type'    => 'input',
                'default' => '&shy;',
                'eval'    => 'required',
            ],
        ],
        'leftmin' => [
            'exclude' => 1,
            'label'   => $ll . 'hyphenationpatterns_leftmin_label',
            'config'  => [
                'type'    => 'input',
                'default' => '2',
                'eval'    => 'required,trim,num,int'
            ],
        ],
        'rightmin' => [
            'exclude' => 1,
            'label'   => $ll . 'hyphenationpatterns_rightmin_label',
            'config'  => [
                'type'    => 'input',
                'default' => '2',
                'eval'    => 'required,trim,num,int'
            ],
        ],
        'patternfile' => [
            'label'   => $ll . 'hyphenationpatterns_patternfile_label',
            'exclude' => 1,
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'patternfile',
                    [
                        'maxitems' => 1,
                        'minitems' => 1,
                        'appearance' => [
                            'enabledControls' => [
                                'dragdrop' => FALSE,
                                'localize' => FALSE,
                            ],
                        ],
                    ]
                ),
        ],
        'patternfileformat' => [
            'label'   => $ll . 'hyphenationpatterns_patternfileformat_label',
            'exclude' => 1,
            'config'  => [
                'type' => 'select',
                'items' => [
                    [$ll . 'hyphenationpatterns_patternfileformat_hyphenatorjs_itemtext', 'hyphenatorjs'],
                ]
            ],
        ],
    ],
    'types' => [
        0 => [
            'showitem' => 'hidden,title,system_language,wordcharacters,hyphen,leftmin,rightmin,patternfile,patternfileformat',
        ],
    ],
);
