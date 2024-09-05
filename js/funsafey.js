//filtro usuarios safey
function filtrarUsuariosSafey(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaSafeyNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "33", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaSafeyNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
				cargarTabla.init("tablaSafeyNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//filtro conexion safey
function filtrarConexionSafey(elemento){
	var e=$("#"+elemento.id+" option:selected").val();
	mostrarRecargarTabla("tablaSafeyNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "34", 'e': e },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaSafeyNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
                cargarTabla.init("tablaSafeyNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//anadir puertas al nodo safey
function anadirPuertaNodoSafey(n){
	//$("#cargandotablaHorarioHoras").show();
	var nombrePuerta=$("#nombrePuertaNew").val();
	var urlEmergenciaPuerta=$("#urlEmergenciaPuertaNew").val();
	
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "35", "n": n, "nombrePuerta": nombrePuerta, "urlEmergenciaPuerta": urlEmergenciaPuerta},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				
				Swal.fire('Correcto','Se ha añadido una nueva puerta.','info');
				
				$("#nombrePuertaNew").val("");
				$("#urlEmergenciaPuertaNew").val("");
				
				$("#divImgSafeyNodo").html(data);
			}else{
				Swal.fire('Error','Error al crear la puerta.','error');
			}
			//$("#cargandotablaHorarioHoras").hide();
		}
	});
}

//borrar puerta nodo safey
function borrarPuertasNodosSafey(n,lin){
	//$("#cargandotablaNodosHorario").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "36", "n": n, "lin": lin},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				Swal.fire('Correcto','Ok.','success');
				
				$("#divImgSafeyNodo").html(data);
			}
			//$("#cargandotablaNodosHorario").hide();
		}
	});
}

//lanzar ajax a la url de emergencia
function ejecutarUrlEmergenciaPuertaSafey(ulrIr,n,p,tipo){
	$.ajax({
		url : ulrIr,
		type : 'GET',
		success : function(data){
			//historial
			abrirPuertaWebSafeyHistorial(p,n,tipo)
		}
	});
}

//ir al anotar el historial y abrir
function abrirPuertaWebSafeyHistorial(p,n,tipo){
	//historial
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "37", "n": n, "p": p, "tipo": tipo},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				Swal.fire('Correctamente','Abriendo puerta.','success');
			}else{
				Swal.fire('Error','Problemas al abrir la puerta.','error');
			}
		}
	});
}

//filtrar historial puertas safey
function filtrarHistorialPuertasSafey(n){
	var fechaIni=$("#fechaIniHistorialPuertas").val();
    var fechaFin=$("#fechaFinHistorialPuertas").val();
	var puerta=$("#puertaHistorialSafey option:selected").val();
	//$('#cargando').show();
    mostrarRecargarTabla("tablaHistorialPuertas");
    
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "38", 'fechaIni': fechaIni, 'fechaFin': fechaFin, "n": n, "puerta": puerta },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data!="n"){
				//Swal.fire('Correcto','Ok.','success');
				
			   	$("#tablaHistorialPuertas").html(data);
                
               	var columnasTabHistorial= [null,{ "width": "15%" },{ "width": "15%" },{ "width": "20%" },{ "width": "15%" },{ "width": "15%" },{ "width": "15%" },{ "width": "10%" }];
				cargarTabla.init("tablaHistorialPuertas",columnasTabHistorial,[1, "asc"],200,true);
			}
		}
	});
}

//filtrar historial pagos safey
function filtrarHistorialPagosSafey(n){
	var fechaIni=$("#fechaIniHistorialPagos").val();
    var fechaFin=$("#fechaFinHistorialPagos").val();
	
	//$('#cargando').show();
    mostrarRecargarTabla("tablaHistorialPagos");
    
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "120", 'fechaIni': fechaIni, 'fechaFin': fechaFin, "n": n},
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data!="n"){
				//Swal.fire('Correcto','Ok.','success');
				
			   	$("#tablaHistorialPagos").html(data);
                
               	var columnasTabHistorialPagos= [null,{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "20%" }];
				cargarTabla.init("tablaHistorialPagos",columnasTabHistorialPagos,[1, "asc"],200,true);
			}
		}
	});
}

//filtro usuarios safey accesos
function filtrarUsuariosSafeyAccesos(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaSafeyAccesos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "39", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				//Swal.fire('Correcto','Ok.','success');
				
                $("#tablaSafeyAccesos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
				cargarTabla.init("tablaSafeyAccesos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//borrar safey nodo
function borraSafeyNodo(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "40", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=15";
		}
	});
}

//borrar safey acceso
function borraSafeyAcceso(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "41", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=17";
		}
	});
}

