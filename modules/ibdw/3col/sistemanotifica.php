<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
mysql_query("SET NAMES 'utf8'");
$userid = (int)$_COOKIE['memberID'];
$richiesta=$_POST['richiestaamiciaz'];
$userid=$_POST['ottieniid'];
$scelta=$_POST['scelta'];
$nameformat=$_POST['nameformat'];

$valoriarray_s = getProfileInfo($richiesta);
$valoriarray_r = getProfileInfo($userid);
   
$array["sender_p_link"]= BX_DOL_URL_ROOT.$valoriarray_s['NickName'];
$array["recipient_p_link"]= BX_DOL_URL_ROOT.$valoriarray_r['NickName'];
if($nameformat==0) 
{
 $array["sender_p_nick"]= $valoriarray_s['NickName'];
 $array["recipient_p_nick"]= $valoriarray_r['NickName'];
}
elseif($nameformat==1) 
{
 $array["sender_p_nick"]= ucfirst($valoriarray_s['FirstName'])." ".ucfirst($valoriarray_s['LastName']);
 $array["recipient_p_nick"]= ucfirst($valoriarray_r['FirstName'])." ".ucfirst($valoriarray_r['LastName']);
}
elseif($nameformat==2) 
{
 $array["sender_p_nick"]= ucfirst($valoriarray_s['FirstName']);
 $array["recipient_p_nick"]= ucfirst($valoriarray_r['FirstName']);
}  
$aParams = serialize($array);
                            
  if ($scelta=="ok") 
  { 
   $querydecidi="UPDATE sys_friend_list SET sys_friend_list.Check = 1 WHERE ID =". $richiesta . " AND Profile=" . $userid;
   $resultdecidi = mysql_query($querydecidi) or die(mysql_error());
   $inseriscispy = "INSERT INTO `bx_spy_data` (sender_id,recipient_id,lang_key,params,type) VALUES ('$userid','$richiesta','_bx_spy_profile_friend_accept','$aParams','profiles_activity')";
   $esecuzione = mysql_query($inseriscispy);
  }
  elseif ($scelta=="no") 
  { 
   $querydecidi="DELETE FROM `sys_friend_list` WHERE Profile=" . $userid . " AND ID=" . $richiesta ;
   $resultdecidi = mysql_query($querydecidi) or die(mysql_error());
  }
?>