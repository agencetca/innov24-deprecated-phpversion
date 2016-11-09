<?
/**********************************************************************************
*                            IBDW MobileWall Dolphin Smart Community Builder
*                              -------------------
*     begin                : Oct 18 2011
*     copyright            : (C) 2011 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW MobileWall is not free and you cannot redistribute and/or modify it.
* 
* IBDW MobileWall is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details write to info@ilbellodelweb.it
**********************************************************************************/
$aConfig = array(
	'title' => 'Mobile Wall',
	'version' => '2.0.3',
	'vendor' => 'IlBelloDelWeb.it',
	'update_url' => '',
	
	'compatible_with' => array( // module compatibility
        '7.0.8','7.0.9'
    ),
	'home_dir' => 'ibdw/mobilewall/',
	'home_uri' => 'mobilewall',
	'db_prefix' => 'mobilewall',
  'class_prefix' => 'mobilewall',

	/**
	 * Installation instructions, for complete list refer to BxDolInstaller Dolphin class
	 */
	'install' => array( 
	  'check_dependencies' => 1,
		'execute_sql' => 1,
    'recompile_permalinks' => 1, 	
		'recompile_global_paramaters' => 1,
		'clear_db_cache' => 1,
		'update_languages' => 1,
	),
	/**
	 * Uninstallation instructions, for complete list refer to BxDolInstaller Dolphin class
	 */    
	'uninstall' => array (
	    'execute_sql' => 1,
		'update_languages' => 1,
    	'recompile_permalinks' => 1,
		'recompile_global_paramaters' => 1,
		'clear_db_cache' => 1,        
    ),
    
   /**
	 * Dependencies Section
	 */
    'dependencies' => array(
        'spy' => 'BoonEx Spy Module',
	),  

	/**
	 * Category for language keys, all language keys will be places to this category, but it is still good practive to name each language key with module prefix, to avoid conflicts with other mods.
	 */
	'language_category' => 'mobilewall',

	/**
	 * Permissions Section, list all permissions here which need to be changed before install and after uninstall, see examples in other BoonEx modules
	 */
	'install_permissions' => array(),
    'uninstall_permissions' => array(),

	/**
	 * Introduction and Conclusion Section, reclare files with info here, see examples in other BoonEx modules
	 */
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