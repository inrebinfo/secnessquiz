<?php
//moodleform is defined in formslib.php
require_once($CFG->libdir."/formslib.php");

class actions_add_form extends moodleform {
	public function definition() {
		global $CFG;
		
		$mform = $this->_form;
		// 		Don't forget the underscore! 
        $mform->addElement('text', 'name', 'Action Name/Label');
        $mform->setType('name', PARAM_NOTAGS);
        $mform->addRule('name', 'Name is required', 'required');

        $mform->addElement('textarea', 'alerttext', 'Alerttext to show');
        $mform->setType('alerttext', PARAM_NOTAGS);
        $mform->addRule('alerttext', 'Alerttext is required', 'required');

        $mform->addElement('text', 'points', 'Points for this action');
        $mform->setType('points', PARAM_INT);
        $mform->addRule('points', 'Must be numeric and is required', 'required');

        $mform->addElement('textarea', 'function', 'Optional javascript function');
		$mform->setType('function', PARAM_RAW);
		
		$scenes = $this->_customdata['scenes'];
		
		$options = array(
			'multiple' => false,
			'noselectionstring' => 'Search Scenes',
		);
		$mform->addElement('autocomplete', 'redirect', 'Scene to Redirect', $scenes, $options);
        
    }
    function validation($data, $files) {
        return array();
    }
}
class actions_edit_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore! 
		
		$mform->addElement('text', 'name', 'Action Name/Label');
		$mform->setType('name', PARAM_NOTAGS);
		$mform->addRule('name', 'Name is required', 'required');
		$mform->setDefault('name', $this->_customdata['name']);
		
		$mform->addElement('textarea', 'alerttext', 'Alerttext to show');
		$mform->setType('alerttext', PARAM_NOTAGS);
		$mform->addRule('alerttext', 'Alerttext is required', 'required');
		$mform->setDefault('alerttext', $this->_customdata['alerttext']);
		
		$mform->addElement('text', 'points', 'Points for this action');
		$mform->setType('points', PARAM_INT);
		$mform->addRule('points', 'Must be numeric and is required', 'required');
		$mform->setDefault('points', $this->_customdata['points']);
		
		$mform->addElement('textarea', 'function', 'Optional javascript function');
		$mform->setType('function', PARAM_RAW);
		$mform->setDefault('function', $this->_customdata['function']);
		
		$scenes = $this->_customdata['scenes'];
		
		$options = array(
			'multiple' => false,
			'noselectionstring' => 'Search Scenes',
		);
		$mform->addElement('autocomplete', 'redirect', 'Scene to Redirect', $scenes, $options);
		
		$mform->addElement('hidden', 'id', 0);
		$mform->setType('id', PARAM_INT);
		$mform->setDefault('id', $this->_customdata['id']);

        $mform->addElement('hidden', 'act_uniqueid', 0);
		$mform->setType('act_uniqueid', PARAM_NOTAGS);
		$mform->setDefault('act_uniqueid', $this->_customdata['act_uniqueid']);
		
	}
	
	
	function validation($data, $files) {
		return array();
	}
}

class textboxes_add_form extends moodleform {
	public function definition() {
		global $CFG;
		
		$mform = $this->_form;
		//Don't forget the underscore! 
        $mform->addElement('text', 'title', 'Textbox Title');
        $mform->setType('title', PARAM_NOTAGS);
        $mform->addRule('title', 'Title is required', 'required');

        $mform->addElement('textarea', 'text', 'Text to show');
        $mform->setType('text', PARAM_NOTAGS);
		$mform->addRule('text', 'Text is required', 'required');

		$mform->addElement('static', 'tb_uniqueid', 'Unique Textbox ID', $this->_customdata['tb_uniqueid']);

        $mform->addElement('textarea', 'optional', 'Optional javascript function');
        $mform->setType('optional', PARAM_RAW);

        $areanames = $this->_customdata['actions'];

        $options = array(
            'multiple' => true,
            'noselectionstring' => 'Search Textbox Actions',
        );
        $mform->addElement('autocomplete', 'textboxactions', 'Textbox Actions for this Textbox', $areanames, $options);

    }
    function validation($data, $files) {
        return array();
    }
}
class textboxes_edit_form extends moodleform {
	public function definition() {
		global $CFG;
		$mform = $this->_form;
		// 		Don't forget the underscore! 
		        $mform->addElement('text', 'title', 'Textbox Title');
		$mform->setType('title', PARAM_NOTAGS);
		$mform->addRule('title', 'Title is required', 'required');
		$mform->setDefault('title', $this->_customdata['title']);
		
		$mform->addElement('textarea', 'text', 'Text to show');
		$mform->setType('text', PARAM_NOTAGS);
		$mform->addRule('text', 'Text is required', 'required');
		$mform->setDefault('text', $this->_customdata['text']);

		$mform->addElement('static', 'tb_uniqueid', 'Unique Textbox ID', $this->_customdata['tb_uniqueid']);
		
		$mform->addElement('textarea', 'optional', 'Optional javascript function');
		$mform->setType('optional', PARAM_RAW);
		$mform->setDefault('optional', $this->_customdata['optional']);
        
        $areanames = $this->_customdata['actions'];
        
		$options = array(
			'multiple' => true,
			'noselectionstring' => 'Search Textbox Actions',
		);

        $mform->addElement('autocomplete', 'textboxactions', 'Textbox Actions for this Textbox', $areanames, $options);
		
		$mform->addElement('hidden', 'id', 0);
		$mform->setType('id', PARAM_INT);
		$mform->setDefault('id', $this->_customdata['id']);
		
	}
	function validation($data, $files) {
		return array();
	}
}



