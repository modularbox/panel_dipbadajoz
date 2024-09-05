<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');

session_name("modularboxpanel");
session_start([
  'read_and_close'  => false,
]);

require_once("const/constantes.php");
require_once("phpincludes/phpgeneral.php");
require_once("phpincludes/phppropios.php");

if(usuarioCorrecto($con) && is_numeric($_GET["o"])){
	switch($_GET["o"]){
		case 1:
			infHistorialSafeyPuertas($con);
		break;
		case 2:
			infHistorialAutomatizacionSalidas($con);
		break;
		case 3:
			puertasPistaPadelHistorialExcel($con);
		break;
        case 4:
			infHistorialFallidoSafeyPuertas($con);
		break;
        case 5:
			//infListadoCredencialesSafey($con);
			infListadoCredencialesSafeyDos($con);
		break;
        case 6:
			infPagosAccesoSafey($con);
		break;
		default:
			echo "opcion incorrecta al generar informe, ".$_GET["o"];
		break;
	}
}


//listado pagos del acceso de safey
function infPagosAccesoSafey($con){
	$tituloText="Pagos_acceso_usuario_";
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		//todo el administrador
	}else if($_SESSION["permisossession"]==2){//solo lo suyo
		$consulta=" AND safey_accesos.idusuario=\"".$_SESSION["idusersession"]."\" ";
	}else{
		//nadie ver nada
		$consulta=" AND safey_accesos.idusuario=\"-99\" ";
	}
	
	//filtros
	if(isset($_SESSION["idAccesoHistorialPagoAcceso"])){
		$consulta.=" AND id=".$_SESSION["idAccesoHistorialPagoAcceso"];		
	}
	/*
	$_SESSION["fInicioHistorialPagoAcceso"]=quitaComillasD($_POST["fechaIni"]);
                $_SESSION["fFinHistorialPagoAcceso"]=quitaComillasD($_POST["fechaFin"]);
                $_SESSION["puertaHistorialPagoAcceso"]=intval(quitaComillasD($_POST["puerta"]));		
                $_SESSION["nodoHistorialPagoAcceso"]=intval(quitaComillasD($_POST["idNodo"]));
	*/
	
	
	cabeceraExcel($tituloText.date("Y-m-d"));
	css_Excel();
	
	$color3="#3EDD7C;";
	
	echo "<body>";
	echo utf8_decode("<table><tr>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>NOMBRE APELLIDOS</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Email</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>IMPORTE</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>FECHA PAGO</td>
			  </tr>");
	
	$patron="SELECT id,nombre,apellidos,email FROM safey_accesos WHERE guardado=\"s\" AND borrado=\"n\"%s";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632346565784546454565469963455899");
	if(mysqli_num_rows($respuesta)>0){
		//for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$nombreApellidosAcceso=$fila[1]." ".$fila[2];
			$emailAcceso=$fila[3];
			
				
			//recorrer pagos
			$patron1="SELECT id,total,fechapago,pagado FROM safey_pagos WHERE idacceso=\"%s\" AND borrado=\"n\" ORDER BY fechapago DESC";
			$sql1=sprintf($patron1,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9632346546565784546454565469963455899");
			if(mysqli_num_rows($respuesta1)>0){
				for($j=0;$j<mysqli_num_rows($respuesta1);$j++){
					$fila1=mysqli_fetch_array($respuesta1);

					printf("<tr>
								<td class='char'>%s</td>
								<td class='char'>%s</td>
								<td class='char'>%s</td>
								<td class='char'>%s</td></tr>",$nombreApellidosAcceso,$emailAcceso,floatval($fila1[1]),convierteFechaBarra($fila1[2]));



				}
			}
			mysqli_free_result($respuesta1);
		
			
		//}//cierro primer for
	}
	mysqli_free_result($respuesta);
	
	
	echo "</table>";
	echo "</tbody>";
		
}

//listado credenciales usuarios safey
function infListadoCredencialesSafeyDos($con){
	$tituloText="Credenciales_usuarios_safey_";
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		//todo o lo seleccionado
		if(isset($_SESSION["usuarioSelecCredencialesSafey"]) && $_SESSION["usuarioSelecCredencialesSafey"]>0  ){
			$consulta=" AND safey_accesos.idusuario=\"".$_SESSION["usuarioSelecCredencialesSafey"]."\" ";
		}
	}else if($_SESSION["permisossession"]==2){//solo lo suyo
		$consulta=" AND safey_accesos.idusuario=\"".$_SESSION["idusersession"]."\" ";
	}else{
		//nadie ver nada
		$consulta=" AND safey_accesos.idusuario=\"-99\" ";
	}
	
	cabeceraExcel($tituloText.date("Y-m-d"));
	css_Excel();
	
	$color3="#3EDD7C;";
	
	echo "<body>";
	echo utf8_decode("<table><tr>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Nº</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>NOMBRE</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>APELLIDOS</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>DNI</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>MOVIL</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>EMAIL</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>RESIDENTE/VECINO</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>IMPORTE</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>FECHA</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>PIN</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>ESTADO PIN</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>PAGO COMPLETADO</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>JUNIO FECHA PAGO</td>
			  </tr>");
	
  	$patron="SELECT safey_accesos.id,safey_accesos.nombre,safey_accesos.pin,safey_accesos.pinactivo,safey_accesos.llave,safey_accesos.llaveactivo,safey_accesos.mando,safey_accesos.mandoactivo,safey_accesos.maillogin,safey_accesos.mailloginactivo,safey_accesos.apellidos,safey_accesos.dni,safey_accesos.telefono,safey_accesos.email,safey_accesos.residente FROM safey_accesos WHERE safey_accesos.guardado=\"s\" AND safey_accesos.borrado=\"n\"%s ORDER BY safey_accesos.id DESC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632346565789963455899");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//pin
			$pin="";
			if($fila[2]>0){
				$patron3="SELECT pin FROM safey_credenciales_pin WHERE id=\"%s\"";
				$sql3=sprintf($patron3,$fila[2]);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 9635349956566345002286454509258");
				if(mysqli_num_rows($respuesta3)>0){
					$fila3=mysqli_fetch_array($respuesta3);
					$pin=$fila3[0];
				}
				mysqli_free_result($respuesta3);
			}
			if($fila[3]=="on"){
				$pinEstado="ON";
			}else{
				$pinEstado="OFF";
			}
			//llave
			$llave="";
			if($fila[4]>0){
				$patron4="SELECT descripcion FROM safey_credenciales_llaves WHERE id=\"%s\"";
				$sql4=sprintf($patron4,$fila[4]);
				$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 96353456566367899045002286454509258");
				if(mysqli_num_rows($respuesta4)>0){
					$fila4=mysqli_fetch_array($respuesta4);
					$llave=$fila4[0];
				}
				mysqli_free_result($respuesta4);
			}
			if($fila[5]=="on"){
				$llaveEstado="ON";
			}else{
				$llaveEstado="OFF";
			}
			//mando
			$mando="";
			if($fila[6]>0){
				$patron5="SELECT mandoserie FROM safey_credenciales_mandos WHERE id=\"%s\"";
				$sql5=sprintf($patron5,$fila[6]);
				$respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 9635345656637893545002286454509258");
				if(mysqli_num_rows($respuesta5)>0){
					$fila5=mysqli_fetch_array($respuesta5);
					$mando=$fila5[0];
				}
				mysqli_free_result($respuesta5);
			}
			if($fila[7]=="on"){
				$mandoEstado="ON";
			}else{
				$mandoEstado="OFF";
			}
			//web
			$webUsuario="";
			if($fila[8]>0){
				$patron6="SELECT email FROM usuarios WHERE id=\"%s\"";
				$sql6=sprintf($patron6,$fila[8]);
				$respuesta6=mysqli_query($con,$sql6) or die ("Error al buscar 9635345656663545002286454509258");
				if(mysqli_num_rows($respuesta6)>0){
					$fila6=mysqli_fetch_array($respuesta6);
					$webUsuario=$fila6[0];
				}
				mysqli_free_result($respuesta6);
			}
			if($fila[9]=="on"){
				$webEstado="ON";
			}else{
				$webEstado="OFF";
			}
			      
			$residente="No";
			if($fila[14]=="s"){
				$residente="Sí";
			}
			
			/*START calcular pago del mes al descargar, EN DESARROLLO*/
			$fechaInicio=date("Y-m")."-01";
			
			// Crear un objeto DateTime para el último día del mes especificado
			$lastDayOfMonth = new DateTime(date("Y-m")."-01");
			$lastDayOfMonth->modify('last day of this month');
			// Formatear la fecha como 'Y-m-d' (año-mes-día)
			$fechaFin = $lastDayOfMonth->format('Y-m-d');
			
			$fechaPagoMesDescargar="";
			$importePagoMesDescargar="";
			
			$patron7="SELECT total FROM safey_pagos WHERE id=\"%s\"";
			$sql7=sprintf($patron7,$fila[0]);
			$respuesta7=mysqli_query($con,$sql7) or die ("Error al buscar 9637765666354507702286454509258");
			if(mysqli_num_rows($respuesta7)>0){
				$fila7=mysqli_fetch_array($respuesta7);
				$importePagoMesDescargar=floatval($fila7[0]);
			}
			mysqli_free_result($respuesta7);
			/*END calcular pago del mes al descargar, EN DESARROLLO*/
			
            printf("<tr>
                        <td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td></tr>",$fila[0],utf8_decode($fila[1]),utf8_decode($fila[10]),utf8_decode($fila[11]),utf8_decode($fila[12]),utf8_decode($fila[13]),utf8_decode($residente),"","",$pin,$pinEstado,"","");
			
		}
	}
	echo "</table>";
	echo "</tbody>";
		
	mysqli_free_result($respuesta);
}

