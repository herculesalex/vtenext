<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
*
 ********************************************************************************/

global $result,$client;

$smarty = new VTECRM_Smarty();
$smarty->assign('TITLE',getTranslatedString("LBL_SERVICE"));

$customerid = $_SESSION['customer_id'];
$username = $_SESSION['customer_name'];
$sessionid = $_SESSION['customer_sessionid'];

$onlymine=$_REQUEST['onlymine'];
if($onlymine == 'true') {
    $mine_selected = 'selected';
    $all_selected = '';
} else {
    $mine_selected = '';
    $all_selected = 'selected';
}

if ($customerid != '')
{
	$module = $block;	
	$params = Array('id'=>$customerid,'module'=>$module,'sessionid'=>$sessionid,'onlymine'=>$onlymine);
	$result = $client->call('get_service_list_values', $params, $Server_Path, $Server_Path);

	$allow_all = $client->call('show_all',array('module'=>$module),$Server_Path, $Server_Path);
	
    if($allow_all == 'true'){
    	$smarty->assign('ALLOW_ALL',$allow_all);
    	$smarty->assign('MINE_SELECTED',$mine_selected);
    	$smarty->assign('ALL_SELECTED',$all_selected);
	}

	$smarty->assign('MODULE',$block);
	
	$smarty->assign('FIELDLISTVIEW',getblock_fieldlistview_product($result,$block));
}
$smarty->display('List.tpl');
?>