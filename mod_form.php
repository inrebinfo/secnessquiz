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
 * The main secnessquiz configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_secnessquiz
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once('locallib.php');

/**
 * Module instance settings form
 *
 * @package    mod_secnessquiz
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_secnessquiz_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $DB;

        $scenarios_db = $DB->get_records('secness_scenarios');
        
        $scenarios = array();

        foreach($scenarios_db as $scenario)
        {
            $scenarios[$scenario->scenario_uniqueid] = $scenario->title;
        }

        // echo print_r($scenes); die();

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('secnessquizname', 'secnessquiz'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'secnessquizname', 'secnessquiz');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }


        // Adding the rest of secnessquiz settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        // $mform->addElement('text', 'startscene', 'startscene', array('size' => '64'));
        // $mform->addRule('startscene', 'must numeric', 'required', 'numeric', 'client');


        //$images = $this->_customdata['images'];
        $options = array(
            'multiple' => false,
            'noselectionstring' => 'Search Scenario',
        );
        $mform->addElement('autocomplete', 'scenario_uniqueid', ' Associated Scenario', $scenarios, $options);


        // $mform->addElement('hidden', 'scenario_uniqueid', 0);
		// $mform->setType('scenario_uniqueid', PARAM_NOTAGS);
		// $mform->setDefault('scenario_uniqueid', 'scenario_'.secness_random_string(12));

        //$mform->addElement('header', 'secnessquizfieldset', get_string('secnessquizfieldset', 'secnessquiz'));
        //$mform->addElement('static', 'label2', 'secnessquizsetting2', 'Your secnessquiz fields go here. Replace me!');

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }
}
