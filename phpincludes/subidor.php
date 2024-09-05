<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');

session_name("modularboxpanel");
session_start([
  'read_and_close'  => false,
]);

require_once("../const/constantes.php");
require_once("phpgeneral.php");
require_once("phppropios.php");

if(usuarioCorrecto($con)){
	$fileTypes = array('jpg','jpeg','tif','tiff','gif','png','JPG','JPEG','TIF','TIFF','GIF','PNG', 'BMP' , 'PNG');
						  
	var_dump($_FILES);//fichero
	var_dump($_POST);//datos extra
	

}

?>