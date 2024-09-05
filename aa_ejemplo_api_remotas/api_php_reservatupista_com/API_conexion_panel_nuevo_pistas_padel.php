<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');

header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");

/*start mostrar errores*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*end mostrar errores*/

$tokenUno=-1;
if(isset($_GET['tokenUno'])){
	$tokenUno = intval($_GET['tokenUno']);
}
$tokenAuxDos=-1;
if(isset($_GET['TokenDos'])){
	$tokenAuxDos = intval($_GET['TokenDos']);	
}
/*
$tokenDos=($tokenAuxDos-$tokenUno)/3;//decodificar

//pasar y gestionar fecha
$tokenDosFormateado=date("H:i:s",$tokenDos);
$tokenDosFormateadoAux=explode(":",$tokenDosFormateado);
$horaSegundosAbrir=($tokenDosFormateadoAux[0]*60*60)+($tokenDosFormateadoAux[1]*60)+$tokenDosFormateadoAux[2];

//fecha ahora
$horaActualSegundosAux=explode(":",date("H:i:s"));
$horaActualSegundos=($horaActualSegundosAux[0]*60*60)+($horaActualSegundosAux[1]*60)+$horaActualSegundosAux[2];

//resta
if($horaSegundosAbrir>=$horaActualSegundos){
	$restaSegundos=$horaSegundosAbrir-$horaActualSegundos;
}else{
	$restaSegundos=$horaActualSegundos-$horaSegundosAbrir;
}*/

//margen permitir acceso 0.3 minutos * 60 = 10 segundos
$tiempoSegundosMargen=0.2*60;

