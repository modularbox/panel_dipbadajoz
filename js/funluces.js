//filtro usuarios luces
function filtrarUsuariosLuces(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaLucesNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "17", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaLucesNodos").html(data);
                var columnasTabHistorial= [null,{ "width": "10%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" }];
				cargarTabla.init("tablaLucesNodos",columnasTabHistorial,[1, "asc"],50,true);
			}
		}
	});
}

//filtro estado luces
function filtrarEstadoLuces(elemento){
	var e=$("#"+elemento.id+" option:selected").val();
	mostrarRecargarTabla("tablaLucesNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "18", 'e': e },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaLucesNodos").html(data);
                var columnas= [null,{ "width": "10%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" }];
                cargarTabla.init("tablaLucesNodos",columnas,[1, "asc"],50,true);
			}
		}
	});
}
//borrar nodo luces
function borraNodoLuces(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "19", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=8";
		}
	});
}

//filtro usuarios programas luces
function filtrarUsuariosProgramasLuces(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaProgramasLuces");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "20", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaProgramasLuces").html(data);
                var columnasTab= [null,{ "width": "30%" },{ "width": "30%" },{ "width": "30%" },{ "width": "10%" }];
				cargarTabla.init("tablaProgramasLuces",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//borrar programa luces
function borraProgramaLuces(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "21", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data=="n"){
			   //alert("El programa esta en uso en algún horario.");
               Swal.fire('Error','El programa está en uso en algún horario.','error');
			}else{
			   location.href="index.php?s=11";
			}
		}
	});
}

function borrarLineaConfiguracionLuces(programa,lin){
	$("#cargandotablaConfigPrograma").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "22", "programa": programa, "lin": lin},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigPrograma").html(data);
				
				//tabla            
                var columnasTab= [null,{ "width": "2.5%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "7%" },{ "width": "5%" }];
                cargarTabla.init("tablaConfigPrograma",columnasTab,[1, "asc"],50,true);
            
			}
			$("#cargandotablaConfigPrograma").hide();
		}
	});
}

//crear nueva linea programa
function crearLineaProgramaluces(programa){
	$("#cargandotablaConfigPrograma").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "23", "programa": programa},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigPrograma").html(data);
				
				//tabla            
				var columnasTab= [null,{ "width": "2.5%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "7%" },{ "width": "5%" }];
                cargarTabla.init("tablaConfigPrograma",columnasTab,[1, "asc"],50,true);
            
			}
			$("#cargandotablaConfigPrograma").hide();
		}
	});
}

//cambiar color div
function cambiarColorCelda(color,idPulsado,idCambiar,programa,lin){
	$("#"+idCambiar+"").css("background-color",color);
	$("#"+idCambiar+"_hidden").val(idPulsado/*color*/);
	
	editarLineaConfiguracionLuces(programa,lin);//lamo para editar
}

//editar lineas programas luces
function editarLineaConfiguracionLuces(programa,lin){
	$("#cargandotablaConfigPrograma").show();
	
    var columnaUno=$("#luzModal"+lin+"_1_hidden").val();
	var columnaDos=$("#luzModal"+lin+"_2_hidden").val();
	var columnaTres=$("#luzModal"+lin+"_3_hidden").val();
	var columnaCuatro=$("#luzModal"+lin+"_4_hidden").val();
	var columnaCinco=$("#luzModal"+lin+"_5_hidden").val();
	var columnaSeis=$("#luzModal"+lin+"_6_hidden").val();
	var columnaSiete=$("#luzModal"+lin+"_7_hidden").val();
	var columnaOcho=$("#luzModal"+lin+"_8_hidden").val();
	var columnaNueve=$("#luzModal"+lin+"_9_hidden").val();
	var columnaDiez=$("#luzModal"+lin+"_10_hidden").val();
	var columnaOnce=$("#luzModal"+lin+"_11_hidden").val();
	var columnaDoce=$("#luzModal"+lin+"_12_hidden").val();
	var columnaTrece=$("#luzModal"+lin+"_13_hidden").val();
	var columnaCatorce=$("#luzModal"+lin+"_14_hidden").val();
	var columnaQuince=$("#luzModal"+lin+"_15_hidden").val();
	var columnaDieciseis=$("#luzModal"+lin+"_16_hidden").val();
	var columnaDiecisiete=$("#luzModal"+lin+"_17_hidden").val();
	var columnaDieciocho=$("#luzModal"+lin+"_18_hidden").val();
	var columnaDiecinueve=$("#luzModal"+lin+"_19_hidden").val();
	var columnaVeinte=$("#luzModal"+lin+"_20_hidden").val();
	
	var temp=$("#temporizacion"+lin+" option:selected").val();
	
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "24", "programa": programa, "lin": lin,"c1": columnaUno,"c2": columnaDos,"c3": columnaTres,"c4": columnaCuatro,"c5": columnaCinco,"c6": columnaSeis,"c7": columnaSiete,"c8": columnaOcho,"c9": columnaNueve,"c10": columnaDiez,"c11": columnaOnce,"c12": columnaDoce,"c13": columnaTrece,"c14": columnaCatorce,"c15": columnaQuince,"c16": columnaDieciseis,"c17": columnaDiecisiete,"c18": columnaDieciocho,"c19": columnaDiecinueve,"c20": columnaVeinte, "temp" :temp },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigPrograma").html(data);
				
				//tabla            
				var columnasTab= [null,{ "width": "2.5%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "7%" },{ "width": "5%" }];
                cargarTabla.init("tablaConfigPrograma",columnasTab,[1, "asc"],50,true);
			}
			$("#cargandotablaConfigPrograma").hide();
		}
	});
}

