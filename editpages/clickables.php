<?php
error_reporting(E_ALL);
//defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../secness_forms.php');
require_once('../locallib.php');
admin_externalpage_setup('secness_clickables');
// functionality like processing form submissions goes here

if(isset($_GET['mode']) && $_GET['mode'] == 'save')
{
    if(isset($_POST['submitted']))
    {
        $postedData = json_decode($_POST['data']);

        //db object: sceneid, type = rectangle, x, y, width, height, radius = 0, opacity = 0.3, fillcolor = #ffffff, strokecolor = #000000, linewidth = 1

        //get all clickables for scene
        $db_clickables = $DB->get_records('secness_clickableobject', array('scene_uniqueid' => $postedData->sceneid));

        foreach($postedData->clickables as $clickable)
        {
            $DB->execute("UPDATE mdl_secness_textboxes SET clickable_uniqueid = '' WHERE clickable_uniqueid = '".$clickable->cid."'");
            
            if(in_array_field($clickable->cid, 'click_uniqueid', $db_clickables))
            {
                $oldrec = $DB->get_record('secness_clickableobject', array('click_uniqueid' => $clickable->cid));

                $oldrec->x = $clickable->x;
                $oldrec->y = $clickable->y;
                $oldrec->width = $clickable->width;
                $oldrec->height = $clickable->height;

                $DB->update_record('secness_clickableobject', $oldrec);

                //update textbox to clickableid
                $tb_to_update = $DB->get_record('secness_textboxes', array('tb_uniqueid' => $clickable->textbox));

                $tb_to_update->clickable_uniqueid = $clickable->cid;

                $DB->execute("UPDATE mdl_secness_textboxes SET clickable_uniqueid = '".$clickable->cid."' WHERE id = '".$tb_to_update->id."'");
            }
            else
            {
                //insert
                $record_to_add = new stdClass();
                
                $record_to_add->scene_uniqueid = $postedData->sceneid;
                $record_to_add->type = 'rectangle';
                $record_to_add->x = $clickable->x;
                $record_to_add->y = $clickable->y;
                $record_to_add->width = $clickable->width;
                $record_to_add->height = $clickable->height;
                $record_to_add->radius = 0;
                $record_to_add->opacity = '0.3';
                $record_to_add->fillcolor = '#ffffff';
                $record_to_add->strokecolor = '#000000';
                $record_to_add->linewidth = 1;
                $record_to_add->click_uniqueid = $clickable->cid;
                
                $lastinsertid = $DB->insert_record('secness_clickableobject', $record_to_add, true);

                //update textbox to clickableid
                $tb_to_update = $DB->get_record('secness_textboxes', array('tb_uniqueid' => $clickable->textbox));

                $tb_to_update->clickable_uniqueid = $clickable->cid;

                $DB->execute("UPDATE mdl_secness_textboxes SET clickable_uniqueid = '".$clickable->cid."' WHERE id = '".$tb_to_update->id."'");
            }
        }

        //loop each clickable from post
        //if looped is in clickables from db
            //update
        //else
            //insert

        //each in clickables from db that is not in clickables from post (maybe check on unique id)
            //delete
    }
}

