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
 
class BxCreditPageTransactions extends BxDolTwigPageMain {
    function BxCreditPageTransactions(&$oMain) {
		$this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
         $this->_oMain = $oMain;
		parent::BxDolTwigPageMain('modzzz_credit_transactions', $oMain);
	}
   
    function getBlockCode_Transactions() { 
 
		$sFilter = process_db_input($_GET['filter']);
		 
		$iTotalBought = $this->oDb->getTotalCreditsBought($this->_oMain->_iProfileId, $sFilter); 
	    $aResult = $this->oDb->getCreditTransactions($this->_oMain->_iProfileId, $sFilter);
		$aTrans = $aResult['transactions'];
		$sPaginate = $aResult['paginate'];
		switch($sFilter){
			case "today":
				$sPeriodC = _t('_modzzz_credit_today'); 
			break;
			case "week":
				$sPeriodC = _t('_modzzz_credit_week');
			break;
			case "month":
				$sPeriodC = _t('_modzzz_credit_month');
			break; 
			default:	
				$sPeriodC = _t('_modzzz_credit_all'); 
		}
		if(count($aTrans)) {
 			$aParent = array();
			foreach($aTrans as $aEachTrans)
			{ 
 				$aParent[] = array ( 
 						'transaction' => $aEachTrans['trans_id'], 
 						'date' => date('M d, Y g:i A',$aEachTrans['created']) .' ('. defineTimeInterval($aEachTrans['created']).')', 
						'credits' => number_format($aEachTrans['credits']),  
					); 
			}
			$aVars = array(
				'bx_repeat:items' => $aParent,
				'total_bought' => $iTotalBought,
				'period' => $sPeriodC, 
			);
	 
			$sCode = $this->oTemplate->parseHtmlByName("credits_transactions", $aVars); 
		}else{
			$sCode = MsgBox(_t("_modzzz_credit_no_transactions")); 
		}
 
		return array(
			$sCode,
			array(
 				_t('_modzzz_credit_all') => array(
					'href' => $this->_oConfig->getBaseUri() . 'transactions', 
					'active' => ($_REQUEST['filter']=='') ? true : false,
				),
				_t('_modzzz_credit_today') => array(
					'href' => $this->_oConfig->getBaseUri() . 'transactions?filter=today', 
					'active' => ($_REQUEST['filter']=='today') ? true : false,
				),
				_t('_modzzz_credit_week') => array(
					'href' => $this->_oConfig->getBaseUri() . 'transactions?filter=week', 
					'active' => ($_REQUEST['filter']=='week') ? true : false, 
				),
				_t('_modzzz_credit_month') => array(
					'href' => $this->_oConfig->getBaseUri() . 'transactions?filter=month', 
					'active' => ($_REQUEST['filter']=='month') ? true : false, 
				)  
			),
			$sPaginate
		);  
    }
 
  
}
?>
