<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');

session_name("modularboxpanel");
session_start([
  'read_and_close'  => false,
]);

/*start mostrar errores*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*end mostrar errores*/

require_once("const/constantes.php");
require_once("phpincludes/phpgeneral.php");
require_once("phpincludes/phppropios.php");
require_once("phpincludes/phpdocumentos.php");
//require_once("phpmailer/class.phpmailer.php");
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

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


//notificaciones, quitar y luego meter en cronjobs
//require_once("phpincludes/timerNotificaciones.php");
//cronjobsgeneral, quitar y luego meter en cronjobs
//require_once("phpincludes/cronjobsgeneral.php");

if(isset($_POST["useraplicacion"]) && isset($_POST["claveaplicacion"])){
	$res=conectaUsuario($_POST["useraplicacion"],$_POST["claveaplicacion"],$con);
	header("Location:index.php");
}

if(usuarioCorrecto($con)){
	
    
	if(isset($_GET["s"]) && is_numeric($_GET["s"])){
		switch($_GET["s"]){
			case 1:
				//quita filtros
				
				//filtros
				
				if($_SESSION["permisossession"]==1){
					$archivocarga="usuarios.html";
				}
			break;
			case 2:
				if($_SESSION["permisossession"]==1 || ($_SESSION["permisossession"]==2 && $_SESSION["idusersession"]==$_GET["i"])){
					
					$archivocarga="usuario.html";
				}else{
					header("Location:index.php");
				}
			break;
			case 3:
				//quita filtros
				//filtros
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="multiwaters.html";
				}
			break;
			case 4:
				//quita filtros
				
				//filtros
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					if(isset($_GET["i"])){
						comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					}
					$archivocarga="multiwater.html";
				}
			break;
			case 5:
				//quita filtros
				//filtros
				if(!isset($_SESSION["usuarioContadorList"])){
					$_SESSION["usuarioContadorList"]=0;		
				}
				if(!isset($_SESSION["estadoContadorList"])){
					$_SESSION["estadoContadorList"]="";
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="contadores.html";
				}
			break;
			case 6:
				//quita filtros
				
				//filtros
				if(!isset($_SESSION["fechaHistorialContadorLectu"])){
					$_SESSION["fechaHistorialContadorLectu"]=date("Y-m-d");		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					if(isset($_GET["i"])){
						comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					}
					$archivocarga="contador.html";
				}
			break;
			case 7:
				//quita filtros
				
				//filtros
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2){
					$archivocarga="ajustes.html";
				}
			break;
			case 8:
				//quita filtros
				//filtros
				if(!isset($_SESSION["usuarioLucesList"])){
					$_SESSION["usuarioLucesList"]=0;		
				}
				if(!isset($_SESSION["estadoLucesList"])){
					$_SESSION["estadoLucesList"]="";
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="luces.html";
				}
			break;
			case 9:
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					if(isset($_GET["i"])){
						comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					}
					$archivocarga="luz.html";
				}
			break;
			case 11:
				//quita filtros
				//filtros
				if(!isset($_SESSION["usuarioProgramasLucesList"])){
					$_SESSION["usuarioProgramasLucesList"]=0;		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="luces_programas.html";
				}
			break;
			case 12:
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					
					if(isset($_GET["i"])){
						comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					}
					
					$archivocarga="luces_programa.html";
				}
			break;
			case 13:
				//quita filtros
				//filtros
				if(!isset($_SESSION["usuarioProgramasLucesList"])){
					$_SESSION["usuarioProgramasLucesList"]=0;		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="luces_horarios.html";
				}
			break;
			case 14:
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					if(isset($_GET["i"])){
						comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					}
					$archivocarga="luces_horario.html";
				}
			break;
			case 15:
				//quita filtros
				//filtros
				if(!isset($_SESSION["usuarioSafeyList"])){
					$_SESSION["usuarioSafeyList"]=0;		
				}
				if(!isset($_SESSION["conexionSafeyList"])){
					$_SESSION["conexionSafeyList"]="";
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="safeys.html";
				}
			break;
			case 16:
				//filtros
				if(!isset($_SESSION["fechaIniHistorialPagos"])){
					$_SESSION["fechaIniHistorialPagos"]=date("Y-m-d");		
				}
                if(!isset($_SESSION["fechaFinHistorialPagos"])){
					$_SESSION["fechaFinHistorialPagos"]=date("Y-m-d");		
				}
				
				if(!isset($_SESSION["fechaIniHistorialPuertasSafey"])){
					$_SESSION["fechaIniHistorialPuertasSafey"]=date("Y-m-d");		
				}
				if(!isset($_SESSION["fechaFinHistorialPuertasSafey"])){
					$_SESSION["fechaFinHistorialPuertasSafey"]=date("Y-m-d");		
				}
                
				if(!isset($_SESSION["puertaHistorialPuertasSafey"])){
					$_SESSION["puertaHistorialPuertasSafey"]=0;		
				}
				
				if(!isset($_SESSION["fechaIniHistorialFallidoPuertasSafey"])){
					$_SESSION["fechaIniHistorialFallidoPuertasSafey"]=date("Y-m-d");		
				}
                if(!isset($_SESSION["fechaFinHistorialFallidoPuertasSafey"])){
					$_SESSION["fechaFinHistorialFallidoPuertasSafey"]=date("Y-m-d");		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					if(isset($_GET["i"])){
						comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					}
					$archivocarga="safey.html";
				}
			break;
			case 17:
				//quita filtros
				
				//filtros
				if(!isset($_SESSION["usuarioSafeyList"])){
					$_SESSION["usuarioSafeyList"]=0;		
				}
					
				if(!isset($_SESSION["usuarioSelecCredencialesSafey"])){
					$_SESSION["usuarioSelecCredencialesSafey"]=0;		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="safey_gestionaccesos.html";
				}
			break;
			case 18:
				//filtros
				if(!isset($_SESSION["fechaIniHistorialPagos"])){
					$_SESSION["fechaIniHistorialPagos"]=date("Y-m")."-01";		
				}
				if(!isset($_SESSION["fechaFinHistorialPagos"])){
					$_SESSION["fechaFinHistorialPagos"]=date("Y-m-d");		
				}
				
                if(!isset($_SESSION["fechaIniHistorialPuertasSafeyAcceso"])){
					$_SESSION["fechaIniHistorialPuertasSafeyAcceso"]=date("Y-m-d");		
				}
				if(!isset($_SESSION["fechaFinHistorialPuertasSafeyAcceso"])){
					$_SESSION["fechaFinHistorialPuertasSafeyAcceso"]=date("Y-m-d");		
				}
                
				if(!isset($_SESSION["puertaHistorialPuertasSafeyAcceso"])){
					$_SESSION["puertaHistorialPuertasSafeyAcceso"]=0;		
				}
                
				
				/*start filtros tabla pagos*/
				if(!isset($_SESSION["fInicioHistorialPagoAcceso"])){
					$_SESSION["fInicioHistorialPagoAcceso"]=date("Y-m-d");		
				}
				if(!isset($_SESSION["fFinHistorialPagoAcceso"])){
					$_SESSION["fFinHistorialPagoAcceso"]=date("Y-m-d");		
				}
				if(!isset($_SESSION["puertaHistorialPagoAcceso"])){
					$_SESSION["puertaHistorialPagoAcceso"]=0;		
				}
				if(!isset($_SESSION["nodoHistorialPagoAcceso"])){
					$_SESSION["nodoHistorialPagoAcceso"]=0;		
				}
				if(!isset($_SESSION["idAccesoHistorialPagoAcceso"])){
					$_SESSION["idAccesoHistorialPagoAcceso"]=0;		
				}
				/*end filtros tabla pagos*/
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					//comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);//CORREGIR ESTO PARA EL PERMISO 2
					$archivocarga="safey_gestionacceso.html";
				}
			break;
			case 19:
				//quita filtros
				
				//filtros
				if(!isset($_SESSION["usuarioSafeyList"])){
					$_SESSION["usuarioSafeyList"]=0;		
				}
				
				if($_SESSION["permisossession"]==1){
					$archivocarga="safey_credencialesconf.html";
				}
			break;
            case 20:
				if($_SESSION["permisossession"]==1){
					$archivocarga="imagenesInicio.html";
				}
            break;
			case 21:
				//quita filtros
				
				//filtros
				if(!isset($_SESSION["usuarioPistasPadelList"])){
					$_SESSION["usuarioPistasPadelList"]=0;		
				}
				if(!isset($_SESSION["conexionPistasPadelList"])){
					$_SESSION["conexionPistasPadelList"]="";
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="pistaspadel.html";
				}
			break;
			case 22:
				//filtros
				if(!isset($_SESSION["fechaIniHistorialPuertasPistasPadel"])){
					$_SESSION["fechaIniHistorialPuertasPistasPadel"]=date("Y-m-d");		
				}
				if(!isset($_SESSION["fechaFinHistorialPuertasPistasPadel"])){
					$_SESSION["fechaFinHistorialPuertasPistasPadel"]=date("Y-m-d");		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					if(isset($_GET["i"])){
						comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					}
					$archivocarga="pistapadel.html";
				}
			break;
			case 23:
				//quita filtros
				
				//filtros
				if(!isset($_SESSION["usuarioParquesList"])){
					$_SESSION["usuarioParquesList"]=0;		
				}
				if(!isset($_SESSION["conexionParquesList"])){
					$_SESSION["conexionParquesList"]="";
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="parques.html";
				}
			break;
			case 24:	
				//filtros
				if(!isset($_SESSION["fechaIniHistorialPuertasParques"])){
					$_SESSION["fechaIniHistorialPuertasParques"]=date("Y-m-d");		
				}
				if(!isset($_SESSION["fechaFinHistorialPuertasParques"])){
					$_SESSION["fechaFinHistorialPuertasParques"]=date("Y-m-d");		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					if(isset($_GET["i"])){
						comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					}
					$archivocarga="parque.html";
				}
			break;
			case 25:
				if($_SESSION["permisossession"]==1){
					$archivocarga="almacenPinesLlaves.html";
				}
            break;
			case 26:
				//quita filtros
				
				//filtros
				if(!isset($_SESSION["usuarioCampanasList"])){
					$_SESSION["usuarioCampanasList"]=0;		
				}
				if(!isset($_SESSION["conexionCampanasList"])){
					$_SESSION["conexionCampanasList"]="";
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="campanas.html";
				}
			break;
			case 27:
				//filtros
				if(!isset($_SESSION["fechaIniHistorialProgramasCampanas"])){
					$_SESSION["fechaIniHistorialProgramasCampanas"]=date("Y-m-d");		
				}
                if(!isset($_SESSION["fechaFinHistorialProgramasCampanas"])){
					$_SESSION["fechaFinHistorialProgramasCampanas"]=date("Y-m-d");		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					if(isset($_GET["i"])){
						comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					}
					$archivocarga="campana.html";
				}
			break;
			case 28:
				//quita filtros
				
				//filtros
				if(!isset($_SESSION["usuarioCampanasProgramasList"])){
					$_SESSION["usuarioCampanasProgramasList"]=0;		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="campanas_programas.html";
				}
			break;
			case 29:
				//filtros
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					if(isset($_GET["i"])){
						comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					}
					$archivocarga="campanas_programa.html";
				}
			break;
            case 30:
				//filtros
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					//comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					$archivocarga="campanas_reloj.html";
				}
			break;
			case 31:
				//quita filtros
				
				//filtros
				if(!isset($_SESSION["usuarioAutomatizacionProgramasList"])){
					$_SESSION["usuarioAutomatizacionProgramasList"]=0;		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="automatizacion_programas.html";
				}
			break;
			case 32:
				//filtros
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					if(isset($_GET["i"])){
						comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					}
					$archivocarga="automatizacion_programa.html";
				}
			break;
			case 33:
				//quita filtros
				
				//filtros
				if(!isset($_SESSION["usuarioAutomatizacionList"])){
					$_SESSION["usuarioAutomatizacionList"]=0;		
				}
				if(!isset($_SESSION["conexionAutomatizacionList"])){
					$_SESSION["conexionAutomatizacionList"]="";
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="automatizacions.html";
				}
			break;
			case 34:
				//filtros
				if(!isset($_SESSION["fechaIniHistorialSalidasAutomatizacion"])){
					$_SESSION["fechaIniHistorialSalidasAutomatizacion"]=date("Y-m-d");			
				}
				if(!isset($_SESSION["fechaFinHistorialSalidasAutomatizacion"])){
					$_SESSION["fechaFinHistorialSalidasAutomatizacion"]=date("Y-m-d");		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					if(isset($_GET["i"])){
						comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);
					}
					$archivocarga="automatizacion.html";
				}
			break;
            case 36:
                //quita filtros
				//filtros
				if(!isset($_SESSION["usuarioProgramasAudioList"])){
					$_SESSION["usuarioProgramasAudioList"]=0;		
				}
				if(!isset($_SESSION["usuarioAudioList"])){
					$_SESSION["usuarioAudioList"]=0;		
				}
				if(!isset($_SESSION["conexionAudioList"])){
					$_SESSION["conexionAudioList"]="";		
				}
				
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="audios.html";
				}
            break;
            case 37:
                //filtros
				if(!isset($_SESSION["fechaIniHistorialProgramasAudio"])){
					$_SESSION["fechaIniHistorialProgramasAudio"]=date("Y-m-d");		
				}
                if(!isset($_SESSION["fechaFinHistorialProgramasAudio"])){
					$_SESSION["fechaFinHistorialProgramasAudio"]=date("Y-m-d");		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					//comprobarAccesoFicha($_GET["s"],$_GET["i"],$con);//FALTA
					$archivocarga="audio.html";
				}
            break;
            case 38:
                //filtros
				if(!isset($_SESSION["usuarioVideovigilanciaList"])){
					$_SESSION["usuarioVideovigilanciaList"]=0;		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 /*|| $_SESSION["permisossession"]==3*/){
					$archivocarga="videoVigilancia.html";
				}
            break;
			case 39:
				//quita filtros
				
				//filtros
				if(!isset($_SESSION["usuarioAudioSubirList"])){
					$_SESSION["usuarioAudioSubirList"]=0;		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="audio_ficherosaudios.html";
				}
			break;
			case 40:
				//quita filtros
				
				//filtros
				if(!isset($_SESSION["usuarioAudioList"])){
					$_SESSION["usuarioAudioList"]=0;		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="audios_colasreproduccion.html";
				}
			break;
			case 41:
				//quita filtros
				
				//filtros
				if(!isset($_SESSION["usuarioAudioList"])){
					$_SESSION["usuarioAudioList"]=0;		
				}
				
				if(!isset($_SESSION["fechaIniHistorialGeneralAudio"])){
					$_SESSION["fechaIniHistorialGeneralAudio"]=date("Y-m-d");		
				}
                if(!isset($_SESSION["fechaFinHistorialGeneralAudio"])){
					$_SESSION["fechaFinHistorialGeneralAudio"]=date("Y-m-d");		
				}
				
				if(!isset($_SESSION["nodoHistorialGenAudioList"])){
					$_SESSION["nodoHistorialGenAudioList"]=0;		
				}
				
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2 || $_SESSION["permisossession"]==3){
					$archivocarga="audios_historial.html";
				}
			break;
			case 42:
				//quita filtros
				
				//filtros
				
				
				if($_SESSION["permisossession"]==1){
					$archivocarga="luces_configurarhomologaciones.html";
				}
			break;
			case 43:
				//quita filtros
				
				//filtros
				
				
				if($_SESSION["permisossession"]==1){
					$archivocarga="luces_tiposprogramaspredefinidos.html";
				}
			break;
				
			case 9999000:
				if($_SESSION["permisossession"]==1 && false){
					$contadorEliminados=0;
					//eliminar pines cliente safey y almacen
					$patron="SELECT id,pinactivo,pin,idacceso FROM safey_accesos WHERE borrado=\"n\" AND guardado=\"s\" AND idusuario=\"%s\" AND pinactivo=\"off\"";
					$sql=sprintf($patron,38);
					$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 112123354646989607");
					if(mysqli_num_rows($respuesta)>0){
						for($i=0;$i<mysqli_num_rows($respuesta);$i++){
							$fila=mysqli_fetch_array($respuesta);
						
							if($fila[1]=="off"){
								//pin safey
								$patron1="SELECT id,idpinalmacen,pinserie FROM safey_credenciales_pin WHERE borrado=\"n\" AND id=\"%s\" ";
								$sql1=sprintf($patron1,$fila[2]);
								$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 112123311121241154646989607");
								if(mysqli_num_rows($respuesta1)>0){
									$fila1=mysqli_fetch_array($respuesta1);
									
									//pin almacen general
									$patron2="SELECT id FROM almacen_credenciales_pin WHERE borrado=\"n\" AND id=\"%s\" AND pinserie=\"%s\"";
									$sql2=sprintf($patron2,$fila1[1],$fila1[2]);
									$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 112123333322154646989607");
									if(mysqli_num_rows($respuesta2)>0){
										$fila2=mysqli_fetch_array($respuesta2);

										//echo "Eliminar almacén:".$fila2[0];

										//borrar de safey
										$patron3="UPDATE safey_credenciales_pin SET borrado=\"s\" WHERE id=\"%s\"";
										$sql3=sprintf($patron3,$fila1[0]);
										//$respuesta3=mysqli_query($con,$sql3) or die ("Error al borrar 123456347893333");
										
										//update el acceso ese pin
										$patron5="UPDATE safey_accesos SET pin=\"s\",pinactivo=\"off\" WHERE id=\"%s\"";
										$sql5=sprintf($patron5,$fila1[3]);
										//$respuesta5=mysqli_query($con,$sql5) or die ("Error al borrar 123456347893333");

										//borrar de almacen general
										$patron4="UPDATE almacen_credenciales_pin SET borrado=\"s\" WHERE id=\"%s\"";
										$sql4=sprintf($patron4,$fila2[0]);
										//$respuesta4=mysqli_query($con,$sql4) or die ("Error al borrar 1234563478933322223");
										
										$contadorEliminados+=1;
									}
									mysqli_free_result($respuesta2);
								}
								mysqli_free_result($respuesta1);
							}

						}
					}
					mysqli_free_result($respuesta);
					echo "Eliminados: ".$contadorEliminados;
				  }
			break;
			case 9999001:
				if($_SESSION["permisossession"]==1 && false){
					//eliminar accesos con pines repetidos o otros clientes
					$patron="SELECT id,pin,idusuario FROM safey_accesos WHERE borrado=\"n\" AND guardado=\"s\" AND pin<>0";
					$sql=sprintf($patron);
					$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1121233542222646989607");
					if(mysqli_num_rows($respuesta)>0){
						for($i=0;$i<mysqli_num_rows($respuesta);$i++){
							$fila=mysqli_fetch_array($respuesta);
							
							/*start solucionar problema1*/
							//saber si ese acceso es de ese usuario, en caso de no ser, poner a cero
							$patron1="SELECT id,idpinalmacen,pinserie,idacceso FROM safey_credenciales_pin WHERE borrado=\"n\" AND idusuario=\"%s\"";
							$sql1=sprintf($patron1,$fila[2]);
							$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 1121233111212675641154646989607");
							if(mysqli_num_rows($respuesta1)>0){
								for($j=0;$j<mysqli_num_rows($respuesta1);$j++){
									$fila1=mysqli_fetch_array($respuesta1);

									//miro si el acceso asignado es de ese cliente
									$patron12="SELECT id,idusuario FROM safey_accesos WHERE id=\"%s\" ";
									$sql12=sprintf($patron12,$fila1[3]);
									$respuesta12=mysqli_query($con,$sql12) or die ("Error al buscar 112123311121267564115464622989607");
									if(mysqli_num_rows($respuesta12)>0){
										$fila12=mysqli_fetch_array($respuesta12);

										if($fila12[1]==$fila[2]){
											//echo "ok";
										}else{
											//echo "ko, poner a cero".$fila1[0]."<br><br>";
											//pin safey poner el resto a cero 
											$patron13="UPDATE safey_credenciales_pin SET idacceso=\"0\" WHERE id=\"%s\"";
											$sql13=sprintf($patron13,$fila1[0]);
											//$respuesta13=mysqli_query($con,$sql13) or die ("Error al buscar 112123544531111321241154646989607");
										}

									}
									mysqli_free_result($respuesta12);
								}
							}
							mysqli_free_result($respuesta1);
							/*end solucionar problema1*/
							
							
							/*start solucionar problema2*/
							//pin safey poner el resto a cero 
							
							$patron13="SELECT id,idacceso FROM safey_credenciales_pin WHERE borrado=\"n\" AND id<>\"%s\" AND idacceso=\"%s\" AND idusuario=\"%s\" AND borrado=\"n\"";
							$sql13=sprintf($patron13,$fila[1],$fila[0],$fila[2]);
							$respuesta13=mysqli_query($con,$sql13) or die ("Error al buscar 112123311121267564115464622989607");
							if(mysqli_num_rows($respuesta13)>0){
								for($p=0;$p<mysqli_num_rows($respuesta13);$p++){
									$fila13=mysqli_fetch_array($respuesta13);
									
									//echo "id a resertear: ".$fila13[0].", id acceso duplicado usado:".$fila13[1]."<br>";
									
									$patron14="UPDATE safey_credenciales_pin SET idacceso=\"0\" WHERE id=\"%s\"";
									$sql14=sprintf($patron14,$fila13[0]);
									//$respuesta14=mysqli_query($con,$sql14) or die ("Error al buscar 1121235445311121241154646989607");
								}
							}
							mysqli_free_result($respuesta13);
							/*end solucionar problema2*/
								
						}
					}
					mysqli_free_result($respuesta);
				  }
			break;
			case 9999002:
				if($_SESSION["permisossession"]==1 && false){
					
					//liberar pines de accesos eliminados, es decir, acceso eliminado liberar los pines
					$patron="SELECT id,pin,idusuario FROM safey_accesos WHERE borrado=\"s\" AND guardado=\"s\" AND pin<>0";
					$sql=sprintf($patron);
					$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 11212335456762222646989607");
					if(mysqli_num_rows($respuesta)>0){
						for($i=0;$i<mysqli_num_rows($respuesta);$i++){
							$fila=mysqli_fetch_array($respuesta);
					
							
							$patron1="SELECT id,idpinalmacen,pinserie,idacceso FROM safey_credenciales_pin WHERE borrado=\"n\" AND idacceso=\"%s\"";
							$sql1=sprintf($patron1,$fila[0]);
							$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 112571233111212675641154646989607");
							if(mysqli_num_rows($respuesta1)>0){
								for($j=0;$j<mysqli_num_rows($respuesta1);$j++){
									$fila1=mysqli_fetch_array($respuesta1);
									
									
									$patron14="UPDATE safey_credenciales_pin SET idacceso=\"0\" WHERE id=\"%s\"";
									$sql14=sprintf($patron14,$fila1[0]);
									//$respuesta14=mysqli_query($con,$sql14) or die ("Error al buscar 1121235445311121241154646989607");

								}
							}
							mysqli_free_result($respuesta1);
						}
					}
					mysqli_free_result($respuesta);
				}
			break;
			case 9999003:
				if($_SESSION["permisossession"]==1 && false){
					
					//mirar los pines no borrados si tienen el acceso asignado, en caso contrario liberar
					$patron="SELECT id,idacceso FROM safey_credenciales_pin WHERE idacceso<>\"0\"";
					$sql=sprintf($patron);
					$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 11121233453455456762222646989607");
					if(mysqli_num_rows($respuesta)>0){
						for($i=0;$i<mysqli_num_rows($respuesta);$i++){
							$fila=mysqli_fetch_array($respuesta);
					
							$patron1="SELECT id,pin FROM safey_accesos WHERE id=\"%s\"";
							$sql1=sprintf($patron1,$fila[1]);
							$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 11257123311451212675641154646989607");
							if(mysqli_num_rows($respuesta1)>0){
								for($j=0;$j<mysqli_num_rows($respuesta1);$j++){
									$fila1=mysqli_fetch_array($respuesta1);
									
									if($fila1[1]==0){
										$patron14="UPDATE safey_credenciales_pin SET idacceso=\"0\" WHERE id=\"%s\"";
										$sql14=sprintf($patron14,$fila[0]);
										//$respuesta14=mysqli_query($con,$sql14) or die ("Error al buscar 1121235445311121241154646989607");
									}
									

								}
							}else{//liberar igualmente
								$patron14="UPDATE safey_credenciales_pin SET idacceso=\"0\" WHERE id=\"%s\"";
								$sql14=sprintf($patron14,$fila[0]);
								//$respuesta14=mysqli_query($con,$sql14) or die ("Error al buscar 1121235445311121241154646989607");
							}
							mysqli_free_result($respuesta1);
						}
					}
					mysqli_free_result($respuesta);
							
					
				}
			break;
			default:
				$archivocarga="inicio.html";
			break;
		}
		include("cabecera.html");
		include($archivocarga);
	}else{
        if(!isset($_SESSION["anioSeleccionadoGrafica"])){
            $_SESSION["anioSeleccionadoGrafica"]=date("Y");
        }
		include("cabecera.html");
		include("inicio.html");
	}
	include("pie.html");
}else{
	include("login.html");  
}
?>