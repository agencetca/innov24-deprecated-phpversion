
CREATE TABLE `afk_cfgme_interest` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `cat_id` VARCHAR( 255 ) NOT NULL ,
    `user_id` INT UNSIGNED NOT NULL ,
    INDEX ( `user_id` )
);

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Configure Me', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('afk_cfgme_permalinks', 'on', 26, 'Enable friendly permalinks in Configure Me', 'checkbox', '', '', '0', '');
#('afk_cfgme_max_posts_to_show', '10', @iCategId, 'Max number of posts to show', 'digit', '', '', '1', '');

-- permalinks
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=configure/', 'm/configure/', 'afk_cfgme_permalinks');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'afk_cfgme', 'Configure Me', '{siteUrl}modules/?r=configure/administration/', 'Configure Me by AFK system', 'modules/afk/configure/|icon.png', @iMax+1);

