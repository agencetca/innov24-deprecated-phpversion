SET @sPluginName = 'aqb_df';

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_df_am_item', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/admin/'), 'Dependent Fields', 'modules/aqb/df/|adm_menu_icon.gif', '', '', @iOrder+1);

INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
(CONCAT('modules/?r=', @sPluginName, '/'), CONCAT('m/', @sPluginName, '/'), CONCAT('permalinks_module_', @sPluginName));

INSERT INTO `sys_options`(`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
(CONCAT('permalinks_module_', @sPluginName), 'on', 26, 'Enable user friendly permalinks for Dependent Fields module', 'checkbox', '', '', 0);

CREATE TABLE `[db_prefix]dependencies` (
`Field` INT NOT NULL ,
`DependsOn` INT NOT NULL ,
`UseAjax` TINYINT DEFAULT '0' NOT NULL,
PRIMARY KEY ( `Field` )
);

CREATE TABLE `[db_prefix]custom_dependencies` (
`ID` INT AUTO_INCREMENT NOT NULL,
`Field` varchar(255) NOT NULL,
`DependsOn` varchar(255) NOT NULL,
`ValuesList` varchar(255) NOT NULL,
`UseAjax` TINYINT DEFAULT '0' NOT NULL,
PRIMARY KEY ( `ID` )
);

INSERT INTO `[db_prefix]custom_dependencies` (`Field`, `DependsOn`, `ValuesList`, `UseAjax`) VALUES ('ValuesList', 'DependsOn', 'DFDependentLists', 0);