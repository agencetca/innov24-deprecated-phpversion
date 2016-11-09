DELETE FROM `sys_page_compose` WHERE `Caption` = 'MegaProfile';
DELETE FROM `sys_page_compose` WHERE `Caption` = '_ibdw_megaprofile_modulename';

DELETE FROM `sys_menu_admin` WHERE `name` = 'Active Megaprofile';
DELETE FROM `sys_menu_admin` WHERE `name` = 'Megaprofile Config';

DROP TABLE IF EXISTS `megaprofile_config`;
DROP TABLE IF EXISTS `ibdw_mega_extralink`;