//filtro conexion audio
function filtrarConexionAudio(elemento){
	var e=$("#"+elemento.id+" option:selected").val();
	mostrarRecargarTabla("tablaAudioNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "107", 'e': e },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaAudioNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
				cargarTabla.init("tablaAudioNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//filtro usuarios campanas
function filtrarUsuariosAudio(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaAudioNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "108", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaAudioNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
				cargarTabla.init("tablaAudioNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//borrar audios nodo
function borraAudioNodo(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "109", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=36";
		}
	});
}

//activar parada de emergencia audio
function activarParadaEmergenciaAudio(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "110", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
            $("#paradaDeEmergenciaModal").modal('toggle');
            if(data=="s"){
               
            }
            //console.log(data);
		}
	});
}

function addZero(input) {
    var val = $(input).val();

    if(val/10 < 1) {
        $(input).val(0 + val);
    }
}

//filtrar historial audios
function filtrarHistorialColaAudios(n){
	var fechaIni=$("#fechaIniHistorialAudios").val();
	var fechaFin=$("#fechaFinHistorialAudios").val();
	//$('#cargando').show();
    mostrarRecargarTabla("tablaHistorialAudios");
    
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "130", 'fechaIni': fechaIni, 'fechaFin': fechaFin, "n": n },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data!="n"){
				//Swal.fire('Correcto','Ok.','success');
				
			   	$("#tablaHistorialAudios").html(data);
                
                var columnasTabHistorial= [null,{ "width": "24%" },{ "width": "10%" },{ "width": "19%" },{ "width": "10%" },{ "width": "19%" },{ "width": "15%" },{ "width": "10%" }];
        		cargarTabla.init("tablaHistorialAudios",columnasTabHistorial,[4, "asc"],50,true);
			}
		}
	});
}
//subir fichero audio
function borraFicheroAudio(id,usuario){
	$("#cargandotablaFicheroAudios").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "131", "id": id, "usuario": usuario},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaFicheroAudios").html(data);
				
				Swal.fire('Correcto','Eliminado correctamente.','success');
				
				var columnasTablaFicheros= [null,{ "width": "45%" },{ "width": "15%" },{ "width": "30%" },{ "width": "10%" }];
        		cargarTabla.init("tablaFicheroAudios",columnasTablaFicheros,[0, "asc"],50,true);
			}
		}
	});
}

//subir fichero audio
function subirFicheroAudio(seccion){
	
	var idUsuario=$("#selectUsuariosAudiosSubirFiltro option:selected").val();
    var archivo = $('#file_upload').prop('files')[0]; 
    var textoAudio = $('#textoAudio').val(); 
	if(idUsuario>0 && nombreAudio!="" && (seccion==1 || seccion==2)){
		var nombreAudio="";
		if(seccion==1){//subidor
		   nombreAudio=$("#nombreAudioSubir").val();
		}else if(seccion==2){//convertir texto a audio
			nombreAudio=$("#nombreAudioConvertir").val();
		}
		
		$("#cargandoSubidorFicheroAudios").show();
		var form_data = new FormData();   
		form_data.append('op', 132);
		form_data.append('file_upload', archivo); 
		form_data.append('idUsuario', idUsuario); 
		form_data.append('nombreAudio', nombreAudio);  
		form_data.append('textoAudio', textoAudio);  
		form_data.append('seccion', seccion);                        
		$.ajax({
			url: 'adminajax.php',
			dataType: 'text',
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,                         
			type: 'post',
			success: function(data){
				$("#cargandoSubidorFicheroAudios").hide();
				var respuesta=data.split("@#");
				if(respuesta[0]=="s"){
					Swal.fire('Correcto','Se ha guardado el audio correctamente.','success');

					$("#nombreAudio").val("");//limpiar

					/*start devolver contenido tabla audios*/			
					$("#tablaFicheroAudios").html(respuesta[1]);

					var columnasTablaFicheros= [null,{ "width": "45%" },{ "width": "15%" },{ "width": "30%" },{ "width": "10%" }];
					cargarTabla.init("tablaFicheroAudios",columnasTablaFicheros,[0, "asc"],50,true);
					/*end devolver contenido tabla audios*/	

				}else if(respuesta[0]=="n" || respuesta[0]=="l"){
					Swal.fire('Error','No se ha guardado el fichero, prueba nuevamente, completa todos los campos.','error');
				}else if(respuesta[0]=="e"){
					Swal.fire('Tipo fichero no válido','El tipo de documento no es compatible','warning');
				}				
			}
		 });
	}else{
		Swal.fire('Faltan datos por completar','Por favor completa los campos e intentalo nuevamente.','warning');
	}
}


