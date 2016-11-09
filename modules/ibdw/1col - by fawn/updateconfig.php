<?
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/1col/myconfig.php';
$userid = (int)$_COOKIE['memberID'];
if(!isAdmin()) { exit;}
mysql_query("SET NAMES 'utf8'");
$photo = $_POST['photo'];
$video = $_POST['video'];
$group = $_POST['group'];
$event = $_POST['event'];
$site = $_POST['site'];
$poll = $_POST['poll'];
$ads = $_POST['ads'];
$file = $_POST['file'];
$sound = $_POST['sound'];
$avatartype = $_POST['avatartype'];
$emailad = $_POST['emailad'];
$status = $_POST['status'];
$city = $_POST['city'];
$slide = $_POST['slide'];
$slidevelocity = $_POST['slidevelocity'];
$numbermaxfriend = $_POST['numbermaxfriend'];
$timereload = $_POST['timereload'];
$usernameformat = $_POST['nickname'];
$mainmenuvar = $_POST['mainmenuvar'];
//Ajout perso Market place
$marketvar = $_POST['marketvar'];
//fin
//Ajout perso Alertes
$alertevar = $_POST['alertevar'];
//fin
$mediavar = $_POST['mediavar'];
$acceditvar = $_POST['acceditvar'];
$onlinefriendvar = $_POST['onlinefriendvar'];
$deletebutton = $_POST['deletebutton'];
$avaset = $_POST['avaset'];
$privasett = $_POST['privasett'];
$sottoscrizione  = $_POST['sottoscrizione'];
$mailset = $_POST['mailset'];
$amiciset = $_POST['amiciset'];
if(trim($_POST['customlink_1']!='')){$customlink_1 = trim($_POST['customlink_1']);} else { $customlink_1 = ''; }
if(trim($_POST['customlink_2']!='')){$customlink_2 = trim($_POST['customlink_2']);} else { $customlink_2 = ''; }
if(trim($_POST['customlink_3']!='')){$customlink_3 = trim($_POST['customlink_3']);} else { $customlink_3 = ''; }
if(trim($_POST['customlink_4']!='')){$customlink_4 = trim($_POST['customlink_4']);} else { $customlink_4 = ''; }
if(trim($_POST['customlink_5']!='')){$customlink_5 = trim($_POST['customlink_5']);} else { $customlink_5 = ''; }
if(trim($_POST['customlinksect1']!='')){$customsect1 = trim($_POST['customlinksect1']);} else { $customsect1 = ''; }
if(trim($_POST['customlinksect2']!='')){$customsect2 = trim($_POST['customlinksect2']);} else { $customsect2 = ''; }
if(trim($_POST['customlinksect3']!='')){$customsect3 = trim($_POST['customlinksect3']);} else { $customsect3 = ''; }
if(trim($_POST['customlinksect4']!='')){$customsect4 = trim($_POST['customlinksect4']);} else { $customsect4 = ''; }
if(trim($_POST['customlinksect5']!='')){$customsect5 = trim($_POST['customlinksect5']);} else { $customsect5 = ''; }
if(trim($_POST['customlinksect6']!='')){$customsect6 = trim($_POST['customlinksect6']);} else { $customsect6 = ''; }
if(trim($_POST['customlinksect7']!='')){$customsect7 = trim($_POST['customlinksect7']);} else { $customsect7 = ''; }
if(trim($_POST['customlinksect8']!='')){$customsect8 = trim($_POST['customlinksect8']);} else { $customsect8 = ''; }
if(trim($_POST['customlinksect9']!='')){$customsect9 = trim($_POST['customlinksect9']);} else { $customsect9 = ''; }
if(trim($_POST['customlinksect10']!='')){$customsect10 = trim($_POST['customlinksect10']);} else { $customsect10 = ''; }
if(trim($_POST['customlinksect11']!='')){$customsect11 = trim($_POST['customlinksect11']);} else { $customsect11 = ''; }
if(trim($_POST['customlinksect12']!='')){$customsect12 = trim($_POST['customlinksect12']);} else { $customsect12 = ''; }
if(trim($_POST['customlinksect13']!='')){$customsect13 = trim($_POST['customlinksect13']);} else { $customsect13 = ''; }
if(trim($_POST['customlinksect14']!='')){$customsect14 = trim($_POST['customlinksect14']);} else { $customsect14 = ''; }
if(trim($_POST['customlinksect15']!='')){$customsect15 = trim($_POST['customlinksect15']);} else { $customsect15 = ''; }
$mailurl = addslashes($_POST['mailurl']);
$groupurl = addslashes($_POST['groupurl']);
$addgroupurl = addslashes($_POST['addgroupurl']);
$eventurl = addslashes($_POST['eventurl']);
$addeventurl = addslashes($_POST['addeventurl']);
$pollurl = addslashes($_POST['pollurl']);
$addpollurl = addslashes($_POST['addpollurl']);
$adsurl = addslashes($_POST['adsurl']);
$addadsurl = addslashes($_POST['addadsurl']);
$siteurl = addslashes($_POST['siteurl']);
$fileurl = addslashes($_POST['fileurl']);
$addfileurl = addslashes($_POST['addfileurl']);
$addsiteurl = addslashes($_POST['addsiteurl']);
$photourl = addslashes($_POST['photourl']);
$addphotourl = addslashes($_POST['addphotourl']);
$videourl = addslashes($_POST['videourl']);
$addvideourl = addslashes($_POST['addvideourl']);
$soundurl = addslashes($_POST['soundurl']);
$addsoundurl = addslashes($_POST['addsoundurl']);
$avatarurl = addslashes($_POST['avatarurl']);
//GIGS
$gigsserv = addslashes($_POST['gigsserv']);
$gigsvar = addslashes($_POST['gigsvar']);
$gigsurl = addslashes($_POST['gigsurl']);
//FIN GIGS
//STORE
$storeserv = addslashes($_POST['storeserv']);
$storevar = addslashes($_POST['storevar']);
$storeurl = addslashes($_POST['storeurl']);
//FIN STORE
//ARTICLES
$topicserv = addslashes($_POST['topicserv']);
$topicvar = addslashes($_POST['topicvar']);
$topicurl = addslashes($_POST['topicurl']);
//FIN ARTICLES
//TCHAT
$tchatvar = addslashes($_POST['tchatvar']);
$tchaturl = addslashes($_POST['tchaturl']);
//FIN TCHAT
//WALL
$wallvar = addslashes($_POST['wallvar']);
$wallurl = addslashes($_POST['wallurl']);
//FIN WALL
//BLOG
$blogvar = addslashes($_POST['blogvar']);
$blogurl = addslashes($_POST['blogurl']);
$addblogurl = addslashes($_POST['addblogurl']);
//FIN BLOG
//ALERT
$addalert=addslashes($_POST['addalert']);
$alertvar = addslashes($_POST['alertvar']);
$alerturl = addslashes($_POST['alerturl']);
//FIN ALERT

