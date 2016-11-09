SET @sPluginName = 'aqb_mls';

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_mls_am_item', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/admin/'), 'Membership Levels Splitter', 'modules/aqb/mls/|adm_menu_icon.gif', '', '', @iOrder+1);

INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
(CONCAT('modules/?r=', @sPluginName, '/'), CONCAT('m/', @sPluginName, '/'), CONCAT('permalinks_module_', @sPluginName));

INSERT INTO `sys_options`(`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
(CONCAT('permalinks_module_', @sPluginName), 'on', 26, 'Enable user friendly permalinks for Membership Levels Splitter module', 'checkbox', '', '', 0);

CREATE TABLE `[db_prefix]acl_levels` (
`AclID` INT NOT NULL ,
`ProfileTypes` INT DEFAULT '1073741823' NOT NULL ,
PRIMARY KEY ( `AclID` )
);