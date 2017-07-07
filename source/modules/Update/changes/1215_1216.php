<?php

global $enterprise_current_version,$enterprise_mode;

SDK::setLanguageEntries('APP_STRINGS', 'LBL_BROWSER_TITLE', array(
	'it_it'=>"$enterprise_mode $enterprise_current_version",
	'en_us'=>"$enterprise_mode $enterprise_current_version",
	'de_de'=>"$enterprise_mode $enterprise_current_version",
	'nl_nl'=>"$enterprise_mode $enterprise_current_version",
	'pt_br'=>"$enterprise_mode $enterprise_current_version")
);

// enable app new interface
require_once('modules/Touch/Touch.php');
$touch = Touch::getInstance();
$touch->setProperty('app_theme', 'vte16');