<?php 

													/*************************************
													 *									 *
													 *		        LUCES		         *
													 *									 *
													 *************************************/	
// CARGA luces
function cargaNodosLucesList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioLucesList"]!="0"*/){
		//$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioLucesList"])."\"";
	}else{
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	if($_SESSION["estadoLucesList"]!=""){
		$consulta.=" AND conexion=\"".quitaComillasD($_SESSION["estadoLucesList"])."\"";
	}
	
	$patron="SELECT id,nombre,idusuario,conexion FROM luces_nodos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632787879958");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>Programas</th>
					  <th>XXXX</th>
					  <th>Conexión</th>
					  <th>XXXX</th>
					  <th>XXXXX</th>
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
			if(mysqli_num_rows($respuesta1)>0){
				$fila1=mysqli_fetch_array($respuesta1);
			}
			mysqli_free_result($respuesta1);
			
			//programa
			$programas="XXMuchosXX";
			/*$patron2="SELECT nombre FROM luces_programas WHERE id=\"%s\"";
			$sql2=sprintf($patron2,$fila[4]);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 9632456254658");
			if(mysqli_num_rows($respuesta2)>0){
				$fila2=mysqli_fetch_array($respuesta2);
			}
			//$programas=$fila2[0];
			mysqli_free_result($respuesta2);*/
			
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
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=9&i=%s\");'",$fila[0]);
			
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
				</tr>",$funcion,$fila[1],$funcion,$fila1[0],$funcion,$programas,$funcion,"XXXX",$funcion,$conexion,$funcion,"XXXX",$funcion,"XXXX",$botonesAcciones);
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>Programas</th>
					  <th>XXXX</th>
					  <th>Conexión</th>
					  <th>XXXX</th>
					  <th>XXXXX</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}

//filtro clientes luces list
function cargaUsuariosLucesFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 31215656456655875565778676746456455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosLuces(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioLucesList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//filtro estado contadores list
function cargaEstadosLucesFiltro($con){
	$selectedUno="";
	$selectedDos="";
	if(isset($_SESSION["estadoLucesrList"]) && $_SESSION["estadoLucesrList"]=="on"){
		$selectedUno=" selected";
	}else if(isset($_SESSION["estadoLucesrList"]) &&  $_SESSION["estadoLucesrList"]=="off"){
		$selectedDos=" selected";
	}
	
	printf("<select class='form-control' id='selectEstadoFiltro' onChange='filtrarEstadoLuces(this);'><option value=''>Selecciona Estado:</option>");
	printf("<option value=\"on\" %s>Online</option><option value=\"off\" %s>Offline</option>",$selectedUno,$selectedDos);	
	printf('</select>');
}

// CARGA progrmas luces
function cargaProgramasLucesList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 && $_SESSION["usuarioProgramasLucesList"]!="0"){
		$consulta.=" AND usuarios.id=\"".quitaComillasD($_SESSION["usuarioProgramasLucesList"])."\"";
	}else{
		$consulta.=" AND usuarios.id=\"".calculaIdEmpresa($con)."\"";
	}
	
	//$patron="SELECT id,nodo,nombre,programa FROM luces_programas WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$patron="SELECT luces_programas.id,luces_programas.nombre,usuarios.id FROM luces_programas,usuarios WHERE luces_programas.borrado=\"n\" AND luces_programas.guardado=\"s\" AND luces_programas.idusuario=usuarios.id AND usuarios.guardado=\"s\" AND usuarios.borrado=\"n\"%s ORDER BY luces_programas.nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96327878794567958");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Nodo</th>
					  <th>Usuario</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
	
			//usuario
			$patron1="SELECT nombre FROM usuarios WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$fila[2]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 961133258");
			$fila1=mysqli_fetch_array($respuesta1);
			mysqli_free_result($respuesta1);
		
			$botonesAcciones="";

			//pulsar tr
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=12&i=%s\");'",$fila[0]);
			
			printf("<tr>
				<td></td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class=''>%s</td>
				</tr>",$funcion,$fila[1],$funcion,"los que sean",$funcion,$fila1[0],$botonesAcciones);
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Nodo</th>
					  <th>Usuario</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}