//anadir nodo horario
function anadirNodoLucesHorario(h){
	$("#cargandotablaNodosHorario").show();
	var n=$("#nodoHorario option:selected").val();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "25", "h": h, "n": n},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				var respuesta=data.split("@#");

				$("#divSelectNodos").html(respuesta[1]);
				$("#tablaNodosHorario").html(respuesta[0]);
				
				//tabla
        		var columnasTab= [null,{ "width": "80%" },{ "width": "20%" }];
        		cargarTabla.init("tablaNodosHorario",columnasTab,[1, "asc"],50,true);
				
			}else{
				mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
			}
			$("#cargandotablaNodosHorario").hide();
		}
	});
}

//borrar nodo horario luces
function borrarNodoHorarioLuces(h,lin){
	$("#cargandotablaNodosHorario").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "26", "h": h, "lin": lin},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				var respuesta=data.split("@#");

				$("#divSelectNodos").html(respuesta[1]);
				$("#tablaNodosHorario").html(respuesta[0]);
				
				//tabla
        		var columnasTab= [null,{ "width": "80%" },{ "width": "20%" }];
        		cargarTabla.init("tablaNodosHorario",columnasTab,[1, "asc"],50,true);
			}
			$("#cargandotablaNodosHorario").hide();
		}
	});
}

//borrar horario luces
function borraHorarioLuces(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "27", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=13";
		}
	});
}

//anadir configurar luces programas, horarios
function anadirProgramaHorario(h){
	$("#cargandotablaHorarioHoras").show();
	
	var programa=$("#programaConfHorario option:selected").val();
	var diaSemana=$("#diaConfHorario option:selected").val();
	var horaDe=$("#horadConfHorario").val();
	var horaHasta=$("#horahConfHorario").val();
	
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "28", "h": h, "programa": programa, "diaSemana": diaSemana, "horaDe": horaDe, "horaHasta": horaHasta},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaHorarioHoras").html(data);
				
				//tabla
        		var columnasHorarioHoras= [null,{ "width": "30%" },{ "width": "30%" },{ "width": "15%" },{ "width": "15%" },{ "width": "10%" }];
        		cargarTabla.init("tablaHorarioHoras",columnasHorarioHoras,[0, "asc"],50,true);
			}
			$("#cargandotablaHorarioHoras").hide();
		}
	});
}

//borrar configuracion horario programa
function borrarNodoHorarioLuces(h,lin){
	$("#cargandotablaHorarioHoras").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "29", "h": h, "lin": lin},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaHorarioHoras").html(data);
				
				//tabla
        		var columnasHorarioHoras= [null,{ "width": "30%" },{ "width": "30%" },{ "width": "15%" },{ "width": "15%" },{ "width": "10%" }];
        		cargarTabla.init("tablaHorarioHoras",columnasHorarioHoras,[0, "asc"],50,true);
			}
			$("#cargandotablaHorarioHoras").hide();
		}
	});
}

function mueveFilaPrograma(accion,id,programa){
	$("#cargandotablaConfigPrograma").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "30", 'programa': programa, 'accion': accion, 'id': id},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigPrograma").html(data);
				
				//tabla            
				var columnasTab= [null,{ "width": "2.5%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "7%" },{ "width": "5%" }];
                cargarTabla.init("tablaConfigPrograma",columnasTab,[1, "asc"],50,true);
			}
			$("#cargandotablaConfigPrograma").hide();
		}
	});
}