//listado credenciales usuarios safey
function infListadoCredencialesSafey($con){
	$tituloText="Credenciales_usuarios_safey_";
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		//todo o lo seleccionado
		if(isset($_SESSION["usuarioSelecCredencialesSafey"]) && $_SESSION["usuarioSelecCredencialesSafey"]>0  ){
			$consulta=" AND safey_accesos.idusuario=\"".$_SESSION["usuarioSelecCredencialesSafey"]."\" ";
		}
	}else if($_SESSION["permisossession"]==2){//solo lo suyo
		$consulta=" AND safey_accesos.idusuario=\"".$_SESSION["idusersession"]."\" ";
	}else{
		//nadie ver nada
		$consulta=" AND safey_accesos.idusuario=\"-99\" ";
	}
	
	cabeceraExcel($tituloText.date("Y-m-d"));
	css_Excel();
	
	$color3="#3EDD7C;";
	
	echo "<body>";
	echo utf8_decode("<table><tr>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Nombre</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Pin</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Estado Pin</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Llave</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Estado Llave</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Mando</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Estado Mando</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Web</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Estado Web</td>
			  </tr>");
	
  	$patron="SELECT safey_accesos.id,safey_accesos.nombre,safey_accesos.pin,safey_accesos.pinactivo,safey_accesos.llave,safey_accesos.llaveactivo,safey_accesos.mando,safey_accesos.mandoactivo,safey_accesos.maillogin,safey_accesos.mailloginactivo,safey_accesos.apellidos FROM safey_accesos WHERE safey_accesos.guardado=\"s\" AND safey_accesos.borrado=\"n\"%s ORDER BY safey_accesos.id DESC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632346565789963455899");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//pin
			$pin="";
			if($fila[2]>0){
				$patron3="SELECT pin FROM safey_credenciales_pin WHERE id=\"%s\"";
				$sql3=sprintf($patron3,$fila[2]);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 9635349956566345002286454509258");
				if(mysqli_num_rows($respuesta3)>0){
					$fila3=mysqli_fetch_array($respuesta3);
					$pin=$fila3[0];
				}
				mysqli_free_result($respuesta3);
			}
			if($fila[3]=="on"){
				$pinEstado="ON";
			}else{
				$pinEstado="OFF";
			}
			//llave
			$llave="";
			if($fila[4]>0){
				$patron4="SELECT descripcion FROM safey_credenciales_llaves WHERE id=\"%s\"";
				$sql4=sprintf($patron4,$fila[4]);
				$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 96353456566367899045002286454509258");
				if(mysqli_num_rows($respuesta4)>0){
					$fila4=mysqli_fetch_array($respuesta4);
					$llave=$fila4[0];
				}
				mysqli_free_result($respuesta4);
			}
			if($fila[5]=="on"){
				$llaveEstado="ON";
			}else{
				$llaveEstado="OFF";
			}
			//mando
			$mando="";
			if($fila[6]>0){
				$patron5="SELECT mandoserie FROM safey_credenciales_mandos WHERE id=\"%s\"";
				$sql5=sprintf($patron5,$fila[6]);
				$respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 9635345656637893545002286454509258");
				if(mysqli_num_rows($respuesta5)>0){
					$fila5=mysqli_fetch_array($respuesta5);
					$mando=$fila5[0];
				}
				mysqli_free_result($respuesta5);
			}
			if($fila[7]=="on"){
				$mandoEstado="ON";
			}else{
				$mandoEstado="OFF";
			}
			//web
			$webUsuario="";
			if($fila[8]>0){
				$patron6="SELECT email FROM usuarios WHERE id=\"%s\"";
				$sql6=sprintf($patron6,$fila[8]);
				$respuesta6=mysqli_query($con,$sql6) or die ("Error al buscar 9635345656663545002286454509258");
				if(mysqli_num_rows($respuesta6)>0){
					$fila6=mysqli_fetch_array($respuesta6);
					$webUsuario=$fila6[0];
				}
				mysqli_free_result($respuesta6);
			}
			if($fila[9]=="on"){
				$webEstado="ON";
			}else{
				$webEstado="OFF";
			}
			           
            printf("<tr>
                        <td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td></tr>",utf8_decode($fila[1]." ".$fila[10]),$pin,$pinEstado,$llave,$llaveEstado,$mando,$mandoEstado,$webUsuario,$webEstado);
			
		}
	}
	echo "</table>";
	echo "</tbody>";
		
	mysqli_free_result($respuesta);
}

