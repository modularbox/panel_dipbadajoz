<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');

header("Access-Control-Allow-Origin: *");

require_once("../const/constantes.php");
require_once("../phpincludes/phpgeneral.php");
require_once("../phpincludes/phppropios.php");
require_once("../phpincludes/phpsafey.php");

//https://panel.modularbox.com/apisafey/safeynodos.php?abrirCerrarConexion=abrirCerrarConexion&token=F4E3-4v9q-4W5Q5D7N7Q8T8R8&internal=00000001&conexion=1
if(isset($_POST["abrirCerrarConexion"])){

	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$abrirCerrarConexion=quitaComillasD($_POST["conexion"]);
	
    if($token!="" && $internal!="" && ($abrirCerrarConexion=="1" || $abrirCerrarConexion=="2")){
        $patron="SELECT id FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 12234768935");
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
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al editar 156732345");
				
				$correcto="s";
           //}
        }
		mysqli_free_result($respuesta);
    }
   	echo $correcto;
}

//controlar estado puerta
if(isset($_POST["estadoPuertaSafey"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$estadoPuerta=intval(quitaComillasD($_POST["estado"]));
	$idPuerta=intval(quitaComillasD($_POST["idPuerta"]));
	
    if($token!="" && $internal!="" && ($estadoPuerta==0 || $estadoPuerta==1) && $idPuerta>0){
        $patron="SELECT id FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586476893599");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);

			$patron1="SELECT id FROM safey_puertas WHERE borrado=\"n\" AND idnodo=\"%s\" AND id=\"%s\"";
			$sql1=sprintf($patron1,$fila[0],$idPuerta);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error 1223476414556893596");
			if(mysqli_num_rows($respuesta1)>0){
				$fila1=mysqli_fetch_array($respuesta1);

				//estado puerta
				if($estadoPuerta==0){
					$estado=2;//cerrada
				}else if($estadoPuerta==1){
					$estado=1;//abierta
				}else{
					$estado=2;//cerrada por defecto
				}

				$patron2="UPDATE safey_puertas SET estado=\"%s\" WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$estado,$fila1[0]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al editar 156723432345");

				$correcto="s";
			}
			mysqli_free_result($respuesta1);
        }
		mysqli_free_result($respuesta);
    }
   	echo $correcto;
}

//saber configuracion electros puertas
if(isset($_POST["confPuertasElectros"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
    if($token!="" && $internal!=""){
        $patron="SELECT id FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 12236758643566767706893599");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);

			$confSalidas="";
			//muestro ordenado por salida, es decir primero el electro1 y despues el electro2 importante esto, ya que la placa el array usa estas posiciones
			$patron1="SELECT id,salidaplaca,estado FROM safey_puertas WHERE borrado=\"n\" AND idnodo=\"%s\" ORDER BY salidaplaca ASC";
			$sql1=sprintf($patron1,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error 12234764157454645568935");
			if(mysqli_num_rows($respuesta1)>0){
				for($i=0;$i<mysqli_num_rows($respuesta1);$i++){
					$fila1=mysqli_fetch_array($respuesta1);
								   //id puerta // salida placa // estado puerta
					$confSalidas.=$fila1[0].'::'.$fila1[1]."::".$fila1[2];
					if($i<mysqli_num_rows($respuesta1)-1){
						$confSalidas.='@#';
					}
				}
				
				$correcto=$confSalidas;
			}
			mysqli_free_result($respuesta1);
        }
		mysqli_free_result($respuesta);
    }
   	echo $correcto;
}

