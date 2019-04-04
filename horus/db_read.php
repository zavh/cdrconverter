<?php
$servername = "localhost";
$username = "homeauto";
$password = "@lpha7\$Igm@1";
$dbname = "homeauto";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
