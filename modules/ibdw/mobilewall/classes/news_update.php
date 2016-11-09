<?php
require_once('../../../../inc/header.inc.php');
mysql_query("SET NAMES 'utf8'");
//AVVIO VARIABILI UTENTE
include('urlsite.php');
//INCLUSIONE FILE INDISPENSABILI
include('style_mobile.css');
include('config.php');
//configurazione gestiblie con l'amministrazione classica di spywall 
include('funzioni.php');
//query d'esecuzione
$inizioquery = 5;
$limite = 100;
 $userid = 43; $mioid = 43; 
include('masterquery.php');
$result = mysql_query($query) or die(mysql_error());
include('basecore.php');
?>