//accesos nodos puertas
function editarLineaAccesosNodosPuertas(idlin,nodo,puerta,idacceso){
	var hidenPermiso=$("#nodoPuertaAc_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val();
	var fechaDe=$("#horadAccesosPuertasAc_"+nodo+"_"+puerta+"_"+idlin+"").val();
	var fechaHasta=$("#horahAccesosPuertasAc_"+nodo+"_"+puerta+"_"+idlin+"").val();
	
	var l=$("#nodoPuertaAcL_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val();
	var m=$("#nodoPuertaAcM_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val();
	var x=$("#nodoPuertaAcX_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val();
	var j=$("#nodoPuertaAcJ_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val();
	var v=$("#nodoPuertaAcV_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val();
	var s=$("#nodoPuertaAcS_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val();
	var d=$("#nodoPuertaAcD_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val();
	
    mostrarRecargarTabla("tablaAccesosNodosPuertas");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "42", 'idlin': idlin, 'puerta': puerta, 'nodo': nodo, 'idacceso': idacceso, 'hidenPermiso': hidenPermiso, 'fechaDe': fechaDe, 'fechaHasta': fechaHasta, "l": l, "m": m, "l": l, "x": x, "j": j, "v": v, "s": s, "d": d },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaAccesosNodosPuertas").html(data);//recargar ok
				
				Swal.fire('Correcto','Ok.','success');
			}else{
				 Swal.fire('Error','No se ha podido realizar la acción.','error');
			}
			var columnasTabAccesosNodos= [null,{ "width": "20%" },{ "width": "5%" },{ "width": "5%" },{ "width": "5%" },{ "width": "5%" },{ "width": "5%" },{ "width": "5%" },{ "width": "5%" },{ "width": "5%" },{ "width": "15%" },{ "width": "15%" },{ "width": "10%" }];
			cargarTabla.init("tablaAccesosNodosPuertas",columnasTabAccesosNodos,[0, "asc"],50,true);
		}
	});
}

//marcar desmarcar check
function activarDesactivarCheck(elemento,idlin,nodo,puerta,idacceso){
	//var pulsado=$("#nodoPuertaAc_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val();
	var pulsado=$("#"+elemento.id+"_hidden").val();
	
	if(pulsado=="s"){
		/*$("#nodoPuertaAc_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val("n");
		$("#nodoPuertaAc_"+nodo+"_"+puerta+"_"+idlin+"").children('i').removeClass('fas fa-check');*/
		
		$("[id="+elemento.id+"_hidden]").val("n");
        //quitar check
		$("[id="+elemento.id+"]").children('i').removeClass('fas fa-check');
        //poner aspa
        $("[id="+elemento.id+"]").children('i').addClass('fas fa-times');
        $("[id="+elemento.id+"]").children('i').css("color", "red");
	}else if(pulsado=="n"){
		/*$("#nodoPuertaAc_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val("s");
		$("#nodoPuertaAc_"+nodo+"_"+puerta+"_"+idlin+"").children('i').addClass('fas fa-check');*/
		$("[id="+elemento.id+"_hidden]").val("s");
        //quitar aspa
		$("[id="+elemento.id+"]").children('i').removeClass('fas fa-times');
        //poner check
		$("[id="+elemento.id+"]").children('i').addClass('fas fa-check');
        $("[id="+elemento.id+"]").children('i').css("color", "green");
		
		//poner en verde el general
		var idPulsadoSplit=elemento.id.split("_");
		var idFormado="nodoPuertaAc_"+idPulsadoSplit[1]+"_"+idPulsadoSplit[2]+"_"+idPulsadoSplit[3];
		
		$("#"+idFormado+"_hidden").val("s");
        //quitar aspa
		$("#"+idFormado+"").children('i').removeClass('fas fa-times');
        //poner check
		$("#"+idFormado+"").children('i').addClass('fas fa-check');
        $("#"+idFormado+"").children('i').css("color", "green");
		
	}
	//por si queremos que esto update//editarLineaAccesosNodosPuertas(idlin,puerta,nodo,idacceso);
}

//marcar desmarcar check
function activarDesactivarCheckGeneral(elemento,idlin,nodo,puerta,idacceso){
	//var pulsado=$("#nodoPuertaAc_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val();
	var pulsado=$("#"+elemento.id+"_hidden").val();
	var pulsadoSplit=elemento.id.split("nodoPuertaAc");
	
	if(pulsado=="s"){
		/*$("#nodoPuertaAc_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val("n");
		$("#nodoPuertaAc_"+nodo+"_"+puerta+"_"+idlin+"").children('i').removeClass('fas fa-check');*/
		
		$("#"+elemento.id+"_hidden").val("n");
        //quitar check
		$("#"+elemento.id+"").children('i').removeClass('fas fa-check');
        //poner aspa
        $("#"+elemento.id+"").children('i').addClass('fas fa-times');
        $("#"+elemento.id+"").children('i').css("color", "red");
		
		//LUNES
		$("#nodoPuertaAcL"+pulsadoSplit[1]+"_hidden").val("n");
        //quitar check
		$("#nodoPuertaAcL"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-check');
		//poner aspa
        $("#nodoPuertaAcL"+pulsadoSplit[1]+"").children('i').addClass('fas fa-times');
        $("#nodoPuertaAcL"+pulsadoSplit[1]+"").children('i').css("color", "red");
		
		//MARTES
		$("#nodoPuertaAcM"+pulsadoSplit[1]+"_hidden").val("n");
        //quitar check
		$("#nodoPuertaAcM"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-check');
		//poner aspa
        $("#nodoPuertaAcM"+pulsadoSplit[1]+"").children('i').addClass('fas fa-times');
        $("#nodoPuertaAcM"+pulsadoSplit[1]+"").children('i').css("color", "red");
		
		//MIERCOLES
		$("#nodoPuertaAcX"+pulsadoSplit[1]+"_hidden").val("n");
        //quitar check
		$("#nodoPuertaAcX"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-check');
		//poner aspa
        $("#nodoPuertaAcX"+pulsadoSplit[1]+"").children('i').addClass('fas fa-times');
        $("#nodoPuertaAcX"+pulsadoSplit[1]+"").children('i').css("color", "red");
		
		//JUEVES
		$("#nodoPuertaAcJ"+pulsadoSplit[1]+"_hidden").val("n");
        //quitar check
		$("#nodoPuertaAcJ"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-check');
		//poner aspa
        $("#nodoPuertaAcJ"+pulsadoSplit[1]+"").children('i').addClass('fas fa-times');
        $("#nodoPuertaAcJ"+pulsadoSplit[1]+"").children('i').css("color", "red");
		
		//VIERNES
		$("#nodoPuertaAcV"+pulsadoSplit[1]+"_hidden").val("n");
        //quitar check
		$("#nodoPuertaAcV"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-check');
		//poner aspa
        $("#nodoPuertaAcV"+pulsadoSplit[1]+"").children('i').addClass('fas fa-times');
        $("#nodoPuertaAcV"+pulsadoSplit[1]+"").children('i').css("color", "red");
		
		//SABADO
		$("#nodoPuertaAcS"+pulsadoSplit[1]+"_hidden").val("n");
        //quitar check
		$("#nodoPuertaAcS"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-check');
		//poner aspa
        $("#nodoPuertaAcS"+pulsadoSplit[1]+"").children('i').addClass('fas fa-times');
        $("#nodoPuertaAcS"+pulsadoSplit[1]+"").children('i').css("color", "red");
		
		//Domingo
		$("#nodoPuertaAcD"+pulsadoSplit[1]+"_hidden").val("n");
        //quitar check
		$("#nodoPuertaAcD"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-check');
		//poner aspa
        $("#nodoPuertaAcD"+pulsadoSplit[1]+"").children('i').addClass('fas fa-times');
        $("#nodoPuertaAcD"+pulsadoSplit[1]+"").children('i').css("color", "red");
		
	}else if(pulsado=="n"){
		/*$("#nodoPuertaAc_"+nodo+"_"+puerta+"_"+idlin+"_hidden").val("s");
		$("#nodoPuertaAc_"+nodo+"_"+puerta+"_"+idlin+"").children('i').addClass('fas fa-check');*/
		$("#"+elemento.id+"_hidden").val("s");
        //quitar aspa
		$("#"+elemento.id+"").children('i').removeClass('fas fa-times');
        //poner check
		$("#"+elemento.id+"").children('i').addClass('fas fa-check');
        $("#"+elemento.id+"").children('i').css("color", "green");
		
		//LUNES
		$("#nodoPuertaAcL"+pulsadoSplit[1]+"_hidden").val("s");
        //quitar check
		$("#nodoPuertaAcL"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-times');
		//poner aspa
        $("#nodoPuertaAcL"+pulsadoSplit[1]+"").children('i').addClass('fas fa-check');
        $("#nodoPuertaAcL"+pulsadoSplit[1]+"").children('i').css("color", "green");
		
		//MARTES
		$("#nodoPuertaAcM"+pulsadoSplit[1]+"_hidden").val("s");
        //quitar check
		$("#nodoPuertaAcM"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-times');
		//poner aspa
        $("#nodoPuertaAcM"+pulsadoSplit[1]+"").children('i').addClass('fas fa-check');
        $("#nodoPuertaAcM"+pulsadoSplit[1]+"").children('i').css("color", "green");
		
		//MIERCOLES
		$("#nodoPuertaAcX"+pulsadoSplit[1]+"_hidden").val("s");
        //quitar check
		$("#nodoPuertaAcX"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-times');
		//poner aspa
        $("#nodoPuertaAcX"+pulsadoSplit[1]+"").children('i').addClass('fas fa-check');
        $("#nodoPuertaAcX"+pulsadoSplit[1]+"").children('i').css("color", "green");
		
		//JUEVES
		$("#nodoPuertaAcJ"+pulsadoSplit[1]+"_hidden").val("s");
        //quitar check
		$("#nodoPuertaAcJ"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-times');
		//poner aspa
        $("#nodoPuertaAcJ"+pulsadoSplit[1]+"").children('i').addClass('fas fa-check');
        $("#nodoPuertaAcJ"+pulsadoSplit[1]+"").children('i').css("color", "green");
		
		//VIERNES
		$("#nodoPuertaAcV"+pulsadoSplit[1]+"_hidden").val("s");
        //quitar check
		$("#nodoPuertaAcV"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-times');
		//poner aspa
        $("#nodoPuertaAcV"+pulsadoSplit[1]+"").children('i').addClass('fas fa-check');
        $("#nodoPuertaAcV"+pulsadoSplit[1]+"").children('i').css("color", "green");
		
		//SABADO
		$("#nodoPuertaAcS"+pulsadoSplit[1]+"_hidden").val("s");
        //quitar check
		$("#nodoPuertaAcS"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-times');
		//poner aspa
        $("#nodoPuertaAcS"+pulsadoSplit[1]+"").children('i').addClass('fas fa-check');
        $("#nodoPuertaAcS"+pulsadoSplit[1]+"").children('i').css("color", "green");
		
		//DOMINGO
		$("#nodoPuertaAcD"+pulsadoSplit[1]+"_hidden").val("s");
        //quitar check
		$("#nodoPuertaAcD"+pulsadoSplit[1]+"").children('i').removeClass('fas fa-times');
		//poner aspa
        $("#nodoPuertaAcD"+pulsadoSplit[1]+"").children('i').addClass('fas fa-check');
        $("#nodoPuertaAcD"+pulsadoSplit[1]+"").children('i').css("color", "green");
	}
	//por si queremos que esto update//editarLineaAccesosNodosPuertas(idlin,puerta,nodo,idacceso);
}

//borrar PIN safey credencial
function borraSafeyPinCredenciales(id){
	$("#cargandotablaCredencialesPinSafey").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "43", "id": id},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesPinSafey").html(data);
				Swal.fire('Correcto','Ok.','success');
				
				var columnasTablaPin= [null,{ "width": "8%" },{ "width": "8%" },{ "width": "10%" },{ "width": "15%" },{ "width": "26%" },{ "width": "27%" },{ "width": "6%" }];
        		cargarTabla.init("tablaCredencialesPinSafey",columnasTablaPin,[0, "asc"],25,true);
			}
		}
	});
}

//borrar LLAVE safey credencial
function borraSafeyLlaveCredenciales(id){
	$("#cargandotablaCredencialesLlaveSafey").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "44", "id": id},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesLlaveSafey").html(data);
				
				Swal.fire('Correcto','Ok.','success');
				
				var columnasTablaLlave= [null,{ "width": "8%" },{ "width": "8%" },{ "width": "13.25%" },{ "width": "27.25%" },{ "width": "10.62%" },{ "width": "7.62%" },{ "width": "2%" },{ "width": "15.25%" },{ "width": "5%" }];
				cargarTabla.init("tablaCredencialesLlaveSafey",columnasTablaLlave,[0, "asc"],25,true);
			}
		}
	});
}

//borrar MANDO safey credencial
function borraSafeyMandoCredenciales(id){
	$("#cargandotablaCredencialesMandoSafey").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "45", "id": id},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesMandoSafey").html(data);
				
				Swal.fire('Correcto','Ok.','success');
				
				var columnasTablaMando= [null,{ "width": "10%" },{ "width": "21.25%" },{ "width": "21.25%" },{ "width": "21.25%" },{ "width": "21.25%" },{ "width": "5%" }];
        		cargarTabla.init("tablaCredencialesMandoSafey",columnasTablaMando,[0, "asc"],25,true);
			}
		}
	});
}

function historialSafeyPuertas(idnodo){
	$('#cargando').show();
	
	var fechaIni=$("#fechaIniHistorialPuertas").val();
    var fechaFin=$("#fechaFinHistorialPuertas").val();
	var puerta=$("#puertaHistorialSafey").val();
	
	$.ajax({
		url : 'adminajax.php',
		data : { "op": "46", "fechaIni": fechaIni, "fechaFin": fechaFin, "puerta": puerta , "idnodo": idnodo },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data=="n"){
				mostrarToastFire("Problemas al realizar la acción.","error");
			}else{
				cargaLocation('generaXLS.php?o=1');
			}
		}
	});
}

