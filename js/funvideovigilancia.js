//filtro usuarios videovigilancia
function filtrarUsuariosVideoVigilancia(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "111", 'u': u },
		type : 'POST',
		success : function(data){
			//if(data!="n"){
				location.href="index.php?s=38";
			//}
		}
	});
}

//crear nueva camara
function anadirCamaraNodoVideovigilancia(){
	var u=$("#selectUsuariosFiltro option:selected").val();
	var url=$("#urlCamara").val();
	if(u>0 && url!=""){
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "112", 'u': u, 'url': url },
			type : 'POST',
			success : function(data){
				//if(data!="n"){
					location.href="index.php?s=38";
				//}
			}
		});
	}
}

//eliminar camara
function borrarCamarasNodosVideovigilancia(idCam){
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "113", 'idCam': idCam },
		type : 'POST',
		success : function(data){
			//if(data!="n"){
				location.href="index.php?s=38";
			//}
		}
	});
}

//recargar interfaz camaras
function recargarCamarasMostrar(){
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "114"},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				var respuesta=data.split("@#");
				for (i = 0; i < respuesta.length; i++) {
					if($("#videoVigilanciaCam"+i)){
					   $("#videoVigilanciaCam"+i).attr("src",respuesta[i]);
					}
				}
			}
		}
	});
}