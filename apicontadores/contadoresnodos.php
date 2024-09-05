<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');
		
header("Access-Control-Allow-Origin: *");

require_once("../const/constantes.php");
require_once("../phpincludes/phpgeneral.php");
require_once("../phpincludes/phppropios.php");

//abir conexion
//http://78.136.74.2:6080/panel/apicontadores/contadoresnodos.php?registrarLectura=registrarLectura&token=N7Z5-f7w4-2Z2H7X5Y5Y8j&internal=500901&lectura=100
if(isset($_POST["abrirCerrarConexion"])){
	
	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$abrirCerrarConexion=quitaComillasD($_POST["conexion"]);
	
    if($token!="" && $internal!="" && ($abrirCerrarConexion=="1" || $abrirCerrarConexion=="2")){
        $patron="SELECT id FROM contadores_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 1235");
        if(mysqli_num_rows($respuesta)>0){
            for($i=0;$i<mysqli_num_rows($respuesta);$i++){
                $fila=mysqli_fetch_array($respuesta);
				
				//establecer como conectada
				if($abrirCerrarConexion=="1"){
					$conexion="on";//encendida/encender
				}else if($abrirCerrarConexion=="2"){
					$conexion="off";//apagada/apagar
				}
				
				$patron1="UPDATE contadores_nodos SET conexion=\"%s\" WHERE id=\"%s\" AND token=\"%s\" AND internal=\"%s\"";
				$sql1=sprintf($patron1,$conexion,$fila[0],$token,$internal);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al editar 12345");
				
				$correcto="s";
           }
        }
		mysqli_free_result($respuesta);
    }
   	echo $correcto;
}

//saber estado conexion
if(isset($_POST["estadoConexion"])){
	
	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
    if($token!="" && $internal!=""){
        $patron="SELECT id,estado FROM contadores_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 12346");
        if(mysqli_num_rows($respuesta)>0){
            for($i=0;$i<mysqli_num_rows($respuesta);$i++){
                $fila=mysqli_fetch_array($respuesta);
				
				$correcto=$fila[1];
           }
        }
		mysqli_free_result($respuesta);
    }
   	echo $correcto;
}

//registrar lectura
if(isset($_POST["registrarLectura"])){
	
	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$lecturaPulsos=quitaComillasD($_POST["lectura"]);
	
    if($token!="" && $internal!=""){
		$patron="SELECT contadores_nodos.id,contadores_nodos.litrosporpulsos FROM contadores_nodos,usuarios WHERE contadores_nodos.borrado=\"n\" AND contadores_nodos.guardado=\"s\" AND contadores_nodos.token=\"%s\" AND contadores_nodos.internal=\"%s\" AND usuarios.id=contadores_nodos.idusuario AND usuarios.borrado=\"n\" AND usuarios.guardado=\"s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 1234677");
        if(mysqli_num_rows($respuesta)>0){
            for($i=0;$i<mysqli_num_rows($respuesta);$i++){
                $fila=mysqli_fetch_array($respuesta);
				
				$lectura=$fila[1]*$lecturaPulsos;
				
				//update
				$patron1="INSERT INTO contadores_historial SET borrado=\"n\",contador=\"%s\",lectura=\"%s\",hora=\"%s\",fecha=\"%s\",pulso=\"%s\",creado=\"1\"";
				$sql1=sprintf($patron1,$fila[0],$lectura,date("H:i:s"),date("Y-m-d"),$lecturaPulsos);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error 1234565465467");
				
				$correcto="s";
           	}
		
			//anotar esta conexion, para online y offline
			$patron199="UPDATE contadores_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
			$sql199=sprintf($patron199,date("Y-m-d"),date("H:i:s"),$fila[0]);
			$respuesta199=mysqli_query($con,$sql199) or die ("Error al editar 4545343454434534547563737009090");
        }
		mysqli_free_result($respuesta);
    }
   	echo $correcto;
}

//cierro la conexion
$con->close();
?>