//filtro usuarios horarios luces
function filtrarUsuariosHorariosLuces(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaHorariosLuces");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "31", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaHorariosLuces").html(data);
                var columnasTab= [null,{ "width": "30%" },{ "width": "30%" },{ "width": "30%" },{ "width": "10%" }];
				cargarTabla.init("tablaHorariosLuces",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//crear nuevo tipo de foco DMX
function crearNuevoTipoFocoDmx(){
	var ref=$("#refTipoFocoLuz").val();
	var numCanales=$("#canalesTipoFocoLuz option:selected").val();
	if(ref!="" && (numCanales>=3 && numCanales<=14)){
		
	   	$("#cargandotablaConfigProgramaGenerico").show();
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "142", "ref": ref, "numCanales": numCanales},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaConfigProgramaGenerico").html(data);
					
					$("#refTipoFocoLuz").val("");
					
					mostrarToastFire("¡Guardado Correctamente!","success")
					
					//tabla generico
					var columnasTab= [null,{ "width": "20%" },{ "width": "20%" },{ "width": "20%" },{ "width": "20%" },{ "width": "20%" }];
        			cargarTabla.init("tablaConfigProgramaGenerico",columnasTab,[1, "asc"],50,true);

				}
				$("#cargandotablaConfigProgramaGenerico").hide();
			}
		});
	}else{
		Swal.fire('Faltan datos por completar','Prueba de nuevo.','warning');
	}
}

//editar tipo foco dmx
function editarConfigTipoFoco(idTipoFoco){
	var ref=$("#refTipoFocoLuz"+idTipoFoco).val();
	var marca=$("#marcaTipoFocoLuz"+idTipoFoco).val();
	var modelo=$("#modeloTipoFocoLuz"+idTipoFoco).val();
	var numCanales=$("#canalesTipoFocoLuz"+idTipoFoco+" option:selected").val();
	if(idTipoFoco>0 && ref!="" && (numCanales>=3 && numCanales<=14)){
		
	   	$("#cargandotablaConfigProgramaGenerico").show();
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "143", "idTipoFoco": idTipoFoco, "ref": ref, "numCanales": numCanales, "marca": marca, "modelo": modelo},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaConfigProgramaGenerico").html(data);
					
					mostrarToastFire("¡Guardado Correctamente!","success")
					
					//tabla generico
					var columnasTab= [null,{ "width": "20%" },{ "width": "20%" },{ "width": "20%" },{ "width": "20%" },{ "width": "20%" }];
        			cargarTabla.init("tablaConfigProgramaGenerico",columnasTab,[1, "asc"],50,true);

				}
				$("#cargandotablaConfigProgramaGenerico").hide();
			}
		});
	}else{
		Swal.fire('Faltan datos por completar','Prueba de nuevo.','warning');
	}
}

//borrar tipo de foco dmx
function borrarTipoFocoDmxLuces(idLin){
	if(idLin>0){
		$("#cargandotablaConfigProgramaGenerico").show();
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "144", 'idLin': idLin },
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaConfigProgramaGenerico").html(data);
					
					mostrarToastFire("¡Guardado Correctamente!","success");
					
					//tabla generico
					var columnasTab= [null,{ "width": "20%" },{ "width": "20%" },{ "width": "20%" },{ "width": "20%" },{ "width": "20%" }];
        			cargarTabla.init("tablaConfigProgramaGenerico",columnasTab,[1, "asc"],50,true);

				}
				$("#cargandotablaConfigProgramaGenerico").hide();
			}
		});
	}else{
		Swal.fire('Faltan datos por completar','Prueba de nuevo.','warning');
	}
}

//abrir modal para configurar foco
function abrirModalConfiguracionFocoCanales(idLin){
	
    if(idLin>0){
        $("#idLingTipoFocoConf").val(idLin);

        $.ajax({
            url : 'adminajax.php',
            data : { 'op': "145", "idLin": idLin},
            type : 'POST',
            success : function(data){
                if(data!="n"){
                    $("#abrirConfiguracionTipoFoco").html(data);//recargar div con las variables de la lin
                    $("#abrirConfiguracionTipoFoco").modal("show");//abrir modal
                    
                    setTimeout(function(){
                        $("#funcionalidadesCanalesTipoFocoDmx1").focus()
                    }, 200);
                }
            }
        });
         
	}else{
		Swal.fire('Faltan datos por completar','Prueba de nuevo.','warning');
	}
}

