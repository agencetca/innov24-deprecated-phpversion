<?php
/***************************************************************************
*
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
*
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
*
* This notice may not be removed from the source code.
*
***************************************************************************/

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolPFM.php' );
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

class AqbPTSFieldsParser {
	var $_oDb;
	function AqbPTSFieldsParser($oDB) {
		$this->_oDb = $oDB;
	}

	function genAreaJSON( $iAreaID ) {
		$oFields = new BxDolPFM( $iAreaID );
		header( 'Content-Type:text/javascript' );
		return $oFields -> genJSON();
	}

	function saveItem( $iFieldID, $aData ) {
		$iProfileTypes = 0;
		$pts_relevant_to = 0;
		if (is_array($_POST['pts_relevant_to'])) {
			$aData = array_flip($aData['pts_relevant_to']);
			if ( isset($aData[1073741823]) ) {
				$pts_relevant_to = 1073741823;
			} else {
				foreach ($aData as $ptype => $dummy) {
					$pts_relevant_to += intval($ptype);
				}
		    }
		}

		$this->_oDb->setPFItemRelevancy($iFieldID, $pts_relevant_to);

		$aResult = array('message' => MsgBox(_t('_Saved')), 'timer' => 1);
		$oJson = new Services_JSON();
        return $oJson->encode($aResult);
	}

	function showEditForm( $iItemID ) {
		list($sFieldName, $iPFItemVisibility) = $this->_oDb->getPFItemRelevancy($iItemID);

		if ($sFieldName == 'ProfileType') return PopupBox('pf_edit_popup', $sFieldName, MsgBox(_t('_aqb_pts_pt_cant_be_edited')));

		$aProfileTypeValues = array();
		$aProfileTypeCheckedValues = array();
		$aProfileTypeValues[1073741823] = _t('_aqb_pts_visible_for_all');
		if ($iPFItemVisibility == 1073741823) $aProfileTypeCheckedValues[] = 1073741823;
		$aPTypes = $this->_oDb->getPairs("SELECT ID, Name FROM `aqb_pts_profile_types`", 'ID', 'Name');
		foreach ($aPTypes as $iType => $sName) {
			$aProfileTypeValues[$iType] = $sName;
			if ($iPFItemVisibility & $iType) $aProfileTypeCheckedValues[] = $iType;
		}

		$aPFItemEditForm = array(
	    	'form_attrs' => array(
                'id' => 'aqb_pf_item_edit',
                'name' => 'aqb_pf_item_edit',
                'action' => '',
                'method' => 'post',
                'onsubmit' => 'savePFItem(this); return false;'
            ),
            'inputs' => array (
            	'action' => array(
            		'type' => 'hidden',
            		'name' => 'action',
            		'value' => 'Save'
            	),
            	'field_id' => array(
            		'type' => 'hidden',
            		'name' => 'field_id',
            		'value' => $iItemID
            	),
            	'pts_relevant_to' => array(
            		'type' => 'checkbox_set',
					'caption' => _t('_aqb_pts_relevant_to'),
					'name' => 'pts_relevant_to',
					'value' => $aProfileTypeCheckedValues,
					'values' => $aProfileTypeValues
            	),
                'submit' => array(
					'type' => 'submit',
					'name' => 'save',
					'value' => _t('_Save Changes')
                )
			)
		);

		$oForm = new BxTemplFormView($aPFItemEditForm);

		$sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode().LoadingBox('formItemEditLoading')));
		return PopupBox('pf_edit_popup', $sFieldName, $sResult);
	}

	function updateCache($sCacheFolder) {
		 $rCacheFile = fopen($sCacheFolder.'profile_fields.inc', 'w');

		 $sCacheString = '';
		 $aProfileFields = $this->_oDb->getPFItemsRelevancy();
		 foreach ($aProfileFields as $aField) {
		 	$sCacheString .= "\t{$aField['FieldID']} => {$aField['ProfileTypes']},\n";
		 }

		 fputs($rCacheFile, "return array(\n{$sCacheString});");
		 fclose($rCacheFile);
	}
	function loadCache($sCacheFolder, $bStopRecursion = false) {
		$sCacheFile = $sCacheFolder.'profile_fields.inc';

		$aRet = array();
		if (!file_exists( $sCacheFile ) or !$aRet = @eval( file_get_contents($sCacheFile) ) or !is_array($aRet)) {
           $this->updateCache($sCacheFolder);
           if (!$bStopRecursion) $this->loadCache($sCacheFolder, true);
		}


        return $aRet;
	}
}
?>