if(isset($_GET['accion']) && $tokenUno=="123456" /*&& $restaSegundos>=$tiempoSegundosMargen*/){//codificarlos de alguna manera con fecha y hora para solo ese tiempo y mitigar ataques
	//https://www.modularbox.es/panel/phpincludes/API_reservatupista.php
	
	/* START CONEXION BBDD*/
	$servidor="localhost:3306";
	$usuario="reservatupistacom";
	$clave="34mf7G!LbW";
	$baseDatos="dbs4516290";
	function conectaReservaTuspistaBBDD($servidor,$usuario,$clave,$baseDatos){//conecta a la base de datos
		$mysqli = new mysqli($servidor,$usuario,$clave,$baseDatos);
		$mysqli->query("SET NAMES 'utf8'");
		return $mysqli;
	}
	$conReservaTuPista=conectaReservaTuspistaBBDD($servidor,$usuario,$clave,$baseDatos);
	/* END CONEXION BBDD*/
	
	
	/*---------------------------------------------------------------------------------------------------------------------------------*/
	/*-----------------START metodo para devolver reservas desde un id en adelante-----------------*/
	
	//https://reservatupista.com/api_php/API_conexion_panel_nuevo_pistas_padel.php?tokenUno=123456&TokenDos=4261&accion=obtenerNewReservas&idultimareserva=12&evento=1
	
	//https://www.modularbox.es/panel/phpincludes/API_reservatupista.php
	
	if($_GET['accion']=="obtenerNewReservas" && isset($_GET['idultimareserva']) && ($_GET['evento']==1 || $_GET['evento']==2)){
		$ultimoIdReservaLeido=intval($_GET['idultimareserva']);
		$evento=intval($_GET['evento']);
		
		$consulta="";
		if($ultimoIdReservaLeido>0){
			if($evento==1){//devolver lo nuevo, insert
				$consulta=" WHERE ID>".$ultimoIdReservaLeido;
			}else if($evento==2){//devolver lo ya leido, para update
				$consulta=" WHERE ID<=".$ultimoIdReservaLeido;
			}
		}
		
		if($consulta!=""){
			$arrayDatos=array();
			$patron="SELECT ID,Parent_Id,Created,User,Location,Service,Worker,Status,Start,End,Seats,Gcal_ID,Gcal_Updated,Price,Deposit,Payment_Method FROM wp_base_bookings%s";//ORDER BY ID DESC LIMIT 0,1 
			$sql=sprintf($patron,$consulta);
			$respuesta=mysqli_query($conReservaTuPista,$sql) or die ("Error al buscar 12399");
			if(mysqli_num_rows($respuesta)>0){
				for($i=0;$i<mysqli_num_rows($respuesta);$i++){
					$fila=mysqli_fetch_array($respuesta);

					//limpio cada vez que leo una fila
					$arrayReserva=array();
					//anado objeto al array
					array_push($arrayReserva,$fila[0]);//ID
					array_push($arrayReserva,$fila[1]);//Parent_Id
					array_push($arrayReserva,$fila[2]);//Created
					array_push($arrayReserva,$fila[3]);//User
					array_push($arrayReserva,$fila[4]);//Location
					array_push($arrayReserva,$fila[5]);//Service
					array_push($arrayReserva,$fila[6]);//Worker
					array_push($arrayReserva,$fila[7]);//Status
					array_push($arrayReserva,$fila[8]);//Start (partida)
					array_push($arrayReserva,$fila[9]);//End (partida)

					array_push($arrayReserva,$fila[13]);//Price
					array_push($arrayReserva,$fila[14]);//Deposit
					array_push($arrayReserva,$fila[15]);//Payment_Method

					//anado al array final, el array de cada fila
					array_push($arrayDatos,$arrayReserva);
				}
			}
			mysqli_free_result($respuesta);

			//codificamos el json
			print_r(json_encode($arrayDatos));
			
		}
	}
	/*-----------------END metodo para devolver reservas desde un id en adelante-----------------*/
	
	
	
	/*---------------------------------------------------------------------------------------------------------------------------------*/
	/*-----------------START metodo para devolver los servicios-----------------*/
	//https://reservatupista.com/api_php/API_conexion_panel_nuevo_pistas_padel.php?tokenUno=123456&TokenDos=42861&accion=listadoServicios
	if($_GET['accion']=="listadoServicios"){
		$arrayDatosServicios=array();
		$patron="SELECT ID,Sort_Order,Name,Locations,Capacity,Duration,Price FROM wp_base_services";
		$sql=sprintf($patron);
		$respuesta=mysqli_query($conReservaTuPista,$sql) or die ("Error al buscar 123499");
		if(mysqli_num_rows($respuesta)>0){
			for($i=0;$i<mysqli_num_rows($respuesta);$i++){
				$fila=mysqli_fetch_array($respuesta);
				
				//limpio cada vez que leo una fila
				$arrayServicio=array();
				//anado objeto al array
				array_push($arrayServicio,$fila[0]);//ID
				array_push($arrayServicio,$fila[1]);//Sort_Order
				array_push($arrayServicio,$fila[2]);//Name
				array_push($arrayServicio,$fila[3]);//Locations
				array_push($arrayServicio,$fila[4]);//Capacity
				array_push($arrayServicio,$fila[5]);//Duration
				array_push($arrayServicio,$fila[6]);//Price
				
				//anado al array final
				array_push($arrayDatosServicios,$arrayServicio);
			}
		}
		mysqli_free_result($respuesta);
		
		//codificamos el json
		print_r(json_encode($arrayDatosServicios));
	}
	/*-----------------END metodo para devolver los servicios-----------------*/
	
	
	/*---------------------------------------------------------------------------------------------------------------------------------*/
	/*-----------------START metodo para devolver las localizaciones-----------------*/
	if($_GET['accion']=="listadoLocalizaciones"){
		$arrayDatosLocalizaciones=array();
		$patron="SELECT ID,Sort_Order,Name,Capacity,Price FROM wp_base_locations";
		$sql=sprintf($patron);
		$respuesta=mysqli_query($conReservaTuPista,$sql) or die ("Error al buscar 1234599");
		if(mysqli_num_rows($respuesta)>0){
			for($i=0;$i<mysqli_num_rows($respuesta);$i++){
				$fila=mysqli_fetch_array($respuesta);
				
				//limpio cada vez que leo una fila
				$arrayLocalizacion=array();
				//anado objeto al array
				array_push($arrayLocalizacion,$fila[0]);//ID
				array_push($arrayLocalizacion,$fila[1]);//Sort_Order
				array_push($arrayLocalizacion,$fila[2]);//Name
				array_push($arrayLocalizacion,$fila[3]);//Capacity
				array_push($arrayLocalizacion,$fila[4]);//Price
				
				//anado al array final
				array_push($arrayDatosLocalizaciones,$arrayLocalizacion);
			}
		}
		mysqli_free_result($respuesta);
		
		//codificamos el json
		print_r(json_encode($arrayDatosLocalizaciones));
	}
	/*-----------------END metodo para devolver las localizaciones-----------------*/
	
	
	/*---------------------------------------------------------------------------------------------------------------------------------*/
	/*-----------------START metodo para devolver los usuarios-----------------*/
	if($_GET['accion']=="listadoUsuarios"){
		$arrayDatosUsuariosClientes=array();
		$patron="SELECT ID,User_login,User_Pass,User_Nicename,User_Email,User_Registered,User_Status,Display_Name FROM wp_users";//tabla usuarios
		$sql=sprintf($patron);
		$respuesta=mysqli_query($conReservaTuPista,$sql) or die ("Error al buscar 1234699");
		if(mysqli_num_rows($respuesta)>0){
			for($i=0;$i<mysqli_num_rows($respuesta);$i++){
				$fila=mysqli_fetch_array($respuesta);
				
				//limpio cada vez que leo una fila
				$arrayUsuarioCliente=array();
				//anado objeto al array
				array_push($arrayUsuarioCliente,$fila[0]);//ID
				array_push($arrayUsuarioCliente,$fila[1]);//User_login
				//array_push($arrayUsuarioCliente,$fila[2]);//User_Pass
				array_push($arrayUsuarioCliente,$fila[3]);//User_Nicename
				array_push($arrayUsuarioCliente,$fila[4]);//User_Email
				array_push($arrayUsuarioCliente,$fila[5]);//User_Registered
				array_push($arrayUsuarioCliente,$fila[6]);//User_Status
				array_push($arrayUsuarioCliente,$fila[7]);//Display_Name
				
				//anado al array final
				array_push($arrayDatosUsuariosClientes,$arrayUsuarioCliente);
			}
		}
		mysqli_free_result($respuesta);
		
		//codificamos el json
		print_r(json_encode($arrayDatosUsuariosClientes));
	}
	/*-----------------END metodo para devolver los usuarios-----------------*/
	
	/*---------------------------------------------------------------------------------------------------------------------------------*/
	/*-----------------START metodo para devolver los usuarios-----------------*/
	if($_GET['accion']=="numPistas"){
		$arrayDatosPistasClientes=array();
		$patron="SELECT ID,sort_order,name,dummy,price,services_provided,page FROM wp_base_workers";//tabla usuarios
		$sql=sprintf($patron);
		$respuesta=mysqli_query($conReservaTuPista,$sql) or die ("Error al buscar 1234699");
		if(mysqli_num_rows($respuesta)>0){
			for($i=0;$i<mysqli_num_rows($respuesta);$i++){
				$fila=mysqli_fetch_array($respuesta);
				
				//limpio cada vez que leo una fila
				$arrayPistaCliente=array();
				//anado objeto al array
				array_push($arrayPistaCliente,$fila[0]);//ID
				array_push($arrayPistaCliente,$fila[1]);//sort_order
				array_push($arrayPistaCliente,$fila[2]);//name
				//array_push($arrayPistaCliente,$fila[3]);//dummy
				//array_push($arrayPistaCliente,$fila[4]);//price
				array_push($arrayPistaCliente,$fila[5]);//services_provided
				//array_push($arrayPistaCliente,$fila[6]);//page
				
				//anado al array final
				array_push($arrayDatosPistasClientes,$arrayPistaCliente);
			}
		}
		mysqli_free_result($respuesta);
		
		//codificamos el json
		print_r(json_encode($arrayDatosPistasClientes));
	}
	/*-----------------END metodo para devolver los usuarios-----------------*/
	
	
	/*
	 1-parent_id ---integer---Parent ID of the booking if this is a child booking (0 if left empty)
	 ID principal de la reserva si se trata de una reserva secundaria (0 si se deja en blanco)
	 
	 2-created ---integer/string---Creation date/time as timestamp or date time in any standard format, preferably Y-m-d H:i:s. If left empty, current date/time
	 Fecha/hora de creación como marca de tiempo o fecha y hora en cualquier formato estándar, preferiblemente Y-m-d H:i:s. Si se deja vacío, fecha/hora actual
	 
	 3-user ---integer---Client ID (Wordpress user ID of the client)
	 ID de cliente (ID de usuario de WordPress del cliente)
	 
	 4-location ---integer---Location ID. It must exist in the DB
	 Identificación de ubicación. Debe existir en la base de datos.
	 
	 5-service ---integer---Service ID. It must exist in the DB
	 Identificación de servicio. Debe existir en la base de datos.
	 
	 6-worker ---integer---Worker ID (WordPress user ID of the service provider)
	 ID de trabajador (ID de usuario de WordPress del proveedor de servicios)
	 
	 7-status ---integer---Status of the booking. Must be an existing status (pending, confirmed, paid, running, cart, removed, test, cart). If left empty, "confirmed"
	 Estado de la reserva. Debe ser un estado existente (pendiente, confirmado, pagado, en ejecución, carrito, eliminado, prueba, carrito). Si se deja vacío, "confirmado"
	 
	 8-start ---integer/string---Start of the slot as timestamp or date time in any standard format, preferably Y-m-d H:i:s. If not set, first free time will be used
	 Comienzo de la ranura como marca de tiempo o fecha y hora en cualquier formato estándar, preferiblemente Y-m-d H:i:s. Si no se establece, se utilizará el primer tiempo libre
	 
	 9-end ---integer/string---End of the slot as time stamp or date time. If left empty, end time is calculated from duration of service
	 Fin de la ranura como marca de tiempo o fecha y hora. Si se deja en blanco, la hora de finalización se calcula a partir de la duración del servicio
	 
	 10-price ---string---Price of the booking. Comma and/or point allowed, e.g. $1.234,56
	 Precio de la reserva. Coma y/o punto permitido, por ej. $1.234,56
	 
	 11-deposit ---string---Security deposit for the booking. Comma and/or point allowed, e.g. $1.234,56
	 Depósito de seguridad para la reserva. Coma y/o punto permitido, por ej. $1.234,56
	 
	 12-payment_method ---string---Name of the payment method for the booking. Common values: manual-payments, paypal-standard, stripe, paymill
	 Nombre del método de pago de la reserva. Valores comunes: pagos manuales, paypal-estándar, stripe, paymill
	 
	*/
	
}else{
	print_r(json_encode("No entras pimo."));
}
?>