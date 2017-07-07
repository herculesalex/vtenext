<?php
/* crmv@104180 crmv@106857 crmv@112297 */
require_once('modules/SDK/src/220/220Utils.php');
require_once('Smarty_setup.php');

global $mod_strings, $app_strings, $theme, $current_module;

$recordid = intval($_REQUEST['recordid']);
$processid = intval($_REQUEST['processid']);
$runid = intval($_REQUEST['running_process']);
$rowno = intval($_REQUEST['rowno']);
$fieldname = $_REQUEST['fieldname'];
$duplicate_rowno = intval($_REQUEST['duplicate_rowno']);

$values = array();
if (!empty($_REQUEST['duplicate_form'])) {
	$duplicate_form = Zend_Json::decode($_REQUEST['duplicate_form']);
	foreach($duplicate_form as $k => $v) {
		$key = str_replace($fieldname.'_','',substr($k,0,strpos($k,"_{$duplicate_rowno}")));
		$values[$key] = $v;
	}
	unset($values[$fieldname.'_row']);
	unset($values[$fieldname.'_rowid']);
	unset($values[$fieldname.'_seq']);
}

$TFUtils = new TableFieldUtils();

$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", "themes/$theme/images/");
$smarty->assign("SHOW_ACTIONS", true);
$smarty->assign("CANDELETEROWS", true);

if (isset($_REQUEST['processid'])) {
	$columns = $TFUtils->getColumnsFromProcess($recordid, $processid, $runid, $fieldname);
} else {
	require_once('include/utils/ModLightUtils.php');
	$MLUtils = ModLightUtils::getInstance();
	$columns = $MLUtils->getColumns($current_module,$fieldname);
}
$vars = $TFUtils->generateRowVars('', $fieldname, $rowno, $columns, $values);
$typeofdata = array();
if ($vars) {
	foreach ($vars as $vname => $value) {
		$smarty->assign($vname, $value);
		if ($vname == 'COLUMNS' && !empty($value)) {
			foreach($value as $info) {
				$typeofdata[$info['fieldname'].'_'.$rowno] = $info['typeofdata'];
			}
		}
	}
}
$single_line = true;
foreach($columns as $column) {
	if ($column['newline'] == 1) {
		$single_line = false;
		break;
	}
}
$smarty->assign('SINGLE_LINE', $single_line);
$html = $smarty->fetch('modules/SDK/src/220/Row.tpl');
echo Zend_Json::encode(array('html'=>$html,'typeofdata'=>$typeofdata));
exit;