<?php 

//establecer por defecto
date_default_timezone_set('Europe/Madrid');
/*
session_name("modularboxpanel");
session_start([
  'read_and_close'  => false,
]);*/

$restringir="";
if(isset($_GET["p"])){
	$restringir=$_GET["p"];//ruta.php?p=h9.JModuRALh89
}

if($restringir=="h9.JModuRALh89"){
	require_once("../const/constantes.php");
	require_once("phpgeneral.php");
	require_once("phppropios.php");
	require_once("phppistaspadel.php");


	/*start mostrar errores*/
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
	/*end mostrar errores*/


	$tokenUno=123456;
	$tokenDos=(strtotime("now")*3)+$tokenUno;

	//https://panel.modularbox.com/phpincludes/API_reservatupista.php?p=h9.JModuRALh89

	/*START recuperar y actualizar datos*/
	crearReservasLeerNuevas($tokenUno,$tokenDos,1,$con);//1 solo lo nuevo, 2 antiguo para update
	crearServiciosLeer($tokenUno,$tokenDos,$con);
	crearLocalizacionesLeer($tokenUno,$tokenDos,$con);
	crearUsuariosClientesLeer($tokenUno,$tokenDos,$con);
	crearNumeroPistasClientesLeer($tokenUno,$tokenDos,$con);//num pistas
	/*END recuperar y actualizar datos*/
}else{
	header("Location: https://panel.modularbox.com/");
}