//anadir pin safey
function crearCredencialPinSafey(){
	var pin=$("#pinCredencialSafey").val();
	var serie=$("#pinserieCredencialSafey").val();
	var serial=$("#pinSerialCredencialSafey").val();
	
    mostrarRecargarTabla("tablaCredencialesPinSafey");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "47", 'pin': pin, 'serie': serie, 'serial': serial },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesPinSafey").html(data);
				Swal.fire('Correcto','Ok.','success');
			}else{
				Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
			}
			var columnasTablaPin= [null,{ "width": "8%" },{ "width": "8%" },{ "width": "10%" },{ "width": "15%" },{ "width": "26%" },{ "width": "27%" },{ "width": "6%" }];
			cargarTabla.init("tablaCredencialesPinSafey",columnasTablaPin,[0, "asc"],25,true);
		}
	});
}

//editar pin safey
function editarCredencialPinSafey(idlin){
	var pin=$("#pinCredencialSafey"+idlin+"").val();
	var serie=$("#pinSerieCredencialSafey"+idlin+"").val();
	var serial=$("#pinSerialCredencialSafey"+idlin+"").val();
	var cliente=$("#pinClienteCredencialSafey"+idlin+" option:selected").val();
	
    mostrarRecargarTabla("tablaCredencialesPinSafey");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "48", 'pin': pin, 'serie': serie, 'serial': serial, 'idlin': idlin, 'cliente': cliente },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesPinSafey").html(data);
				Swal.fire('Correcto','Ok.','success');
			}else{
				Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
			}
			var columnasTablaPin= [null,{ "width": "8%" },{ "width": "8%" },{ "width": "10%" },{ "width": "15%" },{ "width": "26%" },{ "width": "27%" },{ "width": "6%" }];
        	cargarTabla.init("tablaCredencialesPinSafey",columnasTablaPin,[0, "asc"],25,true);
		}
	});
}

