<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');

header("Access-Control-Allow-Origin: *");

require_once("../const/constantes.php");
require_once("../phpincludes/phpgeneral.php");
require_once("../phpincludes/phppropios.php");
//require_once("../phpincludes/phpaudios.php");

if(isset($_POST["abrirCerrarConexion"])){

	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$abrirCerrarConexion=quitaComillasD($_POST["conexion"]);
	
    if($token!="" && $internal!="" && ($abrirCerrarConexion=="1" || $abrirCerrarConexion=="2")){
        $patron="SELECT id FROM audio_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122347652354754567648952435");
        if(mysqli_num_rows($respuesta)>0){
            //for($i=0;$i<mysqli_num_rows($respuesta);$i++){
                $fila=mysqli_fetch_array($respuesta);
				
				//establecer como conectada
				if($abrirCerrarConexion=="1"){
					$conexion="on";//encendida/encender
				}else if($abrirCerrarConexion=="2"){
					$conexion="off";//apagada/apagar
				}
				
				$patron1="UPDATE audio_nodos SET conexion=\"%s\",ultimaconexion=\"%s\",horaultimaconexion=\"%s\" WHERE id=\"%s\" AND token=\"%s\" AND internal=\"%s\"";
				$sql1=sprintf($patron1,$conexion,date("Y-m-d"),date("H:i:s"),$fila[0],$token,$internal);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al editar 1567456546454334553452345");
				
				$correcto="s";
           //}
        }
		mysqli_free_result($respuesta);
    }
   	echo $correcto;
}

//devolver audios pendientes
//https://panel.modularbox.com/apiaudios/audiosnodos.php?returnAudiosSonar=returnAudiosSonar&token=3K5Y-2u7e-3C2P7R7G7W4X7K7&internal=00000001&fecha=2023-11-21&hora=11:44
if(isset($_POST["returnAudiosSonar"])){
	
	//$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	
	$auxFechaConsulta="";
	$auxHoraConsulta="";
	if(isset($_POST["fecha"])){
		$auxFechaConsulta=quitaComillasD($_POST["fecha"]);
	}
	if(isset($_POST["hora"])){
		$auxHoraConsulta=quitaComillasD($_POST["hora"]);
	}
	
	if($token!="" && $internal!=""){
		
		//validar fecha
		$fechaConsulta="";
		if($auxFechaConsulta!="" && strlen($auxFechaConsulta)==10){
			if(strpos($auxFechaConsulta, '-') !== false) {
				//ok
				$fechaConsulta=" AND fechareproducir=\"".$auxFechaConsulta."\"";
			}else{
				//ko
				$fechaConsulta=" AND fechareproducir=\"".date("Y-m-d")."\"";
			}
		}else{
			$fechaConsulta=" AND fechareproducir=\"".date("Y-m-d")."\"";
		}
		
		//validar hora
		$horaConsulta="";
		if($auxHoraConsulta!="" && strlen($auxHoraConsulta)>=5 && strlen($auxHoraConsulta<=8)){
			if(strpos($auxHoraConsulta, ':') !== false) {
				//ok
				//$horaConsulta=" AND horareproducir>=\"".$auxHoraConsulta."\" AND horareproducir<=\"23:59:59\"";
			}else{
				//ko
			}
		}
	 	
		$arrayReproducirSonido=array();//declaro vacio
		$patron="SELECT id,idusuario,horarioinicioaudios,horariofinaudios FROM audio_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 1223476545345567648952435");
        if(mysqli_num_rows($respuesta)>0){
            //for($i=0;$i<mysqli_num_rows($respuesta);$i++){
                $fila=mysqli_fetch_array($respuesta);
			
				$idNodo=$fila[0];
			
				//anotar esta conexion
				$patron3="UPDATE audio_nodos SET fechaultimaconsulta=\"%s\",horaultimaconsulta=\"%s\" WHERE id=\"%s\"";
				$sql3=sprintf($patron3,date("Y-m-d"),date("H:i:s"),$idNodo);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error al editar 43566723423434556756746534567467546");
			
				/*START recorrer el historial*/
				$patron1="SELECT id,idaudio,numeroreproducciones,fechareproducir,horareproducir FROM audio_colashistorial WHERE borrado=\"n\" AND reproducido=\"n\" AND idnodo=\"%s\" %s %s ORDER BY fechareproducir ASC,horareproducir ASC";
				$sql1=sprintf($patron1,$idNodo,$fechaConsulta,$horaConsulta);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error 1223476545345456489524359965");
				if(mysqli_num_rows($respuesta1)>0){
					for($j=0;$j<mysqli_num_rows($respuesta1);$j++){
						$fila1=mysqli_fetch_array($respuesta1);
						
						//datos del fichero del audio
						$idFicheroAudio="";
						$nombreFicheroAudio="";
						$urlFicheroAudio="";
						$patron2="SELECT id,nombre,url FROM audio_ficheroaudio WHERE borrado=\"n\" AND id=\"%s\"";
						$sql2=sprintf($patron2,$fila1[1]);
						$respuesta2=mysqli_query($con,$sql2) or die ("Error 222231565637");
						if(mysqli_num_rows($respuesta2)>0){
							$fila2=mysqli_fetch_array($respuesta2);
							$idFicheroAudio=$fila2[0];
							$nombreFicheroAudio=$fila2[1];
							$urlFicheroAudio="https://panel.modularbox.com/archivos_subidos/clientes/".$fila[1]."/audios/".$fila2[2];
						}
						mysqli_free_result($respuesta2);
						
						$arrayReproducirSonido[]=array("idColaHistorial"=>$fila1[0], "numeroReproducciones"=>$fila1[2], "fechaReproducir"=>$fila1[3], "horaReproducir"=>$fila1[4], "idAudio"=>$idFicheroAudio, "nombreAudio"=>$nombreFicheroAudio, "urlAudio"=>$urlFicheroAudio, "horarioInicioAudios"=>$fila[2], "horarioFinAudios"=>$fila[3]);
						
						
					}
				}
				mysqli_free_result($respuesta1);
				/*END recorrer el historial*/
		}
		
		mysqli_free_result($respuesta);
		//codificamos el json
		print_r(json_encode($arrayReproducirSonido));
	}
}

