<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require './inc/restcontroller/restcontroller.php';

$restcont = new restController($_CONTROLLERURI);
$restcont->refreshData();
$switches = $restcont->getSwitches();
$edges = $restcont->getEdges();
$networks = $restcont->getNetworks($edges);
$nodes = array_merge($switches, $networks);
$routing = $restcont->getRoutingTables();
$reqrouters = $restcont->getRequestRouters();
$cdnrouting = $restcont->getCDNRouting();
print_r($cdnrouting);
?>
<div width="100%">
    <h1>Network topology</h1>
</div>
<div width="100%">
    <?php $mynetwork_width = '800px'; ?>
    <div id="mynetwork" style="border:solid 1px; float:left"></div>
    <div style="float:left; width:220px; height:602px">
        <h2>Node info</h2>
        <div id="nodecontroller"></div>
    </div>
</div>
<div style="width:100%; float:left">
    <a href="<?php echo $_SITEROOT; ?>">Back</a>
</div>

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
        width: '<?php echo $mynetwork_width ?>',
        height: '600px',
        smoothCurves: false,
        nodes: {fontSize: 10},
        edges: {fontSize: 8},
        groups: {
            FW: {
                shape: 'image',
                image: './inc/visjs/img/network/switch-blue-hi.png'
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
    network.on('select', onSelect);

    function onSelect(properties) {
        for (i = 0; i < switches.length; i++) {
            if (switches[i]['id'] == properties.nodes) {
                var nodecon = document.getElementById("nodecontroller");
                var routing = <?php echo json_encode($routing); ?>;
                var reqrouters = <?php echo json_encode($reqrouters); ?>;
                var cdnrouting = <?php echo json_encode($cdnrouting); ?>;

                var routingdiv = document.createElement("div");

                //Loading routing table in JS
                if (properties.nodes in routing) {
                    if (routing[properties.nodes] != null) {
                        var rtableh3 = document.createElement('h3');
                        rtableh3.textContent = "Routing Table:";
                        var rtable = document.createElement('table');
                        var thid = document.createElement('th');
                        thid.textContent = "ID";
                        var thnetwork = document.createElement('th');
                        thnetwork.textContent = "Destination network";
                        var thgw = document.createElement('th');
                        thgw.textContent = "Gateway";
                        rtable.appendChild(thid);
                        rtable.appendChild(thnetwork);
                        rtable.appendChild(thgw);

                        for (i = 0; i < routing[properties.nodes].length; i++) {
                            var row = document.createElement('tr');
                            var id = document.createElement('td');
                            id.textContent = routing[properties.nodes][i]['route_id'];
                            row.appendChild(id);
                            var destination = document.createElement('td');
                            destination.textContent = routing[properties.nodes][i]['destination'];
                            row.appendChild(destination);
                            var gateway = document.createElement('td');
                            gateway.textContent = routing[properties.nodes][i]['gateway'];
                            row.appendChild(gateway);
                            rtable.appendChild(row);
                        }
                        routingdiv.appendChild(rtableh3);
                        routingdiv.appendChild(rtable);
                    } else {
                        var par = document.createElement('p');
                        par.textContent = "No Routing table information found";
                        routingdiv.appendChild(par);
                    }
                }

                var reqroutersdiv = document.createElement('div');

                //loading request routers in JS
                if (properties.nodes in reqrouters) {
                    if (reqrouters[properties.nodes] != null) {
                        var rrlisth3 = document.createElement('h3');
                        rrlisth3.textContent = "Request routers defined:";
                        var rrtable = document.createElement('table');
                        var thid = document.createElement('th');
                        thid.textContent = "ID";
                        var thrr = document.createElement('th');
                        thrr.textContent = "Request Router";
                        rrtable.appendChild(thid);
                        rrtable.appendChild(thrr);

                        for (i = 0; i < reqrouters[properties.nodes].length; i++) {
                            var row = document.createElement('tr');
                            var id = document.createElement('td');
                            id.textContent = reqrouters[properties.nodes][i]['request_router_id'];
                            row.appendChild(id);
                            var reqrouter = document.createElement('td');
                            reqrouter.textContent = reqrouters[properties.nodes][i]['request_router'];
                            row.appendChild(reqrouter);

                            rrtable.appendChild(row);
                        }
                        reqroutersdiv.appendChild(rrlisth3);
                        reqroutersdiv.appendChild(rrtable);
                    } else {
                        var par = document.createElement('p');
                        par.textContent = "Not a CDN router";
                        var cdnform = document.createElement('form');
                        cdnform.setAttribute('method', 'get');
                        cdnform.setAttribute('action', 'index.php');

                        //Other variables...reserved as reference for later use
//                        var cdnizeinput = document.createElement('input');
//                        cdnizeinput.setAttribute('type', 'hidden');
//                        cdnizeinput.setAttribute('name', 'do');
//                        cdnizeinput.setAttribute('value', 'cdnize');

                        var switchid = document.createElement('input');
                        switchid.setAttribute('type', 'hidden');
                        switchid.setAttribute('name', 'switchid');
                        switchid.setAttribute('value', properties.nodes);

                        var submitbutton = document.createElement('button');
                        submitbutton.setAttribute('type', 'submit');
                        submitbutton.setAttribute('name', 'do');
                        submitbutton.setAttribute('value', 'cdnize');
                        submitbutton.textContent = "CDNize this switch";

//                        cdnform.appendChild(cdnizeinput);
                        cdnform.appendChild(switchid);
                        cdnform.appendChild(submitbutton);

                        reqroutersdiv.appendChild(par);
                        reqroutersdiv.appendChild(cdnform);
                    }
                }

                var cdnroutingdiv = document.createElement("div");

                //Loading routing table in JS
                if (properties.nodes in cdnrouting) {
                    if (cdnrouting[properties.nodes] != null) {
                        var rtableh3 = document.createElement('h3');
                        rtableh3.textContent = "CDN Routing Table:";
                        var rtable = document.createElement('table');
                        var thid = document.createElement('th');
                        thid.textContent = "ID";
                        var thnetwork = document.createElement('th');
                        thnetwork.textContent = "Source subnet";
                        var thgw = document.createElement('th');
                        thgw.textContent = "Service Engine";
                        rtable.appendChild(thid);
                        rtable.appendChild(thnetwork);
                        rtable.appendChild(thgw);

                        for (i = 0; i < cdnrouting[properties.nodes].length; i++) {
                            var row = document.createElement('tr');
                            var id = document.createElement('td');
                            id.textContent = cdnrouting[properties.nodes][i]['route_id'];
                            row.appendChild(id);
                            var subnet = document.createElement('td');
                            subnet.textContent = cdnrouting[properties.nodes][i]['address'];
                            row.appendChild(subnet);
                            var seip = document.createElement('td');
                            seip.textContent = cdnrouting[properties.nodes][i]['service_engine'];
                            row.appendChild(seip);
                            rtable.appendChild(row);
                        }
                        cdnroutingdiv.appendChild(rtableh3);
                        cdnroutingdiv.appendChild(rtable);

                        var sessionsurl = document.createElement('a');
                        var linkText = document.createTextNode("List sessions");
                        sessionsurl.appendChild(linkText);
                        sessionsurl.title = "Session list";
                        sessionsurl.href = "index.php?do=sessions&switchid="+ properties.nodes;
                        cdnroutingdiv.appendChild(sessionsurl);
                    }
                }

                while (nodecon.firstChild) {
                    nodecon.removeChild(nodecon.firstChild);
                }

                nodecon.appendChild(routingdiv);
                nodecon.appendChild(reqroutersdiv);
                nodecon.appendChild(cdnroutingdiv);

                return;
            }
        }
    }
</script>