//abrir modal horario audio
function abrirModalConfiguracionAudio(idAudio,opcion){
	$("#numReproduccionesAudio").val("2");//restablecer siempre al abrir la modal
	var usu=$("#selectUsuariosAudiosSubirFiltro option:selected").val();
	if(usu>0 && (opcion==1 || opcion==2) ){
		
		if(opcion==1){//si mostrar
			$("#divDiaReproducirAudio").show();
			$("#divHoraReproducirAudio").show();
			$("#btnConfirmarReproducirAudio").show();
			$("#btnReprodicirAhoraAudio").hide();
			$("#abrirProgramacionAudioLabel").html("Programar Audio");
		}else if(opcion==2){//no mostrar
			$("#divDiaReproducirAudio").hide();
			$("#divHoraReproducirAudio").hide();
			$("#btnConfirmarReproducirAudio").hide();
			$("#btnReprodicirAhoraAudio").show();
			$("#abrirProgramacionAudioLabel").html("Enviar Audio Ahora");
		}
		   
		   
	   	$("#idAudioConf").val(idAudio);
		$("#abrirProgramacionAudio").modal("show");
		
		if(usu>0){
			$.ajax({
				url : 'adminajax.php',
				data : { 'op': "136", "usu": usu},
				type : 'POST',
				success : function(data){
					
					var respuesta=data.split("@#");
					$("#horaIniAudio").val(respuesta[1]);
					$("#minIniAudio").val(respuesta[2]);
					if(respuesta[0]!="n"){
						//$("#divSeleccionarNodoCliente").html(respuesta[0]);
					}else{
						Swal.fire('Problemas al mostrar los datos.','Intenta nuevamente.','error');
					}
				}
			});
		}else{
			Swal.fire('No has seleccionado un cliente','Selecciona un cliente/usuario.','warning');
		}
		
	}else{
		Swal.fire('Faltan datos por completar','Selecciona un cliente/usuario.','warning');
	}
	
}

//guardar horario audio
function guardarProgramacionAudio(opcion){
	
	$("#cargandotablaFicheroAudios").show();
	
	var idAudio=$("#idAudioConf").val();
	var idUsuario=$("#selectUsuariosAudiosSubirFiltro option:selected").val();
	var horaIniAudio=$("#horaIniAudio").val();
	var minIniAudio=$("#minIniAudio").val();
	var fechaReproducirAudio=$("#fechaReproducirAudio").val();
	var arrayNodos=$("#kt_select2_3").val();
	var numReproducciones=$("#numReproduccionesAudio").val();;
	
	if(idAudio>0 && arrayNodos.length>0 && idUsuario>0 && ((fechaReproducirAudio!="" && horaIniAudio!="" && minIniAudio!="" && opcion==1) || opcion==2) && numReproducciones>=1){
		
		/*start array datos nodos, enviar texto*/
		var textoNodos="";
		for(let i=0;i<arrayNodos.length;i++){
			if(i>0){
			   textoNodos+=",";
			}
			textoNodos+=arrayNodos[i];
		}
		/*end array datos nodos, enviar texto*/
		
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "133", "idUsuario": idUsuario, "idAudio": idAudio, "horaIniAudio": horaIniAudio, "minIniAudio": minIniAudio, "fechaReproducirAudio": fechaReproducirAudio, "nodos": textoNodos, "numReproducciones": numReproducciones, "opcion": opcion},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#cargandotablaFicheroAudios").hide();
					$("#kt_select2_3").val("");;	
					Swal.fire('Correcto','Se ha programado el audio según el horario indicado.','success');
					
					$("#abrirProgramacionAudio").modal("hide");
				}else{
					Swal.fire('Problemas programar el audio.','Intentalo nuevamente.','error');
				}
			}
		});
	}else{
		Swal.fire('Faltan datos por completar','Completa todos los campos.','error');
	}
}

//filtro usuarios audios ficheros subidor
function filtrarUsuariosAudioSubidor(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaAudioNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "134", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){	
				location.reload();
				
				$("#tablaFicheroAudios").html(data);

				var columnasTablaFicheros= [null,{ "width": "45%" },{ "width": "15%" },{ "width": "30%" },{ "width": "10%" }];
				cargarTabla.init("tablaFicheroAudios",columnasTablaFicheros,[0, "asc"],50,true);
			}
		}
	});
}

