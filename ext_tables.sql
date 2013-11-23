#
# Table structure for table 'tx_nkhyphenation_domain_model_hyphenationpatterns'
#
CREATE TABLE tx_nkhyphenation_domain_model_hyphenationpatterns (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
	system_language int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

    title tinytext,
    wordcharacters tinytext,
    hyphen varchar(255) DEFAULT '&shy;' NOT NULL,
    leftmin int(11) DEFAULT '2' NOT NULL,
    rightmin int(11) DEFAULT '2' NOT NULL,
    patternfile varchar(255) DEFAULT '' NOT NULL,
    patternfileformat varchar(255) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);