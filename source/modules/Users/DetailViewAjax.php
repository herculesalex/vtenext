<?php
/*+********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*********************************************************************************/
// crmv@67410

global $currentModule, $current_user;
$modObj = CRMEntity::getInstance($currentModule);

$ajaxaction = $_REQUEST["ajxaction"];
if($ajaxaction == "DETAILVIEW")
{
	$crmid = $_REQUEST["recordid"];
	$tablename = $_REQUEST["tableName"];
	$fieldname = $_REQUEST["fldName"];
	
	//crmv@74565
	if(strtolower($current_user->is_admin) == 'off'  && $current_user->id != $crmid){
		$log->fatal("SECURITY:Non-Admin ". $current_user->id . " attempted to change settings for user:". $crmid);
		header("Location: index.php?module=Users&action=Logout");
		exit;
	}
	if(strtolower($current_user->is_admin) == 'off'  && $fieldname == 'is_admin'){
		$log->fatal("SECURITY:Non-Admin ". $current_user->id . " attempted to change is_admin settings for user: ". $crmid);
		header("Location: index.php?module=Users&action=Logout");
		exit;
	}
	//crmv@74565e
	
	$fieldvalue = utf8RawUrlDecode($_REQUEST["fieldValue"]); 
	if($crmid != "")
	{
	    //crmv@69568
        $modObj->retrieve_entity_info($crmid,$currentModule);

        // crmv@42024 - translate separators
        if ($fieldname == 'decimal_separator' || $fieldname == 'thousands_separator')
            $fieldvalue = $modObj->convertToSeparatorValue($fieldvalue);
        // crmv@42024e

        $modObj->column_fields[$fieldname] = $fieldvalue;

        if($fieldname == 'internal_mailer') {
            if(isset($_SESSION['internal_mailer']) && $_SESSION['internal_mailer'] != $modObj->column_fields['internal_mailer'])
                $_SESSION['internal_mailer'] = $modObj->column_fields['internal_mailer'];
        }

        $modObj->id = $crmid;
        $modObj->mode = "edit";
        $modObj->save($currentModule);
        if($modObj->id != "") {
            echo ":#:SUCCESS";
        } else {
            echo ":#:FAILURE";
        }
        //crmv@69568e
	} else {
		echo ":#:FAILURE";
	}
} elseif($ajaxaction == "LOADRELATEDLIST" || $ajaxaction == "DISABLEMODULE"){
	require_once 'include/ListView/RelatedListViewContents.php';
}
?>