<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
    include 'config.php';
    
    $dt = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_view");
    
    $data = array();
    
    while($row = mysqli_fetch_assoc($dt)){
        $data[] = $row;
    }
    
    echo json_encode($data);
//    if($_POST['rowid']) {
//    
//        $id = $_POST['rowid'];
//        // mengambil data berdasarkan id
//        // dan menampilkan data ke dalam form modal bootstrap
//        $data = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_view WHERE id = $id");
//        
//        while($d = mysqli_fetch_array($data)){
//      