<?php

$host = "127.0.0.1";

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$puerto = 443;

if (socket_connect($socket, $host, $puerto)){
	echo "\nConexion Exitosa, puerto: " . $puerto;

	// communicate over $conn//la conexion ya esta abierta no se cierra
	while (true) {
	   $result = socket_read ($socket, 1024) or die("Could not read server response\n");
		echo $result;
		if($result=="adios pimo"){
			socket_write($socket, "buenoooo adiosss", strlen("buenoooo adiosss")) or die("Could not send data to server\n");
		}
	}
}
else{
	echo "\nLa conexion TCP no se pudo realizar, puerto: ".$puerto;
}

echo "cerrando socket parte cliente";
socket_close($socket);

?>