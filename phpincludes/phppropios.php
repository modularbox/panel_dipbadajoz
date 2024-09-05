<?php
//propios
													/*************************************
													 *									 *
													 *			   funcionamiento		 *
													 *									 *
													 *************************************/


//funtion obtener archivos con el mismo nombre de una misma tabla e idrelacionado
function obtenerNombreArchivoDisponible($con){	
	$total="_".date("YmdHis")."_".rand(1,100);
	
	return $total;
}
//encontrar extension
function encontrarExtension($texto){
	$posicion="";
	
	for($i=strlen($texto);$i>=0;$i--){
		if($texto[$i]=="."){
			$posicion=$i;
			break;
		}
	}
	return $posicion;
}
		
//function para comprobar que no se repita el email de acceso
function comprobarEmailUsado($email,$tabla,$id,$con){
	$duplicado="n";
	$consulta1="";
	if($id==0){
		$consulta1="";
	}else if($tabla=="usuarios"){
		$consulta1=" AND id<>".$id;
	}
	
	if($tabla!=""){
		$patron="SELECT id FROM usuarios WHERE email=\"%s\" AND guardado=\"s\" AND borrado=\"n\"%s";
		$sql=sprintf($patron,$email,$consulta1);
		$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 2389289923436653343437723424234232349943ff");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);

			$duplicado="s";
		}
	}
	return $duplicado;
}

							/******************/
							/**	gestionar rutas**/
							/******************/

//controlar el no acceso por rutas a las fichas no autorizadas
function comprobarAccesoFicha($lugar,$idelemento,$con){
	
	$correcto=false;
	if($_SESSION["permisossession"]==1){//entra admin modularbox
		$correcto=true;
	}else{//comprobar
		
		$idUser=0;
		if($_SESSION["permisossession"]==2){
			$idUser=$_SESSION["idusersession"];
		}else if($_SESSION["permisossession"]==3){
			$idUser=calculaIdEmpresa($con);
		}
		
		switch($lugar){
			case 2:
				//comprobar que la empresa solo acceda a su ficha
				$patron="SELECT id FROM usuarios WHERE id=\"%s\"";
				$sql=sprintf($patron,$idUser);
				$respuesta=mysqli_query($con,$sql) or die ("Error 12");
				if(mysqli_num_rows($respuesta)>0 && $_SESSION["permisossession"]==2){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=2&i=".$idUser;
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 4:
				//multiwater
				$patron="SELECT id FROM multiwater_nodos WHERE idusuario=\"%s\" AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 14");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=3";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 6:
				//contador
				$patron="SELECT id FROM contadores_nodos WHERE idusuario=\"%s\" AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 16");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=5";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 9:
				//luz
				$patron="SELECT id FROM luces_nodos WHERE idusuario=\"%s\" AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 167");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=8";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 12:
				//luces programa
				$patron="SELECT id FROM luces_programas WHERE idusuario=\"%s\" AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 16787632");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=11";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 14:
				//luces horario
				$patron="SELECT id FROM luces_horarios WHERE (idusuario=\"%s\" OR idusuario=0) AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 1678763");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=13";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 16:
				//safey
				$patron="SELECT id FROM safey_nodos WHERE idusuario=\"%s\" AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 16745");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=16";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 17:
				//safey accesos
				$patron="SELECT id FROM safey_accesos WHERE idusuario=\"%s\" AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 16745");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=18";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 22:
				//pistas de pael accesos
				$patron="SELECT id FROM pistaspadel_nodos WHERE idusuario=\"%s\" AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 1674576");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=21";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 24:
				//parques accesos
				$patron="SELECT id FROM parques_nodos WHERE idusuario=\"%s\" AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 16745764542454");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=23";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 27:
				//campanas accesos
				$patron="SELECT id FROM campanas_nodos WHERE idusuario=\"%s\" AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 167457646558900542454");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=26";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 29:
				//programas campanas accesos
				$patron="SELECT id FROM campanas_programas WHERE (idusuario=\"%s\" OR idusuario=0) AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 1674576445456558900542454");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=26";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 32:
				//programas automatizacion
				$patron="SELECT id FROM automatizacion_programa WHERE idusuario=\"%s\" AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 167457644545434456558900542454");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=31";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			case 34:
				//automatizacion safey
				$patron="SELECT id FROM safey_nodos WHERE idusuario=\"%s\" AND id=\"%s\"";
				$sql=sprintf($patron,$idUser,$idelemento);
				$respuesta=mysqli_query($con,$sql) or die ("Error 16567567745");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					if($fila[0]==$idelemento){
						$correcto=true;
					}else{
						$ruta="?s=33";
					}
				}else{
					$ruta="";
				}
				mysqli_free_result($respuesta);
			break;
			default:
				$ruta="";
			break;
		}
	}
	
	if(!$correcto){
		//printf("<script>location.href='index.php%s';</script>",$ruta);
		header("Location:index.php".$ruta);//por si deshabilitan el js del navegador
	}/*else{
		printf("<script>alert('todo ok')</script>");
	}*/
}
/***************************************************************/


													/*************************************
													 *									 *
													 *			   general			     *
													 *									 *
													 *************************************/								

