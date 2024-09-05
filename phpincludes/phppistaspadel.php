<?php

													/*************************************
													 *									 *
													 *		     PISTAS DE PADEL         *
													 *									 *
													 *************************************/	
// CARGA pista de padel
function cargaNodosPistaPadelList($con){
	$consulta="";
	
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioPistasPadelList"]!="0"*/){
		//$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioPistasPadelList"])."\"";
		
		if($_SESSION["usuarioPistasPadelList"]!="0"){
			$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioPistasPadelList"])."\"";
		}
		
	}else{
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	if($_SESSION["conexionPistasPadelList"]!=""){
		$consulta.=" AND conexion=\"".quitaComillasD($_SESSION["conexionPistasPadelList"])."\"";
	}
	
	/*START actualizar estado placas*/
	ultimaConexionOnlinOfflinePistasPadel($con);
	/*END actualizar estado placas*/
	
	$patron="SELECT id,nombre,idusuario,conexion,ubicacion,horaultimaconsulta FROM pistaspadel_nodos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963235345467783543457879958");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>Ubicación</th>
					  <th>Conexión</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//usuario
			$patron1="SELECT nombre FROM usuarios WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$fila[2]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9635344653509258");
			$fila1=mysqli_fetch_array($respuesta1);
			mysqli_free_result($respuesta1);
			
			$botonesAcciones="";
			
			//conexion
			if($fila[3]=="on"){
				$conexion="<span class='label label-lg label-light-success label-inline'>Online</span>";
			}else if($fila[3]=="off"){
				$conexion="<span class='label label-lg label-light-danger label-inline'>Offline</span>";
				//mostrar el de encender
			}else{
				$conexion="<span class='label label-lg label-light-primary label-inline'>Sin datos</span>";
			}
			
			//pulsar tr
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=22&i=%s\");'",$fila[0]);
			
			printf("<tr>
				<td></td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class=''>%s</td>
				</tr>",$funcion,$fila[1],$funcion,$fila1[0],$funcion,$fila[4],$funcion,$conexion,$botonesAcciones);
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>XXXX</th>
					  <th>Conexión</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}

