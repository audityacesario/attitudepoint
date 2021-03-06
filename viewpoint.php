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
    $annul = "false";
    $annul = $_GET['annul'];
    $id_log_cat = $_GET['idlog'];
    $namalengkap = $_GET['namalengkap'];
    $id_mhs = $_GET['id_mhs'];
    $id_mod = $_GET['id'];
    $id_user = $USER->id;
        date_default_timezone_set("Asia/Bangkok");
        $tgl = date("Y-m-d h:i:sa");

        $sec = strtotime($tgl); 

        // convert seconds into a specific format 
        $date = date("Y-m-d H:i:s", $sec);
    
//    $title = get_string("users");
//    $rs = $DB->get_recordset_select("user", "deleted = 0 AND picture > 0", array(), "lastaccess DESC", user_picture::fields());
// 
//    foreach ($rs as $user) {
//        $full = fullname($user);
//        
//        if($full == $namalengkap){
//            $headerinfo = array('heading' => fullname($user), 'user' => $user, 'usercontext' => $usercontext);
//            $cek = $OUTPUT->context_header($headerinfo, 2);
//            echo $OUTPUT->context_header($headerinfo, 2);
//            
//        }
//         
//    }
//    $rs->close();
    
      $dataket = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_view WHERE id = $id_mhs && idmodules = $id_mod");
      $dkt=mysqli_fetch_array($dataket);
      $id_student = $dkt['idstudent'];
      $poin = $dkt['point'];
      $poin_baru = $dkt['point'];
      
      $get = mysqli_query($db ,"SELECT * FROM mdl_user WHERE id = $id_student");
      $g = mysqli_fetch_array($get);
      
      if($annul=="true"){
//          $id_mhs_1 = $_GET['id_mhs'];
//          $id = $_GET['id'];
//          $id_user = $USER->id;
          
//          $dataket2 = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_view WHERE id = $id_mhs_1 && idmodules = $id_mod");
//          $dkt2=mysqli_fetch_array($dataket2);
//          $id_mhs_2 = $dkt2['idstudent'];
//          $poin = $dkt2['point'];
          
          $id_log_cat = $_GET["idlog"];
          //echo $idl." ".$id_mhs_1." ";
          $dataupdate = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_log WHERE id = '$id_log_cat'");
          $dup = mysqli_fetch_array($dataupdate);
          $idscore = $dup['idscore'];
          $efek = $dup['effect'];
          $ket = $dup['notes'];
          
          $poin_baru = $poin - $efek;
          $efek = $efek - ($efek * 2);
          
          $poin_baru = $poin + $efek;
          
          //$datascore = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_score WHERE id = '$idscore'");
          //$dsc = mysqli_fetch_array($datascore);
          //$efek = $dsc['effect'];
          
          mysqli_query($db,"update mdl_attitudepoint_log set annul ='disabled' where id='$id_log_cat'");
          mysqli_query($db,"insert into mdl_attitudepoint_log values('','$id_mod','$idscore','$id_mhs','$id_user', '$efek', '$poin', '$poin_baru', 'enabled', 'annul', '$ket', '$date')");
          mysqli_query($db,"update mdl_attitudepoint_view set point='$poin_baru' where id='$id_mhs'");
          //mysqli_query($db,"delete from mdl_attitudepoint_log where id='$key'");
          //$poin = $hasil;
          
          echo "<script type='text/javascript'>alert('Annul data successfully!');</script>";
          
      }
    
    
    ?>
                                    
    <h3><b>Profil Mahasiswa</b></h3>
    <br>
    <table width = 60% border =0 cellpadding="12">
        <tr bgcolor=#E6E6FA>
            <td><b>Nama Mahasiswa</b></td>
            <td style="margin-left: 20px;"><?php echo $namalengkap;?></td>
        </tr>
        <tr>
            <td><b>Email</b></td>
            <td><?php echo $g['email']; ?></td>
        </tr>
        <tr bgcolor=#E6E6FA>
            <td><b>Poin Sekarang</b></td>
            <td><?php echo $poin_baru; ?></td>
        </tr>
        <tr>
            <td><b>Terakhir Poin Diubah</b></td>
            <td><?php echo $dkt['date']; ?></td>
        </tr>
        
    </table>
    <br>
    <form action="viewallsub.php" method="get" >
        <input type="text" value="<?php echo $id_mod; ?>" name="id" hidden="true">
        <input name="btn" type="submit" style="padding: 10px;" value="Kembali" class="btn btn-primary">
    </form>
    <hr>
    <h3><b>Detail Perubahan Poin </b></h3>
    <br>
    
    <form method="get">
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
                              <th>Aksi</th> 
                            </tr>
                          </thead>
                          
                          <tbody>
                              
                                <?php 
                                    $id_mod = $cm->id;
                                    include 'config.php';
                                    $data = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_log WHERE idview = $id_mhs && idmodules = $id_mod");
                                    while($d = mysqli_fetch_array($data)){
                                    $idscore = $d['idscore'];   
                                    $id_log = $d['id'];
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
                                    
                                    <td>
                                        <?php 
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
                                        <input type="text" name="idlog" value="<?php echo $id_log;?>" hidden="true">
                                    <td><?php
                                        if($d['annul'] == "enabled"){
                                            ?>
                                            <a onclick="return confirm('Apakah Anda ingin menganulir history data ini?')" href="viewpoint.php?annul=<?php echo "true";?>&id=<?php echo $id_mod;?>&idlog=<?php echo $id_log;?>&id_mhs=<?php echo $id_mhs;?>&namalengkap=<?php echo $namalengkap;?>" data-placement="top" style="color: green" title="Anulir poin">Anulir<i class="fa fa-refresh"></i></a>
                                            <?php
                                        }?>
                                        
                                    </td>
                                    
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

