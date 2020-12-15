<!DOCTYPE html>
<html>
    
<head>
        <style>
            .button {
                background-color: #008CBA; /* Hijau */
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
              .button2{
                background-color: #ffd700;
                border: none;
                color: black;
                padding: 6px 28px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                cursor: pointer;
              }

        </style>
	<title></title>
        <link href="Style.css" rel="stylesheet" />
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    
        <script>
            function tampilkanwaktu(){         //fungsi ini akan dipanggil di bodyOnLoad dieksekusi tiap 1000ms = 1detik    
                var waktu = new Date();            //membuat object date berdasarkan waktu saat 
                var sh = waktu.getHours() + "";    //memunculkan nilai jam, //tambahan script + "" supaya variable sh bertipe string sehingga bisa dihitung panjangnya : sh.length    //ambil nilai menit
                var sm = waktu.getMinutes() + "";  //memunculkan nilai detik    
                var ss = waktu.getSeconds() + "";  //memunculkan jam:menit:detik dengan menambahkan angka 0 jika angkanya cuma satu digit (0-9)
                document.getElementById("clock").innerHTML = (sh.length==1?"0"+sh:sh) + ":" + (sm.length==1?"0"+sm:sm) + ":" + (ss.length==1?"0"+ss:ss);
            }
            
            function update() {
                alert ("Updated Data Successfully!")
            }
            
            
        </script>
</head>


<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
include 'config.php';

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // attitudepoint instance ID - it should be named as the first character of the module
       
       

//if(isset($_POST['data'])){
//    $dataArr = $_POST['data'];
//    $x = 0;
//    
//    foreach ($dataArr as $id){
//        //mysqli_query($db, "DELETE FROM mdl_attitudepoint_view WHERE id = '$id'");
//        $x = $x + 1;
//        
//    }
//    
//    if($x > 0){
//        echo 'record deleted successfully';
//    }else{
//        echo 'no row selected';
//    }
//    
//}
?>
<body onload="tampilkanwaktu();setInterval('tampilkanwaktu()', 1000);">
    
<!--    <div class="row">
            <div class="col-lg-4" style="margin-left: 52px">
                <p>&nbsp;</p>
                <a>Hai</a>
            </div>
    </div>-->
    <div class="header">
        <div style="text-align: center;">
            <span id="clock" style="color: black" ></span>
            
            <?php
            $hari = date('l');
            /*$new = date('l, F d, Y', strtotime($Today));*/
            if ($hari=="Sunday") {
              ?><a style="color: black"><b><?php echo "Minggu, "?></b></a><?php
            }elseif ($hari=="Monday") {
             ?><a  style="color: black"><b><?php echo "Senin, "?></b></a><?php
            }elseif ($hari=="Tuesday") {
             ?><a  style="color: black"><b><?php echo "Selasa, "?></b></a><?php
            }elseif ($hari=="Wednesday") {
             ?><a  style="color: black"><b><?php echo "Rabu, "?></b></a><?php
            }elseif ($hari=="Thursday") {
             ?><a  style="color: black"><b><?php echo "Kamis, "?></b></a><?php
            }elseif ($hari=="Friday") {
             ?><a  style="color: black"><b><?php echo "Jum'at, "?></b></a><?php
            }elseif ($hari=="Saturday") {
             ?><a  style="color: black"><b><?php echo "Sabtu, "?></b></a><?php
            }
            ?>

            <?php
            $tgl =date('d');
            ?><a  style="color: black"><b><?php echo $tgl;?></b></a><?php
            $bulan =date('F');
            if ($bulan=="January") {
             ?><a  style="color: black"><b><?php echo " Januari "?></b></a><?php
            }elseif ($bulan=="February") {
             ?><a  style="color: black"><b><?php echo " Februari "?></b></a><?php
            }elseif ($bulan=="March") {
             ?><a  style="color: black"><b><?php echo " Maret "?></b></a><?php
            }elseif ($bulan=="April") {
             ?><a  style="color: black"><b><?php echo " April "?></b></a><?php
            }elseif ($bulan=="May") {
             ?><a  style="color: black"><b><?php echo " Mei "?></b></a><?php
            }elseif ($bulan=="June") {
             ?><a  style="color: black"><b><?php echo " Juni "?></b></a><?php
            }elseif ($bulan=="July") {
             ?><a  style="color: black"><b><?php echo " Juli "?></b></a><?php
            }elseif ($bulan=="August") {
             ?><a  style="color: black"><b><?php echo " Agustus "?></b></a><?php
            }elseif ($bulan=="September") {
             ?><a  style="color: black"><b><?php echo " September "?></b></a><?php
            }elseif ($bulan=="October") {
             ?><a  style="color: black"><b><?php echo " Oktober "?></b></a><?php
            }elseif ($bulan=="November") {
             ?><a  style="color: black"><b><?php echo " November "?></b></a><?php
            }elseif ($bulan=="December") {
             ?><a  style="color: black"><b><?php echo " Desember "?></b></a><?php
            }
            $tahun=date('Y');
            ?><a  style="color: black"><b><?php echo $tahun;?></b></a><?php
            ?>
            
        </div>
         

        
        <?php
        $custompoin = "";
        $id_mod = $_GET['id_mod'];
        $data = mysqli_query($db ,"SELECT * FROM mdl_course_modules WHERE id = $id_mod");
    
        $d = mysqli_fetch_array($data);
        
        $id_course = $d['course'];
        
        $data2 = mysqli_query($db ,"SELECT * FROM mdl_course WHERE id = $id_course");
        $d2 = mysqli_fetch_array($data2);
        
        $name_course = $d2['fullname'];
        
        ?>  
        <table style="width:100%">
            <thead>
              <tr>
                  <th style="text-align: left;"><a><?php echo "Kursus: ".$name_course; ?></a></th>
                <?php
                $title = get_string("users");
                $rs = $DB->get_recordset_select("user", "deleted = 0 AND picture > 0", array(), "lastaccess DESC", user_picture::fields());
                    foreach ($rs as $user) {
                        $fullname = s(fullname($user));
                        echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=1\" ".
                             "title=\"$fullname\">";
                        
                        if($user->id=$USER->id){
                            ?>
                    <th style="text-align: center;"><?php echo $OUTPUT->user_picture($user); ?></th>
                <?php
                            echo "</a> \n";
                            break;
                        }
                    }
                    $rs->close();
                ?>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="text-align: left;">View all submissions</th>
                <th style="text-align: center;"><?php echo $USER->firstname." ".$USER->lastname; ?></th>
        
              </tr>
              
            </tbody>
        </table>
    </div>
    
    <div class="main">
        <?php
        $id_mod = $_GET['id_mod'];
        $daftarNama = "";
        $fn = "";
        $ln = "";
        $x = 0;
        //$id_arr = array();
        
//-----------------------------tombol update banyak orang----------------------------------------------      
            if($_GET['updateall']=="true"){
                //$id_arr = array();
                if(isset($_GET['id'])){
                  $id_arr = array();
                  foreach($_GET['id'] as $val){
                   
                   $data = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_view WHERE id = $val");
                   $d = mysqli_fetch_array($data);
                   $fn = $d['firstname'];
                   $ln = $d['lastname'];
                   
                   $id_arr[] = $val;
                    
                   if($x==0){
                       $daftarNama = $fn." ".$ln;
                       $x = 2;
                   }else{
                       $daftarNama = $daftarNama.", ".$fn." ".$ln;
                   }
                  }
                }
                if($_GET['btncustomall']=="true"||$_GET['btncustomall']=="false"){
                    $daftarNama = $_GET['daftarNama'];
                    $id_arr = $_GET['id_arr'];
                    foreach($id_arr as $val){
                        //$id_arr[] = $val;
                        echo $val."-";
                    }
                }
        ?>
                <center>
                    <form onsubmit="return update()" style="margin-bottom: 40px;margin-top: 40px;width: 30%;" method="post" action="viewallsub.php">
                        <input type="text" value="<?php echo $_GET['id_mod']; ?>" name="id" hidden="true">

                        <?php
                        
                        foreach ($id_arr as $value){
                        //    echo $value." hai ";
                        ?>
                        <input type="text" value="<?php echo $value; ?>" name="id_arr[]" size="30" hidden="true">
                        <?php
                        }
                        ?>

                    <fieldset>
                        <legend><b>Nama Mahasiswa</b></legend>
                      <textarea style="margin-bottom: 10px;margin-top: 10px;" disabled="true" id="" name="daftarNama" rows="5" cols="30"><?php echo $daftarNama; ?></textarea>
                    </fieldset>
                    <fieldset>
                            <legend><b>Kategori perubahan poin sikap</b></legend>
                            <select <?php
                                $custompoinall = $_GET['btncustomall'];
                                if($custompoinall == "true"){
                                    ?>disabled<?php
                                }
                            ?> name="poin" id="poin" style="margin-bottom: 10px;margin-top: 10px;" required>
                                
                                <option value=""> Pilih </option>
                               <?php 
                                $sql=mysqli_query($db, "SELECT * FROM mdl_attitudepoint_score where id != 12");
                                while ($data=mysqli_fetch_array($sql)) {
                               ?>
                                 <option value="<?=$data['behavior']?>"><?=$data['behavior']?></option> 
                               <?php
                                }
                               ?>
                            </select>
                            
                            <!--              Custom Points               -->
                            <!--             Custom Pooints               -->
                            
                            <br><br>
                            <?php
                                //$custompoin = $_GET['btncustom'];
                                if($custompoinall == "true"){
                                ?>
                                    <a>Masukan Jumlah Poin</a>
                                    <input type="text" placeholder="ex : 20 atau -20" name="poin_custom" required="true">
                                    <br><br>
                                <?php
                                        
                                }
                            ?>
                            
                        </fieldset>
                        
                    <fieldset>
                        
                        <legend><b>Keterangan tambahan</b></legend>
                      <textarea style="margin-bottom: 10px;margin-top: 10px;" id="" name="keterangan" rows="5" cols="30" placeholder="Isi alasan perubahan poin sikap ...."></textarea>
                    </fieldset>
                        <input onclick="return confirm('Apakah Anda ingin mengubah data poin semua peserta ini?')" class="button2" type="submit" value="Update" name="update_all">
                        <br><br><br><br>
                </form>
                </center>
        <?php
            }
//-----------------------------tombol update satu orang----------------------------------------------
            
            if($_GET['btnedit']=="true"){
        ?>
                <center>
                    <form onsubmit="return update()" style="margin-bottom: 40px;margin-top: 40px;width: 30%;" method="post" action="viewallsub.php">
                            <input type="text" value="<?php echo $_GET['id_mod']; ?>" name="id" hidden="true">

                            <input type="text" value="<?php echo $_GET['id']; ?>" name="id_mhs" size="30" hidden="true">

                        <fieldset>
                            <legend><b>Nama Mahasiswa</b></legend>
                            <input style="margin-bottom: 10px;margin-top: 10px;" disabled="true" value="<?php echo $_GET['namalengkap'];?>" name="namalengkap">
                        </fieldset>
                        <fieldset>
                            <legend><b>Kategori perubahan poin sikap</b></legend>
                            <select <?php
                                $custompoin = $_GET['btncustom'];
                                if($custompoin == "true"){
                                    ?>disabled<?php
                                }
                            ?> name="poin" id="poin" style="margin-bottom: 10px;margin-top: 10px;" required>
                                
                                <option value=""> Pilih </option>
                               <?php 
                                $sql=mysqli_query($db, "SELECT * FROM mdl_attitudepoint_score where id != 12");
                                while ($data=mysqli_fetch_array($sql)) {
                               ?>
                                 <option value="<?=$data['behavior']?>"><?=$data['behavior']?></option> 
                               <?php
                                }
                               ?>
                            </select>
                            
                            <?php
                                if($custompoin != "true"){
                                    ?><br><a href="update.php?id_mod=<?php echo $id_mod;?>&id=<?php echo $_GET['id'];?>&btncustom=<?php echo "true";?>&namalengkap=<?php echo $_GET['namalengkap'];?>&btnedit=<?php echo "true";?>" class="btn btn-success" title="Poin kustom">Custom points</a>
                                    <input type="text" value="false" name="btn_custom" hidden="true">
                                    <?php
                                }else{
                                    ?><br><a href="update.php?id_mod=<?php echo $id_mod;?>&id=<?php echo $_GET['id'];?>&btncustom=<?php echo "false";?>&namalengkap=<?php echo $_GET['namalengkap'];?>&btnedit=<?php echo "true";?>" class="btn btn-success" title="Poin kustom">Not custom points</a>
                                     <input type="text" value="true" name="btn_custom" hidden="true">   
                                    <?php
                                    
                                }
                            ?>
                            
                            <br><br>
                            <?php
                                $custompoin = $_GET['btncustom'];
                                if($custompoin == "true"){
                                ?>
                                    <a>Masukan Jumlah Poin</a>
                                    <input type="text" placeholder="ex : 20 atau -20" name="poin_custom" required="true">
                                    <br><br>
                                <?php
                                        
                                }
                            ?>
                            
                        </fieldset>
                        <fieldset>
                            <legend><b>Keterangan tambahan</b></legend>
                          <textarea style="margin-bottom: 10px;margin-top: 10px;" id="" name="keterangan" rows="5" cols="30" placeholder="Isi alasan perubahan poin sikap ...."></textarea>
                        </fieldset>
                            <input onclick="return confirm('Apakah Anda ingin mengubah data poin peserta ini?')" class="button2" type="submit" value="Update" name="update_alone">
                    </form>
                </center>
                <?php echo $_GET['id'];
            }
        
        ?>
               
    </div>
    
    <div class="footer">
        <form action="viewallsub.php" method="get" style="margin-top: 10px;text-align: center;background-color: white; border: 0px solid #c1c1c1;">
            <input type="text" value="<?php echo $_GET['id_mod']; ?>" name="id" hidden="true">
            <input name="btn" type="submit" style="padding: 10px;" value="View all submissions" class="button">
        </form>
    </div>
    
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="js/jquery-2.1.1.min.js"></script>	
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.prettyPhoto.js"></script>
    <script src="js/jquery.isotope.min.js"></script>  
	<script src="js/wow.min.js"></script>
	<script src="js/functions.js"></script>
</body>
</html>