//anadir llave safey
function crearCredencialLlaveSafey(){
	var serie=$("#llaveSerieCredencialSafey").val();
	var serial=$("#llaveSerialCredencialSafey").val();
	var tipo=$("#llaveTipoCredencialSafey").val();
	var frecuencia=$("#llaveFrecuenciaCredencialSafey").val();
	var descripcion=$("#llaveDescripcionCredencialSafey").val();
	
    mostrarRecargarTabla("tablaCredencialesLlaveSafey");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "49", 'frecuencia': frecuencia, 'tipo': tipo, 'serie': serie, 'serial': serial, 'descripcion': descripcion },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesLlaveSafey").html(data);
				
				$("#llaveSerieCredencialSafey").val("");
				$("#llaveSerialCredencialSafey").val("");
				
				Swal.fire('Correcto','Ok.','success');
			}else{
				Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
			}
			var columnasTablaLlave= [null,{ "width": "8%" },{ "width": "8%" },{ "width": "13.25%" },{ "width": "27.25%" },{ "width": "10.62%" },{ "width": "7.62%" },{ "width": "2%" },{ "width": "15.25%" },{ "width": "5%" }];
        	cargarTabla.init("tablaCredencialesLlaveSafey",columnasTablaLlave,[0, "asc"],25,true);
		}
	});
}

//editar llave safey
function editarCredencialLlaveSafey(idlin){
	var serie=$("#llaveSerieCredencialSafey"+idlin+"").val();
	var serial=$("#llaveSerialCredencialSafey"+idlin+"").val();
	//var tipo=$("#llaveTipoCredencialSafey").val();
	//var frecuencia=$("#llaveFrecuenciaCredencialSafey").val();
	var cliente=$("#llaveClienteCredencialSafey"+idlin+" option:selected").val();
	var descripcion=$("#llaveDescripcionCredencialSafey"+idlin+"").val();
	
    mostrarRecargarTabla("tablaCredencialesLlaveSafey");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "50", 'idlin': idlin, 'cliente': cliente, 'serie': serie, 'serial': serial, 'descripcion': descripcion },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesLlaveSafey").html(data);
				Swal.fire('Correcto','Ok.','success');
			}else{
				 Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
			}
			var columnasTablaLlave= [null,{ "width": "8%" },{ "width": "8%" },{ "width": "13.25%" },{ "width": "27.25%" },{ "width": "10.62%" },{ "width": "7.62%" },{ "width": "2%" },{ "width": "15.25%" },{ "width": "5%" }];
        	cargarTabla.init("tablaCredencialesLlaveSafey",columnasTablaLlave,[0, "asc"],25,true);
		}
	});
}

//anadir mando safey
function crearCredencialMandoSafey(){
	var serie=$("#mandoserieCredencialSafey").val();
	var serial=$("#mandoerialCredencialSafey").val();
	
    mostrarRecargarTabla("tablaCredencialesMandoSafey");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "51", 'serie': serie, 'serial': serial },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesMandoSafey").html(data);
				Swal.fire('Correcto','Ok.','success');
			}else{
				Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
			}
			var columnasTablaMando= [null,{ "width": "10%" },{ "width": "21.25%" },{ "width": "21.25%" },{ "width": "21.25%" },{ "width": "21.25%" },{ "width": "5%" }];
        	cargarTabla.init("tablaCredencialesMandoSafey",columnasTablaMando,[0, "asc"],25,true);
		}
	});
}

//editar mando safey
function editarCredencialMandoSafey(idlin){
	var serie=$("#mandoSerieCredencialSafey"+idlin+"").val();
	var serial=$("#mandoSerialCredencialSafey"+idlin+"").val();
	var cliente=$("#mandoClienteCredencialSafey"+idlin+" option:selected").val();
	
    mostrarRecargarTabla("tablaCredencialesMandoSafey");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "52", 'serie': serie, 'serial': serial, 'cliente': cliente, 'idlin': idlin },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesMandoSafey").html(data);
				//Swal.fire('Correcto','Ok.','success');
			}else{
				Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
			}
			var columnasTablaMando= [null,{ "width": "10%" },{ "width": "21.25%" },{ "width": "21.25%" },{ "width": "21.25%" },{ "width": "21.25%" },{ "width": "5%" }];
        	cargarTabla.init("tablaCredencialesMandoSafey",columnasTablaMando,[0, "asc"],25,true);
		}
	});
}

function guardarNombrePuertaSafey(puerta,usu){
	var nombre=$("#puertaSafeyNom"+puerta+"").val();
	
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "53", 'nombre': nombre, 'puerta': puerta, 'usu': usu},
		type : 'POST',
		success : function(data){
			if(data=="n"){
				//$("#puertaSafeyNom"+puerta+"").val("");
				//Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
			}else{
				Swal.fire('Correcto','Ok.','success');
			}
		}
	});
}

//borrar nodo vinculado safey
function borraNodosVinculadosSafey(idlin,idNodo){
	$("#cargandotablaNodosVinculados").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "54", "idNodo": idNodo, "idlin": idlin},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				var respuesta=data.split("@#");

				$("#tablaNodosVinculados").html(respuesta[0]);
				
				//tabla
        		var columnasTabNodosVinculados= [null,{ "width": "40%" },{ "width": "45%" },{ "width": "15%" }];
        		cargarTabla.init("tablaNodosVinculados",columnasTabNodosVinculados,[1, "asc"],50,true);
				
				$("#divImgSafeyNodo").html(respuesta[1]);
				
				$("#divSelectNodos").html(respuesta[2]);
				
			}
			$("#cargandotablaNodosVinculados").hide();
		}
	});
}

//anadir nodo horario
function anadirNodoVinculadoSafey(npadre){
	$("#cargandotablaNodosVinculados").show();
	var n=$("#nodoVinculadoSafey option:selected").val();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "55", "npadre": npadre, "n": n},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				var respuesta=data.split("@#");

				$("#tablaNodosVinculados").html(respuesta[0]);
				//tabla
        		var columnasTabNodosVinculados= [null,{ "width": "40%" },{ "width": "45%" },{ "width": "15%" }];
        		cargarTabla.init("tablaNodosVinculados",columnasTabNodosVinculados,[1, "asc"],50,true);
				
				$("#divImgSafeyNodo").html(respuesta[1]);
				$("#divSelectNodos").html(respuesta[2]);
				
			}else{
				mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
			}
			$("#cargandotablaNodosVinculados").hide();
		}
	});
}

//establecer las salidas placa cada puerta
function cambiarSalidaPuertaPlaca(nodo,puerta,electro){
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "56", "nodo": nodo, "puerta": puerta, "electro": electro},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#divImgSafeyNodo").html(data);
			}else{
				mostrarSwalFire("No se ha podido realizar la acción.","Intentalo de nuevo","warning");
			}
		}
	});
}

//establecer la configuracion del rele
function cambiarPulsacionCorrienteRele(nodo,puerta,tipo){
	var segundos=$("#releSegundosPulsacion"+puerta+"").val();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "57", "nodo": nodo, "puerta": puerta, "tipo": tipo, "segundos": segundos},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#divImgSafeyNodo").html(data);
			}else{
				mostrarSwalFire("No se ha podido realizar la acción.","Intentalo de nuevo","warning");
			}
		}
	});
}

//establecer la configuracion del rele, tiempo
function cambiarTiempoPulsacionCorrienteRele(nodo,puerta){
	var segundos=$("#releSegundosPulsacion"+puerta+"").val();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "58", "nodo": nodo, "puerta": puerta, "segundos": segundos},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#divImgSafeyNodo").html(data);
			}else{
				mostrarSwalFire("No se ha podido realizar la acción.","Intentalo de nuevo","warning");
			}
		}
	});
}