//historial puertas pista padel
function puertasPistaPadelHistorialExcel($con){
	
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialPuertasPistasPadel"]) && isset($_SESSION["fechaFinHistorialPuertasPistasPadel"]) ){
		$consulta=" AND pistaspadel_historial.fechaalta>=\"".$_SESSION["fechaIniHistorialPuertasPistasPadel"]."\" AND pistaspadel_historial.fechaalta<=\"".$_SESSION["fechaFinHistorialPuertasPistasPadel"]."\"";
	}
	
	$idNodo=0;
	if(isset($_SESSION["idNodoPuertaPistaPadel"])){
		$idNodo=$_SESSION["idNodoPuertaPistaPadel"];
	}
	
	
	$nombreNodoFichero="";
	$patron99="SELECT pistaspadel_nodos.nombre FROM pistaspadel_historial,pistaspadel_nodos WHERE pistaspadel_historial.idnodo=\"%s\" AND pistaspadel_historial.idnodo=pistaspadel_nodos.id AND pistaspadel_nodos.guardado=\"s\" AND pistaspadel_nodos.borrado=\"n\"%s ORDER BY pistaspadel_historial.fechaalta DESC, pistaspadel_historial.horaalta DESC, pistaspadel_historial.id DESC";//LIMIT 0,50
	$sql99=sprintf($patron99,$idNodo,$consulta);
	$respuesta99=mysqli_query($con,$sql99) or die ("Error al buscar 96324564999563463455899");
	if(mysqli_num_rows($respuesta99)>0){
		for($i=0;$i<mysqli_num_rows($respuesta99);$i++){
			$fila99=mysqli_fetch_array($respuesta99);
			$nombreNodoFichero=$fila99[0];
		}
	}
	mysqli_free_result($respuesta99);
	
	$tituloText="Historial_pistas_de_padel_".$nombreNodoFichero."_".$_SESSION["fechaIniHistorialPuertasPistasPadel"]."_".$_SESSION["fechaFinHistorialPuertasPistasPadel"];
	
	
	cabeceraExcel($tituloText.date("Y-m-d"));
	css_Excel();
	
	$color3="#3EDD7C;";
	
	echo "<body>";
	echo utf8_decode("<table><tr>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Nodo</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Puerta</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Dispositivo</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Nombre Acceso</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Acción</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Partida</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Precio</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Fecha</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Hora</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Resultado</td>
			  </tr>");
	
	$patron="SELECT pistaspadel_historial.id,pistaspadel_historial.puerta,pistaspadel_historial.tipo,pistaspadel_historial.idacceso,pistaspadel_historial.idusuario,pistaspadel_historial.horaalta,pistaspadel_historial.fechaalta,pistaspadel_nodos.id,pistaspadel_historial.accionrealizada,pistaspadel_historial.miradoplaca,pistaspadel_historial.modo,pistaspadel_historial.idreservapadel,pistaspadel_historial.minutospartida,pistaspadel_historial.precio,pistaspadel_nodos.nombre FROM pistaspadel_historial,pistaspadel_nodos WHERE pistaspadel_historial.idnodo=\"%s\" AND pistaspadel_historial.idnodo=pistaspadel_nodos.id AND pistaspadel_nodos.guardado=\"s\" AND pistaspadel_nodos.borrado=\"n\"%s ORDER BY pistaspadel_historial.fechaalta DESC, pistaspadel_historial.horaalta DESC, pistaspadel_historial.id DESC";//LIMIT 0,50
	$sql=sprintf($patron,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323463455899");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//puerta
			if($fila[1]=="izq"){
				$puerta="Izquierda";
			}else if($fila[1]=="der"){
				$puerta="Derecha";
			}else if($fila[1]=="amb"){
				$puerta="Ambas";
			}else{
				$puerta="Sin datos";
			}
			
			//tiempo partida
			$tiempoPartida="";
			if($fila[12]>0){
				$tiempoPartida=$fila[12]." min";
			}
			
			//accion
			$dispositivo="";
			$accion="--";
			if($fila[2]==1){
				$accion="Partida ".$tiempoPartida;
				$dispositivo="Pin";
			}else if($fila[2]==2){
				$accion="Apertura Web";
				$dispositivo="Web";
			}else if($fila[2]==3){
				$accion="Apertura Web Emergencia";
				$dispositivo="Web";
			}else if($fila[2]==4){
				$accion="Cierre Manual";
				$dispositivo="Web";
			}else if($fila[2]==5){
				$accion="Modo Mantenimiento";
				$nombreAcceso="ADMINISTRADOR";
				$dispositivo="Web";
			}
			
			
			$nombreAcceso="ADMINISTRADOR";
			//nombre usuario
			if($fila[2]==2 || $fila[2]==3 || $fila[2]==4){
				//nombre acceso
				$nombreAcceso="Sin datos";
				if($fila[4]>0){
					$patron3="SELECT nombre,apellidos FROM usuarios WHERE id=\"%s\"";
					$sql3=sprintf($patron3,$fila[4]);
					$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 96353445668764664222286454509258");
					if(mysqli_num_rows($respuesta3)>0){
						$fila3=mysqli_fetch_array($respuesta3);
						$nombreAcceso=$fila3[0]." ".$fila3[1];
					}
					mysqli_free_result($respuesta3);
				}
			}else{
				$accion="Modo Mantenimiento";
				$nombreAcceso="ADMINISTRADOR";
			}
			
			
			
			//id reserva padel, reserva tu pista
			if($fila[11]>0){
				$accion="Partida";
				$dispositivo="reservatupista.com";
				$nombreAcceso="reservatupista";
				
				//email del usuario de reserva
				$patron4="SELECT usuario FROM pistaspadel_reservas WHERE id=\"%s\"";
				$sql4=sprintf($patron4,$fila[11]);
				$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 963534456687646644449258");
				if(mysqli_num_rows($respuesta4)>0){
					$fila4=mysqli_fetch_array($respuesta4);
					
					$patron5="SELECT emailusuario FROM pistaspadel_usuariosclientes WHERE idbbddplugin=\"%s\"";
					$sql5=sprintf($patron5,$fila4[0]);
					$respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 96353445665555449258");
					if(mysqli_num_rows($respuesta5)>0){
						$fila5=mysqli_fetch_array($respuesta5);
						
						$nombreAcceso=$fila5[0];
					}
					mysqli_free_result($respuesta5);
				}
				mysqli_free_result($respuesta4);
			}
			
			
			//historial acciones realizadas, o comprobadas
			$resultadoAccion="";
			if($fila[8]=="s"){
				$resultadoAccion="Apertura leído";
			}else if($fila[8]=="n" && $fila[9]=="s"){
				$resultadoAccion="leído";
			}else{
				$resultadoAccion="Sin datos";
			}
			
			//tiempo partida
			$tiempoPartida="";
			if($fila[12]>0){
				$tiempoPartida=$fila[12]." min";
			}
			
			//modo pista
			$modo="";
			if($fila[10]=="m"){
				$modo=", Modo Mantenimiento";
			}/*else if($fila[10]=="n"){
				$modo="Modo Normal";
			}*/
			
			$precioPartida=0;
			if($fila[13]>0){
				$precioPartida=$fila[13];
			}
			
			$botones="";
			           
            printf("<tr>
						<td class='char' >%s</td>
						<td class='char' >%s</td>
                        <td class='char' >%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s%s</td>
						<td class='char'>%s</td>
						<td class='num'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
					</tr>",$fila[14],$puerta,$dispositivo,$nombreAcceso,utf8_decode($accion),$modo,$tiempoPartida,$precioPartida,convierteFechaBarra($fila[6]),$fila[5],utf8_decode($resultadoAccion));
			
		}
	}
	echo "</table>";
	echo "</tbody>";
		
	mysqli_free_result($respuesta);
}

