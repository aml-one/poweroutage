<?php
include "_config.php";
date_default_timezone_set('Europe/Athens');

$conn = new mysqli($server, $user, $passwd, $db);
$sql = "SELECT * FROM PowerLog WHERE PEvent = 'off' AND PValid = '1' ORDER BY PTime ASC";

$result = $conn->query($sql);

$array = [];
$i = 0;

if ($result->num_rows > 0)
{
    $firstEventChecked = false;
    while($row = $result->fetch_assoc())
    {
        $stack["timestamp"] = (int)((string)((int)strtotime($row["PTime"]))."000");
        $stack["count"] = 1;
        $array[$i] = $stack;
        $i++;
    }
}

echo json_encode($array);

$conn->close();