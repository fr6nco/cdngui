<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require './inc/restcontroller/restcontroller.php';


$restcont = new restController($_CONTROLLERURI);
$sessions = $restcont->getSessions($_GET['switchid']);
?>
<div>
    <table border="1px solid">
        <th>Source IP</th>
        <th>Source Port</th>
        <th>Session State</th>
        <th>Service Engine</th>
        <th>Request Router</th>
        <th>Timestamp</th>
        <th>Request URI</th>
        <th></th>

        <?php foreach ($sessions as $ses) { ?>
            <tr>
                <td><?php echo $ses['source_ip']; ?></td>
                <td><?php echo $ses['source_port']; ?></td>
                <td><?php echo $ses['state']; ?></td>
                <td><?php echo $ses['service_engine_ip']; ?></td>
                <td><?php echo $ses['request_router_ip']; ?></td>
                <td><?php echo $ses['session_timestamp']; ?></td>
                <td><?php echo $ses['request_uri']; ?></td>
                <td><a href="index.php?<?php echo http_build_query($ses);?>">Show on topology</a></td>
            </tr>
        <?php } ?>


    </table>
</div>
<br>
<a href="<?php echo $_SITEROOT; ?>">Back</a>