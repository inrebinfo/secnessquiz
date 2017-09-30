<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of secnessquiz
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_secnessquiz
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace secnessquiz with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once('locallib.php');
require_once($CFG->libdir.'/gradelib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... secnessquiz instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('secnessquiz', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $secnessquiz  = $DB->get_record('secnessquiz', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $secnessquiz  = $DB->get_record('secnessquiz', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $secnessquiz->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('secnessquiz', $secnessquiz->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$event = \mod_secnessquiz\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $secnessquiz);
$event->trigger();

// Print the page header.

$PAGE->set_url('/mod/secnessquiz/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($secnessquiz->name));
$PAGE->set_heading(format_string($course->fullname));

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('secnessquiz-'.$somevar);
 */

// Output starts here.
echo $OUTPUT->header();

echo '
<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="css/jquery-ui.structure.min.css">
<link rel="stylesheet" type="text/css" href="css/jquery-ui.theme.min.css">
<script type="text/javascript" src="scripts/jquery-2.1.4.min.js"></script>
<script type="text/javascript" src="scripts/jquery-ui.min.js"></script>
';

// Conditions to show the intro can change to look for own settings or whatever.
if ($secnessquiz->intro) {
    //echo $OUTPUT->box(format_module_intro('secnessquiz', $secnessquiz, $cm->id), 'generalbox mod_introbox', 'secnessquizintro');
}

if(isset($_GET['sceneid']))
{
    $sceneid = $_GET['sceneid'];

    //get sceneinfo
    $scene = $DB->get_record('secness_scenes', array('scenes_uniqueid' => $sceneid));

    echo '<h2>'.$scene->title.'</h2>';
    echo '<p>'.$scene->description.'</p>';

    //get background
    $background = $DB->get_record('secness_sceneimages', array('imgs_uniqueid' => $scene->imgs_uniqueid));
    $bg = 'upload/'.$background->filename;
    $imgsize = getimagesize('upload/'.$background->filename);

    if($scene->isend == '1')
    {
        echo '
        <img src="'.$bg.'" width="'.$imgsize[0].'" height="'.$imgsize[1].'">
        ';

        // $color = $scene->color;
        // $points = 0;

        // if($color == 'red')
        // {
        //     $points = 0;
        // }
        // elseif($color == 'yellow')
        // {
        //     $points = 1;
        // }
        // elseif($color == 'green')
        // {
        //     $points = 2;
        // }

        $result = new stdClass();
        $result->userid = $USER->id;
        $result->scenario_uniqueid = $scene->scenario_uniqueid;
        $result->scene_uniqueid = $scene->scenes_uniqueid;
        $result->color = $scene->color;
        $result->time = time();
        $result->result_uniqueid = 'result_'.secness_random_string(12);

        // echo print_r($result);

        $lastinsertid = $DB->insert_record('secness_results', $result);
        

        // print_r($secnessquiz); die();
        // $grade = new grade_grade();
        
        // $grade->userid = $USER->id;
        // $grade->rawgrade = 2;
        // $grade->finalgrade = 2;

        // secnessquiz_grade_item_update($secnessquiz, $grade);
    }
    else
    {
        //get clickables
        $clickables = $DB->get_records('secness_clickableobject', array('scene_uniqueid' => $sceneid));

        $textboxes = array();
        
        $clickable_objects = array();

        echo '<canvas id="roomCanvas" width="'.$imgsize[0].'" height="'.$imgsize[1].'" style="background: url('.$bg.');"></canvas>';
        
        $tb_js = '';
        $clickable = '';

        foreach($clickables as $click)
        {
            $db_textbox = $DB->get_records('secness_textboxes', array('clickable_uniqueid' => $click->click_uniqueid));

            //print_r($db_textbox); die();

            foreach($db_textbox as $textbox)
            {
                echo '<div id="'.$textbox->tb_uniqueid.'" title="'.$textbox->title.'" style="display:none;">'.$textbox->text.'<div id="additional_'.$textbox->tb_uniqueid.'"><script type="text/javascript">'.$textbox->optional.'</script></div></div>' . "\r\n";
                
                array_push($textboxes, $textbox);

                $actions = $DB->get_records_sql("SELECT a.name, a.alerttext, a.points, a.redirect, a.act_uniqueid FROM mdl_secness_actions as a
                LEFT JOIN mdl_secness_textbox_actions as ta ON ta.act_uniqueid = a.act_uniqueid
                LEFT JOIN mdl_secness_textboxes as tb ON tb.tb_uniqueid = ta.tb_uniqueid
                WHERE tb.tb_uniqueid = '".$textbox->tb_uniqueid."'");

                $tb_actions = '';

                foreach($actions as $action)
                {
                    $tb_actions .= '{"text": "'.$action->name.'", "redirect" : "'.$action->redirect.'", "givesPoint": "'.$action->points.'", "actionid": "'.$action->act_uniqueid.'", "userid": "'.$USER->id.'"},' . "\r\n";
                }
                $tb_js .= '
                var ' . $textbox->tb_uniqueid . ' = {
                    "id": "' . $textbox->tb_uniqueid . '",
                    "buttons": [
                        ' . $tb_actions . '
                    ],
                };' . "\r\n";

                // add clickable element to element-string
                    $clickable .= 'ClickableObject('.$click->x.', '.$click->y.', '.$click->width.', '.$click->height.', '.$click->opacity.', "'.$click->fillcolor.'", "'.$click->strokecolor.'", '.$click->linewidth.', '.$textbox->tb_uniqueid.', false, "'.$click->click_uniqueid.'");' . "\r\n";
            }
        }

        echo '
        <script type="text/javascript" src="scripts/paper-core.min.js"></script>
        <script type="text/javascript" src="scripts/scripts.js"></script>
        <script type="text/javascript">
            window.globals = {
                baseurl: \'' . $CFG->wwwroot . '/\'
            };
            paper.install(window);
            window.onload = function () {
                paper.setup(\'roomCanvas\');

            '.$tb_js.'
            '.$clickable.'

            };
        
        </script>
        ';
    }
}
else
{
    $scenario = $DB->get_record('secness_scenarios', array('scenario_uniqueid' => $secnessquiz->scenario_uniqueid));
    $scene = $DB->get_record('secness_scenes', array('scenes_uniqueid' => $scenario->startscene));

    echo '<h2>'.$scenario->title.'</h2>';
    echo '<p>'.$scenario->description.'</p>';
    echo '<a class="btn btn-primary" href="view.php?id='.$id.'&sceneid='.$scene->scenes_uniqueid.'">Start Scenario</a>';

    echo '<h2>Previous results</h2>';

    $results = $DB->get_records('secness_results', array('userid' => $USER->id, 'scenario_uniqueid' => $scenario->scenario_uniqueid), 'id DESC');

    if(!empty($results))
    {
        echo '<table class="table"><tr><th>Time</th><th>Color</th><th>Points</th></tr>';
        foreach($results as $result)
        {
            $points = 0;
            $tablecolor = '';
            switch($result->color)
            {
                case 'red':
                    $points = 0;
                    $tablecolor = 'bg-danger';
                    break;
                case 'yellow':
                    $points = 1;
                    $tablecolor = 'bg-warning';
                    break;
                case 'green':
                    $points = 2;
                    $tablecolor = 'bg-success';
                    break;
            }
            echo '<tr class="'.$tablecolor.'"><td>'.date('d.m.Y, H:i:s', $result->time).'</td><td>'.ucfirst($result->color).'</td><td>'.$points.'/2</td></tr>';
        }
        echo '</table>';

        echo '<h3>Overall trys</h3>';
        echo '<table class="table"><tr><th># of trys</th><th>Green</th><th>Yellow</th><th>Red</th><th>Pass rate</th></tr>';
        
        $red = 0;
        $yellow = 0;
        $green = 0;

        foreach($results as $result)
        {
            switch($result->color)
            {
                case 'red':
                    $red++;
                    break;
                case 'yellow':
                    $yellow++;
                    break;
                case 'green':
                    $green++;
                    break;
            }
        }

        $overall = $red + $yellow + $green;

        $passrate = round($green * 100 / $overall, 1);

        echo '<tr><td>'.count($results).'</td><td>'.$green.'</td><td>'.$yellow.'</td><td>'.$red.'</td><td>'.$passrate.'/100%</td></tr>';
        echo '</table>';
    }
    else
    {
        echo '<p>No previous results found!</p>';
    }
}


// Finish the page.
echo $OUTPUT->footer();
?>