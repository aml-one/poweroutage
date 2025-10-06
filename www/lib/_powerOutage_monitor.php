<?php
$data = json_decode(file_get_contents('php://input'), true);

$myfile = fopen("plog.txt", "w") or die("Unable to open file!");


$text = "";
$array = explode("],", $data["data"]);

$server = "mysql-mariadb";
$db = "power";
$user = "power";
$passwd = "Power2025!";

$conn = new mysqli($server, $user, $passwd, $db);
foreach ($array as $line) {
    $lineExp = explode(",", $line);

    if (strlen(trim(str_replace('"', "", str_replace("[","", $lineExp[0])))) > 13) {
        $sql = "INSERT IGNORE INTO PowerLog (PId, PEvent, PTime, PValid)
                VALUES ('".trim(str_replace('"', "", str_replace("[","", $lineExp[0])))."',
                        '".trim(str_replace('"', "", str_replace("[","", $lineExp[1])))."',
                        '".trim(str_replace('"', "", str_replace("[","", $lineExp[2])))."',
                        '1')";
    }

    if ($conn->query($sql) !== TRUE) {
        $text .= "Error: " . $sql . "<br>" . $conn->error."\n";
    }
}

$conn->close();

fwrite($myfile, $text);
fclose($myfile);

