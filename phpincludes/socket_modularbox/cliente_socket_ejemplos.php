<?php
/*START EJEMPLO1*/
/*
*http://www.php.net/manual/en/ref.sockets.php
*/
/*
$host = "127.0.0.1";

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$puerto = 443;

if (socket_connect($socket, $host, $puerto)){
	echo "\nConexion Exitosa, puerto: " . $puerto;
}
else{
	echo "\nLa conexion TCP no se pudo realizar, puerto: ".$puerto;
}
//socket_close($socket);
*/
/*END EJEMPLO1*/

/*START EJEMPLO2*/
/*
*http://www.php.net/manual/en/ref.sockets.php
*/
/*
$host    = "127.0.0.1";
$port    = 443;
$message = "Hello Server";
echo "Message To server (mensaje al servidor):".$message;
// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
// connect to server
$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");  
// send string to server
socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
// get server response
$result = socket_read ($socket, 1024) or die("Could not read server response\n");
echo "Reply From Server  :".$result;
// close socket
socket_close($socket);

*/
/*END EJEMPLO2*/



?>