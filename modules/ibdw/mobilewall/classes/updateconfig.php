<?
require_once( '../../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

//queste due righe successive sembrano ormai inutili
include BX_DIRECTORY_PATH_MODULES.'ibdw/mobilewall/classes/config.php';
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
$datava = $_POST['datava'];
$viewpro = $_POST['viewpro'];
$uppro = $_POST['uppro'];
$nickname = $_POST['nickname'];
$offset = $_POST['offset'];
$shareact = $_POST['shareact'];
$likeact = $_POST['likeact'];
$commentact = $_POST['commentact'];
$limite = $_POST['limite'];
                    
$inserimento="UPDATE mobilewall_config SET foto='".$photo."',video='".$video."', gruppi='".$group."', eventi='".$event."', siti='".$site."', sondaggi='".$poll."', annunci='".$ads."', formatodata='".$datava."', limite='".$limite."', offset='".$offset."',shareact='".$shareact."',likeact='".$likeact."',commentact='".$commentact."', spywallprofileview='".$viewpro."', profileupdate='".$uppro."',usernameformat='".$nickname."'";
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
background:url("../templates/base/images/mobile_config_logo.jpg") no-repeat scroll 35px 22px #283B51;
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

  <div id="notifica"><?php echo _t("_ibdw_mobilewall_updater");?></div>

    <div id="return"><a href="../../../../<?php echo $admin_dir;?>"><?php echo _t("_ibdw_mobilewall_backadmin");?></a></div>  <br/>   <br/>
    <div id="return"><a href="<?php echo BX_DOL_URL_MODULES;?>ibdw/mobilewall/classes/configurazione.php">Return to Mobilewall Configuration</a></div>
    </div>
</body>
</html>
