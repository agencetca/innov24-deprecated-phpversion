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
class BxCreditPageMain extends BxDolTwigPageMain {
    function BxCreditPageMain(&$oMain) {
		$this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->oMain = $oMain;
        $this->sSearchResultClassName = 'BxCreditSearchResult';
        $this->sFilterName = 'modzzz_credit_filter';
		
		if(isset($_REQUEST['filter']) && ($_REQUEST['filter']=='history')) {
			parent::BxDolTwigPageMain('modzzz_credit_history', $oMain);
		}else{
			parent::BxDolTwigPageMain('modzzz_credit_main', $oMain);
		} 
	}
   
    function getBlockCode_Desc() {
		if(isset($_REQUEST['filter']) && ($_REQUEST['filter']=='history')) {
			return $this->getCreditHistory();
		}else{
			return $this->getCreditActions(); 
		}
	}
     
	function getCreditHistory() {
	 
		$sHeadActionsC = _t("_modzzz_credit_actions");
		$sHeadEarnedRedeemedC = _t("_modzzz_credit_earned_or_redeemed");
  
		$arrActions = $this->oDb->getCreditHistory($this->oMain->_iProfileId);
 		
		if(count($arrActions)){
			$arrTotals = $this->oDb->getCreditHistoryTotals($this->oMain->_iProfileId);
			$iEarned = $arrTotals[0];
			$iRedeemed = $arrTotals[1];
	 
			$aForm = array(
				'form_attrs' => array(
					'action' => '',
					'method' => 'post',
				),
				'params' => array(
					'remove_form' => true,
				) 
			);
		
			$iter=1;
			$sOldGroup = "";
			foreach($arrActions as $aEachAction)
			{ 
				
				$sActionC = _t($aEachAction['desc']);
				
				$sCreditsEarned = ($aEachAction['credits_earned']) ?
										 (double)$aEachAction['credits_earned'].' '._t("_modzzz_credit_credits_earned") : '';
	
				$sCreditsRedeemed = ($aEachAction['credits_redeemed']) ?
										 (double)$aEachAction['credits_redeemed'].' '._t("_modzzz_credit_credits_redeemed") : '';
				
	
			$sNewGroup = _t($aEachAction['group']);
				if($sOldGroup != $sNewGroup) {
					
					if($sOldGroup != "") { 
						$aForm['inputs']["header{$iter}_end"] = array(
							'type' => 'block_end'
						);
					}
	
					$aForm['inputs']["header{$iter}"] = array(
						'type' => 'block_header',
						'caption' => "<b>{$sNewGroup}</b>",
						'collapsable' => true,
						'collapsed' => ($iter==1) ? false : true, 
					);
	 
					$aForm['inputs']["ItemHead{$iter}"] = array(
						'type' => 'custom',
						'name' => "ItemHead{$iter}", 
						'content' =>  "<div style='width:100%'><div style='float:left;width:50%'><b>{$sHeadActionsC}</b></div><div style='float:left;width:50%'><b>{$sHeadEarnedRedeemedC}</b></div></div><div class='clear_both'></div>",  
					 
						'colspan' => true
					);
	 
				}
	 
				 $aForm['inputs']["Item{$iter}"] = array(
					'type' => 'custom',
					'name' => "Item{$iter}", 
					'content' =>  "<div style='width:100%'><div style='float:left;width:50%'>{$sActionC}</div><div style='float:left;width:50%'>{$sCreditsEarned}{$sCreditsRedeemed}</div></div><div class='clear_both'></div>",  
				 
					'colspan' => true
				);
	
				$sOldGroup = $sNewGroup;
				$iter++;
	
			}//END
	
			$aForm['inputs']["header{$iter}_end"] = array(
				'type' => 'block_end'
			);
	  
			$oForm = new BxTemplFormView($aForm);
			$sCode = '<div class="dbContent">' . $oForm->getCode() . '</div>'; 
		}else{
			$sCode = MsgBox(_t('_modzzz_credit_msg_no_history'));
		}
		$aTopMenu = array(
			_t('_modzzz_credit_menu_main') => array(
				'href' => $this->_oConfig->getBaseUri() . 'home', 
			),
			_t('_modzzz_credit_menu_history') => array(
				'href' => $this->_oConfig->getBaseUri() . 'history',
				'active' => true, 
			)
		);
 
		return array(
				$sCode, 
				$aTopMenu	 
			);    
	}
  
	function getCreditActions() {
 
		$sHeadValueC = _t("_modzzz_credit_caption_value");
		$sHeadActionsC = _t("_modzzz_credit_actions");
 		$arrActions = $this->oDb->getCreditActions();
	  
        $aForm = array(
            'form_attrs' => array(
                'action' => '',
                'method' => 'post',
            ),
            'params' => array(
                'remove_form' => true,
            ) 
		);
 	
		$iter=1;
		$sOldGroup = "";
		foreach($arrActions as $aEachAction){
		 
			$sCreditsEarned = ($aEachAction['credits_earned']) ?
									 (double)$aEachAction['credits_earned'].' '._t("_modzzz_credit_credits_earned") : '';
 
			
			$sNewGroup = _t($aEachAction['group']);
			if($sOldGroup != $sNewGroup) {
				
				if($sOldGroup != "") { 
					$aForm['inputs']["header{$iter}_end"] = array(
						'type' => 'block_end'
					);
				}
				$aForm['inputs']["header{$iter}"] = array(
					'type' => 'block_header',
					'caption' => "<b>{$sNewGroup}</b>",
					'collapsable' => true,
					'collapsed' => ($iter==1) ? false : true, 
				);
				 $aForm['inputs']["ItemHead{$iter}"] = array(
					'type' => 'custom',
					'name' => "ItemHead{$iter}",
					'content' =>  "<div style='width:100%'><div style='float:left;width:48%'><b>{$sHeadActionsC}</b></div><div style='float:left;width:48%'><b>{$sHeadValueC}</b></div></div><div class='clear_both'></div>",  
					'colspan' => true
				);
 
			}
			
			$sAction = _t($aEachAction['desc']);
			
			$sValue = ($aEachAction['action_type']=='system') ? '-' : number_format($aEachAction['value']); 
 				
			 $aForm['inputs']["Item{$iter}"] = array(
				'type' => 'custom',
				'name' => "Item{$iter}",
				'content' =>  "<div style='width:100%'><div style='float:left;width:48%'>{$sAction}</div><div style='float:left;width:48%'>{$sValue}</div> </div><div class='clear_both'></div>",  
				'colspan' => true
			);
			$sOldGroup = $sNewGroup;
			$iter++;
		}//END
		$aForm['inputs']["header{$iter}_end"] = array(
			'type' => 'block_end'
		);
 
 
		$oForm = new BxTemplFormView($aForm);
		$sCode = '<div class="dbContent">' . $oForm->getCode() . '</div>'; 
 
		$aTopMenu = array(
			_t('_modzzz_credit_menu_main') => array(
				'href' => $this->_oConfig->getBaseUri() . 'home', 
				'active' => true,
			),
			_t('_modzzz_credit_menu_history') => array(
				'href' => $this->_oConfig->getBaseUri() . 'history', 
			)
		);
  
		return array(
				$sCode, 
				$aTopMenu	 
			);  
	}
 
	function getCreditMain() {
        return BxDolModule::getInstance('BxCreditModule');
    }
  
}
?>