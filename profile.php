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

define('BX_PROFILE_PAGE', 1);

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

bx_import('BxTemplProfileView');
bx_import('BxDolInstallerUtils');

$profileID = getID( $_GET['ID'] );
$memberID = getLoggedId();

// check profile membership, status, privacy and if it is exists
bx_check_profile_visibility($profileID, $memberID);

// make profile view alert and record profile view event
if ($profileID != $memberID) {
    require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
    $oAlert = new BxDolAlerts('profile', 'view', $profileID, $memberID);
    $oAlert->alert();

	bx_import ('BxDolViews');
	new BxDolViews('profiles', $profileID);
}

$oProfile = new BxBaseProfileGenerator( $profileID );

$oProfile->oCmtsView->getExtraCss();
$oProfile->oCmtsView->getExtraJs();
$oProfile->oVotingView->getExtraJs();

$oSysTemplate->addJs('view_edit.js');

$_page['name_index'] = 5;
$_ni = $_page['name_index'];

$p_arr  = $oProfile -> _aProfile;
$_page['header'] = process_line_output( $p_arr['NickName'] ) . ": ". htmlspecialchars_adv( $p_arr['Headline'] );

$oPPV = new BxTemplProfileView($oProfile, $site, $dir);
$_page_cont[$_ni]['page_main_code'] = $oPPV->getCode();
$_page_cont[$_ni]['custom_block'] = '';
$_page_cont[$_ni]['page_main_css'] = '';

// add profile customizer
if (BxDolInstallerUtils::isModuleInstalled("profile_customize"))
{
    $_page_cont[$_ni]['custom_block'] = '<div id="profile_customize_page" style="display: none;">' .
        BxDolService::call('profile_customize', 'get_customize_block', array()) . '</div>';
    $_page_cont[$_ni]['page_main_css'] = '<style type="text/css">' . 
        BxDolService::call('profile_customize', 'get_profile_style', array($profileID)) . '</style>';
}

PageCode();

