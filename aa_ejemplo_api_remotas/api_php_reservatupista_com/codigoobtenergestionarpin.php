<?php

$servidor="78.136.74.2:3306";
$usuario="xxxx";
$clave="xxxx";
$baseDatos="modularndb";
/* START CONEXION BBDD*/
function conectaReservaPanelBBDD($servidor,$usuario,$clave,$baseDatos){//conecta a la base de datos
	$mysqli = new mysqli($servidor,$usuario,$clave,$baseDatos);
	$mysqli->query("SET NAMES 'utf8'");
	return $mysqli;
}
$con=conectaReservaPanelBBDD($servidor,$usuario,$clave,$baseDatos);
var_dump($con);
/* END CONEXION BBDD*/

$con=conectaReservaPanelBBDD($servidor,$usuario,$clave,$baseDatos);
if ($con->connect_errno != null) {
   echo "Error número $con->connect_errno conectando a la base de datos.<br>Mensaje: $con->connect_error.";
   exit(); 
}

function obtenerPinesPanelModularboxGestionar($con){
	
	$patron="SELECT id,alias,serial,serie FROM pistaspadel_pines WHERE borrado=\"n\"";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error 1345");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			echo $fila[0];
		}
	}
	mysqli_free_result($respuesta);
}
//echo obtenerPinesPanelModularboxGestionar($con);
/*
//poner esto en  https://reservatupista.com/wp-content/plugins/wp-base-booking-of-appointments-services-and-events/includes/core.php
//function function _replace
//en el array primero de arriba "SEQURITY_CODE"
//abajo al final la variable securitycode

require_once("../../../../api_php/codigoobtenergestionarpin.php");
echo obtenerPinesPanelModularboxGestionar();
$sequrityCode=999999;
*/
?>