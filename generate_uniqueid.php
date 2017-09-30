<?php
require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('locallib.php');

echo secness_random_string(12);
?>