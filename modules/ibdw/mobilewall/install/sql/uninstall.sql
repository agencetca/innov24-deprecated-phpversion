DELETE FROM `sys_menu_mobile` WHERE `title` = '_idbw_mobilewall_member_news';
DROP TABLE IF EXISTS `mobilewall_config`;

DELETE FROM `sys_menu_admin` WHERE `name` = 'MobileWall Activation';
DELETE FROM `sys_menu_admin` WHERE `name` = 'MobileWall';