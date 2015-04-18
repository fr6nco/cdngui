<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'dbconnection.php';
include 'inc/restcontroller/restcontroller.php';

echo "includes";
echo $_CONTROLLERURI;
$restcont = new restController($_CONTROLLERURI);

echo "restcont";
$query = mysql_query("SELECT INET_NTOA(ip_address) as ip FROM request_router") 
        or die('mysql error' . mysql_error());

$rrouters = array();

while($row = mysql_fetch_array($query)) {
    $rrouters[] = $row['ip'];
}

$query = mysql_query("SELECT INET_NTOA(prefix) as prefix, INET_NTOA(mask) as mask, "
        . "INET_NTOA(ip_address) as seip FROM `routing` "
        . "JOIN streaming_engine ON streaming_engine.streaming_engine_id = routing.streaming_engine_id") or die('mysql error' . mysql_error());


$routes = array();

while($row = mysql_fetch_array($query)) {
    $routes[] = array(
            'prefix' => $row['prefix'],
            'mask' => $row['mask'],
            'seip' => $row['seip']
        );
}

foreach($rrouters as $rr) {
    $restcont->postRequestRouter($rr, $_GET['switchid']);
}
echo "posts";

foreach($routes as $rr) {
    $restcont->postCDNRoute($rr['prefix'], $rr['mask'], $rr['seip'], $_GET['switchid']);
}
echo "posts";
?>
<h3>Request router and routes loaded to switch</h3>
