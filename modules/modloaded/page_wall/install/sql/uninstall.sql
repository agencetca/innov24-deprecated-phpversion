DROP TABLE IF EXISTS `[db_prefix]events`;
DROP TABLE IF EXISTS `[db_prefix]handlers`;
DROP TABLE IF EXISTS `[db_prefix]comments`;
DROP TABLE IF EXISTS `[db_prefix]comments_track`;

DELETE FROM `sys_page_compose` WHERE `Caption` IN ('_page_wall_pc_post', '_page_wall_pc_view');


DELETE FROM `sys_objects_cmts` WHERE `ObjectName`='ml_page_wall' LIMIT 1;

SET @iCategoryId = (SELECT `ID` FROM `sys_options_cats` WHERE `name`='Page Wall' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `name`='Page Wall' LIMIT 1;
DELETE FROM `sys_options` WHERE `kateg`=@iCategoryId OR `Name`='permalinks_module_page_wall';

DELETE FROM `sys_acl_actions` WHERE `Name` IN ('Page Wall Post Comment', 'Page Wall Delete Comment');

DELETE FROM `sys_categories` WHERE `Category`='Page Wall';

DELETE FROM `sys_permalinks` WHERE `check`='permalinks_module_page_wall';

SELECT @iHandlerId:=`id` FROM `sys_alerts_handlers` WHERE `name`='ml_page_wall' LIMIT 1;
DELETE FROM `sys_alerts_handlers` WHERE `name`='ml_page_wall' LIMIT 1;
DELETE FROM `sys_alerts` WHERE `handler_id`=@iHandlerId;




DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='ml_page_wall';
DELETE FROM `sys_sbs_types` WHERE `unit`='ml_page_wall';

DELETE FROM `sys_email_templates` WHERE `Name` IN ('t_sbsPageWallUpdates');

DELETE FROM `sys_localization_keys` WHERE `Key` like '_page_wall%';
DELETE FROM `sys_localization_keys` WHERE `Key` like '%ml_page_wall%';