<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');
		
header("Access-Control-Allow-Origin: *");

require_once("../const/constantes.php");
require_once("../phpincludes/phpgeneral.php");
require_once("../phpincludes/phppropios.php");
require_once("../phpincludes/phpparques.php");

//https://panel.modularbox.com/apiparques/parquesnodos.php?abrirCerrarConexion=abrirCerrarConexion&token=F4E3-4v9q-4W5Q5D7N7Q8T8R8&internal=00000001&conexion=1
if(isset($_POST["abrirCerrarConexion"])){

	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$abrirCerrarConexion=quitaComillasD($_POST["conexion"]);
	
    if($token!="" && $internal!="" && ($abrirCerrarConexion=="1" || $abrirCerrarConexion=="2")){
        $patron="SELECT id FROM parques_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 1223476567648952435");
        if(mysqli_num_rows($respuesta)>0){
            //for($i=0;$i<mysqli_num_rows($respuesta);$i++){
                $fila=mysqli_fetch_array($respuesta);
				
				//establecer como conectada
				if($abrirCerrarConexion=="1"){
					$conexion="on";//encendida/encender
				}else if($abrirCerrarConexion=="2"){
					$conexion="off";//apagada/apagar
				}
				
				$patron1="UPDATE parques_nodos SET conexion=\"%s\",ultimaconexion=\"%s\",horaultimaconexion=\"%s\" WHERE id=\"%s\" AND token=\"%s\" AND internal=\"%s\"";
				$sql1=sprintf($patron1,$conexion,date("Y-m-d"),date("H:i:s"),$fila[0],$token,$internal);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al editar 1567454334553452345");
				
				$correcto="s";
           //}
        }
		mysqli_free_result($respuesta);
    }
   	echo $correcto;
}

//obtener horario puertas
if(isset($_POST["obtenerHorarioPuertasParques"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
    if($token!="" && $internal!=""){
		
		$consultaCampos="";
		$diaSemana=date("N");
		switch($diaSemana){
			case 1:
				$consultaCampos=",lhoraabrir,lhoracerrar";
			break;
			case 2:
				$consultaCampos=",mhoraabrir,mhoracerrar";
			break;
			case 3:
				$consultaCampos=",xhoraabrir,xhoracerrar";
			break;
			case 4:
				$consultaCampos=",jhoraabrir,jhoracerrar";
			break;
			case 5:
				$consultaCampos=",vhoraabrir,vhoracerrar";
			break;
			case 6:
				$consultaCampos=",shoraabrir,shoracerrar";
			break;
			case 7:
				$consultaCampos=",dhoraabrir,dhoracerrar";
			break;
			default:
				$consultaCampos=",lhoraabrir,lhoracerrar";
			break;
		}
		
		if($consultaCampos!=""){
			$patron="SELECT id%s FROM parques_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
			$sql=sprintf($patron,$consultaCampos,$token,$internal);
			$respuesta=mysqli_query($con,$sql) or die ("Error 1223675864545764358956467675467677678670689359955");
			if(mysqli_num_rows($respuesta)>0){
				$fila=mysqli_fetch_array($respuesta);

				$correcto=$fila[1]."@#".$fila[2];
			}
			mysqli_free_result($respuesta);
		}
        
	}
	
	echo $correcto;//texto
}

//controlar estado puerta
if(isset($_POST["estadoPuertaParques"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$finalCarrera=intval(quitaComillasD($_POST["finalCarrera"]));
	$idHistorial=intval(quitaComillasD($_POST["idHistorial"]));
	$estadoPuertaPlaca=intval(quitaComillasD($_POST["estado"]));
	
    if($token!="" && $internal!="" && ($finalCarrera==35 || $finalCarrera==11) /*&& $idHistorial>0*/){
        $patron="SELECT id,estadopuertaizq,estadopuertader FROM parques_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586476834423593599");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);

			//estado
			$estadoBBDD=2;//cerrada por defecto
			if($estadoPuertaPlaca==0){
				$estadoBBDD=2;//cerrada
			}else if($estadoPuertaPlaca==1){
				$estadoBBDD=1;//abierta
			}
			
			$consultaEstado="";
			if($finalCarrera==35){//electro 1, izquierda
				$consultaEstado="estadopuertaizq=\"".$estadoBBDD."\"";
			}else if($finalCarrera==11){//electro 2, derecha
				$consultaEstado="estadopuertader=\"".$estadoBBDD."\"";
			}
			
			if($consultaEstado!=""){
				$patron2="UPDATE parques_nodos SET %s WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$consultaEstado,$fila[0]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al editar 15672343234545");
			}

			$correcto="s";
			
        }
		mysqli_free_result($respuesta);
    }
   	echo $correcto;
}

