<?php
$data = json_decode(file_get_contents('php://input'), true);

$myfile = fopen("pmping.txt", "w") or die("Unable to open file!");

$text = $data["ping"];

$server = "mysql-mariadb";
$db = "power";
$user = "power";
$passwd = "Power2025!";

$conn = new mysqli($server, $user, $passwd, $db);

$sql = 'INSERT INTO Health (HKey, HValue) VALUES("pm", "'.$text.'") ON DUPLICATE KEY UPDATE HKey="pm", HValue="'.$text.'"';

if ($conn->query($sql) !== TRUE) {
    $text .= "Error: " . $sql . "<br>" . $conn->error."\n";
}

$conn->close();

fwrite($myfile, $text);
fclose($myfile);

