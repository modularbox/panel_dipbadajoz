<?php
// ********** FUNCIONES GENERALES Y DE CONEXION ***********

function conecta($servidor,$usuario,$clave,$baseDatos){//conecta a la base de datos
	$mysqli = new mysqli($servidor,$usuario,$clave,$baseDatos);
	$mysqli->query("SET NAMES 'utf8'");
    return $mysqli;
}

$con=conecta(SERVIDOR,USUARIO,CLAVE,BBDD);

function conectaUsuario($nombre,$clave,$con){//conecta a la tabla usuarios
    
	$patron="SELECT aes_decrypt(contrasena, \"%s\"),id,nombre,accesos,permisos FROM usuarios WHERE email=\"%s\" AND guardado=\"s\"";
	$sql=sprintf($patron,BBDDK,strtolower(quitaComillasD($nombre)));
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 34343453554");
	if(mysqli_num_rows($respuesta)>0){
		$fila=mysqli_fetch_array($respuesta);
		if($fila[0]==$clave && $nombre!="" && $clave!=""){

			$_SESSION["idusersession"]=$fila[1];
			$_SESSION["nombresession"]=$fila[2];
			$_SESSION["clavesession"]=$clave;
			$_SESSION["usersession"]=$nombre;
			$_SESSION["permisossession"]=$fila[4];

			//cookies
			$tiempoCaduca=31536000;//365*24*60*60;/* expira en un año */
			setcookie("idusersession", $fila[1], time()+$tiempoCaduca);  
			setcookie("nombresession", $fila[2], time()+$tiempoCaduca); 
			setcookie("clavesession", $clave, time()+$tiempoCaduca);  
			setcookie("usersession", $nombre, time()+$tiempoCaduca); 
			setcookie("permisossession", $fila[4], time()+$tiempoCaduca);
			
			$patron3="UPDATE usuarios SET accesos=\"%s\",ultimaconexion=\"%s\" WHERE id=\"%s\"";
			$sql3=sprintf($patron3,$fila[3]+1,date("Y-m-d"),$fila[1]);
			$respuesta3=mysqli_query($con,$sql3) or die ("Error 13456456547ere454546");
			
			return $_SESSION["idusersession"];
		}else{
			unset($_SESSION["idusersession"]);
			unset($_SESSION["nombresession"]);
			unset($_SESSION["clavesession"]);
			unset($_SESSION["usersession"]);
			unset($_SESSION["permisossession"]);
			
			$_SESSION["errorlogin"]="Los datos introducidos no son correctos.";
			return 0;
		}
		mysqli_free_result($respuesta);
	}else{//siguiente loguin
		unset($_SESSION["idusersession"]);
		unset($_SESSION["nombresession"]);
		unset($_SESSION["clavesession"]);
		unset($_SESSION["usersession"]);
		unset($_SESSION["permisossession"]);

		$_SESSION["errorlogin"]="Los datos introducidos no son correctos.";
		return 0;
	}
}

function usuarioCorrecto($con){
	$correcto=false;
	if(isset($_COOKIE["nombresession"]) && isset($_COOKIE["clavesession"]) && isset($_COOKIE["usersession"]) && isset($_COOKIE["idusersession"]) && $_COOKIE["usersession"]!="" && $_COOKIE["clavesession"]!="" && $_COOKIE["idusersession"]!=""){
		
		//comprobar si existen las sesiones
		if(!isset($_SESSION["nombresession"])){
			$_SESSION["nombresession"]=$_COOKIE["nombresession"];
		}
		if(!isset($_SESSION["clavesession"])){
			$_SESSION["clavesession"]=$_COOKIE["clavesession"];
		}
		if(!isset($_SESSION["usersession"])){
			$_SESSION["usersession"]=$_COOKIE["usersession"];
		}
		if(!isset($_SESSION["idusersession"])){
			$_SESSION["idusersession"]=$_COOKIE["idusersession"];
		}
		if(!isset($_SESSION["permisossession"])){
			$_SESSION["permisossession"]=$_COOKIE["permisossession"];
		}
		
		//login usuarios
		$patron6="SELECT aes_decrypt(contrasena, \"%s\"),permisos FROM usuarios WHERE email=\"%s\" AND id=%s AND guardado=\"s\" AND borrado=\"n\"";
		$sql6=sprintf($patron6,BBDDK,strtolower($_SESSION["usersession"]),$_SESSION["idusersession"]);
		$respuesta6=mysqli_query($con,$sql6) or die ("Error al buscar usuario 16");
		if(mysqli_num_rows($respuesta6)>0){
			$fila6=mysqli_fetch_array($respuesta6);						
			if($fila6[0]==$_SESSION["clavesession"]){
				
				$_SESSION["permisossession"]=$fila6[1];
				
				return true;
			}else{
				unset($_SESSION["idusersession"]);
				unset($_SESSION["nombresession"]);
				unset($_SESSION["clavesession"]);
				unset($_SESSION["usersession"]);
				unset($_SESSION["idempresasesion"]);
				return false;
			}
		}else{//empresas clientes
			unset($_SESSION["idusersession"]);
			unset($_SESSION["nombresession"]);
			unset($_SESSION["clavesession"]);
			unset($_SESSION["usersession"]);
		}
		
	}else{
		unset($_SESSION["idusersession"]);
		unset($_SESSION["nombresession"]);
		unset($_SESSION["clavesession"]);
		unset($_SESSION["usersession"]);

		return false;
	}
}

