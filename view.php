<style>
    .button {
        background-color: #4CAF50; /* Hijau */
        border: none;
        color: white;
        padding: 6px 28px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
      }
    .button2 {background-color: #D3D3D3;color: black;}
    .button3 {background-color: #008CBA;}
    .scroll {
        display:block;
        border: 1px solid whitesmoke;
        padding: 10px;
        margin-top:5px;
        width:100%;
        height:60%;
        overflow:scroll;
        
     }
     
</style>
<script>
    function accept() {
                alert ("Data Anda telah ditambahkan untuk mengikuti peraturan poin sikap di kursus ini!")
            }
</script>
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
//include 'config.php'; 19-nov 


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

$PAGE->set_url('/mod/attitudepoint/view.php', array('id' => $cm->id));
require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

//Diverge logging logic at Moodle 2.7
if($CFG->version<2014051200){
	add_to_log($course->id, 'attitudepoint', 'view', "view.php?id={$cm->id}", $moduleinstance->name, $cm->id);
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
//echo $renderer->show_something($someinstancesetting);

if(isset($_GET['btn_daftar'])){
      
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

        //$data = mysqli_query($db, "select count(*) from mdl_attitudepoint_view");
        //$d = mysqli_fetch_array($data);
        //var_dump($d[0]);
        var_dump(mysqli_query($db,"insert into mdl_attitudepoint_view(idstudent, idcourse, idmodules, firstname, lastname, point, date, information) "
                   . "values('$id_curent_user', '$id_course', '$id_mod', '$fn', '$ln', '50', '$date', '')"));
        
        //die("insert into mdl_attitudepoint_view(idstudent, idcourse, idmodules, firstname, lastname, point, date, information) "
          //         . "values('$id_curent_user', '$id_course', '$id_mod', '$fn', '$ln', '50', '$date', '')");
      }

//--------------------------------------------------------------------
$id_curent_user = $USER -> id;
$count_participants = 0;
$id_mod = $cm->id;

//------------------------cek sudah terdaftar atau belom----------------------------
$x = 0;
$participants = 0;
$cekpeserta = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_view WHERE idmodules = $id_mod " );

while($cp = mysqli_fetch_array($cekpeserta)){
    $participants = $participants + 1;
}

$cek = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_view WHERE idmodules = $id_mod && idstudent = $id_curent_user" );

while($ck = mysqli_fetch_array($cek)){
    $x = $x + 1;
    
}

//---------------------------------------------------------------------------------
$student = 0;
$guest = 0;
$else = 0;
$etc = 0;

$cekstudent = mysqli_query($db ,"SELECT * FROM mdl_role_assignments WHERE userid = $id_curent_user" );

while($cs = mysqli_fetch_array($cekstudent)){
    $etc++;
    if($cs['roleid'] == 5){
        $student = $student + 1;
    }elseif ($cs['roleid'] == 6) {
        $guest = $guest + 1;
    }else{
        $else = $else + 1;
    }
    
}
//--------------------------------------------------------------------------------------
?>
<h3> Aturan Poin Sikap</h3>
<br>
<div class="scroll">
    <center>
        <table width = 100% border =0 cellpadding="8">
            <tr bgcolor=#90EE90>
                <td><b>Prilaku</b></td>
                <td><b>Efek</b></td>
                <td><b>Catatan</b></td>
            </tr>
            
            <?php
            $datapoin = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_score");
            while($dp = mysqli_fetch_array($datapoin)){
                ?>
                <tr>
                <td><?php echo $dp['behavior'];?></td>
                <td><?php echo $dp['effect'];?></td>
                <td><?php echo $dp['note'];?></td>
			<?php 
			$tampil = json_encode($dp, JSON_PRETTY_PRINT);
			echo $tampil;
			
			
			?>
            </tr>
            <?php
            }                    
            ?>                 
        </table>
        <br>
    </center>
</div>
<?php
//----------------------------------------------------------------------------------
 
if($x == 0 && $guest == 0 && $etc > 0){
    ?>
    <br>
    <div class="row">
        <div class="" style="margin-left: 200px">
            <a>Dengan ini saya menyatakan menyetujui aturan poin sikap yang tertera di kontrak kuliah.</a>
        </div>
        <br>
    </div>
    <br>
    <div class="row" style="margin-left: 400px">
        <form onsubmit="return accept()" method="get">
            <input type="text" value="<?php echo $cm->id; ?>" name="id" hidden="true">
            <input type="submit" onclick="return confirm('Apakah Anda ingin menyetujui aturan poin sikap yang tertera pada kontrak kuliah?')" value="Saya menyetujui" name="btn_daftar" class="button">
        </form>
    </div>
       
    <?php

}

//-----------------jika user adalah student-----------------------------------------
if($student > 0 && $else == 0){
    echo "student";
    $count_participants = 0;
    $data = mysqli_query($db ,"SELECT * FROM mdl_course_modules WHERE id = $id_mod");
    
    while($d = mysqli_fetch_array($data)){
        
        $id_course = $d['course'];
        $data2 = mysqli_query($db ,"SELECT mdl_context.*
        , mdl_role_assignments.* 
       FROM mdl_context
       INNER JOIN mdl_role_assignments
       on mdl_context.id = mdl_role_assignments.contextid
       WHERE mdl_context.instanceid = $id_course && mdl_role_assignments.roleid = 5");
    
        while($d2 = mysqli_fetch_array($data2)){
            $count_participants ++;
        }
       
    }
    
?>
    <hr>
    
    
    <div class="row">
        <div class="col-lg-1" style="margin-left: 420px">
            <form action="viewpointstudent.php" method="get">
                <input type="text" value="<?php echo $cm->id; ?>" name="id" hidden="true">
                <input type="submit" <?php if ($x == 0 || $guest > 0){ ?> disabled <?php   } ?> value="View my poin" name="btn" class="btn btn-primary">
            </form>
        </div>
    </div>

<?php

//-----------------jika user selain student---------------------------------------------------
}else{
    
    $data = mysqli_query($db ,"SELECT * FROM mdl_course_modules WHERE id = $id_mod");
    
    while($d = mysqli_fetch_array($data)){
        
        $id_course = $d['course'];
        $data2 = mysqli_query($db ,"SELECT mdl_context.*
        , mdl_role_assignments.* 
       FROM mdl_context
       INNER JOIN mdl_role_assignments
       on mdl_context.id = mdl_role_assignments.contextid
       WHERE mdl_context.instanceid = $id_course && mdl_role_assignments.roleid = 5");
    
        while($d2 = mysqli_fetch_array($data2)){
            $count_participants ++;
        }
       
    }
    
?>
    <hr>
    <table width = 100% border =0 cellpadding="12">
        <tr bgcolor=#E6E6FA>
            <td><b>Participants<b></td>
            <td><?php echo $participants; ?></td>
        </tr>
        
        <tr>
            <td><b>Time remaining<b></td>
            <td>Assignment is due</td>
        </tr>
        
    </table>
    <br>
    
    <div class="row">
        <div class="col-lg-1" style="margin-left: 400px">
            <form action="viewallsub.php" method="get">
                <input type="text" value="<?php echo $cm->id; ?>" name="id" hidden="true">
                <?php
                if($participants > 0){
                    ?><input type="submit" value=" View all submissions " name="btn" class="btn btn-primary"><?php
                }
                ?>
                
            </form>
        </div>
        
    </div>

<!-- ------------------------------------------ssss-------------------------------------------------- -->

<!-- ------------------------------------------ssss-------------------------------------------------- -->

<?php
}
    
// Finish the page
echo $renderer->footer();
