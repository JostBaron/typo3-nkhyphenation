<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "nkhyphenation".
 *
 * Auto generated 13-07-2013 00:46
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Hyphenation',
	'description' => 'Adds soft hyphenation capabilities to TYPO3. Provides a ViewHelper, a stdWrap property and some static templates to enable hyphenation.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '0.1.0',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'alpha',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Jost Baron',
	'author_email' => 'j.baron@netzkoenig.de',
	'author_company' => 'Netzkönig GbR',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'dependencies' => array(
			'typo3' => '4.5.0-6.1.99',
			'php' => '5.3.0-0.0.0',
			'fluid' => '1.3.0-0.0.0',
			'extbase' => '1.3.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'xliff' => '1.0.1',
		),
		'depends' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:36:{s:12:"ext_icon.gif";s:4:"e922";s:14:"ext_tables.php";s:4:"d58b";s:14:"ext_tables.sql";s:4:"85a1";s:44:"Classes/Domain/Model/HyphenationPatterns.php";s:4:"2279";s:38:"Classes/Service/HyphenationService.php";s:4:"9cc6";s:41:"Configuration/Tca/hyphenationpatterns.php";s:4:"163a";s:43:"Resources/Private/Language/locallang_db.xlf";s:4:"3efd";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"ca96";s:64:"Resources/Private/Language/locallang_hyphenationpatterns_csh.xlf";s:4:"25c3";s:64:"Resources/Private/Language/locallang_hyphenationpatterns_csh.xml";s:4:"05ac";s:33:"Resources/Private/patterns/bn.php";s:4:"32f7";s:33:"Resources/Private/patterns/de.php";s:4:"9fea";s:33:"Resources/Private/patterns/en.php";s:4:"81b5";s:33:"Resources/Private/patterns/es.php";s:4:"6636";s:33:"Resources/Private/patterns/fi.php";s:4:"c910";s:33:"Resources/Private/patterns/fr.php";s:4:"027f";s:33:"Resources/Private/patterns/gu.php";s:4:"96d2";s:33:"Resources/Private/patterns/hi.php";s:4:"cfba";s:33:"Resources/Private/patterns/it.php";s:4:"56e7";s:33:"Resources/Private/patterns/ka.php";s:4:"f609";s:33:"Resources/Private/patterns/ml.php";s:4:"9702";s:33:"Resources/Private/patterns/nl.php";s:4:"f055";s:33:"Resources/Private/patterns/or.php";s:4:"c016";s:33:"Resources/Private/patterns/pa.php";s:4:"0592";s:33:"Resources/Private/patterns/pl.php";s:4:"fa1f";s:33:"Resources/Private/patterns/ru.php";s:4:"2285";s:33:"Resources/Private/patterns/sv.php";s:4:"cab7";s:33:"Resources/Private/patterns/ta.php";s:4:"3446";s:33:"Resources/Private/patterns/te.php";s:4:"bac9";s:51:"Tests/Unit/Domain/Model/HyphenationPatternsTest.php";s:4:"b81f";s:45:"Tests/Unit/Service/HyphenationServiceTest.php";s:4:"9d9e";s:28:"nbproject/project.properties";s:4:"7a11";s:21:"nbproject/project.xml";s:4:"6c0d";s:35:"nbproject/private/config.properties";s:4:"d41d";s:36:"nbproject/private/private.properties";s:4:"aab6";s:29:"nbproject/private/private.xml";s:4:"0b1a";}',
);

?>