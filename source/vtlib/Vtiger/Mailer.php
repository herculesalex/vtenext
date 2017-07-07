<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('modules/Emails/class.phpmailer.php');

include_once('config.inc.php');
include_once('include/database/PearDatabase.php');
include_once('vtlib/Vtiger/Utils.php');
include_once('vtlib/Vtiger/Event.php');

/**
 * Provides API to work with PHPMailer & Email Templates
 * @package vtlib
 */
class Vtiger_Mailer extends PHPMailer {

	var $_serverConfigured = false;

	/**
	 * Constructor
	 */	
	function __construct() {
		$this->initialize();
	}

	/**
	 * Get the unique id for insertion
	 * @access private
	 */
	function __getUniqueId() {
		global $adb,$table_prefix;
		return $adb->getUniqueID($table_prefix.'_mailer_queue');
	}

	/**
	 * Initialize this instance
	 * @access private
	 */
	function initialize() {
		$this->IsSMTP();

		global $adb;
		$result = $adb->pquery("SELECT * FROM vtiger_systems WHERE server_type=?", Array('email'));
		if($adb->num_rows($result)) {
			$this->Host = $adb->query_result($result, 0, 'server');
			$this->Username = $adb->query_result($result, 0, 'server_username');
			$this->Password = $adb->query_result($result, 0, 'server_password');
			$this->SMTPAuth = $adb->query_result($result, 0, 'smtp_auth');
			if(empty($this->SMTPAuth)) $this->SMTPAuth = false;

			$this->_serverConfigured = true;
		}
	}

	/**
	 * Reinitialize this instance for use
	 * @access private
	 */
	function reinitialize() {
		$this->From = '';
		$this->FromName = '';
		$this->to = Array();
		$this->cc = Array();
		$this->bcc = Array();
		$this->ReplyTo = Array();
		$this->Body = '';
		$this->Subject ='';
		$this->attachment = Array();
	}

	/**
	 * Initialize this instance using mail template
	 * @access private
	 */
	function initFromTemplate($emailtemplate) {
		global $adb,$table_prefix;
		$result = $adb->pquery("SELECT * from ".$table_prefix."_emailtemplates WHERE templatename=? AND foldername=?", 
			Array($emailtemplate, 'Public'));
		if($adb->num_rows($result)) {
			$this->IsHTML(true);
			$usesubject = $adb->query_result($result, 0, 'subject');
			$usebody = decode_html($adb->query_result($result, 0, 'body'));

			$this->Subject = $usesubject;
			$this->Body    = $usebody;
			return true;			
		}
		return false;		
	}

	/**
	 * Configure sender information
	 */
	function ConfigSenderInfo($fromemail, $fromname='', $replyto='') {
		if(empty($fromname)) $fromname = $fromemail;

		$this->From = $fromemail;
		$this->FromName = $fromname;
		$this->AddReplyTo($replyto);
	}

	/**
	 * Overriding default send
	 */
	function Send($sync=false, $linktoid=false) {
		if(!$this->_serverConfigured) return;

		if($sync) return parent::Send();

		$this->__AddToQueue($linktoid);
		return true;
	}

	/**
	 * Send mail using the email template
	 * @param String Recipient email
	 * @param String Recipient name
	 * @param String vtiger CRM Email template name to use
	 */
	function SendTo($toemail, $toname='', $emailtemplate=false, $linktoid=false, $sync=false) {
		if(empty($toname)) $toname = $toemail;
		$this->AddAddress($toemail, $toname);
		if($emailtemplate) $this->initFromTemplate($emailtemplate);
		return $this->Send($sync, $linktoid);
	}

	/** Mail Queue **/
	// Check if this instance is initialized.
	var $_queueinitialized = false;
	function __initializeQueue() {
		global $table_prefix;
		if(!$this->_queueinitialized) {
			if(!Vtiger_Utils::CheckTable($table_prefix.'_mailer_queue')) {
				Vtiger_Utils::CreateTable($table_prefix.'_mailer_queue',
					'id I(19) NOTNULL PRIMARY,
					fromname C(100), 
					fromemail C(100),
					mailer C(10), 
					content_type C(15), 
					subject X, 
					body X2, 
					relcrmid I(11), 
					failed I(1) NOTNULL DEFAULT 0, 
					failreason C(255)',
					true);
			}
			if(!Vtiger_Utils::CheckTable($table_prefix.'_mailer_queueinfo')) {
				Vtiger_Utils::CreateTable($table_prefix.'_mailer_queueinfo',
					'id I(11), 
					name C(100), 
					email C(100), 
					type C(7)',
					true);
			}
			$this->_queueinitialized = true;
		}
		return true;
	}

