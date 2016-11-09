-- tables
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]images`;
DROP TABLE IF EXISTS `[db_prefix]videos`;
DROP TABLE IF EXISTS `[db_prefix]sounds`;
DROP TABLE IF EXISTS `[db_prefix]files`;
DROP TABLE IF EXISTS `[db_prefix]fans`;
DROP TABLE IF EXISTS `[db_prefix]admins`;
DROP TABLE IF EXISTS `[db_prefix]rating`;
DROP TABLE IF EXISTS `[db_prefix]rating_track`;
DROP TABLE IF EXISTS `[db_prefix]cmts`;
DROP TABLE IF EXISTS `[db_prefix]cmts_track`;
DROP TABLE IF EXISTS `[db_prefix]views_track`;
DROP TABLE IF EXISTS `[db_prefix]fields`;
DROP TABLE IF EXISTS `[db_prefix]categories`;

-- forum tables
DROP TABLE IF EXISTS `[db_prefix]forum`;
DROP TABLE IF EXISTS `[db_prefix]forum_cat`;
DROP TABLE IF EXISTS `[db_prefix]forum_cat`;
DROP TABLE IF EXISTS `[db_prefix]forum_flag`;
DROP TABLE IF EXISTS `[db_prefix]forum_post`;
DROP TABLE IF EXISTS `[db_prefix]forum_topic`;
DROP TABLE IF EXISTS `[db_prefix]forum_user`;
DROP TABLE IF EXISTS `[db_prefix]forum_user_activity`;
DROP TABLE IF EXISTS `[db_prefix]forum_user_stat`;
DROP TABLE IF EXISTS `[db_prefix]forum_vote`;
DROP TABLE IF EXISTS `[db_prefix]forum_actions_log`;
DROP TABLE IF EXISTS `[db_prefix]forum_attachments`;
DROP TABLE IF EXISTS `[db_prefix]forum_signatures`;

-- compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('ml_pages_view', 'ml_pages_celendar', 'ml_pages_main', 'ml_pages_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN('ml_pages_view', 'ml_pages_celendar', 'ml_pages_main', 'ml_pages_my');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'Pages';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'User Pages';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'Joined Pages';

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=pages/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'ml_pages';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'ml_pages';
DELETE FROM `sys_objects_views` WHERE `name` = 'ml_pages';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'ml_pages';
DELETE FROM `sys_categories` WHERE `Type` = 'ml_pages';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'Pages';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'ml_pages';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'ml_pages';
DELETE FROM `sys_tags` WHERE `Type` = 'ml_pages';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'ml_pages' OR `Type` = 'ml_pages_title';
DELETE FROM `sys_stat_site` WHERE `Name` = 'evs';
DELETE FROM `sys_stat_member` WHERE TYPE IN('ml_pages', 'ml_pagesp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_ml_pages';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Pages' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Pages' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Pages';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Pages';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'ml_pages';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` = 'ml_pages_invitation' OR `Name` = 'ml_pages_broadcast' OR `Name` = 'ml_pages_sbs' OR `Name` = 'ml_pages_join_request' OR `Name` = 'ml_pages_join_reject' OR `Name` = 'ml_pages_join_confirm' OR `Name` = 'ml_pages_fan_remove' OR `Name` = 'ml_pages_fan_become_admin' OR `Name` = 'ml_pages_admin_become_fan';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Pages' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'ml_pages_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('pages view', 'pages browse', 'pages search', 'pages add', 'pages comments delete and edit', 'pages edit any page', 'pages delete any page', 'pages mark as featured', 'pages approve', 'pages broadcast message');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('pages view', 'pages browse', 'pages search', 'pages add', 'pages comments delete and edit', 'pages edit any page', 'pages delete any page', 'pages mark as featured', 'pages approve', 'pages broadcast message');

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'ml_pages_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'ml_pages_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'ml_pages';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'pages';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='ml_pages';
DELETE FROM `sys_sbs_types` WHERE `unit`='ml_pages';

DELETE FROM `sys_localization_keys` WHERE `Key` LIKE '%ml_pages%';
DELETE FROM `sys_localization_keys` WHERE `Key` LIKE '%_ml_page%';
DELETE FROM `sys_localization_categories` WHERE `Name`='Modloaded Pages';