//guardar la direccion dmx del tipo de foco
function actualizarDireccionDmxTipoFoco(idTipoFoco,idFuncionalidad,elemento){
	 if(idTipoFoco>0 && (idFuncionalidad>0 || idFuncionalidad==-99)){
        var direccionDmx=$("#"+elemento.id+" option:selected").val();

        $.ajax({
            url : 'adminajax.php',
            data : { 'op': "146", "idTipoFoco": idTipoFoco, "idFuncionalidad": idFuncionalidad, "direccionDmx": direccionDmx},
            type : 'POST',
            success : function(data){
                if(data!="n" && data!="d"){
                    mostrarToastFire("¡Guardado Correctamente!","success");
					
					$("#abrirConfiguracionTipoFoco").html(data);//recargar div con las variables de la lin
					setTimeout(function(){
                        $("#funcionalidadesCanalesTipoFocoDmx1").focus()
                    }, 200);
					
                }else if(data=="d"){
					mostrarToastFire("¡Dirección ya en uso!","error")
				}
            }
        });
         
	}else{
		Swal.fire('Faltan datos por completar','Prueba de nuevo.','warning');
	}
}

//actualizar num focos al seleccionar foco y guardar el mismo y canales vacios
function actualizarCanalesDmxVacios(elemento,idFocoActual,nameNumFoco){
	var idFocoSeleccionado=$("#"+elemento.id+" option:selected").val();
	if(idFocoSeleccionado>0){
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "147", "idFoco": idFocoSeleccionado},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					var respuesta=data.split("@#");
					$("#canalesFocoLuces"+nameNumFoco).val(respuesta[0]);
					$("#columCanVacios"+nameNumFoco).val(respuesta[1]);
				}
			}
		});
	}
}

//actualizar modo nodo luz y tiempo
function actualizarModoNodoLuces(elemento,idNodo){
	var modo=$("#"+elemento.id+" option:selected").val();
    if(modo==1 || modo==2){
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "148", 'modo': modo, 'idNodo': idNodo },
			type : 'POST',
			success : function(data){
				if(data!="n"){
					
					if(modo==1){
					   $("#bloqueTiempoModoNodo").css("display","none");
					}else if(modo==2){
						$("#bloqueTiempoModoNodo").css("display","block"); 
					}
					

				}
			}
		});
	}else{
		Swal.fire('Faltan datos por completar','Prueba de nuevo.','warning');
	}
}

//crear nuevo tipo de programa DMX
function crearTipoProgramaPredefinidoLuz(){
	var nombre=$("#nombreTipoProg").val();
	if(nombre!="" ){
		
	   	$("#cargandotablaProgramasPredefinidosConf").show();
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "149", "nombre": nombre},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaProgramasPredefinidosConf").html(data);
					
					$("#nombreTipoProg").val("");
					
					mostrarToastFire("¡Guardado Correctamente!","success")
					
					//tabla generico
					var columnasTab= [null,{ "width": "80%" },{ "width": "20%" }];
        			cargarTabla.init("tablaProgramasPredefinidosConf",columnasTab,[1, "asc"],50,true);

				}
				$("#cargandotablaProgramasPredefinidosConf").hide();
			}
		});
	}else{
		Swal.fire('Faltan datos por completar','Prueba de nuevo.','warning');
	}
}

//editar tipo de programa DMX
function editarTipoProgramaPredefinidoLuz(idLin){
	var nombre=$("#nombreTipoProg"+idLin).val();
	if(nombre!="" && idLin>0){
		
	   	$("#cargandotablaProgramasPredefinidosConf").show();
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "150", "idLin": idLin, "nombre": nombre},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaProgramasPredefinidosConf").html(data);
					
					mostrarToastFire("¡Guardado Correctamente!","success")
					
					//tabla generico
					var columnasTab= [null,{ "width": "80%" },{ "width": "20%" }];
        			cargarTabla.init("tablaProgramasPredefinidosConf",columnasTab,[1, "asc"],50,true);

				}
				$("#cargandotablaProgramasPredefinidosConf").hide();
			}
		});
	}else{
		Swal.fire('Faltan datos por completar','Prueba de nuevo.','warning');
	}
}

//borrar tipo de programa DMX
function borrarTipoProgramaPredefinidoLuz(idLin){
	if(idLin>0){
		$("#cargandotablaProgramasPredefinidosConf").show();
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "151", 'idLin': idLin },
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaProgramasPredefinidosConf").html(data);
					
					mostrarToastFire("¡Guardado Correctamente!","success");
					
					//tabla generico
					var columnasTab= [null,{ "width": "80%" },{ "width": "20%" }];
        			cargarTabla.init("tablaProgramasPredefinidosConf",columnasTab,[1, "asc"],50,true);

				}
				$("#cargandotablaProgramasPredefinidosConf").hide();
			}
		});
	}else{
		Swal.fire('Faltan datos por completar','Prueba de nuevo.','warning');
	}
}