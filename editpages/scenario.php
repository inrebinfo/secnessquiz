<?php
error_reporting(E_ALL);
//defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../secness_forms.php');
require_once('../locallib.php');
admin_externalpage_setup('secness_scenario');
// functionality like processing form submissions goes here

//Edit mode for actions
if(isset($_GET['mode']) && $_GET['mode'] == 'edit')
{
	$id = (int)$_GET['id'];

    $scenario_db = $DB->get_record('secness_scenarios', array('id' => $id));

    $scenes_db = $DB->get_records('secness_scenes');
    
    $defaultdata = array(
        'scenario_title' => $scenario_db->title,
        'scenario_description' => $scenario_db->description,
        'scenario_id' => $scenario_db->id,
        'old_startscene' => $scenario_db->startscene,
        'scenario_uniqueid' => $scenario_db->scenario_uniqueid
    );

    // $defaultdata['scenes'][] = 'No Redirect';
    foreach($scenes_db as $rec)
	{
		$defaultdata['scenes'][$rec->scenes_uniqueid] = $rec->title;
	}

	$edit_scenario_form = new scenario_edit_form('?mode=edit', $defaultdata);
    
	$edit_scenario_form->set_data(['startscene' => $scenario_db->startscene]);
    

	if ($edit_scenario_form->is_cancelled()) {
        
	}
	else if ($formdata = $edit_scenario_form->get_data()) {
		

		$record_to_add = new stdClass();
		
        $record_to_add->id = $formdata->scenario_id;
		$record_to_add->title = $formdata->scenario_title;
		$record_to_add->description = $formdata->scenario_description;
		$record_to_add->startscene = $formdata->startscene;

		// echo "record to add: ".print_r($record_to_add); die();

        $DB->update_record('secness_scenarios', $record_to_add, false);
        
        if($formdata->startscene != $formdata->old_startscene)
        {
            $old_startscene = $DB->get_record('secness_scenes', array('scenes_uniqueid' => $formdata->old_startscene));
            $old_startscene->scenario_uniqueid = '';
            $DB->update_record('secness_scenes', $old_startscene, false);

            $new_startscene = $DB->get_record('secness_scenes', array('scenes_uniqueid' => $formdata->startscene));
            $new_startscene->scenario_uniqueid = $formdata->scenario_uniqueid;
            $DB->update_record('secness_scenes', $new_startscene, false);
        }

        header('Location: scenario.php');
	}
	else {
        // echo "else";
	}
	
	echo $OUTPUT->header();
	
	echo '<h2>Edit Scene</h2>';
	
	$edit_scenario_form->add_action_buttons($cancel = false, $submitlabel = 'Edit Scene');
	$edit_scenario_form->display();
	
	echo $OUTPUT->footer();
}
//Standard mode for actions
else
{
	$record = $DB->get_records('secness_sceneimages');

	$record_scenarios = $DB->get_records('secnessquiz');
	
	// header('Content-type: text/plain');
	// echo print_r($record); die();

	foreach($record as $rec)
	{
		$extradata['images'][$rec->imgs_uniqueid] = $rec->name;
	}

	$add_scenario_form = new scenario_add_form(null, $extradata);
	
	if ($add_scenario_form->is_cancelled()) {
	}
	else if ($formdata = $add_scenario_form->get_data()) {
        
        $sceneid = 'scene_'.secness_random_string(12);
        $scenarioid = 'scenario_'.secness_random_string(12);

		$scenario_to_add = new stdClass();
		
		$scenario_to_add->title = $formdata->scenario_title;
		$scenario_to_add->description = $formdata->scenario_description;
        $scenario_to_add->startscene = $sceneid;
        $scenario_to_add->scenario_uniqueid = $scenarioid;

		$lastinsertscenario = $DB->insert_record('secness_scenarios', $scenario_to_add, true);

        
        $scene_to_add = new stdClass();

        $scene_to_add->title = $formdata->scene_title;
        $scene_to_add->description = $formdata->scene_description;
		$scene_to_add->imgs_uniqueid = $formdata->scene_backgroundimage;
        $scene_to_add->isend = $formdata->scene_isend;
		$scene_to_add->scenes_uniqueid = $sceneid;
		$scene_to_add->scenario_uniqueid = $scenarioid;
		
        $lastinsertscene = $DB->insert_record('secness_scenes', $scene_to_add, true);
        
        header('Location: scenario.php');
	}
	else {
		
	}
	echo $OUTPUT->header();
	
	echo '<h2>Insert Scenario</h2>';
	
	$add_scenario_form->add_action_buttons($cancel = false, $submitlabel = 'Add Scenario');
	$add_scenario_form->display();
	
	echo '<h2>Edit Scenarios</h2>';
	$result = $DB->get_records('secness_scenarios');
	echo '<ul>';
	foreach($result as $res)
								{
		echo '<li><a href="?mode=edit&id='.$res->id.'">'.$res->title.'</a></li>';
	}
	echo '</ul>';
	
	echo $OUTPUT->footer();
}
?>