<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

$datapoin = mysqli_query($db ,"SELECT * FROM mdl_attitudepoint_score");
            while($dp = mysqli_fetch_array($datapoin)){
				//echo $dp['note'];
				$tampil = json_encode($dp);
				echo $tampil;
			}
?>
