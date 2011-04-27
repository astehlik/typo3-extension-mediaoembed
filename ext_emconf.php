<?php

########################################################################
# Extension Manager/Repository config file for ext "mediaoembed".
#
# Auto generated 27-04-2011 19:19
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
	'version' => '0.0.1',
	'_md5_values_when_last_written' => 'a:34:{s:16:"ext_autoload.php";s:4:"c2dc";s:17:"ext_localconf.php";s:4:"5a3d";s:14:"ext_tables.php";s:4:"4b56";s:14:"ext_tables.sql";s:4:"e348";s:33:"Classes/Content/Configuration.php";s:4:"f020";s:26:"Classes/Content/Oembed.php";s:4:"2c8b";s:32:"Classes/Content/RegisterData.php";s:4:"38af";s:43:"Classes/Exception/HttpNotFoundException.php";s:4:"5a07";s:49:"Classes/Exception/HttpNotImplementedException.php";s:4:"6b44";s:47:"Classes/Exception/HttpUnauthorizedException.php";s:4:"e20c";s:50:"Classes/Exception/InvalidResourceTypeException.php";s:4:"9b73";s:46:"Classes/Exception/InvalidResponseException.php";s:4:"d125";s:41:"Classes/Exception/InvalidUrlException.php";s:4:"68db";s:49:"Classes/Exception/NoMatchingProviderException.php";s:4:"b7cc";s:37:"Classes/Exception/OEmbedException.php";s:4:"e8d5";s:31:"Classes/Hooks/CmsMediaitems.php";s:4:"5902";s:50:"Classes/Hooks/TslibContentGetDataRegisterArray.php";s:4:"ff7e";s:31:"Classes/Request/HttpRequest.php";s:4:"9d6e";s:36:"Classes/Request/ProviderResolver.php";s:4:"2919";s:34:"Classes/Request/RequestBuilder.php";s:4:"ca79";s:36:"Classes/Response/GenericResponse.php";s:4:"6065";s:33:"Classes/Response/LinkResponse.php";s:4:"9490";s:34:"Classes/Response/PhotoResponse.php";s:4:"b4b2";s:36:"Classes/Response/ResponseBuilder.php";s:4:"3386";s:33:"Classes/Response/RichResponse.php";s:4:"d25e";s:34:"Classes/Response/VideoResponse.php";s:4:"c3ae";s:39:"Classes/Tasks/ImportFromEmbedlyTask.php";s:4:"979e";s:40:"Classes/Tasks/ImportFromOhhembedTask.php";s:4:"5123";s:30:"Classes/Utility/Validation.php";s:4:"a25b";s:43:"Classes/Ux/class.ux_tslib_content_media.php";s:4:"bb3d";s:21:"Configuration/TCA.php";s:4:"1982";s:38:"Configuration/TypoScript/constants.txt";s:4:"061b";s:34:"Configuration/TypoScript/setup.txt";s:4:"89ce";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"f312";}',
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