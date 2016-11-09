INSERT INTO `sys_page_compose` (`ID` ,`Page` ,`PageWidth` ,`Desc` ,`Caption` ,`Column` ,`Order` ,`Func` ,`Content` ,`DesignBox` ,`ColWidth` ,`Visible` ,`MinWidth`) VALUES (NULL , 'profile', '', 'News Feed about the users and their friends', '_ibdw_megaprofile_modulename', '0', '0', 'PHP', 'require_once(BX_DIRECTORY_PATH_MODULES .''ibdw/megaprofile/profilecore.php'');', '1', '0', 'memb', '0');

CREATE TABLE IF NOT EXISTS `ibdw_mega_profile` (`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,`Owner` INT( 11 ) NOT NULL ,`Hash` VARCHAR( 32 ) NOT NULL) ENGINE = MYISAM ;

ALTER TABLE `ibdw_mega_profile` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `ibdw_mega_profile` CHANGE `Hash` `Hash` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `megaprofile_code` (`id` INT NOT NULL PRIMARY KEY ,`code` VARCHAR( 100 ) NOT NULL) ENGINE = MYISAM;

      
CREATE TABLE `megaprofile_config` (
`maxsize` VARCHAR( 10 ) NOT NULL ,
`linkphoto` VARCHAR( 5 ) NOT NULL ,
`linkvideo` VARCHAR( 5 ) NOT NULL ,
`linksound` VARCHAR( 5 ) NOT NULL ,
`sndmessage` VARCHAR( 5 ) NOT NULL ,
`greet` VARCHAR( 5 ) NOT NULL ,
`rpspam` VARCHAR( 5 ) NOT NULL ,
`friendblock` VARCHAR( 5 ) NOT NULL ,
`blockblock` VARCHAR( 5 ) NOT NULL ,
`subscribe` VARCHAR( 5 ) NOT NULL ,
`infoblock` VARCHAR( 5 ) NOT NULL ,
`descblock` VARCHAR( 5 ) NOT NULL ,
`photoalbum` VARCHAR( 5 ) NOT NULL ,
`videoalbum` VARCHAR( 5 ) NOT NULL ,
`friend` VARCHAR( 5 ) NOT NULL ,
`mutualfr` VARCHAR( 5 ) NOT NULL ,
`nfriend` VARCHAR( 5 ) NOT NULL ,
`nfriendm` VARCHAR( 5 ) NOT NULL ,
`nalbum` VARCHAR( 5 ) NOT NULL ,
`usernameformat` VARCHAR( 5 ) NOT NULL ,
`namestm` VARCHAR( 5 ) NOT NULL ,
`crtdesk` VARCHAR( 5 ) NOT NULL ,
`frmdata` VARCHAR( 5 ) NOT NULL ,
`albumdescr` VARCHAR( 5 ) NOT NULL ,
`webcam` VARCHAR( 5 ) NOT NULL ,
`custompro` VARCHAR( 5 ) NOT NULL ,
`favepro` VARCHAR( 5 ) NOT NULL ,
`relstatusview` VARCHAR( 5 ) NOT NULL ,
`datebirthview` VARCHAR( 5 ) NOT NULL ,
`infocityview` VARCHAR( 5 ) NOT NULL ,
`headlineview` VARCHAR( 5 ) NOT NULL ,
`emailview` VARCHAR( 5 ) NOT NULL ,
`sexview` VARCHAR( 5 ) NOT NULL ,
`lookingforview` VARCHAR( 5 ) NOT NULL ,
`occupationview` VARCHAR( 5 ) NOT NULL ,
`religionview` VARCHAR( 5 ) NOT NULL ,
`agestyle` VARCHAR( 5 ) NOT NULL ,
`setavatard` VARCHAR( 5 ) NOT NULL ,
`friendsord` VARCHAR( 5 ) NOT NULL ,
`defimage` VARCHAR( 5 ) NOT NULL ,
`mutualfriendord` VARCHAR( 5 ) NOT NULL,
`typeofallowed` VARCHAR( 5 ) NOT NULL,
`reportspamtool` VARCHAR( 5 ) NOT NULL,
`thumbtype` VARCHAR( 5 ) NOT NULL
) ENGINE = MYISAM ;

INSERT INTO `megaprofile_config` (
`maxsize` ,
`linkphoto` ,
`linkvideo` ,
`linksound` ,
`sndmessage` ,
`greet` ,
`rpspam` ,
`friendblock` ,
`blockblock` ,
`subscribe` ,
`infoblock` ,
`descblock` ,
`photoalbum` ,
`videoalbum` ,
`friend` ,
`mutualfr` ,
`nfriend` ,
`nfriendm` ,
`nalbum` ,
`usernameformat` ,
`namestm` ,
`crtdesk` ,
`frmdata` ,
`albumdescr` ,
`webcam` ,
`custompro` ,
`favepro` ,
`relstatusview` ,
`datebirthview` ,
`infocityview` ,
`headlineview` ,
`emailview` ,
`sexview` ,
`lookingforview` ,
`occupationview` ,
`religionview`,
`agestyle`,
`setavatard`,
`friendsord`,
`defimage`,
`mutualfriendord`,
`typeofallowed`,
`reportspamtool`,
`thumbtype`
)
VALUES (
'200000', 'ON', 'ON', 'ON', 'ON','ON', 'ON', 'ON', 'ON', 'ON', 'ON', 'ON', 'ON', 'ON', 'ON', 'ON', '6', '6', '2', '0', 'OFF', '150', '1', '80', 'ON', 'OFF', 'OFF', 'ON', 'ON', 'ON', 'OFF', 'OFF', 'OFF', 'OFF', 'OFF', 'OFF','3','ON','0','0','0','1','0','0'
);

CREATE TABLE IF NOT EXISTS `ibdw_mega_extralink` (
  `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Name` varchar(20) NOT NULL,
  `LangKey` varchar(30) NOT NULL,
  `Status` tinyint(4) NOT NULL,
  `UrlDyn` varchar(200) NOT NULL,
  `Destination` tinyint(4) NOT NULL,
  `Order` tinyint(4) NOT NULL
) ENGINE=MyISAM;

INSERT INTO `ibdw_mega_extralink` (`Name`, `LangKey`, `Status`, `UrlDyn`, `Destination`, `Order`) VALUES ('Simple Messenger', '', 1, '', 1, 100);

INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES (NULL, '0', 'Active Megaprofile', 'Active Megaprofile', '{siteUrl}modules/ibdw/megaprofile/activation.php', 'Active Megaprofile', 'modules/ibdw/megaprofile/templates/base/images/|warning.png', '', '', '10');