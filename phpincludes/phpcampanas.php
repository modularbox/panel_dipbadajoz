<?php 
//poner online offline, segun ultima conexion
function ultimaConexionOnlinOfflineCampanas($con){
	$patron="SELECT id,fechaultimaconsulta,horaultimaconsulta FROM campanas_nodos WHERE borrado=\"n\" AND guardado=\"s\"";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632343563634563546345444652355899");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);

			$horaUltimaConexionExplode=explode(":",$fila[2]);
			$horaEnSegundos=(($horaUltimaConexionExplode[0]*60)*60)+($horaUltimaConexionExplode[1]*60)+$horaUltimaConexionExplode[2];

			$horaAhoraExplode=explode(":",date("H:i:s"));
			$horaAhoraEnSegundos=(($horaAhoraExplode[0]*60)*60)+($horaAhoraExplode[1]*60)+$horaAhoraExplode[2];

			//restado
			$restarHoras=$horaAhoraEnSegundos-$horaEnSegundos;

			//limite para dar por off, 3 min
			$tiempoLimite=3*60;

			$estado="";
			if($restarHoras>=$tiempoLimite || $fila[1]<date("Y-m-d")){//off
				$estado="off";
			}else{//on
				$estado="on";
			}

			$patron1="UPDATE campanas_nodos SET conexion=\"%s\" WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$estado,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al borrar 1234563423412123435567431789");
		}
	}
	mysqli_free_result($respuesta);
}

													/*************************************
													 *									 *
													 *		        CAMPANAS             *
													 *									 *
													 *************************************/	
// CARGA campanas
function cargaNodosCampanasList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioCampanasList"]!="0"*/){
		if($_SESSION["usuarioCampanasList"]!="0"){
			$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioCampanasList"])."\"";
		}
	}else{
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	if($_SESSION["conexionCampanasList"]!=""){
		$consulta.=" AND conexion=\"".quitaComillasD($_SESSION["conexionCampanasList"])."\"";
	}
	
	$patron="SELECT id,nombre,idusuario,conexion,ubicacion FROM campanas_nodos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963233435342365545467545783543457879958");
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
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963534325533366764434653509258");
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
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=27&i=%s\");'",$fila[0]);
			
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