//anotar audio reproducido 
//https://panel.modularbox.com/apiaudios/audiosnodos.php?anotarAudioReproducido=anotarAudioReproducido&token=3K5Y-2u7e-3C2P7R7G7W4X7K7&internal=00000001&idColaHistorial=1&accionAudio=s
if(isset($_POST["anotarAudioReproducido"])){
	
	$correcto="n";
	$token=quitaComillasD($_POST["token"]);
	$internal=quitaComillasD($_POST["internal"]);
	$idColaHistorial=intval(quitaComillasD($_POST["idColaHistorial"]));
	
	if(isset($_POST["hora"])){
		$accionAudio=quitaComillasD($_POST["accionAudio"]);// si->s, no ->, mal ->m 
	}else{
		$accionAudio="n";// si->s, no ->, mal ->m 
	}
	
	if($token!="" && $internal!="" && $idColaHistorial>0){
		$patron="SELECT id,idusuario FROM audio_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND token=\"%s\" AND internal=\"%s\"";
        $sql=sprintf($patron,$token,$internal);
        $respuesta=mysqli_query($con,$sql) or die ("Error 122353454566476545345567648955672435");
        if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);

			/*START mirar el historial*/
			$patron1="SELECT id FROM audio_colashistorial WHERE borrado=\"n\" AND id=\"%s\" AND idnodo=\"%s\"";
			$sql1=sprintf($patron1,$idColaHistorial,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error 12234765670545345456489524359965");
			if(mysqli_num_rows($respuesta1)>0){
				$fila1=mysqli_fetch_array($respuesta1);
				
				if($accionAudio=="s" || $accionAudio=="n" || $accionAudio=="m"){
					$patron2="UPDATE audio_colashistorial SET reproducido=\"s\",resultado=\"%s\",horareproducido=\"%s\" WHERE id=\"%s\" AND idnodo=\"%s\"";
					$sql2=sprintf($patron2,$accionAudio,date("H:i:s"),$fila1[0],$fila[0]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al editar 156722232345222");

					$correcto="s";
				}
			}
			mysqli_free_result($respuesta1);
			/*END mirar el historial*/
		}
		mysqli_free_result($respuesta);
	}
	echo $correcto;
}
?>