<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/megaprofile/config.php';
$mioid=$_COOKIE['memberID'];
$idfoto=$_POST['idfoto'];
$targ_w = $_POST['w']; 
$targ_h = $_POST['h'];
$jpeg_quality = 90;
	
function cropImage($nw, $nh, $source, $stype, $dest) 
{
 $size = getimagesize($source);
 $w = $size[0];
 $h = $size[1];
 switch($stype) 
 {
	case 'gif':
		$simg = imagecreatefromgif($source);
		break;
		case 'jpg':
		$simg = imagecreatefromjpeg($source);
		break;
		case 'png':
		$simg = imagecreatefrompng($source);
		break;
 }
 $dimg = imagecreatetruecolor($nw, $nh);
 $wm = $w/$nw;
 $hm = $h/$nh;
 $h_height = $nh/2;
 $w_height = $nw/2;
 if($w> $h) 
 {
  $adjusted_width = $w / $hm;
  $half_width = $adjusted_width / 2;
  $int_width = $half_width - $w_height;
  imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$nh,$w,$h);
 } 
 elseif(($w <$h) || ($w == $h)) 
 {
	$adjusted_height = $h / $wm;
	$half_height = $adjusted_height / 2;
	$int_height = $half_height - $h_height;
	imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$nw,$adjusted_height,$w,$h);
 } 
 else imagecopyresampled($dimg,$simg,0,0,0,0,$nw,$nh,$w,$h);
 imagejpeg($dimg,$dest,100);
}
	
//output destination files
$srcmn = BX_DIRECTORY_PATH_ROOT.'modules/boonex/photos/data/files/'.$_POST['ift'].'.jpg';
$src = BX_DIRECTORY_PATH_ROOT.'modules/boonex/photos/data/files/'.$_POST['ift'].'_m.jpg';
$srcri = BX_DIRECTORY_PATH_ROOT.'modules/boonex/photos/data/files/'.$_POST['ift'].'_ri.jpg';
$srcrt = BX_DIRECTORY_PATH_ROOT.'modules/boonex/photos/data/files/'.$_POST['ift'].'_rt.jpg';
$srct = BX_DIRECTORY_PATH_ROOT.'modules/boonex/photos/data/files/'.$_POST['ift'].'_t.jpg';

//temp output for the cropped image	
$temp = BX_DIRECTORY_PATH_ROOT.'modules/ibdw/megaprofile/tempcrop/'.$_POST['ift'].'.jpg';
$img_r = imagecreatefromjpeg($src);
$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],$targ_w,$targ_h,$_POST['w'],$_POST['h']);

//copy of the cropped images resized
imagejpeg($dst_r,$srcmn,$jpeg_quality);
imagejpeg($dst_r,$src,$jpeg_quality);

  
cropImage(140, 140, $srcmn,'jpg',$srct);
cropImage(64, 64, $srcmn,'jpg',$srcrt);
cropImage(32, 32, $srcmn,'jpg',$srcri);
	
if($_POST['setavatar']==1 and ($thumbtype==0))
{ 
 $targetPath = BX_DIRECTORY_PATH_ROOT.'modules/boonex/avatar/data/images/';
 list($width, $height) = getimagesize($src);
 $controllo = "SELECT id FROM bx_avatar_images ORDER BY id DESC LIMIT 0,1";
 $esegui = mysql_query($controllo);
 $fetch = mysql_fetch_assoc($esegui);
 $idavatar = $fetch['id']+1;
 $namefiles = $idavatar.'.jpg';
 cropImage(64, 64, $src,'jpg',$targetPath.$namefiles);
 $namefiles = $idavatar.'i.jpg';
 cropImage(32, 32, $src,'jpg',$targetPath.$namefiles);   
 $query="UPDATE Profiles SET Avatar = '$idavatar' WHERE ID = $mioid";
 $esegui = mysql_query($query);
 $query="INSERT INTO bx_avatar_images (id,author_id) VALUES ('".$idavatar."','".$mioid."')";
 $esegui = mysql_query($query);
}
//update order for the main profile pic
  $versione="SELECT Name,sys_options.VALUE FROM sys_options WHERE Name='sys_tmp_version'";
  $esegui=mysql_query($versione);
  $riga=mysql_fetch_assoc($esegui);
  $explode=explode('.',$riga['VALUE']);
  $versionedolmain=$explode[1];
  $versionedol=$explode[2];
  if($versionedolmain=='0') 
  {
   $nomeutente = getNickName($mioid);
  }
  else
  {
   $nomeutente = getUsername($mioid);
  }
  
	$ottienialbum = "SELECT VALUE FROM sys_options WHERE Name='bx_photos_profile_album_name'";
	$eseguialbum = mysql_query($ottienialbum);
	$fetchalbum= mysql_fetch_assoc($eseguialbum);
	$nalbumm = $fetchalbum['VALUE'];
	$nomealbum = uriFilter(str_replace("{nickname}",$nomeutente,$nalbumm));
  $estrazione = "SELECT ID FROM sys_albums WHERE Owner = '$mioid' AND Uri='$nomealbum'";
  $esegui = mysql_query($estrazione);
  $info = mysql_fetch_assoc($esegui);
  $idalbum = $info['ID'];

  //ordering images
  $ordercount=2;
  $itemis=0;
  $initorder="SELECT * FROM sys_albums_objects WHERE sys_albums_objects.id_album=".$idalbum." ORDER BY obj_order ASC, id_object DESC";
  $esegui = mysql_query($initorder);
  
  while($foto = mysql_fetch_array($esegui)) 
  {
    $order[$itemis]=$ordercount;
    $ordercount=$ordercount+1;
    $itemis=$itemis+1;
  }
  $ordercount=1;
  $esegui = mysql_query($initorder);
  while($foto = mysql_fetch_array($esegui)) 
  {
    $query="UPDATE sys_albums_objects SET obj_order = ".$order[$ordercount-1]." WHERE id_object=".$foto[1]." AND sys_albums_objects.id_album=".$idalbum;
    $esegui2 = mysql_query($query);
    $ordercount=$ordercount+1;
  }
  
  $updateimage="UPDATE sys_albums_objects SET obj_order=1 WHERE id_album=".$idalbum." AND id_object=".$idfoto;
  $esegui = mysql_query($updateimage);
//end
    
    deleteUserDataFile($mioid);
?>