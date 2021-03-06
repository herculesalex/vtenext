
{* crmv@119414 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@119414e *} 

{* crmv@113771 *}
{assign var="MODS4ROW" value="4"}
{assign var="MODS4AREA" value="8"}
{math equation="x / y" x=100 y=$MODS4ROW assign="AREACOLWIDTH"}
{assign var="AREACOLWIDTH" value=$AREACOLWIDTH|cat:"%"}
<div id="{if !empty($UNIFIED_SEARCH_AREAS_ID)}{$UNIFIED_SEARCH_AREAS_ID}{else}UnifiedSearchAreas{/if}" class="{if !empty($UNIFIED_SEARCH_AREAS_CLASS)}{$UNIFIED_SEARCH_AREAS_CLASS}{else}drop_mnu_all{/if}" style="min-width:700px;">	{* crmv@59091 *}
	<table cellspacing="0" cellpadding="5" border="0" class="small" style="width:100%">	{* crmv@59091 *}
	{if !$SKIP_UNIFIED_SEARCH_AREAS}
		<tr id="UnifiedSearchAreasUnifiedRow" style="display:none;">
			<td colspan="{$MODS4ROW}" style="font-size:13px;font-weight:bold;border-bottom:1px solid #E0E0E0;">
				<form name="UnifiedSearch" method="post" action="index.php" target="_blank">
				<input type="hidden" name="action" value="UnifiedSearch">
				<input type="hidden" name="module" value="Home">
				<input type="hidden" name="parenttab" value="{$CATEGORY}">
				<input type="hidden" name="search_onlyin" value="--USESELECTED--">
				<input type="hidden" id="unifiedsearch_query_string" name="query_string" value="">
				<input type="button" class="crmbutton" value="{$APP.LBL_SEARCH_ALL}" onClick="jQuery('#unifiedsearch_query_string').val(jQuery('#unifiedsearchnew_query_string').val());this.form.submit();" style="width:100%" />
				</form>
			</td>
		</tr>
	{/if}
	{assign var="count" value=0}
	{assign var="count_tmp" value=0}
	{foreach key=number item=info from=$AREAMODULELIST}
		{assign var="type" value=$info.type}
		{assign var="areainfo" value=$info.info}
		{assign var="areaid" value=$areainfo.id}
		{assign var="areaname" value=$areainfo.name}
		{assign var="arealabel" value=$areainfo.translabel}
		{assign var="areaurl" value=$areainfo.index_url}
		{assign var="areamodules" value=$areainfo.info}
		{if $count is div by $MODS4ROW}
			{assign var="count_tmp" value=1}
			<tr valign="top">
		{/if}
		<td width="{$AREACOLWIDTH}">
			<table cellspacing="0" cellpadding="3" border="0" width="100%">
				<tr height="25">
					<td style="padding:3px;font-size:13px;font-weight:bold;">
						{if $areaid neq 0 and $areaid neq -1}
							<input type="button" class="crmbutton" value="{$arealabel}" onClick="UnifiedSearchAreasObj.openArea('{$areaurl}');" style="width:100%" />
						{else}
							<span class="warning">{$arealabel}</span>
						{/if}
					</td>
				</tr>
				{assign var="count_modules" value=0}
				{foreach item=mod from=$areamodules}
					{if $count_modules gt 0 && $count_modules is div by $MODS4AREA}
						</table></td>
						{if $count_tmp is div by $MODS4ROW}
							</tr>
						{/if}
						{assign var="count" value=$count+1}
						{assign var="count_tmp" value=1}
						{if $count is div by $MODS4ROW}
							{assign var="count_tmp" value=1}
							<tr valign="top">
						{/if}
						<td width="{$AREACOLWIDTH}">
							<table cellspacing="0" cellpadding="3" border="0" width="100%">
								<tr height="25"><td style="font-size:13px;font-weight:bold;border-bottom:1px solid #E0E0E0;"></td></tr>
					{/if}
					{assign var="count_modules" value=$count_modules+1}
					<tr>
						{* crmv@59091 *}
						<td width="25">
							<a href="javascript:;" onClick="UnifiedSearchAreasObj.openModule('{$mod.index_url}','{$mod.list_url}');">	{* crmv@107077 *}
								{assign var="first_letter" value=$mod.translabel|substr:0:1|strtoupper}
								<div class="UnifiedSearchAreasUnifiedItem" style="padding:5px">
									<div class="vcenter text-left" style="width:20%">
										<i class="vteicon icon-module icon-{$mod.name|strtolower}" data-first-letter="{$first_letter}"></i>
									</div>
									<span class="vcenter">{$mod.translabel}</span>
								</div>
							</a>
						</td>
						{* crmv@59091e *}
					</tr>
				{/foreach}
			</table>
		</td>
		{if $count_tmp is div by $MODS4ROW}
			</tr>
		{/if}
		{assign var="count" value=$count+1}
		{assign var="count_tmp" value=1}
	{/foreach}
	{if $IS_ADMIN eq '1' || $BLOCK_AREA_LAYOUT eq '0'}
		<tr>
			<td colspan="{$MODS4ROW}" align="right" style="border-top:1px solid #E0E0E0;">
				<a href='javascript:void(0);' onclick="ModuleAreaManager.showSettings();">
					<i class="vteicon md-sm md-text md-link">settings</i>&nbsp;{'LBL_AREAS_SETTINGS'|getTranslatedString}
				</a>
			</td>
		</tr>
	{/if}
	</table>
</div>
