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

//require_once("phpmailer/class.phpmailer.php");
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

require_once("phpincludes/phpdocumentos.php");
require_once("phpincludes/phpemails.php");
require_once("phpincludes/phpmultiwater.php");
require_once("phpincludes/phpcontadores.php");
require_once("phpincludes/phpluces.php");
require_once("phpincludes/phpsafey.php");
require_once("phpincludes/phppistaspadel.php");
require_once("phpincludes/phpparques.php");
require_once("phpincludes/phpcampanas.php");
require_once("phpincludes/phpautomatizacion.php");
require_once("phpincludes/phpaudios.php");
require_once("phpincludes/phpvideovigilancia.php");

if(isset($_POST["op"]) && is_numeric($_POST["op"]) && (usuarioCorrecto($con) || $_POST["op"]==2 || $_POST["op"]==7 || $_POST["op"]==9998)){
	
	/*------------------------START saber ruta por si voy necesitando------------------------------*/
	$rutaHttpCliente=$_SERVER["HTTP_REFERER"];
	///getS
	$posSAux=strpos($rutaHttpCliente, "?s=");
	$getSAux=substr($rutaHttpCliente,$posSAux);
    
	$posS=strpos($getSAux, "&i=");
    if($posS===false){//no encontrado
    	$getS=substr($getSAux,1);
    }else{//encontrado
    	$getS=substr($getSAux,1,$posS-1);//1 para quitar el ? y -1 para quitar ?
    }
    
	//getI
	$posI = strpos($rutaHttpCliente, "&i=");
    if($posI===false){//no encontrado
    	$getI=0;
    }else{//encontrado
    	$getI=substr($rutaHttpCliente,$posI+1);//quitar el &
    }
	/*-----------------------END saber ruta por si voy necesitando--------------------------------*/
	
	switch($_POST["op"]){
		case 1:
			unset($_SESSION["idusersession"]);
			unset($_SESSION["nombresession"]);
			unset($_SESSION["clavesession"]);
			unset($_SESSION["permisossession"]);
			unset($_SESSION["usersession"]);
			session_destroy();

			//destruir cokies
			$tiempoCaduca=32536000;//365*24*60*60;/* expira en un año */
			setcookie("idusersession","",time() - $tiempoCaduca);
			setcookie("nombresession","",time() - $tiempoCaduca);
			setcookie("tipouser", "",time() - $tiempoCaduca);
			setcookie("clavesession","",time() - $tiempoCaduca);
			setcookie("usersession","",time() - $tiempoCaduca);
			setcookie("permisossession","",time() - $tiempoCaduca);
		break;
		case 2:
			//$_SESSION["guardausuario"]=$_POST["guardauser"];
			if(isset($_POST["useraplicacion"]) && isset($_POST["claveaplicacion"])){
				$res=conectaUsuario($_POST["useraplicacion"],$_POST["claveaplicacion"],$con);
			}
			
			if(usuarioCorrecto($con) || $res>0){
				echo "s";
			}else{
				echo /*"n"*/$_SESSION["errorlogin"];
			}
		break;
		case 3:
			if($_SESSION["permisossession"]==1){
				$patron="UPDATE usuarios SET borrado=\"s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$_POST["id"]);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 12345634789");
			}
		break;
		case 4:
			//$correcto="n";
			if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2){
				
				$conexion=$_POST["estado"];
				$idNodo=$_POST["id"];
				
				if($idNodo>0 && ($conexion=="on" || $conexion=="off")){
					
					if($conexion=="on"){
						$conexionUpdate="off";
					}else if($conexion=="off"){
						$conexionUpdate="on";
					}
					
					$consulta="";
					if($_SESSION["permisossession"]!=1){
						$consulta=" AND idusuario=\"".$_SESSION["idusersession"]."\"";
					}
					
					$patron="UPDATE multiwater_nodos SET conexion=\"%s\" WHERE id=\"%s\"%s";
					$sql=sprintf($patron,$conexionUpdate,$idNodo,$consulta);
					$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 78945");
					
					//$correcto="s";
				}
			}
			//echo $correcto;
		break;
		case 5:
			if($_SESSION["permisossession"]==1 && $_POST["id"]>0){
				$patron="UPDATE multiwater_nodos SET borrado=\"s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$_POST["id"]);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 123456347894657");
			}
		break;
		case 6:
			if($_SESSION["permisossession"]==1 && $_POST["id"]>0){
				$patron="UPDATE contadores_nodos SET borrado=\"s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$_POST["id"]);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 1234563478946574546");
			}
		break;
		case 7:
			$recuperar="n";

			$email=quitaComillasD($_POST["useraplicacionRecu1"]);
			if($email!=""){
				$patron="SELECT id,email,aes_decrypt(contrasena, \"%s\"),nombre FROM usuarios WHERE email=\"%s\" AND guardado=\"s\" AND borrado=\"n\"";
				$sql=sprintf($patron,BBDDK,$email);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 34535675673454544355656434sdfsdf34345454recpass");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if(strlen($fila[1])>5 && strlen($fila[2])>2){

						if(restringirIntentosRecuperarPass($fila[0],"usuarios",$con)=="s"){
							$recuperar="s";

							$copia="";
							$asunto="Recuperación de contraseña de MODULARBOX";
							$contenido=$fila[3].", tu contraseña es: ".$fila[2]."<br><br>Un saludo.<br><br>https://panel.modularbox.com";
							$recuperar=mailGenerico($fila[1],$copia,$asunto,$contenido,$fila[0],"","",$con);//si viene la "s"//o error
						}else{
							$recuperar="m";
						}
					}
				}else{
					//other
				}
			}
			echo $recuperar;
		break;
		case 8:
			if($getS=="s=6" && $_POST["contador"]>0){
				$_SESSION["fechaHistorialContadorLectu"]=quitaComillasD($_POST["fecha"]);
				echo contadoresHistorialLecturas($_POST["contador"],$con);
			}else{
				echo "n";
			}
		break;
		case 9:
			if($_SESSION["permisossession"]==1 && $getS=="s=5"){
				$_SESSION["usuarioContadorList"]=$_POST["u"];
				echo cargaNodosContadoresList($con);
			}else{
				echo "n";
			}
		break;
		case 10:
			if($getS=="s=5"){
				$_SESSION["estadoContadorList"]=quitaComillasD($_POST["e"]);
				echo cargaNodosContadoresList($con);
			}else{
				echo "n";
			}
		break;
		case 11:
			$i=explode("=",$getI);
			$contador=quitaComillasD($_POST["contador"]);
			
			if($getS=="s=6" && $contador>0 && $i[1]==$contador){
				$arrayDatos=array();
				$arrayLecturas=array();
				
				if($_POST["fecha"]=="" || $_POST["fecha"]=="0000-00-00"){
					$fechaGrafica=date("Y-m-d");
				}else{
					$fechaGrafica=quitaComillasD($_POST["fecha"]);
				}
				
				$metrosCubicosLitros=1000;
				if(isset($_POST["formato"]) && $_POST["formato"]==1){//en metros cubicos/1000
					$metrosCubicosLitros=1000;
				}else if(isset($_POST["formato"]) && $_POST["formato"]==2){//en litros/1
					$metrosCubicosLitros=1;
				}
				
				if(!isset($_POST["tipo"]) || ($_POST["tipo"]!=1 && $_POST["tipo"]!=2 && $_POST["tipo"]!=3 && $_POST["tipo"]!=4)){
					$tipo=1;
				}else{
					$tipo=$_POST["tipo"];
				}
				
				switch($tipo){
					case 1://24 horas
						$arrayPeriodo=array();
						
						$fechaConsulta=$fechaGrafica;
						for($i=0;$i<=23;$i++){
							array_push($arrayPeriodo,str_pad($i,2 ,"0" ,STR_PAD_LEFT).":00h");
							
							$hora=str_pad($i,2 ,"0" ,STR_PAD_LEFT).":00:00";
							$horaSiguiente=str_pad($i+1,2 ,"0" ,STR_PAD_LEFT).":00:00";
							
							$consulta=" AND contadores_historial.fecha=\"".$fechaConsulta."\" AND hora>=\"".$hora."\" AND hora<=\"".$horaSiguiente."\"";
							
							$patron="SELECT SUM(contadores_historial.lectura) FROM contadores_nodos,contadores_historial WHERE contadores_nodos.guardado=\"s\" AND contadores_nodos.borrado=\"n\" AND contadores_nodos.id=contadores_historial.contador AND contadores_historial.borrado=\"n\" AND contadores_nodos.id=\"%s\"%s";
							$sql=sprintf($patron,$contador,$consulta);
							$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 345456547565658886756660093454545");
							if(mysqli_num_rows($respuesta)>0){
								$fila=mysqli_fetch_array($respuesta);
								array_push($arrayLecturas, intval($fila[0])/$metrosCubicosLitros);
							}
							mysqli_free_result($respuesta);
						}
						
						array_push($arrayDatos, $arrayLecturas);
						array_push($arrayDatos, $arrayPeriodo);
						print_r(json_encode($arrayDatos));
					break;
					case 2://semana
						$arrayPeriodo=["L","M","X","J","V","S","D"];
						
						$fechaConsulta=$fechaGrafica;
						$diaFechaPasadoFiltro=intval(substr($fechaConsulta,-2));
						
						//inicio y fin de semana
						$iniFinSemana=inicio_fin_semana($fechaConsulta);
						$inicioSemanaAux=$iniFinSemana["fechaInicio"];

						//saber dia
						$diaInicio=intval(substr($inicioSemanaAux,-2));
						
						//saber fecha sin dia, montar for
						$inicioSinDiaSemanaAux=substr($inicioSemanaAux,0,7);//todo menos el dia
						$inicioAnoFecha=substr(date("Y-m-d"),0,4);//solo el ano
						$inicioMesFecha=substr(date("Y-m-d"),5,2);//solo el mes
						
						$anoDos=substr($fechaConsulta,0,4);//date("Y");
						
						$mesDos=str_pad(substr($fechaConsulta,5,2),2 ,"0" ,STR_PAD_LEFT);//str_pad(date("m"),2 ,"0" ,STR_PAD_LEFT);
						
						if($diaFechaPasadoFiltro<=6){//porque es del mes anterior
							$mesDos=intval($mesDos)-1;
						}
						$ultimoDiaMesDos=str_pad(getUltimoDiaMes($anoDos,$mesDos),2 ,"0" ,STR_PAD_LEFT);
						
						for($j=0;$j<=6;$j++){
							
							$diaSemanaSumadoFor=$j+$diaInicio;
							if($diaSemanaSumadoFor<=$ultimoDiaMesDos){
								$diaSemanaSumadoFor=$diaSemanaSumadoFor;//indiferente
								$inicioSinDiaSemanaAux=$inicioSinDiaSemanaAux;//indiferente, aqui usamos el mesDos
							}else{
								$diaSemanaSumadoFor=$diaSemanaSumadoFor-$ultimoDiaMesDos;
								$inicioSinDiaSemanaAux=$inicioAnoFecha."-".str_pad(intval($inicioMesFecha),2 ,"0" ,STR_PAD_LEFT);//aqui usamos el mes actual fecha
							}
							
							$fechaConsultaProcesada=$inicioSinDiaSemanaAux."-".str_pad($diaSemanaSumadoFor,2 ,"0" ,STR_PAD_LEFT);
							
							$consulta=" AND contadores_historial.fecha=\"".$fechaConsultaProcesada."\"";
						
							$patron="SELECT SUM(contadores_historial.lectura) FROM contadores_nodos,contadores_historial WHERE contadores_nodos.guardado=\"s\" AND contadores_nodos.borrado=\"n\" AND contadores_nodos.id=contadores_historial.contador AND contadores_historial.borrado=\"n\" AND contadores_nodos.id=\"%s\"%s";
							$sql=sprintf($patron,$contador,$consulta);
							$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 34556760093454545");
							if(mysqli_num_rows($respuesta)>0){
								$fila=mysqli_fetch_array($respuesta);
								array_push($arrayLecturas, intval($fila[0])/$metrosCubicosLitros);
							}
							mysqli_free_result($respuesta);
						}
						
						array_push($arrayDatos, $arrayLecturas);
						array_push($arrayDatos, $arrayPeriodo);
						print_r(json_encode($arrayDatos));
					break;
					case 3://mes
						$arrayPeriodo=array();
						
						$fechaConsulta=$fechaGrafica;
						
						$ano=substr($fechaConsulta,0,4);//date("Y");
						$mes=str_pad(substr($fechaConsulta,5,2),2 ,"0" ,STR_PAD_LEFT);//str_pad(date("m"),2 ,"0" ,STR_PAD_LEFT);
						$ultimoDiaMes=str_pad(getUltimoDiaMes($ano,$mes),2 ,"0" ,STR_PAD_LEFT);
						
						for($i=0;$i<$ultimoDiaMes;$i++){
							array_push($arrayPeriodo,($i+1));
		
							$diaConsulta=str_pad($i+1,2 ,"0" ,STR_PAD_LEFT);
							$fechaUno=$ano."-".$mes."-".$diaConsulta;
							$consulta=" AND contadores_historial.fecha=\"".$fechaUno."\" ";

							$patron="SELECT SUM(contadores_historial.lectura) FROM contadores_nodos,contadores_historial WHERE contadores_nodos.guardado=\"s\" AND contadores_nodos.borrado=\"n\" AND contadores_nodos.id=contadores_historial.contador AND contadores_historial.borrado=\"n\" AND contadores_nodos.id=\"%s\"%s";
							$sql=sprintf($patron,$contador,$consulta);
							$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 345565658886756660093454545");
							if(mysqli_num_rows($respuesta)>0){
								$fila=mysqli_fetch_array($respuesta);
								array_push($arrayLecturas, intval($fila[0])/$metrosCubicosLitros);
							}
							mysqli_free_result($respuesta);
						}
						array_push($arrayDatos, $arrayLecturas);
						array_push($arrayDatos, $arrayPeriodo);
						print_r(json_encode($arrayDatos));
					break;
					case 4://ano
						$arrayPeriodo=["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
						
						$fechaConsulta=$fechaGrafica;
						
						$ano=substr($fechaConsulta,0,4);//date("Y");
						for($i=0;$i<count($arrayPeriodo);$i++){
							$mes=str_pad($i+1 ,2 ,"0" ,STR_PAD_LEFT);
							$fechaUno=$ano."-".$mes."-01";
							$fechaDos=$ano."-".$mes."-".getUltimoDiaMes($ano,$mes);

							$consulta=" AND contadores_historial.fecha>=\"".$fechaUno."\" AND contadores_historial.fecha<=\"".$fechaDos."\"";

							$patron="SELECT SUM(contadores_historial.lectura) FROM contadores_nodos,contadores_historial WHERE contadores_nodos.guardado=\"s\" AND contadores_nodos.borrado=\"n\" AND contadores_nodos.id=contadores_historial.contador AND contadores_historial.borrado=\"n\" AND contadores_nodos.id=\"%s\"%s";
							$sql=sprintf($patron,$contador,$consulta);
							$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 34556756660093454545");
							if(mysqli_num_rows($respuesta)>0){
								$fila=mysqli_fetch_array($respuesta);
								array_push($arrayLecturas, intval($fila[0])/$metrosCubicosLitros);
							}
							mysqli_free_result($respuesta);
						}
						array_push($arrayDatos, $arrayLecturas);
						array_push($arrayDatos, $arrayPeriodo);
						print_r(json_encode($arrayDatos));
					break;
				}
			}
		break;
		case 12:
			$devolver="n";
			$i=explode("=",$getI);
			
			$contador=quitaComillasD($_POST["contador"]);
			$metros=quitaComillasD($_POST["metros"]);
			$fecha=quitaComillasD($_POST["fecha"]);
			if($_POST["hora"]==""){
				$hora="00:00:00";
			}else{
				$hora=quitaComillasD($_POST["hora"]);
			}
			
			if($getS=="s=6" && $contador>0 && $i[1]==$contador && $fecha!="0000-00-00" && $metros>0){
				$pulso=1;
				
				$lecturasTotales=0;
				$patron="SELECT SUM(lectura) FROM contadores_historial WHERE borrado=\"n\" AND contador=\"%s\"";
				$sql=sprintf($patron,$contador);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 345565675675886660093454545");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					$lecturasTotales=$fila[0];
				}
				mysqli_free_result($respuesta);
				
				$ajusteRestar=0;
				$patron2="SELECT ajuste FROM contadores_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
				$sql2=sprintf($patron2,$contador);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 34556567563457258866600923454545");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$ajusteRestarLitros=floatval($fila2[0])*1000;
				}
				mysqli_free_result($respuesta2);
				
				$litros=floatval($metros)*1000;
				
				if($lecturasTotales>$litros){
					$litrosAjuste=$lecturasTotales-$litros;
				}else{
					$litrosAjuste=$litros-$lecturasTotales;
				}
				
				$litrosAjuste=$litrosAjuste-$ajusteRestarLitros;//restar el inicializado
				
				//crear
				$patron1="INSERT INTO contadores_historial SET borrado=\"n\",contador=\"%s\",lectura=\"%s\",hora=\"%s\",fecha=\"%s\",pulso=\"%s\",creado=\"2\"";
				$sql1=sprintf($patron1,$contador,$litrosAjuste,$hora,$fecha,$pulso);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error 1233577636646776467");
				
				$devolver=contadoresHistorialLecturas($contador,$con);
			}
			echo $devolver;
		break;
		case 13:
			$devolver="n";
			$i=explode("=",$getI);
			
			$contador=quitaComillasD($_POST["contador"]);
			$id=quitaComillasD($_POST["idlin"]);
			
			if($getS=="s=6" && $contador>0 && $i[1]==$contador && $id>0){
				
				$patron="UPDATE contadores_historial SET borrado=\"s\" WHERE id=\"%s\" AND creado=\"2\" AND contador=\"%s\"";
				$sql=sprintf($patron,$id,$contador);
				$respuesta=mysqli_query($con,$sql) or die ("Error 123355478677763664670076467");
				
				$devolver=contadoresHistorialLecturas($contador,$con);
			}
			echo $devolver;
		break;
		case 14:
			$devolver="n";
			$i=explode("=",$getI);
			
			$cliente=quitaComillasD($_POST["cliente"]);
			$nombre=quitaComillasD($_POST["nombre"]);
			$telefono=quitaComillasD($_POST["telefono"]);
			$email=quitaComillasD($_POST["email"]);
			$contrasena=quitaComillasD($_POST["contrasena"]);
		
			$emailDuplicado=comprobarEmailUsado($email,"usuarios",$id,$con);//email duplicado
			
			if($getS=="s=2" && $cliente>0 && $i[1]==$cliente && $nombre!="" && $emailDuplicado!="s"){
				//crear
				$patron1="INSERT INTO usuarios SET borrado=\"n\",guardado=\"s\",idempresa=\"%s\",nombre=\"%s\",telefono=\"%s\",email=\"%s\",contrasena=aes_encrypt(\"%s\",\"%s\"),permisos=\"3\",apellidos=\"\",nif=\"\",movil=\"\",localidad=\"\",cp=\"\",provincia=\"0\",direccion=\"\",fotoperfil=\"\",observaciones=\"\",accesos=\"0\",ultimaconexion=\"2020-01-01\",numrecuperacionespass=\"0\",tipoentidad=\"0\",frecuperacionespasss=\"2020-01-01\",fechaalta=\"%s\" ";
				$sql1=sprintf($patron1,$cliente,$nombre,$telefono,$email,$contrasena,BBDDK,date("Y-m-d"));
				$respuesta1=mysqli_query($con,$sql1) or die ("Error 1233576767535345636646776467");
				$id=mysqli_insert_id($con);
				
				$nombreEmpresa="Sin datos";
				$patron="SELECT nombre FROM usuarios WHERE id=\"%s\" AND guardado=\"s\" AND borrado=\"n\"";
				$sql=sprintf($patron,$cliente);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 345567566600956563454545");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					$nombreEmpresa=$fila[0];
				}
				mysqli_free_result($respuesta);
				
				//enviar mail de accesos
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					$copia="";
					$asunto=$nombre." -- INVITACIÓN (MODULARBOX)";
					$contenido="Hola, <b>".$nombre."</b><br><br>Ha sido invitado a la administración del panel de modularbox por: <b>".$nombreEmpresa."</b>.<br><br>Usuario: <b>".$email."</b><br>Contraseña: <b>".$contrasena."</b><br><br><a href='https://panel.modularbox.com/'>Acceder al panel</a><br><br>Un saludo. <br><br><br> <b>No responda a este mensaje</b> ha sido autogenerado por la plataforma <b>(MODULARBOX)</b>.";
					mailGenerico($email,$copia,$asunto,$contenido,$id,"","",$con);
				}
				
				$devolver=accessoUsuariosClientes($cliente,$con);
			}
			echo $devolver;
		break;
		case 15:
			$devolver="n";
			$i=explode("=",$getI);
			
			$cliente=quitaComillasD($_POST["cliente"]);
			$lin=quitaComillasD($_POST["lin"]);
		
			if($getS=="s=2" && $cliente>0 && $i[1]==$cliente && $lin>0){
				//borrar
				$patron1="UPDATE usuarios SET borrado=\"s\" WHERE id=\"%s\" AND idempresa=\"%s\" AND permisos=\"3\"";
				$sql1=sprintf($patron1,$lin,$cliente);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error 1233576767535676345636646776467");
				
				$devolver=accessoUsuariosClientes($cliente,$con);
			}
			echo $devolver;
		break;
		case 16:
			$devolver="n";
			$i=explode("=",$getI);
			
			$cliente=quitaComillasD($_POST["cliente"]);
			$nombre=quitaComillasD($_POST["nombre"]);
			$telefono=quitaComillasD($_POST["telefono"]);
			$email=quitaComillasD($_POST["email"]);
			$contrasena=quitaComillasD($_POST["contrasena"]);
			$lin=quitaComillasD($_POST["lin"]);
			
			if($getS=="s=2" && $cliente>0 && $i[1]==$cliente && $nombre!="" && $lin>0){
				//update
				$patron1="UPDATE usuarios SET nombre=\"%s\",telefono=\"%s\",email=\"%s\",contrasena=aes_encrypt(\"%s\",\"%s\") WHERE id=\"%s\" AND idempresa=\"%s\"";
				$sql1=sprintf($patron1,$nombre,$telefono,$email,$contrasena,BBDDK,$lin,$cliente);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error 123357676753534563664677646700");
				
				//enviar actualizacion mail??
				
				$devolver=accessoUsuariosClientes($cliente,$con);
			}
			echo $devolver;
		break;
		case 17:
			if($_SESSION["permisossession"]==1 && $getS=="s=8"){
				$_SESSION["usuarioLucesList"]=$_POST["u"];
				echo cargaNodosLucesList($con);
			}else{
				echo "n";
			}
		break;
		case 18:
			if($getS=="s=8"){
				$_SESSION["estadoLucesList"]=quitaComillasD($_POST["e"]);
				echo cargaNodosLucesList($con);
			}else{
				echo "n";
			}
		break;
		case 19:
			if($_SESSION["permisossession"]==1 && $_POST["id"]>0){
				$patron="UPDATE luces_nodos SET borrado=\"s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$_POST["id"]);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 123456347894657454685213");
			}
		break;
		case 20:
			if($_SESSION["permisossession"]==1 && ($getS=="s=11")){
				$_SESSION["usuarioProgramasLucesList"]=$_POST["u"];
				echo cargaProgramasLucesList($con);
			}else{
				echo "n";
			}
		break;
		case 21:
			$correcto="n";
			if($_SESSION["permisossession"]==1 && $_POST["id"]>0){
				
				$idPrograma=$_POST["id"];
				
				//comprobar que el programa no esta en ningun horario en uso
				$patron3="SELECT luces_horarios_programas_conf.programa FROM luces_horarios,luces_horarios_programas_conf WHERE luces_horarios.borrado=\"n\" AND luces_horarios.guardado=\"s\" AND luces_horarios.id=luces_horarios_programas_conf.horario AND luces_horarios_programas_conf.programa=\"%s\"";
				$sql3=sprintf($patron3,$idPrograma);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 963332345466462565776345345899");
				if(mysqli_num_rows($respuesta3)>0){
					$fila3=mysqli_fetch_array($respuesta3);
				}else{
					$patron="UPDATE luces_programas SET borrado=\"s\" WHERE id=\"%s\"";
					$sql=sprintf($patron,$idPrograma);
					$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 12345634548900785694657454685213");
					$correcto="s";
				}
			}
			echo $correcto;
		break;
        case 22:
            $devolver="n";
			$i=explode("=",$getI);
			
			$programa=quitaComillasD($_POST["programa"]);
			$lin=quitaComillasD($_POST["lin"]);
            
			if($getS=="s=12" && $programa>0 && $i[1]==$programa && $lin>0){
				
				//borrar
				$patron1="UPDATE luces_filasprograma SET borrado=\"s\" WHERE id=\"%s\" AND programa=\"%s\"";
				$sql1=sprintf($patron1,$lin,$programa);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error 1233576767535676345636646776467");
				
				//generarSequencePrograma($programa,$con);//generar el sequence
				
                $devolver=configuracionProgramaLuces($programa,$con);
			}
			echo $devolver;
        break;
		case 23:
			$devolver="n";
			$i=explode("=",$getI);

			$programa=quitaComillasD($_POST["programa"]);

			if($getS=="s=12" && $programa>0 && $i[1]==$programa){
				//crear, en blanco
				$patron="INSERT INTO luces_filasprograma SET programa=\"%s\",colorcolumuno=8,colorcolumdos=8,colorcolumtres=8,colorcolumcuatro=8,colorcolumcinco=8,colorcolumseis=8,colorcolumsiete=8,colorcolumocho=8,colorcolumnueve=8,colorcolumdiez=8,colorcolumonce=8,colorcolumdoce=8,colorcolumtrece=8,colorcolumcatorce=8,colorcolumquince=8,colorcolumdieciseis=8,colorcolumdiecisiete=8,colorcolumdieciocho=8,colorcolumdiecinueve=8,colorcolumveinte=8,temporizacion=8,borrado=\"n\"";
				$sql=sprintf($patron,$programa);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1334663765375763");
				
				//generarSequencePrograma($programa,$con);//generar el sequence

				$devolver=configuracionProgramaLuces($programa,$con);
			}
			echo $devolver;
		break;
		case 24:
			$devolver="n";
			$i=explode("=",$getI);

			$id=quitaComillasD($_POST["lin"]);
			$programa=quitaComillasD($_POST["programa"]);
			
			$c1=intval(quitaComillasD($_POST["c1"]));
			$c2=intval(quitaComillasD($_POST["c2"]));
			$c3=intval(quitaComillasD($_POST["c3"]));
			$c4=intval(quitaComillasD($_POST["c4"]));
			$c5=intval(quitaComillasD($_POST["c5"]));
			$c6=intval(quitaComillasD($_POST["c6"]));
			$c7=intval(quitaComillasD($_POST["c7"]));
			$c8=intval(quitaComillasD($_POST["c8"]));
			$c9=intval(quitaComillasD($_POST["c9"]));
			$c10=intval(quitaComillasD($_POST["c10"]));
			$c11=intval(quitaComillasD($_POST["c11"]));
			$c12=intval(quitaComillasD($_POST["c12"]));
			$c13=intval(quitaComillasD($_POST["c13"]));
			$c14=intval(quitaComillasD($_POST["c14"]));
			$c15=intval(quitaComillasD($_POST["c15"]));
			$c16=intval(quitaComillasD($_POST["c16"]));
			$c17=intval(quitaComillasD($_POST["c17"]));
			$c18=intval(quitaComillasD($_POST["c18"]));
			$c19=intval(quitaComillasD($_POST["c19"]));
			$c20=intval(quitaComillasD($_POST["c20"]));
			$temp=intval(quitaComillasD($_POST["temp"]));
			
			if($getS=="s=12" && $programa>0 && $i[1]==$programa && $id>0 && is_numeric($temp)){
				
				$menor=0;//el cero, para dejar vacio
				$mayor=11;
				
				if(($c1>=$menor && $c1<=$mayor) && ($c2>=$menor && $c2<=$mayor) && ($c3>=$menor && $c3<=$mayor) && ($c4>=$menor && $c4<=$mayor) && ($c5>=$menor && $c5<=$mayor) && ($c6>=$menor && $c6<=$mayor) && ($c7>=$menor && $c7<=$mayor) && ($c8>=$menor && $c8<=$mayor) && ($c9>=$menor && $c9<=$mayor) && ($c10>=$menor && $c10<=$mayor) && ($c11>=$menor && $c11<=$mayor) && ($c11>=$menor && $c11<=$mayor) && ($c12>=$menor && $c12<=$mayor) && ($c13>=$menor && $c13<=$mayor) && ($c14>=$menor && $c14<=$mayor) && ($c15>=$menor && $c15<=$mayor) && ($c16>=$menor && $c16<=$mayor) && ($c17>=$menor && $c17<=$mayor) && ($c18>=$menor && $c18<=$mayor) && ($c19>=$menor && $c19<=$mayor)&& ($c20>=$menor && $c20<=$mayor)){//validar datos
					//crear
					$patron="UPDATE luces_filasprograma SET colorcolumuno=\"%s\",colorcolumdos=\"%s\",colorcolumtres=\"%s\",colorcolumcuatro=\"%s\",colorcolumcinco=\"%s\",colorcolumseis=\"%s\",colorcolumsiete=\"%s\",colorcolumocho=\"%s\",colorcolumnueve=\"%s\",colorcolumdiez=\"%s\",colorcolumonce=\"%s\",colorcolumdoce=\"%s\",colorcolumtrece=\"%s\",colorcolumcatorce=\"%s\",colorcolumquince=\"%s\",colorcolumdieciseis=\"%s\",colorcolumdiecisiete=\"%s\",colorcolumdieciocho=\"%s\",colorcolumdiecinueve=\"%s\",colorcolumveinte=\"%s\",temporizacion=%s WHERE id=\"%s\" AND programa=\"%s\"";
					$sql=sprintf($patron,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14,$c15,$c16,$c17,$c18,$c19,$c20,$temp,$id,$programa);
					$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 133468008587663765375763");
				}
				
				//generarSequencePrograma($programa,$con);//generar el sequence
				
				$devolver=configuracionProgramaLuces($programa,$con);
			}
			echo $devolver;
		break;
		case 25:
			$i=explode("=",$getI);

			$n=intval(quitaComillasD($_POST["n"]));
			$h=intval(quitaComillasD($_POST["h"]));

			if($getS=="s=14" && $h>0 && $n>0 && $i[1]==$h){
				
				$patron1="SELECT id FROM luces_horarios_nodos WHERE nodo=\"%s\" AND horario=\"%s\"";
				$sql1=sprintf($patron1,$n,$h);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 34535672342344df34345454");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
				}else{
					if(comprobarSolapamientoHoraioLucesNodos($h,$n,"","",0,2,$con)){
						//crear
						$patron="INSERT INTO luces_horarios_nodos SET nodo=\"%s\",horario=\"%s\"";
						$sql=sprintf($patron,$n,$h);
						$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 133466376537534565763");
						
						inicioFinActividadNodoLuz($h,$con);//calcular el schedule
						
						echo cargaNodosHorariosLuces($h,$con);
						echo "@#";
						echo cargaNodosHorarioLucesSelect(2,$h,"nodoHorario",$con);
					}else{
						echo "n";
					}
				}
			}else{
				echo "n";
			}
		break;
		case 26:
			$i=explode("=",$getI);

			$id=intval(quitaComillasD($_POST["lin"]));
			$h=intval(quitaComillasD($_POST["h"]));

			if($getS=="s=14" && $h>0 && $id>0 && $i[1]==$h){
				//borar
				$patron="DELETE FROM luces_horarios_nodos WHERE id=\"%s\" AND horario=\"%s\"";
				$sql=sprintf($patron,$id,$h);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1334663756766537534565763");
				
				inicioFinActividadNodoLuz($h,$con);//calcular el schedule
				
				echo cargaNodosHorariosLuces($h,$con);
				echo "@#";
				echo cargaNodosHorarioLucesSelect(2,$h,"nodoHorario",$con);
			}else{
				echo "n";
			}
		break;
		case 27:
			$i=explode("=",$getI);
			
			$id=intval(quitaComillasD($_POST["id"]));
			
			if($id>0 && $getS=="s=14" && $i[1]==$id){
				$patron="UPDATE luces_horarios SET borrado=\"s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$id);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 12345465685347896634546574546");
				
				//borrar todo lo relacionado de nodos horarios 
				$patron="DELETE FROM luces_horarios_nodos WHERE horario=\"%s\"";
				$sql=sprintf($patron,$id);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1334663756565886766537534565763");
				
				//borrar todo lo relacionado de horas programas
				$patron2="DELETE FROM luces_horarios_programas_conf WHERE horario=\"%s\"";
				$sql2=sprintf($patron2,$id);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 133466334655756766537534565763");
				
				inicioFinActividadNodoLuz($id,$con);//calcular el schedule
			}
		break;
		case 28:
			$devolver="n";
			
			$i=explode("=",$getI);

			$h=intval(quitaComillasD($_POST["h"]));
			
			$programa=intval(quitaComillasD($_POST["programa"]));
			$diaSemana=intval(quitaComillasD($_POST["diaSemana"]));
			$horaDe=substr(quitaComillasD($_POST["horaDe"]),0,5).":01";//fuerzo el :01, para que la luego comprobar el solapamiento
			$horaHasta=substr(quitaComillasD($_POST["horaHasta"]),0,5).":00";
		
			if($getS=="s=14" && $h>0 && $i[1]==$h && comprobarSolapamientoHoraioLucesNodos($h,0,$horaDe,$horaHasta,$diaSemana,1,$con)){
				
				//crear
				$patron="INSERT INTO  luces_horarios_programas_conf SET horario=\"%s\",programa=\"%s\",diasemana=\"%s\",horade=\"%s\",horahasta=\"%s\"";
				$sql=sprintf($patron,$h,$programa,$diaSemana,$horaDe,$horaHasta);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 133466345565376537534565763");
				
				inicioFinActividadNodoLuz($h,$con);//calcular el schedule
				
				$devolver=horariosLucesConf($h,$con);
			}
			echo $devolver;
		break;
		case 29:
			$devolver="n";
			
			$i=explode("=",$getI);

			$id=intval(quitaComillasD($_POST["lin"]));
			$h=intval(quitaComillasD($_POST["h"]));

			if($getS=="s=14" && $h>0 && $id>0 && $i[1]==$h){
				//borar
				$patron="DELETE FROM luces_horarios_programas_conf WHERE id=\"%s\" AND horario=\"%s\"";
				$sql=sprintf($patron,$id,$h);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1334663756766537534565763");
				
				inicioFinActividadNodoLuz($h,$con);//calcular el schedule
				
				$devolver=horariosLucesConf($h,$con);
			}
			echo $devolver;
		break;
		case 30:
			$devolver="n";
			$i=explode("=",$getI);
			
			if($_POST["accion"]==1){
				$accion="<";
				$orden="ORDER BY id DESC";
			}else{
				$accion=">";
				$orden="ORDER BY id";
			}

			$programa=intval(quitaComillasD($_POST["programa"]));
			$id=intval(quitaComillasD($_POST["id"]));
			
			if($getS=="s=12" && $programa>0 && $id>0 && $i[1]==$programa){
				$patron="SELECT id FROM luces_filasprograma WHERE id%s%s AND programa=%s %s LIMIT 0,1";
				$sql=sprintf($patron,$accion,$id,$programa,$orden);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 3453567545456asdasd734324sdfsdf34345454");
				$fila=mysqli_fetch_array($respuesta);

				$patron3="UPDATE luces_filasprograma SET id=\"0\" WHERE id=\"%s\"";
				$sql3=sprintf($patron3,$fila[0]);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error 345353454567534534256a345asd7343432sdfs234df34345454");
				
				$patron3="UPDATE luces_filasprograma SET id=\"%s\" WHERE id=\"%s\"";
				$sql3=sprintf($patron3,$fila[0],$id);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error 345353454567555463453456as754d73434sdfs78d7f34345454");
				
				$patron3="UPDATE luces_filasprograma SET id=\"%s\" WHERE id=\"0\"";
				$sql3=sprintf($patron3,$id);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error 345352345456345753453456as66as900d73434sdfsdf34345454");

				//generarSequencePrograma($programa,$con);//generar el sequence
				
				$devolver=configuracionProgramaLuces($programa,$con);
			}
			echo $devolver;
		break;
		case 31:
			if($_SESSION["permisossession"]==1 && ($getS=="s=13")){
				$_SESSION["usuarioProgramasLucesList"]=$_POST["u"];
				echo cargaHorariosLucesList($con);
			}else{
				echo "n";
			}
		break;
		case 32:
			//??
		break;
		case 33:
			if($_SESSION["permisossession"]==1 && $getS=="s=15"){
				$_SESSION["usuarioSafeyList"]=$_POST["u"];
				echo cargaNodosSafeyList($con);
			}else{
				echo "n";
			}
		break;
		case 34:
			if($getS=="s=15"){
				$_SESSION["conexionSafeyList"]=quitaComillasD($_POST["e"]);
				echo cargaNodosSafeyList($con);
			}else{
				echo "n";
			}
		break;
		case 35:
			$devolver="n";
			
			$i=explode("=",$getI);

			$n=intval(quitaComillasD($_POST["n"]));
			
			$nombre=quitaComillasD($_POST["nombrePuerta"]);
			$urlEmergenciaPuerta=quitaComillasD($_POST["urlEmergenciaPuerta"]);
		
			if($getS=="s=16" && $n>0 && $i[1]==$n && $nombre!="" && $_SESSION["permisossession"]==1){
				
                $maxPuertas=2;
                
                $patron1="SELECT id FROM safey_puertas WHERE idnodo=\"%s\" AND borrado=\"n\"";
                $sql1=sprintf($patron1,$n);
                $respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 238921189923436653343437723424234534444232349943ff");
                $puertasNodo=mysqli_num_rows($respuesta1);
                /*if(mysqli_num_rows($respuesta1)>0){
                    $fila1=mysqli_fetch_array($respuesta1);
                }*/
                mysqli_free_result($respuesta1);
                
				//crear
				$salidaPlaca=0;
                if($puertasNodo<$maxPuertas){
					
					if($puertasNodo==0){
						$salidaPlaca=1;
					}else if($puertasNodo==1){//en este caso habria que comprobar si se esta usando ya la dos
						$salidaPlaca=2;
					}
					
                    $patron="INSERT INTO  safey_puertas SET nombre=\"%s\",idnodo=\"%s\",rutaimg=\"\",urlemergencia=\"%s\",salidaplaca=\"%s\",borrado=\"n\",fechaalta=\"%s\"";
				    $sql=sprintf($patron,$nombre,$n,$urlEmergenciaPuerta,$salidaPlaca,date("Y-m-d"));
				    $respuesta=mysqli_query($con,$sql) or die ("Error al buscar 13344657546366345565376537534565763");
                }
				
				$devolver=cargaPuertasNodoSafey($n,$con);
			}
			echo $devolver;
		break;
		case 36:
			$devolver="n";
			$i=explode("=",$getI);

			$id=intval(quitaComillasD($_POST["lin"]));
			$n=intval(quitaComillasD($_POST["n"]));

			if($getS=="s=16" && $n>0 && $id>0 && $i[1]==$n && $_SESSION["permisossession"]==1){
				//borar
				$patron="DELETE FROM safey_puertas WHERE id=\"%s\" AND idnodo=\"%s\"";
				$sql=sprintf($patron,$id,$n);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 13346633567756766537534565763");
				
				$devolver=cargaPuertasNodoSafey($n,$con);
			}
			echo $devolver;
		break;
		case 37: 
			$devolver="n";
			$i=explode("=",$getI);

			$tipo=intval(quitaComillasD($_POST["tipo"]));
			$n=intval(quitaComillasD($_POST["n"]));
			$p=intval(quitaComillasD($_POST["p"]));

			if($getS=="s=16" && $n>0 /*&& $i[1]==$n*/ && $tipo>0 && $tipo<=4 && $p>0){
				
				/*$idUsuario=$_SESSION["idusersession"];
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2){
					$idUsuario.=" AND idusuario=\"".$_SESSION["idusersession"]."\"";
				}else if(){
					$idUsuario.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
				}*/
				
				//FALTARIA COMPROBAR QUE PUEDES HACER LA ACCION, se deja para mas tarde 
				
				$correcto=false;
				if($tipo==2){
					$correcto=true;
					//FALTA GESTIONAR ALGUN CAMPO O CAMPOS QUE CONSULTARA LA API SAFEY PARA RASPBERRY
				}else if($tipo==3){
					$correcto=true;//web url emergencia
				}
				
				if($correcto){
					$idUsuario=$_SESSION["idusersession"];
					if($idUsuario>0){
						$patron="INSERT INTO safey_historial SET idnodo=\"%s\",idpuerta=\"%s\",tipo=\"%s\",idacceso=0,horaalta=\"%s\",fechaalta=\"%s\",idusuario=\"%s\",accionrealizada=\"n\",miradoplaca=\"n\"";
						$sql=sprintf($patron,$n,$p,$tipo,date("H:i:s"),date("Y-m-d"),$idUsuario);
						$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 13346633567456756766537534565763");
						
						$devolver="s";
					}
					
				}
			}
			echo $devolver;
		break;
		case 38:
			$n=quitaComillasD($_POST["n"]);
			if($getS=="s=16" && $n>0){
				$_SESSION["fechaIniHistorialPuertasSafey"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialPuertasSafey"]=quitaComillasD($_POST["fechaFin"]);
				$_SESSION["puertaHistorialPuertasSafey"]=quitaComillasD($_POST["puerta"]);
				
				echo puertasSafeyHistorial($n,$con);
			}else{
				echo "n";
			}
		break;
		case 39:
			if($_SESSION["permisossession"]==1 && $getS=="s=17"){
				$_SESSION["usuarioSafeyList"]=$_POST["u"];
				echo cargaAccesosSafeyList($con);
			}else{
				echo "n";
			}
		break;
		case 40:
			$i=explode("=",$getI);
			
			$id=intval(quitaComillasD($_POST["id"]));
			
			if($id>0 && $getS=="s=16" && $i[1]==$id && $_SESSION["permisossession"]==1){
				$patron="UPDATE safey_nodos SET borrado=\"s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$id);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 1234504656853478966345565746645574546");
			}
		break;
		case 41:
			$i=explode("=",$getI);
			
			$id=intval(quitaComillasD($_POST["id"]));
			
			if($id>0 && $getS=="s=18" && $i[1]==$id){
				
				
				/*START liberar pin, borrar*/
				$patron1="SELECT id FROM safey_credenciales_pin WHERE borrado=\"n\" AND idacceso=\"%s\"";
				$sql1=sprintf($patron1,$id);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963235115345345467787879958");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					
					$patron2="UPDATE safey_credenciales_pin SET borrado=\"s\" WHERE id=\"%s\"";//idacceso=\"0\",
					$sql2=sprintf($patron2,$fila1[0]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al borrar 12345045428534789663455657464546");
				}
				mysqli_free_result($respuesta1);
				/*END liberar pin, borrar*/
				
				
				$patron="UPDATE safey_accesos SET borrado=\"s\",pin=\"0\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$id);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 123450464654756853478966345565746645574546");
			}
		break;
		case 42:
			$devolver="n";
			$i=explode("=",$getI);
			
			$idacceso=intval(quitaComillasD($_POST["idacceso"]));
			$idlin=intval(quitaComillasD($_POST["idlin"]));
			
			$puerta=intval(quitaComillasD($_POST["puerta"]));
			$nodo=intval(quitaComillasD($_POST["nodo"]));
			$hidenPermiso=quitaComillasD($_POST["hidenPermiso"]);
			$fechaDe=quitaComillasD($_POST["fechaDe"]);
			$fechaHasta=quitaComillasD($_POST["fechaHasta"]);
			
			$l=quitaComillasD($_POST["l"]);
			$m=quitaComillasD($_POST["m"]);
			$x=quitaComillasD($_POST["x"]);
			$j=quitaComillasD($_POST["j"]);
			$v=quitaComillasD($_POST["v"]);
			$s=quitaComillasD($_POST["s"]);
			$d=quitaComillasD($_POST["d"]);
			
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2) && $getS=="s=18" && $idacceso>0 && $puerta>0 && $nodo>0  && ($hidenPermiso=="s" || $hidenPermiso=="n") && ($l=="s" || $l=="n") && ($m=="s" || $m=="n") && ($x=="s" || $x=="n") && ($j=="s" || $j=="n") && ($v=="s" || $v=="n") && ($s=="s" || $s=="n") && ($d=="s" || $d=="n")){
				
				$patron="SELECT id,nodo,puerta,permisos,horade,horahasta FROM safey_accesosnodos WHERE borrado=\"n\" AND nodo=\"%s\" AND puerta=\"%s\" AND idacceso=\"%s\" AND id=\"%s\"";
				$sql=sprintf($patron,$nodo,$puerta,$idacceso,$idlin);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 566907563566464587890097");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					
					/*$borrado="n";
					if($hidenPermiso=="n"){
						$borrado="s";
					}*/
								
					//update
					$patron1="UPDATE safey_accesosnodos SET permisos=\"%s\",horade=\"%s\",horahasta=\"%s\",l=\"%s\",m=\"%s\",x=\"%s\",j=\"%s\",v=\"%s\",s=\"%s\",d=\"%s\" WHERE id=\"%s\"";
					$sql1=sprintf($patron1,$hidenPermiso,$fechaDe,$fechaHasta,$l,$m,$x,$j,$v,$s,$d,$idlin);
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 123450464165475681534789661345565746645574546");
				}else{
					//insert
					$patron1="INSERT INTO safey_accesosnodos SET permisos=\"%s\",horade=\"%s\",horahasta=\"%s\",nodo=\"%s\",puerta=\"%s\",idacceso=\"%s\",borrado=\"n\",fechaalta=\"%s\",l=\"%s\",m=\"%s\",x=\"%s\",j=\"%s\",v=\"%s\",s=\"%s\",d=\"%s\"";
					$sql1=sprintf($patron1,$hidenPermiso,$fechaDe,$fechaHasta,$nodo,$puerta,$idacceso,date("Y-m-d"),$l,$m,$x,$j,$v,$s,$d);
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 123450464165475681534789661345565746645574546");
				}
				mysqli_free_result($respuesta);
				
				$devolver=permisosNodosPuertasAccesos($idacceso,$con);
			}
			echo $devolver;
		break;
		case 43:
			$devolver="n";
			
			$id=intval(quitaComillasD($_POST["id"]));
			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $id>0){
				
				//pin safey
				$patron1="SELECT id,idpinalmacen,pinserie,idacceso FROM safey_credenciales_pin WHERE borrado=\"n\" AND id=\"%s\" ";
				$sql1=sprintf($patron1,$id);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 1121233111212356541154646989607");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);

					//pin almacen general
					$patron2="SELECT id FROM almacen_credenciales_pin WHERE borrado=\"n\" AND id=\"%s\" AND pinserie=\"%s\"";
					$sql2=sprintf($patron2,$fila1[1],$fila1[2]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 112123333322132423454646989607");
					if(mysqli_num_rows($respuesta2)>0){
						$fila2=mysqli_fetch_array($respuesta2);

						//borrar de safey
						$patron3="UPDATE safey_credenciales_pin SET borrado=\"s\" WHERE id=\"%s\"";
						$sql3=sprintf($patron3,$fila1[0]);
						$respuesta3=mysqli_query($con,$sql3) or die ("Error al borrar 1234563474535435893333");
						
						//update el acceso ese pin
						$patron3="UPDATE safey_accesos SET pin=\"s\",pinactivo=\"off\" WHERE id=\"%s\"";
						$sql3=sprintf($patron3,$fila1[3]);
						$respuesta3=mysqli_query($con,$sql3) or die ("Error al borrar 123456347893333");
						
						//borrar de almacen general
						$patron4="UPDATE almacen_credenciales_pin SET borrado=\"s\" WHERE id=\"%s\"";
						$sql4=sprintf($patron4,$fila2[0]);
						$respuesta4=mysqli_query($con,$sql4) or die ("Error al borrar 1234563478933322223");
						
					}else{
						//borrar de safey, no viene de almacen
						$patron3="UPDATE safey_credenciales_pin SET borrado=\"s\" WHERE id=\"%s\"";
						$sql3=sprintf($patron3,$id);
						$respuesta3=mysqli_query($con,$sql3) or die ("Error al borrar 1234563474535435893333");
					}
					mysqli_free_result($respuesta2);
				}
				mysqli_free_result($respuesta1);
				
				//echo credencialesPinSafeyConfiguracion($con);
			}
			echo $devolver;
		break;
		case 44:
			$devolver="n";
			
			$id=intval(quitaComillasD($_POST["id"]));
			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $id>0){
				
				//update
				$patron1="UPDATE safey_credenciales_llaves SET borrado=\"s\" WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$id);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 12345034646246566775681534789661345565746645574546");
				
				$devolver=credencialesLlaveSafeyConfiguracion($con);
			}
			echo $devolver;
		break;
		case 45:
			$devolver="n";
			
			$id=intval(quitaComillasD($_POST["id"]));
			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $id>0){
			
				//update
				$patron1="UPDATE safey_credenciales_mandos SET borrado=\"s\" WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$id);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 1234503475845646246566775681534789661345565746645574546");
				
				$devolver=credencialesMandoSafeyConfiguracion($con);
			}
			echo $devolver;
		break;
		case 46:
            if($getS=="s=16"){
                $_SESSION["fechaIniHistorialPuertasSafey"]=quitaComillasD($_POST["fechaIni"]);
                $_SESSION["fechaFinHistorialPuertasSafey"]=quitaComillasD($_POST["fechaFin"]);
                $_SESSION["puertaHistorialPuertasSafey"]=quitaComillasD($_POST["puerta"]);		
                $_SESSION["idNodo"]=intval(quitaComillasD($_POST["idnodo"]));
            }
		break;
		case 47:
			$devolver="n";
			
			$pin=quitaComillasD($_POST["pin"]);
			$serie=quitaComillasD($_POST["serie"]);
			$serial=quitaComillasD($_POST["serial"]);
			
			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $pin!="" && $serie!="" && $serial!=""){
				$patron="INSERT INTO safey_credenciales_pin SET pin=\"%s\",pinserie=\"%s\",pinserial=\"%s\",fechaalta=\"%s\",idacceso=0";
				$sql=sprintf($patron,$pin,$serie,$serial,date("Y-m-d"));
				$respuesta=mysqli_query($con,$sql) or die ("Error al 1234503475845646246566775681534789679661345565746645574546");
				
				$devolver=credencialesPinSafeyConfiguracion($con);
			}
			echo $devolver;
		break;		
		case 48:
			$devolver="n";
			
			$pin=quitaComillasD($_POST["pin"]);
			$serie=quitaComillasD($_POST["serie"]);
			$serial=quitaComillasD($_POST["serial"]);
			$idlin=intval(quitaComillasD($_POST["idlin"]));
			$cliente=intval(quitaComillasD($_POST["cliente"]));
			
			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $pin!="" && $serie!="" && $serial!="" && $idlin>0){
				
				$patron1="SELECT id,idacceso,idusuario FROM safey_credenciales_pin WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$idlin);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 11121233453451112646989607");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					
					
					if($fila1[1]>0){//tiene acceso asociado, no puedes pasar a nada
						//no puedes pasar a nada
					}else{
						//pasar al que toque
						
						$patron="UPDATE safey_credenciales_pin SET pin=\"%s\",pinserie=\"%s\",pinserial=\"%s\",idusuario=\"%s\",idacceso=0 WHERE id=\"%s\"";
						$sql=sprintf($patron,$pin,$serie,$serial,$cliente,$idlin);
						$respuesta=mysqli_query($con,$sql) or die ("Error al 1234503475844575646246566775681534789679661345565746645574546");
					}
				}
				mysqli_free_result($respuesta1);
				
				$devolver=credencialesPinSafeyConfiguracion($con);
			}
			echo $devolver;
		break;	
		case 49:
			$devolver="n";
			
			$tipo=quitaComillasD($_POST["tipo"]);
			$serie=quitaComillasD($_POST["serie"]);
			$serial=quitaComillasD($_POST["serial"]);
			$frecuencia=quitaComillasD($_POST["frecuencia"]);
			$descripcion=quitaComillasD($_POST["descripcion"]);
			
			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $serie!="" && $serial!="" && $tipo>0 && ($frecuencia=="13.56" || $frecuencia=="125")){
				
				$patron="INSERT INTO safey_credenciales_llaves SET llaveserie=\"%s\",llavepinserial=\"%s\",tipo=\"%s\",frecuencia=\"%s\",fechaalta=\"%s\",descripcion=\"%s\",idacceso=0";
				$sql=sprintf($patron,$serie,$serial,$tipo,$frecuencia,date("Y-m-d"),$descripcion);
				$respuesta=mysqli_query($con,$sql) or die ("Error al 012345034758445459081534789679661345565746645574546");
				
				$devolver=credencialesLlaveSafeyConfiguracion($con);
			}
			echo $devolver;
		break;
		case 50:
			$devolver="n";
			
			//$tipo=quitaComillasD($_POST["tipo"]);
			$serie=quitaComillasD($_POST["serie"]);
			$serial=quitaComillasD($_POST["serial"]);
			//$frecuencia=quitaComillasD($_POST["frecuencia"]);
			$idlin=intval(quitaComillasD($_POST["idlin"]));
			$cliente=intval(quitaComillasD($_POST["cliente"]));
			$descripcion=quitaComillasD($_POST["descripcion"]);
			
			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $serie!="" && $serial!="" && $idlin>0){
				
				/*$patron="UPDATE safey_credenciales_llaves SET llaveserie=\"%s\",llavepinserial=\"%s\",idusuario=\"%s\",descripcion=\"%s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$serie,$serial,$cliente,$descripcion,$idlin);*/
				$patron="UPDATE safey_credenciales_llaves SET idusuario=\"%s\",descripcion=\"%s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$cliente,$descripcion,$idlin);
				$respuesta=mysqli_query($con,$sql) or die ("Error al 0126589345034758445459081534789679661345565746645574546");
				
				$devolver=credencialesLlaveSafeyConfiguracion($con);
			}
			echo $devolver;
		break;	
		case 51:
			$devolver="n";
			
			$serie=quitaComillasD($_POST["serie"]);
			$serial=quitaComillasD($_POST["serial"]);
			
			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $serie!="" && $serial!=""){
				
				$patron="INSERT INTO safey_credenciales_mandos SET mandoserie=\"%s\",mandoserial=\"%s\"";
				$sql=sprintf($patron,$serie,$serial);
				$respuesta=mysqli_query($con,$sql) or die ("Error al 0126534646906808153478967966591345565746645574546");
				
				$devolver=credencialesMandoSafeyConfiguracion($con);
			}
			echo $devolver;
		break;
		case 52:
			$devolver="n";
			
			$serie=quitaComillasD($_POST["serie"]);
			$serial=quitaComillasD($_POST["serial"]);
			$idlin=intval(quitaComillasD($_POST["idlin"]));
			$cliente=intval(quitaComillasD($_POST["cliente"]));
			
			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $serie!="" && $serial!="" && $idlin>0){
				$patron="UPDATE safey_credenciales_mandos SET mandoserie=\"%s\",mandoserial=\"%s\",idusuario=\"%s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$serie,$serial,$cliente,$idlin);
				$respuesta=mysqli_query($con,$sql) or die ("Error al 012653433464690680815347823433967966591345565746645574546");
				
				$devolver=credencialesMandoSafeyConfiguracion($con);
			}
			echo $devolver;
		break;
		case 53:
			$devolver="n";
			
			$nombre=quitaComillasD($_POST["nombre"]);
			$puerta=quitaComillasD($_POST["puerta"]);
			
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3) && $getS=="s=16" && $nombre!="" && $puerta>0){
				$patron="UPDATE safey_puertas SET nombre=\"%s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$nombre,$puerta);
				$respuesta=mysqli_query($con,$sql) or die ("Error al 012653433464694560680815347823433967966591345565746645574546");
				
				$devolver=$nombre;
			}
			echo $devolver;
		break;
		case 54:
			$devolver="n";
			$i=explode("=",$getI);
			
			$devolver="n";
			
			$idlin=intval(quitaComillasD($_POST["idlin"]));
			$idNodo=intval(quitaComillasD($_POST["idNodo"]));
			
			if($_SESSION["permisossession"]==1 && $getS=="s=16" && $idNodo>0 && $idlin>0 && $i[1]==$idNodo){
				$patron="UPDATE safey_nodos_vinculados SET borrado=\"s\" WHERE id=\"%s\" AND (idnodouno=\"%s\" OR idnododos=\"%s\")";
				$sql=sprintf($patron,$idlin,$idNodo,$idNodo);
				$respuesta=mysqli_query($con,$sql) or die ("Error al 012653433464234563433967966591345565746645574546");
				
				$idusuario=0;
				$patron1="SELECT idusuario FROM safey_nodos WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$idNodo);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96323534542323423467787879958");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					$idusuario=$fila1[0];
				}
				
				echo nodosVinculadosSafey($idNodo,$con);
				echo "@#";
				echo cargaPuertasNodoSafey($idNodo,$con);
				echo "@#";
				echo cargaNodosSafeySelect($idusuario,$idNodo,"nodoVinculadoSafey",$con);
			}else{
				echo "n";
			}
		break;
		case 55:
			$i=explode("=",$getI);

			$n=intval(quitaComillasD($_POST["n"]));
			$npadre=intval(quitaComillasD($_POST["npadre"]));

			if($getS=="s=16" && $npadre>0 && $n>0 && $i[1]==$npadre && $_SESSION["permisossession"]==1){

				//crear
				$patron="INSERT INTO safey_nodos_vinculados SET idnodouno=\"%s\",idnododos=\"%s\"";
				$sql=sprintf($patron,$npadre,$n);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 133466372354223567736537534565763");

				$idusuario=0;
				$patron1="SELECT idusuario FROM safey_nodos WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$npadre);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963223433534542323423467787879958");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					$idusuario=$fila1[0];
				}
				
				echo nodosVinculadosSafey($npadre,$con);
				echo "@#";
				echo cargaPuertasNodoSafey($npadre,$con);
				echo "@#";
				echo cargaNodosSafeySelect($idusuario,$npadre,"nodoVinculadoSafey",$con);
				
			}else{
				echo "n";
			}
		break;
		case 56:
			$i=explode("=",$getI);

			$devolver="n";
			
			$nodo=intval(quitaComillasD($_POST["nodo"]));
			$puerta=intval(quitaComillasD($_POST["puerta"]));
			$salidaMarcada=intval(quitaComillasD($_POST["electro"]));
			
			$salidaOpuesta=0;
			
			if($getS=="s=16" && $puerta>0 && $nodo>0 && $i[1]==$nodo && $_SESSION["permisossession"]==1 && ($salidaMarcada==1 || $salidaMarcada==2)){
			
				$patron="SELECT id,salidaplaca FROM safey_puertas WHERE idnodo=\"%s\" AND borrado=\"n\"";
				$sql=sprintf($patron,$nodo);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963223433546465734542323423467787879958");
				if(mysqli_num_rows($respuesta)>0){
					for($i=0;$i<mysqli_num_rows($respuesta);$i++){
						$fila=mysqli_fetch_array($respuesta);
						
						if($salidaMarcada==1){
							$salidaOpuesta=2;
						}else if($salidaMarcada==2){
							$salidaOpuesta=1;
						}
						
						if($fila[0]==$puerta){
							$patron1="UPDATE safey_puertas SET salidaplaca=\"%s\" WHERE id=\"%s\"";
							$sql1=sprintf($patron1,$salidaMarcada,$fila[0]);
							$respuesta1=mysqli_query($con,$sql1) or die ("Error al 01265343346421115574546");
						}else if($fila[0]!=$puerta){
							$patron2="UPDATE safey_puertas SET salidaplaca=\"%s\" WHERE id=\"%s\"";
							$sql2=sprintf($patron2,$salidaOpuesta,$fila[0]);
							$respuesta2=mysqli_query($con,$sql2) or die ("Error al 012653422334642111557452246");
						}
					}
				}
				mysqli_free_result($respuesta);
			
				$devolver=cargaPuertasNodoSafey($nodo,$con);
			}
			echo $devolver;
		break;
		case 57:
			$i=explode("=",$getI);

			$devolver="n";
			
			$nodo=intval(quitaComillasD($_POST["nodo"]));
			$puerta=intval(quitaComillasD($_POST["puerta"]));
			$tipo=quitaComillasD($_POST["tipo"]);
			$segundos=quitaComillasD($_POST["segundos"]);
			
			if($getS=="s=16" && $puerta>0 && $nodo>0 && $i[1]==$nodo && $_SESSION["permisossession"]==1 && ($tipo=="p" || $tipo=="c")){
			
				$patron="SELECT id,salidaplaca FROM safey_puertas WHERE idnodo=\"%s\" AND borrado=\"n\"";
				$sql=sprintf($patron,$nodo);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632234345657354634637367699890574623423467787879958");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);

					if($segundos<=0 || $segundos>50){
						$segundos=1;
					}
					
					$patron1="UPDATE safey_puertas SET pulsocorriente=\"%s\",duracionsegundos=\"%s\" WHERE id=\"%s\"";
					$sql1=sprintf($patron1,$tipo,$segundos,$puerta);
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al 01264564575343346421115574546");
					
				}
				mysqli_free_result($respuesta);
			
				$devolver=cargaPuertasNodoSafey($nodo,$con);
			}
			echo $devolver;
		break;
		case 58:
			$i=explode("=",$getI);

			$devolver="n";
			
			$nodo=intval(quitaComillasD($_POST["nodo"]));
			$puerta=intval(quitaComillasD($_POST["puerta"]));
			$segundos=quitaComillasD($_POST["segundos"]);
			
			if($getS=="s=16" && $puerta>0 && $nodo>0 && $i[1]==$nodo && $_SESSION["permisossession"]==1 && ($segundos>0 && $tipo<50)){
			
				$patron="SELECT id,salidaplaca FROM safey_puertas WHERE idnodo=\"%s\" AND borrado=\"n\"";
				$sql=sprintf($patron,$nodo);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632234386864634637367699890574623423467787879958");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);

					if($segundos<=0 || $segundos>50){
						$segundos=1;
					}
					
					$patron1="UPDATE safey_puertas SET duracionsegundos=\"%s\" WHERE id=\"%s\"";
					$sql1=sprintf($patron1,$segundos,$puerta);
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al 0126456457534334689787645421115574546");
					
				}
				mysqli_free_result($respuesta);
			
				$devolver=cargaPuertasNodoSafey($nodo,$con);
			}
			echo $devolver;
		break;
		case 59:
			if($_SESSION["permisossession"]==1 && $getS=="s=21"){
				$_SESSION["usuarioPistasPadelList"]=$_POST["u"];
				echo cargaNodosPistaPadelList($con);
			}else{
				echo "n";
			}
		break;
		case 60:
			if($getS=="s=21"){
				$_SESSION["conexionPistasPadelList"]=quitaComillasD($_POST["e"]);
				echo cargaNodosPistaPadelList($con);
			}else{
				echo "n";
			}
		break;
		case 61:
			$n=quitaComillasD($_POST["n"]);
			if($getS=="s=22" && $n>0){
				$_SESSION["fechaIniHistorialPuertasPistasPadel"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialPuertasPistasPadel"]=quitaComillasD($_POST["fechaFin"]);
				
				echo puertasPistaPadelHistorial($n,$con);
			}else{
				echo "n";
			}
		break;
		case 62:
			$i=explode("=",$getI);
			
			$id=intval(quitaComillasD($_POST["id"]));
			
			if($id>0 && $getS=="s=22" && $i[1]==$id && $_SESSION["permisossession"]==1){
				$patron="UPDATE pistaspadel_nodos SET borrado=\"s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$id);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 123450465685347896634556534544575676746645574546");
			}
		break;
		case 63: 
			$devolver="n";
			$i=explode("=",$getI);

			$tipo=intval(quitaComillasD($_POST["tipo"]));
			$n=intval(quitaComillasD($_POST["n"]));
			$p=quitaComillasD($_POST["p"]);

			if($getS=="s=22" && $n>0 && ($tipo>0 && $tipo<=5) && ($p=="izq" || $p=="der" || $p=="amb")){
				
				/*$idUsuario=$_SESSION["idusersession"];
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2){
					$idUsuario.=" AND idusuario=\"".$_SESSION["idusersession"]."\"";
				}else if(){
					$idUsuario.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
				}*/
				
				$minutosPartida=0;
				$patron1="SELECT tiempopartida FROM pistaspadel_nodos WHERE id=\"%s\" AND borrado=\"n\" AND guardado=\"s\"";
				$sql1=sprintf($patron1,$n);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96322343868646346373600227879958");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					$minutosPartida=$fila1[0];
				}
				mysqli_free_result($respuesta1);
				
				
				$idUsuario=$_SESSION["idusersession"];
				
				if($idUsuario>0){
					
					/*START saber si han pasado dos minutos desde la ultima accion*/
					$horaAltaAccion="";
					$fechaAltaAccion="";
					$accionRealizada="";
					$patron2="SELECT horaalta,fechaalta,miradoplaca,accionrealizada FROM pistaspadel_historial WHERE idnodo=\"%s\" AND fechaalta=\"%s\" AND idreservapadel=0 ORDER BY id DESC LIMIT 0,1 ";
					$sql2=sprintf($patron2,$n,date("Y-m-d"));
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 96322228634546346373222600227879958");
					if(mysqli_num_rows($respuesta2)>0){
						$fila2=mysqli_fetch_array($respuesta2);
						$horaAltaAccion=strtotime($fila2[0]);
						$fechaAltaAccion=$fila2[1];
						$accionRealizada=$fila2[2];
					}
					mysqli_free_result($respuesta2);
					
					$crearNuevaAccion=false;
					if($horaAltaAccion!="" && $fechaAltaAccion!="" && $accionRealizada!="s"){
						$auxNuevaPulsacionCalculada=strtotime('+2 minute',$horaAltaAccion);
						//echo strtotime(date("H:i:s"))."--".$auxNuevaPulsacionCalculada;
						if(strtotime(date("H:i:s"))>$auxNuevaPulsacionCalculada || $fechaAltaAccion<date("Y-m-d") ){
							$crearNuevaAccion=true;
						}
					}else{
						$crearNuevaAccion=true;
					}
					/*END saber si han pasado dos minutos desde la ultima accion*/
					
					if($crearNuevaAccion){
						$patron="INSERT INTO pistaspadel_historial SET idnodo=\"%s\",puerta=\"%s\",tipo=\"%s\",idacceso=0,horaalta=\"%s\",fechaalta=\"%s\",idusuario=\"%s\",accionrealizada=\"n\",miradoplaca=\"n\",minutospartida=\"%s\",idreservapadel=0,precio=0";
						$sql=sprintf($patron,$n,$p,$tipo,date("H:i:s"),date("Y-m-d"),$idUsuario,$minutosPartida);
						$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 13346633567456756766537534565763");

						$devolver="s";
					}else{//esperar unos momentos
						$devolver="d";
					}
				}
			}
			echo $devolver;
		break;
		case 64:
			if($_SESSION["permisossession"]==1 && $getS=="s=23"){
				$_SESSION["usuarioParquesList"]=$_POST["u"];
				echo cargaNodosParquesList($con);
			}else{
				echo "n";
			}
		break;
		case 65:
			if($getS=="s=23"){
				$_SESSION["conexionParquesList"]=quitaComillasD($_POST["e"]);
				echo cargaNodosParquesList($con);
			}else{
				echo "n";
			}
		break;
		case 66:
			$n=quitaComillasD($_POST["n"]);
			if($getS=="s=24" && $n>0){
				$_SESSION["fechaIniHistorialPuertasParques"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialPuertasParques"]=quitaComillasD($_POST["fechaFin"]);
				
				echo puertasParqueHistorial($n,$con);
			}else{
				echo "n";
			}
		break;
		case 67: 
			$devolver="n";
			$i=explode("=",$getI);

			$tipo=intval(quitaComillasD($_POST["tipo"]));
			$n=intval(quitaComillasD($_POST["n"]));
			$p=quitaComillasD($_POST["p"]);

			if($getS=="s=24" && $n>0 && ($tipo>0 && $tipo<=4) && ($p=="izq" || $p=="der" || $p=="amb")){
				
				/*$idUsuario=$_SESSION["idusersession"];
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2){
					$idUsuario.=" AND idusuario=\"".$_SESSION["idusersession"]."\"";
				}else if(){
					$idUsuario.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
				}*/
				
				
				$idUsuario=$_SESSION["idusersession"];
				$patron="INSERT INTO parques_historial SET idnodo=\"%s\",puerta=\"%s\",tipo=\"%s\",idacceso=0,horaalta=\"%s\",fechaalta=\"%s\",idusuario=\"%s\",accionrealizada=\"n\",miradoplaca=\"n\"";
				$sql=sprintf($patron,$n,$p,$tipo,date("H:i:s"),date("Y-m-d"),$idUsuario);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 133466335674234523456756766537534565763");

				$devolver="s";
				
			}
			echo $devolver;
		break;
		case 68:
			$i=explode("=",$getI);
			
			$id=intval(quitaComillasD($_POST["id"]));
			
			if($id>0 && $getS=="s=24" && $i[1]==$id && $_SESSION["permisossession"]==1){
				$patron="UPDATE parques_nodos SET borrado=\"s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$id);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 1234454504652345544575676746645574546");
			}
		break;
		case 69:
			$devolver="n";
			$id=intval(quitaComillasD($_POST["id"]));
			
			if($id>0 && $getS=="s=18"){
				$email="";
				$nombre="";
				$apellidos="";
				$nombreEmpresa="";
				
				$pinAcceso="";
				$llaveAcceso="";
				$mandoAcceso="";
				$panelAcceso="";
				$emailAcceso="";
				$contrasenaAcceso="-";
				$emailAdministradorSistema="-";
				
				//obtener datos
				$patron="SELECT id,nombre,idusuario,pin,llave,mando,maillogin,email,apellidos FROM safey_accesos WHERE id=\"%s\" AND borrado=\"n\"";
				$sql=sprintf($patron,$id);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632234386864634637367690023423467787879958");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					
					$nombre=$fila[1];
					$apellidos=$fila[8];
					$email=$fila[7];
					
					//datos del cliente
					$patron1="SELECT nombre,apellidos,email FROM usuarios WHERE id=\"%s\"";
					$sql1=sprintf($patron1,$fila[2]);
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9632234386864113467787879958");
					if(mysqli_num_rows($respuesta1)>0){
						$fila1=mysqli_fetch_array($respuesta1);
						$nombreEmpresa=$fila1[0]." ".$fila1[1];
						$emailAdministradorSistema=$fila1[2];
					}
					mysqli_free_result($respuesta1);
                    
                    //datos de la placa del cliente, coge uno de ellos, no coge el que aplique
                    $rutaFicheroAdjunto="";
                    $nombreFicheroAdjunto="";
                    /*$patron8="SELECT nodo FROM safey_accesosnodos WHERE idacceso=\"%s\" AND borrado=\"n\" ";
					$sql8=sprintf($patron8,$id);
					$respuesta8=mysqli_query($con,$sql8) or die ("Error al buscar 96323357889652343868641134677878708");
					if(mysqli_num_rows($respuesta8)>0){
						$fila8=mysqli_fetch_array($respuesta8);*/
                        
                        //datos
                        $patron7="SELECT ficheronormas FROM safey_nodos WHERE idusuario=\"%s\" AND ficheronormas<>\"\" AND borrado=\"n\" AND guardado=\"s\" ";//AND id=\"%s\"
                        $sql7=sprintf($patron7,$fila[2]/*,$fila8[0]*/);
                        $respuesta7=mysqli_query($con,$sql7) or die ("Error al buscar 9632335776775234386864113467787879958");
                        if(mysqli_num_rows($respuesta7)>0){
                            $fila7=mysqli_fetch_array($respuesta7);
                            $rutaFicheroAdjunto="archivos_subidos/clientes/".$fila[2]."/safey/".$fila7[0];
                            $nombreFicheroAdjunto=$fila7[0];
                        }
                        mysqli_free_result($respuesta7);
                    /*}
					mysqli_free_result($respuesta8);*/
                    
					
					//pin
					$patron2="SELECT pin FROM safey_credenciales_pin WHERE id=\"%s\"";
					$sql2=sprintf($patron2,$fila[3]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 9632234386864113467782227879958");
					if(mysqli_num_rows($respuesta2)>0){
						$fila2=mysqli_fetch_array($respuesta2);
						$pinAcceso="- Su pin de acceso es: <b>".$fila2[0]."#</b><br>";
					}
					mysqli_free_result($respuesta2);
					
					//llave
					$patron3="SELECT descripcion FROM safey_credenciales_llaves WHERE id=\"%s\"";
					$sql3=sprintf($patron3,$fila[3]);
					$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 96322343333113467782227879958");
					if(mysqli_num_rows($respuesta3)>0){
						$fila3=mysqli_fetch_array($respuesta3);
						$llaveAcceso="- Su llave de acceso es: <b>".$fila3[0]."</b><br>";
					}
					mysqli_free_result($respuesta3);
					
					//acceso panel
					$patron5="SELECT descripcion FROM safey_credenciales_llaves WHERE id=\"%s\"";
					$sql5=sprintf($patron5,$fila[3]);
					$respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 963225343333511534677825227879958");
					if(mysqli_num_rows($respuesta5)>0){
						$fila5=mysqli_fetch_array($respuesta5);
						$panelAcceso=$fila5[0];
					}
					mysqli_free_result($respuesta5);
					
					//mando
					$patron4="SELECT id FROM safey_credenciales_mandos WHERE id=\"%s\"";
					$sql4=sprintf($patron4,$fila[5]);
					$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 9632234444333311346778442227879958");
					if(mysqli_num_rows($respuesta4)>0){
						$fila4=mysqli_fetch_array($respuesta4);
						$mandoAcceso="- Su mando de acceso es: <b>M ".$fila4[0]."#</b><br>";
					}
					mysqli_free_result($respuesta4);
					
					//datos del acceso web creado
					$patron6="SELECT email,aes_decrypt(contrasena, \"%s\") FROM usuarios WHERE id=\"%s\"";
					$sql6=sprintf($patron6,BBDDK,$fila[6]);
					$respuesta6=mysqli_query($con,$sql6) or die ("Error al buscar 9632234386864113467787879958666");
					if(mysqli_num_rows($respuesta6)>0){
						$fila6=mysqli_fetch_array($respuesta6);
						$emailAcceso="- Se le ha creado un usuario de acceso al portal web: <a href='https://panel.modularbox.com/'>Acceder al panel</a><br>Usuario: <b>".$fila6[0]."</b>";
						$contrasenaAcceso=" contraseña: <b>".$fila6[1]."</b><br>";
					}
					mysqli_free_result($respuesta6);
				}
				mysqli_free_result($respuesta);
				
				//enviar mail de accesos
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					$copia="";
					$asunto=$nombre." -- SAFEY INVITACIÓN (MODULARBOX)";
					//$contenido="Hola, <b>".$nombre."</b><br><br>Ha sido invitado a la utilización de Safey de MODULARBOX S.L. por: <b>".$nombreEmpresa."</b>.<br><br>".$pinAcceso."".$llaveAcceso."".$mandoAcceso."<br>".$emailAcceso."".$contrasenaAcceso."<br>Para cualquier duda contacte con el administrador del sistema: <a href='mailto:".$emailAdministradorSistema."'>".$emailAdministradorSistema."</a><br><br>Un saludo. <br><br><br> <b>No responda a este mensaje</b> ha sido autogenerado por la plataforma <b>(MODULARBOX)</b>.";
					
					
					/*START contenido mail html definido julio*/
					$contenido="<!DOCTYPE html>
                                    <html>
                                    <head>
                                        <meta charset='UTF-8' />
                                        <meta name='viewport' content='width=device-width, initial-scale=1.0' />
                                        <title>Confirmación de Pago y PIN de Acceso</title>
                                        <style>
                                            body {
                                                font-family: monospace, Helvetica, Arial, sans-serif !important;
                                                background-color: #f4f4f4 !important;
                                                margin: 0 !important;
                                                padding: 0 !important;
                                                font-size: 0.9rem !important;
                                            }

                                            .container {
                                                width: 100% !important;
                                                max-width: 600px !important;
                                                margin: 0 auto !important;
                                                background-color: #ffffff !important;
                                                padding: 20px !important;
                                                border-radius: 8px !important;
                                                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1) !important;
                                            }

                                            .header {
                                                text-align: center !important;
                                                padding: 10px 0 !important;
                                                background-color: #28a745 !important;
                                                color: #ffffff !important;
                                                border-radius: 8px 8px 0 0 !important;
                                            }

                                            .header h1 {
                                                margin: 0 !important;
                                                font-size: 24px !important;
                                            }

                                            .content {
                                                margin: 20px 0 !important;
                                            }

                                            .content p {
                                                line-height: 1.6 !important;
                                                color: #333333 !important;
                                            }

                                            .pin {
                                                text-align: center !important;
                                                margin: 20px 0 !important;
                                                padding: 15px !important;
                                                color: #ffffff !important;
                                                font-size: 2rem !important;
                                                word-break: break-all !important;
                                                font-weight: 900 !important;
                                            }

                                            .pin span {
                                                background-color: #28a745 !important;
                                                padding: 15px !important;
                                                border-radius: 5px !important;
                                            }

                                            .footer {
                                                text-align: center !important;
                                                font-size: 12px !important;
                                                color: #999999 !important;
                                                margin: 20px 0 0 !important;
                                            }
                                        </style>

                                    </head>

                                    <body>
                                        <div class='container'>
                                            <div class='header'>
                                                <h1>PIN de Acceso GYM<br><strong>".$nombreEmpresa."</strong></h1>
                                            </div>
                                            <div class='content'>
                                                <p>Estimad@".$nombre." ".$apellidos.",<br>
                                                    nos complace informarle que su pago de la subscripción al gym de ".$nombreEmpresa." ha sido procesado
                                                    satisfactoriamente.
                                                </p>
                                                <p>
                                                    A continuación, encontrará su PIN de acceso que le permitirá ingresar a nuestras instalaciones:
                                                </p>
                                                <div class='pin'><span>".$pinAcceso."</span></div>
                                                <p>
                                                    <strong>Contacto:</strong><br>
                                                    Si tiene alguna pregunta o necesita asistencia adicional, no dude en contactarnos a través de:
                                                    <br>Email: <a href='mailto:gym@modularbox.com'>gym@modularbox.com</a>
                                                    <br>WhatsApp: <a href='https://wa.me/34653483483'>653 483 483</a>
                                                    <br>Horario: L-V 09:00 - 14:00 | 17:00 - 20:00</a>
                                                    <!--Si tiene alguna pregunta o necesita asistencia adicional, no dude en contactarnos a través de <a
                                                        href='mailto:gym@modularbox.com'>gym@modularbox.com</a>, enviándonos un mensaje al <a
                                                        href='https://wa.me/34607373372'>607 373 372</a> o a través de la web dónde se dió de alta.-->
                                                </p>
                                                <p>
                                                    Agradecemos su confianza y esperamos que disfrute del gym.
                                                </p>
                                                <p>Atentamente,<br />El equipo de <a href='https://gym.modularbox.com/'>Modularbox</a></p>
                                            </div>

                                            <p style='color: #708c91;text-decoration: none;font-size: 12px;'>Te informamos de que seguirás recibiendo mensajes relacionados con tus subscripciones. Para saber más sobre la forma en la que usamos tu información, puedes consultar nuestra política de privacidad <a href='https://reservatupista.com/politica-de-privacidad-proteccion-de-datos-y-politica-de-cookies/' target='_blank' rel='noopener noreferrer' data-auth='NotApplicable' title='date de baja aquí'  style='text-decoration: none !important; color: #2dbeff' data-linkindex='11'>aquí</a>. <br /><br />
                                            </p>
                                            <div class='footer'>
                                                <p>&copy; 2024 Modularbox. Todos los derechos reservados.</p>
                                            </div>
                                        </div>
                                    </body>";
                    
					echo mailGenerico($email,$copia,$asunto,$contenido,$id,$rutaFicheroAdjunto,$nombreFicheroAdjunto,$con);
					
					$devolver="s";
				}
			}
			echo $devolver;
		break;
		case 70:
			$devolver="n";
			
			$pin=quitaComillasD($_POST["pin"]);
			$serie=quitaComillasD($_POST["serie"]);
			$serial=quitaComillasD($_POST["serial"]);
			
			if($_SESSION["permisossession"]==1 && $getS=="s=25" && $pin!="" && $serie!="" && $serial!=""){
				
				$patron1="SELECT id FROM almacen_credenciales_pin WHERE pin=\"%s\" OR pinserie=\"%s\" OR pinserial=\"%s\"";
				$sql1=sprintf($patron1,$pin,$serie,$serial);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9632111114677825227879958");
				if(mysqli_num_rows($respuesta1)>0){
					//$fila1=mysqli_fetch_array($respuesta1);
				}else{
					$patron="INSERT INTO almacen_credenciales_pin SET pin=\"%s\",pinserie=\"%s\",pinserial=\"%s\",fechaalta=\"%s\"";
					$sql=sprintf($patron,$pin,$serie,$serial,date("Y-m-d"));
					$respuesta=mysqli_query($con,$sql) or die ("Error al 1234503475845646246566774456746645574546");
				}
				mysqli_free_result($respuesta1);
				
				$devolver=credencialesPinAlmacenConfiguracion($con);
			}
			echo $devolver;
		break;	
		case 71:
			$devolver="n";
			
			$id=intval(quitaComillasD($_POST["id"]));
			if($_SESSION["permisossession"]==1 && $getS=="s=25" && $id>0){
				
				//update
				$patron1="UPDATE almacen_credenciales_pin SET borrado=\"s\" WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$id);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 12345046234343681534789661345565746645574546");
				
				echo credencialesPinAlmacenConfiguracion($con);
			}
			echo $devolver;
		break;
		case 72:
			$devolver="n";
			
			$pin=quitaComillasD($_POST["pin"]);
			$serie=quitaComillasD($_POST["serie"]);
			$serial=quitaComillasD($_POST["serial"]);
			$idlin=intval(quitaComillasD($_POST["idlin"]));
			
			if($_SESSION["permisossession"]==1 && $getS=="s=25" && $pin!="" && $serie!="" && $serial!="" && $idlin>0){
				
				$patron="UPDATE almacen_credenciales_pin SET pin=\"%s\",pinserie=\"%s\",pinserial=\"%s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$pin,$serie,$serial,$idlin);
				$respuesta=mysqli_query($con,$sql) or die ("Error al 12345034758445756462463435789679661345565746645574546");
				
				$devolver=credencialesPinAlmacenConfiguracion($con);
			}
			echo $devolver;
		break;
		case 73:
			$devolver="n";
			
			$tipo=quitaComillasD($_POST["tipo"]);
			$serie=quitaComillasD($_POST["serie"]);
			$serial=quitaComillasD($_POST["serial"]);
			$frecuencia=quitaComillasD($_POST["frecuencia"]);
			$descripcion=quitaComillasD($_POST["descripcion"]);
			
			if($_SESSION["permisossession"]==1 && $getS=="s=25" && $serie!="" && $serial!="" && $tipo>0 && ($frecuencia=="13.56" || $frecuencia=="125")){
				
				$patron1="SELECT id FROM almacen_credenciales_llaves WHERE (llaveserie=\"%s\" OR llavepinserial=\"%s\") AND borrado=\"n\"";
				$sql1=sprintf($patron1,$serie,$serial);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963211111467556767825227879958");
				if(mysqli_num_rows($respuesta1)>0){
					//$fila1=mysqli_fetch_array($respuesta1);
				}else{
					$patron="INSERT INTO almacen_credenciales_llaves SET llaveserie=\"%s\",llavepinserial=\"%s\",tipo=\"%s\",frecuencia=\"%s\",fechaalta=\"%s\",descripcion=\"%s\",borrado=\"n\"";
					$sql=sprintf($patron,$serie,$serial,$tipo,$frecuencia,date("Y-m-d"),$descripcion);
					$respuesta=mysqli_query($con,$sql) or die ("Error al 0123450347584454590817874431345565746645574546");
				}
				mysqli_free_result($respuesta1);
				
				$devolver=credencialesLlaveAlmacenConfiguracion($con);
			}
			echo $devolver;
		break;
		case 74:
			$devolver="n";
			
			$id=intval(quitaComillasD($_POST["id"]));
			if($_SESSION["permisossession"]==1 && $getS=="s=25" && $id>0){
				
				//update
				$patron1="UPDATE almacen_credenciales_llaves SET borrado=\"s\" WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$id);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 1245454345034646246566775681534789661345565746645574546");
				
				$devolver=credencialesLlaveAlmacenConfiguracion($con);
			}
			echo $devolver;
		break;
		case 75:
			$devolver="n";
			
			//$tipo=quitaComillasD($_POST["tipo"]);
			$serie=quitaComillasD($_POST["serie"]);
			$serial=quitaComillasD($_POST["serial"]);
			//$frecuencia=quitaComillasD($_POST["frecuencia"]);
			$idlin=intval(quitaComillasD($_POST["idlin"]));
			$descripcion=quitaComillasD($_POST["descripcion"]);
			
			if($_SESSION["permisossession"]==1 && $getS=="s=25" && $serie!="" && $serial!="" && $idlin>0){
				
				$patron="UPDATE almacen_credenciales_llaves SET descripcion=\"%s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$descripcion,$idlin);
				$respuesta=mysqli_query($con,$sql) or die ("Error al 01265800919081534789679661345565746645574546");
				
				$devolver=credencialesLlaveAlmacenConfiguracion($con);
			}
			echo $devolver;
		break;	
		case 76:
			$devolver="n";
			
			if($_SESSION["permisossession"]==1 && $getS=="s=25" && $_POST["cantidadPines"]>0){
				
                $cantidadGenerarPines=$_POST["cantidadPines"];
				$longitudNumero=6;
                				
				for($i=0;$i<$cantidadGenerarPines;$i++){
					//generar numero, comprobar que no este en bbdd
					$numeroGenerado=rand(1, 999999);
					$numeroGenerado=str_pad($numeroGenerado, $longitudNumero, "0", STR_PAD_LEFT); 
					
					$patron1="SELECT id FROM almacen_credenciales_pin WHERE pin=\"%s\"";
					$sql1=sprintf($patron1,$numeroGenerado);
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96321111144545677825227879958");
					if(mysqli_num_rows($respuesta1)>0){
						//$fila1=mysqli_fetch_array($respuesta1);
						$cantidadGenerarPines-=1;//para repetir la vuelta del bucle
					}else{
						
						if($numeroGenerado=="111111" || $numeroGenerado=="222222" || $numeroGenerado=="333333" || $numeroGenerado=="444444" || $numeroGenerado=="555555" || $numeroGenerado=="666666" || $numeroGenerado=="777777" || $numeroGenerado=="888888" || $numeroGenerado=="999999" || $numeroGenerado=="000000" || $numeroGenerado=="123456"){
							$cantidadGenerarPines-=1;//para repetir la vuelta del bucle
						}else{
							$patron="INSERT INTO almacen_credenciales_pin SET pin=\"%s\",pinserie=\"\",pinserial=\"\",fechaalta=\"%s\"";
							$sql=sprintf($patron,$numeroGenerado,date("Y-m-d"));
							$respuesta=mysqli_query($con,$sql) or die ("Error al 12345335345342465545746645574546");
						}
						
					}
					mysqli_free_result($respuesta1);
				}
				$devolver=credencialesPinAlmacenConfiguracion($con);
			}
			echo $devolver;
		break;	
		case 77:
			$devolver="n";
            
			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $_POST["cantidadPines"]>0){
				
                $cantidadPinesCrear=$_POST["cantidadPines"];
				$cantidadPinesCreados=0;
				
				$patron="SELECT id,pin,pinserie,pinserial FROM almacen_credenciales_pin WHERE borrado=\"n\"";
				$sql=sprintf($patron);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96321000111144545677825227879958");
				if(mysqli_num_rows($respuesta)>0){
					for($i=0;$i<mysqli_num_rows($respuesta);$i++){
						$fila=mysqli_fetch_array($respuesta);
						
						if($fila[2]!="" && $fila[3]!=""){
							
							$patron1="SELECT id FROM safey_credenciales_pin WHERE idpinalmacen=\"%s\"";
							$sql1=sprintf($patron1,$fila[0]);
							$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963211111444454561111227879958");
							if(mysqli_num_rows($respuesta1)>0){
								//$fila1=mysqli_fetch_array($respuesta1);
							}else{
								$patron2="INSERT INTO safey_credenciales_pin SET pin=\"%s\",pinserie=\"%s\",pinserial=\"%s\",idusuario=0,idacceso=0,idpinalmacen=\"%s\",fechaalta=\"%s\"";
								$sql2=sprintf($patron2,$fila[1],$fila[2],$fila[3],$fila[0],date("Y-m-d"));
								$respuesta2=mysqli_query($con,$sql2) or die ("Error al 12345335345342465545222645574546");

								$cantidadPinesCreados+=1;
							}
							mysqli_free_result($respuesta1);
						}
						
						//romper bucle si ya tenemos la cantidad que queremos
						if($cantidadPinesCrear<=$cantidadPinesCreados){
							break;
						}
					}
				}
				mysqli_free_result($respuesta);
				
				echo credencialesPinSafeyConfiguracion($con);
				echo "@#";
				echo $cantidadPinesCreados;
			}else{
				echo "n";
			}
		break;
		case 78:
			$devolver="n";
			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $_POST["cantidadLlaves"]>0){
				
                $cantidadLlavesCrear=$_POST["cantidadLlaves"];
				$cantidadLlavesCreados=0;
				
				$patron="SELECT id,descripcion,llaveserie,llavepinserial,tipo,frecuencia,color FROM almacen_credenciales_llaves WHERE borrado=\"n\"";
				$sql=sprintf($patron);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963210343400111144545677889825227879958");
				if(mysqli_num_rows($respuesta)>0){
					for($i=0;$i<mysqli_num_rows($respuesta);$i++){
						$fila=mysqli_fetch_array($respuesta);
						
						if($fila[2]!="" && $fila[3]!=""){
							
							$patron1="SELECT id FROM safey_credenciales_llaves WHERE idllavealmacen=\"%s\"";
							$sql1=sprintf($patron1,$fila[0]);
							$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9632111114444524784561111227879958");
							if(mysqli_num_rows($respuesta1)>0){
								//$fila1=mysqli_fetch_array($respuesta1);
							}else{
								$patron2="INSERT INTO safey_credenciales_llaves SET descripcion=\"%s\",llaveserie=\"%s\",llavepinserial=\"%s\",tipo=\"%s\",frecuencia=\"%s\",color=\"%s\",idusuario=0,idacceso=0,idllavealmacen=\"%s\",fechaalta=\"%s\"";
								$sql2=sprintf($patron2,$fila[1],$fila[2],$fila[3],$fila[4],$fila[5],$fila[6],$fila[0],date("Y-m-d"));
								$respuesta2=mysqli_query($con,$sql2) or die ("Error al 12345335345342465645545222645574546");

								$cantidadLlavesCreados+=1;
							}
							mysqli_free_result($respuesta1);
						}
						//romper bucle si ya tenemos la cantidad que queremos
						if($cantidadLlavesCrear<=$cantidadLlavesCreados){
							break;
						}
					}
				}
				mysqli_free_result($respuesta);
				
				echo credencialesLlaveSafeyConfiguracion($con);
				echo "@#";
				echo $cantidadPinesCreados;
			}else{
				echo "n";
			}
		break;
		case 79:
			if($getS=="s=26"){
				$_SESSION["conexionCampanasList"]=quitaComillasD($_POST["e"]);
				echo cargaNodosCampanasList($con);
			}else{
				echo "n";
			}
		break;
		case 80:
			if($_SESSION["permisossession"]==1 && $getS=="s=26"){
				$_SESSION["usuarioCampanasList"]=$_POST["u"];
				echo cargaNodosCampanasList($con);
			}else{
				echo "n";
			}
		break;
		case 81:
			$i=explode("=",$getI);
			$id=intval(quitaComillasD($_POST["id"]));
			if($id>0 && $getS=="s=27" && $i[1]==$id && $_SESSION["permisossession"]==1){
				$patron="UPDATE campanas_nodos SET borrado=\"s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$id);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 12344545046523454545544575676746645574546");
			}
		break;
		case 82:
			if($_SESSION["permisossession"]==1 && $getS=="s=28"){
				$_SESSION["usuarioCampanasProgramasList"]=$_POST["u"];
				echo cargaNodosCampanasProgramasList($con);
			}else{
				echo "n";
			}
		break;
		case 83:
			$i=explode("=",$getI);
			$id=intval(quitaComillasD($_POST["id"]));
			if($id>0 && $getS=="s=29" && $i[1]==$id && $_SESSION["permisossession"]==1){
				//borrar programa
				$patron="UPDATE campanas_programas SET borrado=\"s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$id);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 123445455656504652345454556762005574546");
				
				//quitar de la tabla de activos
				$patron2="UPDATE campanas_programas_activos SET activo=\"n\" WHERE idprograma=\"%s\"";
				$sql2=sprintf($patron2,$id);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al 1234534577835345343432465545743436645574546");
				
			}
		break;
        case 84:
            $devolver="n";
			$i=explode("=",$getI);
			
			$programa=quitaComillasD($_POST["programa"]);
			$lin=quitaComillasD($_POST["lin"]);
            
			if($getS=="s=29" && $programa>0 && $i[1]==$programa && $lin>0){
				
				//borrar
				$patron1="UPDATE campanas_programas_configuracion SET borrado=\"s\" WHERE id=\"%s\" AND idprograma=\"%s\"";
				$sql1=sprintf($patron1,$lin,$programa);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error 1233576734326753567634563565346646776467");
				
				
                $devolver=configuracionProgramaCampanas($programa,$con);
			}
			echo $devolver;
        break;
		case 85:
			$devolver="n";
			$i=explode("=",$getI);
			
			if($_POST["accion"]==1){
				$accion="<";
				$orden="ORDER BY id DESC";
			}else{
				$accion=">";
				$orden="ORDER BY id";
			}

			$programa=intval(quitaComillasD($_POST["programa"]));
			$id=intval(quitaComillasD($_POST["id"]));
			
			if($getS=="s=29" && $programa>0 && $id>0 && $i[1]==$programa){
				$patron="SELECT id FROM campanas_programas_configuracion WHERE id%s%s AND idprograma=%s %s LIMIT 0,1";
				$sql=sprintf($patron,$accion,$id,$programa,$orden);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 345356754532342456asdasd734324sdfsdf34345454");
				$fila=mysqli_fetch_array($respuesta);

				$patron3="UPDATE campanas_programas_configuracion SET id=\"0\" WHERE id=\"%s\"";
				$sql3=sprintf($patron3,$fila[0]);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error 345353454567534534256a345asd465678477343432sdfs234df34345454");
				
				$patron3="UPDATE campanas_programas_configuracion SET id=\"%s\" WHERE id=\"%s\"";
				$sql3=sprintf($patron3,$fila[0],$id);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error 345353454567555463453456as754d73434s77356dfs78d7f34345454");
				
				$patron3="UPDATE campanas_programas_configuracion SET id=\"%s\" WHERE id=\"0\"";
				$sql3=sprintf($patron3,$id);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error 345352345456345753453456as66as900d7343490'87sdfsdf34345454");
				
				$devolver=configuracionProgramaCampanas($programa,$con);
			}
			echo $devolver;
		break;
		case 86:
			$devolver="n";
			$i=explode("=",$getI);

			$id=quitaComillasD($_POST["lin"]);
			$programa=quitaComillasD($_POST["programa"]);
			
			$r1=intval(quitaComillasD($_POST["r1"]));
			$r2=intval(quitaComillasD($_POST["r2"]));
			$r3=intval(quitaComillasD($_POST["r3"]));
			$temp=intval(quitaComillasD($_POST["temp"]));
			
			if($getS=="s=29" && $programa>0 && $i[1]==$programa && $id>0 && is_numeric($temp)){
				
				$menor=0;//el cero, para dejar vacio
				$mayor=2;
				
				if(($r1>=$menor && $r1<=$mayor) && ($r2>=$menor && $r2<=$mayor) && ($r3>=$menor && $r3<=$mayor)){//validar datos
					//crear
					$patron="UPDATE campanas_programas_configuracion SET releuno=\"%s\",reledos=\"%s\",reletres=\"%s\",temporizacion=%s WHERE id=\"%s\" AND idprograma=\"%s\"";
					$sql=sprintf($patron,$r1,$r2,$r3,$temp,$id,$programa);
					$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 13346800787878587663765375763");
				}
				
				$devolver=configuracionProgramaCampanas($programa,$con);
			}
			echo $devolver;
		break;
		case 87:
			$devolver="n";
			$i=explode("=",$getI);

			$programa=quitaComillasD($_POST["programa"]);

			if($getS=="s=29" && $programa>0){
				//crear, en blanco
				$patron="INSERT INTO campanas_programas_configuracion SET idprograma=\"%s\",releuno=2,reledos=2,reletres=2,temporizacion=4,borrado=\"n\"";
				$sql=sprintf($patron,$programa);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1334663765375763");

				$devolver=configuracionProgramaCampanas($programa,$con);
			}
			echo $devolver;
		break;
		case 88:
			$devolver="n";
			$i=explode("=",$getI);

			$nodo=quitaComillasD($_POST["nodo"]);
			$idPrograma=quitaComillasD($_POST["idPrograma"]);
			$pulsado=quitaComillasD($_POST["pulsado"]);

			if($getS=="s=27" && $idPrograma>0 && $nodo>0 && $i[1]==$nodo){
				
				$patron1="SELECT id FROM campanas_programas_activos WHERE idnodo=\"%s\" AND idprograma=\"%s\"";
				$sql1=sprintf($patron1,$nodo,$idPrograma);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963211111445456778234325227879958");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					
					$patron2="UPDATE campanas_programas_activos SET activo=\"%s\" WHERE idnodo=\"%s\" AND idprograma=\"%s\" AND id=\"%s\"";
					$sql2=sprintf($patron2,$pulsado,$nodo,$idPrograma,$fila1[0]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al 12345335345343432465545743436645574546");
				}else{
					$patron="INSERT INTO campanas_programas_activos SET idnodo=\"%s\",idprograma=\"%s\",activo=\"%s\"";
					$sql=sprintf($patron,$nodo,$idPrograma,$pulsado);
					$respuesta=mysqli_query($con,$sql) or die ("Error al 12345335345343432465545743436645574546");
				}
				mysqli_free_result($respuesta1);
				
				$devolver=configuracionProgramasCampanas($nodo,$con);
			}
			echo $devolver;
		break;
        case 89:
            
            $devolver="n";
            
            $cantidad=intval(quitaComillasD($_POST["cantidad"]));
			$cliente=intval(quitaComillasD($_POST["cliente"]));            

			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $cantidad>0 && $cliente>0){
                
                $patron="SELECT id FROM safey_credenciales_pin WHERE idusuario=\"0\" AND borrado=\"n\" ORDER BY id LIMIT %s";
				$sql=sprintf($patron,$cantidad);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632103434001115725227879958");

				if(mysqli_num_rows($respuesta)>0 && $cantidad==mysqli_num_rows($respuesta)){
					for($i=0;$i<mysqli_num_rows($respuesta);$i++){
						$fila=mysqli_fetch_array($respuesta);
                        
                        $patron2="UPDATE safey_credenciales_pin SET idusuario=\"%s\" WHERE id=\"%s\"";
				        $sql2=sprintf($patron2,$cliente, $fila[0]);
				        $respuesta2=mysqli_query($con,$sql2) or die ("Error al 1234503475844575121775681534789679661345565746645574546");

					}
                $devolver=credencialesPinSafeyConfiguracion($con);
                }
                
			}
			echo $devolver;
        break;
        case 90:
            $devolver="n";
            
            $cantidad=intval(quitaComillasD($_POST["cantidad"]));
			$cliente=intval(quitaComillasD($_POST["cliente"]));            

			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $cantidad>0 && $cliente>0){
                
                $patron="SELECT id FROM safey_credenciales_llaves WHERE idusuario=\"0\" AND borrado=\"n\" ORDER BY id LIMIT %s";
				$sql=sprintf($patron,$cantidad);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96321034340011157215227879958");

				if(mysqli_num_rows($respuesta)>0 && $cantidad==mysqli_num_rows($respuesta)){
					for($i=0;$i<mysqli_num_rows($respuesta);$i++){
						$fila=mysqli_fetch_array($respuesta);
                        
                        $patron2="UPDATE safey_credenciales_llaves SET idusuario=\"%s\" WHERE id=\"%s\"";
				        $sql2=sprintf($patron2,$cliente, $fila[0]);
				        $respuesta2=mysqli_query($con,$sql2) or die ("Error al 123450347584457512177568153498746645574546");

					}
                $devolver=credencialesLlaveSafeyConfiguracion($con);
                }
                
			}
			echo $devolver;
        break;
		case 91:
			$n=quitaComillasD($_POST["n"]);
			if($getS=="s=27" && $n>0){
				$_SESSION["fechaIniHistorialProgramasCampanas"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialProgramasCampanas"]=quitaComillasD($_POST["fechaFin"]);
				
				echo programasCampanasHistorial($n,$con);
			}else{
				echo "n";
			}
		break;
		case 92:
			$devolver="n";
			$i=explode("=",$getI);

			$idNodo=quitaComillasD($_POST["idNodo"]);

			if($getS=="s=27" && $idNodo>0 && $i[1]==$idNodo){
				
				//crear, en blanco
				$patron="INSERT INTO campanas_luces SET idnodo=\"%s\",l=\"n\",m=\"n\",x=\"n\",j=\"n\",v=\"n\",s=\"n\",d=\"n\",horainicio=\"%s\",horafin=\"%s\",borrado=\"n\"";
				$sql=sprintf($patron,$idNodo,"00:00:00","23:59:59");
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 13346454545463765375763");

				$devolver=configuracionHorarioRelojCampanas($idNodo,$con);
			}
			echo $devolver;
		break;
		case 93:
			$devolver="n";
			$i=explode("=",$getI);

			$idNodo=quitaComillasD($_POST["idNodo"]);
			$idLin=quitaComillasD($_POST["idLin"]);
			
			$l=quitaComillasD($_POST["l"]);
			$m=quitaComillasD($_POST["m"]);
			$x=quitaComillasD($_POST["x"]);
			$j=quitaComillasD($_POST["j"]);
			$v=quitaComillasD($_POST["v"]);
			$s=quitaComillasD($_POST["s"]);
			$d=quitaComillasD($_POST["d"]);
			$horaIni=quitaComillasD($_POST["horaIni"]);
			$horaFin=quitaComillasD($_POST["horaFin"]);
			
			if($getS=="s=27" && $idNodo>0 && $i[1]==$idNodo && $idLin>0){
				
				//update
				$patron="UPDATE campanas_luces SET l=\"%s\",m=\"%s\",x=\"%s\",j=\"%s\",v=\"%s\",s=\"%s\",d=\"%s\",horainicio=\"%s\",horafin=\"%s\" WHERE id=\"%s\" AND idnodo=\"%s\" ";
				$sql=sprintf($patron,$l,$m,$x,$j,$v,$s,$d,$horaIni,$horaFin,$idLin,$idNodo);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 13346454545463733435565375763");
				
				$devolver=configuracionHorarioRelojCampanas($idNodo,$con);
			}
			echo $devolver;
		break;
		case 94:
			$devolver="n";
			$i=explode("=",$getI);

			$idNodo=quitaComillasD($_POST["idNodo"]);
			$idLin=quitaComillasD($_POST["idLin"]);
			
			if($getS=="s=27" && $idNodo>0 && $i[1]==$idNodo && $idLin>0){
				
				//update
				$patron="UPDATE campanas_luces SET borrado=\"s\" WHERE id=\"%s\" AND idnodo=\"%s\" ";
				$sql=sprintf($patron,$idLin,$idNodo);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 156533346454545463733435565375763");
				
				$devolver=configuracionHorarioRelojCampanas($idNodo,$con);
			}
			echo $devolver;
		break;
		case 95:
			$n=quitaComillasD($_POST["n"]);
			if($getS=="s=16" && $n>0){
				$_SESSION["fechaIniHistorialFallidoPuertasSafey"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialFallidoPuertasSafey"]=quitaComillasD($_POST["fechaFin"]);
                $_SESSION["puertaHistorialFallidoPuertasSafey"]=quitaComillasD($_POST["puerta"]);
				
				echo puertasSafeyHistorialFallidos($n,$con);
			}else{
				echo "n";
			}        
		break;
		case 96:
			if($_SESSION["permisossession"]==1 && $getS=="s=31"){
				$_SESSION["usuarioAutomatizacionProgramasList"]=$_POST["u"];
				echo cargaAutomatizacionProgramasList($con);
			}else{
				echo "n";
			}
		break;	
		case 97:
			$i=explode("=",$getI);
			$id=intval(quitaComillasD($_POST["id"]));
			if($id>0 && $getS=="s=32" && $i[1]==$id && ($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2)){
				$patron="UPDATE automatizacion_programa SET borrado=\"s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$id);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 1234434345455656504652345454556762005574546");
			}
		break;
		case 98:
			$devolver="n";
			$i=explode("=",$getI);

			$idPrograma=intval(quitaComillasD($_POST["idPrograma"]));
			$salida=intval(quitaComillasD($_POST["salida"]));

			if($getS=="s=32" && $idPrograma>0 && $i[1]==$idPrograma && $salida>0){
				
				//crear, en blanco
				$patron="INSERT INTO automatizacion_programa_salidas SET idprograma=\"%s\",l=\"n\",m=\"n\",x=\"n\",j=\"n\",v=\"n\",s=\"n\",d=\"n\",horainicio=\"%s\",horafin=\"%s\",salida=\"%s\",borrado=\"n\"";
				$sql=sprintf($patron,$idPrograma,"00:00:00","00:00:59",$salida);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 133464544545545463765375763");

				$devolver=configuracionSalidasAutomatizacion($idPrograma,$salida,$con);
			}
			echo $devolver;
		break;
		case 99:
			$devolver="n";
			$i=explode("=",$getI);

			$idPrograma=intval(quitaComillasD($_POST["idPrograma"]));
			$idLin=intval(quitaComillasD($_POST["idLin"]));
			$salida=intval(quitaComillasD($_POST["salida"]));
			
			if($getS=="s=32" && $idPrograma>0 && $i[1]==$idPrograma && $idLin>0 && $salida>0){
				
				//update
				$patron="UPDATE automatizacion_programa_salidas SET borrado=\"s\" WHERE id=\"%s\" AND idprograma=\"%s\" AND salida=\"%s\"";
				$sql=sprintf($patron,$idLin,$idPrograma,$salida);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 156533346454545463733435565375763");
				
				$devolver=configuracionSalidasAutomatizacion($idPrograma,$salida,$con);
			}
			echo $devolver;
		break;
		case 100:
			$devolver="n";
			$solapamiento="n";
			$textoProgramaDuplicado="n";
			$i=explode("=",$getI);

			$idPrograma=intval(quitaComillasD($_POST["idPrograma"]));
			$idLin=intval(quitaComillasD($_POST["idLin"]));
			$salida=intval(quitaComillasD($_POST["salida"]));
			
			$l=quitaComillasD($_POST["l"]);
			$m=quitaComillasD($_POST["m"]);
			$x=quitaComillasD($_POST["x"]);
			$j=quitaComillasD($_POST["j"]);
			$v=quitaComillasD($_POST["v"]);
			$s=quitaComillasD($_POST["s"]);
			$d=quitaComillasD($_POST["d"]);
			$horaIni=quitaComillasD($_POST["horaIni"]);
			$horaFin=quitaComillasD($_POST["horaFin"]);
			
			if($getS=="s=32" && $idPrograma>0 && $i[1]==$idPrograma && $idLin>0 && $salida>0){
				
				//comprobar, los 7 dias
				for($auxJ=1;$auxJ<=7;$auxJ++){//mirar para cada dia de la semana, si hay coincidencias de esa linea de esa salida
					$diaConsulta="";
					$verSiComprobarDia="n";
					switch($auxJ){
						case 1://lunes
							$diaConsulta="l";
							
							if($l=="s"){
								$verSiComprobarDia="s";
							}else{
								$verSiComprobarDia="n";
							}
							
						break;
						case 2://martes
							$diaConsulta="m";
							
							if($m=="s"){
								$verSiComprobarDia="s";
							}else{
								$verSiComprobarDia="n";
							}
						break;
						case 3://miercoles
							$diaConsulta="x";
							
							if($x=="s"){
								$verSiComprobarDia="s";
							}else{
								$verSiComprobarDia="n";
							}
						break;
						case 4://jueves
							$diaConsulta="j";
							
							if($j=="s"){
								$verSiComprobarDia="s";
							}else{
								$verSiComprobarDia="n";
							}
						break;
						case 5://viernes
							$diaConsulta="v";
							
							if($v=="s"){
								$verSiComprobarDia="s";
							}else{
								$verSiComprobarDia="n";
							}
						break;
						case 6://sabado
							$diaConsulta="s";
							
							if($s=="s"){
								$verSiComprobarDia="s";
							}else{
								$verSiComprobarDia="n";
							}
						break;
						case 7://domingo
							$diaConsulta="d";
							
							if($d=="s"){
								$verSiComprobarDia="s";
							}else{
								$verSiComprobarDia="n";
							}
						break;
					}
					
					if($verSiComprobarDia=="s" && $diaConsulta!=""){
						$consultaComprobar=" AND ".$diaConsulta."=\"s\" AND salida=\"".$salida."\" AND id<>".$idLin." AND (horainicio BETWEEN \"".$horaIni."\" AND \"".$horaFin."\" OR
						horafin BETWEEN \"".$horaIni."\" AND \"".$horaFin."\")
						";

						$patron1="SELECT id FROM automatizacion_programa_salidas WHERE idprograma=\"%s\" AND borrado=\"n\"%s";
						$sql1=sprintf($patron1,$idPrograma,$consultaComprobar);
						$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96323463565345899055899");
						if(mysqli_num_rows($respuesta1)>0){
							for($auxX=0;$auxX<mysqli_num_rows($respuesta1);$auxX++){
								$fila1=mysqli_fetch_array($respuesta1);
								//si encuentro uno no sigo buscando, rompo bucle
								
								$solapamiento="s";
								break;
							}
						}
						mysqli_free_result($respuesta1);
						
					}//cierro case
				}//cierro for
				
				//updatear
				if($solapamiento=="n"){
					
					//no dejar ser igual las dos horas
					$horaIniAux=explode(":",$horaIni);
					$horaFinAux=explode(":",$horaFin);
					if($horaIniAux[0].":".$horaIniAux[1].":".$horaIniAux[$fila2]==$horaFinAux[0].":".$horaFinAux[1].":".$horaFinAux[2]){
						if(intval($horaFinAux[2])<59){
							$horaFinAux[2]=intval($horaFinAux[2])+1;
							if($horaFinAux[2]<10){
								$horaFinAux[2]="0".$horaFinAux[2];
							}
						}
						$horaFin=$horaFinAux[0].":".$horaFinAux[1].":".$horaFinAux[2];
					}
					
					//update
					$patron="UPDATE automatizacion_programa_salidas SET l=\"%s\",m=\"%s\",x=\"%s\",j=\"%s\",v=\"%s\",s=\"%s\",d=\"%s\",horainicio=\"%s\",horafin=\"%s\" WHERE id=\"%s\" AND idprograma=\"%s\" AND salida=\"%s\" ";
					$sql=sprintf($patron,$l,$m,$x,$j,$v,$s,$d,$horaIni,$horaFin,$idLin,$idPrograma,$salida);
					$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 133463434454545463733435565375763");
				}
				
				/*START COMPROBAR SOLAPAMIENTO*/
				$programaDuplicado="n";
				
				$patron5="SELECT DISTINCT(idnodo) FROM automatizacion_programas_activos WHERE idprograma=\"%s\" AND activo=\"s\" ";
				$sql5=sprintf($patron5,$idPrograma);
				$respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 96323463563464234456743534589955055899");
				if(mysqli_num_rows($respuesta5)>0){
					for($auxQ=0;$auxQ<mysqli_num_rows($respuesta5);$auxQ++){
						$fila5=mysqli_fetch_array($respuesta5);
						
						$programaDuplicado=comprobarSolapamientoDeProgramas($fila5[0],$idPrograma,$con);
						if($programaDuplicado=="s"){
							// desactivar programa solapado relacionado con este nodo (misma salida diferente programa mismo nodo)
							
							$patron7="UPDATE automatizacion_programas_activos SET activo=\"n\" WHERE idnodo=\"%s\" AND idprograma=\"%s\" AND activo=\"s\"";
							$sql7=sprintf($patron7,$fila5[0],$idPrograma);
							$respuesta7=mysqli_query($con,$sql7)or die ("Error al buscar 2834759234666");
							
							$patron8="SELECT nombre FROM safey_nodos WHERE id=\"%s\" AND borrado=\"n\" AND guardado=\"s\"";
							$sql8=sprintf($patron8,$fila5[0]);
							$respuesta8=mysqli_query($con,$sql8)or die ("Error al buscar 3793457832834578");
							if(mysqli_num_rows($respuesta8)>0){
								$fila8=mysqli_fetch_array($respuesta8);
								$textoProgramaDuplicado="Se ha desactivado este programa en el nodo ".$fila8[0]." por solapamiento horario entre programas.";
							}
							mysqli_free_result($respuesta8);
							
						}	
					}
				}
				mysqli_free_result($respuesta5);
				/*END COMPROBAR SOLAPAMIENTO*/
				
				$devolver=configuracionSalidasAutomatizacion($idPrograma,$salida,$con);
			}
			echo $devolver;
			echo "@#";
			echo $solapamiento;
			echo "@#";
			echo $textoProgramaDuplicado;
		break;
		case 101:
			if($_SESSION["permisossession"]==1 && $getS=="s=33"){
				$_SESSION["usuarioAutomatizacionList"]=$_POST["u"];
				echo cargaNodosSafeyAutomatizacionList($con);
			}else{
				echo "n";
			}
		break;
		case 102:
			if($getS=="s=33"){
				$_SESSION["conexionAutomatizacionList"]=quitaComillasD($_POST["e"]);
				echo cargaNodosSafeyAutomatizacionList($con);
			}else{
				echo "n";
			}
		break;
		case 103:
			$devolver="n";
			$i=explode("=",$getI);

			$nodo=quitaComillasD($_POST["nodo"]);
			$idPrograma=quitaComillasD($_POST["idPrograma"]);
			$pulsado=quitaComillasD($_POST["pulsado"]);

			if($getS=="s=34" && $idPrograma>0 && $nodo>0 && $i[1]==$nodo){
				
				$programaDuplicado="n";
				if($pulsado=="s"){
					$programaDuplicado=comprobarSolapamientoDeProgramas($nodo,$idPrograma,$con);
				}
				
				if($programaDuplicado=="n"){
					$patron1="SELECT id FROM automatizacion_programas_activos WHERE idnodo=\"%s\" AND idprograma=\"%s\"";
					$sql1=sprintf($patron1,$nodo,$idPrograma);
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96324511111445456778234689004325227879958");
					if(mysqli_num_rows($respuesta1)>0){
						$fila1=mysqli_fetch_array($respuesta1);

						$patron2="UPDATE automatizacion_programas_activos SET activo=\"%s\" WHERE idnodo=\"%s\" AND idprograma=\"%s\" AND id=\"%s\"";
						$sql2=sprintf($patron2,$pulsado,$nodo,$idPrograma,$fila1[0]);
						$respuesta2=mysqli_query($con,$sql2) or die ("Error al 1234533534545434343246522545743436645574546");
					}else{
						$patron="INSERT INTO automatizacion_programas_activos SET idnodo=\"%s\",idprograma=\"%s\",activo=\"%s\"";
						$sql=sprintf($patron,$nodo,$idPrograma,$pulsado);
						$respuesta=mysqli_query($con,$sql) or die ("Error al 12345335345003454543432465543455743436645574546");
					}
					mysqli_free_result($respuesta1);
					
					
				}
				
				$devolver=configuracionProgramasAutomatizacion($nodo,$con);
				
			}
			echo $devolver;
			echo"#@";
			echo $programaDuplicado;
		break;	
		case 104:
			$n=quitaComillasD($_POST["n"]);
			if($getS=="s=34" && $n>0){
				$_SESSION["fechaIniHistorialSalidasAutomatizacion"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialSalidasAutomatizacion"]=quitaComillasD($_POST["fechaFin"]);
				
				echo historialAccionesAutomatizacion($n,$con);
			}else{
				echo "n";
			}
		break;
		case 105:
			if($getS=="s=34"){
				$_SESSION["fechaIniHistorialSalidasAutomatizacion"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialSalidasAutomatizacion"]=quitaComillasD($_POST["fechaFin"]);		
				$_SESSION["idNodoSalidasAutomatizacion"]=intval(quitaComillasD($_POST["idnodo"]));
			}
		break;
		case 106:
			$devolver="n";
			$i=explode("=",$getI);

			$nodo=intval(quitaComillasD($_POST["idNodo"]));
			//uno
			$salidaUno=quitaComillasD($_POST["salidaUno"]);
			$confAutoSUno=quitaComillasD($_POST["confAutoSUno"]);
			//dos
			$salidaDos=quitaComillasD($_POST["salidaDos"]);
			$confAutoSDos=quitaComillasD($_POST["confAutoSDos"]);
			//tres
			$salidaTres=quitaComillasD($_POST["salidaTres"]);
			$confAutoSTres=quitaComillasD($_POST["confAutoSTres"]);
			//cuatro
			$salidaCuatro=quitaComillasD($_POST["salidaCuatro"]);
			$confAutoSCuatro=quitaComillasD($_POST["confAutoSCuatro"]);
			//cinco
			$salidaCinco=quitaComillasD($_POST["salidaCinco"]);
			$confAutoSCinco=quitaComillasD($_POST["confAutoSCinco"]);
			//seis
			$salidaSeis=quitaComillasD($_POST["salidaSeis"]);
			$confAutoSSeis=quitaComillasD($_POST["confAutoSSeis"]);

			if($getS=="s=34" && $nodo>0 && $i[1]==$nodo && ($confAutoSUno=="on" || $confAutoSUno=="off") && ($confAutoSDos=="on" || $confAutoSDos=="off") && ($confAutoSTres=="on" || $confAutoSTres=="off") && ($confAutoSCuatro=="on" || $confAutoSCuatro=="off") && ($confAutoSCinco=="on" || $confAutoSCinco=="off") && ($confAutoSSeis=="on" || $confAutoSSeis=="off") ){
				
				$patron1="SELECT salidaunomodo, salidaunomanualactivado, salidadosmodo, salidadosmanualactivado, salidatresmodo, salidatresmanualactivado, salidacuatromodo, salidacuatromanualactivado, salidacincomodo, salidacincomanualactivado, salidaseismodo, salidaseismanualactivado FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
				$sql1=sprintf($patron1,$nodo);
				$respuesta1=mysqli_query($con,$sql1)or die ("Error al buscar 83290874726358472495328954");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					
					//SALIDA 1
					$consultaSalida="";
					
					if($fila1[0]!=$salidaUno || $fila1[1]!=$confAutoSUno){
						$consultaSalida=",salida=\"1\"";
						$entraSUno=false;
						if($fila1[0]!=$salidaUno){
							$entraSUno=true;
							$consultaSalida.=",modo='".$salidaUno."'";
						}
						
						if($fila1[1]!=$confAutoSUno){
							$consultaSalida.=",estado='".$confAutoSUno."'";
							if(!$entraSUno){
								$consultaSalida.=",modo='".$salidaUno."'";
							}
						}
						
						$patron2="INSERT INTO automatizacion_historial SET idnodo=\"%s\", idprograma=\"%s\",horaalta=\"%s\", fechaalta=\"%s\" %s";
						$sql2=sprintf($patron2,$nodo,0,date("H:i:s"),date("Y-m-d"),$consultaSalida);
						$respuesta2=mysqli_query($con,$sql2) or die("Error al insertar 85208435655729420127391652");
					}
					//SALIDA 2
					if($fila1[2]!=$salidaDos || $fila1[3]!=$confAutoSDos){
						$consultaSalida=",salida=\"2\"";
						$entraSDos=false;
						if($fila1[2]!=$salidaDos){
							$entraSDos=true;
							$consultaSalida.=",modo='".$salidaDos."'";
						}
						
						if($fila1[3]!=$confAutoSDos){
							$consultaSalida.=",estado='".$confAutoSDos."'";
							if(!$entraSDos){
								$consultaSalida.=",modo='".$salidaDos."'";
							}
						}
						$patron2="INSERT INTO automatizacion_historial SET idnodo=\"%s\", idprograma=\"%s\",horaalta=\"%s\", fechaalta=\"%s\" %s";
						$sql2=sprintf($patron2,$nodo,0,date("H:i:s"),date("Y-m-d"),$consultaSalida);
						$respuesta2=mysqli_query($con,$sql2) or die("Error al insertar 85208457294201275686577391652");
					}
					// SALIDA 3
					if($fila1[4]!=$salidaTres || $fila1[5]!=$confAutoSTres){
						$consultaSalida=",salida=\"3\"";
						$entraSTres=false;
						if($fila1[4]!=$salidaTres){
							$entraSTres=true;
							$consultaSalida.=",modo='".$salidaTres."'";
						}
						
						if($fila1[5]!=$confAutoSTres){
							$consultaSalida.=",estado='".$confAutoSTres."'";
							if(!$entraSTres){
								$consultaSalida.=",modo='".$salidaTres."'";
							}
							
						}
						$patron2="INSERT INTO automatizacion_historial SET idnodo=\"%s\", idprograma=\"%s\",horaalta=\"%s\", fechaalta=\"%s\" %s";
						$sql2=sprintf($patron2,$nodo,0,date("H:i:s"),date("Y-m-d"),$consultaSalida);
						$respuesta2=mysqli_query($con,$sql2) or die("Error al insertar 8520845555545729420127391652");
					}
					//SALIDA 4
					if($fila1[6]!=$salidaCuatro || $fila1[7]!=$confAutoSCuatro){
						$consultaSalida=",salida=\"4\"";
						$entraSCuatro=false;
						if($fila1[6]!=$salidaCuatro){
							$entraSCuatro=true;
							$consultaSalida.=",modo='".$salidaCuatro."'";
						}
						
						if($fila1[7]!=$confAutoSCuatro){
							$consultaSalida.=",estado='".$confAutoSCuatro."'";
							if(!$entraSCuatro){
								$consultaSalida.=",modo='".$salidaCuatro."'";
							}
						}
						$patron2="INSERT INTO automatizacion_historial SET idnodo=\"%s\", idprograma=\"%s\",horaalta=\"%s\", fechaalta=\"%s\" %s";
						$sql2=sprintf($patron2,$nodo,0,date("H:i:s"),date("Y-m-d"),$consultaSalida);
						$respuesta2=mysqli_query($con,$sql2) or die("Error al insertar 85208223423445729420127391652");
					}
					//SALIDA 5
					if($fila1[8]!=$salidaCinco || $fila1[9]!=$confAutoSCinco){
						$consultaSalida=",salida=\"5\"";
						$entraSCinco=false;
						if($fila1[8]!=$salidaCinco){
							$entraSCinco=true;
							$consultaSalida.=",modo='".$salidaCinco."'";
						}
						
						if($fila1[9]!=$confAutoSCinco){
							$consultaSalida.=",estado='".$confAutoSCinco."'";
							if(!$entraSCinco){
								$consultaSalida.=",modo='".$salidaCinco."'";
							}
						}
						$patron2="INSERT INTO automatizacion_historial SET idnodo=\"%s\", idprograma=\"%s\",horaalta=\"%s\", fechaalta=\"%s\" %s";
						$sql2=sprintf($patron2,$nodo,0,date("H:i:s"),date("Y-m-d"),$consultaSalida);
						$respuesta2=mysqli_query($con,$sql2) or die("Error al insertar 852084572674567649420127391652");
					}
					//SALIDA 6
					if($fila1[10]!=$salidaSeis || $fila1[11]!=$confAutoSSeis){
						$consultaSalida=",salida=\"6\"";
						$entraSSeis=false;
						if($fila1[10]!=$salidaSeis){
							$entraSSeis=true;
							$consultaSalida.=",modo='".$salidaSeis."'";
						}
						
						if($fila1[11]!=$confAutoSSeis){
							$consultaSalida.=",estado='".$confAutoSSeis."'";
							if(!$entraSSeis){
								$consultaSalida.=",modo='".$salidaSeis."'";
							}
						}
						
						$patron2="INSERT INTO automatizacion_historial SET idnodo=\"%s\", idprograma=\"%s\",horaalta=\"%s\", fechaalta=\"%s\" %s";
						$sql2=sprintf($patron2,$nodo,0,date("H:i:s"),date("Y-m-d"),$consultaSalida);
						$respuesta2=mysqli_query($con,$sql2) or die("Error al insertar 852084572927543457420127391652");
					}
				}
				mysqli_free_result($respuesta1);
				
				$patron="UPDATE safey_nodos SET salidaunomodo=\"%s\",salidaunomanualactivado=\"%s\",salidadosmodo=\"%s\",salidadosmanualactivado=\"%s\", 
				salidatresmodo=\"%s\",salidatresmanualactivado=\"%s\",
				salidacuatromodo=\"%s\",salidacuatromanualactivado=\"%s\",
				salidacincomodo=\"%s\",salidacincomanualactivado=\"%s\",
				salidaseismodo=\"%s\",salidaseismanualactivado=\"%s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$salidaUno,$confAutoSUno, $salidaDos,$confAutoSDos,$salidaTres,$confAutoSTres,$salidaCuatro,$confAutoSCuatro,$salidaCinco,$confAutoSCinco,$salidaSeis,$confAutoSSeis,$nodo);
				$respuesta=mysqli_query($con,$sql) or die ("Error al 123453353454534343434324659095574546");
				
				$devolver="s";
			}
			echo $devolver;
		break;
        case 107:
			if($getS=="s=36"){
				$_SESSION["conexionAudioList"]=quitaComillasD($_POST["e"]);
				echo cargaNodosAudioList($con);
			}else{
				echo "n";
			}
		break;
        case 108:
            if($_SESSION["permisossession"]==1 && $getS=="s=36"){
				$_SESSION["usuarioAudioList"]=$_POST["u"];
				echo cargaNodosAudioList($con);
			}else{
				echo "n";
			}    
        break;
        case 109:
			$i=explode("=",$getI);
			$id=intval(quitaComillasD($_POST["id"]));
			if($id>0 && $getS=="s=37" && $i[1]==$id && $_SESSION["permisossession"]==1){
				$patron="UPDATE audio_nodos SET borrado=\"s\" WHERE id=\"%s\"";
				$sql=sprintf($patron,$id);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 123445450465232454545544575676746645574546");
			}
		break;
        case 110:
			//ACTIVAR PARADA DE EMERGENCIA AUDIO
            //$_POST["id"];
            echo "s";
		break;
        case 111:
            if($_SESSION["permisossession"]==1 && $getS=="s=38"){
				$_SESSION["usuarioVideovigilanciaList"]=$_POST["u"];
				echo "s";//echo cargaNodosVideoVigilanciaList($con);
			}else{
				echo "n";
			}
		break;
        case 112:
            if($_SESSION["permisossession"]==1 && $getS=="s=38"){
				$_SESSION["usuarioVideovigilanciaList"]=$_POST["u"];
				
				$idusuario=$_POST["u"];
				$url=quitaComillasD($_POST["url"]);
				
				if($idusuario>0){
					$patron="INSERT INTO videovigilancia_nodos SET idusuario=\"%s\",url=\"%s\",nombre=\"camara\",guardado=\"s\",borrado=\"n\",fechaalta=\"%s\"";
					$sql=sprintf($patron,$idusuario,$url,date("Y-m-d"));
					$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 123445450465232454545544575676746645574546");
					
					echo "s";//echo cargaNodosVideoVigilanciaList($con);
				}
			}else{
				echo "n";
			}
		break;
        case 113:
            if($_SESSION["permisossession"]==1 && $getS=="s=38"){
				$idCam=intval(quitaComillasD($_POST["idCam"]));
				
				if($idCam>0){
					$patron="UPDATE videovigilancia_nodos SET borrado=\"s\" WHERE id=\"%s\"";
					$sql=sprintf($patron,$idCam);
					$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 1234454504652324545475544575676746645574546");
					
					echo "s";//echo cargaNodosVideoVigilanciaList($con);
				}
			}else{
				echo "n";
			}
		break;
        case 114:
            if( ($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2) && $getS=="s=38"){
				
				$consulta="";
				if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioVideovigilanciaList"]!="0"*/){
					if($_SESSION["usuarioVideovigilanciaList"]!="0"){
						$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioVideovigilanciaList"])."\"";
					}
				}else{
					$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
				}

				//solo en este caso para no mostrar nada
				if($consulta==""){
					$consulta.=" AND idusuario=\"-99\"";
				}
				
				$listadoUlr="";
				$patron="SELECT id,url FROM videovigilancia_nodos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre ";
				$sql=sprintf($patron,$consulta);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963223234559004236554546754578009857879958");
				if(mysqli_num_rows($respuesta)>0){
					for($i=0;$i<mysqli_num_rows($respuesta);$i++){
						$fila=mysqli_fetch_array($respuesta);
						
						$listadoUlr.=$fila[1]."&t=".time();
						
						if($i<mysqli_num_rows($respuesta)-1){
							$listadoUlr.="@#";
						}
					}
				}
				mysqli_free_result($respuesta);
				
				echo $listadoUlr;
			}else{
				echo "n";
			}
		break;
		case 115:
			if($getS=="s=22"){
				$_SESSION["fechaIniHistorialPuertasPistasPadel"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialPuertasPistasPadel"]=quitaComillasD($_POST["fechaFin"]);		
				$_SESSION["idNodoPuertaPistaPadel"]=intval(quitaComillasD($_POST["idnodo"]));
			}
		break;
        case 116:
            if($getS=="s=16"){
                $_SESSION["fechaIniHistorialFallidoPuertas"]=quitaComillasD($_POST["fechaIni"]);
                $_SESSION["fechaFinHistorialFallidoPuertas"]=quitaComillasD($_POST["fechaFin"]);
                $_SESSION["puertaHistorialFallidoSafey"]=quitaComillasD($_POST["puerta"]);		
                $_SESSION["idNodo"]=intval(quitaComillasD($_POST["idnodo"]));
            }
		break;	
		case 117:
			$devolver="n";
			if($_SESSION["permisossession"]==1 && $getS=="s=19" && $_POST["cantidadMandos"]>0){
				
                $cantidadMandosCrear=$_POST["cantidadMandos"];
				$cantidadMandosCreados=0;
				//descomentar una vez montado almacen mandos generico
				/*$patron="SELECT id,descripcion,llaveserie,llavepinserial,tipo,frecuencia,color FROM almacen_credenciales_mandos WHERE borrado=\"n\"";
				$sql=sprintf($patron);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96321034340011112433825227879958");
				if(mysqli_num_rows($respuesta)>0){
					for($i=0;$i<mysqli_num_rows($respuesta);$i++){
						$fila=mysqli_fetch_array($respuesta);
						
						if($fila[2]!="" && $fila[3]!=""){
							
							$patron1="SELECT id FROM safey_credenciales_mandos WHERE idllavealmacen=\"%s\"";
							$sql1=sprintf($patron1,$fila[0]);
							$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9632111114444524784561111227879958");
							if(mysqli_num_rows($respuesta1)>0){
								//$fila1=mysqli_fetch_array($respuesta1);
							}else{
								$patron2="INSERT INTO safey_credenciales_mandos SET mandoserie=\"%s\",mandoserial=\"%s\",idusuario=0,idacceso=0fechaalta=\"%s\"";
								$sql2=sprintf($patron2,$fila[2],$fila[3],date("Y-m-d"));
								$respuesta2=mysqli_query($con,$sql2) or die ("Error al 12345335345342465645545222645574546");

								$cantidadMandosCreados+=1;
							}
							mysqli_free_result($respuesta1);
						}
						//romper bucle si ya tenemos la cantidad que queremos
						if($cantidadMandosCrear<=$cantidadMandosCreados){
							break;
						}
					}
				}
				mysqli_free_result($respuesta);*/
				
				echo credencialesMandoSafeyConfiguracion($con);
				echo "@#";
				echo $cantidadMandosCreados;
			}else{
				echo "n";
			}
		break;
		case 118:
            if($getS=="s=17"){
                $_SESSION["usuarioSelecCredencialesSafey"]=quitaComillasD($_POST["usuario"]);
            }
		break;
		case 119:
			$devolver="n";
			$i=explode("=",$getI);
				
			$idPin=intval(quitaComillasD($_POST["idPin"]));
			$idAcceso=intval(quitaComillasD($_POST["idAcceso"]));
			if($_SESSION["permisossession"]==1 && $getS=="s=18" && $idPin>0 && $i[1]==$idAcceso){
				
				//pin safey
				$patron1="SELECT id,idpinalmacen,pinserie,idacceso FROM safey_credenciales_pin WHERE borrado=\"n\" AND id=\"%s\" ";
				$sql1=sprintf($patron1,$idPin);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 1121233111212356541154646989607");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);

					//pin almacen general
					$patron2="SELECT id FROM almacen_credenciales_pin WHERE borrado=\"n\" AND id=\"%s\" AND pinserie=\"%s\"";
					$sql2=sprintf($patron2,$fila1[1],$fila1[2]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 112123333322132423454646989607");
					if(mysqli_num_rows($respuesta2)>0){
						$fila2=mysqli_fetch_array($respuesta2);

						//borrar de safey
						$patron3="UPDATE safey_credenciales_pin SET borrado=\"s\",idusuario=0 WHERE id=\"%s\"";
						$sql3=sprintf($patron3,$fila1[0]);
						$respuesta3=mysqli_query($con,$sql3) or die ("Error al borrar 123456347893333");
						$idPin=0;
						
						//update el acceso ese pin
						$patron3="UPDATE safey_accesos SET pin=\"0\",pinactivo=\"off\" WHERE id=\"%s\"";
						$sql3=sprintf($patron3,$fila1[3]);
						$respuesta3=mysqli_query($con,$sql3) or die ("Error al borrar 123456347893333");
						
						//borrar de almacen general
						/*
						$patron4="UPDATE almacen_credenciales_pin SET borrado=\"s\" WHERE id=\"%s\"";
						$sql4=sprintf($patron4,$fila2[0]);
						//$respuesta4=mysqli_query($con,$sql4) or die ("Error al borrar 1234563478933322223");
						*/
					}else{
						//borrar de safey, no viene de almacen
						$patron3="UPDATE safey_credenciales_pin SET borrado=\"s\" WHERE id=\"%s\"";
						$sql3=sprintf($patron3,$id);
						$respuesta3=mysqli_query($con,$sql3) or die ("Error al borrar 1234563474535435893333");
					}
					mysqli_free_result($respuesta2);
				
				}
				mysqli_free_result($respuesta1);
				
				$devolver=cargaPinClientesSafey($idPin,"pin",$idAcceso,$con);
			}
			echo $devolver;
		break;
		case 120:
			$n=quitaComillasD($_POST["n"]);
			if($getS=="s=18" && $n>0){
				$_SESSION["fechaIniHistorialPagos"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialPagos"]=quitaComillasD($_POST["fechaFin"]);
				
				echo pagosSafeyHistorial($n,$con);
			}else{
				echo "n";
			}
		break;	
		case 121:
			$i=explode("=",$getI);
				
			$nodo=intval(quitaComillasD($_POST["nodo"]));
			$cliente=intval(quitaComillasD($_POST["cliente"]));
			$metodoPago=intval(quitaComillasD($_POST["metodoPago"]));
			
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2) && $getS=="s=16" && $i[1]==$nodo && $cliente>0 && $metodoPago>0){
				
				//comprobar que no se haya añadido ya ese metodo de pago
				$comprobarMetodosPago=true;
				
				$patron2="SELECT metodopago FROM safey_metodospago WHERE idnodo=\"%s\" AND idusuario=\"%s\" AND metodopago=\"%s\" AND borrado=\"n\"";
				$sql2=sprintf($patron2,$nodo,$cliente,$metodoPago);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error 3097134565234378465698234984840035545453245454");
				if(mysqli_num_rows($respuesta2)>0){
					for($j=0;$j<mysqli_num_rows($respuesta2);$j++){
						$fila2=mysqli_fetch_array($respuesta2);
						if($fila2[0]>0){
							$comprobarMetodosPago=false;
						}
					}
				}
				mysqli_free_result($respuesta2);
				
				//Insertar si no se ha añadido ese metodo de pago antes
				if($comprobarMetodosPago){
					$patron="INSERT INTO safey_metodospago SET idnodo=\"%s\",idusuario=\"%s\",metodopago=\"%s\",borrado=\"n\"";
					$sql=sprintf($patron,$nodo,$cliente,$metodoPago);
					$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 192657341234563474535435809313567");
					
					echo configuracionMetodosPagosPuertasAccesos($nodo,$cliente,$con);
					echo "@#";
					echo cargaTiposMetodosPago($nodo,"metodopago","",$con);
					
				}else{
					echo "n";	
				}
				
			}else{
				echo "n";	
			}
		break;	
		case 122:
			$devolver="n";
			$i=explode("=",$getI);
			$id=intval(quitaComillasD($_POST["id"]));
			$idNodo=intval(quitaComillasD($_POST["idNodo"]));
			$metodoPago=intval(quitaComillasD($_POST["metodoPago"]));
			
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2) && $getS=="s=16" && $i[1]==$idNodo && $metodoPago>0){
				//buscar usuario
				$patron1="SELECT idusuario FROM safey_metodospago WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
				$sql1=sprintf($patron1,$id,$idNodo);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 125378563453567234230934345454");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					
					//update
					$patron="UPDATE safey_metodospago SET metodopago=\"%s\" WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
					$sql=sprintf($patron,$metodoPago,$id,$idNodo);
					$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 192657341234563474535435809313567");
					
					$devolver=configuracionMetodosPagosPuertasAccesos($idNodo,$fila1[0],$con);
				}
				mysqli_free_result($respuesta1);
			}
			echo $devolver;
		break;		
		case 123:
			$devolver="n";
			$i=explode("=",$getI);
			$id=intval(quitaComillasD($_POST["id"]));
			$idNodo=intval(quitaComillasD($_POST["idNodo"]));
			
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2) && $getS=="s=16" && $i[1]==$idNodo){
				//buscar usuario
				$patron1="SELECT idusuario FROM safey_metodospago WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
				$sql1=sprintf($patron1,$id,$idNodo);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 125378563453567234230934345454");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					
					//borrado
					$patron="UPDATE safey_metodospago SET borrado=\"s\" WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
					$sql=sprintf($patron,$id,$idNodo);
					$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 192657341234563474535435809313567");
					
					echo configuracionMetodosPagosPuertasAccesos($idNodo,$fila1[0],$con);
					echo "@#";
					echo cargaTiposMetodosPago($idNodo,"metodopago","",$con);
				}
				mysqli_free_result($respuesta1);
			}else{
				echo "n";	
			}
		break;		
		case 124:
			$devolver="n";
			$i=explode("=",$getI);
				
			$nodo=intval(quitaComillasD($_POST["nodo"]));
			$cliente=intval(quitaComillasD($_POST["cliente"]));
			$codigoPromocional=quitaComillasD($_POST["codigoPromocional"]);
			$tipo=quitaComillasD($_POST["tipo"]);
			$cantidad=floatval(quitaComillasD($_POST["cantidad"]));
			
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2) && $getS=="s=16" && $i[1]==$nodo && $cliente>0 && $codigoPromocional!="" && (($tipo!="e" && $cantidad>0.0) || ($tipo!="p" && $cantidad<=99.0))){
				
				//comprobar que no existe un codigo igual
				$comprobarCodPromo=true;
				$patron2="SELECT codigo FROM safey_codigospromocionales WHERE idnodo=\"%s\" AND idusuario=\"%s\" AND borrado=\"n\"";
				$sql2=sprintf($patron2,$nodo,$cliente);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 29763453567265345904912309343453256402140");
				if(mysqli_num_rows($respuesta2)>0){
					for($i=0;$i<mysqli_num_rows($respuesta2);$i++){
						$fila2=mysqli_fetch_array($respuesta2);
						if($codigoPromocional==$fila2[0]){
							$comprobarCodPromo=false;
						}
					}
				}
				mysqli_free_result($respuesta2);
				
				if($comprobarCodPromo){
					//insert
					$patron1="INSERT INTO safey_codigospromocionales SET idnodo=\"%s\",idusuario=\"%s\",codigo=\"%s\",tipo=\"%s\",cantidad=\"%s\",borrado=\"n\"";
					$sql1=sprintf($patron1,$nodo,$cliente,$codigoPromocional,$tipo,$cantidad);
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al insertar 3201297341234563474535964435809313567");

					$devolver=configuracionCodigosPromoPuertasAccesos($nodo,$cliente,$con);	
				}
			}
			echo $devolver;
		break;	
		case 125:
			$devolver="n";
			$i=explode("=",$getI);
			$id=intval(quitaComillasD($_POST["id"]));
			$idNodo=intval(quitaComillasD($_POST["idNodo"]));
			$codigoPromocional=quitaComillasD($_POST["codigoPromocional"]);
			$tipo=quitaComillasD($_POST["tipo"]);
			$cantidad=floatval(quitaComillasD($_POST["cantidad"]));
			
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2) && $getS=="s=16" && $i[1]==$idNodo && $codigoPromocional!="" && (($tipo=="e" && $cantidad>0.0) || ($tipo=="p" && $cantidad<=99.0))){
				//buscar usuario
				$patron1="SELECT idusuario FROM safey_codigospromocionales WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
				$sql1=sprintf($patron1,$id,$idNodo);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 2312537856345356723459872309343454533400214");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					
					//comprobar que no existe un codigo igual
					$comprobarCodPromo=true;
					$patron2="SELECT codigo FROM safey_codigospromocionales WHERE id!=\"%s\" AND idnodo=\"%s\" AND idusuario=\"%s\" AND borrado=\"n\"";
					$sql2=sprintf($patron2,$id,$idNodo,$fila1[0]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 29763453567265345904912309343453256402140");
					if(mysqli_num_rows($respuesta2)>0){
						for($i=0;$i<mysqli_num_rows($respuesta2);$i++){
							$fila2=mysqli_fetch_array($respuesta2);
							if($codigoPromocional==$fila2[0]){
								$comprobarCodPromo=false;
							}
						}
					}
					mysqli_free_result($respuesta2);

					if($comprobarCodPromo){
						//update
						$patron="UPDATE safey_codigospromocionales SET codigo=\"%s\",tipo=\"%s\",cantidad=\"%s\" WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
						$sql=sprintf($patron,$codigoPromocional,$tipo,$cantidad,$id,$idNodo);
						$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 900841926573239867634745354351213567");

						$devolver=configuracionCodigosPromoPuertasAccesos($idNodo,$fila1[0],$con);
					}
				}
				mysqli_free_result($respuesta1);
			}
			echo $devolver;
		break;		
		case 126:
			$devolver="n";
			$i=explode("=",$getI);
			$id=intval(quitaComillasD($_POST["id"]));
			$idNodo=intval(quitaComillasD($_POST["idNodo"]));
			
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2) && $getS=="s=16" && $i[1]==$idNodo){
				//buscar usuario
				$patron1="SELECT idusuario FROM safey_codigospromocionales WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
				$sql1=sprintf($patron1,$id,$idNodo);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 1982537855767865356723420876441154");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					
					//borrado
					$patron="UPDATE safey_codigospromocionales SET borrado=\"s\" WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
					$sql=sprintf($patron,$id,$idNodo);
					$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 43219265734123456347453543580935");
					
					$devolver=configuracionCodigosPromoPuertasAccesos($idNodo,$fila1[0],$con);
				}
				mysqli_free_result($respuesta1);
			}
			echo $devolver;
		break;	
		case 127:
			$devolver="n";
			$i=explode("=",$getI);
				
			$idAcceso=intval(quitaComillasD($_POST["idAcceso"]));
			$idNodo=intval(quitaComillasD($_POST["idNodo"]));
			$servicio=quitaComillasD($_POST["servicio"]);
			$codPromo=quitaComillasD($_POST["codPromo"]);
			$metodoPago=floatval(quitaComillasD($_POST["metodoPago"]));
			$fInicio=quitaComillasD($_POST["fInicio"]);
			$fFin=quitaComillasD($_POST["fFin"]);
			//$fPago=quitaComillasD($_POST["fPago"]);
			
			$totalPrecio=0.0;
			$descuento=0.0;
			$tipoCodPromo="";
			
			if(($_SESSION["permisossession"]==1) && $getS=="s=18" && $i[1]==$idAcceso && $idNodo>0 && $servicio!="" && $metodoPago>0 && $fInicio!="" && $fFin!=""){
				
				//consultar usuario de acceso
				$idUsuario=0;
				$patron3="SELECT idusuario FROM safey_accesos WHERE id=\"%s\" AND borrado=\"n\" AND guardado=\"s\"";
				$sql3=sprintf($patron3,$idAcceso);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 29763453565904912309343453402140");
				if(mysqli_num_rows($respuesta3)>0){
					$fila3=mysqli_fetch_array($respuesta3);
					$idUsuario=$fila3[0];
				}
				mysqli_free_result($respuesta3);
				
				//consulta configuracion economica del nodo
                $tipoReserva=0;
				$patron4="SELECT precio,tiporeserva FROM safey_nodos_configuracioneconomica WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
				$sql4=sprintf($patron4,$servicio,$idNodo);
				$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 872976345567265345904909343453256402140");
				if(mysqli_num_rows($respuesta4)>0){
					$fila4=mysqli_fetch_array($respuesta4);
					$totalPrecio=$fila4[0];
                    $tipoReserva=$fila4[1];
				}
				mysqli_free_result($respuesta4);
				
				//comprobar que el codigo sea correcto
				$idCodPromo=0;
				$patron2="SELECT id,codigo,tipo,cantidad FROM safey_codigospromocionales WHERE codigo=\"%s\" AND idnodo=\"%s\" AND idusuario=\"%s\" AND borrado=\"n\"";
				$sql2=sprintf($patron2,$codPromo,$idNodo,$idUsuario);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 28997634535672653459049123092354343453256");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$idCodPromo=$fila2[0];
					switch($fila2[2]){
						case "e"://descuento
							$descuento=$fila2[3];
							$totalPrecio=$totalPrecio-$descuento;
						break;
						case "p"://porcentaje
							$descuento=($fila2[3] / 100) * $totalPrecio;
							$totalPrecio=$totalPrecio-$$descuento;
						break;
					}
				}
				mysqli_free_result($respuesta2);

				if($idAcceso>0){
					$patron1="INSERT INTO safey_pagos SET idnodo=\"%s\",idusuario=\"%s\",idacceso=\"%s\",tiposervicio=\"%s\",codigopromocional=\"%s\",descuento=\"%s\",total=\"%s\",metodopago=\"%s\",fechainicio=\"%s\",fechafin=\"%s\",borrado=\"n\",tiporeserva=\"%s\",fechaalta=\"%s\"";
					$sql1=sprintf($patron1,$idNodo,$idUsuario,$idAcceso,$servicio,$idCodPromo,$descuento,$totalPrecio,$metodoPago,$fInicio,$fFin,$tipoReserva,date("Y-m-d"));
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al insertar 3201297341234563474535964435809313567");

					$devolver=pagosSafeyHistorial($idAcceso,$con);	
				}
			}
			echo $devolver;
		break;	
		case 128:
			$devolver="n";
			$i=explode("=",$getI);
			$idAcceso=intval(quitaComillasD($_POST["idAcceso"]));
			$idPago=intval(quitaComillasD($_POST["idPago"]));
			
			if(($_SESSION["permisossession"]==1) && $getS=="s=18" && $i[1]==$idAcceso && $idPago>0){
				//buscar usuario
				$patron1="SELECT idusuario FROM safey_accesos WHERE id=\"%s\" AND borrado=\"n\" AND guardado=\"s\"";
				$sql1=sprintf($patron1,$idAcceso);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 978541982537855767865356723420876441154");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					
					//borrado
					$patron="UPDATE safey_pagos SET borrado=\"s\" WHERE id=\"%s\" AND idusuario=\"%s\" AND borrado=\"n\" AND idacceso=\"%s\"";
					$sql=sprintf($patron,$idPago,$fila1[0],$idAcceso);
					$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 656743219265734123456347453543580935");
					
					$devolver=pagosSafeyHistorial($idAcceso,$con);	
				}
				mysqli_free_result($respuesta1);
			}
			echo $devolver;
		break;
		case 129:
			$devolver="n";
			$i=explode("=",$getI);
				
			$idAcceso=intval(quitaComillasD($_POST["idAcceso"]));
			$idPago=intval(quitaComillasD($_POST["idPago"]));
			$idNodo=intval(quitaComillasD($_POST["idNodo"]));
			$idSservicio=intval(quitaComillasD($_POST["servicio"]));
			$codPromo=quitaComillasD($_POST["codPromo"]);
			$metodoPago=floatval(quitaComillasD($_POST["metodoPago"]));
			$fInicio=quitaComillasD($_POST["fInicio"]);
			$fFin=quitaComillasD($_POST["fFin"]);
			//$fRealPago=quitaComillasD($_POST["fRealPago"]);
			
			
			$total=0.0;
			$descuento=0.0;
			$tipoCodPromo="";
			
			if(($_SESSION["permisossession"]==1) && $getS=="s=18" && $i[1]==$idAcceso && $idPago>0 && $idNodo>0 && $idSservicio!="" && $metodoPago>0 && $fInicio!="" && $fFin!=""){
				
				//consultar usuario de acceso
				$idUsuario=0;
				$patron3="SELECT idusuario FROM safey_accesos WHERE id=\"%s\" AND borrado=\"n\" AND guardado=\"s\"";
				$sql3=sprintf($patron3,$idAcceso);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 29763453565904912309343453402140");
				if(mysqli_num_rows($respuesta3)>0){
					$fila3=mysqli_fetch_array($respuesta3);
					$idUsuario=$fila3[0];
				}
				mysqli_free_result($respuesta3);
				
				//consulta nodo
				$patron4="SELECT precio,tiporeserva FROM safey_nodos_configuracioneconomica WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
				$sql4=sprintf($patron4,$idSservicio,$idNodo);
				$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 872976345567265345904909343453256402140");
				if(mysqli_num_rows($respuesta4)>0){
					$fila4=mysqli_fetch_array($respuesta4);
					$total=$fila4[0];
				}
				mysqli_free_result($respuesta4);
				
				//comprobar que el codigo sea correcto
				$idCodPromo=0;
				$patron2="SELECT id,codigo,tipo,cantidad FROM safey_codigospromocionales WHERE codigo=\"%s\" AND idnodo=\"%s\" AND idusuario=\"%s\" AND borrado=\"n\"";
				$sql2=sprintf($patron2,$codPromo,$idNodo,$idUsuario);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 28997634535672653459049123092354343453256");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$idCodPromo=$fila2[0];
					switch($fila2[2]){
						case "e"://descuento
							$descuento=$fila2[3];
							$total=$total-$descuento;
						break;
						case "p"://porcentaje
							$descuento=($fila2[3] / 100) * $total;
							$total=$total-$$descuento;
						break;
					}
				}
				mysqli_free_result($respuesta2);

				if($idUsuario>0){
					$patron1="UPDATE safey_pagos SET tiposervicio=\"%s\",codigopromocional=\"%s\",descuento=\"%s\",total=\"%s\",metodopago=\"%s\",fechainicio=\"%s\",fechafin=\"%s\" WHERE id=\"%s\" AND idnodo=\"%s\" AND idusuario=\"%s\" AND borrado=\"n\" AND idacceso=\"%s\"";
					$sql1=sprintf($patron1,$idSservicio,$idCodPromo,$descuento,$total,$metodoPago,$fInicio,$fFin,$idPago,$idNodo,$idUsuario,$idAcceso);
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al insertar 3201297341234563474535964435809313567");

					$devolver=pagosSafeyHistorial($idAcceso,$con);	
				}
			}
			echo $devolver;
		break;	
		case 130:
			$n=quitaComillasD($_POST["n"]);
			if($getS=="s=37" && $n>0){
				$_SESSION["fechaIniHistorialProgramasAudio"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialProgramasAudio"]=quitaComillasD($_POST["fechaFin"]);
				
				echo audiosColasHistorial($n,$con);
			}else{
				echo "n";
			}
		break;
		case 131:
			$devolver="n";
			
			$id=intval(quitaComillasD($_POST["id"]));
			$idUsuario=intval(quitaComillasD($_POST["usuario"]));
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==3 || ($_SESSION["permisossession"]==2 && $_SESSION["idusersession"]==$idUsuario) ) && $getS=="s=39" && $id>0){
				
				//audio
				$patron="SELECT id,idusuario,url FROM audio_ficheroaudio WHERE borrado=\"n\" AND id=\"%s\" AND idusuario=\"%s\"";
				$sql=sprintf($patron,$id,$idUsuario);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1121233113451212356541154646989607");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);

					//borrar
					$patron3="UPDATE audio_ficheroaudio SET borrado=\"s\" WHERE id=\"%s\"";
					$sql3=sprintf($patron3,$fila[0]);
					$respuesta3=mysqli_query($con,$sql3) or die ("Error al borrar 123456343534574535435893333");
					
					$archivo=$fila[2];
					$directorio = "./archivos_subidos/clientes/".$fila[1]."/audios/";
					
					/*start borrar, falta login ftp ftp*/
					//unlink($directorio.$archivo);
					/*end borrar, falta login ftp ftp*/
					
				}
				mysqli_free_result($respuesta);
				
				$devolver=audiosSubidosList($con);
			}
			echo $devolver;
		break;
		case 132:
			$correcto="n";
			
			$idUsuario=intval(quitaComillasD($_POST["idUsuario"]));
			$nombreAudio=quitaComillasD($_POST["nombreAudio"]);
			//$ficheroAudio=$_FILES['file_upload'];
			$textoAudio=quitaComillasD($_POST["textoAudio"]);
			$seccion=intval(quitaComillasD($_POST["seccion"]));
			
			$tamanoMaximoFichero=209715200;//tamano 200mb = 209715200.09 bytes
			
			if($idUsuario>0 && $nombreAudio!="" && ($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==3 || ($_SESSION["permisossession"]==2  && $_SESSION["idusersession"]==$idUsuario)) && ($seccion==1 || $seccion==2) ){
				
				/*start crear carpeta si no existe*/
				if(is_dir("modular/archivos_subidos/clientes/".$idUsuario)){
					if(is_dir("modular/archivos_subidos/clientes/".$idUsuario."/audios")){
					}else{
						mkdir("./archivos_subidos/clientes/".$idUsuario."/audios",0777);
					}
				}else{
					mkdir("./archivos_subidos/clientes/".$idUsuario,0777);
					mkdir("./archivos_subidos/clientes/".$idUsuario."/audios",0777);
				}
				/*end crear carpeta si no existe*/
				
				if($textoAudio!="" && $seccion==2){//CONVERTIR TEXTO A AUDIO .mp3
					
					/*start gestion carpetas*/
					$patron="SELECT id FROM audio_ficheroaudio WHERE idusuario=\"%s\"";
					$sql=sprintf($patron,$idUsuario);
					$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1892356565121235656607");
					if(mysqli_num_rows($respuesta)>0){
						//$fila=mysqli_fetch_array($respuesta);
					}else{
						//creardir("audios","/panel.modularbox.com/archivos_subidos/clientes/".$idUsuario);
						mkdir("./archivos_subidos/clientes/".$idUsuario."/audios",0777);
					}
					mysqli_free_result($respuesta);
					/*end gestion carpetas*/
					$rutaGuardar="/".$idUsuario."/audios";
					$ficheroAudio=convertirTextoAMp3($textoAudio,$rutaGuardar,$con);//convertir audio y guardar
					
					if($ficheroAudio!="n"){
						$auxNombreFicheroAudio=explode("/audios/",$ficheroAudio);//para quitar la parte de la ruta obtener solo nombre
						
						$nombreFicheroAudio=$auxNombreFicheroAudio[1];
						//crear en bbdd
						$patron7="INSERT INTO audio_ficheroaudio SET url=\"%s\", nombre=\"%s\", idusuario=\"%s\", fechaalta=\"%s\"";
						$sql7=sprintf($patron7,$nombreFicheroAudio,$nombreAudio,$idUsuario,date("Y-m-d"));
						$respuesta7=mysqli_query($con,$sql7) or die ("Error 567345323456354423444578899055677");

						$correcto="s";//ok
					}
					
				}else if($seccion==1){//SUBIDOR FICHERO DE AUDIO
				
					if(isset($_FILES["file_upload"]) && $_FILES["file_upload"]["tmp_name"]!="" && $_FILES["file_upload"]["size"]>0 && $_FILES["file_upload"]["size"]<=$tamanoMaximoFichero){

						$fileTypes = array('mp3','MP3','wav','WAV','aac','AAC','ogg','OGG','webm','WEBM');//extensiones validas

						/*if($nombreAudio==""){
							//$nombreAudio="NuevoAudio_".rand(1, 1000);
						}*/		

						$servidor_ftp = FTP;
						$conexion_id = ftp_connect($servidor_ftp);
						$ftp_usuario = USER_FTP;
						$ftp_clave = PASS_FTP;
						$directorio = "/panel.modularbox.com/archivos_subidos/clientes/".$idUsuario."/audios/";
						$resultado_login = ftp_login($conexion_id,$ftp_usuario,$ftp_clave);
						ftp_pasv($conexion_id,TRUE);

						if((!$conexion_id) || (!$resultado_login)){
							$correcto="l";//mal login ftp -->l
						}else{

							$horaSubida=date("Ymd_His");
							$numAleatorio=generaCodigo(5,1);
							$documento=str_replace(' ', '',$numAleatorio."_".$horaSubida."_".quitaComillasD($_FILES['file_upload']['name']));

							$fileParts = pathinfo($_FILES['file_upload']['name']);

							if(in_array($fileParts['extension'],$fileTypes)){

								/*start gestion carpetas*/
								$patron="SELECT id FROM audio_ficheroaudio WHERE idusuario=\"%s\"";
								$sql=sprintf($patron,$idUsuario);
								$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 18921234565121235656607");
								if(mysqli_num_rows($respuesta)>0){
									//$fila=mysqli_fetch_array($respuesta);
								}else{
									//creardir("audios","/panel.modularbox.com/archivos_subidos/clientes/".$idUsuario);
									mkdir("./archivos_subidos/clientes/".$idUsuario."/audios",0777);
								}
								mysqli_free_result($respuesta);
								/*end gestion carpetas*/

								$upload = ftp_put($conexion_id,$directorio.$documento,$_FILES['file_upload']['tmp_name'], FTP_BINARY);

								if(!$upload){
									$formato=false;
								}else{
									//crear en bbdd
									$patron7="INSERT INTO audio_ficheroaudio SET url=\"%s\", nombre=\"%s\", idusuario=\"%s\", fechaalta=\"%s\"";
									$sql7=sprintf($patron7,$documento,$nombreAudio,$idUsuario,date("Y-m-d"));
									$respuesta7=mysqli_query($con,$sql7) or die ("Error 567345323456354478899055677");

									$correcto="s";//ok
							   }
							}else{
								$correcto="e";//mal la extension ->e
							}
						}
					}//cierro if subidor fichero
				}//cierro else
			}
			echo $correcto;
			echo "@#";
			echo audiosSubidosList($con);
		break;
		case 133:
			$correcto="n";
			
			$idAudio=intval(quitaComillasD($_POST["idAudio"]));
			$idUsuario=intval(quitaComillasD($_POST["idUsuario"]));
			$horaIniAudio=substr(quitaComillasD($_POST["horaIniAudio"]),0,2);
			$minIniAudio=substr(quitaComillasD($_POST["minIniAudio"]),0,2);
			$fechaReproducirAudio=quitaComillasD($_POST["fechaReproducirAudio"]);
			
			$opcionEnviar=intval(quitaComillasD($_POST["opcion"]));
			
			$auxNodos=$_POST["nodos"];
			if($auxNodos!=""){
				$arrayNodos=explode(",",$auxNodos);
			}else{
				$arrayNodos=array();
			}
			
			$numReproducciones=intval(quitaComillasD($_POST["numReproducciones"]));
			
			/*start tipo de envio programado, o enviar ahora*/
			if($opcionEnviar==2){//audio programado
				$horaReproducir=$horaIniAudio.":".$minIniAudio;//concatenar, para guardar bbdd
			}else if($opcionEnviar==1){//enviar ahora
				$fechaReproducirAudio=date("Y-m-d");
				$horaIniAudio=date("H");
				$minIniAudio=date("i");
				
				if($minIniAudio<59){
					$minIniAudio=$minIniAudio+1;
					
					if($minIniAudio<10){//concatenar el cero
						$minIniAudio="0".$minIniAudio;
					}
					
				}else{
					$horaIniAudio=$horaIniAudio+1;
					$minIniAudio=0;
				}
				$horaReproducir=$horaIniAudio.":".$minIniAudio;//concatenar, para guardar bbdd
			}
			/*end tipo de envio programado, o enviar ahora*/
			
			$audioSuperiorHoraActual=false;
			if($fechaReproducirAudio==date("Y-m-d")){
				if($horaReproducir<=date("H:i")){//la hora es inferior a la actual
					$audioSuperiorHoraActual=false;
				}else{
					$audioSuperiorHoraActual=true;
				}
			}else{
				$audioSuperiorHoraActual=true;
			}
			
			if($audioSuperiorHoraActual && $idUsuario>0 && count($arrayNodos)>0 && $idAudio>0 && $fechaReproducirAudio!="" && $horaIniAudio!="" && $horaIniAudio<=23 && $minIniAudio!="" && $minIniAudio<=59 && ($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==3 || ($_SESSION["permisossession"]==2 && $_SESSION["idusersession"]==$idUsuario) ) && ($opcionEnviar==1 || $opcionEnviar==2) && $numReproducciones>=1){
				
				if($numReproducciones<1){
					$numReproducciones=1;
				}
				
				//recorrer para cada nodo
				if(count($arrayNodos)>0){//no necesario lo compruebo en di de arriba
					
					if(intval($arrayNodos[0])==-99 && $idUsuario>0){//si es el -99 es reproducir en todos
						$arrayNodos=array();//para que si hay otros id no hacerlo dos veces
						$patron1="SELECT id FROM audio_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND idusuario=\"%s\"";
						$sql1=sprintf($patron1,$idUsuario);
						$respuesta1=mysqli_query($con,$sql1) or die ("Error 31121523512145445589912112115");
						if(mysqli_num_rows($respuesta1)>0){
							for($i=0;$i<mysqli_num_rows($respuesta1);$i++){
								$fila1=mysqli_fetch_array($respuesta1);
								array_push($arrayNodos,$fila1[0]);
							}
						}
						mysqli_free_result($respuesta1);
					}
					
					//crear audio en los nodos indicados
					$contadorBucleNodos=0;
					$idPrimerInsertAccionRelacionada=0;//guardar el primero para vincular la accion de todos los nodos marcados
					foreach ($arrayNodos as $idNodoRecorrido) {
						
						$idInsertCreado=0;
						$contadorBucleNodos++;
						
						if($idNodoRecorrido>0){
							//guardar la accion para ese nodo
							$patron="INSERT INTO audio_colashistorial SET idnodo=\"%s\", idaudio=\"%s\", numeroreproducciones=\"%s\", fechareproducir=\"%s\", horareproducir=\"%s\", reproducido=\"n\",idenviadopor=\"%s\", borrado=\"n\", fechaalta=\"%s\"";
							$sql=sprintf($patron,$idNodoRecorrido,$idAudio,$numReproducciones,$fechaReproducirAudio,$horaReproducir,$_SESSION["idusersession"],date("Y-m-d"));
							$respuesta=mysqli_query($con,$sql) or die ("Error 5673453257578055677");
							$idInsertCreado=mysqli_insert_id($con);
							
							//solo coger el id del primer insert para asi relacionar todas las acciones, es decir, misma peticion para varios nodos
							if($contadorBucleNodos==1){
								$idPrimerInsertAccionRelacionada=$idInsertCreado;
							}
							
							//update, de los vinculados, es decir, reproducir un audio en conjunto para x nodos
							if($idInsertCreado>0){
								$patron2="UPDATE audio_colashistorial SET idaccionrelacionada=\"%s\" WHERE idnodo=\"%s\" AND idaudio=\"%s\" AND id=\"%s\"";
								$sql2=sprintf($patron2,$idPrimerInsertAccionRelacionada,$idNodoRecorrido,$idAudio,$idInsertCreado);
								$respuesta2=mysqli_query($con,$sql2) or die ("Error 567345325757805535477677");
							}
						}
					}
				}

				$correcto="s";
			}
			echo $correcto;
		break;
        case 134:
            if($_SESSION["permisossession"]==1 && $getS=="s=39"){
				$_SESSION["usuarioAudioSubirList"]=$_POST["u"];
				echo audiosSubidosList($con);
			}else{
				echo "n";
			}
        break;
		case 135:
			$devolver="n";
			$id=intval(quitaComillasD($_POST["lin"]));
			$nodo=intval(quitaComillasD($_POST["n"]));
			$seccion=intval(quitaComillasD($_POST["seccion"]));
			
			if($id>0 && $nodo>0 && ($seccion==1 || $seccion==2) && ($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3) && ($getS=="s=37" || $getS=="s=40")){
				
				
				/*start obtener el idAccionRelacionada*/
				$idAccionRelacionada=0;
				$patron229="SELECT idaccionrelacionada FROM audio_colashistorial WHERE id=\"%s\" AND idnodo=\"%s\"";
				$sql229=sprintf($patron229,$id,$nodo);
				$respuesta229=mysqli_query($con,$sql229) or die ("Error al buscar 9635325343453457556655313366762234653509258");
				if(mysqli_num_rows($respuesta229)>0){
					$fila229=mysqli_fetch_array($respuesta229);
					
					$idAccionRelacionada=$fila229[0];
				}
				mysqli_free_result($respuesta229);
				/*END obtener el idAccionRelacionada*/
				
				//borrar
				if($idAccionRelacionada>0){
					$patron3="UPDATE audio_colashistorial SET borrado=\"s\" WHERE idaccionrelacionada=\"%s\"";
					$sql3=sprintf($patron3,$idAccionRelacionada);
					$respuesta3=mysqli_query($con,$sql3) or die ("Error al borrar 123456342343534574535435893333");
					
				}else if($idAccionRelacionada==0){//borrar el actual, para antes del cambio, luego sin uso
					/*$patron3="UPDATE audio_colashistorial SET borrado=\"s\" WHERE id=\"%s\" AND idnodo=\"%s\"";
					$sql3=sprintf($patron3,$id,$nodo);
					$respuesta3=mysqli_query($con,$sql3) or die ("Error al borrar 123456342343534574535435893333");*/
				}
				
				if($seccion==1){
					$devolver=audiosColasHistorial($nodo,$con);
				}else if($seccion==2){
					$devolver=cargaColasReproduccionAudiosList($con);
				}
			}
			echo $devolver;
		break;
		case 136:
			$devolver="n";
			$idUsuario=intval(quitaComillasD($_POST["usu"]));
			if($idUsuario>0 && ($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3) && $getS=="s=39"){
				$devolver=cargaNodosAudiosCliente($idUsuario,$con);
			}
			//gestion hora
			$hora=0;
			if(date("H")<23){
				$hora=intval(date("H"))/*+1*/;
			}
			//gestion minutos
			$minutos=0;
			if(date("i")<=57){
				$minutos=intval(date("i"))+2;
			}else if(date("i")==58){
				$minutos=intval(date("i"))+1;
			}else if(date("i")>58){
				$minutos=intval(0);
				$hora=intval(date("H"))+1;
			}
			
			echo $devolver;
			echo "@#";
			echo $hora;
			echo "@#";
			echo $minutos;
		break;
        case 137:
            if($_SESSION["permisossession"]==1 && $getS=="s=40"){
				$_SESSION["usuarioAudioList"]=$_POST["u"];
				echo cargaColasReproduccionAudiosList($con);
			}else{
				echo "n";
			}    
        break;
		case 138:
			$correcto="n";
			
			$idUsuario=intval(quitaComillasD($_POST["idUsuario"]));
			$nombreAudio=quitaComillasD($_POST["nombreAudio"]);
			//$ficheroAudio=$_FILES['file_upload'];
			//$textoAudio=quitaComillasD($_POST["textoAudio"]);
			
			$tamanoMaximoFichero=209715200;//tamano 200mb = 209715200.09 bytes
            
            
			if($idUsuario>0 && $nombreAudio!="" && ($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==3 || ($_SESSION["permisossession"]==2 && $_SESSION["idusersession"]==$idUsuario) )){
				
				if(isset($_FILES["file_upload"]) && $_FILES["file_upload"]["tmp_name"]!="" && $_FILES["file_upload"]["size"]>0 && $_FILES["file_upload"]["size"]<=$tamanoMaximoFichero){

					$fileTypes = array('mp3','MP3','wav','WAV','aac','AAC','webm', 'WEBM','ogg','OGG');//extensiones validas

					/*if($nombreAudio==""){
						//$nombreAudio="NuevoAudio_".rand(1, 1000);
					}*/		

					/*start crear carpeta si no existe*/
					if(is_dir("./archivos_subidos/clientes/".$idUsuario)){
						if(is_dir("./archivos_subidos/clientes/".$idUsuario."/audios")){
						}else{
							mkdir("./archivos_subidos/clientes/".$idUsuario."/audios",0777);
						}
					}else{
						mkdir("./archivos_subidos/clientes/".$idUsuario,0777);
						mkdir("./archivos_subidos/clientes/".$idUsuario."/audios",0777);
					}
					/*end crear carpeta si no existe*/
					
					$servidor_ftp = FTP;
					$conexion_id = ftp_connect($servidor_ftp);
					$ftp_usuario = USER_FTP;
					$ftp_clave = PASS_FTP;
					$directorio = "/panel.modularbox.com/archivos_subidos/clientes/".$idUsuario."/audios/";
					$resultado_login = ftp_login($conexion_id,$ftp_usuario,$ftp_clave);
					ftp_pasv($conexion_id,TRUE);

					if((!$conexion_id) || (!$resultado_login)){
						$correcto="l";//mal login ftp -->l
					}else{

						$horaSubida=date("Ymd_His");
						$numAleatorio=generaCodigo(5,1);
						$documento=str_replace(' ', '', $numAleatorio."_".$horaSubida."_".quitaComillasD(str_replace('.', '',$_FILES['file_upload']['name']).".mp3"));

						$fileParts = pathinfo($_FILES['file_upload']['name']);

						if(/*in_array($fileParts['extension'],$fileTypes) || */true){

							/*start gestion carpetas*/
							$patron="SELECT id FROM audio_ficheroaudio WHERE idusuario=\"%s\"";
							$sql=sprintf($patron,$idUsuario);
							$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 18921234565121235656607");
							if(mysqli_num_rows($respuesta)>0){
								//$fila=mysqli_fetch_array($respuesta);
							}else{
								//creardir("audios","/panel.modularbox.com/archivos_subidos/clientes/".$idUsuario);
								mkdir("./archivos_subidos/clientes/".$idUsuario."/audios",0777);
							}
							mysqli_free_result($respuesta);
							/*end gestion carpetas*/

							$upload = ftp_put($conexion_id,$directorio.$documento,$_FILES['file_upload']['tmp_name'], FTP_BINARY);

							if(!$upload){
								$formato=false;
							}else{
								//crear en bbdd
								$patron7="INSERT INTO audio_ficheroaudio SET url=\"%s\", nombre=\"%s\", idusuario=\"%s\", fechaalta=\"%s\"";
								$sql7=sprintf($patron7,$documento,$nombreAudio,$idUsuario,date("Y-m-d"));
								$respuesta7=mysqli_query($con,$sql7) or die ("Error 567345323456354478899055677");

								$correcto="s";//ok
						   }
						}else{
							$correcto="e";//mal la extension ->e
						}
					}
				}//cierro if subidor fichero
			}
			echo $correcto;
			echo "@#";
			echo audiosSubidosList($con);
		break;
        case 139:
			$devolver="n";
			
			$idUsuario=intval(quitaComillasD($_POST["idCli"]));
			$idLinAudio=intval(quitaComillasD($_POST["idLin"]));
			$nombreAudio=quitaComillasD($_POST["nombreAudio"]);
			
            if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3) && $idUsuario>0 && $idLinAudio>0 && $nombreAudio!="" && $getS=="s=39"){
				
				//update del nombre del audio
				$patron="UPDATE audio_ficheroaudio SET nombre=\"%s\" WHERE id=\"%s\" AND idusuario=\"%s\" AND borrado=\"n\"";
				$sql=sprintf($patron,$nombreAudio,$idLinAudio,$idUsuario);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 12345634789");
				
				
				$devolver=audiosSubidosList($con);
			}
			
			echo $devolver;
        break;	
        case 140:
            if($_SESSION["permisossession"]==1 && $getS=="s=41"){
				$_SESSION["usuarioAudioList"]=$_POST["u"];
				$_SESSION["nodoHistorialGenAudioList"]=0;//restablecer por si es otro cliente
				echo cargaHistorialGeneralAudios($con);
			}else{
				echo "n";
			}    
        break;
		case 141:
			if($getS=="s=41"){
				$_SESSION["fechaIniHistorialGeneralAudio"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialGeneralAudio"]=quitaComillasD($_POST["fechaFin"]);
				$_SESSION["nodoHistorialGenAudioList"]=intval(quitaComillasD($_POST["nodo"]));
				
				echo cargaHistorialGeneralAudios($con);
			}else{
				echo "n";
			}
		break;
		case 142:
			$devolver="n";
			$i=explode("=",$getI);

			$referencia=quitaComillasD($_POST["ref"]);
			$canales=/*10*/intval(quitaComillasD($_POST["numCanales"]));
			$marca=""/*quitaComillasD($_POST["marca"])*/;
			$modelo=""/*quitaComillasD($_POST["modelo"])*/;
			if($getS=="s=42" && ($canales>=3 && $canales<=14) && $referencia!="" && $_SESSION["permisossession"]==1){
				
				//insert
				$patron="INSERT INTO luces_referenciafocos SET referencia=\"%s\",canales=\"%s\",marca=\"%s\",modelo=\"%s\",borrado=\"n\",direcciondmxrojo=0,direcciondmxverde=0,direcciondmxazul=0,direcciondmxblancocalido=0,direcciondmxblancofrio=0,direcciondmxstrobe=0,direcciondmxsped=0,direcciondmxdimer=0,direcciondmxfun=0,direcciondmxuv=0 ";
				$sql=sprintf($patron,$referencia,$canales,$marca,$modelo);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 13346635675967676234235653457375763");
				
				$devolver=configuracionProgramaLucesGenerico($con);
			}
			echo $devolver;
		break;
		case 143:
			$devolver="n";
			$i=explode("=",$getI);

			$idLin=intval(quitaComillasD($_POST["idTipoFoco"]));
			$referencia=quitaComillasD($_POST["ref"]);
			$marca=quitaComillasD($_POST["marca"]);
			$modelo=quitaComillasD($_POST["modelo"]);
			$canales=/*10*/intval(quitaComillasD($_POST["numCanales"]));

			if($getS=="s=42" && $idLin>0 && ($canales>=3 && $canales<=14) && $referencia!="" && $_SESSION["permisossession"]==1){
				
				//update
				$patron="UPDATE luces_referenciafocos SET referencia=\"%s\",canales=\"%s\",marca=\"%s\",modelo=\"%s\" WHERE id=\"%s\" AND borrado=\"n\"";
				$sql=sprintf($patron,$referencia,$canales,$marca,$modelo,$idLin);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 133466376234235653457375763");
				
				$devolver=configuracionProgramaLucesGenerico($con);
			}
			echo $devolver;
		break;
		case 144:
			$devolver="n";
			$i=explode("=",$getI);

			$idLin=intval(quitaComillasD($_POST["idLin"]));
			
			if($getS=="s=42" && $idLin>0 && $_SESSION["permisossession"]==1){
				
				//update
				$patron="UPDATE luces_referenciafocos SET borrado=\"s\" WHERE id=\"%s\" AND borrado=\"n\"";
				$sql=sprintf($patron,$idLin);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 139034565676234235653457375763");
				
				$devolver=configuracionProgramaLucesGenerico($con);
			}
			echo $devolver;
		break;
		case 145:
			$devolver="n";
			$i=explode("=",$getI);

			$idLin=intval(quitaComillasD($_POST["idLin"]));

			
			/*tipo funcionalidades, creadas en bbdd, 10 por ahora
			--> direcciondmxrojo
			--> direcciondmxverde
			--> direcciondmxazul
			--> direcciondmxblancocalido
			--> direcciondmxblancofrio
			--> direcciondmxstrobe
			--> direcciondmxsped
			--> direcciondmxdimer
			--> direcciondmxfun
			--> direcciondmxuv
			*/
			/*variables de la modal*/
			$direccionDmxRojo="";//contenido, desplegable
			$direccionDmxVerde="";//contenido, desplegable
			$direccionDmxAzul="";//contenido, desplegable
			$direccionDmxBlancocalido="";//contenido, desplegable
			$direccionDmxBlancofrio="";//contenido, desplegable
			$direccionDmxStrobe="";//contenido, desplegable
			$direccionDmxSped="";//contenido, desplegable
			$direccionDmxDimer="";//contenido, desplegable
			$direccionDmxFun="";//contenido, desplegable
			$direccionDmxUv="";//contenido, desplegable
			
			if($getS=="s=42" && $idLin>0){
				
                $idLin=$idLin;//para el valor del hidden de la modal
				
				/*start obtener el valor (direccion dmx) de cada fucionalidad*/
				$valorDireccionDmxRojo=0;
				$valorDireccionDmxVerde=0;
				$valorDireccionDmxAzul=0;
				$valorDireccionDmxBlancocalido=0;
				$valorDireccionDmxBlancofrio=0;
				$valorDireccionDmxStrobe=0;
				$valorDireccionDmxSped=0;
				$valorDireccionDmxDimer=0;
				$valorDireccionDmxFun=0;
				$valorDireccionDmxUv=0;
				$patron="SELECT direcciondmxrojo,direcciondmxverde,direcciondmxazul,direcciondmxblancocalido,direcciondmxblancofrio,direcciondmxstrobe,direcciondmxsped,direcciondmxdimer,direcciondmxfun,direcciondmxuv FROM luces_referenciafocos WHERE borrado=\"n\" AND id=\"%s\"";
				$sql=sprintf($patron,$idLin);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632346686574567456557890899");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);

					$valorDireccionDmxRojo=intval($fila[0]);
					$valorDireccionDmxVerde=intval($fila[1]);
					$valorDireccionDmxAzul=intval($fila[2]);
					$valorDireccionDmxBlancocalido=intval($fila[3]);
					$valorDireccionDmxBlancofrio=intval($fila[4]);
					$valorDireccionDmxStrobe=intval($fila[5]);
					$valorDireccionDmxSped=intval($fila[6]);
					$valorDireccionDmxDimer=intval($fila[7]);
					$valorDireccionDmxFun=intval($fila[8]);
					$valorDireccionDmxUv=intval($fila[9]);
				}
				mysqli_free_result($respuesta);
				/*end obtener el valor (direccion dmx) de cada fucionalidad*/
				
				$direccionDmxRojo=cargaDireccionesDmxTipoFocos($idLin,"direccionDmxRojo",1,$valorDireccionDmxRojo,$con);
				$direccionDmxVerde=cargaDireccionesDmxTipoFocos($idLin,"direccionDmxVerde",2,$valorDireccionDmxVerde,$con);
				$direccionDmxAzul=cargaDireccionesDmxTipoFocos($idLin,"direccionDmxAzul",3,$valorDireccionDmxAzul,$con);
				$direccionDmxBlancocalido=cargaDireccionesDmxTipoFocos($idLin,"direccionDmxBlancocalido",4,$valorDireccionDmxBlancocalido,$con);
				$direccionDmxBlancofrio=cargaDireccionesDmxTipoFocos($idLin,"direccionDmxBlancofrio",5,$valorDireccionDmxBlancofrio,$con);
				$direccionDmxStrobe=cargaDireccionesDmxTipoFocos($idLin,"direccionDmxStrobe",6,$valorDireccionDmxStrobe,$con);
				$direccionDmxSped=cargaDireccionesDmxTipoFocos($idLin,"direccionDmxSped",7,$valorDireccionDmxSped,$con);
				$direccionDmxDimer=cargaDireccionesDmxTipoFocos($idLin,"direccionDmxDimer",8,$valorDireccionDmxDimer,$con);
				$direccionDmxFun=cargaDireccionesDmxTipoFocos($idLin,"direccionDmxFun",9,$valorDireccionDmxFun,$con);
				$direccionDmxUv=cargaDireccionesDmxTipoFocos($idLin,"direccionDmxRojo",10,$valorDireccionDmxUv,$con);
				
				include("luces_modal_configurarfococanales.html");	
			}
		break;
		case 146:
			$devolver="n";
			$i=explode("=",$getI);

			$idTipoFoco=intval(quitaComillasD($_POST["idTipoFoco"]));
			$idFuncionalidadDmx=intval(quitaComillasD($_POST["idFuncionalidad"]));
			$direccionDmx=intval(quitaComillasD($_POST["direccionDmx"]));
			
			/*variables de la modal*/
			$direccionDmxRojo="";//contenido, desplegable//1
			$direccionDmxVerde="";//contenido, desplegable//2
			$direccionDmxAzul="";//contenido, desplegable//3
			$direccionDmxBlancocalido="";//contenido, desplegable//4
			$direccionDmxBlancofrio="";//contenido, desplegable//5
			$direccionDmxStrobe="";//contenido, desplegable//6
			$direccionDmxSped="";//contenido, desplegable//7
			$direccionDmxDimer="";//contenido, desplegable//8
			$direccionDmxFun="";//contenido, desplegable//9
			$direccionDmxUv="";//contenido, desplegable//10
			
			if($getS=="s=42" && $idTipoFoco>0 && ($direccionDmx>=1 && $direccionDmx<=10 || $direccionDmx==-99) && ($idFuncionalidadDmx>0 ) ){
				
				guardarDireccionDmxFuncionalidad($idTipoFoco,$idFuncionalidadDmx,$direccionDmx,$con);//actualizar y comprobar
				
				/*start obtener el valor (direccion dmx) de cada fucionalidad*/
				$valorDireccionDmxRojo=0;
				$valorDireccionDmxVerde=0;
				$valorDireccionDmxAzul=0;
				$valorDireccionDmxBlancocalido=0;
				$valorDireccionDmxBlancofrio=0;
				$valorDireccionDmxStrobe=0;
				$valorDireccionDmxSped=0;
				$valorDireccionDmxDimer=0;
				$valorDireccionDmxFun=0;
				$valorDireccionDmxUv=0;
				$patron="SELECT direcciondmxrojo,direcciondmxverde,direcciondmxazul,direcciondmxblancocalido,direcciondmxblancofrio,direcciondmxstrobe,direcciondmxsped,direcciondmxdimer,direcciondmxfun,direcciondmxuv FROM luces_referenciafocos WHERE borrado=\"n\" AND id=\"%s\"";
				$sql=sprintf($patron,$idTipoFoco);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632675346683456574567456557890899");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);

					$valorDireccionDmxRojo=intval($fila[0]);//1
					$valorDireccionDmxVerde=intval($fila[1]);//2
					$valorDireccionDmxAzul=intval($fila[2]);//3
					$valorDireccionDmxBlancocalido=intval($fila[3]);//4
					$valorDireccionDmxBlancofrio=intval($fila[4]);//5
					$valorDireccionDmxStrobe=intval($fila[5]);//6
					$valorDireccionDmxSped=intval($fila[6]);//7
					$valorDireccionDmxDimer=intval($fila[7]);//8
					$valorDireccionDmxFun=intval($fila[8]);//9
					$valorDireccionDmxUv=intval($fila[9]);//10
				}
				mysqli_free_result($respuesta);
				/*end obtener el valor (direccion dmx) de cada fucionalidad*/
				
				//pintar desplegables
				$direccionDmxRojo=cargaDireccionesDmxTipoFocos($idTipoFoco,"direccionDmxRojo",1,$valorDireccionDmxRojo,$con);
				$direccionDmxVerde=cargaDireccionesDmxTipoFocos($idTipoFoco,"direccionDmxVerde",2,$valorDireccionDmxVerde,$con);
				$direccionDmxAzul=cargaDireccionesDmxTipoFocos($idTipoFoco,"direccionDmxAzul",3,$valorDireccionDmxAzul,$con);
				$direccionDmxBlancocalido=cargaDireccionesDmxTipoFocos($idTipoFoco,"direccionDmxBlancocalido",4,$valorDireccionDmxBlancocalido,$con);
				$direccionDmxBlancofrio=cargaDireccionesDmxTipoFocos($idTipoFoco,"direccionDmxBlancofrio",5,$valorDireccionDmxBlancofrio,$con);
				$direccionDmxStrobe=cargaDireccionesDmxTipoFocos($idTipoFoco,"direccionDmxStrobe",6,$valorDireccionDmxStrobe,$con);
				$direccionDmxSped=cargaDireccionesDmxTipoFocos($idTipoFoco,"direccionDmxSped",7,$valorDireccionDmxSped,$con);
				$direccionDmxDimer=cargaDireccionesDmxTipoFocos($idTipoFoco,"direccionDmxDimer",8,$valorDireccionDmxDimer,$con);
				$direccionDmxFun=cargaDireccionesDmxTipoFocos($idTipoFoco,"direccionDmxFun",9,$valorDireccionDmxFun,$con);
				$direccionDmxUv=cargaDireccionesDmxTipoFocos($idTipoFoco,"direccionDmxUv",10,$valorDireccionDmxUv,$con);
				
				include("luces_modal_configurarfococanales.html");
			}
		break;	
		case 147:
			$devolver="n";
			$i=explode("=",$getI);

			$idTipoFoco=intval(quitaComillasD($_POST["idFoco"]));
			
			if($getS=="s=12" && $idTipoFoco>0){
				
				$canales=0;
				$vacios=10;
				
				$patron="SELECT canales FROM luces_referenciafocos WHERE borrado=\"n\" AND id=\"%s\"";
				$sql=sprintf($patron,$idTipoFoco);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963223434445456574567456557890899");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					
					$canales=$fila[0];
				}
				mysqli_free_result($respuesta);
				
				/*guardar al seleccionar???, por ahora no*/
				
				echo $canales;
				echo "@#";
				echo $vacios=10-$canales;//10 diez canales por defecto
			}else{
				echo "n";
			}
		break;	
		case  148:
			$devolver="n";
			$i=explode("=",$getI);

			$idNodo=intval(quitaComillasD($_POST["idNodo"]));
			$modo=intval(quitaComillasD($_POST["modo"]));
			
			if($getS=="s=9" && $idNodo>0 && $modo>0 && $modo<3){
				//update
				$patron="UPDATE luces_nodos SET modo=\"%s\" WHERE id=\"%s\" AND borrado=\"n\"";
				$sql=sprintf($patron,$modo,$idNodo);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 133466376234235653457375763");
				
				$devolver="s";
			}
			echo $devolver;
		break;
		case 149:
			$devolver="n";
			$i=explode("=",$getI);

			$nombre=quitaComillasD($_POST["nombre"]);
			if($getS=="s=43" && $nombre!="" && $_SESSION["permisossession"]==1){
				
				//insert
				$patron="INSERT INTO luces_tiposprogramaspredefinidos SET nombre=\"%s\",borrado=\"n\" ";
				$sql=sprintf($patron,$nombre);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 133466356759676762342356534556787375763");
				
				$devolver=configuracionTipoProgramaLucesGenerico($con);
			}
			echo $devolver;
		break;
		case 150:
			$devolver="n";
			$i=explode("=",$getI);

			$idLin=intval(quitaComillasD($_POST["idLin"]));
			$nombre=quitaComillasD($_POST["nombre"]);

			if($getS=="s=43" && $idLin>0 && $nombre!="" && $_SESSION["permisossession"]==1){
				
				//update
				$patron="UPDATE luces_tiposprogramaspredefinidos SET nombre=\"%s\" WHERE id=\"%s\" AND borrado=\"n\"";
				$sql=sprintf($patron,$nombre,$idLin);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1334663762343453452356534573757763");
				
				$devolver=configuracionTipoProgramaLucesGenerico($con);
			}
			echo $devolver;
		break;
		case 151:
			$devolver="n";
			$i=explode("=",$getI);

			$idLin=intval(quitaComillasD($_POST["idLin"]));
			
			if($getS=="s=43" && $idLin>0 && $_SESSION["permisossession"]==1){
				
				//update
				$patron="UPDATE luces_tiposprogramaspredefinidos SET borrado=\"s\" WHERE id=\"%s\" AND borrado=\"n\"";
				$sql=sprintf($patron,$idLin);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 13903456534535676234235653457375763");
				
				$devolver=configuracionTipoProgramaLucesGenerico($con);
			}
			echo $devolver;
		break;
		case 152:
			$acceso=quitaComillasD($_POST["acceso"]);
			if($getS=="s=18" && $acceso>0){
				$_SESSION["fechaIniHistorialPuertasSafeyAcceso"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialPuertasSafeyAcceso"]=quitaComillasD($_POST["fechaFin"]);
				$_SESSION["puertaHistorialPuertasSafeyAcceso"]=quitaComillasD($_POST["puerta"]);
				
				echo puertasSafeyAccesoHistorial($acceso,$con);
			}else{
				echo "n";
			}
		break;	
		case 153:
			$devolver="n";
			$idAcceso=intval(quitaComillasD($_POST["idAcceso"]));
			$idLin=intval(quitaComillasD($_POST["idLin"]));
			
			if($idAcceso>0 && $idLin&& $getS=="s=18"){
				$email="";
				$nombre="";
				$apellidos="";
				$nombreEmpresa="";
				
				$pinAcceso="";
				$llaveAcceso="";
				$mandoAcceso="";
				$panelAcceso="";
				$emailAcceso="";
				$contrasenaAcceso="-";
				$emailAdministradorSistema="-";
				
				//obtener datos
				$patron="SELECT id,nombre,idusuario,pin,llave,mando,maillogin,email,apellidos,dni FROM safey_accesos WHERE id=\"%s\" AND borrado=\"n\"";
				$sql=sprintf($patron,$idAcceso);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963223438686463463737867690023423467787879958");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					
					$nombre=$fila[1];
					$apellidos=$fila[8];
					$email=$fila[7];
					$dni=$fila[9];
					
					//datos del cliente
					$patron1="SELECT nombre,apellidos,email FROM usuarios WHERE id=\"%s\"";
					$sql1=sprintf($patron1,$fila[2]);
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9632234386864113467787879958");
					if(mysqli_num_rows($respuesta1)>0){
						$fila1=mysqli_fetch_array($respuesta1);
						$nombreEmpresa=$fila1[0]." ".$fila1[1];
						$emailAdministradorSistema=$fila1[2];
					}
					mysqli_free_result($respuesta1);
                    
                    //datos de la placa del cliente, coge uno de ellos, no coge el que aplique
                    $rutaFicheroAdjunto="";
                    $nombreFicheroAdjunto="";
                    /*$patron8="SELECT nodo FROM safey_accesosnodos WHERE idacceso=\"%s\" AND borrado=\"n\" ";
					$sql8=sprintf($patron8,$id);
					$respuesta8=mysqli_query($con,$sql8) or die ("Error al buscar 96323357889652343868641134677878708");
					if(mysqli_num_rows($respuesta8)>0){
						$fila8=mysqli_fetch_array($respuesta8);*/
                        
                        //datos
                        $patron7="SELECT ficheronormas FROM safey_nodos WHERE idusuario=\"%s\" AND ficheronormas<>\"\" AND borrado=\"n\" AND guardado=\"s\" ";//AND id=\"%s\"
                        $sql7=sprintf($patron7,$fila[2]/*,$fila8[0]*/);
                        $respuesta7=mysqli_query($con,$sql7) or die ("Error al buscar 9632335776775234386864113467787879958");
                        if(mysqli_num_rows($respuesta7)>0){
                            $fila7=mysqli_fetch_array($respuesta7);
                            $rutaFicheroAdjunto="archivos_subidos/clientes/".$fila[2]."/safey/".$fila7[0];
                            $nombreFicheroAdjunto=$fila7[0];
                        }
                        mysqli_free_result($respuesta7);
                    /*}
					mysqli_free_result($respuesta8);*/
                    
					
					//pin
					$patron2="SELECT pin FROM safey_credenciales_pin WHERE id=\"%s\"";
					$sql2=sprintf($patron2,$fila[3]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 9632234386864118903467782227879958");
					if(mysqli_num_rows($respuesta2)>0){
						$fila2=mysqli_fetch_array($respuesta2);
						$pinAcceso="- Su pin de acceso es: <b>".$fila2[0]."#</b><br>";
					}
					mysqli_free_result($respuesta2);
					
					//llave
					/*$patron3="SELECT descripcion FROM safey_credenciales_llaves WHERE id=\"%s\"";
					$sql3=sprintf($patron3,$fila[3]);
					$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 963223433331134898067782227879958");
					if(mysqli_num_rows($respuesta3)>0){
						$fila3=mysqli_fetch_array($respuesta3);
						$llaveAcceso="- Su llave de acceso es: <b>".$fila3[0]."</b><br>";
					}
					mysqli_free_result($respuesta3);*/
					
					//acceso panel
					/*$patron5="SELECT descripcion FROM safey_credenciales_llaves WHERE id=\"%s\"";
					$sql5=sprintf($patron5,$fila[3]);
					$respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 9632253433335118089534677825227879958");
					if(mysqli_num_rows($respuesta5)>0){
						$fila5=mysqli_fetch_array($respuesta5);
						$panelAcceso=$fila5[0];
					}
					mysqli_free_result($respuesta5);*/
					
					//mando
					/*$patron4="SELECT id FROM safey_credenciales_mandos WHERE id=\"%s\"";
					$sql4=sprintf($patron4,$fila[5]);
					$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 96322344443333113898046778442227879958");
					if(mysqli_num_rows($respuesta4)>0){
						$fila4=mysqli_fetch_array($respuesta4);
						$mandoAcceso="- Su mando de acceso es: <b>M ".$fila4[0]."#</b><br>";
					}
					mysqli_free_result($respuesta4);*/
					
					//datos del acceso web creado
					/*$patron6="SELECT email,aes_decrypt(contrasena, \"%s\") FROM usuarios WHERE id=\"%s\"";
					$sql6=sprintf($patron6,BBDDK,$fila[6]);
					$respuesta6=mysqli_query($con,$sql6) or die ("Error al buscar 9632234386864113467890787879958666");
					if(mysqli_num_rows($respuesta6)>0){
						$fila6=mysqli_fetch_array($respuesta6);
						$emailAcceso="- Se le ha creado un usuario de acceso al portal web: <a href='https://panel.modularbox.com/'>Acceder al panel</a><br>Usuario: <b>".$fila6[0]."</b>";
						$contrasenaAcceso=" contraseña: <b>".$fila6[1]."</b><br>";
					}
					mysqli_free_result($respuesta6);*/
				}
				mysqli_free_result($respuesta);
				
				//enviar mail de accesos
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					
					//datos de la linea del pago
					$linkPago="https://modularbox.com";
                    $importePagar=0;
					$messageSubscripcion="Mensaje";
					$periodoPagado="";
					$tipoSuscripcion="";
					
					$tipoServicio=0;
					$patron123="SELECT idacceso,idusuario,idnodo,tiposervicio,fechainicio,fechafin,tiporeserva FROM safey_pagos WHERE id=\"%s\"";
					$sql123=sprintf($patron123,$idLin);
					$respuesta123=mysqli_query($con,$sql123) or die ("Error al buscar 963223438686234113467787879958");
					if(mysqli_num_rows($respuesta123)>0){
						$fila123=mysqli_fetch_array($respuesta123);
						
						$tipoServicio=$fila123[3];
						
						$periodoPagado=convierteFechaBarra($fila123[4])." - ".convierteFechaBarra($fila123[5]);
						
						//tipo reserva
						if($fila123[6]>0){
							$patron123456="SELECT tipo FROM safey_tiporeservapagos WHERE id=\"%s\"";
							$sql123456=sprintf($patron123456,$fila123[6]);
							$respuesta123456=mysqli_query($con,$sql123456) or die ("Error al buscar 9632464523438686234661134677487879958");
							if(mysqli_num_rows($respuesta123456)>0){
								$fila123456=mysqli_fetch_array($respuesta123456);

								$tipoSuscripcion=$fila123456[0];
							}
							mysqli_free_result($respuesta123456);
						}
						
						//tipo servicio
						if($tipoServicio>0){
							$patron1234="SELECT urlpago,precio,tiposervicio,descripcion FROM safey_nodos_configuracioneconomica WHERE id=\"%s\"";
							$sql1234=sprintf($patron1234,$tipoServicio);
							$respuesta1234=mysqli_query($con,$sql1234) or die ("Error al buscar 9632234386862341134677487879958");
							if(mysqli_num_rows($respuesta1234)>0){
								$fila1234=mysqli_fetch_array($respuesta1234);
								$linkPago=$fila1234[0];
								$importePagar=floatval($fila1234[1]);
								
								//$messageSubscripcion=$fila1234[3];
								
								if($fila1234[2]>0){
									$patron12345="SELECT tipo FROM safey_tiposerviciopagos WHERE id=\"%s\"";
									$sql12345=sprintf($patron12345,$fila1234[2]);
									$respuesta12345=mysqli_query($con,$sql12345) or die ("Error al buscar 96324645234386862341134677487879958");
									if(mysqli_num_rows($respuesta12345)>0){
										$fila12345=mysqli_fetch_array($respuesta12345);
										
										$messageSubscripcion=$fila12345[0];
									}
									mysqli_free_result($respuesta12345);
								}
							}
							mysqli_free_result($respuesta1234);
						}
						
					}
					mysqli_free_result($respuesta123);
					
					
					$copia="";
					$asunto=$nombre." -- SAFEY PAGO SUSCRIPCIÓN (MODULARBOX)";
					//$contenido="Hola, <b>".$nombre."</b><br><br>Ha sido invitado a la utilización de Safey de MODULARBOX S.L. por: <b>".$nombreEmpresa."</b>.<br><br>".$pinAcceso."".$llaveAcceso."".$mandoAcceso."<br>".$emailAcceso."".$contrasenaAcceso."<br>Para cualquier duda contacte con el administrador del sistema: <a href='mailto:".$emailAdministradorSistema."'>".$emailAdministradorSistema."</a><br><br>Un saludo. <br><br><br> <b>No responda a este mensaje</b> ha sido autogenerado por la plataforma <b>(MODULARBOX)</b>.";
					
                    
					/*START contenido mail html definido julio*/
					$contenido="<!DOCTYPE html>
                      <html>
                      <head>
                          <meta charset='UTF-8' />
                          <meta name='viewport' content='width=device-width, initial-scale=1.0' />
                          <title>Pago Subscripción</title>
                          <style>
                            body {
                                font-family: monospace, Helvetica, Arial, sans-serif !important;
                                background-color: #f4f4f4 !important;
                                margin: 0 !important;
                                padding: 0 !important;
                                font-size: 0.9rem !important;
                            }
                            .container {
                                width: 100% !important;
                                max-width: 600px !important;
                                margin: 0 auto !important;
                                background-color: #ffffff !important;
                                padding: 20px !important;
                                border-radius: 8px !important;
                                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1) !important;
                            }
                            .header {
                                text-align: center !important;
                                padding: 10px 0 !important;
                                background-color: #007bff !important;
                                color: #ffffff !important;
                                border-radius: 8px 8px 0 0 !important;
                            }
                            .header h1 {
                                margin: 0 !important;
                                font-size: 24px !important;
                            }
                            .content {
                                margin: 20px 0 !important;
                            }
                            .content p {
                                line-height: 1.6 !important;
                                color: #333333 !important;
                            }
                            .button {
                                display: block !important;
                                width: 200px !important;
                                margin: 20px auto !important;
                                padding: 15px !important;
                                text-align: center !important;
                                background-color: #007bff !important;
                                color: #ffffff !important;
                                /*text-decoration: none !important;*/
                                border-radius: 5px !important;
                            }
                            .footer {
                                text-align: center !important;
                                font-size: 12px !important;
                                color: #999999 !important;
                                margin: 20px 0 0 !important;
                            }
                            .btn-wrapper{
                                text-align: center;
                            }
							
							/* Estilo para el enlace */
							.enlace-chulo {
								text-decoration: none;
								color: #000000;
								background-color: #CCFFC8;
								text-color: #000000;
								padding: 15px 30px;
								margin-top: 15px;
								border: 2px solid #007bff;
								border-radius: 25px;
								transition: all 0.3s ease;
								font-size: 18px;
							}

							/* Efecto hover para el enlace */
							.enlace-chulo:hover {
								background-color: #0056b3;
								border-color: #0056b3;
								box-shadow: 0 0 10px rgba(0, 91, 187, 0.5);
								color: #ffffff;
							}

							/* Efecto focus para el enlace */
							.enlace-chulo:focus {
								outline: none;
								box-shadow: 0 0 10px rgba(0, 91, 187, 0.5);
							}
							
                    	</style>
                      </head>

                      <body>
                          <div class='container'>
                              <div class='header'>
                                  <h1>Pago de subscripción Gym<br> <strong>".$nombreEmpresa."</strong></h1>
                              </div>
                              <div class='content'>
                                  <p>
                                    Estimad@ ".$nombre." ".$apellidos.", <br>
                                    pulse sobre el siguiente botón para completar el pago: 
                                  </p>
                                  <div class='btn-wrapper'>
                                    <a class='enlace-chulo' href='".$linkPago."' >Proceder con el pago</a>
                                  </div>
                                  <p>
                                    <strong>Datos:</strong><br>
                                    Nombre: ".$nombre."<br>
                                    Apellidos: ".$apellidos."<br>
                                    Email: ".$email."<br>
                                    Servicio: GYM<br>
                                    Lugar: ".$nombreEmpresa."<br>
                                    Subscripción: ".$messageSubscripcion."<br>
                                    Tipo: ".$tipoSuscripcion."<br>
                                    Importe:".$importePagar." &#8364;<br>
									Periodo:".$periodoPagado."<br>
                                  </p>
                                  <p>
                                    <strong>Contacto:</strong><br>
                                    Si tiene alguna pregunta o necesita asistencia adicional, no dude en contactarnos a través de:
                                    <br>Email: <a href='mailto:gym@modularbox.com'>gym@modularbox.com</a>
                                    <br>WhatsApp: <a href='https://wa.me/34653483483'>653 483 483</a>
                                    <br>Horario: L-V 09:00 - 14:00 | 17:00 - 20:00</a>
                                    <!--Si tiene alguna pregunta o necesita asistencia adicional, no dude en contactarnos a través de <a
                                        href='mailto:gym@modularbox.com'>gym@modularbox.com</a>, enviándonos un mensaje al <a
                                        href='https://wa.me/34607373372'>607 373 372</a> o a través de la web dónde se dió de alta.-->
                                </p>
                                <p>
                                    Agradecemos su confianza y esperamos que disfrute del gym.
                                </p>
                                <p>Atentamente,<br />El equipo de <a href='https://gym.modularbox.com/'>Modularbox</a></p>
                              </div>

                              <p style='color: #708c91;text-decoration: none;font-size: 12px;'>Te informamos de que seguirás recibiendo mensajes relacionados con tus subscripciones. Para saber más sobre la forma en la que usamos tu información, puedes consultar nuestra política de privacidad <a href='https://reservatupista.com/politica-de-privacidad-proteccion-de-datos-y-politica-de-cookies/' target='_blank' rel='noopener noreferrer' data-auth='NotApplicable' title='date de baja aquí' style='text-decoration: none !important; color: #2dbeff' data-linkindex='11'>aquí</a>. <br /><br /></p>
                            <div class='footer'>
                                <p>&copy; 2024 Modularbox. Todos los derechos reservados.</p>
                            </div>
                          </div>
                      </body>

                      </html>";
                    
					echo mailGenerico($email,$copia,$asunto,$contenido,$id,$rutaFicheroAdjunto,$nombreFicheroAdjunto,$con);
					
					$devolver="s";
				}
			}
			echo $devolver;
		break;
		case 154:
			if($_SESSION["permisossession"]==1 && $getS=="s=19"){
				$_SESSION["usuarioSafeyList"]=$_POST["u"];
				echo credencialesPinSafeyConfiguracion($con);
			}else{
				echo "n";
			}
		break;
		case 155:
			$devolver="n";
			$i=explode("=",$getI);
			$id=intval(quitaComillasD($_POST["id"]));
			$idNodo=intval(quitaComillasD($_POST["idNodo"]));
			
			
			$tipoServicio=intval(quitaComillasD($_POST["tipoServicio"]));
			$precio=floatval(quitaComillasD($_POST["precio"]));
			$tipoReserva=intval(quitaComillasD($_POST["tipoReserva"]));
			$activo=quitaComillasD($_POST["activo"]);
			$urlPagoTarifa=quitaComillasD($_POST["urlPagoTarifa"]);
			$descripcion=quitaComillasD($_POST["descripcion"]);
			
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2) && $getS=="s=16" && $i[1]==$idNodo){
				
				$patron1="SELECT id FROM safey_nodos_configuracioneconomica WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
				$sql1=sprintf($patron1,$id,$idNodo);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 19837656785576978786535672342087644781154");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					
					//update
					$patron="UPDATE safey_nodos_configuracioneconomica SET tiposervicio=\"%s\",precio=\"%s\",activo=\"%s\",tiporeserva=\"%s\",urlpago=\"%s\",descripcion=\"%s\" WHERE id=\"%s\" AND idnodo=\"%s\"";
					$sql=sprintf($patron,$tipoServicio,$precio,$activo,$tipoReserva,$urlPagoTarifa,$descripcion,$id,$idNodo);
					$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 4219265567474534123456347453543580935");
					
					$devolver=configuracionEconomicaNodo($idNodo,$con);
				}
				mysqli_free_result($respuesta1);
			}
			echo $devolver;
		break;	
		case 156:
			$devolver="n";
			$i=explode("=",$getI);
			$id=intval(quitaComillasD($_POST["id"]));
			$idNodo=intval(quitaComillasD($_POST["idNodo"]));
			
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2) && $getS=="s=16" && $i[1]==$idNodo){
				
				$patron1="SELECT id FROM safey_nodos_configuracioneconomica WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
				$sql1=sprintf($patron1,$id,$idNodo);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 19825376567855767865356723420876441154");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					
					//borrado
					$patron="UPDATE safey_nodos_configuracioneconomica SET borrado=\"s\" WHERE id=\"%s\" AND idnodo=\"%s\" AND borrado=\"n\"";
					$sql=sprintf($patron,$id,$idNodo);
					$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 4321926574534123456347453543580935");
					
					$devolver=configuracionEconomicaNodo($idNodo,$con);
				}
				mysqli_free_result($respuesta1);
			}
			echo $devolver;
		break;	 
		case 157:
			$devolver="n";
			$i=explode("=",$getI);
			$idNodo=intval(quitaComillasD($_POST["nodo"]));
			
			$tipoServicio=intval(quitaComillasD($_POST["tipoServicioTarifa"]));
			$precio=floatval(quitaComillasD($_POST["precioTarifa"]));
			$tipoReserva=intval(quitaComillasD($_POST["tipoReservaTarifa"]));
			//$activo=quitaComillasD($_POST["activo"]);
			$descripcion=quitaComillasD($_POST["descripcion"]);
			
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2) && $getS=="s=16" && $i[1]==$idNodo){
				$activo="s";
				//insert
				$patron="INSERT INTO safey_nodos_configuracioneconomica SET tiposervicio=\"%s\",precio=\"%s\",activo=\"%s\",tiporeserva=\"%s\",idnodo=\"%s\",descripcion=\"%s\"";
				$sql=sprintf($patron,$tipoServicio,$precio,$activo,$tipoReserva,$idNodo,$descripcion);
				$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 421924357655674745341234563475679003580935");

				$devolver=configuracionEconomicaNodo($idNodo,$con);
			}
			echo $devolver;
		break;	 
		case 158:
			$devolver="n";
			$i=explode("=",$getI);
			$idAcceso=intval(quitaComillasD($_POST["idAcceso"]));
			$idNodo=intval(quitaComillasD($_POST["nodo"]));
			
			if(($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2) && $getS=="s=18" && $i[1]==$idAcceso){
				$devolver=cargarTipoServicioPagoNodo("","",$idNodo,$con);
				
			}
			echo $devolver;
		break;	
		case 159:
			$i=explode("=",$getI);
			
			$n=quitaComillasD($_POST["n"]);
			if($getS=="s=16" && $n>0  && $i[1]==$n){
				$_SESSION["fechaIniHistorialPagos"]=quitaComillasD($_POST["fechaIni"]);
				$_SESSION["fechaFinHistorialPagos"]=quitaComillasD($_POST["fechaFin"]);
				
				echo pagosSafeyNodoHistorial($n,$con);
			}else{
				echo "n";
			}
		break;
		case 160:
			$devolver="n";
			$i=explode("=",$getI);
			$idAcceso=intval(quitaComillasD($_POST["idAcceso"]));
			$idPago=intval(quitaComillasD($_POST["idPago"]));
            
			$fPago=quitaComillasD($_POST["fPago"]);
			
			if(($_SESSION["permisossession"]==1) && $getS=="s=18" && $i[1]==$idAcceso && $idPago>0){
				//buscar usuario
				$patron1="SELECT idusuario FROM safey_accesos WHERE id=\"%s\" AND borrado=\"n\" AND guardado=\"s\"";
				$sql1=sprintf($patron1,$idAcceso);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 97854136345982537855767865356723420876441154");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					
					//update fecha pago y establecer como pagado
					$patron="UPDATE safey_pagos SET fechapago=\"%s\",pagado=\"s\" WHERE id=\"%s\" AND idusuario=\"%s\" AND borrado=\"n\" AND idacceso=\"%s\"";
					$sql=sprintf($patron,$fPago,$idPago,$fila1[0],$idAcceso);
					$respuesta=mysqli_query($con,$sql) or die ("Error al borrar 6567432192234565734123456347453543580935");
					
					$devolver=pagosSafeyHistorial($idAcceso,$con);	
				}
				mysqli_free_result($respuesta1);
			}
			echo $devolver;
		break; 
		case 161:
			$correcto="n";
			$i=explode("=",$getI);
			$idAcceso=intval(quitaComillasD($_POST["idAcceso"]));
			
            if($getS=="s=18" && $i[1]==$idAcceso){
                $_SESSION["fInicioHistorialPagoAcceso"]=quitaComillasD($_POST["fechaIni"]);
                $_SESSION["fFinHistorialPagoAcceso"]=quitaComillasD($_POST["fechaFin"]);
                $_SESSION["puertaHistorialPagoAcceso"]=intval(quitaComillasD($_POST["puerta"]));		
                $_SESSION["nodoHistorialPagoAcceso"]=intval(quitaComillasD($_POST["idNodo"]));		
                $_SESSION["idAccesoHistorialPagoAcceso"]=intval(quitaComillasD($_POST["idAcceso"]));
				
				$correcto="s";
            }
			echo $correcto;
		break;
			
        case 9998:
            $imagenes=array();
            $patron="SELECT archivo FROM archivossubidos WHERE borrado=\"n\" AND tabla=\"ini\"";
            $sql=sprintf($patron);
            $respuesta=mysqli_query($con,$sql) or die ("Error al buscar 3453567234234334df34345454");
            if(mysqli_num_rows($respuesta)>0){
                for($i=0;$i<mysqli_num_rows($respuesta);$i++){
                    $fila=mysqli_fetch_array($respuesta);
                    array_push($imagenes, "archivos_subidos/img_inicio_login/".$fila[0]);
                }
            }
			mysqli_free_result($respuesta);
            echo json_encode($imagenes);
        break;
		default:
			echo "No se ha encontrado una opción correctas.";
		break;
	}
}
?>