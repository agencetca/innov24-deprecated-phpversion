<?
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
$userid = (int)$_COOKIE['memberID'];
if(!isAdmin()) { exit;}
mysql_query("SET NAMES 'utf8'");
		  $linkphoto = $_POST['linkphoto'];
          $linkvideo = $_POST['linkvideo'];
		  $linksound = $_POST['linksound'];
          $sndmessage = $_POST['sndmessage'];
          $greet = $_POST['greet'];
          $rpspam = $_POST['rpspam'];
          $friendblock = $_POST['friendblock'];
          $blockblock = $_POST['blockblock'];
          $subscribe = $_POST['subscribe'];
          $infoblock = $_POST['infoblock'];
          $descblock = $_POST['descblock'];
          $photoalbum = $_POST['photoalbum'];
          $videoalbum = $_POST['videoalbum'];
          $friend = $_POST['friend'];
          $mutualfr = $_POST['mutualfr'];
          $nfriend = $_POST['nfriend'];
          $nfriendm = $_POST['nfriendm'];
          $nalbum = $_POST['nalbum'];
          $usernameformat = $_POST['nickname'];
		  $namestm = $_POST['namestm'];
		  $crtdesk = $_POST['crtdesk'];
		  $frmdata = $_POST['frmdata'];
		  $albumdescr = $_POST['albumdescr'];
		  $webcam = $_POST['webcam'];
		  $custompro = $_POST['custompro'];
		  $favepro = $_POST['favepro'];
		  $relstatusview = $_POST['relstatusview'];
		  $datebirthview = $_POST['datebirthview'];
		  $infocityview = $_POST['infocityview'];
		  $headlineview = $_POST['headlineview'];
		  $emailview = $_POST['emailview'];
		  $sexview = $_POST['sexview'];
		  $lookingforview = $_POST['lookingforview'];
		  $occupationview = $_POST['occupationview'];
		  $religionview = $_POST['religionview'];
		  $setavatard = $_POST['setavatard'];
		  $agestyle = $_POST['agestyle'];
		  $maxsize = $_POST['maxsize'];
		  $friendsord = $_POST['friendsord'];
		  $defimage = $_POST['defimage'];
		  $mutualfriendord = $_POST['mutualfriendord'];
		  $typeofallowed = $_POST['typeofallowed'];
		  $reportspamtool = $_POST['reportspamtool'];
      $thumbtype= $_POST['thumbtype'];
          $inserimento = "UPDATE megaprofile_config SET maxsize='".$maxsize."',linkphoto='".$linkphoto."',linkvideo='".$linkvideo."',linksound='".$linksound."', 
		  			   	 sndmessage='".$sndmessage."', greet='".$greet."', rpspam='".$rpspam."', friendblock='".$friendblock."', 
						 blockblock='".$blockblock."', subscribe='".$subscribe."', infoblock='".$infoblock."', descblock='".$descblock."',
						 photoalbum='".$photoalbum."', videoalbum='".$videoalbum."', friend='".$friend."', mutualfr='".$mutualfr."', 
						 nfriend='".$nfriend."', nfriendm='".$nfriendm."', nalbum='".$nalbum."', usernameformat='".$usernameformat."', 
						 namestm='".$namestm."', crtdesk='".$crtdesk."', frmdata='".$frmdata."', albumdescr='".$albumdescr."', webcam='".$webcam."', 
						 custompro='".$custompro."', favepro='".$favepro."', relstatusview='".$relstatusview."', datebirthview='".$datebirthview."', 
						 infocityview='".$infocityview."', headlineview='".$headlineview."',defimage='".$defimage."', setavatard='".$setavatard."', emailview='".$emailview."', sexview='".$sexview."', 
						 lookingforview='".$lookingforview."', occupationview='".$occupationview."', agestyle='".$agestyle."', friendsord='".$friendsord."', mutualfriendord='".$mutualfriendord."', religionview='".$religionview."', typeofallowed='".$typeofallowed."', reportspamtool='".$reportspamtool."', thumbtype=".$thumbtype;
		  $resultquery = mysql_query($inserimento);
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
background:url("templates/base/images/spyconfiglogo.jpg") no-repeat scroll 11px 8px #283B51;
border:7px solid #FFFFFF;
color:#FFFFFF;
height:682px;
margin:20px auto;
padding:20px;
width:940px; }

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
line-height:15px;
margin:10px;
width:365px;


background-color: #4682B4;
border: 2px solid #FFFFFF;
    float: left;
    font-size: 10px;
    margin: 20px 0 10px;
    padding: 10px 0;

}

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
    <div id="return"><a href="configurazione.php">Return to the Megaprofile Configuration</a></div>
    </div>
</body>
</html>
