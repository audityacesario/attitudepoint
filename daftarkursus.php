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
 * Prints a particular instance of attitudepoint
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_attitudepoint
 * @copyright  "2020 Ydham Halid"
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
include 'config.php';


$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // attitudepoint instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('attitudepoint', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance  = $DB->get_record('attitudepoint', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $moduleinstance  = $DB->get_record('attitudepoint', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('attitudepoint', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid',MOD_ATTITUDEPOINT_LANG));
}

$PAGE->set_url('/mod/attitudepoint/daftarkursus.php', array('id' => $cm->id));
require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

//Diverge logging logic at Moodle 2.7
if($CFG->version<2014051200){
	add_to_log($course->id, 'attitudepoint', 'view', "daftarkursus.php?id={$cm->id}", $moduleinstance->name, $cm->id);
}else{
	// Trigger module viewed event.
	$event = \mod_attitudepoint\event\course_module_viewed::create(array(
	   'objectid' => $moduleinstance->id,
	   'context' => $modulecontext
	));
	$event->add_record_snapshot('course_modules', $cm);
	$event->add_record_snapshot('course', $course);
	$event->add_record_snapshot('attitudepoint', $moduleinstance);
	$event->trigger();
} 

//if we got this far, we can consider the activity "viewed"
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

//are we a teacher or a student?
$mode= "view";

/// Set up the page header
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('course');

	//Get an admin settings 
	$config = get_config(MOD_ATTITUDEPOINT_FRANKY);
  	$someadminsetting = $config->someadminsetting;

	//Get an instance setting
	$someinstancesetting = $moduleinstance->someinstancesetting;


//get our javascript all ready to go
//We can omit $jsmodule, but its nice to have it here, 
//if for example we need to include some funky YUI stuff
$jsmodule = array(
	'name'     => 'mod_attitudepoint',
	'fullpath' => '/mod/attitudepoint/module.js',
	'requires' => array()
);
//here we set up any info we need to pass into javascript
$opts =Array();
$opts['someinstancesetting'] = $someinstancesetting;


//this inits the M.mod_attitudepoint thingy, after the page has loaded.
$PAGE->requires->js_init_call('M.mod_attitudepoint.helper.init', array($opts),false,$jsmodule);

//this loads any external JS libraries we need to call
//$PAGE->requires->js("/mod/attitudepoint/js/somejs.js");
///$PAGE->requires->js(new moodle_url('http://www.somewhere.com/some.js'),true);

//This puts all our display logic into the renderer.php file in this plugin
//theme developers can override classes there, so it makes it customizable for others
//to do it this way.
$renderer = $PAGE->get_renderer('mod_attitudepoint');

//From here we actually display the page.
//this is core renderer stuff


//if we are teacher we see tabs. If student we just see the quiz
if(has_capability('mod/attitudepoint:preview',$modulecontext)){
	echo $renderer->header($moduleinstance, $cm, $mode, null, get_string('view', MOD_ATTITUDEPOINT_LANG));
        
}else{
	echo $renderer->notabsheader();
}

//echo $renderer->show_intro($moduleinstance,$cm);

//if we have too many attempts, lets report that.
if($moduleinstance->maxattempts > 0){
	$attempts =  $DB->get_records(MOD_ATTITUDEPOINT_USERTABLE,array('userid'=>$USER->id, MOD_ATTITUDEPOINT_MODNAME.'id'=>$moduleinstance->id));
	if($attempts && count($attempts)<$moduleinstance->maxattempts){
		echo get_string("exceededattempts",MOD_ATTITUDEPOINT_LANG,$moduleinstance->maxattempts);
	}
}

//This is specfic to our renderer
//echo $renderer->show_something($someadminsetting);
echo $renderer->show_something($someinstancesetting);

    date_default_timezone_set("Asia/Bangkok");
    $tgl = date("Y-m-d h:i:sa");
    
    $sec = strtotime($tgl); 
  
    // convert seconds into a specific format 
    $date = date("Y-m-d H:i:s", $sec); 
    $id_curent_user = $USER -> id;
    $id_mod = $cm->id;
    $id_course = "";
    $id_context = "";
    
    $x = 0;
    $data = mysqli_query($db ,"SELECT * FROM mdl_course_modules WHERE id = $id_mod");
    $d = mysqli_fetch_array($data);
    
    $id_course = $d['course'];
    
    $fn = $USER->firstname;
    $ln = $USER->lastname;
       
    mysqli_query($db,"insert into mdl_attitudepoint_view(id, idstudent, idcourse, idmodules, firstname, lastname, point, date, information) "
               . "values('','$id_curent_user', '$id_course', '$id_mod', '$fn', '$ln', '50', '$date', '')");

?>
<div class="row">
        <div class="" style="margin-left: 250px">
            <a>Data Anda telah ditambahkan untuk mengikuti peraturan poin sikap di kursus ini!</a>
        </div>
        
        <div class="col-lg-2" style="margin-left: 10px">
            <form action="view.php" method="post">
                <input type="text" value="<?php echo $cm->id; ?>" name="id" hidden="true">
                <input type="submit" value="Kembali" name="btn_daftar" class="btn btn-success">
            </form>
        </div>
        
        <br>
        
    </div>
<?php

echo $renderer->footer();

