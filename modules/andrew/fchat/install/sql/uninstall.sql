-- tables
DROP TABLE IF EXISTS `[db_prefix]_messages`;
DROP TABLE IF EXISTS `[db_prefix]_rooms`;

-- injections
DELETE FROM `sys_injections` WHERE `name`='fchat_injection';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'FChat';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'FChat' LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

-- settings
SET @iCategoryID := (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'FChat' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategoryID;
DELETE FROM `sys_options` WHERE `kateg` = @iCategoryID;
DELETE FROM `sys_options` WHERE `Name` = 'permalinks_[db_prefix]';

-- permalinks
DELETE FROM `sys_permalinks` WHERE `permalink`='m/fchat/';

-- blocks
DELETE FROM `sys_page_compose` WHERE `Page`='index' AND `Desc`='FChat' AND `Caption`='_fch_main' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page`='member' AND `Desc`='FChat' AND `Caption`='_fch_main' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page`='profile' AND `Desc`='FChat' AND `Caption`='_fch_main' AND `Func`='PHP';

-- cron jobs
DELETE FROM `sys_cron_jobs` WHERE `name` = 'fchat';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` = 't_fchat_notify';