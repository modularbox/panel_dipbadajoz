<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');
		
header("Access-Control-Allow-Origin: *");

require_once("../const/constantes.php");
require_once("../phpincludes/phpgeneral.php");
require_once("../phpincludes/phppropios.php");
require_once("../phpincludes/phpcampanas.php");

//https://panel.modularbox.com/apicampanas/campanasnodos.php?abrirCerrarConexion=abrirCerrarConexion&token=F4E3-4v9q-4W5Q5D7N7Q8T8R8&internal=00000001&conexion=1
if(isset($_POST["abrirCerrarConexion"])){

	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$abrirCerrarConexion=quitaComillasD($_POST["conexion"]);
	
    if($token!="" && $internal!="" && ($abrirCerrarConexion=="1" || $abrirCerrarConexion=="2")){
        $patron="SELECT id FROM campanas_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122347689309905");
        if(mysqli_num_rows($respuesta)>0){
            //for($i=0;$i<mysqli_num_rows($respuesta);$i++){
                $fila=mysqli_fetch_array($respuesta);
				
				//establecer como conectada
				if($abrirCerrarConexion=="1"){
					$conexion="on";//encendida/encender
				}else if($abrirCerrarConexion=="2"){
					$conexion="off";//apagada/apagar
				}
				
				$patron1="UPDATE campanas_nodos SET conexion=\"%s\",ultimaconexion=\"%s\",horaultimaconexion=\"%s\" WHERE id=\"%s\" AND token=\"%s\" AND internal=\"%s\"";
				$sql1=sprintf($patron1,$conexion,date("Y-m-d"),date("H:i:s"),$fila[0],$token,$internal);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al editar 156766390032345");
				
				$correcto="s";
           //}
        }
		mysqli_free_result($respuesta);
    }
   	echo $correcto;
}

