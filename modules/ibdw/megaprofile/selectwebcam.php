<?php
  require_once( '../../../inc/header.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' ); 
  mysql_query("SET NAMES 'utf8'");
  $megaprofile_query = "SELECT ID FROM bx_photos_main ORDER BY ID DESC LIMIT 0,1";
  $esegui = mysql_query($megaprofile_query);
  $megaprofile_estrai = mysql_fetch_assoc($esegui);
  $mioultimoid = $megaprofile_estrai['ID'];
  $mioultimoid++;
  
  $idutente = $_COOKIE['memberID'];
  $filename = "../../../flash/modules/photo/files/".$idutente.".jpg";
  $filedest = "../../../modules/boonex/photos/data/files/".$mioultimoid.".jpg";
  
  $filedest2 = "../../../modules/boonex/photos/data/files/".$mioultimoid."_m.jpg";
  $nfiledest = "../../../modules/boonex/photos/data/files/";
  $targetPath = $_SERVER['DOCUMENT_ROOT']."/modules/boonex/photos/data/files/".$mioultimoid.".jpg";
  $targetPathwk = $_SERVER['DOCUMENT_ROOT']."/modules/boonex/photos/data/files/";
  copy($filename,$filedest);
  copy($filename,$filedest2);
  list($width, $height) = getimagesize($filedest); 
  
  // SISTEMA MINIATURE RI
    $thumb = imagecreatetruecolor(32,32);
    $source = imagecreatefromjpeg($targetPath);
    imagecopyresized($thumb, $source, 0, 0, 0, 0, 32,32, $width, $height);
    $namefiles = $mioultimoid.'_ri.jpg';
    // Salvo l'immagine ridimensionata
    imagejpeg($thumb,$targetPathwk.$namefiles,70);
    // FINE MINIATURA
    
    // SISTEMA MINIATURE RT
    $thumb = imagecreatetruecolor(64,64);
    $source = imagecreatefromjpeg($targetPath);
    imagecopyresized($thumb, $source, 0, 0, 0, 0, 64,64, $width, $height);
    $namefiles = $mioultimoid.'_rt.jpg';
    // Salvo l'immagine ridimensionata
    imagejpeg($thumb,$targetPathwk.$namefiles,70);
    // FINE MINIATURA
    
    // SISTEMA MINIATURE RT
    $thumb = imagecreatetruecolor(140,140);
    $source = imagecreatefromjpeg($targetPath);
    imagecopyresized($thumb, $source, 0, 0, 0, 0, 140,140, $width, $height);
    $namefiles = $mioultimoid.'_t.jpg';
    // Salvo l'immagine ridimensionata
    imagejpeg($thumb,$targetPathwk.$namefiles,70);
    // FINE MINIATURA
      
  $size='100x100';
  $hash = md5(RAND());
  $inserimento = "INSERT INTO bx_photos_main (Owner,Ext,Size,Title,Uri,Status,Hash) VALUES('$idutente','jpg','$size','$mioultimoid','$mioultimoid','approved','$hash')";
  $esegui = mysql_query($inserimento);
  
  $versione="SELECT Name,sys_options.VALUE FROM sys_options WHERE Name='sys_tmp_version'";
  $esegui=mysql_query($versione);
  $riga=mysql_fetch_assoc($esegui);
  $explode=explode('.',$riga['VALUE']);
  $versionedolmain=$explode[1];
  $versionedol=$explode[2];
  if($versionedolmain=='0') 
  {
   $nomeutente = getNickName($idutente);
  }
  else
  {
   $nomeutente = getUsername($idutente);
  }
  
	$ottienialbum = "SELECT VALUE FROM sys_options WHERE Name='bx_photos_profile_album_name'";
	$eseguialbum = mysql_query($ottienialbum);
	$fetchalbum= mysql_fetch_assoc($eseguialbum);
	$nalbumm = $fetchalbum['VALUE'];
	$nomealbum = uriFilter(str_replace("{nickname}",$nomeutente,$nalbumm));
  
  
  
  $estrazione = "SELECT ID FROM sys_albums WHERE Owner = '$idutente' AND Type='bx_photos' AND Uri='$nomealbum'";
  $esegui = mysql_query($estrazione);
  $info = mysql_fetch_assoc($esegui);
    
  $idalbum = $info['ID'];
    
  $inserimento = "INSERT INTO sys_albums_objects (id_album,id_object) VALUES('$idalbum','$mioultimoid')";
  $esegui = mysql_query($inserimento);
  
  $estreifoto = "SELECT Hash FROM ibdw_mega_profile WHERE Owner = '$idutente'"; 
  $esegui = mysql_query($estreifoto);
  $rgx = mysql_num_rows($esegui);
  
  if($rgx == 0) 
  { 
   $inserimento = "INSERT INTO ibdw_mega_profile (Owner,Hash) VALUES ('$idutente','0')";
   $esegui = mysql_query($inserimento);
  }
  else
  {  
   $estrazione = "UPDATE ibdw_mega_profile SET Hash = '$hash' WHERE Owner = '$idutente'";
   $esegui = mysql_query($estrazione);
  } 
  $estrazione = "UPDATE sys_albums SET LastObjId='$mioultimoid' WHERE Uri='$nomealbum'";
  $esegui = mysql_query($estrazione);
?>