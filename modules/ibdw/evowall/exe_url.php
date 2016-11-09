<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php' );
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';

function sostcomm($string) 
{ 
 $elabora=str_replace("execommerciale","&amp;",$string);
 return htmlentities($elabora);
}
$utente=(int)$_COOKIE['memberID'];
$nomesito=sostcomm($_POST['nomesito']);
$descrizione=html_entity_decode(sostcomm($_POST['descrizione']));
$descrizione=str_replace("\\","ibdwbackslash",$descrizione);
$indirizzo=$_POST['indirizzo'];
$immagine=$_POST['immagine'];
$messaggio=$_POST['message'];
$anteprimano=$_POST['anteprimano'];
mysql_query("SET NAMES 'utf8'");
$idprofile=$_POST['idprofile'];

//il nickname del sender
$aInfomember1=getProfileInfo($utente);



if($usernameformat=='Nickname') $sendernick=$aInfomember1['NickName'];
elseif($usernameformat=='FirstName') $sendernick=$aInfomember1['FirstName'];
elseif($usernameformat=='Full name') $sendernick=$aInfomember1['FirstName']." ".$aInfomember1['LastName'];
$senderlink=$aInfomember1['NickName'];


$array["sender_p_link"]=BX_DOL_URL_ROOT.$senderlink;
$array["sender_p_nick"]=$sendernick;
$array["titolosito"]=$nomesito;
$array["indirizzo"]=$indirizzo;
$array["descrizione"]=$descrizione;
$array["immagine"]=$immagine;
$array["messaggio"]=$messaggio;
$array["anteprimano"]=$anteprimano;
$str=serialize($array);


if ($utente==$idprofile) $query="INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params,type) VALUES (".$utente.",0,'_ibdw_evowall_bx_url_add','$str','profiles_activity')";
else $query="INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params,type) VALUES (".$utente.",".$idprofile.",'_ibdw_evowall_bx_url_add','$str','profiles_activity')";
$result=mysql_query($query);
?>