<?
/**********************************************************************************
*                            IBDW EvoWall for Dolphin Smart Community Builder
*                              -------------------
*     begin                : July 23 2012
*     copyright            : (C) 2012 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW EvoWall is not free and you cannot redistribute and/or modify it.
* 
* IBDW EvoWall is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php' );

class evowallDb extends BxDolModuleDb 
{
 var $_oConfig;
 var $sTablePrefix;
 
 /*
 Constructor
 */
 
 function evowallDb(&$oConfig) 
 {
  parent::BxDolModuleDb();
  $this -> _oConfig = $oConfig;
  $this -> sTablePrefix = $oConfig -> getDbPrefix();
 }

 function getSettingsCategory() 
 {
  return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'EVO Wall' LIMIT 1");
 }
 
 function removeInexComments()
 {
	   $this -> query("DELETE FROM `commenti_spy_data` WHERE `data` NOT IN (SELECT ID FROM `bx_spy_data`) OR `user` NOT IN (SELECT ID FROM `Profiles`)");
	   $this -> query("OPTIMIZE TABLE `commenti_spy_data`");	   
	   $this -> query("DELETE FROM `datacommenti` WHERE `IDCommento` NOT IN (SELECT id FROM `commenti_spy_data`)");
	   $this -> query("OPTIMIZE TABLE `datacommenti`");
	   $this -> query("DELETE FROM `ibdw_likethis` WHERE `id_utente` NOT IN (SELECT ID FROM `Profiles`) OR `id_utente`=''");
 }	
 
 function updateProfilePrivacy()
 {  
  $getprofileprivacydefault=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='DefaultProfilePrivacy'");
  $profileprivacyval=mysql_fetch_assoc($getprofileprivacydefault);
  
  if ($profileprivacyval['VALUE']=="Default") {$privacyfornewprofile=1;}
  elseif ($profileprivacyval['VALUE']=="Me Only") {$privacyfornewprofile=2;}
  elseif ($profileprivacyval['VALUE']=="Public") {$privacyfornewprofile=3;}
  elseif ($profileprivacyval['VALUE']=="Members") {$privacyfornewprofile=4;}
  elseif ($profileprivacyval['VALUE']=="Friends") {$privacyfornewprofile=5;}
  elseif ($profileprivacyval['VALUE']=="Faves") {$privacyfornewprofile=6;}
  
  mysql_query("UPDATE sys_privacy_actions SET default_group = ".$privacyfornewprofile." WHERE sys_privacy_actions.module_uri ='evowall'");
 }
 
 function updateSocialNetworks()
 {
  //FACEBOOK SHARING
  //get if facebook sharing is enabled
  $getsetvalfb=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowFacebook' and order_in_kateg=61");
  $getvaluefbs=mysql_fetch_assoc($getsetvalfb);
  if ($getvaluefbs['VALUE']=="on")
  {
    $getfball=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowfbshare'");
	$getiffbpres=mysql_num_rows($getfball);
	if ($getiffbpres==0) $addfboption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowfbshare', '_ibdw_evowall_facebook_share_p', '4');");
  }
  else
  {
    $getfball=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowfbshare'");
	$getiffbpres=mysql_num_rows($getfball);
	if ($getiffbpres==1) $addfboption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowfbshare' AND title='_ibdw_evowall_facebook_share_p');");
  }
  //END FACEBOOK SHARING
  
  //GOOGLE+ SHARING
  //get if google sharing is enabled
  $getsetvalgg=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowGoogle' and order_in_kateg=62");
  $getvalueggs=mysql_fetch_assoc($getsetvalgg);
  if ($getvalueggs['VALUE']=="on")
  {
    $getggall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowgoogleshare'");
	$getifggpres=mysql_num_rows($getggall);
	if ($getifggpres==0) $addggoption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowgoogleshare', '_ibdw_evowall_google_share_p', '4');");
  }
  else
  {
    $getggall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowgoogleshare'");
	$getifggpres=mysql_num_rows($getggall);
	if ($getifggpres==1) $addggoption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowgoogleshare' AND title='_ibdw_evowall_google_share_p');");
  }
  //END GOOGLE SHARING
  
  //TWITTER SHARING
  //get if twitter sharing is enabled
  $getsetvaltw=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowTwitter' and order_in_kateg=63");
  $getvaluetws=mysql_fetch_assoc($getsetvaltw);
  if ($getvaluetws['VALUE']=="on")
  {
    $gettwall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowtwshare'");
	$getiftwpres=mysql_num_rows($gettwall);
	if ($getiftwpres==0) $addtwoption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowtwshare', '_ibdw_evowall_twitter_share_p', '4');");
  }
  else
  {
    $gettwall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowtwshare'");
	$getiftwpres=mysql_num_rows($gettwall);
	if ($getiftwpres==1) $addtwoption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowtwshare' AND title='_ibdw_evowall_twitter_share_p');");
  }
  //END TWITTER SHARING
  
  //LINKEDIN SHARING
  //get if linkedin sharing is enabled
  $getsetvalli=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowLinkedIn' and order_in_kateg=64");
  $getvaluelis=mysql_fetch_assoc($getsetvalli);
  if ($getvaluelis['VALUE']=="on")
  {
    $getliall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowlinkedinshare'");
	$getiflipres=mysql_num_rows($getliall);
	if ($getiflipres==0) $addlioption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowlinkedinshare', '_ibdw_evowall_linkedin_share_p', '4');");
  }
  else
  {
    $getliall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowlinkedinshare'");
	$getiflipres=mysql_num_rows($getliall);
	if ($getiflipres==1) $addlioption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowlinkedinshare' AND title='_ibdw_evowall_linkedin_share_p');");
  }
  //END LINKEDIN SHARING
  
  //PINTEREST SHARING
  //get if pinterest sharing is enabled
  $getsetvalps=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowPinterest' and order_in_kateg=65");
  $getvaluepss=mysql_fetch_assoc($getsetvalps);
  if ($getvaluepss['VALUE']=="on")
  {
    $getpsall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowpinterestshare'");
	$getifpspres=mysql_num_rows($getpsall);
	if ($getifpspres==0) $addpsoption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowpinterestshare', '_ibdw_evowall_pinterest_share_p', '4');");
  }
  else
  {
    $getpsall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowpinterestshare'");
	$getifpspres=mysql_num_rows($getpsall);
	if ($getifpspres==1) $addpsoption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowpinterestshare' AND title='_ibdw_evowall_linkedin_share_p');");
  }
  //END PINTEREST SHARING  

  //BAIDU SHARING
  //get if baidu sharing is enabled
  $getsetvalbi=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowBaidu' and order_in_kateg=66");
  $getvaluebis=mysql_fetch_assoc($getsetvalbi);
  if ($getvaluebis['VALUE']=="on")
  {
    $getbiall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowbaidushare'");
	$getifbipres=mysql_num_rows($getbiall);
	if ($getifbipres==0) $addbioption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowbaidushare', '_ibdw_evowall_baidu_share_p', '4');");
  }
  else
  {
    $getbiall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowbaidushare'");
	$getifbipres=mysql_num_rows($getbiall);
	if ($getifbipres==1) $addbioption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowbaidushare' AND title='_ibdw_evowall_baidu_share_p');");
  }
  //END BAIDU SHARING 
  
  //WEIBO SHARING
  //get if weibo sharing is enabled
  $getsetvalwb=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowWeibo' and order_in_kateg=67");
  $getvaluewbs=mysql_fetch_assoc($getsetvalwb);
  if ($getvaluewbs['VALUE']=="on")
  {
    $getwball=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowweiboshare'");
	$getifwbpres=mysql_num_rows($getwball);
	if ($getifwbpres==0) $addwboption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowweiboshare', '_ibdw_evowall_weibo_share_p', '4');");
  }
  else
  {
    $getwball=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowweiboshare'");
	$getifwbpres=mysql_num_rows($getwball);
	if ($getifwbpres==1) $addwboption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowweiboshare' AND title='_ibdw_evowall_weibo_share_p');");
  }
  //END WEIBO SHARING 
  
  //QZONE SHARING
  //get if qzone sharing is enabled
  $getsetvalqz=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowQzone' and order_in_kateg=68");
  $getvalueqzs=mysql_fetch_assoc($getsetvalqz);
  if ($getvalueqzs['VALUE']=="on")
  {
    $getqzall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowqzoneshare'");
	$getifqzpres=mysql_num_rows($getqzall);
	if ($getifqzpres==0) $addqzoption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowqzoneshare', '_ibdw_evowall_weibo_share_p', '4');");
  }
  else
  {
    $getqzall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowqzoneshare'");
	$getifqzpres=mysql_num_rows($getqzall);
	if ($getifqzpres==1) $addqzoption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowqzoneshare' AND title='_ibdw_evowall_weibo_share_p');");
  }
  //END WEIBO SHARING 
  
 }
 
}
?>