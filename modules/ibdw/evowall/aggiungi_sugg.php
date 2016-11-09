<?php
 require_once('../../../inc/header.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
 mysql_query("SET NAMES 'utf8'");
 $verifica="SELECT uri FROM sys_modules WHERE uri='ibdwemail'";
 $exegui=mysql_query($verifica);
 $num_ver=mysql_num_rows($exegui);
 if($num_ver!=0) {include('../ibdwemail/email_notify.php');}
 $accountid=(int)$_COOKIE['memberID'];
 $id_user=$_POST['id_user'];
 $query="INSERT INTO sys_friend_list (ID,Profile,sys_friend_list.Check) VALUES (".$accountid.",".$id_user.",0)";
 $resultquery=mysql_query($query);
 if($id_user!=$accountid AND $istruzione_5=='1' AND $num_ver!=0) {email_notify('richiesta_amicizia',$id_user,$accountid,'0');}
?>