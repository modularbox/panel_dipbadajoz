//filtro usuarios pistas de padel
function filtrarUsuariosPistasPadel(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaPistaPadelNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "59", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaPistaPadelNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
				cargarTabla.init("tablaPistaPadelNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//filtro conexion pistas de padel
function filtrarConexionPistasPadel(elemento){
	var e=$("#"+elemento.id+" option:selected").val();
	mostrarRecargarTabla("tablaPistaPadelNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "60", 'e': e },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaPistaPadelNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
                cargarTabla.init("tablaPistaPadelNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//filtrar historial puertas pistas de padel
function filtrarHistorialPuertasPistasPadel(n){
	var fechaIni=$("#fechaIniHistorialPuertas").val();
	var fechaFin=$("#fechaFinHistorialPuertas").val();
	//$('#cargando').show();
    mostrarRecargarTabla("tablaHistorialPuertasPadel");
    
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "61", 'fechaIni': fechaIni, 'fechaFin': fechaFin, "n": n },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data!="n"){
				//Swal.fire('Correcto','Ok.','success');
				
			   	$("#tablaHistorialPuertasPadel").html(data);
                
               	var columnasTabHistorial= [null,{ "width": "9%" },{ "width": "15%" },{ "width": "9%" },{ "width": "9%" },{ "width": "9%" },{ "width": "8%" },{ "width": "8%" },{ "width": "8%" },{ "width": "8%" },{ "width": "8%" },{ "width": "8%" },{ "width": "8%" }];
        		cargarTabla.init("tablaHistorialPuertasPadel",columnasTabHistorial,[5, "asc"],50,true);
			}
		}
	});
}

//borrar pista de padel nodo
function borraPistaPadelNodo(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "62", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=21";
		}
	});
}

//ir al anotar el historial y abrir
function abrirPuertaWebPistaPadelHistorial(p,n,tipo){
	//historial
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "63", "n": n, "p": p, "tipo": tipo},
		type : 'POST',
		success : function(data){
			
			//accion
			var texto="";
			var titulo="";
			var clase="";
			if(tipo==1){
				var texto="Abriendo puertas.";
				var titulo="Correctamente";
				var clase="success";
			}else if(tipo==2){
				var texto="Abriendo puertas.";
				var titulo="Correctamente";
				var clase="success";
			}else if(tipo==3){
				/*var texto="Abriendo puertas.";
				var titulo="Correctamente";
				var clase="success";*/
			}else if(tipo==4){
				var texto="Cerrando puertas.";
				var titulo="Correctamente";
				var clase="error";
			}else if(tipo==5){
				var texto="Abriendo puertas modo Mantenimiento.";
				var titulo="Correctamente";
				var clase="success";
			}
			
			if(data=="s"){
				Swal.fire(titulo,texto,clase);
			}else if(data=="d"){
				Swal.fire('Error',"Aún hay una acción pendiente de realizar, intentalo pasados 2 minutos.",'error');
			}else{
				Swal.fire('Error',texto,'error');
			}
		}
	});
}

function historialPuertaPistaPadelExcel(idnodo){
	$('#cargando').show();
	
	var fechaIni=$("#fechaIniHistorialPuertas").val();
	var fechaFin=$("#fechaFinHistorialPuertas").val();
	
	$.ajax({
		url : 'adminajax.php',
		data : { "op": "115", 'fechaIni': fechaIni, 'fechaFin': fechaFin, "idnodo": idnodo },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data=="n"){
				mostrarToastFire("Problemas al realizar la acción.","error");
			}else{
				cargaLocation('generaXLS.php?o=3');
			}
		}
	});
}