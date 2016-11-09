
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Master Database Backup' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;

DELETE FROM `sys_options` WHERE `Name` = 'Mdb_permalinks';

DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=MasterBackup/';

DELETE FROM `sys_menu_admin` WHERE `name` = 'MasterBackup';

