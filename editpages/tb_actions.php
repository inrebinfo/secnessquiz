<?php
error_reporting(E_ALL);
//defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../secness_forms.php');
require_once('../locallib.php');
admin_externalpage_setup('secness_tb_actions');
// functionality like processing form submissions goes here

//Edit mode for actions
if(isset($_GET['mode']) && $_GET['mode'] == 'edit')
{
	$id = (int)$_GET['id'];

	$record = $DB->get_record('secness_actions', array('id'=>$id));

    $defaultdata = array(
        'name' => $record->name,
        'alerttext' => $record->alerttext,
        'points' => $record->points,
        'function' => $record->function,
        'redirect' => $record->redirect,
        'id' => $record->id,
		'act_uniqueid' => $record->act_uniqueid
	);
	
	$record_scenes = $DB->get_records('secness_scenes');
	
	$defaultdata['scenes'][] = 'No Redirect';

	foreach($record_scenes as $rec)
	{
		$defaultdata['scenes'][$rec->scenes_uniqueid] = $rec->title;
	}

	$edit_action_form = new actions_edit_form('?mode=edit', $defaultdata);
	if($record->redirect != "")
	{
		$edit_action_form->set_data(['redirect' => $record->redirect]);
	}
	else{
		$edit_action_form->set_data(['redirect' => '']);
	}
	
	if ($edit_action_form->is_cancelled()) {
	}
	else if ($formdata = $edit_action_form->get_data()) {
		
		$record_to_add = new stdClass();
		
        $record_to_add->id = $formdata->id;
		$record_to_add->name = $formdata->name;
		$record_to_add->alerttext = $formdata->alerttext;
		$record_to_add->points = $formdata->points;
		$record_to_add->function = $formdata->function;
		$record_to_add->redirect = $formdata->redirect;
		$record_to_add->act_uniqueid = $formdata->act_uniqueid;

		
		$DB->update_record('secness_actions', $record_to_add, false);
        header('Location: tb_actions.php');
	}
	else {
	}
	
	echo $OUTPUT->header();
	
	echo '<h2>Edit Action</h2>';
	
	$edit_action_form->add_action_buttons($cancel = false, $submitlabel = 'Edit Action');
	$edit_action_form->display();
	
	echo $OUTPUT->footer();
}
//Standard mode for actions
else
{
	$record_scenes = $DB->get_records('secness_scenes');

	$extradata['scenes'][] = 'No Redirect';
	foreach($record_scenes as $rec)
	{
		$extradata['scenes'][$rec->scenes_uniqueid] = $rec->title;
	}

	$add_action_form = new actions_add_form(null, $extradata);
	
	if ($add_action_form->is_cancelled()) {
	}
	else if ($formdata = $add_action_form->get_data()) {
		
		$record_to_add = new stdClass();
		
		$record_to_add->name = $formdata->name;
		$record_to_add->alerttext = $formdata->alerttext;
		$record_to_add->points = $formdata->points;
		$record_to_add->function = $formdata->function;
		$record_to_add->redirect = $formdata->redirect;
		$record_to_add->act_uniqueid = 'act_'.secness_random_string(12);
		
		$lastinsertid = $DB->insert_record('secness_actions', $record_to_add, true);
        header('Location: tb_actions.php');
	}
	else {
		
	}
	echo $OUTPUT->header();
	
	echo '<h2>Insert Action</h2>';
	
	$add_action_form->add_action_buttons($cancel = false, $submitlabel = 'Add Action');
	$add_action_form->display();
	
	echo '<h2>Edit Action</h2>';
	$result = $DB->get_records('secness_actions');
	echo '<ul>';
	foreach($result as $res)
								{
		echo '<li><a href="?mode=edit&id='.$res->id.'">'.$res->name.'</a></li>';
	}
	echo '</ul>';
	
	echo $OUTPUT->footer();
}
?>