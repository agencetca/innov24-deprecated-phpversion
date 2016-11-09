<?php 
  require_once( '../../../inc/header.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );   
  mysql_query("SET NAMES 'utf8'");
  $idutente = $_POST['idutente'];
  $idfoto = $_POST['idfoto'];
  $estreifoto = "SELECT ID,Hash FROM bx_photos_main WHERE ID = '$idfoto'"; 
  $esegui = mysql_query($estreifoto);
  $rgx = mysql_fetch_assoc($esegui);
  $codefoto = $rgx['Hash'];
  $estreifoto = "SELECT Hash FROM ibdw_mega_profile WHERE Owner = '$idutente'"; 
  $esegui = mysql_query($estreifoto);
  $rgx = mysql_num_rows($esegui);
  if($rgx==0)
  { 
   $inserimento = "INSERT INTO ibdw_mega_profile (Owner,Hash) VALUES ('$idutente','0')";
   $esegui = mysql_query($inserimento);
  }
  $estrazione = "UPDATE ibdw_mega_profile SET Hash = '$codefoto' WHERE Owner = '$idutente'";
  $esegui = mysql_query($estrazione);
  
  
  
  
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
  
  $estrazione = "UPDATE sys_albums SET LastObjId='$idfoto' WHERE Uri='$nomealbum'";
  $esegui = mysql_query($estrazione); 
?>