<?php
include('../../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
mysql_query("SET NAMES 'utf8'");
$invia=$_POST['invia'];
$riceve=$_POST['riceve'];
$messaggio=$_POST['messaggio'];
$messaggio = str_replace("xyapostrofo","&apos;",$messaggio);
$messaggio = str_replace("ecommerciale","&amp;",$messaggio);
$nickinvia=$_POST['nickinvia'];
$nickriceve=$_POST['nickriceve'];
$testopredefinito=_t("_ibdw_mobilewall_personalwall");
$messaggiovuoto=trim($_POST['messaggio']);
if($messaggio==$testopredefinito OR $messaggiovuoto == '') { exit();}	
$array["sender_p_link"] = $indirizzo.$nickinvia;
$array["sender_p_nick"] = $nickinvia;
$array["recipient_p_link"] = $indirizzo.$nickriceve;
$array["recipient_p_nick"] = $nickriceve;
$array["messaggioo"] = $messaggio;
$str = serialize($array);	
if($invia==$riceve) { $query  = "INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params,type) VALUES ('$invia','$riceve','_bx_spywall_messageseitu','$str','profiles_activity')";}
else { $query  = "INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params,type) VALUES ('$invia','$riceve','_bx_spywall_message','$str','profiles_activity')";}
$result = mysql_query($query);
?>