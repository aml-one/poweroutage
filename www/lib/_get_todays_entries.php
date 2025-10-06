<?php
include "_config.php";
date_default_timezone_set('Europe/Athens');

$conn = new mysqli($server, $user, $passwd, $db);
$sql = "SELECT * FROM PowerLog WHERE PValid = '1' AND PTime LIKE '%".date("Y-m-d")."%' ORDER BY PTime ASC";

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
        if ($firstEventChecked == false)
        {
            if ($row["PEvent"] == "on")
            {
                $firstEvent = 1;
            }
            else
            {
                $firstEvent = 0;
            }
            $firstEventChecked = true;
        }

        $stack = [];

        if ($firstEventAdded == false)
        {
            $stack["event"] = 1;
            $stack["time"] = (int)(strtotime(date('m/d/Y', time()). " 0:00:01")."001");
            $array[$i] = $stack;
            $i++;
            $firstEventAdded = true;
        }

        if ($row["PEvent"] == "on")
        {
            if ($firstEvent == 0)
            {
                $stack["event"] = 0;
                $stack["time"] = (int)((string)((int)strtotime($row["PTime"])-1)."000");
                $array[$i] = $stack;
                $i++;
            }

            $stack["event"] = 1;
            $stack["time"] = (int)(strtotime($row["PTime"])."000");
            $array[$i] = $stack;
            $i++;
            $lastEvent = 1;
        }
        else
        {
            $stack["event"] = 1;
            $stack["time"] = (int)((string)((int)strtotime($row["PTime"])-1)."000");
            $array[$i] = $stack;
            $i++;
            $stack["event"] = 0;
            $stack["time"] = (int)(strtotime($row["PTime"])."000");
            $array[$i] = $stack;
            $i++;
            $lastEvent = 0;
        }
    }

    $stack["event"] = $lastEvent;
    $stack["time"] = (int)(strtotime(date('m/d/Y h:i:s a', time()))."000");
    $array[$i] = $stack;

    echo json_encode($array);
}
else
{
    $i = 0;
    $stack = [];
    $array = [];

    $stack["event"] = $lastEvent;
    $stack["time"] = (int)(strtotime(date('m/d/Y', time()). " 0:00:01.001")."000");
    $array[$i] = $stack;
    $i++;

    $stack["event"] = $lastEvent;
    $stack["time"] = (int)(strtotime(date('m/d/Y h:i:s a', time()))."000");
    $array[$i] = $stack;
    $i++;

    echo json_encode($array);
}


$conn->close();