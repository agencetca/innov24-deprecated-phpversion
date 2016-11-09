<?
/*************************Product owner info********************************
*
*     author               : Boonexpert 
*     contact info         : boonexpert@gmail.com
*
/*************************Product info**************************************
*
*                          Master Database Backup
*                          ------------------------
*     version              : 1.0
*     date		   : 27 December 2010
*     compability          : Dolphin 7.0.0 - 7.x.x
*     License type         : Custom
*
* IMPORTANT: This is a commercial product made by Boonexpert and cannot be modified for other than personal use. 
* This product cannot be redistributed for free or a fee without written permission from Boonexpert. 
*
*     Upgrade possibilities : All future upgrades will be added to this product package
*
****************************************************************************/

$aConfig = array(

	'title' => 'Master Database Backup',
	'version' => '1.0',
	'vendor' => 'Boonexpert',
	'update_url' => '',
	
	'compatible_with' => array(
        '7.x.x' 
   ),


	  'home_dir' => 'boonexpert/MasterBackup/',
	  'home_uri' => 'MasterBackup',
	  'db_prefix' => 'Mdb_',
	  'class_prefix' => 'Mdb',

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


	  'language_category' => 'MasterBackup',

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
