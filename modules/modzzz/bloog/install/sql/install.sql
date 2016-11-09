-- Alerts Handler and Events
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_bloog_profile_join', '', '', 'BxDolService::call(''bloog'', ''response_blog_create'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'join', @iHandler);



 
SET @iGlCategID = (SELECT `ID` FROM `sys_options_cats` WHERE `name`='Blogs'); 
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`) VALUES
('bx_blogs_auto_create', 'on', @iGlCategID, 'Enable auto creation of Blog for new members', 'checkbox', '', '');
