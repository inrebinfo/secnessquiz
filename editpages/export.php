<?php
error_reporting(E_ALL);
//defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../secness_forms.php');
require_once('../locallib.php');
admin_externalpage_setup('secness_export');

// functionality like processing form submissions goes here

if(isset($_GET['scenarioid']))
{
    //header('Content-type: text/xml');

    $xml = new XMLWriter();
    // $xml->openURI("php://output");
    $xml->openURI("tempexport.xml");
    $xml->startDocument();
    $xml->setIndent(true);

    $xml->startElement('scenario');

    $scenarioid = $_GET['scenarioid'];
    $scenario = $DB->get_record('secness_scenarios', array('scenario_uniqueid' => $scenarioid));

    //start metadata
    $xml->startElement('metadata');
    $xml->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');

    $xml->startElement('dc:identifier');
        $xml->writeRaw($scenario->scenario_uniqueid);
    $xml->endElement();

    $xml->startElement('dc:format');
        $xml->writeRaw('text/xml');
    $xml->endElement();

    $xml->startElement('dc:type');
        $xml->writeRaw('InteractiveResource');
    $xml->endElement();

    $xml->startElement('dc:language');
        $xml->writeRaw('de-AT');
    $xml->endElement();

    $xml->startElement('dc:title');
        $xml->writeRaw($scenario->title);
    $xml->endElement();

    $xml->startElement('dc:subject');
        $xml->writeRaw('e-learning, secness, scenario');
    $xml->endElement();

    $xml->startElement('dc:coverage');
        $xml->writeRaw('WWW, '.date('Y'));
    $xml->endElement();

    $xml->startElement('dc:description');
        $xml->writeRaw($scenario->description);
    $xml->endElement();

    $xml->startElement('dc:creator');
        $xml->writeRaw('Bernhard Punz');
    $xml->endElement();

    $xml->startElement('dc:publisher');
        $xml->writeRaw('Moodle');
    $xml->endElement();

    $xml->startElement('dc:contributor');
        $xml->writeRaw('None');
    $xml->endElement();

    $xml->startElement('dc:rights');
        $xml->writeRaw('GNU GPLv3');
    $xml->endElement();

    $xml->startElement('dc:source');
        $xml->writeRaw('None');
    $xml->endElement();

    $xml->startElement('dc:relation');
        $xml->writeRaw('None');
    $xml->endElement();

    $xml->startElement('dc:date');
        $xml->writeRaw(date('Y-m-d'));
    $xml->endElement();

    $xml->endElement();
    //end metadata

    $xml->startElement('title');
        $xml->writeRaw($scenario->title);
    $xml->endElement();

    $xml->startElement('description');
        $xml->writeRaw($scenario->description);
    $xml->endElement();
    
    $xml->startElement('startscene');
        $xml->writeRaw($scenario->startscene);
    $xml->endElement();

    $xml->startElement('scenario_uniqueid');
        $xml->writeRaw($scenario->scenario_uniqueid);
    $xml->endElement();

    $scenes = $DB->get_records('secness_scenes', array('scenario_uniqueid' => $scenario->scenario_uniqueid));

    foreach($scenes as $scene)
    {
        //echo print_r($scene).'<br>';

        $xml->startElement('scene');

        $xml->startElement('scenario_uniqueid');
            $xml->writeRaw($scene->scenario_uniqueid);
        $xml->endElement();

        $xml->startElement('title');
            $xml->writeRaw($scene->title);
        $xml->endElement();

        $xml->startElement('description');
            $xml->writeRaw($scene->description);
        $xml->endElement();

        $xml->startElement('imgs_uniqueid');
            $xml->writeRaw($scene->imgs_uniqueid);
        $xml->endElement();

        $xml->startElement('isend');
            $xml->writeRaw($scene->isend);
        $xml->endElement();

        $xml->startElement('color');
            $xml->writeRaw($scene->color);
        $xml->endElement();

        $xml->startElement('scenes_uniqueid');
            $xml->writeRaw($scene->scenes_uniqueid);
        $xml->endElement();


        $backgroundimage = $DB->get_record('secness_sceneimages', array('imgs_uniqueid' => $scene->imgs_uniqueid));

        $type = pathinfo('../upload/'.$backgroundimage->filename, PATHINFO_EXTENSION);
        $data = file_get_contents('../upload/'.$backgroundimage->filename);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $xml->startElement('backgroundimage');
        
        $xml->startElement('name');
            $xml->writeRaw($backgroundimage->name);
        $xml->endElement();

        $xml->startElement('filename');
            $xml->writeRaw($backgroundimage->filename);
        $xml->endElement();

        $xml->startElement('imgs_uniqueid');
            $xml->writeRaw($backgroundimage->imgs_uniqueid);
        $xml->endElement();

        $xml->startElement('imagedata');
            $xml->writeRaw($base64);
        $xml->endElement();

        //end backgroundimage element
        $xml->endElement();


        // echo print_r($base64).'<br>';

        $clickables = $DB->get_records('secness_clickableobject', array('scene_uniqueid' => $scene->scenes_uniqueid));

        foreach($clickables as $clickable)
        {
            $xml->startElement('clickableobject');

            $xml->startElement('scene_uniqueid');
                $xml->writeRaw($clickable->scene_uniqueid);
            $xml->endElement();

            $xml->startElement('x');
                $xml->writeRaw($clickable->x);
            $xml->endElement();

            $xml->startElement('y');
                $xml->writeRaw($clickable->y);
            $xml->endElement();

            $xml->startElement('width');
                $xml->writeRaw($clickable->width);
            $xml->endElement();

            $xml->startElement('height');
                $xml->writeRaw($clickable->height);
            $xml->endElement();

            $xml->startElement('click_uniqueid');
                $xml->writeRaw($clickable->click_uniqueid);
            $xml->endElement();

            // echo print_r($clickable).'<br>';
            $textboxes = $DB->get_records('secness_textboxes', array('clickable_uniqueid' => $clickable->click_uniqueid));

            foreach($textboxes as $textbox)
            {
                $xml->startElement('textbox');

                $xml->startElement('clickable_uniqueid');
                    $xml->writeRaw($textbox->clickable_uniqueid);
                $xml->endElement();

                $xml->startElement('title');
                    $xml->writeRaw($textbox->title);
                $xml->endElement();

                $xml->startElement('text');
                    $xml->writeRaw($textbox->text);
                $xml->endElement();

                $xml->startElement('optional');
                    $xml->writeRaw($textbox->optional);
                $xml->endElement();

                $xml->startElement('tb_uniqueid');
                    $xml->writeRaw($textbox->tb_uniqueid);
                $xml->endElement();

                // echo print_r($textbox).'<br>';
                $actions = $DB->get_records_sql("SELECT a.name, a.alerttext, a.points, a.redirect, a.act_uniqueid FROM mdl_secness_actions as a
                LEFT JOIN mdl_secness_textbox_actions as ta ON ta.act_uniqueid = a.act_uniqueid
                LEFT JOIN mdl_secness_textboxes as tb ON tb.tb_uniqueid = ta.tb_uniqueid
                WHERE tb.tb_uniqueid = '".$textbox->tb_uniqueid."'");

                foreach($actions as $action)
                {
                    $xml->startElement('action');

                    $xml->startElement('name');
                        $xml->writeRaw($action->name);
                    $xml->endElement();

                    $xml->startElement('alerttext');
                        $xml->writeRaw($action->alerttext);
                    $xml->endElement();

                    $xml->startElement('points');
                        $xml->writeRaw($action->points);
                    $xml->endElement();

                    $xml->startElement('function');
                        if(property_exists($action, 'function'))
                        {
                            $xml->writeRaw($action->function);
                        }
                        else
                        {
                            $xml->writeRaw('');
                        }
                    $xml->endElement();

                    $xml->startElement('redirect');
                        $xml->writeRaw($action->redirect);
                    $xml->endElement();

                    $xml->startElement('act_uniqueid');
                        $xml->writeRaw($action->act_uniqueid);
                    $xml->endElement();

                    //end action element
                    $xml->endElement();
                    // echo print_r($action).'<br>';
                }

                $tb_actions = $DB->get_records('secness_textbox_actions', array('tb_uniqueid' => $textbox->tb_uniqueid));

                foreach($tb_actions as $tba)
                {
                    $xml->startElement('textbox_action');

                    $xml->startElement('tb_uniqueid');
                        $xml->writeRaw($tba->tb_uniqueid);
                    $xml->endElement();

                    $xml->startElement('act_uniqueid');
                        $xml->writeRaw($tba->act_uniqueid);
                    $xml->endElement();

                    $xml->startElement('tba_uniqueid');
                        $xml->writeRaw($tba->tba_uniqueid);
                    $xml->endElement();

                    //end tba element
                    $xml->endElement();
                }

                //end textbox element
                $xml->endElement();
            }
            //end clickableobject element
            $xml->endElement();
        }

    //end scene element
    $xml->endElement();
    }

    //end scenario element
    $xml->endElement();

    $xml->flush();

    $file_url = 'tempexport.xml';

    $filename_dl = str_replace(' ', '-', $scenario->title); // Replaces all spaces with hyphens.
    
    $filename_dl = preg_replace('/[^A-Za-z0-9\-]/', '', $filename_dl); // Removes special chars.

    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary"); 
    header('Content-disposition: attachment; filename="'.$filename_dl.'.xml"'); 
    header('Content-Length: '.filesize($file_url));
    readfile($file_url);

    unlink('tempexport.xml');

}
else {
    echo $OUTPUT->header();

echo '<h2>Export Scenario</h2>';
$result = $DB->get_records('secness_scenarios');
echo '<ul>';
foreach($result as $res)
                            {
    echo '<li><a href="?scenarioid='.$res->scenario_uniqueid.'">'.$res->title.'</a></li>';
}
echo '</ul>';

echo $OUTPUT->footer();
}

?>