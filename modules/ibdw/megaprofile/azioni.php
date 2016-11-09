<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );     
mysql_query("SET NAMES 'utf8'");
$userid=(int)$_COOKIE['memberID'];
if($_POST['tipo']=='delete') 
{ 
 $ottieniID=$_POST['id'];
 $proprietario=$_POST['profile'];
 $QueryCancella="DELETE FROM `sys_friend_list` WHERE (`ID`='$ottieniID' AND `Profile`='$proprietario') OR (`ID`='$proprietario' AND `Profile`='$ottieniID')";
 $risultatamitol=mysql_query($QueryCancella); 
}
elseif ($_POST['tipo']=='aggiungi') 
{
 $ottieniID=$_POST['id'];
 $proprietario=$_POST['profile'];  
 //verifico se l'altro utente mi ha fatto una richiesta d'amicizia
 $esegui="SELECT ID,Profile FROM sys_friend_list WHERE ID='$proprietario' AND Profile='$ottieniID'";
 $parti=mysql_query($esegui);
 $numero=mysql_num_rows($parti);
 if($numero!=0) 
 { 
  $QueryX="UPDATE sys_friend_list SET sys_friend_list.Check = 1 WHERE ID =".$proprietario." AND Profile =".$ottieniID." ";
  $risultaX=mysql_query($QueryX);
 }
 else 
 { 
  $esegui="INSERT INTO sys_friend_list (ID,Profile,sys_friend_list.Check) VALUES('$ottieniID','$proprietario','0')";
  $parti=mysql_query($esegui);  
  if($proprietario!=$userid) 
  {
   //invio email
   $senderemail=$ottieniID;
   $recipientemail=$proprietario;
   $protocol=strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https')=== FALSE ? 'http' : 'https';
   $pageaddress=$protocol."://".$_SERVER['HTTP_HOST']."/communicator.php?communicator_mode=friends_requests";
   bx_import('BxDolEmailTemplates');
   $oEmailTemplate = new BxDolEmailTemplates();
   $aTemplate = $oEmailTemplate -> getTemplate('t_FriendRequest');
   $infoamico=getProfileInfo($recipientemail);
   $aInfomembers=getProfileInfo($senderemail);
   $usermailadd=trim($infoamico['Email']);
   if($usernamem==0) 
   {
    $execactionname=$infoamico['NickName'];
    $authorname=$aInfomembers['NickName'];
   }
   elseif($usernamem==2) 
   {
    $execactionname=$infoamico['FirstName'];
    $authorname=$aInfomembers['FirstName'];
   }
   elseif($usernamem==1) 
   {
    $execactionname=$infoamico['FirstName']." ".$infoamico['LastName'];
    $authorname=$aInfomembers['FirstName']." ".$aInfomembers['LastName'];
   }
   $senderlinkis=$protocol."://".$_SERVER['HTTP_HOST']."/".$aInfomembers['NickName'];
   $sitenameis=getParam('site_title');
   $aTemplate['Body']=str_replace('<Sender>',$authorname,$aTemplate['Body']);
   $aTemplate['Body']=str_replace('<Recipient>',$execactionname,$aTemplate['Body']);
   $aTemplate['Body']=str_replace('<RequestLink>',$pageaddress,$aTemplate['Body']);
   $aTemplate['Body']=str_replace('<SenderLink>',$senderlinkis,$aTemplate['Body']);
   $aTemplate['Body']=str_replace('<SiteName>',$sitenameis,$aTemplate['Body']);
   if ($infoamico['EmailNotify']==1) sendMail($usermailadd, $aTemplate['Subject'], $aTemplate['Body'], $recipientemail, 'html');
   //fine invio email 
  }
 }
}
elseif($_POST['tipo']=='blocca')
{
 $ottieniID=$_POST['id'];
 $proprietario=$_POST['profile'];  
 $esegui="INSERT INTO sys_block_list (ID,Profile,sys_block_list.When) VALUES ('$ottieniID','$proprietario','CURRENT_TIMESTAMP')";
 $parti=mysql_query($esegui);
}
elseif($_POST['tipo']=='sblocca')
{
 $ottieniID=$_POST['id'];
 $proprietario=$_POST['profile'];  
 $QueryCancella="DELETE FROM `sys_block_list` WHERE (`ID`='$ottieniID' AND `Profile`='$proprietario') OR (`ID`='$proprietario' AND `Profile`='$ottieniID')";
 $risultatamitol=mysql_query($QueryCancella);
}
elseif($_POST['tipo']=='fave')
{
 $ottieniID=$_POST['id'];
 $proprietario=$_POST['profile'];  
 $esegui="INSERT INTO sys_fave_list (ID,Profile,sys_fave_list.When) VALUES ('$ottieniID','$proprietario','CURRENT_TIMESTAMP')";
 $parti=mysql_query($esegui);
}
?>
