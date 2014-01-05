<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "nkhyphenation".
 *
 * Auto generated 04-01-2014 14:34
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Server side hyphenation for TYPO3',
	'description' => 'Adds soft hyphenation capabilities to TYPO3. Provides a ViewHelper, a stdWrap property and some static templates to enable hyphenation.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.0.2',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 1,
	'createDirs' => 'uploads/tx_nkhyphenation/',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Jost Baron',
	'author_email' => 'j.baron@netzkoenig.de',
	'author_company' => 'Netzkönig GbR',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => 
	array (
		'conflicts' => 
		array (
		),
		'suggests' => 
		array (
		),
		'depends' => 
		array (
			'typo3' => '6.0.0-6.2.99',
			'php' => '5.3.7-5.5.99',
			'fluid' => '6.0.0-6.2.99',
			'extbase' => '6.0.0-6.2.99',
		),
	),
	'_md5_values_when_last_written' => 'a:43:{s:16:"ext_autoload.php";s:4:"ad3f";s:12:"ext_icon.gif";s:4:"e922";s:17:"ext_localconf.php";s:4:"296b";s:14:"ext_tables.php";s:4:"9f44";s:14:"ext_tables.sql";s:4:"8c7a";s:44:"Classes/Domain/Model/HyphenationPatterns.php";s:4:"a85f";s:59:"Classes/Domain/Repository/HyphenationPatternsRepository.php";s:4:"7e8e";s:27:"Classes/Hooks/BuildTrie.php";s:4:"4d1d";s:38:"Classes/Service/HyphenationService.php";s:4:"9cc6";s:36:"Classes/Utility/BuildTrieUtility.php";s:4:"1c12";s:41:"Configuration/Tca/hyphenationpatterns.php";s:4:"ec9c";s:40:"Resources/Private/Language/locallang.xlf";s:4:"1a15";s:40:"Resources/Private/Language/locallang.xml";s:4:"231f";s:43:"Resources/Private/Language/locallang_db.xlf";s:4:"5c75";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"c278";s:64:"Resources/Private/Language/locallang_hyphenationpatterns_csh.xlf";s:4:"25c3";s:64:"Resources/Private/Language/locallang_hyphenationpatterns_csh.xml";s:4:"05ac";s:33:"Resources/Private/patterns/bn.php";s:4:"32f7";s:33:"Resources/Private/patterns/de.php";s:4:"9fea";s:33:"Resources/Private/patterns/en.php";s:4:"81b5";s:33:"Resources/Private/patterns/es.php";s:4:"6636";s:33:"Resources/Private/patterns/fi.php";s:4:"c910";s:33:"Resources/Private/patterns/fr.php";s:4:"027f";s:33:"Resources/Private/patterns/gu.php";s:4:"96d2";s:33:"Resources/Private/patterns/hi.php";s:4:"cfba";s:33:"Resources/Private/patterns/it.php";s:4:"56e7";s:33:"Resources/Private/patterns/ka.php";s:4:"f609";s:33:"Resources/Private/patterns/ml.php";s:4:"9702";s:33:"Resources/Private/patterns/nl.php";s:4:"f055";s:33:"Resources/Private/patterns/or.php";s:4:"c016";s:33:"Resources/Private/patterns/pa.php";s:4:"0592";s:33:"Resources/Private/patterns/pl.php";s:4:"fa1f";s:33:"Resources/Private/patterns/ru.php";s:4:"2285";s:33:"Resources/Private/patterns/sv.php";s:4:"cab7";s:33:"Resources/Private/patterns/ta.php";s:4:"3446";s:33:"Resources/Private/patterns/te.php";s:4:"bac9";s:51:"Tests/Unit/Domain/Model/HyphenationPatternsTest.php";s:4:"17c9";s:45:"Tests/Unit/Service/HyphenationServiceTest.php";s:4:"9d9e";s:28:"nbproject/project.properties";s:4:"7a11";s:21:"nbproject/project.xml";s:4:"6c0d";s:35:"nbproject/private/config.properties";s:4:"d41d";s:36:"nbproject/private/private.properties";s:4:"aab6";s:29:"nbproject/private/private.xml";s:4:"0b1a";}',
);

?>