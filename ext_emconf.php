<?php

########################################################################
# Extension Manager/Repository config file for ext "mediaoembed".
#
# Auto generated 07-05-2011 14:18
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'oEmbed media render type',
	'description' => 'Adds an oEmbed render type to the media content element.',
	'category' => 'fe',
	'shy' => 0,
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => 1,
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => 'S',
	'author' => 'Alexander Stehlik',
	'author_email' => 'alexander.stehlik.deleteme@googlemail.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '0.0.0',
	'_md5_values_when_last_written' => 'a:39:{s:16:"ext_autoload.php";s:4:"1796";s:17:"ext_localconf.php";s:4:"77d6";s:14:"ext_tables.php";s:4:"4b56";s:14:"ext_tables.sql";s:4:"af2f";s:25:"ext_tables_static+adt.sql";s:4:"08a9";s:33:"Classes/Content/Configuration.php";s:4:"220d";s:26:"Classes/Content/Oembed.php";s:4:"a945";s:32:"Classes/Content/RegisterData.php";s:4:"4275";s:43:"Classes/Exception/HttpNotFoundException.php";s:4:"7188";s:49:"Classes/Exception/HttpNotImplementedException.php";s:4:"6ea4";s:47:"Classes/Exception/HttpUnauthorizedException.php";s:4:"84eb";s:50:"Classes/Exception/InvalidResourceTypeException.php";s:4:"219c";s:46:"Classes/Exception/InvalidResponseException.php";s:4:"a8c2";s:41:"Classes/Exception/InvalidUrlException.php";s:4:"8c73";s:49:"Classes/Exception/NoMatchingProviderException.php";s:4:"ba16";s:49:"Classes/Exception/NoProviderEndpointException.php";s:4:"c424";s:37:"Classes/Exception/OEmbedException.php";s:4:"a30d";s:38:"Classes/Exception/RequestException.php";s:4:"950e";s:31:"Classes/Hooks/CmsMediaitems.php";s:4:"a61b";s:50:"Classes/Hooks/TslibContentGetDataRegisterArray.php";s:4:"e93b";s:31:"Classes/Request/HttpRequest.php";s:4:"7d45";s:28:"Classes/Request/Provider.php";s:4:"78ed";s:36:"Classes/Request/ProviderResolver.php";s:4:"9d33";s:34:"Classes/Request/RequestBuilder.php";s:4:"5475";s:36:"Classes/Response/GenericResponse.php";s:4:"2fac";s:33:"Classes/Response/LinkResponse.php";s:4:"5a30";s:34:"Classes/Response/PhotoResponse.php";s:4:"a9fe";s:36:"Classes/Response/ResponseBuilder.php";s:4:"d739";s:33:"Classes/Response/RichResponse.php";s:4:"d25e";s:34:"Classes/Response/VideoResponse.php";s:4:"c3ae";s:39:"Classes/Tasks/ImportFromEmbedlyTask.php";s:4:"995f";s:40:"Classes/Tasks/ImportFromOhhembedTask.php";s:4:"82ca";s:30:"Classes/Utility/Validation.php";s:4:"9fc6";s:43:"Classes/Ux/class.ux_tslib_content_media.php";s:4:"bb3d";s:21:"Configuration/TCA.php";s:4:"2052";s:38:"Configuration/TypoScript/constants.txt";s:4:"061b";s:34:"Configuration/TypoScript/setup.txt";s:4:"adae";s:40:"Resources/Private/Language/locallang.xml";s:4:"9056";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"f959";}',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
);

?>