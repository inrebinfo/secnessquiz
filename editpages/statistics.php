<?php
error_reporting(E_ALL);
//defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../secness_forms.php');
require_once('../locallib.php');
admin_externalpage_setup('secness_statistics');

// functionality like processing form submissions goes here

if(isset($_GET['scenarioid']))
{
    $scenarioid = $_GET['scenarioid'];
    echo $OUTPUT->header();

    $scenario = $DB->get_record('secness_scenarios', array('id' => $scenarioid));

    echo '<h2>'.$scenario->title.'</h2>';
    echo '<p>'.$scenario->description.'</p>';

    echo '<h2>Results</h2>';

    $users = $DB->get_records_select('user', "username <> 'guest'");
    $users_count = count($users);

    $results = $DB->get_records('secness_results', array('scenario_uniqueid' => $scenario->scenario_uniqueid), 'id DESC');

    if(!empty($results))
    {
        $participated_db = $DB->get_records_sql("SELECT * FROM mdl_secness_results GROUP BY userid");

        $participated = count($participated_db);

        echo '<h3>Overall</h3>';
        echo '<table class="table"><tr><th>Total number</th><th>Participated</th><th>Participated in %</th><th>Green</th><th>Yellow</th><th>Red</th></tr>';
        
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

        $participated_percent = round($participated * 100 / $users_count, 1);

        // $passrate = round($green * 100 / $participated, 1);

        echo '<tr><td>'.$users_count.'</td><td>'.$participated.'</td><td>'.$participated_percent.'</td><td>'.$green.'</td><td>'.$yellow.'</td><td>'.$red.'</td></tr>';
        
        echo '</table>';

        echo '<h3>User results</h3>';
        echo '<table class="table"><tr><th>User #</th><th>Time</th><th>Color</th><th>Points</th></tr>';
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
            echo '<tr class="'.$tablecolor.'"><td>'.$result->userid.'</td><td>'.date('d.m.Y, H:i:s', $result->time).'</td><td>'.ucfirst($result->color).'</td><td>'.$points.'/2</td></tr>';
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

    echo $OUTPUT->footer();
}
else {
    echo $OUTPUT->header();

echo '<h2>Show Statistics</h2>';
$result = $DB->get_records('secness_scenarios');
echo '<ul>';
foreach($result as $res)
                            {
    echo '<li><a href="?scenarioid='.$res->id.'">'.$res->title.'</a></li>';
}
echo '</ul>';

echo $OUTPUT->footer();
}

?>