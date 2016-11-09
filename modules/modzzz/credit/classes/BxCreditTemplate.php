<?
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

bx_import('BxDolTwigTemplate');
bx_import('BxDolCategories');

/*
 * Credit module View
 */
class BxCreditTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    
	/**
	 * Constructor
	 */
	function BxCreditTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }

    function unit ($aData, $sTemplateName, &$oVotingView) {
 
    }

    // ======================= ppage compose block functions 

    function blockDesc (&$aDataEntry) {
 
    }

    function blockFields (&$aDataEntry) { 
    }

	function displayCurrentLevel($aUserLevel) {
	    
		$iCredits = $this->_oDb->getMemberCredits($_COOKIE['memberID']);
		
		$aLevelInfo = $this->_oDb->getMembershipsBy(array('type' => 'level_id', 'id' => $aUserLevel['ID']));
	    if(isset($aUserLevel['DateExpires']))
            $sTxtExpiresIn = _t('_membership_txt_expires_in', floor(($aUserLevel['DateExpires'] - time())/86400));
        else
            $sTxtExpiresIn = _t('_membership_txt_expires_never');
	    
        $this->addCss('levels.css');
	    $aVars = array(
            'id' => $aLevelInfo['mem_id'],
            'title' => $aLevelInfo['mem_name'],
            'icon' =>  $this->_oConfig->getIconsUrl() . $aLevelInfo['mem_icon'],
            'description' => str_replace("\$", "&#36;", $aLevelInfo['mem_description']),
            'expires' => $sTxtExpiresIn,
			'credits' => number_format($iCredits),
		
        );
 
	    return $this->parseHtmlByName('current', $aVars);  
	}
 
	function displayAvailableLevels($aValues) {

		$iCostPerCredit = getParam("modzzz_credit_membership_cost");
		$iCredits = $this->_oDb->getMemberCredits($_COOKIE['memberID']);
		$sCreditIcon = $this->getIconUrl('credit.png');
		$sCreditPayUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .'purchase_membership/';
  		 
	    $sCurrencyCode = strtolower($this->_oConfig->getCurrencyCode());
	    $sCurrencySign = $this->_oConfig->getCurrencySign();
	 	      
	    $aMemberships = array();
	    foreach($aValues as $aValue) { 
   
			if(!$aValue['price_amount'])
				continue;
			
			$iLevelCredits = ceil($aValue['price_amount'] / $iCostPerCredit);

            $aMemberships[] = array(
                'url_root' => BX_DOL_URL_ROOT,
                'id' => $aValue['mem_id'],
                'title' => $aValue['mem_name'],
                'icon' =>  $this->_oConfig->getIconsUrl() . $aValue['mem_icon'],
                'description' => str_replace("\$", "&#36;", $aValue['mem_description']),
                'days' => $aValue['price_days'] > 0 ?  $aValue['price_days'] . ' ' . _t('_membership_txt_days') : _t('_membership_txt_expires_never') ,
                'price' => $aValue['price_amount'],
                'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
                'credits' => number_format($iLevelCredits),
				'bx_if:creditsbutton' => array( 
					'condition' => ($iCredits>$iLevelCredits),
					'content' => array(
						'credit_icon' => $sCreditIcon,
						'credits_pay_url' => $sCreditPayUrl,
						'id' => $aValue['price_id'], 

					), 
				), 	 

	        );
	    }

		if(!count($aMemberships)){
			return MsgBox(_t('_modzzz_credit_no_credit_memberships'));
		}

		$aVars = array('bx_repeat:levels' => $aMemberships);

	    $this->addCss('memblevels.css');
	    return $this->parseHtmlByName('available_memberships', $aVars);
	}

	function showSimpleMessengerBuy($sMessage){

		$aVariables = array(
			'message' => $sMessage
		);
 
		$this  -> addCss('simple_messenger.css');

		return $this->parseHtmlByName("simple_messenger_buy", $aVariables); 
	}

	function showSimpleMessengerTalk($iProfileId, $iCredits){

		$aVariables = array(
			'message' => _t('_modzzz_credit_smessenger_credits_to_chat', $iCredits),
			'accept_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .'home?ajax=messenger&accept=yes&id='.$iProfileId, 
			'reject_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .'home?ajax=messenger&accept=no&id='.$iProfileId 
		);
 		
		$this  -> addCss('simple_messenger.css');

		return $this->parseHtmlByName("simple_messenger_talk", $aVariables); 
	}


	function showMessage($sMessage){

		$aVariables = array(
			'content' => $sMessage
		);
 
 		$this  -> addCss('common.css');
		$this  -> addCss('general.css');

		return $this->parseHtmlByName("default_padding", $aVariables); 
	}

}

?>