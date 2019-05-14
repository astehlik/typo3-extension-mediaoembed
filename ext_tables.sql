#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_mediaoembed_maxwidth int(11) unsigned DEFAULT '0' NOT NULL,
	tx_mediaoembed_maxheight int(11) unsigned DEFAULT '0' NOT NULL,
	tx_mediaoembed_url varchar(255) DEFAULT '' NOT NULL
);
