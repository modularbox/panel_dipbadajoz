//borrar nodo contador
function borraNodoContador(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "6", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=5";
		}
	});
}

//mostrar input relacionado ficha contadores
function mostrarContadorInputRel(elemento){
	var pulsado=$("#tipo option:selected").val();
	if(pulsado==1){
		$('#divInputContadorRel').css("display","none");
		$('#divInputDepositoRel').css("display","block");
	}else if(pulsado==2 || pulsado==3){
		$('#divInputDepositoRel').css("display","none");
		$('#divInputContadorRel').css("display","block");
	}
}

//aplicar filtro historial
function filtrarHistorialLecturas(contador){
	var fecha=$("#fechaHistorialLectura").val();
	//$('#cargando').show();
    mostrarRecargarTabla("tablaLecturasContador");
    
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "8", 'fecha': fecha, "contador": contador },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			if(data!="n"){
			   $("#tablaLecturasContador").html(data);
                
               var columnas= [null,{ "width": "30%" },{ "width": "30%" },{ "width": "30%" },{ "width": "10%" }];
               cargarTabla.init("tablaLecturasContador",columnas,[1, "asc"],50,true);
			}
		}
	});
}

//filtro usuarios contadores
function filtrarUsuariosContador(elemento){
	var u=$("#"+elemento.id+" option:selected").val();
    mostrarRecargarTabla("tablaContadoresMultiwater");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "9", 'u': u },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaContadoresMultiwater").html(data);
                var columnasTabHistorial= [null,{ "width": "10%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" }];
				cargarTabla.init("tablaContadoresMultiwater",columnasTabHistorial,[1, "asc"],50,true);
			}
		}
	});
}

//filtro estado contadores
function filtrarEstadoContador(elemento){
	var e=$("#"+elemento.id+" option:selected").val();
	mostrarRecargarTabla("tablaContadoresMultiwater");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "10", 'e': e },
		type : 'POST',
		success : function(data){
			if(data!="n"){
                $("#tablaContadoresMultiwater").html(data);
                var columnas= [null,{ "width": "10%" },{ "width": "18%" },{ "width": "17%" },{ "width": "15%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" },{ "width": "10%" }];
                cargarTabla.init("tablaContadoresMultiwater",columnas,[1, "asc"],50,true);
			}
		}
	});
}

function cargarGraficaConsumosContadores(idGrafica,contador,formato){
	$("#cargandoGraficaLecturasContador").show();
	
	var tipo=$("#selectGraficaLecturas option:selected").val();
	var formato=$("#selectGraficaLecturasFormato option:selected").val();
	var fechaGrafica=$("#fechaGraficaContador").val();
	
	$.getJSON({//ajax//getJSON
		url : 'adminajax.php',
		data : { 'op': "11", "tipo": tipo, "contador": contador, "formato": formato , "fecha": fechaGrafica},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				var texto="";
				if(formato==2){
				   texto="Litros";
				}else{//1
					texto="m3";
				}
               	var barrasSeries= [{name: 'Consumo',data: data[0]}];
				var barrasCategorias=data[1];
				cargaGrafica.init(idGrafica,barrasSeries,barrasCategorias,texto);
			}
			$("#cargandoGraficaLecturasContador").hide();
		}
	});
}

//crear historial contador
function crearHistorialContador(contador){
	$("#cargandotablaLecturasContador").show();
	var metros=$("#metrosh").val();
	var fecha=$("#fechah").val();
	var hora=$("#horah").val();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "12", "contador": contador, "metros": metros, "fecha": fecha, "hora": hora},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaLecturasContador").html(data);
				
				//tabla
        		var columnasTabHistorial= [null,{ "width": "15%" },{ "width": "15%" },{ "width": "30%" },{ "width": "30%" },{ "width": "10%" }];
				cargarTabla.init("tablaLecturasContador",columnasTabHistorial,[1, "asc"],50,true);
				
				//limpiar
				$("#metrosh").val("");
				$("#fechah").val("");
				$("#horah").val("");
			}
			$("#cargandotablaLecturasContador").hide();
		}
	});
}

//borrar historial contador
function borrarHistorialContador(contador,idlin){
	$("#cargandotablaLecturasContador").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "13", "contador": contador, "idlin": idlin},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaLecturasContador").html(data);
				
				//tabla
        		var columnasTabHistorial= [null,{ "width": "15%" },{ "width": "15%" },{ "width": "30%" },{ "width": "30%" },{ "width": "10%" }];
				cargarTabla.init("tablaLecturasContador",columnasTabHistorial,[1, "asc"],50,true);
			}
			$("#cargandotablaLecturasContador").hide();
		}
	});
}

//borrar mail avisos//SIN USO POR AHORA
function borrarEmailContadorAvisos(c,m,tipo){
	$("#cargandotablaAvisosMailUno").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "31", "c": c, "m": m , "tipo": tipo},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaAvisosMailUno").html(data);
				
				var columnasTabAvisosUno= [null,{ "width": "95%" },{ "width": "5%" }];
        		cargarTabla.init("tablaAvisosMailUno",columnasTabAvisosUno,[1, "asc"],50,true);
			}
			$("#cargandotablaAvisosMailUno").hide();
		}
	});
}