<?php
/***************************************************************************
*
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
*
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
*
* This notice may not be removed from the source code.
*
***************************************************************************/

class AqbPTSPageComposer {
	var $_sPage;
	var $_sBaseURL;
	var $_oDb;

	function AqbPTSPageComposer($sPage, $sBaseURL, $oDb) {
		$this->_sPage = $sPage;
		$this->_sBaseURL = $sBaseURL;
		$this->_oDb = $oDb;
	}

	function constructPageLayout() {
		if (empty($this->_sPage)) {
			return $this->_getAvailablePages().'<div id="page_builder_zone"></div>';
		} else {
			return $this->_getPageBlocks($this->_sPage);
		}
	}

	function _getAvailablePages() {
		$sPagesQuery = "SELECT `Name`, `Title` FROM `sys_page_compose_pages` ORDER BY `Order`";

		$sRet = '
            '._t('_adm_txt_pb_page').'
	            <select class="form_input_select" name="Page" onchange="if (this.value && this.value != \'none\') refreshPagesBuilder(\''.$this->_sBaseURL.'action_get_page_page_blocks/\', this.value);">
					<option value="none">'._t('_adm_txt_pb_select_page').'</option>
		';
		$rPages = $this->_oDb->res( $sPagesQuery );
		while( $aPage = mysql_fetch_assoc($rPages) ) {
			$sTitle = htmlspecialchars( $aPage['Title'] ? $aPage['Title'] : $aPage['Name'] );
			$sSelected = $this->_sPage == $aPage['Name'] ? 'selected="selected"' : '';
			$sRet .= '<option value="'.htmlspecialchars_adv( urlencode($aPage['Name'])).'" '.$sSelected.'>'.$sTitle.'</option>';
		}
		$sRet .= '</select>';
		return $sRet;
	}
	function _getPageBlocks($sPage) {
		$sRet = '<table width="100%"><tr>';

		$sPage = process_db_input($sPage);
		$sQuery = "
            SELECT
                `Column`
            FROM `sys_page_compose`
            WHERE
                `Page` = '{$sPage}' AND `Column` != 0
            GROUP BY `Column`
            ORDER BY `Column`";
        $rColumns = $this->_oDb->res( $sQuery );
        $iColumn = 0;
        while( $aColumn = mysql_fetch_assoc( $rColumns ) ) {
        	$sRet .= '<td valign="top">';
        	$sRet .= '<div class="aqb_column_cont">';
        	$sRet .= '<div class="aqb_column_header">';
        	$sRet .= 'Column '.++$iColumn;
        	$sRet .= '</div>';
            $iColumn = (int)$aColumn['Column'];

            //get active blocks
            $sQueryActive = "
                SELECT
                    `ID`,
                    `Caption`
                FROM `sys_page_compose`
                WHERE
                    `Page` = '{$sPage}' AND `Column` = $iColumn
                ORDER BY `Order`";

            $rBlocks = $this->_oDb->res( $sQueryActive );

            while( $aBlock  = mysql_fetch_assoc( $rBlocks ) ) {
            	$sRet .= '<div class="aqb_column_block">';
	        	$sRet .= '<a href="javascript:showPageEditForm('.$aBlock['ID'].')">'._t( $aBlock['Caption'] ).'</a>';
	        	$sRet .= '</div>';

                $aBlock['ID'];

            }
            $sRet .= '</div>';
            $sRet .= '</td>';
        }

        $sRet .= '</tr></table>';
        $sRet .= '<script language="javascript">var sParserURL = "'.$this->_sBaseURL.'action_page_block/"</script>';
        return $sRet;
	}

	function updateCache($sCacheFolder) {
		 $rCacheFile = @fopen($sCacheFolder.'page_blocks.inc', 'w');

		 $sCacheString = '';
		 $aPageBlocks = $this->_oDb->getAll("SELECT `PageBlockID`, `ProfileTypesVisibility`, `ProfileTypes` FROM `aqb_pts_page_blocks_visibility`");
		 foreach ($aPageBlocks as $aPageBlock) {
		 	$sCacheString .= "\t{$aPageBlock['PageBlockID']} => array('ProfileTypes' => {$aPageBlock['ProfileTypes']}, 'ProfileTypesVisibility' => {$aPageBlock['ProfileTypesVisibility']}),\n";
		 }

		 @fputs($rCacheFile, "return array(\n{$sCacheString});");
		 @fclose($rCacheFile);
	}
	function loadCache($sCacheFolder, $bStopRecursion = false) {
		$sCacheFile = $sCacheFolder.'page_blocks.inc';

		$aRet = array();
		if (!file_exists( $sCacheFile ) or !$aRet = @eval( file_get_contents($sCacheFile) ) or !is_array($aRet)) {
           AqbPTSPageComposer::updateCache($sCacheFolder);
           if (!$bStopRecursion) AqbPTSPageComposer::loadCache($sCacheFolder, true);
		}

        return $aRet;
	}
}
?>