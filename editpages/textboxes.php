<?php
error_reporting(E_ALL);
//defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../secness_forms.php');
require_once('../locallib.php');
admin_externalpage_setup('secness_textboxes');
// functionality like processing form submissions goes here

//Edit mode for actions
if(isset($_GET['mode']) && $_GET['mode'] == 'edit')
{
	$id = (int)$_GET['id'];

	$record = $DB->get_record('secness_textboxes', array('id'=>$id));

    $defaultdata = array(
        'title' => $record->title,
        'text' => $record->text,
        'optional' => $record->optional,
        'id' => $record->id,
		'clickable_uniqueid' => $record->clickable_uniqueid,
		'tb_uniqueid' => $record->tb_uniqueid
	);
	
	$record_all_actions = $DB->get_records('secness_actions');

	$record_associations = $DB->get_records('secness_textbox_actions', array('tb_uniqueid' => $record->tb_uniqueid));


	$record_textbox_actions = $DB->get_records_sql("SELECT a.name, a.alerttext, a.points, a.redirect, a.act_uniqueid FROM mdl_secness_actions as a
	LEFT JOIN mdl_secness_textbox_actions as ta ON ta.act_uniqueid = a.act_uniqueid
	LEFT JOIN mdl_secness_textboxes as tb ON tb.tb_uniqueid = ta.tb_uniqueid
	WHERE tb.tb_uniqueid = '".$record->tb_uniqueid."'");

	$preselected = array();

	foreach($record_textbox_actions as $tba)
	{
		$preselected[] = $tba->act_uniqueid;
	}

	foreach($record_all_actions as $rec)
	{
		$defaultdata['actions'][$rec->act_uniqueid] = $rec->name.' - Redirect to: '.$rec->redirect;
	}

	$edit_textbox_form = new textboxes_edit_form('?mode=edit', $defaultdata);

	$edit_textbox_form->set_data(['textboxactions' => $preselected]);

	if ($edit_textbox_form->is_cancelled()) {
        
	}
	else if ($formdata = $edit_textbox_form->get_data()) {

		$record_to_add = new stdClass();
		
        $record_to_add->id = $formdata->id;
		$record_to_add->title = $formdata->title;
		$record_to_add->text = $formdata->text;
		$record_to_add->optional = $formdata->optional;
		
		$DB->update_record('secness_textboxes', $record_to_add, false);

		$textbox = $DB->get_record('secness_textboxes', array('id' => $formdata->id));

		$DB->delete_records('secness_textbox_actions', array('tb_uniqueid' => $textbox->tb_uniqueid));

		$textbox_actions = array();
		
		foreach($formdata->textboxactions as $tba)
		{
			$rec = new stdClass();
			$rec->tb_uniqueid = $textbox->tb_uniqueid;
			$rec->act_uniqueid = $tba;
			$rec->tba_uniqueid = 'tba_'.secness_random_string(12);
			$textbox_actions[] = $rec;
		}

		$DB->insert_records('secness_textbox_actions', $textbox_actions);

        header('Location: textboxes.php');
	}
	else {
        // echo "else";
	}
	
	echo $OUTPUT->header();
	
	echo '<h2>Edit Textbox</h2>';
	
	$edit_textbox_form->add_action_buttons($cancel = false, $submitlabel = 'Edit Textbox');
	$edit_textbox_form->display();
	
	echo $OUTPUT->footer();
}
//Standard mode for actions
else
{
	$record = $DB->get_records('secness_actions');

	$tb_uniqueid = 'tb_'.secness_random_string(12);

	$extradata['tb_uniqueid'] = $tb_uniqueid;
	
	// header('Content-type: text/plain');
	// echo print_r($record); die();

	foreach($record as $rec)
	{
		$extradata['actions'][$rec->act_uniqueid] = $rec->name;
	}


	// header('Content-type: text/plain');
	// echo print_r($extradata); die();

	$add_textbox_form = new textboxes_add_form(null, $extradata);
	
	if ($add_textbox_form->is_cancelled()) {
	}
	else if ($formdata = $add_textbox_form->get_data()) {
		
		$record_to_add = new stdClass();
		
		$record_to_add->title = $formdata->title;
		$record_to_add->text = $formdata->text;
		$record_to_add->optional = $formdata->optional;
		$record_to_add->tb_uniqueid = $tb_uniqueid;
		
		
		$lastinsertid = $DB->insert_record('secness_textboxes', $record_to_add, true);

        $lasttb = $DB->get_record('secness_textboxes', array('id' => $lastinsertid));

		$textbox_actions = array();

		foreach($formdata->textboxactions as $tba)
		{
			$rec = new stdClass();
			$rec->tb_uniqueid = $lasttb->tb_uniqueid;
			$rec->act_uniqueid = $tba;
            $rec->tba_uniqueid = 'tba_'.secness_random_string(12);
			$textbox_actions[] = $rec;
		}

		// header('Content-type: text/plain');
		// echo print_r($textbox_actions); die();
		$DB->insert_records('secness_textbox_actions', $textbox_actions);
		
		

        header('Location: textboxes.php');
	}
	else {
		
	}
	echo $OUTPUT->header();
	
	echo '<h2>Insert Textbox</h2>';
	
	$add_textbox_form->add_action_buttons($cancel = false, $submitlabel = 'Add Textbox');
	$add_textbox_form->display();
	
	echo '<h2>Edit Textboxes</h2>';
	$result = $DB->get_records('secness_textboxes');
	echo '<ul>';
	foreach($result as $res)
								{
		echo '<li><a href="?mode=edit&id='.$res->id.'">'.$res->title.'</a></li>';
	}
	echo '</ul>';
	
	echo $OUTPUT->footer();
}
?>