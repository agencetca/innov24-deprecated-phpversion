<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Confession
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

bx_import('BxDolTwigSearchResult');

class BxAlertsSearchResult extends BxDolTwigSearchResult {
  
    var $aCurrent = array(
        'name' => 'modzzz_alerts',
        'title' => '_modzzz_alerts_page_title_browse',
        'table' => 'modzzz_alerts_main',
        'ownFields' => array('id', 'author_id', 'phrase', 'unit', 'notify', 'created' ),
        'searchFields' => array('title' ), 
        'join' => array(
            'profile' => array(
                    'type' => 'left',
                    'table' => 'Profiles',
                    'mainField' => 'author_id',
                    'onField' => 'ID',
                    'joinFields' => array('NickName'),
            ),
        ), 
        'restriction' => array( 
            'owner' => array('value' => '', 'field' => 'author_id', 'operator' => '='), 
            'activeStatus' => array('value' => 'approved', 'field'=>'status', 'operator'=>'='), 
            'public' => array('value' => '', 'field' => 'allow_view_alerts_to', 'operator' => '='),
        ),
        'paginate' => array('perPage' => 14, 'page' => 1, 'totalNum' => 0, 'totalPages' => 1),
        'sorting' => 'last',
        'rss' => array( 
            'title' => '',
            'link' => '',
            'image' => '',
            'profile' => 0,
            'fields' => array (
                'Link' => '',
                'Title' => 'title',
                'DateTimeUTS' => 'created',
                'Desc' => 'desc',
                'Photo' => '',
            ),
        ),
        'ident' => 'id'
    );
    
    
    function BxAlertsSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '') {   

        switch ($sMode) {
 
            case 'search':
                if ($sValue)
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

                if ($sValue2) {

                    $this->aCurrent['join']['category'] = array(
                        'type' => 'inner',
                        'table' => 'sys_categories',
                        'mainField' => 'id',
                        'onField' => 'ID',
                        'joinFields' => '',
                    );

                    $this->aCurrent['restriction']['category']['value'] = $sValue2;
                    if (is_array($sValue2)) {
                        $this->aCurrent['restriction']['category']['operator'] = 'in';
                    } 
                }

                $this->sBrowseUrl = "search/$sValue/" . (is_array($sValue2) ? implode(',',$sValue2) : $sValue2);
                $this->aCurrent['title'] = _t('_modzzz_alerts_page_title_search_results') . ' ' . (is_array($sValue2) ? implode(', ',$sValue2) : $sValue2) . ' ' . $sValue;
                unset($this->aCurrent['rss']);
                break;

            case 'user':
                $iProfileId = $GLOBALS['oBxAlertsModule']->_oDb->getProfileIdByNickName ($sValue);
                //$GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
             
				$this->sBrowseUrl = "browse/user/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_alerts_page_title_browse_by_author');
                if (isset($_REQUEST['rss']) && $_REQUEST['rss']) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['Avatar']) {
                        $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
                        $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    } 
                }
				  
                break;

            case 'admin':
                $this->aCurrent['restriction']['owner']['value'] = 0;
                $this->sBrowseUrl = "browse/admin";
                $this->aCurrent['title'] = _t('_modzzz_alerts_page_title_admin_alerts');
                break;
  
            case 'tag':
            	$sValue = uri2title($sValue);

                $this->aCurrent['restriction']['tag']['value'] = $sValue;
                $this->sBrowseUrl = "browse/tag/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_alerts_page_title_browse_by_tag') . ' ' . $sValue;
                break;

            case 'recent':
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_modzzz_alerts_page_title_browse_recent');
                break; 
            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_modzzz_alerts');
                break;

            default:
                $this->isError = true;
        }

        $oMain = $this->getMain();

        $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_alerts_perpage_browse');

        if (isset($this->aCurrent['rss']))
            $this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;

        if (isset($_REQUEST['rss']) && $_REQUEST['rss']) {
            $this->aCurrent['ownFields'][] = 'desc';
            $this->aCurrent['ownFields'][] = 'created';
            $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_alerts_max_rss_num');
        }
 
        $this->sFilterName = 'modzzz_alerts_filter';

        parent::BxDolTwigSearchResult();
    }

    function getAlterOrder() {
		if ($this->aCurrent['sorting'] == 'last') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_alerts_main`.`created` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'top') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_alerts_main`.`rate` DESC, `modzzz_alerts_main`.`rate_count` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_alerts_main`.`views` DESC";
			return $aSql;
		}
	    return array();
    }

    function displayResultBlock () {
        global $oFunctions;
        $s = parent::displayResultBlock ();
        if ($s) {
            $oMain = $this->getMain();
            $GLOBALS['oSysTemplate']->addDynamicLocation($oMain->_oConfig->getHomePath(), $oMain->_oConfig->getHomeUrl());
            $GLOBALS['oSysTemplate']->addCss('unit.css');
            return $oFunctions->centerContent ($s, '.modzzz_alerts_unit');
        }
        return '';
    }

    function getMain() {
        return BxDolModule::getInstance('BxAlertsModule');
    }

    function getRssUnitLink (&$a) {
        $oMain = $this->getMain();
        return BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'view/' . $a['uri'];
    }
    
    function _getPseud () {
        return array(    
            'id' => 'id',
            'title' => 'title',
            'uri' => 'uri',
            'created' => 'created',
            'author_id' => 'author_id',
            'NickName' => 'NickName',
            'thumb' => 'thumb',  
        );
    }
}

?>
