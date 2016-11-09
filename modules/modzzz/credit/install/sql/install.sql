-- create tables 
 
CREATE TABLE IF NOT EXISTS `[db_prefix]packages` (
  `id` int(11) NOT NULL auto_increment,
  `price` float NOT NULL,
  `credits` int(11) NOT NULL, 
  `status` enum('active','pending') collate utf8_unicode_ci NOT NULL default 'active',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

CREATE TABLE IF NOT EXISTS `[db_prefix]credits` (
  `id` int(11) NOT NULL auto_increment,
  `member_id` int(11) NOT NULL default '0',
  `credits` bigint(20) NOT NULL default '0',
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci; 
  
CREATE TABLE IF NOT EXISTS `[db_prefix]paypal_trans` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `credits` int(11) unsigned NOT NULL, 
  `buyer_id` int(11) unsigned NOT NULL,
  `trans_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `trans_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL, 
  `created` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `profile_id` (`buyer_id`,`trans_id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `[db_prefix]history` (
  `id` int(11) NOT NULL auto_increment,
  `action_id` int(11) NOT NULL default '0',
  `member_id` int(11) NOT NULL default '0',
  `credits_earned` int(11) NOT NULL default '0',
  `credits_redeemed` int(11) NOT NULL default '0',
  `action_type` enum('add','subtract') NOT NULL default 'add',
  `entry_id` int(11) NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(11) NOT NULL auto_increment,
  `group` varchar(100) NOT NULL,
  `unit` varchar(50) NOT NULL default '',
  `action` varchar(50) NOT NULL default '',
  `value` bigint(20) NOT NULL default '0',
  `desc` varchar(150) NOT NULL default '',
  `active` int(11) NOT NULL default '1',
  `action_type` enum('standard','system') NOT NULL default 'standard',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]views` (
  `id` int(11) NOT NULL auto_increment,
  `file_id` int(11) NOT NULL default '1',
  `viewer_id` int(11) NOT NULL default '1',
  `type` varchar(50) NOT NULL default '',
  `date` int(11) NOT NULL,
  `expire` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
 
--dumping data
INSERT INTO `[db_prefix]main` (  `group`, `unit`, `action`, `value`, `desc`, `active`, `action_type`) VALUES
  
('_messenger_page_caption', 'messenger', 'chat', 2, '_modzzz_credit_messenger_chat_desc', 1, 'standard'), 
('_simple_messenger_title', 'simple_messenger', 'chat', 2, '_modzzz_credit_simple_messenger_chat_desc', 1, 'standard'), 

('_Mail', 'mail', 'send', 2, '_modzzz_credit_mail_send_desc', 1, 'standard'),
('_Mail', 'mail', 'view', 2, '_modzzz_credit_mail_read_desc', 1, 'standard'),
('_Profile', 'profile', 'view', 2, '_modzzz_credit_profile_view_desc', 1, 'standard'),

('_modzzz_credit_header_videos', 'bx_videos', 'view', 2, '_modzzz_credit_video_view_desc', 1, 'standard'),
('_modzzz_credit_header_photos', 'bx_photos', 'view', 2, '_modzzz_credit_photo_view_desc', 1, 'standard'),
('_modzzz_credit_header_sounds', 'bx_sounds', 'view', 2, '_modzzz_credit_sound_view_desc', 1, 'standard'),
('_modzzz_credit_header_files', 'bx_files', 'view', 2, '_modzzz_credit_file_view_desc', 1, 'standard'),

('_modzzz_credit_header_videos', 'bx_videos', 'add', 2, '_modzzz_credit_video_add_desc', 1, 'standard'),
('_modzzz_credit_header_photos', 'bx_photos', 'add', 2, '_modzzz_credit_photo_add_desc', 1, 'standard'),
('_modzzz_credit_header_sounds', 'bx_sounds', 'add', 2, '_modzzz_credit_sound_add_desc', 1, 'standard'),
('_modzzz_credit_header_files', 'bx_files', 'add', 2, '_modzzz_credit_file_add_desc', 1, 'standard'),
('_modzzz_credit_header_ads', 'ads', 'create', 2, '_modzzz_credit_ad_add_desc', 1, 'standard'), 
('_modzzz_credit_header_events', 'bx_events', 'add', 2, '_modzzz_credit_event_add_desc', 1, 'standard'), 
('_modzzz_credit_header_sites', 'bx_sites', 'add', 2, '_modzzz_credit_site_add_desc', 1, 'standard'),
 
('_modzzz_credit_header_donation', 'site_donation', 'add', 0, '_modzzz_credit_donation_allocate_desc', 1, 'system'),
('_modzzz_credit_header_donation', 'site_donation', 'subtract', 0, '_modzzz_credit_donation_deduct_desc', 1, 'system'),
('_modzzz_credit_header_donation', 'member_donation', 'add', 0, '_modzzz_credit_member_donation_received_desc', 1, 'system'),
('_modzzz_credit_header_donation', 'member_donation', 'subtract', 0, '_modzzz_credit_member_donation_transfer_desc', 1, 'system'),
('_modzzz_credit_header_paypal', 'paypal_purchase', 'add', 0, '_modzzz_credit_paypal_purchase_desc', 1, 'system'),
('_modzzz_credit_header_membership', 'membership_purchase', 'subtract', 0, '_modzzz_credit_membership_purchase_desc', 1, 'system') 
 ;
 
ALTER TABLE `sys_objects_actions` CHANGE `Type` `Type` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_credit_main', 'Credit Home', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_credit_history', 'Credit History', @iMaxOrder+2); 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_credit_transactions', 'Credit Transactions', @iMaxOrder+5);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_credit_membership', 'Credit Membership', @iMaxOrder+6);

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_credit_main', '998px', 'Credits description block', '_modzzz_credit_block_actions', '1', '0', 'Desc', '', '1', '100', 'non,memb', '0'),
    ('modzzz_credit_history', '998px', 'Credits history block', '_modzzz_credit_block_history', '1', '0', 'Desc', '', '1', '100', 'non,memb', '0'), 
    ('modzzz_credit_transactions', '998px', 'Credits transactions block', '_modzzz_credit_block_transactions', '1', '0', 'Transactions', '', '1', '100', 'non,memb', '0'),
    ('modzzz_credit_membership', '998px', 'Credit''s current level block', '_modzzz_credit_block_current_level', '1', '0', 'CurrentLevels', '', '1', '34', 'non,memb', '0'),
    ('modzzz_credit_membership', '998px', 'Credit''s available levels block', '_modzzz_credit_block_available_levels', '2', '0', 'AvailableLevels', '', '1', '66', 'non,memb', '0'),
   
    ('member', '998px', 'Member Credits', '_modzzz_credit_block_acountpage', 1, 5, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''credit'', ''account_block'');', 1, 34, 'non,memb', 0);

-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=credit/', 'm/credit/', 'modzzz_credit_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Credit', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_credit_permalinks', 'on', 26, 'Enable friendly permalinks in credit', 'checkbox', '', '', '0', ''),
('modzzz_credit_activated', 'on', @iCategId, 'Credits feature turned on', 'checkbox', '', '', '0', ''),
('modzzz_credit_packages_active', '', @iCategId, 'Activate Credit Packages', 'checkbox', '', '', '0', ''),  

('modzzz_credit_free_gender', 'none', @iCategId, 'This gender will not be charged Credits', 'select', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', 'none,female,male'),
('modzzz_credit_view_duration', '20', @iCategId, 'Number of days before paid viewing access expires (zero means never expires)', 'digit', '', '', 0, ''),

('modzzz_credit_messenger_chat_activated', 'on', @iCategId, 'Credits activated for messenger chat', 'checkbox', '', '', '0', ''), 
('modzzz_credit_simple_messenger_chat_activated', 'on', @iCategId, 'Credits activated for simple simple_messenger chat', 'checkbox', '', '', '0', ''), 
 
('modzzz_credit_mail_send_activated', 'on', @iCategId, 'Credits activated for mail sending', 'checkbox', '', '', '0', ''),
('modzzz_credit_mail_view_activated', 'on', @iCategId, 'Credits activated for mail reading', 'checkbox', '', '', '0', ''),
  
('modzzz_credit_event_add_activated', 'on', @iCategId, 'Credits activated for event posting', 'checkbox', '', '', '0', ''),
('modzzz_credit_ad_add_activated', 'on', @iCategId, 'Credits activated for ads posting', 'checkbox', '', '', '0', ''),
('modzzz_credit_site_add_activated', 'on', @iCategId, 'Credits activated for site listing', 'checkbox', '', '', '0', ''),
('modzzz_credit_photo_add_activated', 'on', @iCategId, 'Credits activated for photo uploading', 'checkbox', '', '', '0', ''),
('modzzz_credit_video_add_activated', 'on', @iCategId, 'Credits activated for video uploading', 'checkbox', '', '', '0', ''),
('modzzz_credit_sound_add_activated', 'on', @iCategId, 'Credits activated for sound uploading', 'checkbox', '', '', '0', ''),
('modzzz_credit_file_add_activated', 'on', @iCategId, 'Credits activated for file uploading', 'checkbox', '', '', '0', ''),

('modzzz_credit_profile_view_activated', 'on', @iCategId, 'Credits activated for profile view', 'checkbox', '', '', '0', ''),
('modzzz_credit_photo_view_activated', 'on', @iCategId, 'Credits activated for photo viewing', 'checkbox', '', '', '0', ''),
('modzzz_credit_video_view_activated', 'on', @iCategId, 'Credits activated for video viewing', 'checkbox', '', '', '0', ''),
('modzzz_credit_sound_view_activated', 'on', @iCategId, 'Credits activated for sound listening', 'checkbox', '', '', '0', ''),
('modzzz_credit_file_view_activated', 'on', @iCategId, 'Credits activated for file viewing', 'checkbox', '', '', '0', ''),
 
('modzzz_credit_buy_membership', 'on', @iCategId, 'Use Credits to purchase membership', 'checkbox', '', '', '0', ''),
('modzzz_credit_trans_perpage_browse', '50', @iCategId, 'Number of transactions to show on per page', 'digit', '', '', 0, ''), 
('modzzz_credit_credit_cost', '0.01', @iCategId, 'Cost per Credit (if credit packages disabled)', 'digit', '', '', 0, ''), 
('modzzz_credit_membership_cost', '1', @iCategId, 'Credit to dollar ratio (for membership purchase)<br>eg. if 1.5 then 15 credits needed to purchase membership that costs $10', 'digit', '', '', 0, ''),
('modzzz_credit_currency_code', 'USD', @iCategId, 'Currency code for checkout system (eg USD,EUR,GBP)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', ''),
('modzzz_credit_currency_sign', '&#36;', @iCategId, 'Currency sign (for display purposes only)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', ''), 
('modzzz_credit_paypal_email', '', @iCategId, 'Paypal Business Email', 'digit', '', '', 0, ''),
('modzzz_credit_paypal_item_desc', 'Purchase Website Credits', @iCategId, 'Item description displayed on PayPal Order', 'digit', '', '', 0, ''),
('modzzz_credit_paypal_active', 'on', @iCategId, 'Enable Paypal purchase of Credits', 'checkbox', '', '', 0, '')
;
 
-- top menu 
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Credits', '_modzzz_credit_menu_root', 'modules/?r=credit/view/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', 'modules/modzzz/credit/|modzzz_credit.png', '', '0', '');
 
SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Credits', '_modzzz_credit_menu_root', 'modules/?r=credit/home/|modules/?r=credit/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'modules/modzzz/credit/|modzzz_credit.png', '', 1, '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Credit Main Page', '_modzzz_credit_menu_main', 'modules/?r=credit/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modzzz/credit/|modzzz_credit.png', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Credit History', '_modzzz_credit_menu_history', 'modules/?r=credit/history', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modzzz/credit/|modzzz_credit.png', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Credit Purchase', '_modzzz_credit_menu_purchase_credits', 'modules/?r=credit/purchase_credits', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modzzz/credit/|modzzz_credit.png', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Credit Transactions', '_modzzz_credit_menu_transactions', 'modules/?r=credit/transactions', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modzzz/credit/|modzzz_credit.png', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Credit Membership', '_modzzz_credit_menu_membership', 'modules/?r=credit/membership', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modzzz/credit/|modzzz_credit.png', '', 0, ''); 
 
 
-- member menu 
-- SET @iParentID = IFNULL((SELECT `ID` FROM `sys_menu_top` WHERE `Link` = 'member.php' AND `Type`='top' AND `Active`=1),1);

-- SET @iParentID = @iParentID + 1;
-- INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
-- (NULL, @iParentID, 'Credits Activity', '_modzzz_credit_activity', 'm/credit/home?filter=history', 10, 'memb', '', '', '', 1, 1, 1, 'custom', '', 0, '');

 
-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_credit', '_modzzz_credit', '{siteUrl}modules/?r=credit/administration/', 'Credit module by Modzzz','modules/modzzz/credit/|credit.png', @iMax+1);


SET @iMaxOrder = (SELECT `Order` + 1 FROM `sys_objects_actions` WHERE `Type`='Profile' ORDER BY `Order` DESC LIMIT 1); 
INSERT INTO `sys_objects_actions` ( `Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES
( '{evalResult}', 'modules/modzzz/credit/|action_donate.png', 'm/credit/donate_credits/{ID}', '', '$oCredit = BxDolModule::getInstance(''BxCreditModule'');if (($GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin'']) && ({ID} != {member_id}) && ($oCredit->isAllowedDonate({member_id},{ID}))) return _t(''_modzzz_credit_donate_credits'');', @iMaxOrder, 'Profile', 0);    
 

-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_credit_profile_delete', '', '', 'BxDolService::call(''credit'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);


-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_credit', 'BxCreditResponse', 'modules/modzzz/credit/classes/BxCreditResponse.php', '');
SET @iHandler := LAST_INSERT_ID();

INSERT INTO `sys_alerts` VALUES  
(NULL, 'profile', 'send_mail_internal', @iHandler), 
(NULL, 'profile', 'view', @iHandler),
(NULL, 'mail', 'view', @iHandler),
(NULL, 'bx_files', 'view', @iHandler),
(NULL, 'bx_photos', 'view', @iHandler), 
(NULL, 'bx_sounds', 'view', @iHandler),
(NULL, 'bx_videos', 'view', @iHandler),

(NULL, 'bx_files', 'add', @iHandler),
(NULL, 'bx_photos', 'add', @iHandler), 
(NULL, 'bx_sounds', 'add', @iHandler),
(NULL, 'bx_videos', 'add', @iHandler),
(NULL, 'bx_events', 'add', @iHandler),
(NULL, 'bx_sites', 'add', @iHandler),
(NULL, 'ads', 'create',  @iHandler),

(NULL, 'bx_files', 'pre_add', @iHandler),
(NULL, 'bx_photos', 'pre_add', @iHandler), 
(NULL, 'bx_sounds', 'pre_add', @iHandler),
(NULL, 'bx_videos', 'pre_add', @iHandler),
(NULL, 'bx_events', 'pre_add', @iHandler),
(NULL, 'bx_sites', 'pre_add', @iHandler),
(NULL, 'ads', 'pre_create', @iHandler) 
;

INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
 ( 'BxCredit', '*/5 * * * *', 'BxCreditCron', 'modules/modzzz/credit/classes/BxCreditCron.php', '');


INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_credit_donate_notify', 'Your Recieved Credits at <SiteName>', '<html><head></head><body style="font: 12px Verdana; color:#000000">\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p><a href="<SenderUrl>"><SenderName></a> has donated <Credits> Credits to you.</p><b>Thank you for using our services!</b></p>\r\n\r\n<p>---</p>\r\n<p style="font: bold 10px Verdana; color:red">!!!Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Credits Donation Notification', '0');



-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'credit donate credits', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
     (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);