<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx 
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
function modzzz_credit_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'credit') {
        $oMain = BxDolModule::getInstance('BxCreditModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}
bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxTemplSearchResult');
 
/*
 * Credit module
 *
 * This module allow users to create user's credit, 
 * users can rate, comment and discuss credit.
 * Credit can have photos, videos, sounds and files, uploaded
 * by credit's admins.
 *
 * 
 *
 * Profile's Wall:
 * 'add credit' event is displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new credit was created
 * change - credit was chaned
 * rate - somebody rated credit
 * commentPost - somebody posted comment in credit
 *
 *
 *
 
 * 
 *
 
 *
 * Member menu item for credit (for internal usage only)
 * @see BxCreditModule::serviceGetMemberMenuItem
 * BxDolService::call('credit', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'modzzz_credit'
 * The following alerts are rised
 *
 
 *
 */
class BxCreditModule extends BxDolTwigModule {
 
    var $_aQuickCache = array ();
    function BxCreditModule(&$aModule) {
        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'modzzz_credit_filter';
        $this->_sPrefix = 'modzzz_credit';
 
	    $this->_oConfig->init($this->_oDb);
        $GLOBALS['oBxCreditModule'] = &$this;
		
		//reloads simple messenger block 
		if($_GET['ajax']=='messenger') { 
			$iViewedMemberId = (int)$_GET['id'];
			$sAction = $_GET['action'];
 
			if($_GET['accept']=='no'){
				echo "&nbsp;";
				exit;
			}
			if($_GET['accept']=='yes'){
				$this->_oDb->assignCredits($this->_iProfileId, 'simple_messenger', 'chat', 'subtract'); 
				
				echo $this->serviceGetMessengerField($iViewedMemberId); 
				exit;
			}
		}  
    }
	/**
	 * Function will generate messenger's input field ;
	 * Will generate messenger's part that allow logged member to send message ;
	 *
	 * @param  : $iViewedMemberId (integer) - Viewed member's Id ;
	 * @return : (text) - Html presentation data ;
	 */
	function serviceGetMessengerField($iViewedMemberId)
	{
		    $oMessenger = BxDolModule::getInstance('BxSimpleMessengerModule');
            if (!$oMessenger -> iLoggedMemberId || !get_user_online_status($iViewedMemberId) 
                    || $oMessenger -> iLoggedMemberId == $iViewedMemberId 
                    || isBlocked($iViewedMemberId, $oMessenger -> iLoggedMemberId)) {
                return '';
            }
            $sOutputCode = '';
   
			$aTemplateKeys = array (
				'message'   => _t( '_simple_messenger_chat_now' )  . '...',
				'res_id'    => $iViewedMemberId,
			);
 			
            $oMessenger -> _oTemplate  -> addJs('messenger_core.js');
			$sOutputCode = $oMessenger -> _oTemplate -> parseHtmlByName('send_message_field.html', $aTemplateKeys);
     
            return $sOutputCode;
    }
 
    function actionHome () {
        parent::_actionHome(_t('_modzzz_credit_page_title_home'));
    }
    function actionHistory () {
		$_REQUEST["filter"] = "history";
        parent::_actionHome(_t('_modzzz_credit_page_title_history'));
	}
  
    function actionTransactions () {
        $this->_oTemplate->pageStart();
        bx_import ('PageTransactions', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageTransactions';
        $oPage = new $sClass ($this);
        echo $oPage->getCode();
        $this->_oTemplate->addCss ('unit.css');
        $this->_oTemplate->addCss ('transactions.css');
        $this->_oTemplate->pageCode(_t('_modzzz_credit_page_title_transactions'), false, false);
    }
  
    function actionMembership () {
     
		if(!$this->_iProfileId)
			login_form( _t( "_LOGIN_OBSOLETE" ), 0, false );
 
	    $this->_oTemplate->pageStart();
 
        bx_import ('PageMembership', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageMembership';
        $oPage = new $sClass ($this);
		if( getParam("modzzz_credit_buy_membership")!='on' )
			echo MsgBox(_t("_modzzz_credit_membership_purchase_turned_off"));
		else
			echo $oPage->getCode();
 
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('unit.css');
        $this->_oTemplate->addCss ('memblevels.css');
        $this->_oTemplate->pageCode(_t('_modzzz_credit_page_title_membership_purchase'), false, false);
    }
    // ================================== external actions
	function assignCredits ($iMemberID, $sUnit, $sAction, $sActionType="add", $iEntryId=0, $bAdminCredits=0, $iMultiplier=1) { 
		$this->_oDb->assignCredits($iMemberID, $sUnit, $sAction, $sActionType, $iEntryId, $bAdminCredits, $iMultiplier);  
	}
  
    /**
     * Member accountpage block with different credit
     * @return html to display on accountpage in a block
     */     
    function serviceAccountBlock () {
 
		if(getParam("modzzz_credit_activated") != "on")
			return;
		if(! $this->_oDb->paidGender($this->_iProfileId) )
			return;
        $this->_oTemplate->addCss(array('unit.css'));
		$iCredits = $this->_oDb->getMemberCredits($this->_iProfileId); 
 
        $aVars = array ( 
			'buy_icon' => $this->_oTemplate->getIconUrl('buy_now.gif'),
            'credits' => number_format($iCredits),   
			'buy_link' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_credits' 
        );
 
        $ret = $this->_oTemplate->parseHtmlByName("member_credits", $aVars);
   
        return array(
			$ret,
			array(
                _t('_modzzz_credit_history') => array('href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/?filter=history', 'active' => false), 
            )	 
		);
       
    }
 
    function serviceGetMemberMenuItem () {
        parent::_serviceGetMemberMenuItem (_t('_modzzz_credit'), _t('_modzzz_credit'), 'credit.png');
    }
 
    // ================================== admin actions
    function actionAdministration ($sUrl = '', $sParam1='') {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        
        $this->_oTemplate->pageStart();
        $aMenu = array(
 /*
			'membership' => array(
                'title' => _t('_modzzz_credit_menu_admin_manage_membership'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/membership', 
                '_func' => array ('name' => 'actionAdministrationMembership', 'params' => array()),
            ), 	
*/			
			'manage' => array(
                'title' => _t('_modzzz_credit_menu_admin_manage_actions'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/manage', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array()),
            ), 
			'packages' => array(
                'title' => _t('_modzzz_credit_menu_manage_packages'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages',
                '_func' => array ('name' => 'actionAdministrationPackages', 'params' => array($sParam1)),
            ),  
            'allocate' => array(
                'title' => _t('_modzzz_credit_menu_admin_allocate'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/allocate',
                '_func' => array ('name' => 'actionAdministrationAllocate', 'params' => array()),
            ),  
            'settings' => array(
                'title' => _t('_modzzz_credit_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );
        if (empty($aMenu[$sUrl]))
            $sUrl = 'settings';
        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);
        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_credit_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin ('admin.css');
        $this->_oTemplate->addCssAdmin ('unit.css');
        $this->_oTemplate->addCssAdmin ('main.css');
        $this->_oTemplate->addCssAdmin ('forms_extra.css'); 
        $this->_oTemplate->addCssAdmin ('forms_adv.css');        
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_credit_page_title_administration'));
    }
    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Credit');
    }
 
    function actionAdministrationPackages ($sParam1='') {
 		$sMessage = "";
  		$iPackage = (int)process_db_input($sParam1);
 
		// check actions
		if(is_array($_POST)){
		
			if(isset($_POST['action_save']) && !empty($_POST['action_save']))
			{  
 				$this->_oDb->SavePackage();
				$sMessage = _t("Successfully Saved Package");
 			} 
			if(isset($_POST['action_edit']) && !empty($_POST['action_edit']))
			{   
 				$this->_oDb->UpdatePackage();
				$sMessage = _t("Successfully Updated Package");
  			} 
			if(isset($_POST['action_delete']) && !empty($_POST['action_delete']))
			{  
 				$this->_oDb->DeletePackage();
				$sMessage = _t("Successfully Removed Package");
			} 
			if(isset($_POST['action_add']) && !empty($_POST['action_add']))
			{  
				$iPackage = 0;  
			} 
 
		}
 
		$aPackages = $this->_oDb->getPackages();
		$aPackage[] = array(
			'value' => '',
			'caption' => ''  
		);
		foreach ($aPackages as $oPackage)
		{
			$sKey = $oPackage['id'];
			$sValue = $oPackage['credits'] .'-'. $this->_oConfig->getCurrencySign() . $oPackage['price'] ;
 
			$aPackage[] = array(
				'value' => $sKey,
				'caption' => $sValue ,
				'selected' => ($sKey == $iPackage) ? 'selected="selected"' : ''
			);
		}
		
		$sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('top_block_select.html', array(
			'name' => _t('_modzzz_credit_packages'),
			'bx_repeat:items' => $aPackage,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages/'
		));
		$aPackage = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "packages` WHERE  `id` = '$iPackage'");
		  
		$sFormName = 'packages_form';
  
	    if($iPackage){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_credit_btn_edit'),
				'action_delete' => _t('_modzzz_credit_btn_delete'), 
				'action_add' => _t('_modzzz_credit_btn_add')  
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_credit_btn_save')
			), 'pathes', false);	 
	    }
 
		$aVars = array(
 			'id'=> $aPackage['id'],  
   			'price' => $aPackage['price'],
			'credits' => $aPackage['credits'],  
 			'form_name' => $sFormName, 
			'controls' => $sControls,   
		);
		if($sMessage){
 			$sContent .= MsgBox($sMessage) ;
			$sContent .= "<form method=post>";
			$sContent .= BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
 				'action_add' => _t('_modzzz_credit_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_packages',$aVars);
		}
		return $sContent;
	}
    function actionAdministrationAllocate () {
		$sMessage = "";
		$iSearchCredits = "";
		$sSearchNick = "";
		// check actions
		if(is_array($_POST))
		{
			if(isset($_POST['action_search']) && !empty($_POST['action_search'])) { 
			 
				$sSearchParam = process_db_input($_POST['search_nickname']);
 				 
				if($sSearchParam) {  
					$iId = (double)$sSearchParam ? $sSearchParam :  getID($sSearchParam) ;
				} 
				if($sSearchParam && $iId) {
					$iSearchCredits = $this->_oDb->getMemberCredits($iId);
					$sSearchNick = getNickName($iId);  
				}else{
					$sMessage = MsgBox(_t("_modzzz_credit_member_not_found"));	
				} 
			} 
			if(isset($_POST['action_save']) && !empty($_POST['action_save'])) { 
			 
				$sName = process_db_input($_POST['nickname']);
 				$iCredits = process_db_input($_POST['credits']);
				$iId = $this->_oDb->getID($sName);
				 
				if($iId) { 
					if($iCredits == 0) {  
						$sMessage = MsgBox(_t("_modzzz_credit_allocate_credits_invalid"));
					}elseif($iCredits > 0) { 
						$this->_oDb->allocateCredits($iId, $iCredits); 
						$iSearchCredits = $this->_oDb->getMemberCredits($iId);
						$sSearchNick = getNickName($iId);  
						$sMessage = MsgBox(_t("_modzzz_credit_allocate_success"));
					}else {
						$this->_oDb->allocateCredits($iId, $iCredits); 
						$iSearchCredits = $this->_oDb->getMemberCredits($iId);
						$sSearchNick = getNickName($iId);  				
						$sMessage = MsgBox(_t("_modzzz_credit_deduct_success")); 
					}
				}else{
					$sMessage = MsgBox(_t("_modzzz_credit_member_not_found"));	
				} 
			} 
 
		}
  
		$sFormName = 'alloc_searchform';
   
		$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
			'action_search' => _t('_modzzz_credit_btn_search')
		), 'pathes', false);	 
	   
		$aVars = array(
			'info_icon' => getTemplateIcon("info.gif"),  
			'message' => $sMessage,  
  			'form_name' => $sFormName, 
			'controls' => $sControls
		);
		$sContent = $this->_oTemplate->parseHtmlByName('credit_admin_allocate_search',$aVars);
	  
		/******************/
		$sMessage = ""; 
		$sFormName = 'alloc_form';
   
		$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
			'action_save' => _t('_modzzz_credit_btn_submit')
		), 'pathes', false);	 
	   
		$aVars = array(
			'info_icon' => getTemplateIcon("info.gif"),  
			'message' => $sMessage, 
			'nick_value' => $sSearchNick, 
  			'form_name' => $sFormName, 
			'controls' => $sControls,
			'bx_if:search' => array( 
				'condition' =>  ($iSearchCredits>0),
				'content' => array(
					'present_credit_value' => $iSearchCredits,
				) 
			), 
		);
		$sContent .= $this->_oTemplate->parseHtmlByName('credit_admin_allocate',$aVars);
 
		return $sContent;
	}
/*  
    function actionAdministrationMembership () {
		$sHeadDaysC = _t("_modzzz_credit_price");
		$sHeadCreditsC = _t("_modzzz_credits");
		$sDaysC = strtolower( _t("_modzzz_credit_days") );
  
		if(getParam("modzzz_credit_buy_membership")!="on") {
			$sCode .= '<div class="dbContent">' . MsgBox(_t("_modzzz_credit_auto_membership_off")) . '</div>'; 
			return $sCode;
		}
		$sCurrencySign = getParam("currency_sign");
 
		if(count($_POST['credits'])) {
			foreach($_POST['credits'] as $key => $val)
			{
 				$iCredits = (double)$_POST['credits'][$key];
	 
				$this->_oDb->query("UPDATE `sys_acl_level_prices` SET `Credits`='$iCredits' WHERE `id`=$key"); 
				 
			}
		}  
  
		$arrLevels = getMemberships(); 
 
        $aForm = array(
            'form_attrs' => array(
                'action' => '',
                'method' => 'post',
            ), 
		    'params' => array (
			   'db' => array( 
				   'submit_name' => 'submit_form',     
			   ),
		   ), 
		);
 			   
		$aForm['inputs']["headerLevel"] = array(
			'type' => 'block_header',
			'caption' =>  "<b>"._t("_modzzz_credit_manage_membership_credits")."</b>",
			'colspan' => true 
		);
  
		$aForm['inputs']["headerLevel_end"] = array(
			'type' => 'block_end'
		);
 
		foreach($arrLevels as $iId=>$sMembershipName)
		{    
			if( ($iId == MEMBERSHIP_ID_NON_MEMBER) || ($iId == MEMBERSHIP_ID_STANDARD) || ($iId == MEMBERSHIP_ID_PROMOTION) ){
				continue;
			} 
			$aForm['inputs']["header_{$iId}"] = array(
				'type' => 'block_header',
				'caption' => "<b>{$sMembershipName}</b>",
				'collapsable' => true, 
				'collapsed' => false,
			);
			$iter=1;
			$arrPrices = $this->_oDb->getMembershipPrices($iId); 
			foreach($arrPrices as $aEachPrice) {
				$iPriceId = $aEachPrice['id'];
				$iDays = $aEachPrice['Days'];
				$iPrice = $aEachPrice['Price'];
				$iMembershipCredits = $aEachPrice['Credits'];
 
				if($iter==1) {
					$aForm['inputs']["ItemHeader_{$iId}_{$iter}"] = array(
						'type' => 'custom',
						'name' => "ItemHeader{$iter}",
						'content' =>  "<div style='width:100%'>
											<div style='float:left;width:50%'><b>{$sHeadDaysC}</b></div>
											<div style='float:left;width:49%'><b>{$sHeadCreditsC}</b></div> 
									  </div>
									  <div class='clear_both'></div>",  
						'colspan' => true
					); 
				}
				 
				$aForm['inputs']["Item_{$iId}_{$iter}"] = array(
					'type' => 'custom',
					'name' => "Item{$iter}",
					'content' =>  "<div style='width:100%'>
									<div style='float:left;width:50%'>{$iDays} {$sDaysC} - {$sCurrencySign}{$iPrice}</div>
									<div style='float:left;width:49%'><input type=text name='credits[$iPriceId]' value='$iMembershipCredits' size=5></div>  
								  </div>
								  <div class='clear_both'></div>",  
					'colspan' => true
				);
				
				$iter++;
			}//end price loop
			$aForm['inputs']["footer_{$iId}"] = array(
				'type' => 'block_end'
			);
		}//END level loop
		if($iter > 1) {
  
			$aForm['inputs']['Submit'] = array (
				'type' => 'submit',
				'name' => 'submit_form',
				'value' => _t('_Submit'),
				'colspan' => false,
			);  
		}else{
			 $aForm['inputs']["NoItem"] = array(
				'type' => 'custom',
				'name' => "NoItem",
				'content' =>  MsgBox(_t("_modzzz_credits_no_auto_levels")), 
				'colspan' => true
			);  
		}
		$oForm = new BxTemplFormView($aForm);
		$sCode .= '<div class="dbContent">' . $oForm->getCode() . '</div>'; 
 
		return $sCode;
	}
 */
    function actionAdministrationManage () {
 
		$sHeadValueC = _t("_modzzz_credit_caption_value");
		$sHeadActionsC = _t("_modzzz_credit_actions");
 		$sHeadActiveC = _t("_modzzz_credit_active");
 
		if(count($_POST['credits'])) {   
			foreach($_POST['credits'] as $key => $val){
 
 				$iCredits = (double)$_POST['credits'][$key];
				$bStatus = (int)$_POST['active'][$key];
 
		 		$this->_oDb->query("UPDATE `" . $this->_oDb->_sPrefix . "main` SET `value`='$iCredits', `active`='$bStatus' WHERE `id`=$key ");
			}  
		}  
 
        $aForm = array(
            'form_attrs' => array(
                'action' => '',
                'method' => 'post',
            ), 
		    'params' => array (
			   'db' => array( 
				   'submit_name' => 'submit_form',     
			   ),
		   ), 
		);
 			  
 		$arrActions =  $this->_oDb->getCreditActions(false, true);
 
		$iter=1;
		$sOldGroup = "";
		foreach($arrActions as $aEachAction)
		{ 
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
 
			$aForm['inputs']["ItemHeader{$iter}"] = array(
				'type' => 'custom',
				'name' => "ItemHeader{$iter}",
				'content' =>  "<div style='width:100%'>
									<div style='float:left;width:50%'><b>{$sHeadActionsC}</b></div>
									<div style='float:left;width:15%'><b>{$sHeadValueC}</b></div>
									<div style='float:left;width:10%'><b>{$sHeadActiveC}</b></div>
							  </div>
							  <div class='clear_both'></div>",  
				'colspan' => true
			);
			}
			
			$iId = $aEachAction['id'];
			$sAction = _t($aEachAction['desc']);
			$iValue = $aEachAction['value']; 
			$sStatus = ($aEachAction['active']) ? "checked='checked'" : "";
  
			$aForm['inputs']["Item{$iter}"] = array(
				'type' => 'custom',
				'name' => "Item{$iter}",
				'content' =>  "<div style='width:100%'>
								<div style='float:left;width:50%'>{$sAction}</div>
								<div style='float:left;width:15%'><input type=text name='credits[$iId]' value='{$iValue}' size=5></div>
								<div style='float:left;width:10%'><input type=checkbox name='active[$iId]' value='1' {$sStatus}></div>  
								<input type=hidden name='item[$iId]' value='{$iValue}'> 
							  </div>
							  <div class='clear_both'></div>",  
				'colspan' => true
			);
  
			$sOldGroup = $sNewGroup;
			$iter++;
		}//END
		if(count($arrActions)) {
			$aForm['inputs']["header{$iter}_end"] = array(
				'type' => 'block_end'
			);
	 
			$aForm['inputs']['Submit'] = array (
				'type' => 'submit',
				'name' => 'submit_form',
				'value' => _t('_Submit'),
				'colspan' => false,
			);  
		}else{
			 $aForm['inputs']["NoItem"] = array(
				'type' => 'custom',
				'name' => "NoItem",
				'content' =>  MsgBox(_t("_modzzz_credits_no_level_actions")), 
				'colspan' => true
			);  
		}
		$oForm = new BxTemplFormView($aForm);
		$sCode .= '<div class="dbContent">' . $oForm->getCode() . '</div>'; 
 
		return $sCode;
	}
    // ================================== events
 
 
    // ================================== permissions
  
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('permalinks_credit') == 'on'));
		 
        return $bEnabled;
    }
  
    function onEventCreate ($iEntryId, $sStatus, $aDataEntry = array()) {
  
		if ('approved' == $sStatus) {
            $this->reparseTags ($iEntryId);
            $this->reparseCategories ($iEntryId);
        }
 		$oAlert = new BxDolAlerts($this->_sPrefix, 'add', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
		$oAlert->alert();
    }
  
    function actionPaypalProcess($iProfileId) {
        
        $aData = &$_REQUEST;
  
		$sPostData = '';
        $sPageContent = '';
        if($aData) {
  
        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));
        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPurchaseCredits(_t('_modzzz_credit_purchase_failed')); 
 				return;
			}
        	array_walk($aResponse['content'], create_function('&$arg', "\$arg = trim(\$arg);"));
        	if(strcmp($aResponse['content'][0], "INVALID") == 0){
  				$this->actionPurchaseCredits(_t('_payment_pp_err_wrong_transaction'));
 				return; 
        	}
			if(strcmp($aResponse['content'][0], "VERIFIED") != 0){
  				$this->actionPurchaseCredits(_t('_payment_pp_err_wrong_verification_status'));
 				return;  
			}
  
			if($aData['txn_type'] != 'web_accept') {
				$this->actionPurchaseCredits(_t('_modzzz_credit_purchase_failed'));
 			}else { 
 
				if($this->_oDb->isExistPaypalTransaction($iProfileId, $aData['txn_id'])) {
					$this -> actionPurchaseCredits(_t('_modzzz_credit_transaction_completed_already')); 
  				
				} else {
  
					if( $this->_oDb->saveTransactionRecord($iProfileId, $aData['item_number'], $aData['txn_id'], 'Paypal Purchase')) {
						$this->assignCredits($iProfileId, "paypal_purchase", "add", "add", time(), $aData['item_number']); 
						$this->actionPurchaseCredits(_t('_modzzz_credit_purchase_success',  $aData['item_number']));
					} else {
						$this -> actionPurchaseCredits(_t('_modzzz_credit_purchase_fail'));
					}
				}
			}
 
        } 
    }
 
    function _readValidationData($sRequest) {
        $sHeader = "POST /cgi-bin/webscr HTTP/1.0\r\n";
    	$sHeader .= "Host: www.paypal.com\r\n";
    	$sHeader .= "Content-Type: application/x-www-form-urlencoded\r\n";
    	$sHeader .= "Content-Length: " . strlen($sRequest) . "\r\n";
    	$sHeader .= "Connection: close\r\n\r\n";
    	
    	$iErrCode = 0;
    	$sErrMessage = "";
		$rSocket = fsockopen("ssl://www.paypal.com", 443, $iErrCode, $sErrMessage, 60);
 
    	if(!$rSocket)
    		return array('code' => 2, 'message' => 'Can\'t connect to remote host for validation (' . $sErrMessage . ')');
    	fputs($rSocket, $sHeader . $sRequest);
    	$sResponse = '';
        while(!feof($rSocket))
            $sResponse .= fread($rSocket, 1024);
    	fclose($rSocket);
      
    	$aResponse = explode("\r\n\r\n", $sResponse);
    	$sResponseHeader = $aResponse[0];
    	$sResponseContent = $aResponse[1];
    	return array('code' => 0, 'content' => explode("\n", $sResponseContent));
    }
 
    function actionPurchaseCredits($sTransMessage = '') {
 
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }
	    if($sTransMessage){
			$sMessageOutput = MsgBox($sTransMessage);  
		}
		$aPackages = $this->_oDb->getPackageList();
		$iCreditCost = getParam('modzzz_credit_credit_cost');
		$iCredits = (int)$this->_oDb->getMemberCredits($this->_iProfileId);
        $this->_oTemplate->pageStart();
 
		$aForm = array(
            'form_attrs' => array(
                'name' => 'payment_form',
                'method' => 'post', 
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_credits/',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_purchase',
                ),
            ),
            'inputs' => array( 
				'credits' => array(
                    'type' => 'custom',
                    'name' => 'credits',
                    'caption' => _t('_modzzz_credit_present_credits'),
                    'content' => $iCredits . ' ' . _t('_modzzz_credits'),
                ), 
                'cost' => array(
                    'type' => 'custom',
                    'name' => 'cost',
					'caption'  => _t('_modzzz_credit_paypal_cost_per_credit'),
                    'content' => $this->_oConfig->getCurrencySign() . $iCreditCost,
                ),
                'quantity' => array(
                    'caption'  => _t('_modzzz_credit_quantity'),
                    'type'   => 'text',
                    'name' => 'quantity',
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_modzzz_credit_paypal_quantity_error'),
                    ),
                ),
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_credit_purchase_credits'),
                    'name'  => 'submit_purchase',
                ),
            ),
        );
 
		if(getParam('modzzz_credit_packages_active')=='on'){ 
			unset($aForm['inputs']['cost']);
			$aForm['inputs']['quantity']['values'] = $aPackages;
			$aForm['inputs']['quantity']['type'] = 'select'; 
			$aForm['inputs']['quantity']['required'] = false; 
			$aForm['inputs']['quantity']['checker'] = null; 
		}
 
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid() && $oForm->getCleanValue('quantity')) { 
			$iCost=0;
			$iQuantity=0;
			if(getParam('modzzz_credit_packages_active')=='on'){ 
				list($iCost,$iQuantity) = explode('|',$oForm->getCleanValue('quantity'));
			}else{
				$iQuantity = $oForm->getCleanValue('quantity');
			}
 
			$this->initializeCheckout($iQuantity, $iCost);  
        } else {
             echo $sMessageOutput . $oForm->getCode();
        }
        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_credit_purchase_credits'));
  
    }
 
	function initializeCheckout( $iQuantity, $iCost=0 ) {
      
	    if(getParam('modzzz_credit_packages_active')=='on'){
 			$fTotalCost   =  $iCost; 
	    }else{
			$fUnitPrice   = (float)getParam('modzzz_credit_credit_cost');
			$fTotalCost   =  $iQuantity * $fUnitPrice;
	    }
    	$sActionURL = 'https://www.paypal.com/cgi-bin/webscr';
 	
		$aFormData = array(
			'cmd' => '_xclick',
			'amount' => sprintf( "%.2f", (float)$fTotalCost)
		);
    	
        $aFormData = array_merge($aFormData, array(
            'business' => getParam('modzzz_credit_paypal_email'),
            'item_name' => getParam('modzzz_credit_paypal_item_desc'),
            'item_number' => $iQuantity,
            'currency_code' => $this->_oConfig->getPurchaseCurrency(),
            'no_note' => '1',
            'no_shipping' => '1',  
			'return' => $this->_oConfig->getReturnUrl(),
			'notify_url' => $this->_oConfig->getPurchaseCallbackUrl() . $this->_iProfileId,  
			'rm' => '1'
        ));
  
    	Redirect($sActionURL, $aFormData, 'post', "Credits Purchase");
    	exit();
	}
 
    function actionDonateCredits($iRecipient = 0) {
 
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }
 
		$sDonateUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'donate_credits/'.$iRecipient;
		$iCredits = (int)$this->_oDb->getMemberCredits($this->_iProfileId);
		$aPackages = $this->_oDb->getPackageList();
        $this->_oTemplate->pageStart();
 
		$aForm = array(
            'form_attrs' => array(
                'name' => 'donate_form',
                'method' => 'post', 
                'action' => $sDonateUrl,
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_donate',
                ),
            ),
            'inputs' => array( 
				'recipient' => array(
                    'name' => 'recipient',
                    'type' => 'hidden',
                    'value' => $iRecipient,
                ),				
				'receiver' => array(
                    'type' => 'value',
                    'name' => 'receiver',
                    'caption' => _t('_modzzz_credit_recipient'),
                    'value' => getNickName($iRecipient) ,  
                ),  
 				'credits_display' => array(
                    'type' => 'value',
                    'name' => 'credits_display',
                    'caption' => _t('_modzzz_credit_present_credits'),
                    'value' => $iCredits . ' ' . _t('_modzzz_credits'), 
                ),  
                'quantity' => array(
                    'caption'  => _t('_modzzz_credit_donate_quantity'),
                    'type'   => 'text',
                    'name' => 'quantity',
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_modzzz_credit_donate_quantity_error'),
                    ),
                ), 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_credit_donate_credits'),
                    'name'  => 'submit_donate',
                ),
            ),
        );
 
 
        $oForm = new BxTemplFormView($aForm);
		$sErrorCode = $oForm->getCode();
        $oForm->initChecker();  
        if ($oForm->isSubmittedAndValid() && $oForm->getCleanValue('quantity')) { 
 
			$iRecipientId = $oForm->getCleanValue('recipient');
			$iTransferCredits = $oForm->getCleanValue('quantity');
 
			if($iTransferCredits > $iCredits){
				$sMessageOutput = MsgBox(_t("_modzzz_credit_donate_credits_greater_error", $iCredits, $iTransferCredits)); 
				$iProcessed = false;
			}else{  
				$iProcessed = true;
				$this->_oDb->transferMemberCredits($this->_iProfileId, $iRecipientId, $iTransferCredits);
			}
			if($iProcessed){ 
 
				echo MsgBox(_t("_modzzz_credit_donate_success", $iTransferCredits, getProfileLink($iRecipientId),getNickName($iRecipientId))); 
				//header('location:' . getProfileLink($iRecipientId));
			} else {
				echo $sMessageOutput . $sErrorCode;
			}
		}else{
			if($oForm->getCleanValue('recipient')){
				$iTransferCredits = (int)$oForm->getCleanValue('quantity');	
				$sMessageOutput = MsgBox(_t("_modzzz_credit_donate_credits_greater_error", $iCredits, $iTransferCredits)); 
				echo $sMessageOutput . $sErrorCode;
			}else{
				echo $oForm->getCode();
			}
		}
        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_credit_donate_credits')); 
    }
    /**
     * Added by afk Custom page make for sale
     */
    function actionPayement($iRecipient = 0) {
 		$product_id = trim(process_db_input($_GET['Q'], BX_TAGS_STRIP));
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }
 		$iRoom='Q'.$product_id.'W'.$iRecipient.'B'.$this->_iProfileId; // reconstruct room number
 		$last_price=$this->_oDb->getLastPrice($iRoom, $product_id);
		$sPaymentUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'payement/'.$iRecipient.'&Q='.$product_id;
		$iCredits = (int)$this->_oDb->getMemberCredits($this->_iProfileId);
		$aPackages = $this->_oDb->getPackageList();
        $this->_oTemplate->pageStart();
 		
 		$payement_name=_t('Credit_payement_for_product') . ' : ' . $this->_oDb->getNameProd($product_id);
		$aForm = array(
            'form_attrs' => array(
                'name' => 'creditpay_form',
                'method' => 'post', 
                'action' => $sPaymentUrl,
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_donate',
                ),
            ),
            'inputs' => array( 
				'recipient' => array(
                    'name' => 'recipient',
                    'type' => 'hidden',
                    'value' => $iRecipient,
                ),				
				'receiver' => array(
                    'type' => 'value',
                    'name' => 'receiver',
                    'caption' => _t('_modzzz_credit_recipient'),
                    'value' => getNickName($iRecipient) ,  
                ),  
 				'credits_display' => array(
                    'type' => 'value',
                    'name' => 'credits_display',
                    'caption' => _t('_modzzz_credit_present_credits'),
                    'value' => $iCredits . ' ' . _t('_modzzz_credits'), 
                ),   				
                'quantity_display' => array(
                    'type' => 'value',
                    'name' => 'credits_display',
                    'caption' => _t('credit_quantite_a_payer'),
                    'value' => $last_price .' ' . _t('_modzzz_credits'), 
                ),                
                'credits_sec' => array(
                    'type' => 'hidden',
                    'name' => 'credits_sec',
                    'value' => $last_price, 
                ),
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('confirm_credit_payement'),
                    'name'  => 'submit_donate',
                ),
            ),
        );
 
 
        $oForm = new BxTemplFormView($aForm);
		$sErrorCode = $oForm->getCode();
        $oForm->initChecker();  
        if ($oForm->isSubmittedAndValid() && $oForm->getCleanValue('credits_sec')) { 
			$iRecipientId = $oForm->getCleanValue('recipient');
			$iTransferCredits = $oForm->getCleanValue('credits_sec');
			if($last_price==$iTransferCredits){ //security for see if price not changed in validatation time
				if($iTransferCredits > $iCredits){
					$sMessageOutput = MsgBox(_t("_modzzz_credit_donate_credits_greater_error", $iCredits, $iTransferCredits)); 
					echo MsgBox('<a href="'.BX_DOL_URL_ROOT.'m/credit/purchase_credits" target="_blank">'._t('buy_credits').'</a>');
					$iProcessed = false;
				}else{  
					$iProcessed = true;
					$this->_oDb->transferMemberCredits($this->_iProfileId, $iRecipientId, $last_price);
				}
				if($iProcessed){ 
	 				//donate sucess register order
	 				//include module and run function
					$amod = array(
					"id"=> "13",
					"title"=> "Payment",
					"vendor"=> "Boonex",
					"version"=> "1.0.9",
					"update_url"=> "",
					"path"=> "boonex/payment/",
					"uri"=> "payment",
					"class_prefix"=> "BxPmt",
					"db_prefix"=> "bx_pmt_",
					"date"=> "1384266642" );
				    $pmtModule = BxDolRequest::_require($amod, "BxPmtModule");
				    $sname=getNickName($this->_iProfileId);
				    $order = $this->_oDb->getLastOrder() +1;
				    $item = array(
				    	0 => $this->_oDb->getFileFromPID($product_id), 
				    	);
				    $passData= array(
				    		"client" => $sname,
				    		"self_hack" => "1", //added to hack and add auto order
				    		"order" => $order,
				    		"module_id" => "36", // 36 = store, 16 = membership
				    		"item-price-$item[0]" => $iTransferCredits,
				    		"item-quantity-$item[0]" => '1',
				    		"items" => $item,
				    	);
				    $mixedResult = $pmtModule->_oOrders->addManualOrder($passData);
				    if(!is_array($mixedResult)){
				    	$pmtModule->_oCart->updateInfo((int)$mixedResult);  
				    }
				    echo "please vote profile";
				    $oMembVoting = new BxTemplVotingView('profile', $iRecipientId);
					$sVotingVal = $oMembVoting->getBigVoting();
					$iVotesCnt = $oMembVoting->getVoteCount();
					echo $sVotingVal;
					echo MsgBox(_t("_modzzz_credit_donate_success", $iTransferCredits, getProfileLink($iRecipientId),getNickName($iRecipientId))); 
					echo MsgBox('<a href="'.BX_DOL_URL_ROOT.'m/fchat/&room='.$iRoom.'">'._t('Return_to_negociation').'</a>');
					//header('location:' . getProfileLink($iRecipientId));
				} else {

					echo $sMessageOutput . $sErrorCode;
				}
			}else{
				echo MsgBox(_t("_modzzz_credit_payment_credits_error_price_changed", $iCredits, $iTransferCredits)); //security for see if price not changed in validatation time
			}
		}else{
			if($oForm->getCleanValue('recipient')){
				$iTransferCredits = (int)$oForm->getCleanValue('quantity');	
				$sMessageOutput = MsgBox(_t("_modzzz_credit_donate_credits_greater_error", $iCredits, $iTransferCredits));
				echo MsgBox('<a href="'.BX_DOL_URL_ROOT.'m/credit/purchase_credits" target="_blank">'._t('buy_credits').'</a>');
				echo $sMessageOutput . $sErrorCode;
			}else{
				echo $oForm->getCode();
			}
		}
        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->pageCode($payement_name); 
    }
 
    function actionPurchaseMembership($iLevel=0) {
		$sActionMessage = '';
        $iLevel = (int)$iLevel;
		
		$iPresentCredits = $this->_oDb->getMemberCredits($this->_iProfileId);
		if ($iLevel) {  
            $iMoneyPrice = $this->_oDb->getMembershipLevelPrice($iLevel);
			$iCostPerCredit = getParam("modzzz_credit_membership_cost"); 
			$iCreditPrice = ceil($iMoneyPrice * $iCostPerCredit);  
            if ($iPresentCredits>=$iCreditPrice) { 
     
				$this->assignCredits($this->_iProfileId, "membership_purchase", "subtract", "subtract", time(), $iCreditPrice); 
  
				$sTransId = 'credits_'.time();
                buyMembership($this->_iProfileId, $iLevel, $sTransId);
                
				$sActionMessage = MsgBox(_t('_modzzz_credits_membership_purchase_success'));
            }else{
				$sActionMessage = MsgBox(_t('_modzzz_credits_membership_purchase_fail'));
            }
        }else{
			$sActionMessage = MsgBox(_t('_modzzz_credits_membership_purchase_fail')); 
		}
        
		$this->_oTemplate->pageStart();
  
		echo $sActionMessage ;
 
        $this->_oTemplate->addCss ('membership.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_credit_purchase_membership'), false, false); 
    }
 
	function getMemberCredits($iProfileId){
		return $this->_oDb->getMemberCredits($iProfileId);
	}
 
    function serviceDeleteProfileData ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        if (!$iProfileId)
            return false;
 
       $this->_oDb->removeProfileEntries($iProfileId); 
    }
    function serviceResponseProfileDelete ($oAlert) {
        if (!($iProfileId = (int)$oAlert->iObject))
            return false;
        $this->serviceDeleteProfileData ($iProfileId);
        
        return true;
    }
 
    function serviceGetWallPost ($aEvent) {
         return $this->_serviceGetWallPost ($aEvent);
    }
 
    function _serviceGetWallPost ($aEvent) {
		
		$aContent = unserialize($aEvent['content']);
		$iCredits = $aContent['credits'];
		$sTextWallObject = _t('_modzzz_credit_wall_object', $iCredits);
        if (!($aOwnerProfile = getProfileInfo($aEvent['owner_id'])))
            return '';
        if (!($aObjectProfile = getProfileInfo($aEvent['object_id'])))
            return '';
	 
        $sCss = '';        
        if($aEvent['js_mode'])
            $sCss = $this->_oTemplate->addCss('wall_post.css', true);
        else 
            $this->_oTemplate->addCss('wall_post.css');
        $aVars = array(
                'cpt_object' => $sTextWallObject,
                'cpt_recipient_name' => $aObjectProfile['NickName'], 
                'cpt_recipient_url' => getProfileLink($aObjectProfile['ID']), 
                'cpt_sender_name' => $aOwnerProfile['NickName'],
                'cpt_sender_url' => getProfileLink($aOwnerProfile['ID']), 
			
        );
        return array(
            'title' => $aOwnerProfile['NickName'] . ' ' . $sTextWallObject . ' ' . $aObjectProfile['NickName'],
            'description' => _t('_modzzz_credit_donation'),
            'content' => $sCss . $this->_oTemplate->parseHtmlByName('wall_post', $aVars)
        );
    }
    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        return $this->_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_modzzz_credit_spy_post'
        ));
    }
    function _serviceGetSpyPost($sAction, $iRecipientId, $iSenderId, $aExtraParams, $aLangKeys)
    { 
	    if (!($iSenderId || $iRecipientId))
		    return;
 
        if (empty($aLangKeys[$sAction]))
            return array();
		  
		return array(
            'lang_key' => $aLangKeys[$sAction],
            'params' => array(
                'profile_link' => getProfileLink($iSenderId), 
                'profile_nick' => getNickName($iSenderId),
                'receiver_link' => getProfileLink($iRecipientId), 
                'receiver_nick' => getNickName($iRecipientId),
                'credits' => $aExtraParams['credits'],
               ),
            /*'recipient_id' => $iRecipientId,*/
            'spy_type' => 'content_activity',
        );
    }
	function isAllowedDonate($iSenderId, $iRecipientId){
		
        if ($this->isAdmin()) 
            return true;
  
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CREDIT_DONATE_CREDITS, $isPerformAction);
        $bAllowed = ($aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED);
		return($bAllowed && $this->_oDb->paidGender($iSenderId) && $this->_oDb->paidGender($iRecipientId) );
	}
    function _defineActions () {
        defineMembershipActions(array('credit donate credits'));
    }
}
?>