//Edit mode for actions
if(isset($_GET['id']))
{
    
    $sceneid = $_GET['id'];
    
    //get scene
    $scene = $DB->get_record('secness_scenes', array('scenes_uniqueid' => $sceneid));

    //load image
    $backgroundimage = $DB->get_record('secness_sceneimages', array('imgs_uniqueid' => $scene->imgs_uniqueid));
    $imgsize = getimagesize('../upload/'.$backgroundimage->filename);
    
    $textboxes = $DB->get_records('secness_textboxes');

    $select_tb_options = '';
    foreach($textboxes as $tb)
    {
        $select_tb_options .= '<option value="'.$tb->tb_uniqueid.'">'.$tb->title.'</option>';
    }


    echo $OUTPUT->header();
    echo '
    <script type="text/javascript" src="../scripts/paper-core.min.js"></script>    
    <script type="text/javascript" src="../scripts/scripts.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.min.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.min.css">
    <script type="text/javascript" src="../scripts/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="../scripts/jquery-ui.min.js"></script>
    ';
	
    echo '<h2>Edit Scene</h2>';
    echo '<span id="btnAdd" class="btn btn-primary">Add Clickable</span> <span id="btnSubmit" class="btn btn-primary">Save</span>
    <br><br><div id="tb-chooser" style="display: none;"><select id="sel-tb-chooser">'.$select_tb_options.'</select> <span id="btnSet" class="btn btn-primary">Set Textbox</span> Unique Clickable ID: <span id="tbID"></span></div><br>';

    //ClickableObject(x, y, width, height, opacity, fillcolor, strokecolor, linewidth, tb, admin=true/false, cid)
    
    $sql_clickables = $DB->get_records('secness_clickableobject', array('scene_uniqueid' => $sceneid));

    $clickables = '';

    foreach ($sql_clickables as $clickable)
    {
        $tb_click = $DB->get_record('secness_textboxes', array('clickable_uniqueid' => $clickable->click_uniqueid));

        $clickables .= 'ClickableObject('.$clickable->x.', '.$clickable->y.', '.$clickable->width.', '.$clickable->height.', 0.6 , "'.$clickable->fillcolor.'", "red", 3 ,  "'.$tb_click->tb_uniqueid.'" , true, "'.$clickable->click_uniqueid.'");' . "\r\n";
    }

    $sid = 0;

    echo '<script>
    window.globals = {
        clickables : [],
        baseurl: \'' . $CFG->wwwroot . '/\',
        changes: false,
        chosenClickable: 0
    };    
    var newbg;    
    paper.install(window);
    window.onload = function() {
        paper.setup(\'roomCanvas\'); ' . $clickables . '
    };

    $(document).ready(function() {
        //debugger;

        $("#btnAdd").click(function() {
            if(!$("#tb-chooser").is(":visible"))
            {
                $("#tb-chooser").show();
            }
            //debugger;

            console.log("before add:" + Object.keys(globals.clickables).length);

            $.get("../generate_uniqueid.php", function(data) {
                clickable = new ClickableObject(10, 10, 150, 150, 0.6, "#ffffff", "red", 2, $("#sel-tb-chooser").val(), true, "clickable_" + data);
                $("#tbID").html("clickable_"+data);
                delete clickable;
            });
        });

        $("#btnSet").click(function() {

            globals.clickables.forEach(function(clickable){   
                if(clickable.cid == globals.chosenClickable)
                {
                    clickable.textbox = $("#sel-tb-chooser").val();
                }
            });
        });

        $("#btnSubmit").click(function(){
            if(globals.changes){
                var allClickables = [];

                globals.clickables.forEach(function(clickable){   
                    var obj = {
                    cid: clickable.cid,
                    x: clickable.bounds.x,
                    y: clickable.bounds.y,
                    width: clickable.bounds.width,
                    height: clickable.bounds.height,
                    textbox: clickable.textbox
                    };

                    allClickables.push(obj);
                });

                // var clickableObjectsJSON = JSON.stringify(allClickables);
                // console.log(clickableObjectsJSON);
                // alert(clickableObjectsJSON);

                var postParam = {
                    sceneid: "'.$sceneid.'",
                    clickables: allClickables
                };

                var postParamJSON = JSON.stringify(postParam);
                console.log(postParamJSON);
                alert(postParamJSON);
                
                //$.post("clickables.php?mode=save", postParamJSON);

                $("#hiddendata").val(postParamJSON);
                $("#sendform").submit();

                //globals.clickables.forEach(function(clickable){   

                    //var xhttp = new XMLHttpRequest();
                    //xhttp.open("GET", "scene.php?mode=edit&sid=' . $sid . '&cid=" + clickable.cid + "&x=" + clickable.bounds.x + "&y=" + clickable.bounds.y + "&w=" + clickable.bounds.width + "&h=" + clickable.bounds.height, true);
                    //xhttp.send();
                   
                //});
                    //var xhttp2 = new XMLHttpRequest();
                    //xhttp2.open("GET", "scene.php?mode=edit&sid=' . $sid . '&bgid=" + newbg , true);
                    //xhttp2.send();
                 alert("Änderungen gespeichert!");
            } else {
                alert("KEINE Änderungen!");
            }
        });
    });        
   
    </script>';
    echo '
    <form id="sendform" action="clickables.php?mode=save" method="post">
    <input type="hidden" name="submitted" value="true">
    <input type="hidden" id="hiddendata" name="data" value="default">
    </form>
    ';
    echo '<canvas id="roomCanvas" width="'.$imgsize[0].'" height="'.$imgsize[1].'" style="background: url(../upload/'.$backgroundimage->filename.');"></canvas>';

	
	
	echo $OUTPUT->footer();
}
//Standard mode for actions
else {
	echo $OUTPUT->header();
	
	echo '<h2>Add/Edit Clickables in Scenes</h2>';
	$result = $DB->get_records('secness_scenes');
	echo '<ul>';
	foreach($result as $res)
								{
		echo '<li><a href="?id='.$res->scenes_uniqueid.'">'.$res->title.'</a></li>';
	}
	echo '</ul>';
	
	echo $OUTPUT->footer();
}
?>