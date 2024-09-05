<?php 

													/*************************************
													 *									 *
													 *		 nodos MULTIWATER/depositos  *
													 *									 *
													 *************************************/

// CARGA empresas- listadoc
function cargaNodosMultiwatersList($con){
	
	$patron="SELECT id,nombre,idusuario,conexion FROM multiwater_nodos WHERE borrado=\"n\" AND guardado=\"s\" AND idusuario=\"%s\" ORDER BY nombre";
	$sql=sprintf($patron,calculaIdEmpresa($con));
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 963258");
	if(mysqli_num_rows($respuesta)>0){
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
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
			$respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9632585656");
			$fila1=mysqli_fetch_array($respuesta1);
			mysqli_free_result($respuesta1);
			
			//conexion
			$botonesAcciones="";
			if($fila[3]=="on"){
				$conexion="<span class='label label-lg label-light-success label-inline'>Encendido</span>";
				//mostrar el de apagar
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2){
					$botonesAcciones="<button class='btn btn-danger font-weight-bold btn-sm mr-2' onClick='onOffnodoMultiwater(\"".$fila[0]."\",\"".$fila[3]."\");'>Apagar</button>";
				}
			}else if($fila[3]=="off"){
				$conexion="<span class='label label-lg label-light-danger label-inline'>Apagado</span>";
				//mostrar el de encender
				if($_SESSION["permisossession"]==1 || $_SESSION["permisossession"]==2){
					$botonesAcciones="<button class='btn btn-success font-weight-bold btn-sm mr-2' onClick='onOffnodoMultiwater(\"".$fila[0]."\",\"".$fila[3]."\");'>Encender</button>";
				}
			}else{
				$conexion="<span class='label label-lg label-light-primary label-inline'>Sin datos</span>";
			}
			
			//pulsar tr
			$funcion=sprintf(" onClick='cargaLocation(\"index.php?s=4&i=%s\");'",$fila[0]);
			
			printf("<tr>
				<td></td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='clickable' %s>%s</td>
				<td class='' %s>%s</td>
				</tr>",$funcion,$fila[1],$funcion,$fila1[0],$funcion,$conexion,$funcion,$botonesAcciones);
		}
		echo "</tbody>";
		
		mysqli_free_result($respuesta);
	}else{
		printf('<thead>
					<tr>
					  <th>#</th>
					  <th>Nombre</th>
					  <th>Usuario</th>
					  <th>Conexión</th>
					  <th>Acciones</th>
					</tr>
                </thead>
                <tbody>');
	}
}

function cargaModosMultiwaterGenerico($seleccionada,$nombre,$con){
	printf("<select class='form-control' name=\"%s\" id=\"%s\" >",$nombre,$nombre);
	printf("<option value='0'>Selecciona un Modo</option>");

	$selectUno="";
	$selectDos="";	
	$selectTres="";	
	if($seleccionada=="aut"){
		$selectUno=" selected='selected'";
	}else if($seleccionada=="man"){
		$selectDos=" selected='selected'";
	}else if($seleccionada=="con"){
		$selectTres=" selected='selected'";
	}
	printf("<option value='aut'%s>Automático</option><option value='man'%s>Manual</option><option value='con'%s>Condicional</option>",$selectUno,$selectDos,$selectTres);

	printf("</select>");
}

?>