//historial automatizacion salidas
function infHistorialAutomatizacionSalidas($con){
	$tituloText="Historial_automatizacion_".$_SESSION["fechaFinHistorialSalidasAutomatizacion"]."_".$_SESSION["fechaFinHistorialSalidasAutomatizacion"];
	
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialSalidasAutomatizacion"]) && isset($_SESSION["fechaFinHistorialSalidasAutomatizacion"]) ){
		$consulta=" AND automatizacion_historial.fechaalta>=\"".$_SESSION["fechaIniHistorialSalidasAutomatizacion"]."\" AND automatizacion_historial.fechaalta<=\"".$_SESSION["fechaFinHistorialSalidasAutomatizacion"]."\"";
	}
	
	$idNodo=0;
	if(isset($_SESSION["idNodoSalidasAutomatizacion"])){
		$idNodo=$_SESSION["idNodoSalidasAutomatizacion"];
	}
	
	cabeceraExcel($tituloText.date("Y-m-d"));
	css_Excel();
	
	$color3="#3EDD7C;";
	
	echo "<body>";
	
	echo utf8_decode("<table><tr>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Nodo</td>
			  <td class='titulo' style='background-color:".$color3."'>Salida</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Programa</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Modo</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Estado</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Hora</td>
			  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Fecha</td>
			  </tr>");
	
	$patron="SELECT automatizacion_historial.id,automatizacion_historial.salida,automatizacion_historial.horaalta,automatizacion_historial.fechaalta,automatizacion_historial.idprograma,automatizacion_historial.modo, automatizacion_historial.estado FROM automatizacion_historial,safey_nodos WHERE automatizacion_historial.idnodo=\"%s\" AND automatizacion_historial.idnodo=safey_nodos.id AND safey_nodos.guardado=\"s\" AND safey_nodos.borrado=\"n\"%s ORDER BY automatizacion_historial.fechaalta DESC, automatizacion_historial.horaalta DESC, automatizacion_historial.id DESC";
	$sql=sprintf($patron,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963234656563455899");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			//salida
			$campoConsulta="";
			$nombreSalida="";
			$nombrePrograma="";
			if($fila[1]==1){
				$campoConsulta="salidauno";
				$nombreSalida="Salida 1";
			}else if($fila[1]==2){
				$campoConsulta="salidados";
				$nombreSalida="Salida 2";
			}else if($fila[1]==3){
				$campoConsulta="salidatres";
				$nombreSalida="Salida 3";
			}else if($fila[1]==4){
				$campoConsulta="salidacuatro";
				$nombreSalida="Salida 4";
			}else if($fila[1]==5){
				$campoConsulta="salidacinco";
				$nombreSalida="Salida 5";
			}else if($fila[1]==6){
				$campoConsulta="salidaseis";
				$nombreSalida="Salida 6";
			}
			if($campoConsulta!=""){
				$patron1="SELECT %s,nombre FROM automatizacion_programa WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$campoConsulta,$fila[4]);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9633454598984543456486454509258");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					$nombreSalida.=$fila1[0];
					$nombrePrograma=$fila1[1];
				}
				mysqli_free_result($respuesta1);
			}
			$estado="";
			if($fila[6]=="on"){
				$estado="Encencido";
			}else if($fila[6]=="off"){
				$estado="Apagado";
			}
			$modo="";
			if($fila[5]=="a"){
				$modo="Automático";
			}else if($fila[5]=="m"){
				$modo="Manual";
			}
			
			//nodo
			$nombreNodo="";
			$patron2="SELECT nombre FROM safey_nodos WHERE id=\"%s\"";
			$sql2=sprintf($patron2,$idNodo);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 963222346522656342255899");
			if(mysqli_num_rows($respuesta2)>0){
				$fila2=mysqli_fetch_array($respuesta2);
				$nombreNodo=$fila2[0];
			}
			mysqli_free_result($respuesta2);
			
			printf("<tr>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
					</tr>",$nombreNodo,$nombreSalida,$nombrePrograma,$modo,utf8_decode($estado),$fila[2],convierteFechaBarra($fila[3]));	
		}
	}
	mysqli_free_result($respuesta);
		
	echo "</table>";
	echo "</body>";
}


