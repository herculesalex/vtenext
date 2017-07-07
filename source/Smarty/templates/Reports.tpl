{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
*}

{* crmv@82831 crmv@97862 *}

<!-- module header -->
<script language="JavaScript" type="text/javascript" src="modules/Reports/Reports.js"></script>
<script language="JavaScript" type="text/javascript">
	/* labels for reports used in javascript */
	var ReportLabels = {ldelim}
		LBL_ADD_NEW_GROUP: '{$MOD.LBL_ADD_NEW_GROUP}',
		DELETE_FOLDER_CONFIRMATION: '{$APP.DELETE_FOLDER_CONFIRMATION}',
		FOLDERNAME_CANNOT_BE_EMPTY: '{$APP.FOLDERNAME_CANNOT_BE_EMPTY}',
		FOLDER_NAME_ALLOW_20CHARS: '{$APP.FOLDER_NAME_ALLOW_20CHARS}',
		FOLDER_NAME_ALREADY_EXISTS: '{$APP.FOLDER_NAME_ALREADY_EXISTS}',
		SPECIAL_CHARS_NOT_ALLOWED: '{$APP.SPECIAL_CHARS_NOT_ALLOWED}',
		LBL_RENAME_FOLDER: '{$MOD.LBL_RENAME_FOLDER}',
		DELETE_CONFIRMATION: '{$APP.DELETE_CONFIRMATION}',
		SELECT_ATLEAST_ONE_REPORT: '{$APP.SELECT_ATLEAST_ONE_REPORT}',
		DELETE_REPORT_CONFIRMATION: '{$APP.DELETE_REPORT_CONFIRMATION}',
	{rdelim}
</script>

<!-- Toolbar -->
{include file="Buttons_List1.tpl"}	<!-- crmv@18549 -->

<div id="reportContents">
	{include file="ReportContents.tpl"}
</div>
<!-- Reports Table Ends Here -->

{assign var="FLOAT_WIDTH" value="400px"}
{assign var="FLOAT_TITLE" value=$MOD.Move_Reports}
{capture assign="FLOAT_BUTTONS"}
<button type="button" title="{$APP.LBL_MOVE}" class="crmbutton small save" onclick="MoveReport()">{$APP.LBL_MOVE}</button>
{/capture}
{capture assign="FLOAT_CONTENT"}
<form name="lview_folder_addform" id="lview_folder_addform">
	<input type="hidden" name="formodule" value="{$MODULE}" />
	<input type="hidden" name="subaction" value="add" />
	<table cellpadding="5" cellspacing="0" class="hdrNameBg" >
		<tr>
			<td>{$APP.LBL_SELECT_FOLDER}</td>
			<td>
				<select id="select_move_report" name="select_move_report">
				{foreach item=rfold from=$REPT_FOLDERS}
					<option value="{$rfold.id}">{$rfold.name}</option>
				{/foreach}
				</select>

			</td>
		</tr>
	</table>
</form>
{/capture}
{include file="FloatingDiv.tpl" FLOAT_ID="ReportMove"}


<div id="Buttons_List_3_Container" style="display:none;">	{*<!-- crmv@18592 -->*}
<table border=0 cellspacing=0 cellpadding=2 width=100% class="small">
	<tr>
		<td style="padding:5px">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td align="left">
						{if $FOLDERID > 0}
							<a href="index.php?module={$MODULE}&action=index"><img src="{'folderback.png'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_GO_BACK}" title="{$APP.LBL_GO_BACK}" align="absbottom" border="0" /></a>
						{else}
							<button class="crmbutton small edit" type="button" title="{$APP.LBL_FOLDERS}" onclick="location.href='index.php?module={$MODULE}&action=index';">{$APP.LBL_FOLDERS}</button>
						{/if}
						<button class="crmbutton small delete" type="button" onclick="return massDeleteReport()" >{$APP.LBL_MASS_DELETE}</button>
						<button class="crmbutton small edit" type="button" title="{$MOD.Move_Reports}" onclick="showMoveReport(this)" >{$MOD.Move_Reports}</button>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>

<script type="text/javascript">calculateButtonsList3();</script>
{* crmv@30967e *}

