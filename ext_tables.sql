#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_mediaoembed_aspect_ratio VARCHAR(255) DEFAULT '' NOT NULL,
	tx_mediaoembed_maxwidth INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	tx_mediaoembed_maxheight INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	tx_mediaoembed_url VARCHAR(1024) DEFAULT '' NOT NULL,
	tx_mediaoembed_play_related TINYINT(1) DEFAULT '1' NOT NULL
);
