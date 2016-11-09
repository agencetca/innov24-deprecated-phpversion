<?php
 header("refresh: 1; ../../../administration");
 require_once( '../../../inc/header.inc.php' );
 require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
 require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
 require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
 $userid = (int)$_COOKIE['memberID'];
 if(!isAdmin()) { exit;}
 mysql_query("SET NAMES 'utf8'");
 $queryx="UPDATE sys_menu_admin SET name='Megaprofile Config' , title='Megaprofile Config', url='{siteUrl}modules/ibdw/megaprofile/configurazione.php', icon='modules/ibdw/megaprofile/templates/base/images/|gearspy.png' WHERE name='Active Megaprofile'";
 $resultx = mysql_query($queryx);
?>