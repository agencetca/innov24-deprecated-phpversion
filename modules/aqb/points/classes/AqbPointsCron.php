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

bx_import('BxDolCron');
require_once('AqbPointsModule.php'); 

class AqbPointsCron extends BxDolCron {
    var $_oMain; 
	function processing() {
		$a = array();
		$this -> _oMain = BxDolModule::getInstance('AqbPointsModule');
		$this -> sendReport();
    }
	
	function sendReport(){
		set_time_limit( 36000 );
        ignore_user_abort();

        $sResult = "";        
        $iTotalPointsDuring24 = $this -> _oMain -> _oDb -> getTotalPoints24();
        $iTotalPoints = $this -> _oMain -> _oDb -> getTotalPoints();
		
        $sResult .= "- Start Global Points System Report - <br/>";
        $sResult .= "Total points earned : " . $iTotalPoints . "<br/>";
		$sResult .= "Total points earned during 24 hours : " . $iTotalPointsDuring24 . "<br/>";
		$sResult .= "<br/>";    
		$sResult .= "<b>10 leaders during 24 hours:</b> <br/><br/>";
      
		$aProfiles = $this -> _oMain -> _oDb -> getMembersForPointsLeadersBlock(0,'upper',10);
		
        foreach($aProfiles['data'] as $aProfile) {
     	  $sResult .= '<u>'.$aProfile['NickName'] . "</u> - points(<b>" . $aProfile['points'] . "</b>) <br/>";
       }            
         
	    sendMail($this -> _oMain -> _oDb -> getParam('site_email'), $this -> _oMain -> _oDb -> getParam('site_title') . ": Periodic Report", $sResult);

		return true;
	}
}
?>