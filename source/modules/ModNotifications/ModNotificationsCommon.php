<?php
//crmv@start
function getFollowImg($record,$type='') {
	global $current_user;
	$focus = CRMEntity::getInstance('ModNotifications');
	if (in_array($record,$focus->getFollowedRecords($current_user->id,$type))) {
		return 'modules/ModNotifications/img/follow_on.png';
	} else {
		return 'modules/ModNotifications/img/follow_off.png';
	}
}
function getFollowCls($record,$type='') {
	global $current_user;
	$focus = CRMEntity::getInstance('ModNotifications');
	if (in_array($record,$focus->getFollowedRecords($current_user->id,$type))) {
		return 'notifications_on';
	} else {
		return 'notifications_none';
	}
}
function getNotificationsModuleSettings($record) {
	$focus = CRMEntity::getInstance('ModNotifications');
	return $focus->getModuleSettings($record);
}
function getPortalAvatar() {
	global $theme;
	return vtiger_imageurl('portal_avatar.png',$theme);
}
function checkListNotificationCount($list_query_count,$userid,$viewid,$new_count) {
	global $adb, $current_user,$table_prefix;
	$result = $adb->pquery('SELECT vte_modnot_follow_cv.cvid, vte_modnot_follow_cv.userid, vte_modnot_follow_cv.count, vte_modnot_follow_cv.modifiedtime, '.$table_prefix.'_customview.entitytype FROM vte_modnot_follow_cv INNER JOIN '.$table_prefix.'_customview ON '.$table_prefix.'_customview.cvid = vte_modnot_follow_cv.cvid WHERE vte_modnot_follow_cv.cvid = ? AND vte_modnot_follow_cv.userid = ?',array($viewid,$userid));
	if ($result && $adb->num_rows($result) > 0) {
		$count = $adb->query_result($result,0,'count');
		$modifiedtime = $adb->query_result($result,0,'modifiedtime');
		$module = $adb->query_result($result,0,'entitytype');
		if($_SESSION['lv_user_id_'.$module] == "all" || $_SESSION['lv_user_id_'.$module] == "") { // crmv@107328
			//uso la query in sessione
		} else {
			//ricreo la query perch� ha dei controlli anche sull'assegnatario
			$current_user_tmp = $current_user;
			
			$current_user = CRMEntity::getInstance('Users');
			$current_user->retrieve_entity_info($userid,'Users');
			$queryGenerator = QueryGenerator::getInstance($module, $current_user);
			$queryGenerator->initForCustomViewById($viewid);
			$list_query_count = $adb->query($queryGenerator->getQuery());
			$new_count = $adb->num_rows($list_query_count);
			$list_query_count = $list_query_count->sql;	//crmv@OPER5904
			
			$current_user = $current_user_tmp;
		}
		return updateListNotificationCount($list_query_count,$userid,$viewid,$new_count,$count,$modifiedtime);	//crmv@OPER5904
	}
	return false;
}

//crmv@98778
function checkAllListNotificationCount() {
	global $adb, $current_user,$table_prefix;
	$limit_q = 10000;
	$result = $adb->limitquery('select vte_modnot_follow_cv.cvid, vte_modnot_follow_cv.userid, vte_modnot_follow_cv.count, vte_modnot_follow_cv.modifiedtime, '.$table_prefix.'_customview.entitytype from vte_modnot_follow_cv inner join '.$table_prefix.'_customview on '.$table_prefix.'_customview.cvid = vte_modnot_follow_cv.cvid order by last_processed asc, vte_modnot_follow_cv.userid, vte_modnot_follow_cv.cvid',0,$limit_q);
	if ($result && $adb->num_rows($result) > 0) {
		$current_user_template = CRMEntity::getInstance('Users');
		$cached_users = Array();
		$cached_qgen = Array();
		while($row=$adb->fetchByAssoc($result)) {
			$viewid = $row['cvid'];
			$userid = $row['userid'];
			$module = $row['entitytype'];
			$count = $row['count'];
			$modifiedtime = $row['modifiedtime'];
			if (!isset($cached_users[$userid])){
				$cached_users[$userid] = $current_user_template;
				$cached_users[$userid]->retrieve_entity_info($userid,'Users');
			}
			$current_user = $cached_users[$userid];
			if (!isset($cached_qgen[$module])){
				$cached_qgen[$module] = QueryGenerator::getInstance($row['entitytype'], $current_user);
			}
			$queryGenerator = $cached_qgen[$module];
			$queryGenerator->resetAll();
			$queryGenerator->initForCustomViewById($viewid);
			$list_query_count = $adb->query(replaceSelectQuery($queryGenerator->getQuery(),"count(*) as count"));
			if ($list_query_count) {
				$new_count = $adb->query_result_no_html($list_query_count,0,'count');
				updateListNotificationCount($list_query_count->sql,$userid,$viewid,$new_count,$count,$modifiedtime);
			}
		}
	}
}

function updateListNotificationCount($list_query_count,$userid,$viewid,$new_count,$count,$modifiedtime) {
	global $adb,$table_prefix;
	$now = date('Y-m-d H:i:s');
	if ($new_count > $count) {
		$adb->pquery('update vte_modnot_follow_cv set count = ?, modifiedtime = ?,last_processed = ? where cvid = ? and userid= ?',array($new_count,$now,$now,$viewid,$userid));
		if ($count != -1) {
			if (stripos($list_query_count,'order by') !== false) {
				$list_query_count = substr($list_query_count,0,stripos($list_query_count,'order by'));
			}
			$list_query_count .= " and ".$table_prefix."_crmentity.modifiedtime > '$modifiedtime'";
			$res = $adb->query(replaceSelectQuery($list_query_count,$table_prefix.'_crmentity.crmid'));
			$ids = array();
			if ($res && $adb->num_rows($res) > 0) {
				while($row=$adb->fetchByAssoc($res)) {
					$ids[] = $row['crmid'];
				}
			}
			if (!empty($ids)) {
				$obj = CRMEntity::getInstance('ModNotifications');
				$obj->saveFastNotification(
					array(
						'assigned_user_id' => $userid,
						'createdtime' => $now,
						'mod_not_type' => 'ListView changed',
						'related_to' => $viewid,
						'description' => implode(',',$ids),
					),false
				);
				return true;
			}
		}
	} elseif ($new_count < $count) {
		$adb->pquery('update vte_modnot_follow_cv set count = ?, modifiedtime = ?,last_processed = ? where cvid = ? and userid= ?',array($new_count,$now,$now,$viewid,$userid));
	}
	else{
		$adb->pquery('update vte_modnot_follow_cv set last_processed = ? where cvid = ? and userid= ?',array($now,$viewid,$userid));
	}
	return false;
}
//crmv@98778e
