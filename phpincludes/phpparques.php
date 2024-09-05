<?php

													/*************************************
													 *									 *
													 *		        PARQUES              *
													 *									 *
													 *************************************/	
// CARGA parques
function cargaNodosParquesList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioParquesList"]!="0"*/){
		if($_SESSION["usuarioParquesList"]!="0"){
			$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioParquesList"])."\"";
		}
	}else {
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	if($_SESSION["conexionParquesList"]!=""){
		$consulta.=" AND conexion=\"".quitaComillasD($_SESSION["conexionParquesList"])."\"";
	}
	
	$patron="SELECT id,nombre,idusuario,conexion,ubicacion FROM parques_nodos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963233435342365545467783543457879958");
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
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9635343255334434653509258");
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
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=24&i=%s\");'",$fila[0]);
			
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

//filtro clientes Parques list
function cargaUsuariosParquesFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 31215235834375565778676741236456454455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosParques(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioParquesList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//filtro estado Parques list
function cargaEstadosParquesFiltro($con){
	$selectedUno="";
	$selectedDos="";
	if($_SESSION["conexionParquesList"]=="on"){
		$selectedUno=" selected";
	}else if($_SESSION["conexionParquesList"]=="off"){
		$selectedDos=" selected";
	}
	
	printf("<select class='form-control' id='selectConexionFiltro' onChange='filtrarConexionParques(this);'><option value=''>Selecciona Conexión:</option>");
	printf("<option value=\"on\" %s>Online</option><option value=\"off\" %s>Offline</option>",$selectedUno,$selectedDos);	
	printf('</select>');
}

//cargar puertas nodo parque
function cargaPuertasNodoParque($idNodo,$con){

	$consultaPermisos="";
	$nodosPermisosId="";
	if($_SESSION["permisossession"]==3){
		
		/*$patron3="SELECT idempresa FROM usuarios WHERE borrado=\"n\" AND id=\"%s\" AND guardado=\"s\"C";
		$sql3=sprintf($patron3,$_SESSION["idusersession"]);
		$respuesta3=mysqli_query($con,$sql) or die ("Error 31215235857009090564558556549912127878515");
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
	
	$patron="SELECT id,nombre,idusuario,estadopuertaizq,rutaimg,estadopuertader,nombreizquierda,nombrederecha,fechaultimaconsulta,horaultimaconsulta FROM parques_nodos WHERE borrado=\"n\" AND id=\"%s\" AND guardado=\"s\"%s ORDER BY id ASC";
	$sql=sprintf($patron,$idNodo,$nodosPermisosId);
	$respuesta=mysqli_query($con,$sql) or die ("Error 312152358570090905645589912127878515");
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
			
			if($fila[4]!="" && $fila[4]!="NULL"){
				$rutaImg="./archivos_subidos/clientes/".$fila[2]."/parques/".$fila[4];
				$styleImg="";
			}else{
				$rutaImg="./nimg/img_puerta_parque.jpg";
				$styleImg=" style='height: 237.08px'";
			}
			
			$estadoPuertaIzq="";
			$nombrePuertaIzq=$fila[6];
			if($fila[3]==1){
				$estadoPuertaIzq="<span class='btn btn-light btn-text-success btn-hover-text-success font-weight-bold' style='cursor: default;margin-left: 6px;'>".$nombrePuertaIzq.": Abierta</span>";
			}else if($fila[3]==2){
				$estadoPuertaIzq="<span class='btn btn-light btn-text-danger btn-hover-text-danger font-weight-bold' style='cursor: default;margin-left: 6px;'>".$nombrePuertaIzq.": Cerrada</span>";
			}else{
				$estadoPuertaIzq="<span class='btn btn-light btn-text-warning btn-hover-text-warning font-weight-bold' style='cursor: default;margin-left: 6px;'>".$nombrePuertaIzq.": _______</span>";
			}
			
			$estadoPuertaDer="";
			$nombrePuertaDer=$fila[7];
			if($fila[5]==1){
				$estadoPuertaDer="<span class='btn btn-light btn-text-success btn-hover-text-success font-weight-bold' style='cursor: default;margin-left: 6px;'>".$nombrePuertaDer.": Abierta</span>";
			}else if($fila[5]==2){
				$estadoPuertaDer="<span class='btn btn-light btn-text-danger btn-hover-text-danger font-weight-bold' style='cursor: default;margin-left: 6px;'>".$nombrePuertaDer.": Cerrada</span>";
			}else{
				$estadoPuertaDer="<span class='btn btn-light btn-text-warning btn-hover-text-warning font-weight-bold' style='cursor: default;margin-left: 6px;'>".$nombrePuertaDer.": _______</span>";
			}
			
			/*start saber si esta online u offline*/
			$horaUltimaConexionExplode=explode(":",$fila[9]);
			$horaEnSegundos=(($horaUltimaConexionExplode[0]*60)*60)+($horaUltimaConexionExplode[1]*60)+$horaUltimaConexionExplode[2];

			$horaAhoraExplode=explode(":",date("H:i:s"));
			$horaAhoraEnSegundos=(($horaAhoraExplode[0]*60)*60)+($horaAhoraExplode[1]*60)+$horaAhoraExplode[2];

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
								<a href='javascript: void(0)' class='btn font-weight-bold btn-warning btn-shadow' onClick='abrirCerrarPuertaWebParqueHistorial(\"izq\",\"".$fila[0]."\",2);'>Pulsación %s</a>
								<a href='javascript: void(0)' class='btn font-weight-bold btn-success btn-shadow' style='margin-left: 10px;' onClick='abrirCerrarPuertaWebParqueHistorial(\"amb\",\"".$fila[0]."\",2);'>Pulsación Ambas</a>
								<a href='javascript: void(0)' class='btn font-weight-bold btn-warning btn-shadow' style='margin-left: 10px;'onClick='abrirCerrarPuertaWebParqueHistorial(\"der\",\"".$fila[0]."\",2);'>Pulsación %s</a>
							</div>
						</div>

						<!--end::Code example-->
					</div>",$rutaImg,$styleImg,$estadoPuertaIzq,$estadoPuertaDer,$estadoPlaca,$nombrePuertaIzq,$nombrePuertaDer);
		}
		//cerrar div row
		printf("</div>");
	}
	mysqli_free_result($respuesta);
}

