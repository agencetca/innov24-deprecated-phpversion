<?
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.modloaded.com
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
* see license.txt file; if not, write to marketing@modloaded.com
***************************************************************************/

bx_import('BxTemplVotingView');

class MlPagesVoting extends BxTemplVotingView {
    
	/**
	 * Constructor
	 */
	function MlPagesVoting($sSystem, $iId) {
	    parent::BxTemplVotingView($sSystem, $iId);
    }

    function getMain() {
        $aPathInfo = pathinfo(__FILE__);
        require_once ($aPathInfo['dirname'] . '/MlPagesSearchResult.php');
        return MlPagesSearchResult::getMain();
    }

    function checkAction () {
        if (!parent::checkAction())
            return false;
        $oMain = $this->getMain();        
        $aPage = $oMain->_oDb->getEntryByIdAndOwner($this->getId (), 0, true);
        return $oMain->isAllowedRate($aPage);
    }
}

?>
