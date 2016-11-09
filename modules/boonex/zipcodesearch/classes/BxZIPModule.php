<?php
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

bx_import('BxDolModule');

class BxZIPModule extends BxDolModule {

    var $_server = 'http://ws.geonames.org'; // geonames server to use for geocoding
	var $_style = 'SHORT'; // SHORT,MEDIUM,LONG,FULL
	var $_maxRows = 500; // max number of zip codes to return	
    var $_error = '';

    function BxZIPModule(&$aModule) {
        parent::BxDolModule($aModule);
    }

    function serviceGetSqlParts ($mixedCountry, $sZip, $sMetric, $iDistance, &$sJoin, &$aWhere) {

        if (!getParam('bx_zip_enabled'))
            return false;

        $sWhere = '';
        
        // check input fields
        $sZip = process_db_input( strtoupper( trim($sZip) ), BX_TAGS_STRIP);
        $sMetric = process_db_input( $sMetric, BX_TAGS_STRIP);
        $iDistance = (int)$iDistance;
        if (is_array($mixedCountry))
        {
            $sCountry = process_db_input( $mixedCountry[0], BX_TAGS_STRIP); // no reason to search by zipcode in different countries
        }
        else
        {
            $sCountry = process_db_input( $mixedCountry, BX_TAGS_STRIP);
        }

        // check GB and CA zipcodes
		if ('CA' == $sCountry)
		{
	        $sZip = substr($sZip, 0, 3);
        }
        elseif ('GB' == $sCountry)
        {            
	        $sZip = strlen($sZip) > 4 ? trim(substr($sZip, 0, -3)) : trim($sZip);
    	}        

        // search using google - Worls Maps module needs to be installed and configured
        if ('Google' == getParam('bx_zip_mode'))
        {
    		if ($sMetric == 'km')
			    $iDistance *= 0.62;             

            do
            {
                $sAddress = "$sZip " . str_replace('_', '', $GLOBALS['aPreValues']['Country'][$sCountry]['LKey']);
                if (200 != $this->_geocodeGoogle ($sAddress, $fLat, $fLng, $sCountry)) 
                {
                    $this->_setError (_t('_No zip codes found'));
                    break;
                }
                
                if (!class_exists('RadiusAssistant'))
                    require_once( BX_DIRECTORY_PATH_INC . 'RadiusAssistant.inc.php' );
                
			    $zcdRadius = new RadiusAssistant( $fLat, $fLng, $iDistance );
			    $minLat = $zcdRadius->MinLatitude();
			    $maxLat = $zcdRadius->MaxLatitude();
			    $minLong = $zcdRadius->MinLongitude();
			    $maxLong = $zcdRadius->MaxLongitude();

                $sWhere = " `Profiles`.`Country` = '$sCountry' AND `bx_map_profiles`.`country` = '$sCountry' AND `bx_map_profiles`.`failed` = 0 AND `bx_map_profiles`.`lat` >= {$minLat} AND `bx_map_profiles`.`lat` <= {$maxLat} AND `bx_map_profiles`.`lng` >= {$minLong} AND `bx_map_profiles`.`lng` <= {$maxLong} ";
                $sJoin .= " INNER JOIN `bx_map_profiles` ON (`bx_map_profiles`.`id` = `Profiles`.`ID`) ";

            } 
            while (0);
        }
        // search using geonames
        else
        {
    		if ($sMetric != 'km')
			    $iDistance *= 1.61; 

            if ($iDistance > 30 ) // free service don't allow to search more than 30 km
                $iDistance = 30;
            
            do
            {
                $s = $this->_readFromUrl($this->_server . "/findNearbyPostalCodes?postalcode={$sZip}&country={$sCountry}&radius={$iDistance}&style={$this->_style}&maxRows={$this->_maxRows}", 'r');
                if (!$s)
                {
                    $this->_setError (_t('_Server is too busy'));
                    break;
                }

                if (!$this->_getStatusMessage  ($s))
                    break;

                if (false === ($aZips = $this->_getZipCodesArray ($s)))
                    break;

                foreach ($aZips as $k => $s)
                    $aZips[$k] = strtoupper($s);

				if ('GB' == $sCountry)
                {
                    $sWhere = " `Country` = '$sCountry' AND (";
                    foreach ($aZips as $s)
                        $sWhere .= " UPPER(IF(LENGTH(`Profiles`.`zip`) > 4,TRIM(SUBSTRING(`Profiles`.`zip`,1,LENGTH(`Profiles`.`zip`)-3)),`Profiles`.`zip`)) = '$s' OR ";
                    $sWhere = substr($sWhere, 0, -4);
                    $sWhere .= ")";
                }
                elseif ('CA' == $sCountry)
					$sWhere = " `Country` = '$sCountry' AND (UPPER(SUBSTRING(`Profiles`.`zip`,1,3)) = '" . join ("' OR UPPER(SUBSTRING(`Profiles`.`zip`,1,3)) = '", $aZips) . "') ";
				else                    
					$sWhere = " `Country` = '$sCountry' AND (UPPER(`Profiles`.`zip`) = '" . join ("' OR UPPER(`Profiles`.`zip`) = '", $aZips) . "') ";
            }
            while (0);

        }
                
        if ($s = $this->getError())
        {
            $aWhere[] = ' 0 ';
        }
        else
        {
            foreach ($aWhere as $k => $v)
                if (preg_match('/`zip`/', $v) || preg_match('/`Country`/', $v))
                    unset ($aWhere[$k]);
            $aWhere[] = $sWhere ? $sWhere : ' 0 ';
        }

        return true;
    }