//reservas nuevas
function crearReservasLeerNuevas($tokenUno,$tokenDos,$accion,$con){
	
	//ver lo que traer nuevo
	$consultaUltimoTraer=0;
	$patron="SELECT idbbddplugin FROM pistaspadel_reservas ORDER BY idbbddplugin DESC,id DESC LIMIT 0,1";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 3455675666444400956563454545");
	if(mysqli_num_rows($respuesta)>0){
		$fila=mysqli_fetch_array($respuesta);
		$consultaUltimoTraer=$fila[0];
	}
	mysqli_free_result($respuesta);
	
	//1 listar nuevas
	//2 traer antiguas actualizar
	if($accion!=1 && $accion!=2){
		$accion=1;
	}
	
	//url consumir
	$urlAPI='https://reservatupista.com/api_php/API_conexion_panel_nuevo_pistas_padel.php?tokenUno='.$tokenUno.'&TokenDos='.$tokenDos.'&accion=obtenerNewReservas&idultimareserva='.$consultaUltimoTraer.'&evento='.$accion;
	//echo $urlAPI."<br>";
	
    // Initiate curl session
    $handle = curl_init();
    // Will return the response, if false it prints the response
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    // Set the url
    curl_setopt($handle, CURLOPT_URL,$urlAPI);
    // Execute the session and store the contents in $datos
    $datos=curl_exec($handle);
    // Closing the session
    curl_close($handle);

    $reservas = json_decode($datos,true);
	//var_dump($reservas);
	
	//recorrer
	for($i=0;$i<count($reservas);$i++){
		//var_dump($reservas[$i]);echo "<br><br>";//tengo la reserva
		
		//
		if($reservas[$i][0]!=""){
			$idbbddplugin=$reservas[$i][0];
		}else{
			$idbbddplugin=0;
		}
		//
		if($reservas[$i][1]!=""){
			$parentid=$reservas[$i][1];
		}else{
			$parentid=0;
		}
		//
		$creado=$reservas[$i][2];
		//
		if($reservas[$i][3]!=""){
			$usuario=$reservas[$i][3];
		}else{
			$usuario=0;
		}
		//
		if($reservas[$i][4]!=""){
			$localizacion=$reservas[$i][4];
		}else{
			$localizacion=0;
		}
		//
		if($reservas[$i][5]!=""){
			$servicio=$reservas[$i][5];
		}else{
			$servicio=0;
		}
		//
		if($reservas[$i][6]!=""){
			$idNumPistaCliente=$reservas[$i][6];
		}else{
			$idNumPistaCliente=0;
		}
		//
		$estado=$reservas[$i][7];
		//
		$startpartida=$reservas[$i][8];
		//
		$endpartida=$reservas[$i][9];
		//
		if($reservas[$i][10]!=""){
			$precio=$reservas[$i][10];
		}else{
			$precio=0;
		}
		//
		if($reservas[$i][11]!=""){
			$deposito=$reservas[$i][11];
		}else{
			$deposito=0;
		}
		//
		$metodopago=$reservas[$i][12];
		
		//comprobar si no existe
		$patron1="SELECT id FROM pistaspadel_reservas WHERE idbbddplugin=\"%s\"";
		$sql1=sprintf($patron1,$reservas[$i][0]);
		$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 345567566600956563454545");
		if(mysqli_num_rows($respuesta1)>0){
			$fila1=mysqli_fetch_array($respuesta1);
			
			//update
			$patron2="UPDATE pistaspadel_reservas SET idbbddplugin=\"%s\",parentid=\"%s\",creado=\"%s\",usuario=\"%s\",localizacion=\"%s\",servicio=\"%s\",idnumpistacliente=\"%s\",estadao=\"%s\",startpartida=\"%s\",endpartida=\"%s\",precio=\"%s\",deposito=\"%s\",metodopago=\"%s\" WHERE id=\"%s\"";
			$sql2=sprintf($patron2,$idbbddplugin,$parentid,$creado,$usuario,$localizacion,$servicio,$idNumPistaCliente,$estado,$startpartida,$endpartida,$precio,$deposito,$metodopago,$fila1[0]);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 1234555677");
		}else{
			//crear
			$patron2="INSERT INTO pistaspadel_reservas SET idbbddplugin=\"%s\",parentid=\"%s\",creado=\"%s\",usuario=\"%s\",localizacion=\"%s\",servicio=\"%s\",idnumpistacliente=\"%s\",estadao=\"%s\",startpartida=\"%s\",endpartida=\"%s\",precio=\"%s\",deposito=\"%s\",metodopago=\"%s\",idpin=0,borrado=\"n\",fechaalta=\"%s\"";
			$sql2=sprintf($patron2,$idbbddplugin,$parentid,$creado,$usuario,$localizacion,$servicio,$idNumPistaCliente,$estado,$startpartida,$endpartida,$precio,$deposito,$metodopago,date("Y-m-d"));
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 12345677");
			$idReservaPadel=mysqli_insert_id($con);
			
			/*START crear la accion de apertura automática*/
			//echo $startpartida."--".$estado."--".$idReservaPadel."--".$localizacion."<br>";
			if(/*intval($usuario)>0 &&*/ $startpartida!="" && $estado!="removed" && $idReservaPadel>0 && $localizacion>0){
				//comprobar si no existe
				$patron100="SELECT idusuario,idbbddplugin FROM pistaspadel_localizaciones WHERE idbbddplugin=\"%s\"";
				$sql100=sprintf($patron100,$localizacion);
				$respuesta100=mysqli_query($con,$sql100) or die ("Error al buscar 345567566600956563451003454545");
				if(mysqli_num_rows($respuesta100)>0){
					$fila100=mysqli_fetch_array($respuesta100);
					$idClienteUsuario=$fila100[0];
					if(intval($idClienteUsuario)>0){
						
						//servicio
						/*$patron102="SELECT idusuario FROM pistaspadel_servicios WHERE localizacion=\":%s:\"";
						$sql102=sprintf($patron102,$fila100[1]);
						$respuesta102=mysqli_query($con,$sql102) or die ("Error al buscar 345567566600956563451003454510210245");
						if(mysqli_num_rows($respuesta102)>0){
							$fila102=mysqli_fetch_array($respuesta102);
						}
						mysqli_free_result($respuesta102);*/
							
						
						//numero pista
						$idPistaNodo=0;
						$patron103="SELECT id FROM pistaspadel_numpistasclientes WHERE idbbddplugin=\"%s\"";
						$sql103=sprintf($patron103,$idNumPistaCliente);
						$respuesta103=mysqli_query($con,$sql103) or die ("Error al buscar 345567566601031030956563451003454510210245");
						if(mysqli_num_rows($respuesta103)>0){
							$fila103=mysqli_fetch_array($respuesta103);
							$idPistaNodo=$fila103[0];
						}
						mysqli_free_result($respuesta103);
						
						if(intval($idPistaNodo)>0){
							$auxStartPartida=explode(" ",$startpartida);
						
							$horaPartida=$auxStartPartida[1];
							$fechaPartida=$auxStartPartida[0];
							$clienteUsuario=$fila100[0];
							$idNodo=0;
							$minutosPartida=4;
							
							$patron101="SELECT id,tiempopartida FROM pistaspadel_nodos WHERE idusuario=\"%s\" AND guardado=\"s\" AND borrado=\"n\" AND idserviciopistarel=\"%s\" ";
							$sql101=sprintf($patron101,$idClienteUsuario,$idPistaNodo);
							$respuesta101=mysqli_query($con,$sql101) or die ("Error al buscar 3455675666009554566563451003454577");
							if(mysqli_num_rows($respuesta101)>0){
								$fila101=mysqli_fetch_array($respuesta101);
								$idNodo=$fila101[0];
								$minutosPartida=$fila101[1];

								$patron99="INSERT INTO pistaspadel_historial SET idnodo=\"%s\",puerta=\"amb\",tipo=\"2\",idacceso=0,horaalta=\"%s\",fechaalta=\"%s\",idusuario=\"%s\",accionrealizada=\"n\",miradoplaca=\"n\",minutospartida=\"%s\",idreservapadel=\"%s\",precio=\"%s\"";
								$sql99=sprintf($patron99,$idNodo,$horaPartida,$fechaPartida,$clienteUsuario,$minutosPartida,$idReservaPadel,$precio);
								$respuesta99=mysqli_query($con,$sql99) or die ("Error al buscar 1334663356745675676996537534565763");

								//FALTA CONTROLAR SI DESPUES HAY OTRA PARTIDA, PARA FINALIZAR LA ANTERIOR JUSTO A LA HORA DE FIN

							}
							mysqli_free_result($respuesta101);
						}
						
					}
				}
				mysqli_free_result($respuesta100);
			}
			/*END crear la accion de apertura automática*/
			
		}
		mysqli_free_result($respuesta1);
		
		//for($j=0;$j<count($reservas[$i]);$j++){
			//$reservas[$i][$j];
		//}
	}
}

