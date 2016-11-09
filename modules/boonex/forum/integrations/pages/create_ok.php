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
$_page['header'] = _t('_ml_pages_create_page');
$_page['header_text'] = _t('_ml_pages_create_page');
$oMlPages = new MlPagesModule($aModule);
$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = PageCompPages($oMlPages);
$oMlPages->_oTemplate->addCss ('forms_extra.css');
  
function PageCompPages($oMlPages)
{
	if (!$oMlPages->isAllowedAdd()) {
		return DesignBoxContent(_t('_ml_pages_create_page'), MsgBox(_t('_Access denied')), 1);
	} 
	require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesCreatePageProcessor.php');
	$oCreatePageProc = new MlPagesCreatePageProcessor();
	$aVars = $oMlPages->_getCreatePageCategoriesForm();
	$aVars = array_merge($aVars, array ('form' => $oCreatePageProc->process(), 'create_url' => BX_DOL_URL_ROOT . 'modules/modloaded/pages/create.php'));
	$GLOBALS['oSysTemplate']->addJsTranslation('_Errors in join form');
	$GLOBALS['oSysTemplate']->addJs(array('join.js', 'jquery.form.js'));
	$GLOBALS['oSysTemplate']->addCss(array('join.css'));
	$oMlPages->_oTemplate->addCss(array('main.css'));
	return DesignBoxContent(_t('_ml_pages_create_page'), $oMlPages->_oTemplate->parseHtmlByName('my_pages_create_page', $aVars), 1);
}
PageCode();
?>
