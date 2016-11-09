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
 * Alerts module View
 */
class BxAlertsTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    var $aUnit = array(); 
 
	/**
	 * Constructor
	 */
	function BxAlertsTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);

		$this->aUnit = $oDb->getUnits();
    }

    function unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxAlertsModule');
 
		$sDateTime = defineTimeInterval($aData['created']);
		
		$aNotifyOptions = array(
			'now' => _t('_modzzz_alerts_notify_now'),
			'daily' => _t('_modzzz_alerts_notify_daily'),
			'weekly' => _t('_modzzz_alerts_notify_weekly')  
		);

		$sNotify = $aNotifyOptions[$aData['notify']];
 
        $aVars = array (
            'id' => $aData['id'], 
            'alerts_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'alerts_title' => $aData['phrase'],
            'alerts_units' => $this->getUnits($aData['unit']),
            'alerts_notify' => $sNotify,
            'post_date' => strtolower($sDateTime)   
         );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

	function getUnits($sUnits){
		$aUnits = explode(';', $sUnits);
		
		$aFormattedUnits =array();
		foreach($aUnits as $iKey=>$sEachUnit){  
 			$aFormattedUnits[] = $this->aUnit[$sEachUnit];
		} 
 
		$sFormattedUnits = implode(', ', $aFormattedUnits);

		return $sFormattedUnits;
	}

    // ======================= ppage compose block functions 

    function blockDesc (&$aDataEntry) {
        $aVars = array (
            'description' => $aDataEntry['desc'],
        );
        return $this->parseHtmlByName('block_description', $aVars);
    }

    function blockFields (&$aDataEntry) { 
    }
  

}

?>