//filtro clientes Pistas de Padel list
function cargaUsuariosPistasPadelFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 31215235834375565778676741236456455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosPistasPadel(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioPistasPadelList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//filtro estado Pistas de Padel list
function cargaEstadosPistasPadelFiltro($con){
	$selectedUno="";
	$selectedDos="";
	if($_SESSION["conexionPistasPadelList"]=="on"){
		$selectedUno=" selected";
	}else if($_SESSION["conexionPistasPadelList"]=="off"){
		$selectedDos=" selected";
	}
	
	printf("<select class='form-control' id='selectConexionFiltro' onChange='filtrarConexionPistasPadel(this);'><option value=''>Selecciona Conexión:</option>");
	printf("<option value=\"on\" %s>Online</option><option value=\"off\" %s>Offline</option>",$selectedUno,$selectedDos);	
	printf('</select>');
}

//cargar puertas nodo pista padel
function cargaPuertasNodoPistaPadel($idNodo,$con){

	$consultaPermisos="";
	$nodosPermisosId="";
	if($_SESSION["permisossession"]==3){
		
		/*$patron3="SELECT idempresa FROM usuarios WHERE borrado=\"n\" AND id=\"%s\" AND guardado=\"s\"";
		$sql3=sprintf($patron3,$_SESSION["idusersession"]);
		$respuesta3=mysqli_query($con,$sql) or die ("Error 312152358570090905645589912127878515998");
		if(mysqli_num_rows($respuesta)>0 && $entraPuertas){
			for($i=0;$i<mysqli_num_rows($respuesta3);$i++){
				$fila3=mysqli_fetch_array($respuesta3);
			}
		}
		mysqli_free_result($respuesta3);*/
		
		$nodosPermisosId="";
		
		$entraPuertas=true;
	}else{
		$entraPuertas=true;
	}
	
	$patron="SELECT id,nombre,rutaimg,idusuario,estadopuertaizq,estadopuertader,fechaultimaconsulta,horaultimaconsulta FROM pistaspadel_nodos WHERE borrado=\"n\" AND id=\"%s\" AND guardado=\"s\"%s ORDER BY id ASC";
	$sql=sprintf($patron,$idNodo,$nodosPermisosId);
	$respuesta=mysqli_query($con,$sql) or die ("Error 31215235857009090564558991212787851577");
	if(mysqli_num_rows($respuesta)>0 && $entraPuertas){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//abrir div row
			if(($i % 3)==0){
				//cerrar
				if($i>0){
					printf("</div>");
				}
				printf("<div class='row'>");
			}
			
			if($fila[2]!="" && $fila[2]!="NULL"){
				$rutaImg="./archivos_subidos/clientes/".$fila[3]."/pistaspadel/".$fila[2];
				$styleImg="";
			}else{//por defecto
				$rutaImg="./nimg/pista_padel_generica.png";
				$styleImg=" style='height: 237.08px'";
			}
            
			$estadoPuertaIzq="";
			if($fila[4]==1){
				$estadoPuertaIzq="<span class='btn btn-light btn-text-success btn-hover-text-success font-weight-bold' style='cursor: default;margin-left: 6px;'>Izquierda Abierta</span>";
			}else if($fila[4]==2){
				$estadoPuertaIzq="<span class='btn btn-light btn-text-danger btn-hover-text-danger font-weight-bold' style='cursor: default;margin-left: 6px;'>Izquierda Cerrada</span>";
			}else{
				$estadoPuertaIzq="<span class='btn btn-light btn-text-warning btn-hover-text-warning font-weight-bold' style='cursor: default;margin-left: 6px;'>Izquierda Sin datos</span>";
			}
			
			$estadoPuertaDer="";
			if($fila[5]==1){
				$estadoPuertaDer="<span class='btn btn-light btn-text-success btn-hover-text-success font-weight-bold' style='cursor: default;margin-left: 6px;'>Derecha Abierta</span>";
			}else if($fila[5]==2){
				$estadoPuertaDer="<span class='btn btn-light btn-text-danger btn-hover-text-danger font-weight-bold' style='cursor: default;margin-left: 6px;'>Derecha Cerrada</span>";
			}else{
				$estadoPuertaDer="<span class='btn btn-light btn-text-warning btn-hover-text-warning font-weight-bold' style='cursor: default;margin-left: 6px;'>Derecha Sin datos</span>";
			}
			
			/*start saber si esta online u offline*/
			$horaUltimaConexionExplode=explode(":",$fila[7]);//6
			if(!isset($horaUltimaConexionExplode[0])){
				$horaUltimaConexionExplode[0]=0;
			}
			if(!isset($horaUltimaConexionExplode[1])){
				$horaUltimaConexionExplode[1]=0;
			}
			if(!isset($horaUltimaConexionExplode[2])){
				$horaUltimaConexionExplode[2]=0;
			}
			$horaEnSegundos=((intval($horaUltimaConexionExplode[0])*60)*60)+(intval($horaUltimaConexionExplode[1])*60)+intval($horaUltimaConexionExplode[2]);

			$horaAhoraExplode=explode(":",date("H:i:s"));
			if(!isset($horaAhoraExplode[0])){
				$horaAhoraExplode[0]=0;
			}
			if(!isset($horaAhoraExplode[1])){
				$horaAhoraExplode[1]=0;
			}
			if(!isset($horaAhoraExplode[2])){
				$horaAhoraExplode[2]=0;
			}
			$horaAhoraEnSegundos=((intval($horaAhoraExplode[0])*60)*60)+(intval($horaAhoraExplode[1])*60)+intval($horaAhoraExplode[2]);

			//restado
			$restarHoras=$horaAhoraEnSegundos-$horaEnSegundos;

			//limite para dar por off
			$tiempoLimite=3*60;

			$estadoPlaca="";
			if($restarHoras>=$tiempoLimite){//off
				$estado="off";
				$estadoPlaca="<span class='btn btn-light btn-text-danger btn-hover-text-danger font-weight-bold' style='cursor: default;margin-left: 6px;'>Offline</span>";
			}else{//on
				$estado="on";
				$estadoPlaca="<span class='btn btn-light btn-text-success btn-hover-text-success font-weight-bold' style='cursor: default;margin-left: 6px;'>Online</span>";
			}

			$patron1="UPDATE pistaspadel_nodos SET conexion=\"%s\" WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$estado,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 1234563412121789");
			
			$conexionOnOffline="";
			/*end saber si esta online u offline*/
			
			printf("<div class='col-lg-4'>
						<!--begin::Card-->

                        <div class='card card-custom overlay'>
							<div class='card-body p-0'>
								<div class='overlay-wrapper'>
									<img src='%s' alt='Img_Puertas' class='w-100 rounded'%s/>
								</div>
								<div class='overlay-layer align-items-start justify-content-center' style='opacity:1;'>
                                    <div class='d-flex flex-grow-1 flex-center bg-white-o-5 py-5'>
                                       %s
									   %s
									   %s
                                    </div>
								</div>
							</div>
						</div>
						<!--end::Card-->
						<!--begin::Code example-->
						<div class='example example-compact mt-2 mb-7'>
							<div class='example-tools justify-content-center'>
								<a href='javascript: void(0)' class='btn font-weight-bold btn-success btn-shadow' onClick='abrirPuertaWebPistaPadelHistorial(\"amb\",\"".$fila[0]."\",2);'>Empezar Partida</a>
								<a href='javascript: void(0)' class='btn font-weight-bold btn-primary btn-shadow' style='margin-left: 10px;' onClick='abrirPuertaWebPistaPadelHistorial(\"amb\",\"".$fila[0]."\",5);'>Modo Mantenimiento</a>
								<a href='javascript: void(0)' class='btn font-weight-bold btn-danger btn-shadow' style='margin-left: 10px;' onClick='abrirPuertaWebPistaPadelHistorial(\"amb\",\"".$fila[0]."\",4);'>Cierre Manual</a>
							</div>
						</div>

						<!--end::Code example-->
					</div>",$rutaImg,$styleImg,$estadoPuertaIzq,$estadoPuertaDer,$estadoPlaca);
			
			/*
			<a href='javascript: void(0)' class='btn font-weight-bold btn-success btn-shadow' style='margin-left: 10px;' onClick='abrirPuertaWebPistaPadelHistorial(\"izq\",\"".$fila[0]."\",2);'>Abrir Puerta Izquierda</a>
			<a href='javascript: void(0)' class='btn font-weight-bold btn-success btn-shadow' style='margin-left: 10px;' onClick='abrirPuertaWebPistaPadelHistorial(\"der\",\"".$fila[0]."\",2);'>Abrir Puerta Derecha</a>
			*/
		}
		//cerrar div row
		printf("</div>");
	}
	mysqli_free_result($respuesta);
}

