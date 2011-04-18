#


#
# Table structure for table 'tx_mediaoembed_provider'
#
CREATE TABLE tx_mediaoembed_provider (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  name varchar(50) DEFAULT '' NOT NULL,
  hidden tinyint(3) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(3) unsigned DEFAULT '0' NOT NULL,
  description text,
  endpoint varchar(255) DEFAULT '' NOT NULL,
  regular_expressions int(11) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (uid),
);


#
# Table structure for table 'tx_mediaoembed_provider_regex'
#
CREATE TABLE tx_mediaoembed_provider_regex (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  regex varchar(255) DEFAULT '' NOT NULL,
  hidden tinyint(3) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(3) unsigned DEFAULT '0' NOT NULL,
  provider int(11) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (uid),
  KEY parent (pid,provider),
  KEY regex (regex),
);
