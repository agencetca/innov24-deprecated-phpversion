<?php
/***************************************************************************
* Date				: Jun 17, 2011
* Copywrite			: (c) 2011 by kazatzo
*
* Product Name		: Image News
* Product Version	: 1.0
*
* IMPORTANT: This is a commercial product made by kazatzo
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from kazatzo
*
***************************************************************************/

$aConfig = array(
	/**
	 * Main Section.
	 */
	'title' => 'Image News',
	'version' => '1.0.0',
	'vendor' => 'kazatzo',
	'update_url' => '',
	
	'compatible_with' => array(
        '7.0.4',
        '7.0.5',
        '7.0.6',
		'7.0.7',
		'7.0.8',
		'7.0.9'
	),

	/**
	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
	 */
	'home_dir' => 'kazatzo/imagenews/',
	'home_uri' => 'imagenews',
	
	'db_prefix' => 'imagenews_',
	'class_prefix' => 'ImageNews',
	/**
	 * Installation/Uninstallation Section.
	 */
	'install' => array(
		'show_introduction' => 1,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_global_paramaters' => 1,
		'recompile_main_menu' => 1,
		'recompile_member_menu' => 1,
		'recompile_site_stats' => 0,
		'recompile_page_builder' => 1,
		'recompile_profile_fields' => 0,
		'recompile_comments' => 1,
		'recompile_member_actions' => 1,
		'recompile_tags' => 1,
		'recompile_votes' => 1,
		'recompile_categories' => 1,
		'recompile_search' => 1,
		'recompile_injections' => 0,
		'recompile_permalinks' => 1,
		'recompile_alerts' => 1,
		'clear_db_cache' => 1,
		'show_conclusion' => 1
	),
	'uninstall' => array (
		'show_introduction' => 1,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_global_paramaters' => 1,
		'recompile_main_menu' => 1,
		'recompile_member_menu' => 1,
		'recompile_site_stats' => 0,
		'recompile_page_builder' => 1,
		'recompile_profile_fields' => 0,
		'recompile_comments' => 1,
		'recompile_member_actions' => 1,
		'recompile_tags' => 1,
		'recompile_votes' => 1,
		'recompile_categories' => 1,
		'recompile_search' => 1,
		'recompile_injections' => 0,
		'recompile_permalinks' => 1,
		'recompile_alerts' => 1,
		'clear_db_cache' => 1,
		'show_conclusion' => 1
	),
	/**
	 * Dependencies Section
	 */
	'dependencies' => array(),
	/**
	 * Category for language keys.
	 */
	'language_category' => 'Image News',
	/**
	 * Permissions Section
	 */
	'install_permissions' => array(),
	'uninstall_permissions' => array(),
	/**
	 * Introduction and Conclusion Section.
	 */
	'install_info' => array(
		'introduction' => 'inst_intro.html',
		'conclusion' => 'inst_concl.html'
	),
	'uninstall_info' => array(
		'introduction' => 'uninst_intro.html',
		'conclusion' => 'uninst_concl.html'
	)
);
?>