//https://panel.modularbox.com/apicampanas/campanasnodos.php?obtenerConfiguracionReles=obtenerConfiguracionReles&token=S3J8-5k5c-7B6Z3R5M7F8E6Q4&internal=00000001
if(isset($_POST["obtenerConfiguracionReles"])){

	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
    if($token!="" && $internal!=""){
		$arrayGeneral=array();
		$arrayProgramas=array();
        $patron="SELECT id,idusuario FROM campanas_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 12234789857689309905");
        if(mysqli_num_rows($respuesta)>0){
        	$fila=mysqli_fetch_array($respuesta);
			
			//gestion de la programacion de luces
			$arrayConfiguracionCampanasLuces=array();
			//obtener la configuracion de luces
			$patron991="SELECT id,l,m,x,j,v,s,d,horainicio,horafin FROM campanas_luces WHERE idnodo=\"%s\" AND borrado=\"n\"";
			$sql991=sprintf($patron991,$fila[0]);
			$respuesta991=mysqli_query($con,$sql991) or die ("Error 1223478991911857689309905");
			if(mysqli_num_rows($respuesta991)>0){
				for($l=0;$l<mysqli_num_rows($respuesta991);$l++){
        			$fila991=mysqli_fetch_array($respuesta991);
					$arrayConfiguracionLuz=array();
					
					//anadir al array los dias semana, devuelvo numeros para buscar en python3
					if($fila991[1]=="s"){//L
						array_push($arrayConfiguracionLuz,"1");//"L" si
					}else{
						array_push($arrayConfiguracionLuz,"0");//"L"
					}
					
					if($fila991[2]=="s"){//M
						array_push($arrayConfiguracionLuz,"1");//"M" si
					}else{
						array_push($arrayConfiguracionLuz,"0");//"M"
					}
					
					if($fila991[3]=="s"){//X
						array_push($arrayConfiguracionLuz,"1");//"X" si
					}else{
						array_push($arrayConfiguracionLuz,"0");//"X"
					}
					
					if($fila991[4]=="s"){//J
						array_push($arrayConfiguracionLuz,"1");//"J" si
					}else{
						array_push($arrayConfiguracionLuz,"0");//"J"
					}
					
					if($fila991[5]=="s"){//V
						array_push($arrayConfiguracionLuz,"1");//"V" si
					}else{
						array_push($arrayConfiguracionLuz,"0");//"V"
					}
					
					if($fila991[6]=="s"){//S
						array_push($arrayConfiguracionLuz,"1");//"S" si
					}else{
						array_push($arrayConfiguracionLuz,"0");//"S"
					}
					if($fila991[7]=="s"){//D
						array_push($arrayConfiguracionLuz,"1");//"D" si
					}else{
						array_push($arrayConfiguracionLuz,"0");//"D"
					}
					
					array_push($arrayConfiguracionLuz,$fila991[8]);//horainicio
					array_push($arrayConfiguracionLuz,$fila991[9]);//fin
					
					//anadir al array de luces
					array_push($arrayConfiguracionCampanasLuces,$arrayConfiguracionLuz);
				}
			}
			mysqli_free_result($respuesta991);
			
			//ver configuracion reloj
			$arrayConfiguracionCampanasReloj=array();
			//obtener la configuracion de reles
			$patron9992="SELECT id,relecuatrofrecuencia,relecuatroduracion,relecincofrecuencia,relecincoduracion FROM campanas_reloj WHERE idusuario=\"%s\" AND borrado=\"n\"";
			$sql9992=sprintf($patron9992,$fila[1]);
			$respuesta9992=mysqli_query($con,$sql9992) or die ("Error 12234454778991911857689309905");
			if(mysqli_num_rows($respuesta9992)>0){
				for($l=0;$l<mysqli_num_rows($respuesta9992);$l++){
        			$fila9992=mysqli_fetch_array($respuesta9992);
					
					$arrayConfiguracionReleCuatro=array();
					$arrayConfiguracionReleCinco=array();
					
					//rele 4
					array_push($arrayConfiguracionReleCuatro,$fila9992[1]);
					array_push($arrayConfiguracionReleCuatro,$fila9992[2]);
						//anadir al general del rele4
					array_push($arrayConfiguracionCampanasReloj,$arrayConfiguracionReleCuatro);
					
					//rele 5
					array_push($arrayConfiguracionReleCinco,$fila9992[3]);
					array_push($arrayConfiguracionReleCinco,$fila9992[4]);
						//anadir al general del rele5
					array_push($arrayConfiguracionCampanasReloj,$arrayConfiguracionReleCinco);
				}
			}
			mysqli_free_result($respuesta9992);
			
			//ver los programas activos
			$patron1="SELECT id,idprograma FROM campanas_programas_activos WHERE activo=\"s\" AND idnodo=\"%s\"";
			$sql1=sprintf($patron1,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error 1223478911857689309905");
			if(mysqli_num_rows($respuesta1)>0){
				for($i=0;$i<mysqli_num_rows($respuesta1);$i++){
        			$fila1=mysqli_fetch_array($respuesta1);
					
					//datos del programa
					$arrayProgramas=array();
					$arrayItemProgramaDias=array();
					$arrayItemProgramaHorario=array();
					$arrayRepeticionesPrograma=array();
					$patron2="SELECT id,activo,descripcion,horainicio,horafin,l,m,x,j,v,s,d,tiemporepeticiones,nrepeticiones FROM campanas_programas WHERE guardado=\"s\" AND borrado=\"n\" AND id=\"%s\" AND idusuario=\"%s\"";
					$sql2=sprintf($patron2,$fila1[1],$fila[1]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error 1223472222689309905");
					if(mysqli_num_rows($respuesta2)>0){
        				$fila2=mysqli_fetch_array($respuesta2);
						
						//anadir al array los dias semana, devuelvo numeros para buscar en python3
						if($fila2[5]=="s"){//L
							array_push($arrayItemProgramaDias,"0");//"L"
						}
						if($fila2[6]=="s"){//M
							array_push($arrayItemProgramaDias,"1");//"M"
						}
						if($fila2[7]=="s"){//X
							array_push($arrayItemProgramaDias,"2");//"X"
						}
						if($fila2[8]=="s"){//J
							array_push($arrayItemProgramaDias,"3");//"J"
						}
						if($fila2[9]=="s"){//V
							array_push($arrayItemProgramaDias,"4");//"V"
						}
						if($fila2[10]=="s"){//S
							array_push($arrayItemProgramaDias,"5");//"S"
						}
						if($fila2[11]=="s"){//D
							array_push($arrayItemProgramaDias,"6");//"D"
						}
						
						//anadir al array el horario
						$programaEjecutadoYa="n";//si esta en si, no reproduce nada
						//esto para ver si queremos que se repita en el dia o no, por ahora si repetir
						/*$patron5="SELECT id FROM campanas_historial WHERE idprograma=\"%s\" AND idnodo=\"%s\" AND fechaalta=\"%s\"";
						$sql5=sprintf($patron5,$fila2[0],$fila[0],date("Y-m-d"));
						$respuesta5=mysqli_query($con,$sql5) or die ("Error 12234555789118885765558933309905");
						if(mysqli_num_rows($respuesta5)>0){
							for($j=0;$j<mysqli_num_rows($respuesta5);$j++){
								$fila5=mysqli_fetch_array($respuesta5);
								$programaEjecutadoYa="s";//esto hace que no se vuelva a repetir ese programa
							}
						}
						mysqli_free_result($respuesta5);*/
						
						array_push($arrayItemProgramaHorario,$fila2[3]);//hora inicio
						array_push($arrayItemProgramaHorario,$programaEjecutadoYa);#saber si ya se ha ejecutado o no
						
						
						//anadir al array el horario las repeticiones
						$arrayRepeticionesPrograma=array();
						array_push($arrayRepeticionesPrograma,$fila2[13]);//numero de repeticiones
						array_push($arrayRepeticionesPrograma,$fila2[12]);//pausa entre repeticiones
						
						//recorrer la tabla programas configuracion
						$arrayConfiguracionCampanasPrograma=array();
						$patron3="SELECT id,releuno,reledos,reletres,temporizacion FROM campanas_programas_configuracion WHERE idprograma=\"%s\" AND borrado=\"n\"";
						$sql3=sprintf($patron3,$fila2[0]);
						$respuesta3=mysqli_query($con,$sql3) or die ("Error 122347891185768933309905");
						if(mysqli_num_rows($respuesta3)>0){
							for($k=0;$k<mysqli_num_rows($respuesta3);$k++){
								$fila3=mysqli_fetch_array($respuesta3);
								$arrayConfiguracionFila=array();
								
								//rele1
								if($fila3[1]=="1"){
									array_push($arrayConfiguracionFila,"1");
								}else{
									array_push($arrayConfiguracionFila,"0");
								}
								//rele2
								if($fila3[2]=="1"){
									array_push($arrayConfiguracionFila,"1");
								}else{
									array_push($arrayConfiguracionFila,"0");
								}
								//rele3
								if($fila3[3]=="1"){
									array_push($arrayConfiguracionFila,"1");
								}else{
									array_push($arrayConfiguracionFila,"0");
								}
								
								//temporizacion
								$temporizacion=0;
								$patron4="SELECT tiemposegundos FROM campanas_temporizacion WHERE id=\"%s\"";
								$sql4=sprintf($patron4,$fila3[4]);
								$respuesta4=mysqli_query($con,$sql4) or die ("Error 12234789441185768933309905");
								if(mysqli_num_rows($respuesta4)>0){
									$fila4=mysqli_fetch_array($respuesta4);
									$temporizacion=$fila4[0];
								}
								mysqli_free_result($respuesta4);
								array_push($arrayConfiguracionFila,2);
								
								//anadir al array de filas
								array_push($arrayConfiguracionCampanasPrograma,$arrayConfiguracionFila);
							}
						}
						mysqli_free_result($respuesta3);
						
						
						
						//montar
						array_push($arrayProgramas,$fila2[0]);//idprograma [0]
						array_push($arrayProgramas,$arrayRepeticionesPrograma);//array repeticiones y tiempo [1]
						array_push($arrayProgramas,$arrayItemProgramaDias);//array dias [2]
						array_push($arrayProgramas,$arrayItemProgramaHorario);//array horario [3]
						array_push($arrayProgramas,$arrayConfiguracionCampanasPrograma);//array configuracion campanas [4]
						array_push($arrayProgramas,$arrayConfiguracionCampanasLuces);//array configuracion luces campanas [5]
						array_push($arrayProgramas,$arrayConfiguracionCampanasReloj);//array reloj campanas [6]
						
					}
					mysqli_free_result($respuesta2);
					
					array_push($arrayGeneral,$arrayProgramas);//anadir todo al array general
				}
			}
			mysqli_free_result($respuesta1);
			
			//anotar esta conexion
			$patron199="UPDATE campanas_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
			$sql199=sprintf($patron199,date("Y-m-d"),date("H:i:s"),$fila[0]);
			$respuesta199=mysqli_query($con,$sql199) or die ("Error al editar 45453447563737009344534534565650909");
			
        }
		mysqli_free_result($respuesta);
    }
   	print_r(json_encode($arrayGeneral));//respuesta
}

//anotar en el historia
if(isset($_POST["historialProgramaEjecutado"])){
	$correcto="n";
	
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$idPrograma=quitaComillasD($_POST["idPrograma"]);

    if($token!="" && $internal!=""){
        $patron="SELECT id FROM campanas_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122367584546423433356676745476782342367068935993");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			
			//anotar
			$patron2="INSERT INTO campanas_historial SET idprograma=\"%s\",idnodo=\"%s\",horaalta=\"%s\",fechaalta=\"%s\" ";
			$sql2=sprintf($patron2,$idPrograma,$fila[0],date("H:i:s"),date("Y-m-d"));
			$respuesta2=mysqli_query($con,$sql2) or die ("Error al borrar 12345110463434345465234454222265746645574546");

			//anotar esta conexion
			$patron199="UPDATE campanas_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
			$sql199=sprintf($patron199,date("Y-m-d"),date("H:i:s"),$fila[0]);
			$respuesta199=mysqli_query($con,$sql199) or die ("Error al editar 4545343434347563737009344534534565650909");
			
			$correcto="s";
		}
		mysqli_free_result($respuesta);
	}
	echo $correcto;
}

//cierro la conexion
$con->close();
?>