function generaCodigo($long,$tipo){
	if($tipo==0){
		$letras="ABCDEFGHJKMNPQRSTUVWXYZ";
	}else{
		$letras="abcdefghjkmnpqrstuvwxyz";
	}
	$numeros="23456789";
	$codigo="";
	$letraonumero=rand(0, 1);
	for($i=0;$i<$long;$i++){
		if($letraonumero){
			$codigo.=substr($letras, mt_rand(0, 22), 1);
		}else{
			$codigo.=substr($numeros, mt_rand(0, 7), 1);
		}
		$letraonumero=!$letraonumero;
	}
	return $codigo;
}

function quitaComillasD($cadena){
	$cadenaSinComillas=str_replace("\"","'",$cadena);//"
	$cadenaSinSignos=str_replace("<","&lt;",$cadenaSinComillas);//<
	$cadenaSinSignos=str_replace(">","&gt;",$cadenaSinSignos );//>
	
	return $cadenaSinSignos;
}

/* Convierte las tildes de un texto a sus entidades HTML */
function TildesHtml($cadena){
    return str_replace(array("á","é","í","ó","ú","ñ","Á","É","Í","Ó","Ú","Ñ","ª"),
                                     array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&ntilde;",
                                                "&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;","&ordf;"), $cadena);
}

//saber utliomo dia mes
function getUltimoDiaMes($elAnio,$elMes) {
  return date("d",(mktime(0,0,0,$elMes+1,1,$elAnio)-1));
}
function restaDias($fecha,$dias){
	$fecha=strtotime($fecha);
	$fecha=strtotime("-".$dias." day",$fecha);
	return date("Y-m-d",$fecha);
}

// *********************************************************************************************
// *********** FUNCIONES DE CREACION, LISTADO Y ELIMINACION DE ARCHIVOS Y CARPETAS *************
// *********************************************************************************************

function creardir($carpeta,$ruta){// creamos una carpeta concreta en una ruta concreta de nuestro ftp, ejemplo: creardir("NUEVACARPETA","imginmuebles");
	if(is_dir($carpeta)){
		echo "el Directorio ya existe";
	}else{
		if($ftp_c = ftp_connect (FTP,6021)){
			if(ftp_login($ftp_c,USER_FTP,PASS_FTP)){
				ftp_chdir($ftp_c, $ruta);
				//echo "Carpeta actual: ".ftp_pwd($ftp_c);
				ftp_mkdir($ftp_c,$carpeta);
				//echo "Carpeta actual: ".ftp_pwd($ftp_c);
				ftp_chdir($ftp_c,$carpeta);
				//echo "Carpeta actual: ".ftp_pwd($ftp_c);
			}else{
				echo "Error: En el usuario o contrase&ntilde;a invalida";
			}
		}else{
			echo "Error: El servidor FTP no responde";
		}
		ftp_close($ftp_c);
	}
}