//filtro clientes Campanas list
function cargaUsuariosCampanasFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 312152358343755657786767412364443455456454455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosCampanas(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioCampanasList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//filtro estado Parques list
function cargaEstadosCampanasFiltro($con){
	$selectedUno="";
	$selectedDos="";
	if($_SESSION["conexionCampanasList"]=="on"){
		$selectedUno=" selected";
	}else if($_SESSION["conexionCampanasList"]=="off"){
		$selectedDos=" selected";
	}
	
	printf("<select class='form-control' id='selectConexionFiltro' onChange='filtrarConexionCampanas(this);'><option value=''>Selecciona Conexión:</option>");
	printf("<option value=\"on\" %s>Online</option><option value=\"off\" %s>Offline</option>",$selectedUno,$selectedDos);	
	printf('</select>');
}

// CARGA programas campanas
function cargaNodosCampanasProgramasList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioCampanasProgramasList"]!="0"*/){
		if($_SESSION["usuarioCampanasProgramasList"]!="0"){
			$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioCampanasProgramasList"])."\"";
		}
	}else{
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	$patron="SELECT id,nombre,idusuario,activo,descripcion,nrepeticiones,tiemporepeticiones,l,m,x,j,v,s,d,horainicio FROM campanas_programas WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre ASC,activo DESC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323343533442365545467545783543457879958");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>Descripción</th>
					  <th>Estado</th>
					  <th>Hora y Días</th>
					  <th>Repeticiones</th>
					  <th title="Con retardo">Duración</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//usuario
			$patron1="SELECT nombre FROM usuarios WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$fila[2]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963534325533366764434653509258");
			$fila1=mysqli_fetch_array($respuesta1);
			mysqli_free_result($respuesta1);
			
			$botonesAcciones="";
			
			//estado, activo
			$activo="n";
			//activo s, para traer los q esten activos por si estan en otras placas pero no activo
			$patron2="SELECT activo FROM campanas_programas_activos WHERE idprograma=\"%s\" AND activo=\"s\"";
			$sql2=sprintf($patron2,$fila[0]);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 96323343533442322265545467545783543457879958");
			if(mysqli_num_rows($respuesta2)>0){
				$fila2=mysqli_fetch_array($respuesta2);
				if($fila2[0]=="s"){
					$activo="s";
				}else if($fila2[0]=="n"){
					$activo="n";
				}
			}
			mysqli_free_result($respuesta2);
			if($activo=="s"){
				$conexion="<span class='label label-lg label-light-success label-inline'>Activo</span>";
			}else if($activo=="n"){
				$conexion="<span class='label label-lg label-light-danger label-inline'>Desactivado</span>";
			}else{
				$conexion="<span class='label label-lg label-light-primary label-inline'>Sin datos</span>";
			}
			
			//duracion
			$nRepeticiones=$fila[5];
			$tiempoEntreRepeticiones=$fila[6];
			
			$contadorConfiguracionConRetardo=0;
			$contadorConfiguracionSinRetardo=0;
			
			$tiempoRetardo=0.4;
			$patron3="SELECT id,releuno,reledos,reletres,temporizacion FROM campanas_programas_configuracion WHERE borrado=\"n\" AND idprograma=\"%s\"";
			$sql3=sprintf($patron3,$fila[0]);
			$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 96323343533333442365545467545783543457879958");
			if(mysqli_num_rows($respuesta3)>0){
				for($j=0;$j<mysqli_num_rows($respuesta3);$j++){
					$fila3=mysqli_fetch_array($respuesta3);
					
					$segundosTemporizacion=0;
					$patron4="SELECT tiemposegundos FROM campanas_temporizacion WHERE id=\"%s\"";
					$sql4=sprintf($patron4,$fila3[4]);
					$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 96323343533444442322265545467545783543457879958");
					if(mysqli_num_rows($respuesta4)>0){
						$fila4=mysqli_fetch_array($respuesta4);
						$segundosTemporizacion=$fila4[0];
					}
					mysqli_free_result($respuesta4);
					
					if($fila3[1]==1 || $fila3[2]==1 || $fila3[3]==1){
						$contadorConfiguracionConRetardo+=$tiempoRetardo;
						$contadorConfiguracionConRetardo+=$segundosTemporizacion;
						
						$contadorConfiguracionSinRetardo+=$segundosTemporizacion;
					}
				}
			}
			mysqli_free_result($respuesta3);
			
			if($nRepeticiones>0){
				$contadorConfiguracionConRetardo=$contadorConfiguracionConRetardo*$nRepeticiones;
				$contadorConfiguracionSinRetardo=$contadorConfiguracionSinRetardo*$nRepeticiones;
			}
			
			if($tiempoEntreRepeticiones>0){
				$sumaTiempoTotalRepeticiones=($tiempoEntreRepeticiones*$nRepeticiones)-$tiempoEntreRepeticiones;
				
				$contadorConfiguracionConRetardo+=$sumaTiempoTotalRepeticiones;
				$contadorConfiguracionSinRetardo+=$sumaTiempoTotalRepeticiones;
			}
			$duracionContadorConRetardo=conversor_segundos($contadorConfiguracionConRetardo);
			$duracionContadorSinRetardo=conversor_segundos($contadorConfiguracionSinRetardo);
			$duracion=$duracionContadorSinRetardo." (".$duracionContadorConRetardo.")";
			
			//dias activo y horario
			$diasActivo="";
			if($fila[7]=="s"){
				$diasActivo.="L, ";
			}
			if($fila[8]=="s"){
				$diasActivo.="M, ";
			}
			if($fila[9]=="s"){
				$diasActivo.="X, ";
			}
			if($fila[10]=="s"){
				$diasActivo.="J, ";
			}
			if($fila[11]=="s"){
				$diasActivo.="V, ";
			}
			if($fila[12]=="s"){
				$diasActivo.="S, ";
			}
			if($fila[13]=="s"){
				$diasActivo.="D, ";
			}
			$diasActivo.=$fila[14];
			
			//pulsar tr
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=29&i=%s\");'",$fila[0]);
			
			printf("<tr>
				<td></td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				</tr>",$funcion,$fila[1],$funcion,$fila1[0],$funcion,$fila[4],$funcion,$conexion,$funcion,$diasActivo,$funcion,$nRepeticiones,$funcion,$duracion);
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>Descripción</th>
					  <th>Estado</th>
					  <th>Repeticiones</th>
					  <th>Hora y Días</th>
					  <th>Duración</th>
					</tr>
                </thead>
                <tbody>');
	}
}


