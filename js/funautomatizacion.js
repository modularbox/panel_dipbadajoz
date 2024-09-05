//filtro usuarios programas campanas
function filtrarUsuariosAutomatizacionProgramas(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaAutomatizacionProgramasNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "96", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
            	$("#tablaAutomatizacionProgramasNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
				cargarTabla.init("tablaAutomatizacionProgramasNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//borrar automatizacion programa
function borraAutomatizacionProgramas(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "97", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=31";
		}
	});
}

//crear nueva linea programas automatizacion
function crearLineaSalidaAutomatizacion(idPrograma,salida){
	$("#cargandoConfigS"+salida+"Automatizacion").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "98", "idPrograma": idPrograma, "salida": salida},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigS"+salida+"Automatizacion").html(data);
				
				//tabla para horario luces
        		var columnasTab= [null,{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" }];
				cargarTabla.init("tablaConfigS"+salida+"Automatizacion",columnasTab,[1, "asc"],50,true);
            
			}
			$("#cargandoConfigS"+salida+"Automatizacion").hide();
		}
	});
}

//eliminar lineas programas automatizacion
function borrarLineaSalidasAutomatizacion(idLin,idPrograma,salida){
	
	$("#cargandoConfigS"+salida+"Automatizacion").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "99", "idLin": idLin, "idPrograma": idPrograma, "salida": salida},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigS"+salida+"Automatizacion").html(data);
				
				//tabla para horario luces
        		var columnasTab= [null,{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" }];
				cargarTabla.init("tablaConfigS"+salida+"Automatizacion",columnasTab,[1, "asc"],50,true);
            
			}
			$("#cargandoConfigS"+salida+"Automatizacion").hide();
		}
	});
}

//editar lineas programas automatizacion
function editarLineaProgramaAutomatizacion(idLin,idPrograma,salida){
	var l=$("#luzL_"+idLin+"_hidden").val();
	var m=$("#luzM_"+idLin+"_hidden").val();
	var x=$("#luzX_"+idLin+"_hidden").val();
	var j=$("#luzJ_"+idLin+"_hidden").val();
	var v=$("#luzV_"+idLin+"_hidden").val();
	var s=$("#luzS_"+idLin+"_hidden").val();
	var d=$("#luzD_"+idLin+"_hidden").val();
	var horaIni=$("#horaIniAutomatizacion"+idLin+"").val();
	var horaFin=$("#horaFinAutomatizacion"+idLin+"").val();
	
	$("#cargandoConfigS"+salida+"Automatizacion").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "100", "idLin": idLin, "idPrograma": idPrograma, "salida": salida, "l": l, "m": m, "x": x, "j": j, "v": v, "s": s, "d": d, "horaIni": horaIni, "horaFin": horaFin},
		type : 'POST',
		success : function(data){
			console.log(data);
			var respuesta=data.split("@#");
			if(respuesta[0]!="n"){
				$("#tablaConfigS"+salida+"Automatizacion").html(respuesta[0]);
				
				//tabla para horario luces
        		var columnasTab= [null,{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" }];
				cargarTabla.init("tablaConfigS"+salida+"Automatizacion",columnasTab,[1, "asc"],50,true);
				
				//Swal.fire('Guardado Correctamente','Ok.','success');
				//mostrarToastFire('Guardado Correctamente','success');
            
			}
			
			if(respuesta[0]!="n" && respuesta[1]!="s" && respuesta[2]=="n"){
			   Swal.fire('Guardado Correctamente','Ok.','success');
			}else{
				if(respuesta[2]!="n"){
					Swal.fire('Linea solapada.',respuesta[2],'error');
				}else{
					Swal.fire('Línea con configuración solapada.','Error!','error');
				}
			}
			
			$("#cargandoConfigS"+salida+"Automatizacion").hide();
		}
	});
}

//filtro usuarios automatizacion
function filtrarUsuariosAutomatizacion(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaAutomatizacionNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "101", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaAutomatizacionNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "45%" },{ "width": "30%" }];
        		cargarTabla.init("tablaAutomatizacionNodos",columnasTab,[1, "asc"],50);
			}
		}
	});
}

//filtro conexion automatizacion
function filtrarConexionAutomatizacion(elemento){
	var e=$("#"+elemento.id+" option:selected").val();
	mostrarRecargarTabla("tablaAutomatizacionNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "102", 'e': e },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaAutomatizacionNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
        		cargarTabla.init("tablaAutomatizacionNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//marcar desmarcar check
function actDesCheckProgramaAutomatizacion(elemento,idPrograma,nodo){
	var pulsado=$("#"+elemento.id+"_hidden").val();
	
	if(pulsado=="s"){
		$("#"+elemento.id+"_hidden").val("n");
        
		//quitar check
		$("#"+elemento.id+"").children('i').removeClass('fas fa-check');
        //poner aspa
        $("#"+elemento.id+"").children('i').addClass('fas fa-times');
        $("#"+elemento.id+"").children('i').css("color", "red");
		
	}else if(pulsado=="n"){
		$("#"+elemento.id+"_hidden").val("s")
		
		//quitar aspa
		$("#"+elemento.id+"").children('i').removeClass('fas fa-times');
        //poner check
        $("#"+elemento.id+"").children('i').addClass('fas fa-check');
        $("#"+elemento.id+"").children('i').css("color", "green");
	}
	editarLineaProgramasAutomatizacionActivos(idPrograma,nodo);
}

//editar programa activo/desactivado
function editarLineaProgramasAutomatizacionActivos(idPrograma,nodo){
	var pulsado=$("#programaAutomatizacion_"+idPrograma+"_hidden").val();
	$("#cargandotablaConfigProgramAutomatizacion").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "103", "idPrograma": idPrograma, "nodo": nodo, "pulsado": pulsado},
		type : 'POST',
		success : function(data){console.log(data)
			var respuesta=data.split("#@");		
			if(respuesta[0]!="n"){
				
				if(respuesta[0]!="n" && respuesta[1]!="s"){
					 Swal.fire('Guardado Correctamente','Ok.','success');
				}else{
					Swal.fire('Programas con horario solapado para este nodo.','Error!','error');
				}
				
				$("#tablaConfigProgramAutomatizacion").html(respuesta[0]);
				

				//tabla
				var columnasTabConfProgramAutomatizacion= [null,{ "width": "60%" },{ "width": "30%" },{ "width": "10%" }];
				cargarTabla.init("tablaConfigProgramAutomatizacion",columnasTabConfProgramAutomatizacion,[0, "asc"],50,true);	
            
			}
			$("#cargandotablaConfigProgramAutomatizacion").hide();
		}
	});
}