    function actionAdministration () {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

	    $iId = $this->_oDb->getSettingsCategory();
	    if(empty($iId)) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_bx_zip_administration'));
            return;
        }

        bx_import('BxDolAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {
	        $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();
        	       
        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;

        $aVars = array (
            'content' => $sResult,
        );
        echo $this->_oTemplate->adminBlock ($this->_oTemplate->parseHtmlByName('default_padding', $aVars), _t('_bx_zip_administration')); 

        $aVars = array (
            'content' => _t('_bx_zip_help_text'),
        );
        echo $this->_oTemplate->adminBlock ($this->_oTemplate->parseHtmlByName('default_padding', $aVars), _t('_bx_zip_help')); 

        $this->_oTemplate->addCssAdmin ('forms_adv.css');
        $this->_oTemplate->pageCodeAdmin (_t('_bx_zip_administration'));
    }

    function isAdmin () {
        return $GLOBALS['logged']['admin'] ? true : false;
    }

    function _readFromUrl ($sUrl)
    {
        if (function_exists('curl_init'))
        {
                $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, $sUrl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, false);

                $s = curl_exec($curl);

                curl_close($curl);

                if (true === $s) $s = '';
        }	
        else
        {	
            $s = @file_get_contents($sUrl);
        }
        return $s;
    }

    function _getTotalResultsNum (&$s)
    {
		if (preg_match('/<totalResultsCount>(\d+)<\/totalResultsCount>/', $s, $m))
		{			
			return $m[1];
		}    
        return 0;
    }

    function _getLat (&$s)
    {
		if (preg_match('/<lat>([0-9\.-]+)<\/lat>/', $s, $m))
		{			
			return $m[1];
		}    
        return false;
    }
    
    function _getLng (&$s)
    {
		if (preg_match('/<lng>([0-9\.-]+)<\/lng>/', $s, $m))
		{			
			return $m[1];
		}    
        return false;
    }

    function _getStatusMessage  (&$s)
    {
        if (preg_match('/<status\s+message="(.*)"\s+value="(\d+)"\s*\/>/', $s, $m))
        {
            $this->_setError ($m[1]);
            return false;
        }
        return true;
    }

    function _getZipCodesArray (&$s)
    {
		if (!preg_match_all('/<postalcode>(.*)<\/postalcode>/', $s, $m))
		{
			$this->_setError (_t('_No zip codes found'));
			return false;
		}
        return array_unique($m[1]);
    }

    function _getCountriesArray (&$s)
    {
		if (!preg_match_all('/<countryCode>(.*)<\/countryCode>/', $s, $m))
		{
            return array ();
		}
        return array_unique($m[1]);
    }

	function _setError ($s)
	{
		$this->_error = $s;
	}

	function getError ()
	{
		return $this->_error;
    }   

    function _geocodeGoogle (&$sAddress, &$fLongitude, &$fLatitude, &$sCountryCode)
    {
        $iRet = 404;

        $sAddress = rawurlencode($sAddress);
        $sUrl = "http://maps.google.com/maps/geo?q=$sAddress&output=xml&key=".getParam('bx_map_key');

        $s = $this->_readFromUrl ($sUrl);

        if (preg_match ('/<code>(\d+)<\/code>/', $s, $m))
        {
            $iRet = $m[1];
            if (200 != $iRet) return $iRet;

            if (preg_match_all ('/<coordinates>([0-9,\.-]+)<\/coordinates>/', $s, $mCoord))
            {
                if (preg_match_all ('/<CountryNameCode>([A-Za-z]+)<\/CountryNameCode>/', $s, $mCountry))                    
                {
                    foreach ($mCountry AS $k => $v)
                    {
                        if ($sCountryCode == $v[0]) 
                        {
                            // Parse coordinate string
                            list ($fLatitude, $fLongitude, $fAltitude) = explode(",", $mCoord[$k][0]);
                            return $iRet;
                        }
                    }
                }
                else
                {
                    list ($fLatitude, $fLongitude, $fAltitude) = explode(",", $mCoord[1][0]);
                    return $iRet;
                }
            }
        }
        
        return 404;
    }
}

?>
