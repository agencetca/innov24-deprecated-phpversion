<!--Appel du Jquery-->
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="jquery.vticker.min.js"></script>
<script>
jQuery(document).ready(function(){
  setInterval(function(){     
            $("#com1").load("newsslider.php #com1");
			$("#com2").load("newsslider.php #com2");
			$("#com3").load("newsslider.php #com3");
			$("#com4").load("newsslider.php #com4");
			$("#com5").load("newsslider.php #com5");
			$("#com6").load("newsslider.php #com6");
			//alert("lol");
			//images
			$("#img1").load("imagetri.php #img1");
			$("#img2").load("imagetri.php #img2");
			$("#img3").load("imagetri.php #img3");
			$("#img4").load("imagetri.php #img4");
			$("#img5").load("imagetri.php #img5");
			$("#img6").load("imagetri.php #img6");
			$("#img7").load("imagetri.php #img7");
			$("#img8").load("imagetri.php #img8");
			$("#img9").load("imagetri.php #img9");
			$("#img10").load("imagetri.php #img10");
			$("#img11").load("imagetri.php #img11");
			$("#img12").load("imagetri.php #img12");
			$("#img13").load("imagetri.php #img13");
			$("#img14").load("imagetri.php #img14");
			$("#img15").load("imagetri.php #img15");
			$("#img16").load("imagetri.php #img16");
			$("#img17").load("imagetri.php #img17");
			$("#img18").load("imagetri.php #img18");
			$("#img19").load("imagetri.php #img19");
			$("#img20").load("imagetri.php #img20");
			$("#img21").load("imagetri.php #img21");
  },5000);
});
</script>
<!--Script Vticker-->
<script>
$(function() {
   $('#example').vTicker('init', {speed: 1500, 
    pause: 1000,
    showItems: 6,
    padding:4});
	});
</script>
<?php

