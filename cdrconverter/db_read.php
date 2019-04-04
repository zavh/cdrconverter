<?php
$servername = "localhost";
$username = "webcdr";
$password = "@lpha7\$Igm@1";
$dbname = "webcdr";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
