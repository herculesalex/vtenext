{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}

<div class="row">
<div class="col-lg-12">
	<h1 class="page-header">
		{'LBL_POTENTIALS'|getTranslatedString}
	</h1>

{if $ALLOW_ALL eq 'true'}
	<div class="row">
 		<div class="col-lg-12" align="right">
			{'SHOW'|getTranslatedString}
			<select class="form-control" name="list_type" onchange="getList(this, '{$MODULE}');">
 				<option value="mine" {$MINE_SELECTED}>{'MINE'|getTranslatedString}</option>
				<option value="all" {$ALL_SELECTED}>{'ALL'|getTranslatedString}</option>
			</select>
		</div>
	</div>
{/if}
<!-- <div class="table-responsive">  -->

{if $FIELDLISTVIEW eq 'MODULE_INACTIVE' || $FIELDLISTVIEW eq 'LBL_NOT_AVAILABLE'}
	{include file='ListViewEmpty.tpl' ERR_MESSAGE=$FIELDLISTVIEW}
{elseif $MODULE eq 'Products' || $MODULE eq 'Services'}
	{foreach from=$FIELDLISTVIEW key=LABEL item=LIST}
		<div class="row">
 			<div class="col-lg-12">
				<h4>{$LABEL}</h4>
			</div>
		</div>

		{if $LIST eq 'MODULE_INACTIVE' || $LIST eq 'LBL_NOT_AVAILABLE'}
			{include file='ListViewEmpty.tpl' ERR_MESSAGE=$FIELDLISTVIEW}
		{elseif empty($LIST.ENTRIES)}
			{include file='ListViewEmpty.tpl'}
		{else}
			{include file='ListViewFields.tpl' HEADER=$LIST.HEADER ENTRIES=$LIST.ENTRIES LINKS=$LIST.LINKS}
		{/if}		
	{/foreach}
{elseif $MODULE eq 'Potentials'}
	{include file='ListViewFieldsPotentials.tpl' HEADER=$FIELDLISTVIEW.HEADER ENTRIES=$FIELDLISTVIEW.ENTRIES LINKS=$FIELDLISTVIEW.LINKS}
{elseif empty($FIELDLISTVIEW.ENTRIES)}
	{include file='ListViewEmpty.tpl'}
{else}
	{include file='ListViewFields.tpl' HEADER=$FIELDLISTVIEW.HEADER ENTRIES=$FIELDLISTVIEW.ENTRIES LINKS=$FIELDLISTVIEW.LINKS}
{/if}

<!-- </div> -->