CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(9) NOT NULL auto_increment,
  `author_id` int(11) NOT NULL default '0',
  `uri` varchar(255) COLLATE utf8_general_ci NOT NULL default '', 
  `phrase` varchar(255) COLLATE utf8_general_ci NOT NULL default '',
  `deliver` varchar(255) COLLATE utf8_general_ci NOT NULL default '', 
  `unit`  text NOT NULL, 
  `notify` ENUM( 'daily', 'weekly', 'now' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'now',
  `search` ENUM( 'title', 'desc','both' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'both', 
  `status` ENUM( 'approved', 'pending' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'approved', 
  `created` int(11) NOT NULL default '0', 
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]cron` (
  `id` int(11) NOT NULL auto_increment,
  `notify` ENUM( 'daily', 'weekly' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'daily', 
  `last_run` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `[db_prefix]actions` (
  `id` int(11) NOT NULL auto_increment,
  `group` varchar(255) NOT NULL,
  `unit` varchar(50) NOT NULL default '',
  `active` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

 
-- Dumping data for table `[db_prefix]actions` 
INSERT INTO `[db_prefix]actions` ( `group`, `unit`, `active`) VALUES
('_modzzz_alerts_news_group_title', 'bx_news', 1),
('_modzzz_alerts_articles_group_title', 'bx_arl', 1), 
('_modzzz_alerts_classified_group_title', 'bx_ads', 1),
('_modzzz_alerts_store_group_title', 'bx_store', 1),
('_modzzz_alerts_event_group_title', 'bx_events', 1),
('_modzzz_alerts_group_group_title', 'bx_groups', 1),
('_modzzz_alerts_sound_group_title', 'bx_sounds', 1),
('_modzzz_alerts_photo_group_title', 'bx_photos', 1),
('_modzzz_alerts_video_group_title', 'bx_videos', 1),
('_modzzz_alerts_site_group_title', 'bx_sites', 1),
('_modzzz_alerts_blog_group_title', 'blogposts', 1),
('_modzzz_alerts_poll_group_title', 'bx_poll', 1),
('_modzzz_alerts_file_group_title', 'bx_files', 1),
('_modzzz_alerts_forum_group_title', 'bx_forum', 1);
  

CREATE TABLE IF NOT EXISTS `[db_prefix]field_mapping` (
  `unit` varchar(50) NOT NULL default '',
  `table` varchar(30) NOT NULL default '',
  `id_field` varchar(30) NOT NULL default '',
  `owner_field` varchar(30) NOT NULL default '',
  `title_field` varchar(30) NOT NULL default '',
  `desc_field` varchar(30) NOT NULL,
  `uri_field` varchar(30) NOT NULL default '',
  `view_uri` varchar(100) NOT NULL default '',
  `class` varchar(100) NOT NULL,
  `create_field` varchar(100) NOT NULL, 
  PRIMARY KEY  (`unit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
-- Dumping data for table `[db_prefix]field_mapping`
INSERT INTO `[db_prefix]field_mapping` (`unit`, `table`, `id_field`, `owner_field`, `title_field`, `desc_field`, `uri_field`, `view_uri`, `class`, `create_field`) VALUES
('ads', 'bx_ads_main', 'ID', 'IDProfile', 'Subject', 'Message', 'EntryUri', '{site_url}ads/entry/{uri}', 'BxAdsModule', 'DateTime'),
('blogposts', 'bx_blogs_posts', 'PostID', 'OwnerID', 'PostCaption', 'PostText', 'PostUri', '{site_url}blogs/entry/{uri}', 'BxBlogsModule', 'PostDate'),
('bx_news', 'bx_news_entries', 'id', 'author_id', 'caption', 'content', 'uri', '{module_url}view/{uri}', 'BxNewsModule', 'date'),
('bx_arl', 'bx_arl_entries', 'id', 'author_id', 'caption', 'content', 'uri', '{module_url}view/{uri}', 'BxArlModule', 'date'),
('bx_events', 'bx_events_main', 'ID', 'ResponsibleID', 'Title', 'Description', 'EntryUri', '{module_url}view/{uri}', 'BxEventsModule', 'Date'),
('bx_files', 'bx_files_main', 'ID', 'Owner', 'Title', 'Desc', 'Uri', '{module_url}view/{uri}', 'BxFilesModule', 'Date'),
('bx_groups', 'bx_groups_main', 'id', 'author_id', 'title', 'desc', 'uri', '{module_url}view/{uri}', 'BxGroupsModule', 'created'),
('bx_photos', 'bx_photos_main', 'ID', 'Owner', 'Title', 'Desc', 'Uri', '{module_url}view/{uri}', 'BxPhotosModule', 'Date'),
('bx_poll', 'bx_poll_data', 'id_poll', 'id_profile', 'poll_question', 'poll_question', 'id_poll', '{module_url}&action=show_poll_info&id={id}', 'BxPollModule', 'poll_date'),
('bx_sites', 'bx_sites_main', 'id', 'ownerid', 'title', 'description', 'entryUri', '{module_url}view/{uri}', 'BxSitesModule', 'date'),
('bx_store', 'bx_store_products', 'id', 'author_id', 'title', 'desc', 'uri', '{module_url}view/{uri}', 'BxStoreModule', 'created'),
('bx_videos', 'RayVideoFiles', 'ID', 'Owner', 'Title', 'Description', 'Uri', '{module_url}view/{uri}', 'BxVideosModule', 'Date'),
('bx_sounds', 'RayMp3Files', 'ID', 'Owner', 'Title', 'Description', 'Uri', '{module_url}view/{uri}', 'BxSoundsModule', 'Date');

  

ALTER TABLE `sys_objects_actions` CHANGE `Type` `Type` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_alerts_my', 'Alerts My', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_alerts_main', 'Alerts Home', @iMaxOrder+2);
 
-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
 
    ('modzzz_alerts_my', '998px', 'Administration Owner', '_modzzz_alerts_block_administration_owner', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
    ('modzzz_alerts_my', '998px', 'User''s Alerts', '_modzzz_alerts_block_users_alerts', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),
 
    ('modzzz_alerts_main', '998px', 'Alerts description block', '_modzzz_alerts_block_friend_actions', '1', '0', 'Desc', '', '1', '100', 'non,memb', '0'),
    
    ('member', '998px', 'Alerts', '_modzzz_alerts_block_accountpage', 2, 2, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''alerts'', ''account_block'');', 1, 66, 'non,memb', 0) 
    ;

-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=alerts/', 'm/alerts/', 'modzzz_alerts_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Alerts', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_alerts_permalinks', 'on', 26, 'Enable friendly permalinks in alerts', 'checkbox', '', '', '0', ''),
('modzzz_alerts_activated', 'on', @iCategId, 'Alerts activated', 'checkbox', '', '', '0', '') 
;
  
INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
 ( 'BxAlerts', '1 0 * * *', 'BxAlertsCron', 'modules/modzzz/alerts/classes/BxAlertsCron.php', '') ;
  
-- top menu 
SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Alerts', '_modzzz_alerts_menu_root', 'modules/?r=alerts/home/|modules/?r=alerts/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'modules/modzzz/alerts/|modzzz_alerts.png', '', 1, '');
SET @iCatRoot := LAST_INSERT_ID();

INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Alerts My Page', '_modzzz_alerts_menu_my', 'modules/?r=alerts/browse/my', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modzzz/alerts/|modzzz_alerts.png', '', 0, '');

 
-- member menu
SET @iParentID = IFNULL((SELECT `ID` FROM `sys_menu_top` WHERE `Link` = 'member.php' AND `Type`='top' AND `Active`=1 LIMIT 1),1);


INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iParentID, 'Alerts Activity', '_modzzz_alerts_activity', 'm/alerts/browse/my/', 10, 'memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''); 
  
-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_alerts', '_modzzz_alerts', '{siteUrl}modules/?r=alerts/administration/', 'Alerts module by Modzzz','modules/modzzz/alerts/|alerts.png', @iMax+1);


-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_alerts_profile_delete', '', '', 'BxDolService::call(''alerts'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_alerts_profile_join', '', '', 'BxDolService::call(''alerts'', ''response_profile_join'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'join', @iHandler);
 
-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_alerts', 'BxAlertsResponse', 'modules/modzzz/alerts/classes/BxAlertsResponse.php', '');
SET @iHandler := LAST_INSERT_ID();

INSERT INTO `sys_alerts` VALUES 
  
(NULL, 'blogposts', 'commentPost', @iHandler),
(NULL, 'bx_blogs', 'create', @iHandler),
(NULL, 'bx_sites', 'commentPost', @iHandler),
(NULL, 'bx_sites', 'add', @iHandler),
(NULL, 'bx_videos', 'commentPost', @iHandler),
(NULL, 'bx_videos', 'add', @iHandler),
(NULL, 'bx_photos', 'commentPost', @iHandler),
(NULL, 'bx_photos', 'add', @iHandler),
(NULL, 'bx_sounds', 'commentPost', @iHandler),
(NULL, 'bx_sounds', 'add', @iHandler),
(NULL, 'bx_groups', 'commentPost', @iHandler),
(NULL, 'bx_groups', 'add', @iHandler),
(NULL, 'bx_events', 'commentPost', @iHandler),
(NULL, 'bx_events', 'add', @iHandler),
(NULL, 'bx_store', 'commentPost', @iHandler),
(NULL, 'bx_store', 'add', @iHandler),
(NULL, 'bx_news', 'commentPost', @iHandler),
(NULL, 'bx_arl', 'commentPost', @iHandler),
(NULL, 'ads', 'commentPost', @iHandler),
(NULL, 'ads', 'create', @iHandler),
(NULL, 'profile', 'commentPost', @iHandler),
(NULL, 'bx_poll', 'add', @iHandler),
(NULL, 'bx_poll', 'commentPost', @iHandler),
(NULL, 'bx_wall', 'update', @iHandler),
(NULL, 'bx_forum', 'reply', @iHandler),
(NULL, 'bx_forum', 'new_topic', @iHandler),
(NULL, 'bx_files', 'add', @iHandler),
(NULL, 'bx_files', 'commentPost', @iHandler)
 ;

