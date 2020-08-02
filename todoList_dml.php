<?php

// Data functions (insert, update, delete, form) for table todoList

// This script and data application were generated by AppGini 5.84
// Download AppGini for free from https://bigprof.com/appgini/download/

function todoList_insert() {
	global $Translation;

	// mm: can member insert record?
	$arrPerm = getTablePermissions('todoList');
	if(!$arrPerm[1]) return false;

	$data = array();
	$data['task'] = $_REQUEST['task'];
		if($data['task'] == empty_lookup_value) { $data['task'] = ''; }
	$data['taskReady'] = $_REQUEST['taskReady'];
		if($data['taskReady'] == empty_lookup_value) { $data['taskReady'] = ''; }
	$data['reminder'] = intval($_REQUEST['reminderYear']) . '-' . intval($_REQUEST['reminderMonth']) . '-' . intval($_REQUEST['reminderDay']);
	$data['reminder'] = parseMySQLDate($data['reminder'], '');
	$data['reminder_time'] = $_REQUEST['reminder_time'];
		if($data['reminder_time'] == empty_lookup_value) { $data['reminder_time'] = ''; }
	$data['reminder_time'] = time24($data['reminder_time']);
	$data['prority'] = $_REQUEST['prority'];
		if($data['prority'] == empty_lookup_value) { $data['prority'] = ''; }
	$data['notes'] = $_REQUEST['notes'];
		if($data['notes'] == empty_lookup_value) { $data['notes'] = ''; }
	$data['order'] = $_REQUEST['order'];
		if($data['order'] == empty_lookup_value) { $data['order'] = ''; }
	if($data['task']== '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">" . $Translation['error:'] . " 'Task': " . $Translation['field not null'] . '<br><br>';
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	if($data['taskReady'] == '') $data['taskReady'] = "0";
	if($data['prority'] == '') $data['prority'] = "Low";

	// hook: todoList_before_insert
	if(function_exists('todoList_before_insert')) {
		$args = array();
		if(!todoList_before_insert($data, getMemberInfo(), $args)) { return false; }
	}

	$error = '';
	// set empty fields to NULL
	$data = array_map(function($v) { return ($v === '' ? NULL : $v); }, $data);
	insert('todoList', backtick_keys_once($data), $error);
	if($error)
		die("{$error}<br><a href=\"#\" onclick=\"history.go(-1);\">{$Translation['< back']}</a>");

	$recID = db_insert_id(db_link());

	// hook: todoList_after_insert
	if(function_exists('todoList_after_insert')) {
		$res = sql("select * from `todoList` where `id`='" . makeSafe($recID, false) . "' limit 1", $eo);
		if($row = db_fetch_assoc($res)) {
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args=array();
		if(!todoList_after_insert($data, getMemberInfo(), $args)) { return $recID; }
	}

	// mm: save ownership data
	set_record_owner('todoList', $recID, getLoggedMemberID());

	// if this record is a copy of another record, copy children if applicable
	if(!empty($_REQUEST['SelectedID'])) todoList_copy_children($recID, $_REQUEST['SelectedID']);

	return $recID;
}

function todoList_copy_children($destination_id, $source_id) {
	global $Translation;
	$requests = array(); // array of curl handlers for launching insert requests
	$eo = array('silentErrors' => true);
	$uploads_dir = realpath(dirname(__FILE__) . '/../' . $Translation['ImageFolder']);
	$safe_sid = makeSafe($source_id);

	// launch requests, asynchronously
	curl_batch($requests);
}

function todoList_delete($selected_id, $AllowDeleteOfParents=false, $skipChecks=false) {
	// insure referential integrity ...
	global $Translation;
	$selected_id=makeSafe($selected_id);

	// mm: can member delete record?
	$arrPerm=getTablePermissions('todoList');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='todoList' and pkValue='$selected_id'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='todoList' and pkValue='$selected_id'");
	if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3) { // allow delete?
		// delete allowed, so continue ...
	}else{
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: todoList_before_delete
	if(function_exists('todoList_before_delete')) {
		$args=array();
		if(!todoList_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'];
	}

	sql("delete from `todoList` where `id`='$selected_id'", $eo);

	// hook: todoList_after_delete
	if(function_exists('todoList_after_delete')) {
		$args=array();
		todoList_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("delete from membership_userrecords where tableName='todoList' and pkValue='$selected_id'", $eo);
}

function todoList_update($selected_id) {
	global $Translation;

	// mm: can member edit record?
	$arrPerm=getTablePermissions('todoList');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='todoList' and pkValue='".makeSafe($selected_id)."'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='todoList' and pkValue='".makeSafe($selected_id)."'");
	if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3) { // allow update?
		// update allowed, so continue ...
	}else{
		return false;
	}

	$data['task'] = makeSafe($_REQUEST['task']);
		if($data['task'] == empty_lookup_value) { $data['task'] = ''; }
	if($data['task']=='') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Task': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	$data['taskReady'] = makeSafe($_REQUEST['taskReady']);
		if($data['taskReady'] == empty_lookup_value) { $data['taskReady'] = ''; }
	$data['reminder'] = intval($_REQUEST['reminderYear']) . '-' . intval($_REQUEST['reminderMonth']) . '-' . intval($_REQUEST['reminderDay']);
	$data['reminder'] = parseMySQLDate($data['reminder'], '');
	$data['reminder_time'] = makeSafe($_REQUEST['reminder_time']);
		if($data['reminder_time'] == empty_lookup_value) { $data['reminder_time'] = ''; }
	$data['reminder_time'] = time24($data['reminder_time']);
	$data['prority'] = makeSafe($_REQUEST['prority']);
		if($data['prority'] == empty_lookup_value) { $data['prority'] = ''; }
	$data['notes'] = makeSafe($_REQUEST['notes']);
		if($data['notes'] == empty_lookup_value) { $data['notes'] = ''; }
	$data['order'] = makeSafe($_REQUEST['order']);
		if($data['order'] == empty_lookup_value) { $data['order'] = ''; }
	$data['selectedID'] = makeSafe($selected_id);

	// hook: todoList_before_update
	if(function_exists('todoList_before_update')) {
		$args = array();
		if(!todoList_before_update($data, getMemberInfo(), $args)) { return false; }
	}

	$o = array('silentErrors' => true);
	sql('update `todoList` set       `task`=' . (($data['task'] !== '' && $data['task'] !== NULL) ? "'{$data['task']}'" : 'NULL') . ', `taskReady`=' . (($data['taskReady'] !== '' && $data['taskReady'] !== NULL) ? "'{$data['taskReady']}'" : 'NULL') . ', `reminder`=' . (($data['reminder'] !== '' && $data['reminder'] !== NULL) ? "'{$data['reminder']}'" : 'NULL') . ', `reminder_time`=' . (($data['reminder_time'] !== '' && $data['reminder_time'] !== NULL) ? "'{$data['reminder_time']}'" : 'NULL') . ', `prority`=' . (($data['prority'] !== '' && $data['prority'] !== NULL) ? "'{$data['prority']}'" : 'NULL') . ', `notes`=' . (($data['notes'] !== '' && $data['notes'] !== NULL) ? "'{$data['notes']}'" : 'NULL') . ', `order`=' . (($data['order'] !== '' && $data['order'] !== NULL) ? "'{$data['order']}'" : 'NULL') . " where `id`='".makeSafe($selected_id)."'", $o);
	if($o['error']!='') {
		echo $o['error'];
		echo '<a href="todoList_view.php?SelectedID='.urlencode($selected_id)."\">{$Translation['< back']}</a>";
		exit;
	}


	// hook: todoList_after_update
	if(function_exists('todoList_after_update')) {
		$res = sql("SELECT * FROM `todoList` WHERE `id`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) {
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = $data['id'];
		$args = array();
		if(!todoList_after_update($data, getMemberInfo(), $args)) { return; }
	}

	// mm: update ownership data
	sql("update `membership_userrecords` set `dateUpdated`='" . time() . "' where `tableName`='todoList' and `pkValue`='" . makeSafe($selected_id) . "'", $eo);

}

function todoList_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $ShowCancel = 0, $TemplateDV = '', $TemplateDVP = '') {
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;

	// mm: get table permissions
	$arrPerm=getTablePermissions('todoList');
	if(!$arrPerm[1] && $selected_id=='') { return ''; }
	$AllowInsert = ($arrPerm[1] ? true : false);
	// print preview?
	$dvprint = false;
	if($selected_id && $_REQUEST['dvprint_x'] != '') {
		$dvprint = true;
	}


	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: reminder
	$combo_reminder = new DateCombo;
	$combo_reminder->DateFormat = "mdy";
	$combo_reminder->MinYear = 1900;
	$combo_reminder->MaxYear = 2100;
	$combo_reminder->DefaultDate = parseMySQLDate('', '');
	$combo_reminder->MonthNames = $Translation['month names'];
	$combo_reminder->NamePrefix = 'reminder';
	// combobox: prority
	$combo_prority = new Combo;
	$combo_prority->ListType = 0;
	$combo_prority->MultipleSeparator = ', ';
	$combo_prority->ListBoxHeight = 10;
	$combo_prority->RadiosPerLine = 1;
	if(is_file(dirname(__FILE__).'/hooks/todoList.prority.csv')) {
		$prority_data = addslashes(implode('', @file(dirname(__FILE__).'/hooks/todoList.prority.csv')));
		$combo_prority->ListItem = explode('||', entitiesToUTF8(convertLegacyOptions($prority_data)));
		$combo_prority->ListData = $combo_prority->ListItem;
	}else{
		$combo_prority->ListItem = explode('||', entitiesToUTF8(convertLegacyOptions("Low;;Mid;;High")));
		$combo_prority->ListData = $combo_prority->ListItem;
	}
	$combo_prority->SelectName = 'prority';

	if($selected_id) {
		// mm: check member permissions
		if(!$arrPerm[2]) {
			return "";
		}
		// mm: who is the owner?
		$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='todoList' and pkValue='".makeSafe($selected_id)."'");
		$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='todoList' and pkValue='".makeSafe($selected_id)."'");
		if($arrPerm[2]==1 && getLoggedMemberID()!=$ownerMemberID) {
			return "";
		}
		if($arrPerm[2]==2 && getLoggedGroupID()!=$ownerGroupID) {
			return "";
		}

		// can edit?
		if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3) {
			$AllowUpdate=1;
		}else{
			$AllowUpdate=0;
		}

		$res = sql("SELECT * FROM `todoList` WHERE `id`='" . makeSafe($selected_id) . "'", $eo);
		if(!($row = db_fetch_array($res))) {
			return error_message($Translation['No records found'], 'todoList_view.php', false);
		}
		$combo_reminder->DefaultDate = $row['reminder'];
		$combo_prority->SelectedData = $row['prority'];
		$urow = $row; /* unsanitized data */
		$hc = new CI_Input();
		$row = $hc->xss_clean($row); /* sanitize data */
	} else {
		$combo_prority->SelectedText = ( $_REQUEST['FilterField'][1]=='6' && $_REQUEST['FilterOperator'][1]=='<=>' ? (get_magic_quotes_gpc() ? stripslashes($_REQUEST['FilterValue'][1]) : $_REQUEST['FilterValue'][1]) : "Low");
	}
	$combo_prority->Render();

	ob_start();
	?>

	<script>
		// initial lookup values

		jQuery(function() {
			setTimeout(function() {
			}, 10); /* we need to slightly delay client-side execution of the above code to allow AppGini.ajaxCache to work */
		});
	</script>
	<?php

	$lookups = str_replace('__RAND__', $rnd1, ob_get_contents());
	ob_end_clean();


	// code for template based detail view forms

	// open the detail view template
	if($dvprint) {
		$template_file = is_file("./{$TemplateDVP}") ? "./{$TemplateDVP}" : './templates/todoList_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	}else{
		$template_file = is_file("./{$TemplateDV}") ? "./{$TemplateDV}" : './templates/todoList_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'TodoList details', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', ($_REQUEST['Embedded'] ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($AllowInsert) {
		if(!$selected_id) $templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return todoList_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return todoList_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	}else{
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if($_REQUEST['Embedded']) {
		$backAction = 'AppGini.closeParentModal(); return false;';
	}else{
		$backAction = '$j(\'form\').eq(0).attr(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($selected_id) {
		if(!$_REQUEST['Embedded']) $templateCode = str_replace('<%%DVPRINT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" onclick="$j(\'form\').eq(0).prop(\'novalidate\', true); document.myform.reset(); return true;" title="' . html_attr($Translation['Print Preview']) . '"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($AllowUpdate) {
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return todoList_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		}else{
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		}
		if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3) { // allow delete?
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '<button type="submit" class="btn btn-danger" id="delete" name="delete_x" value="1" onclick="return confirm(\'' . $Translation['are you sure?'] . '\');" title="' . html_attr($Translation['Delete']) . '"><i class="glyphicon glyphicon-trash"></i> ' . $Translation['Delete'] . '</button>', $templateCode);
		}else{
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		}
		$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>', $templateCode);
	}else{
		$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		$templateCode = str_replace('<%%DESELECT_BUTTON%%>', ($ShowCancel ? '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>' : ''), $templateCode);
	}

	// set records to read only if user can't insert new records and can't edit current record
	if(($selected_id && !$AllowUpdate && !$AllowInsert) || (!$selected_id && !$AllowInsert)) {
		$jsReadOnly .= "\tjQuery('#task').replaceWith('<div class=\"form-control-static\" id=\"task\">' + (jQuery('#task').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#taskReady').replaceWith('<div class=\"form-control-static\" id=\"taskReady\">' + (jQuery('#taskReady').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#reminder').prop('readonly', true);\n";
		$jsReadOnly .= "\tjQuery('#reminderDay, #reminderMonth, #reminderYear').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#reminder_time').replaceWith('<div class=\"form-control-static\" id=\"reminder_time\">' + (jQuery('#reminder_time').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#prority').replaceWith('<div class=\"form-control-static\" id=\"prority\">' + (jQuery('#prority').val() || '') + '</div>'); jQuery('#prority-multi-selection-help').hide();\n";
		$jsReadOnly .= "\tjQuery('#order').replaceWith('<div class=\"form-control-static\" id=\"order\">' + (jQuery('#order').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	}elseif($AllowInsert) {
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
		$jsEditable .= "\tjQuery('#reminder_time').addClass('always_shown').timepicker({ defaultTime: false, showSeconds: true, showMeridian: true, showInputs: false, disableFocus: true, minuteStep: 5 });";
			$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode = str_replace('<%%COMBO(reminder)%%>', ($selected_id && !$arrPerm[3] ? '<div class="form-control-static">' . $combo_reminder->GetHTML(true) . '</div>' : $combo_reminder->GetHTML()), $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(reminder)%%>', $combo_reminder->GetHTML(true), $templateCode);
	$templateCode = str_replace('<%%COMBO(prority)%%>', $combo_prority->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(prority)%%>', $combo_prority->SelectedData, $templateCode);

	/* lookup fields array: 'lookup field name' => array('parent table name', 'lookup field caption') */
	$lookup_fields = array();
	foreach($lookup_fields as $luf => $ptfc) {
		$pt_perm = getTablePermissions($ptfc[0]);

		// process foreign key links
		if($pt_perm['view'] || $pt_perm['edit']) {
			$templateCode = str_replace("<%%PLINK({$luf})%%>", '<button type="button" class="btn btn-default view_parent hspacer-md" id="' . $ptfc[0] . '_view_parent" title="' . html_attr($Translation['View'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-eye-open"></i></button>', $templateCode);
		}

		// if user has insert permission to parent table of a lookup field, put an add new button
		if($pt_perm['insert'] && !$_REQUEST['Embedded']) {
			$templateCode = str_replace("<%%ADDNEW({$ptfc[0]})%%>", '<button type="button" class="btn btn-success add_new_parent hspacer-md" id="' . $ptfc[0] . '_add_new" title="' . html_attr($Translation['Add New'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-plus-sign"></i></button>', $templateCode);
		}
	}

	// process images
	$templateCode = str_replace('<%%UPLOADFILE(id)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(task)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(taskReady)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(reminder)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(reminder_time)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(prority)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(notes)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(order)%%>', '', $templateCode);

	// process values
	if($selected_id) {
		if( $dvprint) $templateCode = str_replace('<%%VALUE(id)%%>', safe_html($urow['id']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(id)%%>', html_attr($row['id']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(id)%%>', urlencode($urow['id']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(task)%%>', safe_html($urow['task']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(task)%%>', html_attr($row['task']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(task)%%>', urlencode($urow['task']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(taskReady)%%>', safe_html($urow['taskReady']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(taskReady)%%>', html_attr($row['taskReady']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(taskReady)%%>', urlencode($urow['taskReady']), $templateCode);
		$templateCode = str_replace('<%%VALUE(reminder)%%>', @date('m/d/Y', @strtotime(html_attr($row['reminder']))), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(reminder)%%>', urlencode(@date('m/d/Y', @strtotime(html_attr($urow['reminder'])))), $templateCode);
		$templateCode = str_replace('<%%VALUE(reminder_time)%%>', time12(html_attr($row['reminder_time'])), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(reminder_time)%%>', urlencode(time12($urow['reminder_time'])), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(prority)%%>', safe_html($urow['prority']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(prority)%%>', html_attr($row['prority']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(prority)%%>', urlencode($urow['prority']), $templateCode);
		if($AllowUpdate || $AllowInsert) {
			$templateCode = str_replace('<%%HTMLAREA(notes)%%>', '<textarea name="notes" id="notes" rows="5">' . html_attr($row['notes']) . '</textarea>', $templateCode);
		}else{
			$templateCode = str_replace('<%%HTMLAREA(notes)%%>', '<div id="notes" class="form-control-static">' . $row['notes'] . '</div>', $templateCode);
		}
		$templateCode = str_replace('<%%VALUE(notes)%%>', nl2br($row['notes']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(notes)%%>', urlencode($urow['notes']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(order)%%>', safe_html($urow['order']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(order)%%>', html_attr($row['order']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(order)%%>', urlencode($urow['order']), $templateCode);
	}else{
		$templateCode = str_replace('<%%VALUE(id)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(id)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(task)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(task)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(taskReady)%%>', '0', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(taskReady)%%>', urlencode('0'), $templateCode);
		$templateCode = str_replace('<%%VALUE(reminder)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(reminder)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(reminder_time)%%>', time12(''), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(reminder_time)%%>', urlencode(time12('')), $templateCode);
		$templateCode = str_replace('<%%VALUE(prority)%%>', 'Low', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(prority)%%>', urlencode('Low'), $templateCode);
		$templateCode = str_replace('<%%HTMLAREA(notes)%%>', '<textarea name="notes" id="notes" rows="5"></textarea>', $templateCode);
		$templateCode = str_replace('<%%VALUE(order)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(order)%%>', urlencode(''), $templateCode);
	}

	// process translations
	foreach($Translation as $symbol=>$trans) {
		$templateCode = str_replace("<%%TRANSLATION($symbol)%%>", $trans, $templateCode);
	}

	// clear scrap
	$templateCode = str_replace('<%%', '<!-- ', $templateCode);
	$templateCode = str_replace('%%>', ' -->', $templateCode);

	// hide links to inaccessible tables
	if($_REQUEST['dvprint_x'] == '') {
		$templateCode .= "\n\n<script>\$j(function() {\n";
		$arrTables = getTableList();
		foreach($arrTables as $name => $caption) {
			$templateCode .= "\t\$j('#{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\t\$j('#xs_{$name}_link').removeClass('hidden');\n";
		}

		$templateCode .= $jsReadOnly;
		$templateCode .= $jsEditable;

		if(!$selected_id) {
		}

		$templateCode.="\n});</script>\n";
	}

	// ajaxed auto-fill fields
	$templateCode .= '<script>';
	$templateCode .= '$j(function() {';


	$templateCode.="});";
	$templateCode.="</script>";
	$templateCode .= $lookups;

	// handle enforced parent values for read-only lookup fields

	// don't include blank images in lightbox gallery
	$templateCode = preg_replace('/blank.gif" data-lightbox=".*?"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	/* default field values */
	$rdata = $jdata = get_defaults('todoList');
	if($selected_id) {
		$jdata = get_joined_record('todoList', $selected_id);
		if($jdata === false) $jdata = get_defaults('todoList');
		$rdata = $row;
	}
	$templateCode .= loadView('todoList-ajax-cache', array('rdata' => $rdata, 'jdata' => $jdata));

	// hook: todoList_dv
	if(function_exists('todoList_dv')) {
		$args=array();
		todoList_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}
?>