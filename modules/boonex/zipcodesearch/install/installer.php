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

bx_import('BxDolInstaller');

class BxZIPInstaller extends BxDolInstaller {

    function BxZIPInstaller($aConfig) {
        parent::BxDolInstaller($aConfig);
    }

    function install($aParams) {
        $aResult = parent::install($aParams);

        $s = $this->_readFromUrl("http://ws.geonames.org/postalCodeCountryInfo?");
        $a = $this->_getCountriesArray ($s);
        if (count($a)) {
            db_res ("TRUNCATE TABLE `bx_zip_countries_geonames`");
            foreach ($a as $sCountry)
                db_res ("INSERT INTO `bx_zip_countries_geonames` VALUES ('$sCountry')");
        } else {
            return array('code' => BX_DOL_INSTALLER_FAILED, 'content' => 'Network error - can not get list of countries');
        }

        return $aResult;
    }

    function uninstall($aParams) {
        return parent::uninstall($aParams);
    }    

    function _getCountriesArray (&$s) {
		if (!preg_match_all('/<countryCode>(.*)<\/countryCode>/', $s, $m)) {
            return array ();
		}
        return array_unique($m[1]);
    }    

    function _readFromUrl ($sUrl) {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $sUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            $s = curl_exec($curl);
            curl_close($curl);
            if (true === $s) 
                $s = '';
        } else {	
            $s = @file_get_contents($sUrl);
        }
        return $s;
    }    
}

?>
