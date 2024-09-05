// JavaScript Document

/*$(document).ready(function(){

});*/

/**********START gestionar elementos pulsados o no pulsados**********/
// Creamos un array vacio
var ElementosClick = new Array();
// Capturamos el click y lo pasamos a una funcion
document.onclick = captura_click;

function captura_click(e){
	// Funcion para capturar el click del raton
	var HaHechoClick;
	if(e == null){
		// Si hac click un elemento, lo leemos
		HaHechoClick = event.srcElement;
		//console.log("Contenido sobre lo que ha hecho click 1: "+HaHechoClick.id);
	}else{
		// Si ha hecho click sobre un destino, lo leemos
		HaHechoClick = e.target;
		//console.log("Contenido sobre lo que ha hecho click 2: "+HaHechoClick.id);
	}
	
	//console.log(e.srcElement.className);
	if(HaHechoClick.id!="buscadorTipoT4"){//esconder buscador tipos wizard
		if($("#buscadorTipoT4")){
			$("#buscadorTipoT4").css("display", "none");
		}
	}
}
/**********END gestionar elementos pulsados o no pulsados**********/

function pulsarIntro(e,obj){//ejecuta un boton concreto por su ID al pulsar intro en ese input text. Añadir en el input: onkeypress = "pulsarIntro(event,'id')"
	tecla=(document.all) ? e.keyCode : e.which;
  	if(tecla==13)
  		document.getElementById(obj).onclick();
}

function ejecutaInput(obj){
	var oTest=document.getElementById(obj);
	oTest.click();
}

function reduceText(textarea,n){//reduce un textarea al salir de el segun la cifra N. Añadir en el html: onblur='reduceText(this,<N>)'
	if(textarea.value.length>n){
		textarea.value=textarea.value.substring(0,n);
		swal("Aviso","Se ha reducido el texto introducido a "+n+" caracteres","warning");
	}
}

function aleatorio(inferior,superior){//calcula un numero aleatorio
    numPosibilidades = superior - inferior;
    aleat = Math.random() * numPosibilidades;
    aleat = Math.floor(aleat);
    return parseInt(inferior) + aleat;
}

