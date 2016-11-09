<?
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

bx_import('BxDolAlerts');
require_once('AqbPointsAssign.php');

class AqbPointsAlertsResponse extends BxDolAlertsResponse {
	var $_oAssignPoints;
	
	function AqbPointsAlertsResponse() {
	    parent::BxDolAlertsResponse();
		$this -> _oAssignPoints = new AqbPointsAssign();
	}

    function response($oAlert) {
    	$sMethodName = '_process' . ucfirst($oAlert->sUnit) . ucfirst($oAlert->sAction);
		
		if ($sMethodName == '_processProfileDelete' || $sMethodName == '_processAqb_pointsAssign') $this -> $sMethodName($oAlert);
		else if (stristr($_SERVER['PHP_SELF'], $GLOBALS['admin_dir']) !== FALSE || isset($_POST['save_membership'])) return false;
		else $this -> _oAssignPoints -> assignPoints($oAlert);
		
    }
	
	function _processProfileDelete(&$oAlert){
		$this -> _oAssignPoints -> deleteProfileInfo($oAlert -> iObject);
	}
	
	function _processAqb_pointsAssign(&$oAlert){
		$this -> _oAssignPoints -> _oMain -> _oDb -> applyLevelsOptions($oAlert -> iObject);
	}
}
?>