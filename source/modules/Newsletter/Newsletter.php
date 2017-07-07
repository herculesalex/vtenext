<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class Newsletter extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name;
	var $table_index= 'newsletterid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array();

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array();

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array();

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Newsletter Name'=> Array('newsletter', 'newslettername'),
		'Date scheduled'=> Array('newsletter', 'date_scheduled'),
		'Time scheduled'=> Array('newsletter', 'time_scheduled'),
		'Assigned To' => Array('crmentity','smownerid'),
		'Scheduled' => Array('newsletter','scheduled'),
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Newsletter Name'=> 'newslettername',
		'Date scheduled'=> 'date_scheduled',
		'Time scheduled'=> 'time_scheduled',
		'Assigned To' => 'assigned_user_id',
		'Scheduled' => 'scheduled',
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'newslettername';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Newsletter Name'=> Array('newsletter', 'newslettername')
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Newsletter Name'=> 'newslettername'
	);

	// For Popup window record selection
	var $popup_fields = Array('newslettername');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'newslettername';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'newslettername';

	// Required Information for enabling Import feature
	var $required_fields = Array('newslettername'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'newslettername';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'newslettername');
	//crmv@10759
	var $search_base_field = 'newslettername';
	//crmv@10759 e

	//Newsletter & Campaigns params - i
	var $email_fields = array();
	var $url_tracklink_file;
	var $url_trackuser_file;
	var $track_userhistory_systeminfo = array(
		'HTTP_USER_AGENT',
		'HTTP_REFERER',
		'REMOTE_ADDR'
	);
	var $max_attempts_permitted = 5;					//numero di tentativi possibili di spedizione di una mail in coda
	var $no_email_processed_by_schedule = 70;			//numero di mail processate per schedulazione
	var $interval_between_email_delivery = 0;			//(seconds) intervallo tra la spedizione delle singole email
	var $interval_between_blocks_email_delivery = 120;	//(seconds) intervallo tra le schedulazioni
	//crmv@36796
	/* change config base of maximum number of emails server can send every day
	 * 5K:
			var $no_email_processed_by_schedule = 60;
			var $interval_between_email_delivery = 2;
			var $interval_between_blocks_email_delivery = 0;
	 * 10K:
			var $no_email_processed_by_schedule = 60;
			var $interval_between_email_delivery = 1;
			var $interval_between_blocks_email_delivery = 0;
	 * 20K:
			var $no_email_processed_by_schedule = 120;
			var $interval_between_email_delivery = 1;
			var $interval_between_blocks_email_delivery = 0;
	 * 100K:
			var $no_email_processed_by_schedule = 350;
			var $interval_between_email_delivery = 0;
			var $interval_between_blocks_email_delivery = 0;
	 */
	//crmv@36796 e
	//crmv@34245
	var $smtp_config = array(
		'enable'=>false, // true o false
		'server'=>'',
		'server_username'=>'',
		'server_password'=>'',
		'smtp_auth'=>'false', //valori: 'true' o 'false' (string)
	);
	//crmv@34245e
	//Newsletter & Campaigns params - e

	// crmv@38592
	var $status_list = array(
		0 => 'Unknown',
		// unsubscription
		1 => 'User unsubscription from email',
		// failed
		2 => 'LBL_OWNER_MISSING',
		3 => 'LBL_RECORD_DELETE',
		4 => 'LBL_RECORD_NOT_FOUND',
		5 => 'LBL_ERROR_MAIL_UNSUBSCRIBED',
		6 => 'LBL_ATTEMPTS_EXHAUSTED',	//crmv@83542
	);
	// crmv@38592e

	function __construct() {
		global $log, $currentModule, $site_URL, $table_prefix;
		parent::__construct(); // crmv@37004
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
		$this->table_name = $table_prefix.'_newsletter';
		$this->customFieldTable = Array($table_prefix.'_newslettercf', 'newsletterid');
		$this->tab_name = Array($table_prefix.'_crmentity', $table_prefix.'_newsletter', $table_prefix.'_newslettercf');
		$this->tab_name_index = Array(
			$table_prefix.'_crmentity' => 'crmid',
			$table_prefix.'_newsletter'   => 'newsletterid',
		    $table_prefix.'_newslettercf' => 'newsletterid'
		);
		$this->column_fields = getColumnFields($currentModule);
		$this->email_fields = array(
			'Accounts'=>array('fieldname'=>'email1','tablename'=>$table_prefix.'_account','columnname'=>'email1'),
			'Contacts'=>array('fieldname'=>'email','tablename'=>$table_prefix.'_contactdetails','columnname'=>'email'),
			'Leads'=>array('fieldname'=>'email','tablename'=>$table_prefix.'_leaddetails','columnname'=>'email')
		);
		$this->url_tracklink_file = $site_URL.'/modules/Newsletter/TrackLink.php';
		$this->url_trackuser_file = $site_URL.'/modules/Newsletter/TrackUser.php';
		$this->url_unsubscription_file = $site_URL.'/modules/Newsletter/Unsubscription.php';
	}

	function save_module($module) {
		//crmv@104558
		if($_REQUEST['isDuplicate']){
			$templateid = vtlib_purify($_REQUEST['templateemailid']);
			$this->duplicateTemplateEmail($templateid);
		}
		//crmv@104558e
	}
	
	//crmv@104558
	function duplicateTemplateEmail($templateid = null) {
		global $adb, $table_prefix;
		
		$newsletterid = $this->id;
		
		if (empty($templateid)) $templateid = $this->column_fields['templateemailid'];
		if (empty($templateid)) return false;
	
		$date_scheduled = $this->column_fields['date_scheduled'].' '.$this->column_fields['time_scheduled'];
	
		$tplres = $adb->pquery("select * from {$table_prefix}_emailtemplates where templateid = ?", array($templateid));
		if ($tplres && $adb->num_rows($tplres) > 0) {
			$templateName = getTranslatedString('LBL_AUTO_TMP_NAME', 'Newsletter')." - ".$this->column_fields['newsletter_no'];
			$description = $adb->query_result($tplres,0,'description');
			$subject = $adb->query_result($tplres,0,'subject');
			$body = $adb->query_result_no_html($tplres,0,'body');
			$use_signature = $adb->query_result($tplres,0,'use_signature');
			$overwrite_message = $adb->query_result($tplres,0,'overwrite_message');
			$templatetype = $adb->query_result($tplres,0,'templatetype');
			
			$templateid = $adb->getUniqueID($table_prefix.'_emailtemplates');
			$sql = "INSERT INTO ".$table_prefix."_emailtemplates (foldername,templatename,subject,description,deleted,templateid,templatetype,use_signature,overwrite_message,body) values (?,?,?,?,?,?,?,?,?,?)";
			$params = array('Public', $templateName, $subject, $description, 0, $templateid, $templatetype, $use_signature, $overwrite_message, $body);
			$adb->pquery($sql, $params);
			$result = $adb->updateClob($table_prefix.'_emailtemplates','body',"templateid=$templateid",$body);
			
			if($result){
				$adb->pquery("UPDATE {$table_prefix}_newsletter SET templateemailid = ? WHERE newsletterid = ?",array($templateid,$newsletterid));
			}
		}
			
	}
	//crmv@104558e

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord) {
		// $srcrecord could be empty
	}


	/**
	 * Create query to export the records.
	 */
	function create_export_query($where,$oCustomView,$viewId)	//crmv@31775
	{
		global $current_user;
		global $table_prefix;
		$thismodule = $_REQUEST['module'];

		include_once("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery($thismodule, "detail_view");

		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, {$table_prefix}_users.user_name AS user_name
					FROM {$table_prefix}_crmentity INNER JOIN $this->table_name ON {$table_prefix}_crmentity.crmid=$this->table_name.$this->table_index";

		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index";
		}

		$query .= " LEFT JOIN ".$table_prefix."_groups ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid";
		$query .= " LEFT JOIN ".$table_prefix."_users ON ".$table_prefix."_crmentity.smownerid = ".$table_prefix."_users.id and ".$table_prefix."_users.status='Active'";

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM ".$table_prefix."_field" .
				" INNER JOIN ".$table_prefix."_fieldmodulerel ON ".$table_prefix."_fieldmodulerel.fieldid = ".$table_prefix."_field.fieldid" .
				" WHERE uitype='10' AND ".$table_prefix."_fieldmodulerel.module=?", array($thismodule));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
		}

		//crmv@31775
		$reportFilter = $oCustomView->getReportFilter($viewId);
		if ($reportFilter) {
			$tableNameTmp = $oCustomView->getReportFilterTableName($reportFilter,$current_user->id);
			$query .= " INNER JOIN $tableNameTmp ON $tableNameTmp.id = {$table_prefix}_crmentity.crmid";
		}
		//crmv@31775e

		$where_auto = " ".$table_prefix."_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		require('user_privileges/requireUserPrivileges.php'); // crmv@39110
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

		// Security Check for Field Access
		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[7] == 3)
		{
			//Added security check to get the permitted records only
			$query = $query." ".getListViewSecurityParameter($thismodule);
		}
		return $query;
	}

	/**
	 * Initialize this instance for importing.
	 */
	function initImport($module) {
		$this->db = PearDatabase::getInstance();
		$this->initImportableFields($module);
	}

	/**
	 * Create list query to be shown at the last step of the import.
	 * Called From: modules/Import/UserLastImport.php
	 */
	function create_import_query($module) {
		global $current_user;
		global$table_prefix;
		$query = "SELECT ".$table_prefix."_crmentity.crmid, case when (".$table_prefix."_users.user_name is not null) then ".$table_prefix."_users.user_name else ".$table_prefix."_groups.groupname end as user_name, $this->table_name.* FROM $this->table_name
			INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = $this->table_name.$this->table_index
			LEFT JOIN ".$table_prefix."_users_last_import ON ".$table_prefix."_users_last_import.bean_id=".$table_prefix."_crmentity.crmid
			LEFT JOIN ".$table_prefix."_users ON ".$table_prefix."_users.id = ".$table_prefix."_crmentity.smownerid
			LEFT JOIN ".$table_prefix."_groups ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid
			WHERE ".$table_prefix."_users_last_import.assigned_user_id='$current_user->id'
			AND ".$table_prefix."_users_last_import.bean_type='$module'
			AND ".$table_prefix."_users_last_import.deleted=0";
		return $query;
	}

	/**
	 * Delete the last imported records.
	 */
	function undo_import($module, $user_id) {
		global $adb;
		global $table_prefix;
		$count = 0;
		$query1 = "select bean_id from ".$table_prefix."_users_last_import where assigned_user_id=? AND bean_type='$module' AND deleted=0";
		$result1 = $adb->pquery($query1, array($user_id)) or die("Error getting last import for undo: ".mysql_error());
		while ( $row1 = $adb->fetchByAssoc($result1))
		{
			$query2 = "update ".$table_prefix."_crmentity set deleted=1 where crmid=?";
			$result2 = $adb->pquery($query2, array($row1['bean_id'])) or die("Error undoing last import: ".mysql_error());
			$count++;
		}
		return $count;
	}

	/**
	 * Transform the value while exporting
	 */
	function transform_export_value($key, $value) {
		return parent::transform_export_value($key, $value);
	}

	/**
	 * Function which will set the assigned user id for import record.
	 */
	function set_import_assigned_user()
	{
		global $current_user, $adb;
		global $table_prefix;
		$record_user = $this->column_fields["assigned_user_id"];

		if($record_user != $current_user->id){
			$sqlresult = $adb->pquery("select id from ".$table_prefix."_users where id = ? union select groupid as id from ".$table_prefix."_groups where groupid = ?", array($record_user, $record_user));
			if($this->db->num_rows($sqlresult)!= 1) {
				$this->column_fields["assigned_user_id"] = $current_user->id;
			} else {
				$row = $adb->fetchByAssoc($sqlresult, -1, false);
				if (isset($row['id']) && $row['id'] != -1) {
					$this->column_fields["assigned_user_id"] = $row['id'];
				} else {
					$this->column_fields["assigned_user_id"] = $current_user->id;
				}
			}
		}
	}

	/**
	 * Function which will give the basic query to find duplicates
	 */
	function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
		global $table_prefix;
		$select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, ".$table_prefix."_users_last_import.deleted,".$table_cols;

		// Select Custom Field Table Columns if present
		if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$from_clause = " FROM $this->table_name";

		$from_clause .= "	INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(isset($this->customFieldTable)) {
			$from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index";
		}
		$from_clause .= " LEFT JOIN ".$table_prefix."_users ON ".$table_prefix."_users.id = ".$table_prefix."_crmentity.smownerid
						LEFT JOIN ".$table_prefix."_groups ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid";

		$where_clause = "	WHERE ".$table_prefix."_crmentity.deleted = 0";
		$where_clause .= $this->getListViewSecurityParameter($module);

		if (isset($select_cols) && trim($select_cols) != '') {
			$sub_query = "SELECT $select_cols FROM  $this->table_name AS t " .
				" INNER JOIN ".$table_prefix."_crmentity AS crm ON crm.crmid = t.".$this->table_index;
			// Consider custom table join as well.
			if(isset($this->customFieldTable)) {
				$sub_query .= " INNER JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";
		} else {
			$sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
		}

		$query = $select_clause . $from_clause .
					" LEFT JOIN ".$table_prefix."_users_last_import ON ".$table_prefix."_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
					" INNER JOIN (" . $sub_query . ") temp ON ".get_on_clause($field_values,$ui_type_arr,$module) .
					$where_clause .
					" ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";

		return $query;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		if($event_type == 'module.postinstall') {

			global $adb;
			global $table_prefix;
			$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0 WHERE name=?', array($modulename));

			$newsletterModule = Vtiger_Module::getInstance($modulename);
			$campaignsModule = Vtiger_Module::getInstance('Campaigns');
			$campaignsModule->setRelatedList($newsletterModule, 'Newsletter', Array('ADD'), 'get_newsletter');

			Vtiger_Link::addLink($newsletterModule->id, 'LISTVIEWBASIC', 'OpenNewsletterWizard', "openNewsletterWizard('\$MODULE\$', '');", '', 1);

			$i=2;
			$adb->query("UPDATE ".$table_prefix."_relatedlists SET sequence = $i WHERE tabid = 26 AND label = 'Newsletter'");
			$res = $adb->query("SELECT * FROM ".$table_prefix."_relatedlists WHERE tabid = 26 AND label NOT IN ('Newsletter','Targets') ORDER BY sequence");
			while($row=$adb->fetchByAssoc($res)) {
				$i++;
				$adb->pquery("UPDATE ".$table_prefix."_relatedlists SET sequence = $i WHERE relation_id = ?",array($row['relation_id']));
			}

			// add related for newsletter emails
			$nlmods = array('Accounts', 'Leads', 'Contacts');
			foreach ($nlmods as $nlmod) {
				$res = $adb->pquery("select * from vte_relatedlists where name = ? and tabid = ?", array('get_newsletter_emails', getTabid($nlmod)));
				if ($res && $adb->num_rows($res) == 0) {
					$otherModule = Vtiger_Module::getInstance($nlmod);
					$otherModule->setRelatedList($newsletterModule, 'Newsletter Emails', Array(), 'get_newsletter_emails');
				}
			}

			$em = new VTEventsManager($adb);
			$em->registerHandler('vtiger.entity.beforesave','modules/Newsletter/NewsletterHandler.php','NewsletterHandler');
			$em->registerHandler('vtiger.entity.aftersave','modules/Newsletter/NewsletterHandler.php','NewsletterHandler');

			require_once('modules/Newsletter/InstallCampaignStatistics.php');
			installCampaignStatistics();

			// crmv@38592
			$schema_tables = array(
				'tbl_s_newsletter_queue'=>
					'<schema version="0.3">
					  <table name="tbl_s_newsletter_queue">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
						<field name="newsletterid" type="R" size="19">
						  <KEY/>
						</field>
						<field name="crmid" type="R" size="19">
						  <KEY/>
						</field>
						<field name="status" type="C" size="255"/>
						<field name="attempts" type="I" size="19"/>
						<field name="date_scheduled" type="T"/>
						<field name="last_attempt" type="T"/>
						<field name="date_sent" type="T"/>
						<field name="first_view" type="T"/>
						<field name="last_view" type="T"/>
						<field name="num_views" type="I" size="19"/>
						<field name="fieldvalues" type="X"/>
						<index name="NewIndex1">
						  <col>newsletterid</col>
						</index>
						<index name="NewIndex2">
						  <col>crmid</col>
						</index>
						<index name="NewIndex3">
						  <col>status</col>
						</index>
						<index name="NewIndex4">
						  <col>attempts</col>
						</index>
					  </table>
					</schema>',
				'tbl_s_newsletter_tl'=>
					'<schema version="0.3">
					  <table name="tbl_s_newsletter_tl">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
						<field name="trackid" type="R" size="19">
						  <KEY/>
						</field>
						<field name="newsletterid" type="I" size="19"/>
						<field name="crmid" type="I" size="19"/>
						<field name="linkurlid" type="I" size="19"/>
						<field name="firstclick" type="T"/>
						<field name="latestclick" type="T"/>
						<field name="clicked" type="I" size="19">
						  <DEFAULT value="0"/>
						</field>
						<index name="midindex">
						  <col>newsletterid</col>
						</index>
						<index name="uidindex">
						  <col>crmid</col>
						</index>
						<index name="tbl_s_nl_linkurlid_idx">
						  <col>linkurlid</col>
						</index>
						<index name="miduidindex">
						  <col>newsletterid</col>
						  <col>crmid</col>
						</index>
					  </table>
					</schema>',
				'tbl_s_newsletter_links'=>
					'<schema version="0.3">
						<table name="tbl_s_newsletter_links">
						<opt platform="mysql">ENGINE=InnoDB</opt>
							<field name="linkid" type="R" size="19">
								<KEY/>
							</field>
							<field name="newsletterid" type="I" size="19"/>
							<field name="url" type="C" size="1000"/>
							<field name="forward" type="C" size="1000"/>
							<index name="tbl_s_nl_links_nlid">
								<col>newsletterid</col>
							</index>
							<index name="tbl_s_nl_links_url">
								<col>url</col>
							</index>
						</table>
				</schema>',
				'tbl_s_newsletter_unsub'=>
					'<schema version="0.3">
					  <table name="tbl_s_newsletter_unsub">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
						<field name="newsletterid" type="R" size="19">
						  <KEY/>
						</field>
						<field name="email" type="C" size="100">
						  <KEY/>
						</field>
						<field name="statusid" type="I" size="11"/>
					  </table>
					</schema>',
				'tbl_s_newsletter_bounce'=>
					'<schema version="0.3">
					  <table name="tbl_s_newsletter_bounce">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
						<field name="id" type="R" size="19">
						  <KEY/>
						</field>
						<field name="date" type="T"/>
						<field name="header" type="X"/>
						<field name="data" type="B"/>
						<field name="status" type="C" size="255"/>
						<field name="comment" type="X"/>
						<index name="dateindex">
						  <col>date</col>
						</index>
					  </table>
					</schema>',
				'tbl_s_newsletter_bounce_rel'=>
					'<schema version="0.3">
					  <table name="tbl_s_newsletter_bounce_rel">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
						<field name="id" type="R" size="19">
						  <KEY/>
						</field>
						<field name="crmid" type="I" size="19"/>
						<field name="newsletterid" type="I" size="19"/>
						<field name="bounce" type="I" size="19"/>
						<field name="time" type="T"/>
						<index name="umbindex">
						  <col>crmid</col>
						  <col>newsletterid</col>
						  <col>bounce</col>
						</index>
						<index name="useridx">
						  <col>crmid</col>
						</index>
						<index name="msgidx">
						  <col>newsletterid</col>
						</index>
						<index name="bounceidx">
						  <col>bounce</col>
						</index>
					  </table>
					</schema>',
				//crmv@25872
				'tbl_s_newsletter_failed'=>
					'<schema version="0.3">
					  <table name="tbl_s_newsletter_failed">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
						<field name="newsletterid" type="R" size="19">
						  <KEY/>
						</field>
						<field name="crmid" type="R" size="19">
						  <KEY/>
						</field>
						<field name="statusid" type="I" size="11"/>
						<index name="NewIndex1">
						  <col>newsletterid</col>
						</index>
						<index name="NewIndex2">
						  <col>crmid</col>
						</index>
						<index name="NewIndex3">
						  <col>statusid</col>
						</index>
					  </table>
					</schema>',
				//crmv@25872e
				'tbl_s_newsletter_status' =>
					'<schema version="0.3">
						<table name="tbl_s_newsletter_status">
							<opt platform="mysql">ENGINE=InnoDB</opt>
							<field name="id" type="R" size="19">
								<KEY/>
							</field>
							<field name="name" type="C" size="200"/>
						</table>
					</schema>',
				'tbl_s_newsletter_tpl'=>
					'<schema version="0.3">
						<table name="tbl_s_newsletter_tpl">
							<opt platform="mysql">ENGINE=InnoDB</opt>
							<field name="tplid" type="R" size="19">
								<KEY/>
							</field>
							<field name="newsletterid" type="R" size="19" />
							<field name="datesent" type="T">
								<DEFAULT value="0000-00-00 00:00:00"/>
							</field>
							<field name="templatename" type="C" size="200"/>
							<field name="subject" type="C" size="200" />
							<field name="description" type="X"/>
							<field name="body" type="X"/>
							<field name="fields" type="X"/>
							<index name="tbl_s_nl_tpl_nlid_idx">
								<col>newsletterid</col>
							</index>
							<index name="tbl_s_nl_tpl_date_idx">
								<col>datesent</col>
							</index>
						</table>
					</schema>',
				'tbl_s_newsletter_g_unsub'=>
					'<schema version="0.3">
						<table name="tbl_s_newsletter_g_unsub">
							<opt platform="mysql">ENGINE=InnoDB</opt>
							<field name="email" type="C" size="100">
								<KEY/>
							</field>
							<field name="unsub_date" type="T">
							  <DEFAULT value="0000-00-00 00:00:00"/>
							</field>
						</table>
					</schema>',
			);
			// crmv@38592e
			foreach($schema_tables as $table_name => $schema_table) {
				if(!Vtiger_Utils::CheckTable($table_name)) {
					$schema_obj = new adoSchema($adb->database);
					$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
				}
			}

			// crmv@38592
			$res = $adb->query('select count(*) as m from tbl_s_newsletter_status');
			if ($res && $adb->query_result($res, 0, 'm') == 0) {
				foreach ($this->status_list as $k => $v) {
					$adb->pquery('insert into tbl_s_newsletter_status (id, name) values (?,?)', array($k, $v));
				}
			}
			// crmv@38592e

			$adb->query("UPDATE ".$table_prefix."_relatedlists SET actions = '' WHERE related_tabid = 26 AND tabid IN (4,6,7)");

			create_tab_data_file();

			$this->setModuleSeqNumber('configure', 'Newsletter', 'NWS-', 1);

			require_once('modules/Newsletter/MigrateRelatedToTarget.php');
			migrateRelatedToTarget();

			// crmv@47611
			if (Vtiger_Utils::CheckTable($table_prefix.'_cronjobs')) {
				require_once('include/utils/CronUtils.php');
				$CU = CronUtils::getInstance();
				
				$cj = new CronJob();
				$cj->name = 'Newsletter';
				$cj->active = 1;
				$cj->singleRun = false;
				$cj->fileName = 'cron/modules/Newsletter/Newsletter.service.php';
				$cj->timeout = 600;			// 10min timeout
				$cj->repeat = 300;			// repeat every 5 min
				$CU->insertCronJob($cj);
				
				// crmv@92075
				$cj = new CronJob();
				$cj->name = 'ProcessBounces';
				$cj->active = 0;
				$cj->singleRun = false;
				$cj->fileName = 'cron/modules/Newsletter/ProcessBounces.service.php';
				$cj->timeout = 5400;
				$cj->repeat = 14400;			// repeat every 4 hours
				$CU->insertCronJob($cj);
				// crmv@92075e

			}
			// crmv@47611e
			
			//crmv@49823
			$result = $adb->pquery("SELECT relation_id FROM {$table_prefix}_relatedlists WHERE related_tabid = ? AND name = ?",array($newsletterModule->id,'get_newsletter_emails'));
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByAssoc($result)) {
					SDK::setTurboliftCount($row['relation_id'], 'get_newsletter_emails_count');
				}
			}
			//crmv@49823e
			
			//crmv@55961
			Vtiger_Link::addLink($newsletterModule->id,'HEADERSCRIPT','ReportGlobalUnsubscribe','modules/SDK/src/modules/Newsletter/ReportGlobalUnsubscribe.js');
			SDK::setReportFolder('NEWSLETTER_G_UNSUBSCRIBE_DIR', '');
			SDK::setReport('NEWSLETTER_G_UNSUBSCRIBE', 'NEWSLETTER_G_UNSUBSCRIBE_DESC', 'NEWSLETTER_G_UNSUBSCRIBE_DIR', 'modules/SDK/src/modules/Newsletter/ReportGlobalUnsubscribe.php', 'GlobalUnsubscribeReportRun', 'FilterUnsubReport');

			$q = "SELECT reportid FROM sdk_reports WHERE runclass = ?";
			$res = $adb->pquery($q,array('GlobalUnsubscribeReportRun'));
			if($res && $adb->num_rows($res) > 0){
				$reportid = $adb->query_result($res,0,'reportid');

				$q1 = "SELECT folderid FROM vte_crmentityfolder WHERE foldername =?";
				$res1 = $adb->pquery($q1,array('NEWSLETTER_G_UNSUBSCRIBE_DIR'));
				if($res1 && $adb->num_rows($res1) > 0){
					$folderid = $adb->query_result($res1,0,'folderid');
					$onclick = "window.location='index.php?module=Reports&action=SaveAndRun&record=".$reportid."&folderid=".$folderid."';";
					SDK::setMenuButton('contestual', 'NEWSLETTER_G_UNSUBSCRIBE', $onclick, 'contact_mail', 'Newsletter');
				}
			}
			//crmv@55961e

		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
			//aggiorno il file cron/modules/Newsletter/Newsletter.service.php che altrimenti non sarebbe aggiornato con il normale update
			$tmp_dir = "packages/vte/mandatory/tmp1";
			mkdir($tmp_dir);
			$unzip = new Vtiger_Unzip('packages/vte/mandatory/Newsletters.zip');
			$unzip->unzipAllEx($tmp_dir);
			if($unzip) $unzip->close();

			$tmp_dir1 = "$tmp_dir/Newsletter";
			mkdir($tmp_dir1);
			$unzip1 = new Vtiger_Unzip('packages/vte/mandatory/tmp1/Newsletter.zip');
			$unzip1->unzipAllEx($tmp_dir1);
			if($unzip1) $unzip1->close();
			copy("$tmp_dir1/cron/Newsletter.service.php",'cron/modules/Newsletter/Newsletter.service.php');
			copy("$tmp_dir1/cron/ProcessBounces.service.php",'cron/modules/Newsletter/ProcessBounces.service.php');

			if ($handle = opendir($tmp_dir)) {
				require_once('modules/SDK/src/Utils.php');
				folderDetete($tmp_dir);
			}
			//end
		}
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	function getTargetList($check_duplicates=true,$remove_unsubscripted=true) {
		global $adb;
		$return = array();
		$return_contacts = array();
		$return_accounts = array();
		$return_leads = array();
		
		$rm = RelationManager::getInstance();
		$targetsIds = $rm->getRelatedIds('Campaigns', $this->column_fields['campaignid'], array('Targets'));
		if (!empty($targetsIds)) {
			foreach($targetsIds as $targetsId) {
				$this->getTargetElements($return,$return_contacts,$return_accounts,$return_leads,$targetsId);
			}
		}
		
		if ($check_duplicates) {	//Controllo duplicati sul campo email. La priorita' e' Contatto, Azienda, Lead
			$emails_array = array();
			/*
			$contacts_ids = array_keys($return_contacts);
			$accounts_ids = array_keys($return_accounts);
			$leds_ids = array_keys($return_leads);
			foreach($return as $crmid => $email) {
				$ids = array_keys($return,$email);
				//echo "<pre>$id: $email: ";print_r($ids);echo '</pre>';
				$weight = array();
				foreach($ids as $id) {
					if (in_array($id,$contacts_ids)) {
						$weight[$id] = 2;
					} elseif (in_array($id,$accounts_ids)) {
						$weight[$id] = 1;
					} elseif (in_array($id,$leds_ids)) {
						$weight[$id] = 0;
					}
				}
				//echo "<pre>";print_r($weight);echo '</pre>';
				$winner = array_search(max($weight),$weight);
				//echo $winner;
				unset($weight[$winner]);
				foreach($weight as $id => $w) {
					unset($return[$id]);
				}
			}
			*/
			foreach($return_leads as $crmid => $email) {
				$emails_array[$email] = $crmid;
			}
			foreach($return_accounts as $crmid => $email) {
				$emails_array[$email] = $crmid;
			}
			foreach($return_contacts as $crmid => $email) {
				$emails_array[$email] = $crmid;
			}
			$return = array();
			foreach($emails_array as $email => $crmid) {
				$return[$crmid] = $email;
			}
		}
		
		//crmv@25085
		$unsubscripted = $this->getUnsubscriptedList();
		if ($remove_unsubscripted && !empty($unsubscripted)) {	//Rimuovo i disiscritti dalla Newsletter
			foreach($return as $crmid => $email) {
				if (in_array($email,$unsubscripted)) {
					$ids = array_keys($return,$email);
					foreach($ids as $id) {
						unset($return[$id]);
					}
				}
			}
		}
		//crmv@25085e
		
		return array_keys($return);
	}

	function getTargetElements(&$return,&$return_contacts,&$return_accounts,&$return_leads,$targetid) {

		global $adb,$onlyquery;
		global $table_prefix;
		$onlyquery = true;

		$focus_target = CRMEntity::getInstance('Targets');
		
		if (vtlib_isModuleActive('Leads')){ //crmv@48990
			$focus_target->get_related_list_target($targetid, getTabid('Targets'), getTabid('Leads'));
			$result_leads = $adb->query(replaceSelectQuery($_SESSION['leads_listquery'],$table_prefix.'_crmentity.crmid,'.$this->email_fields['Leads']['tablename'].'.'.$this->email_fields['Leads']['columnname']));
			while($leads=$adb->fetchByAssoc($result_leads)) {
				if ($leads[$this->email_fields['Leads']['columnname']] != '') {
					$return[$leads['crmid']] = $leads[$this->email_fields['Leads']['columnname']];
					$return_leads[$leads['crmid']] = $leads[$this->email_fields['Leads']['columnname']];
				}
			}
		}

		if (vtlib_isModuleActive('Accounts')){ //crmv@48990
			$focus_target->get_related_list_target($targetid, getTabid('Targets'), getTabid('Accounts'));
			$result_accounts = $adb->query(replaceSelectQuery($_SESSION['accounts_listquery'],$table_prefix.'_crmentity.crmid,'.$this->email_fields['Accounts']['tablename'].'.'.$this->email_fields['Accounts']['columnname']));
			while($accounts=$adb->fetchByAssoc($result_accounts)) {
				if ($accounts[$this->email_fields['Accounts']['columnname']] != '') {
					$return[$accounts['crmid']] = $accounts[$this->email_fields['Accounts']['columnname']];
					$return_accounts[$accounts['crmid']] = $accounts[$this->email_fields['Accounts']['columnname']];
				}
			}
		}

		if (vtlib_isModuleActive('Contacts')){ //crmv@48990
			$focus_target->get_related_list_target($targetid, getTabid('Targets'), getTabid('Contacts'));
			$result_contacts = $adb->query(replaceSelectQuery($_SESSION['contacts_listquery'],$table_prefix.'_crmentity.crmid,'.$this->email_fields['Contacts']['tablename'].'.'.$this->email_fields['Contacts']['columnname']));
			while($contacts=$adb->fetchByAssoc($result_contacts)) {
				if ($contacts[$this->email_fields['Contacts']['columnname']]) {
					$return[$contacts['crmid']] = $contacts[$this->email_fields['Contacts']['columnname']];
					$return_contacts[$contacts['crmid']] = $contacts[$this->email_fields['Contacts']['columnname']];
				}
			}
		}
		
		//crmv@88671
		$ids = array();
		global $currentModule;
		$currentModule_tmp = $currentModule;
		$currentModule = 'Targets';
		$focus_target->get_related_list($targetid, getTabid('Targets'), getTabid('Targets'));
		$result_targets = $adb->query($_SESSION['targets_listquery']);
		while($targets=$adb->fetchByAssoc($result_targets)) {
			$ids[] = $targets['crmid'];
		}
		$currentModule = $currentModule_tmp;
		//crmv@88671e
		if (!empty($ids)) {
			if (!is_array($this->foundTargets)) $this->foundTargets = array();
			$ids = array_diff($ids,$this->foundTargets);
			$this->foundTargets = array_merge($this->foundTargets,$ids);
			foreach ($ids as $tid) {
				$this->getTargetElements($return,$return_contacts,$return_accounts,$return_leads,$tid);
			}
		}

		$onlyquery = false;
	}

	function getTargetTree() {
		global $adb;
		$return = array();
		$focus_campaign = CRMEntity::getInstance('Campaigns');
		$focus_campaign->retrieve_entity_info($this->column_fields['campaignid'],"Campaigns");
		$focus_campaign->get_related_list($this->column_fields['campaignid'],getTabid('Campaigns'),getTabid('Targets'));
		$result_targets = $adb->query($_SESSION['targets_listquery']);
		while($targets=$adb->fetchByAssoc($result_targets)) {
			$return[$targets['crmid']] = $this->getTargetBranches($targets['crmid']);
		}
		return $return;
	}

	function getTargetBranches($targetid) {

		global $adb;

		$focus_target = CRMEntity::getInstance('Targets');

		$focus_target->get_related_list_target($targetid, getTabid('Targets'), getTabid('Leads'));
		$result_leads = $adb->query($_SESSION['leads_listquery']);
		while($leads=$adb->fetchByAssoc($result_leads)) {
			$return['Leads'][] = $leads['crmid'];
		}

		$focus_target->get_related_list_target($targetid, getTabid('Targets'), getTabid('Accounts'));
		$result_accounts = $adb->query($_SESSION['accounts_listquery']);
		while($accounts=$adb->fetchByAssoc($result_accounts)) {
			$return['Accounts'][] = $accounts['crmid'];
		}

		$focus_target->get_related_list_target($targetid, getTabid('Targets'), getTabid('Contacts'));
		$result_contacts = $adb->query($_SESSION['contacts_listquery']);
		while($contacts=$adb->fetchByAssoc($result_contacts)) {
			$return['Contacts'][] = $contacts['crmid'];
		}

		$focus_target->get_targets($targetid, getTabid('Targets'), getTabid('Targets'));
		$result_targets_targets = $adb->query($_SESSION['targets_listquery']);
		while($targets_targets=$adb->fetchByAssoc($result_targets_targets)) {
			$return['Targets'][$targets_targets['crmid']] = $this->getTargetBranches($targets_targets['crmid']);
		}

		return $return;
	}

	function sendNewsletter($crmid='',$mode='',$to_address='') {
		require_once('modules/Emails/mail.php');
		global $adb;
		global $table_prefix;
		$module = getSalesEntityType($crmid);
		//crmv@25872
		if ($to_address == '' && $crmid != '') {
			$focus = CRMEntity::getInstance($module);
			$error = $focus->retrieve_entity_info($crmid,$module,false);
			if ($error != '') {
				return $error; // return LBL_RECORD_DELETE
			}
			$to_address = $focus->column_fields[$this->email_fields[$module]['fieldname']];
		}
		//crmv@25872e
		//crmv@55961 check unsubscripted
		if ($mode != 'test') {
			static $unsubscripted = array();
			if (empty($unsubscripted)) {
				$unsubscripted = $this->getUnsubscriptedList();
			}
			if (!empty($unsubscripted) && in_array($to_address,$unsubscripted)) {
				return 'LBL_ERROR_MAIL_UNSUBSCRIBED';
			}
		}
		//crmv@55961e
		//crmv@28170	crmv@64475
		static $emailtemplates = array();
		if (!isset($emailtemplates[$this->column_fields['templateemailid']])) {
			$result = $adb->query('select subject,body from '.$table_prefix.'_emailtemplates where templateid = '.$this->column_fields['templateemailid']);
			$emailtemplates[$this->column_fields['templateemailid']]['description'] = $description = $adb->query_result($result,0,'body');
			$emailtemplates[$this->column_fields['templateemailid']]['subject'] = $subject = $adb->query_result_no_html($result,0,'subject');	//crmv@25243
		} else {
			$description = $emailtemplates[$this->column_fields['templateemailid']]['description'];
			$subject = $emailtemplates[$this->column_fields['templateemailid']]['subject'];
		}
		//crmv@28170e	crmv@64475e
		//crmv@34219
		$ownerId = getOwnerId($crmid);
		if ($mode != 'test' && ($ownerId == '' || (vtws_isRecordOwnerUser($ownerId) != true && vtws_isRecordOwnerGroup($ownerId) != true))) {
			return 'LBL_OWNER_MISSING';
		}
		//crmv@34219e

		// crmv@38592
		// the preview link will then be tracked like normal links
		$description = $this->setGlobalLinks($description, $crmid);
		if ($mode != 'test') {
			$replBody = $replSubject = array();
			$description = getMergedDescription($description,$crmid,$module,$this->id,$this->column_fields['templateemailid'], $replBody);
			$description = $this->setTrackLinks($description,$crmid);
			$subject = getMergedDescription($subject,$crmid,$module,$this->id,$this->column_fields['templateemailid'], $replSubject);
			// save field values
			$values = array_values(array_merge(array_values($replSubject), array_values($replBody)));
			$adb->updateClob('tbl_s_newsletter_queue','fieldvalues',"newsletterid={$this->id} and crmid=$crmid", Zend_Json::encode($values));
			// crmv@38592e
		}
		//logo - i
		if (is_array($description)) {
			foreach($description as $type => $descr) {
				if (strpos($description[$type], '$logo$') !== false)
				{
					$description[$type] = str_replace('$logo$','<img src="cid:logo" />',$description[$type]);
					$logo=1;
				}
			}
		} else {
			if (strpos($description, '$logo$') !== false)
			{
				$description = str_replace('$logo$','<img src="cid:logo" />',$description);
				$logo=1;
			}
		}
		//logo - e
		$from_name = $this->column_fields['from_name'];
		$from_address = $this->column_fields['from_address'];
		if ($mode == 'test') {
			$newsletter_params = array(
				'smtp_config'=>$this->smtp_config, //crmv@34245
			);
		} else {
			if (file_exists('modules/Campaigns/ProcessBounces.config.php')) include('modules/Campaigns/ProcessBounces.config.php');
			$newsletter_params = array(
				'sender'=>$message_envelope,
				'newsletterid'=>$this->id,
				'crmid'=>$crmid,
				'smtp_config'=>$this->smtp_config, //crmv@34245
			);
		}
		$mail_status = send_mail('Emails',$to_address,$from_name,$from_address,$subject,$description,'','','all',$this->id,$logo,$newsletter_params);
		// crmv@38592 - collego la mail al Contatto/Azienda/Lead -> skipped
		return $mail_status;
	}

	// crmv@38592
	// make replacements without crmid
	function setGlobalLinks($description, $crmid=null, &$replacements = null) {
		// preview link
		$previewLink = "H|0|$this->id|".intval($crmid);
		$masked = urlencode(base64_encode($previewLink));
		$r = array(
			'$Newsletter||tracklink#preview$' => '<a href="'.$this->url_tracklink_file.'?id='.$masked.'">'.getTranslatedString('LBL_HERE').'</a>',
		);
		if (!is_null($replacements)) $replacements = $r;
		$description = str_replace(array_keys($r), array_values($r), $description);
		return $description;
	}

	function setTrackLinks($description,$crmid, $trackuser = true) { // crmv@47490
		global $adb,$site_URL;

		$htmlmessage = $textmessage = html_entity_decode($description);

		preg_match_all('/<a(.*)href=["\'](.*)["\']([^>]*)>(.*)<\/a>/Umis',$htmlmessage,$links);
		for($i=0; $i<count($links[2]); $i++){
			$link = $this->cleanTrackUrl($links[2][$i]);
			$link = str_replace('"','',$link);
			if (preg_match('/\.$/',$link)) {
				$link = substr($link,0,-1);
			}
			$linkid = 0;
			if ((preg_match('/^http|ftp/',$link) || preg_match('/^http|ftp/',$urlbase)) && !strpos($link,$this->url_tracklink_file)) {

				$url = $this->cleanTrackUrl($link,array('PHPSESSID','uid'));

				// check if already inserted
				$res = $adb->pquery("select linkid from tbl_s_newsletter_links where newsletterid = ? and url = ?", array($this->id, $url));
				if ($res && $adb->num_rows($res) > 0) {
					$linkid = $adb->query_result_no_html($res, 0, 'linkid');
				} else {
					$linkid = $adb->getUniqueID('tbl_s_newsletter_links');
					$adb->pquery('insert into tbl_s_newsletter_links (linkid,newsletterid,url,forward) values (?,?,?,?)',array($linkid,$this->id,$url,addslashes($link)));
				}

				$masked = "H|$linkid|$this->id|$crmid";
				//$masked = $masked ^ XORmask;
				$masked = urlencode(base64_encode($masked));
				$newlink = sprintf('<a%shref="%s?id=%s" %s>%s</a>',$links[1][$i],$this->url_tracklink_file,$masked,$links[3][$i],$links[4][$i]);
				$htmlmessage = str_replace($links[0][$i], $newlink, $htmlmessage);

				$masked_t = "T|$linkid|$this->id|$crmid";
				//$masked_t = $masked_t ^ XORmask;
				$masked_t = urlencode(base64_encode($masked_t));
				$newlink_t = sprintf('%s?id=%s',$this->url_tracklink_file,$masked_t);
        		$textmessage = str_replace($links[0][$i], '#link#'.$newlink_t.'#link-e#', $textmessage);
			}
		}

		$track_user = '<img src="'.$this->url_trackuser_file.'?c='.$crmid.'&n='.$this->id.'" width="1" height="1" border="0">';
		$htmlmessage = ($trackuser ? $track_user : '').$htmlmessage; // crmv@47490

		$textmessage = strip_tags(preg_replace(array("/<p>/i","/<br>/i","/<br \/>/i"),array("\n","\n","\n"),$textmessage));
		$textmessage = str_replace('#link#','<',$textmessage);
		$textmessage = str_replace('#link-e#','>',$textmessage);

		return array('html'=>$htmlmessage,'text'=>$textmessage);
	}

	function saveTemplateEmail($templateid = null) {
		global $adb, $table_prefix;

		if (empty($templateid)) $templateid = $this->column_fields['templateemailid'];
		if (empty($templateid)) return false;

		$date_scheduled = $this->column_fields['date_scheduled'].' '.$this->column_fields['time_scheduled'];

		$tplres = $adb->pquery("select * from {$table_prefix}_emailtemplates where templateid = ?", array($templateid));
		if ($tplres && $adb->num_rows($tplres) > 0) {
			$tplinfo = $adb->FetchByAssoc($tplres, -1, false);
			$tplid = $adb->getUniqueID('tbl_s_newsletter_tpl');
			$tplnl = array($tplid, $this->id, $adb->formatDate($date_scheduled,true), $tplinfo['templatename'], $tplinfo['subject']);
			$res = $adb->pquery('insert into tbl_s_newsletter_tpl (tplid, newsletterid, datesent, templatename, subject) values ('.generateQuestionMarks($tplnl).')', $tplnl);
			// update longtext columns
			$adb->updateClob('tbl_s_newsletter_tpl','description',"tplid=$tplid",$tplinfo['description']);
			$adb->updateClob('tbl_s_newsletter_tpl','body',"tplid=$tplid",$tplinfo['body']);
			// calculate fields
			$fields = array('subject'=>array(), 'body'=>array());
			if (preg_match_all('/\$[^|]+\|[^|]*\|[^$]+\$/', $tplinfo['subject'], $matches) && count($matches[0]) > 0) {
				foreach ($matches[0] as $m) {
					$fields['subject'][] = $m;
				}
			}
			if (preg_match_all('/\$[^|]+\|[^|]*\|[^$]+\$/', $tplinfo['body'], $matches) && count($matches[0]) > 0) {
				foreach ($matches[0] as $m) {
					$fields['body'][] = $m;
				}
			}
			// and save them
			$adb->updateClob('tbl_s_newsletter_tpl','fields',"tplid=$tplid",Zend_Json::encode($fields));
		}
	}
	
	// shows the newsletter
	// NOTE: this method may be called from un-logged users
	function showNewsletter($crmid = null, $hideAddress = false, $trackUser = true) { // crmv@47490
		global $adb, $table_prefix;

		if (empty($this->id)) return null;

		$templateid = intval($this->column_fields['templateemailid']);

		// try to retrieve from saved templates
		$res = $adb->pquery("select * from tbl_s_newsletter_tpl where newsletterid = ?", array($this->id));
		if ($res && $adb->num_rows($res) > 0) {
			$templateInfo = $adb->FetchByAssoc($res, -1, false);
			$jsonStruct = Zend_Json::decode($templateInfo['fields']);
		} elseif ($templateid > 0) {
			// retrieve from current templates
			$res = $adb->pquery("select * from {$table_prefix}_emailtemplates where deleted = 0 and templateid = ?", array($templateid));
			if ($res && $adb->num_rows($res) > 0) {
				$templateInfo = $adb->FetchByAssoc($res, -1, false);
			}
		} else {
			return null;
		}
		if (empty($templateInfo)) return null;

		// crmid-dependant replacements
		if ($crmid > 0) {
			// initialize related module
			$module = getSalesEntityType($crmid);
			if (empty($module) || !in_array($module, array('Contacts', 'Leads', 'Accounts'))) {
				$module = $crimd = '';
			} else {
				$focus = CRMEntity::getInstance($module);

				// global substitutions
				$replacements = array();
				$templateInfo['body'] = $this->setGlobalLinks($templateInfo['body'], $crmid, $replacements);
				if (is_array($jsonStruct['body']) && is_array($replacements) && count($replacements) > 0)  {
					$jsonStruct['body'] = array_diff($jsonStruct['body'], array_keys($replacements));
				}

				// check if we have saved fields
				$res = $adb->pquery("select fieldvalues from tbl_s_newsletter_queue where newsletterid = ? and crmid = ?", array($this->id, $crmid));
				if ($res && $adb->num_rows($res) > 0 && is_array($jsonStruct) && count($jsonStruct) > 0) {
					$jsonFields = Zend_Json::decode($adb->query_result_no_html($res, 0, 'fieldvalues'));
					if (is_array($jsonFields) && count($jsonFields) > 0) {
						if (is_array($jsonStruct['subject']) && count($jsonStruct['subject']) > 0) {
							$subfields = array_slice($jsonFields, 0, count($jsonStruct['subject']));
							$bodyfields = array_slice($jsonFields, count($jsonStruct['subject']));
						} else {
							$subfields = array();
							$bodyfields = $jsonFields;
						}
						if (!empty($subfields)) {
							$templateInfo['subject'] = str_replace($jsonStruct['subject'], $subfields, $templateInfo['subject']);
						}
						if (!empty($bodyfields)) {
							$templateInfo['body'] = str_replace($jsonStruct['body'], $bodyfields, $templateInfo['body']);
							//crmv@94298
							$body = $this->setTrackLinks(htmlentities($templateInfo['body']),$crmid, $trackUser); // stupid and useless trick to have correct chars, crmv@47490
							$templateInfo['body'] = $body['html'];
							//crmv@94298e
						}
					}
				} else {
					$templateInfo['subject'] = getMergedDescription($templateInfo['subject'],$crmid,$module);
					$templateInfo['body'] = getMergedDescription($templateInfo['body'],$crmid,$module);
					$body = $this->setTrackLinks(htmlentities($templateInfo['body']),$crmid, $trackUser); // stupid and useless trick to have correct chars, crmv@47490
					$templateInfo['body'] = $body['html'];
				}
			}
		// global substitutions
		} else {
			$templateInfo['body'] = $this->setGlobalLinks($templateInfo['body']);
		}

		global $small_page_title, $small_page_path;
		$small_page_title= getTranslatedString('LBL_PREVIEW_NEWSLETTER', 'Newsletter');

		// draw header and get smarty instance
		include('themes/SmallHeader.php');

		$smarty->assign('NEWSLETTERINFO', $this->column_fields);
		$smarty->assign('TEMPLATEINFO', $templateInfo);

		if ($crmid > 0 && $focus && !$hideAddress) {
			// get address
			$error = $focus->retrieve_entity_info($crmid,$module,false);
			if ($error == '') {
				$to_address = $focus->column_fields[$this->email_fields[$module]['fieldname']];
				$smarty->assign('TO_ADDRESS', $to_address);
				$ename = getEntityName($module, $crmid);
				$smarty->assign('TO_NAME', $ename[$crmid]);
			}
		}

		$smarty->display('modules/Newsletter/ShowPreview.tpl');
	}
	// crmv@38592e

	function cleanTrackUrl($url,$disallowed_params = array('PHPSESSID')) {
		$parsed = @parse_url($url);
		$params = array();

		if (empty($parsed['query'])) {
			$parsed['query'] = '';
		}
		# hmm parse_str should take the delimiters as a parameter
		if (strpos($parsed['query'],'&amp;')) {
			$pairs = explode('&amp;',$parsed['query']);
			foreach ($pairs as $pair) {
				list($key,$val) = explode('=',$pair);
				$params[$key] = $val;
			}
		} else {
			parse_str($parsed['query'],$params);
		}
		$uri = !empty($parsed['scheme']) ? $parsed['scheme'].':'.((strtolower($parsed['scheme']) == 'mailto') ? '':'//'): '';
		$uri .= !empty($parsed['user']) ? $parsed['user'].(!empty($parsed['pass'])? ':'.$parsed['pass']:'').'@':'';
		$uri .= !empty($parsed['host']) ? $parsed['host'] : '';
		$uri .= !empty($parsed['port']) ? ':'.$parsed['port'] : '';
		$uri .= !empty($parsed['path']) ? $parsed['path'] : '';
		#  $uri .= $parsed['query'] ? '?'.$parsed['query'] : '';
		$query = '';
		foreach ($params as $key => $val) {
			if (!in_array($key,$disallowed_params)) {
				//0008980: Link Conversion for Click Tracking. no = will be added if key is empty.
				$query .= $key . ( ($val !== '' && !is_null($val)) ? '=' . $val . '&' : '&' );	//crmv@36574
			}
		}
		$query = substr($query,0,-1);
		$uri .= $query ? '?'.$query : '';
		#  if (!empty($params['p'])) {
		#    $uri .= '?p='.$params['p'];
		#  }
		$uri .= !empty($parsed['fragment']) ? '#'.$parsed['fragment'] : '';
		return $uri;
	}

	//crmv@55961
	function unsubscribe($crmid, $mode) {
		//il controllo lo faccio sul campo email perche' se modifico il target e aggiungo un lead con
		//la stessa email di un contatto che si e' gia' disiscritto non devo comunque mandargli la mail
		/*
		 * return: 1	done
		 * return: 2	already done
		 * return: 3	problems
		 */
		global $adb;
		$module = getSalesEntityType($crmid);
		$focus = CRMEntity::getInstance($module);
		$focus->retrieve_entity_info($crmid,$module);
		$email = $focus->column_fields[$this->email_fields[$module]['fieldname']];

		if ($mode == 'campaign') {
			$result = $adb->pquery('select * from tbl_s_newsletter_unsub where newsletterid = ? and email = ?',array($this->id,$email));
			if ($result && $adb->num_rows($result)>0) {
				return 2;
			} else {
				$adb->pquery('insert into tbl_s_newsletter_unsub (newsletterid,email,statusid) values (?,?,?)',array($this->id,$email,1)); //crmv@38592
				$result = $adb->pquery('select * from tbl_s_newsletter_unsub where newsletterid = ? and email = ?',array($this->id,$email));
				if ($result && $adb->num_rows($result)>0) {
					return 1;
				}
			}
		} elseif ($mode == 'all') {
			$result = $adb->pquery('select * from tbl_s_newsletter_g_unsub where email = ?',array($email));
			if ($result && $adb->num_rows($result)>0) {
				return 2;
			} else {
				$this->lockReceivingNewsletter($email,'lock');
				$result = $adb->pquery('select * from tbl_s_newsletter_g_unsub where email = ?',array($email));
				if ($result && $adb->num_rows($result)>0) {
					return 1;
				}
			}
		}
		return 3;
	}
	
	function receivingNewsletter($email) {
		global $adb;
		static $_cache = array();
		if (!isset($_cache[$email])) {
			$result = $adb->pquery("select * from tbl_s_newsletter_g_unsub where email = ?",array($email));
			if ($result && $adb->num_rows($result) > 0) {
				// this email is locked
				$_cache[$email] = false;
			} else {
				$_cache[$email] = true;
			}
		}
		return $_cache[$email];
	}
	
	function lockReceivingNewsletter($email, $mode) {
		global $adb;
		if ($mode == 'lock') {
			if ($adb->isMysql()){
				$adb->pquery('insert ignore into tbl_s_newsletter_g_unsub (email,unsub_date) values (?,?)',array($email,date('Y-m-d H:i:s')));
			// crmv@87062
			} else {
				$result = $adb->pquery('select email from tbl_s_newsletter_g_unsub where email = ?',array($email));
				if ($result && $adb->num_rows($result) == 0) {
					$adb->pquery('insert into tbl_s_newsletter_g_unsub (email,unsub_date) values (?,?)',array($email,date('Y-m-d H:i:s')));
				}
			}
			// crmv@87062e
		} elseif ($mode == 'unlock') {
			$adb->pquery('delete from tbl_s_newsletter_g_unsub where email = ?',array($email));
		}
	}
	//crmv@55961e

	function getUnsubscriptedList() {
		global $adb;
		global $table_prefix;
		$newsletterid = array();
		if ($this->column_fields['campaignid'] != '') {
			$result = $adb->query('SELECT newsletterid FROM '.$table_prefix.'_newsletter
									INNER JOIN '.$table_prefix.'_crmentity ON '.$table_prefix.'_crmentity.crmid = '.$table_prefix.'_newsletter.newsletterid
									WHERE deleted = 0 AND campaignid = '.$this->column_fields['campaignid']);
			if ($result && $adb->num_rows($result)>0) {
				while($row=$adb->fetchByAssoc($result)) {
					$newsletterid[] = $row['newsletterid'];
				}
			}
		} else {
			$newsletterid[] = $this->id;
		}
		$unsubscripted = array();
		$result = $adb->query('select email from tbl_s_newsletter_unsub where newsletterid in ('.implode(',',$newsletterid).')');
		if ($result && $adb->num_rows($result)>0) {
			while($row=$adb->fetchByAssoc($result)) {
				$unsubscripted[] = $row['email'];
			}
		}
		//crmv@55961
		$result = $adb->query('select email from tbl_s_newsletter_g_unsub');
		if ($result && $adb->num_rows($result)>0) {
			while($row=$adb->fetchByAssoc($result)) {
				if (!in_array($row['email'],$unsubscripted)) $unsubscripted[] = $row['email'];
			}
		}
		//crmv@55961e
		return $unsubscripted;
	}

	function getNoEmailProcessedBySchedule() {
		return $this->no_email_processed_by_schedule;
	}

	function getIntervalBetweenEmailDelivery() {
		return $this->interval_between_email_delivery;
	}

	function getIntervalBetweenBlocksEmailDelivery() {
		return $this->interval_between_blocks_email_delivery;
	}
	
	/* crmv@24933 crmv@55961 */
	function trackLink($linkid,$newsletterid,$crmid,$linkurlid,$msgtype) {
		global $adb;
		
		// get existing tracklink
		$result = $adb->pquery('select * from tbl_s_newsletter_tl where linkurlid = ? and newsletterid = ? and crmid = ?',array($linkid,$newsletterid,$crmid));
		$trackdata = $adb->FetchByAssoc($result, -1, false);
		
		$date = $adb->formatDate(date('Y-m-d H:i:s'),true);
		if (empty($trackdata['trackid'])) {
			// insert
			$trackid = $adb->getUniqueID('tbl_s_newsletter_tl');
			$adb->pquery('insert into tbl_s_newsletter_tl (trackid, newsletterid, linkurlid, crmid, firstclick, latestclick, clicked) values(?,?,?,?,?,?,?)',
				array($trackid, $newsletterid, $linkid, $crmid, $date, $date, 1)
			);
		} else {
			$trackid = $trackdata['trackid'];
			// update
			$adb->pquery('update tbl_s_newsletter_tl set latestclick = ?, clicked = clicked + 1 where trackid = ? and crmid = ? and newsletterid = ? and linkurlid = ?',array($date,$trackid,$crmid,$newsletterid,$linkurlid));
		}
		
		$result = $adb->pquery('select first_view,last_view,num_views from tbl_s_newsletter_queue where newsletterid = ? and crmid = ?',array($newsletterid,$crmid));
		if ($result && $adb->num_rows($result)>0) {
			$first_view = $adb->query_result($result,0,'first_view');
			$last_view = $adb->query_result($result,0,'last_view');
			$num_views = $adb->query_result($result,0,'num_views');
			if (in_array($first_view,array('','0000-00-00 00:00:00'))) {
				$adb->pquery('update tbl_s_newsletter_queue set first_view = ? where newsletterid = ? and crmid = ?',array($date,$newsletterid,$crmid));
			}
			if ($msgtype == 'T' || in_array($last_view,array('','0000-00-00 00:00:00'))) {
				//questa parte viene eseguita qui quando la mail viene letta come testo semplice in quanto non viene mostrata l'immagine per il track user
				$adb->pquery('update tbl_s_newsletter_queue set last_view = ? where newsletterid = ? and crmid = ?',array($date,$newsletterid,$crmid));
			}
			if ($msgtype == 'T' || $num_views == 0) {
				$adb->pquery('update tbl_s_newsletter_queue set num_views = ? where newsletterid = ? and crmid = ?',array(($num_views+1),$newsletterid,$crmid));
			}
		}
	}
	
	//crmv@101506 crmv@104975
	function getExtraDetailTabs() {
		$return = array();
		
		$url = "index.php?module=Newsletter&action=Statistics&record={$this->id}";
		$return[] = array('label' => getTranslatedString('LBL_STATISTICS'), 'href'=> $url, 'onclick' => '');
		
		$others = parent::getExtraDetailTabs() ?: array();

		return array_merge($return, $others);
	}
	// crmv@104975e
	
	function getExtraDetailBlock() {
		return '<div id="Statisticts" class="detailTabsMainDiv" style="display:none"></div>';
	}
	//crmv@101506e
}
?>