//historial puertas pista padel
function puertasPistaPadelHistorial($idNodo,$con){
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialPuertasPistasPadel"]) && isset($_SESSION["fechaFinHistorialPuertasPistasPadel"]) ){
		$consulta=" AND pistaspadel_historial.fechaalta>=\"".$_SESSION["fechaIniHistorialPuertasPistasPadel"]."\" AND pistaspadel_historial.fechaalta<=\"".$_SESSION["fechaFinHistorialPuertasPistasPadel"]."\"";
	}
	
	$patron="SELECT pistaspadel_historial.id,pistaspadel_historial.puerta,pistaspadel_historial.tipo,pistaspadel_historial.idacceso,pistaspadel_historial.idusuario,pistaspadel_historial.horaalta,pistaspadel_historial.fechaalta,pistaspadel_nodos.id,pistaspadel_historial.accionrealizada,pistaspadel_historial.miradoplaca,pistaspadel_historial.modo,pistaspadel_historial.minutospartida,pistaspadel_historial.idreservapadel,pistaspadel_historial.precio,pistaspadel_historial.horacierre,pistaspadel_historial.estadocierre FROM pistaspadel_historial,pistaspadel_nodos WHERE pistaspadel_historial.idnodo=\"%s\" AND pistaspadel_historial.idnodo=pistaspadel_nodos.id AND pistaspadel_nodos.guardado=\"s\" AND pistaspadel_nodos.borrado=\"n\"%s ORDER BY pistaspadel_historial.fechaalta DESC, pistaspadel_historial.horaalta DESC, pistaspadel_historial.id DESC";//LIMIT 0,50
	$sql=sprintf($patron,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323463455899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Puerta</th>
                      <th>Dispt</th>
					  <th>Nombre Acceso</th>
					  <th>Acción</th>
					  <th>Partida</th>
					  <th>Precio</th>
					  <th>Fecha</th>
					  <th>Hora Inicio</th>
					  <th>Estado Inicio</th>
					  <th>Hora Fin</th>
					  <th>Estado Fin</th>
					  <th>Resultado</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//puerta
			if($fila[1]=="izq"){
				$puerta="Izquierda";
			}else if($fila[1]=="der"){
				$puerta="Derecha";
			}else if($fila[1]=="amb"){
				$puerta="Ambas";
			}else{
				$puerta="Sin datos";
			}
			
			//tiempo partida
			$auxTiempoPartida=$fila[11];
			$tiempoPartida="";
			if(($fila[11])>0 && $fila[10]=="n" && $fila[2]!="4" && $fila[2]!="5"){
				$tiempoPartida=$fila[11]." min";
			}
			
			//accion
			$dispositivo="";
			$accion="--";
			if($fila[2]==1){
				if($fila[10]=="m"){
					$accion="Modo Mantenimiento";
				}else{
					$accion="Partida "/*.$tiempoPartida*/;
				}
				$dispositivo="Pin";
			}else if($fila[2]==2){
				$accion="Partida";
				$dispositivo="Web";
			}else if($fila[2]==3){
				$accion="Apertura Web Emergencia";
				$dispositivo="Web";
			}else if($fila[2]==4){
				$accion="Cierre Manual";
				$dispositivo="Web";
			}else if($fila[2]==5){
				$accion="Modo Mantenimiento";
				$nombreAcceso="ADMINISTRADOR";
				$dispositivo="Web";
			}
			
			
			$nombreAcceso="ADMINISTRADOR";
			//nombre usuario
			if($fila[2]==2 || $fila[2]==3 || $fila[2]==4){
				//nombre acceso
				$nombreAcceso="Sin datos";
				if($fila[4]>0){
					$patron3="SELECT nombre,apellidos FROM usuarios WHERE id=\"%s\"";
					$sql3=sprintf($patron3,$fila[4]);
					$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 96353445668764664222286454509258");
					if(mysqli_num_rows($respuesta3)>0){
						$fila3=mysqli_fetch_array($respuesta3);
						$nombreAcceso=$fila3[0]." ".$fila3[1];
					}
					mysqli_free_result($respuesta3);
				}
			}else{
				//$accion="Modo Mantenimiento";
				$nombreAcceso="ADMINISTRADOR";
			}
			
			
			//id reserva padel, reserva tu pista
			if($fila[12]>0){
				$accion="Partida";
				$dispositivo="reservatupista.com";
				$nombreAcceso="reservatupista";
				
				//email del usuario de reserva
				$patron4="SELECT usuario FROM pistaspadel_reservas WHERE id=\"%s\"";
				$sql4=sprintf($patron4,$fila[12]);
				$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 963534456687646644449258");
				if(mysqli_num_rows($respuesta4)>0){
					$fila4=mysqli_fetch_array($respuesta4);
					
					$patron5="SELECT emailusuario FROM pistaspadel_usuariosclientes WHERE idbbddplugin=\"%s\"";
					$sql5=sprintf($patron5,$fila4[0]);
					$respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 96353445665555449258");
					if(mysqli_num_rows($respuesta5)>0){
						$fila5=mysqli_fetch_array($respuesta5);
						
						$nombreAcceso=$fila5[0];
					}
					mysqli_free_result($respuesta5);
				}
				mysqli_free_result($respuesta4);
			}
			
			//historial acciones realizadas, o comprobadas,
			$resultadoAccion="";
			if($_SESSION["permisossession"]==1){
				if($fila[8]=="s" && $fila[9]=="s"){
					$resultadoAccion="Ejecutado y Mirado";
				}else if($fila[8]=="n" && $fila[9]=="s"){
					$resultadoAccion="Mirado";
				}else if($fila[8]=="s" && $fila[9]=="n"){
					$resultadoAccion="Ejecutado";
				}else{
					$resultadoAccion="KO";
				}
			}
			
			//modo pista
			$modo="";
			/*if($fila[10]=="m"){
				$modo=", Modo Mantenimiento";
			}*//*else if($fila[10]=="n"){
				$modo="Modo Normal";
			}*/
			
			//precio partida
			$precioPartida=0;
			if($fila[13]>0){
				$precioPartida=$fila[13];
			}
			
			//*** START Comprobar estado INICIO***
			$estado="-";
			$styleEstado="label label-lg label-light-danger label-inline";
			
			if($fila[6]<date("Y-m-d")){//si ya ha pasado ese dia
				if($fila[8]=="s" && $fila[9]=="s" /*&& $fila[2]!=4*/){//procesado
					$estado="Iniciada";
					$styleEstado="label label-lg label-light-success label-inline";
				}else if($fila[8]=="n" && $fila[9]=="n"){//no procesado
					$estado="Error";
					$styleEstado="label label-lg label-light-danger label-inline";
				}else if( ($fila[8]=="s" && $fila[9]=="n") || ($fila[8]=="n" && $fila[9]=="s")){//solo se ha realizado una
					$estado="Error";
					$styleEstado="label label-lg label-light-danger label-inline";
				}
			}else if($fila[6]>=date("Y-m-d")){//ver si ha llegado ya ese dia
				if($fila[8]=="s" && $fila[9]=="s" /*&& $fila[2]!=4*/){//procesado
					$estado="Iniciada";
					$styleEstado="label label-lg label-light-success label-inline";
				}else if($fila[8]=="n" && $fila[9]=="n" && $fila[5]<date("H:i:s") ){//no procesado y SI ha llegado ya la hora de realizar accion
					$estado="Error";
					$styleEstado="label label-lg label-light-danger label-inline";
				}else if( ($fila[8]=="s" && $fila[9]=="n") || ($fila[8]=="n" && $fila[9]=="s") ){//solo se ha realizado una
					$estado="Error";
					$styleEstado="label label-lg label-light-danger label-inline";
				}else if($fila[8]=="n" && $fila[9]=="n" && $fila[5]>=date("H:i:s")){//no procesado y NO ha llegado ya la hora de realizar accion
					$estado="Pendiente";
					$styleEstado="label label-lg label-light-warning label-inline";
				}
			}else{//aun no ha llegado la hora de realizar la accion
				$estado="Pendiente";
				$styleEstado="label label-lg label-light-warning label-inline";
			}
			/*** END Comprobar estado INICIO***/
			
			/*START estado cierre FIN*/
			$estadoFin="-";
			$styleEstadoFin="label label-lg label-light-danger label-inline";
			$auxFinPartidaCalculado=strtotime('+'.$auxTiempoPartida.' minute',strtotime($fila[5]));
			if($fila[6]<date("Y-m-d")){//ya ha pasado el dia
				
				if($fila[2]!=4){//excluir el cierre forzoso
					if($fila[15]=="s"){
						$estadoFin="Finalizada";
						$styleEstadoFin="label label-lg label-light-info label-inline";
					}else if($fila[15]=="n"){//anotado cierre NO
						$estadoFin="Error";
						$styleEstadoFin="label label-lg label-light-danger label-inline";
					}
				}else if($fila[2]==4){//entra cierre forzoso
					if($fila[15]=="s"){
						$estadoFin="Finalizada";
						$styleEstadoFin="label label-lg label-light-info label-inline";
					}else if($fila[15]=="n"){
						$estadoFin="Error";
						$styleEstadoFin="label label-lg label-light-danger label-inline";
					}
				}
			}else if($fila[6]>=date("Y-m-d")){//si es hoy o posterior
				if($fila[2]!=4){//excluir el cierre forzoso
					if($fila[15]=="n" && $fila[5]>=date("H:i:s")){//anotado cierre NO, pero NO ha llegado aun la hora de accion
						$estadoFin="Pendiente";
						$styleEstadoFin="label label-lg label-light-warning label-inline";
					}else if($fila[15]=="n" && $fila[5]<date("H:i:s")){//anotado cierre NO, pero NO ha llegado aun la hora de accion
						if($auxFinPartidaCalculado>strtotime(date("H:i:s")) /*|| $fila[14]!=""*/){
							$estadoFin="Pendiente";
							$styleEstadoFin="label label-lg label-light-warning label-inline";
						}else{
							$estadoFin="Error";
							$styleEstadoFin="label label-lg label-light-danger label-inline";
						}
					}else if($fila[15]=="s"){//anotado cierre SI
						$estadoFin="Finalizada";
						$styleEstadoFin="label label-lg label-light-info label-inline";
					}
				}else if($fila[2]==4){//entra cierre forzoso
					if($fila[15]=="s"){
						$estadoFin="Finalizada";
						$styleEstadoFin="label label-lg label-light-info label-inline";
					}else if($fila[15]=="n"){
						$estadoFin="Error";
						$styleEstadoFin="label label-lg label-light-danger label-inline";
					}
				}
			}else{//aun no ha llegado la hora de realizar la accion
				$estadoFin="Pendiente";
				$styleEstadoFin="label label-lg label-light-warning label-inline";
			}
			/*END estado cierre FIN*/
			
			$botones="";
			
            printf("<tr>
                        <td></td>
						<td><input type='hidden' value=\"%s\"/>%s</td>
                        <td>%s</td>
						<td>%s</td>
						<td>%s%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td><span class='%s'>%s</span></td>
						<td>%s</td>
						<td><span class='%s'>%s</span></td>
						<td>%s</td>
					</tr>",$fila[0],$puerta,$dispositivo,$nombreAcceso,$accion,$modo,$tiempoPartida,$precioPartida."€",convierteFechaBarra($fila[6]),$fila[5],$styleEstado,$estado,$fila[14],$styleEstadoFin,$estadoFin,$resultadoAccion);
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                        <th>#</th>
						<th>Puerta</th>
                        <th>Dispt.</th>
					  	<th>Nombre Acceso</th>
					  	<th>Acción</th>
						<th>Partida</th>
						<th>Precio</th>
						<th>Fecha Inicio</th>
						<th>Hora Inicio</th>
						<th>Fecha Fin</th>
						<th>Hora Fin</th>
						<th>Estado</th>
						<th>Resultado</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//poner online offline, segun ultima conexion
function ultimaConexionOnlinOfflinePistasPadel($con){
	$patron="SELECT id,fechaultimaconsulta,horaultimaconsulta FROM pistaspadel_nodos WHERE borrado=\"n\" AND guardado=\"s\"";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323435636345663455899");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);

			$horaUltimaConexionExplode=explode(":",$fila[2]);
			if(!isset($horaUltimaConexionExplode[0])){
				$horaUltimaConexionExplode[0]=0;
			}
			if(!isset($horaUltimaConexionExplode[1])){
				$horaUltimaConexionExplode[1]=0;
			}
			if(!isset($horaUltimaConexionExplode[2])){
				$horaUltimaConexionExplode[2]=0;
			}
			$horaEnSegundos=((intval($horaUltimaConexionExplode[0])*60)*60)+(intval($horaUltimaConexionExplode[1])*60)+intval($horaUltimaConexionExplode[2]);

			$horaAhoraExplode=explode(":",date("H:i:s"));
			if(!isset($horaAhoraExplode[0])){
				$horaAhoraExplode[0]=0;
			}
			if(!isset($horaAhoraExplode[1])){
				$horaAhoraExplode[1]=0;
			}
			if(!isset($horaAhoraExplode[2])){
				$horaAhoraExplode[2]=0;
			}
			$horaAhoraEnSegundos=((intval($horaAhoraExplode[0]*60))*60)+(intval($horaAhoraExplode[1])*60)+intval($horaAhoraExplode[2]);

			//restado
			$restarHoras=$horaAhoraEnSegundos-$horaEnSegundos;

			//limite para dar por off
			$tiempoLimite=3*60;

			$estado="";
			if($restarHoras>=$tiempoLimite || $fila[1]<date("Y-m-d")){//off
				$estado="off";
			}else{//on
				$estado="on";
			}

			$patron1="UPDATE pistaspadel_nodos SET conexion=\"%s\" WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$estado,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 1234563412121789");
		}
		
	}
	mysqli_free_result($respuesta);
}

//carga pistas id de reserva tu pìsta
function cargaIdPistaReservaTuPista($seleccionada,$idUsuario,$con){

	$consulta="-99";
	if($idUsuario>0){
		$consulta=" AND idusuario=\"".$idUsuario."\"";
	}
	
	$class="";
	if($seleccionada=="0"){
		$class=" is-invalid";
	}
	
	$patron="SELECT id,nombrepista,idbbddplugin FROM pistaspadel_numpistasclientes WHERE borrado=\"n\"%s";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12345466");
	printf("<select class='form-control%s' name=\"idServicioPistaRel\" id=\"idServicioPistaRel\" >",$class);
	printf("<option value='0'>Selecciona pista</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionada){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]." - id:".$fila[2]);
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}
?>