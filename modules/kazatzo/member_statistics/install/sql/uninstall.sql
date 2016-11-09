	-- 
    -- `sys_menu_admin`;
    --

    DELETE FROM 
        `sys_menu_admin` 
    WHERE
        `name` = 'Member Statistics';
		
	-- 
    -- `[db_prefix]image`;
    --
		
	DROP TABLE `mem_stats_image`;