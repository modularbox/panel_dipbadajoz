<?php 

													/*************************************
													 *									 *
													 *		        SAFEY		         *
													 *									 *
													 *************************************/	
// CARGA safey
function cargaNodosSafeyList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioSafeyList"]!="0"*/){
		if($_SESSION["usuarioSafeyList"]!="0"){
			$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioSafeyList"])."\"";
		}
	}else {
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	if($_SESSION["conexionSafeyList"]!=""){
		$consulta.=" AND conexion=\"".quitaComillasD($_SESSION["conexionSafeyList"])."\"";
	}
	
	/*START actualizar estado placas*/
	ultimaConexionOnlinOfflineSafey($con);
	/*END actualizar estado placas*/
	
	$patron="SELECT id,nombre,idusuario,conexion,ubicacion FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963235345467787879958");
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
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963534509258");
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
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=16&i=%s\");'",$fila[0]);
			
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

//filtro clientes Safey list
function cargaUsuariosSafeyFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre ASC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 31215235875565778676741236456455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosSafey(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioSafeyList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//filtro estado contadores list
function cargaEstadosSafeyFiltro($con){
	$selectedUno="";
	$selectedDos="";
	if($_SESSION["conexionSafeyList"]=="on"){
		$selectedUno=" selected";
	}else if($_SESSION["conexionSafeyList"]=="off"){
		$selectedDos=" selected";
	}
	
	printf("<select class='form-control' id='selectConexionFiltro' onChange='filtrarConexionSafey(this);'><option value=''>Selecciona Conexión:</option>");
	printf("<option value=\"on\" %s>Online</option><option value=\"off\" %s>Offline</option>",$selectedUno,$selectedDos);	
	printf('</select>');
}

//cargar puertas nodo safey
function cargaPuertasNodoSafey($idNodo,$con){

	$consultaPermisos="";
	$nodosPermisosId="";
	if($_SESSION["permisossession"]==3){
		$nodosPermisosId="";
			
		$patron99="SELECT id FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
		$sql99=sprintf($patron99,$_SESSION["idusersession"]);
		$respuesta99=mysqli_query($con,$sql99) or die ("Error 3121523585709909990909956455899121215");
		if(mysqli_num_rows($respuesta99)>0){
			$fila99=mysqli_fetch_array($respuesta99);
			
			$patron98="SELECT id FROM safey_accesos WHERE borrado=\"n\" AND guardado=\"s\" AND maillogin=\"%s\"";
			$sql98=sprintf($patron98,$fila99[0]);
			$respuesta98=mysqli_query($con,$sql98) or die ("Error 3121523585709909099809956455899121215");
			if(mysqli_num_rows($respuesta98)>0){
				$fila98=mysqli_fetch_array($respuesta98);

				$patron97="SELECT DISTINCT(nodo) FROM safey_accesosnodos WHERE borrado=\"n\" AND idacceso=\"%s\" AND (permisos=\"s\" OR l=\"s\" OR m=\"s\" OR x=\"s\" OR j=\"s\" OR v=\"s\" OR s=\"s\" OR d=\"s\")";
				$sql97=sprintf($patron97,$fila98[0]);
				$respuesta97=mysqli_query($con,$sql97) or die ("Error 31215235857099090998979709956455899121215");
				if(mysqli_num_rows($respuesta97)>0){
					$nodosPermisosId=" AND (";
					for($z=0;$z<mysqli_num_rows($respuesta97);$z++){
						$fila97=mysqli_fetch_array($respuesta97);

						$nodosPermisosId.=" safey_nodos.id=\"".$fila97[0]."\"";

						if($z<mysqli_num_rows($respuesta97)-1){
							$nodosPermisosId.=" OR ";
						}
					}
					$nodosPermisosId.=")";
				}
			}
		}
		
		$entraPuertas=true;
		if($nodosPermisosId==""){
			$entraPuertas=false;
		}
		
	}else{
		$entraPuertas=true;
	}
	
	/*START cargar las propias del nodo*/
	$patron="SELECT safey_puertas.id,safey_puertas.nombre,safey_puertas.rutaimg,safey_nodos.idusuario,safey_puertas.urlemergencia,safey_puertas.estado,safey_puertas.salidaplaca,safey_puertas.pulsocorriente,safey_puertas.duracionsegundos FROM safey_puertas,safey_nodos WHERE safey_puertas.borrado=\"n\" AND safey_puertas.idnodo=\"%s\" AND safey_nodos.id=safey_puertas.idnodo AND safey_nodos.guardado=\"s\" AND safey_nodos.borrado=\"n\"%s ORDER BY id ASC";
	$sql=sprintf($patron,$idNodo,$nodosPermisosId);
	$respuesta=mysqli_query($con,$sql) or die ("Error 3121523585700909056455899121215");
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
			
			if($fila[2]!=""){
				$rutaImg="./archivos_subidos/clientes/".$fila[3]."/safey/".$fila[2];
				$styleImg="style='max-height: 296px'";
			}else{//por defecto
				$rutaImg="./nimg/img_demo.png";
				$styleImg=" style='height: 296px'";//340//525.11px
			}
		
			$botonBorrar="";
			if($_SESSION["permisossession"]==1 /*|| $_SESSION["permisossession"]==2*/){
				$botonBorrar="<a href='javascript: void(0)' class='btn font-weight-bold btn-light-danger btn-shadow ml-2' onClick='confirmacion(\"warning\",\"Eliminar Puerta\",\"¿Estas seguro de que deseas eliminar esta puerta?\",13,\"".$idNodo."\",\"".$fila[0]."\",\"\");return false;'>Eliminar</a>";
			}
			
			$botonUrlEmergencia="";
			if($fila[4]!=""){
				$botonUrlEmergencia="<a href='javascript: void(0)' class='btn font-weight-bold btn-light-warning btn-shadow ml-2' onClick='ejecutarUrlEmergenciaPuertaSafey(\"".$fila[4]."\",\"".$idNodo."\",\"".$fila[0]."\",3);'>Emergencia</a>";
			}
            
			$estadoPuerta="";
			if($fila[5]==1){
				$estadoPuerta="<span class='btn btn-light btn-text-success btn-hover-text-success font-weight-bold' style='cursor: default;margin-left: 6px;'>Abierta</span>";
			}else if($fila[5]==2){
				$estadoPuerta="<span class='btn btn-light btn-text-danger btn-hover-text-danger font-weight-bold' style='cursor: default;margin-left: 6px;'>Cerrada</span>";
			}else{
				$estadoPuerta="<span class='btn btn-light btn-text-warning btn-hover-text-warning font-weight-bold' style='cursor: default;margin-left: 6px;'>Sin datos</span>";
			}
			
			$botonesSalidaPuerta="";
			if($_SESSION["permisossession"]==1){
				
				$classActiveSUno="";
				$classActiveSDos="";
				if($fila[6]==1){
					$classActiveSUno="active";
				}else if($fila[6]==2){
					$classActiveSDos="active";
				}
				
				//pulsacion rele, o corriente, duracion
				$classActiveReleUno="";
				$classActiveReleDos="";
				if($fila[7]=="p"){//pulso
					$classActiveReleUno="active";
				}else if($fila[7]=="c"){//corriente
					$classActiveReleDos="active";
				}
				$tiempoSegundosRele=$fila[8];
				
				$botonesSalidaPuerta="<div class='dropdown dropdown-inline' style='margin-left: 6px;'>
										<button type='button' class='btn btn-light-primary btn-icon btn-sm' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
											<i class='ki ki-bold-more-ver'></i>
										</button>
										<div class='dropdown-menu'>
											<a class='dropdown-item ".$classActiveSUno."' href='javascript: void(0)' id=\"electroPuertaE1".$fila[0]."\" onClick='cambiarSalidaPuertaPlaca(\"".$idNodo."\",\"".$fila[0]."\",1);'>Electro 1</a>
											<a class='dropdown-item ".$classActiveSDos."' href='javascript: void(0)' id=\"electroPuertaE2".$fila[0]."\" onClick='cambiarSalidaPuertaPlaca(\"".$idNodo."\",\"".$fila[0]."\",2);'>Electro 2</a>
                                            <ul class='navi navi-hover'>
                                                <li class='navi-separator my-3'></li>
                                                <li class='navi-header font-weight-bold py-2'>
								                    <span class='font-size-lg'>Relé:</span>
								                </li>
                                            </ul>
                                            <a class='dropdown-item ".$classActiveReleUno."' href='javascript: void(0)' id=\"relePulsacion".$fila[0]."\" onClick='cambiarPulsacionCorrienteRele(\"".$idNodo."\",\"".$fila[0]."\",\"p\");'>Pulsación</a>
                                            <a class='dropdown-item ".$classActiveReleDos."' href='javascript: void(0)' id=\"releCorriente".$fila[0]."\" onClick='cambiarPulsacionCorrienteRele(\"".$idNodo."\",\"".$fila[0]."\",\"c\");'>12v</a>
											<input type='number' class='form-control col-md-11 my-2' onKeyUp='cambiarTiempoPulsacionCorrienteRele(\"".$idNodo."\",\"".$fila[0]."\");' id=\"releSegundosPulsacion".$fila[0]."\" value='".$tiempoSegundosRele."' style='margin: 0 auto;' min='1' max='50' placeholder='1' >
                                            <!--<select class='form-control col-md-11 my-2' name='opcionPuerta' id='opcionPuerta' style='margin: 0 auto;'>
                                                <option value='0'>Potencia</option>
                                                <option value='1'>Opción 1</option>
                                                <option value='2'>Opción 2</option>
                                            </select>-->
                                            <!--<ul class='navi navi-hover'>
                                                <li class='navi-separator my-3'></li>
                                            </ul>-->
										</div>
									</div>";
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
                                        <div class='col-md-4 my-2 my-md-0'>
								            <input type='text' class='form-control' id='puertaSafeyNom%s' value='%s' placeholder='Nombre'>
			                             </div>
									   <a href='javascript: void(0)' class='btn font-weight-bold btn-success btn-shadow' onClick='guardarNombrePuertaSafey(\"%s\",\"%s\");'>%s</a>
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
								<a href='javascript: void(0)' class='btn font-weight-bold btn-success btn-shadow' onClick='abrirPuertaWebSafeyHistorial(\"".$fila[0]."\",\"".$idNodo."\",2);'>Abrir</a>
								%s
								%s
							</div>
						</div>

						<!--end::Code example-->
					</div>",$rutaImg,$styleImg,$fila[0],$fila[1],$fila[0],$fila[3],"Guardar"/*$fila[1]*/,$estadoPuerta,$botonesSalidaPuerta,$botonUrlEmergencia,$botonBorrar);
		}
		//cerrar div row
		printf("</div>");
	}
	mysqli_free_result($respuesta);
	/*END cargar las propias del nodo*/
	
	////-/-/-/-/-/-/-/-/-
	
	/*START cargar las de nodos asociasdos*/
	$consultaNodosMostrar=obtenerConsultaPlacasAsociadas($idNodo,1,$con);
	if($consultaNodosMostrar!="" && $entraPuertas){
		$patron1="SELECT safey_puertas.id,safey_puertas.nombre,safey_puertas.rutaimg,safey_nodos.idusuario,safey_puertas.urlemergencia,safey_nodos.id,safey_puertas.estado,safey_puertas.idnodo FROM safey_puertas,safey_nodos WHERE safey_puertas.borrado=\"n\" AND safey_nodos.id=safey_puertas.idnodo AND safey_nodos.guardado=\"s\" AND safey_nodos.borrado=\"n\"%s%s ORDER BY safey_nodos.id ASC";
		$sql1=sprintf($patron1,$consultaNodosMostrar,$nodosPermisosId);
		$respuesta1=mysqli_query($con,$sql1) or die ("Error 3121523585700909051116455899121215");
		if(mysqli_num_rows($respuesta1)>0){
			for($j=0;$j<mysqli_num_rows($respuesta1);$j++){
				$fila1=mysqli_fetch_array($respuesta1);

				//abrir div row
				if(($j % 4)==0){
					//cerrar
					if($j>0){
						printf("</div>");
					}
					printf("<div class='row'>");
				}

				if($fila1[2]!=""){
					$rutaImg="./archivos_subidos/clientes/".$fila1[3]."/safey/".$fila1[2];
					$styleImg="style='max-height: 296px'";
				}else{//por defecto
					$rutaImg="./nimg/img_demo.png";
					$styleImg=" style='height: 296px'";//340//525.11
				}

				$botonBorrar="";//no si no estas en el nodo que es

				$botonUrlEmergencia="";
				if($fila1[4]!=""){
					$botonUrlEmergencia="<a href='javascript: void(0)' class='btn font-weight-bold btn-light-warning btn-shadow ml-2' onClick='ejecutarUrlEmergenciaPuertaSafey(\"".$fila1[4]."\",\"".$idNodo."\",\"".$fila1[0]."\",3);'>Emergencia</a>";
				}
				
				$estadoPuerta="";
				if($fila1[6]==1){
					$estadoPuerta="<span class='btn btn-light btn-text-success btn-hover-text-success font-weight-bold' style='cursor: default;margin-left: 6px;'>Abierta</span>";
				}else if($fila1[6]==2){
					$estadoPuerta="<span class='btn btn-light btn-text-danger btn-hover-text-danger font-weight-bold' style='cursor: default;margin-left: 6px;'>Cerrada</span>";
				}else{
					$estadoPuerta="<span class='btn btn-light btn-text-warning btn-hover-text-warning font-weight-bold' style='cursor: default;margin-left: 6px;'>Sin datos</span>";
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
										   <a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm' onClick='cargaLocation(\"index.php?s=16&i=%s\");return false;' title='Ir al nodo'>
												<span class='svg-icon svg-icon-primary svg-icon-2x'><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Navigation\Right-2.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
													<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
														<polygon points='0 0 24 0 24 24 0 24'/>
														<rect fill='#000000' opacity='0.3' transform='translate(8.500000, 12.000000) rotate(-90.000000) translate(-8.500000, -12.000000) ' x='7.5' y='7.5' width='2' height='9' rx='1'/>
														<path d='M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z' fill='#000000' fill-rule='nonzero' transform='translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997) '/>
													</g>
												</svg><!--end::Svg Icon--></span>
											</a>
										   %s
										</div>
									</div>
								</div>
							</div>
							<!--end::Card-->
							<!--begin::Code example-->
							<div class='example example-compact mt-2 mb-7'>
								<div class='example-tools justify-content-center'>
									<a href='javascript: void(0)' class='btn font-weight-bold btn-success btn-shadow' onClick='abrirPuertaWebSafeyHistorial(\"".$fila1[0]."\",\"".$fila1[7]."\",2);'>Abrir</a>
									%s
									%s
								</div>
							</div>

							<!--end::Code example-->
						</div>",$rutaImg,$styleImg,$fila1[5],$estadoPuerta,$botonUrlEmergencia,$botonBorrar);
			}
			//cerrar div row
			printf("</div>");
		}
		mysqli_free_result($respuesta1);
	}
	/*START cargar las de nodos asociasdos*/
}

//saber placas asociadas a una placa y sus relacciones
function obtenerConsultaPlacasAsociadas($idNodoPlaca,$opcion,$con){
	$consultaPlacasAsociadas="";
	
	$idNodoVinculado=$idNodoPlaca;
	$arrayNodosVinculados=array();
	
	$patron="SELECT idnodouno,idnododos FROM safey_nodos_vinculados WHERE borrado=\"n\"  AND (idnodouno=\"%s\" OR idnododos=\"%s\")";
	$sql=sprintf($patron,$idNodoVinculado,$idNodoVinculado);
	$respuesta=mysqli_query($con,$sql) or die ("Error 312323451523222585700909056455899121215");
	if(mysqli_num_rows($respuesta)>0){
		for($j=0;$j<mysqli_num_rows($respuesta);$j++){
			$fila=mysqli_fetch_array($respuesta);

			//calcular asociado
			if($fila[0]==$idNodoVinculado){
				$idNodoVinculadoArray=$fila[1];
			}else if($fila[1]==$idNodoVinculado){
				$idNodoVinculadoArray=$fila[0];
			}else{
				$idNodoVinculadoArray=0;
			}

			//anadir al array
			if($idNodoVinculadoArray!=0){
				array_push($arrayNodosVinculados, intval($idNodoVinculadoArray));
			}
		}
	}
	mysqli_free_result($respuesta);
	
	$arrayTotales=$arrayNodosVinculados;
	$idExcluir="";
	do{
		if(!isset($arrayNodosVinculados[0])){ $arrayNodosVinculados[0]=0;}
		$idCompararBusqueda=$arrayNodosVinculados[0];
		
		$patron1="SELECT idnodouno,idnododos,id FROM safey_nodos_vinculados WHERE borrado=\"n\"  AND (idnodouno=\"%s\" OR idnododos=\"%s\")%s";
		$sql1=sprintf($patron1,$arrayNodosVinculados[0],$arrayNodosVinculados[0],$idExcluir);
		$respuesta1=mysqli_query($con,$sql1) or die ("Error 312323451523222565585334534700909056455899121215");
		if(mysqli_num_rows($respuesta1)>0){
			for($x=0;$x<mysqli_num_rows($respuesta1);$x++){
				$fila1=mysqli_fetch_array($respuesta1);

				$idExcluir.=" AND id<>".$fila1[2];
				
				if($fila1[0]==$idCompararBusqueda){
					$idNodoVinculadoArray=$fila1[1];
				}else if($fila1[1]==$idCompararBusqueda){
					$idNodoVinculadoArray=$fila1[0];
				}else{
					$idNodoVinculadoArray=0;
				}
				
				//anadir al array
				if($idNodoVinculadoArray!=0 && $arrayNodosVinculados[0]!=$idNodoVinculadoArray){
					array_push($arrayNodosVinculados, intval($idNodoVinculadoArray));
					array_push($arrayTotales, intval($idNodoVinculadoArray));
				}

				//borrar esa posicion,
				unset($arrayNodosVinculados[0]);
				$arrayNodosVinculados = array_unique($arrayNodosVinculados);
				$arrayNodosVinculados=array_values($arrayNodosVinculados);
				
			}
		}else{
			//borrar esa posicion
			unset($arrayNodosVinculados[0]);
			$arrayNodosVinculados = array_unique($arrayNodosVinculados);
			$arrayNodosVinculados=array_values($arrayNodosVinculados);
		}
    	
	}while(count($arrayNodosVinculados)>0);
	
	$arrayTotales = array_unique($arrayTotales);
	$arrayTotales = array_diff($arrayTotales, array($idNodoPlaca));
	$arrayTotales=array_values($arrayTotales);
	
	//montar consulta
	if($opcion==1){
		if(count($arrayTotales)>0){
			$consultaPlacasAsociadas=" AND (";
			for($z=0;$z<count($arrayTotales);$z++){
				$consultaPlacasAsociadas.="safey_nodos.id=\"".$arrayTotales[$z]."\"";

				if($z<count($arrayTotales)-1){
					$consultaPlacasAsociadas.=" OR ";
				}
			}
			$consultaPlacasAsociadas.=")";
		}
	}else if($opcion==2){;
		if(count($arrayTotales)>0){
			$consultaPlacasAsociadas="";
			for($z=0;$z<count($arrayTotales);$z++){
				$consultaPlacasAsociadas.=" AND safey_nodos.id<>\"".$arrayTotales[$z]."\"";
			}
		}
	}
	
	return $consultaPlacasAsociadas;
}

//historial puertas safey
function puertasSafeyHistorial($idNodo,$con){
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialPuertasSafey"]) && isset($_SESSION["fechaFinHistorialPuertasSafey"]) ){
		$consulta=" AND safey_historial.fechaalta>=\"".$_SESSION["fechaIniHistorialPuertasSafey"]."\" AND safey_historial.fechaalta<=\"".$_SESSION["fechaFinHistorialPuertasSafey"]."\"";
	}
	
	if(isset($_SESSION["puertaHistorialPuertasSafey"]) && $_SESSION["puertaHistorialPuertasSafey"]>0){
		$consulta=" AND safey_historial.idpuerta=".$_SESSION["puertaHistorialPuertasSafey"];
	}
	
	$patron="SELECT safey_historial.id,safey_historial.idpuerta,safey_historial.tipo,safey_historial.idacceso,safey_historial.idusuario,safey_historial.horaalta,safey_historial.fechaalta,safey_nodos.id,safey_historial.accionrealizada,safey_historial.miradoplaca FROM safey_historial,safey_nodos WHERE safey_historial.idnodo=\"%s\" AND safey_historial.idnodo=safey_nodos.id AND safey_nodos.guardado=\"s\" AND safey_nodos.borrado=\"n\"%s ORDER BY safey_historial.fechaalta DESC, safey_historial.horaalta DESC, safey_historial.id DESC";//LIMIT 0,50
	$sql=sprintf($patron,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323463455899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Puerta</th>
					  <th>Apertura</th>
					  <th>Nombre Acceso</th>
					  <th>Hora</th>
					  <th>Fecha</th>
					  <th>Resultado</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//nombre acceso
			$idUsuario=0;
			$nombreAcceso="Sin datos";
			if($fila[3]>0){
				$patron2="SELECT nombre,idusuario,apellidos FROM safey_accesos WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$fila[3]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 963534564222286454509258");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$nombreAcceso=$fila2[0]." ".$fila2[2];
					$idUsuario=$fila2[1];
				}
				mysqli_free_result($respuesta2);
			}
			
			if($idUsuario==$_SESSION["idusersession"] || $_SESSION["permisossession"]!=3){
				//puerta
				$patron1="SELECT nombre FROM safey_puertas WHERE id=\"%s\" AND idnodo=\"%s\"";
				$sql1=sprintf($patron1,$fila[1],$fila[7]);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96353456486454509258");
				$fila1=mysqli_fetch_array($respuesta1);
				mysqli_free_result($respuesta1);

				//accion
				$accion="";
				if($fila[2]==1){
					$accion="Pin";//Apertura Pin/LLave/Mando
				}else if($fila[2]==2){
					$accion="Apertura Web";
				}else if($fila[2]==3){
					$accion="Apertura Web Emergencia";
				}

				//nombre usuario
				if($fila[2]==2 || $fila[2]==3){
					//nombre acceso
					$nombreAcceso="Sin datos";
					if($fila[4]>0){
						$patron3="SELECT nombre,apellidos FROM usuarios WHERE id=\"%s\"";
						$sql3=sprintf($patron3,$fila[4]);
						$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 963534456687664222286454509258");
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
				if($fila[3]>0){
					$botones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm' onClick='cargaLocation(\"index.php?s=18&i=".$fila[3]."\");return false;' title='Ir al nodo' title='Ir al acceso.'>
								<span class='svg-icon svg-icon-primary svg-icon-2x'><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Navigation\Right-2.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
									<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
										<polygon points='0 0 24 0 24 24 0 24'/>
										<rect fill='#000000' opacity='0.3' transform='translate(8.500000, 12.000000) rotate(-90.000000) translate(-8.500000, -12.000000) ' x='7.5' y='7.5' width='2' height='9' rx='1'/>
										<path d='M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z' fill='#000000' fill-rule='nonzero' transform='translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997) '/>
									</g>
								</svg><!--end::Svg Icon--></span>
							</a>";
				}
				

				printf("<tr>
							<td></td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td nowrap='nowrap'>%s</td>
						</tr>",$fila1[0],$accion,$nombreAcceso,$fila[5],convierteFechaBarra($fila[6]),$resultadoAccion,$botones);	
			}
			
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                        <th>#</th>
						<th>Puerta</th>
					  	<th>Apertura</th>
					  	<th>Nombre Acceso</th>
						<th>Hora</th>
						<th>Fecha</th>
						<th>Resultado</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//historial pagos safey
function pagosSafeyHistorial($id,$con){
	$consulta="";
    //QUITADO FILTRO 03/07/2024
	/*if(isset($_SESSION["fechaIniHistorialPagos"]) && isset($_SESSION["fechaFinHistorialPagos"]) ){
		$consulta=" AND fechainicio>=\"".$_SESSION["fechaIniHistorialPagos"]."\" AND fechafin<=\"".$_SESSION["fechaFinHistorialPagos"]."\"";
	}*/
	
	//obtener usuario de accesos
	/*$idUsuario=0;
	$patron1="SELECT idusuario FROM safey_accesos WHERE id=\"%s\" AND borrado=\"n\"";
	$sql1=sprintf($patron1,$id);
	$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 72154296908912346350452589785619");
	if(mysqli_num_rows($respuesta1)>0){
		$fila1=mysqli_fetch_array($respuesta1);
		$idUsuario=$fila1[0];
	}
	mysqli_free_result($respuesta1);*/
	
	//recorrer pagos
	$patron="SELECT id,idusuario,tiposervicio,codigopromocional,descuento,total,metodopago,fechainicio,fechafin,idnodo,fechapago,pagado FROM safey_pagos WHERE borrado=\"n\" AND idacceso=\"%s\"%s ORDER BY fechainicio DESC, id DESC";//LIMIT 0,50
	$sql=sprintf($patron,$id,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 969089173732346345258919");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      	<th>#</th>
						<th>Nodo</th>
						<th>Servicio</th>
						<th title="Descuento">Des.</th>
						<th>Importe</th>
						<th>Método De Pago</th>
						<th>Fecha Inicio</th>
						<th>Fecha Fin</th>
						<th>Fecha Pago</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$idUsuario=$fila[1];
			$idNodo=$fila[9];
            $idTipoServicio=$fila[2];
            $pagado=$fila[11];
                
			//consultar metodo de pago
			$codPromo="Sin datos";
			if($fila[3]>0){
				$patron2="SELECT codigo FROM safey_codigospromocionales WHERE borrado=\"n\" AND id=\"%s\"";
				$sql2=sprintf($patron2,$fila[3]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 349762963534564281972280909258");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$codPromo=$fila2[0];
				}
				mysqli_free_result($respuesta2);
			}
			
            /*START boton pagar*/
			$btnPagar="";
            $classInputFechaPago="";
            $readOnlyInputFechaPago="";
            $estiloInputFechaPago="";
            if($pagado=="n"){
                 $btnPagar="<a href='#' class='btn btn-icon btn-light btn-hover-info btn-sm mx-2' onClick='confirmacion(\"warning\",\"Establecer como pagado\",\"¿Estas seguro que quieres configurar como pagado esta línea?\",42,\"".$id."\",\"".$fila[0]."\",\"\");return false;'>
                    <span class='svg-icon svg-icon-md svg-icon-info'>
                        <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Write.svg-->
                        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d='M64 0C46.3 0 32 14.3 32 32V96c0 17.7 14.3 32 32 32h80v32H87c-31.6 0-58.5 23.1-63.3 54.4L1.1 364.1C.4 368.8 0 373.6 0 378.4V448c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V378.4c0-4.8-.4-9.6-1.1-14.4L488.2 214.4C483.5 183.1 456.6 160 425 160H208V128h80c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H64zM96 48H256c8.8 0 16 7.2 16 16s-7.2 16-16 16H96c-8.8 0-16-7.2-16-16s7.2-16 16-16zM64 432c0-8.8 7.2-16 16-16H432c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16zm48-168a24 24 0 1 1 0-48 24 24 0 1 1 0 48zm120-24a24 24 0 1 1 -48 0 24 24 0 1 1 48 0zM160 344a24 24 0 1 1 0-48 24 24 0 1 1 0 48zM328 240a24 24 0 1 1 -48 0 24 24 0 1 1 48 0zM256 344a24 24 0 1 1 0-48 24 24 0 1 1 0 48zM424 240a24 24 0 1 1 -48 0 24 24 0 1 1 48 0zM352 344a24 24 0 1 1 0-48 24 24 0 1 1 0 48z'/></svg>
                        <!--end::Svg Icon-->
                    </span>
                </a>";
                
                
                $estiloInputFechaPago="style='background-color: #ffe7a5 !important;'";
            }else if($pagado=="s"){
                $classInputFechaPago=" inputReadOnly";
                $readOnlyInputFechaPago=" readonly";
                $estiloInputFechaPago="style='background-color: #e0ffe1 !important;'";
            }
            /*END boton pagar*/
            
			$acciones="";
			$acciones="
			<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm mx-2' onClick='editarPago(\"".$id."\",\"".$fila[0]."\");return false;'>
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
            <a href='#' class='btn btn-icon btn-light btn-hover-warning btn-sm mx-2' onClick='enviarMailSuscripcionPagadaAcceso(\"".$id."\",\"".$fila[0]."\");return false;' title='Enviar mail para pagar suscripción'>
				<span class='svg-icon svg-icon-md svg-icon-success'>
					<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Write.svg-->
					<svg xmlns='http://www.w3.org/2000/svg' id='Outline' viewBox='0 0 24 24' width='512' height='512'><path d='M19,1H5A5.006,5.006,0,0,0,0,6V18a5.006,5.006,0,0,0,5,5H19a5.006,5.006,0,0,0,5-5V6A5.006,5.006,0,0,0,19,1ZM5,3H19a3,3,0,0,1,2.78,1.887l-7.658,7.659a3.007,3.007,0,0,1-4.244,0L2.22,4.887A3,3,0,0,1,5,3ZM19,21H5a3,3,0,0,1-3-3V7.5L8.464,13.96a5.007,5.007,0,0,0,7.072,0L22,7.5V18A3,3,0,0,1,19,21Z'/></svg>
					<!--end::Svg Icon-->
				</span>
			</a>
            ".$btnPagar."
			<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm mx-2' onClick='confirmacion(\"warning\",\"Eliminar Pago\",\"¿Estas seguro de que deseas eliminar este pago?\",36,\"".$id."\",\"".$fila[0]."\",\"\");return false;'>
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

			
			//<td><input type='text' class='form-control' id='codpromo%s' value='%s' maxlength='255' placeholder='Código promocional'></td>
			//,$fila[0],$codPromo
			printf("<tr>
						<td></td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td><input type='date' class='form-control' id='finiciopago%s' value='%s'/></td>
						<td><input type='date' class='form-control' id='ffinpago%s' value='%s'/></td>
						<td><input type='date' class='form-control%s' %s id='fRealPago%s' value='%s'%s/></td>
						<td nowrap='nowrap'>%s</td>
					</tr>",cargaNodosSafeyAccesosParaTabla($fila[9],"nodopagos".$fila[0],$id,$con),cargarTipoServicioPagoNodo($idTipoServicio,$fila[0],$idNodo,$con),$fila[4]." €",$fila[5]." €",cargaTiposMetodosPagoParaTabla($fila[6],"metodopago".$fila[0],"",$con),$fila[0],$fila[7],$fila[0],$fila[8],$classInputFechaPago,$estiloInputFechaPago,$fila[0],$fila[10],$readOnlyInputFechaPago,$acciones);	
			}
			
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
						<th>#</th>
						<th>Nodo</th>
						<th>Servicio</th>
						<th title="Descuento">Des.</th>
						<th>Importe</th>
						<th>Método De Pago</th>
						<th>Fecha Inicio</th>
						<th>Fecha Fin</th>
						<th>Fecha Pago</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//carga puertas historial nodos
function cargaFiltroPuertasHistorial($nodo,$con){

	$consulta="";
	if($_SESSION["permisossession"]!=1){//resto usuarios
		$consulta="";
	}else{//administrador
		$consulta="";
	}
	
	$patron="SELECT id,nombre FROM safey_puertas WHERE borrado=\"n\" AND idnodo=\"%s\"%s";
	$sql=sprintf($patron,$nodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 123454450557366");
	printf("<select class='form-control' name=\"puertaHistorialSafey\" id=\"puertaHistorialSafey\">");
	printf("<option value='0'>Selecciona Puerta</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$_SESSION["puertaHistorialPuertasSafey"]){
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

//carga puertas historial nodos
function cargaFiltroPuertasHistorialFallido($nodo,$con){

	$consulta="";
	if($_SESSION["permisossession"]!=1){//resto usuarios
		$consulta="";
	}else{//administrador
		$consulta="";
	}
	
	$patron="SELECT id,nombre FROM safey_puertas WHERE borrado=\"n\" AND idnodo=\"%s\"%s";
	$sql=sprintf($patron,$nodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12345445150557366");
	printf("<select class='form-control' name=\"puertaHistorialFallidoSafey\" id=\"puertaHistorialFallidoSafey\">");
	printf("<option value='0'>Selecciona Puerta</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$_SESSION["puertaHistorialFallidoPuertasSafey"]){
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

// CARGA safey accesos
function cargaAccesosSafeyList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 && $_SESSION["usuarioSafeyList"]!="0"){
		$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioSafeyList"])."\"";
	}else{
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	$patron="SELECT id,nombre,idusuario,nombre,telefono,email,pinactivo,llaveactivo,mandoactivo,mailloginactivo,apellidos FROM safey_accesos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632353454677872475879958");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>Teléfono</th>
					  <th>Email</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//usuario
			$patron1="SELECT nombre FROM usuarios WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$fila[2]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9635345774509258");
			$fila1=mysqli_fetch_array($respuesta1);
			mysqli_free_result($respuesta1);

			//colores info
			if($fila[6]=="on"){//pin
				$colorModoPinAcceso="98D572";//verde
			}else{
				$colorModoPinAcceso="D5727E";//rojo
			}
			if($fila[7]=="on"){//llave
				$colorModoLLaveoAcceso="98D572";//verde
			}else{
				$colorModoLLaveoAcceso="D5727E";//rojo
			}
			if($fila[8]=="on"){//mando
				$colorModoMandoAcceso="98D572";//verde
			}else{
				$colorModoMandoAcceso="D5727E";//rojo
			}
			if($fila[9]=="on"){//mail
				$colorModoMailWebAcceso="98D572";//verde
			}else{
				$colorModoMailWebAcceso="D5727E";//rojo
			}
			$botonesAcciones="<i class='fas fa-hand-point-right' style='font-size: 20px;color:#".$colorModoPinAcceso.";margin-right: 17px;'></i>
			<i class='fas fa-key' style='font-size: 20px;color:#".$colorModoLLaveoAcceso.";margin-right: 17px;'></i>
			<i class='fas fa-door-open' style='font-size: 20px;color:#".$colorModoMandoAcceso.";margin-right: 17px;'></i>
			<i class='fas fa-globe' style='font-size: 20px;color:#".$colorModoMailWebAcceso.";'></i>";
			
			//pulsar tr
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=18&i=%s\");'",$fila[0]);
			
			printf("<tr>
				<td></td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='' style=''>%s</td>
				</tr>",$funcion,$fila[1]." ".$fila[10],$funcion,$fila1[0],$funcion,$fila[4],$funcion,$fila[5],$botonesAcciones);
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>Teléfono</th>
					  <th>Email</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}

//filtro clientes Safey list
function cargaUsuariosSafeyAccesosFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 399778676741236457056455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosSafeyAccesos(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioSafeyList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//conceder permisos nodos puertas accesos
function permisosNodosPuertasAccesos($idAcceso,$con){
	
	/*
	if($_SESSION["permisossession"]==1){
		$consulta="";
	}else{
		$consulta=" AND safey_nodos.idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	*/
	
	$consulta=" AND safey_nodos.idusuario=\"0\"";
	$patron3="SELECT idusuario FROM safey_accesos WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
	$sql3=sprintf($patron3,$idAcceso);
	$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 5669073356356890097");
	if(mysqli_num_rows($respuesta3)>0){
		$fila3=mysqli_fetch_array($respuesta3);
		
		$consulta=" AND safey_nodos.idusuario=\"".$fila3[0]."\"";
	}
	mysqli_free_result($respuesta3);
	
	//recorrer nodos y puertas, de ese usuario
	$patron="SELECT safey_nodos.id,safey_nodos.nombre,safey_puertas.id,safey_puertas.nombre,safey_nodos.l,safey_nodos.m,safey_nodos.x,safey_nodos.j,safey_nodos.v,safey_nodos.s,safey_nodos.d,safey_nodos.horade,safey_nodos.horahasta FROM safey_nodos,safey_puertas WHERE safey_nodos.borrado=\"n\" AND safey_nodos.guardado=\"s\" AND safey_nodos.id=safey_puertas.idnodo AND safey_puertas.borrado=\"n\"%s ORDER BY safey_nodos.id ASC,safey_puertas.nombre DESC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963234633344545765899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Puerta</th>
					  <th>Permisos</th>
					  <th>L</th>
					  <th>M</th>
					  <th>X</th>
					  <th>J</th>
					  <th>V</th>
					  <th>S</th>
					  <th>D</th>
					  <th>Hora de</th>
					  <th>Hora hasta</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		$idNodoAux=0;
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($idNodoAux!=$fila[0]){
				printf("<tr>
                        <td></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'>%s</td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
					</tr>",$fila[1]);
				$idNodoAux=$fila[0];
			}
			
			//ver cada registro
			$idTablaAccesosNodos=0;
			$permisos="n";
			$horadAccesosPuertas="00:00:00";
			$horahAccesosPuertas="23:59:00";
			$l="n";
			$m="n";
			$x="n";
			$j="n";
			$v="n";
			$s="n";
			$d="n";
			
			$patron1="SELECT id,nodo,puerta,permisos,horade,horahasta,l,m,x,j,v,s,d FROM safey_accesosnodos WHERE borrado=\"n\" AND nodo=\"%s\" AND puerta=\"%s\" AND idacceso=\"%s\"";
			$sql1=sprintf($patron1,$fila[0],$fila[2],$idAcceso);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 56690756356890097");
			if(mysqli_num_rows($respuesta1)>0){
				$fila1=mysqli_fetch_array($respuesta1);
				$idTablaAccesosNodos=$fila1[0];
				$permisos=$fila1[3];
				$horadAccesosPuertas=$fila1[4];
				$horahAccesosPuertas=$fila1[5];
				$l=$fila1[6];
				$m=$fila1[7];
				$x=$fila1[8];
				$j=$fila1[9];
				$v=$fila1[10];
				$s=$fila1[11];
				$d=$fila1[12];
			}else{
                //del select por defecto
				
                $permisos="s";
                $l=$fila[4];
                $m=$fila[5];
                $x=$fila[6];
                $j=$fila[7];
                $v=$fila[8];
                $s=$fila[9];
                $d=$fila[10];
                $horadAccesosPuertas=$fila[11];
                $horahAccesosPuertas=$fila[12];
                
                //insert por defecto
                $patron2="INSERT INTO safey_accesosnodos SET permisos=\"s\",horade=\"%s\",horahasta=\"%s\",nodo=\"%s\",puerta=\"%s\",idacceso=\"%s\",borrado=\"n\",fechaalta=\"%s\",l=\"%s\",m=\"%s\",x=\"%s\",j=\"%s\",v=\"%s\",s=\"%s\",d=\"%s\"";
                $sql2=sprintf($patron2,$horadAccesosPuertas,$horahAccesosPuertas,$fila[0],$fila[2],$idAcceso,date("Y-m-d"),$l,$m,$x,$j,$v,$s,$d);
                $respuesta2=mysqli_query($con,$sql2) or die ("Error al borrar 123450464165475681534789661345565746645574546");
                
                $idTablaAccesosNodos=mysqli_insert_id($con);
            }
			mysqli_free_result($respuesta1);
			
			//boton check
			$iconoClassCheck="";
			if($permisos=="s"){
				$iconoClassCheck=" fa-check";
                $colorIcon="color: green;";
			}else{
                $iconoClassCheck=" fa-times";
                $colorIcon="color: red;";
            }
			
			$botonCheck="<div id='nodoPuertaAc_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."' onClick='activarDesactivarCheckGeneral(this,\"".$idTablaAccesosNodos."\",\"".$fila[0]."\",\"".$fila[2]."\",\"".$idAcceso."\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
			<input type='hidden' id='nodoPuertaAc_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."_hidden' value='".$permisos."'>
			<i class='fas".$iconoClassCheck."' style='font-size:25px;".$colorIcon."' title='Activado'></i>
			</div>";
			
			
			/***start check dias***/
			$iconoClassCheckL="";
			if($l=="s"){
				$iconoClassCheckL=" fa-check";
                $colorIconL="color: green;";
			}else{
                $iconoClassCheckL=" fa-times";
                $colorIconL="color: red;";
            }
            
			$botonCheckLunes="<div id='nodoPuertaAcL_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."' onClick='activarDesactivarCheck(this,\"".$idTablaAccesosNodos."\",\"".$fila[0]."\",\"".$fila[2]."\",\"".$idAcceso."\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
			<input type='hidden' id='nodoPuertaAcL_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."_hidden' value='".$l."'>
			<i class='fas".$iconoClassCheckL."' style='font-size:25px;".$colorIconL."' title='Activado'></i>
			</div>";
			
			$iconoClassCheckM="";
			if($m=="s"){
				$iconoClassCheckM=" fa-check";
                $colorIconM="color: green;";
			}else{
                $iconoClassCheckM=" fa-times";
                $colorIconM="color: red;";
            }
			$botonCheckMartes="<div id='nodoPuertaAcM_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."' onClick='activarDesactivarCheck(this,\"".$idTablaAccesosNodos."\",\"".$fila[0]."\",\"".$fila[2]."\",\"".$idAcceso."\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
			<input type='hidden' id='nodoPuertaAcM_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."_hidden' value='".$m."'>
			<i class='fas".$iconoClassCheckM."' style='font-size:25px;".$colorIconM."' title='Activado'></i>
			</div>";
			
			$iconoClassCheckX="";
			if($x=="s"){
				$iconoClassCheckX=" fa-check";
                $colorIconX="color: green;";
			}else{
                $iconoClassCheckX=" fa-times";
                $colorIconX="color: red;";
            }
			$botonCheckMiercoles="<div id='nodoPuertaAcX_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."' onClick='activarDesactivarCheck(this,\"".$idTablaAccesosNodos."\",\"".$fila[0]."\",\"".$fila[2]."\",\"".$idAcceso."\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
			<input type='hidden' id='nodoPuertaAcX_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."_hidden' value='".$x."'>
			<i class='fas".$iconoClassCheckX."' style='font-size:25px;".$colorIconX."' title='Activado'></i>
			</div>";
			
			$iconoClassCheckJ="";
			if($j=="s"){
				$iconoClassCheckJ=" fa-check";
                $colorIconJ="color: green";
			}else{
                $iconoClassCheckJ=" fa-times";
                $colorIconJ="color: red";
            }
			$botonCheckJueves="<div id='nodoPuertaAcJ_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."' onClick='activarDesactivarCheck(this,\"".$idTablaAccesosNodos."\",\"".$fila[0]."\",\"".$fila[2]."\",\"".$idAcceso."\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
			<input type='hidden' id='nodoPuertaAcJ_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."_hidden' value='".$j."'>
			<i class='fas".$iconoClassCheckJ."' style='font-size:25px;".$colorIconJ."' title='Activado'></i>
			</div>";
			
			$iconoClassCheckV="";
			if($v=="s"){
				$iconoClassCheckV=" fa-check";
                $colorIconV="color: green;";
			}else{
                $iconoClassCheckV=" fa-times";
                $colorIconV="color: red;";
            }
			$botonCheckViernes="<div id='nodoPuertaAcV_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."' onClick='activarDesactivarCheck(this,\"".$idTablaAccesosNodos."\",\"".$fila[0]."\",\"".$fila[2]."\",\"".$idAcceso."\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
			<input type='hidden' id='nodoPuertaAcV_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."_hidden' value='".$v."'>
			<i class='fas".$iconoClassCheckV."' style='font-size:25px;".$colorIconV."' title='Activado'></i>
			</div>";
			
			$iconoClassCheckS="";
			if($s=="s"){
				$iconoClassCheckS=" fa-check";
                $colorIconS="color: green;";
			}else{
                $iconoClassCheckS=" fa-times";
                $colorIconS="color: red;";
            }
			$botonCheckSabado="<div id='nodoPuertaAcS_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."' onClick='activarDesactivarCheck(this,\"".$idTablaAccesosNodos."\",\"".$fila[0]."\",\"".$fila[2]."\",\"".$idAcceso."\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
			<input type='hidden' id='nodoPuertaAcS_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."_hidden' value='".$s."'>
			<i class='fas".$iconoClassCheckS."' style='font-size:25px;".$colorIconS."' title='Activado'></i>
			</div>";
			
			$iconoClassCheckD="";
			if($d=="s"){
				$iconoClassCheckD=" fa-check";
                $colorIconD="color: green;";
			}else{
                $iconoClassCheckD=" fa-times";
                $colorIconD="color: red;";
            }
			$botonCheckDomingo="<div id='nodoPuertaAcD_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."' onClick='activarDesactivarCheck(this,\"".$idTablaAccesosNodos."\",\"".$fila[0]."\",\"".$fila[2]."\",\"".$idAcceso."\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
			<input type='hidden' id='nodoPuertaAcD_".$fila[0]."_".$fila[2]."_".$idTablaAccesosNodos."_hidden' value='".$d."'>
			<i class='fas".$iconoClassCheckD."' style='font-size:25px;".$colorIconD."' title='Activado'></i>
			</div>";
			/***end check dias***/
			
			
			//acciones
			$acciones="";
			if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2){
				$acciones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm ' 	 		onClick='editarLineaAccesosNodosPuertas(\"".$idTablaAccesosNodos."\",\"".$fila[0]."\",\"".$fila[2]."\",\"".$idAcceso."\");return false;' title='Guardar'>
                
                	<span class='svg-icon svg-icon-md svg-icon-success'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Save.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<polygon points='0 0 24 0 24 24 0 24'/>
									<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
									<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
								</g>
							</svg><!--end::Svg Icon-->
							</span>
					</a>";
			}
			
			printf("<tr>
                        <td></td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td><input type='time' class='form-control' id='horadAccesosPuertasAc_%s_%s_%s' value='%s'></td>
						<td><input type='time' class='form-control' id='horahAccesosPuertasAc_%s_%s_%s' value='%s'></td>
						<td nowqrap='nowrap'>%s</td>
					</tr>",$fila[3],$botonCheck,$botonCheckLunes,$botonCheckMartes,$botonCheckMiercoles,$botonCheckJueves,$botonCheckViernes,$botonCheckSabado,$botonCheckDomingo,$fila[0],$fila[2],$idTablaAccesosNodos,$horadAccesosPuertas,$fila[0],$fila[2],$idTablaAccesosNodos,$horahAccesosPuertas,$acciones);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
						<th>#</th>
						<th>Puerta</th>
						<th>Permisos</th>
						<th>L</th>
						<th>M</th>
						<th>X</th>
						<th>J</th>
						<th>V</th>
						<th>S</th>
						<th>D</th>
						<th>Hora de</th>
						<th>Hora hasta</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//configuracion metodos de pagos 
function configuracionMetodosPagosPuertasAccesos($idNodo,$idUsuario,$con){
	$patron="SELECT id,metodopago FROM safey_metodospago WHERE idnodo=\"%s\" AND idusuario=\"%s\" AND borrado=\"n\" ORDER BY id ASC,metodopago DESC";
	$sql=sprintf($patron,$idNodo,$idUsuario);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 912907863234633343454576544549190");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Métodos de pago</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$metodoPago="Desconocido";
			$patron1="SELECT tipo FROM metodospago WHERE id=\"%s\" AND borrado=\"n\"";
			$sql1=sprintf($patron1,$fila[1]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9312907863921634633343454576544549190");
			if(mysqli_num_rows($respuesta1)>0){
				$fila1=mysqli_fetch_array($respuesta1);
				$metodoPago=$fila1[0];
			}
			mysqli_free_result($respuesta1);
			
			$acciones="";
			
			$acciones="
			<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Método De Pago\",\"¿Estas 					seguro de que deseas eliminar este método de pago?\",34,\"".$fila[0]."\",\"".$idNodo."\",\"\");return false;'>
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
						<td><input type='text' class='form-control inputReadOnly' id='metodopago%s' value='%s' placeholder='Método de pago' readonly/></td>
						<td>%s</td>
					</tr>",$fila[0],$metodoPago,$acciones);
		}
		
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
						<th>#</th>
						<th>Métodos de pago</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//configuracion de codigos promocionales
function configuracionCodigosPromoPuertasAccesos($idNodo,$idUsuario,$con){
	$patron="SELECT id,codigo,tipo,cantidad FROM safey_codigospromocionales WHERE idnodo=\"%s\" AND idusuario=\"%s\" AND borrado=\"n\" ORDER BY id ASC,codigo DESC";
	$sql=sprintf($patron,$idNodo,$idUsuario);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 249891290786323463334345457601491902");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Códigos promocionales</th>
					  <th>Tipo</th>
					  <th>Cantidad</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$cantidad=floatval($fila[3]);
			
			//selector de  tipo
			$selector="";
			$selector="<select class='form-control' id='tipocodpromo$fila[0]'>";
			switch ($fila[2]){
				case "e":
					$selector.="<option value='e' selected>€</option>
								<option value='p'>%</option>";
				break;
				case "p":
					$selector.="<option value='e'>€</option>
								<option value='p' selected>%</option>";
				break;
			}
			
			$selector.="</select>";
			
			$acciones="";
			$acciones="
			<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm mx-3' onClick='editarCodigoPromocional(\"".$fila[0]."\",\"".$idNodo."\");return false;'>
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
			<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Código Promocional\",\"¿Estas 					seguro de que deseas eliminar este código promocional?\",35,\"".$fila[0]."\",\"".$idNodo."\",\"\");return false;'>
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
						<td><input type='text' class='form-control' id='codigopromocional%s' value='%s' placeholder='Método de pago'/></td>
						<td>%s</td>
						<td><input type='text' class='form-control' id='cantidadcodpromo%s' value='%s' placeholder='Cantidad' maxlength='3'/></td>
						<td>%s</td>
					</tr>",$fila[0],$fila[1],$selector,$fila[0],$cantidad,$acciones);
		}
		
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
						<th>#</th>
						<th>Códigos promocionales</th>
						<th>Tipo</th>
					  	<th>Cantidad</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//carga usuarios
function cargaUsuariosSafeyCredenciales($seleccionado,$idlin,$name,$con){
	$dev="";
	
	$consulta=" AND (permisos=\"1\" OR permisos=\"2\")";
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre ASC, apellidos ASC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 123454456776266");
	$dev.=sprintf("<select class='form-control' name=\"%s%s\" id=\"%s%s\" >",$name,$idlin,$name,$idlin);
	$dev.=sprintf("<option value='0'>Cliente (Sin Asignar):</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionado){
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

//crear y configurar credenciales PIN
function credencialesPinSafeyConfiguracion($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		if($_SESSION["usuarioSafeyList"]!="0"){
			$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioSafeyList"])."\"";
		}
	}
	
	$patron="SELECT id,pin,pinserie,pinserial,idusuario,idacceso FROM safey_credenciales_pin WHERE borrado=\"n\"%s ORDER BY idusuario ASC, id ASC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96324447007656345346555899");
	if(mysqli_num_rows($respuesta)>0){
		printf("<thead>
					<tr>
						<th>#</th>
						<th>ID Pin</th>
						<th>Pin</th>
						<th>Pin Serie</th>
						<th>Pin Serial</th>
						<th>Cliente</th>
						<th>Acceso</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody>");
		$idUsuarioAux=-1;
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$botones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm' onClick='editarCredencialPinSafey(\"".$fila[0]."\");return false;' title='Guardar Cambios'>
							<span class='svg-icon svg-icon-md svg-icon-success'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Save.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<polygon points='0 0 24 0 24 24 0 24'/>
									<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
									<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
								</g>
							</svg><!--end::Svg Icon-->
							</span>
					</a>
					<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Registro Definitivamente\",\"¿Estas seguro de que deseas eliminar este registro?\",16,\"".$fila[0]."\",\"\");return false;'>
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
				
				//saber total pines asociados al cliente
				$pinesTotalesCliente=0;
				$patron22="SELECT COUNT(id) FROM safey_credenciales_pin WHERE idusuario=\"%s\" AND borrado=\"n\"";
				$sql22=sprintf($patron22,$fila[4]);
				$respuesta22=mysqli_query($con,$sql22) or die ("Error al buscar 56690754225646635622890097");
				if(mysqli_num_rows($respuesta22)>0){
					$fila22=mysqli_fetch_array($respuesta22);
					$pinesTotalesCliente=$fila22[0];
				}
				mysqli_free_result($respuesta22);
				
				//saber total pines ya asociados a accesos
				$pinesEnUsoCliente=0;
				$patron23="SELECT COUNT(id) FROM safey_credenciales_pin WHERE idusuario=\"%s\" AND borrado=\"n\" AND idacceso>0";
				$sql23=sprintf($patron23,$fila[4]);
				$respuesta23=mysqli_query($con,$sql23) or die ("Error al buscar 566907542322564663562223890097");
				if(mysqli_num_rows($respuesta23)>0){
					$fila23=mysqli_fetch_array($respuesta23);
					$pinesEnUsoCliente=$fila23[0];
				}
				mysqli_free_result($respuesta23);
				
				$textoPinesEnUsoTotales=" <br> Pines: ".$pinesEnUsoCliente." / ".$pinesTotalesCliente;
				
				printf("<tr>
                        <td></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;' >%s</td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
					</tr>",$cliente.$textoPinesEnUsoTotales);
				$idUsuarioAux=$fila[4];
			}
			//nombre acceso
			$nombreAcceso="";
			if($fila[5]>0){
				$patron1="SELECT nombre,apellidos FROM safey_accesos WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
				$sql1=sprintf($patron1,$fila[5]);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9632444711007656345346555899");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					$nombreAcceso=$fila1[0]." ".$fila1[1];
					
				}
				mysqli_free_result($respuesta1);
			}
			
			printf("<tr>
                        <td></td>
						<td><input type='text' class='form-control inputReadOnly' id='idPinCredencialSafey%s' value='%s' readonly/></td>
						<td><input type='text' class='form-control' id='pinCredencialSafey%s' value='%s' placeholder='Pin'/></td>
						<td><input type='text' class='form-control' id='pinSerieCredencialSafey%s' value='%s' placeholder='Pin Serie'/></td>
						<td><input type='text' class='form-control' id='pinSerialCredencialSafey%s' value='%s' placeholder='Pin Serial'/></td>
						<td>%s</td>
						<td><input type='text' class='form-control inputReadOnly' id='accesoRelCredencialSafey%s' value='%s' readonly/></td>
						<td nowrap='nowrap'>%s</td></tr>",$fila[0],$fila[0],$fila[0],$fila[1],$fila[0],$fila[2],$fila[0],$fila[3],cargaUsuariosSafeyCredenciales($fila[4],$fila[0],"pinClienteCredencialSafey",$con),$fila[0],$nombreAcceso,$botones);
			
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
						<th>Cliente</th>
						<th>Acceso</th>
						<th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>');
	}
}

//crear y configurar credenciales LLAVE
function credencialesLlaveSafeyConfiguracion($con){
	$patron="SELECT id,llaveserie,llavepinserial,idusuario,tipo,frecuencia,descripcion,color FROM safey_credenciales_llaves WHERE borrado=\"n\" ORDER BY idusuario ASC, id ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632444700765624753345346555899");
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
						<th>Cliente</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody>");
		$idUsuarioAux=-1;
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
            
            $tipo="Sin datos";
            if($fila[4]==1){
                $tipo="Tarjeta";
            }else if($fila[4]==2){
                $tipo="LLaveros";
            }else if($fila[4]==3){
                $tipo="Taco Metacrilato";
            }else if($fila[4]==4){
                $tipo="Pulsera";
            }else if($fila[4]==5){
                $tipo="Etiqueta";
            }else if($fila[4]==6){
                $tipo="Pegatina";
            }
            
            $frecuencia="Sin datos";
            if($fila[5]=="13.56"){
                $frecuencia="13.56 Khz";
            }else if($fila[5]=="156"){
                $frecuencia="125 Khz";
            }
	
			$botones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm ' 	 		onClick='editarCredencialLlaveSafey(\"".$fila[0]."\");return false;' title='Guardar Cambios'>
							<span class='svg-icon svg-icon-md svg-icon-success'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Save.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<polygon points='0 0 24 0 24 24 0 24'/>
									<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
									<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
								</g>
							</svg><!--end::Svg Icon-->
							</span>
					</a>
					<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas 					seguro de que deseas eliminar este registro?\",17,\"".$fila[0]."\",\"\");return false;'>
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
			if($idUsuarioAux!=$fila[3]){
				$cliente="Sin asignar";
				$patron1="SELECT nombre FROM usuarios WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$fila[3]);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 56690754753545456466356890097");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					$cliente=$fila1[0];
				}
				mysqli_free_result($respuesta1);
				
				printf("<tr>
                        <td></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'>%s</td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
                        <td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
                        <td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
					</tr>",$cliente);
				$idUsuarioAux=$fila[3];
			}
			
			//color div celda
			$colorCelda="#ffffff";
			if($fila[7]>0){
				$patron2="SELECT color,valor FROM coloresgenericos WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$fila[7]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 56690754753545456466356893456456540097");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$colorCelda=$fila2[1];
				}
				mysqli_free_result($respuesta2);
			}
			
			$divColor="<div id='color".$fila[0]."' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color:".$colorCelda.";cursor: not-allowed;' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><input type='hidden' id='luzModal16_4_hidden' value='1'>";
			
			printf("<tr>
                        <td></td>
						<td><input type='text' class='form-control inputReadOnly' id='llaveiDCredencialSafey%s' value='%s' placeholder='' readonly/></td>
						<td><input type='text' class='form-control' id='llaveDescripcionCredencialSafey%s' value='%s' placeholder='' /></td>
						<td><input type='text' class='form-control inputReadOnly' id='llaveSerieCredencialSafey%s' value='%s' placeholder='Llave Serie' readonly/></td>
						<td><input type='text' class='form-control inputReadOnly' id='llaveSerialCredencialSafey%s' value='%s' placeholder='Llave Serial' readonly/></td>
						<td><input type='text' class='form-control inputReadOnly' id='llaveTipoCredencialSafey%s' value='%s' placeholder='Tipo' readonly/>
						<td>%s</td>
						<td><input type='text' class='form-control inputReadOnly' id='llaveFrecuenciaCredencialSafey%s' value='%s' placeholder='Tipo' readonly/></td>
						<td>%s</td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$fila[0],$fila[0],$fila[0],$fila[6],$fila[0],$fila[1],$fila[0],$fila[2],$fila[0],$tipo,$divColor,$fila[0],$frecuencia,cargaUsuariosSafeyCredenciales($fila[3],$fila[0],"llaveClienteCredencialSafey",$con),$botones);
			
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
						<th>Cliente</th>
						<th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>');
	}
}

//crear y configurar credenciales MANDO
function credencialesMandoSafeyConfiguracion($con){
	$patron="SELECT id,mandoserie,mandoserial,idusuario FROM safey_credenciales_mandos WHERE borrado=\"n\" ORDER BY idusuario ASC, id ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632444700765624753345346555899");
	if(mysqli_num_rows($respuesta)>0){
		printf("<thead>
					<tr>
						<th>#</th>
						<th>ID Mando</th>
						<th>Mando Serie</th>
						<th>Mando Serial</th>
						<th>---</th>
						<th>Cliente</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody>");
		$idUsuarioAux=-1;
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
	
			$botones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm ' 	 		onClick='editarCredencialMandoSafey(\"".$fila[0]."\");return false;' title='Guardar Cambios'>
							<span class='svg-icon svg-icon-md svg-icon-success'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Save.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<polygon points='0 0 24 0 24 24 0 24'/>
									<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
									<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
								</g>
							</svg><!--end::Svg Icon-->
							</span>
					</a>
					<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas 					seguro de que deseas eliminar este registro?\",18,\"".$fila[0]."\",\"\");return false;'>
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
			if($idUsuarioAux!=$fila[3]){
				$cliente="Sin asignar";
				$patron1="SELECT nombre FROM usuarios WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$fila[3]);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 56690754753545456466356890097");

				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					$cliente=$fila1[0];
				}
				mysqli_free_result($respuesta1);
				
				printf("<tr>
                        <td></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'>%s</td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
						<td style='text-align: center;font-weight: bold;background-color: #f5f5f5;'></td>
					</tr>",$cliente);
				$idUsuarioAux=$fila[3];
			}
			
			printf("<tr>
                        <td></td>
						<td><input type='text' class='form-control inputReadOnly' id='idLlaveCredencialSafey%s' value='%s' readonly/></td>
						<td><input type='text' class='form-control' id='mandoSerieCredencialSafey%s' value='%s' placeholder='Mando Serie'/></td>
						<td><input type='text' class='form-control' id='mandoSerialCredencialSafey%s' value='%s' placeholder='Mando Serial'/></td>
						<td><input type='text' class='form-control' id='--%s' value='%s' placeholder='--'/></td>
						<td>%s</td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$fila[0],$fila[0],$fila[0],$fila[1],$fila[0],$fila[2],$fila[0],"--",cargaUsuariosSafeyCredenciales($fila[3],$fila[0],"mandoClienteCredencialSafey",$con),$botones);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
                    <tr>
                        <th>#</th>
						<th>ID Mando</th>
						<th>Mando Serie</th>
						<th>Mando Serial</th>
						<th>---</th>
						<th>Cliente</th>
						<th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>');
	}
}

//carga pin genericos safey
function cargaPinClientesSafey($seleccionada,$nombre,$idacceso,$con){

	$consulta="";
	$textoDesplegable="Selecciona Pin";
	if($_SESSION["permisossession"]!=1){//resto usuarios
		$consulta=" AND idusuario=".$_SESSION["idusersession"]." AND idusuario>0";//CREO QUE FALTA EL calculaIdEmpresa....
	}else{//administrador, solo ver los asignados a ese cliente
        
        $idUsuarioCalculado=0;
		$guardadoAcceso="n";
        $patron99="SELECT idusuario,guardado FROM safey_accesos WHERE borrado=\"n\" AND id=\"%s\"";
        $sql99=sprintf($patron99,$idacceso);
        $respuesta99=mysqli_query($con,$sql99) or die ("Error al buscar 12345454535767542466");
        if(mysqli_num_rows($respuesta99)>0){
            $fila99=mysqli_fetch_array($respuesta99);
            $idUsuarioCalculado=$fila99[0];
			$guardadoAcceso=$fila99[1];
        }
        mysqli_free_result($respuesta99);
        
        //$consulta=" AND idusuario>0";
		if($guardadoAcceso=="s"){
			$consulta=" AND idusuario=".$idUsuarioCalculado;
		}else{
			$consulta=" AND idusuario=-98";
			$textoDesplegable="Debes guardar la ficha";
		}
	}
	
	$excluir=" AND (idacceso=0 OR idacceso=\"".$idacceso."\")";
	
	$patron="SELECT id,pin,pinserie,pinserial,idacceso,idusuario FROM safey_credenciales_pin WHERE borrado=\"n\"%s%s";
	$sql=sprintf($patron,$consulta,$excluir);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12345357542466");
	printf("<select class='form-control' name=\"%s\" id=\"%s\" >",$nombre,$nombre);
	printf("<option value='0'>%s</option>",$textoDesplegable);
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionada){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>Pin %s</option>",$fila[0],$select,$fila[1]);
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//carga llave genericos safey
function cargaLlavesClientesSafey($seleccionada,$nombre,$idacceso,$con){

	$consulta="";
	$textoDesplegable="Selecciona Llave";
	if($_SESSION["permisossession"]!=1){//resto usuarios
		$consulta=" AND idusuario=".$_SESSION["idusersession"]." AND idusuario>0";//CREO QUE FALTA EL calculaIdEmpresa....
	}else{//administrador
		
		$idUsuarioCalculado=0;
		$guardadoAcceso="n";
        $patron99="SELECT idusuario,guardado FROM safey_accesos WHERE borrado=\"n\" AND id=\"%s\"";
        $sql99=sprintf($patron99,$idacceso);
        $respuesta99=mysqli_query($con,$sql99) or die ("Error al buscar 12345454535457990767542466");
        if(mysqli_num_rows($respuesta99)>0){
            $fila99=mysqli_fetch_array($respuesta99);
            $idUsuarioCalculado=$fila99[0];
			$guardadoAcceso=$fila99[1];
        }
        mysqli_free_result($respuesta99);
        
        //$consulta=" AND idusuario>0";
		if($guardadoAcceso=="s"){
			$consulta=" AND idusuario=".$idUsuarioCalculado;
		}else{
			$consulta=" AND idusuario=-98";
			$textoDesplegable="Debes guardar la ficha";
		}
	}
	
	$excluir=" AND (idacceso=0 OR idacceso=\"".$idacceso."\")";
	
	$patron="SELECT id,llaveserie,llavepinserial,idacceso,idusuario,descripcion FROM safey_credenciales_llaves WHERE borrado=\"n\"%s%s";
	$sql=sprintf($patron,$consulta,$excluir);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12345357544542466");
	printf("<select class='form-control' name=\"%s\" id=\"%s\" >",$nombre,$nombre);
	printf("<option value='0'>%s</option>",$textoDesplegable);
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionada){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>Llave %s</option>",$fila[0],$select,$fila[5]." (".$fila[0].")");
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//carga mandos genericos safey
function cargaMandosClientesSafey($seleccionada,$nombre,$idacceso,$con){

	$consulta="";
	if($_SESSION["permisossession"]!=1){//resto usuarios
		$consulta=" AND idusuario=".$_SESSION["idusersession"]." AND idusuario>0";//CREO QUE FALTA EL calculaIdEmpresa....
	}else{//administrador
		$consulta=" AND idusuario>0";
	}
	
	$excluir=" AND (idacceso=0 OR idacceso=\"".$idacceso."\")";
	
	$patron="SELECT id,mandoserie,mandoserial,idacceso,idusuario FROM safey_credenciales_mandos WHERE borrado=\"n\"%s%s";
	$sql=sprintf($patron,$consulta,$excluir);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1234535723544664542466");
	printf("<select class='form-control' name=\"%s\" id=\"%s\" >",$nombre,$nombre);
	printf("<option value='0'>Selecciona Mando</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionada){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>M %s</option>",$fila[0],$select,$fila[0]);
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//vincular nodos safey
function nodosVinculadosSafey($idNodo,$con){
	$consulta="";
	
	$patron="SELECT safey_nodos_vinculados.id,safey_nodos_vinculados.idnodouno,safey_nodos_vinculados.idnododos FROM safey_nodos_vinculados WHERE safey_nodos_vinculados.borrado=\"n\" AND (idnodouno=\"%s\" OR idnododos=\"%s\")%s";
	$sql=sprintf($patron,$idNodo,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963345788623463455899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Nombre Nodo</th>
					  <th>Puertas Nodo</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//saber el asociado a este grupo
			if($idNodo==$fila[1]){
				$nodoAsociado=$fila[2];
			}else if($idNodo==$fila[2]){
				$nodoAsociado=$fila[1];
			}
			
			//nodo
			$nombreNodo="Sin datos";
			$patron1="SELECT nombre FROM safey_nodos WHERE id=\"%s\" ";
			$sql1=sprintf($patron1,$nodoAsociado);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9635345648645455486568609258");
			if(mysqli_num_rows($respuesta1)>0){
				$fila1=mysqli_fetch_array($respuesta1);
				$nombreNodo=$fila1[0];
			}
			mysqli_free_result($respuesta1);
			
			//puertas nodo
			$textoPuertasNodo="Sin datos";
			$patron2="SELECT nombre FROM safey_puertas WHERE idnodo=\"%s\" AND borrado=\"n\"";
			$sql2=sprintf($patron2,$nodoAsociado);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 9633457676788623463455899");
			if(mysqli_num_rows($respuesta2)>0){
				$textoPuertasNodo="";
				for($j=0;$j<mysqli_num_rows($respuesta2);$j++){
					$fila2=mysqli_fetch_array($respuesta2);
					$textoPuertasNodo.=$fila2[0];
					if($j<mysqli_num_rows($respuesta2)-1){
						$textoPuertasNodo.=", ";
					}
				}
			}
			mysqli_free_result($respuesta2);
			
			$botonBorrar="";
			if($_SESSION["permisossession"]==1){
				$botonBorrar="<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Vinculación\",\"¿Estas seguro de que deseas eliminar esta vinculación?\",20,\"".$fila[0]."\",\"".$idNodo."\");return false;'>
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
						<td nowrap='nowrap'>%s</td>
					</tr>",$nombreNodo,$textoPuertasNodo,$botonBorrar);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                        <th>#</th>
						<th>Nombre Nodo</th>
						<th>Puertas Nodo</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//carga nodos generico
function cargaNodosSafeySelect($idUsuario,$idNodo,$nombre,$con){

	$consulta=" AND safey_nodos.idusuario=".$idUsuario;

	$excluir=obtenerConsultaPlacasAsociadas($idNodo,2,$con)." AND safey_nodos.id<>".$idNodo;
	
	$class="";
	/*if($faltacampo && $seleccionada=="0"){
		$class=" is-invalid";
	}*/
	
	$patron="SELECT safey_nodos.id,safey_nodos.nombre,usuarios.nombre FROM safey_nodos,usuarios WHERE safey_nodos.borrado=\"n\" AND safey_nodos.guardado=\"s\" AND safey_nodos.idusuario=usuarios.id%s";
	$sql=sprintf($patron,$consulta.$excluir);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 123343455653454565458466");
	printf("<select class='form-control%s' name=\"%s\" id=\"%s\" >",$class,$nombre,$nombre);
	printf("<option value='0'>Selecciona Nodo</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionada){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]." (".$fila[2].")");
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//carga mandos genericos safey
function cargaUsuariosClientesSafey($seleccionada,$nombre,$idacceso,$idUsuarioEmpresa,$con){

	if($idUsuarioEmpresa==0 || $idUsuarioEmpresa==""){
		$idUsuarioEmpresa="-1";
	}/*else{
		//nada
	}*/
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\" AND idempresa=\"%s\"";
	$sql=sprintf($patron,$idUsuarioEmpresa);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1234535635723544664542466");
	printf("<select class='form-control' name=\"%s\" id=\"%s\" >",$nombre,$nombre);
	printf("<option value='0'>Selecciona Usuario</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionada){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]." ".$fila[2]);
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//poner online offline, segun ultima conexion
function ultimaConexionOnlinOfflineParques($con){
	$patron="SELECT id,fechaultimaconsulta,horaultimaconsulta FROM parques_nodos WHERE borrado=\"n\" AND guardado=\"s\"";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963234356363456634542355899");
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
			$tiempoLimite=3*60;

			$estado="";
			if($restarHoras>=$tiempoLimite || $fila[1]<date("Y-m-d")){//off
				$estado="off";
			}else{//on
				$estado="on";
			}

			$patron1="UPDATE parques_nodos SET conexion=\"%s\" WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$estado,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 12345634121234431789");
		}
		
	}
	mysqli_free_result($respuesta);
}


//historial fallidos puertas safey
function puertasSafeyHistorialFallidos($idNodo,$con){
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialFallidoPuertasSafey"]) || isset($_SESSION["fechaFinHistorialFallidoPuertasSafey"]) ){
		$consulta=" AND safey_historial_fallidos.fechaalta>=\"".$_SESSION["fechaIniHistorialFallidoPuertasSafey"]."\" AND safey_historial_fallidos.fechaalta<=\"".$_SESSION["fechaFinHistorialFallidoPuertasSafey"]."\"";
	}
	
	$patron="SELECT safey_historial_fallidos.id,safey_historial_fallidos.tipo,safey_historial_fallidos.horaalta,safey_historial_fallidos.fechaalta,safey_nodos.id,safey_historial_fallidos.idaccesocliente,safey_historial_fallidos.serial,safey_historial_fallidos.tipoerror FROM safey_historial_fallidos,safey_nodos WHERE safey_historial_fallidos.idnodo=\"%s\" AND safey_historial_fallidos.idnodo=safey_nodos.id AND safey_nodos.guardado=\"s\" AND safey_nodos.borrado=\"n\"%s ORDER BY safey_historial_fallidos.fechaalta DESC, safey_historial_fallidos.horaalta DESC, safey_historial_fallidos.id DESC";
	$sql=sprintf($patron,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632344545463455899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Puerta</th>
					  <th>Apertura</th>
					  <th>Nombre Acceso</th>
					  <th>Hora</th>
					  <th>Fecha</th>
					  <th>Resultado</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//accion//Tipo
			$accion="";
			if($fila[1]==1){
				$accion="Pin";//Apertura Pin/LLave/Mando
			}else if($fila[1]==2){
				$accion="Apertura Web";
			}else if($fila[1]==3){
				$accion="Apertura Web Emergencia";
			}else if($fila[1]==4){
				$accion="Llave";
			}
			
			/*START historial acciones realizadas, o comprobadas*/
				/*bien mal
				1 bien
				2 mal, pin mal
				3 mal por fuera de horario dia
				4 mal por fuera de horario hora
				5 mal por fuera de horario puerta 
				6 mal por pin desactivado, en el momento de usar
				*/
			$resultadoAccion="Se ha registrado un intento incorrecto.";
			
			if($fila[7]=="2"){
				$resultadoAccion="Se ha registrado un intento incorrecto.";
			}else if($fila[7]=="3"){
				$resultadoAccion="Fuera de horario.";
			}else if($fila[7]=="4"){
				$resultadoAccion="Fuera de horario.";
			}else if($fila[7]=="5"){
				$resultadoAccion="Fuera de horario.";
			}else if($fila[7]=="6"){
				$resultadoAccion="Pin desactivado.";
			}
			/*END historial acciones realizadas, o comprobadas*/
			
			$nombreAcceso="";
			if($fila[5]>0){
				$patron2="SELECT nombre,idusuario,apellidos FROM safey_accesos WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$fila[5]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 963534564222286434342254509258");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$nombreAcceso=$fila2[0]." ".$fila2[2];
					$idUsuario=$fila2[1];
					
					/*if($fila[7]=="6"){
						$resultadoAccion="Pin desactivado.";
					}*/
				}
				mysqli_free_result($respuesta2);
			}
			
			$puerta="";
			if($fila[4]>0){
				$patron11="SELECT nombre FROM safey_puertas WHERE idnodo=\"%s\" AND salidaplaca=\"1\" AND borrado=\"n\"";
				$sql11=sprintf($patron11,$fila[4]);
				$respuesta11=mysqli_query($con,$sql11) or die ("Error al buscar 9635345642211114225450118");
				if(mysqli_num_rows($respuesta11)>0){
					$fila11=mysqli_fetch_array($respuesta11);
					$puerta=$fila11[0];
				}
				mysqli_free_result($respuesta11);
			}
			
			$botones="";
			
			printf("<tr>
                        <td></td>
                        <td name='idlin%s'>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td nowrap='nowrap'>%s</td>
					</tr>",$fila[0],$puerta,$accion,$nombreAcceso,$fila[2],convierteFechaBarra($fila[3]),$resultadoAccion,$botones);
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
            printf('<thead>
                <tr>
                    <th>#</th>
                    <th>Puerta</th>
                    <th>Apertura</th>
                    <th>Nombre Acceso</th>
                    <th>Hora</th>
                    <th>Fecha</th>
                    <th>Resultado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>');
	}
}

//carga teclados
function cargaTecladosSafey($seleccionado,$nombre,$con){

	printf("<select class='form-control' name=\"%s\" id=\"%s\" >",$nombre,$nombre);
	printf("<option value='0'>Selecciona Teclado</option>");
	
	$patron="SELECT id,nombre,frecuencia,unidad FROM teclados WHERE borrado=\"n\" ORDER BY nombre ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12343454545466");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionado){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			printf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]." - ".$fila[2].$fila[3]);
		}
	}
	printf("</select>");
	mysqli_free_result($respuesta);
}

//carga nodos de usuario en safey accesos
function cargaNodosSafeyAccesos($seleccionado,$nombre,$idAcceso,$con){

	printf("<select class='form-control' name=\"%s\" id=\"%s\" onChange='recargarServicioNodo(this,%s);'>",$nombre,$nombre,$idAcceso);
	printf("<option value='0'>Selecciona un nodo</option>");
	
	$patron="SELECT safey_nodos.id,safey_nodos.nombre FROM safey_accesos,safey_nodos WHERE safey_accesos.id=\"%s\" AND safey_accesos.idusuario=safey_nodos.idusuario AND safey_accesos.borrado=\"n\" AND safey_accesos.guardado=\"s\" AND safey_nodos.borrado=\"n\" AND safey_nodos.guardado=\"s\"";
	$sql=sprintf($patron,$idAcceso);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 8967565248012343457053564546126");
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

//carga nodos de usuario en safey accesos para
function cargaNodosSafeyAccesosParaTabla($seleccionado,$nombre,$idAcceso,$con){

	$dev="";
	$dev.=sprintf("<select class='form-control' name=\"%s\" id=\"%s\" >",$nombre,$nombre);
	$dev.=sprintf("<option value='0'>Selecciona un nodo</option>");
	
	$patron="SELECT safey_nodos.id,safey_nodos.nombre FROM safey_accesos,safey_nodos WHERE safey_accesos.id=\"%s\" AND safey_accesos.idusuario=safey_nodos.idusuario AND safey_accesos.borrado=\"n\" AND safey_accesos.guardado=\"s\" AND safey_nodos.borrado=\"n\" AND safey_nodos.guardado=\"s\"";
	$sql=sprintf($patron,$idAcceso);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 8967565248012343457053564546126");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionado){
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

//historial puertas safey desde el acceso en si
function puertasSafeyAccesoHistorial($idAcceso,$con){
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialPuertasSafeyAcceso"]) && isset($_SESSION["fechaFinHistorialPuertasSafeyAcceso"]) ){
		$consulta=" AND safey_historial.fechaalta>=\"".$_SESSION["fechaIniHistorialPuertasSafeyAcceso"]."\" AND safey_historial.fechaalta<=\"".$_SESSION["fechaFinHistorialPuertasSafeyAcceso"]."\"";
	}
	
	if(isset($_SESSION["puertaHistorialPuertasSafeyAcceso"]) && $_SESSION["puertaHistorialPuertasSafeyAcceso"]>0){
		$consulta=" AND safey_historial.idpuerta=".$_SESSION["puertaHistorialPuertasSafeyAcceso"];
	}
	
	$patron="SELECT safey_historial.id,safey_historial.idpuerta,safey_historial.tipo,safey_historial.idacceso,safey_historial.idusuario,safey_historial.horaalta,safey_historial.fechaalta,safey_nodos.id,safey_historial.accionrealizada,safey_historial.miradoplaca FROM safey_historial,safey_nodos WHERE safey_historial.idacceso=\"%s\" AND safey_historial.idnodo=safey_nodos.id AND safey_nodos.guardado=\"s\" AND safey_nodos.borrado=\"n\"%s ORDER BY safey_historial.fechaalta DESC, safey_historial.horaalta DESC, safey_historial.id DESC";//LIMIT 0,50
	$sql=sprintf($patron,$idAcceso,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323463455899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Puerta</th>
					  <th>Apertura</th>
					  <th>Nombre Acceso</th>
					  <th>Hora</th>
					  <th>Fecha</th>
					  <th>Resultado</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
            $idNodo=$fila[0];
            
			//nombre acceso
			$idUsuario=0;
			$nombreAcceso="Sin datos";
			if($fila[3]>0){
				$patron2="SELECT nombre,idusuario,apellidos FROM safey_accesos WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$fila[3]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 963534564222286454509258");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$nombreAcceso=$fila2[0]." ".$fila2[2];
					$idUsuario=$fila2[1];
				}
				mysqli_free_result($respuesta2);
			}
			
			if($idUsuario==$_SESSION["idusersession"] || $_SESSION["permisossession"]!=3){
				//puerta
				$patron1="SELECT nombre FROM safey_puertas WHERE id=\"%s\" AND idnodo=\"%s\"";
				$sql1=sprintf($patron1,$fila[1],$fila[7]);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96353456486454509258");
				$fila1=mysqli_fetch_array($respuesta1);
				mysqli_free_result($respuesta1);

				//accion
				$accion="";
				if($fila[2]==1){
					$accion="Pin";//Apertura Pin/LLave/Mando
				}else if($fila[2]==2){
					$accion="Apertura Web";
				}else if($fila[2]==3){
					$accion="Apertura Web Emergencia";
				}

				//nombre usuario
				if($fila[2]==2 || $fila[2]==3){
					//nombre acceso
					$nombreAcceso="Sin datos";
					if($fila[4]>0){
						$patron3="SELECT nombre,apellidos FROM usuarios WHERE id=\"%s\"";
						$sql3=sprintf($patron3,$fila[4]);
						$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 963534456687664222286454509258");
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
				if($fila[3]>0){
					$botones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm' onClick='cargaLocation(\"index.php?s=16&i=".$idNodo."\");return false;' title='Ir al nodo'>
								<span class='svg-icon svg-icon-primary svg-icon-2x'><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Navigation\Right-2.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
									<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
										<polygon points='0 0 24 0 24 24 0 24'/>
										<rect fill='#000000' opacity='0.3' transform='translate(8.500000, 12.000000) rotate(-90.000000) translate(-8.500000, -12.000000) ' x='7.5' y='7.5' width='2' height='9' rx='1'/>
										<path d='M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z' fill='#000000' fill-rule='nonzero' transform='translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997) '/>
									</g>
								</svg><!--end::Svg Icon--></span>
							</a>";
				}
				

				printf("<tr>
							<td></td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td nowrap='nowrap'>%s</td>
						</tr>",$fila1[0],$accion,$nombreAcceso,$fila[5],convierteFechaBarra($fila[6]),$resultadoAccion,$botones);	
			}
			
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                        <th>#</th>
						<th>Puerta</th>
					  	<th>Apertura</th>
					  	<th>Nombre Acceso</th>
						<th>Hora</th>
						<th>Fecha</th>
						<th>Resultado</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//carga puertas historial nodos
function cargaFiltroPuertasAccesoHistorial($idAcceso,$con){

	$consulta="";
	if($_SESSION["permisossession"]!=1){//resto usuarios
		$consulta="";
	}else{//administrador
		$consulta="";
	}

	$patron="SELECT DISTINCT(safey_puertas.id),safey_puertas.nombre FROM safey_puertas,safey_accesosnodos,safey_nodos WHERE safey_puertas.borrado=\"n\" AND safey_accesosnodos.idacceso=\"%s\" AND safey_nodos.id=safey_accesosnodos.nodo AND safey_nodos.id=safey_puertas.idnodo AND safey_accesosnodos.borrado=\"n\" AND safey_nodos.borrado=\"n\" AND safey_nodos.guardado=\"s\" %s";
	$sql=sprintf($patron,$idAcceso,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 123454450553345457366");
	printf("<select class='form-control' name=\"puertaHistorialSafey\" id=\"puertaHistorialSafey\">");
	printf("<option value='0'>Selecciona Puerta</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$_SESSION["puertaHistorialPuertasSafeyAcceso"]){
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

//filtro clientes Safey configuracion de credenciales
function cargaUsuariosSafeyConfigCredencialesFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 399778676741236457056455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosSafeyConfCredenciales(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioSafeyList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//configuracion de economica nodo
function configuracionEconomicaNodo($idNodo,$con){
	$patron="SELECT id,idnodo,tiposervicio,precio,activo,tiporeserva,urlpago,descripcion FROM safey_nodos_configuracioneconomica WHERE idnodo=\"%s\" AND borrado=\"n\" ORDER BY id ASC";
	$sql=sprintf($patron,$idNodo);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 2498934361290786323463334345457601491902");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Descripción</th>
					  <th>Tipo Servicio</th>
					  <th>Precio €</th>
					  <th>Tipo Reserva</th>
					  <th>Url Pago</th>
					  <th>Activo</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$precio=floatval($fila[3]);
			
			/*start activo*/
			$optionUnoActivo="";
			$optionDosActivo="";
			if($fila[4]=="s"){
				$optionUnoActivo=" selected";
			}else if($fila[4]=="n"){
				$optionDosActivo=" selected";
			}
			$desplegableActivo="<select class='form-control' id='activoTarifa".$fila[0]."'><option value='s' ".$optionUnoActivo.">Sí</option><option value='n'".$optionDosActivo.">No</option></select>";
			/*end activo*/
			
			
			$acciones="";
			
			$acciones="
			<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm mx-3' onClick='editarTarifaNodo(\"".$fila[0]."\",\"".$idNodo."\");return false;'>
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
			<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Tarifa\",\"¿Estas 					seguro de que deseas eliminar esta tarifa?\",41,\"".$fila[0]."\",\"".$idNodo."\",\"\");return false;'>
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
						<td><input type='text' class='form-control' id='descripcionTarifa%s' value='%s' placeholder='Descripción servicio'/></td>
						<td>%s</td>
						<td><input type='number' class='form-control' id='precioTarifa%s' value='%s' placeholder='Precio'/></td>
						<td>%s</td>
						<td><input type='text' class='form-control' id='urlPagoTarifa%s' value='%s' placeholder='Url Pago'/></td>
						<td>%s</td>
						<td>%s</td>
					</tr>",$fila[0],$fila[7],cargarTipoServicioPagos($fila[2],$fila[0],$con),$fila[0],$precio,cargarTipoReservaPagos($fila[5],$fila[0],$con),$fila[0],$fila[6],$desplegableActivo,$acciones);
		}
		
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
						<th>#</th>
					  	<th>Descripción</th>
					  	<th>Tipo Servicio</th>
					  	<th>Precio €</th>
					  	<th>Tipo Reserva</th>
					  	<th>Url Pago</th>
					  	<th>Activo</th>
					  	<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//cargar tipo reserva pagos
function cargarTipoReservaPagos($seleccionado,$idLinea,$con){
	$desplegable="";
	
	$patron="SELECT id,tipo FROM safey_tiporeservapagos ORDER BY tipo ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1235457635865466585545466");
	$desplegable.=sprintf("<select class='form-control' name=\"tipoReservaTarifa%s\" id=\"tipoReservaTarifa%s\" >",$idLinea,$idLinea);
	$desplegable.=sprintf("<option value='0'>Tipo Reserva:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionado){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			$desplegable.=sprintf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]);
		}
	}
	$desplegable.=sprintf("</select>");
	mysqli_free_result($respuesta);
	
	return $desplegable;
}


//cargar stipo servicio
function cargarTipoServicioPagos($seleccionado,$idLinea,$con){
	$desplegable="";
	
	$patron="SELECT id,tipo FROM safey_tiposerviciopagos ORDER BY id ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12353434457635865466586555545466");
	$desplegable.=sprintf("<select class='form-control' name=\"tipoServicioTarifa%s\" id=\"tipoServicioTarifa%s\" >",$idLinea,$idLinea);
	$desplegable.=sprintf("<option value='0'>Tipo Servicio:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionado){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			$desplegable.=sprintf("<option value='%s'%s>%s</option>",$fila[0],$select,$fila[1]);
		}
	}
	$desplegable.=sprintf("</select>");
	mysqli_free_result($respuesta);
	
	return $desplegable;
}


//cargar tipoServicio pagado del noco
function cargarTipoServicioPagoNodo($seleccionado,$idLinea,$idNodo,$con){
	$desplegable="";
	
	if($idNodo>0){
		$patron="SELECT id,descripcion,tiposervicio,precio FROM safey_nodos_configuracioneconomica WHERE idnodo=\"%s\" AND borrado=\"n\" ORDER BY id ASC";
		$sql=sprintf($patron,$idNodo);
		$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12353588900434457635865466586555545466");
		$desplegable.=sprintf("<select class='form-control' name=\"tipoServicioPago%s\" id=\"tipoServicioPago%s\" >",$idLinea,$idLinea);
		$desplegable.=sprintf("<option value='0'>Tipo Servicio Nodo:</option>");
		if(mysqli_num_rows($respuesta)>0){
			for($i=0;$i<mysqli_num_rows($respuesta);$i++){
				$fila=mysqli_fetch_array($respuesta);
				
				$textoOpcionTipoServicio=$fila[1]." (";
				if($fila[2]>0){
					$patron1="SELECT tipo FROM safey_tiposerviciopagos WHERE id=\"%s\" AND borrado=\"n\" ";
					$sql1=sprintf($patron1,$fila[2]);
					$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 1235345800434457635865466586555545466");
					if(mysqli_num_rows($respuesta1)>0){
						$fila1=mysqli_fetch_array($respuesta1);
					
						$textoOpcionTipoServicio.=$fila1[0];
					}
					mysqli_free_result($respuesta1);
				}
				$textoOpcionTipoServicio.=" - ".$fila[3]." €)";
				
				if($fila[0]==$seleccionado){
					$select=" selected='selected'";
				}else{
					$select="";
				}
				$desplegable.=sprintf("<option value='%s'%s>%s</option>",$fila[0],$select,$textoOpcionTipoServicio/*$fila[1]*/);
			}
		}
		$desplegable.=sprintf("</select>");
		mysqli_free_result($respuesta);
	}else{
		$desplegable.=sprintf("<select class='form-control' name=\"tipoServicioPago\" id=\"tipoServicioPago\" ><option value=\"0\">Selecciona un Nodo</option></select>");
	}
	
	return $desplegable;
}

//historial pagos safey NODO
function pagosSafeyNodoHistorial($idNodo,$con){
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialPagos"]) && isset($_SESSION["fechaFinHistorialPagos"]) ){
		$consulta=" AND fechainicio>=\"".$_SESSION["fechaIniHistorialPagos"]."\" AND fechafin<=\"".$_SESSION["fechaFinHistorialPagos"]."\"";
	}
	
	//recorrer pagos
	$patron="SELECT id,idusuario,tiposervicio,codigopromocional,descuento,total,metodopago,fechainicio,fechafin,idnodo,idacceso FROM safey_pagos WHERE borrado=\"n\" AND idnodo=\"%s\"%s ORDER BY fechainicio DESC, id DESC";
	$sql=sprintf($patron,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 969089173732346345258919");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      	<th>#</th>
						<th>Acceso</th>
						<th>Servicio</th>
						<th>Cód. Promocional</th>
						<th>Descuento</th>
						<th>Importe</th>
						<th>Método De Pago</th>
						<th>Fecha Inicio</th>
						<th>Fecha Fin</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$idUsuario=$fila[1];
			$idNodo=$fila[9];
            $idTipoServicio=$fila[2];
            $idAcceso=$fila[10];
                
			//consultar metodo de pago
			$codPromo="Sin datos";
			if($fila[3]>0){
				$patron2="SELECT codigo FROM safey_codigospromocionales WHERE borrado=\"n\" AND id=\"%s\"";
				$sql2=sprintf($patron2,$fila[3]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 349762963534564281972280909258");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$codPromo=$fila2[0];
				}
				mysqli_free_result($respuesta2);
			}
			
			
			//obtener usuario de accesos
			$nombreApellidosAcceso="";
			$patron1="SELECT nombre,apellidos FROM safey_accesos WHERE id=\"%s\" AND borrado=\"n\"";
			$sql1=sprintf($patron1,$idAcceso);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 7215429695608912346350452589785619");
			if(mysqli_num_rows($respuesta1)>0){
				$fila1=mysqli_fetch_array($respuesta1);
				$nombreApellidosAcceso=$fila1[0]." ".$fila1[1];
			}
			mysqli_free_result($respuesta1);
			
			$acciones="";

			printf("<tr>
						<td></td>
						<td>%s</td>
						<td>%s</td>
						<td><input type='text' class='form-control' id='codpromo%s' value='%s' maxlength='255' placeholder='Código promocional'></td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td><input type='date' class='form-control' id='finiciopago%s' value='%s'/></td>
						<td><input type='date' class='form-control' id='ffinpago%s' value='%s'/></td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$nombreApellidosAcceso,cargarTipoServicioPagoNodo($idTipoServicio,$fila[0],$idNodo,$con),$fila[0],$codPromo,$fila[4]." €",$fila[5]." €",cargaTiposMetodosPagoParaTabla($fila[6],"metodopago".$fila[0],"",$con),$fila[0],$fila[7],$fila[0],$fila[8],$acciones);	
			}
			
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
						<th>#</th>
						<th>Acceso</th>
						<th>Servicio</th>
						<th>Cód. Promocional</th>
						<th>Descuento</th>
						<th>Importe</th>
						<th>Método De Pago</th>
						<th>Fecha Inicio</th>
						<th>Fecha Fin</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

?>