<?php
	if (!extension_loaded('sockets')) {
   		 die('The sockets extension is not loaded.');
	}
	set_time_limit(0);
	$ip = "103.245.143.8";
	$port = 9000; 

	$socket = socket_create(AF_INET, SOCK_STREAM, 0);
	if (!$socket)
		die('Unable to create AF_UNIX socket');
	print "The Socket protocol info has been set!\n";
	if(!socket_bind($socket, $ip, $port)){
		showError($socket);
	}
	print "The Socket has been bound to a specific IP and Port\n";

	if(!socket_listen($socket)){
		showError($socket);
	}
	print "Now, Listening for new connection @ @ @\n";

//	do{
		$client = socket_accept($socket);
		print "New connection with client established !!\n";
		$message = "\n Hey! Welcome to another exciting walk!\n";
		$input = socket_read($client, 1024) or die("Could not read input\n");
		
		socket_write($clinet, $message, strlen($message));

		//Check for message from user
//		do{
//			if(!$clientMsg = socket_read($client, 2048, PHP_NORMAL_READ)){
//				showError($socket);
//			}
//			// Say something back
//			$msgForUser = "Thank you for your input. Will think about it.";
//			socket_write($client, $msgForUser, strlen($msgForUser));
			
//			if(! $clientMsg = trim($clinetMsg))
//				continue;
//			if($clientMsg == 'close'){
//				socket_close($client);
//				print"\n\n---------------------------------------\n
//					The user has left the connection\n";
//				break 2;
//			}
//		}while(true);
//	}while(true);
	//Closing the socket
	print "Ending the socket\n";
	socket_close($socket);
	socket_close($client);

        function showError($theSocket = null){
		$errorCode = socket_last_error($theSocket);
		$errorMssg = socket_strerror($errorCode);
		die("Could not create socket: [$errorCode]: $errorMssg\n");
        }

?>