	/**
	 * Add this mail to queue
	 */
	function __AddToQueue($linktoid) {
		if($this->__initializeQueue()) {
			global $adb,$table_prefix;
			$uniqueid = self::__getUniqueId();
			$adb->pquery('INSERT INTO '.$table_prefix.'_mailer_queue(id,fromname,fromemail,content_type,subject,body,mailer,relcrmid) VALUES(?,?,?,?,?,?,?,?)',
				Array($uniqueid, $this->FromName, $this->From, $this->ContentType, $this->Subject, $this->Body, $this->Mailer, $linktoid));
			$queueid = $adb->database->Insert_ID();
			foreach($this->to as $toinfo) {
				if(empty($toinfo[0])) continue;
				$adb->pquery('INSERT INTO '.$table_prefix.'_mailer_queueinfo(id, name, email, type) VALUES(?,?,?,?)',
					Array($queueid, $toinfo[1], $toinfo[0], 'TO'));
			}
			foreach($this->cc as $ccinfo) {
				if(empty($ccinfo[0])) continue;
				$adb->pquery('INSERT INTO '.$table_prefix.'_mailer_queueinfo(id, name, email, type) VALUES(?,?,?,?)',
					Array($queueid, $ccinfo[1], $ccinfo[0], 'CC'));
			}
			foreach($this->bcc as $bccinfo) {
				if(empty($bccinfo[0])) continue;
				$adb->pquery('INSERT INTO '.$table_prefix.'_mailer_queueinfo(id, name, email, type) VALUES(?,?,?,?)',
					Array($queueid, $bccinfo[1], $bccinfo[0], 'BCC'));
			}
			foreach($this->ReplyTo as $rtoinfo) {
				if(empty($rtoinfo[0])) continue;
				$adb->pquery('INSERT INTO '.$table_prefix.'_mailer_queueinfo(id, name, email, type) VALUES(?,?,?,?)',
					Array($queueid, $rtoinfo[1], $rtoinfo[0], 'RPLYTO'));
			}
		}
	}

	/**
	 * Dispatch (send) email that was queued.
	 */
	static function dispatchQueue() {
		global $adb,$table_prefix;
		if(!Vtiger_Utils::CheckTable($table_prefix.'_mailer_queue')) return;

		$class = get_called_class() ?: get_class();
		$instance = new $class();
		$queue = $adb->query('SELECT * FROM '.$table_prefix.'_mailer_queue WHERE failed != 1');
		if($adb->num_rows($queue)) {
			for($index = 0; $index < $adb->num_rows($queue); ++$index) {
				$mailer->reinitialize();

				$queue_record = $adb->fetch_array($queue, $index);
				$queueid = $queue_record['id'];
				$relcrmid= $queue_record['relcrmid'];

				$mailer->From = $queue_record['fromemail'];
				$mailer->From = $queue_record['fromname'];
				$mailer->Subject=$queue_record['subject'];
				$mailer->Body = decode_html($queue_record['body']);
				$mailer->Mailer=$queue_record['mailer'];
				$mailer->ContentType = $queue_record['content_type'];

				$emails = $adb->pquery('SELECT * FROM '.$table_prefix.'_mailer_queueinfo WHERE id=?', Array($queueid));
				for($eidx = 0; $eidx < $adb->num_rows($emails); ++$eidx) {
					$email_record = $adb->fetch_array($emails, $eidx);
					if($email_record[type] == 'TO')     $mailer->AddAddress($email_record[email], $email_record[name]);
					else if($email_record[type] == 'CC')$mailer->AddCC($email_record[email], $email_record[name]);
					else if($email_record[type] == 'BCC')$mailer->AddBCC($email_record[email], $email_record[name]);
					else if($email_record[type] == 'RPLYTO')$mailer->AddReplyTo($email_record[email], $email_record[name]);
				}
				$sent = $mailer->Send(true);
				if($sent) {
					Vtiger_Event::trigger($table_prefix.'.mailer.mailsent', $relcrmid);
					$adb->pquery('DELETE FROM '.$table_prefix.'_mailer_queue WHERE id=?', Array($queueid));
					$adb->pquery('DELETE FROM '.$table_prefix.'_mailer_queueinfo WHERE id=?', Array($queueid));
				} else {
					$adb->pquery('UPDATE '.$table_prefix.'_mailer_queueinfo SET failed=?, failreason=? WHERE id=?', Array(1, $mailer->ErrorInfo, $queueid));
				}
			}
		}
	}
}