//enviar mail con accesos safey credenciales
function enviarAcceosSafeyMail(id){
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "69", "id": id},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				Swal.fire('Correcto','Se ha enviado el email con los accesos.','success');
			}else{
				Swal.fire('Error','Error no se ha podido enviar el email.','error');
			}
		}
	});
}

//funcion para traspasar y utilizar 100 pines del almacen a safey
function obtenerPinesDesdeAlmacenSafey(){
    
    var cantidadPinesCredenciales=$("#cantidadPinesCredenciales").val();
    
    if(cantidadPinesCredenciales>0){
        $("#cargandotablaCredencialesPinSafey").show();
        $.ajax({
            url : 'adminajax.php',
            data : { 'op': "77", cantidadPines:cantidadPinesCredenciales},
            type : 'POST',
            success : function(data){
                if(data!="n"){

                    var datos=data.split("@#");

                    $("#tablaCredencialesPinSafey").html(datos[0]);

                    Swal.fire('Correcto','Se han generado '+datos[1]+' nuevos pines para asignar a clientes.','success');

                    var columnasTablaPin= [null,{ "width": "8%" },{ "width": "8%" },{ "width": "10%" },{ "width": "15%" },{ "width": "26%" },{ "width": "27%" },{ "width": "6%" }];
        			cargarTabla.init("tablaCredencialesPinSafey",columnasTablaPin,[0, "asc"],25,true);
                }else{
                    Swal.fire('Error','Error no se ha podido enviar el email.','error');
                }
            }
        });
    }else{
         Swal.fire('Error','Introduzca un número mayor de 0 para generar los pines deseados','error');
    }

}

//funcion para traspasar y utilizar 100 llaves del almacen a safey
function obtenerLlavesDesdeAlmacenSafey(){
    
    var cantidadLlavesCredenciales=$("#cantidadLlavesCredenciales").val();
    
    if(cantidadLlavesCredenciales>0){
	    $("#cargandotablaCredencialesLlaveSafey").show();
        $.ajax({
            url : 'adminajax.php',
            data : { 'op': "78", cantidadLlaves:cantidadLlavesCredenciales},
            type : 'POST',
            success : function(data){
                if(data!="n"){

                    var datos=data.split("@#");

                    $("#tablaCredencialesLlaveSafey").html(datos[0]);

                    Swal.fire('Correcto','Se han generado '+datos[1]+' nuevas llaves para asignar a clientes.','success');

                    var columnasTablaLlave= [null,{ "width": "8%" },{ "width": "8%" },{ "width": "13.25%" },{ "width": "27.25%" },{ "width": "10.62%" },{ "width": "7.62%" },{ "width": "2%" },{ "width": "15.25%" },{ "width": "5%" }];
                    cargarTabla.init("tablaCredencialesLlaveSafey",columnasTablaLlave,[0, "asc"],25,true);
                }else{
                    Swal.fire('Error','Error no se ha podido enviar el email.','error');
                }
            }
        });
    }else{
        Swal.fire('Error','Introduzca un número mayor de 0 para generar las llaves deseadas','error');
    }
}

//añadir cantidad pin safey
function asignarCantidadCredencialPinSafey(){
    
	var cantidad=$("#cantidadAsignarPinCredencial").val();
	var cliente=$("#clienteAsignarPinCredencial option:selected").val();
        
    if(cantidad>0 && cliente>0){
        mostrarRecargarTabla("tablaCredencialesPinSafey");
       $.ajax({
		url : 'adminajax.php',
		data : { 'op': "89", 'cantidad': cantidad, 'cliente': cliente },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaCredencialesPinSafey").html(data);
				Swal.fire('Correcto','Ok.','success');
                $("#cantidadAsignarPinCredencial").val("");
                $("#clienteAsignarPinCredencial").val(0);
			}else{
				Swal.fire('Error','No hay pines suficientes para la cantidad introducida','error');
			}
           	var columnasTablaPin= [null,{ "width": "8%" },{ "width": "8%" },{ "width": "10%" },{ "width": "15%" },{ "width": "26%" },{ "width": "27%" },{ "width": "6%" }];
        	cargarTabla.init("tablaCredencialesPinSafey",columnasTablaPin,[0, "asc"],25,true);
		}
	});
    }else{
        Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
    }
}

//añadir cantidad llave safey
function asignarCantidadCredencialLlaveSafey(){
    
	var cantidad=$("#cantidadAsignarLlaveCredencial").val();
	var cliente=$("#clienteAsignarLlaveCredencial option:selected").val();
        
    if(cantidad>0 && cliente>0){
        mostrarRecargarTabla("tablaCredencialesLlaveSafey");
       $.ajax({
		url : 'adminajax.php',
		data : { 'op': "90", 'cantidad': cantidad, 'cliente': cliente },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaCredencialesLlaveSafey").html(data);
				Swal.fire('Correcto','Ok.','success');
                $("#cantidadAsignarLlaveCredencial").val("");
                $("#clienteAsignarLlaveCredencial").val(0);
			}else{
				Swal.fire('Error','No hay llaves suficientes para la cantidad introducida','error');
			}
            var columnasTablaLlave= [null,{ "width": "8%" },{ "width": "8%" },{ "width": "13.25%" },{ "width": "27.25%" },{ "width": "10.62%" },{ "width": "7.62%" },{ "width": "2%" },{ "width": "15.25%" },{ "width": "5%" }];
        	cargarTabla.init("tablaCredencialesLlaveSafey",columnasTablaLlave,[0, "asc"],25,true);
		}
	});
    }else{
        Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
    }
}

//filtrar historial fallido puertas safey
function filtrarHistorialFallidoPuertasSafey(n){
	var fechaIni=$("#fechaIniHistorialFallidoPuertas").val();
	var fechaFin=$("#fechaFinHistorialFallidoPuertas").val();
    var puerta=$("#puertaHistorialFallidoSafey option:selected").val();
	//$('#cargando').show();
    mostrarRecargarTabla("tablaHistorialFallidosPuertas");
    
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "95", 'fechaIni': fechaIni, 'fechaFin': fechaFin, "n": n, "puerta": puerta },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data!="n"){
				//Swal.fire('Correcto','Ok.','success');
				
			   	$("#tablaHistorialFallidosPuertas").html(data);
                
               	//tabla historial fallidos
				var columnasTabHistorialFallidos= [null,{ "width": "15%" },{ "width": "15%" },{ "width": "20%" },{ "width": "15%" },{ "width": "15%" },{ "width": "15%" },{ "width": "10%" }];
				cargarTabla.init("tablaHistorialFallidosPuertas",columnasTabHistorialFallidos,[1, "asc"],200,true);
			}
		}
	});
}

