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

bx_import('BxDolTwigModule');

/*
 * Auto Blog Create module by Modzzz
 
 *
 * Profile's Wall:
 * no spy events
 *
 *
 *
 * Spy:
 * no spy events
 *
 *
 *
 * Memberships/ACL:
 * Doesn't depend on user's membership.
 *
 * 
 *
 * Service methods:
 * 
 * 
 *
 * Alerts:
 * no alerts available
 *
 */
class BxBloogModule extends BxDolTwigModule {
 
	/**
	 * Constructor
	 */
	function BxBloogModule($aModule) {
 
        parent::BxDolTwigModule($aModule);
 
        $GLOBALS['oBxBloogModule'] = &$this;
   
 	}
 
     /*
    * Service - response blog delete
    */
    function serviceResponseBlogCreate ($oAlert) {
        if (!($iProfileId = (int)$oAlert->iObject))
            return false;
		
		if(getParam('bx_blogs_auto_create') != 'on')
			return false;

		$this->_oDb->createBlog($iProfileId);
 
        return true;
    }



}
?>