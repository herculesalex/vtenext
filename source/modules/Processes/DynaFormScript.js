/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@99316 crmv@105937 crmv@106857 crmv@112297 */

if (typeof(DynaFormScript) == 'undefined') {
	DynaFormScript = {
		fieldFocused: false,
		fieldNameFocused: false,
		// functions for reload the form in EditView in order to apply conditionals
		initEditViewConditionals: function(record, json_fields, force_reload) {
			var me = this,
				fields = JSON.parse(json_fields);
			if (typeof(force_reload) == 'undefined') var force_reload = false;

			jQuery.each(fields, function(k,field){
				if (typeof(field['columns']) != 'undefined') {
					var columns = JSON.parse(field['columns']);
					jQuery.each(columns, function(k,column){
						column['fieldname'] = field['fieldname']+'_'+column['fieldname'];
						me.initFieldEditViewConditionals(record, json_fields, column, '^=');
					});
				} else {
					me.initFieldEditViewConditionals(record, json_fields, field, '=');
				}
			});
			if (force_reload) {
				me.reloadForm(record, json_fields);
			}
		},
		initFieldEditViewConditionals: function(record, json_fields, field, selector) {
			var me = this,
				fields = JSON.parse(json_fields),
				field_on_event = field['fieldname'],
				event = 'change';
				
			if (field['uitype'] == 10) {
				field_on_event = field['fieldname']+'_display';
				event = 'blur';
			/*
			} else if (field['type'] == 'picklist' || field['type'] == 'date' || field['type'] == 'reference' || field['type'] == 'boolean' || field['type'] == 'file') {
				event = 'change';
			*/
			}
			jQuery('[name'+selector+'"'+field_on_event+'"]').unbind(event);
			jQuery('[name'+selector+'"'+field_on_event+'"]').bind(event, function(){
				me.reloadForm(record, json_fields);
			}).focus(function(){ // track new field on focus
				me.fieldFocused = true;
				me.fieldNameFocused = jQuery(this).attr('name');
			});
		},
		disableKeyboard: function(objEvent){
			objEvent.preventDefault();
		},
		reloadingForm: false,
		reloadForm: function(record, json_fields) {
			var me = this,
				fields = JSON.parse(json_fields),
				formObj = jQuery('form[name="EditView"]'),
				form = me.getForm(formObj, fields);
			
			// prevent multiple calls
			if (me.reloadingForm == true) return false;
			me.reloadingForm = true;
			
			var fieldFocused = false;
			if (me.fieldFocused) fieldFocused = me.fieldFocused;
			jQuery(document).on('keydown', DynaFormScript.disableKeyboard);

			jQuery('#status').show();
			VtigerJS_DialogBox.block();
			
			var formData = new FormData();
			jQuery.each(jQuery('form[name="EditView"]').find("input[type='file']"), function(i, tag) {
		        jQuery.each(jQuery(tag)[0].files, function(i, file) {
					formData.append(tag.name, file);
		        });
		        jQuery.each(jQuery('form[name="EditView"]').serializeArray(), function(i, val){
					formData.append(val.name, val.value);
				});
		    });
			formData.append('module', 'Processes');
			formData.append('action', 'ProcessesAjax');
			formData.append('file', 'DetailViewAjax');
			formData.append('ajxaction', 'DYNAFORMCONDITIONALS');
			formData.append('mode', 'edit');
			formData.append('record', record);
			formData.append('dynaform', JSON.stringify(form));
			
			jQuery.ajax({
				'url': 'index.php',
				'type': 'POST',
				'dataType': 'json',
				data: formData,
				cache: false,
		        contentType: false,
		        processData: false,
				success: function(data) {
					fieldname = data['fieldname'];
					fieldlabel = data['fieldlabel'];
					fielddatatype = data['datatype'];
					fielduitype = data['fielduitype'];
					fieldwstype = data['fieldwstype'];
					
					jQuery.each(data['html'], function(blockid,html){
						jQuery('.blockrow_'+blockid).show();
						if (jQuery('#displayfields_'+blockid).length > 0) {
							//jQuery('#displayfields_'+blockid).html(html);
							var textArea = document.createElement('textarea'); textArea.innerHTML = html;
							jQuery('#displayfields_'+blockid).html(textArea.value);
						}
					});
					jQuery.each(data['block_visibility'], function(blockid,value){
						if (value == 0) jQuery('.blockrow_'+blockid).hide();
					});
					
					DynaFormScript.initEditViewConditionals(record, json_fields);
					jQuery('#status').hide();
					VtigerJS_DialogBox.unblock();
					DynaFormScript.reloadingForm = false;
					
					if (fieldFocused && me.fieldNameFocused != false) {
						me.fieldFocused = false;
						jQuery('[name="'+me.fieldNameFocused+'"]').focus();
					}
					jQuery(document).off('keydown', DynaFormScript.disableKeyboard);
				}
			});
		},
		getForm: function(formObj, fields, seq) {
			var me = this,
				form = {};
			jQuery.each(fields, function(k,field){
				var fieldname = field['fieldname'],
					fieldname_input = fieldname;
				if (typeof(seq) != 'undefined') fieldname_input += '_'+seq;
				if (field['type'] == 'multipicklist') fieldname_input += '[]';

				if (field['type'] == 'boolean') {
					if (jQuery('[name="'+fieldname_input+'"]', formObj).prop('checked') == true)
						form[fieldname] = 1;
					else
						form[fieldname] = 0;
				} else if (field['type'] == 'table') {
					var cont = jQuery('table[name='+fieldname+']'),
						rows = cont.find('.tablefield_rows');
					var table = {};
					/*
					var table = [];
					jQuery.each(rows.find('tr'), function(i,row){
						var name = jQuery(row).find('.tablefield_row_seq').attr('name'),
							tmp = name.split('_'),
							seq = tmp[tmp.length-1];
						table.push(me.getForm(formObj, JSON.parse(field['columns']), seq));
					});
					form[fieldname] = table;
					*/
					table[fieldname] = jQuery('[name="'+fieldname+'"]').val();
					table[fieldname+'_lastrowno'] = jQuery('[name="'+fieldname+'_lastrowno"]').val();
					jQuery('table[name='+fieldname+'] :input').each(function(){
						if (this.type == 'checkbox') {
							if (this.checked) table[this.name] = 1;
							else table[this.name] = 0;
						} else if (this.type == 'select-multiple') {
							table[this.name.replace('[]','')] = jQuery(this).val();
						} else {
							table[this.name] = jQuery(this).val();
						}
					});
					form[fieldname] = table;
				} else {
					form[fieldname] = jQuery('[name="'+fieldname_input+'"]', formObj).val();
				}
			});
			return form;
		},
		// end
		alertDisableAjaxSave: function() {
			// do nothing
			//alert('Click the button EDIT');
		},
		dtlViewAjaxSave: function(fieldLabel,module,uitype,tableName,fieldName,crmId) {
			if (fieldName.indexOf('vcf_') >- 1) {
				DynaFormScript.setFieldDataType(crmId, function(){
					dtlViewAjaxSave(fieldLabel,module,uitype,tableName,fieldName,crmId);
				});
			} else {
				dtlViewAjaxSave(fieldLabel,module,uitype,tableName,fieldName,crmId);
			}
		},
		setFieldDataType: function(record, callback) {
			var form = {};
			var formObj = jQuery('form[name="DetailView"]');
			jQuery.each(jQuery(formObj).serializeArray(), function(){
				if (this.name.indexOf('vcf_') >- 1) {
					form[this.name] = this.value;
				}
			});
			jQuery.ajax({
				'url': 'index.php?module=Processes&action=ProcessesAjax&file=DetailViewAjax&ajxaction=DYNAFORMCONDITIONALS&mode=detail&record='+record,
				'type': 'POST',
				'dataType': 'json',
				'data': {'dynaform':form},
				success: function(data) {
					fielddatatype = data['datatype'];
					if (typeof(callback) != 'undefined') callback();
				}
			});
		},
		reloadValidationData: function(record, callback) {
			var form = {};
			var formObj = jQuery('form[name="DetailView"]');
			jQuery.each(jQuery(formObj).serializeArray(), function(){
				if (this.name.indexOf('vcf_') >- 1) {
					form[this.name] = this.value;
				}
			});
			jQuery.ajax({
				'url': 'index.php?module=Processes&action=ProcessesAjax&file=DetailViewAjax&ajxaction=VALIDATIONDATA&record='+record,
				'type': 'POST',
				'dataType': 'json',
				success: function(data) {
					eval('fieldname = new Array('+data['fieldname']+')');
					eval('fieldlabel = new Array('+data['fieldlabel']+')');
					eval('fielddatatype = new Array('+data['fielddatatype']+')');
					eval('fielduitype = new Array('+data['fielduitype']+')');
					eval('fieldwstype = new Array('+data['fieldwstype']+')');
					if (typeof(callback) != 'undefined') callback();
				}
			});
		},
		loadDetailViewBlocks: function(record) {
			// reload blocks and validation_data_field*
			DynaFormScript.reloadValidationData(record, function(){
				loadDetailViewBlocks('Processes',record,'','DetailViewBlocks',false,'',false,false,function(){
					changeTab('Processes', record, getCurrentPanelid());
				});
			});
		},
		//crmv@93990
		popup: function(record) {
			openPopup('index.php?module=Processes&action=DetailViewAjax&ajxaction=DYNAFORMPOPUP&record='+record+'&hide_button_list=1&hide_menus=true&page_title=SINGLE_Processes&op_mode=dynaform_popup&skip_footer=true');
		}
		//crmv@93990e
	}
}