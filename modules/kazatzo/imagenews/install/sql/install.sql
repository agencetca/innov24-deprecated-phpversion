--
-- Table structure for table `[db_prefix]entries`
--

CREATE TABLE IF NOT EXISTS `[db_prefix]entries` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `author_id` int(11) unsigned NOT NULL default '0',  
  `caption` varchar(64) NOT NULL default '',
  `snippet` text NOT NULL,
  `image` text NOT NULL,
  `content` text NOT NULL,
  `when` int(11) NOT NULL default '0',
  `uri` varchar(64) NOT NULL default '',
  `tags` varchar(255) NOT NULL default '',
  `categories` varchar(255) NOT NULL default '',
  `comment` tinyint(0) NOT NULL default '0',
  `vote` tinyint(0) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `featured` tinyint(4) NOT NULL default '0',
  `rate` int(11) NOT NULL default '0',
  `rate_count` int(11) NOT NULL default '0',
  `view_count` int(11) NOT NULL default '0',
  `cmts_count` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  FULLTEXT KEY `search_group` (`caption`, `content`, `tags`, `categories`),
  FULLTEXT KEY `search_caption` (`caption`),
  FULLTEXT KEY `search_content` (`content`),
  FULLTEXT KEY `search_tags` (`tags`),
  FULLTEXT KEY `search_categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]comments`
--

CREATE TABLE IF NOT EXISTS `[db_prefix]comments` (
  `cmt_id` int(11) NOT NULL auto_increment,
  `cmt_parent_id` int(11) NOT NULL default '0',
  `cmt_object_id` int(11) NOT NULL default '0',
  `cmt_author_id` int(10) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL,
  `cmt_mood` tinyint(4) NOT NULL default '0',
  `cmt_rate` int(11) NOT NULL default '0',
  `cmt_rate_count` int(11) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cmt_id`),
  KEY `cmt_object_id` (`cmt_object_id`,`cmt_parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]comments_track`
--

CREATE TABLE IF NOT EXISTS `[db_prefix]comments_track` (
  `cmt_system_id` int(11) NOT NULL default '0',
  `cmt_id` int(11) NOT NULL default '0',
  `cmt_rate` tinyint(4) NOT NULL default '0',
  `cmt_rate_author_id` int(10) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int(11) unsigned NOT NULL default '0',
  `cmt_rate_ts` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cmt_system_id`,`cmt_id`,`cmt_rate_author_nip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]voting`
