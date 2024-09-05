<?php 


													/*************************************
													 *									 *
													 *	      		AUDIOS			     *
													 *									 *
													 *************************************/


//filtro clientes Audio list
function cargaUsuariosAudioFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 3121523583437556577867617412364443455456454455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosAudio(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioAudioList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}


//filtro estado audio list
function cargaEstadosAudioFiltro($con){
	$selectedUno="";
	$selectedDos="";
	if($_SESSION["conexionAudioList"]=="on"){
		$selectedUno=" selected";
	}else if($_SESSION["conexionAudioList"]=="off"){
		$selectedDos=" selected";
	}
	
	printf("<select class='form-control' id='selectConexionFiltro' onChange='filtrarConexionAudio(this);'><option value=''>Selecciona Conexión:</option>");
	printf("<option value=\"on\" %s>Online</option><option value=\"off\" %s>Offline</option>",$selectedUno,$selectedDos);	
	printf('</select>');
}

// CARGA nodos audios
function cargaNodosAudioList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioAudioList"]!="0"*/){
		if($_SESSION["usuarioAudioList"]!="0"){
			$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioAudioList"])."\"";
		}
	}else{
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	if($_SESSION["conexionAudioList"]!=""){
		$consulta.=" AND conexion=\"".quitaComillasD($_SESSION["conexionAudioList"])."\"";
	}
	
	/*START actualizar estado placas*/
	ultimaConexionOnlinOfflineAudios($con);
	/*END actualizar estado placas*/
	
	$patron="SELECT id,nombre,idusuario,conexion,ubicacion FROM audio_nodos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632334351342365545467545783543457879958");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>Ubicación</th>
					  <th>Conexión</th>
					  <th>XXXXXX</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//usuario
			$patron1="SELECT nombre FROM usuarios WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$fila[2]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9635343255313366764434653509258");
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
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=37&i=%s\");'",$fila[0]);
			
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
					  <th>XXXXXX</th>
					</tr>
                </thead>
                <tbody>');
	}
}


