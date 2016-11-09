<?php
include('../../../../inc/header.inc.php');
mysql_query("SET NAMES 'utf8'");
$id=$_POST['entry'];
$query2="DELETE FROM commenti_spy_data WHERE id=$id";
$resultquery2=mysql_query($query2) or die(mysql_error());
?>