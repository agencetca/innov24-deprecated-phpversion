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

bx_import ('BxTemplProfileView');

class BxDolProfileInfoPageView extends BxTemplProfileView
{
    // contain informaion about viewed profile ;
    var $aMemberInfo = array();
    // logged member ID ;
    var $iMemberID;
    var $oProfilePV;
    
    /**
     * Class constructor ;
     */
    function BxDolProfileInfoPageView( $sPageName, &$aMemberInfo ) 
    {
        global $site, $dir;

        $this->oProfileGen = new BxBaseProfileGenerator( $aMemberInfo['ID'] );
        $this->aConfSite = $site;
        $this->aConfDir  = $dir;
        parent::BxDolPageView($sPageName);

        $this->iMemberID  = getLoggedId();
        $this->aMemberInfo = &$aMemberInfo;    		
    }


    /**
     * Function will generate profile's  general information ;
     *
     * @return : (text) - html presentation data;
     */
    function getBlockCode_GeneralInfo($iBlockID)
    {
        return $this -> getBlockCode_PFBlock($iBlockID, 17);
    }

    /**
     * Function will generate profile's additional information ;
     *
     * @return : (text) - html presentation data;
     */
    function getBlockCode_AdditionalInfo($iBlockID)
    {
        return $this -> getBlockCode_PFBlock($iBlockID, 20);
    }

    /**
     * Function will generate profile's additional information ;
     *
     * @return : (text) - html presentation data;
     */
    function getBlockCode_Description() {
        if(!$this->aMemberInfo['DescriptionMe'])
            return;

        return array($this->aMemberInfo['DescriptionMe']);
    }
}