//historial audios colas
function audiosColasHistorial($idNodo,$con){
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialProgramasAudio"]) && isset($_SESSION["fechaFinHistorialProgramasAudio"]) ){
		$consulta=" AND audio_colashistorial.fechareproducir>=\"".$_SESSION["fechaIniHistorialProgramasAudio"]."\" AND audio_colashistorial.fechareproducir<=\"".$_SESSION["fechaFinHistorialProgramasAudio"]."\"";
	}
	
	$patron="SELECT audio_colashistorial.id,audio_colashistorial.numeroreproducciones,audio_colashistorial.fechareproducir,audio_colashistorial.horareproducir,audio_colashistorial.reproducido,audio_colashistorial.horareproducido,audio_colashistorial.idaudio FROM audio_colashistorial,audio_nodos WHERE audio_colashistorial.idnodo=\"%s\" AND audio_colashistorial.idnodo=audio_nodos.id AND audio_nodos.guardado=\"s\" AND audio_nodos.borrado=\"n\"  AND audio_colashistorial.borrado=\"n\"%s ORDER BY audio_colashistorial.fechareproducir DESC, audio_colashistorial.horareproducir DESC, audio_colashistorial.id DESC";
	$sql=sprintf($patron,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323463455899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Audio</th>
                      <th>Reproducciones</th>
					  <th>Fecha Inicio</th>
					  <th>Hora Inicio</th>
					  <th>Hora Reproducido</th>
					  <th>Resultado</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//nombre audio
			$nombreAudio="";
			$patron3="SELECT nombre FROM audio_ficheroaudio WHERE id=\"%s\"";
			$sql3=sprintf($patron3,$fila[6]);
			$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 96353445354554356764664222286454509258");
			if(mysqli_num_rows($respuesta3)>0){
				$fila3=mysqli_fetch_array($respuesta3);
				$nombreAudio=$fila3[0];
			}
			mysqli_free_result($respuesta3);
			
			
			//*** START Comprobar estado INICIO***
			$estado="-";
			$styleEstado="label label-lg label-light-danger label-inline";
			if($fila[4]=="n"){//no reproducido
				if($fila[2]>date("Y-m-d")){//fecha reproducir, fecha futura
					$estado="Pendiente";
					$styleEstado="label label-lg label-light-warning label-inline";
					
				}else if($fila[2]==date("Y-m-d")){//fecha reproducir, fecha es hoy
					if($fila[3]<=date("H:i:s")){//hora ya pasada
						$estado="Error";
						$styleEstado="label label-lg label-light-danger label-inline";
					}else{//hora pendiente
						$estado="Pendiente";
						$styleEstado="label label-lg label-light-warning label-inline";
					}
				}else if($fila[2]<date("Y-m-d")){//fecha reproducir
					$estado="Error";
					$styleEstado="label label-lg label-light-danger label-inline";
				}else{//no va a darse el caso,
					$estado="Pendiente";
					$styleEstado="label label-lg label-light-warning label-inline";
				}
			}else if($fila[4]=="s"){//si reproducido
				$estado="Reproducido";
				$styleEstado="label label-lg label-light-success label-inline";
			}else if($fila[4]=="m"){//mal, problemas, error
				$estado="Error";
				$styleEstado="label label-lg label-light-danger label-inline";
			}else{//aun no ha llegado la hora de realizar la accion
				$estado="Pendiente";
				$styleEstado="label label-lg label-light-warning label-inline";
			}
			/*** END Comprobar estado INICIO***/
			
			$botonEliminar="";
			if($fila[4]=="n" && ($fila[2]>date("Y-m-d") || ($fila[2]==date("Y-m-d") && $fila[3]>date("H:i:s") )) ){
				$botonEliminar="<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Cancelar Audio\",\"¿Estas seguro de que deseas cancelar este fichero de audio?\",38,\"".$fila[0]."\",\"".$idNodo."\",\"1\");return false;'>
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
						<td><input type='hidden' value=\"%s\"/>%s</td>
                        <td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td><span class='%s'>%s</span></td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$fila[0],$nombreAudio,$fila[1],convierteFechaBarra($fila[2]),$fila[3],$fila[5],$styleEstado,$estado,$botonEliminar );
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Audio</th>
                      <th>Reproducciones</th>
					  <th>Fecha Inicio</th>
					  <th>Hora Inicio</th>
					  <th>Hora Reproducido</th>
					  <th>Resultado</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}


