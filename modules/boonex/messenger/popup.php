<?
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

require_once( '../../../inc/header.inc.php');
require_once( BX_DIRECTORY_PATH_INC . 'admin.inc.php');
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php');
require_once( BX_DIRECTORY_PATH_MODULES . 'boonex/messenger/classes/BxMsgModule.php');

$iSndId = isset($_COOKIE['memberID']) ? (int)$_COOKIE['memberID'] : 0;
$sSndPassword = isset($_COOKIE['memberPassword']) ? $_COOKIE['memberPassword'] : '';
$iRspId = isset($_GET['rspId']) ? (int)$_GET['rspId'] : 0;

$oModuleDb = new BxDolModuleDb();
$aModule = $oModuleDb->getModuleByUri('messenger');

	//[begin] credits mod (modzzz.com)
	if(getParam("modzzz_credit_activated")){ 
		require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
		require_once( BX_DIRECTORY_PATH_INC . 'params.inc.php' ); 
		$oCredit = BxDolModule::getInstance('BxCreditModule');   
		$sMessage = $oCredit->_oDb->hasMessengerAccess($iSndId,$iRspId);
	} 
	if($sMessage){
		echo $sMessage;exit;
	}
	//[end] credits mod (modzzz.com)
$oMessenger = new BxMsgModule($aModule);
echo $oMessenger->getMessenger($iSndId, $sSndPassword, $iRspId);
?>