<?php
  require_once( '../../../../inc/header.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );    
  function findexts ($filename) 
  { $filename = strtolower($filename) ; $exts = split("[/\\.]", $filename) ; $n = count($exts)-1; $exts = $exts[$n]; return $exts; }
  
    function imagetranstowhite($trans) {
	// Create a new true color image with the same size
	$w = imagesx($trans);
	$h = imagesy($trans);
	$white = imagecreatetruecolor($w, $h);
 
	// Fill the new image with white background
	$bg = imagecolorallocate($white, 255, 255, 255);
	imagefill($white, 0, 0, $bg);
 
	// Copy original transparent image onto the new image
	imagecopy($white, $trans, 0, 0, 0, 0, $w, $h);
	return $white;
}
  function cropImage($nw, $nh, $source, $stype, $dest) {
	$size = getimagesize($source);
	$w = $size[0];
	$h = $size[1];
	switch($stype) {
		case 'gif':
		$simg = imagecreatefromgif($source);
		break;
		case 'jpg':
		$simg = imagecreatefromjpeg($source);
		break;
		case 'png':
		$simg1 = imagecreatefrompng($source);
		$simg = imagetranstowhite($simg1);
		break;
	}
	$dimg = imagecreatetruecolor($nw, $nh);
	$wm = $w/$nw;
	$hm = $h/$nh;
	$h_height = $nh/2;
	$w_height = $nw/2;
	if($w> $h) {
		$adjusted_width = $w / $hm;
		$half_width = $adjusted_width / 2;
		$int_width = $half_width - $w_height;
		imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$nh,$w,$h);
	} elseif(($w <$h) || ($w == $h)) {
		$adjusted_height = $h / $wm;
		$half_height = $adjusted_height / 2;
		$int_height = $half_height - $h_height;
		imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$nw,$adjusted_height,$w,$h);
	} else {
		imagecopyresampled($dimg,$simg,0,0,0,0,$nw,$nh,$w,$h);
	}
	imagejpeg($dimg,$dest,100);
}
  
  //recupero ultimo id
  mysql_query("SET NAMES 'utf8'");
  $megaprofile_query = "SELECT ID FROM bx_photos_main ORDER BY ID DESC LIMIT 0,1";
  $esegui = mysql_query($megaprofile_query);
  $megaprofile_estrai = mysql_fetch_assoc($esegui);
  $mioultimoid = $megaprofile_estrai['ID'];
  $mioultimoid++;
  
  $explo = $_REQUEST['folder'];
  $explos = explode("##", $explo);
  
  $cartella = $explos[0];
  $ewsa = $explos[1];  
  
