<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/menu.js"|resourcever}"></script>
{literal}
<style>
DIV.fixedLay{
	border:3px solid #CCCCCC;
	background-color:#FFFFFF;
	width:500px;
	position:fixed;
	left:250px;
	top:200px;
	display:block;
}
</style>
{/literal}
{literal}
<!--[if lte IE 6]>
<STYLE type=text/css>
DIV.fixedLay {
	POSITION: absolute;
}
</STYLE>
<![endif]-->

{/literal}

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tbody><tr>
        <td valign="top"></td>
        <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->

	<div align=center>
	
			{include file='SetMenu.tpl'}
		
			<!-- DISPLAY -->
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
			<tr>
				<td width="50" rowspan="2" valign="top"><img src="{$IMAGE_PATH}notification.gif" alt="{$MOD.LBL_MODULE_NAME}" width="48" height="48" border=0 title="{$MOD.LBL_MODULE_NAME}"></td>
				<td colspan="2" class="heading2" valign=bottom align="left"><b> {$MOD.LBL_SETTINGS} > {$MOD.NOTIFICATIONSCHEDULERS} </b></td> <!-- crmv@30683 -->
				<td rowspan=2 class="small" align=right>&nbsp;</td>
			</tr>
			<tr>
				<td valign=top class="small" align="left">{$MOD.LBL_NOTIF_SCHED_DESCRIPTION}</td>
			</tr>
			</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
						<tr><td>&nbsp;</td></tr>
				</table>

				<table width="100%" border="0" cellpadding="5" cellspacing="0" class="listTableTopButtons">
                  <tr >

                    <td  style="padding-left:5px;" class="big">{$MOD.NOTIFICATIONSCHEDULERS}</td>
                    <td align="right">&nbsp;</td>
                  </tr>
			  </table>


	
	<div id="notifycontents">
	{include file='Settings/EmailNotificationContents.tpl'}
	</div>

	<table border=0 cellspacing=0 cellpadding=5 width=100% >
	<tr><td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
	</table>
	</td>
	</tr>
	</table>
			
			
			
			</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
		
	</div>

</td>
        <td valign="top"></td>
   </tr>
</tbody>
</table>
	<div id="editdiv" style="display:none;position:absolute;width:400px;"></div>
{literal}
<script>
function fetchSaveNotify(id)
{
	$("editdiv").style.display="none";
	$("status").style.display="inline";
	var active = $("notify_status").options[$("notify_status").options.selectedIndex].value;
	var subject = $("notifysubject").value;
        var body = $("notifybody").value;
	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'action=SettingsAjax&module=Settings&file=SaveNotification&active='+active+'&notifysubject='+subject+'&notifybody='+body+'&record='+id,
                        onComplete: function(response) {
					if(response.responseText.indexOf(":#:FAILURE") > -1)
					{
						alert(alert_arr.VALID_DATA);
					}else
					{
						$("notifycontents").innerHTML=response.responseText;
					}
                                $("status").style.display="none";
                        }
                }
        );
}

function fetchEditNotify(id)
{
	$("status").style.display="inline";
	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody:'action=SettingsAjax&module=Settings&file=EditNotification&record='+id,
                        onComplete: function(response) {
                                $("status").style.display="none";
                                $("editdiv").innerHTML=response.responseText;
                        }
                }
        );
}
</script>
{/literal}
