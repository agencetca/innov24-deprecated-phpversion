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

bx_import ('BxDolTwigTemplate');

/*
 * Pages module View
 */
class MlPagesTemplate extends BxDolTwigTemplate {
    
	/**
	 * Constructor
	 */
	function MlPagesTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
        $this->_iPageIndex = 300;
    }

    function unit ($aData, $sTemplateName, &$oVotingView) {
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('MlPagesModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'ml_pages_unit');
            return $this->parseHtmlByName('browse_unit_private', $aVars);
        }

        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
        if ($aData['category'])
					$aCategory = db_arr("SELECT `Name`, `Caption` FROM `ml_pages_categories` WHERE `ID` = {$aData['category']} LIMIT 1");
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getIconUrl('no-photo.png'),
            'page_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'page_title' => $aData['title'],
            'page_start' => defineTimeInterval($aData['created']),
            'author' => $aData['author_id'] ? $aData['NickName'] : _t('_ml_pages_admin'),
            'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
            'author_title' => _t('_From'),
            'spacer' => getTemplateIcon('spacer.gif'),
            'fans' => $aData['fans_count'],
            'country_city' => '<a href="' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/category/' . $aCategory['Name'] . '">' . _t($aCategory['Caption']) . '</a>',
        );        

        $aData['rate'] = $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;';

        $aVars = array_merge ($aVars, $aData);
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
    function blockPrimPhoto (&$aPage) {

				$a = array ('ID' => $aPage['author_id'], 'Avatar' => $aPage['thumb']);
				$aImageIcon = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');	
        if (!$aImageIcon['file']) return '';
        $w = getParam('bx_photos_browse_width');
        $h = getParam('bx_photos_browse_height');
        $aVars = array (
						'width' => $w + 6,
            'page_thumb' => $aImageIcon['file'],
        );
        return $this->parseHtmlByName('primphoto_block', $aVars);
    }
    // ======================= ppage compose block functions 
    
    function blockInfo (&$aPage) {

        $aAuthor = getProfileInfo($aPage['author_id']);

        $aVars = array (
            'author_thumb' => get_member_thumbnail($aAuthor['ID'], 'none'),
            'date' => date('M-d-Y', $aPage['created']),
            'date_ago' => defineTimeInterval($aPage['created']),
            'cats' => $this->parseCategories($aPage['Categories']),
            'tags' => $this->parseTags($aPage['Tags']),
            'country_city' => '<a href="' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/country/' . strtolower($aPage['Country']) . '">' . _t($GLOBALS['aPreValues']['Country'][$aPage['Country']]['LKey']) . '</a>' . (trim($aPage['City']) ? ', '.$aPage['City'] : ''),
            'flag_image' => genFlag($aPage['Country']),
            'fields' => $this->blockFields($aPage),
            'author_username' => $aAuthor ? $aAuthor['NickName'] : _t('_ml_pages_admin'),
            'author_url' => $aAuthor ? getProfileLink($aAuthor['ID']) : 'javascript:void(0)',
        );
        return $this->parseHtmlByName('block_info', $aVars);
    }

    function blockDesc (&$aPage) {
        $aVars = array (
            'description' => $aPage['Description'],
        );
        return $this->parseHtmlByName('block_description', $aVars);
    }

    function blockFields (&$aPage) {
        $sRet = '<table class="ml_pages_fields">';
        ml_pages_import ('FormAdd');
        $oForm = new MlPagesFormAdd ($GLOBALS['oMlPagesModule'], $this->_iProfileId);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
            $sRet .= '<tr><td class="ml_pages_field_name" valign="top">' . $a['caption'] . '<td><td class="ml_pages_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display'])))
                $sRet .= call_user_func_array(array($this, $a['display']), array($aPage[$k]));
            else
                $sRet .= $aPage[$k];
            $sRet .= '<td></tr>';
        }
        $sRet .= '</table>';
        return $sRet;
    }

    // ======================= output display filters functions

    function filtercreated ($i) {
        return getLocalecreated($i, BX_DOL_LOCALE_DATE) . ' ('.defineTimeInterval($i) . ')';
    }
}

?>
