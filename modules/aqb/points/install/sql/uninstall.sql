SET @sPluginName = 'aqb_points';
SET @sPluginTitle = 'Global Points System';

DROP TABLE IF EXISTS `[db_prefix]actions`;
DROP TABLE IF EXISTS `[db_prefix]modules`;
DROP TABLE IF EXISTS `[db_prefix]history`;
DROP TABLE IF EXISTS `[db_prefix]levels`;
DROP TABLE IF EXISTS `[db_prefix]transactions`;
DROP TABLE IF EXISTS `[db_prefix]memlevels_settings`;
DROP TABLE IF EXISTS `[db_prefix]memlevels_pricing`;

DELETE  `sys_alerts_handlers`,`sys_alerts` 
FROM `sys_alerts_handlers`,`sys_alerts` 
WHERE `sys_alerts`.`handler_id` = `sys_alerts_handlers`.`id`  AND `sys_alerts_handlers`.`name` = 'aqb_points_assign_alert';

DELETE FROM `sys_menu_top` WHERE `Caption` = '_aqb_my_points_link_title';
DELETE FROM `sys_menu_top` WHERE `Caption` = '_aqb_browse_points_leaders';
DELETE FROM `sys_menu_top` WHERE `Caption` = '_aqb_points_mem_points';
DELETE FROM `sys_page_compose_pages` WHERE `Name` = 'aqb_points_membership';
DELETE FROM `sys_page_compose` WHERE `Page` = 'aqb_points_membership';

DELETE FROM `sys_page_compose` WHERE `Page`='index' AND `Desc` = @sPluginTitle AND `Caption`='_aqb_browse_points_leaders' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Caption` IN ('_aqb_points_levels_info', '_aqb_points_levels_info_profile');

DELETE FROM `sys_email_templates` WHERE `Name` IN ('t_AqbPointsPresent', 't_AqbPointsPenalized', 't_AqbPointsExchanged');

SET @iId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = @sPluginTitle);
DELETE FROM `sys_options` WHERE `kateg`= @iId;
DELETE FROM `sys_options` WHERE `Name` = 'aqb_points_packages';
DELETE FROM `sys_options_cats` WHERE `name`=@sPluginTitle;
DELETE FROM `sys_options` WHERE `Name`='permalinks_module_aqb_points';
DELETE FROM `sys_menu_admin` WHERE `name`=@sPluginName;
DELETE FROM `sys_permalinks` WHERE `check` = CONCAT('permalinks_module_', @sPluginName);
DELETE FROM `sys_objects_actions` WHERE `Icon` = CONCAT('modules/aqb/points/|points_action.png');
DELETE FROM `sys_cron_jobs` WHERE `name` = 'aqb_points_reports';

DELETE FROM `sys_profile_fields` WHERE `Name` = 'AqbPoints';                                             