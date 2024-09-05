<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');
		
header("Access-Control-Allow-Origin: *");

require_once("../const/constantes.php");
require_once("../phpincludes/phpgeneral.php");
require_once("../phpincludes/phppropios.php");
require_once("../phpincludes/phpluces.php");


//https://panel.modularbox.com/apiluces/luces_programas.php?returnProgramLuzGenerico=aa&token=9C6G-3x8e-Z7R9C9Y9W8Q7B2N&internal=09900002
if(isset($_GET["returnProgramLuzGenerico"])){
	
	/*----PARA PINTAR BOTONERIA DESDE LA WEB**********/
			/*******/
	
    //$correcto="n";
	$token=quitaComillasD($_GET["token"]);//obligatorio
	$internal=quitaComillasD($_GET["internal"]);//obligatorio
	
	$arrayCompletoJson=array();//json a devolver
	if($token!="" && $internal!=""){
		
		$patron="SELECT luces_nodos.id,usuarios.id FROM luces_nodos,usuarios WHERE luces_nodos.borrado=\"n\" AND luces_nodos.guardado=\"s\" AND luces_nodos.internal=\"%s\" AND luces_nodos.token=\"%s\" AND usuarios.id=luces_nodos.idusuario AND usuarios.borrado=\"n\" AND usuarios.guardado=\"s\"";
        $sql=sprintf($patron,$internal,$token);
        $respuesta=mysqli_query($con,$sql) or die ("Error 123565635345344677");
        if(mysqli_num_rows($respuesta)>0){
        	$fila=mysqli_fetch_array($respuesta);
			
			$idClienteUsuario=intval($fila[1]);
			
			//recorrer programas que esten configurados como predeterminados
			$patron1="SELECT id,idtipoprograma FROM luces_programas WHERE borrado=\"n\" AND idusuario=\"%s\" AND idtipoprograma>0";
			$sql1=sprintf($patron1,$idClienteUsuario);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error 1235656224645777");
			if(mysqli_num_rows($respuesta1)>0){
				for($i=0;$i<mysqli_num_rows($respuesta1);$i++){
					$fila1=mysqli_fetch_array($respuesta1);
					
					$idProgramaReal=intval($fila1[0]);
					$idProgramaPredeterminado=intval($fila1[1]);
					
					//obtener datos del programa preconfigurado asociado
					if($idProgramaPredeterminado>0){
						$patron2="SELECT id,nombre,idconfiguracioncolor FROM luces_tiposprogramaspredefinidos WHERE borrado=\"n\" AND id=\"%s\" ";
						$sql2=sprintf($patron2,$idProgramaPredeterminado);
						$respuesta2=mysqli_query($con,$sql2) or die ("Error 22");
						if(mysqli_num_rows($respuesta2)>0){
							$fila2=mysqli_fetch_array($respuesta2);
							
							//obtener color real relacionado
							$colorHexadecimal="";
							$patron3="SELECT colorreal FROM luces_configuracion_color WHERE borrado=\"n\" AND id=\"%s\" ";
							$sql3=sprintf($patron3,$fila2[2]);
							$respuesta3=mysqli_query($con,$sql3) or die ("Error 1235653336224645777");
							if(mysqli_num_rows($respuesta3)>0){
								$fila3=mysqli_fetch_array($respuesta3);
								$colorHexadecimal=$fila3[0];
							}
							mysqli_free_result($respuesta3);
							
							$arrayConfPrograma=array();//crear el array de con la info. del programa predefinido y su programa final asociado
					
							array_push($arrayConfPrograma, $fila2[0]);//id del programa predefinido/*$idProgramaPredeterminado*/
							array_push($arrayConfPrograma, $fila2[1]);//nombre del programa predefinido
							array_push($arrayConfPrograma, $colorHexadecimal);//color del programa predefinido
							array_push($arrayConfPrograma, $idProgramaReal);//id del programa real asociado
						}
						mysqli_free_result($respuesta2);
					}
					//anadir esa info al array contendor
					array_push($arrayCompletoJson, $arrayConfPrograma);//id del programa predefinido
				}//cierro for
			}
			mysqli_free_result($respuesta1);
		}
		mysqli_free_result($respuesta);
	}
	
	//codificamos el json
	print_r(json_encode($arrayCompletoJson));
}

//https://panel.modularbox.com/apiluces/luces_programas.php?configProgramaActivarWeb=aa&token=9C6G-3x8e-Z7R9C9Y9W8Q7B2N&internal=09900002
//&programaAsociado=9
//&programaPre=7
if(isset($_POST["configProgramaActivarWeb"])){
	/*----PARA ACTIVAR EL PROGRAMA PULSADO DESDE LA WEB**********/
			/*******/
	
    $correcto="n";
	$token=quitaComillasD($_POST["token"]);//obligatorio
	$internal=quitaComillasD($_POST["internal"]);//obligatorio
	$idProgramaRealAsociado=intval(quitaComillasD($_POST["programaAsociado"]));//obligatorio //id programa real
	$idProgramaPredeterminado=intval(quitaComillasD($_POST["programaPre"]));//opcional // id programa predeterminado asociado al programa real
	
	if($token!="" && $internal!="" && $idProgramaRealAsociado>0){
		
		$patron="SELECT luces_nodos.id,usuarios.id FROM luces_nodos,usuarios WHERE luces_nodos.borrado=\"n\" AND luces_nodos.guardado=\"s\" AND luces_nodos.internal=\"%s\" AND luces_nodos.token=\"%s\" AND usuarios.id=luces_nodos.idusuario AND usuarios.borrado=\"n\" AND usuarios.guardado=\"s\"";
        $sql=sprintf($patron,$internal,$token);
        $respuesta=mysqli_query($con,$sql) or die ("Error 1235656353353456554545344677");
        if(mysqli_num_rows($respuesta)>0){
        	$fila=mysqli_fetch_array($respuesta);
			
			$idClienteUsuario=intval($fila[1]);
			
			$consulta="";
			if($idProgramaPredeterminado){
				$consulta=" AND idtipoprograma=".$idProgramaPredeterminado;
			}
			
			//recorrer programas que esten configurados como predeterminados
			$patron1="SELECT id,idtipoprograma FROM luces_programas WHERE borrado=\"n\" AND idusuario=\"%s\" AND id=\"%s\"%s";
			$sql1=sprintf($patron1,$idClienteUsuario,$idProgramaRealAsociado,$consulta);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error 123565656792246664645777");
			if(mysqli_num_rows($respuesta1)>0){
				for($i=0;$i<mysqli_num_rows($respuesta1);$i++){
					$fila1=mysqli_fetch_array($respuesta1);
				
					$correcto="s";
				}
			}
			mysqli_free_result($respuesta1);
		}
		mysqli_free_result($respuesta);
	}
	
	echo $correcto;
}

//cierro la conexion
$con->close();
?>