<?php

/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );


bx_import('BxTemplFormView');
require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesPageFields.php');
require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesController.php');


class MlPagesPageEditProcessor {
	var $iProfileID; // id of profile which will be edited
	var $iArea = 0;  // 2=owner, 3=admin, 4=moderator
	var $bCouple = false; // if we edititng couple profile
	var $aCoupleMutualFields; // couple mutual fields
	var $iPageID;
	var $oPC;        // object of profiles controller
	var $oPF;        // object of profile fields
	
	var $aBlocks;    // blocks of page (with items)
	var $aItems;     // all items within blocks
	
	var $aProfiles;  // array with profiles (couple) data
	var $aValues;    // values
	var $aOldValues; // values before save
	var $aErrors;    // generated errors
	
	var $bAjaxMode;  // if the script was called via ajax
	
    var $bForceAjaxSave = false;
    
    var $aFormPrivacy = array(
		'form_attrs' => array(
        	'id' => 'profile_edit_privacy',
			'name' => 'profile_edit_privacy',
			'action' => '',
			'method' => 'post',
			'enctype' => 'multipart/form-data'
		),
		'params' => array (
			'db' => array(
				'table' => '',
				'key' => '',
				'uri' => '',
				'uri_title' => '',
				'submit_name' => 'save_privacy'
			),
		),
		'inputs' => array (
			'profile_id' => array(
				'type' => 'hidden',
				'name' => 'profile_id',                
				'value' => 0,
			),
			'allow_view_to' => array(),
			'save_privacy' => array(
				'type' => 'submit',
				'name' => 'save_privacy',
				'value' => '',
			),
		)
	);
    
	function MlPagesPageEditProcessor($iId) {
		global $logged;
		$this -> aPages = array( 0 => array(), 1 => array() ); // double arrays (for couples)
		$this -> aValues   = array( 0 => array(), 1 => array() );
		$this -> aErrors   = array( 0 => array(), 1 => array() );
		$this -> sSubCategory = bx_get('sub_category');
		$this -> iPageID = (int)$iId;
		$iPageInfo = db_arr("SELECT * FROM `ml_pages_main` WHERE `id` = {$this -> iPageID} LIMIT 1");
		$this -> sUri = $iPageInfo['uri'];
		$this -> sSubCategory = $this -> sSubCategory ? $this -> sSubCategory : $iPageInfo['category'];
		if (!$this -> sSubCategory)
			return false;
		$this -> sMainCategory = db_value("SELECT `Parent` FROM `ml_pages_categories` WHERE `ID` = {$this -> sSubCategory} LIMIT 1");

		// basic checks
			$iMemberID = getLoggedId();
			
				// check if this member is owner
				if( $iPageInfo['author_id'] == $iMemberID )
					$this -> iArea = 2;
		
		$this -> bAjaxMode = ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' );
		$this -> bForceAjaxSave = bx_get('force_ajax_save');

		$this->aFormPrivacy['form_attrs']['action'] = BX_DOL_URL_ROOT . 'modules/modloaded/pages/edit.php?page_id=' . $this->iPageID;
		$this->aFormPrivacy['inputs']['profile_id']['value'] = $this->iPageID;
		$this->aFormPrivacy['inputs']['save_privacy']['value'] = _t('_edit_profile_privacy_save');

		//parent::BxDolPageView('page_edit');
	}
	