//filtro clientes Programas Campanas list
function cargaUsuariosCampanasProgramasFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 3121500653455456454455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosCampanasProgramas(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioCampanasProgramasList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//configuracion programa campanas
function configuracionProgramaCampanas($idPrograma,$con){
	
	
	//saber el numero de campanas activas
	$idUsuario=0;
	$maxCampanasContratadas=0;
	$patron999="SELECT idusuario FROM campanas_programas WHERE borrado=\"n\" AND id=\"%s\" ";//AND guardado=\"s\"
	$sql999=sprintf($patron999,$idPrograma);
	$respuesta999=mysqli_query($con,$sql999) or die ("Error al buscar 9632456345346699900676999945757890899778");
	if(mysqli_num_rows($respuesta999)>0){
		$fila999=mysqli_fetch_array($respuesta999);
				
		$idUsuario=$fila999[0];
		
		if($idUsuario){
			$patron998="SELECT MAX(ncampanas) FROM campanas_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND idusuario=\"%s\"";
			$sql998=sprintf($patron998,$idUsuario);
			$respuesta998=mysqli_query($con,$sql998) or die ("Error al buscar 9632456399845346600676999945757890899778");
			if(mysqli_num_rows($respuesta998)>0){
				//for($h=0;$h<mysqli_num_rows($respuesta998);$h++){
					$fila998=mysqli_fetch_array($respuesta998);
					$maxCampanasContratadas=$fila998[0];
				//}
			}
			mysqli_free_result($respuesta998);
		}
		
	}
	mysqli_free_result($respuesta999);
	
	
	
	//$flecha="fl";
	
	$colorOn=0;
	$colorOff=0;
	
	//obtener colores en un for con variables para pintar
	$patron99="SELECT id,color FROM campanas_configuracion_activacion WHERE borrado=\"n\"";
	$sql99=sprintf($patron99);
	$respuesta99=mysqli_query($con,$sql99) or die ("Error al buscar 9632456345346600676999945757890899778");
	if(mysqli_num_rows($respuesta99)>0){
		for($h=0;$h<mysqli_num_rows($respuesta99);$h++){
			$fila99=mysqli_fetch_array($respuesta99);

			switch($h){
				case 0:
					$colorUno=$fila99[1];	
				break;
				case 1:
					$colorOn=$fila99[1];
				break;
				case 2:
					$colorOff=$fila99[1];
				break;
			}
		}
	}
	mysqli_free_result($respuesta99);
	
	$patron="SELECT id,releuno,reledos,reletres,temporizacion FROM campanas_programas_configuracion WHERE borrado=\"n\" AND idprograma=\"%s\" ORDER BY id ASC";
	$sql=sprintf($patron,$idPrograma);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963257890899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th></th>
					  <th>Una</th>
					  <th>Dos</th>
					  <th>Tres</th>
					  <th>Temporización.</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
		
			//flechas
			$flechas="<a href='javascript: void(0)' class='btn btn-icon btn-sm' title='Mover arriba'>
                                <span class='svg-icon svg-icon-primary svg-icon-2x'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-04-09-093151/theme/html/demo1/dist/../src/media/svg/icons/Navigation/Arrow-up.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
                                    <g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
                                        <polygon points='0 0 24 0 24 24 0 24'/>
                                        <rect fill=''#000000' opacity='0.3' x='11' y='5' width='2' height='14' rx='1'/>
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
				$flechas="<a href='#' class='btn btn-icon btn-light btn-hover-primary btn-sm' title='Mover arriba' onclick='mueveFilaProgramaCampanas(1,\"".$fila[0]."\",\"".$idPrograma."\");'>
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
				$flechab="<a href='#' class='btn btn-icon btn-light btn-hover-primary btn-sm mx-3' title='Mover abajo' onclick='mueveFilaProgramaCampanas(2,\"".$fila[0]."\",\"".$idPrograma."\");'>
                                    <span class='svg-icon svg-icon-primary svg-icon-2x'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-04-09-093151/theme/html/demo1/dist/../src/media/svg/icons/Navigation/Arrow-down.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
                                    <g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
                                        <polygon points='0 0 24 0 24 24 0 24'/>
                                        <rect fill='#000000' opacity='0.3' x='11' y='5' width='2' height='14' rx='1'/>
                                        <path d='M6.70710678,18.7071068 C6.31658249,19.0976311 5.68341751,19.0976311 5.29289322,18.7071068 C4.90236893,18.3165825 4.90236893,17.6834175 5.29289322,17.2928932 L11.2928932,11.2928932 C11.6714722,10.9143143 12.2810586,10.9010687 12.6757246,11.2628459 L18.6757246,16.7628459 C19.0828436,17.1360383 19.1103465,17.7686056 18.7371541,18.1757246 C18.3639617,18.5828436 17.7313944,18.6103465 17.3242754,18.2371541 L12.0300757,13.3841378 L6.70710678,18.7071068 Z' fill='#000000' fill-rule='nonzero' transform='translate(12.000003, 14.999999) scale(1, -1) translate(-12.000003, -14.999999) '/>
                                    </g>
                                </svg><!--end::Svg Icon--></span>
                            </a>";
			}
			
			$botones=$flechas.$flechab."<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm ' 	 		onClick='editarLineaConfiguracionCampanas(\"".$idPrograma."\",\"".$fila[0]."\");return false;' title='Guardar Cambios'>
							<span class='svg-icon svg-icon-md svg-icon-success'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Save.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<polygon points='0 0 24 0 24 24 0 24'/>
									<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
									<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
								</g>
							</svg><!--end::Svg Icon-->
							</span>
					</a>
                    <a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm mx-3' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas 					seguro de que deseas eliminar este registro?\",27,\"".$idPrograma."\",\"".$fila[0]."\",\"\");return false;' title='Borrar'>
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
			
						$numSelectores=3;
			
						printf("<td>%s</td>",$i+1);
                        for($j=0;$j<3;$j++){
							
							//segun ese id, saber color
							$campoConsulta=$fila[1+$j];
							$colorTabla="#ffffff";
							$patron2="SELECT color FROM campanas_configuracion_activacion WHERE id=\"%s\"";
							$sql2=sprintf($patron2,$campoConsulta);
							$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 12331343112177222265544");
							if(mysqli_num_rows($respuesta2)>0){
								$fila2=mysqli_fetch_array($respuesta2);
								$colorTabla=$fila2[0];
							}
							
							//id columna, div
							$idCeldaColumna="releModal".$fila[0]."_".($j+1);
							
							$estiloX="border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center";
							$icono="<i class='fas fa-times' style='font-size:25px;' title='No disponible'></i>";
							$colorDiv="#ffffff";
							$selectorColores="";
                            
                            if($j<$numSelectores){
                                //nada
                                $estiloX="";
                                $icono="";
								
								//si es el 1, es on, es blanco, mostrar icono,off blanco por defecto sin nada
								if($campoConsulta==1){
									$icono="<i class='fas fa-bell' style='font-size: 20px;margin-top: 18%;color:#ffcc3b;'></i>";
								}
								
                                $colorDiv=$colorTabla;
								if($colorDiv==""){
									 $colorDiv="#ffffff";
								}
								
								//solo poder manipular las contratadas
								if($j<$maxCampanasContratadas){
									$selectorColores="data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'";
								}else{//no contratadas
									$colorDiv="#C4C4C4";
								}
								
                            }else{
                                $estiloX="border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center";
                                $icono="<i class='fas fa-times' style='font-size:25px;' title='No disponible'></i>";
                                $colorDiv="#ffffff";
                                $selectorColores="";
                            }
							
                            $estilo="border: 1px solid #000000;border-radius: .42rem;background-color: ".$colorDiv.";cursor: pointer;";
							
							
                            printf("<td style='padding-left: 0;padding-right: 0;text-align: -webkit-center;'>%s</td>", "<div id='".$idCeldaColumna."' style='width:".$alto."px;height:".$ancho."px;".$estilo.$estiloX."'".$selectorColores."><input type='hidden' id='".$idCeldaColumna."_hidden' value='".$campoConsulta/*$colorDiv*/."'/>
                            ".$icono."
                                <div class='dropdown-menu dropdown-menu-sm'>
                                    <div class='dropdown-divider'></div>
                                    <div style='display:flex'>
                                        <div style='width:50%;text-align: -webkit-center;' onClick='cambiarColorCeldaCampanas(\"".$colorOn."\",1,\"".$idCeldaColumna."\",\"".$idPrograma."\",\"".$fila[0]."\");'>
                                            <div style='width:38px;height:38px;border: 1px solid #000000;border-radius: .42rem;cursor: pointer;-webkit-box-align: center;padding-top: 8px;color:#28a745;'>
                                                ON
                                            </div>
                                        </div>
                                        <div style='width:50%;text-align: -webkit-center;' onClick='cambiarColorCeldaCampanas(\"".$colorOff."\",2,\"".$idCeldaColumna."\",\"".$idPrograma."\",\"".$fila[0]."\");'>
                                            <div style='width:38px;height:38px;border: 1px solid #000000;border-radius: .42rem;cursor: pointer;-webkit-box-align: center;padding-top: 8px;color:#000000;'>
                                                OFF
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>");
                        }

            printf("<td>%s</td><td nowrap='nowrap'>%s</td></tr>",cargarCampanasTemporizacion($fila[4],$fila[0],$con),$botones);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th></th>
					  <th>Una</th>
					  <th>Dos</th>
					  <th>Tres</th>
					  <th>Temporización.</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}

//activar programa nodo campanas
function configuracionProgramasCampanas($idNodo,$con){
	
	//obtener los de este cliente
	$consulta=" AND idusuario=\"0\"";
	$patron3="SELECT idusuario FROM campanas_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
	$sql3=sprintf($patron3,$idNodo);
	$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 56690733545446356890097");
	if(mysqli_num_rows($respuesta3)>0){
		$fila3=mysqli_fetch_array($respuesta3);
		
		$consulta=" AND idusuario=\"".$fila3[0]."\"";
	}
	mysqli_free_result($respuesta3);
	
	//recorrer nodos de este cliente
	$patron="SELECT id,nombre FROM campanas_programas WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre ASC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96320999034633344545765899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Programa</th>
					  <th>Estado</th>
					</tr>
                </thead>
                <tbody>');
		
		$idNodoAux=0;
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			
			$permisos="n";
			$patron1="SELECT activo FROM campanas_programas_activos WHERE idnodo=\"%s\" AND idprograma=\"%s\"";
			$sql1=sprintf($patron1,$idNodo,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963211111445456745478234325227879958");
			if(mysqli_num_rows($respuesta1)>0){
				$fila1=mysqli_fetch_array($respuesta1);
				$permisos=$fila1[0];
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
			
			$botonCheck="<div id='programaCampanas_".$fila[0]."' onClick='actDesCheckProgramaCampana(this,\"".$fila[0]."\",\"".$idNodo."\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
			<input type='hidden' id='programaCampanas_".$fila[0]."_hidden' value='".$permisos."'>
			<i class='fas".$iconoClassCheck."' style='font-size:25px;".$colorIcon."' title='Activado'></i>
			</div>";
			
			
			//acciones
			$acciones="";
			/*if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2){
				$acciones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm ' 	 		onClick='editarLineaProgramasCampanasActivos(\"".$fila[0]."\",\"".$idNodo."\");return false;' title='Guardar'>
                	<span class='svg-icon svg-icon-md svg-icon-success'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Save.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<polygon points='0 0 24 0 24 24 0 24'/>
									<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
									<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
								</g>
							</svg><!--end::Svg Icon-->
							</span>
					</a>";
			}*/
			
			printf("<tr>
                        <td></td>
						<td>%s</td>
						<td>%s</td>
					</tr>",$fila[1],$botonCheck/*,$acciones*/);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
						<th>#</th>
						<th>Programa</th>
					  	<th>Estado</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//historial campanas programas
function programasCampanasHistorial($idNodo,$con){
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialProgramasCampanas"]) || isset($_SESSION["fechaFinHistorialProgramasCampanas"]) ){
		$consulta=" AND campanas_historial.fechaalta>=\"".$_SESSION["fechaIniHistorialProgramasCampanas"]."\" AND campanas_historial.fechaalta<=\"".$_SESSION["fechaFinHistorialProgramasCampanas"]."\"";
	}
	
	$patron="SELECT campanas_historial.id,campanas_historial.horaalta,campanas_historial.fechaalta,campanas_nodos.id,campanas_historial.idprograma FROM campanas_historial,campanas_nodos WHERE campanas_historial.idnodo=\"%s\" AND campanas_historial.idnodo=campanas_nodos.id AND campanas_nodos.guardado=\"s\" AND campanas_nodos.borrado=\"n\"%s ORDER BY campanas_historial.id DESC, campanas_historial.horaalta DESC, campanas_historial.id DESC";//LIMIT 0,50
	$sql=sprintf($patron,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323653463455899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Programa</th>
					  <th>Hora</th>
					  <th>Fecha</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
		
			//nombre programa
			$nombrePrograma="Sin datos";
			if($fila[4]>0){
				$patron2="SELECT nombre FROM campanas_programas WHERE id=\"%s\"";
				$sql2=sprintf($patron2,$fila[4]);
				$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 963534545464222286454509258");
				if(mysqli_num_rows($respuesta2)>0){
					$fila2=mysqli_fetch_array($respuesta2);
					$nombrePrograma=$fila2[0];
				}
				mysqli_free_result($respuesta2);
			}
			
			$botones="";
			
			printf("<tr>
                        <td></td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td nowrap='nowrap'>%s</td>
					</tr>",$nombrePrograma,$fila[1],convierteFechaBarra($fila[2]),$botones);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                        <th>#</th>
						<th>Programa</th>
					  	<th>Hora</th>
					  	<th>Fecha</th>
						<th>Acciones</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

//cargar temporizacion campanas
function cargarCampanasTemporizacion($seleccionado,$idLinea,$con){
	$desplegable="";
	
	$patron="SELECT id,tiemposegundos FROM campanas_temporizacion ORDER BY tiemposegundos ASC";
	$sql=sprintf($patron);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 1235457634665855453436466");
	$desplegable.=sprintf("<select class='form-control' name=\"temporizacion%s\" id=\"temporizacion%s\" >",$idLinea,$idLinea);
	//$desplegable.=sprintf("<option value='0'>Temporización</option>");
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

//conf luces de campanas
function configuracionHorarioRelojCampanas($idNodo,$con){
	
	$patron="SELECT id,l,m,x,j,v,s,d,horainicio,horafin FROM campanas_luces WHERE idnodo=\"%s\" AND borrado=\"n\"";
	$sql=sprintf($patron,$idNodo);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323653675654645454343455899");
	if(mysqli_num_rows($respuesta)>0){
		
		printf('<thead>
					<tr>
						<th>#</th>
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
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$acciones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm ' 	 		onClick='editarLineaLucesCampanas(\"".$fila[0]."\",\"".$idNodo."\");return false;' title='Guardar'>
                	<span class='svg-icon svg-icon-md svg-icon-success'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Save.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<polygon points='0 0 24 0 24 24 0 24'/>
									<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
									<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
								</g>
							</svg><!--end::Svg Icon-->
							</span>
					</a>
					<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm mx-3' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas 					seguro de que deseas eliminar este registro?\",28,\"".$fila[0]."\",\"".$idNodo."\",\"\");return false;' title='Borrar'>
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
			
			//lunes
			if($fila[1]=="s"){
				$clasCheckL="fa-check";
				$colorChekL="green";
				$hiddenActivoL="s";
				$titleActivoL="Activo";
			}else{
				$clasCheckL="fa-times";
				$colorChekL="red";
				$hiddenActivoL="n";
				$titleActivoL="Desactivado";
			}
			//martes
			if($fila[2]=="s"){
				$clasCheckM="fa-check";
				$colorChekM="green";
				$hiddenActivoM="s";
				$titleActivoM="Activo";
			}else{
				$clasCheckM="fa-times";
				$colorChekM="red";
				$hiddenActivoM="n";
				$titleActivoM="Desactivado";
			}
			//miercoles
			if($fila[3]=="s"){
				$clasCheckX="fa-check";
				$colorChekX="green";
				$hiddenActivoX="s";
				$titleActivoX="Activo";
			}else{
				$clasCheckX="fa-times";
				$colorChekX="red";
				$hiddenActivoX="n";
				$titleActivoX="Desactivado";
			}
			//jueves
			if($fila[4]=="s"){
				$clasCheckJ="fa-check";
				$colorChekJ="green";
				$hiddenActivoJ="s";
				$titleActivoJ="Activo";
			}else{
				$clasCheckJ="fa-times";
				$colorChekJ="red";
				$hiddenActivoJ="n";
				$titleActivoJ="Desactivado";
			}
			//viernes
			if($fila[5]=="s"){
				$clasCheckV="fa-check";
				$colorChekV="green";
				$hiddenActivoV="s";
				$titleActivoV="Activo";
			}else{
				$clasCheckV="fa-times";
				$colorChekV="red";
				$hiddenActivoV="n";
				$titleActivoV="Desactivado";
			}
			//sabado
			if($fila[6]=="s"){
				$clasCheckS="fa-check";
				$colorChekS="green";
				$hiddenActivoS="s";
				$titleActivoS="Activo";
			}else{
				$clasCheckS="fa-times";
				$colorChekS="red";
				$hiddenActivoS="n";
				$titleActivoS="Desactivado";
			}
			//domingo
			if($fila[7]=="s"){
				$clasCheckD="fa-check";
				$colorChekD="green";
				$hiddenActivoD="s";
				$titleActivoD="Activo";
			}else{
				$clasCheckD="fa-times";
				$colorChekD="red";
				$hiddenActivoD="n";
				$titleActivoD="Desactivado";
			}
			
			printf("<tr>
                        <td></td>
						<td><div id='luzL_%s' onclick='activarDesactivarCheck(this,\"\",\"\",\"\",\"\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
						<input type='hidden' id='luzL_%s_hidden' value=\"%s\">
						<i class='fas %s' style='font-size:25px;color: %s;' title='Activado'></i>
						</div></td>
				   		<td><div id='luzM_%s' onclick='activarDesactivarCheck(this,\"\",\"\",\"\",\"\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
						<input type='hidden' id='luzM_%s_hidden' value=\"%s\">
						<i class='fas %s' style='font-size:25px;color: %s;' title='Activado'></i>
						</div></td>
						<td><div id='luzX_%s' onclick='activarDesactivarCheck(this,\"\",\"\",\"\",\"\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
						<input type='hidden' id='luzX_%s_hidden' value=\"%s\">
						<i class='fas %s' style='font-size:25px;color: %s;' title='Activado'></i>
						</div></td>
						<td><div id='luzJ_%s' onclick='activarDesactivarCheck(this,\"\",\"\",\"\",\"\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
						<input type='hidden' id='luzJ_%s_hidden' value=\"%s\">
						<i class='fas %s' style='font-size:25px;color: %s;' title='Activado'></i>
						</div></td>
						<td><div id='luzV_%s' onclick='activarDesactivarCheck(this,\"\",\"\",\"\",\"\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
						<input type='hidden' id='luzV_%s_hidden' value=\"%s\">
						<i class='fas %s' style='font-size:25px;color: %s;' title='Activado'></i>
						</div></td>
						<td><div id='luzS_%s' onclick='activarDesactivarCheck(this,\"\",\"\",\"\",\"\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
						<input type='hidden' id='luzS_%s_hidden' value=\"%s\">
						<i class='fas %s' style='font-size:25px;color: %s;' title='Activado'></i>
						</div></td>
						<td><div id='luzD_%s' onclick='activarDesactivarCheck(this,\"\",\"\",\"\",\"\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
						<input type='hidden' id='luzD_%s_hidden' value=\"%s\">
						<i class='fas %s' style='font-size:25px;color: %s;' title='Activado'></i>
						</div></td>
						<td><input type='time' class='form-control' id='horaIniLuces%s' value=\"%s\"></td>
						<td><input type='time' class='form-control' id='horaFinLuces%s' value=\"%s\"></td>
						<td nowqrap='nowrap'>%s</td>
					</tr>",$fila[0],$fila[0],$hiddenActivoL,$clasCheckL,$colorChekL,$fila[0],$fila[0],$hiddenActivoM,$clasCheckM,$colorChekM,$fila[0],$fila[0],$hiddenActivoX,$clasCheckX,$colorChekX,$fila[0],$fila[0],$hiddenActivoJ,$clasCheckJ,$colorChekJ,$fila[0],$fila[0],$hiddenActivoV,$clasCheckV,$colorChekV,$fila[0],$fila[0],$hiddenActivoS,$clasCheckS,$colorChekS,$fila[0],$fila[0],$hiddenActivoD,$clasCheckD,$colorChekD,$fila[0],$fila[8],$fila[0],$fila[9],$acciones);
					
					printf("<script>$('#horaFinLuces%s').change(function(){
									if($('#horaFinLuces%s').val() == '00:00:00'){
									   $('#horaFinLuces%s').val('23:59:59')
									}
								});</script>",$fila[0],$fila[0],$fila[0]);
			
		}
	
	}else{
		printf('<thead>
					<tr>
						<th>#</th>
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
	}
}


?>