function historialFallidosSafeyPuertas(idnodo){
	$('#cargando').show();
	
	var fechaIni=$("#fechaIniHistorialFallidoPuertas").val();
    var fechaFin=$("#fechaFinHistorialFallidoPuertas").val();
	var puerta=$("#puertaHistorialFallidoSafey").val();
	
	$.ajax({
		url : 'adminajax.php',
		data : { "op": "116", "fechaIni": fechaIni, "fechaFin": fechaFin, "puerta": puerta , "idnodo": idnodo },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data=="n"){
				mostrarToastFire("Problemas al realizar la acción.","error");
			}else{
				cargaLocation('generaXLS.php?o=4');
			}
		}
	});
}

//funcion para traspasar y utilizar 100 mandos del almacen a safey
function obtenerMandosDesdeAlmacenSafey(){
    
    var cantidadMandosCredenciales=$("#cantidadMandosCredenciales").val();
    
    if(cantidadMandosCredenciales>0){
	    $("#cargandotablaCredencialesMandoSafey").show();
        $.ajax({
            url : 'adminajax.php',
            data : { 'op': "117", cantidadMandos:cantidadMandosCredenciales},
            type : 'POST',
            success : function(data){
                if(data!="n"){

                    var datos=data.split("@#");

                    $("#tablaCredencialesMandoSafey").html(datos[0]);

                    Swal.fire('Correcto','Se han generado '+datos[1]+' nuevas llaves para asignar a clientes.','success');

                    var columnasTablaMando= [null,{ "width": "10%" },{ "width": "21.25%" },{ "width": "21.25%" },{ "width": "21.25%" },{ "width": "21.25%" },{ "width": "5%" }];
        			cargarTabla.init("tablaCredencialesMandoSafey",columnasTablaMando,[0, "asc"],25,true);
                }else{
                    Swal.fire('Error','Error no, intentalo en otro momento.','error');
                }
            }
        });
    }else{
        Swal.fire('Error','Introduzca un número mayor de 0 para generar los mandos deseados','error');
    }
}

//exportar listado credenciales
function historialAccesosSafeyExcel(){
	$('#cargando').show();
	
	var usuario=$("#selectUsuariosFiltro").val();
	
	$.ajax({
		url : 'adminajax.php',
		data : { "op": "118", "usuario": usuario},
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data=="n"){
				mostrarToastFire("Problemas al realizar la acción.","error");
			}else{
				cargaLocation('generaXLS.php?o=5');
			}
		}
	});
}

//eliminar pin del cliente y de bbdd
function borraSafeyPinAcceso(idPin,idAcceso){
	$('#cargando').show();
	
	$.ajax({
		url : 'adminajax.php',
		data : { "op": "119", "idPin": idPin, "idAcceso": idAcceso},
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data=="n"){
				mostrarToastFire("Problemas al realizar la acción.","error");
			}else{
				$("#pin").html(data);
			}
		}
	});
}

//añadir nuevo metodo de pago
function añadirMetodoPago(nodo,cliente){
	$("#cargandotablaConfiguracionPagos").show();
	let metodoPago=$("#metodopago").val();
	
	if(nodo>0 && cliente>0 && metodoPago>0){
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "121", "nodo": nodo, "cliente": cliente, "metodoPago": metodoPago},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					var datos=data.split("@#");
					
					$("#tablaConfiguracionPagos").html(datos[0]); 
					
					$("#metodopago").html(datos[1]); 

					//tabla
					var columnasTabConfiguracionPagos= [null,{ "width": "80%" },{ "width": "20%" }];
					cargarTabla.init("tablaConfiguracionPagos",columnasTabConfiguracionPagos,[1, "asc"],25,true);

					//limpiar
					$("#metodopago").val("");
				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaConfiguracionPagos").hide();
			}
		});  
	}else{
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaConfiguracionPagos").hide();
	}
}

//editar metodo de pago
function editarMetodoPago(id,idNodo){
	$("#cargandotablaConfiguracionPagos").show();
	let metodoPago=$("#metodopago"+id).val();
	
	if(id>0 && metodoPago>0){
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "122", "id": id, "idNodo": idNodo, "metodoPago": metodoPago},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaConfiguracionPagos").html(data);

					//tabla
					var columnasTabConfiguracionPagos= [null,{ "width": "80%" },{ "width": "20%" }];
					cargarTabla.init("tablaConfiguracionPagos",columnasTabConfiguracionPagos,[1, "asc"],25,true);

					//limpiar
					$("#metodopago").val("");
				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaConfiguracionPagos").hide();
			}
		});  
	}else{
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaConfiguracionPagos").hide();
	}
}

//borrar metodo de pago
function borraMetodoPago(id,idNodo){
	$("#cargandotablaConfiguracionPagos").show();
	
	if(id>0 && idNodo>0){
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "123", "id": id, "idNodo": idNodo},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					var datos=data.split("@#");
					
					$("#tablaConfiguracionPagos").html(datos[0]); 
					$("#metodopago").html(datos[1]);

					//tabla
					var columnasTabConfiguracionPagos= [null,{ "width": "80%" },{ "width": "20%" }];
					cargarTabla.init("tablaConfiguracionPagos",columnasTabConfiguracionPagos,[1, "asc"],25,true);

					//limpiar
					$("#metodopago").val("");
				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaConfiguracionPagos").hide();
			}
		});  
	}else{
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaConfiguracionPagos").hide();
	}
}

//añadir nuevo codigo promocional
function añadirCodigoPromocional(nodo,cliente){
	$("#cargandotablaConfiguracionCodigosPromo").show();
	let codigoPromocional=$("#codigopromocional").val();
	let tipo=$("#tipocodpromo").val();
	let cantidad=$("#cantidadcodpromo").val();
	
	if(nodo>0 && cliente>0 && codigoPromocional!="" && ((tipo=="e" && cantidad>0.0) || (tipo=="p" && cantidad<=99.0))){
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "124", "nodo": nodo, "cliente": cliente, "codigoPromocional": codigoPromocional, "tipo": tipo, "cantidad": cantidad},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaConfiguracionCodigosPromo").html(data);

					//tabla
					var columnasTabConfiguracionPagos= [null,{ "width": "55%" },{ "width": "15%" },{ "width": "15%" },{ "width": "15%" }];
					cargarTabla.init("tablaConfiguracionCodigosPromo",columnasTabConfiguracionPagos,[1, "asc"],25,true);

					//limpiar
					$("#metodopago").val("");
				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaConfiguracionCodigosPromo").hide();
			}
		});  
	}else{
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaConfiguracionCodigosPromo").hide();
	}
}

//editar codigo promocional
function editarCodigoPromocional(id,idNodo){
	$("#cargandotablaConfiguracionCodigosPromo").show();
	let codigoPromocional=$("#codigopromocional"+id).val();
	let tipo=$("#tipocodpromo"+id).val();
	let cantidad=$("#cantidadcodpromo"+id).val();

	if(id>0 && codigoPromocional!="" && ((tipo=="e" && cantidad>0.0) || (tipo=="p" && cantidad<=99.0))){
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "125", "id": id, "idNodo": idNodo, "codigoPromocional": codigoPromocional, "tipo": tipo, "cantidad": cantidad},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaConfiguracionCodigosPromo").html(data);

					//tabla
					var columnasTabConfiguracionPagos= [null,{ "width": "55%" },{ "width": "15%" },{ "width": "15%" },{ "width": "15%" }];
					cargarTabla.init("tablaConfiguracionCodigosPromo",columnasTabConfiguracionPagos,[1, "asc"],25,true);

					//limpiar
					$("#metodopago").val("");
				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaConfiguracionCodigosPromo").hide();
			}
		});  
	}else{
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaConfiguracionCodigosPromo").hide();
	}
}

