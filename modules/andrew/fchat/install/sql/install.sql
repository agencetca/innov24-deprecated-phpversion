-- tables
CREATE TABLE `[db_prefix]_messages` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `sender` int(11) unsigned NOT NULL,
  `recipient` int(11) unsigned NOT NULL default '0',
  `message` VARCHAR(255) NOT NULL,
  `when` int(11) NOT NULL default '0',
  `room` int(5) unsigned NOT NULL default '0',
  `type` tinyint(1) unsigned NOT NULL default '0',
  `offmsg` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]_rooms` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` VARCHAR(255) NOT NULL,
  `owner` int(11) unsigned NOT NULL,
  `when` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `[db_prefix]_rooms` (`id`, `title`, `owner`, `when`) VALUES
(NULL, 'Room1', 0, UNIX_TIMESTAMP()),
(NULL, 'Room2', 0, UNIX_TIMESTAMP()),
(NULL, 'Room3', 0, UNIX_TIMESTAMP());

-- injections
INSERT INTO `sys_injections` (`id`, `name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES
(NULL, 'fchat_injection', 0, 'injection_between_content_footer', 'php', 'return BxDolService::call("fchat", "fchat_injection");', 0, 1);

-- admin menu
SET @iExtOrd = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `id`=2);
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, 'FChat', '_fch_main', '{siteUrl}modules/?r=fchat/administration/', 'F-Chat module by AndrewP', 'modules/andrew/fchat/|fch16.png', '', '', @iExtOrd+1);

-- top menu
SET @iTopMenuLastOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'FChat', '_fch_main', 'm/fchat/home/|modules/?r=fchat/home/', @iTopMenuLastOrder, 'memb', '', '', '', 1, 1, 1, 'top', 'modules/andrew/fchat/|fchat.png', 1, '');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` VALUES(NULL, 'FChat', @iMaxOrder);
SET @iGlCategID = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('permalinks_[db_prefix]', 'on', 26, 'Enable friendly permalinks for FChat', 'checkbox', '', '', NULL, ''),
('[db_prefix]_online_cnt', '10', @iGlCategID, 'Online members amount (sidebar)', 'digit', '', '', NULL, ''),
('[db_prefix]_offline_cnt', '10', @iGlCategID, 'Offline members amount (sidebar)', 'digit', '', '', NULL, ''),
('[db_prefix]_fr_mode', '', @iGlCategID, 'Send messages as in-site messages for non-friends (rather than chat messages)', 'checkbox', '', '', NULL, ''),
('[db_prefix]_display', 'onlinefriends,online,friends,last', @iGlCategID, 'Profiles types to display in float sidebar', 'list', '', '', NULL, 'onlinefriends,online,friends,last');

-- permalinks
INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
('modules/?r=fchat/', 'm/fchat/', 'permalinks_[db_prefix]');

-- blocks
INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'index', '998px', 'FChat', '_fch_main', 1, 1, 'PHP', 'return BxDolService::call(''fchat'', ''page_compose_block'');', 1, 34, 'memb', 0),
(NULL, 'member', '998px', 'FChat', '_fch_main', 1, 1, 'PHP', 'return BxDolService::call(''fchat'', ''page_compose_block'', array());', 1, 34, 'memb', 0),
(NULL, 'profile', '998px', 'FChat', '_fch_main', 1, 1, 'PHP', 'return BxDolService::call(''fchat'', ''page_compose_block'', array());', 1, 34, 'memb', 0);

-- cron jobs
INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('fchat', '58 23 * * *', 'AFChatCron', 'modules/andrew/fchat/classes/AFChatCron.php', '');

-- email templates
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`) VALUES 
('t_fchat_notify', 'You have offline messages (fchat)', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear member</b>,</p><br /><p>Someone sent offline messages for you. Feel free to visit our website and read your unread messages.</p>\r\n<bx_include_auto:_email_footer.html />', 'Offline messages notification (FCHAT module)');