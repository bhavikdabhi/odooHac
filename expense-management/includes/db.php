<?php
$host = "localhost";
$db = "money_manager";
$user = "root";
$pass = "";

$conn = new mysqli($host,$user,$pass,$db);
if ($conn->connect_error) {
    die("Connection Failed: ".$conn->connect_error);
}
?>