//borrar codigo promocional
function borraCodigoPromocional(id,idNodo){
	$("#cargandotablaConfiguracionCodigosPromo").show();
	
	if(id>0 && idNodo>0){
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "126", "id": id, "idNodo": idNodo},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaConfiguracionCodigosPromo").html(data);

					//tabla
					var columnasTabConfiguracionPagos= [null,{ "width": "55%" },{ "width": "15%" },{ "width": "15%" },{ "width": "15%" }];
					cargarTabla.init("tablaConfiguracionCodigosPromo",columnasTabConfiguracionPagos,[1, "asc"],25,true);

					//limpiar
					$("#metodopago").val("");
				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaConfiguracionCodigosPromo").hide();
			}
		});  
	}else{
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaConfiguracionCodigosPromo").hide();
	}
}

//añadir nuevo pago manualmente
function anadirPago(idAcceso){
	$("#cargandotablaHistorialPagos").show();
	
	let idNodo=$("#nodopagos").val();
	let servicio=$("#tipoServicioPago").val();
	let metodoPago=$("#metodopago").val();
	let codPromo=/*$("#codpromo").val()*/"";
	let fInicio=$("#finiciopago").val();
	let fFin=$("#ffinpago").val();
	//let fPago=$("#fechaPago").val();

	if(idAcceso>0 && idNodo>0 && servicio!="" && metodoPago>0 && fInicio!="" && fFin!=""){
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "127", "idAcceso": idAcceso,"idNodo": idNodo,"servicio": servicio, "codPromo": codPromo, "metodoPago": metodoPago, "fInicio": fInicio, "fFin": fFin},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaHistorialPagos").html(data);

					//tabla
					var columnasTabHistorialPagos= [null,{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "20%" }];
					cargarTabla.init("tablaHistorialPagos",columnasTabHistorialPagos,[1, "asc"],25,true);

				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaHistorialPagos").hide();
			}
		});  
	}else{
		alert();
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaHistorialPagos").hide();
	}
}

//editar pago 
function editarPago(idAcceso,idPago){
	$("#cargandotablaHistorialPagos").show();
	let idNodo=$("#nodopagos"+idPago).val();
	let servicio=$("#tipoServicioPago"+idPago).val();
	let codPromo=/*$("#codpromo"+idPago).val()*/"";
	let metodoPago=$("#metodopago"+idPago).val();
	let fInicio=$("#finiciopago"+idPago).val();
	let fFin=$("#ffinpago"+idPago).val();
	//let fRealPago=$("#fRealPago"+idPago).val();
	

	if(idAcceso>0 && idNodo>0 && servicio!="" && metodoPago>0 && fInicio!="" && fFin!=""){
		
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "129", "idAcceso": idAcceso,"idPago": idPago,"idNodo": idNodo,"servicio": servicio, "codPromo": codPromo, "metodoPago": metodoPago, "fInicio": fInicio, "fFin": fFin},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaHistorialPagos").html(data);

					//tabla
					var columnasTabHistorialPagos= [null,{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "20%" }];
					cargarTabla.init("tablaHistorialPagos",columnasTabHistorialPagos,[1, "asc"],25,true);

				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaHistorialPagos").hide();
			}
		});  
	}else{
		alert();
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaHistorialPagos").hide();
	}
}

//borrar pago
function borraPago(idAcceso,idPago){
	$("#cargandotablaHistorialPagos").show();
	
	if(idAcceso>0 && idPago>0){
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "128", "idAcceso": idAcceso, "idPago": idPago},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaHistorialPagos").html(data);

					//tabla
					var columnasTabHistorialPagos= [null,{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "20%" }];
                    cargarTabla.init("tablaHistorialPagos",columnasTabHistorialPagos,[1, "asc"],200,true);
					
				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaHistorialPagos").hide();
			}
		});  
	}else{
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaHistorialPagos").hide();
	}
}


//dar linea por pagada
function pagarLineaAccesoSafey(idAcceso,idPago){
	$("#cargandotablaHistorialPagos").show();
	var fPago=$("#fRealPago"+idPago).val();
	if(idAcceso>0 && idPago>0){
        if(fPago!=""){
            $.ajax({
            url : 'adminajax.php',
            data : { 'op': "160", "idAcceso": idAcceso, "idPago": idPago, "fPago": fPago},
            type : 'POST',
                success : function(data){
                    $("#cargandotablaHistorialPagos").hide();
                    if(data!="n"){
                        $("#tablaHistorialPagos").html(data);

                        //tabla
                        var columnasTabHistorialPagos= [null,{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "20%" }];
                        cargarTabla.init("tablaHistorialPagos",columnasTabHistorialPagos,[1, "asc"],200,true);

                        Swal.fire("Pago Registrado","Fecha de pago registrada correctamente.","success");

                    }else{
                        mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
                    }
                }
            });  
        }else{
            Swal.fire("Falta la fecha de pago","Debes completar la fecha del pago para este registro.","warning");
        }
	 	
	}else{
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaHistorialPagos").hide();
	}
}

function comprobarEdadIntroducida(elemento){
	var edad=$("#"+elemento.id).val();
	if(edad>3 && edad<100){
	   //correcto
	}else{
		$("#"+elemento.id).val("3");
		mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
	}
}


//filtrar historial puertas safey
function filtrarHistorialPuertasSafeyAcceso(acceso){
	var fechaIni=$("#fechaIniHistorialPuertas").val();
    var fechaFin=$("#fechaFinHistorialPuertas").val();
	var puerta=$("#puertaHistorialSafey option:selected").val();
	//$('#cargando').show();
    mostrarRecargarTabla("tablaHistorialPuertas");
    
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "152", 'fechaIni': fechaIni, 'fechaFin': fechaFin, "acceso": acceso, "puerta": puerta },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data!="n"){
				//Swal.fire('Correcto','Ok.','success');
				
			   	$("#tablaHistorialPuertas").html(data);
                
               	var columnasTabHistorial= [null,{ "width": "15%" },{ "width": "15%" },{ "width": "20%" },{ "width": "15%" },{ "width": "15%" },{ "width": "15%" },{ "width": "10%" }];
				cargarTabla.init("tablaHistorialPuertas",columnasTabHistorial,[1, "asc"],200,true);
			}
		}
	});
}

//enviar mail con accesos safey credenciales
function enviarMailSuscripcionPagadaAcceso(idAcceso,idLin){
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "153", "idAcceso": idAcceso, "idLin": idLin},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				Swal.fire('Correcto','Se ha enviado el email con los accesos.','success');
			}else{
				Swal.fire('Error','Error no se ha podido enviar el email.','error');
			}
		}
	});
}

