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


    
require_once( '../../../inc/header.inc.php' );
require_once(BX_DIRECTORY_PATH_INC . 'admin.inc.php');
require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesModule.php');
bx_import('BxDolModuleDb');

$_page['name_index'] 	= 151;

check_logged();
$oModuleDb = new BxDolModuleDb();
$aModule = $oModuleDb->getModuleByUri('pages');
$_page['header'] = _t('_ml_pages_edit_page');
$_page['header_text'] = _t('_ml_pages_edit_page');
$oMlPages = new MlPagesModule($aModule);
$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = PageCompPages($oMlPages);
$oMlPages->_oTemplate->addCss ('forms_extra.css');

function PageCompPages($oMlPages)
{
	$iPageId = $_GET['page_id'];
	if (!$iPageId)
	{
		header("location:".BX_DOL_URL_ROOT."m/pages/home/");
		exit;
	}
										
	require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesPageEditProcessor.php');
	$oEditPageProc = new MlPagesPageEditProcessor($iPageId);
	$aVars = $oMlPages->_getCategoriesForm($iPageId, $oEditPageProc -> sMainCategory, $oEditPageProc -> sSubCategory);
	$aVars = array_merge($aVars, array ('form' => $oEditPageProc->process(), 'edit_url' => BX_DOL_URL_ROOT . 'modules/modloaded/pages/edit.php', 'page_id' => $iPageId));
	$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $oEditPageProc -> sUri);
	$GLOBALS['oSysTemplate']->addJsTranslation('_Errors in join form');
	$GLOBALS['oSysTemplate']->addJs(array('join.js', 'jquery.form.js'));
	$GLOBALS['oSysTemplate']->addCss(array('join.css'));
	return DesignBoxContent(_t('_ml_pages_edit_page'), $oMlPages->_oTemplate->parseHtmlByName('my_pages_edit_page', $aVars), 1);
}
PageCode();
?>
