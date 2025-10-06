<?php
include "_config.php";
date_default_timezone_set('Europe/Athens');

$date = $_GET["date"];

$conn = new mysqli($server, $user, $passwd, $db);
$sql = "SELECT * FROM PowerLog WHERE PValid = '1' AND PTime LIKE '%".$date."%' ORDER BY PTime ASC";

$result = $conn->query($sql);

$array = [];
$i = 0;
$firstEvent = 1;
$lastEvent = 1;
$firstEventAdded = false;
if ($result->num_rows > 0)
{
    $firstEventChecked = false;
    while($row = $result->fetch_assoc())
    {
        echo "<div class='event'>";

        if ($row["PEvent"] == "on")
        {
            if ($firstEventAdded == false)
            {
                echo "<span class='green eventStr'>Power is on</span> at ";
                echo "<span class='text-green'>".str_replace($date, "", $row["PTime"])."</span>";
                echo "<hr />";
            }
            else
            {
                echo "<span class='green eventStr'>Power is back on</span> at ";
                echo "<span class='text-green'>".str_replace($date, "", $row["PTime"])."</span>";
                echo "<hr />";
            }
        }

        if ($row["PEvent"] == "off")
        {
            echo "<span class='red eventStr'>Power Loss</span> at";
            echo "<span class='text-red'>".str_replace($date, "", $row["PTime"])."</span>";
        }

        echo "</div>";
        $firstEventAdded = true;
    }
}

$conn->close();