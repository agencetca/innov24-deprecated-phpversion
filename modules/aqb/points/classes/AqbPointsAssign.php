<?php
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

class AqbPointsAssign{
    var $_oMain, $iProfileID = 0;	
  
	function AqbPointsAssign(){
		$this -> _oMain = BxDolModule::getInstance("AqbPointsModule");
		if ($this -> _oMain -> isLogged()) $this -> iProfileID = (int)$this -> _oMain -> getUserId();
	}
	
	function assignPoints(&$oAlertInfo){
		$aActionInfo = $this -> _oMain -> _oDb -> getActionInfo($oAlertInfo -> sUnit, $oAlertInfo -> sAction);
	
		foreach($aActionInfo as $iKey => $aInfo){
			$iMemberID = $this -> getMemberID($aInfo, $oAlertInfo);
			if ($iMemberID === false) continue;
			$this -> _oMain -> _oDb -> assignPoints($aInfo, $iMemberID) ;
		}	
	}
	
	function deleteProfileInfo($iProfileID){
		if ((int)$iProfileID) $this -> _oMain -> _oDb -> deleteProfileInfo($iProfileID);
	}
	
	function getMemberID($aAlertInfo, &$oAlertInfo){
		switch($aAlertInfo['param']){
			case 'first' : return (int)$oAlertInfo -> iObject;
			case 'second': return (int)$oAlertInfo -> iSender;
			case 'cmts'  : 
							$iAuthor = (int)$this -> _oMain -> _oDb -> getCmtsAuthor($aAlertInfo['alerts_unit'], $oAlertInfo -> iObject);
							if ((int)$oAlertInfo -> iSender && $iAuthor && (int)$oAlertInfo -> iSender != $iAuthor) return $iAuthor;
							break;
			case 'topic_owner': return (int)$this -> _oMain -> _oDb -> getTopicOwner($oAlertInfo -> iObject);
			case 'cmts_rate': 
							if ((int)$oAlertInfo -> aExtras['rate'] > 0){
								$iAuthor = (int)$this -> _oMain -> _oDb -> getCmtsRateAuthor($aAlertInfo['alerts_unit'], (int)$oAlertInfo -> aExtras['comment_id']); 
								if ((int)$oAlertInfo -> iSender && $iAuthor && (int)$oAlertInfo -> iSender != $iAuthor) return $iAuthor;
							}
							break;
			case 'cmts_rate_negative' : 
							 if ((int)$oAlertInfo -> aExtras['rate'] < 0){
								$iAuthor = (int)$this -> _oMain -> _oDb -> getCmtsRateAuthor($aAlertInfo['alerts_unit'], (int)$oAlertInfo -> aExtras['comment_id']); 
								if ((int)$oAlertInfo -> iSender && $iAuthor && (int)$oAlertInfo -> iSender != $iAuthor) return $iAuthor;
							 }
							 break;
			case 'vote' :    
							$iAuthor = (int)$this -> _oMain -> _oDb -> getVoteAuthor($aAlertInfo['alerts_unit'], $oAlertInfo -> iObject);
							if ((int)$oAlertInfo -> iSender && $iAuthor && (int)$oAlertInfo -> iSender != $iAuthor) return $iAuthor;
							break;
			case 'delete_media' : 
					  if ((int)$oAlertInfo -> aExtras['medProfId']) return(int)$oAlertInfo -> aExtras['medProfId'];
					  elseif ($this -> _oMain -> isAdmin()) return false;
			default : return (int)$this -> iProfileID;				
		}
		
		return false;	
	}
	
}
?>