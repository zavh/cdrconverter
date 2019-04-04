<?php
include "db_read.php";
// set some variables
$host = "103.245.143.8";
$port = 9000;
// don't timeout!
set_time_limit(0);
// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
// bind socket to port
$result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
// start listening for connections
$result = socket_listen($socket, 3) or die("Could not set up socket listener\n");

// accept incoming connections
// spawn another socket to handle communication
do{
	$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
// read client input
	$input = socket_read($spawn, 1024);

	if(!$input) {
		$logfile = fopen("socket.log", "a+") or die("Unable to open file!");
        	fwrite($logfile, 'Could not read data'."\n");
		fclose($logfile);
	}
	else {
		$mondat = explode('|',$input);
		print($input);
		$dat = json_decode($mondat[1]);
		$sql  = "INSERT INTO stats (`montage`, `voltage`, `current`, `power`, `temperature`) VALUES(";
		$sql .= "'".$mondat[0]."', ".$dat->Voltage.", ".$dat->Current.", ".$dat->Power.", ".$dat->Temperature.")";
		$conn->query($sql);
		print($sql);
	}
	//print "Client Message : ".$input."\n";
// reverse client input and send back
//	$output = strrev($input) . "\n";
//	socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
// close sockets
	socket_close($spawn);
}	while(true);
socket_close($socket);
include "db_close.php";
?>
