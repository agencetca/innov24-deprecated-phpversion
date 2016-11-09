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
 
$tabavatar = array();
$tablien = array();
//Requetes
$sql_defile = "SELECT ID,Avatar,AqbPoints FROM Profiles ORDER BY Profiles.AqbPoints DESC LIMIT 36";
$req_defile = mysql_query($sql_defile) or die ("Impossible de créer l'enregistrement :" . mysql_error());

while($result_defile = mysql_fetch_assoc($req_defile)){
$LienProfil = getProfileLink($result_defile['ID']);
$Miniavatar = $result_defile['Avatar'];
if ($Miniavatar['Avatar']<>"0") {
array_push($tabavatar,$Miniavatar);
array_push($tablien,$LienProfil); 
}
}
$counttabavat = count($tablien); 
$varY=0;
for($varY=0;$varY<$counttabavat;$varY++){
$imglien[$varY]= '<div class="mioavatarfb"><a href="'.$tablien[$varY].'"><img class="mioavatfb" src="'.BX_AVA_URL_USER_AVATARS.$tabavatar[$varY].BX_AVA_EXT.'"></a></div>';
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<meta charset="utf-8" />
</head>
<body>
<div id ="img1"><?php echo $imglien[0];?></div>
<div id ="img2"><?php echo $imglien[1];?></div>
<div id ="img3"><?php echo $imglien[2];?></div>
<div id ="img4"><?php echo $imglien[3];?></div>
<div id ="img5"><?php echo $imglien[4];?></div>
<div id ="img6"><?php echo $imglien[5];?></div>
<div id ="img7"><?php echo $imglien[6];?></div>
<div id ="img8"><?php echo $imglien[7];?></div>
<div id ="img9"><?php echo $imglien[8];?></div>
<div id ="img10"><?php echo $imglien[9];?></div>
<div id ="img11"><?php echo $imglien[10];?></div>
<div id ="img12"><?php echo $imglien[11];?></div>
<div id ="img13"><?php echo $imglien[12];?></div>
<div id ="img14"><?php echo $imglien[13];?></div>
<div id ="img15"><?php echo $imglien[14];?></div>
<div id ="img16"><?php echo $imglien[15];?></div>
<div id ="img17"><?php echo $imglien[16];?></div>
<div id ="img18"><?php echo $imglien[17];?></div>
<div id ="img19"><?php echo $imglien[18];?></div>
<div id ="img20"><?php echo $imglien[19];?></div>
<div id ="img21"><?php echo $imglien[20];?></div>
</body>
</html>