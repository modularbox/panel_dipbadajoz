//filtro usuarios pistas de padel
function filtrarUsuariosParques(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaParquesNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "64", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaParquesNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
				cargarTabla.init("tablaParquesNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//filtro conexion pistas de padel
function filtrarConexionParques(elemento){
	var e=$("#"+elemento.id+" option:selected").val();
	mostrarRecargarTabla("tablaParquesNodos");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "65", 'e': e },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaParquesNodos").html(data);
				var columnasTab= [null,{ "width": "20%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "30%" }];
				cargarTabla.init("tablaParquesNodos",columnasTab,[1, "asc"],50,true);
			}
		}
	});
}

//filtrar historial puertas parques
function filtrarHistorialPuertasParque(n){
	var fechaIni=$("#fechaIniHistorialPuertas").val();
	var fechaFin=$("#fechaFinHistorialPuertas").val();
	//$('#cargando').show();
    mostrarRecargarTabla("tablaHistorialParque");
    
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "66", 'fechaIni': fechaIni, 'fechaFin': fechaFin, "n": n },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data!="n"){
				//Swal.fire('Correcto','Ok.','success');
				
			   	$("#tablaHistorialParque").html(data);
                
               	var columnasTabHistorial= [null,{ "width": "15%" },{ "width": "15%" },{ "width": "15%" },{ "width": "15%" },{ "width": "15%" },{ "width": "15%" },{ "width": "15%" }];
				cargarTabla.init("tablaHistorialParque",columnasTabHistorial,[5, "asc"],50,true);
			}
		}
	});
}

//ir al anotar el historial y abrir
function abrirCerrarPuertaWebParqueHistorial(p,n,tipo){
	//historial
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "67", "n": n, "p": p, "tipo": tipo},
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

//borrar parques nodo
function borraParqueNodo(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "68", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=23";
		}
	});
}