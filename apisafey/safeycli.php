<?php

//establecer por defecto
date_default_timezone_set('Europe/Madrid');
		
header("Access-Control-Allow-Origin: *");

require_once("../const/constantes.php");
require_once("../phpincludes/phpgeneral.php");
require_once("../phpincludes/phppropios.php");
require_once("../phpincludes/phpsafey.php");


//https://panel.modularbox.com/apisafey/safeycli.php?returnClientesSafeyNodos=aa&token=mo24Du&internal=24042024
//&cli=2
if(isset($_GET["returnClientesSafeyNodos"])){

	/*----devolver los clientes de safey con sus nodos**********/
	
    //$correcto="n";
	$token=quitaComillasD($_GET["token"]);//obligatorio
	$internal=quitaComillasD($_GET["internal"]);//obligatorio
	$cli=intval(quitaComillasD($_GET["cli"]));//opcional
	
	$arrayCompletoJson=array();//json a devolver
	if($token=="mo24Du" && $internal=="24042024"){
		$consulta="";
		if($cli>0){
			$consulta=" AND id=\"".$cli."\"";
		}
		
		$patron="SELECT id,nombre,email FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\" AND (permisos=\"2\" OR permisos=\"1\") %s";
        $sql=sprintf($patron,$consulta);
        $respuesta=mysqli_query($con,$sql) or die ("Error 12356563534534534534344677");
        if(mysqli_num_rows($respuesta)>0){
			for($i=0;$i<mysqli_num_rows($respuesta);$i++){
				$fila=mysqli_fetch_array($respuesta);
				
				$idClienteUsuario=intval($fila[0]);
				$nombreCliente=$fila[1];
				$emailCliente=$fila[2];

				//declaro array completo cliente
				$arrayCliente=array();
				
				//datos del cliente
				$arrayDatosCliente = array("codCli" => $idClienteUsuario,
									  "nombreCli" => $nombreCliente,
									  "emailCli" => $emailCliente
									);
				
				/*START recorrer los nodos del cliente*/
				$patron1="SELECT id,nombre,preciodiario,preciosemanal,preciomensual,preciotrimestral,preciosemestral,precioanual,activamodalidaddiario,activamodalidadsemanal,activamodalidadmensual,activamodalidadtrimestral,activamodalidadsemestral,activamodalidadanual FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND idusuario=\"%s\"";
				$sql1=sprintf($patron1,$idClienteUsuario);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error 1235656353453453446556534344677");
				if(mysqli_num_rows($respuesta1)>0){
					$arrayNodosCliente=array();
					for($j=0;$j<mysqli_num_rows($respuesta1);$j++){
						$fila1=mysqli_fetch_array($respuesta1);

						$idNodo=intval($fila1[0]);
						$nombreNodo=$fila1[1];
						
						$preciodiario=floatval($fila1[2]);
						$precioSemanal=floatval($fila1[3]);
						$precioMensual=floatval($fila1[4]);
						$precioTrimestral=floatval($fila1[5]);
						$precioSemestral=floatval($fila1[6]);
						$precioAnual=floatval($fila1[7]);
						
						$activadaModalidadPreciodiario=intval($fila1[8]);
						if($activadaModalidadPreciodiario==1){//activa
							$activadaModalidadPreciodiario=true;
						}else {
							$activadaModalidadPreciodiario=false;
						}
						
						$activadaModalidadPrecioSemanal=intval($fila1[9]);
						if($activadaModalidadPrecioSemanal==1){//activa
							$activadaModalidadPrecioSemanal=true;
						}else {
							$activadaModalidadPrecioSemanal=false;
						}
						
						$activadaModalidadPrecioMensual=intval($fila1[10]);
						if($activadaModalidadPrecioMensual==1){//activa
							$activadaModalidadPrecioMensual=true;
						}else {
							$activadaModalidadPrecioMensual=false;
						}
						
						$activadaModalidadPrecioTrimestral=intval($fila1[11]);
						if($activadaModalidadPrecioTrimestral==1){//activa
							$activadaModalidadPrecioTrimestral=true;
						}else {
							$activadaModalidadPrecioTrimestral=false;
						}
						
						$activadaModalidadPrecioSemestral=intval($fila1[12]);
						if($activadaModalidadPrecioSemestral==1){//activa
							$activadaModalidadPrecioSemestral=true;
						}else {
							$activadaModalidadPrecioSemestral=false;
						}
						
						$activadaModalidadPrecioAnual=intval($fila1[13]);
						if($activadaModalidadPrecioAnual==1){//activa
							$activadaModalidadPrecioAnual=true;
						}else {
							$activadaModalidadPrecioAnual=false;
						}

						$arrayDatosNodo = array("codNodo" => $idNodo,
										   "precioDiario" => $preciodiario,
										   "activadaModalidadPreciodiario" => $activadaModalidadPreciodiario,
										   "precioSemanal" => $precioSemanal,
										   "activadaModalidadPrecioSemanal" => $activadaModalidadPrecioSemanal,
										   "precioMensual" => $precioMensual,
										   "activadaModalidadPrecioMensual" => $activadaModalidadPrecioMensual,
										   "precioTrimestral" => $precioTrimestral,
										   "activadaModalidadPrecioTrimestral" => $activadaModalidadPrecioTrimestral,
										   "precioSemestral" => $precioSemestral,
										   "activadaModalidadPrecioSemestral" => $activadaModalidadPrecioSemestral,
										   "precioAnual" => $precioAnual,
										   "activadaModalidadPrecioAnual" => $activadaModalidadPrecioAnual
										  );
						//anadir array datos nodo al array de nodos de ese cliente
						array_push($arrayNodosCliente, $arrayDatosNodo);
					}
					//-----anadir datos del cliente al array cliente-----
					array_push($arrayCliente, $arrayDatosCliente);
					
					//-----anadir datos de los nodos al array cliente-----
					array_push($arrayCliente, $arrayNodosCliente);
					
					//-----anadir los datos de este cliente al array general, SOLO en caso de tener nodos-----
					array_push($arrayCompletoJson, $arrayCliente);
				}
				mysqli_free_result($respuesta1);
				/*END recorrer los nodos del cliente*/
			}
		}
		mysqli_free_result($respuesta);
		
		//codificamos el json
		print_r(json_encode($arrayCompletoJson));
		
	}else{
		echo "";
	}
}

