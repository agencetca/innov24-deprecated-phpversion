SET @sPluginName = 'aqb_pts';

DELETE FROM `sys_menu_admin` WHERE `name`=@sPluginName;

DELETE FROM `sys_permalinks` WHERE `check`=CONCAT('permalinks_module_', @sPluginName);

DELETE FROM `sys_options` WHERE `Name` = CONCAT('permalinks_module_', @sPluginName);


DROP TABLE `[db_prefix]profile_types`;
DROP TABLE `[db_prefix]top_menu_visibility`;
DROP TABLE `[db_prefix]member_menu_visibility`;
DROP TABLE `[db_prefix]page_blocks_visibility`;
DROP TABLE `[db_prefix]profile_fields`;

DELETE FROM `sys_pre_values` WHERE `Key` = 'ProfileType';

DELETE FROM `sys_profile_fields` WHERE `Name` = 'ProfileType';
ALTER TABLE `Profiles` DROP COLUMN `ProfileType`;

DROP TABLE `[db_prefix]search_result_layout`;

DELETE FROM `sys_injections` WHERE `name` = 'pts_global_css' LIMIT 1;