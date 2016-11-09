DROP TABLE `dbcs_ip_address`;

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'DeanosTools';

--
-- `sys_alerts_handlers` ;
--
SET @iHandlerId := (SELECT `id` FROM `sys_alerts_handlers`  WHERE `name`  =  'dbcs_deanos_tools');

DELETE FROM
    `sys_alerts_handlers`
WHERE
    `id`  = @iHandlerId;

--
-- `sys_alerts` ;
--
DELETE FROM 
    `sys_alerts`
WHERE
    `handler_id` =  @iHandlerId ;

    --
    -- `sys_options_cats` ;
    --

    SET @iKategId = (SELECT `id` FROM `sys_options_cats` WHERE `name` = 'Deanos Tools' LIMIT 1);
    DELETE FROM `sys_options_cats` WHERE `id` = @iKategId;

    --
    -- `sys_options` ;
    --

    DELETE FROM `sys_options` WHERE `kateg` = @iKategId;
