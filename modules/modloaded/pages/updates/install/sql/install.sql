-- create tables

CREATE TABLE IF NOT EXISTS `[db_prefix]images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]fans` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  `confirmed` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id_entry`,`id_profile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]admins` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_entry`, `id_profile`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]rating` (
  `gal_id` int(10) unsigned NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]rating_track` (
  `gal_id` int(10) unsigned NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]views_track` (
  `id` int(10) unsigned NOT NULL,
  `viewer` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `ts` int(10) unsigned NOT NULL,
  KEY `id` (`id`,`viewer`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- create forum tables

CREATE TABLE `[db_prefix]forum` (
  `forum_id` int(10) unsigned NOT NULL auto_increment,
  `forum_uri` varchar(255) NOT NULL default '',
  `cat_id` int(11) NOT NULL default '0',
  `forum_title` varchar(255) default NULL,
  `forum_desc` varchar(255) NOT NULL default '',
  `forum_posts` int(11) NOT NULL default '0',
  `forum_topics` int(11) NOT NULL default '0',
  `forum_last` int(11) NOT NULL default '0',
  `forum_type` enum('public','private') NOT NULL default 'public',
  `forum_order` int(11) NOT NULL default '0',
  `entry_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`forum_id`),
  KEY `cat_id` (`cat_id`),
  KEY `forum_uri` (`forum_uri`),
  KEY `entry_id` (`entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_cat` (
  `cat_id` int(10) unsigned NOT NULL auto_increment,
  `cat_uri` varchar(255) NOT NULL default '',
  `cat_name` varchar(255) default NULL,
  `cat_icon` varchar(32) NOT NULL default '',
  `cat_order` float NOT NULL default '0',
  `cat_expanded` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`cat_id`),
  KEY `cat_order` (`cat_order`),
  KEY `cat_uri` (`cat_uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `[db_prefix]forum_cat` (`cat_id`, `cat_uri`, `cat_name`, `cat_icon`, `cat_order`) VALUES 
(1, 'Pages', 'Pages', '', 64);

CREATE TABLE `[db_prefix]forum_flag` (
  `user` varchar(32) NOT NULL default '',
  `topic_id` int(11) NOT NULL default '0',
  `when` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_post` (
  `post_id` int(10) unsigned NOT NULL auto_increment,
  `topic_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  `user` varchar(32) NOT NULL default '0',
  `post_text` mediumtext NOT NULL,
  `when` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `reports` int(11) NOT NULL default '0',
  `hidden` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`post_id`),
  KEY `topic_id` (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `user` (`user`),
  KEY `when` (`when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_topic` (
  `topic_id` int(10) unsigned NOT NULL auto_increment,
  `topic_uri` varchar(255) NOT NULL default '',
  `forum_id` int(11) NOT NULL default '0',
  `topic_title` varchar(255) NOT NULL default '',
  `when` int(11) NOT NULL default '0',
  `topic_posts` int(11) NOT NULL default '0',
  `first_post_user` varchar(32) NOT NULL default '0',
  `first_post_when` int(11) NOT NULL default '0',
  `last_post_user` varchar(32) NOT NULL default '',
  `last_post_when` int(11) NOT NULL default '0',
  `topic_sticky` int(11) NOT NULL default '0',
  `topic_locked` tinyint(4) NOT NULL default '0',
  `topic_hidden` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `forum_id_2` (`forum_id`,`when`),
  KEY `topic_uri` (`topic_uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_user` (
  `user_name` varchar(32) NOT NULL default '',
  `user_pwd` varchar(32) NOT NULL default '',
  `user_email` varchar(128) NOT NULL default '',
  `user_join_date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_user_activity` (
  `user` varchar(32) NOT NULL default '',
  `act_current` int(11) NOT NULL default '0',
  `act_last` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_user_stat` (
  `user` varchar(32) NOT NULL default '',
  `posts` int(11) NOT NULL default '0',
  `user_last_post` int(11) NOT NULL default '0',
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_vote` (
  `user_name` varchar(32) NOT NULL default '',
  `post_id` int(11) NOT NULL default '0',
  `vote_when` int(11) NOT NULL default '0',
  `vote_point` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`user_name`,`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_actions_log` (
  `user_name` varchar(32) NOT NULL default '',
  `id` int(11) NOT NULL default '0',
  `action_name` varchar(32) NOT NULL default '',
  `action_when` int(11) NOT NULL default '0',
  KEY `action_when` (`action_when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_attachments` (
  `att_hash` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `att_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `att_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `att_when` int(11) NOT NULL,
  `att_size` int(11) NOT NULL,
  `att_downloads` int(11) NOT NULL,
  PRIMARY KEY (`att_hash`),
  KEY `post_id` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]forum_signatures` (
  `user` varchar(32) NOT NULL,
  `signature` varchar(255) NOT NULL,
  `when` int(11) NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('ml_pages_celendar', 'Pages Calendar', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('ml_pages_main', 'Main Pages Page', @iMaxOrder+2);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('ml_pages_my', 'My Pages Page', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('ml_pages_view', 'Page View', @iMaxOrder+4);

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('ml_pages_view', '998px', 'Page''s actions block', '_ml_pages_block_actions', '1', '0', 'Actions', '', '1', '34', 'non,memb', '0'),
    ('ml_pages_view', '998px', 'Page''s page primary photo block', '_ml_pages_page_logo', '1', '0', 'PrimPhoto', '', '1', '34', 'non,memb', '0'),
    ('ml_pages_view', '998px', 'Page''s rate block', '_ml_pages_block_rate', '1', '1', 'Rate', '', '1', '34', 'non,memb', '0'),    
    --('ml_pages_view', '998px', 'Page''s info block', '_ml_pages_block_info', '2', '0', 'Info', '', '1', '66', 'non,memb', '0'),  
    ('ml_pages_view', '998px', 'Page''s info block', '_ml_pages_block_info', '2', '0', 'PFBlock', '17', '1', '66', 'non,memb', '0'),
    ('ml_pages_view', '998px', 'Page''s misc block', '_ml_pages_block_misc', '2', '0', 'PFBlock', '20', '1', '66', 'non,memb', '0'),    
		('ml_pages_view', '998px', 'Page''s author block', '_ml_pages_block_author', '1', '2', 'Author', '', '1', '34', 'non,memb', '0'),
		('ml_pages_view', '998px', 'Page''s embed block', '_ml_pages_block_embed', '1', '3', 'Embed', '', '1', '34', 'non,memb', '0'),
    ('ml_pages_view', '998px', 'Page''s fans block', '_ml_pages_block_fans', '1', '3', 'Fans', '', '1', '34', 'non,memb', '0'),
    ('ml_pages_view', '998px', 'Page''s description block', '_ml_pages_block_desc', '2', '0', 'Desc', '', '1', '66', 'non,memb', '0'),
    ('ml_pages_view', '998px', 'Page''s photos block', '_ml_pages_block_photos', '2', '1', 'Photos', '', '1', '66', 'non,memb', '0'),
    ('ml_pages_view', '998px', 'Page''s videos block', '_ml_pages_block_videos', '2', '2', 'Videos', '', '1', '66', 'non,memb', '0'),
    ('ml_pages_view', '998px', 'Page''s sounds block', '_ml_pages_block_sounds', '2', '3', 'Sounds', '', '1', '66', 'non,memb', '0'),
    ('ml_pages_view', '998px', 'Page''s files block', '_ml_pages_block_files', '2', '4', 'Files', '', '1', '66', 'non,memb', '0'),
    --('ml_pages_view', '998px', 'Page''s comments block', '_ml_pages_block_comments', '2', '5', 'Comments', '', '1', '66', 'non,memb', '0'),    


    ('ml_pages_main', '998px', 'Recently Added Pages', '_ml_pages_block_recently_added_list', '1', '1', 'RecentlyAddedList', '', '1', '100', 'non,memb', '0'),
		

    ('ml_pages_my', '998px', 'Administration', '_ml_pages_block_administration', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
    ('ml_pages_my', '998px', 'User''s pages', '_ml_pages_block_user_pages', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),

    ('index', '998px', 'Pages', '_ml_pages_block_home', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''pages'', ''homepage_block'');', 1, 66, 'non,memb', 0),
	('profile', '998px', 'Joined Pages', '_ml_pages_block_joined_pages', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''pages'', ''profile_block_joined'', array($this->oProfileGen->_iProfileID));', 1, 66, 'non,memb', 0),
    ('profile', '998px', 'User Pages', '_ml_pages_block_my_pages', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''pages'', ''profile_block'', array($this->oProfileGen->_iProfileID));', 1, 66, 'non,memb', 0);
    --('ml_pages_main', '998px', 'Upcoming Pages Photo', '_ml_pages_block_upcoming_photo', '2', '0', 'UpcomingPhoto', '', '1', '66', 'non,memb', '0'),
    --('ml_pages_main', '998px', 'Upcoming Pages List', '_ml_pages_block_upcoming_list', '2', '1', 'UpcomingList', '', '1', '66', 'non,memb', '0'),
    --('ml_pages_main', '998px', 'Past Pages', '_ml_pages_block_past_list', '1', '0', 'PastList', '', '1', '34', 'non,memb', '0'),
--    ('ml_pages_view', '998px', 'Page''s unconfirmed fans block', '_ml_pages_block_fans_unconfirmed', '1', '4', 'FansUnconfirmed', '', '1', '34', 'non,memb', '0'),

-- permalink
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=pages/', 'm/pages/', 'ml_pages_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Pages', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('category_auto_app_ml_pages', 'on', @iCategId, 'Activate all categories after creation automatically', 'checkbox', '', '', '0', ''),
('ml_pages_permalinks', 'on', 26, 'Enable friendly permalinks in pages', 'checkbox', '', '', '0', ''),
('ml_pages_autoapproval', 'on', @iCategId, 'Activate all pages after creation automatically', 'checkbox', '', '', '0', ''),
('ml_pages_main_upcoming_page_from_featured_only', '', @iCategId, 'Main page from featured pages only', 'checkbox', '', '', '0', ''),
('ml_pages_max_email_invitations', '10', @iCategId, 'Max number of email invitation to send per one invite', 'digit', '', '', '0', ''),
('ml_pages_perpage_main_upcoming', '10', @iCategId, 'Number of pages to show on main page', 'digit', '', '', '0', ''),
('ml_pages_perpage_main_recent', '4', @iCategId, 'Number of recently added pages to show on main page', 'digit', '', '', '0', ''),
('ml_pages_perpage_main_past', '6', @iCategId, 'Number of past pages to show on main page', 'digit', '', '', '0', ''),
('ml_pages_perpage_fans', '9', @iCategId, 'Number of fans to show on page view page', 'digit', '', '', '0', ''),
('ml_pages_perpage_browse_fans', '30', @iCategId, 'Number of items to show on browse fans page', 'digit', '', '', '0', ''),
('ml_pages_perpage_browse', '14', @iCategId, 'Number of pages to show on browse pages', 'digit', '', '', '0', ''),
('ml_pages_perpage_homepage', '5', @iCategId, 'Number of pages to show on homepage', 'digit', '', '', '0', ''),
('ml_pages_perpage_profile', '5', @iCategId, 'Number of pages to show on pages page', 'digit', '', '', '0', ''),
('ml_pages_cat_icowidth', '60', @iCategId, 'Category icon width', 'digit', '', '', '0', ''),
('ml_pages_cat_icoheigth', '60', @iCategId, 'Category icon heigth', 'digit', '', '', '0', ''),
('ml_pages_multi_divider', ';', @iCategId, 'Multiple inputs divider character', 'text', '', '', '0', ''),
('ml_pages_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', '0', '');
--('ml_pages_homepage_default_tab', 'upcoming', @iCategId, 'Default block tab on homepage', 'select', '', '', '0', 'upcoming,featured,recent,top,popular'),

-- search objects
INSERT INTO `sys_objects_search` VALUES(NULL, 'ml_pages', '_ml_pages', 'MlPagesSearchResult', 'modules/modloaded/pages/classes/MlPagesSearchResult.php');

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'ml_pages', 'ml_pages_rating', 'ml_pages_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'ml_pages_main', 'Rate', 'RateCount', 'ID', 'MlPagesVoting', 'modules/modloaded/pages/classes/MlPagesVoting.php');

-- comments objects
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'ml_pages', 'ml_pages_cmts', 'ml_pages_cmts_track', '0', '1', '90', '5', '1', '-3', 'slide', '2000', '1', '1', 'cmt', 'ml_pages_main', 'ID', 'CommentsCount', 'MlPagesCmts', 'modules/modloaded/pages/classes/MlPagesCmts.php');

-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'ml_pages', 'ml_pages_views_track', 86400, 'ml_pages_main', 'ID', 'Views', 1);

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'ml_pages', 'SELECT `Tags` FROM `[db_prefix]main` WHERE `ID` = {iID} AND `Status` = ''approved''', 'ml_pages_permalinks', 'm/pages/browse/tag/{tag}', 'modules/?r=pages/browse/tag/{tag}', '_ml_pages');

-- category objects
INSERT INTO `sys_objects_categories` VALUES (NULL, 'ml_pages', 'SELECT `Categories` FROM `[db_prefix]main` WHERE `ID` = {iID} AND `Status` = ''approved''', 'ml_pages_permalinks', 'm/pages/browse/category/{tag}', 'modules/?r=pages/browse/category/{tag}', '_ml_pages');

INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES 
('Pages', '0', 'bx_photos', '0', 'active'),
('Party', '0', 'ml_pages', '0', 'active'),
('Expedition', '0', 'ml_pages', '0', 'active'),
('Presentation', '0', 'ml_pages', '0', 'active'),
('Last Friday', '0', 'ml_pages', '0', 'active'),
('Birthday', '0', 'ml_pages', '0', 'active'),
('Exhibition', '0', 'ml_pages', '0', 'active'),
('Bushwalking', '0', 'ml_pages', '0', 'active');

-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 

    ('{TitleEdit}', 'modules/modloaded/pages/|edit.png', '{evalResult}', '', '$oConfig = $GLOBALS[''oMlPagesModule'']->_oConfig; return  BX_DOL_URL_ROOT . ''modules/modloaded/pages/edit.php?page_id={ID}'';', '0', 'ml_pages'),
		('{evalResult}', 'modules/modloaded/pages/|calendar_add.png', 'modules/modloaded/pages/create.php', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_ml_pages_action_create_page'') : '''';', '1', 'ml_pages_title'),
    ('{TitlePrivacy}', 'modules/modloaded/pages/|privacy.png', '{evalResult}', '', '$oConfig = $GLOBALS[''oMlPagesModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''edit/{ID}'';', '1', 'ml_pages'),
    ('{TitleDelete}', 'modules/modloaded/pages/|action_block.png', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oMlPagesModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', '2', 'ml_pages'),
    ('{TitleJoin}', 'modules/modloaded/pages/|user_add.png', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oMlPagesModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''join/{ID}/{iViewer}'';', '3', 'ml_pages'),
    ('{TitleInvite}', 'modules/modloaded/pages/|group_add.png', '{evalResult}', '', '$oConfig = $GLOBALS[''oMlPagesModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''invite/{ID}'';', '4', 'ml_pages'),
    ('{TitleShare}', 'modules/modloaded/pages/|action_share.png', '', 'showPopupAnyHtml (''{BaseUri}share_popup/{ID}'');', '', '5', 'ml_pages'),
    ('{TitleBroadcast}', 'modules/modloaded/pages/|action_broadcast.png', '{BaseUri}broadcast/{ID}', '', '', '6', 'ml_pages'),
    ('{AddToFeatured}', 'modules/modloaded/pages/|star__plus.png', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oMlPagesModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_featured/{ID}'';', '7', 'ml_pages'),

    ('{TitleManageFans}', 'modules/modloaded/pages/|action_manage_fans.png', '', 'showPopupAnyHtml (''{BaseUri}manage_fans_popup/{ID}'');', '', '8', 'ml_pages'),
    ('{TitleUploadPhotos}', 'modules/modloaded/pages/|action_upload_photos.png', '{BaseUri}upload_photos/{URI}', '', '', '9', 'ml_pages'),
    ('{TitleUploadVideos}', 'modules/modloaded/pages/|action_upload_videos.png', '{BaseUri}upload_videos/{URI}', '', '', '10', 'ml_pages'),
    ('{TitleEmbedVideos}', 'modules/modloaded/pages/|action_embed_video.png', '{BaseUri}embed_videos/{URI}', '', '', '11', 'ml_pages'),
    ('{TitleUploadSounds}', 'modules/modloaded/pages/|action_upload_sounds.png', '{BaseUri}upload_sounds/{URI}', '', '', '12', 'ml_pages'),
    ('{TitleUploadFiles}', 'modules/modloaded/pages/|action_upload_files.png', '{BaseUri}upload_files/{URI}', '', '', '13', 'ml_pages'),    

    ('{TitleSubscribe}', 'action_subscribe.png', '', '{ScriptSubscribe}', '', 7, 'ml_pages'),
    ('{evalResult}', 'modules/modloaded/pages/|pages.png', '{BaseUri}browse/my', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_ml_pages_action_my_pages'') : '''';', '2', 'ml_pages_title'),
    ('{evalResult}', 'modules/modloaded/pages/|pages.png', '{BaseUri}', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_ml_pages_action_pages_home'') : '''';', '3', 'ml_pages_title');


    
-- top menu
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Pages', '_ml_pages_menu_root', 'modules/?r=pages/view/|modules/?r=pages/broadcast/|modules/?r=pages/invite/|modules/?r=pages/edit/|modules/?r=pages/upload_photos/|modules/?r=pages/upload_videos/|modules/?r=pages/upload_sounds/|modules/?r=pages/upload_files/|modules/?r=pages/photos/|modules/?r=pages/videos/|modules/?r=pages/sounds/|modules/?r=pages/files/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', 'modules/modloaded/pages/|ml_pages.png', '', '0', '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Page View', '_ml_pages_menu_view', 'modules/?r=pages/view/{ml_pages_view_uri}', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, ''),
(NULL, @iCatRoot, 'Page View Forum', '_ml_pages_menu_view_forum', 'forum/pages/forum/{ml_pages_view_uri}-0.htm|forum/pages/', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, ''),
(NULL, @iCatRoot, 'Page View Comments', '_ml_pages_menu_view_comments', 'modules/?r=pages/comments/{ml_pages_view_uri}', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, ''),
(NULL, @iCatRoot, 'Page View Fans', '_ml_pages_menu_view_fans', 'modules/?r=pages/browse_fans/{ml_pages_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, ''),
(NULL, @iCatRoot, 'Page View Photos', '_ml_pages_menu_view_photos', 'modules/?r=pages/photos/{ml_pages_view_uri}', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, ''),
(NULL, @iCatRoot, 'Page View Videos', '_ml_pages_menu_view_videos', 'modules/?r=pages/videos/{ml_pages_view_uri}', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, ''),
(NULL, @iCatRoot, 'Page View Sounds', '_ml_pages_menu_view_sounds', 'modules/?r=pages/sounds/{ml_pages_view_uri}', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, ''),
(NULL, @iCatRoot, 'Page View Files', '_ml_pages_menu_view_files', 'modules/?r=pages/files/{ml_pages_view_uri}', 7, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, '');


SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Pages', '_ml_pages_menu_root', 'modules/?r=pages/home/|modules/?r=pages/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'modules/modloaded/pages/|ml_pages.png', '', 1, '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Pages Main Page', '_ml_pages_menu_main', 'modules/?r=pages/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, ''),
(NULL, @iCatRoot, 'Recently Added Pages', '_ml_pages_menu_recently_added', 'modules/?r=pages/browse/recent', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, ''),
(NULL, @iCatRoot, 'Top Rated Pages', '_ml_pages_menu_top_rated', 'modules/?r=pages/browse/top', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, ''),
(NULL, @iCatRoot, 'Popular Pages', '_ml_pages_menu_popular', 'modules/?r=pages/browse/popular', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, ''),
(NULL, @iCatRoot, 'Featured Pages', '_ml_pages_menu_featured', 'modules/?r=pages/browse/featured', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, ''),
(NULL, @iCatRoot, 'Pages Tags', '_ml_pages_menu_tags', 'modules/?r=pages/tags', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, 'ml_pages'),
(NULL, @iCatRoot, 'Pages Categories', '_ml_pages_menu_categories', 'modules/?r=pages/categories', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, 'ml_pages'),
(NULL, @iCatRoot, 'Search', '_ml_pages_menu_search', 'modules/?r=pages/search', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modloaded/pages/|ml_pages.png', '', 0, '');

--SET @iCatProfile := (SELECT `ID` FROM `sys_menu_top` WHERE `Parent` = 0 AND `Name` = 'View Profile' LIMIT 1);
SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 9 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 9, 'Pages', '_ml_pages_menu_my_pages_profile', 'modules/?r=pages/browse/user/{profileNick}|modules/?r=pages/browse/joined/{profileNick}', @iCatProfileOrder, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 4 ORDER BY `Order` DESC LIMIT 1);
--INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
--(NULL, 4, 'Pages', '_ml_pages_menu_my_pages_profile', 'modules/?r=pages/browse/my', @iCatProfileOrder, 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'ml_pages', '_ml_pages', '{siteUrl}modules/?r=pages/administration/', 'Pages module by Modloaded', 'modules/modloaded/pages/|pages.png', @iMax+1);

-- email templates
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('ml_pages_invitation', 'Invitation to page: <PageName>', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p><a href="<InviterUrl>"><InviterNickName></a> has invited you to his page:</p> <pre><InvitationText></pre> <p> <b>Page Information:</b><br /> Name: <PageName><br /> Location: <PageLocation><br /> Date of beginning: <PageStart><br /> <a href="<PageUrl>">More details</a> </p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Pages invitation template', '0'),

('ml_pages_broadcast', '<BroadcastTitle>', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p><a href="<EntryUrl>"><EntryTitle></a> page admin has sent the following broadcast message:</p> <pre><BroadcastMessage></pre> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Pages broadcast message template', '0'),

('ml_pages_sbs', 'Page was changed', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p><a href="<ViewLink>"><EntryTitle></a> page was changed: <br /> <ActionName> </p> <p>You may cancel the subscription by clicking the following link: <a href="<UnsubscribeLink>"><UnsubscribeLink></a></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Pages subscription template', '0'),

('ml_pages_join_request', 'New fan request to your page', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p>New fan request in your page <a href="<EntryUrl>"><EntryTitle></a>. Please review this request and reject or confirm it.</p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New fan request to an page notification message', '0'),

('ml_pages_join_reject', 'Your fan request to an page was rejected', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p>Sorry, but your request to fan <a href="<EntryUrl>"><EntryTitle></a> page was rejected by page admin(s).</p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Fan request to an page was rejected notification message', '0'),

('ml_pages_join_confirm', 'Your fan request to an page was confirmed', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p>Congratulations! Your request to fan <a href="<EntryUrl>"><EntryTitle></a> page was confirmed by page admin(s).</p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Fan request to an page was confirmed notification message', '0'),

('ml_pages_fan_remove', 'You was removed from fans of an page', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p>You was removed from fans of <a href="<EntryUrl>"><EntryTitle></a> page by page admin(s).</p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'User was removed from fans of page notification message', '0'),

('ml_pages_fan_become_admin', 'You become admin of an page', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p>Congratulations! You become admin of <a href="<EntryUrl>"><EntryTitle></a> page.</p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'User become admin of an page notification message', '0'),

('ml_pages_admin_become_fan', 'You page admin status was removed', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p>Your admin status was removed from <a href="<EntryUrl>"><EntryTitle></a> page by page author.</p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'User page admin status was removed notification message', '0');

-- site stats
INSERT INTO `sys_stat_site` VALUES(NULL, 'evs', 'ml_pages', 'modules/?r=pages/', 'SELECT COUNT(`ID`) FROM `[db_prefix]main` WHERE `Status` = ''approved''', '../modules/?r=pages/administration', 'SELECT COUNT(`ID`) FROM `[db_prefix]main` WHERE `Status` != ''approved''', 'modules/modloaded/pages/|pages.png', 0);

-- PQ statistics
INSERT INTO `sys_stat_member` VALUES ('ml_pages', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `ResponsibleID` = ''__member_id__'' AND `Status`=''approved''');
INSERT INTO `sys_stat_member` VALUES ('ml_pagesp', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `ResponsibleID` = ''__member_id__'' AND `Status`!=''approved''');
INSERT INTO `sys_account_custom_stat_elements` VALUES(NULL, '_ml_pages', '__ml_pages__ __l_created__ (<a href="modules/modloaded/pages/create.php">__l_add__</a>)');

-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'pages view', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'pages browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'pages search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'pages add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'pages comments delete and edit', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'pages edit any page', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'pages delete any page', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'pages mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'pages approve', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'pages broadcast message', NULL);

-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ml_pages_profile_delete', '', '', 'BxDolService::call(''pages'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ml_pages_media_delete', '', '', 'BxDolService::call(''pages'', ''response_media_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_videos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_sounds', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_files', 'delete', @iHandler);


-- member menu
INSERT INTO 
    `sys_menu_member` 
SET
    `Name` = 'ml_pages',
    `Eval` = 'return BxDolService::call(''pages'', ''get_member_menu_item'', array({ID}));',
    `Type` = 'linked_item',
    `Parent` = '1';

-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('pages', 'view_page', '_ml_pages_privacy_view_page', '3'),
('pages', 'join', '_ml_pages_privacy_join', '3'),
('pages', 'comment', '_ml_pages_privacy_comment', '3'),
('pages', 'rate', '_ml_pages_privacy_rate', '3'),
('pages', 'view_fans', '_ml_pages_privacy_view_fans', '3'),
('pages', 'post_in_forum', '_ml_pages_privacy_post_in_forum', 'p'),
('pages', 'upload_photos', '_ml_pages_privacy_upload_photos', 'a'),
('pages', 'upload_videos', '_ml_pages_privacy_upload_videos', 'a'),
('pages', 'upload_sounds', '_ml_pages_privacy_upload_sounds', 'a'),
('pages', 'upload_files', '_ml_pages_privacy_upload_files', 'a');

-- subscriptions
INSERT INTO `sys_sbs_types` (`unit`, `action`, `template`, `params`) VALUES
('ml_pages', '', '', 'return BxDolService::call(''pages'', ''get_subscription_params'', array($arg2, $arg3));'),
('ml_pages', 'change', 'ml_pages_sbs', 'return BxDolService::call(''pages'', ''get_subscription_params'', array($arg2, $arg3));'),
('ml_pages', 'commentPost', 'ml_pages_sbs', 'return BxDolService::call(''pages'', ''get_subscription_params'', array($arg2, $arg3));'),
('ml_pages', 'rate', 'ml_pages_sbs', 'return BxDolService::call(''pages'', ''get_subscription_params'', array($arg2, $arg3));'),
('ml_pages', 'join', 'ml_pages_sbs', 'return BxDolService::call(''pages'', ''get_subscription_params'', array($arg2, $arg3));');

--
-- Dumping data for table `ml_pages_fields`
--

CREATE TABLE IF NOT EXISTS `ml_pages_fields` (
  `ID` smallint(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Type` enum('text','html_area','area','pass','date','select_one','select_set','num','range','bool','system','block') NOT NULL DEFAULT 'text',
  `Control` enum('select','checkbox','radio') DEFAULT NULL COMMENT 'input element for selectors',
  `Extra` text NOT NULL,
  `Min` float DEFAULT NULL,
  `Max` float DEFAULT NULL,
  `Values` text NOT NULL,
  `UseLKey` enum('LKey','LKey2','LKey3') NOT NULL DEFAULT 'LKey',
  `MultipleStyle` enum('Unordered','Ordered') NOT NULL,
  `MultipleCss` text NOT NULL,
  `Check` text NOT NULL,
  `Unique` tinyint(1) NOT NULL DEFAULT '0',
  `Default` text NOT NULL,
  `Mandatory` tinyint(1) NOT NULL DEFAULT '0',
  `Link` tinyint(1) NOT NULL DEFAULT '0',
  `Multiplyable` tinyint(1) NOT NULL,
  `WithPhoto` tinyint(1) NOT NULL,
  `BoxHeight` varchar(50) NOT NULL,
  `CaptionDisable` tinyint(1) NOT NULL,
  `Deletable` tinyint(1) NOT NULL DEFAULT '1',
  `JoinPage` int(10) unsigned NOT NULL DEFAULT '0',
  `JoinBlock` int(10) unsigned NOT NULL DEFAULT '0',
  `JoinOrder` float DEFAULT NULL,
  `EditOwnBlock` int(10) unsigned NOT NULL DEFAULT '0',
  `EditOwnOrder` float DEFAULT NULL,
  `EditAdmBlock` int(10) unsigned NOT NULL DEFAULT '0',
  `EditAdmOrder` float DEFAULT NULL,
  `EditModBlock` int(10) unsigned NOT NULL DEFAULT '0',
  `EditModOrder` float DEFAULT NULL,
  `ViewMembBlock` int(10) unsigned NOT NULL DEFAULT '0',
  `ViewMembOrder` float DEFAULT NULL,
  `ViewAdmBlock` int(10) unsigned NOT NULL DEFAULT '0',
  `ViewAdmOrder` float DEFAULT NULL,
  `ViewModBlock` int(10) unsigned NOT NULL DEFAULT '0',
  `ViewModOrder` float DEFAULT NULL,
  `ViewVisBlock` int(10) unsigned NOT NULL DEFAULT '0',
  `ViewVisOrder` float DEFAULT NULL,
  `SearchParams` text NOT NULL,
  `SearchSimpleBlock` int(10) unsigned NOT NULL DEFAULT '0',
  `SearchSimpleOrder` float DEFAULT NULL,
  `SearchQuickBlock` int(10) unsigned NOT NULL DEFAULT '0',
  `SearchQuickOrder` float DEFAULT NULL,
  `SearchAdvBlock` int(10) unsigned NOT NULL DEFAULT '0',
  `SearchAdvOrder` float DEFAULT NULL,
  `MatchField` int(10) unsigned NOT NULL DEFAULT '0',
  `MatchPercent` tinyint(7) unsigned NOT NULL DEFAULT '0',
  `JoinCategories` text,
  `EditCategories` text,
  `ViewCategories` text,
  `SearchCategories` text,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=81 ;

--
-- Dumping data for table `ml_pages_fields`
--

INSERT INTO `ml_pages_fields` (`ID`, `Name`, `Type`, `Control`, `Extra`, `Min`, `Max`, `Values`, `UseLKey`, `MultipleStyle`, `MultipleCss`, `Check`, `Unique`, `Default`, `Mandatory`, `Multiplyable`, `WithPhoto`, `BoxHeight`, `CaptionDisable`, `Deletable`, `JoinPage`, `JoinBlock`, `JoinOrder`, `EditOwnBlock`, `EditOwnOrder`, `EditAdmBlock`, `EditAdmOrder`, `EditModBlock`, `EditModOrder`, `ViewMembBlock`, `ViewMembOrder`, `ViewAdmBlock`, `ViewAdmOrder`, `ViewModBlock`, `ViewModOrder`, `ViewVisBlock`, `ViewVisOrder`, `SearchParams`, `SearchSimpleBlock`, `SearchSimpleOrder`, `SearchQuickBlock`, `SearchQuickOrder`, `SearchAdvBlock`, `SearchAdvOrder`, `MatchField`, `MatchPercent`, `JoinCategories`, `EditCategories`, `ViewCategories`, `SearchCategories`) VALUES
(1, 'ID', 'system', NULL, '', NULL, NULL, '', 'LKey', '', '', '', 1, '', 0, 0, '', '', 0, 0, 0, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 17, 1, 17, 1, 0, NULL, '', 17, 2, 17, 2, 0, NULL, 0, 0, '', NULL, NULL, NULL),
(2, 'Title', 'text', NULL, '', 5, 254, '', 'LKey', '', '', '', 0, '', 1, 0, '', '', 0, 0, 0, 17, 510, 17, 510, 17, 510, 17, 515, 17, 511, 17, 510, 17, 512, 17, 511, '', 17, 511, 17, 511, 17, 509, 0, 0, ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,21', ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,21', ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,21', NULL),
(5, 'Date', 'system', NULL, '', NULL, NULL, '', 'LKey', '', '', '', 0, '', 0, 0, '', '', 0, 0, 0, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 21, 1, 21, 2, 0, NULL, '', 0, NULL, 0, NULL, 0, NULL, 0, 0, '', NULL, NULL, NULL),
(7, 'Status', 'system', NULL, '', NULL, NULL, 'Unconfirmed\nApproval\nActive\nRejected\nSuspended', 'LKey', '', '', '', 0, '', 0, 0, '', '', 0, 0, 0, 0, NULL, 0, NULL, 21, 1, 21, 1, 0, NULL, 17, 4, 17, 3, 0, NULL, '', 0, NULL, 0, NULL, 0, NULL, 0, 0, '', NULL, NULL, NULL),
(9, 'Featured', 'system', NULL, '', NULL, NULL, '', 'LKey', '', '', '', 0, '', 0, 0, '', '', 0, 0, 0, 0, NULL, 0, NULL, 21, 2, 21, 2, 0, NULL, 0, NULL, 0, NULL, 0, NULL, '', 0, NULL, 0, NULL, 0, NULL, 0, 0, '', NULL, NULL, NULL),
(12, 'Description', 'html_area', NULL, '', 20, NULL, '', 'LKey', '', '', '', 0, '', 1, 0, '', '', 0, 0, 0, 17, 510, 17, 510, 17, 509, 17, 514, 17, 510, 17, 510, 17, 511, 17, 510, '', 17, 510, 17, 510, 17, 508, 0, 0, ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,21', ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,21', ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,21', NULL),
(15, 'Country', 'select_one', 'select', '', NULL, NULL, '#!Country', 'LKey', '', '', '', 0, 'US', 1, 0, '', '', 0, 0, 0, 20, 6, 20, 6, 20, 6, 20, 6, 20, 6, 20, 6, 20, 6, 20, 6, '', 20, 6, 20, 6, 20, 11, 15, 40, '', '', '', NULL),
(16, 'City', 'text', NULL, '', 2, 64, '', 'LKey', '', '', '', 0, '', 1, 0, '', '', 0, 0, 0, 17, 375, 17, 272, 17, 268, 17, 273, 17, 269, 17, 315, 17, 270, 17, 269, '', 17, 269, 17, 269, 17, 267, 0, 0, '', '', '', NULL),
(17, 'General Info', 'block', NULL, '', NULL, NULL, '', 'LKey', '', '', '', 0, '', 0, 0, '', '0', 0, 1, 0, 0, 134, 0, 45, 0, 44, 0, 44, 0, 153, 0, 154, 0, 155, 0, 153, '', 0, 1, 0, 1, 0, 1, 0, 0, ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,21', ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,21', ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,21', NULL),
(18, 'Location', 'system', NULL, '', NULL, NULL, '', 'LKey', '', '', '', 0, '', 0, 0, '', '', 0, 0, 0, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, '', 0, NULL, 0, NULL, 20, 5, 0, 0, '', NULL, NULL, NULL),
(19, 'Keyword', 'system', NULL, 'DescriptionMe\nHeadline', NULL, NULL, '', 'LKey', '', '', '', 0, '', 0, 0, '', '', 0, 0, 0, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, '', 0, NULL, 0, NULL, 20, 3, 0, 0, '', NULL, NULL, NULL),
(20, 'Misc Info', 'block', NULL, '', NULL, NULL, '', 'LKey', '', '', '', 0, '', 0, 0, '', '', 0, 1, 0, 0, 38, 0, 31, 0, 25, 0, 25, 0, 50, 0, 88, 0, 52, 0, 50, '', 0, 2, 0, 2, 0, 2, 0, 0, '', '', '', NULL),
(24, 'Captcha', 'system', NULL, '', NULL, NULL, '', 'LKey', '', '', '', 0, '', 1, 0, '', '', 0, 0, 0, 17, 387, 17, 284, 17, 280, 17, 285, 17, 281, 17, 327, 17, 282, 17, 281, '', 17, 281, 17, 281, 17, 279, 0, 0, '', NULL, NULL, NULL),
(25, 'Security Image', 'block', NULL, '', NULL, NULL, '', 'LKey', '', '', '', 0, '', 0, 0, '', '', 0, 1, 0, 0, 100, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, 9, 0, NULL, 0, NULL, '', 0, NULL, 0, NULL, 0, NULL, 0, 0, '', NULL, '', NULL),
(38, 'Tags', 'text', NULL, '', NULL, NULL, '', 'LKey', '', '', '', 0, '', 0, 0, '', '', 0, 0, 0, 17, 374, 17, 271, 17, 267, 17, 272, 17, 268, 17, 314, 17, 269, 17, 268, '', 17, 268, 17, 268, 17, 266, 0, 0, '', '', '', NULL),
(42, 'TermsOfUse', 'system', NULL, '', NULL, NULL, '', 'LKey', '', '', '', 0, '', 1, 0, '', '', 0, 0, 0, 17, 384, 17, 281, 17, 277, 17, 282, 17, 278, 17, 324, 17, 279, 17, 278, '', 17, 278, 17, 278, 17, 276, 0, 0, '', NULL, NULL, NULL),
(43, 'PrimPhoto', 'system', NULL, '', NULL, NULL, '', 'LKey', '', '', '', 0, '', 1, 0, '', '', 0, 0, 0, 17, 442, 17, 339, 17, 335, 17, 340, 17, 336, 17, 382, 17, 337, 17, 336, '', 17, 336, 17, 336, 17, 334, 0, 0, ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175', NULL, NULL, NULL),
(44, 'GoogleLocation', 'system', NULL, '', NULL, NULL, '', 'LKey', '', '', '', 0, '', 1, 0, '', '', 0, 0, 0, 17, 510, 17, 510, 17, 509, 17, 514, 17, 510, 17, 510, 17, 511, 17, 510, '', 17, 510, 17, 510, 17, 508, 0, 0, ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,21', ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,21', ',22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,21', NULL);




CREATE TABLE IF NOT EXISTS `[db_prefix]categories` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Caption` varchar(255) NOT NULL,
  `Parent` int(11) NOT NULL DEFAULT '0',
  `Icon` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=176 ;

--
-- Dumping data for table `ml_pages_categories`
--

INSERT INTO `[db_prefix]categories` (`ID`, `Name`, `Caption`, `Parent`, `Icon`) VALUES
(20, 'Local Business or Place', 'Local Business or Place', 0, ''),
(21, 'Airport', 'Airport', 20, ''),
(22, 'Arts/Entertainment/Nightlife', 'Arts/Entertainment/Nightlife', 20, ''),
(23, 'Attractions/Things to Do', 'Attractions/Things to Do', 20, ''),
(24, 'Automotive', 'Automotive', 20, ''),
(25, 'Bank/Financial Services', 'Bank/Financial Services', 20, ''),
(26, 'Bar', 'Bar', 20, ''),
(27, 'Book Store', 'Book Store', 20, ''),
(28, 'Business Services', 'Business Services', 20, ''),
(29, 'Church/Religious Organization', 'Church/Religious Organization', 20, ''),
(30, 'Club', 'Club', 20, ''),
(31, 'Community/Government', 'Community/Government', 20, ''),
(32, 'Concert Venue', 'Concert Venue', 20, ''),
(33, 'Education', 'Education', 20, ''),
(34, 'Event Planning/Event Services', 'Event Planning/Event Services', 20, ''),
(35, 'Food/Grocery', 'Food/Grocery', 20, ''),
(36, 'Health/Medical/Pharmacy', 'Health/Medical/Pharmacy', 20, ''),
(37, 'Home Improvement', 'Home Improvement', 20, ''),
(38, 'Hospital/Clinic', 'Hospital/Clinic', 20, ''),
(39, 'Hotel', 'Hotel', 20, ''),
(40, 'Landmark', 'Landmark', 20, ''),
(41, 'Library', 'Library', 20, ''),
(42, 'Local Business', 'Local Business', 20, ''),
(43, 'Movie Theater', 'Movie Theater', 20, ''),
(44, 'Museum/Art Gallery', 'Museum/Art Gallery', 20, ''),
(45, 'Pet Services', 'Pet Services', 20, ''),
(46, 'Professional Services', 'Professional Services', 20, ''),
(47, 'Public Places', 'Public Places', 20, ''),
(48, 'Real Estate', 'Real Estate', 20, ''),
(49, 'Restaurant/Cafe', 'Restaurant/Cafe', 20, ''),
(50, 'School', 'School', 20, ''),
(51, 'Shopping/Retail', 'Shopping/Retail', 20, ''),
(52, 'Spas/Beauty/Personal Care', 'Spas/Beauty/Personal Care', 20, ''),
(53, 'Sports Venue', 'Sports Venue', 20, ''),
(54, 'Sports/Recreation/Activities', 'Sports/Recreation/Activities', 20, ''),
(55, 'Tours/Sightseeing', 'Tours/Sightseeing', 20, ''),
(56, 'Transit Stop', 'Transit Stop', 20, ''),
(57, 'Transportation', 'Transportation', 20, ''),
(58, 'University', 'University', 20, ''),
(59, 'Company, Organization, or Institution', 'Company, Organization, or Institution', 0, ''),
(60, 'Aerospace/Defense', 'Aerospace/Defense', 59, ''),
(61, 'Automobiles and Parts', 'Automobiles and Parts', 59, ''),
(62, 'Bank/Financial Institution', 'Bank/Financial Institution', 59, ''),
(63, 'Biotechnology', 'Biotechnology', 59, ''),
(64, 'Cause', 'Cause', 59, ''),
(65, 'Chemicals', 'Chemicals', 59, ''),
(66, 'Computers/Technology', 'Computers/Technology', 59, ''),
(67, 'Consulting/Business Services', 'Consulting/Business Services', 59, ''),
(68, 'Education', 'Education', 59, ''),
(69, 'Energy/Utility', 'Energy/Utility', 59, ''),
(70, 'Engineering/Construction', 'Engineering/Construction', 59, ''),
(71, 'Farming/Agriculture', 'Farming/Agriculture', 59, ''),
(72, 'Food/Beverages', 'Food/Beverages', 59, ''),
(73, 'Government Organization', 'Government Organization', 59, ''),
(74, 'Health/Beauty', 'Health/Beauty', 59, ''),
(75, 'Internet/Software', 'Internet/Software', 59, ''),
(76, 'Legal/Law', 'Legal/Law', 59, ''),
(77, 'Media/News/Publishing', 'Media/News/Publishing', 59, ''),
(78, 'Mining/Materials', 'Mining/Materials', 59, ''),
(79, 'Non-Governmental Organization (NGO)', 'Non-Governmental Organization (NGO)', 59, ''),
(80, 'Non-Profit Organization', 'Non-Profit Organization', 59, ''),
(81, 'Organization', 'Organization', 59, ''),
(82, 'Political Organization', 'Political Organization', 59, ''),
(83, 'Political Party', 'Political Party', 59, ''),
(84, 'Retail and Consumer Merchandise', 'Retail and Consumer Merchandise', 59, ''),
(85, 'School', 'School', 59, ''),
(86, 'Small Business', 'Small Business', 59, ''),
(87, 'Telecommunication', 'Telecommunication', 59, ''),
(88, 'Transport/Freight', 'Transport/Freight', 59, ''),
(89, 'Travel/Leisure', 'Travel/Leisure', 59, ''),
(90, 'University', 'University', 59, ''),
(91, 'Brand or Product', 'Brand or Product', 0, ''),
(92, 'Appliances', 'Appliances', 91, ''),
(93, 'Baby Goods/Kids Goods', 'Baby Goods/Kids Goods', 91, ''),
(94, 'Bags/Luggage', 'Bags/Luggage', 91, ''),
(95, 'Building Materials', 'Building Materials', 91, ''),
(96, 'Camera/Photo', 'Camera/Photo', 91, ''),
(97, 'Cars', 'Cars', 91, ''),
(98, 'Clothing', 'Clothing', 91, ''),
(99, 'Commercial Equipment', 'Commercial Equipment', 91, ''),
(100, 'Computers', 'Computers', 91, ''),
(101, 'Drugs', 'Drugs', 91, ''),
(102, 'Electronics', 'Electronics', 91, ''),
(103, 'Food/Beverages', 'Food/Beverages', 91, ''),
(104, 'Furniture', 'Furniture', 91, ''),
(105, 'Games/Toys', 'Games/Toys', 91, ''),
(106, 'Health/Beauty', 'Health/Beauty', 91, ''),
(107, 'Home Decor', 'Home Decor', 91, ''),
(108, 'Household Supplies', 'Household Supplies', 91, ''),
(109, 'Jewelry/Watches', 'Jewelry/Watches', 91, ''),
(110, 'Kitchen/Cooking', 'Kitchen/Cooking', 91, ''),
(111, 'Movies/Music', 'Movies/Music', 91, ''),
(112, 'Musical Instrument', 'Musical Instrument', 91, ''),
(113, 'Office Supplies', 'Office Supplies', 91, ''),
(114, 'Outdoor Gear/Sporting Goods', 'Outdoor Gear/Sporting Goods', 91, ''),
(115, 'Patio/Garden', 'Patio/Garden', 91, ''),
(116, 'Pet Supplies', 'Pet Supplies', 91, ''),
(117, 'Product/Service', 'Product/Service', 91, ''),
(118, 'Software', 'Software', 91, ''),
(119, 'Tools/Equipment', 'Tools/Equipment', 91, ''),
(120, 'Vitamins/Supplements', 'Vitamins/Supplements', 91, ''),
(121, 'Website', 'Website', 91, ''),
(122, 'Wine/Spirits', 'Wine/Spirits', 91, ''),
(123, 'Artist, Band or Public Figure', 'Artist, Band or Public Figure', 0, ''),
(124, 'Actor/Director', 'Actor/Director', 123, ''),
(125, 'Artist', 'Artist', 123, ''),
(126, 'Athlete', 'Athlete', 123, ''),
(127, 'Author', 'Author', 123, ''),
(128, 'Business Person', 'Business Person', 123, ''),
(129, 'Chef', 'Chef', 123, ''),
(130, 'Coach', 'Coach', 123, ''),
(131, 'Comedian', 'Comedian', 123, ''),
(132, 'Dancer', 'Dancer', 123, ''),
(133, 'Doctor', 'Doctor', 123, ''),
(134, 'Editor', 'Editor', 123, ''),
(135, 'Entertainer', 'Entertainer', 123, ''),
(136, 'Fictional Character', 'Fictional Character', 123, ''),
(137, 'Government Official', 'Government Official', 123, ''),
(138, 'Journalist', 'Journalist', 123, ''),
(139, 'Personality', 'Personality', 123, ''),
(140, 'Politician', 'Politician', 123, ''),
(141, 'Producer', 'Producer', 123, ''),
(142, 'Public Figure', 'Public Figure', 123, ''),
(143, 'Teacher', 'Teacher', 123, ''),
(144, 'Writer', 'Writer', 123, ''),
(145, 'Entertainment', 'Entertainment', 0, ''),
(146, 'Album', 'Album', 145, ''),
(147, 'Amateur Sports Team', 'Amateur Sports Team', 145, ''),
(148, 'Book', 'Book', 145, ''),
(149, 'Book Store', 'Book Store', 145, ''),
(150, 'Concert Tour', 'Concert Tour', 145, ''),
(151, 'Concert Venue', 'Concert Venue', 145, ''),
(152, 'Fictional Character', 'Fictional Character', 145, ''),
(153, 'Library', 'Library', 145, ''),
(154, 'Magazine', 'Magazine', 145, ''),
(155, 'Movie', 'Movie', 145, ''),
(156, 'Movie Theater', 'Movie Theater', 145, ''),
(157, 'Music Award', 'Music Award', 145, ''),
(158, 'Music Chart', 'Music Chart', 145, ''),
(159, 'Music Video', 'Music Video', 145, ''),
(160, 'Musical Instrument', 'Musical Instrument', 145, ''),
(161, 'Playlist', 'Playlist', 145, ''),
(162, 'Professional Sports Team', 'Professional Sports Team', 145, ''),
(163, 'Radio Station', 'Radio Station', 145, ''),
(164, 'Record Label', 'Record Label', 145, ''),
(165, 'School Sports Team', 'School Sports Team', 145, ''),
(166, 'Song', 'Song', 145, ''),
(167, 'Sports League', 'Sports League', 145, ''),
(168, 'Sports Venue', 'Sports Venue', 145, ''),
(169, 'Studio', 'Studio', 145, ''),
(170, 'TV Channel', 'TV Channel', 145, ''),
(171, 'TV Network', 'TV Network', 145, ''),
(172, 'TV Show', 'TV Show', 145, ''),
(173, 'TV/Movie Award', 'TV/Movie Award', 145, ''),
(174, 'Cause or Community', 'Cause or Community', 0, ''),
(175, 'General', 'General', 174, '');


CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Title` varchar(100) NOT NULL DEFAULT '',
  `EntryUri` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Status` enum('approved','pending') NOT NULL DEFAULT 'approved',
  `Country` varchar(2) NOT NULL DEFAULT 'US',
  `City` varchar(50) NOT NULL DEFAULT '',
  `Date` int(11) NOT NULL,
  `ResponsibleID` int(10) unsigned NOT NULL DEFAULT '0',
  `PageMembershipFilter` varchar(100) NOT NULL DEFAULT '',
  `Tags` varchar(255) NOT NULL DEFAULT '',
  `Categories` text NOT NULL,
  `FieldPhotos` text NOT NULL,
  `Views` int(11) NOT NULL,
  `Rate` float NOT NULL,
  `RateCount` int(11) NOT NULL,
  `CommentsCount` int(11) NOT NULL,
  `FansCount` int(11) NOT NULL,
  `Featured` tinyint(4) NOT NULL,
  `allow_view_page_to` int(11) NOT NULL DEFAULT '3',
  `allow_view_fans_to` varchar(16) NOT NULL DEFAULT '3',
  `allow_comment_to` varchar(16) NOT NULL DEFAULT '3',
  `allow_rate_to` varchar(16) NOT NULL DEFAULT '3',
  `allow_join_to` int(11) NOT NULL DEFAULT 3,
  `allow_post_in_forum_to` varchar(16) NOT NULL DEFAULT 'p',
  `JoinConfirmation` tinyint(4) NOT NULL DEFAULT '0',
  `allow_upload_photos_to` varchar(16) NOT NULL DEFAULT 'a',
  `allow_upload_videos_to` varchar(16) NOT NULL DEFAULT 'a',
  `allow_upload_sounds_to` varchar(16) NOT NULL DEFAULT 'a',
  `allow_upload_files_to` varchar(16) NOT NULL DEFAULT 'a',
  `SubCategory` varchar(255) NOT NULL,
  `MainCategory` varchar(255) NOT NULL,
  `DateLastEdit` datetime NOT NULL,
  `PrimPhoto` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `EntryUri` (`EntryUri`),
  KEY `ResponsibleID` (`ResponsibleID`),
  KEY `Date` (`Date`),
  FULLTEXT KEY `Title` (`Title`,`Description`,`City`,`Tags`,`Categories`),
  FULLTEXT KEY `Tags` (`Tags`),
  FULLTEXT KEY `Categories` (`Categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

