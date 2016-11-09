<?php
include('../../../../inc/header.inc.php');
mysql_query("SET NAMES 'utf8'");
if($_POST['valx'] == '1') 
{
 //Controllo query
 $sql = "SELECT id_notizia FROM ibdw_likethis WHERE id_utente='".$_POST['user']."' AND id_notizia='".$_POST['id_like']."' AND ibdw_likethis.like=1";
 $exe = mysql_query($sql);
 $totale = mysql_num_rows($exe);
 if($totale == 0) { $query="INSERT INTO ibdw_likethis (id_notizia,id_utente,ibdw_likethis.like) VALUES ('".$_POST['id_like']."', '".$_POST['user']."', '1')"; }
}
elseif($_POST['valx'] == '0') { $query  = "DELETE FROM ibdw_likethis WHERE id_notizia='".$_POST['id_like']."' AND id_utente='".$_POST['user']."'"; }
$result = mysql_query($query);
?>