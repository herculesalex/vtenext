{if $ENABLE_PDFMAKER eq 'true'}
<table border=0 cellspacing=0 cellpadding=0 style="width:100%;">
  {if $CRM_TEMPLATES_EXIST eq '0'}
        <tr>
	  		<td class="rightMailMergeContent" width="100%">
	  			<div class="dvtCellInfo">
	  				<select name="use_common_template" id="use_common_template" class="detailedViewTextBox" multiple size="5">
	          		{foreach name="tplForeach" from="$CRM_TEMPLATES" item="templates_label" key="templates_prefix"}
	            		{if $smarty.foreach.tplForeach.first}
	               			<option value="{$templates_prefix}" selected="selected">{$templates_label}</option>
	            		{else}
	                		<option value="{$templates_prefix}">{$templates_label}</option>
	            		{/if}
	          		{/foreach}
					</select>
				</div>        
	  		</td>
		</tr>
		
		{if $TEMPLATE_LANGUAGES|@sizeof > 1}
	        <tr>
	    		<td class="rightMailMergeContent">
	    			<div class="dvtCellInfo">
	            		<select name="template_language" id="template_language" class="detailedViewTextBox" size="1">
	    		  			{html_options  options=$TEMPLATE_LANGUAGES selected=$CURRENT_LANGUAGE}
	            		</select>
	            	</div>
	    		</td>
			</tr>
		{else}
			{foreach from="$TEMPLATE_LANGUAGES" item="lang" key="lang_key"}
				<input type="hidden" name="template_language" id="template_language" value="{$lang_key}"/>
			{/foreach}
		{/if}
		
		{* crmv@59091 *}
		<tr>
			<td class="rightMailMergeContent">  
				<div class="pdfActionWrap">
					<div class="pdfActionImage">
						<a href="javascript:;" onclick="if(getSelectedTemplates()=='') alert('{$PDFMAKER_MOD.SELECT_TEMPLATE}'); else {ldelim} releaseOverAll('detailViewActionsContainer');  document.location.href='index.php?module=PDFMaker&relmodule={$MODULE}&action=CreatePDFFromTemplate&record={$ID}&commontemplateid='+getSelectedTemplates()+'&language='+document.getElementById('template_language').value; {rdelim}"><img src="modules/PDFMaker/img/actionGeneratePDF.png" hspace="5" align="absmiddle" border="0"/></a>
					</div>
					<div class="pdfActionText">
						<a href="javascript:;" onclick="if(getSelectedTemplates()=='') alert('{$PDFMAKER_MOD.SELECT_TEMPLATE}'); else {ldelim} releaseOverAll('detailViewActionsContainer');  document.location.href='index.php?module=PDFMaker&relmodule={$MODULE}&action=CreatePDFFromTemplate&record={$ID}&commontemplateid='+getSelectedTemplates()+'&language='+document.getElementById('template_language').value; {rdelim}" class="webMnu">{$APP.LBL_EXPORT_TO_PDF}</a>
					</div>
				</div>
			</td>
		</tr>
		
		<tr>
          	<td class="rightMailMergeContent"> 
				<div class="pdfActionWrap">
					<div class="pdfActionImage">
						<a href="javascript:;" onclick="fnvshobj(this,'sendpdfmail_cont'); sendPDFmail('{$MODULE}','{$ID}');"><img src="modules/PDFMaker/img/PDFMail.png" hspace="5" align="absmiddle" border="0"/></a>
					</div>
					<div class="pdfActionText">
						<a href="javascript:;" onclick="fnvshobj(this,'sendpdfmail_cont'); sendPDFmail('{$MODULE}','{$ID}');" class="webMnu">{$APP.LBL_SEND_EMAIL_PDF}</a>  
					</div>
				</div>
				<div id="sendpdfmail_cont" style="z-index:100001;position:absolute;"></div>
            </td>
        </tr>

		<tr>
	  		<td class="rightMailMergeContent">
				<div class="pdfActionWrap">
					<div class="pdfActionImage">
						<a href="javascript:;" onclick="if(getSelectedTemplates()=='') alert('{$PDFMAKER_MOD.SELECT_TEMPLATE}'); else {ldelim} releaseOverAll('detailViewActionsContainer'); openPopUp('PDF',this,'index.php?module=PDFMaker&relmodule={$MODULE}&action=PDFMakerAjax&file=CreatePDFFromTemplate&mode=content&record={$ID}&commontemplateid='+getSelectedTemplates()+'&language='+document.getElementById('template_language').value, '', '900', '800', 'menubar=no,toolbar=no,location=no,status=no,resizable=yes,scrollbars=yes'); {rdelim}"><img src="{'modules/PDFMaker/img/PDF_edit.png'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
					</div>
					<div class="pdfActionText">
						<a href="javascript:;" onclick="if(getSelectedTemplates()=='') alert('{$PDFMAKER_MOD.SELECT_TEMPLATE}'); else {ldelim} releaseOverAll('detailViewActionsContainer'); openPopUp('PDF',this,'index.php?module=PDFMaker&relmodule={$MODULE}&action=PDFMakerAjax&file=CreatePDFFromTemplate&mode=content&record={$ID}&commontemplateid='+getSelectedTemplates()+'&language='+document.getElementById('template_language').value, '', '900', '800', 'menubar=no,toolbar=no,location=no,status=no,resizable=yes,scrollbars=yes'); {rdelim}" class="webMnu">{$APP.LBL_EDIT}{$APP.AND} {$APP.LBL_EXPORT_TO_PDF}</a>
					</div>
				</div>
			</td>
       </tr>
		
		<tr>
            <td class="rightMailMergeContent">
				<div class="pdfActionWrap">
					<div class="pdfActionImage">
						<a href="javascript:;" onclick="if(getSelectedTemplates()=='') alert('{$PDFMAKER_MOD.SELECT_TEMPLATE}'); else {ldelim} getPDFDocDivContent(this,'{$MODULE}','{$ID}'); {rdelim}"><img src="modules/PDFMaker/img/PDFDoc.png" hspace="5" align="absmiddle" border="0"/></a>
					</div>
					<div class="pdfActionText">
						<a href="javascript:;" onclick="if(getSelectedTemplates()=='') alert('{$PDFMAKER_MOD.SELECT_TEMPLATE}'); else {ldelim} getPDFDocDivContent(this,'{$MODULE}','{$ID}'); {rdelim}" class="webMnu">{$PDFMAKER_MOD.LBL_SAVEASDOC}</a>
					</div>
				</div>
				<div id="PDFDocDiv" style="display:none; width:350px; position:absolute; top:30px; left:30px;" class="crmvDiv"></div>	{* crmv@50159 *}
            </td>
        </tr>
       
        {if $MODULE eq 'Invoice' || $MODULE eq 'SalesOrder' || $MODULE eq 'PurchaseOrder' || $MODULE eq 'Quotes' || $MODULE eq 'Receiptcards' || $MODULE eq 'Issuecards' || $MODULE eq 'Ddt'} {* crmv@18498 *}
        <tr>
            <td class="rightMailMergeContent">
				<div class="pdfActionWrap">
					<div class="pdfActionImage">
						<a href="javascript:;" onclick="getPDFBreaklineDiv(this,'{$ID}');"><img src="modules/PDFMaker/img/PDF_bl.png" hspace="5" align="absmiddle" border="0"/></a>
					</div>
					<div class="pdfActionText">
						<a href="javascript:;" onclick="getPDFBreaklineDiv(this,'{$ID}');" class="webMnu">{$PDFMAKER_MOD.LBL_PRODUCT_BREAKLINE}</a>
					</div>
				</div>
				<div id="PDFBreaklineDiv" style="display:none; width:350px; position:absolute; top:30px; left:30px;" class="crmvDiv"></div>
            </td>
		</tr>
		
		<tr>
            <td class="rightMailMergeContent">
				<div class="pdfActionWrap">
					<div class="pdfActionImage">
						<a href="javascript:;" onclick="getPDFImagesDiv(this,'{$ID}');" class="webMnu"><img src="modules/PDFMaker/img/PDF_img.png" hspace="5" align="absmiddle" border="0"/></a>
					</div>
					<div class="pdfActionText">
						<a href="javascript:;" onclick="getPDFImagesDiv(this,'{$ID}');" class="webMnu">{$PDFMAKER_MOD.LBL_PRODUCT_IMAGE}</a>
					</div>
                <div id="PDFImagesDiv" style="display:none; width:350px; position:absolute; top:30px; left:30px;" class="crmvDiv"></div>                
            </td>
        </tr>
        {/if}
        		
        <tr>
	  	  	<td class="rightMailMergeContent">   
				<div class="pdfActionWrap">
					<div class="pdfActionImage">
						<a href="javascript:;" onclick="if(getSelectedTemplates()=='') alert('{$PDFMAKER_MOD.SELECT_TEMPLATE}'); else {ldelim} releaseOverAll('detailViewActionsContainer');  document.location.href='index.php?module=PDFMaker&relmodule={$MODULE}&action=CreatePDFFromTemplate&record={$ID}&type=rtf&commontemplateid='+getSelectedTemplates()+'&language='+document.getElementById('template_language').value; {rdelim}" class="webMnu"><img src="modules/PDFMaker/img/RtfGenerator.png" hspace="5" align="absmiddle" border="0"/></a>	{* crmv *}
					</div>
					<div class="pdfActionText">
						<a href="javascript:;" onclick="if(getSelectedTemplates()=='') alert('{$PDFMAKER_MOD.SELECT_TEMPLATE}'); else {ldelim} releaseOverAll('detailViewActionsContainer');  document.location.href='index.php?module=PDFMaker&relmodule={$MODULE}&action=CreatePDFFromTemplate&record={$ID}&type=rtf&commontemplateid='+getSelectedTemplates()+'&language='+document.getElementById('template_language').value; {rdelim}" class="webMnu">{$PDFMAKER_MOD.LBL_EXPORT_TO_RTF}</a>
					</div>
				</div>
			</td>
		</tr>
		{* crmv@59091e *}
  {else}
    <tr>
  		<td class="rightMailMergeContent">
  		  {$PDFMAKER_MOD.CRM_TEMPLATES_DONT_EXIST}
    		{if $IS_ADMIN eq '1'} 
    		  {$PDFMAKER_MOD.CRM_TEMPLATES_ADMIN}          
          <a href="index.php?module=PDFMaker&action=EditPDFTemplate&return_module={$MODULE}&return_id={$ID}&parenttab=Tools" class="webMnu">{$PDFMAKER_MOD.TEMPLATE_CREATE_HERE}</a>
        {/if}
      </td>
		</tr>
  {/if}
  
</table>
<div id="alert_doc_title" style="display:none;"><br/>{$PDFMAKER_MOD.ALERT_DOC_TITLE}</div>
{/if}