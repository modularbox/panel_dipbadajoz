<?php 
													/*************************************
													 *									 *
													 *			   contadores		     *
													 *									 *
													 *************************************/	
// CARGA contadores
function cargaNodosContadoresList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioContadorList"]!="0"*/){
		if($_SESSION["usuarioContadorList"]!="0"){
			$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioContadorList"])."\"";
		}
	}else{
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	if($_SESSION["estadoContadorList"]!=""){
		$consulta.=" AND conexion=\"".quitaComillasD($_SESSION["estadoContadorList"])."\"";
	}
	
	$patron="SELECT id,nombre,idusuario,conexion,tipo,ubicacion,ajuste,bateria FROM contadores_nodos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963258");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>Tipo</th>
					  <th>Ubicación</th>
					  <th>Conexión</th>
					  <th>Batería</th>
					  <th>Lect. m3</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//usuario
			$patron1="SELECT nombre FROM usuarios WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$fila[2]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963258");
			$fila1=mysqli_fetch_array($respuesta1);
			mysqli_free_result($respuesta1);
			
			//lecturas
			$patron2="SELECT SUM(lectura) FROM contadores_historial WHERE contador=\"%s\" AND borrado=\"n\"";
			$sql2=sprintf($patron2,$fila[0]);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 96325822");
			$fila2=mysqli_fetch_array($respuesta2);
			$utlimaLecturaMCubicos=($fila2[0]/1000)+$fila[6];//pasar a m3, y mas el ajuste
			mysqli_free_result($respuesta2);
			
			//conexion
			$botonesAcciones="";
			if($fila[3]=="on"){
				$conexion="<span class='label label-lg label-light-success label-inline'>Online</span>";
			}else if($fila[3]=="off"){
				$conexion="<span class='label label-lg label-light-danger label-inline'>Offline</span>";
				//mostrar el de encender
			}else{
				$conexion="<span class='label label-lg label-light-primary label-inline'>Sin datos</span>";
			}
			
			//pulsar tr
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=6&i=%s\");'",$fila[0]);
			
			$tipo="Sin asignar";
			if($fila[4]==1){
				$tipo="General";
			}else if($fila[4]==2){
				$tipo="Zona";
			}else if($fila[4]==3){
				$tipo="Cliente";
			}
			
			printf("<tr>
				<td></td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class=''>%s</td>
				</tr>",$funcion,$fila[1],$funcion,$fila1[0],$funcion,$tipo,$funcion,$fila[5],$funcion,$conexion,$funcion,$fila[7]." %",$funcion,$utlimaLecturaMCubicos,$botonesAcciones);
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>Tipo</th>
					  <th>Ubicación</th>
					  <th>Conexión</th>
					  <th>Batería</th>
					  <th>Lectura m3</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}

