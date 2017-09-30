<?php
// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();


$ADMIN->add('root', new admin_category('secness_links', 'Secness'));


$ADMIN->add(
    'secness_links',
    new admin_externalpage('secness_scenario',
    'Add/Edit Scenario',
    new moodle_url('/mod/secnessquiz/editpages/scenario.php')
    )
);

$ADMIN->add(
    'secness_links',
    new admin_externalpage('secness_scenes',
    'Add/Edit Scenes',
    new moodle_url('/mod/secnessquiz/editpages/scenes.php')
    )
);

$ADMIN->add(
    'secness_links',
    new admin_externalpage('secness_clickables',
    'Add/Edit Clickables',
    new moodle_url('/mod/secnessquiz/editpages/clickables.php')
    )
);

$ADMIN->add(
    'secness_links',
    new admin_externalpage('secness_textboxes',
    'Add/Edit Textboxes',
    new moodle_url('/mod/secnessquiz/editpages/textboxes.php')
    )
);

$ADMIN->add(
    'secness_links',
    new admin_externalpage('secness_tb_actions',
    'Add/Edit Textbox Actions',
    new moodle_url('/mod/secnessquiz/editpages/tb_actions.php')
    )
);

$ADMIN->add(
    'secness_links',
    new admin_externalpage('secness_backgroundimages',
    'Add/Edit Backgroundimages',
    new moodle_url('/mod/secnessquiz/editpages/backgroundimage.php')
    )
);

$ADMIN->add(
    'secness_links',
    new admin_externalpage('secness_statistics',
    'Statistics',
    new moodle_url('/mod/secnessquiz/editpages/statistics.php')
    )
);

$ADMIN->add(
    'secness_links',
    new admin_externalpage('secness_export',
    'Export Scenario',
    new moodle_url('/mod/secnessquiz/editpages/export.php')
    )
);

$ADMIN->add(
    'secness_links',
    new admin_externalpage('secness_import',
    'Import Scenario',
    new moodle_url('/mod/secnessquiz/editpages/import.php')
    )
);


//$settings = null;

// echo 'blubbbbbbbbbb';
// echo $CFG->libdir;

?>