<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');
		
header("Access-Control-Allow-Origin: *");

require_once("../const/constantes.php");
require_once("../phpincludes/phpgeneral.php");
require_once("../phpincludes/phppropios.php");
require_once("../phpincludes/phppistaspadel.php");

//https://panel.modularbox.com/apipistaspadel/pistaspadelnodos.php?abrirCerrarConexion=abrirCerrarConexion&token=F4E3-4v9q-4W5Q5D7N7Q8T8R8&internal=00000001&conexion=1
if(isset($_POST["abrirCerrarConexion"])){

	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$abrirCerrarConexion=quitaComillasD($_POST["conexion"]);
	
    if($token!="" && $internal!="" && ($abrirCerrarConexion=="1" || $abrirCerrarConexion=="2")){
        $patron="SELECT id FROM pistaspadel_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 1223476567648935");
        if(mysqli_num_rows($respuesta)>0){
            //for($i=0;$i<mysqli_num_rows($respuesta);$i++){
                $fila=mysqli_fetch_array($respuesta);
				
				//establecer como conectada
				if($abrirCerrarConexion=="1"){
					$conexion="on";//encendida/encender
				}else if($abrirCerrarConexion=="2"){
					$conexion="off";//apagada/apagar
				}
				
				$patron1="UPDATE pistaspadel_nodos SET conexion=\"%s\",ultimaconexion=\"%s\",horaultimaconexion=\"%s\" WHERE id=\"%s\" AND token=\"%s\" AND internal=\"%s\"";
				$sql1=sprintf($patron1,$conexion,date("Y-m-d"),date("H:i:s"),$fila[0],$token,$internal);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al editar 1567334553452345");
				
				$correcto="s";
           //}
        }
		mysqli_free_result($respuesta);
    }
   	echo $correcto;
}

