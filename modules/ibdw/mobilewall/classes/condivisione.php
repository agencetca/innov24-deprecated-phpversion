<?php
require_once( '../../../../inc/header.inc.php' );
mysql_query("SET NAMES 'utf8'");

$sender=$_POST['sender'];
$lang=$_POST['lang'];
$idnotizia = $_POST['idnotizia']; 
echo $idnotizia;
echo $lang;
 if ($lang=="_bx_ads_added_spy") 
 {
  $recipient=$_POST['recipient'];
  $paramsurl=$_POST['paramsurl'];
  $paramscaption=$_POST['paramscaption'];
  $paramsdesc=$_POST['paramsdesc'];
  $paramsimg=$_POST['paramsimg'];
  $paramsprezzo=$_POST['paramsprezzo'];
  $params=$paramsurl.'##'.$paramscaption.'##'.$paramsdesc.'##'.$paramsimg.'##'.$paramsprezzo;
  $langs="_bx_ads_add_condivisione";
  $query="INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params) VALUES ('$sender','$recipient','$langs','$params')";
  $result=mysql_query($query);
 }
 if ($lang=="_bx_videos_spy_added") 
 {
  $tube=$_POST['youtube'];
  if($tube=="1") 
  {
   $paramsurl=$_POST['paramsurl'];
   $paramstitle=$_POST['paramstitle'];
   $paramsindirizzo=$_POST['paramsindirizzo'];
   $paramsdesc=$_POST['paramsdesc'];
   $params=$paramsurl.'##'.$paramstitle.'##'.$paramsindirizzo.'##'.$paramsdesc;
   $langs="_bx_videotube_add_condivisione";
   $query="INSERT INTO bx_spy_data (sender_id,lang_key,params) VALUES ('$sender','$langs','$params')";
   $result=mysql_query($query);
  }
  else 
  {
   $paramsurl=$_POST['paramsurl'];
   $paramstitle=$_POST['paramstitle'];
   $paramsindirizzo=$_POST['paramsindirizzo'];
   $paramsidvideo=$_POST['paramsidvideo'];
   $params=$paramsurl.'##'.$paramstitle.'##'.$paramsindirizzo.'##'.$paramsidvideo;
   $langs="_bx_videolocal_add_condivisione";
   $querycontrollo="SELECT * FROM RayVideoFiles WHERE ID='$paramsidvideo' AND Title='$paramstitle'";
   $resultquery=mysql_query($querycontrollo);
   $rowcontrollo=mysql_num_rows($resultquery);
   if ($rowcontrollo>0) 
   {
    $query="INSERT INTO bx_spy_data (sender_id,lang_key,params) VALUES ('$sender','$langs','$params')";
	$result=mysql_query($query);
   }
  }
 }
 if ($lang=="_bx_groups_spy_post") 
 {
  $paramsurl=$_POST['paramsurl'];
  $paramstitle=$_POST['paramstitle'];
  $paramsindi=$_POST['paramsindi'];
  $paramsext=$_POST['paramsext'];
  $paramsdesc=$_POST['paramsdesc'];
  $params=$paramsurl.'##'.$paramstitle.'##'.$paramsindi.'##'.$paramsext.'##'.$paramsdesc;
  $langs="_bx_gruppo_add_condivisione";
  $query="INSERT INTO bx_spy_data (sender_id,lang_key,params) VALUES ('$sender','$langs','$params')";
  $result=mysql_query($query);
 }
 if ($lang=="_bx_groups_spy_join") 
 {
  $paramsurl=$_POST['paramsurl'];
  $paramstitle=$_POST['paramstitle'];
  $paramsindi=$_POST['paramsindi'];
  $paramsext=$_POST['paramsext'];
  $paramsdesc=$_POST['paramsdesc'];
  $params=$paramsurl.'##'.$paramstitle.'##'.$paramsindi.'##'.$paramsext.'##'.$paramsdesc;
  $langs="_bx_gruppo_add_condivisione";
  $query="INSERT INTO bx_spy_data (sender_id,lang_key,params) VALUES ('$sender','$langs','$params')";
  $result=mysql_query($query);
 }
 if ($lang=="_bx_photos_spy_added") 
 {
  $paramsurl=$_POST['paramsurl'];
  $paramsext=$_POST['paramsext'];
  $paramstitle=$_POST['paramstitle'];
  $paramsindi=$_POST['paramsindirizzo'];
  $paramsdesc=$_POST['paramsdesc'];
  $paramsidfoto=$_POST['paramsidfoto'];
  $params=$paramsurl.'##'.$paramstitle.'##'.$paramsindi.'##'.$paramsext.'##'.$paramsdesc.'##'.$paramsidfoto;
  $querycontrollo="SELECT * FROM bx_photos_main WHERE ID='$paramsidfoto' AND Hash='$paramsurl'";
  $resultquery=mysql_query($querycontrollo);
  $rowcontrollo=mysql_num_rows($resultquery);
  if ($rowcontrollo>0) 
  {
   $langs="_bx_photo_add_condivisione";
   $query="INSERT INTO bx_spy_data (sender_id,lang_key,params) VALUES ('$sender','$langs','$params')";
   $result=mysql_query($query);
  }
 }
 if ($lang=="_bx_poll_added") 
 {
  $paramstitle=$_POST['paramstitle'];
  $paramsindi=$_POST['paramsindi'];
  $params=$paramstitle.'##'.$paramsindi;
  $langs="_bx_poll_add_condivisione";
  $query="INSERT INTO bx_spy_data (sender_id,lang_key,params) VALUES ('$sender','$langs','$params')";
  $result=mysql_query($query);
 }
 if ($lang=="_bx_sites_poll_add") 
 {
  $paramsurl=$_POST['paramsurl'];
  $paramstitle=$_POST['paramstitle'];
  $paramsimg=$_POST['paramsimg'];
  $paramsext=$_POST['paramsext'];
  $paramsdesc=$_POST['paramsdesc'];
  $params=$paramsurl.'##'.$paramstitle.'##'.$paramsimg.'##'.$paramsext.'##'.$paramsdesc;
  $langs="_bx_site_add_condivisione";
  $query="INSERT INTO bx_spy_data (sender_id,lang_key,params) VALUES ('$sender','$langs','$params')";
  $result=mysql_query($query);
 }
 if ($lang=="_bx_events_spy_post") 
 {
  $paramsurl=$_POST['paramsurl'];
  $paramstitle=$_POST['paramstitle'];
  $paramsimg=$_POST['paramsimg'];
  $paramsext=$_POST['paramsext'];
  $paramsdesc=$_POST['paramsdesc'];
  $params=$paramsurl.'##'.$paramstitle.'##'.$paramsimg.'##'.$paramsext.'##'.$paramsdesc;
  $langs="_bx_event_add_condivisione";
  $query="INSERT INTO bx_spy_data (sender_id,lang_key,params) VALUES ('$sender','$langs','$params')";
  $result=mysql_query($query);
 }
?>