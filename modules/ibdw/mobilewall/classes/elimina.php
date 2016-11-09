<?php
include('../../../../inc/header.inc.php');
mysql_query("SET NAMES 'utf8'");
$id=$_POST['entry'];
$query="SELECT * FROM bx_spy_data WHERE id=$id";
$resultquery=mysql_query($query) or die(mysql_error());
$rowquery=mysql_fetch_row($resultquery);
$querydx="DELETE FROM bx_spy_data WHERE id=$id";
$resultquerydx=mysql_query($querydx) or die(mysql_error());
$query2="DELETE FROM commenti_spy_data WHERE data=$id";
$resultquery2=mysql_query($query2) or die(mysql_error());
$query3="DELETE FROM ibdw_likethis WHERE id_notizia=$id AND typelement =''";
$resultquery3=mysql_query($query3) or die(mysql_error());
?>	  