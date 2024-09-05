<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');
$restringir=$_GET["p"];//ruta.php?p=h9.JModuRALh89
if($restringir=="h9.JModuRALh89"){

	require_once("../const/constantes.php");
	require_once("./phpgeneral.php");
	//require_once("phpmailer/class.phpmailer.php");
	require '../PHPMailer/src/PHPMailer.php';
	require '../PHPMailer/src/SMTP.php';
	require '../PHPMailer/src/Exception.php';

	require_once("./phpemails.php");

					/***************************************************************/
										/******************/
										/**	notificaciones**/
										/******************/

	enviarNotificacionesMailContador($con);
}else{
	header("Location: https://panel.modularbox.com/");
}

//envio de notificaciones automaticas
function enviarNotificacionesMailContador($con){
	
	$patron="SELECT id,nombre,idusuario,horaconsumonotifi,mhoraconsumonotifi,notificadoconsumomail,m3notidiarios,m3notimail,diasnoactividadnotifi,mdiasnoactividadnotifi,diasnoactividadnotifi,notificadopasadoconsumomaildiario,notificadoconsumomail,notificadonoactividadmail,m3notisemanales,m3notimensuales,m3notianuales,notificadopasadoconsumomailsemanal,notificadopasadoconsumomailmensual,notificadopasadoconsumomailanual FROM contadores_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND conexion=\"on\" AND (notificadoconsumomail<>\"%s\" OR notificadopasadoconsumomaildiario<>\"%s\" OR notificadonoactividadmail<>\"%s\" OR notificadopasadoconsumomailsemanal<>\"%s\" OR notificadopasadoconsumomailmensual<>\"%s\" OR notificadopasadoconsumomailanual<>\"%s\")";
	$sql=sprintf($patron,date("Y-m-d"),date("Y-m-d"),date("Y-m-d"),date("Y-m-d"),date("Y-m-d"),date("Y-m-d"),date("Y-m-d"));
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 124563454566789000");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$patron4="SELECT nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
			$sql4=sprintf($patron4,$fila[2]);
			$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 123454546674849000");
			$fila4=mysqli_fetch_array($respuesta4);
			
			/****************----------------------------------------------------------------------------------------*****************/
			/***START notificar si ha sobrepasado los xxx m3 DIARIOS,SEMANALES,MENSUALES,ANUALES***///----------------------------------------------
			//calcular consulta de ese periodo
			$periodo="Diarios";
			$consultaPeriodo=" AND fecha=\"".date("Y-m-d")."\"";
			$lecturaLimiteAvisoConf=0;
			$consultaCampoUpdate="";
			$fechaUnaVezIf="";
			for($j=0;$j<4;$j++){
				
				if($j==0){
					$periodo="Diarios";
					$consultaPeriodo=" AND fecha=\"".date("Y-m-d")."\"";
					
					$lecturaLimiteAvisoConf=$fila[6];
					$consultaCampoUpdate="notificadopasadoconsumomaildiario";
					$fechaUnaVezIf=$fila[11];
					
					$textoDiarios="";
					$textoDiariosDos="";
					$fechaAyerTres=restaDias(date("Y-m-d"),1);
					$patron9="SELECT SUM(lectura) FROM contadores_historial WHERE borrado=\"n\" AND contador=\"%s\" AND fecha<=\"%s\"";
					$sql9=sprintf($patron9,$fila[0],$fechaAyerTres);
					$respuesta9=mysqli_query($con,$sql9) or die ("Error al buscar 123459994566999789000");
					if(mysqli_num_rows($respuesta9)>0){
						$fila9=mysqli_fetch_array($respuesta9);
						
						$totalLecturaHasta=intval($fila9[0])/1000;
						
						$textoDiarios="<br><br>La cantidad acumulada hasta la fecha y hora: <b>".convierteFechaBarra($fechaAyerTres)."- 23:59:59</b> es de: <b>".$totalLecturaHasta." m3.</b><br>";
						$textoDiariosDos=" desde las 00:00:00 ";
					}
					mysqli_free_result($respuesta9);
				}else if($j==1){
					//inicio y fin de semana
					$iniFinSemana=inicio_fin_semana(date("Y-m-d"));
					$inicioSemanaAux=$iniFinSemana["fechaInicio"];
					$finSemanaAux=$iniFinSemana["fechaFin"];
					
					$periodo="Semanal";
					$consultaPeriodo=" AND fecha>=\"".$inicioSemanaAux."\" AND fecha<=\"".$finSemanaAux."\"";
					
					$lecturaLimiteAvisoConf=$fila[14];
					$consultaCampoUpdate="notificadopasadoconsumomailsemanal";
					$fechaUnaVezIf=$fila[17];
				}else if($j==2){
					$periodo="Mensual";
					$ultimoDiaMes=getUltimoDiaMes(date("Y"),date("m"));
					$consultaPeriodo=" AND fecha>=\"".date("Y-m")."-01"."\" AND fecha<=\"".date("Y-m")."-".$ultimoDiaMes."\"";
					
					$lecturaLimiteAvisoConf=$fila[15];
					$consultaCampoUpdate="notificadopasadoconsumomailmensual";
					$fechaUnaVezIf=$fila[18];
				}else if($j==3){
					$periodo="Anual";
					$consultaPeriodo=" AND fecha>=\"".date("Y")."-01-01"."\" AND fecha<=\"".date("Y")."-12-31"."\"";
					
					$lecturaLimiteAvisoConf=$fila[16];
					$consultaCampoUpdate="notificadopasadoconsumomailanual";
					$fechaUnaVezIf=$fila[19];
				}

				$patron6="SELECT SUM(lectura) FROM contadores_historial WHERE borrado=\"n\" AND contador=\"%s\"%s";
				$sql6=sprintf($patron6,$fila[0],$consultaPeriodo);
				$respuesta6=mysqli_query($con,$sql6) or die ("Error al buscar 123459994566789000");
				if(mysqli_num_rows($respuesta6)>0){
					$fila6=mysqli_fetch_array($respuesta6);

					$totalLectura=intval($fila6[0])/1000;
					
					if(strlen($fila[7])>5 && $totalLectura>=$lecturaLimiteAvisoConf && $fechaUnaVezIf<date("Y-m-d") && $consultaCampoUpdate!="" && $lecturaLimiteAvisoConf>0){

						////////////*****
						$copia="";
						$asunto=$fila[1]." -- Límite ".$periodo." Consumo";
						$contenido="Hola, <b>".$fila4[0]."</b><br><br>El contador: <b>".$fila[1]."</b><br>ha sobrepasado el límite de consumo: <b>".$periodo."</b><br> establecido en: <b>".$lecturaLimiteAvisoConf." m3.</b><br><br>La lectura acumulada de <b>hoy</b>, a fecha y hora: <b>".convierteFechaBarra(date("Y-m-d"))." - ".$textoDiariosDos."- ".date("H:i:s")."</b> es de: <b>".$totalLectura." m3</b>.".$textoDiarios."<br><br> Un saludo.<br><br><br> <b>No responda a este mensaje</b> ha sido autogenerado por la plataforma <b>(MODULARBOX)</b>.";
						mailGenerico($fila[7],$copia,$asunto,$contenido,$fila[2],"","",$con);
						///////////*****

						$patron3="UPDATE contadores_nodos SET %s=\"%s\" WHERE id=\"%s\"";
						$sql3=sprintf($patron3,$consultaCampoUpdate,date("Y-m-d"),$fila[0]);
						$respuesta3=mysqli_query($con,$sql3) or die ("Error al borrar 78942298537883522");
					}
				}
				mysqli_free_result($respuesta6);
			}
			/***END notificar si ha sobrepasado los xxx m3 DIARIOS,SEMANALES,MENSUALES,ANUALES***/
			
			/****************----------------------------------------------------------------------------------------*****************/
			/***START notificar a tal hora el consumo acumulado***///----------------------------------------------
			//saber total consumo, de hoy
			if(date("H:i:s")>=$fila[3] && strlen($fila[4])>5 && $fila[12]<date("Y-m-d")){
				//****saber desde la hora xxx del día anterior hasta el xxx de hoy
				$fechaAyer=restaDias(date("Y-m-d"),1);
				
				$consumoHastaHoyHora=0;
				$consumoHastaAyerHora=0;
				//saber desde inicio hasta hoy a xx hora
				$patron8="SELECT SUM(lectura) FROM contadores_historial WHERE borrado=\"n\" AND contador=\"%s\" AND (fecha<=\"%s\" OR fecha<=\"%s\" AND hora<=\"%s\")";
				$sql8=sprintf($patron8,$fila[0],$fechaAyer,date("Y-m-d"),$fila[3]);
				$respuesta8=mysqli_query($con,$sql8) or die ("Error al buscar 1238454998885667889000");
				if(mysqli_num_rows($respuesta8)>0){
					$fila8=mysqli_fetch_array($respuesta8);
					$consumoHastaHoyHora=intval($fila8[0])/1000;
				}
				mysqli_free_result($respuesta8);
				
				//saber desde inicio hasta ayer a xx hora
				$patron9="SELECT SUM(lectura) FROM contadores_historial WHERE borrado=\"n\" AND contador=\"%s\" AND (fecha<=\"%s\" OR fecha<=\"%s\" AND hora<=\"%s\")";
				$sql9=sprintf($patron9,$fila[0],restaDias($fechaAyer,1),$fechaAyer,$fila[3]);
				$respuesta9=mysqli_query($con,$sql9) or die ("Error al buscar 123843453454999985667889000");
				if(mysqli_num_rows($respuesta9)>0){
					$fila9=mysqli_fetch_array($respuesta9);
					$consumoHastaAyerHora=intval($fila9[0])/1000;
				}
				mysqli_free_result($respuesta9);
				
				$diferencia=$consumoHastaHoyHora-$consumoHastaAyerHora;
				
				$copia="";
				$asunto=$fila[1]." -- Lectura Contador";
				$contenido="Hola, <b>".$fila4[0]."</b><br><br>La lectura acumulada de <b>hoy</b> del contador, <b>".$fila[1]."</b>, a fecha y hora: <b>".convierteFechaBarra(date("Y-m-d"))." - ".$fila[3]."</b> es de: <b>".$consumoHastaHoyHora." m3</b>.<br>La lectura acumulada de <b>ayer</b> del contador, <b>".$fila[1]."</b>, a fecha y hora: <b>".convierteFechaBarra($fechaAyer)." - ".$fila[3]."</b>, fue de: <b>".$consumoHastaAyerHora." m3.</b><br>El consumo total es de: <b>".number_format($diferencia,2,",",".")." m3</b><br><br> Un saludo. <br><br><br> <b>No responda a este mensaje</b> ha sido autogenerado por la plataforma <b>(MODULARBOX)</b>.";
			
				mailGenerico($fila[4],$copia,$asunto,$contenido,$fila[2],"","",$con);
				///////////*****

				$patron2="UPDATE contadores_nodos SET notificadoconsumomail=\"%s\" WHERE id=\"%s\"";
				$sql2=sprintf($patron2,date("Y-m-d"),$fila[0]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al borrar 789422522");
			}
			/***END notificar a tal hora el consumo acumulado***/
		
			/****************----------------------------------------------------------------------------------------*****************/
			/***START notificar si hace xx dias no tenemos lecturas***///----------------------------------------------
			//saber total consumo
			if($fila[13]<date("Y-m-d")){
				$patron5="SELECT id FROM contadores_historial WHERE contador=\"%s\" AND borrado=\"n\" AND fecha BETWEEN date_add(NOW(), INTERVAL -\"%s\" DAY) AND NOW() ORDER BY id DESC LIMIT 0,1";
				$sql5=sprintf($patron5,$fila[0],$fila[10]);
				$respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 1235455456657890050");
				if(mysqli_num_rows($respuesta5)>0){
					$fila5=mysqli_fetch_array($respuesta5);
				}else{
					////////////*****
					$copia="";
					$asunto=$fila[1]." -- Posible fallo Contador";
					$contenido="Hola, <b>".$fila4[0]."</b><br><br>El contador, <b>".$fila[1]."</b>, a fecha y hora: <b>".convierteFechaBarra(date("Y-m-d"))." - ".date("H:i:s")."</b>, lleva (al menos) <b>".$fila[10]." días</b> sin actividad. <br> Un saludo. <br><br><br> <b>No responda a este mensaje</b> ha sido autogenerado por la plataforma <b>(MODULARBOX)</b>.";
					mailGenerico($fila[4],$copia,$asunto,$contenido,$fila[2],"","",$con);
					///////////*****

					$patron2="UPDATE contadores_nodos SET notificadonoactividadmail=\"%s\" WHERE id=\"%s\"";
					$sql2=sprintf($patron2,date("Y-m-d"),$fila[0]);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al borrar 789422522");
				}
				mysqli_free_result($respuesta5);
			}
			/***END notificar si hace xx dias no tenemos lecturas***/
		}
	}
	mysqli_free_result($respuesta);
}
?>