class image_add_form extends moodleform {
	public function definition() {
		global $CFG;
		
		$mform = $this->_form;
		// 		Don't forget the underscore! 
        $mform->addElement('text', 'name', 'Background Image Name');
        $mform->setType('name', PARAM_NOTAGS);

        $mform->addRule('name', 'Name is required', 'required');
        $mform->addElement('filepicker', 'filename', 'Background Image', null,
        array('maxbytes' => 0, 'accepted_types' => array('.png', '.jpg', '.gif', '.bmp')));

    }
    function validation($data, $files) {
        return array();
    }
}
class image_edit_form extends moodleform {
	public function definition() {
		global $CFG;
		$mform = $this->_form;
		// 		Don't forget the underscore! 
		        $mform->addElement('text', 'name', 'Background Image Name');
		$mform->setType('name', PARAM_NOTAGS);
		$mform->addRule('name', 'Name is required', 'required');
		$mform->setDefault('name', $this->_customdata['name']);
		
		$htmlcode = '<img src="../upload/'.$this->_customdata['filename'].'" width="25%" height="25%">';
		
		$mform->addElement('static', 'picpreview', 'Picture Preview', $htmlcode);
		
		$mform->addElement('hidden', 'id', 0);
		$mform->setType('id', PARAM_INT);
		$mform->setDefault('id', $this->_customdata['id']);
		
		$mform->addElement('hidden', 'filename', 0);
		$mform->setType('filename', PARAM_NOTAGS);
		$mform->setDefault('filename', $this->_customdata['filename']);

        $mform->addElement('hidden', 'imgs_uniqueid', 0);
		$mform->setType('imgs_uniqueid', PARAM_NOTAGS);
		$mform->setDefault('imgs_uniqueid', $this->_customdata['imgs_uniqueid']);
		
		// 		$mform->addElement('filepicker', 'filename', 'Background Image', null,
		        // 		array('maxbytes' => 0, 'accepted_types' => array('.png', '.jpg', '.gif', '.bmp')));
		
		
	}
	function validation($data, $files) {
		return array();
	}
}

class scenes_add_form extends moodleform {
	public function definition() {
		global $CFG;
		
		$mform = $this->_form;
		//Don't forget the underscore! 
        $mform->addElement('text', 'title', 'Scene Title');
        $mform->setType('title', PARAM_NOTAGS);
        $mform->addRule('title', 'Title is required', 'required');

        $mform->addElement('textarea', 'description', 'Scene Description');
        $mform->setType('description', PARAM_NOTAGS);
        $mform->addRule('description', 'Description is required', 'required');

        $images = $this->_customdata['images'];

        $options = array(
            'multiple' => false,
            'noselectionstring' => 'Search Backgroundimage',
        );
        $mform->addElement('autocomplete', 'scene_backgroundimage', 'Background Image for this Scene', $images, $options);

		$mform->addElement('advcheckbox', 'isend', 'Is Endingscene of Scenario', null, array(0, 1));

		$colors = array(
			'' => 'No Color',
			'red' => 'Red - 0 points',
			'yellow' => 'Yellow - 1 point',
			'green' => 'Green - 2 points'
		);

		$mform->addElement('select', 'color', 'Color (only used in Endingscene for grading)', $colors);
        
		$scenarios = $this->_customdata['scenarios'];

        $options = array(
            'multiple' => false,
            'noselectionstring' => 'Search Scenario',
        );
        $mform->addElement('autocomplete', 'scenario_uniqueid', 'Associated Scenario', $scenarios, $options);

    }
    function validation($data, $files) {
        return array();
    }
}

