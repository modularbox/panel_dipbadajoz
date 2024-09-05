// JavaScript Document

function pulsarIntro(e,obj){//ejecuta un boton concreto por su ID al pulsar intro en ese input text. Añadir en el input: onkeypress = "pulsarIntro(event,'id')"
	tecla=(document.all) ? e.keyCode : e.which;
  	if(tecla==13)
  		document.getElementById(obj).onclick();
}

function saltarCampo(e,obj){//salta un elemento por su ID al pulsar intro en ese input text. Añadir en el input: onkeypress = "saltarCampo(event,'id')"
	tecla=(document.all) ? e.keyCode : e.which;
  	if(tecla==13)
  		document.getElementById(obj).focus();
}

function quitaComillas(texto){//quita las comillas dobles de un texto por simples TAMBIEN REMPLAZA EL MAS
	var texto2="";
	var texto3="";
	texto2=texto.replace(/"/g,"'");
	texto3=texto2.replace("+","%2B");	
	return texto3;
}

function ejecutaForm(nombreform){//ejecuta formulario
	document.getElementById(nombreform).submit();
}

function compruebaLogin(){
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "2", 'useraplicacion': escape(quitaComillas(document.getElementById("useraplicacion").value)) , 'claveaplicacion': escape(quitaComillas(document.getElementById("claveaplicacion").value)) },
		type : 'POST',
		success : function(data){
			if(data=="s"){
				ejecutaForm("kt_login_signin_form");
			}else{
				mostrarSwalFire("Los datos introducidos no son correctos.","Usuario incorrecto","error");//"Los datos introducidos no son correctos."
			}
		}
	});
}

function recuperaClave(){
	if($("#useraplicacionRecu1").val()!=""){
		
		$.ajax({
			url : 'adminajax.php',
			data : { 'op': "7", 'useraplicacionRecu1': $("#useraplicacionRecu1").val()},
			type : 'POST',
			success : function(data){
				if(data=="s"){
					mostrarSwalFire("Tu contraseña ha sido enviada. La recibirás en tu email en breves momentos","Email enviado","success");
					
				}else if(data=="m"){
					mostrarSwalFire("Has excedido el número de recuperaciones permitidas.","Intentalo en otro momento","error");
					
				}else{
					mostrarSwalFire("Usuario incorrecto","Usuario incorrecto","error");
				}
			}
		});
		
	}else{
		mostrarSwalFire("Los datos introducidos no son correctos.","Usuario incorrecto","error");
	}
}

function obtenerImgInicioLogin(){
	//$(function(){
	$.ajax({
		url : 'adminajax.php',
		data : { 'op': "9998" },
		type : 'POST',
		success : function(data){
			var dataArray= $.parseJSON(data);
			
			$('#pasadorImagen').attr("src", dataArray[0]);//pintar la primera por defecto
			
			//EMPIEZA POR LA POSICIÓN 0 DESPUES DE 7 SEGUNDOS
			var imagen=0;
			window.setInterval(function(){
				$('#pasadorImagen').fadeOut(1500, function() {
					$(this).attr("src", dataArray[imagen]).fadeIn(1500);        
				});
				imagen++; //incrementar la posicion del dataArray
				if (imagen==dataArray.length) imagen=0; //volver a empezar
			},7000);    
		}
	});

	/*
	//ARRAY CON LAS IMAGENES
	var dataArray=new Array();
	dataArray[0]="1.jpg";
	dataArray[1]="2.jpg";
	dataArray[2]="3.jpg";

	//EMPIEZA POR LA POSICIÓN 0 DESPUES DE 7 SEGUNDOS
	var imagen=0;
	window.setInterval(function(){

		$('#pasadorImagen').fadeOut(1500, function() {
			$(this).attr("src", dataArray[imagen]).fadeIn(1500);        
		});

		/*$('#pasadorImagen').fadeOut(1000, function() {
			$(this).css("background-image", "url("+dataArray[imagen]+")").fadeIn(1000);        
		});*/

		/*$("#pasadorImagen").css("background-image", "url("+dataArray[imagen]+")").fadeOut(1000);
		$("#pasadorImagen").css("background-image", "url("+dataArray[imagen]+")").fadeIn(1000);*/
	 /*   imagen++; //incrementar la posicion del dataArray
		if (imagen==3) imagen=0; //volver a empezar
	},7000);  */      
        //});
}