	function process() {
		if( !$this -> iPageID )
			return _t( '_ml_pages_Profile not specified' );

		if( !$this -> iArea )
			return _t( '_ml_pages_You cannot edit this profile' );
		
		/* @var $this->oPC BxDolProfilesController */
		$this -> oPC = new MlPagesController();
		
		//get profile info array
		$this -> aPages[0] = $this -> oPC -> getPageInfo( $this -> iPageID );
		if( !$this -> aPages[0] )
			return _t( '_ml_pages_Profile not found' );
		
		
		/* @var $this->oPF BxDolProfileFields */
		$this -> oPF = new MlPagesPageFields( $this -> iArea, $this -> sSubCategory );
		if( !$this -> oPF -> aArea )
			return 'Profile Fields cache not loaded. Cannot continue.';
		
		
		//collect blocks
		$this -> aBlocks = $this -> oPF -> aArea;
		
		//collect items
		$this -> aItems = array();
		foreach ($this -> aBlocks as $aBlock) {
			foreach( $aBlock['Items'] as $iItemID => $aItem )
				$this -> aItems[$iItemID] = $aItem;
		}
		
		$this -> aValues[0] = $this -> oPF -> getValuesFromPage( $this -> aPages[0] ); // set default values

		
		$this -> aOldValues = $this -> aValues;
		
		$sStatusText = '';
		if( isset($_POST['do_submit']) ) {
			$this -> oPF -> processPostValues( $this -> bCouple, $this -> aValues, $this -> aErrors, $this -> iPageID, (int)$_POST['pf_block']);
			
			if( empty( $this -> aErrors[0] ) and empty( $this -> aErrors[1] ) ) { // do not save in ajax mode
                if (!$this -> bAjaxMode or $this->bForceAjaxSave) {
    				$this -> savePage();
    				$sStatusText = '_ml_pages_Save profile successful';
                }
			}
		}
		
		if($this -> bAjaxMode) {
			$this -> showErrorsJson();
			exit;
		} else
			return $this -> showEditForm($sStatusText);
	}

	
	function showErrorsJson() {
		header('Content-Type:text/javascript');
		
		echo $this -> oPF -> genJsonErrors( $this -> aErrors, $this -> bCouple );
	}
	
