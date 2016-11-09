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

bx_import('BxDolModule');
bx_import('BxDolPageView');

require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );
require_once( 'AqbPointsMembershipPage.php' );

class AqbPointsModule extends BxDolModule {
	
	/**
	 * Constructor
	 */
	function AqbPointsModule($aModule) {
	    parent::BxDolModule($aModule);
		$this->_oConfig->init($this->_oDb);
		$this->iUserId = $GLOBALS['logged']['member'] || $GLOBALS['logged']['admin'] ? $_COOKIE['memberID'] : 0;
	}
	
	function isAdmin(){
		return isAdmin($this->iUserId);
	}
    
	function actionAdministration ($sUrl = '', $iLevel = 2) {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        
		
		if ($_POST['aqb-points-delete'] && count($_POST['members']) > 0) 
			foreach($_POST['members'] as $iId)
			{	
				profile_delete((int)$iId);
				$this -> _oDb -> deleteProfileInfo((int)$iId);
			}

	
		$aParam = array('ID' => !(int)$iLevel ? 2 : $iLevel);
		$aParam = array_merge($aParam, $_POST);  	
		
		
		$iNumber = $this -> _oDb -> getUnpaidRequestsNumber();		
        $this->_oTemplate->pageStart();

        $aMenu = array(
        'members' => array(
                'title' => _t('_aqb_points_admin_members'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/members',
                '_func' => array ('name' => 'getCodeMembers', 'params' => array(array())),
            ),
	   'exchange' => array(
                'title' => _t('_aqb_points_admin_exchange_panel', $iNumber ? "($iNumber)" : ''), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/exchange',
                '_func' => array ('name' => 'getExchangePanel', 'params' => array(array())),
            ),    
	   'packages' => array(
                'title' => _t('_aqb_points_admin_packages'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages', 
                '_func' => array ('name' => 'getPackagesPanel', 'params' => array(false)),
            ),                 
        'membership' => array(
                'title' => _t('_aqb_points_admin_membership_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/membership',
                '_func' => array ('name' => 'getMembershipPanel', 'params' => array($aParam)),
            ),
		'levels' => array(
                'title' => _t('_aqb_points_admin_levels'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/levels',
                '_func' => array ('name' => 'getLevelsSection', 'params' => array()),
        ),			
        'settings' => array(
                'title' => _t('_aqb_points_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'getSettingsPanel', 'params' => array()),
            ),
	    'actions' => array(
                'title' => _t('_aqb_points_admin_actions_managmenet'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/actions', 
                '_func' => array ('name' => 'getActionsPanel', 'params' => array(false)),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'members';

        $aMenu[$sUrl]['active'] = 1;
        
        $sContent = call_user_func_array (array($this -> _oTemplate, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);
	    
        echo $this->_oTemplate->adminBlock ($sContent, $aMenu[$sUrl]['title'], $aMenu);
	    if ($sUrl == 'membership') echo $this	->	_oTemplate	->	getMemLevelsBlocks($aParam);
		if ($sUrl == 'levels') echo $this -> _oTemplate -> getLevelsList();
        
	    $this->_oTemplate->addAdminCss(array('admin.css', 'forms_extra.css','forms_adv.css', 'general.css'));
        $this->_oTemplate->addAdminJs(array('admin.js','main.js', 'jquery.dolPopup'));
        $this->_oTemplate->pageCodeAdmin(_t('_aqb_points_administration'));
    }
    
    function servicePointsLeadersIndexPage(){
		return $this -> _oTemplate -> getPointsLeadersBlock('PointsLeadersIndexPage');	
	}
	
	function actionBuyForm(){
	   echo $this -> _oTemplate -> buyPointsBlock();
	   exit;	
	}
	
	function actionDeleteLevel($iItem){
	   if (!$this->isAdmin()) return false;
	   
	    $aResult = array('code' => 1, 'message' => _t('_aqb_points_levels_was_not_deleted'));
	   if ($this -> _oDb -> deleteLevel($iItem))  $aResult = array('code' => 0, 'message' => _t('_aqb_points_levels_delete_successfully'));
	   
	   
	   header('Content-Type:text/javascript');
       $oJson = new Services_JSON();
       echo $oJson->encode($aResult);
	   exit;
	}
	
	function actionEditLevel($iItem){
	   if (!$this->isAdmin() || !$iItem) return '';
	   
	   echo $this -> _oTemplate -> getLevelsSection($iItem);
	   exit;
	}
	
	function actionCheckExchangeRequest(){
		if (!$this -> isLogged() || !$this -> _oConfig -> isExchangePointsToCacheEnabled()) return '';
      
		$iPoints = $this -> _oDb -> getProfileTotalPointsNum($this -> iUserId);
	   	$iPointsLimit = $this -> _oConfig -> getExchangePointsLimit();
		
		$aResult = array('code' => 0);
		
		if ((int)$iPointsLimit && $iPointsLimit > $iPoints) $aResult = array('code' => 1, 'message' => _t('_aqb_points_exchange_limit_is_not_exceeded', $iPointsLimit));
		
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
        echo $oJson->encode($aResult);
		exit;
	}
	
	
	function actionExchangeToMoney(){
		if (!$this -> isLogged() || !$this -> _oConfig -> isExchangePointsToCacheEnabled()) return '';
      
		$iPoints = $this -> _oDb -> getProfileTotalPointsNum($this -> iUserId);
	   	$iPointsLimit = $this -> _oConfig -> getExchangePointsLimit();
	
		if ((int)$iPointsLimit && $iPointsLimit > $iPoints) $aResult = array('code' => 1, 'message' => _t('_aqb_points_exchange_not_succeed'));
		else{	
			$fPrice = $this -> _oConfig -> priceForExchangeToPoints();
				
			$aProfileInfo = getProfileInfo($this -> iUserId);
			$iResult = sendMail($this -> _oDb -> getParam('site_email'),  _t('_aqb_points_exchange_request_email_subject'), _t('_aqb_points_exchange_request_body', '<a href="'.getProfileLink($this -> iUserId). '">' . $aProfileInfo['NickName'] . '</a>', $iPoints, $this -> _oConfig -> getCurrencySign() . round($fPrice*$iPoints, 2)), $this -> iUserId);

			if ($this -> _oDb -> addExchangeTransaction($this -> iUserId)){
				if ($iResult) $aResult = array('code' => 0, 'message' => _t('_aqb_points_exchange_request_email_success'));
				else $aResult = array('code' => 1, 'message' => _t('_aqb_points_exchange_request_email_not_success'));
			}else $aResult = array('code' => 2, 'message' => _t('_aqb_points_exchange_tx_exists'));
		}

		header('Content-Type:text/javascript');
		$oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;
	}
	
    function actionHistory($sAction = '', $page = 1, $sActivities = 'all', $iDays = 0){
    	if (!$this->isLogged()) {
			$this -> _oTemplate-> pageStart();
            echo MsgBox(_t('_aqb_points_have_to_login'));
			$this -> _oTemplate -> pageCode(_t('_aqb_points_title'), false, false);
			exit;
        }

		$iProfileID = !(int)$sAction ? $this -> iUserId :(int)$sAction;
		 
        $aPageSettings = array(
    		'page' => (int)$page ? (int)$page : 1,
		    'per_page' => $this -> _oConfig -> getPageNumHistory(),
        	'activities' => isset($_POST['action_submit']) ? $_POST['action_submit'] : $sActivities,
            'days' => isset($_POST['action_time_submit']) && (int)$_POST['action_time_submit'] >= 0 ? (int)$_POST['action_time_submit'] : (int)$iDays,
 		); 
		if ($this->isAdmin() && isset($_POST['history_delete'])) $this -> deleteHistoryItems($_POST, $iProfileID);

        $this -> _oTemplate-> pageStart();

        if ($sAction == 'actions'){
			echo $this -> _oTemplate -> getInfoBlock((int)$page ? $page : '');
    	}
    	else
    	{	
    		$aProfileInfo = getProfileInfoDirect($iProfileID);
	   		if (count($aProfileInfo) > 0){
	   		if ($this->isAdmin() && (int)$iProfileID) echo $this -> _oTemplate -> getProfileHistoryBlock($iProfileID, true, $aPageSettings);	
	    	else
	    		echo $this -> _oTemplate -> getProfileHistoryBlock($this -> iUserId, false, $aPageSettings);
	   		}else echo MsgBox(_t('_aqb_points_member_not_found'));
    	}	
          	
        $this -> _oTemplate -> addCss('main.css');
        $this -> _oTemplate -> addJs('main.js');
        $this -> _oTemplate -> pageCode(_t('_aqb_points_title'), false, false);
    }
	
	function deleteHistoryItems(&$aArray, $iMemberId){
	   foreach($aArray['history'] as $k => $v){
		  if ((int)$v) $this -> _oDb -> deleteHistoryItem($v);	
	   }
		$this -> _oDb -> updateProfile($iMemberId);
	}

	
    function actionLeaders($sAction = '', $sSort = 'points_down', $page = 1, $per_page = 10, $photos = 0, $online = 0){
    	if (!$this->isLogged()) {
			$this -> _oTemplate-> pageStart();
            echo MsgBox(_t('_aqb_points_have_to_login'));
			$this -> _oTemplate -> pageCode(_t('_aqb_points_title'), false, false);
			exit;
        }
       
        $aDisplaySettings = array(
    		'page' => (int)$page,
		    'per_page' => !(int)$per_page || (int)$per_page < 0 ? $this -> _oConfig -> getPageNumLeadersPage() : (int)$per_page,
		    'sort' => $sSort,
			'mode' => $sAction ? $sAction : 'simple',
            'with_photos' => (int)$photos,
            'online' => (int)$online
		); 
        
	    $this -> _oTemplate-> pageStart();
       	echo $this -> _oTemplate -> getPointsLeadersPagesBlock($aDisplaySettings);
        $this->_oTemplate->addCss('main.css');
        $this -> _oTemplate -> pageCode(_t('_aqb_points_title'), false, false);
    }

	function actionBuyPoints($points_num = 0, $fPrice = 0.0) {
		if (!$this -> isLogged()) {
            return MsgBox(_t('_aqb_points_have_to_login'));
        }

		$aInfo = array('profile' => $this->iUserId, 'module_id' => $this->_oConfig->getId(), 'price' => $fPrice, 'points' => $points_num);
		$iIDPanding = $this -> _oDb -> createPendingTransaction($aInfo);

		if (!(int)$iIDPanding) return false;
		echo BX_DOL_URL_MODULES . '?r=payment/act_add_to_cart/' . $this -> _oConfig -> getSiteId() . '/' . $this->_oConfig->getId() . '/' . $iIDPanding . '/' . $points_num;
	}
	
	function actionBuyPackage($iPoints = 0) {
		if (!$this -> isLogged() || !(int)$iPoints) return false;
		
		$fPrice = $this -> _oDb -> getPriceByPoints($iPoints);
		
		$aInfo = array('profile' => $this->iUserId, 'module_id' => $this->_oConfig->getId(), 'price' => $fPrice, 'points' => $iPoints, 'type' => 'package');
		$iIDPanding = $this -> _oDb -> createPendingTransaction($aInfo);

		if (!(int)$iIDPanding) return false;
		echo BX_DOL_URL_MODULES . '?r=payment/act_add_to_cart/' . $this -> _oConfig -> getSiteId() . '/' . $this->_oConfig->getId() . '/' . $iIDPanding . '/' . $iPoints;
	}
	
    function serviceGetItems($iVendorId){
		$aTransactions = $this -> _oDb ->  getProfileTransactions($iVendorId);
			
		foreach($aTransactions as $k => $v){
		$aResult[] = array(
    	       'id' => $v['id'],
    	       'title' => _t('_aqb_points_number_cart_' . $aTransactions['type']),
    	       'description' => '',
    	       'url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'history/',
    	       'price' => $this -> _oDb -> getPrice($v)
           );
		}   
        
		return  $aResult;  
	}
	
	function serviceGetCartItem($iClientId, $iItemId) {
 		if (!$iItemId || !$iClientId)
            return array();
		
	    $aItem = $this -> _oDb -> getProfileTransactionItem($iClientId, $iItemId);
		if(!count($aItem)) return array();

		return array (
	       'id' => $aItem['id'],
    	   'title' =>  _t('_aqb_points_number_cart_' . $aItem['type']),
    	   'description' => '',
    	   'url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'history/',
    	   'price' => $this -> _oDb -> getPrice($aItem)
         );
 	}
	
	function serviceRegisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrderId) {
		$aResult = array();
		
		$aResult = $this -> _oDb -> pointsBought($iClientId, $iItemId, $iItemCount, $sOrderId);
		
		if ($aResult['params']['type'] == 'exchange'){
			$aProfileInfo = getProfileInfo($aResult['params']['member_id']);
			$aPlus = array(
						'PointsNum' => (int)$aResult['params']['points'],
						'Sum' => (int)$aResult['params']['price'],					
						'PointsHistoryLink' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(). 'history/' 	
					   );
				
			$rEmailTemplate = new BxDolEmailTemplates();
			$aTemplate = $rEmailTemplate -> getTemplate( 't_AqbPointsExchanged') ;
			sendMail( $aProfileInfo['Email'],  $aTemplate['Subject'], $aTemplate['Body'], $aResult['params']['member_id'], $aPlus);
		}
				
    	return $aResult;
	}
	
	function serviceUnregisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrderId) {
		if((int)$iClientId != $this -> iUserId && !$this -> isAdmin())	return false;
		$this -> _oDb -> cancelPointsOrder($iClientId, $iItemId, $iItemCount);
	}
	
	function actionGetPresentPointsForm($iProfileID)
	{
       	if (!$this -> _oConfig -> isPresentFeatureEnabled()) return '';
       	
		if((int)$iProfileID == $this -> iUserId || !$this -> isLogged()) return MsgBox(_t('_aqb_points_have_to_login'));
       	echo $this -> _oTemplate -> getPresentPointsBlock($iProfileID);
        exit;
	}
	
	function actionPresentPoints($iProfileID, $iPointsNum){
		$aResult = array('code' => 1, 'message' => _t('_aqb_points_present_can_not_be_executed'));
		
		if(!(!(int)$iProfileID || (int)$iProfileID == $this -> iUserId || !$this -> isLogged() || !(int)$iPointsNum) && $this -> _oConfig -> isPresentFeatureEnabled() && $this -> _oDb -> presentPoints($this -> iUserId, $iProfileID, $iPointsNum))
		{
			$aResult = array('code' => 0, 'message' => _t('_aqb_points_present_done'));
			
			if ($this -> _oConfig -> isPresentEmailSendEnabled())
			{
				$aProfileInfoR = getProfileInfo($iProfileID);
				$aProfileInfoS = getProfileInfo($this -> iUserId);
				$aPlus = array(
								 'PointsNum' => (int)$iPointsNum,
								 'PointsGiftReason' => '',
								 'PointsSender' => $aProfileInfoS['NickName'],
								 'PointsHistoryLink' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(). 'history/' . $iProfileID 	
								);
				
				$rEmailTemplate = new BxDolEmailTemplates();
				$aTemplate = $rEmailTemplate -> getTemplate( 't_AqbPointsPresent') ;
				sendMail( $aProfileInfoR['Email'],  $aTemplate['Subject'], $aTemplate['Body'], $iProfileID, $aPlus);
			}
		}			
		
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
        echo $oJson->encode($aResult);
		exit;
	}
	
	function actionMembers(){
		if (!$this->isAdmin()) {
            return '';
        }
   
		echo $this->_oTemplate-> getMembersPanel($_REQUEST);
		exit;	
	}
	
	function actionGivePointsFrom($iProfileID, $action = 'give'){
		if (!$this->isAdmin()) {
            return '';
        }
   	   	
        echo $this -> _oTemplate -> getGivePointsBlock($iProfileID, $action);
        exit;
	}
	
    function actionGivePoints($sAction, $iProfileID, $iPointsNum){
		$aResult = array('code' => 1, 'message' => _t('_aqb_points_present_can_not_be_executed'));
		$rEmailTemplate = new BxDolEmailTemplates();
		
		if(!(!(int)$iProfileID || !$this->isAdmin() || !(int)$iPointsNum))
		{
			if ($sAction == 'give') 
			{		
					$aTemplate = $rEmailTemplate -> getTemplate('t_AqbPointsPresent') ;
					$sTitle = '_aqb_points_admin_gave_bonus';
					$iPoints = (int)$iPointsNum;
			}		 
			else 
			{		
					$aTemplate = $rEmailTemplate -> getTemplate('t_AqbPointsPenalized');
					$sTitle = '_aqb_points_admin_gave_penalty';
					$iPoints = -(int)$iPointsNum;
			}		  
			
			$aAction = array('title' => (strlen($_POST['reason']) ? $_POST['reason'] : $sTitle), 'points' => $iPoints, 'id' => 0);
			
			if ($this -> _oDb -> assignPoints($aAction, $iProfileID))
			{	
				$aResult = array('code' => 0, 'message' => _t('_aqb_points_present_done'));
				$aProfileInfo = getProfileInfo($iProfileID);
				
				if (($this -> _oConfig -> isPresentEmailSendEnabled() && $sAction == 'give') || ($this -> _oConfig -> isPenaltyEmailSendEnabled() && $sAction != 'give'))
				{
					$aPlus = array(
							 'PointsNum' => (int)$iPoints,
							 'PointsGiftReason' => strlen($aAction['title']) ? '('. _t($aAction['title']).')' : '',
							 'PointsSender' => _t('_aqb_points_administration_title'),
							 'PointsHistoryLink' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(). 'history/' . $iProfileID 	
							);
					sendMail( $aProfileInfo['Email'],  $aTemplate['Subject'], $aTemplate['Body'], $iProfileID, $aPlus);
				}	
			}	
		}
				
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
        echo $oJson->encode($aResult);
		exit;
	}
	
	function actionHistoryClean($iProfileID){
		$aResult = array('code' => 1, 'message' => _t('_aqb_points_can_not_be_clean'));
		if((int)$iProfileID || $this->isAdmin())
		{
			if ($this -> _oDb -> deleteProfileHistory($iProfileID)) $aResult = array('code' => 0, 'message' => _t('_aqb_points_history_cleaned'));
		}
				
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;
	}
	
	function actionCleanAllHistory(){
		$aResult = array('code' => 1, 'message' => _t('_aqb_points_was_not_clean'));
		
		if($this->isAdmin())
		{
			$this -> _oDb -> cleanHistory();
			$aResult = array('code' => 0, 'message' => _t('_aqb_points_history_cleaned'));
		}	
				
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;
	}
	
	function actionExchangePoints($iLevelID){
		if (!(int)$iLevelID) return false;
		
		if(!$this -> isLogged()) $aResult = array('code' => 1, 'message' => _t('_aqb_points_have_to_login'));
		else{
			 $iResult = $this -> _oDb -> exchangePointsToMemLevel($this -> iUserId, $iLevelID);
			 if (!$iResult) $aResult = array('code' => 2, 'message' => _t('_aqb_points_not_enouth_to_exchange'));
			 else 
				$aResult = array('code' => 0, 'message' => _t('_aqb_points_successfully_exchange_to_memlevel', $iResult));		

		}	

		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;
	}
	
	function actionDeleteAction($iID){
		if (!(int)$iID) return false;
		
		if(!$this -> isAdmin()) return false;
		else{
			 $iResult = $this -> _oDb -> deleteAction((int)$iID);
			 if (!$iResult) $aResult = array('code' => 1, 'message' => _t('_aqb_points_admin_action_delete_not_done'));
			 else 
				$aResult = array('code' => 0, 'message' => _t('_aqb_points_admin_action_delete_done'));		

		}	

		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;
	}
	
	function actionDisableModule($sName){
		if (!$sName) return false;
				
		if(!$this -> isAdmin()) return false;
		else{
			 $iResult = $this -> _oDb -> disableModule($sName);
			 if (!$iResult) $aResult = array('code' => 1, 'message' => _t('_aqb_points_admin_disable_module_not_done'));
			 else 
				$aResult = array('code' => 0, 'message' => _t('_aqb_points_admin_disable_module_done', _t('_aqb_module_title_'.$sName)));		

		}	

		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;
	}
	
	function actionShowIncrementInfo(){
		echo $this -> _oTemplate -> getIncrementInfoBlock(); 
		exit;
	}
	
	function actionMembership(){
		if(!$this -> isLogged())
    	{
		    $this -> _oTemplate-> pageStart();
            echo MsgBox(_t('_aqb_points_have_to_login'));
			$this -> _oTemplate -> pageCode(_t('_aqb_points_title'), false, false);
			exit;
		}
		
		$oMembershipPoints = new AqbPointsMembershipPage($this);

		$this -> _oTemplate-> pageStart();
		echo $oMembershipPoints -> getCode();
        $this -> _oTemplate -> addCss('main.css');
        $this -> _oTemplate -> addJs('main.js');
        $this -> _oTemplate -> pageCode(_t('_aqb_points_title'), false, false);
	}
	
	function getMyMembershipLevel() {
	    return $this -> _oTemplate -> getMyMembershipLevel($this -> iUserId);
	}
	
	function getAvailableLevelsBlock() {
	    if(!$this -> isLogged())
            return MsgBox(_t('_aqb_points_have_to_login'));

        $aMembership = $this -> _oDb -> getMemPrices();
        if(!count($aMembership)) return MsgBox(_t('_aqb_points_nothing_found'));

        return $this -> _oTemplate -> getAvailableMembershipLevels($aMembership);
	}
	
	function actionCreateAction($moduleUri){
	    $aResult = array('code' => 1, 'message' => _t('_aqb_points_action_was_not_added'));
		if(!$this -> isAdmin()) return false;
		else{
			 $aValues = &$_POST;
			 $aValues['module_uri'] = $moduleUri;
			 $iResult = $this -> _oDb -> addAction($aValues);
			 if ($iResult) $aResult = array('code' => 0, 'message' => _t('_aqb_points_action_was_added', _t('_aqb_module_title_'.$moduleUri)));
		}	

		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;
	}
	
	function actionAddActionForm($moduleUri){
		if (!$this->isAdmin()) {
            return '';
        }
   	   	
        echo $this -> _oTemplate -> getAddActionBlock($moduleUri);
        exit;
	}
	
	function serviceGetProfilePointsNum($iProfileId){
		if(!(int)$iProfileId) return 0;
		
		return $this -> _oDb -> getProfileTotalPointsNum($iProfileId);
	}
	
	function serviceAssignPoints($iPointsNum, $sReason = '', $iActionId = 0, $iProfileId = 0){
		if (!(int)$iPointsNum || (!(int)$iProfileId && !(int)$this -> iUserId)) return 0;
		
		$aAction = array('title' => $sReason, 'points' => $iPointsNum, 'id' => $iActionId);
		return $this -> _oDb -> assignPoints($aAction, (int)$iProfileId ? $iProfileId : $this -> iUserId);
	}
	
	function actionRequests(){
		if (!$this->isAdmin()) {
            return '';
        }
   
		echo $this -> _oTemplate -> getRequestsPanel($_REQUEST);
		exit;	
	}
	
	function actionPayForm($iTransactionID){
	  if (!$this -> isAdmin() || !$iTransactionID) return '';
	  
	  $sContent = $this -> _oTemplate -> getPayManuallyForm($iTransactionID);	  
	  echo PopupBox('aqb_popup', _t('_aqb_points_txt_exchange_process_manually'), $sContent);
	  exit;
	}
	
	function serviceLevelsAccountPageBlock(){
		if (!$this -> isLogged() || !$this -> _oConfig -> isLevelsAccountBlockEnabled()) return '';
		
		$this -> _oTemplate -> addCss('main.css');
		$this -> _oTemplate -> addJs('main.js');
		return $this -> _oTemplate -> getAccountLevelsBlock($this -> iUserId);	
	}
	
	function serviceLevelsProfilePageBlock($iProfile){
		if (!(int)$iProfile || !$this -> _oConfig -> isLevelsProfileBlockEnabled()) return '';
		
		$this -> _oTemplate -> addCss('main.css');
		$this -> _oTemplate -> addJs('main.js');
		return $this -> _oTemplate -> getProfileLevelsBlock($iProfile);	
	}
	
	function actionGetLevelsInfo(){
		if (!$this -> isLogged()) return false;
		echo $this -> _oTemplate -> getLevelsInfoTable(); 
		exit;
	}
	
}
?>