
-- tables
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]credits`; 
DROP TABLE IF EXISTS `[db_prefix]history`;
DROP TABLE IF EXISTS `[db_prefix]paypal_trans`;
DROP TABLE IF EXISTS `[db_prefix]views`;
DROP TABLE IF EXISTS `[db_prefix]packages`;
  
-- compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN ( 'modzzz_credit_main', 'modzzz_credit_history', 'modzzz_credit_transactions', 'modzzz_credit_membership' );
DELETE FROM `sys_page_compose` WHERE `Page` IN ( 'modzzz_credit_main', 'modzzz_credit_history', 'modzzz_credit_transactions', 'modzzz_credit_membership');
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'Member Credits';

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=credit/';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_credit';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_credit';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'modzzz_credit';
DELETE FROM `sys_objects_actions` WHERE `Caption` = '{modzzz_credit_title}'; 
DELETE FROM `sys_objects_actions` WHERE `Icon` = CONCAT('modules/modzzz/credit/|action_donate.png');
DELETE FROM `sys_stat_site` WHERE `Name` = 'modzzz_credit';
DELETE FROM `sys_stat_member` WHERE TYPE IN('modzzz_credit', 'modzzz_creditp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_modzzz_credit';
 
-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_credit';
DELETE FROM `sys_menu_top` WHERE `name` = 'Credits';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Credits' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;
 
DELETE FROM `sys_menu_top` WHERE `Name` = 'Credits';
 

 
-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Credit' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` IN ('modzzz_credit_permalinks');
  
-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_credit_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_credit_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

 
-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'credit';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='modzzz_credit';
DELETE FROM `sys_sbs_types` WHERE `unit`='modzzz_credit';


-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_credit_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
 
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_credit' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- cron_jobs
DELETE FROM `sys_cron_jobs` WHERE `name` IN ('BxCredit');


DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_credit_donate_notify');


-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('credit donate credits');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('credit donate credits');


 