//historial puertas safey
function infHistorialSafeyPuertas($con){
	$tituloText="Historial_safey_".$_SESSION["fechaIniHistorialPuertasSafey"]."_".$_SESSION["fechaFinHistorialPuertasSafey"];

	if(isset($_SESSION["idNodo"])){
		$idNodo=$_SESSION["idNodo"];
	}
	
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialPuertasSafey"]) && isset($_SESSION["fechaFinHistorialPuertasSafey"]) ){
		$consulta=" AND safey_historial.fechaalta>=\"".$_SESSION["fechaIniHistorialPuertasSafey"]."\" AND safey_historial.fechaalta<=\"".$_SESSION["fechaFinHistorialPuertasSafey"]."\"";
	}
	
	if(isset($_SESSION["puertaHistorialPuertasSafey"]) && $_SESSION["puertaHistorialPuertasSafey"]>0){
		$consulta=" AND safey_historial.idpuerta=".$_SESSION["puertaHistorialPuertasSafey"];
	}
	
	cabeceraExcel($tituloText.date("Y-m-d"));
	css_Excel();
	
	$color3="#3EDD7C;";
	
	echo "<body>";

    
	echo utf8_decode("<table><tr>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Ubicación</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Nodo</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Puerta</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Apertura</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Nombre Acceso</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Hora</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Fecha</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Resultado</td>
		  </tr>");
	
	$patron="SELECT safey_historial.id,safey_historial.idpuerta,safey_historial.tipo,safey_historial.idacceso,safey_historial.idusuario,safey_historial.horaalta,safey_historial.fechaalta,safey_nodos.id,safey_nodos.ubicacion,safey_nodos.nombre,safey_historial.accionrealizada,safey_historial.miradoplaca FROM safey_historial,safey_nodos WHERE safey_historial.idnodo=\"%s\" AND safey_historial.idnodo=safey_nodos.id AND safey_nodos.guardado=\"s\" AND safey_nodos.borrado=\"n\"%s ORDER BY safey_historial.fechaalta DESC, safey_historial.horaalta DESC, safey_historial.id DESC";
	$sql=sprintf($patron,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323463444555899");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//puerta
			$patron1="SELECT nombre FROM safey_puertas WHERE id=\"%s\" AND idnodo=\"%s\"";
			$sql1=sprintf($patron1,$fila[1],$fila[7]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96353456456486454509258");
			$fila1=mysqli_fetch_array($respuesta1);
			mysqli_free_result($respuesta1);
			
			//accion
			$accion="";
			if($fila[2]==1){
				$accion="Apertura Pin/LLave/Mando";
			}else if($fila[2]==2){
				$accion="Apertura Web";
			}else if($fila[2]==3){
				$accion="Apertura Web Emergencia";
			}
			
			//nombre acceso
			$idUsuario=0;
			$nombreAcceso="Sin datos";
			if($fila[3]>0){
				$patron2="SELECT nombre,idusuario FROM safey_accesos WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$fila[3]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 963534564222286454509258");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$nombreAcceso=$fila2[0];
					$idUsuario=$fila2[1];
				}
				mysqli_free_result($respuesta2);
			}
			
			//nombre usuario
			if($fila[2]==2 || $fila[2]==3){
				//nombre acceso
				$nombreAcceso="Sin datos";
				if($fila[4]>0){
					$patron3="SELECT nombre,apellidos FROM usuarios WHERE id=\"%s\"";
					$sql3=sprintf($patron3,$fila[4]);
					$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 963534456687664222286454509258");
					if(mysqli_num_rows($respuesta3)>0){
						$fila3=mysqli_fetch_array($respuesta3);
						$nombreAcceso=$fila3[0]." ".$fila3[1];
					}
					mysqli_free_result($respuesta3);
				}
			}
            
            //historial acciones realizadas, o comprobadas
            $resultadoAccion="";
			if($fila[11]=="s"){
				$resultadoAccion="Apertura (leído)";
			}else if($fila[11]=="n" && $fila[12]=="s"){
				$resultadoAccion="(leído)";
			}else{
				$resultadoAccion="Sin datos";
			}
			
			printf("<tr>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
                        <td class='char'>%s</td>
					</tr>",utf8_decode($fila[8]),utf8_decode($fila[9]),utf8_decode($fila1[0]),utf8_decode($accion),utf8_decode($nombreAcceso),utf8_decode($fila[5]),convierteFechaBarra($fila[6]),utf8_decode($resultadoAccion));
            

			
		}
	}
	mysqli_free_result($respuesta);
	
	echo "</table>";
	echo "</body>";
}