function eliminaArchivo($archivo,$ruta){//eliminamos un archivo de una ruta concreta, por ejemplo: eliminaArchivo("prueba.jpg","imagenes/")
	$servidor_ftp = FTP;
	$conexion_id = ftp_connect($servidor_ftp);
	$ftp_usuario = USER_FTP;
	$ftp_clave = PASS_FTP;
	$directorio = CARPETARAIZ.$ruta;
	$resultado_login = ftp_login($conexion_id,$ftp_usuario,$ftp_clave);
	ftp_pasv($conexion_id,TRUE);

	if((!$conexion_id) || (!$resultado_login)){
		return "fallo";
	} else {
		$delete = ftp_delete($conexion_id,$directorio.$archivo);
		if(!$delete){
			return "error";
		} else {
			return "Archivo '".$archivo."' eliminado con &eacute;xito.";
		}
	}
}

//********************************************************* FIN *********************************************************
//***********************************************************************************************************************

function puntoPorComa($cadena){
   return str_replace(".",",", $cadena);
}


//validar ficheros subidos
function comprobarNombresRarosExtensionesDoc($nombreFicheroCompleto){
	$correcto=false;
	$arrayComprobarMaliciosas=array(".php","%00","\x00",".js",".html",".css",".py",".c",".xml");
	for($i=0;$i<=count($arrayComprobarMaliciosas)-1;$i++){
		if (strpos($nombreFicheroCompleto, $arrayComprobarMaliciosas[$i]) !== false) {
			//echo "La cadena principal contiene el substring.";
			$correcto=false;//KO
			break;
		} else {
			//echo "La cadena principal no contiene el substring.";
			$correcto=true;//OK
		}
	}
	return $correcto;
}

function convierteFechaBarra($FechaBD){//pasa fecha a formato dd-mm-aaaa
	$dt[0] = substr($FechaBD,8,2);
	$dt[1] = substr($FechaBD,5,2);
	$dt[2] = substr($FechaBD,0,4);
	//return (join($dt,"/"));//anteriores php 7.4
	//return (join("/",$dt));//php 7.4
	return $dt[0]."/".$dt[1]."/".$dt[2];
}

function inicio_fin_semana($fecha){
    $diaInicio="Monday";
    $diaFin="Sunday";

    $strFecha = strtotime($fecha);

    $fechaInicio = date('Y-m-d',strtotime('last '.$diaInicio,$strFecha));
    $fechaFin = date('Y-m-d',strtotime('next '.$diaFin,$strFecha));

    if(date("l",$strFecha)==$diaInicio){
        $fechaInicio= date("Y-m-d",$strFecha);
    }
    if(date("l",$strFecha)==$diaFin){
        $fechaFin= date("Y-m-d",$strFecha);
    }
   
	return Array("fechaInicio"=>$fechaInicio,"fechaFin"=>$fechaFin);
}

//pasar letra de dia de semana a num dia semana
function pasarLetraDiaSemanaNumDiaSemana($letraDiaSemana){
	$numeroDiaSemana="";
	if($letraDiaSemana=="L"){
		$numeroDiaSemana=1;
	}else if($letraDiaSemana=="M"){
		$numeroDiaSemana=2;
	}else if($letraDiaSemana=="X"){
		$numeroDiaSemana=3;
	}else if($letraDiaSemana=="J"){
		$numeroDiaSemana=4;
	}else if($letraDiaSemana=="V"){
		$numeroDiaSemana=5;
	}else if($letraDiaSemana=="S"){
		$numeroDiaSemana=6;
	}else if($letraDiaSemana=="D"){
		$numeroDiaSemana=7;
	}
	
	return $numeroDiaSemana;
}

//pasar num de dia de semana a letra dia semana
function pasarNumDiaSemanaLetraDiaSemana($letraDiaSemana){
	$numeroDiaSemana="";
	if($letraDiaSemana==1){
		$numeroDiaSemana="L";
	}else if($letraDiaSemana==2){
		$numeroDiaSemana="M";
	}else if($letraDiaSemana==3){
		$numeroDiaSemana="X";
	}else if($letraDiaSemana==4){
		$numeroDiaSemana="J";
	}else if($letraDiaSemana==5){
		$numeroDiaSemana="V";
	}else if($letraDiaSemana==6){
		$numeroDiaSemana="S";
	}else if($letraDiaSemana==7){
		$numeroDiaSemana="D";
	}
	
	return $numeroDiaSemana;
}
?>