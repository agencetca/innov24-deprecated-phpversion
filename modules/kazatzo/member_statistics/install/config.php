<?php
/***************************************************************************
* Date				: Jun 06, 2011
* Copywrite			: (c) 2011 by kazatzo
*
* Product Name		: Member Statistics
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
    	'title' => 'Member Statistics',
    	'version' => '1.0',
    	'vendor' => 'kazatzo',
		
		'db_prefix' => 'mem_stats_',
		'class_prefix' => 'MemberStatistics',

    	'update_url' => '',

    	'compatible_with' => array(
            '7.0.0',
            '7.0.1',
            '7.0.2',
            '7.0.3',
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
    	'home_dir' => 'kazatzo/member_statistics/',
    	'home_uri' => 'member_statistics',
		
		'' => '',
    	
 	/**
	 * Installation instructions, for complete list refer to BxDolInstaller Dolphin class
	 */
		'install' => array(
		  'update_languages' => 1,
		  'execute_sql' => 1,
		  'recompile_permalinks' => 1,
		),
		'uninstall' => array (
		  'update_languages' => 1,
		  'execute_sql' => 1,
		  'recompile_permalinks' => 1,
		),
        /**
    	 * Dependencies Section
    	 */
    	'dependencies' => array(
    	),
        /**
    	 * Category for language keys.
    	 */
    	'language_category' => 'Member Statistics',
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
    		'introduction' => '',
    		'conclusion' => 'uninst_concl.html'
    	)
    );
