<script>
function SetCookie(cookieName,cookieValue,nDays) 
{
 var today=new Date();
 var expire=new Date();
 if (nDays==null || nDays==0) nDays=1;
 expire.setTime(today.getTime()+3600000*24*nDays);
 document.cookie=cookieName+"="+escape(cookieValue)+";expires="+expire.toGMTString();
}
</script>
<?php
$paginadicontrollo=$_SERVER['PHP_SELF'];
$ajaxactive=strpos($paginadicontrollo,'member.php');
$ajaxactivehome=strpos($paginadicontrollo,'index.php');
if ($ajaxactive==0 AND $ajaxactivehome==0) 
{
 require_once('../../../inc/header.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
}

if (!defined('BX_AVA_EXT')) {

    define ('BX_AVA_DIR_USER_AVATARS', BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/data/images/'); // directory where avatar images are stored
    define ('BX_AVA_URL_USER_AVATARS', BX_DOL_URL_MODULES . 'boonex/avatar/data/images/'); // base url for all avatar images

    define ('BX_AVA_EXT', '.jpg'); // default avatar extension

    define ('BX_AVA_W', 64); // avatar image width
    define ('BX_AVA_H', 64); // avatar image height
    define ('BX_AVA_ICON_W', 32); // avatar icon width
    define ('BX_AVA_ICON_H', 32); // avatar icon height
}

include BX_DIRECTORY_PATH_MODULES.'ibdw/1col/myconfig.php';
$photodeluxe=0;
//chek photodeluxe installed or not
$verificaphotodeluxe="SELECT uri FROM sys_modules WHERE uri='photo_deluxe'";
$eseguiverificaphotodeluxe=mysql_query($verificaphotodeluxe);
$numerophotodeluxe=mysql_num_rows($eseguiverificaphotodeluxe);
if($numerophotodeluxe!=0) $photodeluxe=1;
if($photodeluxe==1)
{ 
 //check integration with photodeluxe and 1col 
 $integrazionepdx="SELECT integrazione1col FROM photodeluxe_config WHERE ind=1";
 $eseguiintregazionepdx=mysql_query($integrazionepdx);
 $rowintegrazionepdx=mysql_fetch_assoc($eseguiintregazionepdx);
 $attivaintegrazione=$rowintegrazionepdx['integrazione1col']; 
}
if($attivaintegrazione==1) $photourl = BX_DOL_URL_ROOT."page/photodeluxe";
$ottieniID=(int)$_COOKIE['memberID'];
$valoriutente=getProfileInfo($ottieniID);
$LinkUtente=getProfileLink($valoriutente['ID']);
$Miniatura=get_member_thumbnail($valoriutente['ID'],'none',false);
$MiaCitta=$valoriutente['City'];
$MioStato=$valoriutente['Status'];

$visitato=(int)$valoriutente['Views'];
require_once(BX_DIRECTORY_PATH_INC .'match.inc.php');
if($usernamem==0) $NomeUtente=$valoriutente['NickName'];
elseif($usernamem==1) $NomeUtente=ucfirst($valoriutente['FirstName'])." ".ucfirst($valoriutente['LastName']);
elseif($usernamem==2) $NomeUtente=ucfirst($valoriutente['FirstName']);
$sMessaggioStato='';
switch ($MioStato)
{
 case 'Unconfirmed':$sMessaggioStato=_t('_ibdw_1col_unconfirmed');break;
 case 'Approval':$sMessaggioStato=_t('_ibdw_1col_approval');break;
 case 'Active':$sMessaggioStato=_t('_ibdw_1col_active');break;
 case 'Rejected':$sMessaggioStato=_t('_ibdw_1col_rejected');break;
 case 'Suspended':$sMessaggioStato=_t('_ibdw_1col_suspended');break;
}
$MiaEmail=$valoriutente['Email'];
mysql_query("SET NAMES 'utf8'");
$queryverifica="SELECT uri FROM sys_modules WHERE uri='groups'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_group=mysql_num_rows($queryverifica_exe);
if ($groupsvar=="ON" AND $numero_ver_group !=0)
{ 
 //groups number
 $query="SELECT * FROM `bx_groups_main` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contagruppi=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='files'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_file=mysql_num_rows($queryverifica_exe);
if ($filesvar=="ON" AND $numero_ver_file!=0)
{ 
 //files number
 $query="SELECT * FROM `bx_files_main` WHERE `Owner`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contafile=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='photos'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_photo=mysql_num_rows($queryverifica_exe);
if ($photosvar=="ON" AND $numero_ver_photo!=0)
{
//photos number
 $query="SELECT * FROM `bx_photos_main` WHERE `Owner`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contaphoto=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='sounds'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_sound=mysql_num_rows($queryverifica_exe);
if ($soundsvar=="ON" AND $numero_ver_sound!=0)
{
//sounds number
 $query="SELECT * FROM `RayMp3Files` WHERE `Owner`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contasound=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='videos'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_video=mysql_num_rows($queryverifica_exe);
if ($videosvar=="ON" AND $numero_ver_video!=0)
{
//videos number
 $query="SELECT * FROM `RayVideoFiles` WHERE `Owner`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contavideo=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='articles'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_topic=mysql_num_rows($queryverifica_exe);
if ($topicvar=="ON" AND $numero_ver_topic!=0)
{
//articleproposals number
 $query="SELECT * FROM `modzzz_articles_main` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contatopic=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='store'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_store=mysql_num_rows($queryverifica_exe);
if ($storevar=="ON" AND $numero_ver_store!=0)
{
//store number
 $query="SELECT * FROM `bx_store_products` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contastore=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='gigs'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_gigs=mysql_num_rows($queryverifica_exe);
if ($gigsvar=="ON" AND $numero_ver_gigs!=0)
{
//gigs number
 $query="SELECT * FROM `modzzz_gigs_main` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contagigs=mysql_num_rows($result);
}
/*$queryverifica="SELECT uri FROM sys_modules WHERE uri='wall'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_wall=mysql_num_rows($queryverifica_exe);
if ($wallvar=="ON" AND $numero_ver_wall!=0)
{
//wall numbers
 $query="SELECT * FROM `bx_wall_events` WHERE `ID`="'.$ottieniID.'""; Trouver ou compter !!!
 $result=mysql_query($query);
 $contawall=mysql_num_rows($result);
}*/
$queryverifica="SELECT uri FROM sys_modules WHERE uri='blogs'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_blog=mysql_num_rows($queryverifica_exe);
if ($blogvar=="ON" AND $numero_ver_blog!=0)
{
//blog number
 $query="SELECT * FROM `bx_blogs_posts` WHERE `OwnerID`=".$ottieniID." AND `PostStatus`='approval'";
 $result=mysql_query($query);
 $contablog=mysql_num_rows($result);
}
//Alerts
$queryverifica="SELECT uri FROM sys_modules WHERE uri='alerts'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_alert=mysql_num_rows($queryverifica_exe);
if ($alertvar=="ON" AND $numero_ver_alert!=0)
{
//alerts number
 $query="SELECT * FROM `modzzz_alerts_main` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contaalert=mysql_num_rows($result);
}
//Tchat
$queryverifica="SELECT uri FROM sys_modules WHERE uri='chat'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_tchat=mysql_num_rows($queryverifica_exe);
if ($tchatvar=="ON" AND $numero_ver_tchat!=0)
{
//tchat room number
 $query="SELECT * FROM `RayChatCurrentUsers` WHERE `ID`=".$ottieniID." AND `Online`='online'";
 $result=mysql_query($query);
 $contatchat=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='events'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_event=mysql_num_rows($queryverifica_exe);
if ($evntvar=="ON" AND $numero_ver_event!=0)
{
 //events number
 $query="SELECT * FROM `bx_events_main` WHERE `ResponsibleID`=".$ottieniID." AND `Status`='approved'";
 $result=mysql_query($query);
 $contaeventi=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='sites'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_site=mysql_num_rows($queryverifica_exe);
if ($sitesvar=="ON" AND $numero_ver_site != 0)
{
 //sites number
 $query="SELECT * FROM `bx_sites_main` WHERE `ownerid`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contasiti=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='poll'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_poll=mysql_num_rows($queryverifica_exe);
if ($pollsvar=="ON" AND $numero_ver_poll!=0)
{
 //polls number
 $query="SELECT * FROM `bx_poll_data` WHERE `id_profile`=".$ottieniID." AND `poll_approval`=1";
 $result=mysql_query($query);
 $contasondaggi=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='ads'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_ads=mysql_num_rows($queryverifica_exe);
if ($adsvar=="ON" AND $numero_ver_ads!=0)
{
 //ads number
 $query="SELECT * FROM `bx_ads_main` WHERE `IDProfile`=".$ottieniID." AND `Status`='active'";
 $result=mysql_query($query);
 $contaannunci=mysql_num_rows($result);
}
//friends number
$query="SELECT * FROM `sys_friend_list` WHERE ((`Profile`=".$ottieniID." AND `Check`='1') OR (`ID`=".$ottieniID." AND `Check`='1'))";
$resultfriends=mysql_query($query);
$contaamici=mysql_num_rows($resultfriends);
//new messages
$query="SELECT * FROM `sys_messages` WHERE `Recipient`=".$ottieniID." AND `New`='1' AND NOT FIND_IN_SET('Recipient', `sys_messages`.`Trash`)";
$result=mysql_query($query);
$contanuovimessaggi=mysql_num_rows($result);
echo '<div><div class="menuelement1"><div class="infoutentecont"><div class="infoutente1">';
//Display the avatar standard or simplified
if ($avatartype=="standard") echo '<div class="mioavatarsta">'.$Miniatura;
else
{
 echo '<div class="mioavatar1col">';
 if ($valoriutente['Avatar']<>"0") echo '<a href="'.$LinkUtente.'"><img class="mioavat" src="'.BX_AVA_URL_USER_AVATARS.$valoriutente['Avatar'].BX_AVA_EXT.'"></a>';
 else 
 {
  if ($valoriutente['Sex']=="female") echo '<a href="'.$LinkUtente.'"><img class="mioavat" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/woman_medium.gif"></a>';
  else echo '<a href="'.$LinkUtente.'"><img class="mioavat" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/man_medium.gif"></a>';
 }
}
echo '</div>';
echo '<div>';
echo '<div class="spacer1"><a href="'.$LinkUtente.'">'.$NomeUtente.'</a></div><div style=""></div><div class="spacer2"><a href="pedit.php?ID=' . $ottieniID . '">'._t('_ibdw_1col_settings').'</a></div><div style=""></div>';
if($scity == 'ON') echo '<div class="spacer3">'.$MiaCitta.'</div>';
if ($avatartype=="standard") echo '<div class="spacer6"></div>';
if($status == 'ON')
{
 echo '<div class="spacer4">'._t('_ibdw_1col_status').' <b>'.$sMessaggioStato.'</b> ';
 ?>
 (<a onclick="javascript:window.open('explanation.php?explain=<?php echo $MioStato;?>','','width=660,height=200,menubar=no,status=no,resizable=no,scrollbars=yes,toolbar=no, location=no' );" href="javascript:void(0);"><?php echo _t('_ibdw_1col_expl');?></a>, <a href="change_status.php"><?php echo _t('_ibdw_1col_suspend');?></a>)</div><div style=""></div>
 <?php
}


if ($contanuovimessaggi>0) $spaziatoreemail='<div id="mailspace">';
else $spaziatoreemail ='<div class="centerspaceempty">';

if ($contagruppi>0) $spaziatoregroup='<div class="centerspace">';
else $spaziatoregroup ='<div class="centerspaceempty">';

if ($contaeventi>0) $spaziatoreevent='<div class="centerspace">';
else $spaziatoreevent ='<div class="centerspaceempty">';

if ($contaamici>0) $spaziatorefriend='<div class="centerspace">';
else $spaziatorefriend ='<div class="centerspaceempty">';

if ($contasondaggi>0) $spaziatorepoll='<div class="centerspace">';
else $spaziatorepoll ='<div class="centerspaceempty">';

if ($contaannunci>0) $spaziatoread='<div class="centerspace">';
else $spaziatoread ='<div class="centerspaceempty">';

if ($contasiti>0) $spaziatoresite='<div class="centerspace">';
else $spaziatoresite ='<div class="centerspaceempty">';

if ($contafile>0) $spaziatorefile='<div class="centerspace">';
else $spaziatorefile ='<div class="centerspaceempty">';

if ($contaphoto>0) $spaziatorephoto='<div class="centerspace">';
else $spaziatorephoto ='<div class="centerspaceempty">';

if ($contavideo>0) $spaziatorevideo='<div class="centerspace">';
else $spaziatorevideo ='<div class="centerspaceempty">';

if ($contasound>0) $spaziatoresound='<div class="centerspace">';
else $spaziatoresound ='<div class="centerspaceempty">';

if ($contagigs>0) $spaziatoregigs='<div class="centerspace">';
else $spaziatoregigs ='<div class="centerspaceempty">';

if ($contatchat>0) $spaziatoretchat='<div class="centerspace">';
else $spaziatoretchat ='<div class="centerspaceempty">';

if ($contastore>0) $spaziatorestore='<div class="centerspace">';
else $spaziatorestore ='<div class="centerspaceempty">';

if ($contatopic>0) $spaziatoretopic='<div class="centerspace">';
else $spaziatoretopic ='<div class="centerspaceempty">';

if ($contablog>0) $spaziatoreblog='<div class="centerspace">';
else $spaziatoreblog ='<div class="centerspaceempty">';

if ($contaalert>0) $spaziatorealert='<div class="centerspace">';
else $spaziatorealert ='<div class="centerspaceempty">';

/*if ($shemaila=="ON") echo '<div class="spacer5">'._t('_ibdw_1col_email'). ' '.$MiaEmail.'</div><div style=""></div>';*/
echo '</div></div><div style="clear:both;"></div>';

echo '<div class= "infoutente42">';
echo '<div class= "Points">';
$sql_points = "SELECT AqbPoints,ID FROM Profiles WHERE ID=".$ottieniID."";
$req_points = mysql_query($sql_points);
$result_points = mysql_fetch_assoc($req_points);
if($result_points['AqbPoints']!= null){
// echo 'pouet';
// echo $result_points['AqbPoints'].'&nbsp;'._t('_fawn_points_1col').'<br>';
  echo ''._t('_fawn_points_1col').'&nbsp;:&nbsp;'.$result_points['AqbPoints'].'<br>';
}
else{
echo '0&nbsp'._t('_fawn_points_1col').'<br>';
}
echo '</div>';

echo '<div class= "Credits">';
$sql_credits = "SELECT credits,member_id FROM modzzz_credit_credits WHERE member_id=".$ottieniID."";
$req_credits = mysql_query($sql_credits);
$result_credits = mysql_fetch_assoc($req_credits);
if($result_credits['credits'] != null){
echo $result_credits['credits'].'&nbsp;'._t('_fawn_credits_1col').'<br>';
}
else{
echo '0&nbsp'._t('_fawn_credits_1col').'<br>';
}
echo '</div>';
echo '</div>';
echo '</div>';

//if ($tchatvar=="ON") echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e14\').style.display = \'block\';" onmouseout="document.getElementById(\'e14\').style.display = \'none\';"><div class="leftspace"><a href="'.$tchaturl.'" class="titletchat1">'._t('_ibdw_1col_tchat').'</a></div>'.$spaziatoretchat.'<b>' . $contatchat . '</b></div></div><div class="rightspace" id="e14"></div>';


if ($mainmenuvar=="ON")
{
echo '<div id="rigamenuhome1"><a href="/m/imagenews/index/" class="titlehome1">'._t('_ibdw_1col_toolsmenu').'</a></div>';
 //Mettre section alerte ICI !!!
//  if ($alertevar=="ON")
//  {
//   echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e17\').style.display = \'block\';" onmouseout="document.getElementById(\'e17\').style.display = \'none\';"><div class="leftspace"><a href="'.$alerturl.'" class="titlealerte2">'._t('_ibdw_1col_newalert').'</a></div>'.$spaziatorealert.'<b>' . $contaalert . '</b></div><div class="rightspace" id="e17"><a href="'.$addalert.'">'._t('_ibdw_1col_newitem').'</a></div></div>';
//   }
  //Fin Alerte
 //  if ($topicvar=="ON") echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e13\').style.display = \'block\';" onmouseout="document.getElementById(\'e13\').style.display = \'none\';"><div class="leftspace"><a href="'.$topicurl.'" class="titletopic1">'._t('_ibdw_1col_topicproposals').'</a></div>'.$spaziatoretopic.'<b>' . $contatopic . '</b></div><div class="rightspace" id="e13"><a href="'.$topicserv.'">'._t('_ibdw_1col_newitem').'</a></div></div>'; 
  $sql_role_user = "SELECT ProfileType FROM Profiles WHERE ID=".$ottieniID."";
  $req_role_user = mysql_query($sql_role_user);
  $result_role_user = mysql_fetch_assoc($req_role_user);
  if($customnamesect1!='0' or $customnamesect2!='0' or $customnamesect3!='0' or $customnamesect4!='0' or $customnamesect5!='0' or $customnamesect6!='0' or $customnamesect7!='0' or $customnamesect8!='0' or $customnamesect9!='0' or $customnamesect10!='0' or $customnamesect11!='0' or $customnamesect12!='0' or $customnamesect13!='0' or $customnamesect14!='0' or $customnamesect15!='0') 
 { 
	if($result_role_user['ProfileType']==2){
		if($Journalist1=='ON'){
			if($customnamesect1!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect1.'" class="titlesect1">'._t('_ibdw_1col_customsect1').'</a></div>';
		}
		if($Journalist2=='ON'){
			if($customnamesect2!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect2.'" class="titlesect2">'._t('_ibdw_1col_customsect2').'</a></div>';
		}
		if($Journalist3=='ON'){	
			if($customnamesect3!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect3.'" class="titlesect3">'._t('_ibdw_1col_customsect3').'</a></div>';
		}
		if($Journalist4=='ON'){
			if($customnamesect4!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect4.'" class="titlesect4">'._t('_ibdw_1col_customsect4').'</a></div>';
		}
		if($Journalist5=='ON'){
			if($customnamesect5!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect5.'" class="titlesect5">'._t('_ibdw_1col_customsect5').'</a></div>';
		}
		if($Journalist6=='ON'){
			if($customnamesect6!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect6.'" class="titlesect6">'._t('_ibdw_1col_customsect6').'</a></div>';
		}
		if($Journalist7=='ON'){
			if($customnamesect7!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect7.'" class="titlesect7">'._t('_ibdw_1col_customsect7').'</a></div>';
		}
		if($Journalist8=='ON'){
			if($customnamesect8!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect8.'" class="titlesect8">'._t('_ibdw_1col_customsect8').'</a></div>';
		}
		if($Journalist9=='ON'){
			if($customnamesect9!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect9.'" class="titlesect9">'._t('_ibdw_1col_customsect9').'</a></div>';
		}
		if($Journalist10=='ON'){
			if($customnamesect10!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect10.'" class="titlesect10">'._t('_ibdw_1col_customsect10').'</a></div>';
		}
		if($Journalist11=='ON'){
			if($customnamesect11!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect11.'" class="titlesect11">'._t('_ibdw_1col_customsect11').'</a></div>';
		}
		if($Journalist12=='ON'){
			if($customnamesect12!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect12.'" class="titlesect12">'._t('_ibdw_1col_customsect12').'</a></div>';
		}
		if($Journalist13=='ON'){
			if($customnamesect13!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect13.'" class="titlesect13">'._t('_ibdw_1col_customsect13').'</a></div>';
		}
		if($Journalist14=='ON'){
			if($customnamesect14!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect14.'" class="titlesect14">'._t('_ibdw_1col_customsect14').'</a></div>';
		}
		if($Journalist15=='ON'){
			if($customnamesect15!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect15.'" class="titlesect15">'._t('_ibdw_1col_customsect15').'</a></div>';
		}
	}
	if($result_role_user['ProfileType']==4){
		if($Communicator1=='ON'){
			if($customnamesect1!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect1.'" class="titlesect1">'._t('_ibdw_1col_customsect1').'</a></div>';
		}
		if($Communicator2=='ON'){
			if($customnamesect2!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect2.'" class="titlesect2">'._t('_ibdw_1col_customsect2').'</a></div>';
		}
		if($Communicator3=='ON'){	
			if($customnamesect3!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect3.'" class="titlesect3">'._t('_ibdw_1col_customsect3').'</a></div>';
		}
		if($Communicator4=='ON'){
			if($customnamesect4!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect4.'" class="titlesect4">'._t('_ibdw_1col_customsect4').'</a></div>';
		}
		if($Communicator5=='ON'){
			if($customnamesect5!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect5.'" class="titlesect5">'._t('_ibdw_1col_customsect5').'</a></div>';
		}
		if($Communicator6=='ON'){
			if($customnamesect6!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect6.'" class="titlesect6">'._t('_ibdw_1col_customsect6').'</a></div>';
		}
		if($Communicator7=='ON'){
			if($customnamesect7!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect7.'" class="titlesect7">'._t('_ibdw_1col_customsect7').'</a></div>';
		}
		if($Communicator8=='ON'){
			if($customnamesect8!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect8.'" class="titlesect8">'._t('_ibdw_1col_customsect8').'</a></div>';
		}
		if($Communicator9=='ON'){
			if($customnamesect9!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect9.'" class="titlesect9">'._t('_ibdw_1col_customsect9').'</a></div>';
		}
		if($Communicator10=='ON'){
			if($customnamesect10!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect10.'" class="titlesect10">'._t('_ibdw_1col_customsect10').'</a></div>';
		}
		if($Communicator11=='ON'){
			if($customnamesect11!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect11.'" class="titlesect11">'._t('_ibdw_1col_customsect11').'</a></div>';
		}
		if($Communicator12=='ON'){
			if($customnamesect12!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect12.'" class="titlesect12">'._t('_ibdw_1col_customsect12').'</a></div>';
		}
		if($Communicator13=='ON'){
			if($customnamesect13!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect13.'" class="titlesect13">'._t('_ibdw_1col_customsect13').'</a></div>';
		}
		if($Communicator14=='ON'){
			if($customnamesect14!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect14.'" class="titlesect14">'._t('_ibdw_1col_customsect14').'</a></div>';
		}
		if($Communicator15=='ON'){
			if($customnamesect15!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect15.'" class="titlesect15">'._t('_ibdw_1col_customsect15').'</a></div>';
		}
	}
	if($result_role_user['ProfileType']==8){
		if($Leader1=='ON'){
			if($customnamesect1!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect1.'" class="titlesect1">'._t('_ibdw_1col_customsect1').'</a></div>';
		}
		if($Leader2=='ON'){
			if($customnamesect2!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect2.'" class="titlesect2">'._t('_ibdw_1col_customsect2').'</a></div>';
		}
		if($Leader3=='ON'){	
			if($customnamesect3!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect3.'" class="titlesect3">'._t('_ibdw_1col_customsect3').'</a></div>';
		}
		if($Leader4=='ON'){
			if($customnamesect4!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect4.'" class="titlesect4">'._t('_ibdw_1col_customsect4').'</a></div>';
		}
		if($Leader5=='ON'){
			if($customnamesect5!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect5.'" class="titlesect5">'._t('_ibdw_1col_customsect5').'</a></div>';
		}
		if($Leader6=='ON'){
			if($customnamesect6!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect6.'" class="titlesect6">'._t('_ibdw_1col_customsect6').'</a></div>';
		}
		if($Leader7=='ON'){
			if($customnamesect7!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect7.'" class="titlesect7">'._t('_ibdw_1col_customsect7').'</a></div>';
		}
		if($Leader8=='ON'){
			if($customnamesect8!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect8.'" class="titlesect8">'._t('_ibdw_1col_customsect8').'</a></div>';
		}
		if($Leader9=='ON'){
			if($customnamesect9!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect9.'" class="titlesect9">'._t('_ibdw_1col_customsect9').'</a></div>';
		}
		if($Leader10=='ON'){
			if($customnamesect10!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect10.'" class="titlesect10">'._t('_ibdw_1col_customsect10').'</a></div>';
		}
		if($Leader11=='ON'){
			if($customnamesect11!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect11.'" class="titlesect11">'._t('_ibdw_1col_customsect11').'</a></div>';
		}
		if($Leader12=='ON'){
			if($customnamesect12!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect12.'" class="titlesect12">'._t('_ibdw_1col_customsect12').'</a></div>';
		}
		if($Leader13=='ON'){
			if($customnamesect13!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect13.'" class="titlesect13">'._t('_ibdw_1col_customsect13').'</a></div>';
		}
		if($Leader14=='ON'){
			if($customnamesect14!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect14.'" class="titlesect14">'._t('_ibdw_1col_customsect14').'</a></div>';
		}
		if($Leader15=='ON'){
			if($customnamesect15!='0') echo '<div id="rigamenu1"><a href="'.$customnamesect15.'" class="titlesect15">'._t('_ibdw_1col_customsect15').'</a></div>';
		}
	}
  }	
  
  
//MARKET PLACE
	if ($marketvar=="ON")
{
	echo '<div id="rigamenumarket1"> <a href="/page/Marketplace" class="titlemarket1">'._t('_ibdw_1col_market').'</a></div>';
  if ($adsvar=="ON" AND $numero_ver_ads!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e5\').style.display = \'block\';" onmouseout="document.getElementById(\'e5\').style.display = \'none\';"><div class="leftspace"><a href="'.$adsurl.'" class="titleannunci1">'._t('_ibdw_1col_ads').'</a></div>'.$spaziatoread.'<b>' . $contaannunci . '</b></div><div class="rightspace" id="e5"><a href="'.$addadsurl.'">'._t('_ibdw_1col_ad_insert').'</a></div></div>';
	if ($gigsvar=="ON") echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e11\').style.display = \'block\';" onmouseout="document.getElementById(\'e11\').style.display = \'none\';"><div class="leftspace"><a href="'.$gigsurl.'" class="titlegigs1">'._t('_ibdw_1col_gigs').'</a></div>'.$spaziatoregigs.'<b>' . $contagigs . '</b></div><div class="rightspace" id="e11"><a href="'.$gigsserv.'">'._t('_ibdw_1col_newitem').'</a></div></div>';
	if ($storevar=="ON") echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e12\').style.display = \'block\';" onmouseout="document.getElementById(\'e12\').style.display = \'none\';"><div class="leftspace"><a href="'.$storeurl.'" class="titlestore1">'._t('_ibdw_1col_store').'</a></div>'.$spaziatorestore.'<b>' . $contastore . '</b></div><div class="rightspace" id="e12"><a href="'.$storeserv.'">'._t('_ibdw_1col_store_newitem').'</a></div></div>';
}
//MARKET PLACE
 }

 if($slideotherinfo=="ON")
 {

 /*if($cs1 != '0') echo '<div id="rigamenu1"><a href="'.$cs1.'" class="titlelink1">'._t('_ibdw_1col_customlink1').'</a></div>'; 
 if($cs2 != '0') echo '<div id="rigamenu1"><a href="'.$cs2.'" class="titlelink2">'._t('_ibdw_1col_customlink2').'</a></div>';
 if($cs3 != '0') echo '<div id="rigamenu1"><a href="'.$cs3.'" class="titlelink3">'._t('_ibdw_1col_customlink3').'</a></div>';
 if($cs4 != '0') echo '<div id="rigamenu1"><a href="'.$cs4.'" class="titlelink4">'._t('_ibdw_1col_customlink4').'</a></div>';
 if($cs5 != '0') echo '<div id="rigamenu1"><a href="'.$cs5.'" class="titlelink5">'._t('_ibdw_1col_customlink5').'</a></div>';*/
 
 /*if ($filesvar=="ON" AND $numero_ver_file!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e7\').style.display = \'block\';" onmouseout="document.getElementById(\'e7\').style.display = \'none\';"><div class="leftspace"><a href="'.$fileurl.'" class="titlefile1">'._t('_ibdw_1col_files').'</a></div>'.$spaziatorefile.'<b>' . $contafile . '</b></div><div class="rightspace" id="e7"><a href="'.$addfileurl.'">'._t('_ibdw_1col_site_ins').'</a></div></div>';*/
 
  echo '<div id="ibdw_altrosty" class="ibdw_bottid1" ';
  if($_COOKIE['slidedown']==1) echo 'style="display:none;"'; 
  echo '><a href="javascript:ibdw_mostra();">'._t('_ibdw_1col_altrobottone').'</a></div>
  <script>
  function ibdw_mostra()
  { 
   $(".ibdw_onecol_altro").slideDown('.$velocityslide.');
   $(".ibdw_bottid1").fadeOut(10);
   $(".colsep").fadeOut(10);
   SetCookie("slidedown","1","1");
  }
  </script>';
  echo '<div class="ibdw_onecol_altro"';
  if($_COOKIE['slidedown']==1) echo 'style="display:block"';
  }
 if ($slideotherinfo=="ON") echo'>';

 echo '<div id="rigamenumedia1"><div class="titlemedia1">'._t('_ibdw_1col_media').'</div></div>';
 
 if ($mediavar=="ON")
 {

 if ($wallvar=="ON") echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e15\').style.display = \'block\';" onmouseout="document.getElementById(\'e15\').style.display = \'none\';"><div class="leftspace"><a href="'.$wallurl.'" class="titlewall1">'._t('_ibdw_1col_wall').'</a></div></div>';
 if ($blogvar=="ON") echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e16\').style.display = \'block\';" onmouseout="document.getElementById(\'e16\').style.display = \'none\';"><div class="leftspace"><a href="'.$blogurl.'" class="titleblog1">'._t('_ibdw_1col_blog').'</a></div>'.$spaziatoreblog.'<b>' . $contablog . '</b></div><div class="rightspace" id="e16"><a  href="'.$addblogurl.'">'._t('_ibdw_1col_blog_add').'</a></div></div>';
 if ($groupsvar=="ON" AND $numero_ver_group!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e2\').style.display = \'block\';" onmouseout="document.getElementById(\'e2\').style.display = \'none\';"><div class="leftspace"><a href="'.$groupurl.'" class="titlegruppi1">'._t('_ibdw_1col_groups').'</a></div>'.$spaziatoregroup.'<b>' . $contagruppi . '</b></div><div class="rightspace" id="e2"><a  href="'.$addgroupurl.'">'._t('_ibdw_1col_group_make').'</a></div></div>';
 if ($evntvar=="ON" AND $numero_ver_event!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e3\').style.display = \'block\';" onmouseout="document.getElementById(\'e3\').style.display = \'none\';"><div class="leftspace"><a href="'.$eventurl.'" class="titleeventi1">'._t('_ibdw_1col_events').'</a></div>'.$spaziatoreevent.'<b>' . $contaeventi . '</b></div><div class="rightspace" id="e3"><a href="'.$addeventurl.'">'._t('_ibdw_1col_event_add').'</a></div></div>';
 if ($pollsvar=="ON" AND $numero_ver_poll!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e4\').style.display = \'block\';" onmouseout="document.getElementById(\'e4\').style.display = \'none\';"><div class="leftspace"><a href="'.$pollurl.'" class="titlesondaggi1">'._t('_ibdw_1col_polls').'</a></div>'.$spaziatorepoll.'<b>' . $contasondaggi . '</b></div><div class="rightspace" id="e4"><a href="'.$addpollurl.'">'._t('_ibdw_1col_poll_add').'</a></div></div>';
 if ($sitesvar=="ON" AND $numero_ver_site!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e6\').style.display = \'block\';" onmouseout="document.getElementById(\'e6\').style.display = \'none\';"><div class="leftspace"><a href="'.$siteurl.'" class="titlesiti1">'._t('_ibdw_1col_sites').'</a></div>'.$spaziatoresite.'<b>' . $contasiti . '</b></div><div class="rightspace" id="e6"><a href="'.$addsiteurl.'">'._t('_ibdw_1col_site_ins').'</a></div></div>';

  //EX DEBUT DE MEDIA
  if ($photosvar=="ON") echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e8\').style.display = \'block\';" onmouseout="document.getElementById(\'e8\').style.display = \'none\';"><div class="leftspace"><a href="'.$photourl.'" class="titlefoto1">'._t('_ibdw_1col_photos').'</a></div>'.$spaziatorephoto.'<b>' . $contaphoto . '</b></div><div class="rightspace" id="e8"><a href="'.$addphotourl.'">'._t('_ibdw_1col_album').'</a></div></div>';
  if ($videosvar=="ON") echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e9\').style.display = \'block\';" onmouseout="document.getElementById(\'e9\').style.display = \'none\';"><div class="leftspace"><a href="'.$videourl.'" class="titlevideo1">'._t('_ibdw_1col_videos').'</a></div>'.$spaziatorevideo.'<b>' . $contavideo . '</b></div><div class="rightspace" id="e9"><a href="'.$addvideourl.'">'._t('_ibdw_1col_album').'</a></div></div>';
  if ($soundsvar=="ON") echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e10\').style.display = \'block\';" onmouseout="document.getElementById(\'e10\').style.display = \'none\';"><div class="leftspace"><a href="'.$soundurl.'" class="titlesound1">'._t('_ibdw_1col_sound').'</a></div>'.$spaziatoresound.'<b>' . $contasound . '</b></div><div class="rightspace" id="e10"><a href="'.$addsoundurl.'">'._t('_ibdw_1col_album').'</a></div></div>';
  if ($filesvar=="ON" AND $numero_ver_file!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e7\').style.display = \'block\';" onmouseout="document.getElementById(\'e7\').style.display = \'none\';"><div class="leftspace"><a href="'.$fileurl.'" class="titlefile1">'._t('_ibdw_1col_files').'</a></div>'.$spaziatorefile.'<b>' . $contafile . '</b></div><div class="rightspace" id="e7"><a href="'.$addfileurl.'">'._t('_ibdw_1col_site_ins').'</a></div></div>';
 }
 
 if ($accounteditvar=="ON")
 {
  echo '<div id="rigamenumodifica1"><div class="titlemodifica1">'._t('_ibdw_1col_account_set').'</div></div>';
  if ($mailset=="ON") echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e1\').style.display = \'block\';" onmouseout="document.getElementById(\'e1\').style.display = \'none\';"><div class="leftspace"><a href="'.$mailurl.'" class="titleposta1">'._t('_ibdw_1col_mail').'</a></div>'.$spaziatoreemail.'<b>' . $contanuovimessaggi . '</b></a></div><div class="rightspace" id="e1"><a href="mail.php?mode=compose">'._t('_ibdw_1col_write').'</a></div></div>';
  if ($amiciset=="ON") echo '<div id="rigamenu1"><div class="leftspace"><a href="viewFriends.php?iUser=' . $ottieniID . '" class="titleamici1">'._t('_ibdw_1col_friends').'</a></div>'.$spaziatorefriend.'<b>' . $contaamici . '</b></div></div>';
  if($avaset=='ON') echo '<div id="rigamenu1"><a href="'.$avatarurl.'" class="titleavatar1">'._t('_ibdw_1col_avatar').'</a></div>';
  if($privasett=='ON') echo '<div id="rigamenu1"><a href="member_privacy.php" class="titleprivacy1">'._t('_ibdw_1col_privacy').'</a></div>';
  if($sottoscrizione=='ON') echo '<div id="rigamenu1"><a href="member_subscriptions.php" class="titleiscrizioni1">'._t('_ibdw_1col_subscr').'</a></div>';
  if($deleteaccount=='ON') echo '<div id="rigamenu1"><a href="unregister.php" class="titlecancellati1">'._t('_ibdw_1col_del').'</a></div>';
 }
 if($slideotherinfo == "ON") 
 { 
  echo '<div id="ibdw_altrosty"><a href="javascript:ibdw_nasco();">'._t('_ibdw_1col_altroriduci').'</a></div>
        <script>
        function ibdw_nasco()
		{ 
         $(".ibdw_onecol_altro").slideUp('.$velocityslide.');
         $(".ibdw_bottid1").fadeIn(10);
         $(".colsep").fadeIn(10);
         SetCookie("slidedown","0","1");
        }
        </script> 
        </div>';
 }
 echo '</div></div>';
 if (($sonlinefriends=="ON") and (get_user_online_status($ottieniID)==1))
 {
  $contatoreb=0;
  for($contatore=0;$contatore<$contaamici;$contatore++)
  {
   $listaamici=mysql_fetch_array($resultfriends);
   if ($listaamici[0]<>$ottieniID) $amico= $listaamici[0];
   else $amico= $listaamici[1];
   $stato=get_user_online_status($amico);
   if ($stato==1)
   {
	$contatoreb++;
	if ($contatoreb==1) echo '<div class="recallbg"><div id="rigamenufriends1"><div class="titlefriends">'._t('_ibdw_1col_onlinefriends').'</div></div></div><div id="menuelementfriends1">';
	$infoamico=getProfileInfo($amico);
    $Miniaturaamico=get_member_icon($infoamico['ID'],'none',false);
	if($usernamem==0) $NomeAmico=$infoamico['NickName'];
	elseif($usernamem==1) $NomeAmico=ucfirst($infoamico['FirstName'])." ".ucfirst($infoamico['LastName']);
	elseif($usernamem==2) $NomeAmico=ucfirst($infoamico['FirstName']);
    echo '<div id="rigamenuamico1"><div class="mioavatarsmall1">';
	//visualizzo l'avatar in stile dolphin oppure semplice
	if ($avatartype=="standard") echo $Miniaturaamico;
	else
	{
 	 if ($infoamico['Avatar']<>"0") echo '<a href="'.getProfileLink($infoamico['ID']).'"><img class="mioavatsmall" src="'.BX_AVA_URL_USER_AVATARS.$infoamico['Avatar'].BX_AVA_EXT.'"></a>';
	 else 
	  {
	   if ($infoamico['Sex']=="female") echo '<a href="'.getProfileLink($infoamico['ID']).'"><img class="mioavatsmall" src="/templates/base/images/icons/woman_medium.gif"></a>';
	   else echo '<a href="'.getProfileLink($infoamico['ID']).'"><img class="mioavatsmall" src="/templates/base/images/icons/man_medium.gif"></a>';
	  }
	}
	echo '</div><div class="mioutentesmall1"><div class="nameof"><a href="'.getProfileLink($infoamico['ID']).'">'.$NomeAmico.'</a></div><input type="text" class="miachat" onkeyup="if ( typeof oSimpleMessenger != \'undefined\' ){oSimpleMessenger.sendMessage(event, this, ' . $infoamico['ID'] . ')}" onclick="stopaggiornamento();this.value=\'\';" onblur="aggiornajx();" value="'._t('_ibdw_1col_chat_with') . " " .$NomeAmico . '" name="status_message"></div></div>';
   }
   if($contatoreb==$maxnumberonlinef) break;
  }
  if ($contatoreb>0) echo "</div>"; 
}
else {}
?>