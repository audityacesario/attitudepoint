<?php
function curl($url){
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch); 
    curl_close($ch);      
    return $output;
}
$curl = curl("https://api.kawalcorona.com/");
$data = json_decode($curl, TRUE);


 
 print $data['attributes']['OBJECTID'];
?>