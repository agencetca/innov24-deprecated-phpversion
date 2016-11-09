<?php
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

bx_import('BxDolTwigSearchResult');

class MlPagesSearchResult extends BxDolTwigSearchResult  {

    var $aCurrent = array(
        'name' => 'ml_pages',
        'title' => '_ml_pages_caption_browse',
        'table' => 'ml_pages_main',
        'ownFields' => array('id', 'title', 'uri', 'created', 'author_id', 'thumb', 'fans_count', 'rate', 'category'),
        'searchFields' => array('title', 'tags', 'category'),
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
            'activeStatus' => array('value' => 'approved', 'field'=>'status', 'operator'=>'='),
            'owner' => array('value' => '', 'field' => 'author_id', 'operator' => '='),
            'tag' => array('value' => '', 'field' => 'tags', 'operator' => 'against'),
            'category' => array('value' => '', 'field' => 'category', 'operator' => '='),
            'country' => array('value' => '', 'field' => 'Country', 'operator' => '='),
            'public' => array('value' => '', 'field' => 'allow_view_page_to', 'operator' => '='),
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
                'Desc' => 'Description',
                'Photo' => '',
            ),
        ),
        'ident' => 'id'
    );
    
    function MlPagesSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '') {

        $oMain = $this->getMain();

        switch ($sMode) {

            case 'pending':
                if (false !== bx_get('ml_pages_filter'))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input(bx_get('ml_pages_filter'), BX_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "administration";
                $this->aCurrent['title'] = _t('_ml_pages_pending_approval');
                unset($this->aCurrent['rss']);
            break;

            case 'my_pending':                
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_ml_pages_pending_approval');
                unset($this->aCurrent['rss']);
            break;

            case 'search':
                if ($sValue)
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

                if ($sValue2)
                    if (is_array($sValue2)) {
                        $this->aCurrent['restriction']['country'] = array('value' => $sValue2, 'field' => 'Country', 'operator' => 'in');
                    } else {
                        $this->aCurrent['restriction']['country']['value'] = $sValue2;
                    }

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $sValue2 = $GLOBALS['MySQL']->unescape($sValue2);
                $this->sBrowseUrl = "search/$sValue/" . (is_array($sValue2) ? implode(',',$sValue2) : $sValue2);
                $this->aCurrent['title'] = _t('_ml_pages_caption_search_results') . ' ' . (is_array($sValue2) ? implode(', ',$sValue2) : $sValue2) . ' ' . $sValue;
                unset($this->aCurrent['rss']);
                break;

            case 'user':
                $iProfileId = $GLOBALS['oMlPagesModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of pages
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/user/$sValue";
                $this->aCurrent['title'] = _t('_ml_pages_caption_browse_by_author') . ' ' . $sValue;
                if (bx_get('rss')) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['thumb']) {
                        $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
                        $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    } 
                }
                break;

            case 'joined':
                $iProfileId = $GLOBALS['oMlPagesModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                

                if (!$iProfileId) {

                    $this->isError = true;

                } else {

					$this->aCurrent['join']['fans'] = array(
						'type' => 'inner',
						'table' => 'ml_pages_fans',
						'mainField' => 'id',
						'onField' => 'id_entry',
						'joinFields' => array('id_profile'),
					);
					$this->aCurrent['restriction']['fans'] = array(
						'value' => $iProfileId, 
						'field' => 'id_profile', 
						'operator' => '=', 
						'table' => 'ml_pages_fans',
					);
					$this->aCurrent['restriction']['confirmed_fans'] = array(
						'value' => 1, 
						'field' => 'confirmed', 
						'operator' => '=', 
						'table' => 'ml_pages_fans',
					);
				}

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/joined/$sValue";
                $this->aCurrent['title'] = _t('_ml_pages_caption_browse_by_author_joined_pages') . ' ' . $sValue;

                if (bx_get('rss')) {
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
                if (bx_get('ml_pages_filter'))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input(bx_get('ml_pages_filter'), BX_TAGS_STRIP), 'field' => '','operator' => 'against');                
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->sBrowseUrl = "browse/admin";
                $this->aCurrent['title'] = _t('_ml_pages_admin_pages');
                break;

            case 'category':
                $this->aCurrent['join']['category'] = array(
                    'type' => 'inner',
                    'table' => 'sys_categories',
                    'mainField' => 'id',
                    'onField' => 'ID',
                    'joinFields' => '',
                );
                $this->aCurrent['restriction']['category']['value'] = $sValue;                
                $this->aCurrent['restriction']['category']['table'] = 'sys_categories';
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/category/" . title2uri($sValue);
                $this->aCurrent['title'] = _t('_ml_pages_caption_browse_by_category') . ' ' . $sValue;
                break;

            case 'tag':
                $this->aCurrent['restriction'][$sMode]['value'] = $sValue;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/$sMode/" . title2uri($sValue);
                $this->aCurrent['title'] = _t('_ml_pages_caption_browse_by_'.$sMode) . ' ' . $sValue;
                break;

            case 'country':            
                $this->aCurrent['restriction'][$sMode]['value'] = $sValue;
                $this->sBrowseUrl = "browse/$sMode/$sValue";
                $this->aCurrent['title'] = _t('_ml_pages_caption_browse_by_'.$sMode) . ' ' . $sValue;
                break;

            case 'upcoming':
                $this->aCurrent['restriction']['upcoming'] = array('value' => time(), 'field' => 'created', 'operator' => '>');
                $this->aCurrent['sorting'] = 'upcoming';
                $this->sBrowseUrl = 'browse/upcoming';
                $this->aCurrent['title'] = _t('_ml_pages_caption_browse_upcoming');
                break;

            case 'past':
                $this->aCurrent['restriction']['past'] = array('value' => time(), 'field' => 'created', 'operator' => '<');
                $this->aCurrent['sorting'] = 'past';
                $this->sBrowseUrl = 'browse/past';
                $this->aCurrent['title'] = _t('_ml_pages_caption_browse_past');
                break;

            case 'recent':
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_ml_pages_caption_browse_recently_added');
                break;

            case 'top':
                $this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_ml_pages_caption_browse_top_rated');
                break;

            case 'popular':
                $this->sBrowseUrl = 'browse/popular';
                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_ml_pages_caption_browse_popular');
                break;                

            case 'featured':
                $this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'Featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_ml_pages_caption_browse_featured');
                break;                                

            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 00:00:00')", 'field' => 'PageStart', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 23:59:59')", 'field' => 'PageStart', 'operator' => '<=', 'no_quote_value' => true);
                $this->sBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->aCurrent['title'] = _t('_ml_pages_caption_browse_by_day') . sprintf("%04u-%02u-%02u", $sValue, $sValue2, $sValue3);
                break;                                

            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_ml_pages');
                unset($this->aCurrent['rss']);
                break;

            default:
                $this->isError = true;
        }        
				if (is_numeric($sMode)) 
				{
					$this->aCurrent['restriction'][$sMode] = array('value' => $sMode, 'field' => 'category', 'operator' => '=');
					$this->isError = false;
				}
					
        $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('ml_pages_perpage_browse');

        if (isset($this->aCurrent['rss']))
            $this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;

        if (bx_get('rss')) {
            $this->aCurrent['ownFields'][] = 'Description';
            $this->aCurrent['ownFields'][] = 'created';
            $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('ml_pages_max_rss_num');
        }

        ml_pages_import('Voting', $this->getModuleArray());
        $oVotingView = new MlPagesVoting ('ml_pages', 0);
        $this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

        parent::BxDolTwigSearchResult();
    }

    function getAlterOrder() {
		if ($this->aCurrent['sorting'] == 'last') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `ml_pages_main`.`created` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'upcoming') {
			$aSql = array();
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'top') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `ml_pages_main`.`rate` DESC, `ml_pages_main`.`rate_count` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `ml_pages_main`.`fans_count` DESC, `ml_pages_main`.`Views` DESC";
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
            return $oFunctions->centerContent ($s, '.ml_pages_unit');
        }
        return '';
    }

    function getModuleArray() {
        return db_arr ("SELECT * FROM `sys_modules` WHERE `title` = 'Pages' AND `class_prefix` = 'MlPages' LIMIT 1");
    }

    function getMain() {
        return BxDolModule::getInstance('MlPagesModule');
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
            'author_id' => 'author_id',
            'NickName' => 'NickName',
            'thumb' => 'thumb', 
        );
    }
}

?>
