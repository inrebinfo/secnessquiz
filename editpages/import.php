<?php
error_reporting(E_ALL);
//defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../secness_forms.php');
require_once('../locallib.php');
admin_externalpage_setup('secness_import');


$import_scenario_form = new import_scenario_form();

if ($import_scenario_form->is_cancelled())
{
}
else if ($formdata = $import_scenario_form->get_data())
{
    $content = $import_scenario_form->get_file_content('filename');
    $name = $import_scenario_form->get_new_filename('filename');

    $success = $import_scenario_form->save_file('filename', '../upload/tempimport.xml', true);    

    $xml = simplexml_load_file('../upload/tempimport.xml');
    
    
    // print_r($xml);

    //insert new scenario to db
    $scenario_to_import = new stdClass();
    $scenario_to_import->title = $xml->title->__toString();
    $scenario_to_import->description = $xml->description->__toString();
    $scenario_to_import->startscene = $xml->startscene->__toString();
    $scenario_to_import->scenario_uniqueid = $xml->scenario_uniqueid->__toString();


    $lastinsertid = $DB->insert_record('secness_scenarios', $scenario_to_import, true);


    // echo $xml->scene[0]->title;

    foreach($xml->scene as $scene)
    {
        $backgroundimage = $scene->backgroundimage;

        //insert backgroundimage (secness_sceneimages)
        $backgroundimage_to_import = new stdClass();
        $backgroundimage_to_import->name = $backgroundimage->name->__toString();
        $backgroundimage_to_import->filename = $backgroundimage->filename->__toString();
        $backgroundimage_to_import->imgs_uniqueid = $backgroundimage->imgs_uniqueid->__toString();

        $lastinsertid = $DB->insert_record('secness_sceneimages', $backgroundimage_to_import, true);        

        //base64 to image
        // function base64_to_jpeg($base64_string, $output_file) {

        $output_file = '../upload/'.$backgroundimage_to_import->filename;

        // open the output file for writing
        $ifp = fopen($output_file,'wb'); 
    
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode(',', $backgroundimage->imagedata);
    
        // we could add validation here with ensuring count( $data ) > 1
        fwrite($ifp, base64_decode($data[1]));
    
        // clean up the file resource
        fclose($ifp); 
    

        //insert scene (secness_scenes)
        $scene_to_import = new stdClass();
        $scene_to_import->scenario_uniqueid = $scene->scenario_uniqueid->__toString();
        $scene_to_import->title = $scene->title->__toString();
        $scene_to_import->description = $scene->description->__toString();
        $scene_to_import->imgs_uniqueid = $scene->imgs_uniqueid->__toString();
        $scene_to_import->isend = $scene->isend->__toString();
        $scene_to_import->color = $scene->color->__toString();
        $scene_to_import->scenes_uniqueid = $scene->scenes_uniqueid->__toString();

        $lastinsertid = $DB->insert_record('secness_scenes', $scene_to_import, true);
        


        foreach($scene->clickableobject as $clickable)
        {
            //insert clickableobject (secness_clickableobject)
            $clickable_to_import = new stdClass();
            $clickable_to_import->scene_uniqueid = $clickable->scene_uniqueid->__toString();
            $clickable_to_import->type = 'rectangle';
            $clickable_to_import->x = $clickable->x->__toString();
            $clickable_to_import->y = $clickable->y->__toString();
            $clickable_to_import->width = $clickable->width->__toString();
            $clickable_to_import->height = $clickable->height->__toString();
            $clickable_to_import->radius = '0';
            $clickable_to_import->opacity = '0.3';
            $clickable_to_import->fillcolor = '#ffffff';
            $clickable_to_import->strokecolor = '#000000';
            $clickable_to_import->linewidth = '1';
            $clickable_to_import->click_uniqueid = $clickable->click_uniqueid->__toString();

            $lastinsertid = $DB->insert_record('secness_clickableobject', $clickable_to_import, true);
            

            foreach($clickable->textbox as $textbox)
            {
                //insert textbox (secness_textboxes)
                $textbox_to_import = new stdClass();
                $textbox_to_import->clickable_uniqueid = $textbox->clickable_uniqueid->__toString();
                $textbox_to_import->title = $textbox->title->__toString();
                $textbox_to_import->text = $textbox->text->__toString();
                $textbox_to_import->optional = $textbox->optional->__toString();
                $textbox_to_import->tb_uniqueid = $textbox->tb_uniqueid->__toString();

                $lastinsertid = $DB->insert_record('secness_textboxes', $textbox_to_import, true);

                foreach($textbox->action as $action)
                {
                    //insert action (secness_actions)
                    $action_to_import = new stdClass();
                    $action_to_import->name = $action->name->__toString();
                    $action_to_import->alerttext = $action->alerttext->__toString();
                    $action_to_import->points = $action->points->__toString();
                    $action_to_import->function = $action->function->__toString();
                    $action_to_import->redirect = $action->redirect->__toString();
                    $action_to_import->act_uniqueid = $action->act_uniqueid->__toString();

                    $lastinsertid = $DB->insert_record('secness_actions', $action_to_import, true);
                }

                foreach($textbox->textbox_action as $tba)
                {
                    //insert textbox_action (secness_textbox_actions)
                    $tba_to_import = new stdClass();
                    $tba_to_import->tb_uniqueid = $tba->tb_uniqueid->__toString();
                    $tba_to_import->act_uniqueid = $tba->act_uniqueid->__toString();
                    $tba_to_import->tba_uniqueid = $tba->tba_uniqueid->__toString();

                    $lastinsertid = $DB->insert_record('secness_textbox_actions', $tba_to_import, true);
                }
            }
        }
    }

    unlink('../upload/tempimport.xml');
    
    header('Location: import.php');
}
else
{
    echo $OUTPUT->header();
	
	echo '<h2>Import Secness Scenario</h2>';
    $import_scenario_form->add_action_buttons($cancel = false, $submitlabel = 'Import Scenario');
	$import_scenario_form->display();
	
	echo $OUTPUT->footer();
}
?>