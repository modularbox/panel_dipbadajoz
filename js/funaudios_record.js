let video=document.querySelector("#audioGrabar");

/*document.querySelector("#grabarAudio").addEventListener("click",function(ev){
	navigator.mediaDevices.getUserMedia({audio: true})
		.then(record)
		.catch(err=> console.log(err));
	
});*/

function iniciarGrabacionAudio(elemento){
	
	var nombreAudio=$("#nombreAudioGrabar").val();
	if(nombreAudio!=""){
	   //alert("EMPEZANDO GRABACIÓN!!")
		navigator.mediaDevices.getUserMedia({audio: true})
			.then(record)
			.catch(err=> console.log(err));
	}else{
		Swal.fire('¡Completa el nombre del audio!','Debes completar el nombre del audio y vuelve a Iiniciar la Grabación.','warning');
	}
	
	
}

let chunks=[];

function record(stream){
	//video.srcObject=stream;//sereproduce a la vez
	
	/*START gestion del div del tiempo de grabacion*/
	$("#divTiempoGrabacionAudio").css("display", "block");
	repetirCadaSegundo();//llamar al evento que controla las llamadas cada segundo
	/*END gestion del div del tiempo de grabacion*/
	
	let options={
		mimeType: 'audio/webm:codecs=h264'
	}
	
	if(!MediaRecorder.isTypeSupported('audio/webm:codecs=h264')){
	   options={
			mimeType: 'audio/webm:codecs=vp8'
		}
	}
	//Swal.fire('¡Grabando!','Pulsa Finalizar Grabación, para terminar.','info');
	
	let mediaRecorder= new MediaRecorder(stream);
	
	mediaRecorder.start();//inicar el proceso de grabacion
	
	mediaRecorder.ondataavailable=function(e){
		//console.log(e.data)
		chunks.push(e.data)
	}//dataavailable
	
	
	//cuando se termina se junta todo, se ejecuta al terminar de grabar
	mediaRecorder.onstop=function(){
		//alert("Finalizó la grabación");
		let blob= new  Blob(chunks,{type:"audio/webm"});
		
		chunks=[];
		//download(blob);//llamar function para descargar
		subirAudioGrabado(blob);//llamar function para subir
        
        //mediaRecorder.stop();
	}
	
	/*setTimeout(()=>mediaRecorder.stop(),5000);*/
	
    //evento para el boton de parar grabacion
	document.querySelector("#pararAudio").addEventListener("click",function(ev){
		//alert("TERMINANDO GRABACIÓN!!")
		mediaRecorder.stop()
	});
	
}

function download(blob){
    alert("DESCARGANDO audio....")
	
    let link= document.createElement("a");
	link.href=window.URL.createObjectURL(blob);
	link.setAttribute("download","video_recorded.mp3");
	
	document.body.appendChild(link);
	link.click();
	link.remove();
    
    location.reload();
}

function subirAudioGrabado(blob){
	
	var idUsuario=$("#selectUsuariosAudiosSubirFiltro option:selected").val();
	var nombreAudio=$("#nombreAudioGrabar").val();
    var archivo = blob; 
    //var textoAudio = $('#textoAudio').val(); 
	if(idUsuario>0 && nombreAudio!=""){
		$("#cargandoSubidorFicheroAudios").show();
		
		/*START gestion del div del tiempo de grabacion*/
		$("#divTiempoGrabacionAudio").css("display", "none");
		/*END gestion del div del tiempo de grabacion*/
		
		var form_data = new FormData();   
		form_data.append('op', 138);
		form_data.append('file_upload', archivo); 
		form_data.append('idUsuario', idUsuario); 
		form_data.append('nombreAudio', nombreAudio);  
		//form_data.append('textoAudio', textoAudio);                        
		$.ajax({
			url: 'adminajax.php',
			dataType: 'text',
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,                         
			type: 'post',
			success: function(data){
				$("#cargandoSubidorFicheroAudios").hide();
                
				var respuesta=data.split("@#");
				if(respuesta[0]=="s"){
					Swal.fire('Correcto','Se ha guardado el audio correctamente.','success');
                    
					//$("#nombreAudioGrabar").val("");//limpiar

					/*start devolver contenido tabla audios*/			
					//$("#tablaFicheroAudios").html(respuesta[1]);

					//var columnasTablaFicheros= [null,{ "width": "45%" },{ "width": "15%" },{ "width": "30%" },{ "width": "10%" }];
					//cargarTabla.init("tablaFicheroAudios",columnasTablaFicheros,[0, "asc"],50,true);
					/*end devolver contenido tabla audios*/	

				}else if(respuesta[0]=="n" || respuesta[0]=="l"){
					Swal.fire('Error','No se ha guardado el fichero, prueba nuevamente, completa todos los campos.','error');
				}else if(respuesta[0]=="e"){
					Swal.fire('Tipo fichero no válido','El tipo de documento no es compatible','warning');
				}	
                
                //recargar web para parar audio
                setTimeout(()=>location.reload(),2000);
                
			}
		 });
	}else{//se comprueba al iniciar la grabacion, es decir, aqui no entrara
		Swal.fire('Faltan datos por completar','Por favor establece el nombre del audio.','warning');
		
        setTimeout(()=>location.reload(),3000);
	}
}

//llamar a esta que establece el intervalo
function repetirCadaSegundo(){
	let identificadorIntervaloDeTiempo;
  	identificadorIntervaloDeTiempo = setInterval(mandarMensaje, 1000);
}
//que hacer cada segundo de intervalo
function mandarMensaje(){
	var tiempo=parseInt($("#numTiempoGrabacionAudio").html());
	tiempo+=1;
	$("#numTiempoGrabacionAudio").html(tiempo)
}