//filtro clientes programas luces list
function cargaUsuariosProgramasLucesFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre ASC,apellidos ASC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 31215656456655875565778456887776746456455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosProgramasLuces(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioProgramasLucesList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//carga usuarios generico
function cargaProgamasLuces($seleccionada,$nombre,$con){

	$consulta="";
	if($_SESSION["permisossession"]!=1){
		$consulta=" AND idusuario=".$_SESSION["idusersession"];
	}
	
	$patron="SELECT id,nombre FROM luces_programas WHERE borrado=\"n\" AND guardado=\"s\"%s";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1235457645466");
	printf("<select class='form-control' name=\"%s\" id=\"%s\" >",$nombre,$nombre);
	printf("<option value='0'>Selecciona Programa</option>");
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

//configuracion programa luces
function configuracionProgramaLuces($idPrograma,$con){
	
	//$flecha="fl";
	$columuno="L 1";
	$columdos="L 2";
	$columtres="L 3";
	$columcuatro="L 4";
	$columcinco="L 5";
	$columseis="L 6";
	$columsiete="L 7";
	$columocho="L 8";
	$columnueve="L 9";
	$columdiez="L10";
	$columonce="L11";
	$columdoce="L12";
	$columtrece="L13";
	$columcatorce="L14";
	$columquince="L15";
	$columdieciseis="L16";
	$columdiecisiete="L17";
	$columdieciocho="L18";
	$columdiecinueve="L19";
	$columveinte="L20";
    $numFocos=18;
	
	/*
	//ya no se usa, tiramos todo desde bbdd
	$colorUno=0;
	$colorDos=0;
	$colorTres=0;
	$colorCuatro=0;
	$colorCinco=0;
	$colorSeis=0;
	$colorOn=0;
	$colorOff=0;
	
	//obtener colores en un for con variables para pintar
	$patron99="SELECT id,color FROM luces_configuracion_color WHERE borrado=\"n\"";
	$sql99=sprintf($patron99);
	$respuesta99=mysqli_query($con,$sql99) or die ("Error al buscar 96324563459945757890899");
	if(mysqli_num_rows($respuesta99)>0){
		for($h=0;$h<mysqli_num_rows($respuesta99);$h++){
			$fila99=mysqli_fetch_array($respuesta99);

			switch($h){
				case 0:
					$colorUno=$fila99[1];	
				break;
				case 1:
					$colorDos=$fila99[1];
				break;
				case 2:
					$colorTres=$fila99[1];
				break;
				case 3:
					$colorCuatro=$fila99[1];
				break;
				case 4:
					$colorCinco=$fila99[1];
				break;
				case 5:
					$colorSeis=$fila99[1];
				break;
				case 6:
					$colorOn=$fila99[1];
				break;
				case 7:
					$colorOff=$fila99[1];
				break;
			}
		}
	}
	mysqli_free_result($respuesta99);*/
	
	$patron1="SELECT colum1,colum2,colum3,colum4,colum5,colum6,colum7,colum8,colum9,colum10,colum11,colum12,colum13,colum14,colum15,colum16,colum17,colum18,colum19,colum20,numfocos FROM luces_programas WHERE id=\"%s\"";
	$sql1=sprintf($patron1,$idPrograma);
	$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 1233111217765544");
	if(mysqli_num_rows($respuesta1)>0){
		$fila1=mysqli_fetch_array($respuesta1);
		$columuno=$fila1[0];
		$columdos=$fila1[1];
		$columtres=$fila1[2];
		$columcuatro=$fila1[3];
		$columcinco=$fila1[4];
		$columseis=$fila1[5];
		$columsiete=$fila1[6];
		$columocho=$fila1[7];
		$columnueve=$fila1[8];
		$columdiez=$fila1[9];
		$columonce=$fila1[10];
		$columdoce=$fila1[11];
		$columtrece=$fila1[12];
		$columcatorce=$fila1[13];
		$columquince=$fila1[14];
		$columdieciseis=$fila1[15];
		$columdiecisiete=$fila1[16];
		$columdieciocho=$fila1[17];
		$columdiecinueve=$fila1[18];
		$columveinte=$fila1[19];
        $numFocos=$fila1[20];
	}
	mysqli_free_result($respuesta1);
	
	$patron="SELECT id,programa,temporizacion,colorcolumuno,colorcolumdos,colorcolumtres,colorcolumcuatro,colorcolumcinco,colorcolumseis,colorcolumsiete,colorcolumocho,colorcolumnueve,colorcolumdiez,colorcolumonce,colorcolumdoce,colorcolumtrece,colorcolumcatorce,colorcolumquince,colorcolumdieciseis,colorcolumdiecisiete,colorcolumdieciocho,colorcolumdiecinueve,colorcolumveinte FROM luces_filasprograma WHERE borrado=\"n\" AND programa=\"%s\" ORDER BY id ASC";
	$sql=sprintf($patron,$idPrograma);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963257890899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>#</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>Temp.</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>',$columuno,$columdos,$columtres,$columcuatro,$columcinco,$columseis,$columsiete,$columocho,$columnueve,$columdiez,$columonce,$columdoce,$columtrece,$columcatorce,$columquince,$columdieciseis,$columdiecisiete,$columdieciocho,$columdiecinueve,$columveinte);
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			$idLuzFilaPrograma=$fila[0];
			
			//flechas
			$flechas="<a href='javascript: void(0)' class='btn btn-icon btn-sm' title='Mover arriba'>
                                <span class='svg-icon svg-icon-primary svg-icon-2x'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-04-09-093151/theme/html/demo1/dist/../src/media/svg/icons/Navigation/Arrow-up.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
                                    <g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
                                        <polygon points='0 0 24 0 24 24 0 24'/>
                                        <rect fill='#000000' opacity='0.3' x='11' y='5' width='2' height='14' rx='1'/>
                                        <path d='M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z' fill='#000000' fill-rule='nonzero'/>
                                    </g>
                                </svg><!--end::Svg Icon--></span>
                            </a>";
			$flechab="<a href='javascript: void(0)' class='btn btn-icon btn-sm mx-3' title='Mover abajo'>
                                    <span class='svg-icon svg-icon-primary svg-icon-2x'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-04-09-093151/theme/html/demo1/dist/../src/media/svg/icons/Navigation/Arrow-down.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
                                    <g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
                                        <polygon points='0 0 24 0 24 24 0 24'/>
                                        <rect fill='#000000' opacity='0.3' x='11' y='5' width='2' height='14' rx='1'/>
                                        <path d='M6.70710678,18.7071068 C6.31658249,19.0976311 5.68341751,19.0976311 5.29289322,18.7071068 C4.90236893,18.3165825 4.90236893,17.6834175 5.29289322,17.2928932 L11.2928932,11.2928932 C11.6714722,10.9143143 12.2810586,10.9010687 12.6757246,11.2628459 L18.6757246,16.7628459 C19.0828436,17.1360383 19.1103465,17.7686056 18.7371541,18.1757246 C18.3639617,18.5828436 17.7313944,18.6103465 17.3242754,18.2371541 L12.0300757,13.3841378 L6.70710678,18.7071068 Z' fill='#000000' fill-rule='nonzero' transform='translate(12.000003, 14.999999) scale(1, -1) translate(-12.000003, -14.999999) '/>
                                    </g>
                                </svg><!--end::Svg Icon--></span>
                            </a>";
			if($i>0){
				$flechas="<a href='#' class='btn btn-icon btn-light btn-hover-primary btn-sm' title='Mover arriba' onclick='mueveFilaPrograma(1,\"".$fila[0]."\",\"".$idPrograma."\");'>
                                <span class='svg-icon svg-icon-primary svg-icon-2x'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-04-09-093151/theme/html/demo1/dist/../src/media/svg/icons/Navigation/Arrow-up.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
                                    <g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
                                        <polygon points='0 0 24 0 24 24 0 24'/>
                                        <rect fill=''#000000' opacity='0.3' x='11' y='5' width='2' height='14' rx='1'/>
                                        <path d='M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z' fill='#000000' fill-rule='nonzero'/>
                                    </g>
                                </svg><!--end::Svg Icon--></span>
                            </a>";
			}
			if($i+1<mysqli_num_rows($respuesta)){
				$flechab="<a href='#' class='btn btn-icon btn-light btn-hover-primary btn-sm mx-3' title='Mover abajo' onclick='mueveFilaPrograma(2,\"".$fila[0]."\",\"".$idPrograma."\");'>
                                    <span class='svg-icon svg-icon-primary svg-icon-2x'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-04-09-093151/theme/html/demo1/dist/../src/media/svg/icons/Navigation/Arrow-down.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
                                    <g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
                                        <polygon points='0 0 24 0 24 24 0 24'/>
                                        <rect fill='#000000' opacity='0.3' x='11' y='5' width='2' height='14' rx='1'/>
                                        <path d='M6.70710678,18.7071068 C6.31658249,19.0976311 5.68341751,19.0976311 5.29289322,18.7071068 C4.90236893,18.3165825 4.90236893,17.6834175 5.29289322,17.2928932 L11.2928932,11.2928932 C11.6714722,10.9143143 12.2810586,10.9010687 12.6757246,11.2628459 L18.6757246,16.7628459 C19.0828436,17.1360383 19.1103465,17.7686056 18.7371541,18.1757246 C18.3639617,18.5828436 17.7313944,18.6103465 17.3242754,18.2371541 L12.0300757,13.3841378 L6.70710678,18.7071068 Z' fill='#000000' fill-rule='nonzero' transform='translate(12.000003, 14.999999) scale(1, -1) translate(-12.000003, -14.999999) '/>
                                    </g>
                                </svg><!--end::Svg Icon--></span>
                            </a>";
			}
			
			$botones=$flechas.$flechab."<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm ' 	 		onClick='editarLineaConfiguracionLuces(\"".$idPrograma."\",\"".$fila[0]."\");return false;' title='Guardar Cambios'>
							<span class='svg-icon svg-icon-md svg-icon-success'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Save.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<polygon points='0 0 24 0 24 24 0 24'/>
									<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
									<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
								</g>
							</svg><!--end::Svg Icon-->
							</span>
					</a>
                    <a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm mx-3' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas 					seguro de que deseas eliminar este registro?\",8,\"".$idPrograma."\",\"".$fila[0]."\",\"\");return false;' title='Borrar'>
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
			$alto=33;
            $ancho=33;
            
			printf("<tr><td></td>");
                        $estiloX="";
                        $icono="";
                        $colorDiv="#ffffff";
                        $selectorColores="";
			
						printf("<td>%s</td>",$i+1);
                        for($j=0;$j<20;$j++){
							
							//segun ese id, saber color
							$campoConsulta=$fila[3+$j];
							$colorTabla="#ffffff";
							$patron2="SELECT color FROM luces_configuracion_color WHERE id=\"%s\"";
							$sql2=sprintf($patron2,$campoConsulta);
							$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 12331112177222265544");
							if(mysqli_num_rows($respuesta2)>0){
								$fila2=mysqli_fetch_array($respuesta2);
								$colorTabla=$fila2[0];
							}
							
							//id columna, div
							$idCeldaColumna="luzModal".$fila[0]."_".($j+1);
							
                            if($j<$numFocos){
                                //nada
                                $estiloX="";
                                $icono="";
								
								//si es el 7, es on, es blanco, mostrar icono,off blanco por defecto sin nada
								if($campoConsulta==7){
									$icono="<i class='fas fa-sun' style='font-size: 20px;margin-top: 17%;'></i>";
								}
								
                                $colorDiv=$colorTabla/*$fila[$j+3]*//*"#3699ff"*/;
								if($colorDiv==""){
									 $colorDiv="#ffffff";
								}
                                $selectorColores="data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'";
                            }else{
                                $estiloX="border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center";
                                $icono="<i class='fas fa-times' style='font-size:25px;' title='No disponible'></i>";
                                $colorDiv="#ffffff";
                                $selectorColores="";
                            }
                            
                            $estilo="border: 1px solid #000000;border-radius: .42rem;background-color: ".$colorDiv.";cursor: pointer;";
							
							/*START variable pintar modal colores y funcionalidades foco*/
							$pintarModalConfFoco="";
							
								//empiezo a pintar el color cuadrado de fuera el pulsable de la modal
							$auxDivAperturaModalConfFoco="<div id='".$idCeldaColumna."' 	style='width:".$alto."px;height:".$ancho."px;".$estilo.$estiloX."'".$selectorColores."><input type='hidden' id='".$idCeldaColumna."_hidden' value='".$campoConsulta/*$colorDiv*/."'/>
                            ".$icono;
							
								$auxOpcionesModalConFoco="<div class='dropdown-menu dropdown-menu-sm'>";
								/*start pintar funcionalidades de bbdd, pintar modal selector colores*/
								$pintarFuncionaldiad="";
								$patron77="SELECT id,color,colorreal,colortextoingles,estado FROM luces_configuracion_color WHERE borrado=\"n\" ORDER BY id ASC";
								$sql77=sprintf($patron77);
								$respuesta77=mysqli_query($con,$sql77) or die ("Error al buscar 9677325787078997");
								if(mysqli_num_rows($respuesta77)>0){
									$lineaSeparadoraFila="<div class='dropdown-divider'></div>";
									for($z=0;$z<mysqli_num_rows($respuesta77);$z++){
										$fila77=mysqli_fetch_array($respuesta77);
										$idColor=$fila77[0];
										$codigoColor=$fila77[1];
										$numBoton=$idColor/*($i+1)*/;
										$colorReal=$fila77[2];
										$colorTextoIngles=$fila77[3];
										$estadoColor=$fila77[4];
										
										$textoColorStyleMostrar="";
										$textoMostrar="";
										if($estadoColor=="on" && $colorReal=="icono"){//para el blanco encendido
											$textoColorStyleMostrar="-webkit-box-align: center;padding-top: 8px;color:#28a745;";
											$textoMostrar=$estadoColor;
										}else if($estadoColor=="off" && $colorReal=="#ffffff"){
											$textoColorStyleMostrar="-webkit-box-align: center;padding-top: 8px;color:#000000;";
											$textoMostrar=$estadoColor;
										}
										
										/*START pintar el div flex de fila, dos botones por fila*/
										if ($z % 2 == 0){
											$styleDivFila="";
											if($z>0){
												$pintarFuncionaldiad.="</div>";
											}
											if($z>0){
												$styleDivFila="margin: .5rem 0;";
											}
											$pintarFuncionaldiad.="<div style='display:flex;".$styleDivFila."'>";
										}
										/*END pintar el div flex de fila, dos botones por fila*/
										
										$pintarFuncionaldiad.="<div title='".$colorTextoIngles."' style='width:50%;text-align: -webkit-center;' onClick='cambiarColorCelda(\"".$codigoColor."\",".$idColor.",\"".$idCeldaColumna."\",".$idPrograma.",".$idLuzFilaPrograma.");'>
                                            <div style='width:38px;height:38px;border: 1px solid #000000;border-radius: .42rem;background-color: ".$codigoColor.";cursor: pointer;".$textoColorStyleMostrar."'>".$textoMostrar."
                                            </div>
                                        </div>";
									}
								}
								mysqli_free_result($respuesta77);
							
								$auxOpcionesModalConFoco.=$pintarFuncionaldiad;
								/*end pintar funcionalidades de bbdd, pintar modal selector colores*/
							
								//start diferentes opciones
								//ya no uso, tiramos justo de lo anterior, de bbdd
								/*$auxOpcionesModalConFoco.="
                                    <div style='display:flex'>
                                        <div style='width:50%;text-align: -webkit-center;' onClick='cambiarColorCelda(\"".$colorUno."\",1,\"".$idCeldaColumna."\",\"".$idPrograma."\",\"".$fila[0]."\");'>
                                            <div style='width:38px;height:38px;border: 1px solid #000000;border-radius: .42rem;background-color: ".$colorUno.";cursor: pointer;'>
												
                                            </div>
                                        </div>
                                        <div style='width:50%;text-align: -webkit-center;' onClick='cambiarColorCelda(\"".$colorDos."\",2,\"".$idCeldaColumna."\",\"".$idPrograma."\",\"".$fila[0]."\");'>
                                            <div style='width:38px;height:38px;border: 1px solid #000000;border-radius: .42rem;background-color: ".$colorDos.";cursor: pointer;'>
												
                                            </div>
                                        </div>
                                    </div>
                                    <div style='display:flex;margin: .5rem 0;'>
                                        <div style='width:50%;text-align: -webkit-center;' onClick='cambiarColorCelda(\"".$colorTres."\",3,\"".$idCeldaColumna."\",\"".$idPrograma."\",\"".$fila[0]."\");'>
                                            <div style='width:38px;height:38px;border: 1px solid #000000;border-radius: .42rem;background-color: ".$colorTres.";cursor: pointer;'>
												
                                            </div>
                                        </div>
                                        <div style='width:50%;text-align: -webkit-center;' onClick='cambiarColorCelda(\"".$colorCuatro."\",4,\"".$idCeldaColumna."\",\"".$idPrograma."\",\"".$fila[0]."\");'>
                                            <div style='width:38px;height:38px;border: 1px solid #000000;border-radius: .42rem;background-color: ".$colorCuatro.";cursor: pointer;'>
												
                                            </div>
                                        </div>
                                    </div>
                                    <div style='display:flex'>
                                        <div style='width:50%;text-align: -webkit-center;' onClick='cambiarColorCelda(\"".$colorCinco."\",5,\"".$idCeldaColumna."\",\"".$idPrograma."\",\"".$fila[0]."\");'>
                                            <div style='width:38px;height:38px;border: 1px solid #000000;border-radius: .42rem;background-color: ".$colorCinco.";cursor: pointer;'>
											
                                            </div>
                                        </div>
                                        <div style='width:50%;text-align: -webkit-center;' onClick='cambiarColorCelda(\"".$colorSeis."\",6,\"".$idCeldaColumna."\",\"".$idPrograma."\",\"".$fila[0]."\");'>
                                            <div style='width:38px;height:38px;border: 1px solid #000000;border-radius: .42rem;background-color: ".$colorSeis.";cursor: pointer;'>
											
                                            </div>
                                        </div>
                                    </div>
                                    <div class='dropdown-divider'></div>
                                    <div style='display:flex'>
                                        <div style='width:50%;text-align: -webkit-center;' onClick='cambiarColorCelda(\"".$colorOn."\",7,\"".$idCeldaColumna."\",\"".$idPrograma."\",\"".$fila[0]."\");'>
                                            <div style='width:38px;height:38px;border: 1px solid #000000;border-radius: .42rem;cursor: pointer;-webkit-box-align: center;padding-top: 8px;color:#28a745;'>
                                                ON
                                            </div>
                                        </div>
                                        <div style='width:50%;text-align: -webkit-center;' onClick='cambiarColorCelda(\"".$colorOff."\",8,\"".$idCeldaColumna."\",\"".$idPrograma."\",\"".$fila[0]."\");'>
                                            <div style='width:38px;height:38px;border: 1px solid #000000;border-radius: .42rem;cursor: pointer;-webkit-box-align: center;padding-top: 8px;color:#000000;'>
                                                OFF
                                            </div>
                                        </div>
                                    </div>";*/
								//end diferentes opciones
                            $auxDivCierreModalConfFoco="</div></div>";//cerrar div de inicio,'''$variableDivAperturaModalConfFoco="<div id'''
							
							//variable resultante
							$pintarModalConfFoco=$auxDivAperturaModalConfFoco.$auxOpcionesModalConFoco.$auxDivCierreModalConfFoco;
							/*END variable pintar modal colores y funcionalidades foco*/
							
                            printf("<td style='padding-left: 0;padding-right: 0;text-align: -webkit-center;'>%s</td>", $pintarModalConfFoco);
                        }

            printf("<td>%s</td><td nowrap='nowrap'>%s</td></tr>",cargarTemporizacion($fila[2],$fila[0],$con),$botones);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>#</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>%s</th>
					  <th>Temp.</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>',$columuno,$columdos,$columtres,$columcuatro,$columcinco,$columseis,$columsiete,$columocho,$columnueve,$columdiez,$columonce,$columdoce,$columtrece,$columcatorce,$columquince,$columdieciseis,$columdiecisiete,$columdieciocho,$columdiecinueve,$columveinte);
	}
}

//cargar temporizacion
function cargarTemporizacion($seleccionado,$idLinea,$con){
	$desplegable="";
	
	$patron="SELECT id,tiemposegundos FROM temporizacion ORDER BY tiemposegundos ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 123545763466585545466");
	$desplegable.=sprintf("<select class='form-control' name=\"temporizacion%s\" id=\"temporizacion%s\" >",$idLinea,$idLinea);
	$desplegable.=sprintf("<option value='0'>Temporización</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			if($fila[0]==$seleccionado){
				$select=" selected='selected'";
			}else{
				$select="";
			}
			$desplegable.=sprintf("<option value='%s'%s>%s s</option>",$fila[0],$select,$fila[1]);
		}
	}
	$desplegable.=sprintf("</select>");
	mysqli_free_result($respuesta);
	
	return $desplegable;
}

//pintar input focos
function pintarFocosProgramas_old($idPrograma,$con){
	$dev="";
	
	$vueltasFor=20;
	$focosEnUso=20;
	
	$patron="SELECT colum1,colum2,colum3,colum4,colum5,colum6,colum7,colum8,colum9,colum10,colum11,colum12,colum13,colum14,colum15,colum16,colum17,colum18,colum19,colum20,numfocos FROM luces_programas WHERE id=\"%s\"";
	$sql=sprintf($patron,$idPrograma);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 123325454576346658456545543545466");
	if(mysqli_num_rows($respuesta)>0){
		$fila=mysqli_fetch_array($respuesta);
		$focosEnUso=$fila[20];
	}
	mysqli_free_result($respuesta);
	
	///***START pintar campos focos letras
	for($i=0;$i<$vueltasFor;$i++){
		if(($i % 12)==0){
			if($i>0){
				printf("</div>");
			}
			printf("<div class='form-group row'>");
		}
		
		$readOnly="";
		$class="";
		if($i>=$focosEnUso){
			$readOnly=" readonly";
			$class=" inputReadOnly";
		}
		
		printf("<div class='col-md-1'><label>%s:</label><input type='text' maxlength='2' class='form-control%s' name='colum%s' value=\"%s\" placeholder='Luz %s'%s/></div>",$i+1,$class,$i+1,$fila[$i],$i+1,$readOnly);
	}
	printf("</div>");
	///***END pintar campos focos letras
}