//controlar estado puerta
if(isset($_POST["estadoPuertaPistaPadel"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$finalCarrera=intval(quitaComillasD($_POST["finalCarrera"]));
	$idHistorial=intval(quitaComillasD($_POST["idHistorial"]));
	$estadoPuertaPlaca=intval(quitaComillasD($_POST["estado"]));
	
    if($token!="" && $internal!="" && ($finalCarrera==35 || $finalCarrera==11) /*&& $idHistorial>0*/){
        $patron="SELECT id,estadopuertaizq,estadopuertader FROM pistaspadel_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586476834593599");
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
				$patron2="UPDATE pistaspadel_nodos SET %s WHERE id=\"%s\"";
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
if(isset($_POST["consultarAbrirPuertasWebPadel"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$tiempoSegundosMargen=2*60;
	$registros="";
	
	$horaInicioLuz="18:30:00";
	$horaFinLuz="23:59:00";
	
    if($token!="" && $internal!=""){
        $patron="SELECT id FROM pistaspadel_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586454576435667677678670689359955");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			$duracionSegundos=3;//antes dos
			$pulsoCorriente="p";
			
			
			/*STAR consulta temporal -- para no tocar placa -- quitar en el futuro y controlar en placa?¿?*/
			$dateDiezSegundosRestados=new DateTime();
			$dateDiezSegundosRestados->modify('-10 second');
			/*echo $dateDiezSegundosRestados->format("H:i:s");*/
			$dateDosMinutosSumados=new DateTime();
			$dateDosMinutosSumados->modify('+1 minute');
			/*echo $dateDosMinutosSumados->format("H:i:s");*/
			
			$consultaTemporalHoras=" AND horaalta BETWEEN \"".$dateDiezSegundosRestados->format("H:i:s")."\" AND \"".$dateDosMinutosSumados->format("H:i:s")."\"";
			/*END consulta temporal -- para no tocar placa -- quitar en el futuro y controlar en placa?¿?*/
			
			
			//recorrer historial de esa puerta
			$patron2="SELECT id,fechaalta,horaalta,puerta,idnodo,tipo FROM pistaspadel_historial WHERE idnodo=\"%s\" AND accionrealizada=\"n\" AND miradoplaca=\"n\" AND (fechaalta=\"%s\" OR fechaalta=\"%s\") AND (tipo=\"2\" OR tipo=\"5\")%s ORDER BY fechaalta DESC,horaalta DESC, id DESC LIMIT 0,1";
			$sql2=sprintf($patron2,$fila[0],date("Y-m-d"),restaDias(date("Y-m-d"),1),$consultaTemporalHoras);//AND puerta=\"%s\"//,$fila1[0]
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 12234467764157454645545680068935");
			if(mysqli_num_rows($respuesta2)>0){
				for($j=0;$j<mysqli_num_rows($respuesta2);$j++){
					$fila2=mysqli_fetch_array($respuesta2);

					$horaInicioLuz="18:30:00";
					$horaFinLuz="23:59:00";
					$tiempoPartida=0;
					$tipoModo=$fila2[5];
					
					$patron3="SELECT horainicioluz,horafinluz,tiempopartida FROM pistaspadel_nodos WHERE id=\"%s\"";
					$sql3=sprintf($patron3,$fila2[4]);
					$respuesta3=mysqli_query($con,$sql3) or die ("Error 1223446733376415743354645545680068935");
					if(mysqli_num_rows($respuesta3)>0){
						$fila3=mysqli_fetch_array($respuesta3);
						$horaInicioLuz=$fila3[0];
						$horaFinLuz=$fila3[1];
						$tiempoPartida=$fila3[2];
					}
					
					if($j>0){
						$registros.='@#';
					}
					
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
					
					//devolver horario luces
					
					//saber si han pasado dos minutos
					if($restaSegundos<=$tiempoSegundosMargen){
						//id historial
						//puerta(izq-der)
						//pulso o corriente
						// duracion del pulso o corriente segundos
						//h inicio luz
						//h fin luz
						//tiempo partida
						//tipo modo
						$registros.=$fila2[0]."::".$fila2[3]."::".$pulsoCorriente."::".$duracionSegundos."::".$horaInicioLuz."::".$horaFinLuz."::".$tiempoPartida."::".$tipoModo;
					}

				}
			}
			mysqli_free_result($respuesta2);
			
			if($registros!=""){
				$correcto=$registros;//respuesta
			}
			
			//anotar esta conexion, para online y offline
			$patron199="UPDATE pistaspadel_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
			$sql199=sprintf($patron199,date("Y-m-d"),date("H:i:s"),$fila[0]);
			$respuesta199=mysqli_query($con,$sql199) or die ("Error al editar 45453434534547563737009090");
		}
		mysqli_free_result($respuesta);
	}
	
	echo /*"2::izq::p::4@#2::der::p::4::11:00:00::23:59:00::60:2"*/$correcto;//texto
}

//anotar en el historial aperturaweb puertas
if(isset($_POST["anotarAperturaPuertaHistWebPadel"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$idHistorial=quitaComillasD($_POST["idHistorial"]);
	$puerta=quitaComillasD($_POST["puerta"]);

    if($token!="" && $internal!="" && $idHistorial>0 ){
        $patron="SELECT id FROM pistaspadel_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 1223675845464356676776782342367068935993");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			//comprobar ese registro del historial
			$patron3="SELECT id,miradoplaca,accionrealizada,puerta,miradoplaca FROM pistaspadel_historial WHERE id=\"%s\" AND idnodo=\"%s\"";
			$sql3=sprintf($patron3,$idHistorial,$fila[0],$puerta);// AND puerta=\"%s\"//,$puerta
			$respuesta3=mysqli_query($con,$sql3) or die ("Error 122363375864356676733767823423670689359922");
			if(mysqli_num_rows($respuesta3)>0){
				$fila3=mysqli_fetch_array($respuesta3);
			
				//anotar como realizado ese registro
				$patron1="UPDATE pistaspadel_historial SET miradoplaca=\"s\",accionrealizada=\"s\" WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$idHistorial);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 12345117803434656853478966345565746645574546");
				
				//poner como mirado por la placa las acciones anteriores, solamente las anteriores
				$patron2="UPDATE pistaspadel_historial SET miradoplaca=\"s\" WHERE idnodo=\"%s\" AND miradoplaca=\"n\" AND id<\"%s\"";//puerta=\"%s\" AND
				$sql2=sprintf($patron2,$fila[0],$fila3[0]);//,$puerta
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al borrar 1234511046343465234222265746645574546");
				
				//anotar esta conexion
				$patron199="UPDATE pistaspadel_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
				$sql199=sprintf($patron199,date("Y-m-d"),date("H:i:s"),$fila[0]);
				$respuesta199=mysqli_query($con,$sql199) or die ("Error al editar 45453447564564637370093456565090");
				
				$correcto="s";
			}
			mysqli_free_result($respuesta3);
		}
		mysqli_free_result($respuesta);
	}
	echo $correcto;
}

//consultar si hay algo por arbir desde el lector pines
if(isset($_POST["comprobarSiHayAlgoWeb"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$tiempoSegundosMargen=2*60;
	
    if($token!="" && $internal!=""){
        $patron="SELECT id FROM pistaspadel_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586454576456453435667677678670689359955");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			
			/*STAR consulta temporal -- para no tocar placa -- quitar en el futuro y controlar en placa?¿?*/
			$dateDiezSegundosRestados=new DateTime();
			$dateDiezSegundosRestados->modify('-10 second');
			/*echo $dateDiezSegundosRestados->format("H:i:s");*/
			$dateDosMinutosSumados=new DateTime();
			$dateDosMinutosSumados->modify('+1 minute');
			/*echo $dateDosMinutosSumados->format("H:i:s");*/
			
			$consultaTemporalHoras=" AND horaalta BETWEEN \"".$dateDiezSegundosRestados->format("H:i:s")."\" AND \"".$dateDosMinutosSumados->format("H:i:s")."\"";
			/*END consulta temporal -- para no tocar placa -- quitar en el futuro y controlar en placa?¿?*/
			
			
			//recorrer historial de esa puerta
			$patron2="SELECT id,fechaalta,horaalta,puerta,idnodo FROM pistaspadel_historial WHERE idnodo=\"%s\" AND accionrealizada=\"n\" AND miradoplaca=\"n\" AND (fechaalta=\"%s\" OR fechaalta=\"%s\") AND (tipo=\"2\" OR tipo=\"5\")%s ORDER BY fechaalta DESC,horaalta DESC, id DESC LIMIT 0,1";
			$sql2=sprintf($patron2,$fila[0],date("Y-m-d"),restaDias(date("Y-m-d"),1),$consultaTemporalHoras);//AND puerta=\"%s\"//,$fila1[0]
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 12234467534245764157454645545680068935");
			if(mysqli_num_rows($respuesta2)>0){
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
					$correcto="s";
				}
				
			}
			mysqli_free_result($respuesta2);
			
			//anotar esta conexion
			$patron199="UPDATE pistaspadel_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
			$sql199=sprintf($patron199,date("Y-m-d"),date("H:i:s"),$fila[0]);
			$respuesta199=mysqli_query($con,$sql199) or die ("Error al editar 454534475637370093456565090");

		}
		mysqli_free_result($respuesta);
	}
	
	echo $correcto;
}

//comprobar si la pads es valida y anotar en historial
if(isset($_POST["comprobarPadsCorrectaAnotarHistorial"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$serial=quitaComillasD($_POST["serial"]);
	$idPinModo=quitaComillasD($_POST["modo"]);
	
	$horaInicioLuz="18:30:00";
	$horaFinLuz="23:59:00";
	$tiempoPartida=0;
	
    if($token!="" && $internal!=""){
        $patron="SELECT id,horainicioluz,horafinluz,tiempopartida FROM pistaspadel_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586454576456453435667677678670689359955");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);

			$horaInicioLuz=$fila[1];
			$horaFinLuz=$fila[2];
			$tiempoPartida=$fila[3];
			
			$modo="n";
			if($idPinModo==99){//modo mantenimiento, libre
				$modo="m";
			}else{
				$modo="n";
			}

			//id puerta
			//puerta(izq-der)
			//pulso o corriente
			// duracion del pulso o corriente segundos
			//h inicio luz
			//h fin luz
			//tiempo partida

			$duracionSegundos=2;
			$pulsoCorriente="p";
			
			//anotar como realizado ese registro
			$patron1="INSERT INTO pistaspadel_historial SET miradoplaca=\"s\",accionrealizada=\"s\",idnodo=\"%s\",idacceso=0,puerta=\"amb\",idusuario=0,tipo=1,modo=\"%s\",horaalta=\"%s\",fechaalta=\"%s\",minutospartida=\"%s\"";
			$sql1=sprintf($patron1,$fila[0],$modo,date("H:i:s"),date("Y-m-d"),$fila[3]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 12345117345565803434656853478966345565746645574546");
			$idHistorialInsert=mysqli_insert_id($con);
			$correcto=$fila[0]."::".$fila[0]."::".$pulsoCorriente."::".$duracionSegundos."::".$horaInicioLuz."::".$horaFinLuz."::".$tiempoPartida."::".$idHistorialInsert;
		}
		
		mysqli_free_result($respuesta);
	}
	
	echo /*"2::izq::p::4@#2::der::p::4::11:00:00::23:59:00::60"*/$correcto;
}

//comprobar cierre forzoso, imantar puertas apagar luces, y sonar audio de vete
if(isset($_POST["comprobarCierreForzosoWeb"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
    if($token!="" && $internal!=""){
        $patron="SELECT id FROM pistaspadel_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586454576456453435667677678670689123359955");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			//recorrer historial de esa puerta
			$patron2="SELECT id FROM pistaspadel_historial WHERE idnodo=\"%s\" AND accionrealizada=\"n\" AND miradoplaca=\"n\" AND (fechaalta=\"%s\" OR fechaalta=\"%s\") AND tipo=\"4\" ORDER BY fechaalta DESC,horaalta DESC, id DESC LIMIT 0,1";
			$sql2=sprintf($patron2,$fila[0],date("Y-m-d"),restaDias(date("Y-m-d"),1));
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 122344677641574545467464554568003456790068935");
			if(mysqli_num_rows($respuesta2)>0){
				//for($j=0;$j<mysqli_num_rows($respuesta2);$j++){
					$fila2=mysqli_fetch_array($respuesta2);
					$correcto=$fila2[0];
				
				//}
			}
			mysqli_free_result($respuesta2);
			
			//anotar esta conexion
			$patron199="UPDATE pistaspadel_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
			$sql199=sprintf($patron199,date("Y-m-d"),date("H:i:s"),$fila[0]);
			$respuesta199=mysqli_query($con,$sql199) or die ("Error al editar 45453447563737009090");
		}
		mysqli_free_result($respuesta);
	}
	
	echo $correcto;
}

//anotar cierre forzado en el  historial ok
if(isset($_POST["anotarCierreForzosoRealizado"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$idHistorial=quitaComillasD($_POST["idHistorial"]);
	
    if($token!="" && $internal!=""){
        $patron="SELECT id FROM pistaspadel_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586452342344576456453435667677678670689123359955");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			//poner como mirado por la placa las acciones anteriores, solamente las anteriores
			$patron2="UPDATE pistaspadel_historial SET miradoplaca=\"s\",accionrealizada=\"s\" WHERE idnodo=\"%s\" AND miradoplaca=\"n\" AND tipo=\"4\" AND id=\"%s\"";
			$sql2=sprintf($patron2,$fila[0],$idHistorial);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error al borrar 1234514345789001046343465234222265746645574546");
			
			$correcto="s";
		}
		mysqli_free_result($respuesta);
	}
	
	echo $correcto;
}

//anotar cierre partida puertas cerradas, tras sonar audio de puertas han sido cerradas, ok
if(isset($_POST["anotarCierrePartidaPuertas"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$idHistorial=intval(quitaComillasD($_POST["idHistorial"]));
	
    if($token!="" && $internal!="" && $idHistorial>0){
        $patron="SELECT id FROM pistaspadel_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 127767586425475677867068912336997655");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			//establecer registro como cerrado, al sonar audio de puertas han sido cerradas
			$patron2="UPDATE pistaspadel_historial SET horacierre=\"%s\",estadocierre=\"s\" WHERE idnodo=\"%s\" AND id=\"%s\"";
			$sql2=sprintf($patron2,date("H:i:s"),$fila[0],$idHistorial);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error al borrar 123451434543457878578408523424576455574546");
			
			$correcto="s";
		}
		mysqli_free_result($respuesta);
	}
	echo $correcto;
}

//cierro la conexion
$con->close();
?>