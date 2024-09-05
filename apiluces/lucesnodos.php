<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');
		
header("Access-Control-Allow-Origin: *");

require_once("../const/constantes.php");
require_once("../phpincludes/phpgeneral.php");
require_once("../phpincludes/phppropios.php");
require_once("../phpincludes/phpluces.php");

//devolver json programa aplica ahora
//https://panel.modularbox.com/apiluces/lucesnodos.php?returnProgramLuzAplica=aa&internal=00000001
if(isset($_GET["returnProgramLuzAplica"])){
	
	$sequence='';
	$schedule='';
	$error='{"error":true}';
	
	//$token=quitaComillasD($_POST["token"]);//sin uso de momento
	$internal=quitaComillasD($_GET["internal"]);
	
    if($internal!=""){
		
		$patron="SELECT luces_nodos.id,luces_nodos.schedule,usuarios.id FROM luces_nodos,usuarios WHERE luces_nodos.borrado=\"n\" AND luces_nodos.guardado=\"s\" AND luces_nodos.internal=\"%s\" AND usuarios.id=luces_nodos.idusuario AND usuarios.borrado=\"n\" AND usuarios.guardado=\"s\"";
        $sql=sprintf($patron,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 12356564677");
        if(mysqli_num_rows($respuesta)>0){
        	$fila=mysqli_fetch_array($respuesta);
			
			//SCHEDULE // tabla nodo 
			//$schedule='{"init":"01:00:00","finish":"23:00:00","days":[0,1,2,3,4,5,6]}';
			$schedule=$fila[1];
			
			//PROGRAMA//tabla programa
			$diaSemana=date("N");
			$hora=date("H:i:s");
			$patron2="SELECT luces_horarios_programas_conf.programa FROM luces_nodos,luces_horarios,luces_horarios_nodos,luces_horarios_programas_conf WHERE luces_nodos.id=luces_horarios_nodos.nodo AND luces_horarios.id=luces_horarios_nodos.horario AND luces_horarios.id=luces_horarios_programas_conf.horario AND luces_nodos.id=\"%s\" AND luces_horarios_programas_conf.diasemana=\"%s\" AND luces_horarios_programas_conf.horade<=\"%s\" AND luces_horarios_programas_conf.horahasta>=\"%s\"";
			$sql2=sprintf($patron2,$fila[0],$diaSemana,$hora,$hora);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error 1235656224645777");
			if(mysqli_num_rows($respuesta2)>0){
				//for($i=0;$i<mysqli_num_rows($respuesta2);$i++){
					$fila2=mysqli_fetch_array($respuesta2);
					
					$patron3="SELECT id,sequence FROM luces_programas WHERE idusuario=\"%s\" AND borrado=\"n\" AND guardado=\"s\"";
					$sql3=sprintf($patron3,$fila[2]);
					$respuesta3=mysqli_query($con,$sql3) or die ("Error 123577975656224677");
					if(mysqli_num_rows($respuesta3)>0){
						$fila3=mysqli_fetch_array($respuesta3);
						//$sequence=$fila3[1];
						$sequence=generarSequencePrograma($fila3[0],$fila[0],$con);
					}
					mysqli_free_result($respuesta3);
				//}
			}
			mysqli_free_result($respuesta2);
			
			//sequence//mensaje//tabla program //ejemplo
			//$sequence='{"sequences":[{"on":[1,109,26,86,27,28,87,88,32,92,35,95,36,37,96,97],"delay":500},{"on":[1,109,4,64,26,86,27,28,87,88,32,92,35,95,36,37,96,97],"delay":500},{"on":[1,109,4,64,7,67,26,86,27,28,87,88,32,92,35,95,36,37,96,97],"delay":500},{"on":[1,109,4,64,7,67,10,70,26,86,27,28,87,88,32,92,35,95,36,37,96,97],"delay":500},{"on":[1,109,4,64,7,67,10,70,13,73,26,86,27,28,87,88,32,92,35,95,36,37,96,97],"delay":500},{"on":[1,109,4,64,7,67,10,70,13,73,16,76,26,86,27,28,87,88,32,92,35,95,36,37,96,97],"delay":500},{"on":[1,109,4,64,7,67,10,70,13,73,16,76,49,79,26,86,27,28,87,88,32,92,35,95,36,37,96,97],"delay":500},{"on":[1,109,4,64,7,67,10,70,13,73,16,76,49,79,22,82,26,86,27,28,87,88,32,92,35,95,36,37,96,97],"delay":500},{"on":[26,86,27,28,87,88,32,92,35,95,36,37,96,97],"delay":500},{"on":[26,86,27,28,87,88,32,92,35,95,36,37,96,97],"delay":500}],"created": "2020-01-01 10:10:00"}';
			
			//ejemplo SAUCEDILLA PRUEBAS
			if($internal=="00000003"){
				/*$sequence='{"sequences":[{"on":[';
				for($t=0;$t<255;$t++){
					$sequence.=$t.",";
				}
				$sequence=substr($sequence, 0, -1);//quitar ultima coma
				$sequence.='],"created": "2020-01-01 12:00:00"}';*/
				$sequence='{"sequences":[{"on":[1,2,3],"delay":15000}],"created": "2020-01-01 10:10:12"}';
			}
			

			//verde y azul normal//ejemplo
			//$sequence='{"sequences":[{"on":[1,109,4,64,7,67,10,70,13,73,16,76,49,79,22,82,26,86,27,28,87,88,32,92,35,95,38,98,41,101,44,104,45,46,105,106],"delay":15000}],"created": "2020-01-01 10:10:12"}';
			
			//error// segun comprobaciones
			$error='{"error":false}';

			//update
			$patron1="UPDATE luces_nodos SET conexion=\"on\",ultimaconexion=\"%s\",horaultimaconexion=\"%s\" WHERE id=\"%s\"";
			$sql1=sprintf($patron1,date("Y-m-d"),date("H:i:s"),$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 12345634789");
			
		}
		mysqli_free_result($respuesta);
	}
	
	//return
	exit(json_encode(array(
			'error' => false,
			'message' => /*json_encode(*/$sequence/*)*/,
			'schedule' => $schedule
		)));

}

//https://panel.modularbox.com/apiluces/lucesnodos.php?returnProgramLuzGenerico=aa&token=9C6G-3x8e-Z7R9C9Y9W8Q7B2N&internal=09900002
//&diaConsulta=L
//&horaConsulta=12:24
if(isset($_GET["returnProgramLuzGenerico"])){
	
    //$correcto="n";
	$token=quitaComillasD($_GET["token"]);//obligatorio
	$internal=quitaComillasD($_GET["internal"]);//obligatorio
	
	$auxDiaConsulta="";
	$auxHoraConsulta="";
	
	//dia semana a consultar
	if(isset($_GET["diaConsulta"])){
		$auxDiaConsulta=quitaComillasD($_GET["diaConsulta"]);//optativos 
	}else{
		$auxDiaConsulta=date("N");
	}
	//hora a consultar del dia dado
	if(isset($_GET["horaConsulta"])){
		$auxHoraConsulta=quitaComillasD($_GET["horaConsulta"]);//optativos
	}
	
	$arrayCompletoJson=array();//json a devolver
	if($token!="" && $internal!=""){
		
		//validar dia semana
		$diaConsulta="";
		if($_GET["diaConsulta"]=="L" || $_GET["diaConsulta"]=="M" || $_GET["diaConsulta"]=="X" || $_GET["diaConsulta"]=="J" || $_GET["diaConsulta"]=="V" || $_GET["diaConsulta"]=="S" || $_GET["diaConsulta"]=="D"){
			$diaConsulta=pasarLetraDiaSemanaNumDiaSemana($auxDiaConsulta);//en letra
		}else{
			$diaConsulta=date("N");
		}
		
		//validar hora
		$horaConsulta="";
		if($auxHoraConsulta!="" && strlen($auxHoraConsulta)>=5 && strlen($auxHoraConsulta<=8)){
			if(strpos($auxHoraConsulta, ':') !== false) {
				//ok
				$horaConsulta=" AND luces_horarios_programas_conf.horade>=\"".$auxHoraConsulta."\"";// AND luces_horarios_programas_conf.horahasta<=\"23:59:59\"
			}else{
				//ko
			}
		}
		
		/*START devolver conf. programas nodo*/
		$patron="SELECT luces_nodos.id,luces_nodos.schedule,usuarios.id,luces_nodos.modo FROM luces_nodos,usuarios WHERE luces_nodos.borrado=\"n\" AND luces_nodos.guardado=\"s\" AND luces_nodos.token=\"%s\" AND luces_nodos.internal=\"%s\" AND usuarios.id=luces_nodos.idusuario AND usuarios.borrado=\"n\" AND usuarios.guardado=\"s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 12352346345346564677");
        if(mysqli_num_rows($respuesta)>0){
        	$fila=mysqli_fetch_array($respuesta);
			
			//anadir al json en la posicion [0], el id del nodo
			array_push($arrayCompletoJson, $fila[0]);
				
			$modoNodo=$fila[3];
			
			/*start mostrar conf. del programa*/
			$arrayListadoProgramas=array();//crear el array listado de programas
			
			//el horario que aplique este nodo
			$patron1="SELECT horario FROM luces_horarios_nodos WHERE nodo=\"%s\"";
			$sql1=sprintf($patron1,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error 123565622456516767793454511645345777");
			if(mysqli_num_rows($respuesta1)>0){
				for($i=0;$i<mysqli_num_rows($respuesta1);$i++){
					$fila1=mysqli_fetch_array($respuesta1);
					$idHorario=$fila1[0];
					
					//tengo las diferentes lineas de configuracion dentro de un mismo horario
					$patron2="SELECT programa,diasemana,horade,horahasta FROM luces_horarios_programas_conf WHERE horario=\"%s\" AND luces_horarios_programas_conf.diasemana=\"%s\"%s";
					$sql2=sprintf($patron2,$idHorario,$diaConsulta,$horaConsulta);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error 123565622456567677934545645345777");
					if(mysqli_num_rows($respuesta2)>0){
						for($x=0;$x<mysqli_num_rows($respuesta2);$x++){
							$fila2=mysqli_fetch_array($respuesta2);
							
							$arrayConfPrograma=array();//crear el json de conf. de los programas en si
							
							$idPrograma=$fila2[0];
							$diaSemanaPrograma=$fila2[1];
							$horaDe=$fila2[2];
							$horaHasta=$fila2[3];

							//crear json del programa, la configuracion del programa[idPrograma,diaSemana,HORAINICIO,HORAFIN,[lucesconf]],
							array_push($arrayConfPrograma, $idPrograma);//id del programa
							array_push($arrayConfPrograma, pasarNumDiaSemanaLetraDiaSemana($diaSemanaPrograma));//dia de la semana del programa, en letra
							array_push($arrayConfPrograma, $horaDe);//hora inicio
							array_push($arrayConfPrograma, $horaHasta);//hora fin
							array_push($arrayConfPrograma, $modoNodo);//modo programa //0-->nada 1-->automatico 2-->manual
							
							/*start obtener datos del programa*/
							$numeroFocosActivosPrograma=0;
							$patron3="SELECT id,sequence,numfocos,tipofococolum1,tipofococolum2,tipofococolum3,tipofococolum4,tipofococolum5,tipofococolum6,tipofococolum7,tipofococolum8,tipofococolum9,tipofococolum10,tipofococolum11,tipofococolum12,tipofococolum13,tipofococolum14,tipofococolum15,tipofococolum16,tipofococolum17,tipofococolum18,tipofococolum19,tipofococolum20 FROM luces_programas WHERE idusuario=\"%s\" AND borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
							$sql3=sprintf($patron3,$fila[2],$idPrograma);
							$respuesta3=mysqli_query($con,$sql3) or die ("Error 123577954654654623475656224677");
							if(mysqli_num_rows($respuesta3)>0){
								$fila3=mysqli_fetch_array($respuesta3);
								$numeroFocosActivosPrograma=intval($fila3[2]);
								
								/*START id tipos de focos de cada elemento foco*/
								$arrayTipoFocosPosiciones[]=$tipoFocoColum1=intval($fila3[3]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum2=intval($fila3[4]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum3=intval($fila3[5]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum4=intval($fila3[6]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum5=intval($fila3[7]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum6=intval($fila3[8]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum7=intval($fila3[9]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum8=intval($fila3[10]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum9=intval($fila3[11]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum10=intval($fila3[12]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum11=intval($fila3[13]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum12=intval($fila3[14]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum13=intval($fila3[15]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum14=intval($fila3[16]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum15=intval($fila3[17]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum16=intval($fila3[18]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum17=intval($fila3[19]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum18=intval($fila3[20]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum19=intval($fila3[21]);
								$arrayTipoFocosPosiciones[]=$tipoFocoColum20=intval($fila3[22]);
								/*END id tipos de focos de cada elemento foco*/
								
								//montar el array de la propia configuracion luces
								//$arrayConfiguracionLuces=array();

								/*start recorrer las lineas del programa*/
								$patron4="SELECT id,temporizacion,colorcolumuno,colorcolumdos,colorcolumtres,colorcolumcuatro,colorcolumcinco,colorcolumseis,colorcolumsiete,colorcolumocho,colorcolumnueve,colorcolumdiez,colorcolumonce,colorcolumdoce,colorcolumtrece,colorcolumcatorce,colorcolumquince,colorcolumdieciseis,colorcolumdiecisiete,colorcolumdieciocho,colorcolumdiecinueve,colorcolumveinte FROM luces_filasprograma WHERE programa=\"%s\" AND borrado=\"n\"";
								$sql4=sprintf($patron4,$idPrograma);
								$respuesta4=mysqli_query($con,$sql4) or die ("Error 1235774954654454623447564562244677");
								if(mysqli_num_rows($respuesta4)>0){
									for($j=0;$j<mysqli_num_rows($respuesta4);$j++){
										$fila4=mysqli_fetch_array($respuesta4);
                                        
                                        //montar el array de la propia configuracion luces, lo declaro aqui para limpiar no arriba
										$arrayConfiguracionLuces=array();
                                        
										/*start conocer temporizacion*/
										$idTemporizacion=$fila4[1];
										$temporizacion=1;//segundos
										$patron6="SELECT tiemposegundos FROM temporizacion WHERE id=\"%s\"";
										$sql6=sprintf($patron6,$idTemporizacion);
										$respuesta6=mysqli_query($con,$sql6) or die ("Error 1235345665554454623447555244677");
										if(mysqli_num_rows($respuesta6)>0){
											$fila6=mysqli_fetch_array($respuesta6);
											$temporizacion=$fila6[0];//segundos
										}
										mysqli_free_result($respuesta6);
										/*end conocer temporizacion*/
										
										/*start variables id color o funcionalidad asociada a ese foco y fila*/
										$arrayConfiguracionFocoLinea = array();// Declarar un array vacío
										$arrayConfiguracionFocoLinea[]=$idColorcolumuno=$fila4[2];
										$arrayConfiguracionFocoLinea[]=$idColorcolumdos=$fila4[3];
										$arrayConfiguracionFocoLinea[]=$idColorcolumtres=$fila4[4];
										$arrayConfiguracionFocoLinea[]=$idColorcolumcuatro=$fila4[5];
										$arrayConfiguracionFocoLinea[]=$idColorcolumcinco=$fila4[6];
										$arrayConfiguracionFocoLinea[]=$idColorcolumseis=$fila4[7];
										$arrayConfiguracionFocoLinea[]=$idColorcolumsiete=$fila4[8];
										$arrayConfiguracionFocoLinea[]=$idColorcolumocho=$fila4[9];
										$arrayConfiguracionFocoLinea[]=$idColorcolumnueve=$fila4[10];
										$arrayConfiguracionFocoLinea[]=$idColorcolumdiez=$fila4[11];
										$arrayConfiguracionFocoLinea[]=$idColorcolumonce=$fila4[12];
										$arrayConfiguracionFocoLinea[]=$idColorcolumdoce=$fila4[13];
										$arrayConfiguracionFocoLinea[]=$idColorcolumtrece=$fila4[14];
										$arrayConfiguracionFocoLinea[]=$idColorcolumcatorce=$fila4[15];
										$arrayConfiguracionFocoLinea[]=$idColorcolumquince=$fila4[16];
										$arrayConfiguracionFocoLinea[]=$idColorcolumdieciseis=$fila4[17];
										$arrayConfiguracionFocoLinea[]=$idColorcolumdiecisiete=$fila4[18];
										$arrayConfiguracionFocoLinea[]=$idColorcolumdieciocho=$fila4[19];
										$arrayConfiguracionFocoLinea[]=$idColorcolumdiecinueve=$fila4[20];
										$arrayConfiguracionFocoLinea[]=$idColorcolumveinte=$fila4[21];
										/*end variables id color o funcionalidad asociada a ese foco y fila*/

										$direccionDMXEncender=0;
										
										/*start recorrer cada foco para saber su configuracion*/
										$arrayConfiguracionFila=array();
										$cantidadFocos=/*20*/$numeroFocosActivosPrograma;
										$direccionDmxFuncionalidadSumarCanales=0;
										for($f=0;$f<$cantidadFocos;$f++){
											//echo $f."---".$arrayTipoFocosPosiciones[$f]."<br>";
											
											/*START devolver direcciones dmx encender de colores*/
											$patron5="SELECT color,colortexto,colortextoingles,estado,colorreal,id FROM luces_configuracion_color WHERE id=\"%s\"";
											$sql5=sprintf($patron5,$arrayConfiguracionFocoLinea[$f]);
											$respuesta5=mysqli_query($con,$sql5) or die ("Error 12357734495465554454623447555244677");
											if(mysqli_num_rows($respuesta5)>0){
												$fila5=mysqli_fetch_array($respuesta5);
                                                if($fila5[5]!=8){//si esta apagada no hacer nada
                                               		
													/*START primero saber si este tipo de foco tiene el dimer, en caso de encender foco*/
													$patron51="SELECT canales,direcciondmxdimer FROM luces_referenciafocos WHERE id=\"%s\"";
													$sql51=sprintf($patron51,$arrayTipoFocosPosiciones[$f]);
													$respuesta51=mysqli_query($con,$sql51) or die ("Error 1235751515154454623447555244677");
													if(mysqli_num_rows($respuesta51)>0){
														$fila51=mysqli_fetch_array($respuesta51);
														if($f>0){//sumar para cada foco de la misma linea/configuracion
															$direccionDmxFuncionalidadSumarCanales+=/*$fila51[0]*/10;
														}
														//funcionalidad dimer, para cada foco cada configuracion levantar
														if($fila51[1]>0){
															$direccionDmxFuncionalidadDimer=$fila51[1]+$direccionDmxFuncionalidadSumarCanales;

															array_push($arrayConfiguracionLuces, $direccionDmxFuncionalidadDimer);//anadir array configuracion luces
														}
													}
													mysqli_free_result($respuesta51);
													/*END primero saber si este tipo de foco tiene el dimer, en caso de encender foco*/
													
													/*START activar la direccion dmx del color en si*/
													$direccionDMXEncender=calcularDireccionDmxSegunConfVaciosFoco(2,$idPrograma,$f,$arrayConfiguracionFocoLinea[$f],$con);
												
												    array_push($arrayConfiguracionLuces, /*array(*/$direccionDMXEncender/*,$fila5[2])*/);//anadir array configuracion luces
													/*END activar la direccion dmx del color en si*/
                                                }
											}
											mysqli_free_result($respuesta5);
											/*END devolver direcciones dmx encender de colores*/
										}
										/*end recorrer cada foco para saber su configuracion*/
										array_push($arrayConfiguracionLuces, $temporizacion);//añadir array configuracion luces, timer
										
										array_push($arrayConfPrograma, $arrayConfiguracionLuces);//añadir array configuracion luces
									}
								}
								mysqli_free_result($respuesta4);
								/*end recorrer las lineas del programa*/
								
								array_push($arrayCompletoJson, $arrayConfPrograma);//añadir array configuracion programa luces
							}
							mysqli_free_result($respuesta3);
							/*end obtener datos del programa*/
							
						}
					}//cierro if recorrer configuracio horario programa
					mysqli_free_result($respuesta2);
					
				}//cierro for de recorrer los programas asociados
			}
			mysqli_free_result($respuesta1);
			/*end mostrar conf. del programa*/
			
		}
		mysqli_free_result($respuesta);
		/*END devolver conf. programas nodo*/
		
	}
	
	
	/*
	JSON EJEMPLO
	[
	idnodo,
	[idPrograma55,diaSemana(L),HORAINICIO (10:00),HORAFIN(12:00),modo manual/automatico,[ [[1,'blue'],[11,'red'],[21,'red'],.....['delay',0.1]],[nueva secuencia],           ]],
	[idPrograma55,diaSemana(M),HORAINICIO (18:00),HORAFIN(22:00),modo manual/automatico,[]],
	[88,[]],
	[99,[]],
	]
	
	*/
	
	//codificamos el json
	print_r(json_encode($arrayCompletoJson));
}

//*programa ejecutado idprograma, m b **/




//para probar js
/*
	$.getJSON("http://78.136.74.2:6080/panel/apiluces/lucesnodos.php", {
			returnProgramLuzAplica: "returnProgramLuzAplica",
			internal:"00000001"
		},
		function (data) {

		   var hola=JSON.stringify(data);
		   var obj = JSON.parse(hola);
		   console.log(obj)

			console.log(obj.message)
	});
*/

//cierro la conexion
$con->close();
?>