//servicios
function crearServiciosLeer($tokenUno,$tokenDos,$con){

	//url consumir
	$data = file_get_contents("https://reservatupista.com/api_php/API_conexion_panel_nuevo_pistas_padel.php?tokenUno=".$tokenUno."&TokenDos=".$tokenDos."&accion=listadoServicios");
	
	$servicios = json_decode($data, true);
	
	//recorrer
	for($i=0;$i<count($servicios);$i++){
		//var_dump($servicios[$i]);//tengo la reserva
		
		//
		if($servicios[$i][0]!=""){
			$idbbddplugin=$servicios[$i][0];
		}else{
			$idbbddplugin=0;
		}
		//
		if($servicios[$i][1]!=""){
			$ordenclasificacion=$servicios[$i][1];
		}else{
			$ordenclasificacion=0;
		}
		//
		$nombre=$servicios[$i][2];
		//
		$localizacion=$servicios[$i][3];
		//
		if($servicios[$i][4]!=""){
			$capacidad=$servicios[$i][4];
		}else{
			$capacidad=0;
		}
		//
		if($servicios[$i][5]!=""){
			$duracion=$servicios[$i][5];
		}else{
			$duracion=0;
		}
		//
		if($servicios[$i][6]!=""){
			$precio=$servicios[$i][6];
		}else{
			$precio=0;
		}
		
		//comprobar si no existe
		$patron1="SELECT id FROM pistaspadel_servicios WHERE idbbddplugin=\"%s\"";
		$sql1=sprintf($patron1,$servicios[$i][0]);
		$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 34556734345666004545454956563454545");
		if(mysqli_num_rows($respuesta1)>0){
			$fila1=mysqli_fetch_array($respuesta1);
			
			//update
			$patron2="UPDATE pistaspadel_servicios SET idbbddplugin=\"%s\",ordenclasificacion=\"%s\",nombre=\"%s\",localizacion=\"%s\",capacidad=\"%s\",duracion=\"%s\",precio=\"%s\" WHERE id=\"%s\"";
			$sql2=sprintf($patron2,$idbbddplugin,$ordenclasificacion,$nombre,$localizacion,$capacidad,$duracion,$precio,$fila1[0]);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 123456773434");
		}else{
			//crear
			$patron2="INSERT INTO pistaspadel_servicios SET idbbddplugin=\"%s\",ordenclasificacion=\"%s\",nombre=\"%s\",localizacion=\"%s\",capacidad=\"%s\",duracion=\"%s\",precio=\"%s\",borrado=\"n\",fechaalta=\"%s\"";
			$sql2=sprintf($patron2,$idbbddplugin,$ordenclasificacion,$nombre,$localizacion,$capacidad,$duracion,$precio,date("Y-m-d"));
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 123456773434");
		}
		mysqli_free_result($respuesta1);
	}
}

