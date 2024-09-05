<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');
		
header("Access-Control-Allow-Origin: *");

require_once("../const/constantes.php");
require_once("../phpincludes/phpgeneral.php");
require_once("../phpincludes/phppropios.php");

//abir conexion
//https://panel.modularbox.com/apimultiwater/multiwaternodos.php?abrirCerrarConexion=abrirCerrarConexion&token=N7Z5-f7w4-2Z2H7X5Y5Y8A&internal=500900&conexion=1
if(isset($_POST["abrirCerrarConexion"])){
	
	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$abrirCerrarConexion=quitaComillasD($_POST["conexion"]);
	
    if($token!="" && $internal!="" && ($abrirCerrarConexion=="1" || $abrirCerrarConexion=="2")){
        $patron="SELECT id FROM multiwater_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 123");
        if(mysqli_num_rows($respuesta)>0){
            for($i=0;$i<mysqli_num_rows($respuesta);$i++){
                $fila=mysqli_fetch_array($respuesta);
				
				//establecer como conectada
				if($abrirCerrarConexion=="1"){
					$estado="on";//encendida/encender
				}else if($abrirCerrarConexion=="2"){
					$estado="off";//apagada/apagar
				}
				
				$patron1="UPDATE multiwater_nodos SET estado=\"%s\" WHERE id=\"%s\" AND token=\"%s\" AND internal=\"%s\"";
				$sql1=sprintf($patron1,$estado,$fila[0],$token,$internal);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al editar 1234");
				
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
        $patron="SELECT id,estado FROM multiwater_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 1234");
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

//cierro la conexion
$con->close();
?>