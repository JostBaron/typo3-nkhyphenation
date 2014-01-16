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

$TCA['tx_nkhyphenation_domain_model_hyphenationpatterns'] = array(
    'ctrl' => $TCA['tx_nkhyphenation_domain_model_hyphenationpatterns']['ctrl'],
    'columns' => array(
        'pid' => array(
            'label'   => 'pid',
            'config'  => array(
                'type'     => 'passthrough',
            ),
        ),
        'tstamp' => array(
            'label'   => 'tstamp',
            'config'  => array(
                'type'     => 'passthrough',
            ),
        ),
        'crdate' => array(
            'label'   => 'crdate',
            'config'  => array(
                'type'     => 'passthrough',
            ),
        ),
        'cruser_id' => array(
            'label'   => 'cruser_id',
            'config'  => array(
                'type'     => 'passthrough',
            ),
        ),
        'system_language' => array(
            'exclude' => 1,
            'label'  => 'LLL:EXT:cms/locallang_ttc.xml:sys_language_uid_formlabel',
            'config' => array(
                'type'                => 'select',
                'foreign_table'       => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('Default', '0'),
                )
            ),
        ),
        'hidden' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array(
                'type'    => 'check',
                'default' => '0',
            ),
        ),
        'title' => array(
            'exclude' => 1,
            'label'   => $ll . 'hyphenationpatterns_title_label',
            'config' => array(
                'type'    => 'input',
                'default' => '',
                'eval'    => 'trim',
            ),
        ),
        'wordcharacters' => array(
            'exclude' => 1,
            'label'   => $ll . 'hyphenationpatterns_wordcharacters_label',
            'config' => array(
                'type'    => 'input',
                'default' => '',
            ),
        ),
        'hyphen' => array(
            'exclude' => 1,
            'label'   => $ll . 'hyphenationpatterns_hyphen_label',
            'config' => array(
                'type'    => 'input',
                'default' => '&shy;',
                'eval'    => 'required',
            ),
        ),
        'leftmin' => array(
            'exclude' => 1,
            'label'   => $ll . 'hyphenationpatterns_leftmin_label',
            'config'  => array(
                'type'    => 'input',
                'default' => '2',
                'eval'    => 'required,trim,num,int'
            ),
        ),
        'rightmin' => array(
            'exclude' => 1,
            'label'   => $ll . 'hyphenationpatterns_rightmin_label',
            'config'  => array(
                'type'    => 'input',
                'default' => '2',
                'eval'    => 'required,trim,num,int'
            ),
        ),
        'patternfile' => array(
            'label'   => $ll . 'hyphenationpatterns_patternfile_label',
            'exclude' => 1,
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'patternfile',
                    array(
                        'maxitems' => 1,
                        'minitems' => 1,
                        'appearance' => array(
                            'enabledControls' => array(
                                'dragdrop' => FALSE,
                                'localize' => FALSE,
                            ),
                        ),
                    )
                ),
        ),
        'patternfileformat' => array(
            'label'   => $ll . 'hyphenationpatterns_patternfileformat_label',
            'exclude' => 1,
            'config'  => array(
                'type' => 'select',
                'items' => array(
                    array($ll . 'hyphenationpatterns_patternfileformat_hyphenatorjs_itemtext', 'hyphenatorjs'),
                )
            ),
        ),
    ),
    'types' => array(
        0 => array(
            'showitem' => 'hidden,title,system_language,wordcharacters,hyphen,leftmin,rightmin,patternfile,patternfileformat',
        ),
    ),
);

?>