//carga provinvias generico
function cargaProvinciasGenerico($seleccionada,$nombre,$con){
	$consulta="";
	//habilitado para admin
	if($_SESSION["permisossession"]==1 ){
		$consulta="";
	}
	
	$patron="SELECT id,provincia,pais FROM provincias WHERE (pais=\"ES\" OR pais=\"PT\")%s ORDER BY pais,provincia";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 123454");
	printf("<select class='form-control' name=\"%s\" id=\"%s\" >",$nombre,$nombre);
	printf("<option value='0'>Selecciona Provincia</option>");
	$auxPais="";
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$entra=false;
			if($auxPais!=$fila[2]){
				printf("<optgroup label='%s'>",$fila[2]);
				$auxPais=$fila[2];
			}
			
			if($fila[0]==$seleccionada){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]);
			
			if($entra){
				printf("</optgroup>");
			}
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//comprobar el numero de intentos de recuperar pass
function restringirIntentosRecuperarPass($idusuario,$tabla,$con){
	$respuesta="n";
	
	if($tabla!=""){
		$patron="SELECT id,numrecuperacionespass,frecuperacionespasss FROM %s WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
		$sql=sprintf($patron,$tabla,$idusuario);
		$respuesta=mysqli_query($con,$sql) or die ("Error 45656454455545453445655334545454553453554");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);

			if($fila[1]<3 || $fila[2]!=date("Y-m-d")){
				//si recuperar
				$respuesta="s";
				$numIntentos=$fila[1]+1;

				if($numIntentos>3){
					$numIntentos=0;
				}

			}else{
				//no recuperar
				$respuesta="n";
				if($fila[2]!=date("Y-m-d")){
					$numIntentos=0;
				}else{
					$numIntentos=$fila[1];
				}
			}

			//updatear
			$patron1="UPDATE %s SET numrecuperacionespass=\"%s\",frecuperacionespasss=\"%s\" WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$tabla,$numIntentos,date("Y-m-d"),$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 42545345434356456543454774354343");
		}
	}

	return $respuesta;
}

//calcular idempresa, segun permiso sesion
function calculaIdEmpresa($con){
	$idEmpresa=0;
	if($_SESSION["permisossession"]==1){//admin modularbox
		$idEmpresa=2;
	}else if($_SESSION["permisossession"]==2){//usuarios/empresas
		$idEmpresa=$_SESSION["idusersession"];
	}else if($_SESSION["permisossession"]==3){//invitados de las empresas
		
		$patron="SELECT idempresa FROM usuarios WHERE id=\"%s\" AND guardado=\"s\" AND borrado=\"n\"";
		$sql=sprintf($patron,$_SESSION["idusersession"]);
		$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1236464545466");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);
			$idEmpresa=$fila[0];
		}
		mysqli_free_result($respuesta);
	}
	return $idEmpresa;
}

