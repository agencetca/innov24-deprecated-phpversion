<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Credit
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
bx_import('BxDolTwigPageMain');
bx_import('BxTemplCategories');
class BxCreditPageHistory extends BxDolTwigPageMain {
    function BxCreditPageHistory(&$oMain) {
		$this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->sSearchResultClassName = 'BxCreditSearchResult';
        $this->sFilterName = 'modzzz_credit_filter';
		parent::BxDolTwigPageMain('modzzz_credit_main', $oMain);
	}
   
    function getBlockCode_Desc() {
 
		if(isset($_REQUEST['filter']) && ($_REQUEST['filter']=='history')) {
			return $this->getCreditActions();
		}else{
			return $this->getCreditHistory(); 
		}
	}
     
	function getCreditHistory() {
		$arrActions = $this->oDb->getCreditHistory();
		
		if(count($arrActions)){
			$aParent = array();
			foreach($arrActions as $aEachAction){
			 
				$aParent[] = array (
						'group' => ucwords($aEachAction['unit']), 
						'description' => _t($aEachAction['desc']), 
						'value' => (double)$aEachAction['value'], 
						'limit' => (double)$aEachAction['limit'],  
					); 
			}
			$aVars = array('bx_repeat:items' => $aParent);
	
			$this->oTemplate->addCss(array('main.css'));
	  
			$sCode = $this->oTemplate->parseHtmlByName("credits_actions", $aVars);
		}else{
			$sCode = MsgBox(_t('_modzzz_credit_msg_no_history'));
		}
		
		return array(
			$sCode,
			array(
				_t('_modzzz_credit_history') => array(
					'href' => $this->_oConfig->getBaseUri() . 'home?filter=history', 
				)
			)
			);  
	}
  
	function getCreditActions() {
		$arrActions = $this->oDb->getCreditActions();
		
		$aParent = array();
		foreach($arrActions as $aEachAction)
		{ 
			$aParent[] = array (
					'group' => ucwords($aEachAction['unit']), 
					'description' => _t($aEachAction['desc']), 
					'value' => (double)$aEachAction['value'], 
					'limit' => (double)$aEachAction['limit'],  
				); 
		}
		$aVars = array('bx_repeat:items' => $aParent);
		$this->oTemplate->addCss(array('main.css'));
  
		$sCode = $this->oTemplate->parseHtmlByName("credits_actions", $aVars);
 
		return array(
			$sCode,
			array(
				_t('_modzzz_credit_history') => array(
					'href' => $this->_oConfig->getBaseUri() . 'home?filter=history', 
				)
			)
			);  
    }
 
	function getCreditMain() {
        return BxDolModule::getInstance('BxCreditModule');
    }
  
}
?>