//historial puertas parques
function puertasParqueHistorial($idNodo,$con){
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialPuertasParques"]) && isset($_SESSION["fechaFinHistorialPuertasParques"]) ){
		$consulta=" AND parques_historial.fechaalta>=\"".$_SESSION["fechaIniHistorialPuertasParques"]."\"  AND parques_historial.fechaalta<=\"".$_SESSION["fechaFinHistorialPuertasParques"]."\"";
	}
	
	$patron="SELECT parques_historial.id,parques_historial.puerta,parques_historial.tipo,parques_historial.idacceso,parques_historial.idusuario,parques_historial.horaalta,parques_historial.fechaalta,parques_nodos.id,parques_historial.accionrealizada,parques_historial.miradoplaca FROM parques_historial,parques_nodos WHERE parques_historial.idnodo=\"%s\" AND parques_historial.idnodo=parques_nodos.id AND parques_nodos.guardado=\"s\" AND parques_nodos.borrado=\"n\"%s ORDER BY parques_historial.fechaalta DESC, parques_historial.horaalta DESC, parques_historial.id DESC";//LIMIT 0,50
	$sql=sprintf($patron,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963455323463455899");
	if(mysqli_num_rows($respuesta)>0){
		/*printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Puerta</th>
					  <th>Acción</th>
					  <th>Nombre Acceso</th>
					  <th>Fecha</th>
					  <th>Hora</th>
					  <th>Resultado</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');*/
        
        printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Puerta</th>
                      <th>Dispositivo</th>
					  <th>Acción</th>
					  <th>Nombre Acceso</th>
					  <th>Fecha</th>
					  <th>Hora</th>
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
				$puerta="Automático/Ambas";
			}else{
				$puerta="Sin datos";
			}
			
			//accion
			$accion="--";
			if($fila[2]==1){
				$accion="Apertura Automática";
			}else if($fila[2]==2){
				$accion="Apertura/Cierre Web";
			}else if($fila[2]==3){
				$accion="Apertura Web Emergencia";
			}/*else if($fila[2]==4){
				$accion="Cierre Manual Web";
			}*/
			
			$nombreAcceso="";
			//nombre usuario
			if($fila[2]==2 || $fila[2]==3 || $fila[2]==4){
				//nombre acceso
				if($fila[4]>0){
					$patron3="SELECT nombre,apellidos FROM usuarios WHERE id=\"%s\"";
					$sql3=sprintf($patron3,$fila[4]);
					$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 96353445456668764664222286454509258");
					if(mysqli_num_rows($respuesta3)>0){
						$fila3=mysqli_fetch_array($respuesta3);
						$nombreAcceso=$fila3[0]." ".$fila3[1];
					}
					mysqli_free_result($respuesta3);
				}
			}
			
			//historial acciones realizadas, o comprobadas
			$resultadoAccion="";
			if($fila[8]=="s"){
				$resultadoAccion="Apertura (leído)";
			}else if($fila[8]=="n" && $fila[9]=="s"){
				$resultadoAccion="(leído)";
			}else{
				$resultadoAccion="Sin datos";
			}
			
			$botones="";
			
			/*printf("<tr>
                        <td></td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$puerta,$accion,$nombreAcceso,convierteFechaBarra($fila[6]),$fila[5],$resultadoAccion,$botones);*/
            
            printf("<tr>
                        <td></td>
						<td>%s</td>
                        <td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
					</tr>",$puerta,"Panel Web",$accion,$nombreAcceso,convierteFechaBarra($fila[6]),$fila[5],$resultadoAccion);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                        <th>#</th>
						<th>Puerta</th>
						<th>Dispositivo</th>
					  	<th>Acción</th>
					  	<th>Nombre Acceso</th>
						<th>Fecha</th>
						<th>Hora</th>
						<th>Resultado</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//poner online offline, segun ultima conexion
function ultimaConexionOnlinOfflineSafey($con){
	$patron="SELECT id,fechaultimaconsulta,horaultimaconsulta FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\"";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963234356363456354634542355899");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$horaUltimaConexionExplode=explode(":",$fila[2]);
			if(!isset($horaUltimaConexionExplode[0])){ $horaUltimaConexionExplode[0]=0;}
			if(!isset($horaUltimaConexionExplode[1])){ $horaUltimaConexionExplode[1]=0;}
			if(!isset($horaUltimaConexionExplode[2])){ $horaUltimaConexionExplode[2]=0;}
			$horaEnSegundos=((intval($horaUltimaConexionExplode[0])*60)*60)+(intval($horaUltimaConexionExplode[1])*60)+intval($horaUltimaConexionExplode[2]);
			
			$horaAhoraExplode=explode(":",date("H:i:s"));
			if(!isset($horaAhoraExplode[0])){ $horaAhoraExplode[0]=0;}
			if(!isset($horaAhoraExplode[1])){ $horaAhoraExplode[1]=0;}
			if(!isset($horaAhoraExplode[2])){ $horaAhoraExplode[2]=0;}
			$horaAhoraEnSegundos=((intval($horaAhoraExplode[0])*60)*60)+(intval($horaAhoraExplode[1])*60)+intval($horaAhoraExplode[2]);
			
			//restado
			$restarHoras=$horaAhoraEnSegundos-$horaEnSegundos;
			
			//limite para dar por off, 5 min
			$tiempoLimite=5*60;
			
			$estado="";
			if($restarHoras>=$tiempoLimite || $fila[1]<date("Y-m-d")){//off
				$estado="off";
			}else{//on
				$estado="on";
			}
			
			$patron1="UPDATE safey_nodos SET conexion=\"%s\" WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$estado,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 12345634234121234431789");
		}
	}
	mysqli_free_result($respuesta);
}
?>