//filtrar historial salidas automatizacion
function filtrarHistorialSalidasAutomatizacion(n){
	var fechaIni=$("#fechaIniHistorialSalidas").val();
	var fechaFin=$("#fechaFinHistorialSalidas").val();
	
	//$('#cargando').show();
    mostrarRecargarTabla("tablaHistorialSalidas");
    
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "104", 'fechaIni': fechaIni, 'fechaFin': fechaFin, "n": n },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data!="n"){
				//Swal.fire('Correcto','Ok.','success');
				
			   	$("#tablaHistorialSalidas").html(data);
                
                var columnasTabHistorial= [null,{ "width": "15%" },{ "width": "15%" },{ "width": "20%" },{ "width": "20%" },{ "width": "15%" },{ "width": "15%" }];
        		cargarTabla.init("tablaHistorialSalidas",columnasTabHistorial,[1, "asc"],50,true);
			}
		}
	});
}

function historialSalidasAutomatizacionExcel(idnodo){
	$('#cargando').show();
	
	var fechaIni=$("#fechaIniHistorialSalidas").val();
	var fechaFin=$("#fechaFinHistorialSalidas").val();
	
	$.ajax({
		url : 'adminajax.php',
		data : { "op": "105", 'fechaIni': fechaIni, 'fechaFin': fechaFin, "idnodo": idnodo },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data=="n"){
				mostrarToastFire("Problemas al realizar la acción.","error");
			}else{
				cargaLocation('generaXLS.php?o=2');
			}
		}
	});
}

//desplegable modo salida en automatizacion, activar switch de la salida
function cambiarModoSalida(elemento,salida){
    if(salida==1){
       var seleccionado=$("#salidaUnoModo").val();
    }else if(salida==2){
       var seleccionado=$("#salidaDosModo").val();
    }else if(salida==3){
       var seleccionado=$("#salidaTresModo").val();
    }else if(salida==4){
       var seleccionado=$("#salidaCuatroModo").val();
    }else if(salida==5){
       var seleccionado=$("#salidaCincoModo").val();
    }else if(salida==6){
       var seleccionado=$("#salidaSeisModo").val();
    }
	
    if(seleccionado=="m"){//modo manual
       $("#divConfigurarionSalida"+salida+"").css("display", "block");
    }else{//modo automatico
        $("#divConfigurarionSalida"+salida+"").css("display", "none");
    }
}

//guarar funcionalidades salidas reles nodo
function guardarConfSalidasAutomatizacion(idNodo){
	$('#cargando').show();
	
	//uno
	var salidaUno=$("#salidaUnoModo").val();
	var confAutoSUno="off";
	if($('#confAutoSUno').prop('checked')){
		var confAutoSUno="on";
	}
	//dos
	var salidaDos=$("#salidaDosModo").val();
	var confAutoSDos="off";
	if($('#confAutoSDos').prop('checked')){
		var confAutoSDos="on";
	}
	//tres
	var salidaTres=$("#salidaTresModo").val();
	var confAutoSTres="off";
	if($('#confAutoSTres').prop('checked')){
		var confAutoSTres="on";
	}
	//cuatro
	var salidaCuatro=$("#salidaCuatroModo").val();
	var confAutoSCuatro="off";
	if($('#confAutoSCuatro').prop('checked')){
		var confAutoSCuatro="on";
	}
	//cinco
	var salidaCinco=$("#salidaCincoModo").val();
	var confAutoSCinco="off";
	if($('#confAutoSCinco').prop('checked')){
		var confAutoSCinco="on";
	}
	//seis
	var salidaSeis=$("#salidaSeisModo").val();
	var confAutoSSeis="off";
	if($('#confAutoSSeis').prop('checked')){
		var confAutoSSeis="on";
	}
	
	$.ajax({
		url : 'adminajax.php',
		data : { "op": "106", "idNodo": idNodo, 'salidaUno': salidaUno, 'confAutoSUno': confAutoSUno, 'salidaDos': salidaDos, 'confAutoSDos': confAutoSDos, 'salidaTres': salidaTres, 'confAutoSTres': confAutoSTres, 'salidaCuatro': salidaCuatro, 'confAutoSCuatro': confAutoSCuatro, 'salidaCinco': salidaCinco, 'confAutoSCinco': confAutoSCinco, 'salidaSeis': salidaSeis, 'confAutoSSeis': confAutoSSeis },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data=="s"){
				//mostrarToastFire("Guardado correctamente.","success");
				Swal.fire("Guardado correctamente.","OK",'success');
			}else{
				//mostrarToastFire("Problemas al realizar la acción.","error");
				Swal.fire("Problemas al realizar la acción.","Prueba en otro momento.",'error');
			}
			location.reload();
		}
	});
}