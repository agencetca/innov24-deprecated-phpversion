
 CREATE TABLE IF NOT EXISTS `mem_stats_image` (
  `image_id` tinyint(2) NOT NULL default '0',
  `image_name` varchar(15) NOT NULL default '0',
  PRIMARY KEY  (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


  -- 
  -- `sys_menu_admin`;
  --

    INSERT INTO 
        `sys_menu_admin` 
    SET
        `name`           = 'Member Statistics',
        `title`          = '_member_statistics_title', 
        `url`            = '{siteAdminUrl}memstats.php', 
        `description`    = 'Member Statistics module by kazatzo', 
        `icon`           = 'modules/kazatzo/member_statistics/|memstats.png',
        `parent_id`      = 2;
		
	