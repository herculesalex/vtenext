<?php
/* crmv@42247 crmv@107331 */

global $default_charset,$adb,$table_prefix,$autocomplete_return_function;

$forfield = htmlspecialchars($_REQUEST['forfield'], ENT_QUOTES, $default_charset);
$list_result_count = $i-1;

$value = $this->getValue($ui_col_array,$list_result,$fieldname,$focus,$module,$entity_id,$list_result_count,"search",$focus->popup_type);

$slashes_temp_val = getEntityName('Contacts', $entity_id);
$slashes_temp_val = $slashes_temp_val[$entity_id];
$slashes_temp_val = htmlspecialchars($slashes_temp_val,ENT_QUOTES,$default_charset);

if(isset($forfield) && $forfield != '' && $focus->popup_type != 'detailview') {
	// value as it is in the DB
	$rawValue = ListViewUtils::decodeFromDb($value, true);
	
	// value ready for js inclusion
	$valueJs = ListViewUtils::encodeForJs($rawValue);
	
	// value with HTML enitities to be used inside HTML attributes
	$valueHtml = ListViewUtils::encodeForHtml($rawValue);
	
	$popupValues = array();
	$result = $adb->query('SELECT * FROM '.$table_prefix.'_contactaddress WHERE contactaddressid = '.$entity_id);
	if ($result && $adb->num_rows($result)>0) {
		// get the row exactly as it is in the DB
		$popupValues = $adb->fetchByAssoc($result, -1, false);
	}
	
	$argsKeys = array(
		'mailingstreet', 'otherstreet', 'mailingcity', 'othercity', 'mailingstate', 'otherstate', 
		'mailingzip', 'otherzip', 'mailingcountry', 'othercountry', 'mailingpobox', 'otherpobox',
	);

	// prepare the list of arguments
	$argList = array();
	foreach ($argsKeys as $key) {
		$argList[] = ListViewUtils::encodeForJs($popupValues[$key], '"');
	}
	$args = '"'.implode('", "', $argList).'"';

	$autocomplete_return_function[$entity_id] = "set_return_contact_address($entity_id, \"$valueJs\", {$args});";
	$autocompleteHtml = ListViewUtils::encodeForHtmlAttr($autocomplete_return_function[$entity_id], "'");
	
	// html values
	$value = '<a href="index.php?module=Contacts&action=DetailView&record='.$entity_id.'&parenttab=Marketing" />';
	$value .= "<a href='javascript:void(0);' onclick='{$autocompleteHtml}closePopup();'>$valueHtml</a>"; //crmv@21048m
}
