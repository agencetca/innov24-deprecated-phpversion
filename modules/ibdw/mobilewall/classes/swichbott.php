<?php
include('../../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
header("refresh:1 ; ../../../../".$admin_dir);
include BX_DIRECTORY_PATH_MODULES.'ibdw/mobilewall/classes/config.php';
mysql_query("SET NAMES 'utf8'");
$queryx="UPDATE sys_menu_admin SET name='MobileWall', title='MobileWall', url='{siteUrl}modules/ibdw/mobilewall/classes/configurazione.php', icon='modules/ibdw/mobilewall/templates/base/images/icons/|wall_panel.png' WHERE name='MobileWall Activation'";
$resultx = mysql_query($queryx);
?>