<?
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

bx_import('BxDolModuleDb');

class BxZIPDb extends BxDolModuleDb {	

    var $_oConfig;

	function BxZIPDb(&$oConfig) {
        parent::BxDolModuleDb();	
		$this->_oConfig = $oConfig;
    }

    function getCountriesGeonames () {
        $a = $this->getPairs("SELECT `t1`.`ISO2`, `t1`.`Country` FROM `sys_countries` AS `t1` INNER JOIN `bx_zip_countries_geonames` AS `t2` ON `t1`.`ISO2` = `t2`.`ISO2`", 'ISO2', 'Country');
        $this->_countriesSortAndTranslate($a);
        return $a;
    }

    function getCountriesGoogle () {
        $a = $this->getPairs("SELECT `t1`.`ISO2`, `t1`.`Country` FROM `sys_countries` AS `t1` INNER JOIN `bx_zip_countries_google` AS `t2` ON `t1`.`ISO2` = `t2`.`ISO2`", 'ISO2', 'Country');
        $this->_countriesSortAndTranslate($a);
        return $a;
    }        

    function _countriesSortAndTranslate (&$a) {
        foreach ($a as $k => $v) 
            $a[$k] = _t('__'.$v);
        asort ($a);
    }

    function getSettingsCategory() {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'ZIP Code Search' LIMIT 1");
    }    
}

?>
