<?php
$data = json_decode(file_get_contents('php://input'), true);

$myfile = fopen("readerping.txt", "w") or die("Unable to open file!");

$text = $data["ping"];

$server = "mysql-mariadb";
$db = "power";
$user = "power";
$passwd = "Power2025!";

$conn = new mysqli($server, $user, $passwd, $db);

$sql = 'INSERT INTO Health (HKey, HValue) VALUES("reader", "'.$text.'") ON DUPLICATE KEY UPDATE HKey="reader", HValue="'.$text.'"';

if ($conn->query($sql) !== TRUE) {
    $text .= "Error: " . $sql . "<br>" . $conn->error."\n";
}

fwrite($myfile, $text);
fclose($myfile);

