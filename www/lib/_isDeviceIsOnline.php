<?php
include "_config.php";
date_default_timezone_set('Europe/Athens');

$conn = new mysqli($server, $user, $passwd, $db);
$sql = "SELECT * FROM Health";

$result = $conn->query($sql);

$readerIsOnline = false;
$pmIsOnline = false;

if ($result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        if ($row["HKey"] == "reader")
        {
            if ((int)(strtotime($row["HValue"])+30) > time())
                $readerIsOnline = true;
        }

        if ($row["HKey"] == "pm")
        {
            if ((int)(strtotime($row["HValue"])+30) > time())
                $pmIsOnline = true;
        }
    }
}

if ($readerIsOnline && $pmIsOnline)
    echo "online";
else
    echo "offline";


$conn->close();