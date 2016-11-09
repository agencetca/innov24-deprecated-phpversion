<?php
include('../../../../inc/header.inc.php');
include('urlsite.php');

function estrainickname($id,$stile) { 
  $selezione = "SELECT NickName,FirstName,LastName FROM Profiles WHERE ID = $id";
  $esegui = mysql_query($selezione);
  $nickrow = mysql_fetch_assoc($esegui);
    if($stile == 0) { $nick = $nickrow['NickName'];  }
    elseif($stile == 1) { $nick = $nickrow['FirstName'].' '.$nickrow['LastName'];  }
    elseif($stile == 2) { $nick = $nickrow['FirstName'];  }
    return $nick;
  } 

//author of the spam
$iNickNameF = (int) $_POST['tofNick'];  
  
//author of the spam
$iMemberId = (int) $_POST['spammer'];
   
//reporter
$iProfileId = (int) $_POST['reporter'];
   
//action's id reported as spam
$actionId = (int) $_POST['id_action'];
   
//-- get reporter information --//
$aPlus = array();
$aPlus['reporterID'] = $iProfileId;
$aPlus['reporterNick'] = estrainickname($iProfileId,$iNickNameF);

//-- get spamer info --//
$aPlus['spamerID'] = $iMemberId;
$aPlus['spamerNick'] = estrainickname($iMemberId,$iNickNameF);

$selecttemplateemail="SELECT * FROM sys_email_templates WHERE Name='".t_SpamReport."'";
$ottienitemplate=mysql_query($selecttemplateemail);
$emailtemplate=mysql_fetch_assoc($ottienitemplate);

//Get site email
$emailsitequery="SELECT sys_options.VALUE FROM sys_options WHERE Name='site_email'";
$getemailinfo=mysql_query($emailsitequery);
$emailsite=mysql_fetch_assoc($getemailinfo);
$email_site=$emailsite['VALUE'];

//Get site name
$sitenamequery="SELECT sys_options.VALUE FROM sys_options WHERE Name='site_title'";
$getsitenameinfo=mysql_query($sitenamequery);
$getsitename=mysql_fetch_assoc($getsitenameinfo);
$sitename=$getsitename['VALUE'];

//replace var value with the currents in the body and in the subject
$emailtemplate['Body']=str_replace("<Domain>",$urlsite,$emailtemplate['Body']);
$emailtemplate['Body']=str_replace("<reporterNick>",$aPlus['reporterNick'],$emailtemplate['Body']);
$emailtemplate['Body']=str_replace("<reporterID>",$aPlus['reporterID'],$emailtemplate['Body']);
$emailtemplate['Body']=str_replace("<spamerID>",$aPlus['spamerID'],$emailtemplate['Body']);
$emailtemplate['Body']=str_replace("<spamerNick>",$aPlus['spamerNick'],$emailtemplate['Body']);
$emailtemplate['Subject']=str_replace("<SiteName>",$sitename,$emailtemplate['Subject']);

if( !sendMail( $email_site, $emailtemplate['Subject'], $emailtemplate['Body']) ) {return MsgBox( _t('_Report about spam failed to sent') );}
?>