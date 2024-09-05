<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');

$restringir="";
if(isset($_GET["p"])){
	$restringir=$_GET["p"];//ruta.php?p=h9.JModuRAyh77
}

if($restringir=="h9.JModuRAyh77"){
	//https://panel.modularbox.com/phpincludes/cronjobsgeneral.php?p=h9.JModuRAyh77
	
	require_once("../const/constantes.php");
	require_once("phpgeneral.php");
	require_once("phppropios.php");
	
	
	//require_once("./phpmultiwater.php");
	require_once("./phpcontadores.php");
	require_once("./phpluces.php");
	require_once("./phpsafey.php");
	require_once("./phppistaspadel.php");
	require_once("./phpparques.php");
	require_once("./phpcampanas.php");
	require_once("./phpaudios.php");
	//require_once("./phpautomatizacion.php");
	//require_once("./phpvideovigilancia.php");
	
	
				/***************************************************************/
									/******************/
								 /**	tareas automaticas**/
									/******************/
	
	
	/*START gestion online offline*/
	ultimaConexionOnlinOfflinePistasPadel($con);//online/Offline PISTAS PADEL
	ultimaConexionOnlinOfflineParques($con);//online/Offline PARQUES
	ultimaConexionOnlinOfflineLuces($con);//online/Offline LUCES
	ultimaConexionOnlinOfflineContadores($con);//online/Offline CONTADORES
	ultimaConexionOnlinOfflineSafey($con);//online/Offline SAFEY
	ultimaConexionOnlinOfflineCampanas($con);//online/Offline CAMPANAS
	ultimaConexionOnlinOfflineAudios($con);//online/Offline AUDIOS

	/*END gestion online offline*/
}else{
	header("Location: https://panel.modularbox.com/");
}

?>