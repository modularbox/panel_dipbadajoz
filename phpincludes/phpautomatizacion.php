<?php 


													/*************************************
													 *									 *
													 *	       AUTOMATIZACION		     *
													 *									 *
													 *************************************/


//filtro clientes Programas Automatizacion list
function cargaUsuariosAutomatizacionProgramasFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 3121500653455456454455899414253451215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosAutomatizacionProgramas(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioAutomatizacionProgramasList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

// CARGA programas automatizacion
function cargaAutomatizacionProgramasList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioAutomatizacionProgramasList"]!="0"*/){
		if($_SESSION["usuarioAutomatizacionProgramasList"]!="0"){
			$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioAutomatizacionProgramasList"])."\"";
		}
	}else{
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	$patron="SELECT id,nombre,idusuario,descripcion FROM automatizacion_programa WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre ASC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323343565533442365545467545783543457879958");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>Descripción</th>
					  <th>Estado</th>
					  <th>Salidas</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//usuario
			$patron1="SELECT nombre FROM usuarios WHERE id=\"%s\"";
			$sql1=sprintf($patron1,$fila[2]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9635343243455533366764434653509258");
			$fila1=mysqli_fetch_array($respuesta1);
			mysqli_free_result($respuesta1);
			
			//botones acciones
			$botonesAcciones="";
			
			//salida 1
			$patron1="SELECT id FROM automatizacion_programa_salidas WHERE borrado=\"n\" AND idprograma=\"%s\" AND salida=\"1\"";
			$sql1=sprintf($patron1,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963234343311545783543457879958");
			if(mysqli_num_rows($respuesta1)>0){
				//$fila1=mysqli_fetch_array($respuesta1);
				$colorSalidaUno="98D572";//verde
			}else{
				$colorSalidaUno="D5727E";//rojo
			}
			mysqli_free_result($respuesta1);
			//salida 2
			$patron2="SELECT id FROM automatizacion_programa_salidas WHERE borrado=\"n\" AND idprograma=\"%s\" AND salida=\"2\"";
			$sql2=sprintf($patron2,$fila[0]);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 96323434331122545783543457879958");
			if(mysqli_num_rows($respuesta2)>0){
				//$fila2=mysqli_fetch_array($respuesta2);
				$colorSalidaDos="98D572";//verde
			}else{
				$colorSalidaDos="D5727E";//rojo
			}
			mysqli_free_result($respuesta2);
			//salida 3
			$patron3="SELECT id FROM automatizacion_programa_salidas WHERE borrado=\"n\" AND idprograma=\"%s\" AND salida=\"3\"";
			$sql3=sprintf($patron3,$fila[0]);
			$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 963233333434331122545783543457879958");
			if(mysqli_num_rows($respuesta3)>0){
				//$fila3=mysqli_fetch_array($respuesta3);
				$colorSalidaTres="98D572";//verde
			}else{
				$colorSalidaTres="D5727E";//rojo
			}
			mysqli_free_result($respuesta3);
			//salida 4
			$patron4="SELECT id FROM automatizacion_programa_salidas WHERE borrado=\"n\" AND idprograma=\"%s\" AND salida=\"4\"";
			$sql4=sprintf($patron4,$fila[0]);
			$respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 96323334344444331122545783543457879958");
			if(mysqli_num_rows($respuesta4)>0){
				//$fila4=mysqli_fetch_array($respuesta4);
				$colorSalidaCuatro="98D572";//verde
			}else{
				$colorSalidaCuatro="D5727E";//rojo
			}
			mysqli_free_result($respuesta4);
			//salida 5
			$patron5="SELECT id FROM automatizacion_programa_salidas WHERE borrado=\"n\" AND idprograma=\"%s\" AND salida=\"5\"";
			$sql5=sprintf($patron5,$fila[0]);
			$respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 9632333434444433112255553543457879958");
			if(mysqli_num_rows($respuesta5)>0){
				//$fila5=mysqli_fetch_array($respuesta5);
				$colorSalidaCinco="98D572";//verde
			}else{
				$colorSalidaCinco="D5727E";//rojo
			}
			mysqli_free_result($respuesta5);
			//salida 6
			$patron6="SELECT id FROM automatizacion_programa_salidas WHERE borrado=\"n\" AND idprograma=\"%s\" AND salida=\"6\"";
			$sql6=sprintf($patron6,$fila[0]);
			$respuesta6=mysqli_query($con,$sql6) or die ("Error al buscar 9632336634444433112255553543457879958");
			if(mysqli_num_rows($respuesta6)>0){
				//$fila6=mysqli_fetch_array($respuesta6);
				$colorSalidaSeis="98D572";//verde
			}else{
				$colorSalidaSeis="D5727E";//rojo
			}
			mysqli_free_result($respuesta6);
			
			$botonesAcciones=" <span class='svg-icon svg-icon-primary svg-icon-1.5x'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 256 512'><path d='M160 64c0-11.8-6.5-22.6-16.9-28.2s-23-5-32.9 1.6l-96 64C-.5 111.2-4.4 131 5.4 145.8s29.7 18.7 44.4 8.9L96 123.8V416H32c-17.7 0-32 14.3-32 32s14.3 32 32 32h96 96c17.7 0 32-14.3 32-32s-14.3-32-32-32H160V64z' fill='#".$colorSalidaUno."'/></svg></span>
			<span class='svg-icon svg-icon-primary svg-icon-1.5x'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 256 512'><path d='M142.9 96c-21.5 0-42.2 8.5-57.4 23.8L54.6 150.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L40.2 74.5C67.5 47.3 104.4 32 142.9 32C223 32 288 97 288 177.1c0 38.5-15.3 75.4-42.5 102.6L109.3 416H288c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9L200.2 234.5c15.2-15.2 23.8-35.9 23.8-57.4c0-44.8-36.3-81.1-81.1-81.1z' fill='#".$colorSalidaDos."'/></svg></span>
			<span class='svg-icon svg-icon-primary svg-icon-1.5x'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 256 512'><path d='M64 64c0-17.7 14.3-32 32-32H336c13.2 0 25 8.1 29.8 20.4s1.5 26.3-8.2 35.2L226.3 208H248c75.1 0 136 60.9 136 136s-60.9 136-136 136H169.4c-42.4 0-81.2-24-100.2-61.9l-1.9-3.8c-7.9-15.8-1.5-35 14.3-42.9s35-1.5 42.9 14.3l1.9 3.8c8.1 16.3 24.8 26.5 42.9 26.5H248c39.8 0 72-32.2 72-72s-32.2-72-72-72H144c-13.2 0-25-8.1-29.8-20.4s-1.5-26.3 8.2-35.2L253.7 96H96C78.3 96 64 81.7 64 64z' fill='#".$colorSalidaTres."'/></svg></span>
			<span class='svg-icon svg-icon-primary svg-icon-1.5x'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 256 512'><path d='M189 77.6c7.5-16 .7-35.1-15.3-42.6s-35.1-.7-42.6 15.3L3 322.4c-4.7 9.9-3.9 21.5 1.9 30.8S21 368 32 368H256v80c0 17.7 14.3 32 32 32s32-14.3 32-32V368h32c17.7 0 32-14.3 32-32s-14.3-32-32-32H320V160c0-17.7-14.3-32-32-32s-32 14.3-32 32V304H82.4L189 77.6z' fill='#".$colorSalidaCuatro."'/></svg></span>
			<span class='svg-icon svg-icon-primary svg-icon-1.5x'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 256 512'><path d='M32.5 58.3C35.3 43.1 48.5 32 64 32H256c17.7 0 32 14.3 32 32s-14.3 32-32 32H90.7L70.3 208H184c75.1 0 136 60.9 136 136s-60.9 136-136 136H100.5c-39.4 0-75.4-22.3-93-57.5l-4.1-8.2c-7.9-15.8-1.5-35 14.3-42.9s35-1.5 42.9 14.3l4.1 8.2c6.8 13.6 20.6 22.1 35.8 22.1H184c39.8 0 72-32.2 72-72s-32.2-72-72-72H32c-9.5 0-18.5-4.2-24.6-11.5s-8.6-16.9-6.9-26.2l32-176z' fill='#".$colorSalidaCinco."'/></svg></span>
			<span class='svg-icon svg-icon-primary svg-icon-1.5x'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 256 512'><path d='M232.4 84.7c11.4-13.5 9.7-33.7-3.8-45.1s-33.7-9.7-45.1 3.8L38.6 214.7C14.7 242.9 1.1 278.4 .1 315.2c0 1.4-.1 2.9-.1 4.3c0 .2 0 .3 0 .5c0 88.4 71.6 160 160 160s160-71.6 160-160c0-85.5-67.1-155.4-151.5-159.8l63.9-75.6zM64 320c0-53 43-96 96-96s96 43 96 96s-43 96-96 96s-96-43-96-96z' fill='#".$colorSalidaSeis."'/></svg></span>
			";
			
			//estado, activo
			$activo="n";
			//activo s, para traer los q esten activos por si estan en otras placas pero no activo
			$patron2="SELECT activo FROM automatizacion_programas_activos WHERE idprograma=\"%s\" AND activo=\"s\"";
			$sql2=sprintf($patron2,$fila[0]);
			$respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 9630065667552322265545467545783543457879950");
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
			
			
			//pulsar tr
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=32&i=%s\");'",$fila[0]);
			
			printf("<tr>
				<td></td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				</tr>",$funcion,$fila[1],$funcion,$fila1[0],$funcion,$fila[3],$funcion,$conexion,"",$botonesAcciones);
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
					  <th>Salidas</th>
					</tr>
                </thead>
                <tbody>');
	}
}

