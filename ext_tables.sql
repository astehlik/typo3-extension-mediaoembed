#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_mediaoembed_maxwidth int(11) unsigned DEFAULT '0' NOT NULL,
	tx_mediaoembed_maxheight int(11) unsigned DEFAULT '0' NOT NULL,
	tx_mediaoembed_url varchar(255) DEFAULT '' NOT NULL
);

#
# Table structure for table 'tx_mediaoembed_provider'
#
CREATE TABLE tx_mediaoembed_provider (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(3) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(3) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	name varchar(50) DEFAULT '' NOT NULL,
	is_generic tinyint(3) unsigned DEFAULT '0' NOT NULL,
	description text,
	endpoint varchar(255) DEFAULT '' NOT NULL,
	use_generic_providers text,
	url_schemes text,
	embedly_shortname varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (uid)
);
