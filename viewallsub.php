    <head>
        <!--        link yg lama-->
<!--        <link rel="stylesheet" href="assets/datatables.net-bs/css/dataTables.bootstrap.min.css">-->
        <!--        jquery yg lama          -->
<!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->
        
        
        <!-- Latest compiled and minified CSS yg baru-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        
        <!-- jQuery library yg baru -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.js"></script>
        
        <script type="text/javascript">
            $(document).ready(function(){
                $("#checkAll").click(function()){
                    if($(this).is(":checked")){
                        $(".checkItem").prop('checked', true);
                    }else{
                        $(".checkItem").prop('checked',false);
                    }
                });
            });
            
        </script>
    </head>
    <body>
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

        $PAGE->set_url('/mod/attitudepoint/viewallsub.php', array('id' => $cm->id));
        require_login($course, true, $cm);
        $modulecontext = context_module::instance($cm->id);

        //Diverge logging logic at Moodle 2.7
        if($CFG->version<2014051200){
                add_to_log($course->id, 'attitudepoint', 'view', "viewallsub.php?id={$cm->id}", $moduleinstance->name, $cm->id);
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
        
        date_default_timezone_set("Asia/Bangkok");
        $tgl = date("Y-m-d h:i:sa");

        $sec = strtotime($tgl); 

        // convert seconds into a specific format 
        $date = date("Y-m-d H:i:s", $sec);
        $id_mod = $cm->id;
        
        
        //aksi tombol update banyak orang
        if(isset($_POST["update_all"]))
        {
            
            //echo "<script type='text/javascript'>alert('Updated Data Successfully!');</script>";
            foreach ($_POST['id_arr'] as $value){
                echo $value;
                if($value){
                    //echo $_POST['poin']."update all";
                    $poin_custom = $_POST['poin_custom'];
                    $katpoin = $_POST['poin'];
                    $keterangan = $_POST['keterangan'];
                    $stat_btn_custom = $_POST['btn_custom_all'];
                    
                    $dataket = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_score");
                    while ($dkt=mysqli_fetch_array($dataket)) {
                        if($katpoin == $dkt['behavior']){
                            $id_score = $dkt['id'];
                            $efek = $dkt['effect'];
                            $tipe = $dkt['type'];
                        }
                    }
                    
                    $dataupdate = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_view WHERE id = $value");
                    $dup = mysqli_fetch_array($dataupdate);
                    $id_user = $USER->id;
                    $poin_before = $dup['point'];
                    $id_mhs = $value;
                    $poinlama = $dup['point'];
                    
                    if($stat_btn_custom == "true"){
                        $hasil = $poinlama + $poin_custom;
                        $efek = $poin_custom;
                        $id_score = 12;
                    }else{
                        $hasil = $poinlama + $efek;
                    }
                    
                    //echo $hasil;
                    mysqli_query($db,"insert into mdl_attitudepoint_log values('','$id_mod','$id_score','$id_mhs','$id_user', '$efek', '$poin_before', '$hasil', 'enabled', 'no annul', '$keterangan', '$date')");
                    mysqli_query($db,"update mdl_attitudepoint_view set point='$hasil' where id = '$id_mhs'");
                    mysqli_query($db,"update mdl_attitudepoint_view set date ='$date' where id = '$id_mhs'");
                }
            }
            
            
        }
        $ids =  $_GET["idlog"];
        echo $ids;
            //tombol aksi satu orang
        if(isset($_POST["update_alone"])){
            //echo "<script type='text/javascript'>alert('Updated Data Successfully!');</script>";
            //if($_GET['id_mhs']){
                //echo $_POST['poin']."update alone";
                    $katpoin = $_POST['poin'];
                    $poin_custom = $_POST['poin_custom'];
                    $stat_btn_custom = $_POST['btn_custom'];
                    $keterangan = $_POST['keterangan'];
                    $id_mhs = $_POST['id_mhs'];
                    
                    $dataket = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_score");
                    while ($dkt=mysqli_fetch_array($dataket)) {
                        if($katpoin == $dkt['behavior']){
                            $id_score = $dkt['id'];
                            $efek = $dkt['effect'];
                            $tipe = $dkt['type'];
                        }
                    }
                    
                    $dataupdate = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_view WHERE id = '$id_mhs'");
                    $dup = mysqli_fetch_array($dataupdate);
                    $id_user = $USER->id;
                    $poin_before = $dup['point'];
                    $poinlama = $dup['point'];
                    
                    if($stat_btn_custom == "true"){
                        $hasil = $poinlama + $poin_custom;
                        $efek = $poin_custom;
                        $id_score = 12;
                    }else{
                        $hasil = $poinlama + $efek;
                    }
                    
                    //echo $hasil;
                    mysqli_query($db,"insert into mdl_attitudepoint_log values('','$id_mod','$id_score','$id_mhs','$id_user', '$efek', '$poin_before', '$hasil', 'enabled', 'no annul', '$keterangan', '$date')");
                    mysqli_query($db,"update mdl_attitudepoint_view set point='$hasil' where id = '$id_mhs'");
                    mysqli_query($db,"update mdl_attitudepoint_view set date ='$date' where id = '$id_mhs'");
                //}
        }
        
        
        
        ?>
        <form action="view.php" method="get" >
            <input type="text" value="<?php echo $id_mod; ?>" name="id" hidden="true">
            <input name="btn" type="submit" style="padding: 10px;" value="Kembali" class="btn btn-primary">
        </form>
        <div> 
            <form id="sectionForm" action="update.php" method='get'>
                
                        <table id="example1" class="table table-bordered table-striped" cellspacing="0" width="100%" >
                          <thead>
                            <tr>
                              <th><input type="checkbox" id="checkAll"></th>
                              <th>Nama Mahasiswa</th>
                              <th>Terakhir diubah</th>
                              <th>Poin Sekarang</th>
                              <th>Ubah</th> 
                            </tr>
                          </thead>
                          
                          <tbody>
                                <?php 
                                    $id_mod = $cm->id;
                                    include 'config.php';
                                    $data = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_view WHERE idmodules = $id_mod");
                                    while($d = mysqli_fetch_array($data)){
                                ?>
                          
                                <tr>
                                    <td><input type="checkbox" class="checkbox" value="<?php echo $d['id']?>" id="<?php echo $d['id']?>" name="id[]"></td>
                                    <td id="nametbl"><?php echo $d['firstname']." ".$d['lastname'];?></td>
                                    <td><?php echo $d['date'];?></td>
                                    <td id=""><?php echo $d['point'];?></td>
                                    <td>  
                                        <a href="update.php?id_mod=<?php echo $cm->id;?>&id=<?php echo $d['id'];?>&btnedit=<?php echo "true";?>&namalengkap=<?php echo $d['firstname']." ".$d['lastname'];?>" class="btn btn-warning" title="Ubah Poin">Update<i class="fa fa-pencil"></i></a>
                                        <a href="viewpoint.php?id=<?php echo $cm->id;?>&id_mhs=<?php echo $d['id'];?>&namalengkap=<?php echo $d['firstname']." ".$d['lastname'];?>" data-placement="top" class="btn btn-default" title="View Point">View<i class="fa fa-upload"></i></a>
                                    </td>
                                </tr>

                                    <!--<a href="">-->  

                                    <!--</a>-->

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
                        
                                <td colspan="6">
                                    <input type="text" value="<?php echo $cm->id; ?>" name="id_mod" hidden="true">
                                    <input type="text" value="<?php echo "true"; ?>" name="updateall" hidden="true">
<!--                                <input type="submit" name="submit" value="Delete" id="deleteAll" class="btn btn-danger">-->
                                    <input type="submit" name="submit" value="Update" id="updateAll" class="btn btn-warning">
                                </td>
                        
                        </table>
                </form>
            </div>
   
<!--  --------------------------------------------      EditModal    --------------------------------------------------->

            <div class="modal fade" id="EditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success">
                                <h3 align="center" class="modal-title" id="myModalLabel"><b>Update Poin Mahasiswa</b></h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><span class="fa fa-close"></span></span></button>   
                            </div>
                            <div class="modal-body">
                                <div class="fetched-data">
                                    <?php
                                    $data3 = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_view WHERE id = $id_edit");
                                    $d3 = mysqli_fetch_array($data3);
                                    ?>
                                    <form action="viewallsub.php" method="post">
                                        <input type="hidden" name="id" value="<?php echo $cm->id; ?>">
                                        <div class="form-group">
                                            <label>Nama mahasiswa</label>
                                            <input id="p1" name="id_mhs" type="text" class="form-control" value="">
                                        </div>
                                        <div class="form-group">
                                            <label>Poin Sekarang</label>
                                            <input disabled id="p2" type="text" class="form-control" name="deskripsi" value="">
                                        </div>
                                        
                                        <div class="form-group">
                                            <select  name="poin" id="poin" required>
                                                <option value=""> Pilih </option>
                                                    <?php 
                                                     $sql=mysqli_query($db, "SELECT * FROM mdl_attitudepoint_score");
                                                     while ($data=mysqli_fetch_array($sql)) {
                                                    ?>
                                                 <option value="<?=$data['behavior']?>"><?=$data['behavior']?></option> 
                                                    <?php
                                                     }
                                                    ?>
                                            </select>
                                        </div>
                                        
                                        <?php
                                        $id_update = $_POST['p1'];
                                        echo $id_update;
                                        ?>
                                        <input name="btn_update_poin" class="btn btn-primary" value="Update" type="submit">
                                    </form>
                                    
                                </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
<!--                                <button type="submit" class="btn btn-warning btn-flat" id="simpan">Update</button>-->
                            </div>
                        </div>
                    </div>
                </div>
<!--            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->
            <script type="text/javascript">
//                $('#EditModal').on('show.bs.modal', function(e) {
//                    var dataID = $(e.relatedTarget).data('id');
//                    $(e.currentTarget).find('input[name="dataID"]').val(dataID);
//                    var firstName = $(e.relatedTarget).data('firstname');
//                    $(e.currentTarget).find('input[name="firstName"]').val(firstName);
//                });
                
//                $(document).ready(function(){
//                  $('.btnedit').on('click', function(){
//                      $('#EditModal').modal('show');
//                      
//                        var id = $(this).data('id');
//                        
//                        //document.getElementById("p1").value = id;
//                  })
//                });
                 
            </script>
            <script
        <script src="https://cdjns.cloudfare.com/ajax/libs/jquery.min.js"></script>
        <script src="script.js"></script>
        <script>
//            
//            $(document).ready(function(){
//               $document.on('click','a[data-role=update]',function(){
//                   alert($(this).data('id'));
//               }); 
//            });
            
            $(document).ready(function(){
                $('#checkAll').click(function(){
                    if(this.checked){
                        $('.checkbox').each(function(){
                           this.checked = true; 
                        });
                    }else{
                        $('.checkbox').each(function(){
                           this.checked = false; 
                        });
                    }
                });
            });
            
//           $('#updateAll').click(function(){
//                
//                if($('input:checkbox:checked').length > 0){
//                    $("#updateAll").prop("disabled", false);
//                }else{
//                    $("#updateAll").prop("disabled", true);
//                }
//            });
            
            $('#deleteAll').click(function(){
                var dataArr = new Array();
                if($('input:checkbox:checked').length > 0){
                    $('input:checkbox:checked').each(function(){
                      dataArr.push($(this).attr('id'));
                      $(this).closest('tr').remove();
                    });
                    
                }else{
                    alert('No record selected');
                }
            });
            
            function sendResponse(dataArr){
                $.ajax({
                   type     : 'post',
                   url      : 'delete.php',
                   data     : {'data' : dataArr},
                   success  : function(response){
                                alert(response);
                            },
                   error    : function(errResponse){
                                alert(errResponse);
                            }
                });
            }
            
        </script>
        <?php
        
        // Finish the page
        echo $renderer->footer();
        ?>
    </body>