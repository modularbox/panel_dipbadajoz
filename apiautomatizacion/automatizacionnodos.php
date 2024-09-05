<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');

require_once("../const/constantes.php");
require_once("../phpincludes/phpgeneral.php");
require_once("../phpincludes/phppropios.php");
require_once("../phpincludes/phpautomatizacion.php");

//https://panel.modularbox.com/apiautoamtizacion/automatizacionnodos.php?abrirCerrarConexion=abrirCerrarConexion&token=3F9V-7y5a-Q9H9S4F2H3C3F8Y&internal=00000001&conexion=1
/*if(isset($_POST["abrirCerrarConexion"])){

	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$abrirCerrarConexion=quitaComillasD($_POST["conexion"]);
	
    if($token!="" && $internal!="" && ($abrirCerrarConexion=="1" || $abrirCerrarConexion=="2")){
        $patron="SELECT id FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 12234768456456935");
        if(mysqli_num_rows($respuesta)>0){
            //for($i=0;$i<mysqli_num_rows($respuesta);$i++){
                $fila=mysqli_fetch_array($respuesta);
				
				//establecer como conectada
				if($abrirCerrarConexion=="1"){
					$conexion="on";//encendida/encender
				}else if($abrirCerrarConexion=="2"){
					$conexion="off";//apagada/apagar
				}
				
				$patron1="UPDATE safey_nodos SET conexion=\"%s\",ultimaconexion=\"%s\",horaultimaconexion=\"%s\" WHERE id=\"%s\" AND token=\"%s\" AND internal=\"%s\"";
				$sql1=sprintf($patron1,$conexion,date("Y-m-d"),date("H:i:s"),$fila[0],$token,$internal);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al editar 15456466732345");
				
				$correcto="s";
           //}
        }
		mysqli_free_result($respuesta);
    }
   	echo $correcto;
}*/


//https://panel.modularbox.com/apiautomatizacion/automatizacionnodos.php?obtenerConfiguracionProgramasReles=obtenerConfiguracionProgramasReles&token=F4E3-4v9q-4W5Q5D7N7Q8T8FD&internal=00000001
if(isset($_POST["obtenerConfiguracionProgramasReles"])){

	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	if($token!="" && $internal!=""){
		
		$arrayGeneral=array();
		$arrayPrograma=array();
		
		$patron2="SELECT id,idusuario FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
		$sql2=sprintf($patron2,$token,$internal);
		$respuesta2=mysqli_query($con,$sql2)or die ("Error 123996253487254");
		if(mysqli_num_rows($respuesta2)>0){
			$fila2=mysqli_fetch_array($respuesta2);
			$idNodo=intval($fila2[0]);
			//array_push($arrayGeneral,$idNodo);//no mandar id nodo, para leer mejor el array
			$patron1="SELECT idprograma FROM automatizacion_programas_activos WHERE activo=\"s\" AND idnodo=\"%s\"";
			$sql1=sprintf($patron1,$idNodo);
			$respuesta1=mysqli_query($con,$sql1)or die ("Error 673594254672173823594");
			if(mysqli_num_rows($respuesta1)>0){
				for($p=0;$p<mysqli_num_rows($respuesta1);$p++){
					$arrayPrograma=array();
					$fila1=mysqli_fetch_array($respuesta1);
					$idPrograma=intval($fila1[0]);

					array_push($arrayPrograma,$idPrograma);

					for($d=1;$d<=6;$d++){
						$arraySalidas=array();
						array_push($arraySalidas,intval($d));
						$patron="SELECT l,m,x,j,v,s,d,horainicio,horafin FROM automatizacion_programa_salidas WHERE borrado=\"n\" AND idprograma=\"%s\" AND salida=\"%s\"";
						$sql=sprintf($patron,$idPrograma,$d);
						$respuesta=mysqli_query($con,$sql)or die ("Error 9127355234528938475");
						if(mysqli_num_rows($respuesta)>0){
							for($r=0;$r<mysqli_num_rows($respuesta);$r++){
								$fila=mysqli_fetch_array($respuesta);
								$arrayLinea=array();
								$arrayLineaSemanal=array();
								//lunes
								if($fila[0]=="s"){
									array_push($arrayLineaSemanal,"1");
								}else{
									array_push($arrayLineaSemanal,"0");
								}
								//martes
								if($fila[1]=="s"){
									array_push($arrayLineaSemanal,"1");
								}else{
									array_push($arrayLineaSemanal,"0");
								}
								//miercoles
								if($fila[2]=="s"){
									array_push($arrayLineaSemanal,"1");
								}else{
									array_push($arrayLineaSemanal,"0");
								}
								//jueves
								if($fila[3]=="s"){
									array_push($arrayLineaSemanal,"1");
								}else{
									array_push($arrayLineaSemanal,"0");
								}
								//viernes
								if($fila[4]=="s"){
									array_push($arrayLineaSemanal,"1");
								}else{
									array_push($arrayLineaSemanal,"0");
								}
								//sabado
								if($fila[5]=="s"){
									array_push($arrayLineaSemanal,"1");
								}else{
									array_push($arrayLineaSemanal,"0");
								}
								//domingo
								if($fila[6]=="s"){
									array_push($arrayLineaSemanal,"1");
								}else{
									array_push($arrayLineaSemanal,"0");
								}
								array_push($arrayLinea,$arrayLineaSemanal);//METER LOS DIAS MARCADOS, dias semana
								array_push($arrayLinea,$fila[7]);//METER HORA INICIO
								array_push($arrayLinea,$fila[8]);//METER HORA FIN
								array_push($arraySalidas,$arrayLinea);
							}
						}else{
							//pasar vacio para no romper en python
							$arrayLinea=array();
							$vacio=["0","0","0","0","0","0","0"];
							array_push($arrayLinea,$vacio);//le anado array vacio
							array_push($arrayLinea,"00:00:00");//le anado la hora inicio vacia
							array_push($arrayLinea,"00:00:00");//le anado la hora fin vacia
							array_push($arraySalidas,$arrayLinea);//anado la linea a la salida Sx
						}
						mysqli_free_result($respuesta);
						
						array_push($arrayPrograma,$arraySalidas);//METEMOS LAS SALIDAS EN EL ARRAY GENERAL
					}//for salidas
					array_push($arrayGeneral,$arrayPrograma);
				}//for idprograma
			}//if idprograma
			mysqli_free_result($respuesta1);
				
			//anotar esta conexion
			$patron3="UPDATE safey_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
			$sql3=sprintf($patron3,date("Y-m-d"),date("H:i:s"),$idNodo);
			$respuesta3=mysqli_query($con,$sql3) or die ("Error al editar 43566723423456756746534567467546");
				
		}//cierro if nodos
		mysqli_free_result($respuesta2);
		
	}//END IF (TOKEN E INTERNAL DISTINTO DE VACIO)
	print_r(json_encode($arrayGeneral));//respuesta
}