//borrar historial audios
function borrarHistorialColaAudios(lin,n,seccion){
	if(seccion==1){
		mostrarRecargarTabla("tablaHistorialAudios");
	}else if(seccion==2){
		mostrarRecargarTabla("tablaAudiosColas");	 
	}
	
	if(seccion==1 || seccion==2){
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "135", 'lin': lin, "n": n , "seccion": seccion },
			type : 'POST',
			success : function(data){
				$('#cargando').hide();
				if(data!="n"){
					Swal.fire('Cancelado','Se ha cancelado el audio correctamente.','success');
					if(seccion==1){
						$("#tablaHistorialAudios").html(data);

						var columnasTabHistorial= [null,{ "width": "24%" },{ "width": "10%" },{ "width": "19%" },{ "width": "10%" },{ "width": "19%" },{ "width": "15%" },{ "width": "10%" }];
						cargarTabla.init("tablaHistorialAudios",columnasTabHistorial,[4, "asc"],50,true);
					}else if(seccion==2){
						$("#tablaAudiosColas").html(data);

						var columnasTab= [null,{ "width": "14%" },{ "width": "16%" },{ "width": "14%" },{ "width": "14%" },{ "width": "14%" },{ "width": "14%" },{ "width": "14%" }];
						cargarTabla.init("tablaAudiosColas",columnasTab,[6, "asc"],150,true);
					}

				}
			}
		});
	}
}

//filtro usuarios audios colas
function filtrarUsuariosColasAudio(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaAudiosColas");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "137", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaAudiosColas").html(data);

				var columnasTab= [null,{ "width": "14%" },{ "width": "16%" },{ "width": "14%" },{ "width": "14%" },{ "width": "14%" },{ "width": "14%" },{ "width": "14%" }];
				cargarTabla.init("tablaAudiosColas",columnasTab,[6, "asc"],150,true);
			}
		}
	});
}

//mostra u ocultar el subidor de audio si esta escribiendo texto
function mostrarOcultarSubidorAudio(elemento){
	var textoAudio=$("#"+elemento.id).val();
	/*if(textoAudio.length==0){
	   $("#divSubidorFicheroAudio").show();
	}else{
	   $("#divSubidorFicheroAudio").hide();
	}*/
}

//editar nombre del audio
function editarNombreAudio(idCli,idLin){
	var nombreAudio=$("#nombreFicheroAudio"+idLin).val();
	if(idCli>0 && idLin>0){
		mostrarRecargarTabla("tablaAudioNodos");
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "139", 'idCli': idCli, 'idLin': idLin, 'nombreAudio': nombreAudio },
			type : 'POST',
			success : function(data){
				if(data!="n"){	

					mostrarToastFire("¡Guardado Correctamente!","success");
					
					$("#tablaFicheroAudios").html(data);

					var columnasTablaFicheros= [null,{ "width": "45%" },{ "width": "15%" },{ "width": "30%" },{ "width": "10%" }];
					cargarTabla.init("tablaFicheroAudios",columnasTablaFicheros,[0, "asc"],50,true);
				}
			}
		});
	}
}

//filtro usuarios audios historial
function filtrarUsuariosHistorialGeneralAudio(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaAudiosHistorial");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "140", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaAudiosHistorial").html(data);
				
				location.reload();
				
				/*var columnasTab= [null,{ "width": "25%" },{ "width": "13%" },{ "width": "13%" },{ "width": "13%" },{ "width": "12%" },{ "width": "12%" },{ "width": "12%" }];
        		cargarTabla.init("tablaAudiosHistorial",columnasTab,[1, "asc"],150,true);*/
			}
		}
	});
}

//filtrar historial general audios
function filtrarHistorialGeneralAudios(){
	var fechaIni=$("#fechaIniHistorialGenAudio").val();
	var fechaFin=$("#fechaFinHistorialGenAudio").val();
	var nodo=$("#selectNodosFiltro option:selected").val();
	//$('#cargando').show();
    mostrarRecargarTabla("tablaAudiosHistorial");
    
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "141", 'fechaIni': fechaIni, 'fechaFin': fechaFin, 'nodo': nodo },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data!="n"){
				//Swal.fire('Correcto','Ok.','success');
				
			   	$("#tablaAudiosHistorial").html(data);
                
                var columnasTab= [null,{ "width": "25%" },{ "width": "13%" },{ "width": "13%" },{ "width": "13%" },{ "width": "12%" },{ "width": "12%" },{ "width": "12%" }];
        		cargarTabla.init("tablaAudiosHistorial",columnasTab,[1, "asc"],150,true);
			}
		}
	});
}

//no permitir que el num de reproduccines sea inferior a 1
function comprobarNumReproducciones(elemento){
	var numReproducciones=$("#"+elemento.id).val();
	if(numReproducciones<1 && numReproducciones!=""){
	   $("#"+elemento.id).val("1");
	}
}
