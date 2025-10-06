<?php
include "_config.php";
date_default_timezone_set('Europe/Athens');

$conn = new mysqli($server, $user, $passwd, $db);
$sql = "SELECT * FROM Health";

$result = $conn->query($sql);
$array = [];

if ($result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $array[$row["HKey"]] = $row["HValue"];
    }

    echo json_encode($array);
}

$conn->close();