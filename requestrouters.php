<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h1>Request routers</h1>
<?php
if (isset($_GET['delete'])) {
    ?>
    <div>
        <?php
        // sql to delete a record
        $sql = "DELETE FROM request_router WHERE request_router_id=" . $_GET['delete'];
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
    $sql = "INSERT INTO request_router (ip_address) VALUES (INET_ATON('" . $_GET['ipaddress'] . "'))";

    if (mysql_query($sql) === TRUE) {
        echo "Request router added successfully";
    } else {
        echo "Error adding request router " . mysql_error();
    }
}

$routers = mysql_query("SELECT request_router_id as id, INET_NTOA(ip_address) as ip FROM request_router")
        or die('mysql error' . mysql_error());
?>
<div>
    <div class="tableclass">
        <form action="index.php" method="get">

            <table>
                <tr>
                    <td>ID</td>
                    <td>IP address</td>
                    <td></td>
                </tr>

                <?php
                while ($row = mysql_fetch_array($routers)) {
                    ?>
                    <tr>
                        <td>
                            <?php echo $row['id']; ?>
                        </td>
                        <td>
                            <?php echo $row['ip']; ?>
                        </td>
                        <td>
                            <input type="hidden" name="do" value="requestrouters" />
                            <button type="submit" name="delete" value="<?php echo $row['id']; ?>">Delete</button>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>

        </form>
    </div>
    <br>
    <div>
        <h2>Add new Request Router:</h2>
        <form action="index.php" method="get">
            IP address:  <input type="text" name="ipaddress"/>
            <input type="hidden" name="do" value="requestrouters" />
            <button type="submit" name="add" value="add">Add new</button>
        </form>
    </div>
    <br>
    <a href="<?php echo $_SITEROOT; ?>">Back</a>
</div>