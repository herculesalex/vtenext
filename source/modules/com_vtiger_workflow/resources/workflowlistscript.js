/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function workflowlistscript($){

	function jsonget(operation, params, callback){
		var obj = {
				module:'com_vtiger_workflow',
				action:'com_vtiger_workflowAjax',
				file:operation, ajax:'true'};
		$.each(params,function(key, value){
			obj[key] = value;
		});
		$.get('index.php', obj,
			function(result){
				callback(result);
		});
	}

	function center(el){
		el.css({position: 'absolute'});
		el.width("400px");
		el.height("175px");
		placeAtCenter(el.get(0));
	}

	function NewWorkflowPopup(){
		function close(){
			hideFloatingDiv('new_workflow_popup');
		}

		function show(module){
			showFloatingDiv('new_workflow_popup');
		}

		$('#new_workflow_popup_close').click(close);
		$('#new_workflow_popup_cancel').click(close);
		return {
			close:close,show:show
		};
	}

	var workflowCreationMode='from_module';
	var templatesForModule = {};
	function updateTemplateList(){
		var moduleSelect = $('#module_list');
		var currentModule = moduleSelect.val();
		
		$('#template_list').hide();
		$('#template_list_foundnone').hide();
		$('#template_list_busyicon').show();
		
		function fillTemplateList(templates){
			var templateSelect = $('#template_list');
			templateSelect.empty();
			templates = eval(templates);	//crmv@24309 non veniva trasformata in array la stringa json ottenuta precedentemente
			$.each(templates, function(i, v){
				templateSelect.append('<option value="'+v['id']+'">'+
											v['title']+'</option>');
			});
			if(templateSelect.children().length > 0) { templateSelect.show(); } 
			else { $('#template_list_foundnone').show(); }
			$('#template_list_busyicon').hide();
			
		}
		if(templatesForModule[currentModule]==null){

			jsonget('templatesformodulejson',{module_name:currentModule},
			function(templates){
				templatesForModule[currentModule] = templates;
				fillTemplateList(templatesForModule[currentModule]);
			});
		}else{
			fillTemplateList(templatesForModule[currentModule]);
		}
	}

	$(document).ready(function(){
		var newWorkflowPopup = NewWorkflowPopup();
		$("#new_workflow").click(newWorkflowPopup.show);
		$("#pick_module").change(function(){
			VtigerJS_DialogBox.block();
			$("#filter_modules").submit();
		});

		$('.workflow_creation_mode').click(function(){
			var el = $(this);
			workflowCreationMode = el.val();
			if(workflowCreationMode=='from_template'){
				updateTemplateList();
				$('#template_select_field').show();
			}else{
				$('#template_select_field').hide();
			}

		});
		$('#module_list').change(function(){
			if(workflowCreationMode=='from_template'){
				updateTemplateList();
			}
		});

		var filterModule = $('#pick_module').val();
		if(filterModule!='All'){
			$('#module_list').val(filterModule);
			$('#module_list').change();
		}
		
		$('#new_workflow_popup_save').click(function() {
			if(workflowCreationMode == 'from_template') {
				// No templates selected?
				if($('#template_list').val() == '') {
					return false;
				}
			}
		});
	});
}
workflowlistscript(jQuery);