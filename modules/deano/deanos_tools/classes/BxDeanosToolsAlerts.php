<?php
/***************************************************************************
* Date				: Sun August 1, 2010
* Copywrite			: (c) 2009, 2010 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Deanos Tools
* Product Version	: 1.8.4
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/
    class BxDeanosToolsAlerts extends BxDolAlertsResponse 
    {
        var $oModule;
        var $aModule;
        var $iFacebookUid;

        /**
         * Class constructor;
         */
        function BxDeanosToolsAlerts() {
            $this -> oModule = BxDolModule::getInstance('BxDeanosToolsModule');            
        }

		function getRealIpAddr() {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
		    }
			return $ip;
		}

		function response(&$o) 
    	{
            if ( $o -> sUnit == 'profile' ) {
                switch ( $o -> sAction ) {
                    case 'login' :
						// log current members ip address.
						$memID = (int)$_COOKIE['memberID'];
						if ($memID > 0) {
							$sIP = $this->getRealIpAddr();
							// if IP address could not be obtained, then don't log entry.
							if ($sIP != '') {
								$this -> oModule -> _oDb -> saveIP($sIP, $memID);
							}
						}
					break;


                    default :
                }
            }
            if ( $o -> sUnit == 'system' ) {
                switch ( $o -> sAction ) {
                    case 'begin' :
						// log guests ip address.
						// if this is a real logged in member, then we skip this alert.
						$memID = (int)$_COOKIE['memberID'];
						if ($memID == 0) {
							$sIP = $this->getRealIpAddr();
							// if IP address could not be obtained, then don't log entry.
							if ($sIP != '') {
								$this -> oModule -> _oDb -> saveIP($sIP, $memID);
							}
						}
					break;


                    default :
                }
            }
        }
    }
?>