//gestión audios subidos
function audiosSubidosList($con){
	/*start segun permisos*/
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta.="";
		//$_SESSION["usuarioAudioSubirList"]=0;
	}else if($_SESSION["permisossession"]==2){
		$consulta.=" AND idusuario=".$_SESSION["idusersession"];
		$_SESSION["usuarioAudioSubirList"]=$_SESSION["idusersession"];
	}else if($_SESSION["permisossession"]==3){
		$patron99="SELECT idempresa FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
		$sql99=sprintf($patron99,$_SESSION["idusersession"]);
		$respuesta99=mysqli_query($con,$sql99) or die ("Error 312152359943455456454455899121215");
		if(mysqli_num_rows($respuesta99)>0){
			$fila99=mysqli_fetch_array($respuesta99);
			$consulta.=" AND idusuario=\"".$fila99[0]."\"";
			$_SESSION["usuarioAudioSubirList"]=$fila99[0];
		}
		mysqli_free_result($respuesta99);
	}
	/*end segun permisos*/
	
	/*start segun filtro*/
	if(isset($_SESSION["usuarioAudioSubirList"])){
		if($_SESSION["usuarioAudioSubirList"]>0){
			$consulta.=" AND idusuario=".$_SESSION["usuarioAudioSubirList"];
		}else{
			$consulta.=" AND idusuario=-99";//para no mostrar nada
		}
	}
	/*end segun filtro*/
	
	$patron="SELECT id,nombre,url,fechaalta,idusuario FROM audio_ficheroaudio WHERE borrado=\"n\"%s ORDER BY id DESC,fechaalta DESC,idusuario ASC, nombre ASC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632444700765634533453546555899");
	if(mysqli_num_rows($respuesta)>0){
		printf("<thead>
					<tr>
						<th>#</th>
						<th>Nombre</th>
						<th>Fecha Subida</th>
						<th>Reproducir</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody>");
		$idUsuarioAux=-1;
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$botonEditar="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm mx-3' onClick='editarNombreAudio(\"".$fila[4]."\",\"".$fila[0]."\");return false;' title='Guardar cambios'>
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
					</a>";
			
			$botonEnviarAhora="<a href='#' class='btn btn-icon btn-light btn-hover-primary btn-sm mx-3' data-toggle='modal' onClick='abrirModalConfiguracionAudio(\"".$fila[0]."\",2);return false;' title='Enviar ahora'>
						<span class='svg-icon svg-icon-primary svg-icon-2x'>
							<svg xmlns='http://www.w3.org/2000/svg' height='16' width='16' viewBox='0 0 512 512'><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill='#548b50' d='M464 256A208 208 0 1 0 48 256a208 208 0 1 0 416 0zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zM188.3 147.1c7.6-4.2 16.8-4.1 24.3 .5l144 88c7.1 4.4 11.5 12.1 11.5 20.5s-4.4 16.1-11.5 20.5l-144 88c-7.4 4.5-16.7 4.7-24.3 .5s-12.3-12.2-12.3-20.9V168c0-8.7 4.7-16.7 12.3-20.9z'/></svg>
						</span>
					</a>";
			
			$botonProgramarReproducir="<a href='#' class='btn btn-icon btn-light btn-hover-primary btn-sm mx-3' data-toggle='modal' onClick='abrirModalConfiguracionAudio(\"".$fila[0]."\",1);return false;' title='Configurar reproducción'>
						<span class='svg-icon svg-icon-primary svg-icon-2x'>
							<svg xmlns='http://www.w3.org/2000/svg' height='16' width='16' viewBox='0 0 512 512'><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill='#60abf0' d='M464 256A208 208 0 1 1 48 256a208 208 0 1 1 416 0zM0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z'/></svg>
						</span>
					</a>";
			
			$botonEliminar="<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Fichero de Audio Definitivamente\",\"¿Estas seguro de que deseas eliminar este fichero de audio?\",37,\"".$fila[0]."\",\"".$fila[4]."\");return false;' title='Eliminar Audio'>
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
			
			//cliente//usuario
			if($idUsuarioAux!=$fila[4]){
				$cliente="Sin asignar";
				$patron1="SELECT nombre FROM usuarios WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$fila[4]);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 5669075456466356890097");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					$cliente=$fila1[0];
				}
				mysqli_free_result($respuesta1);
				
				printf("<tr>
                        <td></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'>%s</td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
					</tr>",$cliente);
				$idUsuarioAux=$fila[4];
			}
			
			
			//reproductor audio
			$urlFicheroAudio="https://panel.modularbox.com/archivos_subidos/clientes/".$fila[4]."/audios/".$fila[2];
			$reproductorAudio="<audio controls title='Reproducir' style='width: 183px;''>
								  <source src='".$urlFicheroAudio."' type='audio/ogg'>
								  <source src='".$urlFicheroAudio."' type='audio/mpeg'>
								Tu navegador no soporta este formato.
								</audio>";

			
			printf("<tr>
                        <td></td>
						<td><input type='text' class='form-control' id='nombreFicheroAudio%s' value='%s'/></td>
						<td><input type='text' class='form-control inputReadOnly' id='fAltaFicheroAudio%s' value='%s' readonly/></td>
						<td nowrap='nowrap'>%s</td>
						<td nowrap='nowrap'>%s%s%s%s</td>
					</tr>",$fila[0],$fila[1],$fila[0],convierteFechaBarra($fila[3]),$reproductorAudio,$botonEditar,$botonEnviarAhora,$botonProgramarReproducir,$botonEliminar);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
                    <tr>
                        <th>#</th>
						<th>Nombre</th>
						<th>Fecha Subida</th>
						<th>Reproducir</th>
						<th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>');
	}
}