//consultar si hay algo por arbir
if(isset($_POST["consultarAbrirPuertasWebParques"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$tiempoSegundosMargen=2*60;
	$registros="";
	
    if($token!="" && $internal!=""){
        $patron="SELECT id FROM parques_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586454576435645467677678670689359955");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			$duracionSegundos=2;
			$pulsoCorriente="p";

			//recorrer historial de esa puerta
			$patron2="SELECT id,fechaalta,horaalta,puerta,idnodo FROM parques_historial WHERE idnodo=\"%s\" AND accionrealizada=\"n\" AND miradoplaca=\"n\" AND (fechaalta=\"%s\" OR fechaalta=\"%s\") AND tipo=\"2\" ORDER BY fechaalta DESC,horaalta DESC, id DESC LIMIT 0,1";
			$sql2=sprintf($patron2,$fila[0],date("Y-m-d"),restaDias(date("Y-m-d"),1));//AND puerta=\"%s\"//,$fila1[0]
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 122344677641574543436455456800689395");
			if(mysqli_num_rows($respuesta2)>0){
				for($j=0;$j<mysqli_num_rows($respuesta2);$j++){
					$fila2=mysqli_fetch_array($respuesta2);
					
					//hora bbdd
					$horaSegundosAbrirAux=explode(":",$fila2[2]);
					$horaSegundosAbrir=($horaSegundosAbrirAux[0]*60*60)+($horaSegundosAbrirAux[1]*60)+$horaSegundosAbrirAux[2];

					//hora actual
					$horaActualSegundosAbrirAux=explode(":",date("H:i:s"));
					$horaActualSegundosAbrir=($horaActualSegundosAbrirAux[0]*60*60)+($horaActualSegundosAbrirAux[1]*60)+$horaActualSegundosAbrirAux[2];

					if($horaSegundosAbrir>=$horaActualSegundosAbrir){
						$restaSegundos=$horaSegundosAbrir-$horaActualSegundosAbrir;
					}else{
						$restaSegundos=$horaActualSegundosAbrir-$horaSegundosAbrir;
					}
					
					//saber si han pasado dos minutos
					if($restaSegundos<=$tiempoSegundosMargen){
						//id historial
						//puerta(izq-der)
						$registros=$fila2[0]."::".$fila2[3];
					}

				}
			}
			mysqli_free_result($respuesta2);
			
			if($registros!=""){
				$correcto=$registros;//respuesta
			}
			
			//anotar esta conexion
			$patron199="UPDATE parques_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
			$sql199=sprintf($patron199,date("Y-m-d"),date("H:i:s"),$fila[0]);
			$respuesta199=mysqli_query($con,$sql199) or die ("Error al editar 45454453434534547563737009090");
		}
		mysqli_free_result($respuesta);
	}
	
	echo /*"2::izq"*/$correcto;//texto
}

//anotar en el historial aperturaweb puertas
if(isset($_POST["anotarAperturaPuertaHistWebParques"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$idHistorial=intval(quitaComillasD($_POST["idHistorial"]));
	$puerta=quitaComillasD($_POST["puerta"]);

    if($token!="" && $internal!="" && ($idHistorial>0 || $idHistorial==-98 || $idHistorial==-99) ){
        $patron="SELECT id FROM parques_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 1223675845464356676745476782342367068935993");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			if($idHistorial>0){
				//comprobar ese registro del historial
				$patron3="SELECT id,miradoplaca,accionrealizada,puerta,miradoplaca FROM parques_historial WHERE id=\"%s\" AND idnodo=\"%s\"";
				$sql3=sprintf($patron3,$idHistorial,$fila[0],$puerta);// AND puerta=\"%s\"//,$puerta
				$respuesta3=mysqli_query($con,$sql3) or die ("Error 12236337586435667673376782334236734630689359922");
				if(mysqli_num_rows($respuesta3)>0){
					$fila3=mysqli_fetch_array($respuesta3);

					//anotar como realizado ese registro
					$patron1="UPDATE parques_historial SET miradoplaca=\"s\",accionrealizada=\"s\" WHERE id=\"%s\"";
					$sql1=sprintf($patron1,$idHistorial);
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 123451178034346532356853478966345565746645574546");

					//poner como mirado por la placa las acciones anteriores, solamente las anteriores
					$patron2="UPDATE parques_historial SET miradoplaca=\"s\" WHERE puerta=\"%s\" AND idnodo=\"%s\" AND miradoplaca=\"n\" AND id<\"%s\"";
					$sql2=sprintf($patron2,$puerta,$fila[0],$fila3[0]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al borrar 12345110463434345465234222265746645574546");

					$correcto="s";
				}
				mysqli_free_result($respuesta3);
			}else if($idHistorial==-98 || $idHistorial==-99){
				//anotar como realizado ese registro
				$patron4="INSERT INTO parques_historial SET idnodo=\"%s\",puerta=\"amb\",tipo=\"1\",idacceso=\"0\",idusuario=\"0\",miradoplaca=\"s\",accionrealizada=\"s\",horaalta=\"%s\",fechaalta=\"%s\"";
				$sql4=sprintf($patron4,$fila[0],date("H:i:s"),date("Y-m-d"));
				$respuesta4=mysqli_query($con,$sql4) or die ("Error al borrar 123451178034346532344568534447896634556574664557564546");
				
				$correcto="s";
			}
			
		}
		mysqli_free_result($respuesta);
	}
	echo $correcto;
}

//cierro la conexion
$con->close();
?>