//carga dias semana
function cargaDiasSemana($seleccionado,$nombre,$con){

	$class="";
	/*if($faltacampo && $seleccionada=="0"){
		$class=" is-invalid";
	}*/
	
	$patron="SELECT id,dia FROM semana ORDER BY id ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12345445235442656745565458466");
	printf("<select class='form-control%s' name=\"%s\" id=\"%s\" >",$class,$nombre,$nombre);
	printf("<option value='0'>Selecciona Día</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionado){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]);
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//carga usuarios generico
function cargaUsuariosGenerico($seleccionada,$nombre,$faltacampo,$con){

	$consulta="";
	if($_SESSION["permisossession"]!=1){//resto usuarios
		
		if($_SESSION["permisossession"]==3){
			$patron3="SELECT idempresa FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
			$sql3=sprintf($patron3,$_SESSION["idusersession"]);
			$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 123453333466");
			if(mysqli_num_rows($respuesta3)>0){
				$fila3=mysqli_fetch_array($respuesta3);
				$consulta=" AND id=\"".$fila3[0]."\"";
			}
			mysqli_free_result($respuesta3);
			
			
		}else{
			$consulta=" AND (id=".$_SESSION["idusersession"]." AND permisos=\"2\")";
		}
		
	}else{//administrador
		$consulta=" AND (permisos=\"1\" OR permisos=\"2\")";
	}
	
	$class="";
	if($faltacampo && $seleccionada=="0"){
		$class=" is-invalid";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre ASC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12345466");
	printf("<select class='form-control%s' name=\"%s\" id=\"%s\" >",$class,$nombre,$nombre);
	printf("<option value='0'>Selecciona Cliente</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionada || ($fila[0]==$_SESSION["idusersession"] && $_SESSION["permisossession"]==2)){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]);
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//function recursiva para comprobar token no usado
function creaCompruebaTokenUsado($token,$tabla,$con){
    $consulta="";
    if($token==""){
        $tokenGenerado=generaCodigo(4,0)."-".generaCodigo(4,1)."-".generaCodigo(15,0);
        $consulta=" WHERE token=\"".$tokenGenerado."\"";
    }else{
        $consulta=" WHERE token=\"".$token."\"";
    }
    
    $patron="SELECT id FROM %s%s";
    $sql=sprintf($patron,$tabla,$consulta);
    $respuesta=mysqli_query($con,$sql) or die ("Error 223453476745457812700234s56565");
    if(mysqli_num_rows($respuesta)>0){//contadores
        $tokenGenerado=generaCodigo(4,0)."-".generaCodigo(4,1)."-".generaCodigo(15,0);
        
        creaCompruebaTokenUsado($tokenGenerado,$con);
    }else{
		$patron1="SELECT id FROM %s%s";
		$sql1=sprintf($patron1,$tabla,$consulta);
		$respuesta1=mysqli_query($con,$sql1) or die ("Error 22345347674115457812700234s56565");
		if(mysqli_num_rows($respuesta1)>0){//luces
			$tokenGenerado=generaCodigo(4,0)."-".generaCodigo(4,1)."-".generaCodigo(15,0);

			creaCompruebaTokenUsado($tokenGenerado,$con);
		}
		mysqli_free_result($respuesta1);
	}
	mysqli_free_result($respuesta);
    return $tokenGenerado;
}

													/*************************************
													 *									 *
													 *			   empresas			     *
													 *									 *
													 *************************************/

// CARGA empresas- listadoc
function cargaEmpresasList($con){

	$buscado="";

	if($_SESSION["permisossession"]==1){
		$buscado=" AND permisos<>\"3\"";
	}else if($_SESSION["permisossession"]==2){
		$buscado=" AND permisos=\"2\"";
	}else if($_SESSION["permisossession"]==3){
		$buscado=" AND permisos=\"3\"";
	}

	$patron="SELECT id,nombre,permisos,email,telefono,direccion FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s";
	$sql=sprintf($patron,$buscado);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9a33434346fa63se3334r2eswr");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Dirección</th>
					  <th>Email</th>
					  <th>Teléfono</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$botonesAcciones="";
			if($_SESSION["permisossession"]==1){
				//***si hacemos el boton conectar bastaria con cambiar el permisosesion al 2 y el idusersesion al que aplique
				
				//$botonesAcciones="<button type='button' class='btn btn-light font-weight-bold btn-sm mr-2' onClick='alert('INVITAR');'>Invitar</button><button type='button' class='btn btn-success font-weight-bold btn-sm' onClick='alert('CONECTAR');'>Conectar</button>";
			}
			
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=2&i=%s\");'",$fila[0]);
			
			printf("<tr>
				<td></td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='' %s>%s</td>
				</tr>",$funcion,$fila[1],$funcion,$fila[5],$funcion,$fila[3],$funcion,$fila[4],$funcion,$botonesAcciones);
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Dirección</th>
					  <th>Emails</th>
					  <th>Teléfono</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}

function accessoUsuariosClientes($idcliente,$con){
	$patron="SELECT id,nombre,telefono,email,aes_decrypt(contrasena, \"%s\") FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\" AND idempresa=\"%s\" AND permisos=\"3\"";
	$sql=sprintf($patron,BBDDK,$idcliente);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96324447007656555899");
	if(mysqli_num_rows($respuesta)>0){
		printf("<thead>
					<tr>
                      <th>#</th>
					  <th>Nombre</th>
					  <th>Teléfono</th>
					  <th>Contraseña</th>
					  <th>Email</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>");
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$botones="<a href='#' class='btn btn-icon btn-light btn-hover-primary btn-sm mx-3' 	 		onClick='mostrarOcultarPass(\"contrasenaac".$fila[0]."\");return false;'>
					<i class='flaticon-eye'></i>
					</a>
					<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm mx-3' 	 		onClick='editarUsuarioCliente(\"".$idcliente."\",\"".$fila[0]."\");return false;'>
						<span class='svg-icon svg-icon-md svg-icon-success'>
							<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Write.svg-->
							<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<rect x='0' y='0' width='24' height='24' />
									<path d='M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z' fill='#000000' fill-rule='nonzero' transform='translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953)' />
									<path d='M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z' fill='#000000' fill-rule='nonzero' opacity='0.3' />
								</g>
							</svg>
							<!--end::Svg Icon-->
						</span>
					</a>
					<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas 					seguro de que deseas eliminar este registro?\",5,\"".$idcliente."\",\"".$fila[0]."\",\"\");return false;'>
						<span class='svg-icon svg-icon-md svg-icon-danger'>
							<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Write.svg-->
							<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<rect x='0' y='0' width='24' height='24'></rect>
									<path d='M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z' fill='#000000' fill-rule='nonzero'></path>
									<path d='M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z' fill='#000000' opacity='0.3'></path>
								</g>
							</svg>
							<!--end::Svg Icon-->
						</span>
					</a>";
			
			printf("<tr>
                        <td></td>
						<td><input type='text' class='form-control' id='nombreac%s' value='%s' placeholder='Nombre'/></td>
						<td><input type='text' class='form-control' id='telefonoac%s' value='%s' placeholder='Teléfono'/></td>
						<td><input type='password' class='form-control' id='contrasenaac%s' value='%s' placeholder='Contraseña'/></td>
						<td><input type='text' class='form-control' id='emailac%s' value='%s' placeholder='Email'/></td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$fila[0],$fila[1],$fila[0],$fila[2],$fila[0],$fila[4],$fila[0],$fila[3],$botones);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Contraseña</th>
                        <th>Email</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>');
	}
}
													/*************************************
													 *									 *
													 *	   almacen pines y llaves		 *
													 *									 *
													 *************************************/

//crear y configurar credenciales PIN
function credencialesPinAlmacenConfiguracion($con){
	$patron="SELECT id,pin,pinserie,pinserial FROM almacen_credenciales_pin WHERE borrado=\"n\"";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963287874447007656343435346555899");
	if(mysqli_num_rows($respuesta)>0){
		printf("<thead>
					<tr>
						<th>#</th>
						<th>ID Pin</th>
						<th>Pin</th>
						<th>Pin Serie</th>
						<th>Pin Serial</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody>");
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
	
			$botones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm ' onClick='editarCredencialPinAlmacen(\"".$fila[0]."\");return false;' title='Guardar Cambios'>
							<span class='svg-icon svg-icon-md svg-icon-success'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Save.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<polygon points='0 0 24 0 24 24 0 24'/>
									<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
									<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
								</g>
							</svg><!--end::Svg Icon-->
							</span>
					</a>
					<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas 					seguro de que deseas eliminar este registro?\",23,\"".$fila[0]."\",\"\");return false;'>
						<span class='svg-icon svg-icon-md svg-icon-danger'>
							<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Write.svg-->
							<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<rect x='0' y='0' width='24' height='24'></rect>
									<path d='M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z' fill='#000000' fill-rule='nonzero'></path>
									<path d='M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z' fill='#000000' opacity='0.3'></path>
								</g>
							</svg>
							<!--end::Svg Icon-->
						</span>
					</a>";
			
			printf("<tr>
                        <td></td>
						<td><input type='text' class='form-control inputReadOnly' id='idPinCredencialAlmacen%s' value='%s' readonly/></td>
						<td><input type='text' class='form-control' id='pinCredencialAlmacen%s' value='%s' placeholder='Pin'/></td>
						<td><input type='text' class='form-control' id='pinSerieCredencialAlmacen%s' value='%s' placeholder='Pin Serie'/></td>
						<td><input type='text' class='form-control' id='pinSerialCredencialAlmacen%s' value='%s' placeholder='Pin Serial'/></td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$fila[0],$fila[0],$fila[0],$fila[1],$fila[0],$fila[2],$fila[0],$fila[3],$botones);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
                    <tr>
                        <th>#</th>
						<th>ID Pin</th>
						<th>Pin</th>
						<th>Pin Serie</th>
						<th>Pin Serial</th>
						<th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>');
	}
}

//crear y configurar credenciales LLAVE almacen
function credencialesLlaveAlmacenConfiguracion($con){
	$patron="SELECT id,llaveserie,llavepinserial,tipo,frecuencia,descripcion,color FROM almacen_credenciales_llaves WHERE borrado=\"n\" AND fechaalta>=\"2022-11-29\" ORDER BY id ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632444776562475334534657855899");
	if(mysqli_num_rows($respuesta)>0){
		printf("<thead>
					<tr>
						<th>#</th>
                        <th>ID Llave</th>
                        <th>Nombre</th>
						<th>Llave Serie</th>
						<th>Llave Serial</th>
						<th>Tipo</th>
                        <th>Color</th>
                        <th>Frecuencia</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody>");
		$idUsuarioAux=-1;
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
            
            $tipo="Sin datos";
            if($fila[3]==1){
                $tipo="Tarjeta";
            }else if($fila[3]==2){
                $tipo="LLaveros";
            }else if($fila[3]==3){
                $tipo="Taco Metacrilato";
            }else if($fila[3]==4){
                $tipo="Pulsera";
            }else if($fila[3]==5){
                $tipo="Etiqueta";
            }else if($fila[3]==6){
                $tipo="Pegatina";
            }
            
            $frecuencia="Sin datos";
            if($fila[4]=="13.56"){
                $frecuencia="13.56 Mhz";
            }else if($fila[4]=="156"){
                $frecuencia="125 Mhz";
            }
	
			$botones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm ' onClick='editarCredencialLlaveAlmacen(\"".$fila[0]."\");return false;' title='Guardar Cambios'>
							<span class='svg-icon svg-icon-md svg-icon-success'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Save.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<polygon points='0 0 24 0 24 24 0 24'/>
									<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
									<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
								</g>
							</svg><!--end::Svg Icon-->
							</span>
					</a>
					<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas 					seguro de que deseas eliminar este registro?\",24,\"".$fila[0]."\",\"\");return false;'>
						<span class='svg-icon svg-icon-md svg-icon-danger'>
							<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Write.svg-->
							<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<rect x='0' y='0' width='24' height='24'></rect>
									<path d='M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z' fill='#000000' fill-rule='nonzero'></path>
									<path d='M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z' fill='#000000' opacity='0.3'></path>
								</g>
							</svg>
							<!--end::Svg Icon-->
						</span>
					</a>";
			
			
			//color div celda
			$colorCelda="#ffffff";
			if($fila[6]>0){
				$patron2="SELECT color,valor FROM coloresgenericos WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$fila[6]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 56690754753545464356466356893456456540097");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$colorCelda=$fila2[1];
				}
				mysqli_free_result($respuesta2);
			}
			
			$divColor="<div id='color".$fila[0]."' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color:".$colorCelda.";cursor: not-allowed;' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><input type='hidden' id='luzModal16_4_hidden' value='1'>";
			
			printf("<tr>
                        <td></td>
						<td><input type='text' class='form-control inputReadOnly' id='llaveiDCredencialAlmacen%s' value='%s' placeholder='' readonly/></td>
						<td><input type='text' class='form-control' id='llaveDescripcionCredencialAlmacen%s' value='%s' placeholder='' /></td>
						<td><input type='text' class='form-control inputReadOnly' id='llaveSerieCredencialAlmacen%s' value='%s' placeholder='Llave Serie' readonly/></td>
						<td><input type='text' class='form-control inputReadOnly' id='llaveSerialCredencialAlmacen%s' value='%s' placeholder='Llave Serial' readonly/></td>
						<td><input type='text' class='form-control inputReadOnly' id='llaveTipoCredencialAlmacen%s' value='%s' placeholder='Tipo' readonly/>
						<td>%s</td>
						<td><input type='text' class='form-control inputReadOnly' id='llaveFrecuenciaCredencialAlmacen%s' value='%s' placeholder='Tipo' readonly/></td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$fila[0],$fila[0],$fila[0],$fila[5],$fila[0],$fila[1],$fila[0],$fila[2],$fila[0],$tipo,$divColor,$fila[0],$frecuencia,$botones);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
                    <tr>
						<th>#</th>
                        <th>ID Llave</th>
                        <th>Nombre</th>
						<th>Llave Serie</th>
						<th>Llave Serial</th>
						<th>Tipo</th>
                        <th>Color</th>
                        <th>Frecuencia</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

function conversor_segundos($seg) {
  $segundos = $seg;
	$horas = floor($segundos/ 3600);
	$minutos = floor(($segundos - ($horas * 3600)) / 60);
	$segundos = $segundos - ($horas * 3600) - ($minutos * 60);
	
	$devolver="";
	if($horas>0){
		$devolver.=$horas . " h,";
	}
	if($minutos>0){
		$devolver.=$minutos . " min, ";
	}
	if($segundos>0){
		$devolver.=$segundos . " seg";
	}
	return $devolver;
}

//carga modelos raspberry generico
function cargaModelosRPIGenerico($seleccionada,$nombre,$faltacampo,$con){

	$class="";
	/*if($faltacampo && $seleccionada=="0"){
		$class=" is-invalid";
	}*/
	
	$patron="SELECT id,modelo FROM tiporaspberry WHERE borrado=\"n\" ORDER BY id ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12344574565465466");
	printf("<select class='form-control%s' name=\"%s\" id=\"%s\" >",$class,$nombre,$nombre);
	printf("<option value='0'>Selecciona Modelo RPI:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionada){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]);
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//carga tipos cerradura generico
function cargaTiposCerraduraGenerico($seleccionada,$nombre,$faltacampo,$con){

	$class="";
	/*if($faltacampo && $seleccionada=="0"){
		$class=" is-invalid";
	}*/
	
	$patron="SELECT id,tipo FROM tiposcerraduras WHERE borrado=\"n\" ORDER BY id ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12344345655574565465466");
	printf("<select class='form-control%s' name=\"%s\" id=\"%s\" >",$class,$nombre,$nombre);
	printf("<option value='0'>Selecciona Tipo:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionada){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]);
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//carga tipos foco generico
function cargaTiposFocoGenerico($seleccionada,$nombre,$faltacampo,$con){

	$class="";
	/*if($faltacampo && $seleccionada=="0"){
		$class=" is-invalid";
	}*/
	
	$patron="SELECT id,tipo FROM tiposfocos WHERE borrado=\"n\" ORDER BY id ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 123443456555799456574565465466");
	printf("<select class='form-control%s' name=\"%s\" id=\"%s\" >",$class,$nombre,$nombre);
	printf("<option value='0'>Selecciona Tipo:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionada){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]);
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//carga tipos metodo de pago
function cargaTiposMetodosPago($seleccionada,$nombre,$nodo,$con){

	$class="";
	
	//excluir metodos de pago ya añadidos a un nodo
	$excluir="";
	$patron2="SELECT metodopago FROM safey_metodospago WHERE idnodo=\"%s\" AND borrado=\"n\"";
	$sql2=sprintf($patron2,$nodo);
	$respuesta2=mysqli_query($con,$sql2) or die ("Error 3097134565234378465698234984840035545453245454");
	if(mysqli_num_rows($respuesta2)>0){
		for($j=0;$j<mysqli_num_rows($respuesta2);$j++){
			$fila2=mysqli_fetch_array($respuesta2);

			$excluir.=" AND id<>".$fila2[0];
		}
	}
	mysqli_free_result($respuesta2);
	
	$patron="SELECT id,tipo FROM metodospago WHERE borrado=\"n\"%s ORDER BY id DESC";
	$sql=sprintf($patron,$excluir);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 10234434565557994508346574565465466");
	printf("<select class='form-control%s' name=\"%s\" id=\"%s\" >",$class,$nombre,$nombre);
	printf("<option value='0'>Selecciona Tipo:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionada){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]);
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//carga tipos metodo de pago para tabla
function cargaTiposMetodosPagoParaTabla($seleccionada,$nombre,$nodo,$con){
	$dev="";
	$class="";
	
	//excluir metodos de pago ya añadidos a un nodo
	$excluir="";
	$patron2="SELECT metodopago FROM safey_metodospago WHERE idnodo=\"%s\" AND borrado=\"n\"";
	$sql2=sprintf($patron2,$nodo);
	$respuesta2=mysqli_query($con,$sql2) or die ("Error 3097134565234378465698234984840035545453245454");
	if(mysqli_num_rows($respuesta2)>0){
		for($j=0;$j<mysqli_num_rows($respuesta2);$j++){
			$fila2=mysqli_fetch_array($respuesta2);

			$excluir.=" AND id<>".$fila2[0];
		}
	}
	mysqli_free_result($respuesta2);
	
	$patron="SELECT id,tipo FROM metodospago WHERE borrado=\"n\"%s ORDER BY id ASC";
	$sql=sprintf($patron,$excluir);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 10234434565557994508346574565465466");
	$dev.=sprintf("<select class='form-control%s' name=\"%s\" id=\"%s\" >",$class,$nombre,$nombre);
	$dev.=sprintf("<option value='0'>Selecciona Tipo:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionada){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			$dev.=sprintf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]);
		}
	}
	$dev.=sprintf("</select>");
	mysqli_free_result($respuesta);
	return $dev;
}
?>