CREATE TABLE IF NOT EXISTS `mobilewall_access` (`id` INT NOT NULL PRIMARY KEY ,`DateLastNavM` datetime NOT NULL default '0000-00-00 00:00:00') ENGINE = MYISAM;

CREATE TABLE IF NOT EXISTS `mobilewall_code` (`id` INT NOT NULL PRIMARY KEY ,`code` VARCHAR( 100 ) NOT NULL) ENGINE = MYISAM;

INSERT INTO `sys_menu_mobile` (`type`, `page`, `title`, `icon`, `action`, `action_data`, `eval_bubble`, `eval_hidden`, `order`, `active`) VALUES
('mobilewall', 'homepage', '_idbw_mobilewall_member_news', '{site_url}modules/ibdw/mobilewall/templates/base/images/icons/ibdw_wall.png', 100, '{site_url}modules/?r=mobilewall/mobile_news_feed/&id={member_id}&password={member_password}', '$dataultimoaccesso="SELECT UNIX_TIMESTAMP(DateLastNav),DateLastNav FROM Profiles WHERE ID={member_id}";$lanciarichiestadata=mysql_query($dataultimoaccesso);$ladataN=mysql_fetch_row($lanciarichiestadata);$dataUltimodaMobile="SELECT UNIX_TIMESTAMP(DateLastNavM), DateLastNavM FROM mobilewall_access WHERE id={member_id}";$lanciarichiestadataMobile=mysql_query($dataUltimodaMobile);$ladataNM=mysql_fetch_row($lanciarichiestadataMobile);if ($ladataN[0]>$ladataNM[0]) {$miadata=$ladataN[1];}else{$miadata=$ladataNM[1];}$nuove="SELECT sender_id FROM bx_spy_data WHERE (sender_id={member_id} or recipient_id={member_id}) AND bx_spy_data.date>\'".$miadata."\'";$eseguicalcolo=mysql_query($nuove);$conteggionuove=mysql_num_rows($eseguicalcolo);return $conteggionuove;', '', 1, 1),
('mobilewall', 'profile', '_idbw_mobilewall_member_news', '{site_url}modules/ibdw/mobilewall/templates/base/images/icons/ibdw_wall.png', 100, '{site_url}modules/?r=mobilewall/mobile_news_feed/&id={member_id}&password={member_password}&profileid={profile_id}', '', '', 1, 1);

CREATE TABLE `mobilewall_config` (
`foto` VARCHAR( 5 ) NOT NULL ,
`video` VARCHAR( 5 ) NOT NULL ,
`gruppi` VARCHAR( 5 ) NOT NULL ,
`eventi` VARCHAR( 5 ) NOT NULL ,
`siti` VARCHAR( 5 ) NOT NULL ,
`sondaggi` VARCHAR( 5 ) NOT NULL ,
`annunci` VARCHAR( 5 ) NOT NULL ,
`formatodata` VARCHAR( 50 ) NOT NULL ,
`offset` VARCHAR( 50 ) NOT NULL ,
`spywallprofileview` VARCHAR( 5 ) NOT NULL ,
`profileupdate` VARCHAR( 5 ) NOT NULL ,
`spywallprofiledelete` VARCHAR( 5 ) NOT NULL ,
`limite` VARCHAR( 50 ) NOT NULL ,
`viewavatar` VARCHAR( 5 ) NOT NULL ,
`namephoto` VARCHAR( 50 ) NOT NULL ,
`namevideo` VARCHAR( 50 ) NOT NULL ,
`privacyalbum` VARCHAR( 5 ) NOT NULL ,
`usernameformat` VARCHAR( 50 ) NOT NULL ,
`widthvideo` VARCHAR( 50 ) NOT NULL ,
`heightvideo` VARCHAR( 50 ) NOT NULL ,
`other` VARCHAR( 50 ) NOT NULL ,
`nkname` VARCHAR( 5 ) NOT NULL ,
`refreshtype` int(11) NOT NULL ,
`avatartype` VARCHAR( 50 ) NOT NULL ,
`hideupdate` VARCHAR( 50 ) NOT NULL ,
`ordinec` VARCHAR( 50 ) NOT NULL ,
`refreshtime` int(11) NOT NULL ,
`messlength` int(11) NOT NULL ,
`shareact` int(11) NOT NULL ,
`likeact` int(11) NOT NULL ,
`commentact` int(11) NOT NULL ,
`commlength` int(11) NOT NULL
) ENGINE = MYISAM ;


INSERT INTO `mobilewall_config` (
`foto` ,
`video` ,
`gruppi` ,
`eventi` ,
`siti` ,
`sondaggi` ,
`annunci` ,
`formatodata` ,
`offset` ,
`spywallprofileview` ,
`profileupdate` ,
`spywallprofiledelete` ,
`limite` ,
`viewavatar` ,
`namephoto` ,
`namevideo` ,
`privacyalbum` ,
`usernameformat` ,
`widthvideo` ,
`heightvideo` ,
`other` ,
`nkname` ,
`refreshtype` ,
`avatartype` ,
`hideupdate` ,
`ordinec` ,
`refreshtime` ,
`messlength` ,
`shareact`,
`likeact`,
`commentact`,
`commlength`
)
VALUES (
'ON', 'ON', 'ON', 'ON', 'ON', 'ON', 'ON', 'm/d/Y H:i:s', '0', 'OFF', 'OFF', 'OFF', '10', 'OFF', 'wall-photo', 'wall-video', '4', '0', '410px', '249px', '1##1##1##1##1##1##1##1',  'ON' , 1,'standard','ON','0', 1, 500,  1, 1, 1, 500);


INSERT INTO 
        `sys_menu_admin` 
    SET
        `name`      = 'MobileWall Activation',
        `title`         = 'MobileWall Activation', 
        `url`           = '{siteUrl}modules/ibdw/mobilewall/classes/activation.php',
        `description`   = 'To manage the MobileWall',
        `icon`          = 'modules/ibdw/mobilewall/templates/base/images/icons/|wall_panel.png',
        `parent_id`     = 2,
        `order`     	=  99;