//consultar si hay algo por arbir
if(isset($_POST["consultarAbrirPuertasWeb"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$tiempoSegundosMargen=2*60;
	$registros="";
	
    if($token!="" && $internal!=""){
        $patron="SELECT id FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 1223675864356676776786706893599");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			//recorrer puertas
			$patron1="SELECT id,pulsocorriente,duracionsegundos FROM safey_puertas WHERE borrado=\"n\" AND idnodo=\"%s\"";
			$sql1=sprintf($patron1,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error 1223446776415743453454645545680676068935");
			if(mysqli_num_rows($respuesta1)>0){
				for($i=0;$i<mysqli_num_rows($respuesta1);$i++){
					$fila1=mysqli_fetch_array($respuesta1);
					
					//pulso o corriente y el tiempo de esa accion
					$pulsoCorriente="p";
					$duracionSegundos=1;
					if($fila1[1]=="p" || $fila1[1]=="c"){
						$pulsoCorriente=$fila1[1];
					}
					if($fila1[2]>0){
						$duracionSegundos=$fila1[2];
					}
					
					//recorrer historial de esa puerta
					$patron2="SELECT id,fechaalta,horaalta FROM safey_historial WHERE idnodo=\"%s\" AND accionrealizada=\"n\" AND miradoplaca=\"n\" AND (fechaalta=\"%s\" OR fechaalta=\"%s\") AND tipo=\"2\" AND idpuerta=\"%s\" ORDER BY fechaalta DESC,horaalta DESC, id DESC LIMIT 0,1";
					$sql2=sprintf($patron2,$fila[0],date("Y-m-d"),restaDias(date("Y-m-d"),1),$fila1[0]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error 12234467764157454645545680068935");
					if(mysqli_num_rows($respuesta2)>0){
						if($i>0 && $registros!=""){
							$registros.='@#';
						}
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
										//id historial//id puerta//pulso o corriente// duracion del pulso o corriente segundos
								$registros.=$fila2[0]."::".$fila1[0]."::".$pulsoCorriente."::".$duracionSegundos;
							}
						}
					}
					mysqli_free_result($respuesta2);
				}
				if($registros!=""){
					$correcto=$registros;//respuesta
				}
			}
			mysqli_free_result($respuesta1);
		}
	}
	echo /*"1::9@#2::23::p::4"*/$correcto;//texto
}


//consultar si hay algo por arbir desde el lector pines
if(isset($_POST["comprobarSiHayAlgoWeb"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$tiempoSegundosMargen=2*60;
    if($token!="" && $internal!=""){
        $patron="SELECT id FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 12236758645457345346456453435667677678670689359955");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			//recorrer historial de esa puerta
			$patron2="SELECT id,fechaalta,horaalta,idpuerta,idnodo FROM safey_historial WHERE idnodo=\"%s\" AND accionrealizada=\"n\" AND miradoplaca=\"n\" AND (fechaalta=\"%s\" OR fechaalta=\"%s\") AND (tipo=\"2\" OR tipo=\"3\") ORDER BY fechaalta DESC,horaalta DESC, id DESC LIMIT 0,1";
			$sql2=sprintf($patron2,$fila[0],date("Y-m-d"),restaDias(date("Y-m-d"),1));
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
			$patron199="UPDATE safey_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
			$sql199=sprintf($patron199,date("Y-m-d"),date("H:i:s"),$fila[0]);
			$respuesta199=mysqli_query($con,$sql199) or die ("Error al editar 454534475632373700934565650909");

		}
		mysqli_free_result($respuesta);
	}
	echo $correcto;
}

