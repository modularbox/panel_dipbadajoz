<?php
/*START EJEMPLO1*/
/*$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
socket_bind($socket,'127.0.0.1',443);//65500
socket_listen($socket);

echo "Esperando conexión\n\n";
$conn = false;
switch(@socket_select($r = array($socket), $w = array($socket), $e = array($socket), 60)) {
	case 2:
		echo "Conexión rechazada!\n\n";
	break;
	case 1:
		echo "Conexión aceptada!\n\n";
		$conn = @socket_accept($socket);
	break;
	case 0:
		echo "Tiempo de espera excedido!\n\n";
	break;
}


if ($conn !== false) {
	// communicate over $conn
	echo "esta conectado... hacer lo que seaaaa...";
}*/
/*END EJEMPLO1*/

/*START EJEMPLO2*/
/*
//https://www.codeproject.com/Tips/418814/Socket-Programming-in-PHP

// set some variables
$host = "127.0.0.1";
$port = 443;
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
$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
// read client input
$input = socket_read($spawn, 1024) or die("Could not read input\n");
// clean up input string
$input = trim($input);
echo "Client Message : ".$input;
// reverse client input and send back
$output = strrev($input) . "\n";
socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
// close sockets
socket_close($spawn);
socket_close($socket);
*/
/*END EJEMPLO2*/


?>