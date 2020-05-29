#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_mediaoembed_maxwidth INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	tx_mediaoembed_maxheight INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	tx_mediaoembed_url VARCHAR(255) DEFAULT '' NOT NULL
);