//filtro clientes ficheros audios, subidor y filtro
function cargaUsuariosSubidoAudios($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta=" AND (permisos=\"2\" OR permisos=\"1\")";
	}else if($_SESSION["permisossession"]==2){
		$consulta.=" AND permisos=\"2\" AND id=".$_SESSION["idusersession"];
		$_SESSION["usuarioAudioSubirList"]=$_SESSION["idusersession"];
	}else if($_SESSION["permisossession"]==3){
		$patron99="SELECT idempresa FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
		$sql99=sprintf($patron99,$_SESSION["idusersession"]);
		$respuesta99=mysqli_query($con,$sql99) or die ("Error 312152359943455456454455899121215");
		if(mysqli_num_rows($respuesta99)>0){
			$fila99=mysqli_fetch_array($respuesta99);
			$consulta.=" AND id=\"".$fila99[0]."\"";
			$_SESSION["usuarioAudioSubirList"]=$fila99[0];
		}
		mysqli_free_result($respuesta99);
	}
	
	printf("<select class='form-control' id='selectUsuariosAudiosSubirFiltro' onChange='filtrarUsuariosAudioSubidor(this);'>");
	if($_SESSION["permisossession"]==1){
		printf("<option value='0'>Selecciona Cliente:</option>");
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 3121523583437345678900443455456454455899121215");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioAudioSubirList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//cargar nodos audios cliente
function cargaNodosAudiosCliente($idUsuario,$con){
	
	$consulta="";
	if(/*$idUsuario*/$_SESSION["usuarioAudioSubirList"]>0){
		$consulta=" AND idusuario="./*$idUsuario*/$_SESSION["usuarioAudioSubirList"];
	
		printf("<select class='form-control select2' style='width:100%s;' id='kt_select2_3' multiple='multiple'>","%");

		$patron="SELECT id,nombre FROM audio_nodos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
		$sql=sprintf($patron,$consulta);
		$respuesta=mysqli_query($con,$sql) or die ("Error 312152352346234443455456454455899121215");
		if(mysqli_num_rows($respuesta)>0){

			printf("<option value='-99' selected>Enviar a todos</option>");

			for($i=0;$i<mysqli_num_rows($respuesta);$i++){
				$fila=mysqli_fetch_array($respuesta);

				$selected="";
				/*if($fila[0]==$_SESSION["usuarioAudioSubirList"]){
					$selected=" selected";
				}*/

				printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]);
			}
			mysqli_free_result($respuesta);
		}
		printf("</select>");//<script>$('#selectNodoAudioCliente').select2();</script>
	}else{
		printf("<select class='form-control' style='width:100%s;' id='kt_select2_3'><option>Seleciona un Cliente</option></select>","%");
	}
}

