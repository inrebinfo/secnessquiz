<?php
error_reporting(E_ALL);
//defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../secness_forms.php');
require_once('../locallib.php');
admin_externalpage_setup('secness_scenes');
// functionality like processing form submissions goes here

//Edit mode for actions
if(isset($_GET['mode']) && $_GET['mode'] == 'edit')
{
	$id = (int)$_GET['id'];

	$record = $DB->get_record('secness_scenes', array('id'=>$id));

	$record_imgs = $DB->get_records('secness_sceneimages');
	
	$record_scenarios = $DB->get_records('secness_scenarios');
	
    $extradata = array(
        'title' => $record->title,
        'description' => $record->description,
        'imgs_uniqueid' => $record->imgs_uniqueid,
        'id' => $record->id,
		'isend' => $record->isend,
		'scenes_uniqueid' => $record->scenes_uniqueid,
		'color' => $record->color,
		'scenario_uniqueid' => $record->scenario_uniqueid
	);

	foreach($record_imgs as $rec)
	{
		$extradata['images'][$rec->imgs_uniqueid] = $rec->name;
	}

	foreach($record_scenarios as $rec)
	{
		$extradata['scenarios'][$rec->scenario_uniqueid] = $rec->title;
	}
	
	$edit_scene_form = new scenes_edit_form('?mode=edit', $extradata);

	$edit_scene_form->set_data(['scene_backgroundimage' => $record->imgs_uniqueid]);
	$edit_scene_form->set_data(['isend' => $record->isend]);
	$edit_scene_form->set_data(['color' => $record->color]);
	$edit_scene_form->set_data(['scenario_uniqueid' => $record->scenario_uniqueid]);
	
	if ($edit_scene_form->is_cancelled())
	{
	}
	else if ($formdata = $edit_scene_form->get_data())
	{
		$record_to_add = new stdClass();
		
        $record_to_add->id = $formdata->id;
		$record_to_add->title = $formdata->title;
		$record_to_add->description = $formdata->description;
		$record_to_add->imgs_uniqueid = $formdata->scene_backgroundimage;
        $record_to_add->isend = $formdata->isend;
		$record_to_add->scene_uniqueid = $formdata->scene_uniqueid;
		$record_to_add->color = $formdata->color;
        $record_to_add->scenario_uniqueid = $formdata->scenario_uniqueid;

		// echo "record to add: ".print_r($record_to_add); die();

		$DB->update_record('secness_scenes', $record_to_add, false);
        header('Location: scenes.php');
	}
	else
	{
        // echo "else";
	}
	
	echo $OUTPUT->header();
	
	echo '<h2>Edit Scene</h2>';
	
	$edit_scene_form->add_action_buttons($cancel = false, $submitlabel = 'Edit Scene');
	$edit_scene_form->display();
	
	echo $OUTPUT->footer();
}
//Standard mode for actions
else
{
	$record = $DB->get_records('secness_sceneimages');

	$record_scenarios = $DB->get_records('secness_scenarios');
	
	// header('Content-type: text/plain');
	// echo print_r($record); die();

	foreach($record as $rec)
	{
		$extradata['images'][$rec->imgs_uniqueid] = $rec->name;
	}

	foreach($record_scenarios as $rec)
	{
		$extradata['scenarios'][$rec->scenario_uniqueid] = $rec->title;
	}

	$add_scene_form = new scenes_add_form(null, $extradata);
	
	if ($add_scene_form->is_cancelled()) {
	}
	else if ($formdata = $add_scene_form->get_data()) {
		
		$record_to_add = new stdClass();
		
		$record_to_add->title = $formdata->title;
		$record_to_add->description = $formdata->description;
		$record_to_add->imgs_uniqueid = $formdata->scene_backgroundimage;
        $record_to_add->isend = $formdata->isend;
		$record_to_add->scenes_uniqueid = 'scene_'.secness_random_string(12);
		$record_to_add->color = $formdata->color;
		$record_to_add->scenario_uniqueid = $formdata->scenario_uniqueid;
		
		$lastinsertid = $DB->insert_record('secness_scenes', $record_to_add, true);
        header('Location: scenes.php');
	}
	else {
		
	}
	echo $OUTPUT->header();
	
	echo '<h2>Insert Scene</h2>';
	
	$add_scene_form->add_action_buttons($cancel = false, $submitlabel = 'Add Scene');
	$add_scene_form->display();
	
	echo '<h2>Edit Scenes</h2>';
	$result = $DB->get_records('secness_scenes');
	echo '<ul>';
	foreach($result as $res)
								{
		echo '<li><a href="?mode=edit&id='.$res->id.'">'.$res->title.'</a></li>';
	}
	echo '</ul>';
	
	echo $OUTPUT->footer();
}
?>