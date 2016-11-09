<?

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

bx_import('BxDolModuleTemplate');

class BxMbpTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function BxMbpTemplate(&$oConfig, &$oDb) {
	    parent::BxDolModuleTemplate($oConfig, $oDb);
	}
	function displayCurrentLevel($aUserLevel) {
	    $aLevelInfo = $this->_oDb->getMembershipsBy(array('type' => 'level_id', 'id' => $aUserLevel['ID']));
	    if(isset($aUserLevel['DateExpires']))
            $sTxtExpiresIn = _t('_membership_txt_expires_in', floor(($aUserLevel['DateExpires'] - time())/86400));
        else
            $sTxtExpiresIn = _t('_membership_txt_expires_never');
	    
        $this->addCss('levels.css');
	//[begin] credits mod (modzzz.com) 


	if(getParam("modzzz_credit_activated") && getParam("modzzz_credit_buy_membership")) {  


		$oCredit = BxDolModule::getInstance('BxCreditModule');  


		$iCredits = $oCredit->_oDb->getMemberCredits($_COOKIE['memberID']);


		


		$bCreditsEnabled = true;


	}else{


		$bCreditsEnabled = false;


	}

	//[end] credits mod (modzzz.com)
	    return $this->parseHtmlByName('current.html', array(
	//[begin] credits mod (modzzz.com)  


	'bx_if:credits' => array( 


		'condition' =>  $bCreditsEnabled,


		'content' => array(


			'credits' => number_format($iCredits),


		), 


	),  

	//[end] credits mod (modzzz.com)
            'id' => $aLevelInfo['mem_id'],
            'title' => $aLevelInfo['mem_name'],
            'icon' =>  $this->_oConfig->getIconsUrl() . $aLevelInfo['mem_icon'],
            'description' => str_replace("\$", "&#36;", $aLevelInfo['mem_description']),
            'expires' => $sTxtExpiresIn
            )
        );
	}
	function displayAvailableLevels($aValues) {
	//[begin] credits mod (modzzz.com)  


	if(getParam("modzzz_credit_activated") && getParam("modzzz_credit_buy_membership")) {  


		$oCredit = BxDolModule::getInstance('BxCreditModule');  


		$iCredits = $oCredit->_oDb->getMemberCredits($_COOKIE['memberID']);


		$sCreditIcon = $oCredit->_oTemplate->getIconUrl('credit.png');


		$sCreditPayUrl = BX_DOL_URL_ROOT . $oCredit->_oConfig->getBaseUri() .'purchase_membership/';





		$iCostPerCredit = getParam("modzzz_credit_membership_cost");





		$bCreditsEnabled = true;


	}else{


		$iCostPerCredit = 1;


		$bCreditsEnabled = false;


	}		


	//[begin] credits mod (modzzz.com)
	    $sCurrencyCode = strtolower($this->_oConfig->getCurrencyCode());
	    $sCurrencySign = $this->_oConfig->getCurrencySign();
	    
	    $aMemberships = array();
	    foreach($aValues as $aValue) {
	//[begin] credits mod (modzzz.com)


	if($bCreditsEnabled){


		$iCreditLevelPrice = floor($aValue['price_amount'] * $iCostPerCredit);


	}


	//[END] credits mod (modzzz.com)
            $aMemberships[] = array(
	//[begin] credits mod (modzzz.com)  


	'bx_if:credits' => array( 


		'condition' =>  $bCreditsEnabled,


		'content' => array(


			'credits' => number_format($iCreditLevelPrice),  


		), 


	), 


	'bx_if:creditsbutton' => array( 


		'condition' =>  ($bCreditsEnabled && ($iCredits>$iCreditLevelPrice)),


		'content' => array(


			'credit_icon' => $sCreditIcon,


			'credits_pay_url' => $sCreditPayUrl,


			'id' => $aValue['price_id'], 


		), 


	),


	//[END] credits mod (modzzz.com)
                'url_root' => BX_DOL_URL_ROOT,
                'id' => $aValue['mem_id'],
                'title' => $aValue['mem_name'],
                'icon' =>  $this->_oConfig->getIconsUrl() . $aValue['mem_icon'],
                'description' => str_replace("\$", "&#36;", $aValue['mem_description']),
                'days' => $aValue['price_days'] > 0 ?  $aValue['price_days'] . ' ' . _t('_membership_txt_days') : _t('_membership_txt_expires_never') ,
                'price' => $aValue['price_amount'],
                'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
                'add_to_cart' => BxDolService::call('payment', 'get_add_to_cart_link', array(
                    0, 
                    $this->_oConfig->getId(), 
                    $aValue['price_id'],
                    1,
                    1
                ))
	        );
	    }

	    $this->addCss('levels.css');
	    return $this->parseHtmlByName('available.html', array('bx_repeat:levels' => $aMemberships));
	}
}
?>