function quitaComillas(texto){//quita las comillas dobles de un texto por simples TAMBIEN REMPLAZA EL MAS
	var texto2="";
	var texto3="";
	texto2=texto.replace(/"/g,"'");
	texto3=texto2.replace("+","%2B");
	return texto3;
}
function quitaComillasDB(texto){//quita las comillas dobles de un texto por simples TAMBIEN REMPLAZA EL MAS
	var texto2="";
	texto2=texto.replace(/"/g,"'");
	return texto2;
}

function ejecutaForm(nombreform){//ejecuta formulario
	document.getElementById(nombreform).submit();
}


function cargaLocation(direccion){
	location.href=direccion;
}

function prepareTargetBlank(){
	var className = 'external';
	var as = document.getElementsByTagName('a');
	for(i=0;i<as.length;i++){
		var a = as[i];
		r=new RegExp("(^| )"+className+"($| )");
		if(r.test(a.className)){
			a.onclick = function(){
				window.open(this.href);
				return false;
			}
		}
	}
}
window.onload = prepareTargetBlank;

function cargaPagina(op,pagina){
	cargaLocation("index.php?s="+op+"&pg="+pagina);
}

function selecciona_value(objInput){
	$(objInput).select();
}

//MENSAJES DE CONFIRMACION:
//*************************
function confirmacion(tipo,titulo,texto,funcion,campo1,campo2,campo3){	
	Swal.fire({
		title: titulo,
		text: texto,
		type: tipo,
		showCancelButton: true,
		confirmButtonColor: '#1bc5bd',/*3085d6*/
		cancelButtonColor: '#d33',
		confirmButtonText: 'Sí (Aceptar)',
		cancelButtonText: "No (Cancelar)",
	}).then((result) => {
		if (result.value) {
			switch(funcion){
				case 1:
					borraEmpresa(campo1);
				break;
				case 2:
					borraNodoMultiwater(campo1);
				break;
				case 3:
					borraNodoContador(campo1);
				break;
				case 4:
					borrarHistorialContador(campo1,campo2);
				break;
				case 5:
					borrarUsuarioCliente(campo1,campo2);
				break;
				case 6:
					borraNodoLuces(campo1,campo2);
				break;
				case 7:
					borraProgramaLuces(campo1,campo2);
				break;
                case 8:
					borrarLineaConfiguracionLuces(campo1,campo2);
				break;
				case 9:
					borrarNodoHorarioLuces(campo1,campo2);
				break;
				case 10:
					borraHorarioLuces(campo1);
				break;
				case 11:
					borrarNodoHorarioLuces(campo1,campo2);
				break;
				case 12:
					//borrarEmailContadorAvisos(campo1,campo2,campo2);//sin uso por ahora
				break;
				case 13:
					borrarPuertasNodosSafey(campo1,campo2);
				break;
				case 14:
					borraSafeyNodo(campo1);
				break;
				case 15:
					borraSafeyAcceso(campo1);
				break;
				case 16:
					borraSafeyPinCredenciales(campo1);
				break;
				case 17:
					borraSafeyLlaveCredenciales(campo1);
				break;
				case 18:
					borraSafeyMandoCredenciales(campo1);
				break;
				case 20:
					borraNodosVinculadosSafey(campo1,campo2);
				break;
				case 21:
					borraPistaPadelNodo(campo1);
				break;
				case 22:
					borraParqueNodo(campo1);
				break;
				case 23:
					borraAlmacenPinCredenciales(campo1);
				break;
				case 24:
					borraAlmacenLlaveCredenciales(campo1);
				break;
				case 25:
					borraCampanasNodo(campo1);
				break;
				case 26:
					borraCampanasProgramas(campo1);
				break;
                case 27:
					borrarLineaConfiguracionCampanas(campo1,campo2);
				break;
                case 28:
					borrarLineaLucesCampanas(campo1,campo2);
				break;
				case 29:
					borraAutomatizacionProgramas(campo1);
				break;
                case 30:
					borrarLineaSalidasAutomatizacion(campo1,campo2,campo3);
				break;
                case 31:
                    borraAudioNodo(campo1);    
                break;
				case 32:
					borrarCamarasNodosVideovigilancia(campo1);
				break;
				case 33:
					borraSafeyPinAcceso(campo1,campo2);
				break;
				case 34:
					borraMetodoPago(campo1,campo2);
				break;	
				case 35:
					borraCodigoPromocional(campo1,campo2);
				break;	
				case 36:
					borraPago(campo1,campo2);
				break;		
				case 37:
					borraFicheroAudio(campo1,campo2);
				break;
				case 38:
					borrarHistorialColaAudios(campo1,campo2,campo3);
				break;
				case 39:
					borrarTipoFocoDmxLuces(campo1);
				break;
				case 40:
					borrarTipoProgramaPredefinidoLuz(campo1);
				break;
				case 41:
					borraTarifaNodo(campo1,campo2);
				break;
				case 42:
					pagarLineaAccesoSafey(campo1,campo2);
				break;	
				default:
					Swal.fire(
						'Error',
						'No se ha cargado ninguna opción',
						'error'
					);
			}
		}
	});
}
//FIN MENSAJES DE CONFIRMACION
//****************************

function abreFecha(){
	if(document.getElementById("filtromovilmostrado").value=="n"){
		document.getElementById("filtromovil").style.display="block";
		$('#filtromovil').animate({
			opacity: 1,
		}, 300, function() {
			document.getElementById("filtromovilmostrado").value="s";
			if(document.getElementById("buscadormovilmostrado")){
				if(document.getElementById("buscadormovilmostrado").value=="s"){
					abreBusqueda();
				}
			}
		});
	}else{
		$('#filtromovil').animate({
			opacity: 0,
		}, 300, function() {
			document.getElementById("filtromovil").style.display="none";
			document.getElementById("filtromovilmostrado").value="n";
		});
	}
}

function cargaAcciones(actual,todas){
	var cajaabierta=document.getElementById("cajaabierta").value;
	if(cajaabierta!=actual){
		$('#cajacciones'+cajaabierta).animate({
			height: 0,
		}, 100, function() {
			document.getElementById("cajacciones"+cajaabierta).style.display="none";
			document.getElementById("cajaabierta").value=actual;

			document.getElementById("cajacciones"+actual).style.display="block";
			var el = $('#cajacciones'+actual),
			curHeight = el.height(),
			autoHeight = el.css('height', 'auto').height();
			el.height(curHeight).animate({height: autoHeight}, 200);
		});
	}else{
		$('#cajacciones'+cajaabierta).animate({
			height: 0,
		}, 100, function() {
			document.getElementById("cajacciones"+cajaabierta).style.display="none";
			document.getElementById("cajaabierta").value=-1;
		});
	}
}

function buscaCampo(op,op2,minimo,cadena){
	if (cadena.length>=minimo || cadena.length==0){
		var datos="";
		datos="op="+op+"&buscar="+escape(quitaComillas(cadena))+"&long="+cadena.length+"&op2="+op2;
		oXML = AJAXCrearObjeto();
		oXML.open('POST', 'adminajax.php' ,true);
		oXML.onreadystatechange = function(){
			if (oXML.readyState == 1){
			}else{
				if (oXML.readyState == 4){
					if(oXML.responseText=="#nohay"){
						document.getElementById("resbuscador").innerHTML="";
						document.getElementById("resbuscador").style.display="none";
						//document.getElementById("idbuscado").value=0;
//						if(cadena.length==0){
//							seleccionaBuscado(op2,"",0);
//						}
					}else{
						document.getElementById("resbuscador").innerHTML=oXML.responseText;
						document.getElementById("resbuscador").style.display="block";
					}
				}
			}
		}
		oXML.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		oXML.send(datos);
	}else{
		document.getElementById("resbuscador").innerHTML="";
		document.getElementById("resbuscador").style.display="none";
		//document.getElementById("idbuscado").value=0;
	}
}
function seleccionaBuscado(op,id){
	//document.getElementById("cn").value=id;
//	document.getElementById("idbuscado").value=id;
//	document.getElementById("resbuscador").innerHTML="";
//	document.getElementById("resbuscador").style.display="none";
	cargaLocation("index.php?s=3&a="+id);
}
function salirCampo(){
	document.getElementById("resbuscador").style.display="none";
}

function quitaFiltros(seccion){
	$('#cargando').show();
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "11", 'seccion': seccion },
		type : 'POST',
		success : function(data){
			$('#cargando').hide();
			location.reload();
		}
	});
}

