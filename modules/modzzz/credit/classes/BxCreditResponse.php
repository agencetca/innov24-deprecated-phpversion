<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -----------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2006 BoonEx Group
*     website              : http://www.boonex.com/
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 
* http://creativecommons.org/licenses/by/3.0/
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the Creative Commons Attribution 3.0 License for more details. 
* You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

require_once( BX_DIRECTORY_PATH_INC . "design.inc.php");
bx_import('BxDolAlerts');
bx_import('BxDolDb');
bx_import('BxDolModule');

class BxCreditResponse extends BxDolAlertsResponse {
    function response ($oAlert) {
		
		global $_page;
		global $_page_cont;

		$iViewerId = getLoggedId();

		if(isAdmin()){
			return;
		}

		if(getParam('modzzz_credit_activated')!='on'){
			return;
		}
 
		$oCredit = BxDolModule::getInstance('BxCreditModule');  
 
		if ($oAlert->sUnit == 'profile'){ 
			$iOwnerId = $oAlert->iObject;
		}else{
			$iOwnerId = $oAlert->iSender;
		}
 
		switch ($oAlert->sAction) 
		{ 
			case 'view':  
 
				if($oAlert->sUnit == 'mail'){
					$sCode = $oCredit->_oDb->checkMailAccess($oAlert->iSender, $oAlert->iObject);
				 
					if($sCode){  
						$_page['header'] = _t("_modzzz_credit_no_mail_access");
						$_page['header_text'] = _t("_modzzz_credit_no_mail_access");
						$_page['name_index'] = 0;
						$_page_cont[0]['page_main_code'] = $sCode;
						PageCode();
						exit;   
					}				
				}else{ 
					$sCode = $oCredit->_oDb->checkViewAccess($oAlert->iObject, $iViewerId, $iOwnerId, $oAlert->sUnit);

					if($sCode){  
						$_page['header'] = _t("_modzzz_credit_no_view_access");
						$_page['header_text'] = _t("_modzzz_credit_no_view_access");
						$_page['name_index'] = 0;
						$_page_cont[0]['page_main_code'] = $sCode;
						PageCode();
						exit;   
					}
				}
				break;  
 			case 'pre_create':  
					$sCode = $oCredit->_oDb->checkAddAccess($iOwnerId, $oAlert->sUnit, 'create');

					if($sCode){  
						$_page['header'] = _t("_modzzz_credit_no_add_access");
						$_page['header_text'] = _t("_modzzz_credit_no_add_access");
						$_page['name_index'] = 0;
						$_page_cont[0]['page_main_code'] = $sCode;
						PageCode();
						exit;   
					}
				break;
 			case 'pre_add':  
				 
					$sCode = $oCredit->_oDb->checkAddAccess($iOwnerId, $oAlert->sUnit, 'add');

					if($sCode){  
						$_page['header'] = _t("_modzzz_credit_no_add_access");
						$_page['header_text'] = _t("_modzzz_credit_no_add_access");
						$_page['name_index'] = 0;
						$_page_cont[0]['page_main_code'] = $sCode;
						PageCode();
						exit;   
					}
				break;  

 			case 'send_mail_internal':  
 
				$oCredit->_oDb->processMailSending($oAlert->iObject, $oAlert->iSender);
 
				break;  
 
			case 'add':
   			case 'create':
	 
				$bValid = $oCredit->_oDb->paidGender($iOwnerId) && $oCredit->_oDb->validAction($oAlert->sUnit, 'add');
		 
				if($bValid){  
					$iActionValue = (int)$oCredit->_oDb->getActionValue($oAlert->sUnit, $oAlert->sAction);
					$iMemberCredits = (int)$oCredit->_oDb->getMemberCredits($iOwnerId);
			 
					if($iActionValue > $iMemberCredits){ 
						$sMediaType = $oCredit->_oDb->getMediaType($oAlert->sUnit);
						if($sMediaType){
							BxDolService::call($sMediaType, 'remove_object', array($oAlert->iObject));
						}
						//$this->_oDb->deleteMedia ($iEntryId, array($oAlert->iObject), 'videos');
					}else{
						$oCredit->assignCredits($iOwnerId, $oAlert->sUnit, $oAlert->sAction, 'subtract');   
					}  
				} 
				break;  
		}
  
	
	}



}

?>