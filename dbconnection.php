<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$conn = mysql_connect($_HOST, $_USERNAME, $_PASSWORD);

mysql_select_db($_DATABASE);

if ($conn->connect_error) {
    die ("Could not connect to DB");
}