//historial lectura contador
function contadoresHistorialLecturas($idContador,$con){
	$consulta="";
	if(isset($_SESSION["fechaHistorialContadorLectu"])){
		$consulta=" AND fecha=\"".$_SESSION["fechaHistorialContadorLectu"]."\"";
	}/*else{
		$_SESSION["fechaHistorialContadorLectu"]=date("Y-m-d");
		$consulta=" AND fecha=\"".$_SESSION["fechaHistorialContadorLectu"]."\"";
	}*/
	
	$patron="SELECT id,lectura,fecha,hora,creado FROM contadores_historial WHERE borrado=\"n\" AND contador=\"%s\"%s ORDER BY fecha DESC, hora DESC, creado DESC, id DESC LIMIT 0,50";
	$sql=sprintf($patron,$idContador,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96325899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Lectura (L)</th>
					  <th>Lectura (m3)</th>
					  <th>Fecha</th>
					  <th>Hora</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		/*
		<a href="#" class="btn btn-icon btn-light btn-hover-primary btn-sm" onClick="crearHistorialContador(\'\');return false;">
			<span class="svg-icon svg-icon-md svg-icon-primary">
				<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Write.svg-->
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"/>
						<rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"/>
					</g>
				</svg>
				<!--end::Svg Icon-->
			</span>
		</a>
		*/
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$botones="";
			if($fila[4]==2){//poder borrar, se ha creado desde web
				$botones="<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas seguro de que deseas eliminar este registro?\",4,\"".$idContador."\",\"".$fila[0]."\",\"\");return false;' title='Borrar'>
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
			}
			
			printf("<tr>
                        <td></td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$fila[1],$fila[1]/1000,convierteFechaBarra($fila[2]),$fila[3],$botones);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                        <th>#</th>
						<th>Lectura (L)</th>
					  	<th>Lectura (m3)</th>
						<th>Fecha</th>
						<th>Hora</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//carga tipo contador
function cargaTipoContador($seleccionada,$faltacampo,$con){
	$select1="";
	$select2="";
	$select3="";
	if($seleccionada==1){
		$select1=" selected='selected'";
	}else if($seleccionada==2){
		$select2=" selected='selected'";
	}else if($seleccionada==3){
		$select3=" selected='selected'";
	}
	$class="";
	if($faltacampo && $seleccionada=="0"){
		$class=" is-invalid";
	}
	printf("<select class='form-control%s' name=\"tipo\" id=\"tipo\" onChange='mostrarContadorInputRel(this)'><option value='0'>Selecciona tipo</option><option value='1'%s>General</option><option value='2'%s>Zona</option><option value='3'%s>Cliente</option></select>",$class,$select1,$select2,$select3);
}

//carga contadores generico
function cargaContadoresGenerico($seleccionada,$nombre,$idusuario,$con){
	$patron="SELECT id,nombre FROM contadores_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND idusuario=\"%s\" ORDER BY tipo, nombre";
	$sql=sprintf($patron,$idusuario);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 123423254666");
	printf("<select class='form-control' name=\"%s\" id=\"%s\" >",$nombre,$nombre);
	printf("<option value='0'>Selecciona Contador</option>");
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

//filtro clientes contadores list
/*function cargaUsuariosContadoresFiltro($con){
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\" AND permisos=\"2\"";
	$sql=sprintf($patron,$seleccionado);
	$respuesta=mysqli_query($con,$sql) or die ("Error 31215656456655875676746455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosContador(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioContadorList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}*/
//filtro clientes contadores list
function cargaUsuariosContadoresFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 31215656456655875676746455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosContador(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioContadorList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//filtro estado contadores list
function cargaEstadosContadoresFiltro($con){
	$selectedUno="";
	$selectedDos="";
	if($_SESSION["estadoContadorList"]=="on"){
		$selectedUno=" selected";
	}else if($_SESSION["estadoContadorList"]=="off"){
		$selectedDos=" selected";
	}
	
	printf("<select class='form-control' id='selectEstadoFiltro' onChange='filtrarEstadoContador(this);'><option value=''>Selecciona Estado:</option>");
	printf("<option value=\"on\" %s>Online</option><option value=\"off\" %s>Offline</option>",$selectedUno,$selectedDos);	
	printf('</select>');
}

//carga metrica contadores
function cargaMetricaContadores($seleccionada,$nombre,$con){
	$patron="SELECT id,metrica FROM contadores_metrica";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12346545466");
	printf("<select class='form-control' name=\"%s\" id=\"%s\" >",$nombre,$nombre);
	printf("<option value='0'>Selecciona Métrica</option>");
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
/*//sin uso de momento
function contadoresAvisosMailUno($idContador,$tipo,$con){
	$consulta="";
	if(true){
		$consulta="";
	}else{
		$consulta="";
	}
	
	$campoConsulta=",m3notimail";
	if($tipo==1){
		$campoConsulta=",m3notimail";
	}else if($tipo==2){
		$campoConsulta=",mhoraconsumonotifi";
	}else if($tipo==3){
		$campoConsulta=",mdiasnoactividadnotifi";
	}
	
	$patron="SELECT id%s FROM contadores_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"%s";
	$sql=sprintf($patron,$campoConsulta,$idContador,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632354565899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Email</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		$fila=mysqli_fetch_array($respuesta);
			
		$emails = explode(";", $fila[1]);
		//for($i=0;$i<mysqli_num_rows($respuesta);$i++){
		for($i=0;$i<count($emails);$i++){
			
			$botones="<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas 					seguro de que deseas eliminar este registro?\",499,\"".$idContador."\",\"".$emails[$i]."\",\"".$tipo."\");return false;' title='Borrar'>
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
						<td>%s</td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$emails[$i],$botones);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                        <th>#</th>
						<th>Email</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}*/

//poner online offline, segun ultima conexion
function ultimaConexionOnlinOfflineContadores($con){
	$patron="SELECT id,fechaultimaconsulta,horaultimaconsulta FROM contadores_nodos WHERE borrado=\"n\" AND guardado=\"s\"";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963234323423433444356363456634542355899");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);

			$horaUltimaConexionExplode=explode(":",$fila[2]);
			$horaEnSegundos=(($horaUltimaConexionExplode[0]*60)*60)+($horaUltimaConexionExplode[1]*60)+$horaUltimaConexionExplode[2];

			$horaAhoraExplode=explode(":",date("H:i:s"));
			$horaAhoraEnSegundos=(($horaAhoraExplode[0]*60)*60)+($horaAhoraExplode[1]*60)+$horaAhoraExplode[2];

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

			$patron1="UPDATE contadores_nodos SET conexion=\"%s\" WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$estado,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 12345634121234431789");
		}
		
	}
	mysqli_free_result($respuesta);
}
?>