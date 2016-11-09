<?php

    /***************************************************************************
    *                            Dolphin Smart Community Builder
    *                              -----------------
    *     begin                : Mon Mar 23 2006
    *     copyright            : (C) 2006 BoonEx Group
    *     website              : http://www.boonex.com/
    * This file is part of Dolphin - Smart Community Builder
    *
    * Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 
    * http://creativecommons.org/licenses/by/3.0/
    *
    * Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
    * without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    * See the Creative Commons Attribution 3.0 License for more details. 
    * You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 
    * see license.txt file; if not, write to marketing@boonex.com
    ***************************************************************************/

    require_once( 'inc/header.inc.php' );
    require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
    require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolPageView.php' );
    
	bx_import( 'BxTemplProfileView' );

    $sPageCaption = _t( '_Profile info' );

    $_page['name_index'] 	= 7;
    $_page['header'] 		= $sPageCaption;
    $_page['header_text'] 	= $sPageCaption;
    $_page['css_name']		= 'profile_view.css';

    //-- init some needed variables --//;
    
    $iViewedID = false != bx_get('ID') ? (int) bx_get('ID') : 0;
	if (!$iViewedID) {
		$iViewedID = getLoggedId();
	}

    // check profile membership, status, privacy and if it is exists
    bx_check_profile_visibility($iViewedID, getLoggedId());

    $GLOBALS['oTopMenu'] -> setCurrentProfileID($iViewedID);

    // fill array with all profile informaion
    $aMemberInfo  = getProfileInfo($iViewedID);

    // build page;
    $_ni = $_page['name_index'];

    // prepare all needed keys ;
    $aMemberInfo['anonym_mode'] 	= $oTemplConfig -> bAnonymousMode;
    $aMemberInfo['member_pass'] 	= $aMemberInfo['Password'];
    $aMemberInfo['member_id'] 		= $aMemberInfo['ID'];

    $aMemberInfo['url'] = BX_DOL_URL_ROOT;

    bx_import('BxDolProfileInfoPageView');
    $oProfileInfo = new BxDolProfileInfoPageView('profile_info', $aMemberInfo);
    $sOutputHtml  = $oProfileInfo->getCode();

    $_page_cont[$_ni]['page_main_code'] = $sOutputHtml;

    PageCode();