//historial fallido puertas safey
function infHistorialFallidoSafeyPuertas($con){
	$tituloText="Historial_Fallido_safey_".$_SESSION["fechaIniHistorialFallidoPuertas"]."_".$_SESSION["fechaFinHistorialFallidoPuertas"];

	if(isset($_SESSION["idNodo"])){
		$idNodo=$_SESSION["idNodo"];
	}
	
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialFallidoPuertas"]) && isset($_SESSION["fechaFinHistorialFallidoPuertas"]) ){
		$consulta=" AND safey_historial_fallidos.fechaalta>=\"".$_SESSION["fechaIniHistorialFallidoPuertas"]."\" AND safey_historial_fallidos.fechaalta<=\"".$_SESSION["fechaFinHistorialFallidoPuertas"]."\"";
	}
	
	/*if(isset($_SESSION["puertaHistorialPuertasSafey"]) && $_SESSION["puertaHistorialPuertasSafey"]>0){
		$consulta=" AND safey_historial_fallidos.idpuerta=".$_SESSION["puertaHistorialPuertasSafey"];
	}*/
	
	cabeceraExcel($tituloText.date("Y-m-d"));
	css_Excel();
	
	$color3="#3EDD7C;";
	
	echo "<body>";
	
	echo utf8_decode("<table><tr>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Ubicación</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Nodo</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Puerta</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Apertura</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Nombre Acceso</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Hora</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Fecha</td>
		  <td class='titulo' style='background-color:".$color3.";border:1px solid;'>Resultado</td>

		  </tr>");
	
    $patron="SELECT safey_historial_fallidos.id,safey_historial_fallidos.tipo,safey_historial_fallidos.horaalta,safey_historial_fallidos.fechaalta,safey_nodos.id,safey_historial_fallidos.idaccesocliente,safey_historial_fallidos.serial,safey_nodos.nombre,safey_nodos.ubicacion FROM safey_historial_fallidos,safey_nodos WHERE safey_historial_fallidos.idnodo=\"%s\" AND safey_historial_fallidos.idnodo=safey_nodos.id AND safey_nodos.guardado=\"s\" AND safey_nodos.borrado=\"n\"%s ORDER BY safey_historial_fallidos.fechaalta DESC, safey_historial_fallidos.horaalta DESC, safey_historial_fallidos.id DESC";
	$sql=sprintf($patron,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632346563444555899");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
            
            //accion//Tipo
			$accion="";
			if($fila[1]==1){
				$accion="Pin";//Apertura Pin/LLave/Mando
			}else if($fila[1]==2){
				$accion="Apertura Web";
			}else if($fila[1]==3){
				$accion="Apertura Web Emergencia";
			}else if($fila[1]==4){
				$accion="Llave";
			}
			
			//historial acciones realizadas, o comprobadas
			$resultadoAccion="Se ha registrado un intento incorrecto.";
			
			$nombreAcceso="";
			if($fila[5]>0){
				$patron2="SELECT nombre,idusuario FROM safey_accesos WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$fila[5]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 9635345642222864323434342254509258");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$nombreAcceso=$fila2[0];
					$idUsuario=$fila2[1];
				}
				mysqli_free_result($respuesta2);
			}
			
			$puerta="";
			if($fila[4]>0){
				$patron11="SELECT nombre FROM safey_puertas WHERE idnodo=\"%s\" AND salidaplaca=\"1\" AND borrado=\"n\"";
				$sql11=sprintf($patron11,$fila[4]);
				$respuesta11=mysqli_query($con,$sql11) or die ("Error al buscar 96353456422113457114225450118");
				if(mysqli_num_rows($respuesta11)>0){
					$fila11=mysqli_fetch_array($respuesta11);
					$puerta=$fila11[0];
				}
				mysqli_free_result($respuesta11);
			}
			
			printf("<tr>
						<td class='char'>%s</td>
                        <td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
						<td class='char'>%s</td>
                        <td class='char'>%s</td>
                        <td class='char'>%s</td>
                        <td class='char'>%s</td>
					</tr>",utf8_decode($fila[8]),utf8_decode($fila[7]),utf8_decode($puerta),utf8_decode($accion),utf8_decode($nombreAcceso),utf8_decode($fila[2]),convierteFechaBarra($fila[3]),utf8_decode($resultadoAccion));
			
		}
	}
	mysqli_free_result($respuesta);
	
	echo "</table>";
	echo "</body>";
}


