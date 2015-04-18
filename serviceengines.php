<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h1>Service Engines</h1>
<?php
if (isset($_GET['delete'])) {
    ?>
    <div>
        <?php
        // sql to delete a record
        $sql = "DELETE FROM streaming_engine WHERE streaming_engine_id=" . $_GET['delete'];
        if (mysql_query($sql) === TRUE) {
            echo "Service Engine deleted successfully<br>";
        } else {
            echo "Error deleting service engine: " . mysql_error();
        }
        ?>
    </div>
    <?php
}

if (isset($_GET['add'])) {
    //TODO validate
    $sql = "INSERT INTO streaming_engine (ip_address) VALUES (INET_ATON('". $_GET['ipaddress'] ."'))";
    
    if(mysql_query($sql) === TRUE) {
        echo "Service Engine added successfully<br>";
    } else {
        echo "Error adding Service Engine " . mysql_error();
    }
}

$sees = mysql_query("SELECT streaming_engine_id as id, INET_NTOA(ip_address) as ip FROM streaming_engine") 
        or die('mysql error' . mysql_error());
?>
<div>
    <form action="index.php" method="get">
        <table border="1px solid">
            <th>ID</th>
            <th>IP address</th>
            <th></th>

            <?php
            while ($row = mysql_fetch_array($sees)) {
                ?>
                <tr>
                    <td>
                        <?php echo $row['id']; ?>
                    </td>
                    <td>
                        <?php echo $row['ip']; ?>
                    </td>
                    <td>
                        <input type="hidden" name="do" value="serviceengines" />
                        <button type="submit" name="delete" value="<?php echo $row['id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php
            }
            
            ?>
        </table>
    
    </form>
    <br>
    <div>
        <h2>Add new Service Engine:</h2>
        <form action="index.php" method="get">
            IP address:  <input type="text" name="ipaddress"/>
            <input type="hidden" name="do" value="serviceengines" />
            <button type="submit" name="add" value="add">Add new</button>
        </form>
    </div>
    <br>
    <a href="<?php echo $_SITEROOT; ?>">Back</a>
</div>