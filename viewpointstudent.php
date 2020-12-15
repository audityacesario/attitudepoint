<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
</head>
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

$PAGE->set_url('/mod/attitudepoint/viewpoint.php', array('id' => $cm->id));
require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

//Diverge logging logic at Moodle 2.7
if($CFG->version<2014051200){
	add_to_log($course->id, 'attitudepoint', 'view', "viewpoint.php?id={$cm->id}", $moduleinstance->name, $cm->id);
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
?>
<div class="header">
    <?php
    $id_curent_user = $USER -> id;
    $id_mod = $cm->id;
    
    $namalengkap = $_GET['namalengkap'];
    $id_mhs = $_GET['id_mhs'];
    $id_mod = $_GET['id'];
    
      $dataket = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_view WHERE idstudent = $id_curent_user && idmodules = $id_mod");
      $dkt=mysqli_fetch_array($dataket);
      $id_student = $dkt['idstudent'];
      $poin = $dkt['point'];
      $idview = $dkt['id'];
      
      $get = mysqli_query($db ,"SELECT * FROM mdl_user WHERE id = $id_curent_user");
      $g = mysqli_fetch_array($get);
      
      
    ?>
                                    
    <h3><b>Profil Mahasiswa</b></h3>
    <br>
    <table width = 60% border =0 cellpadding="12">
        <tr bgcolor=#E6E6FA>
            <td><b>Nama Mahasiswa</b></td>
            <td style="margin-left: 20px;"><?php echo $dkt['firstname']." ".$dkt['lastname'];?></td>
        </tr>
        <tr>
            <td><b>Email</b></td>
            <td><?php echo $g['email']; ?></td>
        </tr>
        <tr bgcolor=#E6E6FA>
            <td><b>Poin Sekarang</b></td>
            <td><?php echo $poin; ?></td>
        </tr>
        <tr>
            <td><b>Terakhir Poin Diubah</b></td>
            <td><?php echo $dkt['date']; ?></td>
        </tr>
        
    </table>
    <br>
    <form action="view.php" method="get" >
        <input type="text" value="<?php echo $id_mod; ?>" name="id" hidden="true">
        <input name="btn" type="submit" style="padding: 10px;" value="Kembali" class="btn btn-primary">
    </form>
    <hr>
    <h3><b>Detail Perubahan Poin </b></h3>
    <br>
    
    <form action="viewpoint.php" method="get">
    <table id="example1" class="table table-bordered table-striped" cellspacing="0" width="100%" >
        
                          <thead>
                            <tr>
                              <th>Jenis prilaku</th>
                              <th>Poin penalti</th>
                              <th>Diubah oleh</th>
                              <th>Tanggal diubah</th>
                              <th>Poin sebelumnya</th> 
                              <th>Poin sesudah</th> 
                              <th>Keterangan</th> 
                            </tr>
                          </thead>
                          
                          <tbody>
                              
                                <?php 
                                    $id_mod = $cm->id;
                                    include 'config.php';
                                    $data = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_log WHERE idview = $idview && idmodules = $id_mod");
                                    while($d = mysqli_fetch_array($data)){
                                    $idscore = $d['idscore']; 
                                ?>
                                
                                <tr>
                                    
                                    <?php
                                    $edata = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_score WHERE id = $idscore");
                                    $e = mysqli_fetch_array($edata);
                                    ?>
                                    
                                    <td><?php 
                                        if($d['status'] == "annul"){
                                            echo 'Anulir ';
                                        }
                                        echo $e['behavior'];?>
                                    </td>
                                    <td><?php echo $d['effect'];?></td>
                                    
                                    <td><?php 
                                        $idusr = $d['iduser'];
                                        $fdata = mysqli_query($db ,"SELECT * FROM mdl_user WHERE id = $idusr");
                                        $f = mysqli_fetch_array($fdata);
                                        echo $f['username'];?>
                                    </td>
                                    <td><?php echo $d['date'];?></td>
                                    <td><?php echo $d['scorebefore'];?></td>
                                    <td><?php echo $d['scoreafter'];?></td>
                                    <td><?php echo $d['notes'];?></td>
                                        <input type="text" name="id_mhs" value="<?php echo $id_mhs;?>" hidden="true">
                                        <input type="text" name="namalengkap" value="<?php echo $namalengkap;?>" hidden="true">
                                        <input type="text" name="id" value="<?php echo $id_mod;?>" hidden="true">
                                        <input type="text" name="idlog" value="<?php echo $d['id'];?>" hidden="true">
                                    
                                    
                                </tr>

                                <?php 
                                    }
                                   
                                ?>  

        <!--                    /  -->

                          </tbody>
                        <script>
                            $(document).ready(function(){
                                $('#example1').DataTable();
                            });
                        </script>
                        
        </table>
        </form>
    
</div>
<?php
    
// Finish the page
echo $renderer->footer();?>

