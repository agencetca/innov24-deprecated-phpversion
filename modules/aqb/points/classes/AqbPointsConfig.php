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

bx_import('BxDolConfig');

class AqbPointsConfig extends BxDolConfig {
    var $_oDb;
	var $_sDateFormat;
	var $_aCurrency;
	var $_isAdmin;
	var $_NotModules;
	var $_aVoteOwner;

	/**
	 * Constructor
	 */

    function AqbPointsConfig($aModule) {
	    parent::BxDolConfig($aModule);
		$this -> _sDateFormat = '%d.%m.%y %H:%i';
		$this -> _aCurrency = BxDolService::call('payment', 'get_currency_info');
     	$this -> _sIconsFolder = 'media/images/membership/';
		$this -> _sImageFolder = 'data/';
		$this -> _isAdmin = isAdmin($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin'] ? $_COOKIE['memberID'] : 0);
		$this -> _NotModules = array('profile','friend');
		
		$this -> _aVoteOwner = array(
										'Profiles' => 'ID',
										'bx_ads_main' => 'IDProfile',
										'bx_arl_entries' => 'author_id',
										'bx_blogs_posts' => 'OwnerID',
										'bx_fdb_entries' => 'author_id',
										'bx_files_main' => 'Owner',
										'bx_news_entries' => 'author_id',
										'bx_photos_main' => 'Owner',
										'bx_poll_data' => 'id_profile',
										'bx_sites_main' => 'ownerid',
										'RayMp3Files' => 'Owner',
										'RayVideoFiles' => 'Owner',
										'bx_events_main' => 'ResponsibleID',
										'bx_groups_main' => 'author_id',
										'bx_store_products' => 'author_id',
										'bx_wall_events' => 'owner_id'
									);		
	}
	
	function init(&$oDb) {
		$this->_oDb = &$oDb;
	}
	
	function getCurrencySign(){
		return $this -> _aCurrency['sign'];
	} 
	
	function getDateFormat(){
		return $this -> _sDateFormat;
	}
	
	function isPermalinkEnabled(){
	  return $this -> _oDb -> getParam('permalinks_module_aqb_points') == 'on';	
	}
	
	function isPresentEmailSendEnabled(){
	  return $this -> _oDb -> getParam('aqb_points_enable_mail_send_present') == 'on';	
	}
	
	function isPenaltyEmailSendEnabled(){
	  return $this -> _oDb -> getParam('aqb_points_enable_mail_send_penalty') == 'on';	
	}
	
	function isPresentFeatureEnabled(){
	  return $this -> _oDb -> getParam('aqb_points_enable_present_points') == 'on' || $this ->_isAdmin;	
	}
	
	function isIncrementEnabled(){
	  return $this -> _oDb -> getParam('aqb_points_enable_increment') == 'on';	
	}

	function isPointsInfoEnabled(){
	  return $this -> _oDb -> getParam('aqb_points_enable_points_info') == 'on' || $this ->_isAdmin;	
	}
	
	function getSiteId() {
		$iId = 0;
		switch($GLOBALS['site']['ver'] . '.' . $GLOBALS['site']['build']) {
			case '7.0.0':
				$iId = -1;
		}

		return $iId;
	}
	
	function getPageNumHistory(){
		return (int)$this -> _oDb -> getParam('aqb_points_page_num_history');
	}
	
	function getPageNumLeadersBlock(){
		return (int)$this -> _oDb -> getParam('aqb_points_page_num_leaders_block');
	}
	
	function getPageNumLeadersPage(){
		return (int)$this -> _oDb -> getParam('aqb_points_page_num_leaders_page');
	}
	
	function getIconsUrl() {
	    return BX_DOL_URL_ROOT . $this->_sIconsFolder;
	}
		
	function isNotModule($sUnit){
		if (in_array($sUnit, $this -> _NotModules)) return true;
		return false;
	}
	
	function getPointsPrice(){
	   return (float)$this -> _oDb -> getParam('aqb_points_page_price'); 	
	}	
	
	function isExchangePointsToCacheEnabled(){
	   return $this -> _oDb -> getParam('aqb_points_enable_exchange_points_to_cache') == 'on'; 	
	}

	function getExchangePointsLimit(){
	   return (int)$this -> _oDb -> getParam('aqb_points_excahnge_limit'); 	
	}
	
	function priceForExchangeToPoints(){
	   return (float)$this -> _oDb -> getParam('aqb_price_to_excahnge'); 	
	}	
	
	function getLevelFolderPath(){
		return $this -> getHomePath() . $this -> _sImageFolder;
	}	
	
	function getLevelFolderUrl(){
		return $this -> getHomeUrl() . $this -> _sImageFolder;
	}
	
	function isLevelsEnabled(){
	   return $this -> _oDb -> getParam('aqb_points_levels_enabled') == 'on'; 	
	}
	
	function isLevelsAccountBlockEnabled(){
	   return $this -> isLevelsEnabled() && $this -> _oDb -> getParam('aqb_points_levels_member_page_block') == 'on'; 	
	}
	
	function isLevelsProfileBlockEnabled(){
	   return $this -> isLevelsEnabled() && $this -> _oDb -> getParam('aqb_points_levels_profile_page_block') == 'on'; 	
	}
}
?>