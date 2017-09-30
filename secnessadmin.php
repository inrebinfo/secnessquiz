<?php
error_reporting(E_ALL);
//defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
admin_externalpage_setup('secnessmainadmin');
// functionality like processing form submissions goes here
         echo $OUTPUT->header();
echo 'secnessadmin wuhu';
echo $CFG->libdir;

echo '<img src="https://www.xylembassguitar.com/custom-bass-varuna-chicago.jpg">';
echo $OUTPUT->footer();



?>