//filtro usuarios safey confuiguracion de credenciales
function filtrarUsuariosSafeyConfCredenciales(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaCredencialesPinSafey");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "154", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesPinSafey").html(data);
				
                var columnasTablaPin= [null,{ "width": "8%" },{ "width": "8%" },{ "width": "10%" },{ "width": "15%" },{ "width": "26%" },{ "width": "27%" },{ "width": "6%" }];
				cargarTabla.init("tablaCredencialesPinSafey",columnasTablaPin,[0, "asc"],25,true);
				
				//location.href="index.php?s=19";
				
			}
		}
	});
}


//editar tarifa nodo
function editarTarifaNodo(id,idNodo){
	$("#cargandotablaConfiguracionEconomica").show();
	
	let tipoServicio=$("#tipoServicioTarifa"+id+" option:selected").val();
	let precio=$("#precioTarifa"+id).val();
	let tipoReserva=$("#tipoReservaTarifa"+id+" option:selected").val();
	let activo=$("#activoTarifa"+id+" option:selected").val();
	let urlPagoTarifa=$("#urlPagoTarifa"+id).val();
	var descripcion=$("#descripcionTarifa"+id).val();

	if(id>0 && idNodo>0){
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "155", "id": id, "idNodo": idNodo, "tipoServicio": tipoServicio, "precio": precio, "tipoReserva": tipoReserva, "activo": activo, "urlPagoTarifa": urlPagoTarifa, "descripcion": descripcion},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaConfiguracionEconomica").html(data);

					var columnasTabConfiguracionEconomica= [null,{ "width": "18%" },{ "width": "13%" },{ "width": "10%" },{ "width": "15%" },{ "width": "19%" },{ "width": "10%" },{ "width": "15%" }];
                    cargarTabla.init("tablaConfiguracionEconomica",columnasTabConfiguracionEconomica,[0, "asc"],50,true);

					
				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaConfiguracionEconomica").hide();
			}
		});  
	}else{
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaConfiguracionCodigosPromo").hide();
	}
}

//borrar tarifa nodo
function borraTarifaNodo(id,idNodo){
	$("#cargandotablaConfiguracionEconomica").show();
	
	if(id>0 && idNodo>0){
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "156", "id": id, "idNodo": idNodo},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaConfiguracionEconomica").html(data);

					//tabla configuracion economica
                    var columnasTabConfiguracionEconomica= [null,{ "width": "18%" },{ "width": "13%" },{ "width": "10%" },{ "width": "15%" },{ "width": "19%" },{ "width": "10%" },{ "width": "15%" }];
                    cargarTabla.init("tablaConfiguracionEconomica",columnasTabConfiguracionEconomica,[0, "asc"],50,true);

				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaConfiguracionEconomica").hide();
			}
		});  
	}else{
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaConfiguracionEconomica").hide();
	}
}

//añadir nueva tarifa nodo
function anadirTarifaNodo(nodo){
	$("#cargandotablaConfiguracionEconomica").show();
	var tipoServicioTarifa=$("#tipoServicioTarifa option:selected").val();
	var precioTarifa=$("#precioTarifa").val();
	var tipoReservaTarifa=$("#tipoReservaTarifa option:selected").val();
	var descripcion=$("#descripcionTarifa").val();
	
	if(nodo>0 && precioTarifa>0){
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "157", "nodo": nodo, "tipoServicioTarifa": tipoServicioTarifa, "precioTarifa": precioTarifa, "tipoReservaTarifa": tipoReservaTarifa, "descripcion": descripcion},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#tablaConfiguracionEconomica").html(data);

					//tabla configuracion economica
                   var columnasTabConfiguracionEconomica= [null,{ "width": "18%" },{ "width": "13%" },{ "width": "10%" },{ "width": "15%" },{ "width": "19%" },{ "width": "10%" },{ "width": "15%" }];
                    cargarTabla.init("tablaConfiguracionEconomica",columnasTabConfiguracionEconomica,[0, "asc"],50,true);

					//limpiar
					$("#precioTarifa").val("0");
				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaConfiguracionEconomica").hide();
			}
		});  
	}else{
		Swal.fire('Error','Error no, intentalo en otro momento.','error');
		$("#cargandotablaConfiguracionEconomica").hide();
	}
}

//recargar desplegable de servicio con el nodo seleccionado
function recargarServicioNodo(elemento,idAcceso){
	$("#cargandotablaHistorialPagos").show();
	var nodo=$("#"+elemento.id+" option:selected").val();
	
	if(nodo>0){
	 	$.ajax({
			url : 'adminajax.php',
			data : { 'op': "158", "nodo": nodo, "idAcceso": idAcceso},
			type : 'POST',
			success : function(data){
				if(data!="n"){
					$("#divTipoServicioPago").html(data);

					//tabla
					/*var columnasTabHistorialPagos= [null,{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "20%" }];
					cargarTabla.init("tablaHistorialPagos",columnasTabHistorialPagos,[1, "asc"],25,true);*/

				}else{
					mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
				}
				$("#cargandotablaHistorialPagos").hide();
			}
		});  
	}else{
		Swal.fire('Error','Error, intentalo en otro momento.','error');
		$("#cargandotablaHistorialPagos").hide();
	}
}

//filtrar historial pagos safey nodo
function filtrarHistorialPagosSafeyNodo(n){
	var fechaIni=$("#fechaIniHistorialPagos").val();
    var fechaFin=$("#fechaFinHistorialPagos").val();
	
	//$('#cargando').show();
    mostrarRecargarTabla("tablaHistorialPagosNodo");
    
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "159", 'fechaIni': fechaIni, 'fechaFin': fechaFin, "n": n},
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data!="n"){
				//Swal.fire('Correcto','Ok.','success');
				
			   	$("#tablaHistorialPagosNodo").html(data);
                
               	//tabla historial pagos
        		var columnasTabHistorialPagosNodo= [null,{ "width": "16%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "12%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" }];
        		cargarTabla.init("tablaHistorialPagosNodo",columnasTabHistorialPagosNodo,[1, "asc"],200,true);
			}
		}
	});
}

//excel descargar historial pago acceso safey
function historialPagoAccesoSafey(idAcceso){
	$('#cargando').show();
	
	var fechaIni=""/*$("#fechaIniHistorialPuertas").val()*/;
    var fechaFin=""/*$("#fechaFinHistorialPuertas").val()*/;
	var puerta=""/*$("#puertaHistorialSafey").val()*/;
	var nodo=""/*$("#puertaHistorialSafey").val()*/;
	if(idAcceso>0){
		$.ajax({
		url : 'adminajax.php',
		data : { "op": "161", "fechaIni": fechaIni, "fechaFin": fechaFin, "puerta": puerta , "nodo": nodo , "idAcceso": idAcceso },
		type : 'POST',
			success : function(data){
				$('#cargando').hide();
				if(data=="n"){
					mostrarToastFire("Problemas al realizar la acción.","error");
				}else{
					cargaLocation('generaXLS.php?o=6');
				}
			}
		});
	}
}