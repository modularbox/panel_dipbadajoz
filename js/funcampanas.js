//filtro conexion campanas
function filtrarConexionCampanas(elemento){
	var e=$("#"+elemento.id+" option:selected").val();
	mostrarRecargarTabla("tablaCampanasNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "79", 'e': e },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaCampanasNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
				cargarTabla.init("tablaCampanasNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//filtro usuarios campanas
function filtrarUsuariosCampanas(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaCampanasNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "80", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaCampanasNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
				cargarTabla.init("tablaCampanasNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//borrar campanas nodo
function borraCampanasNodo(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "81", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=26";
		}
	});
}

//filtro usuarios programas campanas
function filtrarUsuariosCampanasProgramas(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaCampanasProgramasNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "82", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaCampanasProgramasNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "15%" },{ "width": "15%" },{ "width": "15%" },{ "width": "15%" },{ "width": "5%" },{ "width": "15%" }];
				cargarTabla.init("tablaCampanasProgramasNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//marcar desmarcar check
function activarDesactivarCheckCampanas(elemento,idFicha){
	var pulsado=$("#"+elemento.id+"_hidden").val();
	if(pulsado=="s"){
		$("#"+elemento.id+"_hidden").val("n");
        //quitar check
		$("#"+elemento.id+"").children('i').removeClass('fas fa-check');
        //poner aspa
        $("#"+elemento.id+"").children('i').addClass('fas fa-times');
        $("#"+elemento.id+"").children('i').css("color", "red");
		
	}else if(pulsado=="n"){
		$("#"+elemento.id+"_hidden").val("s");
        //quitar aspa
		$("#"+elemento.id+"").children('i').removeClass('fas fa-times');
        //poner check
		$("#"+elemento.id+"").children('i').addClass('fas fa-check');
        $("#"+elemento.id+"").children('i').css("color", "green");
	}
}

//borrar campanas programa
function borraCampanasProgramas(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "83", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=28";
		}
	});
}

function borrarLineaConfiguracionCampanas(programa,lin){
	$("#cargandotablaConfigPrograma").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "84", "programa": programa, "lin": lin},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigPrograma").html(data);
				
				//tabla            
                var columnasTab= [null,{ "width": "9%" },{ "width": "17%" },{ "width": "17%" },{ "width": "17%" },{ "width": "30%" },{ "width": "10%" }];
        		cargarTabla.init("tablaConfigPrograma",columnasTab,[1, "asc"],50,true);
            
			}
			$("#cargandotablaConfigPrograma").hide();
		}
	});
}

function mueveFilaProgramaCampanas(accion,id,programa){
	$("#cargandotablaConfigPrograma").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "85", 'programa': programa, 'accion': accion, 'id': id},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigPrograma").html(data);
				
				//tabla            
				var columnasTab= [null,{ "width": "9%" },{ "width": "17%" },{ "width": "17%" },{ "width": "17%" },{ "width": "30%" },{ "width": "10%" }];
        		cargarTabla.init("tablaConfigPrograma",columnasTab,[1, "asc"],50,true);
			}
			$("#cargandotablaConfigPrograma").hide();
		}
	});
}

//cambiar color div
function cambiarColorCeldaCampanas(color,idPulsado,idCambiar,programa,lin){
	$("#"+idCambiar+"").css("background-color",color);
	$("#"+idCambiar+"_hidden").val(idPulsado/*color*/);
	
	editarLineaConfiguracionCampanas(programa,lin);//lamo para editar
}

//editar lineas programas campanas
function editarLineaConfiguracionCampanas(programa,lin){
	$("#cargandotablaConfigPrograma").show();
	
    var releUno=$("#releModal"+lin+"_1_hidden").val();
	var releDos=$("#releModal"+lin+"_2_hidden").val();
	var releTres=$("#releModal"+lin+"_3_hidden").val();
	
	var temp=$("#temporizacion"+lin+" option:selected").val();
	
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "86", "programa": programa, "lin": lin,"r1": releUno,"r2": releDos,"r3": releTres, "temp" :temp },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigPrograma").html(data);//por ahora asi
				
				//tabla            
				var columnasTab= [null,{ "width": "9%" },{ "width": "17%" },{ "width": "17%" },{ "width": "17%" },{ "width": "30%" },{ "width": "10%" }];
        		cargarTabla.init("tablaConfigPrograma",columnasTab,[1, "asc"],50,true);
			}
			$("#cargandotablaConfigPrograma").hide();
		}
	});
}