require_once('inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'languages.inc.php');
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

if (!defined('BX_AVA_EXT')) {
    define ('BX_AVA_DIR_USER_AVATARS', BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/data/images/'); // directory where avatar images are stored
    define ('BX_AVA_URL_USER_AVATARS', BX_DOL_URL_MODULES . 'boonex/avatar/data/images/'); // base url for all avatar images
    define ('BX_AVA_EXT', '.jpg'); // default avatar extension
    define ('BX_AVA_W', 64); // avatar image width
    define ('BX_AVA_H', 64); // avatar image height
    define ('BX_AVA_ICON_W', 32); // avatar icon width
    define ('BX_AVA_ICON_H', 32); // avatar icon height
 }

$iId = (int)$_COOKIE['memberID'];
if($iId==0) {$url='splash.php';
}
else 
{$url='index.php';
echo '<script language="JavaScript"> 
  window.location.href = "index.php"
</script>
';
}
$infoUrl = getTemplateIcon('info.gif');
$warnUrl = getTemplateIcon('exclamation.png');
$GLOBALS['oSysTemplate']->addJsTranslation('_Errors in join form');
$GLOBALS['oSysTemplate']->addJs(array('join.js', 'jquery.form.js'));

//Tableaux
$tabnoms = array(); // On cr�e un tableau vide
$tabprenoms = array(); // On cr�e un tableau vide
$tabnoms2 = array(); // On cr�e un tableau vide
$tabprenoms2 = array(); // On cr�e un tableau vide
$tabcoms = array(); // 3eme tableau vide
$tabavatar = array();
$tablien = array();
//Requetes
$sql_defile = "SELECT ID,Avatar,AqbPoints FROM Profiles ORDER BY Profiles.AqbPoints DESC LIMIT 36";
$req_defile = mysql_query($sql_defile) or die ("Impossible de cr�er l'enregistrement :" . mysql_error());

while($result_defile = mysql_fetch_assoc($req_defile)){
$LienProfil = getProfileLink($result_defile['ID']);
$Miniavatar = $result_defile['Avatar'];
if ($Miniavatar['Avatar']<>"0") {
array_push($tabnoms2,$result_defile['LastName']);
array_push($tabprenoms2,$result_defile['FirstName']);
array_push($tabavatar,$Miniavatar);
array_push($tablien,$LienProfil); 
}
}
$counttabavat = count($tablien); 
$varY=0;
for($varY=0;$varY<$counttabavat;$varY++){
$imglien[$varY]= '<div class="mioavatarfb"><a href="'.$tablien[$varY].'"><img class="mioavatfb" src="'.BX_AVA_URL_USER_AVATARS.$tabavatar[$varY].BX_AVA_EXT.'"></a></div>';
}



//--------------------------------------------------
$sql_coms = "SELECT * FROM commenti_spy_data,Profiles WHERE Profiles.ID=commenti_spy_data.user ORDER BY commenti_spy_data.id DESC LIMIT 6";
$req_coms = mysql_query($sql_coms) or die ("Impossible de cr�er l'enregistrement :" . mysql_error());

while($result_coms = mysql_fetch_assoc($req_coms)){
array_push($tabnoms,$result_coms['LastName']);
array_push($tabprenoms,$result_coms['FirstName']); 
array_push($tabcoms,$result_coms['commento']); 
//echo $result_coms['NickName'].' : "'.$result_coms['commento'].'"<br>';
}
$counttab = count($tabnoms);
$varX=0;
$max_length = 75;
for($varX=0;$varX<$counttab;$varX++){
$com[$varX]=$tabprenoms[$varX].' '.$tabnoms[$varX].' : "'.$tabcoms[$varX].'"';
if (strlen($com[$varX]) > $max_length){
	$offset = ($max_length - 3) - strlen($com[$varX]);
	$com[$varX] = substr($com[$varX], 0, strrpos($com[$varX], ' ', $offset)) . '...';
	}
}

?>


<!DOCTYPE html> 
<html> 
<head> 
<meta charset="utf-8" />
<link href="templates/tmpl_uni/css/anchor.css" rel="stylesheet" type="text/css" />
<link href="templates/tmpl_uni/css/splash.css" rel="stylesheet" type="text/css" />
<style type="text/css"></style>
<script type="text/javascript" language="javascript">var aDolLang = {'_Counter': 'Counter','_PROFILE_ERR': 'Error!\nYour username or password was incorrect. Please try again.'};</script>

</head>

<body>
<div class="VeryBigBody">
	<div class="body1">
<!-- 		<div class="header_box"> -->
		<div class="logo">
		<a href="#"><img class="topLogo" src="templates/tmpl_uni/images/splash/top_logo.png" alt="Logo" /></a>
		</div>
		<form onsubmit="validateLoginForm(this); return false;" method="post" action="member.php" id="login_box_form">
			<input type="hidden" value="member.php" name="relocate" class="form_input_hidden">            
			<input type="hidden" value="Tg3Skixqd7Kxg+VGXD=Q" name="csrf_token" class="form_input_hidden">
		<div class="login_box">
			<div class="login_form">
				<div class="login_txt"><?= _t('_NickName') ?>:
				</div>
				<div class="login_field_border">
					<input class="login_field" name="ID" value="" type="text" />
				</div>
			</div>
			<div class="pass_form">
				<div class="pass_txt"><?= _t('_Password') ?>:
				</div>
				<div class="pass_field_border">
					<input class="pass_field" name="Password" value="" type="password" />
				</div>
				<div class="remember_me">
					<input type="checkbox" id="" name="rememberMe" class=""><label><?= _t('_Remember password') ?></label>
				</div> 
			</div>
		</div>
		<div class="login_button"><?= _t('_Login') ?>
			<div class="forgot"><a href="forgot.php"><?= _t('_forgot_your_password') ?></a>
			</div>
		</div>
		<div class="login_button_Trans">
			<input type="image" value="" src="templates/tmpl_uni/images/splash/login_button_trans.png" />
		</div>
		</form>
<!-- 		</div> -->
	</div>
	<div class="BigBody">
		<div class="body2">
			<div class="video_tv">
				<object width="519" height="300">
					<param name="movie" value="http://www.youtube.com/v/kJTOGKmiYBk?version=3&amp;hl=ru_RU&amp;rel=0"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="wmode" value="opaque" />
					<param name="allowscriptaccess" value="always"></param>
					<embed src="http://www.youtube.com/v/kJTOGKmiYBk?version=3&amp;hl=ru_RU&amp;rel=0" type="application/x-shockwave-flash" width="519" height="300" allowscriptaccess="always" wmode="opaque" allowfullscreen="true"></embed>
				</object>
			</div>
		</div>
		<div class="body3">
			<div id ="img1" class="img_carroussel">
			<?php echo $imglien[0];?></div><div id ="img2" class="img_carroussel"><?php echo $imglien[1];?></div><div id ="img3" class="img_carroussel"><?php echo $imglien[2];?></div><div id ="img4" class="img_carroussel"><?php echo $imglien[3];?></div><div id ="img5" class="img_carroussel"><?php echo $imglien[4];?></div><div id ="img6" class="img_carroussel"><?php echo $imglien[5];?></div><div id ="img7" class="img_carroussel"><?php echo $imglien[6];?></div><div id ="img8" class="img_carroussel"><?php echo $imglien[7];?></div><div id ="img9" class="img_carroussel"><?php echo $imglien[8];?></div><div id ="img10" class="img_carroussel"><?php echo $imglien[9];?></div><div id ="img11" class="img_carroussel"><?php echo $imglien[10];?></div><div id ="img12" class="img_carroussel"><?php echo $imglien[11];?></div><div id ="img13" class="img_carroussel"><?php echo $imglien[12];?></div><div id ="img14" class="img_carroussel"><?php echo $imglien[13];?></div><div id ="img15" class="img_carroussel"><?php echo $imglien[14];?></div><div id ="img16" class="img_carroussel"><?php echo $imglien[15];?></div><div id ="img17" class="img_carroussel"><?php echo $imglien[16];?></div><div id ="img18" class="img_carroussel"><?php echo $imglien[17];?></div><div id ="img19" class="img_carroussel"><?php echo $imglien[18];?></div><div id ="img20" class="img_carroussel"><?php echo $imglien[19];?></div><div id ="img21" class="img_carroussel"><?php echo $imglien[20];?>
			</div>
		</div>
		<div class="body4">
			<form name="join_form" method="post" action="join.php?skin=oounisoft" onsubmit="return validateJoinForm(this);" enctype="multipart/form-data" id="join_form" class="form_advanced ">
				<input  class="form_input_hidden" type="hidden" name="join_page" value="1" />
				<div class="join_box">
					<div class="joinNOWtxt"><?= _t('_Join_now_splash') ?>
					</div>
					<div class="joinTXTdiv">
						<div class="jointxt jointxtProfile"><?= _t('_ProfileType_Select_splash') ?>:
						</div>
						<div class="jointxt jointxtNick"><?= _t('_NickName') ?>:
						</div>
						<div class="jointxt jointxtPass"><?= _t('_Password') ?>:
						</div>
						<div class="jointxt jointxtConfrm"><?= _t('_Confirm password') ?>:
						</div>
						<div class="jointxt jointxtEmail"><?= _t('_Email') ?>:
						</div>
					</div>
					<div class="joinFORMSdiv">
						<div  id="join_form_table" class="joinform" >
							<select  class="joinfield" name="ProfileType[0]" id="ProfileType">
								<option value="2" >Journalist</option>
								<option value="4" >Communicator</option>
								<option value="8" >Leader</option>            
							</select>
						</div>
						<div class="joinform">
							<div class="joinfield">
								<input min="4" max="500" class="fieldArea" type="text" name="NickName[0]" id="NickName" />
							</div>
						</div>
						<div class="joinform">
							<div class="joinfield">
								<input min="5" max="16" class="fieldArea" type="password" name="Password[0]" id="Password" />
							</div>
						</div>
						<div class="joinform">
							<div class="joinfield">
								<input  min="5" max="16" class="fieldArea" type="password" name="Password_confirm[0]" id="Password_confirm" />
							</div>
						</div>
						<div class="joinform">
							<div class="joinfield">
								<input  min="6" type="text" name="Email[0]" id="Email" class="fieldArea" />
							</div>
						</div>
					</div>
					<div class="joinPICSdiv">
						<div class="infoPic">
							<img class="info" alt="info" src="<?= $infoUrl?>" float_info="<?= _t('_aqb_pts_profile_types_join') ?>" />
						</div>
						<div class="infoPic">
							<img class="info" alt="info" src="<?= $infoUrl?>" float_info="<?= _t('_FieldDesc_NickName_Join') ?>" />
						</div>
						<div class="infoPic">
							<img class="info" alt="info" src="<?= $infoUrl?>" float_info="<?= _t('_FieldDesc_Password_Join') ?>" />
						</div>
						<div class="infoPic">
							<img class="info" alt="info" src="<?= $infoUrl?>" float_info="<?= _t('_Confirm password') ?>" />
						</div>
						<div class="infoPic">
							<img class="info" alt="info" src="<?= $infoUrl?>" float_info="<?= _t('_FieldDesc_Email_Join') ?>" />
						</div>
					</div>
					<div class="button_joinform" style="height:32px;">
						<div class="button_jointxt"><?= _t('_Join') ?>
						</div>
						<div class="button_joinform_border">
							<div>
								<input class="button_joinform_Trans" type="image" name="do_submit" value="" src="templates/tmpl_uni/images/splash/join_button_trans.png" />
							</div>
						</div>
					</div>
				</div>
			</form>
			<div class="join_desc_box">
				<div class="titlejoin_desc">
					<?= _t('_Why join') ?> ?
				</div>
				<div class="join_desc">
					<?= _t('_why_join_desc') ?>
				</div>
			</div>
		</div>
		<div class="body5">
			<div class="blockTitle_border">
				<div id="example" class="blockTitle">
 					<ul class="listLastNews">
   						<li id="com1"><?php echo $com[0];?></li>
    					<li id="com2"><?php echo $com[1];?></li>
						<li id="com3"><?php echo $com[2];?></li>
						<li id="com4"><?php echo $com[3];?></li>
						<li id="com5"><?php echo $com[4];?></li>
						<li id="com6"><?php echo $com[5];?></li>
  					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="body6">
		<div class="footer_box">
			<div class="bottomLinks">
				<span class="bottomLinksItem"><a href="about_us.php"><?= _t('_About Us') ?></a></span>
				<span class="bottomLinksItem"><a href="privacy.php"><?= _t('_Privacy') ?></a></span>
				<span class="bottomLinksItem"><a href="terms_of_use.php"><?= _t('_Terms_of_use') ?></a></span>
				<span class="bottomLinksItem"><a href="tellfriend.php" target="_blank"><?= _t('_Invite a friend') ?></a></span>
				<span class="bottomLinksItem"><a href="faq.php"><?= _t('_FAQ') ?></a></span>
				<span class="bottomLinksItem"><a href="affiliates.php"><?= _t('_Affiliates') ?></a></span>
				<span class="bottomLinksItem"><a href="help.php"><?= _t('_HELP_H') ?></a></span>
				<span class="bottomLinksItem"><a href="links.php"><?= _t('_Links') ?></a></span>
				<span class="bottomLinksItem"><a href="contact.php"><?= _t('_CONTACT_H') ?></a></span>
			</div>
			<div class="copyright"><?= _t('_copyright') ?>
			</div>
		</div>
	</div>
	<div class="body7">
	</div>
</div>
<div class="fitVeryBigBody">
</div>
</body>
</html>