//conf salidas reles automatizacion
function configuracionSalidasAutomatizacion($idprograma,$salida,$con){
	if($salida>0 && $salida<7){
		$salida=intval($salida);
	}else{
		$salida=0;
	}
	
	$patron="SELECT id,l,m,x,j,v,s,d,horainicio,horafin,salida FROM automatizacion_programa_salidas WHERE idprograma=\"%s\" AND borrado=\"n\" AND salida=\"%s\"";
	$sql=sprintf($patron,$idprograma,$salida);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963236536756546454543434558998");
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
			
			$acciones="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm ' 	 		onClick='editarLineaProgramaAutomatizacion(\"".$fila[0]."\",\"".$idprograma."\",\"".$fila[10]."\");return false;' title='Guardar'>
                	<span class='svg-icon svg-icon-md svg-icon-success'><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Save.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
								<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
									<polygon points='0 0 24 0 24 24 0 24'/>
									<path d='M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z' fill='#000000' fill-rule='nonzero'/>
									<rect fill='#000000' opacity='0.3' x='12' y='4' width='3' height='5' rx='0.5'/>
								</g>
							</svg><!--end::Svg Icon-->
							</span>
					</a>
					<a href='#' class='btn btn-icon btn-light btn-hover-danger btn-sm mx-3' onClick='confirmacion(\"warning\",\"Eliminar Registro\",\"¿Estas 					seguro de que deseas eliminar este registro?\",30,\"".$fila[0]."\",\"".$idprograma."\",\"".$fila[10]."\");return false;' title='Borrar'>
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
						<td><input type='time' class='form-control' style='width:117px;' id='horaIniAutomatizacion%s' value=\"%s\" step=\"1\"></td>
						<td><input type='time' class='form-control' style='width:117px;' id='horaFinAutomatizacion%s' value=\"%s\" step=\"1\"></td>
						<td nowqrap='nowrap'>%s</td>
					</tr>",$fila[0],$fila[0],$hiddenActivoL,$clasCheckL,$colorChekL,$fila[0],$fila[0],$hiddenActivoM,$clasCheckM,$colorChekM,$fila[0],$fila[0],$hiddenActivoX,$clasCheckX,$colorChekX,$fila[0],$fila[0],$hiddenActivoJ,$clasCheckJ,$colorChekJ,$fila[0],$fila[0],$hiddenActivoV,$clasCheckV,$colorChekV,$fila[0],$fila[0],$hiddenActivoS,$clasCheckS,$colorChekS,$fila[0],$fila[0],$hiddenActivoD,$clasCheckD,$colorChekD,$fila[0],$fila[8],$fila[0],$fila[9],$acciones);
					
					printf("<script>$('#horaFinAutomatizacion%s').change(function(){
									if($('#horaFinAutomatizacion%s').val() == '00:00:00'){
									   $('#horaFinAutomatizacion%s').val('23:59:59')
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

// CARGA AUTOMATIZACION-nodos safey
function cargaNodosSafeyAutomatizacionList($con){
	$consulta="";
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioAutomatizacionSafeyList"]!="0"*/){
		if($_SESSION["usuarioAutomatizacionList"]!="0"){
			$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioAutomatizacionList"])."\"";
		}
	}else {
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	if($_SESSION["conexionAutomatizacionList"]!=""){
		$consulta.=" AND conexion=\"".quitaComillasD($_SESSION["conexionAutomatizacionList"])."\"";
	}
	
	$patron="SELECT id,nombre,idusuario,conexion,ubicacion FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323534546755678787879958");
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
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963534543453409258");
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
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=34&i=%s\");'",$fila[0]);
			
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

//filtro clientes Automatizacion list
function cargaUsuariosAutomatizacionFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre ASC, apellidos ASC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 3121523587009987741236456455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosAutomatizacion(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioAutomatizacionList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

//filtro estado Automatizacion list
function cargaEstadosAutomatizacionFiltro($con){
	$selectedUno="";
	$selectedDos="";
	if($_SESSION["conexionAutomatizacionList"]=="on"){
		$selectedUno=" selected";
	}else if($_SESSION["conexionAutomatizacionList"]=="off"){
		$selectedDos=" selected";
	}
	
	printf("<select class='form-control' id='selectConexionFiltro' onChange='filtrarConexionAutomatizacion(this);'><option value=''>Selecciona Conexión:</option>");
	printf("<option value=\"on\" %s>Online</option><option value=\"off\" %s>Offline</option>",$selectedUno,$selectedDos);	
	printf('</select>');
}

//activar programa nodo automatizacion
function configuracionProgramasAutomatizacion($idNodo,$con){
	
	//obtener los de este cliente
	$consulta=" AND idusuario=\"0\"";
	$patron3="SELECT idusuario FROM safey_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND id=\"%s\"";
	$sql3=sprintf($patron3,$idNodo);
	$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 5669075345335654545446356890097");
	if(mysqli_num_rows($respuesta3)>0){
		$fila3=mysqli_fetch_array($respuesta3);
		
		$consulta=" AND idusuario=\"".$fila3[0]."\"";
	}
	mysqli_free_result($respuesta3);
	
	//recorrer nodos de este cliente
	$patron="SELECT id,nombre FROM automatizacion_programa WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre ASC";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632099903345346333443454545765899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Programa</th>
					  <th>Estado</th>
					  <th>Ir</th>
					</tr>
                </thead>
                <tbody>');
		
		$idNodoAux=0;
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$permisos="n";
			$patron1="SELECT activo FROM automatizacion_programas_activos WHERE idnodo=\"%s\" AND idprograma=\"%s\"";
			$sql1=sprintf($patron1,$idNodo,$fila[0]);
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 963211111445455656745478234325227879958");
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
			
			$botonCheck="<div id='programaAutomatizacion_".$fila[0]."' onClick='actDesCheckProgramaAutomatizacion(this,\"".$fila[0]."\",\"".$idNodo."\");' style='width:33px;height:33px;border: 1px solid #000000;border-radius: .42rem;background-color: #ffffff;cursor: pointer;border: 1px solid #b5b5c3;-webkit-box-align: center;padding-top: 3px;text-align:center'>
			<input type='hidden' id='programaAutomatizacion_".$fila[0]."_hidden' value='".$permisos."'>
			<i class='fas".$iconoClassCheck."' style='font-size:25px;".$colorIcon."' title='Activado'></i>
			</div>";
			
			$botonIr="<a href='#' class='btn btn-icon btn-light btn-hover-success btn-sm' onClick='cargaLocation(\"index.php?s=32&i=".$fila[0]."\");return false;' title='Ir al programa'>
								<span class='svg-icon svg-icon-primary svg-icon-2x'><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Navigation\Right-2.svg--><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='24px' height='24px' viewBox='0 0 24 24' version='1.1'>
									<g stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'>
										<polygon points='0 0 24 0 24 24 0 24'/>
										<rect fill='#000000' opacity='0.3' transform='translate(8.500000, 12.000000) rotate(-90.000000) translate(-8.500000, -12.000000) ' x='7.5' y='7.5' width='2' height='9' rx='1'/>
										<path d='M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z' fill='#000000' fill-rule='nonzero' transform='translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997) '/>
									</g>
								</svg><!--end::Svg Icon--></span>
							</a>";
			
			//acciones
			$acciones="";
			
			printf("<tr>
                        <td></td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
					</tr>",$fila[1],$botonCheck,$botonIr/*,$acciones*/);
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
						<th>#</th>
						<th>Programa</th>
					  	<th>Estado</th>
					  <th>Ir</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}


//historial acciones automatizacion
function historialAccionesAutomatizacion($idNodo,$con){
	$consulta="";
	if(isset($_SESSION["fechaIniHistorialSalidasAutomatizacion"]) && isset($_SESSION["fechaFinHistorialSalidasAutomatizacion"]) ){
		$consulta=" AND automatizacion_historial.fechaalta>=\"".$_SESSION["fechaIniHistorialSalidasAutomatizacion"]."\" AND automatizacion_historial.fechaalta<=\"".$_SESSION["fechaFinHistorialSalidasAutomatizacion"]."\"";
	}
	
	$patron="SELECT automatizacion_historial.id,automatizacion_historial.salida,automatizacion_historial.horaalta,automatizacion_historial.fechaalta,automatizacion_historial.idprograma,automatizacion_historial.modo, automatizacion_historial.estado FROM automatizacion_historial,safey_nodos WHERE automatizacion_historial.idnodo=\"%s\" AND automatizacion_historial.idnodo=safey_nodos.id AND safey_nodos.guardado=\"s\" AND safey_nodos.borrado=\"n\"%s ORDER BY automatizacion_historial.fechaalta DESC, automatizacion_historial.horaalta DESC, automatizacion_historial.id DESC";
	$sql=sprintf($patron,$idNodo,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96323463455899");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
                      <th>#</th>
					  <th>Salida</th>
					  <th>Programa</th>
					   <th>Modo</th>
					  <th>Estado</th>
					  <th title="Puede tener un desfase de algunos segundos.">Hora</th>
					  <th>Fecha</th>
					</tr>
                </thead>
                <tbody>');
		
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			//salida
			$campoConsulta="";
			$nombreSalida="";
			$nombrePrograma="";
			if($fila[1]==1){
				$campoConsulta="salidauno";
				$nombreSalida="Salida 1";
			}else if($fila[1]==2){
				$campoConsulta="salidados";
				$nombreSalida="Salida 2";
			}else if($fila[1]==3){
				$campoConsulta="salidatres";
				$nombreSalida="Salida 3";
			}else if($fila[1]==4){
				$campoConsulta="salidacuatro";
				$nombreSalida="Salida 4";
			}else if($fila[1]==5){
				$campoConsulta="salidacinco";
				$nombreSalida="Salida 5";
			}else if($fila[1]==6){
				$campoConsulta="salidaseis";
				$nombreSalida="Salida 6";
			}
			if($campoConsulta!=""){
				$patron1="SELECT %s,nombre FROM automatizacion_programa WHERE id=\"%s\"";
				$sql1=sprintf($patron1,$campoConsulta,$fila[4]);
				$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9633454598983456486454509258");
				if(mysqli_num_rows($respuesta1)>0){
					$fila1=mysqli_fetch_array($respuesta1);
					$nombreSalida.=$fila1[0];
					$nombrePrograma=$fila1[1];
				}
				mysqli_free_result($respuesta1);
			}
			$estado="";
			if($fila[6]=="on"){
				$estado="Encencido";
			}else if($fila[6]=="off"){
				$estado="Apagado";
			}
			$modo="";
			if($fila[5]=="a"){
				$modo="Automático";
			}else if($fila[5]=="m"){
				$modo="Manual";
			}
			
			printf("<tr>
						<td></td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td title='Puede tener un desfase de algunos segundos.'>%s</td>
						<td>%s</td>
					</tr>",$nombreSalida,$nombrePrograma,$modo,$estado,$fila[2],convierteFechaBarra($fila[3]));	
			
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
                        <th>#</th>
					  	<th>Salida</th>
						<th>Programa</th>
						<th>Modo</th>
						<th>Estado</th>
					  	<th>Hora</th>
					  	<th>Fecha</th>
					</tr>
                </thead>
                <tbody></tbody>');
	}
}

function comprobarSolapamientoDeProgramas($nodo,$idPrograma,$con){
	$programaDuplicado="n";
	
	$patron3="SELECT idprograma FROM automatizacion_programas_activos WHERE idnodo=\"%s\" AND activo=\"s\" ";
	$sql3=sprintf($patron3,$nodo);
	$respuesta3=mysqli_query($con,$sql3)or die ("Error al buscar 453634563");
	if(mysqli_num_rows($respuesta3)>0){
		for($n=0;$n<mysqli_num_rows($respuesta3);$n++){
			$fila3=mysqli_fetch_array($respuesta3);
			//sabemos los programas del nodo activos (excepto el que queremos cambiar)

			//if($programaDuplicado=="s"){
			//	break;
			//}
			//obtener la configuración del programa que vamos a activar
			$patron4="SELECT l,m,x,j,v,s,d,horainicio,horafin,salida FROM automatizacion_programa_salidas WHERE borrado=\"n\" AND idprograma=\"%s\"";
			$sql4=sprintf($patron4,$idPrograma);
			$respuesta4=mysqli_query($con,$sql4)or die ("Error al buscar 845203475928345");
			if(mysqli_num_rows($respuesta4)>0){
				for($p=0;$p<mysqli_num_rows($respuesta4);$p++){
					$fila4=mysqli_fetch_array($respuesta4);
					$consulta2="";
					if($fila4[0]=="s"){
						if($consulta2!=""){
							$consulta2.=" OR ";
						}else{
							$consulta2.=" AND( ";
						}
						$consulta2.="  l=\"s\" AND (horafin > '".$fila4[7]."' AND horainicio <'".$fila4[8]."') ";
					}
					if($fila4[1]=="s"){

						if($consulta2!=""){
							$consulta2.=" OR ";
						}else{
							$consulta2.=" AND( ";
						}

						$consulta2.="  m=\"s\" AND (horafin > '".$fila4[7]."' AND horainicio <'".$fila4[8]."')  ";
					}
					if($fila4[2]=="s"){

						if($consulta2!=""){
							$consulta2.=" OR ";
						}else{
							$consulta2.=" AND( ";
						}

						$consulta2.="  x=\"s\" AND (horafin > '".$fila4[7]."' AND horainicio <'".$fila4[8]."') ";
					}
					if($fila4[3]=="s"){
						if($consulta2!=""){
							$consulta2.=" OR ";
						}else{
							$consulta2.=" AND( ";
						}
						$consulta2.=" j=\"s\" AND (horafin > '".$fila4[7]."' AND horainicio <'".$fila4[8]."') ";
					}
					if($fila4[4]=="s"){
						if($consulta2!=""){
							$consulta2.=" OR ";
						}else{
							$consulta2.=" AND(";
						}
						$consulta2.=" v=\"s\" AND (horafin > '".$fila4[7]."' AND horainicio <'".$fila4[8]."') ";
					}
					if($fila4[5]=="s"){
						if($consulta2!=""){
							$consulta2.=" OR ";
						}else{
							$consulta2.=" AND( ";
						}
						$consulta2.="  s=\"s\" AND (horafin > '".$fila4[7]."' AND horainicio <'".$fila4[8]."')  ";
					}
					if($fila4[6]=="s"){
						if($consulta2!=""){
							$consulta2.=" OR ";
						}else{
							$consulta2.=" AND( ";
						}
						$consulta2.="  d=\"s\" AND (horafin > '".$fila4[7]."' AND horainicio <'".$fila4[8]."')  ";
					}
					if($consulta2!=""){
						//tenemos todas las salidas del programa que queremos activar
						$patron5="SELECT id FROM automatizacion_programa_salidas WHERE borrado=\"n\" AND idprograma<>\"%s\" AND salida=\"%s\" %s ) ";
						$sql5=sprintf($patron5,/*$fila3[0]*/$idPrograma,$fila4[9],$consulta2);
						$respuesta5=mysqli_query($con,$sql5)or die("Error al buscar 852938745962346467");
						if(mysqli_num_rows($respuesta5)>0){
							//si coincide es porque el programa que queremos activar comparte rangos de fechas con otros programas ya activados.
							$programaDuplicado="s";
							break;
						}
					}
				}
			} 
		}
	}
	return $programaDuplicado;
}

?>