//crear nueva linea programa
function crearLineaProgramaCampanas(programa){
	$("#cargandotablaConfigPrograma").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "87", "programa": programa},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigPrograma").html(data);
				
				//tabla            
				var columnasTab= [null,{ "width": "9%" },{ "width": "17%" },{ "width": "17%" },{ "width": "17%" },{ "width": "30%" },{ "width": "10%" }];
        		cargarTabla.init("tablaConfigPrograma",columnasTab,[1, "asc"],50,true);
            
			}
			$("#cargandotablaConfigPrograma").hide();
		}
	});
}

//marcar desmarcar check
function actDesCheckProgramaCampana(elemento,idPrograma,nodo){
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
	editarLineaProgramasCampanasActivos(idPrograma,nodo);
}

//editar programa activo/desactivado
function editarLineaProgramasCampanasActivos(idPrograma,nodo){
	var pulsado=$("#programaCampanas_"+idPrograma+"_hidden").val();
	$("#cargandotablaConfigProgram").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "88", "idPrograma": idPrograma, "nodo": nodo, "pulsado": pulsado},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigProgram").html(data);
				
				//tabla
				var columnasTabConfProgram= [null,{ "width": "60%" },{ "width": "40%" }];
				cargarTabla.init("tablaConfigProgram",columnasTabConfProgram,[0, "asc"],50,true);
            
			}
			$("#cargandottablaConfigProgram").hide();
		}
	});
}

//filtrar historial campanas programas
function filtrarHistorialProgramasCampanas(n){
	var fechaIni=$("#fechaIniHistorialProgramas").val();
	var fechaFin=$("#fechaFinHistorialProgramas").val();
	//$('#cargando').show();
    mostrarRecargarTabla("tablaHistorialProgramas");
    
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "91", 'fechaIni': fechaIni, 'fechaFin': fechaFin, "n": n },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data!="n"){
				//Swal.fire('Correcto','Ok.','success');
				
			   	$("#tablaHistorialProgramas").html(data);
                
               	//tabla historial
        		var columnasTabHistorial= [null,{ "width": "30%" },{ "width": "30%" },{ "width": "35%" },{ "width": "10%" }];
				cargarTabla.init("tablaHistorialProgramas",columnasTabHistorial,[1, "asc"],50,true);
			}
		}
	});
}

//crear nueva linea luces campanas
function crearLineaLucesCampanas(idNodo){
	$("#cargandotablaConfigLuzCampana").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "92", "idNodo": idNodo},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigLuzCampana").html(data);
				
				//tabla para horario luces
        		var columnasTab= [null,{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" }];
				cargarTabla.init("tablaConfigLuzCampana",columnasTab,[1, "asc"],50,true);
            
			}
			$("#cargandotablaConfigLuzCampana").hide();
		}
	});
}

//editar luces activo/desactivado campanas
function editarLineaLucesCampanas(idLin,idNodo){
	var l=$("#luzL_"+idLin+"_hidden").val();
	var m=$("#luzM_"+idLin+"_hidden").val();
	var x=$("#luzX_"+idLin+"_hidden").val();
	var j=$("#luzJ_"+idLin+"_hidden").val();
	var v=$("#luzV_"+idLin+"_hidden").val();
	var s=$("#luzS_"+idLin+"_hidden").val();
	var d=$("#luzD_"+idLin+"_hidden").val();
	var horaIni=$("#horaIniLuces"+idLin+"").val();
	var horaFin=$("#horaFinLuces"+idLin+"").val();
	
	$("#cargandotablaConfigLuzCampana").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "93", "idLin": idLin, "idNodo": idNodo, "l": l, "m": m, "x": x, "j": j, "v": v, "s": s, "d": d, "horaIni": horaIni, "horaFin": horaFin},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigLuzCampana").html(data);
				
				//tabla para horario luces
        		var columnasTab= [null,{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" }];
				cargarTabla.init("tablaConfigLuzCampana",columnasTab,[1, "asc"],50,true);
            
			}
			$("#cargandotablaConfigLuzCampana").hide();
		}
	});
}


//eliminar luces campanas
function borrarLineaLucesCampanas(idLin,idNodo){
	
	$("#cargandotablaConfigLuzCampana").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "94", "idLin": idLin, "idNodo": idNodo},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaConfigLuzCampana").html(data);
				
				//tabla para horario luces
        		var columnasTab= [null,{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" }];
				cargarTabla.init("tablaConfigLuzCampana",columnasTab,[1, "asc"],50,true);
            
			}
			$("#cargandotablaConfigLuzCampana").hide();
		}
	});
}