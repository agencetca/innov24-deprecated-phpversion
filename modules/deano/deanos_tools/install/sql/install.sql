CREATE TABLE IF NOT EXISTS `dbcs_ip_address` (
  `member_id` int(11) NOT NULL default '0',
  `nick_name` varchar(255) NOT NULL,
  `ip_address` varchar(40) NOT NULL,
  `ip_long` bigint(20) NOT NULL,
  `time_stamp` bigint(20) NOT NULL,
  UNIQUE KEY `dbcs_unique` (`member_id`,`time_stamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, 'DeanosTools', '_dbcsDeanosTools', '{siteUrl}modules/?r=deanos_tools/administration/', 'Deanos Tools', 'modules/deano/deanos_tools/|deano-tools.png', '', '', @iMax+1);

--
-- `sys_alerts_handlers` ;
--
INSERT INTO
    `sys_alerts_handlers`
SET
    `name`  = 'dbcs_deanos_tools',
    `class` = 'BxDeanosToolsAlerts',
    `file`  = 'modules/deano/deanos_tools/classes/BxDeanosToolsAlerts.php';

SET @iHandlerId := (SELECT `id` FROM `sys_alerts_handlers`  WHERE `name`  =  'dbcs_deanos_tools');

--
-- `sys_alerts` ;
--
INSERT INTO
    `sys_alerts`
SET
    `unit`       = 'profile',
    `action`     = 'login',
    `handler_id` = @iHandlerId;

-- This one disabled now by default. It adds extra load to the system so should only be turned on when needed.
-- INSERT INTO
--     `sys_alerts`
-- SET
--     `unit`       = 'system',
--     `action`     = 'begin',
--     `handler_id` = @iHandlerId;

    --
    -- `sys_options_cats` ;
    --

    SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
    INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Deanos Tools', @iMaxOrder);
    SET @iKategId = (SELECT LAST_INSERT_ID());

    --
    -- Dumping data for table `sys_options`;
    --

    INSERT INTO 
        `sys_options` 
    SET
        `Name` = 'dbcs_DT_sapp',
        `kateg` = @iKategId,
        `desc`  = 'Admin Section Members Per Page',
        `Type`  = 'digit',
        `VALUE` = '10',
        `order_in_kateg` = 1;

    INSERT INTO 
        `sys_options` 
    SET
        `Name` = 'dbcs_DT_stpp',
        `kateg` = @iKategId,
        `desc`  = 'Site Tags Per Page',
        `Type`  = 'digit',
        `VALUE` = '10',
        `order_in_kateg` = 2;

    INSERT INTO 
        `sys_options` 
    SET
        `Name` = 'dbcs_DT_alpp',
        `kateg` = @iKategId,
        `desc`  = 'IP Address Log Per Page',
        `Type`  = 'digit',
        `VALUE` = '10',
        `order_in_kateg` = 3;

    INSERT INTO 
        `sys_options` 
    SET
        `Name` = 'dbcs_DT_logguests',
        `kateg` = @iKategId,
        `desc`  = 'IP Address Log Guest Visits. 1 = Yes, 0 = No',
        `Type`  = 'digit',
        `VALUE` = '0',
        `order_in_kateg` = 4;
