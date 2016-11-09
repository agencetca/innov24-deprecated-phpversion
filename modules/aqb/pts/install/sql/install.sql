SET @sPluginName = 'aqb_pts';

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_pts_am_item', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/admin/'), 'Profile Types Splitter', 'modules/aqb/pts/|adm_menu_icon.gif', '', '', @iOrder+1);

INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
(CONCAT('modules/?r=', @sPluginName, '/'), CONCAT('m/', @sPluginName, '/'), CONCAT('permalinks_module_', @sPluginName));

INSERT INTO `sys_options`(`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
(CONCAT('permalinks_module_', @sPluginName), 'on', 26, 'Enable user friendly permalinks for Profile Types Splitter module', 'checkbox', '', '', 0);

CREATE TABLE `[db_prefix]profile_types` (
`ID` int(4) PRIMARY KEY NOT NULL ,
`Name` varchar( 128 ) NOT NULL,
`Obsolete` tinyint not null default 0
);

CREATE TABLE `[db_prefix]top_menu_visibility` (
`MenuItemID` INT NOT NULL,
`ProfileTypesVisibility` INT DEFAULT '1073741823' NOT NULL ,
PRIMARY KEY ( `MenuItemID` )
);

CREATE TABLE `[db_prefix]member_menu_visibility` (
`MenuItemID` INT NOT NULL,
`ProfileTypesVisibility` INT DEFAULT '1073741823' NOT NULL ,
PRIMARY KEY ( `MenuItemID` )
);

CREATE TABLE `[db_prefix]page_blocks_visibility` (
`PageBlockID` INT NOT NULL,
`ProfileTypesVisibility` INT DEFAULT '1073741823' NOT NULL ,
`ProfileTypes` INT DEFAULT '1073741823' NOT NULL ,
PRIMARY KEY ( `PageBlockID` )
);

CREATE TABLE `[db_prefix]profile_fields` (
`FieldID` INT NOT NULL,
`ProfileTypes` INT DEFAULT '1073741823' NOT NULL ,
PRIMARY KEY ( `FieldID` )
);

INSERT INTO `[db_prefix]profile_types` SET `ID` = 1, `Name` = "Personal";

INSERT INTO `sys_pre_values` SET `Key` = 'ProfileType', `Value` = 1, `Order` = 0, `LKey` = '__Personal';

SET @iBlock = (SELECT `ID` FROM `sys_profile_fields` WHERE `Type`='block' AND `JoinOrder` = 1);
UPDATE `sys_profile_fields` SET `JoinOrder` = `JoinOrder` + 1 WHERE `JoinBlock` = @iBlock;
INSERT INTO `sys_profile_fields`
(`Name`, 			`Type`, 		`Control`, 	`Values`, 			`Mandatory`,	`Deletable`, 	`JoinBlock`, 	`JoinOrder`) VALUES
('ProfileType', 	'select_one', 	'select',  	'#!ProfileType',	1,				 0,  			@iBlock,				0);
UPDATE `sys_profile_fields` SET `Extra` = CONCAT(`Extra`, '\nProfileType') WHERE `Name` = 'Couple';

ALTER TABLE `Profiles` ADD COLUMN `ProfileType` varchar(255) DEFAULT 1;

CREATE TABLE `[db_prefix]search_result_layout` (
`ID` MEDIUMINT NOT NULL AUTO_INCREMENT ,
`ProfileType` INT NOT NULL ,
`FieldID` MEDIUMINT NOT NULL ,
`row` TINYINT DEFAULT '0' NOT NULL,
`col` TINYINT NOT NULL ,
PRIMARY KEY ( `ID` )
);

INSERT INTO `sys_injections` (`name` , `page_index` , `key` , `type` , `data` , `replace` , `active` )
VALUES ('pts_global_css', '0', 'injection_head', 'text', '<link href="modules/aqb/pts/templates/base/css/search.css" rel="stylesheet" type="text/css" />', '0', '1');
