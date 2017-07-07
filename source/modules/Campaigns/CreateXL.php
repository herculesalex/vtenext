<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@101503 */

global $php_max_execution_time;
set_time_limit($php_max_execution_time);
$memory_limit= ini_get('memory_limit');
ini_set('memory_limit','2048M');

// crmv@30385 - cambio classe php per scrivere i xls - tutto il file
require_once('include/PHPExcel/PHPExcel.php');
require_once("modules/Reports/ReportRun.php");
require_once("modules/Reports/Reports.php");

global $tmp_dir, $root_directory, $mod_strings, $app_strings; // crmv@29686

$fname = tempnam($root_directory.$tmp_dir, "merge2.xls");

$currentmodule = vtlib_purify($_REQUEST['currmodule']);
$RECORD = vtlib_purify($_REQUEST['record']);
$title = vtlib_purify($_REQUEST['title']);

$focus = CRMEntity::getInstance($currentmodule);
if(isset($_REQUEST['record']) && $_REQUEST['record']!='') {
	$focus->retrieve_entity_info($RECORD,$currentmodule);
	$focus->id = $RECORD;
}

if($title == 'Message Queue'){
	$temp_xls_list = $focus->get_statistics_message_queue($focus->id, 26, 0, false, false, false, true);
}elseif($title == 'Sent Messages'){
	$temp_xls_list = $focus->get_statistics_sent_messages($focus->id, 26, 0, false, false, false, true);
}elseif($title == 'Viewed Messages'){
	$temp_xls_list = $focus->get_statistics_viewed_messages($focus->id, 26, 0, false, false, false, true);
}elseif($title == 'Tracked Link'){
	$temp_xls_list = $focus->get_statistics_tracked_link($focus->id, 26, 0, false, false, false, true);
}elseif($title == 'Unsubscriptions'){
	$temp_xls_list = $focus->get_statistics_unsubscriptions($focus->id, 26, 0, false, false, false, true);
}elseif($title == 'Bounced Messages'){
	$temp_xls_list = $focus->get_statistics_bounced_messages($focus->id, 26, 0, false, false, false, true);
}elseif($title == 'Suppression list'){
	$temp_xls_list = $focus->get_statistics_suppression_list($focus->id, 26, 0, false, false, false, true);
}elseif($title == 'Failed Messages'){
	$temp_xls_list = $focus->get_statistics_failed_messages($focus->id, 26, 0, false, false, false, true);
}
$arr_val = $temp_xls_list['entries'];

//try caching method from best to worse
$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
$cacheEnabled = PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
if ($cacheEnabled !== true){
	$cacheSettings = array( 'memoryCacheSize' => '8GB');
	$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
	$cacheEnabled = PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);		
}

$objPHPExcel = new PHPExcel();
$objPHPExcel->removeSheetByIndex(0); // remove default sheet

$objPHPExcel->getProperties()
	->setCreator("VTE CRM")
	->setLastModifiedBy("VTE CRM")
	->setTitle($title); // TODO: report title

$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

$xlsStyle1 = new PHPExcel_Style();
$xlsStyle2 = new PHPExcel_Style();

$xlsStyle1->applyFromArray(
	array('font' => array(
		'name' => 'Arial',
		'bold' => true,
		'size' => 12,
		'color' => array( 'rgb' => '0000FF' )
	),
));

$xlsStyle2->applyFromArray(
	array('font' => array(
		'name' => 'Arial',
		'bold' => true,
		'size' => 11,
	),
));


if (is_array($arr_val) && is_array($arr_val[0])) {
	$count = 0;
	$sheet1 = new PHPExcel_Worksheet($objPHPExcel, $app_strings['Report']);
	$objPHPExcel->addSheet($sheet1);
	// crmv@29686e

	$sheet1->setSharedStyle($xlsStyle1, 'A1:'.PHPExcel_Cell::stringFromColumnIndex(count($arr_val[0])).'1');
	foreach($arr_val[0] as $key=>$value) {
		$sheet1->setCellValueByColumnAndRow($count, 1, $key);
		$count = $count + 1;
	}


	$rowcount=2;
	foreach($arr_val as $key=>$array_value)
	{
		$dcount = 0;
		foreach($array_value as $hdr=>$value)
		{
			$value = decode_html($value);
			if (strpos($value,'=') === 0) $value = "'".$value;	//crmv@52501

			//crmv@29016
			//check for strings that looks like numbers (starting with 0)
			if (is_numeric($value) && substr(strval($value), 0, 1) == '0' && !preg_match('/[,.]/', $value)) { // crmv@30385
				$sheet1->setCellValueExplicitByColumnAndRow($dcount, $rowcount, $value, PHPExcel_Cell_DataType::TYPE_STRING);
			// crmv@38798 - currency fields
			} elseif (preg_match('/^([€$]) (-?[0-9.,]+)$/u', $value, $matches)) {
				$symbol = $matches[1];
				$value = $matches[2];
				$sheet1->setCellValueExplicitByColumnAndRow($dcount, $rowcount, $value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
				if ($symbol == '$') {
					$numberFormat = PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;
				} else {
					$numberFormat = PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE;
				}
				$sheet1->getStyleBycolumnAndRow($dcount, $rowcount)->getNumberFormat()->setFormatCode($numberFormat);
			// crmv@38798e
			} else {
				$sheet1->setCellValueByColumnAndRow($dcount, $rowcount, $value);
			}
			//crmv@29016e
			$dcount = $dcount + 1;
		}
		$rowcount++;
	}
}




$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); // replace with Excel2007 and change extension to xlsx for the new format
$objWriter->save($fname);


if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
{
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
}
header("Content-Type: application/vnd.ms-excel");
header("Content-Length: ".@filesize($fname));
header('Content-disposition: attachment; filename="Reports.xls"');
$fh=fopen($fname, "rb");
fpassthru($fh);
ini_set('memory_limit',$memory_limit);
//unlink($fname);