function campoModificado(input){
	input.style.backgroundColor="#FFEAD9";
}

function seleccionaCheck(pos){
	if(parseInt(document.getElementById("valorcheck"+pos).value)==0){
		document.getElementById("check"+pos).className="tdchecks";
		document.getElementById("valorcheck"+pos).value=document.getElementById("idcheck"+pos).value;
	}else{
		document.getElementById("check"+pos).className="tdcheckn";
		document.getElementById("valorcheck"+pos).value=0;
	}
}
function seleccionaCheckT(check){
	if(document.getElementById("todoscheck").value=="n"){
		document.getElementById("todoscheck").value="s";
		check.className="tablacabeceracheck tablacabecerachecks";
		for(var pos=0;pos<parseInt(document.getElementById("regtotales").value);pos++){
			document.getElementById("check"+pos).className="tdchecks";
			document.getElementById("valorcheck"+pos).value=document.getElementById("idcheck"+pos).value;
		}
	}else{
		document.getElementById("todoscheck").value="n";
		check.className="tablacabeceracheck tablacabeceracheckn";
		for(var pos=0;pos<parseInt(document.getElementById("regtotales").value);pos++){
			document.getElementById("check"+pos).className="tdcheckn";
			document.getElementById("valorcheck"+pos).value=0;
		}
	}
}
function checkSeleccionados(){
	var cadena="0";
	for(var pos=0;pos<parseInt(document.getElementById("regtotales").value);pos++){
		if(parseInt(document.getElementById("valorcheck"+pos).value)>0){
			cadena+="-"+document.getElementById("valorcheck"+pos).value;
		}
	}
	return cadena;
}

//mostrar ocultar contrasena
function cambiarVisbilidadPass(idcampo,idboton){
	boton = document.getElementById(idboton);
	if(boton.classList.contains('boton30ver')){
		boton.classList.remove('boton30ver');
		boton.classList.add('boton30ocultar');

		document.getElementById(idcampo).type='number';
	}else{
		boton.classList.add('boton30ver');
		boton.classList.remove('boton30ocultar');

		document.getElementById(idcampo).type='password';
	}
}

/*---------------------- start toast swal----------------------------*****/
//fire
function mostrarToastFire(mensaje,tipo){
	$(function(){
		const Toast = Swal.mixin({
			toast: true,
			position: 'top-end',
			showConfirmButton: false,
  			timer: 3000
		});

		Toast.fire({
			icon: tipo,
			title: mensaje
		});
	});
}

//fire swal central
function mostrarSwalFire(mensaje,titulo,tipo,botones,url,opt){
	Swal.fire({
	  type: tipo,
	  title: titulo,
	  showConfirmButton: botones,
	  text: mensaje
	}).then((result) => {
		if (result.value) {
			if(opt==1){
			   cargaLocation(url);
			}else if(opt==2){
			   cargaLocation(url);
			}
		}
	});
	if(opt==1){
	   	setTimeout(function(){ 
			cargaLocation(url);
		}, 3000);
	}
}

/*---------------------- end toast----------------------------*****/

function deslogea(){
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "1"},
		type : 'POST',
		success : function(data){
			cargaLocation("index.php");
		}
	});
}
///************* - otherfunction ...---*/

//start motrar ocultar contasena*********-------------------
function mostrarOcultarPass(idcampo){
	if($("#"+idcampo).attr('type')=="password"){
	   	$("#"+idcampo).prop('type', 'text');
		/*if($("#btn"+idcampo)){
		   $("#btn"+idcampo).text('Ocultar');
		}*/
	}else if($("#"+idcampo).attr('type')=="text"){
		$("#"+idcampo).prop('type', 'password');
		/*if($("#btn"+idcampo)){
		   $("#btn"+idcampo).text('Mostrar');
		}*/
	}
}
//end motrar ocultar contasena*********-------------------