//pintar input focos
function pintarFocosProgramas($idPrograma,$con){
	$dev="";
	
	$vueltasFor=20;
	$focosEnUso=20;
	
	/*START declarar campos nombre foco*/
	$colum1="";
	$colum2="";
	$colum3="";
	$colum4="";
	$colum5="";
	$colum6="";
	$colum7="";
	$colum8="";
	$colum9="";
	$colum10="";
	$colum11="";
	$colum12="";
	$colum13="";
	$colum14="";
	$colum15="";
	$colum16="";
	$colum17="";
	$colum18="";
	$colum19="";
	$colum20="";
	/*END declarar campos nombre foco*/

	/*START recoger campos tipo foco*/
	$tipoFocoColum1=0;
	$tipoFocoColum2=0;
	$tipoFocoColum3=0;
	$tipoFocoColum4=0;
	$tipoFocoColum5=0;
	$tipoFocoColum6=0;
	$tipoFocoColum7=0;
	$tipoFocoColum8=0;
	$tipoFocoColum9=0;
	$tipoFocoColum10=0;
	$tipoFocoColum11=0;
	$tipoFocoColum12=0;
	$tipoFocoColum13=0;
	$tipoFocoColum14=0;
	$tipoFocoColum15=0;
	$tipoFocoColum16=0;
	$tipoFocoColum17=0;
	$tipoFocoColum18=0;
	$tipoFocoColum19=0;
	$tipoFocoColum20=0;
	/*END recoger campos tipo foco*/
	
	$arrayTipoFocosPosiciones=array();
	$arrayVaciosFocosPosiciones=array();
	$patron="SELECT colum1,colum2,colum3,colum4,colum5,colum6,colum7,colum8,colum9,colum10,colum11,colum12,colum13,colum14,colum15,colum16,colum17,colum18,colum19,colum20,numfocos,tipofococolum1,tipofococolum2,tipofococolum3,tipofococolum4,tipofococolum5,tipofococolum6,tipofococolum7,tipofococolum8,tipofococolum9,tipofococolum10,tipofococolum11,tipofococolum12,tipofococolum13,tipofococolum14,tipofococolum15,tipofococolum16,tipofococolum17,tipofococolum18,tipofococolum19,tipofococolum20,vaciosfococolum1,vaciosfococolum2,vaciosfococolum3,vaciosfococolum4,vaciosfococolum5,vaciosfococolum6,vaciosfococolum7,vaciosfococolum8,vaciosfococolum9,vaciosfococolum10,vaciosfococolum11,vaciosfococolum12,vaciosfococolum13,vaciosfococolum14,vaciosfococolum15,vaciosfococolum16,vaciosfococolum17,vaciosfococolum18,vaciosfococolum19,vaciosfococolum20 FROM luces_programas WHERE id=\"%s\"";
	$sql=sprintf($patron,$idPrograma);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 123325454576346658456545543545466");
	if(mysqli_num_rows($respuesta)>0){
		$fila=mysqli_fetch_array($respuesta);
		$focosEnUso=$fila[20];
		
		/*START declarar campos nombre foco*/
		$colum1=intval($fila[0]);
		$colum2=intval($fila[1]);
		$colum3=intval($fila[3]);
		$colum4=intval($fila[4]);
		$colum5=intval($fila[5]);
		$colum6=intval($fila[6]);
		$colum7=intval($fila[7]);
		$colum8=intval($fila[8]);
		$colum9=intval($fila[9]);
		$colum10=intval($fila[9]);
		$colum11=intval($fila[10]);
		$colum12=intval($fila[11]);
		$colum13=intval($fila[13]);
		$colum14=intval($fila[13]);
		$colum15=intval($fila[14]);
		$colum16=intval($fila[15]);
		$colum17=intval($fila[16]);
		$colum18=intval($fila[17]);
		$colum19=intval($fila[18]);
		$colum20=intval($fila[19]);
		/*END declarar campos nombre foco*/

		/*START recoger campos tipo foco*/
		$arrayTipoFocosPosiciones[]=$tipoFocoColum1=intval($fila[21]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum2=intval($fila[22]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum3=intval($fila[23]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum4=intval($fila[24]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum5=intval($fila[25]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum6=intval($fila[26]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum7=intval($fila[27]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum8=intval($fila[28]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum9=intval($fila[29]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum10=intval($fila[30]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum11=intval($fila[31]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum12=intval($fila[32]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum13=intval($fila[33]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum14=intval($fila[34]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum15=intval($fila[35]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum16=intval($fila[36]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum17=intval($fila[37]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum18=intval($fila[38]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum19=intval($fila[39]);
		$arrayTipoFocosPosiciones[]=$tipoFocoColum20=intval($fila[40]);
		/*END recoger campos tipo foco*/
		
		/*START recoger campos canales vacios foco*/
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum1=intval($fila[41]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum2=intval($fila[42]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum3=intval($fila[43]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum4=intval($fila[44]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum5=intval($fila[45]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum6=intval($fila[46]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum7=intval($fila[47]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum8=intval($fila[48]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum9=intval($fila[49]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum10=intval($fila[50]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum11=intval($fila[51]);;
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum12=intval($fila[52]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum13=intval($fila[53]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum14=intval($fila[54]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum15=intval($fila[55]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum16=intval($fila[56]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum17=intval($fila[57]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum18=intval($fila[58]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum19=intval($fila[59]);
		$arrayVaciosFocosPosiciones[]=$vaciosFocoColum20=intval($fila[60]);
		/*END recoger campos canales vacios foco*/
		
	}
	mysqli_free_result($respuesta);
	
	///***START pintar campos focos letras
    //for($i=0;$i<=count($arrayTipoFocosPosiciones)-1;$i++){//igual al de abajo, pero dinamico
	for($i=0;$i<$vueltasFor;$i++){
		if(($i % 12)==0){
			if($i>0){
				printf("</div>");
			}
			printf("<div class='form-group row'>");
		}
		
		$contadorDeFocoNumFoco=$i+1;
		
		/*start focos sin uso*/
		$readOnly="";
		$class="";
		if($i>=$focosEnUso){
			$readOnly=" readonly";
			$class=" inputReadOnly";
		}
		/*end focos sin uso*/
		
		/*start huecos vacios*/
		$readOnlyHuecosVacios="";
		$classHuecosVacios="";
		/*if(true){
			$readOnlyHuecosVacios=" readonly";
			$classHuecosVacios=" inputReadOnly";
		}*/
		/*end huecos vacios*/
		
		/*start campo select tipo de foco, campo num canales, dmx vacios y direcciones dmx a esconder*/
		$styleDesplegableCamposFoco="";
		if($_SESSION["permisossession"]!=1){
			$styleDesplegableCamposFoco="style='display:none;'";
		}
		/*end campo select tipo de foco, campo num canales, dmx vacios y direcciones dmx a esconder*/
		
		/*START tipo de foco*/
		printf("<div class='col-md-3' %s>
					<label style='font-weight: bold;'>Tipo Foco %s:</label>%s
				</div>",$styleDesplegableCamposFoco,$contadorDeFocoNumFoco,cargaRefTipoFocosDmx($idPrograma,"_f".$contadorDeFocoNumFoco,$arrayTipoFocosPosiciones[$i],$class,$readOnly,$con));
		/*END tipo de foco*/
		
		/*START canales DMX*/
		printf("<div class='col-md-2' %s>
					<label>Canales DMX %s:</label>%s
				</div>",$styleDesplegableCamposFoco,$contadorDeFocoNumFoco,cargaNumCanalesTipoFocosDmx($idPrograma,"_f".$contadorDeFocoNumFoco,$arrayTipoFocosPosiciones[$i],$con));
		/*END canales DMX*/
		
		/*START campo nombre del foco*/
		printf("<div class='col-md-3'>
					<label style='font-weight: bold;'>Nombre Foco %s:</label><input type='text' maxlength='2' class='form-control%s' name='colum%s' value=\"%s\" placeholder='Luz %s'%s/>
				</div>",$contadorDeFocoNumFoco,$class,$contadorDeFocoNumFoco,$fila[$i],$contadorDeFocoNumFoco,$readOnly);
		/*END campo nombre del foco*/
		
		/*START canales DMX vacios*/
		printf("<div class='col-md-2' %s>
					<label>DMX Vacios Foco %s:</label><input type='number' maxlength='2' class='form-control%s' name='columCanVacios_f%s' id='columCanVacios_f%s' value=\"%s\" placeholder='%s'%s/>
				</div>",$styleDesplegableCamposFoco,$contadorDeFocoNumFoco,$classHuecosVacios,$contadorDeFocoNumFoco,$contadorDeFocoNumFoco,$arrayVaciosFocosPosiciones[$i],0,$readOnlyHuecosVacios);
		/*END canales DMX vacios*/
		
		/*START canales DMX */
        $arrayPosicionesDmxCanal=calcularDireccionDmxSegunConfVaciosFoco(1,$idPrograma,$contadorDeFocoNumFoco-1/*$i*/,0,$con);
        //var_dump($arrayPosicionesDmxCanal);
        $direccionDmxCalculada="[".$arrayPosicionesDmxCanal[0].",".$arrayPosicionesDmxCanal[1]."]";
		printf("<div class='col-md-2' %s>
					<label>Direcciones DMX %s:</label><input type='text' maxlength='2' class='form-control inputReadOnly' name='direccionesDmxFocoLuces%s' value=\"%s\" readonly/>
				</div>",$styleDesplegableCamposFoco,$contadorDeFocoNumFoco,$arrayTipoFocosPosiciones[$i],$direccionDmxCalculada);
		/*END canales DMX*/
	}
	printf("</div>");
	///***END pintar campos focos letras
}


// CARGA horarios luces
function cargaHorariosLucesList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 && $_SESSION["usuarioProgramasLucesList"]!="0"){
		$consulta.=" AND usuarios.id=\"".quitaComillasD($_SESSION["usuarioProgramasLucesList"])."\"";
	}else{
		$consulta.=" AND usuarios.id=\"".calculaIdEmpresa($con)."\"";
	}
	
	$patron="SELECT luces_horarios.id,luces_horarios.nombre,usuarios.id FROM luces_horarios,usuarios WHERE luces_horarios.borrado=\"n\" AND luces_horarios.guardado=\"s\" AND luces_horarios.idusuario=usuarios.id AND usuarios.guardado=\"s\" AND usuarios.borrado=\"n\"%s ORDER BY luces_horarios.nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9633457645627878794567958");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Nodo</th>
					  <th>Usuario</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
	
			//usuario
			$patron1="SELECT nombre FROM usuarios WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$fila[2]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96113334678258");
			$fila1=mysqli_fetch_array($respuesta1);
			mysqli_free_result($respuesta1);
		
			$botonesAcciones="";

			//pulsar tr
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=14&i=%s\");'",$fila[0]);
			
			printf("<tr>
				<td></td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class=''>%s</td>
				</tr>",$funcion,$fila[1],$funcion,"los que sean",$funcion,$fila1[0],$botonesAcciones);
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Nodo</th>
					  <th>Usuario</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}
//carga nodos generico
function cargaNodosHorarioLucesSelect($idUsuario,$idHorario,$nombre,$con){

	$consulta="";
	$consulta=" AND luces_nodos.idusuario=".$idUsuario;
	
	$excluir="";
	$nombreNodo="Sin datos";
	$patron1="SELECT nodo FROM luces_horarios_nodos WHERE horario=\"%s\"";
	$sql1=sprintf($patron1,$idHorario);
	$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 123454545456546756758466");
	if(mysqli_num_rows($respuesta1)>0){
		for($i=0;$i<mysqli_num_rows($respuesta1);$i++){
			$fila1=mysqli_fetch_array($respuesta1);
			
			$excluir=" AND luces_nodos.id<>".$fila1[0];
		}
	}
	mysqli_free_result($respuesta1);
	
	$class="";
	/*if($faltacampo && $seleccionada=="0"){
		$class=" is-invalid";
	}*/
	
	$patron="SELECT luces_nodos.id,luces_nodos.nombre,usuarios.nombre FROM luces_nodos,usuarios WHERE luces_nodos.borrado=\"n\" AND luces_nodos.guardado=\"s\" AND luces_nodos.idusuario=usuarios.id%s";
	$sql=sprintf($patron,$consulta.$excluir);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 123454565458466");
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

//cargar los nodos del horario luces
function cargaNodosHorariosLuces($idHorario,$con){
	$patron="SELECT id,nodo FROM luces_horarios_nodos WHERE horario=\"%s\" ";
	$sql=sprintf($patron,$idHorario);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963244470074586355656555899");
	if(mysqli_num_rows($respuesta)>0){
		
		printf("<thead>
					<tr>
                      <th>#</th>
					  <th>Nodo</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>");
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$nombreNodo="Sin datos";
			$patron1="SELECT luces_nodos.nombre FROM luces_nodos,usuarios WHERE luces_nodos.borrado=\"n\" AND luces_nodos.guardado=\"s\" AND luces_nodos.idusuario=usuarios.id AND luces_nodos.id=\"%s\"";
			$sql1=sprintf($patron1,$fila[1]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 12345456546756758466");
			if(mysqli_num_rows($respuesta1)>0){
				$fila1=mysqli_fetch_array($respuesta1);
				$nombreNodo=$fila1[0];
			}
			
			$botones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm' onClick='cargaLocation(\"index.php?s=9&i=".$fila[1]."\");return false;' title='Ir al nodo'>
						<span class='svg-icon svg-icon-primary svg-icon-2x'><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Navigation\Right-2.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
							<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
								<polygon points='0 0 24 0 24 24 0 24'/>
								<rect fill='#000000' opacity='0.3' transform='translate(8.500000, 12.000000) rotate(-90.000000) translate(-8.500000, -12.000000) ' x='7.5' y='7.5' width='2' height='9' rx='1'/>
								<path d='M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z' fill='#000000' fill-rule='nonzero' transform='translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997) '/>
							</g>
						</svg><!--end::Svg Icon--></span>
					</a>
					<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas 					seguro de que deseas eliminar este registro?\",9,\"".$idHorario."\",\"".$fila[0]."\",\"\");return false;' title='Borrar'>
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
						<td><input type='text' class='form-control inputReadOnly' value='%s' placeholder='Nodo' readonly/></td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$nombreNodo,$botones);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
                    <tr>
                        <th>#</th>
                        <th>Nodo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>');
	}
}

//carga programas generico
function cargaProgramasLuces($idUsuario,$idHorario,$nombre,$con){

	$consulta=" AND luces_programas.idusuario=".$idUsuario;
	
	$class="";
	/*if($faltacampo && $seleccionada=="0"){
		$class=" is-invalid";
	}*/
	
	$patron="SELECT luces_programas.id,luces_programas.nombre,usuarios.nombre FROM luces_programas,usuarios WHERE luces_programas.borrado=\"n\" AND luces_programas.guardado=\"s\" AND luces_programas.idusuario=usuarios.id%s";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 12345445656745565458466");
	printf("<select class='form-control%s' name=\"%s\" id=\"%s\" >",$class,$nombre,$nombre);
	printf("<option value='0'>Selecciona Programa</option>");
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

//configuracion horario luces programas
function horariosLucesConf($idHorario,$con){
	
	printf('<thead>
				<tr>
				  <th>#</th>
				  <th class="sorting_disabled">Programa</th>
				  <th>Día Semana</th>
				  <th>Desde</th>
				  <th>Hasta</th>
				  <th>Acciones</th>
				</tr>
			</thead>
			<tbody>');
	
	for($j=1;$j<=7;$j++){
	
		$patron="SELECT id,programa,diasemana,horade,horahasta FROM luces_horarios_programas_conf WHERE horario=\"%s\" AND diasemana=\"%s\" ORDER BY diasemana ASC, id ASC";
		$sql=sprintf($patron,$idHorario,$j);
		$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323454345345899");
		if(mysqli_num_rows($respuesta)>0){
			
			for($i=0;$i<mysqli_num_rows($respuesta);$i++){
				$fila=mysqli_fetch_array($respuesta);

				//saber programa
				$nombrePrograma="Sin datos";
				$patron1="SELECT nombre FROM luces_programas WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$fila[1]);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963234543465345345899");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					$nombrePrograma=$fila1[0];
				}

				//saber dia semana
				$nombreDia="Sin datos";
				$patron2="SELECT dia FROM semana WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$fila[2]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 96324543454345345899");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$nombreDia=$fila2[0];
				}

				$botones="<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas 					seguro de que deseas eliminar este registro?\",11,\"".$idHorario."\",\"".$fila[0]."\",\"\");return false;' title='Borrar'>
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

				//pintar o no tr
				//if($diaSemana!=$fila[2]){
					printf("<tr>
								<td></td>
								<td colspan='5' style='text-align: center;font-weight: bold;background-color: #f5f5f5;'>%s</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>",$nombreDia);
					//$diaSemana=$fila[2];
				//}

				printf("<tr>
							<td></td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td nowrap='nowrap'>%s</td>
						</tr>",$nombrePrograma,$nombreDia,substr($fila[3],0,5)." h",substr($fila[4],0,5)." h",$botones);
			}
			mysqli_free_result($respuesta);
		}else{
			
			//saber dia semana
			$nombreDia="Sin datos";
			$patron3="SELECT dia FROM semana WHERE id=\"%s\"";
			$sql3=sprintf($patron3,$j);
			$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 96324543454333345345899");
			if(mysqli_num_rows($respuesta3)>0){
				$fila3=mysqli_fetch_array($respuesta3);
				$nombreDia=$fila3[0];
			}
			mysqli_free_result($respuesta3);
			printf("<tr>
						<td></td>
						<td colspan='5' style='text-align: center;font-weight: bold;background-color: #f5f5f5;'>%s</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>",$nombreDia);
		}
	}
	echo "</tbody>";
}
//comprobar solapamiento horario luces nodos
function comprobarSolapamientoHoraioLucesNodos($horario,$nodo,$horaDe,$horaHasta,$diaSemana,$opcion,$con){
	$correcto=true;
	
	switch($opcion){
		case 1://tabla de horas anadir horario
			if($horaDe<$horaHasta && $horaHasta>"00:01:00" && $diaSemana>0 && $diaSemana<8 && $horario>0){
				$patron1="SELECT nodo FROM luces_horarios_nodos WHERE horario=\"%s\"";
				$sql1=sprintf($patron1,$horario);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96323145434153145899");
				if(mysqli_num_rows($respuesta1)>0){
					for($i=0;$i<mysqli_num_rows($respuesta1);$i++){
						$fila1=mysqli_fetch_array($respuesta1);

						//v1,// mal o incompleta
						/*
						$patron="SELECT luces_horarios_programas_conf.id FROM luces_horarios,luces_horarios_nodos,luces_horarios_programas_conf WHERE luces_horarios.id=luces_horarios_nodos.horario AND luces_horarios.id=luces_horarios_programas_conf.horario AND luces_horarios.borrado=\"n\" AND luces_horarios.guardado=\"s\" AND luces_horarios_nodos.nodo=\"%s\" AND (luces_horarios_programas_conf.horade BETWEEN (\"%s\") AND (\"%s\") OR luces_horarios_programas_conf.horahasta BETWEEN (\"%s\") AND (\"%s\")) AND luces_horarios_programas_conf.diasemana=\"%s\"";
						$sql=sprintf($patron,$fila1[0],$horaDe,$horaHasta,$horaDe,$horaHasta,$diaSemana);
						*/
						
						//v2, //parece buena
						$patron="SELECT luces_horarios_programas_conf.id FROM luces_horarios,luces_horarios_nodos,luces_horarios_programas_conf WHERE luces_horarios.id=luces_horarios_nodos.horario AND luces_horarios.id=luces_horarios_programas_conf.horario AND luces_horarios.borrado=\"n\" AND luces_horarios.guardado=\"s\" AND luces_horarios_nodos.nodo=\"%s\" AND ((luces_horarios_programas_conf.horade<=\"%s\" AND luces_horarios_programas_conf.horahasta>=\"%s\") OR (luces_horarios_programas_conf.horade<=\"%s\" AND luces_horarios_programas_conf.horahasta>=\"%s\")) AND luces_horarios_programas_conf.diasemana=\"%s\"";
						$sql=sprintf($patron,$fila1[0],$horaDe,$horaDe,$horaHasta,$horaHasta,$diaSemana);
						
						//ejecutarConsulta
						$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96324546676763454333345345899");
						if(mysqli_num_rows($respuesta)>0){
							//$fila=mysqli_fetch_array($respuesta);
							$correcto=false;//echo "pillado";
							break;//no puede meter esa linea ya que afecta a mas nodos
						}else{
							$correcto=true;//echo "libre";
						}
						mysqli_free_result($respuesta);
					}
				}else{
					$correcto=true;
				}
				mysqli_free_result($respuesta1);
			}else{
				$correcto=false;
			}
		break;
		case 2://tabla anadir nodos horario
			if($horario>0 && $nodo>0){
				$patron="SELECT id,diasemana,horade,horahasta FROM luces_horarios_programas_conf WHERE horario=\"%s\" ";
				$sql=sprintf($patron,$horario);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323353454556784345345899");
				if(mysqli_num_rows($respuesta)>0){
					for($i=0;$i<mysqli_num_rows($respuesta);$i++){
						$fila=mysqli_fetch_array($respuesta);
						
						//v1,// mal o incompleta
						/*$patron1="SELECT luces_horarios_programas_conf.id FROM luces_horarios,luces_horarios_nodos,luces_horarios_programas_conf WHERE luces_horarios.id=luces_horarios_nodos.horario AND luces_horarios.id=luces_horarios_programas_conf.horario AND luces_horarios.borrado=\"n\" AND luces_horarios.guardado=\"s\" AND luces_horarios_nodos.nodo=\"%s\" AND (luces_horarios_programas_conf.horade BETWEEN (\"%s\") AND (\"%s\") OR luces_horarios_programas_conf.horahasta BETWEEN (\"%s\") AND (\"%s\")) AND luces_horarios_programas_conf.diasemana=\"%s\" AND luces_horarios_programas_conf.id<>\"%s\"";
						$sql1=sprintf($patron1,$nodo,$fila[2],$fila[3],$fila[2],$fila[3],$fila[1],$fila[0]);*/
						
						//v2, //parece buena
						$patron1="SELECT luces_horarios_programas_conf.id FROM luces_horarios,luces_horarios_nodos,luces_horarios_programas_conf WHERE luces_horarios.id=luces_horarios_nodos.horario AND luces_horarios.id=luces_horarios_programas_conf.horario AND luces_horarios.borrado=\"n\" AND luces_horarios.guardado=\"s\" AND luces_horarios_nodos.nodo=\"%s\" AND ((luces_horarios_programas_conf.horade<=\"%s\" AND luces_horarios_programas_conf.horahasta>=\"%s\") OR (luces_horarios_programas_conf.horade<=\"%s\" AND luces_horarios_programas_conf.horahasta>=\"%s\")) AND luces_horarios_programas_conf.diasemana=\"%s\" AND luces_horarios_programas_conf.id<>\"%s\"";
						$sql1=sprintf($patron1,$nodo,$fila[2],$fila[2],$fila[3],$fila[3],$fila[1],$fila[0]);
						
						//ejecutarConsulta
						$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96324546617672354634541333345345899");
						if(mysqli_num_rows($respuesta1)>0){
							//$fila1=mysqli_fetch_array($respuesta1);
							$correcto=false;//echo "pillado";
							break;//no puede meter esa linea ya que afecta a mas nodos
						}else{
							$correcto=true;//echo "libre";
						}
						mysqli_free_result($respuesta1);
					}
				}else{
					$correcto=true;//echo "libre";
				}
				mysqli_free_result($respuesta);
			}else{
				$correcto=false;
			}
		break;
		default:
			$correcto=false;
		break;
	}
	return $correcto;
}

//configuracion resumen nodo horarios luces programas 
function nodoHorariosLucesConf($idNodo,$con){
	printf('<thead>
				<tr>
				  <th>#</th>
				  <th>Programa/Horario</th>
				  <th>Día Semana</th>
				  <th>Desde</th>
				  <th>Hasta</th>
				  <th>Acciones</th>
				</tr>
			</thead>
			<tbody>');
	$diaSemana=0;
	for($j=1;$j<=7;$j++){
		$patron="SELECT luces_horarios_programas_conf.id,luces_horarios_programas_conf.programa,luces_horarios_programas_conf.diasemana,luces_horarios_programas_conf.horade,luces_horarios_programas_conf.horahasta,luces_horarios_programas_conf.horario FROM luces_horarios_programas_conf,luces_horarios,luces_horarios_nodos WHERE luces_horarios.id=luces_horarios_programas_conf.horario AND luces_horarios.id=luces_horarios_nodos.horario AND luces_horarios.borrado=\"n\" AND luces_horarios.guardado=\"s\" AND luces_horarios_programas_conf.diasemana=\"%s\" AND luces_horarios_nodos.nodo=\"%s\" ORDER BY luces_horarios_programas_conf.programa ASC, luces_horarios_programas_conf.id ASC";
		$sql=sprintf($patron,$j,$idNodo);
		$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323454345345899");
		if(mysqli_num_rows($respuesta)>0){
			for($i=0;$i<mysqli_num_rows($respuesta);$i++){
				$fila=mysqli_fetch_array($respuesta);

				//saber programa
				$nombrePrograma="Sin datos";
				$patron1="SELECT nombre FROM luces_programas WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$fila[1]);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963234543465345345899");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					$nombrePrograma=$fila1[0];
				}

				//saber dia semana
				$nombreDia="Sin datos";
				$patron2="SELECT dia FROM semana WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$fila[2]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 96324543454345345899");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$nombreDia=$fila2[0];
				}
				
				//saber nombre horario
				$nombreHorario="Sin datos";
				$patron4="SELECT nombre FROM luces_horarios WHERE id=\"%s\"";
				$sql4=sprintf($patron4,$fila[5]);
				$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 96324454434543454345899");
				if(mysqli_num_rows($respuesta4)>0){
					$fila4=mysqli_fetch_array($respuesta4);
					$nombreHorario=$fila4[0];
				}

				$botones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm' onClick='cargaLocation(\"index.php?s=14&i=".$fila[5]."\");return false;' title='Ir al horario'>
								<span class='svg-icon svg-icon-primary svg-icon-2x'><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Navigation\Right-2.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
									<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
										<polygon points='0 0 24 0 24 24 0 24'/>
										<rect fill='#000000' opacity='0.3' transform='translate(8.500000, 12.000000) rotate(-90.000000) translate(-8.500000, -12.000000) ' x='7.5' y='7.5' width='2' height='9' rx='1'/>
										<path d='M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z' fill='#000000' fill-rule='nonzero' transform='translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997) '/>
									</g>
								</svg><!--end::Svg Icon--></span>
							</a>";

				//pintar o no tr
				if($diaSemana!=$fila[2]){
					printf("<tr>
								<td></td>
								<td colspan='5' style='text-align: center;font-weight: bold;background-color: #f5f5f5;'>%s</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>",$nombreDia);
					$diaSemana=$fila[2];
				}

				printf("<tr>
							<td></td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td nowrap='nowrap'>%s</td>
						</tr>",$nombrePrograma." (".$nombreHorario.")",$nombreDia,substr($fila[3],0,5)." h",substr($fila[4],0,5)." h",$botones);
			}
		}else{

			//saber dia semana
			$nombreDia="Sin datos";
			$patron3="SELECT dia FROM semana WHERE id=\"%s\"";
			$sql3=sprintf($patron3,$j);
			$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 96324543454333345345899");
			if(mysqli_num_rows($respuesta3)>0){
				$fila3=mysqli_fetch_array($respuesta3);
				$nombreDia=$fila3[0];
			}
			mysqli_free_result($respuesta3);
			printf("<tr>
						<td></td>
						<td colspan='5' style='text-align: center;font-weight: bold;background-color: #f5f5f5;'>%s</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>",$nombreDia);
		}
		mysqli_free_result($respuesta);
	}//cierro for 7 dias
	
	echo "</tbody>";
}

//buscar el inicio y el fin de programas del nodo, para meter en bbdd del nodo
function inicioFinActividadNodoLuz($idHorario,$con){
	if(is_numeric($idHorario)){
		
		$consulta="";
		if($_SESSION["permisossession"]==1){
			$consulta="";//ver y tocar el que sea
		}else{
			$consulta.=" AND luces_horarios.idusuario=\"".calculaIdEmpresa($con)."\"";//forzar a suyo
		}
		
		//recorro los nodos del horario
		$patron3="SELECT luces_horarios_nodos.nodo FROM luces_horarios,luces_horarios_nodos WHERE luces_horarios.borrado=\"n\" AND luces_horarios.guardado=\"s\" AND luces_horarios.id=luces_horarios_nodos.horario AND luces_horarios.id=\"%s\"%s";
		$sql3=sprintf($patron3,$idHorario,$consulta);
		$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 96333234546676345345899");
		if(mysqli_num_rows($respuesta3)>0){
			for($k=0;$k<mysqli_num_rows($respuesta3);$k++){
				$fila3=mysqli_fetch_array($respuesta3);

				//inicializar
				$horaInicioMin="24:00:00";//para forzar a coger mas pequeno
				$horaFinMax="00:00:00";//para forzar a coger el mas grande
				$arrayDiasSemanaProgramas=array();
				$actualizar=false;
				$idNodo=$fila3[0];	

				//miro en los horarios que participa
				$patron="SELECT DISTINCT(luces_horarios_nodos.horario) FROM luces_nodos,luces_horarios_nodos WHERE luces_nodos.borrado=\"n\" AND luces_nodos.guardado=\"s\" AND luces_nodos.id=luces_horarios_nodos.nodo AND luces_nodos.id=\"%s\"";
				$sql=sprintf($patron,$idNodo);
				$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963234546676345345899");
				if(mysqli_num_rows($respuesta)>0){
					for($i=0;$i<mysqli_num_rows($respuesta);$i++){
						$fila=mysqli_fetch_array($respuesta);
						//recorro la configuracion de ese horario
						$patron1="SELECT id,horade,horahasta,diasemana FROM luces_horarios_programas_conf WHERE horario=\"%s\" ORDER BY diasemana ASC";
						$sql1=sprintf($patron1,$fila[0]);
						$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963234543446545345899");
						if(mysqli_num_rows($respuesta1)>0){
							$actualizar=true;
							for($j=0;$j<mysqli_num_rows($respuesta1);$j++){
								$fila1=mysqli_fetch_array($respuesta1);
								if(strtotime($fila1[1])<strtotime($horaInicioMin)){
									$horaInicioMin=$fila1[1];
								}
								if(strtotime($fila1[2])>strtotime($horaFinMax)){
									$horaFinMax=$fila1[2];
								}
								if(!in_array($fila1[3],$arrayDiasSemanaProgramas)){
									array_push($arrayDiasSemanaProgramas, intval($fila1[3]));
								}
							}
						}
						mysqli_free_result($respuesta1);
					}
				}
				mysqli_free_result($respuesta);

				//pintar arreglo dias
				$diasAjustesString="0,1,2,3,4,5,6";//poner esto por defecto
				/*sort($arrayDiasSemanaProgramas);
				if(count($arrayDiasSemanaProgramas)>0){
					for($x=0;$x<count($arrayDiasSemanaProgramas);$x++){
						$diasAjustesString.=$arrayDiasSemanaProgramas[$x]-1;//resto uno empieza el lunes 0 acaba domingo 6
						if($x<count($arrayDiasSemanaProgramas)-1){
							$diasAjustesString.=",";
						}
					}
				}*/

				if(!$actualizar){//si es false lo pongo a esto por defecto
					$schedule="{\"init\":\"00:00:00\",\"finish\":\"00:00:00\",\"days\":[0,1,2,3,4,5,6]}";//por defecto
					$actualizar=true;
				}

				//update del schedule
				if($actualizar && is_numeric($idNodo)){

					$schedule="{\"init\":\"".$horaInicioMin."\",\"finish\":\"".$horaFinMax."\",\"days\":[".$diasAjustesString."]}";

					$patron2="UPDATE luces_nodos SET schedule='%s' WHERE id=\"%s\"";
					$sql2=sprintf($patron2,$schedule,$idNodo);
					$respuesta2=mysqli_query($con,$sql2) or die ("Error al update 15675672345634789");
				}
			}
		}
		mysqli_free_result($respuesta3);
	}
}

//function para generar en el programa la sequence
function generarSequencePrograma($idPrograma,$idNodo,$con){
	
	$sequence="";
	//ejemplo
	//$sequence='{"sequences":[{"on":[1,109,4,64,7,67,10,70,13,73,16,76,49,79,22,82,26,86,27,28,87,88,32,92,35,95,38,98,41,101,44,104,45,46,105,106],"delay":15000}]}';
	
	$consulta="";
	/*if($_SESSION["permisossession"]==1){
		$consulta="";//ver y tocar el que sea
	}else{
		//sin uso porque donde lo uso en la api de luces solo viene el internal, no hay nada mas ni sesiones ni id, no hay manera de saber
		//hay que fiarse del internal
		//$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";//forzar a suyo
	}*/
	
	//saber los canales del nodo por cada salida
	$canalesPorSalida=3;
	$patron3="SELECT id,canalesporsalida FROM luces_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"%s";
	$sql3=sprintf($patron3,$idNodo,$consulta);
	$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 963233454663764656345334582299");
	if(mysqli_num_rows($respuesta3)>0){
		$fila3=mysqli_fetch_array($respuesta3);
		$canalesPorSalida=$fila3[1];
	}
	
	//montar json degun canales por salida del nodo y conf de colores
	$patron="SELECT id,numfocos FROM luces_programas WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"%s";
	$sql=sprintf($patron,$idPrograma,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963234546676465634534582299");
	if(mysqli_num_rows($respuesta)>0){
		$fila=mysqli_fetch_array($respuesta);
		
		$patron1="SELECT colorcolumuno,colorcolumdos,colorcolumtres,colorcolumcuatro,colorcolumcinco,colorcolumseis,colorcolumsiete,colorcolumocho,colorcolumnueve,colorcolumdiez,colorcolumonce,colorcolumdoce,colorcolumtrece,colorcolumcatorce,colorcolumquince,colorcolumdieciseis,colorcolumdiecisiete,colorcolumdieciocho,colorcolumdiecinueve,colorcolumveinte,temporizacion FROM luces_filasprograma WHERE borrado=\"n\" AND programa=\"%s\" ORDER BY id ASC";
		$sql1=sprintf($patron1,$fila[0]);
		$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9631234546161763451345899");
		if(mysqli_num_rows($respuesta1)>0){
			
			$sequence.='{"sequences":[';
			for($i=0;$i<mysqli_num_rows($respuesta1);$i++){
				$fila1=mysqli_fetch_array($respuesta1);
				
				//aqui gestionar los canales
				//de 3 canales por salida, empieza en el cero?? ..... //1-3 // 4-6 //  7-9  //  10-12 //   13-15  //   16-18 ....
				//de 4 canales por salida, ..... //1-4 // 5-8 //  9-12  //  13-16 //   17-20  //   21-24 .....
				//de 6 canales por salida, ..... //1-6 // 7-12 //  13-18  //  19-24 //   25-30  //   31-36 ....
				//de 8 canales por salida, ..... //1-8 // 9-16 //  17-24  //  25-32 //   33-40  //   41-48 ....
				//de 10 canales por salida, ..... //1-10 // 11-20 //  21-30  //  31-40 //   41-50  //   51-60 ....
				
				$canales="";
				$numFocosNumColumnas=20/*$fila[1]*/;//recorrer las 20 columnas
				$canalEmpiezaEnceroUno=0;
				for($k=0;$k<$numFocosNumColumnas;$k++){
					$salidaColumna=$k;
					//gestion de los 8 posibles colores, para el led o le foco o lo que aplique
					switch($fila1[$k]){
						case 1://led azul
							//$canales.="2,";
							$canales.=(($canalesPorSalida*$salidaColumna)+(2+$canalEmpiezaEnceroUno)).",";
						break;
						case 2://led rojo
							//$canales.="0,";
							$canales.=(($canalesPorSalida*$salidaColumna)+(0+$canalEmpiezaEnceroUno)).",";
						break;
						case 3://led amarillo
							//$canales.="0,1,";//encender rojo y verde para formar amarillo
							$canales.=(($canalesPorSalida*$salidaColumna)+(0+$canalEmpiezaEnceroUno)).",".(($canalesPorSalida*$salidaColumna)+(1+$canalEmpiezaEnceroUno)).",";
						break;
						case 4://led morado
							//$canales.="0,2,";//encender rojo y azul para formar morado
							$canales.=(($canalesPorSalida*$salidaColumna)+(0+$canalEmpiezaEnceroUno)).",".(($canalesPorSalida*$salidaColumna)+(2+$canalEmpiezaEnceroUno)).",";
						break;
						case 5://led verde
							//$canales.="1,";
							$canales.=(($canalesPorSalida*$salidaColumna)+(1+$canalEmpiezaEnceroUno)).",";
						break;
						case 6://led cian
							//$canales.="1,2,";
							$canales.=(($canalesPorSalida*$salidaColumna)+(1+$canalEmpiezaEnceroUno)).",".(($canalesPorSalida*$salidaColumna)+(2+$canalEmpiezaEnceroUno)).",";
						break;
						case 7://led blanco, icono, pero color blanco
							if($canalesPorSalida==3){
								//$canales.="0,1,2,";//activando las 3 sale blanco
								$canales.=(($canalesPorSalida*$salidaColumna)+(0+$canalEmpiezaEnceroUno)).",".(($canalesPorSalida*$salidaColumna)+(1+$canalEmpiezaEnceroUno)).",".(($canalesPorSalida*$salidaColumna)+(2+$canalEmpiezaEnceroUno)).",";
							}else if($canalesPorSalida==4){
								//sin info todavia-sin uso
							}else if($canalesPorSalida==6){
								//$canales.="3,";//si el dmx es de 6 canales por salida la 4 es blanco, si empieza en el 0 seria la 3 si no la 4
								$canales.=(($canalesPorSalida*$salidaColumna)+(3+$canalEmpiezaEnceroUno)).",";
							}else if($canalesPorSalida==8){
								//sin info todavia-sin uso
							}else if($canalesPorSalida==10){
								//$canales.="10,";//si el dmx es de 10 canales por salida la 4 es blanco, si empieza en el 0 seria la 3 si no la 4
								$canales.=(($canalesPorSalida*$salidaColumna)+(3+$canalEmpiezaEnceroUno)).",";
							}
						break;
						case 8://led apagado
							$canales.="";//salto de xxx canales de esa salida
						break;
					}
				}
				$canales=substr($canales, 0, -1);//quitar ultima coma
				
				//obtener delay
				$delay=intval(1000);
				$patron4="SELECT tiempomilisegundos FROM temporizacion WHERE id=\"%s\"";
				$sql4=sprintf($patron4,$fila1[20]);
				$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 96324433454663764656345334582299");
				if(mysqli_num_rows($respuesta4)>0){
					$fila4=mysqli_fetch_array($respuesta4);
					$delay=intval($fila4[0]);
				}
				mysqli_free_result($respuesta4);
				$sequence.="{\"on\":[".$canales."],\"delay\":".$delay."},";//
				
			}//cierro for filas programas
			$sequence=substr($sequence, 0, -1);//quitar ultima coma
			
			$created=date("Y-m-d H:i:s");
			$sequence.="],\"created\": \"".$created."\"}";
		}//cierro if filas programas
		
		//update del campo squence del nodo en si, del programa en uso
		$patron9="UPDATE luces_nodos SET sequence='%s' WHERE id=\"%s\"";
		$sql9=sprintf($patron9,$sequence,$idNodo);
		$respuesta9=mysqli_query($con,$sql9) or die ("Error al update 156756972345636784999789");
	}
	mysqli_free_result($respuesta);
	
	return $sequence;
}

//carga usuarios generico
function cargaCanalesPorSalida($seleccionada,$nombre,$faltacampo,$con){

	$class="";
	if($faltacampo && $seleccionada=="0"){
		$class=" is-invalid";
	}
	
	$selectedTres="";
	$selectedCuatro="";
	$selectedSeis="";
	$selectedOcho="";
	$selectedDiez="";
	if($seleccionada==3){
		$selectedTres=" selected='selected'";
	}else if($seleccionada==4){
		$selectedCuatro=" selected='selected'";
	}else if($seleccionada==6){
		$selectedSeis=" selected='selected'";
	}else if($seleccionada==8){
		$selectedOcho=" selected='selected'";
	}else if($seleccionada==10){
		$selectedOcho=" selected='selected'";
	}
	
	printf("<select class='form-control%s' name=\"%s\" id=\"%s\" >",$class,$nombre,$nombre);
	//printf("<option value='0'>Selecciona Canales</option>");
	printf("<option value='3'%s>3 canales</option>",$selectedTres);
	printf("<option value='4'%s>4 canales</option>",$selectedCuatro);
	printf("<option value='6'%s>6 canales</option>",$selectedSeis);
	printf("<option value='8'%s>8 canales</option>",$selectedOcho);
	printf("<option value='10'%s>10 canales</option>",$seleselectedDiezctedOcho);
	printf("</select>");
}

//filtro clientes horarios luces list
function cargaUsuariosHorariosFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre ASC, apellidos ASC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 31215656456655875565778456887776746456455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosHorariosLuces(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioProgramasLucesList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1].", ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//poner online offline, segun ultima conexion
function ultimaConexionOnlinOfflineLuces($con){
	$patron="SELECT id,ultimaconexion,horaultimaconexion FROM luces_nodos WHERE borrado=\"n\" AND guardado=\"s\"";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323434334356363456634542355899");
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

			$patron1="UPDATE luces_nodos SET conexion=\"%s\" WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$estado,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 12345634121234431789");
		}
		
	}
	mysqli_free_result($respuesta);
}

//configuracion programa luces, generico, varios DMX y varios focos
function configuracionProgramaLucesGenerico($con){
	
	$patron="SELECT id,referencia,canales,marca,modelo,direcciondmxrojo,direcciondmxverde,direcciondmxazul,direcciondmxblancocalido,direcciondmxblancofrio,direcciondmxstrobe,direcciondmxsped,direcciondmxdimer,direcciondmxfun,direcciondmxuv FROM luces_referenciafocos WHERE borrado=\"n\" ORDER BY id ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963257890899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>REF</th>
					  <th>Canales</th>
					  <th>Marca</th>
					  <th>Modelo</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
		
			/*START boton editar*/
			$botonEditar="";
			$botonEditar="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm mx-3' onClick='editarConfigTipoFoco(".$fila[0].");return false;' title='Guardar cambios'>
						<span class='svg-icon svg-icon-md svg-icon-success'>
							<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<polygon points='0 0 24 0 24 24 0 24'/>
									<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
									<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
								</g>
							</svg><!--end::Svg Icon-->
						</span>
					</a>";
			/*END boton editar*/
            
            /*start abrir modal configurar foco*/
            $botonAbrirModalConfFoco="";
            $botonAbrirModalConfFoco="<a href='#' class='btn btn-icon btn-light btn-hover-primary btn-sm mx-3' data-toggle='modal' onClick='abrirModalConfiguracionFocoCanales(".$fila[0].");return false;' title='Configurar foco'>
						<span class='svg-icon svg-icon-primary svg-icon-2x'>
							<svg xmlns='http://www.w3.org/2000/svg' height='16' width='16' viewBox='0 0 512 512'><path fill='#B197FC' d='M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z'/></svg>
						</span>
					</a>";
            /*end abrir modal configurar foco*/
			
			//boton eliminar
			$botonEliminar="";
			$botonEliminar="<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar tipo Foco\",\"¿Estas seguro de que deseas eliminar este tipo de Foco?\",39,\"".$fila[0]."\",\"\",\"\");return false;'>
							<span class='svg-icon svg-icon-md svg-icon-danger'>
								<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
									<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
										<rect x='0' y='0' width='24' height='24'></rect>
										<path d='M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z' fill='#000000' fill-rule='nonzero'></path>
										<path d='M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z' fill='#000000' opacity='0.3'></path>
									</g>
								</svg>
							</span>
						</a>";
			
			//pintar
			printf("<tr>
						<td></td>
						<td><input type='text' class='form-control' id='refTipoFocoLuz%s' value='%s'/></td>
						<td>%s</td>
						<td><input type='text' class='form-control' id='marcaTipoFocoLuz%s' value='%s'/></td>
						<td><input type='text' class='form-control' id='modeloTipoFocoLuz%s' value='%s'/></td>
						<td nowrap='nowrap'>%s%s%s</td>
					</tr>",$fila[0],$fila[1],cargaDireccionDmxFuncionalidades($fila[0],$fila[2],1,$con),$fila[0],$fila[3],$fila[0],$fila[4],$botonEditar,$botonAbrirModalConfFoco,$botonEliminar);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>REF</th>
					  <th>Canales</th>
					  <th>Marca</th>
					  <th>Modelo</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}

//carga direccion DMX de cada funcionalidad de configuracion de focos
function cargaDireccionDmxFuncionalidades($idTipoFocoDMX,$seleccionado,$seccion,$con){
	
	/*
	seccion 1-> llamado desde function return $dev;
			2-> llamado desde html devolver con printf
	*/
	
	$numeroCanales=10;//10 por ahora
	
    $dev="";
	
    $class="";
    /*if($idLin>0){
       $class=" inputReadOnly"; 
    }*/
    
	if($seccion==1){//tabla editar
		$dev.=sprintf("<select class='form-control%s' id='canalesTipoFocoLuz%s' ><option value='0'>Número de Canales:</option>",$class,$idTipoFocoDMX);
	}else if($seccion==2){//web, insert
		printf("<select class='form-control%s' id='canalesTipoFocoLuz%s' ><option value='0'>Número de Canales:</option>",$class,$idTipoFocoDMX);
	}
		/*start bucle de canales*/
		for($i=3;$i<=$numeroCanales;$i++){//10 por ahora
			
			$selected="";
			if($i==$seleccionado){
				$selected=" selected";
			}
			
			if($seccion==1){
				$dev.=sprintf("<option value=\"%s\" %s>%s</option>",$i,$selected,$i);
			}else if($seccion==2){
				/*if($i==10){
					$selected=" selected";
				}*/
				printf("<option value=\"%s\" %s>%s</option>",$i,$selected,$i);
			}
		}
		/*end bucle de canales*/
	if($seccion==1){
		$dev.=sprintf('</select>');
    	return $dev;
	}else if($seccion==2){
		printf('</select>');
	}
}

//function para indicar el numero de la direccion DMX de cada funcionalidad
function cargaDireccionesDmxTipoFocos($tipoFocoDmx,$name,$idTipoFuncionalidad,$seleccionado,$con){
	
	/*
	- cada foco puede tener desde 3 canales a xx canales por ahora 10, cada direccion dmx empieza en el 1 hasta xx por ahora 10
	- entonces cada canal tiene varias funcionalidades
	- cada funcionaldiad esta en una DIRECCION DMX
	- como mejora excluir las direcciones DMX YA USADAS?????¿??
	*/
	
	/*tipo funcionalidades, creadas en bbdd, 10 por ahora
	--> direcciondmxrojo - 1
	--> direcciondmxverde - 2
	--> direcciondmxazul - 3
	--> direcciondmxblancocalido - 4
	--> direcciondmxblancofrio - 5
	--> direcciondmxstrobe - 6
	--> direcciondmxsped - 7
	--> direcciondmxdimer - 8
	--> direcciondmxfun - 9
	--> direcciondmxuv - 10
	*/
	
	
	/*start saber el campo que estamos trabajando*/
	$idFuncionalidad=0;
	$campoNumbreFuncionalidad="";
	$patron1="SELECT funcionalidad,id FROM luces_funcionalidades WHERE id=\"%s\"";
	$sql1=sprintf($patron1,$idTipoFuncionalidad);
	$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 96334543534567457341534562345655178960899");
	if(mysqli_num_rows($respuesta1)>0){
		$fila1=mysqli_fetch_array($respuesta1);
		$campoNumbreFuncionalidad=$fila1[0];
		$idFuncionalidad=$fila1[1];
	}
	mysqli_free_result($respuesta1);
	/*end saber el campo que estamos trabajando*/
	
	$numeroFuncionalidadesPorCanal=10;//10 por ahora
	
	$dev="";
    $class="";
    /*if($idLin>0){
       $class=" inputReadOnly"; 
    }*/
    
	/*START exlcuir direcciones DMX ya usadas*/
	$excluirDireccionesDmxUsadas=array();
	if($tipoFocoDmx>0){
		$patron="SELECT direcciondmxrojo,direcciondmxverde,direcciondmxazul,direcciondmxblancocalido,direcciondmxblancofrio,direcciondmxstrobe,direcciondmxsped,direcciondmxdimer,direcciondmxfun,direcciondmxuv FROM luces_referenciafocos WHERE borrado=\"n\" AND id=\"%s\"";
		$sql=sprintf($patron,$tipoFocoDmx);
		$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323466865757890899");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);

			$direccionDmxRojo=intval($fila[0]);
			$direccionDmxVerde=intval($fila[1]);
			$direccionDmxAzul=intval($fila[2]);
			$direccionDmxBlancocalido=intval($fila[3]);
			$direccionDmxBlancofrio=intval($fila[4]);
			$direccionDmxStrobe=intval($fila[5]);
			$direccionDmxSped=intval($fila[6]);
			$direccionDmxDimer=intval($fila[7]);
			$direccionDmxFun=intval($fila[8]);
			$direccionDmxUv=intval($fila[9]);

			if($direccionDmxRojo>0){
				array_push($excluirDireccionesDmxUsadas,$direccionDmxRojo);
			}
			if($direccionDmxVerde>0){
				array_push($excluirDireccionesDmxUsadas,$direccionDmxVerde);
			}
			if($direccionDmxAzul>0){
				array_push($excluirDireccionesDmxUsadas,$direccionDmxAzul);
			}
			if($direccionDmxBlancocalido>0){
				array_push($excluirDireccionesDmxUsadas,$direccionDmxBlancocalido);
			}
			if($direccionDmxBlancofrio>0){
				array_push($excluirDireccionesDmxUsadas,$direccionDmxBlancofrio);
			}
			if($direccionDmxStrobe>0){
				array_push($excluirDireccionesDmxUsadas,$direccionDmxStrobe);
			}
			if($direccionDmxSped>0){
				array_push($excluirDireccionesDmxUsadas,$direccionDmxSped);
			}
			if($direccionDmxDimer>0){
				array_push($excluirDireccionesDmxUsadas,$direccionDmxDimer);
			}
			if($direccionDmxFun>0){
				array_push($excluirDireccionesDmxUsadas,$direccionDmxFun);
			}
			if($direccionDmxUv>0){
				array_push($excluirDireccionesDmxUsadas,$direccionDmxUv);
			}
		}
		mysqli_free_result($respuesta);
	}
	/*END exlcuir direcciones DMX ya usadas*/
	
	$dev.=sprintf("<select class='form-control%s' id='funcionalidadesCanalesTipoFocoDmx%s_%s' onChange='actualizarDireccionDmxTipoFoco(".$tipoFocoDmx.",".$idFuncionalidad.",this)'><option value='-99'>Dirección DMX:</option>",$class,$tipoFocoDmx,$idFuncionalidad);
	
		/*start bucle de direccion DMX de cada funcionalidad o canales*/
		for($i=1;$i<=$numeroFuncionalidadesPorCanal;$i++){
			$selected="";
			
			if (in_array($i, $excluirDireccionesDmxUsadas) && $seleccionado!=$i) {
				//ya esta usada, no pintar esa direccion dmx
				//$dev.=sprintf("<option value=\"%s\">Usadas quitar: %s</option>",$i,$i);
			}else{
				if($i==$seleccionado){
					$selected=" selected";
				}

				$dev.=sprintf("<option value=\"%s\" %s>%s</option>",$i,$selected,$i);
			}
		}
		/*end bucle de canales*/
	
	$dev.=sprintf('</select>');
    return $dev;
}

//guardar y comprobar la direccion dmx de cada funcionalidad
function guardarDireccionDmxFuncionalidad($idTipoFoco,$idFuncionalidadDmx,$direccionAsignada,$con){
	
	$correctoUpdate="n";
	
	/*
	La tabla BBDD 'luces_funcionalidades' muestra la relaccion entre funcionalidades y numero de funcionalidades, sin mas uso
	*/
	/*start saber el campo que estamos trabajando*/
	$campoNumbreFuncionalidad="";
	$patron1="SELECT funcionalidad FROM luces_funcionalidades WHERE id=\"%s\"";
	$sql1=sprintf($patron1,$idFuncionalidadDmx);
	$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963345435468676745734153456565517890899");
	if(mysqli_num_rows($respuesta1)>0){
		$fila1=mysqli_fetch_array($respuesta1);
		$campoNumbreFuncionalidad=$fila1[0];
	}
	mysqli_free_result($respuesta1);
	/*end saber el campo que estamos trabajando*/
	
	if($campoNumbreFuncionalidad!="" && ($direccionAsignada>0 && $direccionAsignada<=10 || $direccionAsignada==-99) && $idTipoFoco>0){
		
		/*start obtener el valor (direccion dmx) de cada fucionalidad*/
		$valorDireccionDmxRojo=0;//funcionalidad 1
		$valorDireccionDmxVerde=0;//funcionalidad 2
		$valorDireccionDmxAzul=0;//funcionalidad 3
		$valorDireccionDmxBlancocalido=0;//funcionalidad 4
		$valorDireccionDmxBlancofrio=0;//funcionalidad 5
		$valorDireccionDmxStrobe=0;//funcionalidad 6
		$valorDireccionDmxSped=0;//funcionalidad 7
		$valorDireccionDmxDimer=0;//funcionalidad 8
		$valorDireccionDmxFun=0;//funcionalidad 9
		$valorDireccionDmxUv=0;//funcionalidad 10
		
		$excluirLasUsadas=array();
		$patron="SELECT direcciondmxrojo,direcciondmxverde,direcciondmxazul,direcciondmxblancocalido,direcciondmxblancofrio,direcciondmxstrobe,direcciondmxsped,direcciondmxdimer,direcciondmxfun,direcciondmxuv FROM luces_referenciafocos WHERE borrado=\"n\" AND id=\"%s\"";
		$sql=sprintf($patron,$idTipoFoco);
		$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632346683457345345656557890899");
		if(mysqli_num_rows($respuesta)>0){
			$fila=mysqli_fetch_array($respuesta);

			$valorDireccionDmxRojo=intval($fila[0]);
			$valorDireccionDmxVerde=intval($fila[1]);
			$valorDireccionDmxAzul=intval($fila[2]);
			$valorDireccionDmxBlancocalido=intval($fila[3]);
			$valorDireccionDmxBlancofrio=intval($fila[4]);
			$valorDireccionDmxStrobe=intval($fila[5]);
			$valorDireccionDmxSped=intval($fila[6]);
			$valorDireccionDmxDimer=intval($fila[7]);
			$valorDireccionDmxFun=intval($fila[8]);
			$valorDireccionDmxUv=intval($fila[9]);

			if($valorDireccionDmxRojo>0){
				array_push($excluirLasUsadas,$valorDireccionDmxRojo);
			}
			if($valorDireccionDmxVerde>0){
				array_push($excluirLasUsadas,$valorDireccionDmxVerde);
			}
			if($valorDireccionDmxAzul>0){
				array_push($excluirLasUsadas,$valorDireccionDmxAzul);
			}
			if($valorDireccionDmxBlancocalido>0){
				array_push($excluirLasUsadas,$valorDireccionDmxBlancocalido);
			}
			if($valorDireccionDmxBlancofrio>0){
				array_push($excluirLasUsadas,$valorDireccionDmxBlancofrio);
			}
			if($valorDireccionDmxStrobe>0){
				array_push($excluirLasUsadas,$valorDireccionDmxStrobe);
			}
			if($valorDireccionDmxSped>0){
				array_push($excluirLasUsadas,$valorDireccionDmxSped);
			}
			if($valorDireccionDmxDimer>0){
				array_push($excluirLasUsadas,$valorDireccionDmxDimer);
			}
			if($valorDireccionDmxFun>0){
				array_push($excluirLasUsadas,$valorDireccionDmxFun);
			}
			if($valorDireccionDmxUv>0){
				array_push($excluirLasUsadas,$valorDireccionDmxUv);
			}
		}
		mysqli_free_result($respuesta);
		/*end obtener el valor (direccion dmx) de cada fucionalidad*/

		/*start si esta en el array es porque esta usada, pero en caso de ser ella misma, esta disponible, esta correcto.*/
		if (in_array($funcionalidadDmx, $excluirLasUsadas) && $funcionalidadDmx!=$funcionalidadDmx) {
			//ya esta usada, no poder usar
			$correctoUpdate="n";
		}else{
			//no usada, ok
			$correctoUpdate="s";
			/*UPDATE y devolver correcto*/
			if($correctoUpdate && $campoNumbreFuncionalidad!=""){
				$patron2="UPDATE luces_referenciafocos SET %s=\"%s\" WHERE id=\"%s\" AND borrado=\"n\"";
				$sql2=sprintf($patron2,$campoNumbreFuncionalidad,$direccionAsignada,$idTipoFoco);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 1390345656748363457455653457375763");
			}
		}
		/*end si esta en el array es porque esta usada, pero en caso de ser ella misma, esta disponible, esta correcto.*/
		
	}
	return $correctoUpdate;
}

//carga tipo ref focos
function cargaRefTipoFocosDmx($idPrograma,$name,$seleccionado,$auxClass,$auxReadonly,$con){
    $dev="";
	
    $class="";
	$readonly="";
    if($auxClass!=""){
       $class=" inputReadOnly"; 
    }
    
	$patron="SELECT id,referencia,canales FROM luces_referenciafocos WHERE borrado=\"n\" ORDER BY referencia ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error 31953602176746456455899121215");
	$dev=sprintf("<select class='form-control%s' name='tipoFocoLuces%s' id='tipoFocoLuces%s' onChange='actualizarCanalesDmxVacios(this,%s,\"%s\")'><option value='0'>Selecciona Tipo:</option>",$class,$name,$name,$seleccionado,$name);
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$seleccionado){
				$selected=" selected";
			}
			$dev.=sprintf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	$dev.=sprintf('</select>');
    return $dev;
}

//carga canales tipo focos
function cargaNumCanalesTipoFocosDmx($idPrograma,$name,$idFocoSeleccionado,$con){
    $dev="";
	
	$patron="SELECT id,canales FROM luces_referenciafocos WHERE borrado=\"n\" AND id=\"%s\"";
	$sql=sprintf($patron,$idFocoSeleccionado);
	$respuesta=mysqli_query($con,$sql) or die ("Error 319536021762347464564565456455899121215");
	if(mysqli_num_rows($respuesta)>0){
		$fila=mysqli_fetch_array($respuesta);

		$dev=sprintf("<input type='text' maxlength='2' class='form-control inputReadOnly' name='canalesFocoLuces%s' id='canalesFocoLuces%s' value=\"%s\" readonly/>",$name,$name,$fila[1]);
		
	}else{
		$dev=sprintf("<input type='text' maxlength='2' class='form-control inputReadOnly' name='canalesFocoLuces%s' id='canalesFocoLuces%s' value=\"%s\" readonly/>",$name,$name,"-");
	}
	mysqli_free_result($respuesta);
		
    return $dev;
}

//devolver cada direccion dmx a encender segun tipo de foco y canales reservados vacios
function calcularDireccionDmxSegunConfVaciosFoco($seccion,$idPrograma,$numFoco,$idColoFuncSeleccionado,$con){
	
    $numFoco=$numFoco;//empieza en cero restar uno en caso de venir un 1 para el primero, no restar si viene un 0 para el primero
    
	/*START VARIABLES por defecto conf*/
    $numFocosActuales=20;//sin uso
	$numCanalesPorFoco=0;
	$auxInicioDirDmxSegunCanales=$numCanalesPorFoco;//el primero es el 0 el ultimo es el 19, de un total de 20 focos
    $direccionDmxEncender=0;//calculada para seccion 2
	/*END VARIABLES por defecto conf*/
	
	
	//podemos calcular este actual, dependiendo del anterior siempre
	if($idPrograma>0 && ($seccion==1 || $seccion==2) ){
		
        
        /*************************************************/
        /*******Seccion 1 y datos para seeccion 2********/
        /***********************************************/
        
		$arrayTipoFocosPosiciones=array();
		$arrayVaciosFocosPosiciones=array();
		$patron1="SELECT colum1,colum2,colum3,colum4,colum5,colum6,colum7,colum8,colum9,colum10,colum11,colum12,colum13,colum14,colum15,colum16,colum17,colum18,colum19,colum20,numfocos,tipofococolum1,tipofococolum2,tipofococolum3,tipofococolum4,tipofococolum5,tipofococolum6,tipofococolum7,tipofococolum8,tipofococolum9,tipofococolum10,tipofococolum11,tipofococolum12,tipofococolum13,tipofococolum14,tipofococolum15,tipofococolum16,tipofococolum17,tipofococolum18,tipofococolum19,tipofococolum20,vaciosfococolum1,vaciosfococolum2,vaciosfococolum3,vaciosfococolum4,vaciosfococolum5,vaciosfococolum6,vaciosfococolum7,vaciosfococolum8,vaciosfococolum9,vaciosfococolum10,vaciosfococolum11,vaciosfococolum12,vaciosfococolum13,vaciosfococolum14,vaciosfococolum15,vaciosfococolum16,vaciosfococolum17,vaciosfococolum18,vaciosfococolum19,vaciosfococolum20 FROM luces_programas WHERE id=\"%s\"";
		$sql1=sprintf($patron1,$idPrograma);
		$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 123325454576346658456545543545466");
		if(mysqli_num_rows($respuesta1)>0){
			$fila1=mysqli_fetch_array($respuesta1);
			
			/*START declarar campos nombre foco*/
			/*$colum1=intval($fila1[0]);
			$colum2=intval($fila1[1]);
			$colum3=intval($fila1[3]);
			$colum4=intval($fila1[4]);
			$colum5=intval($fila1[5]);
			$colum6=intval($fila1[6]);
			$colum7=intval($fila1[7]);
			$colum8=intval($fila1[8]);
			$colum9=intval($fila1[9]);
			$colum10=intval($fila1[9]);
			$colum11=intval($fila1[10]);
			$colum12=intval($fila1[11]);
			$colum13=intval($fila1[13]);
			$colum14=intval($fila1[13]);
			$colum15=intval($fila1[14]);
			$colum16=intval($fila1[15]);
			$colum17=intval($fila1[16]);
			$colum18=intval($fila1[17]);
			$colum19=intval($fila1[18]);
			$colum20=intval($fila1[19]);*/
			/*END declarar campos nombre foco*/

			$focosEnUso=$fila1[20];
			
			/*START recoger campos tipo foco, tengo los id de cada tipo de foco*/
			$arrayTipoFocosPosiciones[]=$tipoFocoColum1=intval($fila1[21]);//num foco, posicion foco --> 1, posicion array --> 0
			$arrayTipoFocosPosiciones[]=$tipoFocoColum2=intval($fila1[22]);//num foco, posicion foco --> 2, posicion array --> 1
			$arrayTipoFocosPosiciones[]=$tipoFocoColum3=intval($fila1[23]);//num foco, posicion foco --> 3, posicion array --> 2
			$arrayTipoFocosPosiciones[]=$tipoFocoColum4=intval($fila1[24]);//num foco, posicion foco --> 4, posicion array --> 3
			$arrayTipoFocosPosiciones[]=$tipoFocoColum5=intval($fila1[25]);//num foco, posicion foco --> 5, posicion array --> 4
			$arrayTipoFocosPosiciones[]=$tipoFocoColum6=intval($fila1[26]);//num foco, posicion foco --> 6, posicion array --> 5
			$arrayTipoFocosPosiciones[]=$tipoFocoColum7=intval($fila1[27]);//num foco, posicion foco --> 7, posicion array --> 6
			$arrayTipoFocosPosiciones[]=$tipoFocoColum8=intval($fila1[28]);//num foco, posicion foco --> 8, posicion array --> 7
			$arrayTipoFocosPosiciones[]=$tipoFocoColum9=intval($fila1[29]);//num foco, posicion foco --> 9, posicion array --> 8
			$arrayTipoFocosPosiciones[]=$tipoFocoColum10=intval($fila1[30]);//num foco, posicion foco --> 10, posicion array --> 9
			$arrayTipoFocosPosiciones[]=$tipoFocoColum11=intval($fila1[31]);//num foco, posicion foco --> 11, posicion array --> 10
			$arrayTipoFocosPosiciones[]=$tipoFocoColum12=intval($fila1[32]);//num foco, posicion foco --> 12, posicion array --> 11
			$arrayTipoFocosPosiciones[]=$tipoFocoColum13=intval($fila1[33]);//num foco, posicion foco --> 13, posicion array --> 12
			$arrayTipoFocosPosiciones[]=$tipoFocoColum14=intval($fila1[34]);//num foco, posicion foco --> 14, posicion array --> 13
			$arrayTipoFocosPosiciones[]=$tipoFocoColum15=intval($fila1[35]);//num foco, posicion foco --> 15, posicion array --> 14
			$arrayTipoFocosPosiciones[]=$tipoFocoColum16=intval($fila1[36]);//num foco, posicion foco --> 16, posicion array --> 15
			$arrayTipoFocosPosiciones[]=$tipoFocoColum17=intval($fila1[37]);//num foco, posicion foco --> 17, posicion array --> 16
			$arrayTipoFocosPosiciones[]=$tipoFocoColum18=intval($fila1[38]);//num foco, posicion foco --> 18, posicion array --> 17
			$arrayTipoFocosPosiciones[]=$tipoFocoColum19=intval($fila1[39]);//num foco, posicion foco --> 19, posicion array --> 18
			$arrayTipoFocosPosiciones[]=$tipoFocoColum20=intval($fila1[40]);//num foco, posicion foco --> 20, posicion array --> 19
			/*END recoger campos tipo foco*/

			/*START recoger campos canales vacios foco, tengo los id de cada tipo de foco*/
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum1=intval($fila1[41]);//num foco, posicion foco --> 1, posicion array --> 0
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum2=intval($fila1[42]);//num foco, posicion foco --> 2, posicion array --> 1
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum3=intval($fila1[43]);//num foco, posicion foco --> 3, posicion array --> 2
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum4=intval($fila1[44]);//num foco, posicion foco --> 4, posicion array --> 3
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum5=intval($fila1[45]);//num foco, posicion foco --> 5, posicion array --> 4
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum6=intval($fila1[46]);//num foco, posicion foco --> 6, posicion array --> 5
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum7=intval($fila1[47]);//num foco, posicion foco --> 7, posicion array --> 6
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum8=intval($fila1[48]);//num foco, posicion foco --> 8, posicion array --> 7
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum9=intval($fila1[49]);//num foco, posicion foco --> 9, posicion array --> 8
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum10=intval($fila1[50]);//num foco, posicion foco --> 10, posicion array --> 9
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum11=intval($fila1[51]);//num foco, posicion foco --> 11, posicion array --> 10
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum12=intval($fila1[52]);//num foco, posicion foco --> 12, posicion array --> 11
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum13=intval($fila1[53]);//num foco, posicion foco --> 13, posicion array --> 12
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum14=intval($fila1[54]);//num foco, posicion foco --> 15, posicion array --> 13
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum15=intval($fila1[55]);//num foco, posicion foco --> 16, posicion array --> 14
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum16=intval($fila1[56]);//num foco, posicion foco --> 16, posicion array --> 15
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum17=intval($fila1[57]);//num foco, posicion foco --> 17, posicion array --> 16
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum18=intval($fila1[58]);//num foco, posicion foco --> 18, posicion array --> 17
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum19=intval($fila1[59]);//num foco, posicion foco --> 19, posicion array --> 18
			$arrayVaciosFocosPosiciones[]=$vaciosFocoColum20=intval($fila1[60]);//num foco, posicion foco --> 20, posicion array --> 19
			/*END recoger campos canales vacios foco*/
			
			
			/*START CALCULAR DIRECCIONES DMX DE CADA UNO DE MANERA DINAMICA*/
			$arrayDireccionesDmxCalculadasFoco=array();
			$posicionInicio=1;
			$posicionFin=0;
			for($j=0;$j<=count($arrayTipoFocosPosiciones)-1;$j++){
                
				/*start saber el num de canales de cada foco*/
				$patron="SELECT id,canales FROM luces_referenciafocos WHERE borrado=\"n\" AND id=\"%s\"";
				$sql=sprintf($patron,$arrayTipoFocosPosiciones[$j]);
				$respuesta=mysqli_query($con,$sql) or die ("Error 319536334567234556455899345345314534521215");
				if(mysqli_num_rows($respuesta)>0){
					$fila=mysqli_fetch_array($respuesta);
					$numCanalesPorFoco=intval($fila[1]);
					
					if($j>0){
						$posicionInicio=$posicionFin+1;
					}
					
					$posicionFin+=$numCanalesPorFoco+$arrayVaciosFocosPosiciones[$j];
					//echo "[".$posicionInicio.", ".$posicionFin."]"."<br>";
					
					//componer array a devolver asi tengo cada una el num de foco
					$arrayDireccionesDmxCalculadasFoco[]=array($posicionInicio,$posicionFin);
					
					

				}else{
                    //por si  no hay tipo de foco seleccionado para ese num. de foco
                    if($j>0){
						$posicionInicio=$posicionFin+1;
					}
					
					$posicionFin+=10;
					//echo "no foco: [".$posicionInicio.", ".$posicionFin."]"."<br>";
					
					//componer array a devolver asi tengo cada una el num de foco
					$arrayDireccionesDmxCalculadasFoco[]=array($posicionInicio,$posicionFin);
                }
				mysqli_free_result($respuesta);
				/*end saber el num de canales de cada foco*/
			}
			/*END CALCULAR DIRECCIONES DMX DE CADA UNO DE MANERA DINAMICA*/
			
		}
		mysqli_free_result($respuesta1);
        
        
        /***********************************************/
        /*****************Seccion 2********************/
        /*********************************************/
        if($seccion==2 && $idColoFuncSeleccionado>0){
            /*start datos de esa funcionalidad o color*/
            $patron1="SELECT id,color,colorreal,colortextoingles,estado FROM luces_configuracion_color WHERE id=\"%s\"";
            $sql1=sprintf($patron1,$idColoFuncSeleccionado);
            $respuesta1=mysqli_query($con,$sql1) or die ("Error 31953633456711993453453115345121215");
            if(mysqli_num_rows($respuesta1)>0){
                $fila1=mysqli_fetch_array($respuesta1);

                $variableEvaluarSwiych=$fila1[0];

                switch($variableEvaluarSwiych){
                    case 1://azul
                        $patron2="SELECT direcciondmxazul FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 3195322226334567119934534220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            
                            /*posicion inicio ese foco+(direccion asignada por defecto-1)--,-1 ya que empieza en la misma*/
                            $direccionDmxEncender=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);

                            //echo intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])."+(".intval($fila2[0])."-1)";
                        }
                        mysqli_free_result($respuesta2);
                    break;
                    case 2://rojo
                        $patron2="SELECT direcciondmxrojo FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 319532222633456227119934534220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $direccionDmxEncender=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);
                    break;
                    case 3://amarillo
                        //no hay amarillo en direcciones DMX de los tipos de foco
                        //luz roja+luz verde

                        //rojo
                        $rojo=0;
                        $patron2="SELECT direcciondmxrojo FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 319532222633456227119333934534220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $rojo=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);
                        //verde
                        $verde=0;
                        $patron2="SELECT direcciondmxverde FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 31953222263355533456711399345334220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $verde=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);

                        $direccionDmxEncender=array($rojo,$verde);
                    break;
                    case 4://morado
                        //no hay morado en direcciones DMX de los tipos de foco
                        //luz azul+luz rojo

                        //azul
                        $azul=0;
                        $patron2="SELECT direcciondmxazul FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 3195322226334567119934534220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $azul=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);
                        //rojo
                        $rojo=0;
                        $patron2="SELECT direcciondmxrojo FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 319532222633456227119333934534220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $rojo=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);

                        $direccionDmxEncender=array($azul,$rojo);
                    break;
                    case 5://verde
                        $patron2="SELECT direcciondmxverde FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 3195322226555334567119934534220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $direccionDmxEncender=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);
                    break;
                    case 6://cian
                        //no hay cian en direcciones DMX de los tipos de foco
                        //luz verde+luz azul

                        //verde
                        $verde=0;
                        $patron2="SELECT direcciondmxverde FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 319532222633555336764456711399345334220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $verde=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);
                        //azul
                        $azul=0;
                        $patron2="SELECT direcciondmxazul FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 3195322226334567234119934534220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $azul=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);

                        $direccionDmxEncender=array($verde,$azul);
                    break;
                    case 7://blanco ON
                        $patron2="SELECT direcciondmxblancofrio FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 31795322226334577767119934534220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $direccionDmxEncender=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);
                    break;
                    case 8://blanco OFF
                        //nada esta apagado OFF
                        $direccionDmxEncender=0;//apagada

                    break;
                    case 9://rosa
                        //no hay rosa en direcciones DMX de los tipos de foco
                        //luz rojo+luz blanco

                        //rojo
                        $rojo=0;
                        $patron2="SELECT direcciondmxrojo FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 319532222633452346227119333934534220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $rojo=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);
                        //blanco
                        $blanco=0;
                        $patron2="SELECT direcciondmxblancofrio FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 3179532222633457734576767119934534220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $blanco=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);

                        $direccionDmxEncender=array($rojo,$blanco);
                    break;
                    case 10://uv
                        $patron2="SELECT direcciondmxuv FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 31953210222610334567119934534220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $direccionDmxEncender=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);
                    break;
                    case 11://blanco calido
                        $patron2="SELECT direcciondmxblancocalido FROM luces_referenciafocos WHERE id=\"%s\"";
                        $sql2=sprintf($patron2,$arrayTipoFocosPosiciones[$numFoco]);
                        $respuesta2=mysqli_query($con,$sql2) or die ("Error 31953222112633456711911934534220025");
                        if(mysqli_num_rows($respuesta2)>0){
                            $fila2=mysqli_fetch_array($respuesta2);
                            $direccionDmxEncender=intval($arrayDireccionesDmxCalculadasFoco[$numFoco][0])+(intval($fila2[0])-1);
                        }
                        mysqli_free_result($respuesta2);
                    break;
                }

            }
            mysqli_free_result($respuesta1);
            /*end datos de esa funcionalidad o color*/
       }
        
    }
    
    //completo seccion 1 --> devolver dinamicamente las direcciones DMX de cada foco, inicio y fin
    //var_dump($arrayDireccionesDmxCalculadasFoco);
    
    //seccion 1 --> devolver la direccion DMX del foco en cuestion dentro del programa en cuestion
    //seccion 2 --> devolver la direccion DMX del color o fincionalidad que aplique
    
    if($seccion==1 && ($numFoco>=0 && $numFoco<20) ){
        return $arrayDireccionesDmxCalculadasFoco[$numFoco];
    }else if($seccion==2){
        return $direccionDmxEncender;
    }else{
        return "-";
    }
}

//carga modo nodo placa luz
function cargaModoNodoLuz($seleccionada,$nombre,$idNodo,$con){
	$optionUno="";
	$optionDos="";
	if($seleccionada==1){
		$optionUno=" selected";
	}else if($seleccionada==2){
		$optionDos=" selected";
	}
	
	printf("<select class='form-control' name=\"%s\" id=\"%s\" onChange='actualizarModoNodoLuces(this,%s);' >",$nombre,$nombre,$idNodo);
	printf("<option value='1'%s>Modo automático</option>",$optionUno);
	printf("<option value='2'%s>Modo manual</option>",$optionDos);	
	printf("</select>");
}

//carga tiempo modo nodo placa luz
function cargaTiempoModoNodoLuz($seleccionada,$nombre,$con){
	$optionUno="";
	$optionDos="";
	$optionTres="";
	$optionCuatro="";
	$optionCinco="";
	if($seleccionada==1){
		$optionUno=" selected";
	}else if($seleccionada==2){
		$optionDos=" selected";
	}else if($seleccionada==3){
		$optionTres=" selected";
	}else if($seleccionada==4){
		$optionCuatro=" selected";
	}else if($seleccionada==5){
		$optionCinco=" selected";
	}
	
	printf("<select class='form-control' name=\"%s\" id=\"%s\">",$nombre,$nombre);
	printf("<option value='1'%s>1 min</option>",$optionUno);
	printf("<option value='2'%s>2 min</option>",$optionDos);	
	printf("<option value='3'%s>3 min</option>",$optionTres);	
	printf("<option value='4'%s>4 min</option>",$optionCuatro);	
	printf("<option value='5'%s>5 min</option>",$optionCinco);	
	printf("</select>");
}

//configuracion tipos programas predefinidos luces 
function configuracionTipoProgramaLucesGenerico($con){
	
	$patron="SELECT id,nombre FROM luces_tiposprogramaspredefinidos WHERE borrado=\"n\" ORDER BY nombre ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96325734574567890899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Nombre</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
		
			/*START boton editar*/
			$botonEditar="";
			if($fila[0]>6){//solo dejar borrar para los id nuevos anadidos
				$botonEditar="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm mx-3' onClick='editarTipoProgramaPredefinidoLuz(".$fila[0].");return false;' title='Guardar cambios'>
							<span class='svg-icon svg-icon-md svg-icon-success'>
								<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
									<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
										<polygon points='0 0 24 0 24 24 0 24'/>
										<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
										<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
									</g>
								</svg><!--end::Svg Icon-->
							</span>
						</a>";
			}
			/*END boton editar*/
            
			//boton eliminar
			$botonEliminar="";
			if($fila[0]>6){//solo dejar borrar para los id nuevos anadidos
				$botonEliminar="<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm' onClick='confirmacion(\"warning\",\"Eliminar tipo Programa Predefinido\",\"¿Estas seguro de que deseas eliminar este tipo de Programa?\",40,\"".$fila[0]."\",\"\",\"\");return false;'>
								<span class='svg-icon svg-icon-md svg-icon-danger'>
									<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
										<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
											<rect x='0' y='0' width='24' height='24'></rect>
											<path d='M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z' fill='#000000' fill-rule='nonzero'></path>
											<path d='M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z' fill='#000000' opacity='0.3'></path>
										</g>
									</svg>
								</span>
							</a>";
			}
			
			//pintar
			printf("<tr>
						<td></td>
						<td><input type='text' class='form-control' id='nombreTipoProg%s' value='%s'/></td>
						<td nowrap='nowrap'>%s%s</td>
					</tr>",$fila[0],$fila[1],$botonEditar,$botonEliminar);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Nombre</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}

//carga tipo programas predefinidos generico
function cargaTipoProgramasPreGenerico($seleccionada,$nombre,$faltacampo,$con){

	$consulta="";
	
	$class="";
	if($faltacampo && $seleccionada=="0"){
		$class=" is-invalid";
	}
	
	$patron="SELECT id,nombre FROM luces_tiposprogramaspredefinidos WHERE borrado=\"n\"%s ORDER BY nombre ASC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1234345345345466456");
	printf("<select class='form-control%s' name=\"%s\" id=\"%s\" >",$class,$nombre,$nombre);
	printf("<option value='0'>Selecciona Predefinido: (Ninguno)</option>");
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

?>