//localizaciones
function crearLocalizacionesLeer($tokenUno,$tokenDos,$con){

	//url consumir
	$data = file_get_contents("https://reservatupista.com/api_php/API_conexion_panel_nuevo_pistas_padel.php?tokenUno=".$tokenUno."&TokenDos=".$tokenDos."&accion=listadoLocalizaciones");
	
	$localizaciones = json_decode($data, true);
	
	//recorrer
	for($i=0;$i<count($localizaciones);$i++){
		//var_dump(localizaciones[$i]);//tengo la reserva
		
		//
		if($localizaciones[$i][0]!=""){
			$idbbddplugin=$localizaciones[$i][0];
		}else{
			$idbbddplugin=0;
		}
		//
		if($localizaciones[$i][1]!=""){
			$ordenclasificacion=$localizaciones[$i][1];
		}else{
			$ordenclasificacion=0;
		}
		//
		$nombre=$localizaciones[$i][2];
		//
		if($localizaciones[$i][3]!=""){
			$capacidad=$localizaciones[$i][3];
		}else{
			$capacidad=0;
		}
		//
		if($localizaciones[$i][4]!=""){
			$precio=$localizaciones[$i][4];
		}else{
			$precio=0;
		}
		
		//comprobar si no existe
		$patron1="SELECT id FROM pistaspadel_localizaciones WHERE idbbddplugin=\"%s\"";
		$sql1=sprintf($patron1,$localizaciones[$i][0]);
		$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 3455673434564568823456600454545495656");
		if(mysqli_num_rows($respuesta1)>0){
			$fila1=mysqli_fetch_array($respuesta1);
			
			//update
			$patron2="UPDATE pistaspadel_localizaciones SET idbbddplugin=\"%s\",ordenclasificacion=\"%s\",nombre=\"%s\",capacidad=\"%s\",precio=\"%s\" WHERE id=\"%s\"";
			$sql2=sprintf($patron2,$idbbddplugin,$ordenclasificacion,$nombre,$capacidad,$precio,$fila1[0]);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 1234567745434347");
		}else{
			//crear
			$patron2="INSERT INTO pistaspadel_localizaciones SET idbbddplugin=\"%s\",ordenclasificacion=\"%s\",nombre=\"%s\",capacidad=\"%s\",precio=\"%s\",borrado=\"n\",fechaalta=\"%s\",idusuario=0";
			$sql2=sprintf($patron2,$idbbddplugin,$ordenclasificacion,$nombre,$capacidad,$precio,date("Y-m-d"));
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 12345677311234345");
		}
		mysqli_free_result($respuesta1);
	}
}