// CARGA colas reproduccion audios
function cargaColasReproduccionAudiosList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioAudioList"]!="0"*/){
		if($_SESSION["usuarioAudioList"]!="0"){
			$consulta.=" AND audio_ficheroaudio.idusuario=\"".quitaComillasD($_SESSION["usuarioAudioList"])."\"";
		}
	}else{
		$consulta.=" AND audio_ficheroaudio.idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	//group by idaccionrelacionada--> accion o mismo evento para varios nodos
	
	$patron="SELECT audio_ficheroaudio.id,audio_ficheroaudio.idusuario,audio_ficheroaudio.nombre,audio_ficheroaudio.url,audio_colashistorial.numeroreproducciones,audio_colashistorial.fechareproducir,audio_colashistorial.horareproducir,audio_colashistorial.idnodo,audio_colashistorial.reproducido,audio_colashistorial.id,audio_colashistorial.idenviadopor,audio_colashistorial.idaccionrelacionada FROM audio_ficheroaudio,audio_colashistorial WHERE audio_ficheroaudio.borrado=\"n\" AND audio_colashistorial.idaudio=audio_ficheroaudio.id AND audio_colashistorial.reproducido=\"n\" AND audio_colashistorial.borrado=\"n\" AND audio_colashistorial.fechareproducir>=\"%s\"%s GROUP BY idaccionrelacionada ORDER BY audio_ficheroaudio.nombre";
	$sql=sprintf($patron,date("Y-m-d"),$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323343513423655454634543757545783541118");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Audio</th>
					  <th>Nodos</th>
					  <th>Rep.</th>
					  <th>Enviado por</th>
					  <th>Fecha</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$idHistorial=$fila[9];
			
			$idEnviadoPor=$fila[10];
			
			//nodo o nodos segun el group by
			$idNodo=$fila[7];
			$nombreNodos="";
			$idaccionrelacionada=$fila[11];
			
			if($idaccionrelacionada>0){
				//nodos a los que afecta
				$patron229="SELECT idnodo,id,reproducido,fechareproducir,horareproducir FROM audio_colashistorial WHERE idaccionrelacionada=\"%s\" AND idaudio=\"%s\" AND borrado=\"n\"";
				$sql229=sprintf($patron229,$idaccionrelacionada,$fila[0]);
				$respuesta229=mysqli_query($con,$sql229) or die ("Error al buscar 9635325343453457556655313366762234653509258");
				if(mysqli_num_rows($respuesta229)>0){
					for($j=0;$j<mysqli_num_rows($respuesta229);$j++){
						$fila229=mysqli_fetch_array($respuesta229);
						
						$patron29="SELECT nombre FROM audio_nodos WHERE id=\"%s\"";
						$sql29=sprintf($patron29,$fila229[0]);
						$respuesta29=mysqli_query($con,$sql29) or die ("Error al buscar 9635325531336296762234653509258");
						if(mysqli_num_rows($respuesta29)>0){
							$fila29=mysqli_fetch_array($respuesta29);
							$auxNombreNodo=$fila29[0];
							
							//*** START Comprobar estado, cada nodo***
							$estadoReproNodo="border-warning";
							if($fila229[2]=="n"){//no reproducido
								if($fila229[3]>date("Y-m-d")){//fecha reproducir, fecha futura
									//="Pendiente";
									$estadoReproNodo="border-warning";

								}else if($fila229[3]==date("Y-m-d")){//fecha reproducir, fecha es hoy
									if($fila229[4]<=date("H:i:s")){//hora ya pasada
										//$estado="Error";
										$estadoReproNodo="border-danger";
									}else{//hora pendiente
										//="Pendiente";
										$estadoReproNodo="border-warning";
									}
								}else if($fila229[3]<date("Y-m-d")){//fecha reproducir
									//="Error";
									$estadoReproNodo="border-danger";
								}else{//no va a darse el caso,
									//="Pendiente";
									$estadoReproNodo="border-warning";
								}
							}else if($fila229[2]=="s"){//si reproducido
								//="Reproducido";
								$estadoReproNodo="border-success";
							}else if($fila229[2]=="m"){//mal, problemas, error
								//="Error";
								$estadoReproNodo="border-danger";
							}else{//aun no ha llegado la hora de realizar la accion
								//="Pendiente";
								$estadoReproNodo="border-warning";
							}
							/*** END Comprobar estado, cada nodo***/
								
							$nombreNodos.="<div class='border ".$estadoReproNodo." rounded' id='idLinHist".$fila229[1]."' style='display: inline-block;margin-right: 5px;padding: 1px;'>".substr($auxNombreNodo,0,3)."</div>";
							
						}
						mysqli_free_result($respuesta29);
					}
					
				}
				mysqli_free_result($respuesta229);
				
			}else{//para antes del ajuste los no vinculados, mostrar al menos el nombre del nodo, sin uso
				/*$patron2="SELECT nombre FROM audio_nodos WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$idNodo);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 96353255313366762234653509258");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$nombreNodos=$fila2[0];
					if(strlen($nombreNodos)>12){
						$nombreNodos=substr($nombreNodos,0,12)."...";
					}
				}
				mysqli_free_result($respuesta2);*/
			}
			
			//enviado por
			$enviadoPor="";
			$patron3="SELECT nombre FROM usuarios WHERE id=\"%s\"";
			$sql3=sprintf($patron3,$idEnviadoPor);
			$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 9635325531333465350925833");
			if(mysqli_num_rows($respuesta3)>0){
				$fila3=mysqli_fetch_array($respuesta3);
				$enviadoPor=$fila3[0];
			}
			mysqli_free_result($respuesta3);
			
			//fecha y hora reproducir
			$fechaHoraReproducir=convierteFechaBarra($fila[5]).", ".$fila[6];
			
			//reproductor audio
			$urlFicheroAudio="https://panel.modularbox.com/archivos_subidos/clientes/".$fila[1]."/audios/".$fila[3];
			$reproductorAudio="<audio controls style='width: 183px;' title='Reproducir'>
								  <source src='".$urlFicheroAudio."' type='audio/ogg'>
								  <source src='".$urlFicheroAudio."' type='audio/mpeg'>
								Tu navegador no soporta este formato.
								</audio>";
			
			
			//boton eliminar
			$botonesAcciones="";
			if($fila[8]=="n" && ($fila[5]>date("Y-m-d") || ($fila[5]==date("Y-m-d") && $fila[6]>date("H:i:s") )) ){
				$botonesAcciones="<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Cancelar Audio\",\"¿Estas seguro de que deseas cancelar este fichero de audio?\",38,\"".$idHistorial."\",\"".$idNodo."\",\"2\");return false;'>
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
			
			
			//pulsar tr
			$funcion="";
			//$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=37&i=%s\");'",$idNodo);
			
			printf("<tr>
				<td></td>
				<td class=''>%s</td>
				<td class=''>%s</td>
				<td class=''%s>%s</td>
				<td class=''>%s</td>
				<td class=''>%s</td>
				<td class=''>%s</td>
				<td class=''>%s</td>
				</tr>",$fila[2],$reproductorAudio,$funcion,$nombreNodos,$fila[4],$enviadoPor,$fechaHoraReproducir,$botonesAcciones);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Audio</th>
					  <th>Nodos</th>
					  <th>Rep.</th>
					  <th>Enviado por</th>
					  <th>Fecha</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}

//filtro clientes colas Audio list
function cargaUsuariosColasAudioFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 318343755657786761734544452346564544551215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosColasAudio(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioAudioList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//convertir texto a audio
function convertirTextoAMp3($texto,$rutaArchivosSubidos,$con){
	$file="n";
	
	if($texto!=""){
  		$ruta=/*$_SERVER["DOCUMENT_ROOT"].*/"./archivos_subidos/clientes".$rutaArchivosSubidos;
		
		$longitudTexto=strlen($texto);
		
		// Yes Español is a beautiful language.
		$lang = "es";

		// MP3 filename generated using MD5 hash
		// Added things to prevent bug if you want same sentence in two different languages
		$file = md5($lang."?".urlencode($texto));

		// Save MP3 file in folder with .mp3 extension 
		$file = $ruta."/" . $file . ".mp3";


		// Check folder exists, if not create it, else verify CHMOD
		if (!is_dir($ruta."/")){
			mkdir($ruta."/");
		}else{
			if (substr(sprintf('%o', fileperms($ruta.'/')), -4) != "0777"){
				chmod($ruta."/", 0777);
			}
		}

		// If MP3 file exists do not create new request
		if (!file_exists($file))
		{
			$mp3 = file_get_contents(
			'https://translate.google.com/translate_tts?ie=UTF-8&client=gtx&q='.urlencode($texto).'&tl=es&total='.$longitudTexto.'&idx=0&textlen='.$longitudTexto.'&prev=input');
			
			file_put_contents($file, $mp3);
			
		}
	}
	return $file;
}

//poner online offline, segun ultima conexion
function ultimaConexionOnlinOfflineAudios($con){
	$restarHoras=0;
	$patron="SELECT id,fechaultimaconsulta,horaultimaconsulta FROM audio_nodos WHERE borrado=\"n\" AND guardado=\"s\"";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632343563634566563455899");
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

			$patron1="UPDATE audio_nodos SET conexion=\"%s\" WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$estado,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 1234534563412121789");
		}
		
	}
	mysqli_free_result($respuesta);
}

// CARGA historial general reproduccion audios
function cargaHistorialGeneralAudios($con){
	$consulta="";
	//consulta usuario/cliente
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioAudioList"]!="0"*/){
		if(isset($_SESSION["usuarioAudioList"]) && $_SESSION["usuarioAudioList"]!="0"){
			$consulta=" AND audio_ficheroaudio.idusuario=\"".quitaComillasD($_SESSION["usuarioAudioList"])."\"";
		}else{//si no selecciona cliente no mostrar nada
			$consulta=" AND audio_ficheroaudio.idusuario=-99";
		}
	}else{
		$consulta=" AND audio_ficheroaudio.idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	//filtro nodo
	if(isset($_SESSION["nodoHistorialGenAudioList"]) && $_SESSION["nodoHistorialGenAudioList"]>0){
		$consulta.=" AND audio_colashistorial.idnodo=\"".intval($_SESSION["nodoHistorialGenAudioList"])."\"";
	}
	
	//consulta fechas
	if(isset($_SESSION["fechaIniHistorialGeneralAudio"]) && isset($_SESSION["fechaFinHistorialGeneralAudio"]) ){
		$consulta.=" AND audio_colashistorial.fechareproducir>=\"".$_SESSION["fechaIniHistorialGeneralAudio"]."\" AND audio_colashistorial.fechareproducir<=\"".$_SESSION["fechaFinHistorialGeneralAudio"]."\"";
	}
	
	//consulta por nodo, cada audio reproducido en cada nodo
	/*$patron="SELECT audio_colashistorial.id,audio_colashistorial.numeroreproducciones,audio_colashistorial.fechareproducir,audio_colashistorial.horareproducir,audio_colashistorial.reproducido,audio_colashistorial.horareproducido,audio_colashistorial.idaudio,audio_nodos.id FROM audio_colashistorial,audio_nodos WHERE audio_colashistorial.idnodo=audio_nodos.id AND audio_nodos.guardado=\"s\" AND audio_nodos.borrado=\"n\" AND audio_colashistorial.borrado=\"n\"%s ORDER BY audio_colashistorial.fechareproducir DESC, audio_colashistorial.horareproducir DESC, audio_colashistorial.id DESC";
	$sql=sprintf($patron,$consulta);*/
	
	//consulta por audio, cada orden y mostrar si esta reproducido o no
	$patron="SELECT audio_colashistorial.id,audio_colashistorial.numeroreproducciones,audio_colashistorial.fechareproducir,audio_colashistorial.horareproducir,audio_colashistorial.reproducido,audio_colashistorial.horareproducido,audio_colashistorial.idaudio,audio_colashistorial.idaccionrelacionada,audio_ficheroaudio.nombre FROM audio_ficheroaudio,audio_colashistorial WHERE audio_ficheroaudio.borrado=\"n\" AND audio_colashistorial.idaudio=audio_ficheroaudio.id AND audio_colashistorial.borrado=\"n\"%s GROUP BY idaccionrelacionada ORDER BY audio_colashistorial.fechareproducir DESC,audio_colashistorial.horareproducir DESC,audio_ficheroaudio.nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323003342365545464568900034543757545783541118");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nodos</th>
					  <th>Audio</th>
					  <th>Rep.</th>
					  <th>Fecha Inicio</th>
					  <th>Hora Inicio</th>
					  <th>Hora Reproducido</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$nombreAudio=$fila[8];
			$idAudio=$fila[6];
			
			//nodo o nodos segun el group by
			$nombreNodos="";
			$idaccionrelacionada=$fila[7];
			
			if($idaccionrelacionada>0){
				//nodos a los que afecta
				$patron229="SELECT idnodo,id,reproducido,fechareproducir,horareproducir FROM audio_colashistorial WHERE idaccionrelacionada=\"%s\" AND idaudio=\"%s\" AND borrado=\"n\"";
				$sql229=sprintf($patron229,$idaccionrelacionada,$idAudio);
				$respuesta229=mysqli_query($con,$sql229) or die ("Error al buscar 96353253446543453457556655313366762234653509258");
				if(mysqli_num_rows($respuesta229)>0){
					for($j=0;$j<mysqli_num_rows($respuesta229);$j++){
						$fila229=mysqli_fetch_array($respuesta229);
						
						$patron29="SELECT nombre FROM audio_nodos WHERE id=\"%s\"";
						$sql29=sprintf($patron29,$fila229[0]);
						$respuesta29=mysqli_query($con,$sql29) or die ("Error al buscar 963532553123479336296762234653509258");
						if(mysqli_num_rows($respuesta29)>0){
							$fila29=mysqli_fetch_array($respuesta29);
							$auxNombreNodo=$fila29[0];
							
							//*** START Comprobar estado, cada nodo***
							$estadoReproNodo="border-warning";
							if($fila229[2]=="n"){//no reproducido
								if($fila229[3]>date("Y-m-d")){//fecha reproducir, fecha futura
									//="Pendiente";
									$estadoReproNodo="border-warning";
								}else if($fila229[3]==date("Y-m-d")){//fecha reproducir, fecha es hoy
									if($fila229[4]<=date("H:i:s")){//hora ya pasada
										//$estado="Error";
										$estadoReproNodo="border-danger";
									}else{//hora pendiente
										//="Pendiente";
										$estadoReproNodo="border-warning";
									}
								}else if($fila229[3]<date("Y-m-d")){//fecha reproducir
									//="Error";
									$estadoReproNodo="border-danger";
								}else{//no va a darse el caso,
									//="Pendiente";
									$estadoReproNodo="border-warning";
								}
							}else if($fila229[2]=="s"){//si reproducido
								//="Reproducido";
								$estadoReproNodo="border-success";
							}else if($fila229[2]=="m"){//mal, problemas, error
								//="Error";
								$estadoReproNodo="border-danger";
							}else{//aun no ha llegado la hora de realizar la accion
								//="Pendiente";
								$estadoReproNodo="border-warning";
							}
							/*** END Comprobar estado, cada nodo***/
								
							$nombreNodos.="<div class='border ".$estadoReproNodo." rounded clickable' id='idLinHist".$fila229[1]."' style='display: inline-block;margin-right: 5px;padding: 1px;' onClick='cargaLocation(\"index.php?s=37&i=".$fila229[0]."\");'>".substr($auxNombreNodo,0,3)."</div>";
						}
						mysqli_free_result($respuesta29);
					}
				}
				mysqli_free_result($respuesta229);
			}
			
			//btn acciones
			$botonEliminar="";
			
			printf("<tr>
                        <td></td>
						<td class=''><input type='hidden' value=\"%s\"/>%s</td>
						<td class=''>%s</td>
                        <td class=''>%s</td>
						<td class=''>%s</td>
						<td class=''>%s</td>
						<td class=''>%s</td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$fila[0],$nombreNodos,$nombreAudio,$fila[1],convierteFechaBarra($fila[2]),$fila[3],$fila[5],$botonEliminar );
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nodos</th>
					  <th>Audio</th>
					  <th>Rep.</th>
					  <th>Fecha Inicio</th>
					  <th>Hora Inicio</th>
					  <th>Hora Reproducido</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}

//filtro clientes historial Audio list
function cargaUsuariosHistorialGeneralAudioFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 3183437556573459800544452346564544551215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosHistorialGeneralAudio(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioAudioList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//filtro nodos historial Audio list
function cargaNodosHistorialGeneralAudioFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1 && isset($_SESSION["usuarioAudioList"]) && $_SESSION["usuarioAudioList"]>0){//si hay usuario/cliente seleccionado
		$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioAudioList"])."\"";
	}else if($_SESSION["permisossession"]==1){//si es el admin y no hay nada seleccionado
		$consulta.=" AND idusuario=-99";
	}else if($_SESSION["permisossession"]!=1){//si entra otro perfil/permiso
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	$patron="SELECT id,nombre FROM audio_nodos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 318343755653457773459800544452344578904544551215");
	printf("<select class='form-control' id='selectNodosFiltro' ><option value='0'>Selecciona Nodo:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["nodoHistorialGenAudioList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}
?>