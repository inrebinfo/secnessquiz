/**
 * Created by Jck44 on 03.05.2016.
 */
//globals.ClickableObject = ClickableObject;

var debug = false;

var hitOptions = {
    segments: true,
    stroke: true,
    fill: true,
    tolerance: 5
};

var segment, path;
var movePath = false;

function ClickableObject(x, y, width, height, opacity, fillcolor, strokecolor, linewidth, tb, admin, cid){

    var minWidth = 10;
    var minHeight = 10;
    var textbox = tb;
    var _x = x, _y = y, _width = width, _height = height;
    var clickableID = cid;

    path = new Path.Rectangle({
        rectangle: {
            point: new Point(x, y),
            size: [width, height]
        },
        fillColor: fillcolor,
        opacity: opacity / 2,
        strokeWidth: linewidth,
        strokeColor: strokecolor,
        cid : cid,
        textbox: textbox
    });

    if(admin) {
        globals.clickables.push(path);
    }

    path.onMouseEnter = function () {
        this.opacity = opacity;
        project.activeLayer.selected = false;
        if (admin) {
            this.selected = true;
            document.body.style.cursor = "move";
        } else {
            document.body.style.cursor = "pointer";
        }
    };


    path.onMouseLeave = function () {
        this.opacity = opacity / 2;
        project.activeLayer.selected = false;
        document.body.style.cursor = "default";
    };

    path.onMouseMove = function (event) {
        var hitResult = project.hitTest(event.point, hitOptions);
        if (this) {
            path = this;

            if (hitResult.type == 'segment') {
                segment = hitResult.segment;
                if (admin) {
                    globals.changes = true;
                    switch (segment.index) {
                        case 0:
                            document.body.style.cursor = "nesw-resize";
                            break;
                        case 1:
                            document.body.style.cursor = "nwse-resize";
                            break;
                        case 2:
                            document.body.style.cursor = "nesw-resize";
                            break;
                        case 3:
                            document.body.style.cursor = "nwse-resize";
                            break;
                        default:
                            document.body.style.cursor = "default";
                            break;
                    }
                }
            } else if (hitResult.type == 'fill') {
                document.body.style.cursor = admin ? "move" : "pointer";
            }
        }

    };

    path.onMouseDown = function (event) {
        segment = path = null;
        var hitResult = project.hitTest(event.point, hitOptions);

        if (!hitResult)
            return;

        if (hitResult) {
            path = hitResult.item;

            if (hitResult.type == 'segment') {
                segment = hitResult.segment;
            }
        }
        movePath = hitResult.type == 'fill';
        if (movePath)
            project.activeLayer.addChild(hitResult.item);

        if (!admin) {
            var btns = {};
            textbox.buttons.forEach(function (button) {
                /*if (button.givesPoint == "1") {
                 var func = "alert('" + button.alertText + "'); points++; $(this).dialog('close'); checkPoints();";
                 }
                 else {
                 var func = "alert('" + button.alertText + "'); $(this).dialog('close');";
                 }
                 btns[button.text] = new Function('', func);*/


                // if (button.givesPoint == "1") {
                //     // var func = "alert('" + button.alertText + "'); points++; $(this).dialog('close'); checkPoints();";
                //     var func = "points++; $(this).dialog('close'); checkPoints();";
                // }
                // else if (typeof button.redirect != 'undefined') {
                //     var func = "$(location).attr('href', '/web/scene/" + button.redirect + "')";
                // }
                // else {
                //     // var func = "alert('" + button.alertText + "'); $(this).dialog('close');";
                //     var func = "$(this).dialog('close');";
                // }

                var searchParams = new URLSearchParams(window.location.search); //?anything=123
                var scenarioid = searchParams.get("id");

                if (button.redirect != '0') {
                    //add points
                    //redirect
                    var func = "insertPointsToDB('"+button.actionid+"', "+button.userid+", "+button.givesPoint+"); $(location).attr('href', ' " + "view.php?id="+scenarioid+"&sceneid="+button.redirect+"')";
                }
                else {
                    var func = "insertPointsToDB('"+button.actionid+"', "+button.userid+", "+button.givesPoint+"); $(this).dialog('close');";
                }
                btns[button.text] = new Function('', func);

            });
            $("#" + textbox.id).dialog({
                buttons: btns
            });
        }
        //we are in admin!
        else {
            globals.changes = true;
            globals.chosenClickable = clickableID;
            if(!$("#tb-chooser").is(":visible"))
            {
                $("#tb-chooser").show();
            }
            $("#tbID").html(clickableID);            
            $("#sel-tb-chooser").val(this.textbox);                    
        }
    };

    path.onMouseDrag = function (event) {

        if (segment && path.bounds.width >= minWidth && path.bounds.height >= minHeight && admin) {
            globals.changes = true;
            switch (segment.index) {
                case 0:
                    _x = path.position.x += event.delta.x;
                    _width = path.bounds.width -= event.delta.x;
                    _height = path.bounds.height += event.delta.y;
                    break;
                case 1:
                    _x = path.position.x += event.delta.x;
                    _y = path.position.y += event.delta.y;
                    _width = path.bounds.width -= event.delta.x;
                    _height = path.bounds.height -= event.delta.y;
                    break;
                case 2:
                    _y = path.position.y += event.delta.y;
                    _width = path.bounds.width += event.delta.x;
                    _height = path.bounds.height -= event.delta.y;
                    break;
                case 3:
                    _width = path.bounds.width += event.delta.x;
                    _height = path.bounds.height += event.delta.y;
                    break;
                default:
                    break;
            }

            //Very primitive (and flawed) way to make sure the rectangle doesn't get to small
            if (path.bounds.width <= minWidth) {
                _width = path.bounds.width = minWidth + 1;
            }

            if (path.bounds.height <= minHeight) {
                _height = path.bounds.height = minHeight + 1;
            }


        } else if (path && admin) {
            globals.changes = true;
            _x = path.position.x += event.delta.x;
            _y = path.position.y += event.delta.y;
        }
    };
    
}

var canvas = document.getElementById('roomCanvas');


function getMousePos(canvas, evt) {
    var rect = canvas.getBoundingClientRect();
    return {
        x: evt.clientX - rect.left,
        y: evt.clientY - rect.top
    };
}

if(debug){
    canvas.addEventListener('mousemove', function (evt) {
        var mousePos = getMousePos(canvas, evt);
        var message = 'Mouse position: ' + mousePos.x + ',' + mousePos.y;

        //writeMessage(canvas, message);
        $('#mousepos').text(message);
    }, false);
}


function insertPointsToDB(actionid, userid, result) {
    var formData = new FormData();
    formData.append("submitpoints", "true");
    formData.append("actionid", actionid);
    formData.append("userid", userid);
    formData.append("result", result);

    $.ajax({
        type: "POST",
        data: formData,
        url: "savepoints.php",
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        //beforeSend: showAlert,
        //success: transferComplete
        //error: uploaderror
    });
}