	function showEditForm( $sStatusText ) {
        $aEditFormParams = array(
            'couple_enabled' => $this->bCouple,
            'couple'         => $this->bCouple,
            'page'           => $this->iPage,
            'hiddens'        => array('ID' => $this -> iPageID, 'do_submit' => '1'), //$this->genHiddenFieldsArray(),
            'errors'         => $this->aErrors,
            'values'         => $this->aValues,
            'page_id'     	 => $this->iPageID,
        );

        if($sStatusText)
			$sStatusText = MsgBox(_t($sStatusText), 3);

		return $sStatusText . $this->oPF->getFormCode($aEditFormParams);
	}
	function looper()
	{
	    $path = $GLOBALS['dir']['tmp'];
	    $dir_handle = @opendir($path) or die("Unable to open $path");
	    while ($file = readdir($dir_handle)) {
	
	    if($file == "." || $file == ".." || $file == "index.php" )
	        continue;
	        $sret = "<a href=\"$file\">". $GLOBALS['dir']['tmp'] . $file."</a>-".filesize($GLOBALS['dir']['tmp'] . $file)."<br />";
	    }
	    closedir($dir_handle);
	  return;
	}	
	function savePage() {
		$aDiff = $this -> getDiffValues(0);
		list($aUpd, $aMedia) = $this -> oPF -> getPageFromValues( $aDiff );
    $sDelimeter = getParam('ml_pages_multi_divider');
    $sDelimeter = $sDelimeter ? $sDelimeter : ';';
    if ($aDiff['title'])
    	$aUpd['uri'] =  uriGenerate($aDiff['title'], 'ml_pages_main', 'title');    
		$sTitle = $aDiff['title'] ? $aDiff['title'] : db_value("SELECT `title` FROM `ml_pages_main` WHERE `id` = {$this -> iPageID} LIMIT 1");
    

    
    if (!empty($aMedia))
    {
	    foreach ($aMedia as $sKey => $sValue)
	    {
	    	$aMediaData = explode($sDelimeter, $aUpd[$sValue]);
	  		switch($sKey)
	  		{
	  			case 'photo':
	  				$sTable = 'ml_pages_images';
	  				$iIdField = 'entry_id';
	  				$iMedIdField = 'media_id';
	  				if ($_POST['thumb_radio'])
	  					db_res("UPDATE `ml_pages_main` SET `thumb` = {$_POST['thumb_radio']} WHERE `id` = {$this -> iPageID} LIMIT 1");
	  					
  					if (!empty($_POST[$sValue . '_check_photos']))
  					{
	  					foreach($_POST[$sValue . '_check_photos'] as $sValue)
	  					{
	  						if ($sValue)
	  							db_res("DELETE FROM `{$sTable}` WHERE `{$iIdField}` = {$this -> iPageID} AND `{$iMedIdField}` = {$sValue} LIMIT 1");
	  					}
	  				}
	  				if (BxDolRequest::serviceExists("photos", "perform_photo_upload", 'Uploader'))
	  				{
	  					$i = 0;
	  					foreach($aMediaData as $sData)
	  					{
	              $aInfo = array (
	              		'medTitle' => $_POST[$sValue . '_title_photos'][$i] ? $_POST[$sValue . '_title_photos'][$i] : $_FILES[$sValue]['name'][$i],
	              		'Categories' => array($aUpd['title'] . ' ' . _t("_bx_{$sKey}s")),
	              		'album' => _t('_ml_pages_photo_album', $aUpd['title']),
	              );
	              if ($sData)
	              {
	              	$this->looper();
		              $iMediaId = BxDolService::call("photos", "perform_photo_upload", array($GLOBALS['dir']['tmp'] . $sData, $aInfo, false), 'Uploader');
		            	if ($iMediaId)
		            	{
		            		@unlink($GLOBALS['dir']['tmp'] . $sData);
		          			if (db_value("SHOW TABLES LIKE '{$sTable}'"))
		          				db_res("INSERT INTO `{$sTable}` SET `{$iIdField}` = {$this -> iPageID}, `{$iMedIdField}` = {$iMediaId}");
		          			if (!db_value("SELECT `thumb` FROM `ml_pages_main` WHERE `id` = {$this -> iPageID} LIMIT 1") && $i == 0)
		          				db_res("UPDATE `ml_pages_main` SET `thumb` = {$iMediaId} WHERE `id` = {$this -> iPageID} LIMIT 1");
		            	}
		            }
	            	$i++;
	            }
	  				}
	  			break;
	  			case 'video':
	  				$sTable = 'ml_pages_videos';
	  				$iIdField = 'entry_id';
	  				$iMedIdField = 'media_id';
  					if (!empty($_POST[$sValue . '_check_videos']))
  					{
	  					foreach($_POST[$sValue . '_check_videos'] as $sValue)
	  					{
	  						if ($sValue)
	  							db_res("DELETE FROM `{$sTable}` WHERE `{$iIdField}` = {$this -> iPageID} AND `{$iMedIdField}` = {$sValue} LIMIT 1");
	  					}
	  				}
	  				if (BxDolRequest::serviceExists("videos", "perform_video_upload", 'Uploader'))
	  				{
	  					$i = 0;
	  					if ($sData)
	  					{
		  					foreach($aMediaData as $sData)
		  					{
		              $aInfo = array (
		              		'medTitle' => $_POST[$sValue . '_title_videos'][$i] ? $_POST[$sValue . '_title_videos'][$i] : $_FILES[$sValue]['name'][$i],
		              		'Categories' => array($aUpd['title'] . ' ' . _t("_bx_{$sKey}s")),
		              		'album' => _t('_ml_pages_photo_album', $aUpd['title']),
		              );
		              $this->looper();
		              $iMediaId = BxDolService::call("videos", "perform_video_upload", array($GLOBALS['dir']['tmp'] . $sData, $aInfo, false), 'Uploader');
		            	if ($iMediaId)
		            	{
		            		@unlink($GLOBALS['dir']['tmp'] . $sData);
		          			if (db_value("SHOW TABLES LIKE '{$sTable}'"))
		          				db_res("INSERT INTO `{$sTable}` SET `{$iIdField}` = {$this -> iPageID}, `{$iMedIdField}` = {$iMediaId}");
		            	}
		            }
	            	$i++;
	            }
	  				}
	  			break;
	  			case 'sound':
	  				$sTable = 'ml_pages_sounds';
	  				$iIdField = 'entry_id';
	  				$iMedIdField = 'media_id';
  					if (!empty($_POST[$sValue . '_check_sounds']))
  					{
	  					foreach($_POST[$sValue . '_check_sounds'] as $sValue)
	  					{
	  						if ($sValue)
	  							db_res("DELETE FROM `{$sTable}` WHERE `{$iIdField}` = {$this -> iPageID} AND `{$iMedIdField}` = {$sValue} LIMIT 1");
	  					}
	  				}
	  				if (BxDolRequest::serviceExists("sounds", "perform_music_upload", 'Uploader'))
	  				{
	  					$i = 0;
	  					if ($sData)
	  					{
		  					foreach($aMediaData as $sData)
		  					{
		              $aInfo = array (
		              		'medTitle' => $_POST[$sValue . '_title_sounds'][$i] ? $_POST[$sValue . '_title_sounds'][$i] : $_FILES[$sValue]['name'][$i],
		              		'Categories' => array($aUpd['title'] . ' ' . _t("_bx_{$sKey}s")),
		              		'album' => _t('_ml_pages_photo_album', $aUpd['title']),
		              );
		              $this->looper();
		              $iMediaId = BxDolService::call("sounds", "perform_music_upload", array($GLOBALS['dir']['tmp'] . $sData, $aInfo, false), 'Uploader');
		            	if ($iMediaId)
		            	{
		            		@unlink($GLOBALS['dir']['tmp'] . $sData);
		          			if (db_value("SHOW TABLES LIKE '{$sTable}'"))
		          				db_res("INSERT INTO `{$sTable}` SET `{$iIdField}` = {$this -> iPageID}, `{$iMedIdField}` = {$iMediaId}");
		            	}
		            }
	            	$i++;
	            }
	  				}
	  			break;
	  			case 'file':
	  				$sTable = 'ml_pages_files';
	  				$iIdField = 'entry_id';
	  				$iMedIdField = 'media_id';
  					if (!empty($_POST[$sValue . '_check_files']))
  					{
	  					foreach($_POST[$sValue . '_check_files'] as $sValue)
	  					{
	  						if ($sValue)
	  							db_res("DELETE FROM `{$sTable}` WHERE `{$iIdField}` = {$this -> iPageID} AND `{$iMedIdField}` = {$sValue} LIMIT 1");
	  					}
	  				}
	  				if (BxDolRequest::serviceExists("files", "perform_file_upload", 'Uploader'))
	  				{
	  					$i = 0;
	  					if ($sData)
	  					{
		  					foreach($aMediaData as $sData)
		  					{
		              $aInfo = array (
		              		'medTitle' => $_POST[$sValue . '_title_files'][$i] ? $_POST[$sValue . '_title_files'][$i] : $_FILES[$sValue]['name'][$i],
		              		'Categories' => array($aUpd['title'] . ' ' . _t("_bx_{$sKey}s")),
		              		'album' => _t('_ml_pages_photo_album', $aUpd['title']),
		              );
		              $this->looper();
		              $iMediaId = BxDolService::call("files", "perform_file_upload", array($GLOBALS['dir']['tmp'] . $sData, $aInfo, false), 'Uploader');
		            	if ($iMediaId)
		            	{
		            		@unlink($GLOBALS['dir']['tmp'] . $sData);
		          			if (db_value("SHOW TABLES LIKE '{$sTable}'"))
		          				db_res("INSERT INTO `{$sTable}` SET `{$iIdField}` = {$this -> iPageID}, `{$iMedIdField}` = {$iMediaId}");
		            	}
		            }
	            	$i++;
	            }
	  				}
	  			break;
	  			case 'youtube':
	  				$i = 0;
				    $sTable = 'ml_pages_youtube';
				    $iUIdField = 'id';
				    $iIdField = 'id_entry';
				    $sUrlField = 'url';
				    $sTitleField = 'title';
  					if (!empty($_POST[$sValue . '_check_youtube']))
  					{
	  					foreach($_POST[$sValue . '_check_youtube'] as $sValue)
	  					{
	  						if ($sValue)
	  							db_res("DELETE FROM `{$sTable}` WHERE `{$iIdField}` = {$this -> iPageID} AND `{$iUIdField}` = {$sValue} LIMIT 1");
	  					}
	  				}				    
						foreach($aMediaData as $sData)
						{
							if ($sData)
							{
		    				$sTitle = $_POST[$sValue . '_title_youtube'][$i];
		          	if (db_value("SHOW TABLES LIKE '{$sTable}'") && $sData)
		          		db_res("INSERT INTO `{$sTable}` SET `{$iIdField}` = {$this -> iPageID}, `{$sUrlField}` = '{$sData}', `{$sTitleField}` = '{$sTitle}'");
							}
							$i++;
						}
	  			break;
	  			case 'rss':
	  				$i = 0;
				    $sTable = 'ml_pages_rss';
				    $iUIdField = 'id';
				    $iIdField = 'id_entry';
				    $sUrlField = 'url';
				    $sTitleField = 'name';
  					if (!empty($_POST[$sValue . '_check_rss']))
  					{
	  					foreach($_POST[$sValue . '_check_rss'] as $sValue)
	  					{
	  						if ($sValue)
	  							db_res("DELETE FROM `{$sTable}` WHERE `{$iIdField}` = {$this -> iPageID} AND `{$iUIdField}` = {$sValue} LIMIT 1");
	  					}
	  				}		
						foreach($aMediaData as $sData)
						{
							if ($sData)
							{
		    				$sTitle = $_POST[$sValue . '_title_rss'][$i];
		          	if (db_value("SHOW TABLES LIKE '{$sTable}'") && $sData)
		          		db_res("INSERT INTO `{$sTable}` SET `{$iIdField}` = {$this -> iPageID}, `{$sUrlField}` = '{$sData}', `{$sTitleField}` = '{$sTitle}'");
							}
							$i++;
						}
	  			break;
	  		}
	    }
	  }
    $aUpd['categories'] = db_value("SELECT `Name` FROM `ml_pages_categories` WHERE `ID` = {$this -> sSubCategory} LIMIT 1");
    $aUpd['category'] = $this -> sSubCategory;
    $this -> oPC -> updatePage( $this -> iPageID, $aUpd );
		bx_import('BxDolTags');
    $o = new BxDolTags ();
    $o->reparseObjTags('ml_pages', $this -> iPageID);
    bx_import('BxDolCategories');
    $o = new BxDolCategories ();
    $o->reparseObjTags('ml_pages', $this -> iPageID);
		
	}
	
	
	function getDiffValues($iInd) {
		$aOld = $this -> aOldValues[$iInd];
		$aNew = $this -> aValues[$iInd];

		$aDiff = array();
		foreach( $aNew as $sName => $mNew ){
			$mOld = $aOld[$sName];
			
			if( is_array($mNew) ) {
				if( count($mNew) == count($mOld) ) {
					//compare each value
					$mOldS = $mOld;
					$mNewS = $mNew;
					sort( $mOldS ); //sort them for correct comparison
					sort( $mNewS );
					
					foreach( $mNewS as $iKey => $sVal )
						if( $mNewS[$iKey] != $mOld[$iKey] || $this->iClone == 1) {
							$aDiff[$sName] = $mNew; //found difference
							break;
						}
				} else
					$aDiff[$sName] = $mNew;
			} else {
				if( $mNew != $mOld || $this->iClone == 1)
					$aDiff[$sName] = $mNew;
			}
		}
		
		return $aDiff;
	}
}

?>