//CHECKBOXES
$Journalist1=addslashes($_POST['Journalist1']);
$Communicator1=addslashes($_POST['Communicator1']);
$Leader1=addslashes($_POST['Leader1']);
$Journalist2=addslashes($_POST['Journalist2']);
$Communicator2=addslashes($_POST['Communicator2']);
$Leader2=addslashes($_POST['Leader2']);
$Journalist3=addslashes($_POST['Journalist3']);
$Communicator3=addslashes($_POST['Communicator3']);
$Leader3=addslashes($_POST['Leader3']);
$Journalist4=addslashes($_POST['Journalist4']);
$Communicator4=addslashes($_POST['Communicator4']);
$Leader4=addslashes($_POST['Leader4']);
$Journalist5=addslashes($_POST['Journalist5']);
$Communicator5=addslashes($_POST['Communicator5']);
$Leader5=addslashes($_POST['Leader5']);
$Journalist6=addslashes($_POST['Journalist6']);
$Communicator6=addslashes($_POST['Communicator6']);
$Leader6=addslashes($_POST['Leader6']);
$Journalist7=addslashes($_POST['Journalist7']);
$Communicator7=addslashes($_POST['Communicator7']);
$Leader7=addslashes($_POST['Leader7']);
$Journalist8=addslashes($_POST['Journalist8']);
$Communicator8=addslashes($_POST['Communicator8']);
$Leader8=addslashes($_POST['Leader8']);
$Journalist9=addslashes($_POST['Journalist9']);
$Communicator9=addslashes($_POST['Communicator9']);
$Leader9=addslashes($_POST['Leader9']);
$Journalist10=addslashes($_POST['Journalist10']);
$Communicator10=addslashes($_POST['Communicator10']);
$Leader10=addslashes($_POST['Leader10']);
$Journalist11=addslashes($_POST['Journalist11']);
$Communicator11=addslashes($_POST['Communicator11']);
$Leader11=addslashes($_POST['Leader11']);
$Journalist12=addslashes($_POST['Journalist12']);
$Communicator12=addslashes($_POST['Communicator12']);
$Leader12=addslashes($_POST['Leader12']);
$Journalist13=addslashes($_POST['Journalist13']);
$Communicator13=addslashes($_POST['Communicator13']);
$Leader13=addslashes($_POST['Leader13']);
$Journalist14=addslashes($_POST['Journalist14']);
$Communicator14=addslashes($_POST['Communicator14']);
$Leader14=addslashes($_POST['Leader14']);
$Journalist15=addslashes($_POST['Journalist15']);
$Communicator15=addslashes($_POST['Communicator15']);
$Leader15=addslashes($_POST['Leader15']);
//FIN CHECKBOXES

