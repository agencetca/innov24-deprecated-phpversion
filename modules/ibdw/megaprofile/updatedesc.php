<?php
require_once( '../../../inc/header.inc.php' );
$text = strip_tags($_POST['testo'],'<br>');
$text = str_replace(array("\n","\r"),"<br>",$text);
$text = str_replace('"','&quot;',$text);
$text = str_replace("'","&#39;",$text);
$text = str_replace("ecommerciale","&",$text);
$id = $_POST['idu'];
$update = "UPDATE Profiles SET DescriptionMe = '$text' WHERE ID = '$id'";
$esegui = mysql_query($update);
?>