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

bx_import ('BxDolTwigPageMain');

class MlPagesPageMain extends BxDolTwigPageMain {	

    function MlPagesPageMain(&$oPagesMain) {        
        parent::BxDolTwigPageMain('ml_pages_main', $oPagesMain);
        $this->sSearchResultClassName = 'MlPagesSearchResult';
        $this->sFilterName = 'ml_pages_filter';
	}

    function getBlockCode_UpcomingPhoto() {

        $aPage = $this->oDb->getUpcomingPage (getParam('ml_pages_main_upcoming_page_from_featured_only') ? true : false);
        if (!$aPage) {
            return MsgBox(_t('_Empty'));
        }

        $aAuthor = getProfileInfo($aPage['author_id']);

        $a = array ('ID' => $aPage['author_id'], 'Avatar' => $aPage['thumb']);
        $aImage = BxDolService::call('photos', 'get_image', array($a, 'file'), 'Search');

        ml_pages_import('Voting');
        $oRating = new MlPagesVoting ('ml_pages', (int)$aPage['id']);

        $aVars = array (
            'image_url' => !$aImage['no_image'] && $aImage['file'] ? $aImage['file'] : $this->oTemplate->getIconUrl('no-photo-110.png'),
            'image_title' => !$aImage['no_image'] && $aImage['title'] ? $aImage['title'] : '',

            'page_url' => BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aPage['uri'],
            'page_title' => $aPage['title'],
            'author_title' => _t('_From'),
            'author_username' => $aAuthor['NickName'],
            'author_url' => getProfileLink($aAuthor['ID']),
            'rating' => $oRating->isEnabled() ? $oRating->getJustVotingElement (true, $aPage['id']) : '',
            'fans' => $aPage['fans_count'],
            'country_city' => '<a href="' . $this->oConfig->getBaseUri() . 'browse/country/' . strtolower($aPage['Country']) . '">' . _t($GLOBALS['aPreValues']['Country'][$aPage['Country']]['LKey']) . '</a>' . (trim($aPage['City']) ? ', '.$aPage['City'] : ''),
            'flag_image' => genFlag($aPage['Country']),
        );
        return $this->oTemplate->parseHtmlByName('main_page', $aVars);
    }

    function getBlockCode_UpcomingList() {
        return $this->ajaxBrowse('upcoming', $this->oDb->getParam('ml_pages_perpage_main_upcoming'));
    }

    function getBlockCode_PastList() { 
        return $this->ajaxBrowse('past', $this->oDb->getParam('ml_pages_perpage_main_past'));
    }        

    function getBlockCode_RecentlyAddedList() { 
        return $this->ajaxBrowse('recent', $this->oDb->getParam('ml_pages_perpage_main_recent'));
    }    
		function getBlockCode_CategoriesBlock() {
			$sCategoriesHtml = '';
			$iColumnsCnt = 1;
	
			$iColumnWidth = (100 - $iColumnsCnt*2) / $iColumnsCnt;
	
			$vSqlRes = $this->oDb->getAllCatsInfo();
			$iCategoriesCnt = mysql_num_rows($vSqlRes);
			$iCategPerColumn = ceil($iCategoriesCnt / $iColumnsCnt);
	
			$iCounter = 0;
			while ($aSqlResStr = mysql_fetch_assoc($vSqlRes)) {
				$iID = $aSqlResStr['ID'];
				$sCatName = _t($aSqlResStr['Caption']);

	
				$sqlResSubs = $this->oDb->getAllSubCatsInfo($aSqlResStr['ID']);
				if (db_affected_rows()==-1) {
					return _t('_Error Occured');
				}
				$sSubsHtml = '';
				while ($aSqlResSubsStr = mysql_fetch_assoc($sqlResSubs)) {
					$iSubID = (int)$aSqlResSubsStr['ID'];
	
					$iAdsCnt = $this->oDb->getCountOfAdsInSubCat($iSubID);
					$sCntSub =  ($iAdsCnt > 0) ? " ({$iAdsCnt})" : '';
	
					$sNameSubUp = _t($aSqlResSubsStr['Caption']);
					$sSCategLink = BX_DOL_URL_ROOT . 'm/pages/browse/category/'.$aSqlResSubsStr['Name'];
	
					$sSubsHtml .= <<<EOF
<div>
	<a class="sub_l" href="{$sSCategLink}">
		{$sNameSubUp}
	</a>
	{$sCntSub}
</div>
EOF;
				}
	
			$sCaption = <<<EOF
	{$sCatName}
EOF;
	
				$sOpenColDiv = $sCloseColDiv = '';
				$iResidueOfDiv = $iCounter % $iCategPerColumn;
	
				if ($iResidueOfDiv == 0) {
				$sOpenColDiv = <<<EOF
<div style="width:{$iColumnWidth}%;float:left;position:relative;margin-left:1%;margin-right:1%;">
EOF;
				}
				if ($iResidueOfDiv == $iCategPerColumn-1) {
				$sCloseColDiv = <<<EOF
</div>
EOF;
				}
	
				$sCategoryBlock = DesignBoxContent($sCaption, $sSubsHtml, 1);
	
				$sCategoryCover = $this->oTemplate->getIconUrl('categories/' . $aSqlResStr['Icon']);

				$aCategoryVariables = array (
					'category_cover_image' => $sCategoryCover,
					'category_name' => $sCatName,
					'sub_categories_list' => $sSubsHtml,
					'target' => '',
					'unit_id' => $iID
				);
				$this->oTemplate->addCss('cat.css');
				$this->oTemplate->addJs('cat.js');
				$sCategoryBlock = $this->oTemplate->parseHtmlByName('category_unit', $aCategoryVariables);
	
				$sCategoriesHtml .= $sOpenColDiv . $sCategoryBlock . $sCloseColDiv;
				$iCounter++;
			}
	
	        if ($iCounter == 0) return MsgBox(_t('_Empty'));
	
			$iResidueOfDivLast = $iCounter % $iCategPerColumn;
			if ($iCounter > 0 && $iResidueOfDivLast>0 && $iResidueOfDivLast < $iCategPerColumn) {
				$sCategoriesHtml .= '</div>';
			}
	
		$sAddJS = <<<EOF
<script type="text/javascript">
	function ShowHideController() {
		this.ShowHideToggle = function(rObject) {
			var sChildID	= $(rObject).attr("bxchild");
			var sBlockState = $("#" + sChildID).css("display");

			if ( sBlockState == 'block' ){
				$("#" + sChildID).slideUp(300);
				$(rObject).css({ backgroundPosition : "0 -17px"});
			} else {
				$(rObject).css({ backgroundPosition : "0 0"});
				$("#" + sChildID).slideDown(300);
			}
		}
	}
</script>
EOF;
	
		$sCategoriesBlocks = <<<EOF
<div class="dbContent">
	{$sAddJS}
	{$sCategoriesHtml}
	<div class="clear_both"></div>
</div>
EOF;
			return DesignBoxContent(_t('_ml_pages_administration_categories'), $sCategoriesBlocks, 0);
		}
}

?>
