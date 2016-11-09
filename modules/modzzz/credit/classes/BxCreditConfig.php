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
 
bx_import('BxDolConfig');
class BxCreditConfig extends BxDolConfig {
	var $sPurchaseBaseUrl;
	var $sPurchaseCurrency;
	var $sPurchaseCallbackUrl;
	var $sReturnUrl;
	var $_sCreditUnitCost; 
	var $_sIconsFolder;
 	var $_sCurrencyCode;
	var $_sCurrencySign;
	/**
	 * Constructor
	 */
	function BxCreditConfig($aModule) {
	    parent::BxDolConfig($aModule);
	     $this->_oDb = null;
 
         $this->sPurchaseBaseUrl = 'https://www.paypal.com/cgi-bin/webscr';
         $this->sPurchaseCurrency = getParam('modzzz_credit_currency_code'); 
         $this->sPurchaseCallbackUrl = BX_DOL_URL_ROOT . $this -> getBaseUri() . 'paypal_process/';  
         $this->sReturnUrl = BX_DOL_URL_ROOT . $this -> getBaseUri() . 'transactions';   
	 }
	function init(&$oDb) {
	    $this->_oDb = &$oDb;
		$this -> _sIconsFolder = 'media/images/membership/';
 	    $this -> _sCreditUnitCost = (float)getParam('modzzz_credit_credit_cost');	 
	    $this -> _sCurrencySign = getParam('modzzz_credit_currency_sign');
	    $this -> _sCurrencyCode = getParam('modzzz_credit_currency_code'); 
	}
	function getCurrencySign() {
	    return $this->_sCurrencySign;
	}
	function getCurrencyCode() {
	    return $this->_sCurrencyCode;
	}
	 
	function getPurchaseCurrency() {
	    return $this->sPurchaseCurrency;
	}	 
 
	function getReturnUrl() {
	    return $this->sReturnUrl;
	}	
	function getPurchaseCallbackUrl() {
	    return $this->sPurchaseCallbackUrl;
	}	
	function getCreditUnitCost(){
	    return $this->_sCreditUnitCost; 
	}
 
	function getIconsUrl() {
	    return BX_DOL_URL_ROOT . $this->_sIconsFolder; 
	}
	function getIconsPath() {
	    return BX_DIRECTORY_PATH_ROOT . $this->_sIconsFolder;  
	}	 
}
?>