//AJOUT DE COLONNES DANS LA DB A FAIRE !!! IMPORTANT !! POUR L'UPDATE DE 1 COL 
$inserimento = "UPDATE 1col_config SET Journalist1='".$Journalist1."',Communicator1 = '".$Communicator1."',Leader1='".$Leader1."',Journalist2='".$Journalist2."',Communicator2 = '".$Communicator2."',Leader2='".$Leader2."',Journalist3='".$Journalist3."',Communicator3 = '".$Communicator3."',Leader3='".$Leader3."',Journalist4='".$Journalist4."',Communicator4 = '".$Communicator4."',Leader4='".$Leader4."',Journalist5='".$Journalist5."',Communicator5 = '".$Communicator5."',Leader5='".$Leader5."',Journalist6='".$Journalist6."',Communicator6 = '".$Communicator6."',Leader6='".$Leader6."',Journalist7='".$Journalist7."',Communicator7 = '".$Communicator7."',Leader7='".$Leader7."',Journalist8='".$Journalist8."',Communicator8 = '".$Communicator8."',Leader8='".$Leader8."',Journalist9='".$Journalist9."',Communicator9 = '".$Communicator9."',Leader9='".$Leader9."',Journalist10='".$Journalist10."',Communicator10 = '".$Communicator10."',Leader10='".$Leader10."',Journalist11='".$Journalist11."',Communicator11 = '".$Communicator11."',Leader11='".$Leader11."',Journalist12='".$Journalist12."',Communicator12 = '".$Communicator12."',Leader12='".$Leader12."',Journalist13='".$Journalist13."',Communicator13 = '".$Communicator13."',Leader13='".$Leader13."',Journalist14='".$Journalist14."',Communicator14 = '".$Communicator14."',Leader14='".$Leader14."',Journalist15='".$Journalist15."',Communicator15 = '".$Communicator15."',Leader15='".$Leader15."',addblogurl = '".$addblogurl."',addalert = '".$addalert."',alerturl = '".$alerturl."',alertvar = '".$alertvar."',alertevar = '".$alertevar."',wallurl = '".$wallurl."',wallvar = '".$wallvar."',blogurl = '".$blogurl."',blogvar = '".$blogvar."',tchaturl = '".$tchaturl."',tchatvar = '".$tchatvar."',topicvar = '".$topicvar."',topicserv = '".$topicserv."',topicurl='".$topicurl."',storevar = '".$storevar."',storeserv = '".$storeserv."',storeurl='".$storeurl."',gigsvar = '".$gigsvar."',gigsserv = '".$gigsserv."',gigsurl='".$gigsurl."',foto='".$photo."',marketvar='".$marketvar."',addphotourl='".$addphotourl."',video='".$video."',addvideourl='".$addvideourl."',addsoundurl='".$addsoundurl."', gruppi='".$group."', eventi='".$event."', siti='".$site."', sondaggi='".$poll."', annunci='".$ads."', file='".$file."', suoni='".$sound."', avatartype='".$avatartype."', emailad='".$emailad."', status='".$status."', city='".$city."', slide='".$slide."',slidevelocity='".$slidevelocity."', numbermaxfriend='".$numbermaxfriend."', timereload='".$timereload."', usernameformat='".$usernameformat."', mainmenuvar='".$mainmenuvar."', mediavar='".$mediavar."', acceditvar='".$acceditvar."', onlinefriendvar='".$onlinefriendvar."', deletebutton='".$deletebutton."', mailurl='".$mailurl."', addgroupurl='".$addgroupurl."', eventurl='".$eventurl."', addeventurl='".$addeventurl."', pollurl='".$pollurl."', addpollurl='".$addpollurl."', adsurl='".$adsurl."', addadsurl='".$addadsurl."', siteurl='".$siteurl."', fileurl='".$fileurl."', addfileurl='".$addfileurl."', addsiteurl='".$addsiteurl."', photourl='".$photourl."', videourl='".$videourl."', soundurl='".$soundurl."', avatarurl='".$avatarurl."', groupurl='".$groupurl."' ,avaset='".$avaset."' ,privasett='".$privasett."' , sottoscrizione='".$sottoscrizione."' , mailset='".$mailset."' , customlink1 = '".$customlink_1."',customlink5 = '".$customlink_5."',customlink2 =  '".$customlink_2."',customlink3 = '".$customlink_3."',customlink4 = '".$customlink_4."',customsectn = '".$customsectn."',customsect1 = '".$customsect1."',customsect2 = '".$customsect2."',customsect3 = '".$customsect3."',customsect4 = '".$customsect4."',customsect5 = '".$customsect5."',customsect6 = '".$customsect6."',customsect7 = '".$customsect7."',customsect8 = '".$customsect8."',customsect9 = '".$customsect9."',customsect10 = '".$customsect10."',customsect11 = '".$customsect11."',customsect12 = '".$customsect12."',customsect13 = '".$customsect13."',customsect14 = '".$customsect14."',customsect15 = '".$customsect15."', amiciset='".$amiciset."'";
$resultquery = mysql_query($inserimento) or die(mysql_error());
?>
<style>
body, td, th {
}
a {
color:#000000;
text-decoration:none;
}
a:hover {
color:#FFFFFF;
text-decoration:none;
}
body  {
background:none repeat scroll 0 0 #334962;
font-family:Verdana;
font-size:11px;
margin:0;
text-align:center; 
}
#pagina  {
background:url("css/immagini/spyconfiglogo.png") no-repeat scroll 35px 22px #283B51;
border:7px solid #FFFFFF;
color:#FFFFFF;
height:1082px;
margin:30px auto auto;
padding:20px;
width:900px; }

