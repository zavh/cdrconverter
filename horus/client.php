<?php
$host    = "103.245.143.8";
$port    = 9000;
$message = "103.245.143.11|VOS-PRIMARY|Could not connect to billing2";
print "Message To server :".$message."\n";
// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
// connect to server
$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");  
// send string to server
socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
// get server response
$result = socket_read ($socket, 1024) or die("Could not read server response\n");
print "Reply From Server  :".$result;
// close socket
socket_close($socket);
?>
