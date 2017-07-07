<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@2043m crmv@56233 */

if (!function_exists('getScannerNameFromAction')) {
	function getScannerNameFromAction($actionid) {
		static $_cache = array();
		if (!empty($_cache[$actionid])) return $_cache[$actionid];

		global $adb, $table_prefix;
		$result = $adb->pquery("SELECT {$table_prefix}_mailscanner.scannername
								FROM {$table_prefix}_mailscanner_actions
								INNER JOIN {$table_prefix}_mailscanner ON {$table_prefix}_mailscanner_actions.scannerid = {$table_prefix}_mailscanner.scannerid
								WHERE actionid = ?",array($actionid));
		if ($result && $adb->num_rows($result) > 0) {
			$_cache[$actionid] = $scannername = $adb->query_result($result,0,'scannername');
		}
		return $scannername;
	}
}

global $sdk_mode;
switch($sdk_mode) {
	case 'detail':
		$label_fld[] = getTranslatedString($fieldlabel,$module);
		$label_fld[] = $col_fields[$fieldname];
		$label_fld['options'] = getScannerNameFromAction($col_fields[$fieldname]);
		break;
	case 'edit':
		$editview_label[] = getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $value;
		break;
	case 'relatedlist':
	case 'list':
		global $current_user;
		if (!empty($sdk_value)) {
			$value = getScannerNameFromAction($sdk_value);
		}
		break;
}
?>