#form_invio {
float:left;
font-size:15px;
line-height:34px;
margin-left:201px;
margin-top:44px;
width:500px;
}
#form_conferma {
float:left;
font-size:16px;
line-height:45px;
margin-left:225px;
margin-top:25px;
width:429px;
}
.title {
font-size:27px;
text-transform:uppercase;
}
.dett_activ {
color:#FFFFFF;
font-size:10px;
line-height:15px;
}
#introright {
float:right;
text-align:right;
}
#notifica {
color:#FFFFFF;
font-size:18px;
margin:135px;
}
#boxgeneraleconfigurazione  {
float:left;
margin-top:101px;
padding:20px;
text-align:left;
width:854px;
}
.introtitle {
font-size:17px;
font-weight:bold;
}
.introdesc  {
color:#5381E1;
font-size:11px;
font-style:italic;
}
#contentbox {
border:3px double #FFFFFF;
float:left;
line-height:15px;
margin:10px;
padding:10px;
width:365px; }

#return  {
border:1px solid #FFFFFF;
color:#FFFFFF;
font-size:15px;
height:31px;
line-height:27px;
width:315px;
margin-left:285px; }

#return:hover {
background:none repeat scroll 0 0 #999999;}

#return a { color:#FFF; }

</style>

<html>
<body>
  <div id="pagina">

  <div id="notifica">Update completed successfully</div>

    <div id="return"><a href="../../../<?php echo $admin_dir;?>"">Return to the main administration</a></div>  <br/>   <br/>
    <div id="return"><a href="configurazione.php">Return to the 1Col Configuration</a></div>
    </div>
</body>
</html>
