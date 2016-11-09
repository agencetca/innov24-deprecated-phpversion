
-- tables
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]field_mapping`; 
DROP TABLE IF EXISTS `[db_prefix]actions`; 
DROP TABLE IF EXISTS `[db_prefix]cron`; 


 
-- compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_alerts_main', 'modzzz_alerts_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_alerts_main', 'modzzz_alerts_my'); 
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'Alerts';
  

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=alerts/';
 
-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_alerts';
DELETE FROM `sys_menu_top` WHERE `name` = 'Alerts Activity';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Alerts' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;
   
-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Alerts' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` IN ('modzzz_alerts_permalinks');
  
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_alerts_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_alerts_profile_join' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
 
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_alerts' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('alerts add alert', 'alerts edit any alert', 'alerts delete any alert');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('alerts add alert', 'alerts edit any alert', 'alerts delete any alert');

DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_alerts_now_notify','modzzz_alerts_daily_notify','modzzz_alerts_weekly_notify'); 

DELETE FROM `sys_cron_jobs` WHERE `name` IN ('BxAlerts', 'modzzz_weekly_alerts', 'modzzz_daily_alerts');