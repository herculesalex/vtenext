<?php
//crmv@24240+23984
include_once('include/utils/utils.php');
$action = vtlib_purify($_REQUEST['action_ajax']);
$module = vtlib_purify($_REQUEST['formodule']);
$record = vtlib_purify($_REQUEST['record']);
$fieldvalues = $_REQUEST['fieldvalues'];
$ret_arr = Array('success'=>false);
if ($action == 'get_merge_fields'){
	$fieldvalues = get_merge_user_fields($module,true);
	if (!empty($fieldvalues)){
		$ret_arr['fieldvalues'] = $fieldvalues;
		$ret_arr['success'] = true;
	}
	//crmv@26280
	$merge_user_fields = Zend_Json::decode($_SESSION['merge_user_fields']);
	$merge_user_fields[$module] = $fieldvalues;
	$_SESSION['merge_user_fields'] = Zend_Json::encode($merge_user_fields);
	//crmv@26280e
}
elseif ($action == 'control_duplicate'){
	$data = check_duplicate($module,$fieldvalues,$record);
	if (!empty($data)){
		$ret_arr['data'] = $data;
		$ret_arr['success'] = true;
	}	
}
echo Zend_Json::encode($ret_arr);
exit;
//crmv@24240+23984e
?>