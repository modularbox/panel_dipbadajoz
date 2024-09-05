<?php
session_name("facturacioncind");
session_start([
  'read_and_close'  => false,
]);

require_once("../cons/constantes.php");
require_once("phpgeneral.php");
require_once("phppropios.php");
if(usuarioCorrecto($con) /*&& !empty($_FILES)*/ ){
	if($_GET["opt"]==1){//subir

		$fileTypes = array('jpg','jpeg','tif','tiff','gif','png','JPG','JPEG','TIF','TIFF','GIF','PNG', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'rtf', 'txt', 'bmp', 'DOC', 'DOCX', 'XLS', 'XLSX', 'PDF', 'RTF', 'TXT', 'BMP' , 'PNG');
		
		$Imagenes =count(isset($_FILES['archivos']['name'])?$_FILES['archivos']['name']:0);
		$infoImagenesSubidas = array();
		for($i = 0; $i < $Imagenes; $i++) {
			// El nombre y nombre temporal del archivo que vamos para adjuntar
			$nombreArchivo=isset($_FILES['archivos']['name'][$i])?$_FILES['archivos']['name'][$i]:null;
			$nombreTemporal=isset($_FILES['archivos']['tmp_name'][$i])?$_FILES['archivos']['tmp_name'][$i]:null;


			/*----------- START gestion nombre final-----------*/
			$nombreAuxiliar=obtenerNombreArchivoDisponible($con);//_loquesea

			$auxPosicionExtension=encontrarExtension($nombreArchivo);//saber posicion del punto de extension

			$auxMontarNombre=substr($nombreArchivo,0, (int)$auxPosicionExtension);//tengo nombre
			$auxMontarExtension=substr($nombreArchivo,(int)$auxPosicionExtension, (int)strlen($nombreArchivo));//tengo .extension

			$nombreArchivoFinal=$auxMontarNombre.$nombreAuxiliar.$auxMontarExtension;//nombre completo nuevo
			/*----------- END gestion nombre final-----------*/

			if(in_array(substr($auxMontarExtension,1),$fileTypes)){//valido extension
				/**--------------START saber carpeta------************/
				$carpetad="";
				if($_POST["tabla"]=="factr"){//fact recibidas
					$carpetad="empresa/".calculaIdEmpresa($con)."/factrecibidas/";
				}else if($_POST["tabla"]=="subidocGe"){//subidor doc generica
					$carpetad="empresa/".calculaIdEmpresa($con)."/docgenerica/";
				}else {

				}
				/**--------------END saber carpeta------************/

				/**--------------START GESTION BASE DE DATOS------************/
				//rutas
				$rutaSubirFichero="../archivossubidos/".$carpetad.$_POST["idRelacionado"]."/".$nombreArchivoFinal;
				$rutaMostrar="http://cinde.es/proyectos/facturacioncinde/admin/archivossubidos/".$carpetad.$_POST["idRelacionado"]."/".$nombreArchivoFinal;

				if($_POST["tabla"]=="factr"){//fact recibidas

					$patron5="SELECT id FROM archivossubidos WHERE tabla=\"%s\" AND idrelacionado=\"%s\" AND idusuario=\"%s\"";
					$sql5=sprintf($patron5,$_POST["tabla"],$_POST["idRelacionado"],calculaIdEmpresa($con));
					$respuesta5=mysqli_query($con,$sql5) or die ("Error 123");
					if(mysqli_num_rows($respuesta5)>0){
					}else{

						//comprobar si crear carpeta hija
						$patron4="SELECT id FROM archivossubidos WHERE tabla=\"%s\" AND idusuario=\"%s\" ";//AND idrelacionado=\"%s\"
						$sql4=sprintf($patron4,$_POST["tabla"],calculaIdEmpresa($con),$_POST["idRelacionado"]);
						$respuesta4=mysqli_query($con,$sql4) or die ("Error 1234545");
						if(mysqli_num_rows($respuesta4)>0){
						}else{
							$carpetaEmpresa=calculaIdEmpresa($con);
							creardir("factrecibidas","admin/archivossubidos/empresa/".calculaIdEmpresa($con)."/");//crear carpeta hija
						}

						creardir($_POST["idRelacionado"],"admin/archivossubidos/".$carpetad);
					}
					mysqli_free_result($respuesta5);

					$patron="INSERT INTO archivossubidos SET tabla=\"%s\", idrelacionado=\"%s\", archivo=\"%s\", idusuario=\"%s\", fechaalta=\"%s\"";
					$sql=sprintf($patron,$_POST["tabla"],$_POST["idRelacionado"],$nombreArchivoFinal,calculaIdEmpresa($con),date("Y-m-d"));
					$respuesta=mysqli_query($con,$sql) or die ("Error 1234");
					$idSubido=mysqli_insert_id($con);

				}else if($_POST["tabla"]=="subidocGe"){//subidor doc generica

					$patron5="SELECT id FROM archivossubidos WHERE tabla=\"%s\" AND idrelacionado=\"%s\"";
					$sql5=sprintf($patron5,$_POST["tabla"],$_POST["idRelacionado"]);
					$respuesta5=mysqli_query($con,$sql5) or die ("Error 1231234124");
					if(mysqli_num_rows($respuesta5)>0){
					}else{

						//comprobar si crear carpeta hija
						$patron4="SELECT id FROM archivossubidos WHERE tabla=\"%s\" AND idusuario=\"%s\" AND idrelacionado=\"%s\"";
						$sql4=sprintf($patron4,$_POST["tabla"],calculaIdEmpresa($con),$_POST["idRelacionado"]);
						$respuesta4=mysqli_query($con,$sql4) or die ("Error 1234898985674574545");
						if(mysqli_num_rows($respuesta4)>0){
						}else{
							$carpetaEmpresa=calculaIdEmpresa($con);
							creardir("docgenerica","admin/archivossubidos/empresa/".$carpetaEmpresa."/");//crear carpeta hija
						}

						creardir($_POST["idRelacionado"],"admin/archivossubidos/".$carpetad);
					}
					mysqli_free_result($respuesta5);

					$patron="INSERT INTO archivossubidos SET tabla=\"%s\", idrelacionado=\"%s\", archivo=\"%s\", idusuario=\"%s\", fechaalta=\"%s\"";
					$sql=sprintf($patron,$_POST["tabla"],$_POST["idRelacionado"],$nombreArchivoFinal,calculaIdEmpresa($con),date("Y-m-d"));
					$respuesta=mysqli_query($con,$sql) or die ("Error 1280089567546734");
					$idSubido=mysqli_insert_id($con);

				}
				/**--------------END GESTION BASE DE DATOS------************/

				//guardar en la carpeta
				move_uploaded_file($nombreTemporal,$rutaSubirFichero);

				$tipo=explode('@#',obtenerTipoFicheroSubidor($nombreArchivoFinal));

				$tipo="$tipo[0]";
				$tipoFile="$tipo[1]";

				//
				$infoImagenesSubidas[$i]=array("caption"=>"$nombreArchivoFinal","type"=> "$tipo", "downloadUrl"=> "$rutaMostrar","filetype"=> "$tipoFile","height"=>"200px","url"=>"includes/subidor.php?opt=2","key"=>$idSubido);
				$ImagenesSubidas[$i]=$rutaMostrar;
			}
		}
		//
		$arr = array("file_id"=>0,"overwriteInitial"=>true,"initialPreviewConfig"=>$infoImagenesSubidas, "initialPreview"=>$ImagenesSubidas);
		echo json_encode($arr);

	}else if($_GET["opt"]==2){//borrar

			parse_str(file_get_contents("php://input"),$archivoEliminar);
			$key= $archivoEliminar['key'];
			$patron="SELECT archivo,idrelacionado,tabla FROM archivossubidos WHERE id=%s";
			$sql=sprintf($patron,$key);
			$respuesta=mysqli_query($con,$sql) or die ("Error al buscar 3453567567344355656434sdfsdf34345454");
			$fila=mysqli_fetch_array($respuesta);

			$archivo=$fila[0];
			$carpetab="";
			if($fila[2]=="factr"){
				$carpetab="empresa/".calculaIdEmpresa($con)."/factrecibidas";
			}if($fila[2]=="subidocGe"){
				$carpetab="empresa/".calculaIdEmpresa($con)."/docgenerica";
			}
		
			$directorio = $_SERVER['DOCUMENT_ROOT']."/proyectos/facturacioncinde/admin/archivossubidos/".$carpetab."/".$fila[1]."/";
		
			//conexión ftp
            $servidor_ftp = FTP;
            $conexion_id = ftp_connect($servidor_ftp);
            $ftp_usuario = USER_FTP;
            $ftp_clave = PASS_FTP;
						
            $resultado_login = ftp_login($conexion_id,$ftp_usuario,$ftp_clave);
            ftp_pasv($conexion_id,TRUE);

			mysqli_free_result($respuesta);

			if((!$conexion_id) || (!$resultado_login)) {
				$arr = array(error=>'Error al eliminar.',errorkeys=> [],append=> true);
				echo json_encode($arr);
			}else{
				unlink($directorio.$archivo);
				$patron2="DELETE FROM archivossubidos WHERE id=%s";
				$sql2=sprintf($patron2,$key);
	       		$respuesta2=mysqli_query($con,$sql2) or die ("Error al borrar 34345676787875455");

				$patron3="SELECT archivo FROM archivossubidos WHERE idrelacionado=%s AND tabla=\"%s\"";
				$sql3=sprintf($patron3,$fila[1],$fila[2]);
				$respuesta3=mysqli_query($con,$sql3) or die ("Error al buscar 34535674567355334545345673434sdfsdf34345454");
				if(mysqli_num_rows($respuesta3)>0){
				}else{
					rmdir($directorio);
				}
            }
			echo 1;
	}else{

	}
	//comprobar espacio
	actualizarEspacioDiscoCliente($con);
}
?>