//https://panel.modularbox.com/apisafey/safeycli.php
if(isset($_POST["recibirPagoSafeyNodos"])){

	/*----crear y registrar el pago, devolver el pin y si el ciente ya existe**********/
	
    //$correcto="n";
	if(isset($_POST["token"])){
		$token=quitaComillasD($_POST["token"]);//obligatorio
	}else{
		$token="";
	}
	if(isset($_POST["internal"])){
		$internal=quitaComillasD($_POST["internal"]);//obligatorio
	}else{
		$internal="";
	}
	if(isset($_POST["codNodo"])){
		$idNOdo=quitaComillasD($_POST["codNodo"]);//obligatorio
	}else{
		$idNOdo="";
	}
	if(isset($_POST["cli"])){
		$idCli=quitaComillasD($_POST["cli"]);//obligatorio
	}else{
		$idCli="";
	}
	
	if(isset($_POST["puertaPago"])){
		$puertaPago=quitaComillasD($_POST["puertaPago"]);// -->1 -->2 -->amb PENDIENTE DE HACER
	}else{
		$puertaPago="amb";
	}
	
	/*start datos recibidos DESDE la web o tpv*/
	if(isset($_POST["nombre"])){
		$nombre=quitaComillasD($_POST["nombre"]);//obligatorio
	}else{
		$nombre="";
	}

	if(isset($_POST["apellidos"])){
		$apellidos=quitaComillasD($_POST["apellidos"]);
	}else{
		$apellidos="";
	}
	
	if(isset($_POST["dni"])){//obligatorio
		$dni=quitaComillasD($_POST["dni"]);
	}else{
		$dni="";
	}
	
	if(isset($_POST["direccion"])){
		$direccion=quitaComillasD($_POST["direccion"]);
	}else{
		$direccion="";
	}
	
	if(isset($_POST["localidad"])){
		$localidad=quitaComillasD($_POST["localidad"]);
	}else{
		$localidad="";
	}
	
	if(isset($_POST["codigo_postal"])){
		$codigo_postal=quitaComillasD($_POST["codigo_postal"]);
	}else{
		$codigo_postal="";
	}
	
	if(isset($_POST["provincia"])){
		$provincia=ucfirst(strtolower(quitaComillasD($_POST["provincia"])));
        
        $patron61="SELECT id FROM provincias WHERE provincia LIKE \"%s%s%s\"";
        $sql61=sprintf($patron61,"%",$provincia,"%");
        $respuesta61=mysqli_query($con,$sql61) or die ("Error 345612432432");
        if(mysqli_num_rows($respuesta61)>0){
        	$fila61=mysqli_fetch_array($respuesta61);
            $provincia=$fila61[0];
        }else{
            $provincia=0;
        }
        mysqli_free_result($respuesta61);
	}else{
		$provincia=0;
	}
	
	if(isset($_POST["correo"])){
		$correo=quitaComillasD($_POST["correo"]);//obligatorio
	}else{
		$correo="";
	}
	
	if(isset($_POST["edad"])){
		$edad=quitaComillasD($_POST["edad"]);
	}else{
		$edad=0;
	}
	
	if(isset($_POST["empadronado"])){
		$empadronado=quitaComillasD($_POST["empadronado"]);
         if($empadronado){
            $empadronado="s";
        }else{
            $empadronado="n";
        }
	}else{
		$empadronado="n";
	}
	
	if(isset($_POST["residente"])){
		$residente=quitaComillasD($_POST["residente"]);
        if($residente){
            $residente="s";
        }else{
            $residente="n";
        }
	}else{
		$residente="n";
	}
	
	if(isset($_POST["dinero_pagado"])){
		//$dinero_pagado=floatval(quitaComillasD($_POST["dinero_pagado"]));//en euros
        if($_POST["dinero_pagado"]>0){//no esperamos negativos,
            $dinero_pagado=floatval(quitaComillasD($_POST["dinero_pagado"]))/100;//en centimos
        }else{
            $dinero_pagado=0;
        }
	}else{
		$dinero_pagado=0;
	}
	
	if(isset($_POST["telefono"])){
		$telefono=quitaComillasD($_POST["telefono"]);
	}else{
		$telefono="";
	}
    
    if(isset($_POST["tipo_subscripcion"])){
		$tipoSubscripcion=quitaComillasD($_POST["tipo_subscripcion"]);
		if($tipoSubscripcion=="diario" || $tipoSubscripcion=="semanal" || $tipoSubscripcion=="mensual" || $tipoSubscripcion=="trimestral" || $tipoSubscripcion=="semestral" || $tipoSubscripcion=="anual"){
			//ok
		}else{
			//mal
			$tipoSubscripcion="diario";//por defecto
		}
	}else{
		$tipoSubscripcion="";
	}
	
	if(isset($_POST["metodo_pago"])){
		$metodo_pago=intval(quitaComillasD($_POST["metodo_pago"]));
	}else{
		$metodo_pago=7;//tarjeta ceca por defecto
	}
	
	if(isset($_POST["cod_promocional"])){
		$cod_promocional=quitaComillasD($_POST["cod_promocional"]);
	}else{
		$cod_promocional="";//por defecto
	}
	if(isset($_POST["num_operacion"])){
		$numOperacion=quitaComillasD($_POST["num_operacion"]);
	}else{
		$numOperacion="";//por defecto
	}
	if(isset($_POST["conf_economica"])){
		$conf_economica=quitaComillasD($_POST["conf_economica"]);
	}else{
		$conf_economica=1;//por defecto
	}
	/*end datos recibidos DESDE la web o tpv*/
	
	if($token=="mo24Du" && $internal=="30042024" && $idNOdo>0 && $idCli>0 && $dni!="" && $nombre!="" && ($correo!="" && filter_var($correo, FILTER_VALIDATE_EMAIL)) ){
		
		$arrayCompletoJson=array();//json a devolver
		
		$patron="SELECT safey_nodos.id,usuarios.id FROM safey_nodos,usuarios WHERE safey_nodos.borrado=\"n\" AND safey_nodos.guardado=\"s\" AND safey_nodos.id=\"%s\" AND usuarios.id=safey_nodos.idusuario AND usuarios.borrado=\"n\" AND usuarios.guardado=\"s\" AND usuarios.id=\"%s\"";
        $sql=sprintf($patron,$idNOdo,$idCli);
        $respuesta=mysqli_query($con,$sql) or die ("Error 12356534564545447564677");
        if(mysqli_num_rows($respuesta)>0){
        	$fila=mysqli_fetch_array($respuesta);
			
			$idCliente=$fila[1];
			
			$pinObtenido=false;
			
			/*$consulta="";
			if($dni!=""){
				$consulta=" AND dni=\"".$dni."\"";//debe ser obligatorio
			}*/
			
			/*start ver si existe el usuario*/
			$patron1="SELECT id,idusuario FROM safey_accesos WHERE borrado=\"n\" AND email=\"%s\" AND dni=\"%s\"";
			$sql1=sprintf($patron1,$correo,$dni);//seria mejor validar con telefono y email
			$respuesta1=mysqli_query($con,$sql1) or die ("Error 123565345641115454471564677");
			if(mysqli_num_rows($respuesta1)>0){
				$fila1=mysqli_fetch_array($respuesta1);
				
				$idAcceso=$fila1[0];
				
                //update
                $patron5="UPDATE safey_accesos SET nombre=\"%s\",telefono=\"%s\",dni=\"%s\",email=\"%s\",apellidos=\"%s\", direccion=\"%s\",localidad=\"%s\",codpostal=\"%s\",provincia=\"%s\",edad=\"%s\",empadronado=\"%s\",residente=\"%s\",tiposuscripcion=\"%s\",dineropagado=\"%s\" WHERE id=\"%s\"";
                $sql5=sprintf($patron5,$nombre,$telefono,$dni,$correo,$apellidos,$direccion,$localidad,$codigo_postal,$provincia,$edad,$empadronado,$residente,$tipoSubscripcion,$dinero_pagado,$idAcceso);
                $respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 1345345546555666345763");
                
				//existe, ver si existe pin o crear pin
				$patron2="SELECT id,pin FROM safey_credenciales_pin WHERE borrado=\"n\" AND idacceso=\"%s\" AND idusuario=\"%s\"";
				$sql2=sprintf($patron2,$idAcceso,$idCliente);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error 123565345641115425447152646277");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					//existe
					$pinParaAbrir=$fila2[1];
					
					/*START gestionar el pago historial*/
					$descuento=0;
					$fechaInicioPeriodo=date("Y-m-d");//FALTA GESTIONAR SEGUN TIPO SERVICIO
					$fechaFinPeriodo=date("Y-m-d");//FALTA GESTIONAR SEGUN TIPO SERVICIO
					$patron34="INSERT INTO safey_pagos SET idacceso=\"%s\",idusuario=\"%s\",idnodo=\"%s\",puerta=\"%s\" ,tiposervicio=\"%s\",codigopromocional=\"%s\",descuento=\"%s\",total=\"%s\",metodopago=\"%s\",fechainicio=\"%s\"    ,fechafin=\"%s\",fechapago=\"%s\",numoperacion=\"%s\",configeconomica=\"%s\",borrado=\"n\"";
					$sql34=sprintf($patron34,$idAcceso,$fila1[1],$idNOdo,$puertaPago,$tipoSubscripcion,$cod_promocional,$descuento,$dinero_pagado,$metodo_pago,$fechaInicioPeriodo,$fechaFinPeriodo,date("Y-m-d"),$numOperacion,$conf_economica);
					$respuesta34=mysqli_query($con,$sql34) or die ("Error al 1234503314434347756831565746645574546");
					/*END gestionar el pago historial*/
					
					array_push($arrayCompletoJson, $idAcceso);//id acceso
					array_push($arrayCompletoJson, true);//existe ya
					array_push($arrayCompletoJson, $pinParaAbrir);//pin para abrir
					
					$pinObtenido=true;
				}else{
					//no existe, seleccionar de los pines disponibles del cliente para crear y asociar un pin
					$patron3="SELECT id,idusuario,pin FROM safey_credenciales_pin WHERE borrado=\"n\" AND idacceso=\"0\" AND idusuario=\"%s\" ORDER BY id ASC LIMIT 0,1";
					$sql3=sprintf($patron3,$idCliente);
					$respuesta3=mysqli_query($con,$sql3) or die ("Error 12356534545641115425447152646277");
					if(mysqli_num_rows($respuesta3)>0){
						$fila3=mysqli_fetch_array($respuesta3);
						
						$pinParaAbrir=$fila3[2];
						
						//actualizar el pin con ese credencial acceso
						$patron31="UPDATE safey_credenciales_pin SET idacceso=\"%s\" WHERE id=\"%s\" AND idusuario=\"%s\"";
				        $sql31=sprintf($patron31,$idAcceso,$fila3[0],$fila3[1]);
				        $respuesta31=mysqli_query($con,$sql31) or die ("Error al 123450331445751217756831565746645574546");
						
						//actualizar ese credencial de acceso con este pin
						$patron32="UPDATE safey_accesos SET pin=\"%s\" WHERE id=\"%s\" AND idusuario=\"%s\"";
				        $sql32=sprintf($patron32,$fila3[0],$idAcceso,$fila3[1]);
				        $respuesta32=mysqli_query($con,$sql32) or die ("Error al 123250331445751326831565746645574546");
						
						
						/*START gestionar el pago historial*/
						$descuento=0;
						$fechaInicioPeriodo=date("Y-m-d");//FALTA GESTIONAR SEGUN TIPO SERVICIO
						$fechaFinPeriodo=date("Y-m-d");//FALTA GESTIONAR SEGUN TIPO SERVICIO
						$patron34="INSERT INTO safey_pagos SET idacceso=\"%s\",idusuario=\"%s\",idnodo=\"%s\",puerta=\"%s\" ,tiposervicio=\"%s\",codigopromocional=\"%s\",descuento=\"%s\",total=\"%s\",metodopago=\"%s\",fechainicio=\"%s\"    ,fechafin=\"%s\",fechapago=\"%s\",numoperacion=\"%s\",configeconomica=\"%s\",borrado=\"n\"";
						$sql34=sprintf($patron34,$idAccesoNuevo,$fila3[1],$idNOdo,$puertaPago,$tipoSubscripcion,$cod_promocional,$descuento,$dinero_pagado,$metodo_pago,$fechaInicioPeriodo,$fechaFinPeriodo,date("Y-m-d"),$numOperacion,$conf_economica);
						$respuesta34=mysqli_query($con,$sql34) or die ("Error al 1234503314434347756831565746645574546");
						/*END gestionar el pago historial*/
						
						array_push($arrayCompletoJson, $idAcceso);//id acceso
						array_push($arrayCompletoJson, true);//existe ya
						array_push($arrayCompletoJson, $pinParaAbrir);//pin para abrir
						
						$pinObtenido=true;
					}else{
						//no hay pines disponibles para este cliente //ASIGNAMOS A CLIENTE NUEVOS PINES?????
						$pinObtenido=false;
						$codigoErrorDevolver="sin_pines_disponibles";
					}
					mysqli_free_result($respuesta3);
				}
				mysqli_free_result($respuesta2);
				
			}else{
				//no existe, crear el acceso y un pin
				if($nombre!="" && $dni!="" /* $telefono!="" && */ && $correo!=""){//obligatorios//ya los compruebo arriba
					
					$patron5="INSERT INTO safey_accesos SET nombre=\"%s\",telefono=\"%s\",dni=\"%s\",guardado=\"s\",idusuario=\"%s\",email=\"%s\",pin=\"0\",pinactivo=\"off\",llave=\"0\",llaveactivo=\"off\",mando=\"0\",mandoactivo=\"off\",maillogin=\"0\",mailloginactivo=\"off\",observaciones=\"%s\",tipo=\"w\",fechaalta=\"%s\",apellidos=\"%s\", direccion=\"%s\",localidad=\"%s\",codpostal=\"%s\",provincia=\"%s\",edad=\"%s\",empadronado=\"%s\",residente=\"%s\",tiposuscripcion=\"%s\",dineropagado=\"%s\"";
					$sql5=sprintf($patron5,$nombre,$telefono,$dni,$idCliente,$correo,"Creado desde pago web.",date("Y-m-d"),$apellidos,$direccion,$localidad,$codigo_postal,$provincia,$edad,$empadronado,$residente,$tipoSubscripcion,$dinero_pagado);
					$respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 1345345555666345763");
					$idAccesoNuevo=mysqli_insert_id($con);

					if($idAccesoNuevo>0){
						//no existe, seleccionar de los pines disponibles del cliente para crear y asociar un pin
						$patron3="SELECT id,idusuario,pin FROM safey_credenciales_pin WHERE borrado=\"n\" AND idacceso=\"0\" AND idusuario=\"%s\" ORDER BY id ASC LIMIT 0,1";
						$sql3=sprintf($patron3,$idCliente);
						$respuesta3=mysqli_query($con,$sql3) or die ("Error 123565345456414345115425447152646277");
						if(mysqli_num_rows($respuesta3)>0){
							$fila3=mysqli_fetch_array($respuesta3);

							$pinParaAbrir=$fila3[2];

							//actualizar el pin con ese credencial acceso
							$patron31="UPDATE safey_credenciales_pin SET idacceso=\"%s\" WHERE id=\"%s\" AND idusuario=\"%s\"";
							$sql31=sprintf($patron31,$idAccesoNuevo,$fila3[0],$fila3[1]);
							$respuesta31=mysqli_query($con,$sql31) or die ("Error al 1234503314457676751217756831565746645574546");

							//actualizar ese credencial de acceso con este pin
							$patron32="UPDATE safey_accesos SET pin=\"%s\" WHERE id=\"%s\" AND idusuario=\"%s\"";
							$sql32=sprintf($patron32,$fila3[0],$idAccesoNuevo,$fila3[1]);
							$respuesta32=mysqli_query($con,$sql32) or die ("Error al 12325033144575189786326831565746645574546");

							
							/*START gestionar el pago historial*/
							$descuento=0;
							$fechaInicioPeriodo=date("Y-m-d");//FALTA GESTIONAR SEGUN TIPO SERVICIO
							$fechaFinPeriodo=date("Y-m-d");//FALTA GESTIONAR SEGUN TIPO SERVICIO
							$patron34="INSERT INTO safey_pagos SET idacceso=\"%s\",idusuario=\"%s\",idnodo=\"%s\",puerta=\"%s\" ,tiposervicio=\"%s\",codigopromocional=\"%s\",descuento=\"%s\",total=\"%s\",metodopago=\"%s\",fechainicio=\"%s\"    ,fechafin=\"%s\",fechapago=\"%s\",numoperacion=\"%s\",configeconomica=\"%s\",borrado=\"n\"";
							$sql34=sprintf($patron34,$idAccesoNuevo,$fila3[1],$idNOdo,$puertaPago,$tipoSubscripcion,$cod_promocional,$descuento,$dinero_pagado,$metodo_pago,$fechaInicioPeriodo,$fechaFinPeriodo,date("Y-m-d"),$numOperacion,$conf_economica);
							$respuesta34=mysqli_query($con,$sql34) or die ("Error al 1234503314434347756831565746645574546");
							/*END gestionar el pago historial*/
							
							array_push($arrayCompletoJson, $idAccesoNuevo);//id acceso
							array_push($arrayCompletoJson, true);//existe ya
							array_push($arrayCompletoJson, $pinParaAbrir);//pin para abrir

							$pinObtenido=true;
						}else{
							//no hay pines disponibles para este cliente //ASIGNAMOS A CLIENTE NUEVOS PINES- crear funtion para reutilizar?????
							$pinObtenido=false;
							$codigoErrorDevolver="sin_pines_disponibles";
						}
						mysqli_free_result($respuesta3);

					}else{
						$codigoErrorDevolver="problemas_contactar_soporte";
					}
				}

			}
			mysqli_free_result($respuesta1);
			/*end ver si existe el usuario*/
			
			if($pinObtenido){
				//codificamos el json
				print_r(json_encode($arrayCompletoJson));
			}else{
				echo $codigoErrorDevolver;
			}
		}
		mysqli_free_result($respuesta);
		
	}else{
		echo "ko";
	}
}


//https://panel.modularbox.com/apisafey/safeycli.php
if(isset($_POST["recibirConfPagoCeca"])){

	/*----crear y registrar el pago, devolver el pin y si el ciente ya existe**********/
	
    //$correcto="n";
	if(isset($_POST["numOperacion"])){
		$token=quitaComillasD($_POST["numOperacion"]);//obligatorio
	}else{
		
    }
}

//cierro la conexion
$con->close();
?>