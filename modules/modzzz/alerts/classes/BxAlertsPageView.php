<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Confession
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

bx_import('BxDolTwigPageView');

class BxAlertsPageView extends BxDolTwigPageView {	

	function BxAlertsPageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('modzzz_alerts_view', $oMain, $aDataEntry);
	}
		
	function getBlockCode_Info() {
        return $this->_blockInfo ($this->aDataEntry, $this->_oTemplate->blockFields($this->aDataEntry));
    }
  	 
	//override the similar mod in the parent class
    function _blockInfo ($aData, $sFields = '') {

        $aAuthor = getProfileInfo($aData['author_id']);
 
		$sAuthorName =  $aAuthor['NickName'];
		$sAuthorLink = getProfileLink($aAuthor['ID']);	
		$icoThumb = get_member_thumbnail($aAuthor['ID'], 'none');
	  
        $aVars = array (
            'author_thumb' => $icoThumb,
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
            'author_username' => $sAuthorName,
            'author_url' => $sAuthorLink,
        );
        return $this->_oTemplate->parseHtmlByName('entry_view_block_info', $aVars);
    }
 
	function getBlockCode_Desc() {
        return $this->_oTemplate->blockDesc ($this->aDataEntry);
    }
 
    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
 
            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_modzzz_alerts_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_alerts_action_title_delete') : '',
            );

            if (!$aInfo['TitleEdit'] && !$aInfo['TitleDelete']) 
                return '';

            return $oFunctions->genObjectsActions($aInfo, 'modzzz_alerts');
        } 

        return '';
    }    
 

    function getCode() {
 
        return parent::getCode();
    }

}

?>