//https://panel.modularbox.com/apiautomatizacion/automatizacionnodos.php?configuracionPlacaModos=configuracionPlacaModos&token=F4E3-4v9q-4W5Q5D7N7Q8T8FD&internal=00000001
//saber el modo de cada rele
if(isset($_POST["configuracionAutomaPlacaModos"])){
	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	if($token!="" && $internal!=""){

		$patron2="SELECT salidaunomodo,salidaunomanualactivado,salidadosmodo,salidadosmanualactivado,salidatresmodo,salidatresmanualactivado,salidacuatromodo,salidacuatromanualactivado,salidacincomodo,salidacincomanualactivado,salidaseismodo,salidaseismanualactivado,id FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
		$sql2=sprintf($patron2,$token,$internal);
		$respuesta2=mysqli_query($con,$sql2)or die ("Error 4564574995");
		if(mysqli_num_rows($respuesta2)>0){
			$fila2=mysqli_fetch_array($respuesta2);
			
			if($fila2[0]=="m" && $fila2[1]=="on"){
				$correcto="1";
			}else{
				$correcto="0";
			}
			
			if($fila2[2]=="m" && $fila2[3]=="on"){
				$correcto.=",1";
			}else{
				$correcto.=",0";
			}
			
			if($fila2[4]=="m" && $fila2[5]=="on"){
				$correcto.=",1";
			}else{
				$correcto.=",0";
			}
			
			if($fila2[6]=="m" && $fila2[7]=="on"){
				$correcto.=",1";
			}else{
				$correcto.=",0";
			}
			
			if($fila2[8]=="m" && $fila2[9]=="on"){
				$correcto.=",1";
			}else{
				$correcto.=",0";
			}
			
			if($fila2[10]=="m" && $fila2[11]=="on"){
				$correcto.=",1";
			}else{
				$correcto.=",0";
			}
			
			$patron3="UPDATE safey_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
			$sql3=sprintf($patron3,date("Y-m-d"),date("H:i:s"),$fila2[12]);
			$respuesta3=mysqli_query($con,$sql3) or die ("Error al editar 345756563673456345");
			
		}
		mysqli_free_result($respuesta2);
	}
	echo $correcto;

}

//https://panel.modularbox.com/apiautomatizacion/automatizacionnodos.php?historialProgramaReleEjecutado=historialProgramaReleEjecutado&token=F4E3-4v9q-4W5Q5D7N7Q8T8FD&internal=00000001&idPrograma=1&salida=1
//anotar en el historiaL
if(isset($_POST["historialProgramaReleEjecutado"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$idPrograma=quitaComillasD($_POST["idPrograma"]);
	$salida=quitaComillasD($_POST["salida"]);

    if($token!="" && $internal!="" && $idPrograma!="" && $salida!=""){
        $patron="SELECT id FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 6729378489234579234636432");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
		
			$modo="a";
			if($idPrograma==0){
				$modo="m";
			}
			
			//anotar
			$patron2="INSERT INTO automatizacion_historial SET idprograma=\"%s\",idnodo=\"%s\",salida=\"%s\",horaalta=\"%s\",fechaalta=\"%s\",estado=\"on\",modo=\"%s\"";
			$sql2=sprintf($patron2,$idPrograma,$fila[0],$salida,date("H:i:s"),date("Y-m-d"),$modo);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error al insertar 3456546734576785");

			//anotar esta conexion
			$patron3="UPDATE safey_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
			$sql3=sprintf($patron3,date("Y-m-d"),date("H:i:s"),$fila[0]);
			$respuesta3=mysqli_query($con,$sql3) or die ("Error al editar 12345542341313424523");
			
			$correcto="s";
		}
		mysqli_free_result($respuesta);
	}
	echo $correcto;
}

//cierro la conexion
$con->close();
?>