--
CREATE TABLE `[db_prefix]voting` (
  `imagenews_id` bigint(8) NOT NULL default '0',
  `imagenews_rating_count` int(11) NOT NULL default '0',
  `imagenews_rating_sum` int(11) NOT NULL default '0',
  UNIQUE KEY `imagenews_id` (`imagenews_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]voting_track`
--
CREATE TABLE `[db_prefix]voting_track` (
  `imagenews_id` bigint(8) NOT NULL default '0',
  `imagenews_ip` varchar(20) default NULL,
  `imagenews_date` datetime default NULL,
  KEY `imagenews_ip` (`imagenews_ip`,`imagenews_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]views_track`
--
CREATE TABLE IF NOT EXISTS `[db_prefix]views_track` (
  `id` int(10) unsigned NOT NULL,
  `viewer` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `ts` int(10) unsigned NOT NULL,
  KEY `id` (`id`,`viewer`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

SET @iTMOrder = (SELECT MAX(`Order`) FROM `sys_menu_top` WHERE `Parent`='0');
INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(0, 'ImageNews', '_imagenews_top_menu_item', 'modules/?r=imagenews/index/|modules/?r=imagenews/', @iTMOrder+1, 'non,memb', '', '', '', 1, 1, 1, 'top', 'modules/kazatzo/imagenews/|top_menu_icon.png', 0, '');

SET @iTMParentId = LAST_INSERT_ID( );
INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(@iTMParentId, 'ImageNewsHome', '_imagenews_home_top_menu_sitem', 'modules/?r=imagenews/index/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ImageNewsArchive', '_imagenews_archive_top_menu_sitem', 'modules/?r=imagenews/archive/', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ImageNewsTop', '_imagenews_top_top_menu_sitem', 'modules/?r=imagenews/top/', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ImageNewsPopular', '_imagenews_popular_top_menu_sitem', 'modules/?r=imagenews/popular/', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ImageNewsFeatured', '_imagenews_featured_top_menu_sitem', 'modules/?r=imagenews/featured/', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ImageNewsTags', '_imagenews_tags_top_menu_sitem', 'modules/?r=imagenews/tags/', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ImageNewsCategories', '_imagenews_categories_top_menu_sitem', 'modules/?r=imagenews/categories/', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ImageNewsCalendar', '_imagenews_calendar_top_menu_sitem', 'modules/?r=imagenews/calendar/', 7, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ImageNewsSearch', '_imagenews_search_top_menu_sitem', 'searchKeyword.php?type=imagenews', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(0, '[db_prefix]_view', '_imagenews_view_top_menu_sitem', 'modules/?r=imagenews/view/', 0, 'non,memb', '', '', '', 1, 1, 1, 'system', 'modules/kazatzo/imagenews/|top_menu_icon.png', 0, '');

INSERT INTO `sys_menu_member`(`Caption`, `Name`, `Icon`, `Link`, `Script`, `Eval`, `Order`, `Active`, `Editable`, `Deletable`, `Target`, `Position`, `Type`) VALUES
('_imagenews_ext_menu_item', 'ImageNews', '', 'modules/?r=imagenews/', '', '', 6, '1', 0, 0, '', 'bottom', 'link');

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, 'imagenews', '_imagenews_admin_menu_sitem', '{siteUrl}modules/?r=imagenews/admin/', 'News with image support - by kazatzo', 'modules/kazatzo/imagenews/|imagenews.png', '', '', @iOrder+1);


INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES('modules/?r=imagenews/', 'm/imagenews/', 'permalinks_module_imagenews');


SET @iCategoryOrder = (SELECT MAX(`menu_order`) FROM `sys_options_cats`) + 1;
INSERT INTO `sys_options_cats` (`name` , `menu_order` ) VALUES ('imagenews', @iCategoryOrder);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('permalinks_module_imagenews', 'on', 26, 'Enable friendly news permalink', 'checkbox', '', '', 0, NULL),
('category_auto_app_imagenews', 'on', 0, 'Autoapprove for categories', 'checkbox', '', '', 0, NULL),
('imagenews_autoapprove', 'on', @iCategoryId, 'Publish image news automatically', 'checkbox', '', '', 1, NULL),
('imagenews_comments', 'on', @iCategoryId, 'Allow comments for image news', 'checkbox', '', '', 2, NULL),
('imagenews_votes', 'on', @iCategoryId, 'Allow votes for image news', 'checkbox', '', '', 3, NULL),
('imagenews_index_number', '10', @iCategoryId, 'The number of image news on home page', 'digit', '', '', 4, NULL),
('imagenews_member_number', '10', @iCategoryId, 'The number of image news on account page', 'digit', '', '', 5, NULL),
('imagenews_per_page', '10', @iCategoryId, 'The number of items shown on the page', 'digit', '', '', 7, NULL),
('imagenews_rss_length', '10', @iCategoryId, 'The number of items shown in the RSS feed', 'digit', '', '', 8, NULL), 
('imagenews_type', 'Image behind text', @iCategoryId, 'Choose the theme to display image news', 'select', '', '', 9, 'Image behind text,Small image next to text');

INSERT INTO `sys_objects_cmts` (`ObjectName`, `TableCmts`, `TableTrack`, `AllowTags`, `Nl2br`, `SecToEdit`, `PerView`, `IsRatable`, `ViewingThreshold`, `AnimationEffect`, `AnimationSpeed`, `IsOn`, `IsMood`, `RootStylePrefix`, `TriggerTable`, `TriggerFieldId`, `TriggerFieldComments`, `ClassName`, `ClassFile`) VALUES
('imagenews', '[db_prefix]comments', '[db_prefix]comments_track', 0, 1, 90, 10, 1, -3, 'slide', 2000, 1, 0, 'cmt', '[db_prefix]entries', 'id', 'cmts_count', 'ImageNewsCmts', 'modules/kazatzo/imagenews/classes/ImageNewsCmts.php');

INSERT INTO `sys_objects_vote` (`ObjectName`, `TableRating`, `TableTrack`, `RowPrefix`, `MaxVotes`, `PostName`, `IsDuplicate`, `IsOn`, `className`, `classFile`, `TriggerTable`, `TriggerFieldRate`, `TriggerFieldRateCount`, `TriggerFieldId`, `OverrideClassName`, `OverrideClassFile`) VALUES
('imagenews', '[db_prefix]voting', '[db_prefix]voting_track', 'imagenews_', 5, 'vote_send_result', 'BX_PERIOD_PER_VOTE', 1, '', '', '[db_prefix]entries', 'rate', 'rate_count', 'id', 'ImageNewsVoting', 'modules/kazatzo/imagenews/classes/ImageNewsVoting.php');

INSERT INTO `sys_objects_tag` (`ObjectName`, `Query`, `PermalinkParam`, `EnabledPermalink`, `DisabledPermalink`, `LangKey`) VALUES
('imagenews', 'SELECT `tags` FROM `[db_prefix]entries` WHERE `id`={iID} AND `status`=0', 'permalinks_module_imagenews', 'm/imagenews/tag/{tag}', 'modules/?r=imagenews/tag/{tag}', '_imagenews_lcaption_tags');

INSERT INTO `sys_objects_categories` (`ObjectName`, `Query`, `PermalinkParam`, `EnabledPermalink`, `DisabledPermalink`, `LangKey`) 
VALUES ('imagenews', 'SELECT `categories` FROM `[db_prefix]entries` WHERE `id`=''{iID}'' AND `status`=''0''', 'permalinks_module_imagenews', 'm/imagenews/category/{tag}', 'modules/?r=imagenews/category/{tag}', '_imagenews_lcaption_categories');

INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES 
('Default', '0', 'imagenews', '0', 'active'),
('BoonEx Products', '0', 'imagenews', '0', 'active'),
('Some Useful Info', '0', 'imagenews', '0', 'active');

INSERT INTO `sys_objects_search` (`ObjectName`, `Title`, `ClassName`, `ClassPath`) VALUES
('imagenews', '_imagenews_lcaption_search_object', 'ImageNewsSearchResult', 'modules/kazatzo/imagenews/classes/ImageNewsSearchResult.php');

INSERT INTO `sys_objects_views`(`name`, `table_track`, `period`, `trigger_table`, `trigger_field_id`, `trigger_field_views`, `is_on`) VALUES
('imagenews', '[db_prefix]views_track', 86400, '[db_prefix]entries', 'id', 'view_count', 1);


SET @iPCPOrder = (SELECT MAX(`Order`) FROM `sys_page_compose_pages`);
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES ('imagenews_single', 'Single News', @iPCPOrder+1);

SET @iPCPOrder = (SELECT MAX(`Order`) FROM `sys_page_compose_pages`);
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES ('imagenews_home', 'News Home', @iPCPOrder+1);

SET @iPCOrder = (SELECT MAX(`Order`) FROM `sys_page_compose` WHERE `Page`='index' AND `Column`='1');
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('index', '998px', 'Show list of featured news', '_imagenews_bcaption_featured', 1, @iPCOrder+1, 'PHP', 'return BxDolService::call(\'imagenews\', \'featured_block_index\', array(0, 0, false));', 1, 66, 'non,memb', 0),
('index', '998px', 'Show list of latest news', '_imagenews_bcaption_latest', 1, @iPCOrder+2, 'PHP', 'return BxDolService::call(\'imagenews\', \'archive_block_index\', array(0, 0, false));', 1, 66, 'non,memb', 0),
('member', '998px', 'Show list of featured news', '_imagenews_bcaption_featured', 2, 3, 'PHP', 'return BxDolService::call(\'imagenews\', \'featured_block_member\', array(0, 0, false));', 1, 66, 'memb', 0),
('member', '998px', 'Show list of latest news', '_imagenews_bcaption_latest', 2, 4, 'PHP', 'return BxDolService::call(\'imagenews\', \'archive_block_member\', array(0, 0, false));', 1, 66, 'memb', 0),
('imagenews_single', '998px', 'News main content', '_imagenews_bcaption_view_main', 1, 0, 'Content', '', 1, 66, 'non,memb', 0),
('imagenews_single', '998px', 'News comments', '_imagenews_bcaption_view_comment', 1, 1, 'Comment', '', 1, 66, 'non,memb', 0),
('imagenews_single', '998px', 'News actions', '_imagenews_bcaption_view_action', 2, 0, 'Action', '', 1, 34, 'non,memb', 0),
('imagenews_single', '998px', 'News rating', '_imagenews_bcaption_view_vote', 2, 1, 'Vote', '', 1, 34, 'non,memb', 0),
('imagenews_home', '998px', 'News featured', '_imagenews_bcaption_featured', 1, 0, 'Featured', '', 1, 34, 'non,memb', 0),
('imagenews_home', '998px', 'News Latest', '_imagenews_bcaption_latest', 2, 0, 'Latest', '', 1, 66, 'non,memb', 0);

INSERT INTO `sys_objects_actions`(`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES
('{sbs_imagenews_title}', 'action_subscribe.png', '', '{sbs_imagenews_script}', '', 1, 'imagenews', 0),
('{del_imagenews_title}', 'action_block.png', '', '{del_imagenews_script}', '', 2, 'imagenews', 0);

INSERT INTO `sys_sbs_types`(`unit`, `action`, `template`, `params`) VALUES
('imagenews', '', '', 'return BxDolService::call(\'imagenews\', \'get_subscription_params\', array($arg1, $arg2, $arg3));'),
('imagenews', 'commentPost', 't_sbsImageNewsComments', 'return BxDolService::call(\'imagenews\', \'get_subscription_params\', array($arg1, $arg2, $arg3));'),
('imagenews', 'rate', 't_sbsImageNewsRates', 'return BxDolService::call(\'imagenews\', \'get_subscription_params\', array($arg1, $arg2, $arg3));');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('t_sbsImageNewsComments', 'New news comments', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The news you subscribed to has new comments!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view them.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New news comments subscription.', '0'),
('t_sbsImageNewsComments', 'New news comments', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The news you subscribed to has new comments!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view them.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New news comments subscription.', '1'),
('t_sbsImageNewsRates', 'News was rated', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The news you subscribed to was rated!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view it.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New news rates subscription.', '0'),
('t_sbsImageNewsRates', 'News was rated', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The news you subscribed to was rated!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view it.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New news rates subscription.', '1');

INSERT INTO `sys_acl_actions`(`Name`, `AdditionalParamName`) VALUES ('ImageNews Delete', '');

INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('imagenews', '*/5 * * * *', 'ImageNewsCron', 'modules/kazatzo/imagenews/classes/ImageNewsCron.php', '');