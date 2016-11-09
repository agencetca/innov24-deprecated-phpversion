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

class BxAlertsResponse extends BxDolAlertsResponse {
    function response ($oAlert) {
		global $gConf;


		if(getParam("modzzz_alerts_activated") != 'on')
			return;
 
		$oAlerts = BxDolModule::getInstance('BxAlertsModule');  
		
		if($oAlerts->_oDb->AllowAlerts($oAlert->iObject)) 		 
			return;

		switch ($oAlert->sAction) {
  
			case 'reply':
 				if($oAlert->sUnit == 'bx_forum'){  
					$oAlerts->alertAtOnceForum($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject);  
 				} 
				break;  
			case 'new_topic':
 
				if($oAlert->sUnit == 'bx_forum'){   
					$oAlerts->alertAtOnceForum($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject, $oAlert->aExtras);  
 				} 
				break;  
			case 'add': 
  
				if( (!$oAlert->aExtras['status']) || in_array($oAlert->aExtras['status'],array('approved','approval','active')) ){
					$oAlerts->alertAtOnce($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject);
 				} 
				break;  
			case 'create':  

			case 'change':
				if( (!$oAlert->aExtras['status']) || in_array($oAlert->aExtras['status'],array('approved','approval','active')) ){
		 
					$oAlerts->alertAtOnce($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject);
 				}
				break;  
			case 'commentPost':
/*
			    $iCommentId = $oAlert->aExtras['comment_id'];
				$iOwnerId = $oAlerts->_oDb->getObjectOwner($oAlert->sUnit, $oAlert->iObject);
 		   
				if($iOwnerId != $oAlert->iSender) { 
					$oAlerts->alertAtOnce($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject);  
 				}
*/
 				break;  
		} 
	
	} 

}

?>