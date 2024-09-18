<?php
//establecer por defecto
date_default_timezone_set('Europe/Madrid');

session_name("modularboxpanel");
session_start([
  'read_and_close'  => false,
]);

require_once("const/constantes.php");
require_once("phpincludes/phpgeneral.php");
require_once("phpincludes/phppropios.php");

//require_once("phpmailer/class.phpmailer.php");
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

require_once("phpincludes/phpdocumentos.php");
require_once("phpincludes/phpemails.php");
require_once("phpincludes/phpmultiwater.php");
require_once("phpincludes/phpcontadores.php");
require_once("phpincludes/phpluces.php");
require_once("phpincludes/phpsafey.php");
require_once("phpincludes/phppistaspadel.php");
require_once("phpincludes/phpparques.php");
require_once("phpincludes/phpcampanas.php");
require_once("phpincludes/phpautomatizacion.php");
require_once("phpincludes/phpaudios.php");
require_once("phpincludes/phpvideovigilancia.php");

try {
    //code...
    // Verifica si el método de la solicitud es POST
    $new_api = isset($_POST['new_api']) ? true : false;
    // Recoge los parámetros enviados en el cuerpo de la solicitud
    $op = isset($_POST['op']) ? $_POST['op'] : '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $new_api && $op == 69) {
            $devolver = "no";
            $id=intval(quitaComillasD($_POST["id"]));
            
            if($id>0){
                $email="";
                $nombre="";
                $apellidos="";
                $nombreEmpresa="";
                
                $pinAcceso="";
                $llaveAcceso="";
                $mandoAcceso="";
                $panelAcceso="";
                $emailAcceso="";
                $contrasenaAcceso="-";
                $emailAdministradorSistema="-";
                
                //obtener datos
                $patron="SELECT id,nombre,idusuario,pin,llave,mando,maillogin,email,apellidos FROM safey_accesos WHERE id=\"%s\" AND borrado=\"n\"";
                $sql=sprintf($patron,$id);
                $respuesta=mysqli_query($con,$sql) or die ("Error al buscar 9632234386864634637367690023423467787879958");
                if(mysqli_num_rows($respuesta)>0){
                    $fila=mysqli_fetch_array($respuesta);
                    
                    $nombre=$fila[1];
                    $apellidos=$fila[8];
                    $email=$fila[7];
                    
                    //datos del cliente
                    $patron1="SELECT nombre,apellidos,email FROM usuarios WHERE id=\"%s\"";
                    $sql1=sprintf($patron1,$fila[2]);
                    $respuesta1=mysqli_query($con,$sql1) or die ("Error al buscar 9632234386864113467787879958");
                    if(mysqli_num_rows($respuesta1)>0){
                        $fila1=mysqli_fetch_array($respuesta1);
                        $nombreEmpresa=$fila1[0]." ".$fila1[1];
                        $emailAdministradorSistema=$fila1[2];
                    }
                    mysqli_free_result($respuesta1);
                    
                    //datos de la placa del cliente, coge uno de ellos, no coge el que aplique
                    $rutaFicheroAdjunto="";
                    $nombreFicheroAdjunto="";
                    /*$patron8="SELECT nodo FROM safey_accesosnodos WHERE idacceso=\"%s\" AND borrado=\"n\" ";
                    $sql8=sprintf($patron8,$id);
                    $respuesta8=mysqli_query($con,$sql8) or die ("Error al buscar 96323357889652343868641134677878708");
                    if(mysqli_num_rows($respuesta8)>0){
                        $fila8=mysqli_fetch_array($respuesta8);*/
                        
                        //datos
                        $patron7="SELECT ficheronormas FROM safey_nodos WHERE idusuario=\"%s\" AND ficheronormas<>\"\" AND borrado=\"n\" AND guardado=\"s\" ";//AND id=\"%s\"
                        $sql7=sprintf($patron7,$fila[2]/*,$fila8[0]*/);
                        $respuesta7=mysqli_query($con,$sql7) or die ("Error al buscar 9632335776775234386864113467787879958");
                        if(mysqli_num_rows($respuesta7)>0){
                            $fila7=mysqli_fetch_array($respuesta7);
                            $rutaFicheroAdjunto="archivos_subidos/clientes/".$fila[2]."/safey/".$fila7[0];
                            $nombreFicheroAdjunto=$fila7[0];
                        }
                        mysqli_free_result($respuesta7);
                    /*}
                    mysqli_free_result($respuesta8);*/
                    
                    
                    //pin
                    $patron2="SELECT pin FROM safey_credenciales_pin WHERE id=\"%s\"";
                    $sql2=sprintf($patron2,$fila[3]);
                    $respuesta2=mysqli_query($con,$sql2) or die ("Error al buscar 9632234386864113467782227879958");
                    if(mysqli_num_rows($respuesta2)>0){
                        $fila2=mysqli_fetch_array($respuesta2);
                        $pinAcceso="- Su pin de acceso es: <b>".$fila2[0]."#</b><br>";
                    }
                    mysqli_free_result($respuesta2);
                    
                    //llave
                    $patron3="SELECT descripcion FROM safey_credenciales_llaves WHERE id=\"%s\"";
                    $sql3=sprintf($patron3,$fila[3]);
                    $respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 96322343333113467782227879958");
                    if(mysqli_num_rows($respuesta3)>0){
                        $fila3=mysqli_fetch_array($respuesta3);
                        $llaveAcceso="- Su llave de acceso es: <b>".$fila3[0]."</b><br>";
                    }
                    mysqli_free_result($respuesta3);
                    
                    //acceso panel
                    $patron5="SELECT descripcion FROM safey_credenciales_llaves WHERE id=\"%s\"";
                    $sql5=sprintf($patron5,$fila[3]);
                    $respuesta5=mysqli_query($con,$sql5) or die ("Error al buscar 963225343333511534677825227879958");
                    if(mysqli_num_rows($respuesta5)>0){
                        $fila5=mysqli_fetch_array($respuesta5);
                        $panelAcceso=$fila5[0];
                    }
                    mysqli_free_result($respuesta5);
                    
                    //mando
                    $patron4="SELECT id FROM safey_credenciales_mandos WHERE id=\"%s\"";
                    $sql4=sprintf($patron4,$fila[5]);
                    $respuesta4=mysqli_query($con,$sql4) or die ("Error al buscar 9632234444333311346778442227879958");
                    if(mysqli_num_rows($respuesta4)>0){
                        $fila4=mysqli_fetch_array($respuesta4);
                        $mandoAcceso="- Su mando de acceso es: <b>M ".$fila4[0]."#</b><br>";
                    }
                    mysqli_free_result($respuesta4);
                    
                    //datos del acceso web creado
                    $patron6="SELECT email,aes_decrypt(contrasena, \"%s\") FROM usuarios WHERE id=\"%s\"";
                    $sql6=sprintf($patron6,BBDDK,$fila[6]);
                    $respuesta6=mysqli_query($con,$sql6) or die ("Error al buscar 9632234386864113467787879958666");
                    if(mysqli_num_rows($respuesta6)>0){
                        $fila6=mysqli_fetch_array($respuesta6);
                        $emailAcceso="- Se le ha creado un usuario de acceso al portal web: <a href='https://panel.modularbox.com/'>Acceder al panel</a><br>Usuario: <b>".$fila6[0]."</b>";
                        $contrasenaAcceso=" contraseña: <b>".$fila6[1]."</b><br>";
                    }
                    mysqli_free_result($respuesta6);
                }
                mysqli_free_result($respuesta);
                
                //enviar mail de accesos
                if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $copia="";
                    $asunto=$nombre." -- SAFEY INVITACIÓN (MODULARBOX)";
                    //$contenido="Hola, <b>".$nombre."</b><br><br>Ha sido invitado a la utilización de Safey de MODULARBOX S.L. por: <b>".$nombreEmpresa."</b>.<br><br>".$pinAcceso."".$llaveAcceso."".$mandoAcceso."<br>".$emailAcceso."".$contrasenaAcceso."<br>Para cualquier duda contacte con el administrador del sistema: <a href='mailto:".$emailAdministradorSistema."'>".$emailAdministradorSistema."</a><br><br>Un saludo. <br><br><br> <b>No responda a este mensaje</b> ha sido autogenerado por la plataforma <b>(MODULARBOX)</b>.";
                    
                    
                    /*START contenido mail html definido julio*/
                    $contenido="<!DOCTYPE html>
                                    <html>
                                    <head>
                                        <meta charset='UTF-8' />
                                        <meta name='viewport' content='width=device-width, initial-scale=1.0' />
                                        <title>Confirmación de Pago y PIN de Acceso</title>
                                        <style>
                                            body {
                                                font-family: monospace, Helvetica, Arial, sans-serif !important;
                                                background-color: #f4f4f4 !important;
                                                margin: 0 !important;
                                                padding: 0 !important;
                                                font-size: 0.9rem !important;
                                            }

                                            .container {
                                                width: 100% !important;
                                                max-width: 600px !important;
                                                margin: 0 auto !important;
                                                background-color: #ffffff !important;
                                                padding: 20px !important;
                                                border-radius: 8px !important;
                                                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1) !important;
                                            }

                                            .header {
                                                text-align: center !important;
                                                padding: 10px 0 !important;
                                                background-color: #28a745 !important;
                                                color: #ffffff !important;
                                                border-radius: 8px 8px 0 0 !important;
                                            }

                                            .header h1 {
                                                margin: 0 !important;
                                                font-size: 24px !important;
                                            }

                                            .content {
                                                margin: 20px 0 !important;
                                            }

                                            .content p {
                                                line-height: 1.6 !important;
                                                color: #333333 !important;
                                            }

                                            .pin {
                                                text-align: center !important;
                                                margin: 20px 0 !important;
                                                padding: 15px !important;
                                                color: #ffffff !important;
                                                font-size: 2rem !important;
                                                word-break: break-all !important;
                                                font-weight: 900 !important;
                                            }

                                            .pin span {
                                                background-color: #28a745 !important;
                                                padding: 15px !important;
                                                border-radius: 5px !important;
                                            }

                                            .footer {
                                                text-align: center !important;
                                                font-size: 12px !important;
                                                color: #999999 !important;
                                                margin: 20px 0 0 !important;
                                            }
                                        </style>

                                    </head>

                                    <body>
                                        <div class='container'>
                                            <div class='header'>
                                                <h1>PIN de Acceso GYM<br><strong>".$nombreEmpresa."</strong></h1>
                                            </div>
                                            <div class='content'>
                                                <p>Estimad@".$nombre." ".$apellidos.",<br>
                                                    nos complace informarle que su pago de la subscripción al gym de ".$nombreEmpresa." ha sido procesado
                                                    satisfactoriamente.
                                                </p>
                                                <p>
                                                    A continuación, encontrará su PIN de acceso que le permitirá ingresar a nuestras instalaciones:
                                                </p>
                                                <div class='pin'><span>".$pinAcceso."</span></div>
                                                <p>
                                                    <strong>Contacto:</strong><br>
                                                    Si tiene alguna pregunta o necesita asistencia adicional, no dude en contactarnos a través de:
                                                    <br>Email: <a href='mailto:gym@modularbox.com'>gym@modularbox.com</a>
                                                    <br>WhatsApp: <a href='https://wa.me/34653483483'>653 483 483</a>
                                                    <br>Horario: L-V 09:00 - 14:00 | 17:00 - 20:00</a>
                                                    <!--Si tiene alguna pregunta o necesita asistencia adicional, no dude en contactarnos a través de <a
                                                        href='mailto:gym@modularbox.com'>gym@modularbox.com</a>, enviándonos un mensaje al <a
                                                        href='https://wa.me/34607373372'>607 373 372</a> o a través de la web dónde se dió de alta.-->
                                                </p>
                                                <p>
                                                    Agradecemos su confianza y esperamos que disfrute del gym.
                                                </p>
                                                <p>Atentamente,<br />El equipo de <a href='https://gym.modularbox.com/'>Modularbox</a></p>
                                            </div>

                                            <p style='color: #708c91;text-decoration: none;font-size: 12px;'>Te informamos de que seguirás recibiendo mensajes relacionados con tus subscripciones. Para saber más sobre la forma en la que usamos tu información, puedes consultar nuestra política de privacidad <a href='https://reservatupista.com/politica-de-privacidad-proteccion-de-datos-y-politica-de-cookies/' target='_blank' rel='noopener noreferrer' data-auth='NotApplicable' title='date de baja aquí'  style='text-decoration: none !important; color: #2dbeff' data-linkindex='11'>aquí</a>. <br /><br />
                                            </p>
                                            <div class='footer'>
                                                <p>&copy; 2024 Modularbox. Todos los derechos reservados.</p>
                                            </div>
                                        </div>
                                    </body>";
                    
                    mailGenerico($email,$copia,$asunto,$contenido,$id,$rutaFicheroAdjunto,$nombreFicheroAdjunto,$con);
                    
                    $devolver = "si";
                }
            }
            echo $devolver;
        
    }else{
        throw new Exception("Error Processing Request", 293);
    }
} catch (\Throwable $th) {
    // Si el método no es POST, responde con un error
    echo "no";
}
?>
