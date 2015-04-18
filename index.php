<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>CDN GUI v0.1</title>
        <script type="text/javascript" src="inc/visjs/vis.js"></script>
        <script type="text/javascript" src="inc/jquery/jquery-1.11.2.min.js"></script>
        <link rel="stylesheet" type="text/css" href="inc/style.css">
    </head>
    <body>
        <h1 class="title">CDN GUI v 1.0</h1>
<!--        <div>
            <h3>GET DEBUG:</h3>
                <?php print_r($_GET); ?>
        </div>-->
    <?php
        //Load config
        include_once 'config.php';
        //Connect to DB every time the page loads
        include_once 'dbconnection.php';
        //Include menu here, so it is shown on every page
        include 'menu.php';
    ?>
        <div class="content" style="width:1024px;">
        <?php
        //Content
            if (isset($_GET['do'])) {
                switch ($_GET['do']){
                    case $_GET['do']=='routes':
                        include 'routes.php';
                        break;
                    case $_GET['do']=='topology':
                        include 'topology.php';
                        break;
                    case $_GET['do']=='requestrouters':
                        include 'requestrouters.php';
                        break;
                    case $_GET['do']=='serviceengines';
                        include 'serviceengines.php';
                        break;
                    case $_GET['do']=='cdnize';
                        include 'cdnize.php';
                        break;
                    case $_GET['do']=='sessions';
                        include 'sessions.php';
                        break;
                    default:
                        include 'about.php';
                        break;
                }
            } else {
                include 'about.php';
            }
        //end content
        ?>   
        </div>
    </body>
</html>