// *********************************** FUNCIONES DE CABECERA ************************************
function cabeceraExcel($nom_file){
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename = ".$nom_file.".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
  		xmlns:x="urn:schemas-microsoft-com:office:excel"
  		xmlns="http://www.w3.org/TR/REC-html40"
  		xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
  		xmlns="http://www.w3.org/TR/REC-html40">';
}

function css_Excel(){

echo "<head>
        <style>        
            <!--
            .style0{
              padding-top:2px;
              padding-right:2px;
              padding-left:2px;
              mso-ignore:padding;
              font-size:10.0pt;
              font-weight:400;
              font-style:normal;
              text-decoration:none;
              font-family:Arial;
              mso-generic-font-family:auto;
              mso-font-charset:0;
              vertical-align:bottom;
              border:none;
              mso-background-source:auto;
              mso-pattern:auto;
              mso-protection:locked visible;
              white-space:nowrap;
              mso-rotate:0;              
            }                       
            td.titulo{
              mso-style-parent:style0;
              mso-number-format:'@';
              font-weight:700;  
              text-align:center;                      
            }
            td.calib{
              mso-style-parent:style0;
              mso-number-format:'@';
              font-weight:700; 
              text-align:center; 
              width:35pt;           
            }
            td.dec{
              mso-style-parent:style0;
              mso-number-format:'0.00';
              border:.5pt solid black;
            }
			td.por{
              mso-style-parent:style0;
              mso-number-format:'0.00%';
              border:.5pt solid black;
            }
            td.num{ 
              mso-style-parent:style0;
              mso-number-format:'0';
              border:.5pt solid black;
            }
            td.char{
              mso-style-parent:style0;
              mso-number-format:'@'; 
              border:.5pt solid black;          
            }
			
            -->                
        </style>
      </head>";
}
?>