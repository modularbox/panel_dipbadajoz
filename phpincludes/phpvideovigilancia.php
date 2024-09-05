<?php 


													/*************************************
													 *									 *
													 *	        VIDEOVIGILANCIA		     *
													 *									 *
													 *************************************/
// CARGA videovigilancia
function cargaNodosVideoVigilanciaList($con){
	$consulta="";
	
	if($_SESSION["permisossession"]==1 /*&& $_SESSION["usuarioVideovigilanciaList"]!="0"*/){
		if($_SESSION["usuarioVideovigilanciaList"]!="0"){
			$consulta.=" AND idusuario=\"".quitaComillasD($_SESSION["usuarioVideovigilanciaList"])."\"";
		}
	}else{
		$consulta.=" AND idusuario=\"".calculaIdEmpresa($con)."\"";
	}
	
	//solo en este caso para no mostrar nada
	if($consulta==""){
		$consulta.=" AND idusuario=\"-99\"";
	}
	
	$patron="SELECT id,nombre,url FROM videovigilancia_nodos WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre ";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 96322323343534236554546754578009857879958");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$time="&t=".time();//url time
			$urlCamara=$fila[2].$time;//"https://www.youtube.com/embed/qIHXpnASPAA";
			
			
			//bloque que agrupa dos
			if(($i % 2 ==0)){//es impar
				if($i>0){//cerrar div
					printf("</div>");
				}
				printf("<div class='row'>");//div que agrupa dos
			}
			
			$botonBorrar="";
			if($_SESSION["permisossession"]==1 /*|| $_SESSION["permisossession"]==2*/){
				$botonBorrar="<a href='javascript: void(0)' class='btn font-weight-bold btn-light-danger btn-shadow ml-2' onClick='confirmacion(\"warning\",\"Eliminar Cámara\",\"¿Estas seguro de que deseas eliminar esta cámara?\",32,\"".$fila[0]."\",\"\",\"\");return false;'>Eliminar</a>";
			}
			
			//bloque con img bloque
			$contadorImg=$i;//esto para el for del js para recargar
			
			/*printf("<div class='col-lg-6'>
						<!--begin::Video-->
						<div class='embed-responsive embed-responsive-16by9'>
							<img id='videoVigilanciaCam%s' src='%s'>
						</div>
						%s
						<!--end::Video-->
					</div>",$contadorImg,$urlCamara,$botonBorrar);*/

			printf("<div class='card card-custom gutter-b col-lg-6 '>
					  <img id='videoVigilanciaCam%s' class='card-img-top' src='%s' >
					  <div class='card-body' style='padding-left: 0; margin: 0 auto;''>
						%s
					  </div>
					</div>",$contadorImg,$urlCamara,$botonBorrar);
            
		}
		printf("</div>");//cerrar ultimo div
		mysqli_free_result($respuesta);
	}else{
		printf("<h1 class='h3 font-weight-bolder text-dark mb-6'>No hay cámaras asociadas a este usuario.</h1>");
	}
}


//filtro clientes VideoVigilancia list
function cargaUsuariosVideoVigilanciaFiltro($con){
	
	$consulta="";
	if($_SESSION["permisossession"]==1){
		$consulta="AND (permisos=\"2\" OR permisos=\"1\")";
	}else{
		$consulta.=" AND permisos=\"2\"";
	}
	
	$patron="SELECT id,nombre,apellidos FROM usuarios WHERE borrado=\"n\" AND guardado=\"s\"%s ORDER BY nombre";
	$sql=sprintf($patron,$consulta);
	$respuesta=mysqli_query($con,$sql) or die ("Error 312152358343755345443455456454455899121215");
	printf("<select class='form-control' id='selectUsuariosFiltro' onChange='filtrarUsuariosVideoVigilancia(this);'><option value='0'>Selecciona Cliente:</option>");
	if(mysqli_num_rows($respuesta)>0){
		for($i=0;$i<mysqli_num_rows($respuesta);$i++){
			$fila=mysqli_fetch_array($respuesta);
			
			$selected="";
			if($fila[0]==$_SESSION["usuarioVideovigilanciaList"]){
				$selected=" selected";
			}

			printf("<option value=\"%s\" %s>%s</option>",$fila[0],$selected,$fila[1]." ".$fila[2]);
		}
		mysqli_free_result($respuesta);
	}
	printf('</select>');
}

?>