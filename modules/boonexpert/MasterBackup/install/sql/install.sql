
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Master Database Backup', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());

INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=MasterBackup/', 'm/MasterBackup/', 'Mdb_permalinks');

SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');

INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'MasterBackup', 'Master Database Backup', '{siteUrl}modules/?r=MasterBackup/administration/', 'Master Database Backup for your website', 'modules/boonexpert/MasterBackup/|MasterBackup.png', @iMax+1);
