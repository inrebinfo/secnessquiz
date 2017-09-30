<?php
error_reporting(E_ALL);
//defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../secness_forms.php');
require_once('../locallib.php');
admin_externalpage_setup('secness_backgroundimages');
// functionality like processing form submissions goes here


// mkdir($CFG->dataroot.'/secness/');

$context = context_system::instance();
$PAGE->set_context( $context );

//Edit mode for actions
if(isset($_GET['mode']) && $_GET['mode'] == 'edit')
{
	$id = (int)$_GET['id'];

	$record = $DB->get_record('secness_sceneimages', array('id'=>$id));

    $defaultdata = array(
        'name' => $record->name,
        'filename' => $record->filename,
        'id' => $record->id,
		'imgs_uniqueid' => $record->imgs_uniqueid
    );
	$edit_image_form = new image_edit_form('?mode=edit', $defaultdata);
	
	if ($edit_image_form->is_cancelled()) {
        
	}
	else if ($formdata = $edit_image_form->get_data()) {
		

		$record_to_add = new stdClass();
		
        $record_to_add->id = $formdata->id;
		$record_to_add->name = $formdata->name;
		$record_to_add->filename = $formdata->filename;
		$record_to_add->imgs_uniqueid = $formdata->imgs_uniqueid;

		// echo "record to add: ".print_r($record_to_add); die();

		$DB->update_record('secness_sceneimages', $record_to_add, false);
        header('Location: backgroundimage.php');
	}
	else {
        // echo "else";
	}
	
	echo $OUTPUT->header();
	
	echo '<h2>Edit Sceneimage</h2>';

	$edit_image_form->add_action_buttons($cancel = false, $submitlabel = 'Edit Sceneimage');
	$edit_image_form->display();
	
	echo $OUTPUT->footer();
}
//Standard mode for actions
else
{
	$add_image_form = new image_add_form();
	
	if ($add_image_form->is_cancelled()) {
	}
	else if ($formdata = $add_image_form->get_data()) {
        

        $content = $add_image_form->get_file_content('filename');
        $name = $add_image_form->get_new_filename('filename');

        $splitname = explode('.', $name);

        $newname = $splitname[0].secness_random_string(32).'.'.$splitname[1];

        $override = false;

        // $success = $add_image_form->save_file('filename', $CFG->dataroot.'/secness/'.$name, $override);
        $success = $add_image_form->save_file('filename', '../upload/'.$newname, $override);

		$record_to_add = new stdClass();
		
		$record_to_add->name = $formdata->name;
        $record_to_add->filename = $newname;
		$record_to_add->imgs_uniqueid = 'imgs_'.secness_random_string(12);
        
        // file_save_draft_area_files($draftitemid, $context->id, 'local_filemanager', 'attachment', $itemid);
		
		$lastinsertid = $DB->insert_record('secness_sceneimages', $record_to_add, true);
        header('Location: backgroundimage.php');
	}
	else {
		
	}
	echo $OUTPUT->header();
	
	echo '<h2>Insert Sceneimage</h2>';
    
    // $itemid = 0; // This is used to distinguish between multiple file areas, e.g. different student's assignment submissions, or attachments to different forum posts, in this case we use '0' as there is no relevant id to use
    // // Fetches the file manager draft area, called 'attachments' 
    // $draftitemid = file_get_submitted_draft_itemid('attachments');
    // // Copy all the files from the 'real' area, into the draft area
    // file_prepare_draft_area($draftitemid, $context->id, 'local_filemanager', 'attachment', $itemid, $filemanageropts);
    // // Prepare the data to pass into the form - normally we would load this from a database, but, here, we have no 'real' record to load
    // $entry = new stdClass();
    // $entry->attachments = $draftitemid; // Add the draftitemid to the form, so that 'file_get_submitted_draft_itemid' can retrieve it
    // // --------- 
    // // Set form data
    // // This will load the file manager with your previous files
    // $add_image_form->set_data($entry);

	$add_image_form->add_action_buttons($cancel = false, $submitlabel = 'Add Sceneimage');
	$add_image_form->display();
	
	echo '<h2>Edit Sceneimage</h2>';
	$result = $DB->get_records('secness_sceneimages');
	echo '<ul>';
	foreach($result as $res)
	{
		echo '<li><a href="?mode=edit&id='.$res->id.'">'.$res->name.'</a></li>';
	}
	echo '</ul>';
	
	echo $OUTPUT->footer();
}

?>