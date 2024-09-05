//START TABLAS****///
function destruirTablaDinamica(id){
	$('#'+id+'').DataTable().destroy();
}

var cargarTabla = function() {
 
	var IniciarTabla = function(idTabla,columnas,ordenacion,registrosmostrar,ordenarTabla) {
		var tabla = $('#'+idTabla);
		
		// begin first table
		tabla.DataTable({
            
            responsive: true,
			// DOM Layout settings
			dom: `<'row'<'col-sm-12'tr>>
			<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
			lengthMenu: [5, 10, 25, 50,100,150,200,350,500,700,900,1100,1300,1500,2000,2500],
			pageLength: registrosmostrar,
			stateSave: true,
			ordering: ordenarTabla,/*true/false*/
			language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 a 0 de 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados"
            },
            "columns": columnas,
            order: [ordenacion],/*[ 1, "asc" ]*/
            columnDefs: [ {
				className: 'control',
				orderable: false,
				targets:   0
			} ]
		});
	};

	return {

		//main function to initiate the module
		init: function(idTabla,columnas,ordenacion,registrosmostrar,ordenarTabla) {
            destruirTablaDinamica(idTabla);
			IniciarTabla(idTabla,columnas,ordenacion,registrosmostrar,ordenarTabla);
            
            if($('#buscador'+idTabla)){
                $('#buscador'+idTabla).keyup(function(){
        
                    $('#'+idTabla).DataTable().search(
                        $('#buscador'+idTabla).val()
                    ).draw();
                });
            }
            
            $("#cargando"+idTabla).hide();
            //$("#cargando"+idTabla).fadeOut(500);
            $("#"+idTabla).css("visibility","visible");
		},
	};
}();

function mostrarRecargarTabla(tabla){
    $("#"+tabla).css("visibility", "hidden");
    $("#cargando"+tabla).show();
}
//END TABLAS****///

//borrar usuario
function borraEmpresa(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "3", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=1";
		}
	});
}

//encender apagar nodo
function onOffnodoMultiwater(id,estado){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "4", 'id': id, 'estado': estado },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=3";
		}
	});
}

//borrar nodo multiwater
function borraNodoMultiwater(id){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "5", 'id': id },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.href="index.php?s=3";
		}
	});
}

//crear usuario seccion clientes
function crearUsuarioCliente(cliente){
	$("#cargandotablaAccesosUsuarios").show();
	var nombre=$("#nombreac").val();
	var telefono=$("#telefonoac").val();
	var email=$("#emailac").val();
	var contrasena=$("#contrasenaac").val();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "14", "cliente": cliente, "nombre": nombre, "telefono": telefono, "email": email, "contrasena": contrasena},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaAccesosUsuarios").html(data);
				
				//tabla
        		var columnasTabUsuarios= [null,{ "width": "20%" },{ "width": "15%" },{ "width": "30%" },{ "width": "30%" },{ "width": "5%" }];
        		cargarTabla.init("tablaAccesosUsuarios",columnasTabUsuarios,[1, "asc"],25,true);
				
				//limpiar
				$("#nombreac").val("");
				$("#telefonoac").val("");
				$("#emailac").val("");
				$("#contrasenaac").val("");
			}else{
				mostrarSwalFire("Los datos introducidos no son correctos.","Intentalo de nuevo","warning");
			}
			$("#cargandotablaAccesosUsuarios").hide();
		}
	});
}

function borrarUsuarioCliente(cliente,lin){
	$("#cargandotablaAccesosUsuarios").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "15", "cliente": cliente, "lin": lin},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaAccesosUsuarios").html(data);
				
				//tabla
        		var columnasTabUsuarios= [null,{ "width": "20%" },{ "width": "15%" },{ "width": "30%" },{ "width": "30%" },{ "width": "5%" }];
        		cargarTabla.init("tablaAccesosUsuarios",columnasTabUsuarios,[1, "asc"],50,true);
			}
			$("#cargandotablaAccesosUsuarios").hide();
		}
	});
}

function editarUsuarioCliente(cliente,lin){
	$("#cargandotablaAccesosUsuarios").show();
	/*var nombre=$("#nombreac"+lin).val();
	var telefono=$("#telefonoac"+lin).val();
	var email=$("#emailac"+lin).val();
	var contrasena=$("#contrasenaac"+lin).val();*/
	
	var nombre=$("[id='nombreac"+lin+"']").last().val()
	var telefono=$("[id='telefonoac"+lin+"']").last().val()
	var email=$("[id='emailac"+lin+"']").last().val()
	var contrasena=$("[id='contrasenaac"+lin+"']").last().val()
	
	
	/*ejemplos todos id
	$("[id='telefonoac45']").last().val()
	$("[id='telefonoac45']")[0].value*/
	
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "16", "cliente": cliente, "nombre": nombre, "telefono": telefono, "email": email, "contrasena": contrasena, "lin":lin},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaAccesosUsuarios").html(data);
				
				//tabla
        		var columnasTabUsuarios= [null,{ "width": "20%" },{ "width": "15%" },{ "width": "30%" },{ "width": "30%" },{ "width": "5%" }];
        		cargarTabla.init("tablaAccesosUsuarios",columnasTabUsuarios,[1, "asc"],50,true);
			}
			$("#cargandotablaAccesosUsuarios").hide();
		}
	});
}

//anadir pin ALMACEN
function crearCredencialPinAlmacen(){
	var pin=$("#pinCredencialAlmacen").val();
	var serie=$("#pinserieCredencialAlmacen").val();
	var serial=$("#pinSerialCredencialAlmacen").val();
	
    mostrarRecargarTabla("tablaCredencialesPinAlmacen");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "70", 'pin': pin, 'serie': serie, 'serial': serial },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesPinAlmacen").html(data);
				Swal.fire('Correcto','Ok.','success');
			}else{
				Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
			}
			var columnasTablaPin= [null,{ "width": "10%" },{ "width": "28.25%" },{ "width": "28.50" },{ "width": "28.25%" },{ "width": "5%" }];
        	cargarTabla.init("tablaCredencialesPinAlmacen",columnasTablaPin,[0, "asc"],25,true);
		}
	});
}

//borrar PIN almacen credencial
function borraAlmacenPinCredenciales(id){
	$("#borraAlmacenPinCredenciales").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "71", "id": id},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesPinAlmacen").html(data);
				
				Swal.fire('Correcto','Ok.','success');
				
				var columnasTablaPin= [null,{ "width": "10%" },{ "width": "28.25%" },{ "width": "28.50" },{ "width": "28.25%" },{ "width": "5%" }];
        		cargarTabla.init("tablaCredencialesPinAlmacen",columnasTablaPin,[0, "asc"],25,true);
			}
		}
	});
}

//editar pin ALMACEN
function editarCredencialPinAlmacen(idlin){
	var pin=$("#pinCredencialAlmacen"+idlin+"").val();
	var serie=$("#pinSerieCredencialAlmacen"+idlin+"").val();
	var serial=$("#pinSerialCredencialAlmacen"+idlin+"").val();
	
    mostrarRecargarTabla("tablaCredencialesPinAlmacen");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "72", 'pin': pin, 'serie': serie, 'serial': serial, 'idlin': idlin },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesPinAlmacen").html(data);
				Swal.fire('Correcto','Ok.','success');
			}else{
				Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
			}
			var columnasTablaPin= [null,{ "width": "10%" },{ "width": "28.25%" },{ "width": "28.50" },{ "width": "28.25%" },{ "width": "5%" }];
        	cargarTabla.init("tablaCredencialesPinAlmacen",columnasTablaPin,[0, "asc"],25,true);
		}
	});
}

//anadir llave ALMACEN
function crearCredencialLlaveAlmacen(){
	var serie=$("#llaveSerieCredencialAlmacen").val();
	var serial=$("#llaveSerialCredencialAlmacen").val();
	var tipo=$("#llaveTipoCredencialAlmacen").val();
	var frecuencia=$("#llaveFrecuenciaCredencialAlmacen").val();
	var descripcion=$("#llaveDescripcionCredencialAlmacen").val();
	
    mostrarRecargarTabla("tablaCredencialesLlaveAlmacen");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "73", 'frecuencia': frecuencia, 'tipo': tipo, 'serie': serie, 'serial': serial, 'descripcion': descripcion },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesLlaveAlmacen").html(data);
				
				$("#llaveSerieCredencialAlmacen").val("");
				$("#llaveSerialCredencialAlmacen").val("");
				
				Swal.fire('Correcto','Ok.','success');
			}else{
				Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
			}
			var columnasTablaLlave= [null,{ "width": "13%" },{ "width": "10%" },{ "width": "13.50%" },{ "width": "25.25%" },{ "width": "10.62%" },{ "width": "7.62%" },{ "width": "2%" },{ "width": "5%" }];
        	cargarTabla.init("tablaCredencialesLlaveAlmacen",columnasTablaLlave,[0, "asc"],25,true);
		}
	});
}

//borrar LLAVE ALMACEN credencial
function borraAlmacenLlaveCredenciales(id){
	$("#cargandotablaCredencialesLlaveAlmacen").show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "74", "id": id},
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesLlaveAlmacen").html(data);
				
				Swal.fire('Correcto','Ok.','success');
				
				var columnasTablaLlave= [null,{ "width": "13%" },{ "width": "10%" },{ "width": "13.50%" },{ "width": "25.25%" },{ "width": "10.62%" },{ "width": "7.62%" },{ "width": "2%" },{ "width": "5%" }];
				cargarTabla.init("tablaCredencialesLlaveAlmacen",columnasTablaLlave,[0, "asc"],25,true);
			}
		}
	});
}

//editar llave ALMACEN
function editarCredencialLlaveAlmacen(idlin){
	var serie=$("#llaveSerieCredencialAlmacen"+idlin+"").val();
	var serial=$("#llaveSerialCredencialAlmacen"+idlin+"").val();
	//var tipo=$("#llaveTipoCredencialAlmacen").val();
	//var frecuencia=$("#llaveFrecuenciaCredencialAlmacen").val();
	var descripcion=$("#llaveDescripcionCredencialAlmacen"+idlin+"").val();
	
    mostrarRecargarTabla("tablaCredencialesLlaveAlmacen");
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "75", 'idlin': idlin, 'serie': serie, 'serial': serial, 'descripcion': descripcion },
		type : 'POST',
		success : function(data){
			if(data!="n"){
				$("#tablaCredencialesLlaveAlmacen").html(data);
				Swal.fire('Correcto','Ok.','success');
			}else{
				 Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
			}
			var columnasTablaLlave= [null,{ "width": "13%" },{ "width": "10%" },{ "width": "13.50%" },{ "width": "25.25%" },{ "width": "10.62%" },{ "width": "7.62%" },{ "width": "2%" },{ "width": "5%" }];
			cargarTabla.init("tablaCredencialesLlaveAlmacen",columnasTablaLlave,[0, "asc"],25,true);
		}
	});
}

//generar pines aleatorios
function generarPinesAleatoriosAlmacen(){
    
    var cantidadPinesAlmacen=$("#cantidadPinesAlmacen").val();
    
	if(cantidadPinesAlmacen>0){
        mostrarRecargarTabla("tablaCredencialesPinAlmacen");
        $.ajax({
            url : 'adminajax.php',
            data : { 'op': "76", cantidadPines:cantidadPinesAlmacen },
            type : 'POST',
            success : function(data){
                if(data!="n"){
                    $("#tablaCredencialesPinAlmacen").html(data);
                    Swal.fire('Correcto','Ok.','success');
                }else{
                    Swal.fire('Error','No se ha podido realizar la acción, revisa los campos.','error');
                }
                var columnasTablaPin= [null,{ "width": "10%" },{ "width": "28.25%" },{ "width": "28.50" },{ "width": "28.25%" },{ "width": "5%" }];
                cargarTabla.init("tablaCredencialesPinAlmacen",columnasTablaPin,[0, "asc"],25,true);
            }
        });
    }else{
        Swal.fire('Error','Introduzca un número mayor de 0 para generar los pines deseados','error');
    }
}