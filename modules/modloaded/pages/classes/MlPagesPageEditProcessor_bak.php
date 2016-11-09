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
		
		//$iId = bx_get('ID');
		$this -> iPageID = (int)$iId;
		$iPageInfo = db_arr("SELECT `EntryUri`, `ResponsibleID`, `SubCategory`, `MainCategory` FROM `ml_pages_main` WHERE `ID` = {$this -> iPageID} LIMIT 1");
		$this -> sUri = $iPageInfo['EntryUri'];
		$this -> sSubCategory = $iPageInfo['SubCategory'];
		$this -> sMainCategory = $iPageInfo['MainCategory'];
		// basic checks
		if( $logged['member'] ) {
			$iMemberID = getLoggedId();
			
				// check if this member is owner
				if( $iPageInfo['ResponsibleID'] == $iMemberID )
					$this -> iArea = 2;

		} elseif( $logged['admin'] )
			$this -> iArea = 3;
		elseif( $logged['moderator'] )
			$this -> iArea = 4;
		
		$this -> bAjaxMode = ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' );
		$this -> bForceAjaxSave = bx_get('force_ajax_save');

		//$this->aFormPrivacy['form_attrs']['action'] = BX_DOL_URL_ROOT . 'm/pages/browse/my&ml_pages_filter=edit_page&page_id=' . $this->iPageID;
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
		$this -> oPF = new MlPagesPageFields( $this -> iArea, $this -> sMainCategory, $this -> sSubCategory );
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
			$this -> oPF -> processPostValues( $this -> bCouple, $this -> aValues, $this -> aErrors, 0, $this -> iPageID, (int)$_POST['pf_block']);
			
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
	
	function savePage() {

		$aDiff = $this -> getDiffValues(0);
		$aUpd = $this -> oPF -> getPageFromValues( $aDiff );


    if ($aDiff['Title'])
    	$aUpd['EntryUri'] =  uriGenerate($aDiff['Title'], 'ml_pages_main', 'Title');    
    	 
		$aUpd['DateLastEdit'] = date( 'Y-m-d H:i:s' );
		$aUpd['MainCategory'] = bx_get('main_category');
		$aUpd['SubCategory'] = bx_get('sub_category');
		$sTitle = $aDiff['Title'] ? $aDiff['Title'] : db_value("SELECT `Title` FROM `ml_pages_main` WHERE `ID` = {$this -> iPageID} LIMIT 1");
		foreach($aUpd as $sKey => $sValue)
		{
			if ($aUpd[$sKey . '_photos'])
			{
				$sDivider = getParam('ml_pages_multi_divider');
				$aPhotos = explode($sDivider, $aUpd[$sKey . '_photos']);
				$aWithPhotoData = explode($sDivider, $aUpd[$sKey]);
        if(isset($aWithPhotoData) && !empty($aWithPhotoData))
        {
        	$aWithPhotos = array();
        	$aPhotoIds = array();
     			for ($i = 0; $i < count($aWithPhotoData); $i++)
     			{

     				if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader')) 
     				{
              $aFileInfo = array (
              		'medTitle' => _t('_ml_page_field_photo'),
              		'Categories' => array('page wall'),
                  'album' => _t('_ml_page_photo_album', $sTitle),
              );
              $iPhotoId = BxDolService::call('photos', 'perform_photo_upload', array($GLOBALS['dir']['tmp'] . $aPhotos[$i], $aFileInfo, false), 'Uploader');
     				}
     				$iPhotoId = $iPhotoId ? $iPhotoId : 0;
     				array_push($aPhotoIds, $iPhotoId);
     				array_push($aWithPhotos, $aWithPhotoData[$i]);
     			}
       		$sPhotoFieldIds = implode($sDivider, $aPhotoIds);
       		$sWithPhotoData = implode($sDivider, $aWithPhotos);
        }
				if ($sWithPhotoData)
				{ 
					$sWithPhotoData= addslashes($sWithPhotoData);
					db_res("UPDATE `ml_pages_main` SET `{$sKey}` = CONCAT(`{$sKey}`,'{$sWithPhotoData}') WHERE `ID` = {$this -> iPageID} LIMIT 1");
					db_res("UPDATE `ml_pages_main` SET `FieldPhotos` = CONCAT(`FieldPhotos`,'{$sPhotoFieldIds}') WHERE `ID` = {$this -> iPageID} LIMIT 1");
				}
				unset($aUpd[$sKey . '_photos']);
				unset($aUpd[$sKey]);
			}
		}
		$this -> oPC -> updatePage( $this -> iPageID, $aUpd );

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
						if( $mNewS[$iKey] != $mOld[$iKey] ) {
							$aDiff[$sName] = $mNew; //found difference
							break;
						}
				} else
					$aDiff[$sName] = $mNew;
			} else {
				if( $mNew != $mOld )
					$aDiff[$sName] = $mNew;
			}
		}
		
		return $aDiff;
	}
}

?>