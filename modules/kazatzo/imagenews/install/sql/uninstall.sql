DROP TABLE IF EXISTS `[db_prefix]entries`;
DROP TABLE IF EXISTS `[db_prefix]comments`;
DROP TABLE IF EXISTS `[db_prefix]comments_track`;
DROP TABLE IF EXISTS `[db_prefix]voting`;
DROP TABLE IF EXISTS `[db_prefix]voting_track`;
DROP TABLE IF EXISTS `[db_prefix]views_track`;

SET @iTMParentId = (SELECT `ID` FROM `sys_menu_top` WHERE `Name`='ImageNews' LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Name` IN ('ImageNews', '[db_prefix]_view') OR `Parent`=@iTMParentId;
DELETE FROM `sys_menu_member` WHERE `Name`='ImageNews';
DELETE FROM `sys_menu_admin` WHERE `name`='imagenews';

DELETE FROM `sys_permalinks` WHERE `check`='permalinks_module_imagenews';

SET @iCategoryId = (SELECT `ID` FROM `sys_options_cats` WHERE `name`='ImageNews' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `name`='ImageNews' LIMIT 1;
DELETE FROM `sys_options` WHERE `kateg`=@iCategoryId OR `Name` IN ('permalinks_module_imagenews', 'category_auto_app_imagenews');

DELETE FROM `sys_objects_cmts` WHERE `ObjectName`='imagenews' LIMIT 1;
DELETE FROM `sys_objects_vote` WHERE `ObjectName`='imagenews' LIMIT 1;
DELETE FROM `sys_objects_tag` WHERE `ObjectName`='imagenews' LIMIT 1;
DELETE FROM `sys_objects_categories` WHERE `ObjectName`='imagenews' LIMIT 1;
DELETE FROM `sys_categories` WHERE `Type` = 'imagenews';
DELETE FROM `sys_objects_search` WHERE `ObjectName`='imagenews' LIMIT 1;
DELETE FROM `sys_objects_views` WHERE `name`='imagenews' LIMIT 1;

DELETE FROM `sys_page_compose_pages` WHERE `Name` IN ('imagenews_single', 'imagenews_home');
DELETE FROM `sys_page_compose` WHERE `Page` IN ('imagenews_single', 'imagenews_home') OR `Caption` IN ('_imagenews_bcaption_featured', '_imagenews_bcaption_latest', '_imagenews_bcaption_member');

DELETE FROM `sys_objects_actions` WHERE `Type`='imagenews';

DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='imagenews';
DELETE FROM `sys_sbs_types` WHERE `unit`='imagenews';

DELETE FROM `sys_email_templates` WHERE `Name` IN ('t_sbsImageNewsComments', 't_sbsImageNewsRates');

DELETE FROM `sys_acl_actions` WHERE `Name` IN ('ImageNews Delete');

DELETE FROM `sys_cron_jobs` WHERE `name`='imagenews';