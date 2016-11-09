<?php
require_once( '../../../inc/header.inc.php' );
mysql_query("SET NAMES 'utf8'");
$verifica = "SELECT uri FROM sys_modules WHERE uri = 'ibdwemail'";
$exegui = mysql_query($verifica);
$num_ver = mysql_num_rows($exegui);

function readyshare($str)
{
 $str = str_replace('doppiequot','"',$str);         
 $str = str_replace('ecommercial','&',$str);    
 $str = str_replace('xeamp','&amp;',$str);
 return $str;
}

if($num_ver != 0) {include('../ibdwemail/email_notify.php');}
$bt_condivisione_params['1']=$_POST['1']; //Sender 
$bt_condivisione_params['2']=$_POST['2']; //Recipient
$bt_condivisione_params['3']=$_POST['3']; //Lang
$bt_condivisione_params['4']=readyshare($_POST['4']); //Params
$query="INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params) VALUES ('".$bt_condivisione_params['1']."','".$bt_condivisione_params['2']."','".$bt_condivisione_params['3']."','".$bt_condivisione_params['4']."')";
$result=mysql_query($query);
?>