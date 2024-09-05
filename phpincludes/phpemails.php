<?php
//gestion mails
function mailGenerico($mails,$copia,$asunto,$contenido,$idusuario,$rutaFicheroAdjunto,$nombreFicheroAdjunto,$con){
	
	//$mail = new PHPMailer(true);// Passing `true` enables exceptions
	$mail = new PHPMailer\PHPMailer\PHPMailer;
	//*****Server settings*****
	//$mail->SMTPDebug = 2;// Enable verbose debug output, para depurar
	$mail->isSMTP();//comentar para no autentificar smtp
	$mail->Host = 'smtp.ionos.es'; // Specify main and backup SMTP servers//mail.example.com paraa no autentificados con smtp
	$mail->SMTPAuth = true;  // Enable SMTP authentication//false para no autentificar smtp
	$mail->Username = 'alertas@modularbox.com';// SMTP username
	$mail->Password = 'Peligrito2021@'; 
	$mail->setFrom('alertas@modularbox.com');
	$mail->FromName = utf8_decode("MODULARBOX");

	// ****SMTP password********
	$mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;// TCP port to connect to
	$mail->Subject    = utf8_decode($asunto);
	//
	
	/*$cuerpo=sprintf('%s; height: auto;font-size:13px">
						%s<br><br></div>',"%",nl2br(TildesHtml($contenido)));*/
    $cuerpo=sprintf('%s',TildesHtml($contenido));
	$mail->WordWrap   = 50;
	$mail->AltBody    = "Necesita activar el HTML";

	$envio=trim($mails);
	$emailsfinales=explode(";",$envio);
	$mail->AddAddress($emailsfinales[0]);

	if($copia!=""){
		$envio2=trim($copia);
		$emailsfinales2=explode(";",$envio2);
		for($j=0;$j<count($emailsfinales2);$j++){
			$mail->AddBCC($emailsfinales2[$j]);
		}
	}
	for($i=1;$i<count($emailsfinales);$i++){
		$mail->AddCC($emailsfinales[$i]);
	}
	
	$mail->MsgHTML($cuerpo);
    
    //adjuntos
    //$rutaFicheroAdjunto="archivos_subidos/clientes/xxxx/safey/xxxx.pdf";
    //$nombreFicheroAdjunto="xxxx.pdf";
    if($rutaFicheroAdjunto!="" && $nombreFicheroAdjunto!=""){
        //echo $rutaFicheroAdjunto."--".$nombreFicheroAdjunto;
		$mail->AddAttachment($rutaFicheroAdjunto, $nombreFicheroAdjunto);
	}
    
	$mail->IsHTML(true); // send as HTML
	if(!$mail->Send()) {
		//$_SESSION["emailenviado"]=2;
	  	echo "*Mail Error: " . $mail->ErrorInfo;
	}else{
		//$_SESSION["emailenviado"]=1;
		echo "s";
	}
}
?>