class scenes_edit_form extends moodleform {
	public function definition() {
		global $CFG;
		
		$mform = $this->_form;
		//Don't forget the underscore! 
        $mform->addElement('text', 'title', 'Scene Title');
        $mform->setType('title', PARAM_NOTAGS);
		$mform->addRule('title', 'Title is required', 'required');
		$mform->setDefault('title', $this->_customdata['title']);				

        $mform->addElement('textarea', 'description', 'Scene Description');
        $mform->setType('description', PARAM_NOTAGS);
        $mform->addRule('description', 'Description is required', 'required');
		$mform->setDefault('description', $this->_customdata['description']);		
		
        $images = $this->_customdata['images'];

        $options = array(
            'multiple' => false,
            'noselectionstring' => 'Search Backgroundimage',
        );
        $mform->addElement('autocomplete', 'scene_backgroundimage', 'Background Image for this Scene', $images, $options);

		$mform->addElement('advcheckbox', 'isend', 'Is Endingscene of Scenario', null, array(0, 1));
		
		$colors = array(
			'' => 'No Color',
			'red' => 'Red - 0 points',
			'yellow' => 'Yellow - 1 point',
			'green' => 'Green - 2 points'
		);

		$mform->addElement('select', 'color', 'Color (only used in Endingscene for grading)', $colors);
        
        $scenarios = $this->_customdata['scenarios'];

        $options = array(
            'multiple' => false,
            'noselectionstring' => 'Search Scenario',
        );
		$mform->addElement('autocomplete', 'scenario_uniqueid', 'Associated Scenario', $scenarios, $options);
		
		$mform->addElement('hidden', 'id', 0);
		$mform->setType('id', PARAM_INT);
		$mform->setDefault('id', $this->_customdata['id']);

    }
    function validation($data, $files) {
        return array();
    }
}

class scenario_add_form extends moodleform {
	public function definition() {
		global $CFG;
		
		$mform = $this->_form;
		//Don't forget the underscore! 
        $mform->addElement('text', 'scenario_title', 'Scenario Title');
        $mform->setType('scenario_title', PARAM_NOTAGS);
        $mform->addRule('scenario_title', 'Title is required', 'required');

        $mform->addElement('textarea', 'scenario_description', 'Scenario Description');
        $mform->setType('scenario_description', PARAM_NOTAGS);
        $mform->addRule('scenario_description', 'Description is required', 'required');

		$htmlcode = '<h2>Also add the first scene of the newly created scenario!</h2>';
		
		$mform->addElement('static', 'notice', '', $htmlcode);

		$mform->addElement('text', 'scene_title', 'Scene Title');
        $mform->setType('scene_title', PARAM_NOTAGS);
        $mform->addRule('scene_title', 'Title is required', 'required');

        $mform->addElement('textarea', 'scene_description', 'Scenario Description');
        $mform->setType('scene_description', PARAM_NOTAGS);
		$mform->addRule('scene_description', 'Description is required', 'required');
		
        $mform->addElement('advcheckbox', 'scene_isend', 'Is Endingscene of Scenario', null, array(0, 1));
		
		$images = $this->_customdata['images'];
		
		$options = array(
			'multiple' => false,
			'noselectionstring' => 'Search Backgroundimage',
		);
		$mform->addElement('autocomplete', 'scene_backgroundimage', 'Background Image for this Scene', $images, $options);
		

    }
    function validation($data, $files) {
        return array();
    }
}

class scenario_edit_form extends moodleform {
	public function definition() {
		global $CFG;
		
		$mform = $this->_form;
		//Don't forget the underscore! 
        $mform->addElement('text', 'scenario_title', 'Scenario Title');
        $mform->setType('scenario_title', PARAM_NOTAGS);
		$mform->addRule('scenario_title', 'Title is required', 'required');
		$mform->setDefault('scenario_title', $this->_customdata['scenario_title']);		

        $mform->addElement('textarea', 'scenario_description', 'Scenario Description');
        $mform->setType('scenario_description', PARAM_NOTAGS);
        $mform->addRule('scenario_description', 'Description is required', 'required');
		$mform->setDefault('scenario_description', $this->_customdata['scenario_description']);

		$areanames = $this->_customdata['scenes'];
        
		$options = array(
			'multiple' => false,
			'noselectionstring' => 'Search Scenes',
		);

        $mform->addElement('autocomplete', 'startscene', 'Startscene', $areanames, $options);
		
		$mform->addElement('hidden', 'old_startscene', 0);
		$mform->setType('old_startscene', PARAM_NOTAGS);
		$mform->setDefault('old_startscene', $this->_customdata['old_startscene']);

		$mform->addElement('hidden', 'scenario_uniqueid', 0);
		$mform->setType('scenario_uniqueid', PARAM_NOTAGS);
		$mform->setDefault('scenario_uniqueid', $this->_customdata['scenario_uniqueid']);

		$mform->addElement('hidden', 'scenario_id', 0);
		$mform->setType('scenario_id', PARAM_INT);
		$mform->setDefault('scenario_id', $this->_customdata['scenario_id']);
		
    }
    function validation($data, $files) {
        return array();
    }
}

class import_scenario_form extends moodleform {
	public function definition() {
		global $CFG;
		
		$mform = $this->_form;
		// 		Don't forget the underscore! 
        $mform->addElement('filepicker', 'filename', 'Secness Scenario as .xml', null,
        array('maxbytes' => 0, 'accepted_types' => array('.xml')));

    }
    function validation($data, $files) {
        return array();
    }
}

?>