<?php
//lanzar el daemon/demonio que ejecuta el fichero siempre a la escucha/ si se reinicia el server debe estar funcionado siempre

$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
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
	// communicate over $conn//la conexion ya esta abierta no se cierra
	echo "esta conectado... hacer lo que seaaaa...";

	// reverse client input and send back
	for($i=0;$i<2;$i++){
		$output = "hola pimo";
		socket_write($conn, $output, strlen ($output)) or die("Could not write output\n");

	}
	/* //ya trabajar, la conexión ya no se cierra
	$output = "adios pimo";
	socket_write($conn, $output, strlen ($output)) or die("Could not write output\n");

	$input = socket_read($conn, 1024) or die("Could not read input\n");
	// clean up input string
	$input = trim($input);
	echo "Client Message : ".$input;
	*/
}

//cerrar//si lo de arriba esa en un while(true){}, siempre estará a la escucha
echo "cerrar socket cliente, parte server,y cerrar el server socket";
socket_close($conn);
socket_close($socket);
?>