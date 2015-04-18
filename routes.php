<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h1>CDN Routing</h1>
<?php
if (isset($_GET['delete'])) {
    ?>
    <div>
        <?php
        // sql to delete a record
        $sql = "DELETE FROM routing WHERE routing_id=" . $_GET['delete'];
        if (mysql_query($sql) === TRUE) {
            echo "Route deleted successfully";
        } else {
            echo "Error deleting record: " . mysql_error();
        }
        ?>
    </div>
    <?php
}

if (isset($_GET['add'])) {
    //TODO validate
    $sql = "INSERT INTO routing (prefix, mask, domain_id, streaming_engine_id) VALUES "
            . "(INET_ATON('" . $_GET['prefix'] . "'), INET_ATON('" . $_GET['mask'] . "'), '1', '" . $_GET['seid'] . "')";

    if (mysql_query($sql) === TRUE) {
        echo "Route added successfully";
    } else {
        echo "Error adding route " . mysql_error();
    }
}

$routes = mysql_query("SELECT routing_id as id, INET_NTOA(prefix) as prefix, INET_NTOA(mask) as mask, INET_NTOA(ip_address) as seip FROM `routing` 
    JOIN streaming_engine ON 
    streaming_engine.streaming_engine_id = routing.streaming_engine_id") or die('mysql error' . mysql_error());
?>
<div>
    <div class="tableclass">
        <form action="index.php" method="get">
            <table>
                <tr>
                    <td>ID</td>
                    <td>Prefix</td>
                    <td>Mask</td>
                    <td>Service engine IP</td>
                    <td></td>
                </tr>

                <?php
                while ($row = mysql_fetch_array($routes)) {
                    ?>
                    <tr>
                        <td>
                            <?php echo $row['id']; ?>
                        </td>
                        <td>
                            <?php echo $row['prefix']; ?>
                        </td>
                        <td>
                            <?php echo $row['mask']; ?>
                        </td>
                        <td>
                            <?php echo $row['seip']; ?>
                        </td>
                        <td>
                            <input type="hidden" name="do" value="routes" />
                            <button type="submit" name="delete" value="<?php echo $row['id']; ?>">Delete</button>
                        </td>
                    </tr>
                    <?php
                }

                $query = "Select streaming_engine_id as id, INET_NTOA(ip_address) as seip from streaming_engine";
                $result = mysql_query($query) or die('mysql error ' . mysql_error());
                ?>
            </table>
        </form>
    </div>
    <br>
    <div>
        <h2>Add new route:</h2>
        <form action="index.php" method="get">
            Prefix IP:  <input type="text" name="prefix"/>
            Netmask:    <input type="text" name="mask"/>
            Service Engine IP
            <select name="seid">
                <?php
                while ($row = mysql_fetch_array($result)) {
                    ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['seip']; ?></option>
                    <?php
                }
                ?>
            </select>
            <input type="hidden" name="do" value="routes" />
            <button type="submit" name="add" value="add">Add new</button>
        </form>
    </div>
    <br>
    <a href="<?php echo $_SITEROOT; ?>">Back</a>
</div>