if (!empty($_FILES)) {

	$tempFile = $_FILES['Filedata']['tmp_name']; 
	
  $ext = findexts ($_FILES['Filedata']['name']); 
  list($width, $height) = getimagesize($_FILES['Filedata']['tmp_name']);
  $namefile = $mioultimoid.'.'.$ext;
  
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $cartella . '/';
	$targetFile =  str_replace('//','/',$targetPath) . $namefile;
	
		move_uploaded_file($tempFile,$targetFile);
		echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
		 $width_m = $width;
		 $height_m = $height;
	   if ($width_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; if ($width_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; if ($width_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; if ($width_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; if ($width_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; if ($width_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2;  if ($width_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; if ($width_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; } } } } } } } }
    if ($height_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2;  if ($height_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; if ($height_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; if ($height_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; if ($height_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; if ($height_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; if ($height_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; if ($height_m >= 600) { $width_m = $width_m/2; $height_m = $height_m/2; }  }  }  }  }  }  }  }    
    // SISTEMA MINIATURE M
    $thumb = imagecreatetruecolor($width_m,$height_m);
    if($ext == 'jpg' OR $ext == 'jpeg') { $source = imagecreatefromjpeg($targetFile); }
    elseif($ext == 'png') { $source1 = imagecreatefrompng($targetFile); $source = imagetranstowhite($source1); }
    elseif($ext == 'gif') { $source = imagecreatefromgif($targetFile); }
    
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $width_m,$height_m, $width, $height);
    $namefiles = $mioultimoid.'_m.jpg';
    // Salvo l'immagine ridimensionata  
    
    imagejpeg($thumb,$targetPath.$namefiles,70);
    
    // FINE MINIATURA
    
  
    // SISTEMA MINIATURE RI
    cropImage(32, 32, $targetFile,$ext,$targetPath.'/'.$mioultimoid.'_ri.jpg');
    // FINE MINIATURA
    
    // SISTEMA MINIATURE RT
    cropImage(64, 64, $targetFile,$ext,$targetPath.'/'.$mioultimoid.'_rt.jpg');
    // FINE MINIATURA
    
    // SISTEMA MINIATURE T
   cropImage(140, 140, $targetFile,$ext,$targetPath.'/'.$mioultimoid.'_t.jpg');
    // FINE MINIATURA

}
    $namefile = $mioultimoid.'.'.$ext;
    $hash = md5(RAND());
    $size = $width.'x'.$height;
    $inserimento = "INSERT INTO bx_photos_main (ID,Owner,Ext,Size,Title,Uri,Status,Hash, bx_photos_main.Date) VALUES($mioultimoid,'$ewsa','$ext','$size','$namefile','$namefile','approved','$hash',".time().")";
    $esegui = mysql_query($inserimento);
      
	
  
  $versione="SELECT Name,sys_options.VALUE FROM sys_options WHERE Name='sys_tmp_version'";
  $esegui=mysql_query($versione);
  $riga=mysql_fetch_assoc($esegui);
  $explode=explode('.',$riga['VALUE']);
  $versionedolmain=$explode[1];
  $versionedol=$explode[2];
  if($versionedolmain=='0') 
  {
   $nomeutente = getNickName($ewsa);
  }
  else
  {
   $nomeutente = getUsername($ewsa);
  }
  
	$ottienialbum = "SELECT VALUE FROM sys_options WHERE Name='bx_photos_profile_album_name'";
	$eseguialbum = mysql_query($ottienialbum) or die(mysql_error());
	$fetchalbum= mysql_fetch_assoc($eseguialbum);
	$nalbumm = $fetchalbum['VALUE'];
	$nomealbum = uriFilter(str_replace("{nickname}",$nomeutente,$nalbumm));

	
    $estrazione = "SELECT ID,ObjCount,Uri FROM sys_albums WHERE Owner = '$ewsa' AND Uri='$nomealbum'";
    $esegui = mysql_query($estrazione);
    $verificanumero = mysql_num_rows($esegui);
    
    if($verificanumero =! 0)  { 
    $info = mysql_fetch_assoc($esegui);
    $idalbum = $info['ID'];  }
    
    else { 
    $estrazione = "SELECT ID,ObjCount,Uri FROM sys_albums WHERE Owner = '$ewsa' ORDER BY ID DESC";
    $esegui = mysql_query($estrazione);
    $info = mysql_fetch_assoc($esegui);
    $idalbum = $info['ID'];
    }
    
    
    $numeroogg = $info['ObjCount'];
    $numeroogg++;
    
    $estrazione = "UPDATE sys_albums SET ObjCount = '$numeroogg', LastObjId='$mioultimoid' WHERE ID='$idalbum'";
    $esegui = mysql_query($estrazione); 
    
    $inserimento = "INSERT INTO sys_albums_objects (id_album,id_object) VALUES('$idalbum','$mioultimoid')";
    $esegui = mysql_query($inserimento);
    
    $estreifoto = "SELECT Hash FROM ibdw_mega_profile WHERE Owner = '$ewsa'"; 
    $esegui = mysql_query($estreifoto);
    $rgx = mysql_num_rows($esegui);
  
    if($rgx == 0) { 
    $inserimento = "INSERT INTO ibdw_mega_profile (Owner,Hash) VALUES ('$ewsa','0')";
    $esegui = mysql_query($inserimento);
    }
    
    $estrazione = "UPDATE ibdw_mega_profile SET Hash = '$hash' WHERE Owner = '$ewsa'";
    $esegui = mysql_query($estrazione);
    


?>