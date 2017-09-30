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
<script type="text/javascript" src="scripts/paper-core.min.js"></script>    
<script type="text/javascript" src="scripts/scripts.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="css/jquery-ui.structure.min.css">
<link rel="stylesheet" type="text/css" href="css/jquery-ui.theme.min.css">
<script type="text/javascript" src="scripts/jquery-2.1.4.min.js"></script>
<script type="text/javascript" src="scripts/jquery-ui.min.js"></script>
';

// Conditions to show the intro can change to look for own settings or whatever.
if ($secnessquiz->intro) {
    echo $OUTPUT->box(format_module_intro('secnessquiz', $secnessquiz, $cm->id), 'generalbox mod_introbox', 'secnessquizintro');
}


/*
 * select einträge von $get id
 * zusätzlicher parameter
 *
 */

 if(isset($_GET['']))

// Replace the following lines with you own code.

$bg = "http://wfiles.brothersoft.com/c/cat-photograph_195928-800x600.jpg";

echo '<canvas id="roomCanvas" width="800" height="600" style="background: url('.$bg.');"></canvas>';

// Finish the page.
echo $OUTPUT->footer();