//usuarios/clientes
function crearUsuariosClientesLeer($tokenUno,$tokenDos,$con){

	//url consumir
	$data = file_get_contents("https://reservatupista.com/api_php/API_conexion_panel_nuevo_pistas_padel.php?tokenUno=".$tokenUno."&TokenDos=".$tokenDos."&accion=listadoUsuarios");
	
	$usuariosClientes = json_decode($data, true);
	
	//recorrer
	for($i=0;$i<count($usuariosClientes);$i++){
		//var_dump(usuariosClientes[$i]);//tengo la reserva
		
		//
		if($usuariosClientes[$i][0]!=""){
			$idbbddplugin=$usuariosClientes[$i][0];
		}else{
			$idbbddplugin=0;
		}
		//
		$usuariologin=$usuariosClientes[$i][1];
		//
		$nombreusuario=$usuariosClientes[$i][2];
		//
		$emailusuario=$usuariosClientes[$i][3];
		//
		$fecharegistrousuario=$usuariosClientes[$i][4];
		//
		if($usuariosClientes[$i][5]!=""){
			$estadousuario=$usuariosClientes[$i][5];
		}else{
			$estadousuario=0;
		}
		//
		$nombredisplayusuario=$usuariosClientes[$i][6];
		
		//comprobar si no existe
		$patron1="SELECT id FROM pistaspadel_usuariosclientes WHERE idbbddplugin=\"%s\"";
		$sql1=sprintf($patron1,$usuariosClientes[$i][0]);
		$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 345567233434564568823456600454545495656");
		if(mysqli_num_rows($respuesta1)>0){
			$fila1=mysqli_fetch_array($respuesta1);
			
			//update
			$patron2="UPDATE pistaspadel_usuariosclientes SET idbbddplugin=\"%s\",usuariologin=\"%s\",nombreusuario=\"%s\",emailusuario=\"%s\",fecharegistrousuario=\"%s\",estadousuario=\"%s\",nombredisplayusuario=\"%s\" WHERE id=\"%s\"";
			$sql2=sprintf($patron2,$idbbddplugin,$usuariologin,$nombreusuario,$emailusuario,$fecharegistrousuario,$estadousuario,$nombredisplayusuario,$fila1[0]);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 123456774256543434");
		}else{
			//crear
			$patron2="INSERT INTO pistaspadel_usuariosclientes SET idbbddplugin=\"%s\",usuariologin=\"%s\",nombreusuario=\"%s\",emailusuario=\"%s\",fecharegistrousuario=\"%s\",estadousuario=\"%s\",nombredisplayusuario=\"%s\",borrado=\"n\",fechaalta=\"%s\"";
			$sql2=sprintf($patron2,$idbbddplugin,$usuariologin,$nombreusuario,$emailusuario,$fecharegistrousuario,$estadousuario,$nombredisplayusuario,date("Y-m-d"));
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 123456773112254403434");
		}
		mysqli_free_result($respuesta1);
	}
}

//numero pistas usuarios
function crearNumeroPistasClientesLeer($tokenUno,$tokenDos,$con){

	//url consumir
	$data = file_get_contents("https://reservatupista.com/api_php/API_conexion_panel_nuevo_pistas_padel.php?tokenUno=".$tokenUno."&TokenDos=".$tokenDos."&accion=numPistas");
	
	$numPistasClientes = json_decode($data, true);
	
	//recorrer
	for($i=0;$i<count($numPistasClientes);$i++){
		//var_dump(numPistasClientes[$i]);//tengo la reserva
		
		//
		if($numPistasClientes[$i][0]!=""){
			$idbbddplugin=$numPistasClientes[$i][0];
		}else{
			$idbbddplugin=0;
		}
		//
		$sortOrder=$numPistasClientes[$i][1];
		//
		$nombrePista=$numPistasClientes[$i][2];
		//
		$servicesProvided=$numPistasClientes[$i][3];
		//
		
		
		//comprobar si no existe
		$patron1="SELECT id FROM pistaspadel_numpistasclientes WHERE idbbddplugin=\"%s\"";
		$sql1=sprintf($patron1,$numPistasClientes[$i][0]);
		$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 345567233434564568823456645676700454545495656");
		if(mysqli_num_rows($respuesta1)>0){
			$fila1=mysqli_fetch_array($respuesta1);
			
			//update
			$patron2="UPDATE pistaspadel_numpistasclientes SET idbbddplugin=\"%s\",sortorder=\"%s\",nombrepista=\"%s\",servicesprovided=\"%s\" WHERE id=\"%s\"";
			$sql2=sprintf($patron2,$idbbddplugin,$sortOrder,$nombrePista,$servicesProvided,$fila1[0]);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 12345677425654345676764347");
		}else{
			//crear
			$patron2="INSERT INTO pistaspadel_numpistasclientes SET idbbddplugin=\"%s\",sortorder=\"%s\",nombrepista=\"%s\",servicesprovided=\"%s\",borrado=\"n\",fechaalta=\"%s\"";
			$sql2=sprintf($patron2,$idbbddplugin,$sortOrder,$nombrePista,$servicesProvided,date("Y-m-d"));
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 12345677456743112254403434908");
		}
		mysqli_free_result($respuesta1);
	}
}
?>