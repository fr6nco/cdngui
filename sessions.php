<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require './inc/restcontroller/restcontroller.php';


$restcont = new restController($_CONTROLLERURI);
$sessions = $restcont->getSessions($_GET['switchid']);
$restcont->refreshData();
$switches = $restcont->getSwitches();
$edges = $restcont->getEdges();
$networks = $restcont->getNetworks($edges);
$nodes = array_merge($switches, $networks);

if (isset($_GET['source_ip'])) {
    foreach ($nodes as $node) {
        if ($node['group'] == "NET") {
            if ($restcont->ipmatch($_GET['source_ip'], $node['id'])) {
                $edges = array_merge($edges, array(array('from' => $node['id'], 'to' => $_GET['source_ip'])));
            }
        }
    }
    $nodes = array_merge($nodes, array(array('id' => $_GET['source_ip'], 'label' => 'client', 'group' => 'CLIENT')));
}

if (isset($_GET['service_engine_ip'])) {
    foreach ($nodes as $node) {
        if ($node['group'] == "NET") {
            if ($restcont->ipmatch($_GET['service_engine_ip'], $node['id'])) {
                $edges = array_merge($edges, array(array('from' => $node['id'], 'to' => $_GET['service_engine_ip'])));
            }
        }
    }
    $nodes = array_merge($nodes, array(array('id' => $_GET['service_engine_ip'], 'label' => 'SE', 'group' => 'SERVER')));
}

if (isset($_GET['request_router_ip']) && isset($_GET['switchid'])) {
    $edges = array_merge($edges, array(array('from' => $_GET['switchid'], 'to' => $_GET['request_router_ip'], 'style' => 'dash-line')));
    $nodes = array_merge($nodes, array(array('id' => $_GET['request_router_ip'], 'label' => 'RR/CNT', 'group' => 'CDN')));
}
?>
<div width="100%">
    <h1>Sessions on switch <?php echo $_GET['switchid']; ?></h1>
</div>
<div class="tableclass">
    <table>
        <tr>
            <td>Source IP</td>
            <td>Source Port</td>
            <td>Host</td>
            <td>Session State</td>
            <td>Service Engine</td>
            <td>Request Router</td>
            <td>Timestamp</td>
            <td>Request URI</td>
            <td></td>
        </tr>

        <?php foreach ($sessions as $ses) { ?>
            <tr>
                <td><?php echo $ses['source_ip']; ?></td>
                <td><?php echo $ses['source_port']; ?></td>
                <td><?php echo $ses['host']; ?></td>
                <td><?php if ($ses['state'] == 6) {
                echo "Joined";
            } else {
                echo "Failed";
            } ?></td>
                <td><?php echo $ses['service_engine_ip']; ?></td>
                <td><?php echo $ses['request_router_ip']; ?></td>
                <td><?php echo $ses['session_timestamp']; ?></td>
                <td><?php echo $ses['request_uri']; ?></td>
                <td><a href="index.php?<?php echo http_build_query(array_merge(array('do' => 'sessions'), $ses)); ?>">Show on topology</a></td>
            </tr>
<?php } ?>


    </table>
</div>
<?php
if (isset($_GET['state']) && $_GET['state'] == 6) {
    echo "<div>";
    echo "<h2>Session description:</h2>";
    echo "<ul>";
    echo "<li>User requested in his browser address " . $_GET['host'] . "</li>";
    echo "<li>" . $_GET['host'] . " is located at ip address " . $_GET['request_router_ip'] . " which is the Request Router defined in the CDN engine</li>";
    echo "<li>The user agent sends a TCP SYN packet to the request router.</li>";
    echo "<li>The packet is captured on the forwarder " . $_GET['switchid'] . " and is sent to the controller</li>";
    echo "<li>The controller creates a new session and sends a TCP SYN ACK packet back to the client</li>";
    echo "<li>The client sends an ACK packet, so the TCP session is now established with the forwarder</li>";
    echo "<li>In the next step the User Agent sent his HTTP GET request which was " . $_GET['request_uri'] . "</li>";
    echo "<li>The controller according to the CDN routing determined, that the content should be served usind the service engine with IP " . $_GET['service_engine_ip'] . "</li>";
    echo "<li>From the forwarder " . $_GET['switchid'] . " a TCP connection is established with the Service Engine</li>";
    echo "<li>When the TCP session is established the two TCP sessions are synchronized and joined using ACK, SEQ number and IP address modification flows on the " . $_GET['switchid'] . "</li>";
    echo "<li>The content is served to the client from the Service Engine</li>";
    echo "</ul>";
    echo "</div>";
}
?>
<div id="mynetwork" >

</div>
<br>
<a href="<?php echo $_SITEROOT . "?do=topology" ?>">Back</a>

<script type="text/javascript">
    // create an array with nodes
    var nodes = <?php echo json_encode($nodes); ?>
    // create an array with edges
    var edges = <?php echo json_encode($edges); ?>
    //Save switches so we know the list of em
    var switches = <?php echo json_encode($switches); ?>

    // create a network
    var container = document.getElementById('mynetwork');
    var data = {
        nodes: nodes,
        edges: edges,
    };
    var options = {
        width: '800px',
        height: '800px',
        smoothCurves: false,
        nodes: {fontSize: 10},
        edges: {fontSize: 8},
        groups: {
            FW: {
                shape: 'image',
                image: './inc/visjs/img/network/switch-blue-hi.png'
            },
            SERVER: {
                shape: 'image',
                image: './inc/visjs/img/network/servericon.png'
            },
            CLIENT: {
                shape: 'image',
                image: './inc/visjs/img/network/client.png'
            }
        },
        physics: {
            barnesHut: {
                enabled: false
            },
            repulsion: {
                nodeDistance: 136,
                centralGravity: 0.15,
                springLength: 101,
                springConstant: 0.5,
                damping: 0.3}
        }
    };
    var network = new vis.Network(container, data, options);
</script>