//anotar en el historial aperturaweb puertas
if(isset($_POST["anotarAperturaPuertaHistWeb"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$idHistorial=quitaComillasD($_POST["idHistorial"]);
	$idPuerta=quitaComillasD($_POST["idPuerta"]);

    if($token!="" && $internal!="" && $idHistorial>0 && $idPuerta>0){
        $patron="SELECT id FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586435667677678234236706893599");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			//comprobar ese registro del historial
			$patron3="SELECT id,miradoplaca,accionrealizada,idpuerta,miradoplaca FROM safey_historial WHERE id=\"%s\" AND idnodo=\"%s\" AND idpuerta=\"%s\"";
			$sql3=sprintf($patron3,$idHistorial,$fila[0],$idPuerta);
			$respuesta3=mysqli_query($con,$sql3) or die ("Error 1223633758643566767337678234236706893599");
			if(mysqli_num_rows($respuesta3)>0){
				$fila3=mysqli_fetch_array($respuesta3);

				//anotar como realizado ese registro
				$patron1="UPDATE safey_historial SET miradoplaca=\"s\",accionrealizada=\"s\" WHERE id=\"%s\" AND idpuerta=\"%s\" AND idnodo=\"%s\" AND miradoplaca=\"n\"";
				$sql1=sprintf($patron1,$idHistorial,$idPuerta,$fila[0]);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 123451103434656853478966345565746645574546");

				//poner como mirado por la placa las acciones anteriores, solamente las anteriores
				$patron2="UPDATE safey_historial SET miradoplaca=\"s\" WHERE idpuerta=\"%s\" AND idnodo=\"%s\" AND miradoplaca=\"n\" AND id<\"%s\"";
				$sql2=sprintf($patron2,$idPuerta,$fila[0],$fila3[0]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al borrar 12345110343465222265746645574546");

				$correcto="s";
			}
			mysqli_free_result($respuesta3);

		}
		mysqli_free_result($respuesta);
	}
	echo $correcto;
}

//devolver conjunto accesos json
//https://panel.modularbox.com/apisafey/safeynodos.php?devolverLlavesKey=devolverLlavesKey&token=F4E3-4v9q-4W5Q5D7N7Q8T8R8&internal=00000001
if(isset($_POST["devolverLlavesKey"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	//$idPuerta=quitaComillasD($_POST["idPuerta"]);

    if($token!="" && $internal!=""  /*&& $idPuerta>0*/){
		
		$idPinCredencial=0;
		$idLlaveCredencial=0;
		$idMandoCredencial=0;
		
        $patron="SELECT id FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586435667600077678234236706893599");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			//recorrer los acessos nodos
			$jsonKeys="";
			$patron1="SELECT idacceso FROM safey_accesosnodos WHERE borrado=\"n\" AND nodo=\"%s\" GROUP BY idacceso";
			$sql1=sprintf($patron1,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96323461133344545711658997");
			if(mysqli_num_rows($respuesta1)>0){
				for($i=0;$i<mysqli_num_rows($respuesta1);$i++){
					$fila1=mysqli_fetch_array($respuesta1);
					
					//
					
					//obtener los id
					$patron2="SELECT id,pin,pinactivo,llave,llaveactivo,mando,mandoactivo FROM safey_accesos WHERE borrado=\"n\" AND id=\"%s\"";
					$sql2=sprintf($patron2,$fila1[0]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 9632346121333424545721165800");
					if(mysqli_num_rows($respuesta2)>0){
						$fila2=mysqli_fetch_array($respuesta2);
						
						//pin
						$entraPinSeparador=false;
						if($fila2[2]=="on"){
							$idPinCredencial=$fila2[1];
							
							$entraPinSeparador=true;
							
							if($i>0){
								$jsonKeys.='@#';
							}
							
							$patron3="SELECT id,pin,pinserie,pinserial FROM safey_credenciales_pin WHERE borrado=\"n\" AND id=\"%s\"";
							$sql3=sprintf($patron3,$idPinCredencial);
							$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 9632346121333333334245457211658030");
							if(mysqli_num_rows($respuesta3)>0){
								$fila3=mysqli_fetch_array($respuesta3);
								
								$jsonKeys.='{"status": 1, "id": 0, "serial": "'.$fila3[3].'", "serie": "'.$fila3[2].'", "alias": "'.$fila3[1].'"}';
							}
							mysqli_free_result($respuesta3);
						}
						
						//llave
						$entraLlaveSeparador=false;
						if($fila2[4]=="on"){
							$idLlaveCredencial=$fila2[3];
							
							$entraLlaveSeparador=true;
							
							if($i>0 || $entraPinSeparador){
								$jsonKeys.='@#';
							}
							
							$patron4="SELECT id,llaveserie,llavepinserial,descripcion FROM safey_credenciales_llaves WHERE borrado=\"n\" AND id=\"%s\"";
							$sql4=sprintf($patron4,$idLlaveCredencial);
							$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 9632346121344333342454572116584040");
							if(mysqli_num_rows($respuesta4)>0){
								$fila4=mysqli_fetch_array($respuesta4);
								
								$jsonKeys.='{"status": 1, "id": 0, "serial": "'.$fila4[2].'", "serie": "'.$fila4[1].'", "alias": "'.$fila4[3].'"}';
							}
							mysqli_free_result($respuesta4);
						}
						
						//mando
						if($fila2[6]=="on"){
							/*$idMandoCredencial=$fila2[5];
							
							if($i>0 || $entraPinSeparador || $entraLlaveSeparador){
								$jsonKeys.='@#';
							}
							
							$patron5="SELECT id,mandoserie,mandoserial FROM safey_credenciales_mandos WHERE borrado=\"n\" AND id=\"%s\"";
							$sql5=sprintf($patron5,$idMandoCredencial);
							$respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 963234612155355333424545721165840550");
							if(mysqli_num_rows($respuesta5)>0){
								$fila5=mysqli_fetch_array($respuesta5);
								//$jsonKeys.='{"status": 1, "id": 0, "serial": "'.$fila4[2].'", "serie": "'.$fila4[1].'", "alias": "'.$fila4[3].'"}';
							}
							mysqli_free_result($respuesta5);*/
						}
						
					}
					mysqli_free_result($respuesta2);
				}
			}
			mysqli_free_result($respuesta1);
			
			//ejemplo//$jsonKeys='{"status": 1, "id": 0, "serial": "a6000c0100f0313131310000005b", "serie": "31313100", "alias": "1111-pin"}@#{"status": 1, "id": 0, "serial": "a6000c0100f0313131310000005b", "serie": "31313100", "alias": "3333-pin"}';
			
			$correcto=$jsonKeys;
		}
		mysqli_free_result($respuesta);
	}
	echo $correcto;
}


//devolver conjunto accesos json
//https://panel.modularbox.com/apisafey/safeynodos.php?devolverLlavesKey_V2=devolverLlavesKey_V2&token=F4E3-4v9q-4W5Q5D7N7Q8T8FD&internal=00000001
if(isset($_POST["devolverLlavesKey_V2"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	//$idPuerta=quitaComillasD($_POST["idPuerta"]);

    if($token!="" && $internal!=""  /*&& $idPuerta>0*/){
		
		$idPinCredencial=0;
		$idLlaveCredencial=0;
		$idMandoCredencial=0;
		
        $patron="SELECT id FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586435667600077678234236706893599");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			$idNodo=intval($fila[0]);
			
			//recorrer los acessos nodos
			$jsonKeys="";
			$patron1="SELECT idacceso FROM safey_accesosnodos WHERE borrado=\"n\" AND nodo=\"%s\" GROUP BY idacceso";
			$sql1=sprintf($patron1,$idNodo);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96323461133344545711658997");
			if(mysqli_num_rows($respuesta1)>0){
				for($i=0;$i<mysqli_num_rows($respuesta1);$i++){
					$fila1=mysqli_fetch_array($respuesta1);
					
					
					//obtener los id
					$patron2="SELECT id,pin,pinactivo,llave,llaveactivo,mando,mandoactivo FROM safey_accesos WHERE borrado=\"n\" AND id=\"%s\"";
					$sql2=sprintf($patron2,$fila1[0]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 9632346121333424545721165800");
					if(mysqli_num_rows($respuesta2)>0){
						$fila2=mysqli_fetch_array($respuesta2);
						$idAcceso=intval($fila2[0]);
						
						
						/*START obtener para cada nodo y para este acceso la configuracion horaria de acceso*/
						$jsonConfPuertasHorario='';
						$patron311="SELECT safey_accesosnodos.id,safey_accesosnodos.puerta,safey_accesosnodos.permisos,safey_accesosnodos.l,safey_accesosnodos.m,safey_accesosnodos.x,safey_accesosnodos.j,safey_accesosnodos.v,safey_accesosnodos.s,safey_accesosnodos.d,safey_accesosnodos.horade,safey_accesosnodos.horahasta FROM safey_accesosnodos,safey_puertas WHERE safey_accesosnodos.borrado=\"n\" AND safey_accesosnodos.idacceso=\"%s\" AND safey_accesosnodos.nodo=\"%s\" AND safey_puertas.id=safey_accesosnodos.puerta AND safey_puertas.borrado=\"n\" AND safey_puertas.idnodo=\"%s\" ORDER BY safey_accesosnodos.puerta ASC";
						$sql311=sprintf($patron311,$idAcceso,$idNodo,$idNodo);
						$respuesta311=mysqli_query($con,$sql311) or die ("Error al buscar 96323461213111317211658030");
						if(mysqli_num_rows($respuesta311)>0){
							$jsonConfPuertasHorario=',"config":[';//crear el inicio del json hijo
							$contadorPuerta=1;
							for($t=0;$t<mysqli_num_rows($respuesta311);$t++){
								$fila311=mysqli_fetch_array($respuesta311);

								//contenido json hijo, separar una puerta de otra
								if($t>0){
									$jsonConfPuertasHorario.=',';
								}

								//contador puerta 1-2
								if(($t%2)==0){
									//Es un número par
									$contadorPuerta=1;
								}else{
									//Es un número impar
									$contadorPuerta=2;
								}

								//dias semana
								$diasSemanaAcceso="";
								if($fila311[3]=="s"){
									$diasSemanaAcceso.="l";
								}
								if($fila311[4]=="s"){
									$diasSemanaAcceso.="m";
								}
								if($fila311[5]=="s"){
									$diasSemanaAcceso.="x";
								}
								if($fila311[6]=="s"){
									$diasSemanaAcceso.="j";
								}
								if($fila311[7]=="s"){
									$diasSemanaAcceso.="v";
								}
								if($fila311[8]=="s"){
									$diasSemanaAcceso.="s";
								}
								if($fila311[9]=="s"){
									$diasSemanaAcceso.="d";
								}

								$jsonConfPuertasHorario.='{"puerta":"'./*$fila311[1]*/$contadorPuerta.'","horario":[{"dias":"'.$diasSemanaAcceso.'", "inicio":"'.$fila311[10].'", "fin":"'.$fila311[11].'"}]}';
							}
							$jsonConfPuertasHorario.=']';//cerrar el inicio del json hijo
						}
						mysqli_free_result($respuesta311);
						/*END obtener para cada nodo y para este acceso la configuracion horaria de acceso*/
						//echo $jsonConfPuertasHorario."<br><br>";
						
						
						//pin
						$entraPinSeparador=false;
						if($fila2[2]=="on"){
							$idPinCredencial=$fila2[1];
							
							$entraPinSeparador=true;
							
							if($i>0){
								$jsonKeys.='@#';
							}
							
							/*START datos del pin*/
							$patron3="SELECT id,pin,pinserie,pinserial FROM safey_credenciales_pin WHERE borrado=\"n\" AND id=\"%s\"";
							$sql3=sprintf($patron3,$idPinCredencial);
							$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 9632346121333333334245457211658030");
							if(mysqli_num_rows($respuesta3)>0){
								$fila3=mysqli_fetch_array($respuesta3);
								
								$jsonKeys.='{"status": 1, "id": 0, "serial": "'.$fila3[3].'", "serie": "'.$fila3[2].'", "alias": "'.$fila3[1].'"'.$jsonConfPuertasHorario.'}';
							}
							mysqli_free_result($respuesta3);
							/*END datos del pin*/
						}
						
						//llave
						$entraLlaveSeparador=false;
						if($fila2[4]=="on"){
							$idLlaveCredencial=$fila2[3];
							
							$entraLlaveSeparador=true;
							
							if($i>0 || $entraPinSeparador){
								$jsonKeys.='@#';
							}
							
							$patron4="SELECT id,llaveserie,llavepinserial,descripcion FROM safey_credenciales_llaves WHERE borrado=\"n\" AND id=\"%s\"";
							$sql4=sprintf($patron4,$idLlaveCredencial);
							$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 9632346121344333342454572116584040");
							if(mysqli_num_rows($respuesta4)>0){
								$fila4=mysqli_fetch_array($respuesta4);
								
								$jsonConfPuertasHorario=',"config":[{"puertas":"1,2", "dias":"l,m,s,d", "inicio":"00:01", "fin":"23:59"}]';
								
								$jsonKeys.='{"status": 1, "id": 0, "serial": "'.$fila4[2].'", "serie": "'.$fila4[1].'", "alias": "'.$fila4[3].'"'.$jsonConfPuertasHorario.'}';
							}
							mysqli_free_result($respuesta4);
						}
						
						//mando
						if($fila2[6]=="on"){
							/*$idMandoCredencial=$fila2[5];
							
							if($i>0 || $entraPinSeparador || $entraLlaveSeparador){
								$jsonKeys.='@#';
							}
							
							$patron5="SELECT id,mandoserie,mandoserial FROM safey_credenciales_mandos WHERE borrado=\"n\" AND id=\"%s\"";
							$sql5=sprintf($patron5,$idMandoCredencial);
							$respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 963234612155355333424545721165840550");
							if(mysqli_num_rows($respuesta5)>0){
								$fila5=mysqli_fetch_array($respuesta5);
								
								//$jsonKeys.='{"status": 1, "id": 0, "serial": "'.$fila4[2].'", "serie": "'.$fila4[1].'", "alias": "'.$fila4[3].'"'.$jsonConfPuertasHorario.'}';
							}
							mysqli_free_result($respuesta5);*/
						}
						
					}
					mysqli_free_result($respuesta2);
				}
			}
			mysqli_free_result($respuesta1);
			
			//ejemplo//$jsonKeys='{"status": 1, "id": 0, "serial": "a6000c0100f0313131310000005b", "serie": "31313100", "alias": "1111-pin"}@#{"status": 1, "id": 0, "serial": "a6000c0100f0313131310000005b", "serie": "31313100", "alias": "3333-pin"}';
			
			$correcto=$jsonKeys;
		}
		mysqli_free_result($respuesta);
	}
	echo $correcto;
}

//https://panel.modularbox.com/panel/apisafey/safeynodos.php?anotarAperturaPuertaHistPlaca=anotarAperturaPuertaHistPlaca&token=F4E3-4v9q-4W5Q5D7N7Q8T8R8&internal=00000001&malBien=2&idPuerta=99
if(isset($_POST["anotarAperturaPuertaHistPlaca"])){
	
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$serial=quitaComillasD($_POST["padsUsada"]);
	$idPuerta=quitaComillasD($_POST["idPuerta"]);
	$malBien=intval(quitaComillasD($_POST["malBien"]));
	
	
	/*bien mal
	1 bien
	2 mal, pin mal
	3 mal por fuera de horario dia
	4 mal por fuera de horario hora
	5 mal por fuera de horario puerta 
	6 mal por pin desactivado, en el momento de usar
	*/
	
	if($token!="" && $internal!="" && $idPuerta>0 && ($malBien==1 || $malBien==2 || $malBien==3 || $malBien==4 || $malBien==5 || $malBien==6)){
        $patron="SELECT id,idusuario FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367586435667677635678234236706893599");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
	
			if($malBien==1){//anotar en bbdd como ok
				//saber puerta usada en el pin
				$patron2="SELECT id FROM safey_puertas WHERE idnodo=\"%s\" AND salidaplaca=\"1\"";
				$sql2=sprintf($patron2,$fila[0]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error 122367586435667677635622227823423670689359009");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$idPuerta=$fila2[0];
				}
				mysqli_free_result($respuesta2);

				//saber el idacceso del pin usado
				$idAcceso=-99;
				$idUsuario=-99;
				$patron4="SELECT idacceso FROM safey_accesosnodos WHERE nodo=\"%s\"";
				$sql4=sprintf($patron4,$fila[0]);
				$respuesta4=mysqli_query($con,$sql4) or die ("Error 12236758643333544466767763562222782342367440689359339");
				if(mysqli_num_rows($respuesta4)>0){
					for($i=0;$i<mysqli_num_rows($respuesta);$i++){
						$fila4=mysqli_fetch_array($respuesta4);

						/*$patron5="SELECT pin FROM safey_accesos WHERE id=\"%s\"";
						$sql5=sprintf($patron5,$fila4[0]);
						$respuesta5=mysqli_query($con,$sql5) or die ("Error 1223675864555333356676776356222278234235567550689359339");
						if(mysqli_num_rows($respuesta5)>0){
							$fila5=mysqli_fetch_array($respuesta5);
						*/
							//saber el pin
							$patron3="SELECT idacceso,idusuario FROM safey_credenciales_pin WHERE pinserial=\"%s\" AND idusuario=\"%s\" ";
							$sql3=sprintf($patron3,$serial,$fila[1]);
							$respuesta3=mysqli_query($con,$sql3) or die ("Error 122367586433335667677635622227823423670689359339");
							if(mysqli_num_rows($respuesta3)>0){
								$fila3=mysqli_fetch_array($respuesta3);
								
								$idAcceso=$fila3[0];
								$idUsuario=$fila3[1];
							}
							mysqli_free_result($respuesta3);
						/*}
						mysqli_free_result($respuesta5);*/
					}
				}
				mysqli_free_result($respuesta4);

				//anotar como realizado
				$patron1="INSERT INTO safey_historial SET miradoplaca=\"s\",accionrealizada=\"s\",tipo=\"%s\",idnodo=\"%s\",idpuerta=\"%s\",idacceso=\"%s\",idusuario=\"%s\",padsusadaserial=\"%s\",horaalta=\"%s\",fechaalta=\"%s\"";
				$sql1=sprintf($patron1,1,$fila[0],$idPuerta,$idAcceso,$idUsuario,$serial,date("H:i:s"),date("Y-m-d"));
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 12345110343445553656853478966345565746645574546");
			}else if($malBien==2 || $malBien==3 || $malBien==4 || $malBien==5 || $malBien==6){//anotar hisotiral bbdd como ko
				
				//hacer un select a los pines de ese cliente y ver los fallidos
				$idAccesoCliente=0;
				$tipo=0;
				
				//pines
				$idPinAcceso=0;
				$patron10="SELECT idacceso,idusuario,idacceso FROM safey_credenciales_pin WHERE pinserial=\"%s\" AND idusuario=\"%s\" ";
				$sql10=sprintf($patron10,$serial,$fila[1]);
				$respuesta10=mysqli_query($con,$sql10) or die ("Error 1223675867106764356610767763510678234236706893599");
				if(mysqli_num_rows($respuesta10)>0){
					$fila10=mysqli_fetch_array($respuesta10);
					$idAccesoCliente=$fila10[0];
					$tipo=1;
					$idPinAcceso=$fila10[2];
				}
				mysqli_free_result($respuesta10);
				//llaves
				$patron11="SELECT idacceso,idusuario FROM safey_credenciales_llaves WHERE llavepinserial=\"%s\" AND idusuario=\"%s\" ";
				$sql11=sprintf($patron11,$serial,$fila[1]);
				$respuesta11=mysqli_query($con,$sql11) or die ("Error 1223675867106761076182342367068919");
				if(mysqli_num_rows($respuesta11)>0){
					$fila11=mysqli_fetch_array($respuesta11);
					$idAccesoCliente=$fila11[0];
					$tipo=4;
				}
				mysqli_free_result($respuesta11);
				
				/*START guardar un 6 en bien mal, para pin desactivado*/
				if($idPinAcceso>0){
					$patron4="SELECT pinactivo FROM safey_accesos WHERE id=\"%s\"";
					$sql4=sprintf($patron4,$idPinAcceso);
					$respuesta4=mysqli_query($con,$sql4) or die ("Error 1223675865658567763562222782342367440689359339");
					if(mysqli_num_rows($respuesta4)>0){
						//for($l=0;$l<mysqli_num_rows($respuesta);$l++){
							$fila4=mysqli_fetch_array($respuesta4);

							if($fila4[0]!="on"){
								$malBien=6;//error pin desactivado
							}
						//}
					}
					mysqli_free_result($respuesta4);
				}
				/*END guardar un 6 en bien mal, para pin desactivado*/
                
				//anotar un nuevo registro como mal, ko
				$patron3="INSERT INTO safey_historial_fallidos SET tipo=\"%s\",idnodo=\"%s\",horaalta=\"%s\",fechaalta=\"%s\",idaccesocliente=\"%s\",serial=\"%s\",tipoerror=\"%s\" ";
				$sql3=sprintf($patron3,$tipo,$fila[0],date("H:i:s"),date("Y-m-d"),$idAccesoCliente,$serial,$malBien);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error al borrar 123451103434656853473333565746645574546");
			}

			$correcto="s";
		}
		mysqli_free_result($respuesta);
	}
	echo $correcto;
}

//cierro la conexion
$con->close();
?>