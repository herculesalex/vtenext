<?php
/* crmv@62394 */
require_once('Smarty_setup.php');
require_once('modules/SDK/src/CalendarTracking/CalendarTrackingUtils.php');
global $theme, $adb, $table_prefix, $currentModule, $current_user;
global $app_strings, $mod_strings;

$record = intval($_REQUEST['record']);
$currentModule = getSalesEntityType($record);

$smarty = new vtigerCRM_Smarty;
$smarty->assign('THEME',$theme);
$smarty->assign('APP',$app_strings);
$smarty->assign('MOD',$mod_strings);
$smarty->assign('ID',$record);
$smarty->assign('MODULE',$currentModule);
$smarty->assign('CURRENT_MODULE',$currentModule);

if (CalendarTracking::isEnabledForModule($currentModule)) {
	$smarty->assign('SHOW_DETAIL_TRACKER', true);
	$smarty->assign('TRACKER_ONLY_BUTTONS', true);
	$smarty->assign('TRACKER_DATA', CalendarTracking::getTrackerData($currentModule, $record));
}

$smarty->display('modules/SDK/src/CalendarTracking/TrackingSmallButtons.tpl');