-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'alerts add alert', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'alerts edit any alert', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'alerts delete any alert', NULL);

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_alerts_now_notify', 'Alerts for Key Phrase (<Phrase>) on <SiteName>', '<html><head></head><body style="font: 12px Verdana; color:#000000"><p><b>Dear <RecipientName></b>,</p><p>Alerts for Key Phrase <b><Phrase></b> on <a href="<SiteUrl>"><SiteName></a></p><p><b>CONTENT TYPE:: <Group></b><BR><Data></p><BR><BR><p><b>Thank you for using our services!</b></p><p>---</p><p style="font: bold 10px Verdana; color:red">!!!Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Key Phrase Alerts Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_alerts_daily_notify', 'Daily Alerts for Key Phrase (<Phrase>) on <SiteName>', '<html><head></head><body style="font: 12px Verdana; color:#000000"><p><b>Dear <RecipientName></b>,</p><p>Daily Alerts for Key Phrase <b><Phrase></b> on <a href="<SiteUrl>"><SiteName></a></p><p><Data></p><BR><BR><p><b>Thank you for using our services!</b></p><p>---</p><p style="font: bold 10px Verdana; color:red">!!!Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Daily Key Phrase Alerts Notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_alerts_weekly_notify', 'Weekly Alerts for Key Phrase (<Phrase>) on <SiteName>', '<html><head></head><body style="font: 12px Verdana; color:#000000"><p><b>Dear <RecipientName></b>,</p><p>Weekly Alerts for Key Phrase <b><Phrase></b> on <a href="<SiteUrl>"><SiteName></a></p><p><Data></p><BR><BR><p><b>Thank you for using our services!</b></p><p>---</p><p style="font: bold 10px Verdana; color:red">!!!Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Weekly Key Phrase Alerts Notification', '0');


INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('modzzz_daily_alerts', '0 0 * * *', 'BxAlertsDailyCron', 'modules/modzzz/alerts/classes/BxAlertsDailyCron.php', '');

INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('modzzz_weekly_alerts', '0 0 * * 0', 'BxAlertsWeeklyCron', 'modules/modzzz/alerts/classes/BxAlertsWeeklyCron.php', '');