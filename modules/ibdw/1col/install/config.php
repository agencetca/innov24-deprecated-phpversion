<?
/**********************************************************************************
*                            IBDW 1Col Dolphin Smart Community Builder
*                              -------------------
*     begin                : May 1 2010
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW 1Col is not free and you cannot redistribute and/or modify it.
* 
* IBDW 1Col is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

$aConfig = array(
	'title' => '1Col',
    'version' => '5.9',
	'vendor' => 'IlBelloDelWeb.it',
	'update_url' => '',
	'compatible_with' => array(
        '7.0.0','7.0.1','7.0.2','7.0.3','7.0.4','7.0.5','7.0.6','7.0.7','7.0.8','7.0.9','7.1.x'
    ),
	'home_dir' => 'ibdw/1col/',
	'home_uri' => '1col',
	'db_prefix' => 'me_fcol_',
    'class_prefix' => 'FirstCol',
	'install' => array( 
	    'clear_db_cache' => 1,
        'update_languages' => 1,
        'check_dependencies' => 1,
        'execute_sql' => 1,
        'recompile_permalinks' => 1,
        'recompile_global_paramaters' => 1,
        'clear_db_cache' => 1,
	),
	'uninstall' => array (
	    'clear_db_cache' => 1,
        'update_languages' => 1,
        'execute_sql' => 1,
        'recompile_permalinks' => 1,
        'recompile_global_paramaters' => 1,
        'clear_cache' => 1,
        'update_languages' => 1,
    ),
	'language_category' => '1colonna',
	'install_permissions' => array(),
    'uninstall_permissions' => array(),
	'install_info' => array(
		'introduction' => '',
		'conclusion' => ''
	),
	'uninstall_info' => array(
		'introduction' => '',
		'conclusion' => ''
	)
);
?>