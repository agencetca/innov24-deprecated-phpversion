<?
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

bx_import('BxDolTwigModuleDb');
 
/*
 * Credit module Data
 */
class BxCreditDb extends BxDolTwigModuleDb {	

	var $_oConfig;
 	var $iStandardLevel;

	/*
	 * Constructor.
	 */
	function BxCreditDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);
		$this->_oConfig = $oConfig;
 
	}
  
 	function getID ($sNick) {  
		$iId = $this->getOne("SELECT `ID` FROM `Profiles` WHERE `NickName`='$sNick' LIMIT 1");
		
		return $iId;
	}

	function allocateCredits($iId, $iCredits) {
		if($iCredits>0) { 
			$this->assignCredits($iId, "site_donation", "add", "add", time(), $iCredits); 
		}else{
			$this->assignCredits($iId, "site_donation", "subtract", "subtract", time(), abs($iCredits)); 
		} 

		//$iSenderId = getLoggedId(); 
		//$this->notifyDonation($iSenderId, $iId, $iCredits);
	}

	function transferMemberCredits($iSenderId, $iRecipientId, $iCredits) {
	 
		$this->assignCredits($iRecipientId, "member_donation", "add", "add", time(), $iCredits); 

		$this->assignCredits($iSenderId, "member_donation", "subtract", "subtract", time(), abs($iCredits)); 
		
 		$oAlert = new BxDolAlerts('modzzz_credit', 'add', $iRecipientId, $iSenderId, array('credits' => $iCredits));
		$oAlert->alert();
 
		$this->notifyDonation($iSenderId, $iRecipientId, $iCredits); 
	}

	function notifyDonation($iSenderId, $iRecipientId, $iCredits) {
	 
	    $aProfile = getProfileInfo($iRecipientId);
	    $sEmail = $aProfile['Email'];
 
		$oEmailTemplate = new BxDolEmailTemplates(); 
		$aTemplate = $oEmailTemplate->getTemplate('modzzz_credit_donate_notify', $iRecipientId);
		$sMessage = $aTemplate['Body'];
		$sSubject = $aTemplate['Subject'];
    
		$aPlus = array();
		$aPlus['Credits'] = $iCredits;
		$aPlus['SenderName'] = getNickName($iSenderId); 
 		$aPlus['SenderUrl'] = getProfileLink($iSenderId); 
 		$aPlus['NickName'] = getNickName($iRecipientId); 
  		$aPlus['SiteName'] = $GLOBALS['site']['title'];
		$aPlus['SiteUrl'] = $GLOBALS['site']['url'];
 
		$sSubject = str_replace("<SiteName>", $aPlus['SiteName'], $sSubject);
	 
		return sendMail( $sEmail, $sSubject, $sMessage, $iRecipientId, $aPlus, "html" ); 
 	}
 
 	function getCreditActions($bActiveOnly=true, $bOnlyStandard=false) {
		
		$sWhere = '';
		if($bActiveOnly)
			$sWhere .= " AND `active`=1 ";
		
		if($bOnlyStandard) 
			$sWhere .= " AND `action_type`!='system'";
 
		$arrActions = $this->getAll("SELECT * FROM `" . $this->_sPrefix . "main` WHERE 1 {$sWhere} ORDER BY `group`");
	 
		return $arrActions;
	}
   
 	function getMembershipLevels() { 
		$aLevels = $this->getAll("SELECT `ID`, `Name` FROM `sys_acl_levels` WHERE `Active`=1 AND `Purchasable`='yes'  ORDER BY `ID` ASC");
	 
		return $aLevels;
	}

  
	/**
	 * Get pricing options for the given membership
	 *
	 * @param int $membershipID	- membership to get prices for
	 *
	 * @return array( days1 => price1, days2 => price2, ...) - if no prices set, then just array()
	 *
	 *
	 */
	function getMembershipPrices($membershipID)
	{
		$membershipID = (int)$membershipID;
  
		$aMemLevelPrices = $this->getAll("SELECT `id`, `Days`, `Price` FROM `sys_acl_level_prices` WHERE `IDLevel` = $membershipID ORDER BY Days ASC");
 
		return $aMemLevelPrices;
	}
  
	function getAutoLevelName($iLevel) {
		$sName = $this->getOne("SELECT `Name` FROM `sys_acl_levels` WHERE `ID` ='$iLevel'");
		
		return $sName;
	}
 
	function getLevelOptions($iSearchLevel) {
		$arrLevels = $this->getLevels();
		
		$sOptions = "";
		foreach($arrLevels as $aEachLevel) {
			$iKey = $aEachLevel['id'];
			$sVal = $aEachLevel['name']; 
			$sSelected = ($iKey==$iSearchLevel) ? "selected='selected'" : "";
			$sOptions .= "<option value='{$iKey}' {$sSelected}>{$sVal}</option>"; 
		}
 
		return $sOptions;
	}

 
	function addLevelActions() { 
		$arrActions = $this->getCreditActions(false);

		foreach ($arrActions as $aEachAction)
		{
			$sGroup = $aEachAction['group'];
			$sUnit = $aEachAction['unit'];
			$sAction = $aEachAction['action'];
			$sDesc = $aEachAction['desc']; 
			$sValue = (double)getParam("modzzz_credit_defvalue");
			$sActionType = $aEachAction['action_type'];
			$sActive = $aEachAction['active'];
		
			$this->query("INSERT INTO `" . $this->_sPrefix . "main` SET 
 			`group` = '$sGroup',
			`unit` = '$sUnit',
			`action` = '$sAction',
			`value` = '$sValue',
			`desc` = '$sDesc',
			`action_type` = '$sActionType', 
			`active` = '$sActive'  
			");  
		}
	}
 
 	function getCreditHistoryTotals($iMemberID) { 
		
		$totalEarned = 0;
		$totalRedeemed = 0;

		$historyArr = array(); 
		$sQuery = "SELECT SUM(h.`credits_earned`) as `credits_earned`, SUM(h.`credits_redeemed`) as `credits_redeemed`, h.`action_id`, h.`action_type`, m.`unit`, m.`desc` FROM `" . $this->_sPrefix . "history` h, `" . $this->_sPrefix . "main` m WHERE m.`id`=h.`action_id` AND `member_id` = '".$iMemberID. "' GROUP BY h.`action_id`, h.`action_type`"; 
	 
		$rHistory = db_res( $sQuery ); 
		while ( $aHistory = mysql_fetch_assoc( $rHistory )) {
		    $iActionID = $aHistory['action_id'];
			if($aHistory['action_type']=='add'){
				$historyArr[$iActionID] = (int)$historyArr[$iActionID] + (int)$aHistory['credits_earned'];
				$totalEarned += (int)$aHistory['credits_earned']; 
			
				$historyArr[$iActionID] = (int)$historyArr[$iActionID] + (int)$aHistory['credits_redeemed'];
				$totalRedeemed += (int)$aHistory['credits_redeemed']; 		
			}else{ 
				$historyArr[$iActionID] = (int)$historyArr[$iActionID] - (int)$aHistory['credits_earned'];
				$totalEarned -= (int)$aHistory['credits_earned']; 

				$historyArr[$iActionID] = (int)$historyArr[$iActionID] + (int)$aHistory['credits_redeemed'];
				$totalRedeemed += (int)$aHistory['credits_redeemed']; 
			}
		}	
		
		$arrTotals = array(); 
		$arrTotals[0] = $totalEarned;
		$arrTotals[1] = $totalRedeemed;

		return $arrTotals;
	}
 
 	function getCreditHistory($iMemberID) { 
 
		$arrActions = $this->getAll("SELECT SUM(h.`credits_earned`) as `credits_earned`, SUM(h.`credits_redeemed`) as `credits_redeemed`, h.`action_id`, h.`action_type`, m.`group`, m.`unit`, m.`desc` FROM `" . $this->_sPrefix . "history` h, `" . $this->_sPrefix . "main` m WHERE m.`id`=h.`action_id` AND `member_id` = '".$iMemberID. "' GROUP BY h.`action_id`, h.`action_type`"); 
   
		return $arrActions;
	}
 
	//get credits for a member
 	function getMemberCredits($iMemberID) { 
		$iCredits = $this->getOne("SELECT `credits` FROM `" . $this->_sPrefix . "credits` WHERE  `member_id`='$iMemberID'");
	 
		return $iCredits;
	}
   
	function getActionInfo($iActionId){
 		$aRow = $this->getRow("
				SELECT `group`,`unit`,`action`,`value`, `desc` FROM `" . $this->_sPrefix . "main`  
				WHERE `id`='$iActionId'" );
		
		return $aRow;
	}
   
	function getActionValue($sUnit, $sAction){
		  
		$iValue = $this->getOne("
					SELECT `value` FROM `" . $this->_sPrefix . "main`  
					WHERE `unit`='$sUnit' AND `action`='$sAction' AND `active`=1 LIMIT 1"); 
	   
		 return $iValue;
    }

	function actionPerformed($sUnit, $sAction, $iEntryId){
		 
		$iActionId = $this->getOne("
					SELECT `id` FROM `" . $this->_sPrefix . "main`  
					WHERE `unit`='$sUnit' AND `action`='$sAction' LIMIT 1"); 
 
		$bExists = $this->getOne("
					SELECT `id` FROM `" . $this->_sPrefix . "history`  
					WHERE `action_id`='$iActionId' AND `entry_id`='$iEntryId' LIMIT 1"); 
	   
		 return $bExists;
    }
 

	function actionProfileAccessed($sUnit, $sAction, $iMemberId, $iEntryId){
		 
		$iActionId = $this->getOne("
					SELECT `id` FROM `" . $this->_sPrefix . "main`  
					WHERE `unit`='$sUnit' AND `action`='$sAction' LIMIT 1"); 
 
		$bExists = $this->getOne("
					SELECT `id` FROM `" . $this->_sPrefix . "history`  
					WHERE `action_id`='$iActionId' AND `member_id`='$iMemberId' AND `entry_id`='$iEntryId' LIMIT 1"); 
	   
		 return $bExists;
    }


	function assignCredits($iMemberID, $sUnit, $sAction, $sActionType="add", $iEntryId=0, $bAdminCredits=0, $iMultiplier=1){
		 
		 if(getParam("modzzz_credit_activated") != "on")
			return;
  
		 $iEntryId = ($iEntryId) ? $iEntryId : time();
 
		 $iMemberID = (int)$iMemberID;
		 if(!$iMemberID)
			return;
	  
 
		$iActionID = $this->getOne("
					SELECT `id` FROM `" . $this->_sPrefix . "main`  
					WHERE `unit`='$sUnit' AND `action`='$sAction' LIMIT 1"); 
	 
 
		 if(!$iActionID)
			return;

		 if(($sAction == 'add') && (!$bAdminCredits))
		 {
			 $bExists = $this->getOne("
				SELECT COUNT(`entry_id`) FROM `" . $this->_sPrefix . "history`  WHERE `entry_id`='$iEntryId' AND `action_id`='$iActionID'" );  
 
			 if($bExists)
				return;
		 }
  
		 $rCredits = db_res("SELECT * FROM `" . $this->_sPrefix . "credits` WHERE 
		                `member_id`='$iMemberID'");
	  
		 if($aCredits = mysql_fetch_array($rCredits)){ 
			$iPresentCredits = (int)$aCredits['credits'];
 		 }else{
			$new = true;
			$iPresentCredits = 0;
 		 }
 
		 if(!$bAdminCredits) { 
			$arrCreditsAdmin = $this->getRow("
						SELECT `active`, `value` FROM `" . $this->_sPrefix . "main`  
						WHERE `id`='$iActionID'"); 

			$iActive = (int)$arrCreditsAdmin['active'];
			if(!$iActive)
				return false; 
						
			$iActionCredits = (int)$arrCreditsAdmin['value'] * $iMultiplier;
		 }else{
			$iActionCredits = $bAdminCredits;
		 }
 
		 if($sActionType=='add') {
			$iCredits = $iActionCredits + $iPresentCredits; 
 		 }else{
			$iCreditsDiff = $iPresentCredits - $iActionCredits;
			$iCredits = ($iCreditsDiff > 0 ) ? $iCreditsDiff : 0;
		 }

		 if($new) {
			$this->query("
				INSERT INTO `" . $this->_sPrefix . "credits` SET `credits`='$iCredits' 
				,`member_id`='$iMemberID' {$sExtraSQL}" ); 
		 }else{
			$this->query("
				UPDATE `" . $this->_sPrefix . "credits` SET `credits`='$iCredits' {$sExtraSQL} 
				WHERE `member_id`=$iMemberID" ); 
		 }
		
		if($sActionType=='add') {
			 $this->query("
				INSERT INTO `" . $this->_sPrefix . "history`  SET `action_id`='$iActionID', `member_id`='$iMemberID', `credits_earned`='$iActionCredits', `action_type`='$sActionType', `entry_id`='$iEntryId', `date`=now()" ); 
		}else{
			 $this->query("
				INSERT INTO `" . $this->_sPrefix . "history`  SET `action_id`='$iActionID', `member_id`='$iMemberID', `credits_redeemed`='$iActionCredits', `action_type`='$sActionType', `entry_id`='$iEntryId', `date`=now()" );  
		} 
	} 
 
    function saveTransactionRecord($iBuyerId, $iCredits, $sTransID, $sTransType) {
        $iBuyerId        = (int)$iBuyerId;
        $iCredits           = (int)$iCredits; 
   
        $res = $this->query("INSERT INTO `" . $this->_sPrefix . "paypal_trans` SET `buyer_id` = {$iBuyerId}, `trans_id` =  '{$sTransID}', `created`  = UNIX_TIMESTAMP(),        `trans_type`  = '{$sTransType}', `credits` = {$iCredits}
        "); 

		return $res;
    }

    function isExistPaypalTransaction($iBuyerId, $sTransID) {
        $iBuyerId  = (int)$iBuyerId;
 
        return $this->getOne("SELECT COUNT(`trans_id`) FROM `" . $this->_sPrefix . "paypal_trans` 
            WHERE `buyer_id` = {$iBuyerId} AND `trans_id` =  '{$sTransID}'  
        "); 
    }
 
	function getTotalCreditsBought($iBuyerId, $sMode='all') {
	   $iBuyerId = (int)$iBuyerId;
        
		$sqlCondition = " WHERE `buyer_id` = {$iBuyerId}";

        // top menu and sorting
        $aModes = array('today', 'week', 'month');
      
        foreach( $aModes as $sMyMode ) {
            switch ($sMyMode) {
                case 'today':
                    if ($sMode == $sMyMode) {
                        $sqlCondition .= " AND MONTH(FROM_UNIXTIME(`" . $this->_sPrefix . "paypal_trans`.`created`)) = MONTH(NOW()) AND DAYOFMONTH(FROM_UNIXTIME(`" . $this->_sPrefix . "paypal_trans`.`created`)) = DAYOFMONTH(NOW())";
                     } 
                break;
                case 'week':
                    if ($sMode == $sMyMode){
                        $sqlCondition .= " AND MONTH(FROM_UNIXTIME(`" . $this->_sPrefix . "paypal_trans`.`created`)) = MONTH(NOW()) AND WEEK(FROM_UNIXTIME(`" . $this->_sPrefix . "paypal_trans`.`created`)) = WEEK(NOW())"; 
 					} 
                break;
                 case 'month':
                    if ($sMode == $sMyMode){
                       $sqlCondition .= " AND MONTH(FROM_UNIXTIME(`" . $this->_sPrefix . "paypal_trans`.`created`)) = MONTH(NOW())"; 
 					}         
                break;
            } 
        }
 
       return (int)$this->getOne("SELECT SUM(`credits`) FROM `" . $this->_sPrefix . "paypal_trans`  $sqlCondition"); 
	}

	function getCreditTransactions($iBuyerId, $sMode='all') {
	    $iBuyerId = (int)$iBuyerId;
     
		$iLimit = (int)getParam( "modzzz_credit_trans_perpage_browse" );
    
		// possible conditions 
        $sqlCondition = " WHERE `buyer_id` = {$iBuyerId}";
         
		$sqlOrder = "ORDER BY  `" . $this->_sPrefix . "paypal_trans`.`created` DESC";
        
        // top menu and sorting
        $aModes = array('today', 'week', 'month');
      
        foreach( $aModes as $sMyMode ) {
            switch ($sMyMode) {
                case 'today':
                    if ($sMode == $sMyMode) {
                        $sqlCondition .= " AND MONTH(FROM_UNIXTIME(`" . $this->_sPrefix . "paypal_trans`.`created`)) = MONTH(NOW()) AND DAYOFMONTH(FROM_UNIXTIME(`" . $this->_sPrefix . "paypal_trans`.`created`)) = DAYOFMONTH(NOW())";
                     } 
                break;
                case 'week':
                    if ($sMode == $sMyMode){
                        $sqlCondition .= " AND MONTH(FROM_UNIXTIME(`" . $this->_sPrefix . "paypal_trans`.`created`)) = MONTH(NOW()) AND WEEK(FROM_UNIXTIME(`" . $this->_sPrefix . "paypal_trans`.`created`)) = WEEK(NOW())"; 
 					} 
                break;
                 case 'month':
                    if ($sMode == $sMyMode){
                       $sqlCondition .= " AND MONTH(FROM_UNIXTIME(`" . $this->_sPrefix . "paypal_trans`.`created`)) = MONTH(NOW())"; 
 					}         
                break;
            } 
        }
 
        $iCount = (int)$this->getOne("SELECT COUNT(`id`) FROM `" . $this->_sPrefix . "paypal_trans` $sqlCondition");
 
        $aData = array();
        $sPaginate = '';
        if ($iCount) {
         
            $iPages = ceil($iCount/ $iLimit);
            $iPage = (int)$_GET['page'];
            if ($iPage < 1)
                $iPage = 1;
            if ($iPage > $iPages)
                $iPage = $iPages;

            $sqlFrom = ($iPage - 1) * $iLimit;
            $sqlLimit = "LIMIT $sqlFrom, $iLimit";
            
			$sqlQuery = "SELECT `credits`, `trans_id`, `trans_type`, `created` FROM `" . $this->_sPrefix . "paypal_trans` $sqlCondition $sqlOrder $sqlLimit"; 
			$aTransactions = $this->getAll($sqlQuery);
 

			$sPageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'transactions/';

            if ($iPages > 1) { 
                $oPaginate = new BxDolPaginate(array(
                    'view_all' => false,
					'page_url' => $sPageUrl,
 					'count' => $iCount,
                    'per_page' => $iLimit,
                    'page' => $iPage,
                    'per_page_changer' => true,
                    'page_reloader' => true, 
                    'on_change_page' => 'return !loadDynamicBlock({id}, \''.$sPageUrl.'?&filter='.$sMode.'&page={page}&per_page={per_page}\');',
                    'on_change_per_page' => ''
                ));
				$sPaginate = $oPaginate->getSimplePaginate('', -1, -1, false); 
            } 
			return array('paginate'=>$sPaginate, 'transactions'=>$aTransactions); 
		}else{
			return array('paginate'=>'', 'transactions'=>array());  
		}
 
    }

    function getMembershipLevelPrice($iLevelID) {
        return (int)$this->getOne("SELECT `Price` FROM `sys_acl_level_prices` WHERE `id`='{$iLevelID}'");
    }

    function getMembershipLevelInfo($iLevelID) {
   
		$sQuery = "
            SELECT `ID` AS `LevelID`, `Name` AS `LevelName`, `Icon`, `Description`  
            FROM `sys_acl_levels` 
            WHERE  `ID` = '$iLevelID'
        ";
        return $this->getRow($sQuery);
    }

    function getMembershipLevelPrices() {
   
		$sQuery = "
            SELECT `sal`.`ID` AS `LevelID`, `sal`.`Name` AS `LevelName`, `sal`.`Icon`, `sal`.`Description` , `salp`.`id` AS `PriceID` , `salp`.`Days`, `salp`.`Price` 
            FROM `sys_acl_levels` AS `sal` 
            INNER JOIN `sys_acl_level_prices` AS `salp` ON `sal`.`ID` = `salp`.`IDLevel` 
            WHERE `sal`.`Active` = 'yes'
            AND `sal`.`Purchasable` = 'yes'  
        ";
        return $this->getAll($sQuery);
    }

    /**begin- package functions **/  
	function getAllPackages() {
		
 		$aPackage = $this->getAll("SELECT `id`, `price`, `credits` FROM `" . $this->_sPrefix . "packages` ORDER BY `credits` ASC"); 
  
		return $aPackage;
	}

	function getPackageById($iId) {
		
 		$aPackage = $this->getRow("SELECT `id`, `price`, `credits` FROM `" . $this->_sPrefix . "packages` WHERE `id`='$iId' LIMIT 1"); 
  
		return $aPackage;
	}
  
	function getPackageList() {
		
 		$aPackages = $this->getAll("SELECT `id`, `price`, `credits` FROM `" . $this->_sPrefix . "packages` ORDER BY `credits` ASC"); 
 
		$arr = array();
 		foreach($aPackages as $aEachPackage){
			$iPrice = $aEachPackage['price'];
			$iCredits = $aEachPackage['credits'];
			$sDesc = $iCredits.' '._t('_modzzz_credits_credits') .' - '.   html_entity_decode($this->_oConfig->getCurrencySign(), ENT_COMPAT, 'UTF-8') . $iPrice;

			$arr[$iPrice.'|'.$iCredits] = $sDesc;
		}

		return $arr;
	}
 
	function getPackagePrice($iPackageId){
	
		$iPrice = $this->getOne("SELECT `price` FROM `" . $this->_sPrefix . "packages` WHERE `id`='$iPackageId'");
		
		return $iPrice;
	}
 
 	function getPackages(){
	 
 		$aAllEntries = $this->getAll("SELECT `id`, `price`, `credits` FROM `" . $this->_sPrefix . "packages`  ORDER BY `credits` ASC"); 
		
		return $aAllEntries; 
	}
	 
	function SavePackage($iParent=0){
	  
		$fPrice = (float)process_db_input($_POST['package_price']);
		$iCredits = (int)process_db_input($_POST['package_credits']);	 
 
		if(!($fPrice && $iCredits)){
			return false;
		}
	 
		$this->query("INSERT INTO `" . $this->_sPrefix . "packages` SET `price`=$fPrice, `credits`=$iCredits");
	 
		return true;
	}
	  
	function UpdatePackage(){  
	
		$iId = process_db_input($_POST['id']); 
		$fPrice = (float)process_db_input($_POST['package_price']);
		$iCredits = (int)process_db_input($_POST['package_credits']);	 
 		
		if(!($fPrice && $iCredits)){
			return false;   
		}
	 
		return $this->query("UPDATE `" . $this->_sPrefix . "packages` SET `price`=$fPrice, `credits`=$iCredits WHERE `id`=$iId");
	}
	 
	function DeletePackage(){ 
		$iId = process_db_input($_POST['id']);
	 
		return $this->query("DELETE FROM `" . $this->_sPrefix . "packages` WHERE `id`='$iId'"); 
	}
    /** end - package functions **/
 

	function validAction($sPrefix, $sType=''){ 

		$bValid = false;

		if($sType=='add'){
			switch($sPrefix){
				case 'ads':
					$bValid = (getParam('modzzz_credit_ad_add_activated')=='on');
				break;
				case 'bx_events':
					$bValid = (getParam('modzzz_credit_event_add_activated')=='on');
				break;
				case 'bx_sites':
					$bValid = (getParam('modzzz_credit_site_add_activated')=='on');
				break; 
				case 'bx_videos':
					$bValid = (getParam('modzzz_credit_video_add_activated')=='on');
				break;
				case 'bx_photos': 
					$bValid = (getParam('modzzz_credit_photo_add_activated')=='on');
				break;
				case 'bx_files':
					$bValid = (getParam('modzzz_credit_file_add_activated')=='on');
				break;
				case 'bx_sounds':
					$bValid = (getParam('modzzz_credit_sound_add_activated')=='on');
				break; 
			}
		}elseif($sType=='view'){
			switch($sPrefix){
				case 'profile':
					$bValid = (getParam('modzzz_credit_profile_view_activated')=='on');
				break; 
				case 'bx_videos':
					$bValid = (getParam('modzzz_credit_video_view_activated')=='on');
				break;
				case 'bx_photos':
					$bValid = (getParam('modzzz_credit_photo_view_activated')=='on');
				break;
				case 'bx_files':
					$bValid = (getParam('modzzz_credit_file_view_activated')=='on');
				break;
				case 'bx_sounds':
					$bValid = (getParam('modzzz_credit_sound_view_activated')=='on');
				break; 
			}
		}

		return $bValid;
	}
  
	function paidGender($iProfileId){ 

		$sFreeGender = getParam('modzzz_credit_free_gender');
		
		if($sFreeGender=='none')
			return true;
 			
		$aProfileInfo = getProfileInfo($iProfileId);
		$iCouple = (int)$aProfileInfo['Couple'];
		$sSex = $aProfileInfo['Sex'];
		if(($sFreeGender=='male') && ($sSex=='male')){
			return false;			
		} 
		if(($sFreeGender=='female') && ($sSex=='female')){
			return false;			
		}
		if(($sFreeGender=='couple') && ($iCouple)){
			return false;			
		}

		return true;	 
	}
  
	function markAsViewed($iFileId, $iProfileId, $sPrefix){
		$iFileId = (int)$iFileId;
		$iProfileId = (int)$iProfileId; 
		$iCreated = time();
	    $SECONDS_IN_DAY = 86400; 

	    $iActiveDays = (int)getParam('modzzz_credit_view_duration');
 
		$iExpire = ($iActiveDays) ? $iCreated + ($SECONDS_IN_DAY * $iActiveDays) : 0;
 
		return $this->query("INSERT INTO `" . $this->_sPrefix . "views` SET `file_id`=$iFileId,  `viewer_id`=$iProfileId, `type`='$sPrefix', `date`=$iCreated, `expire`=$iExpire"); 
	}
 
	function processViews(){
		$iExpire = time();
 
		return $this->query("DELETE FROM `" . $this->_sPrefix . "views` WHERE `expire` > 0 AND `expire` < $iExpire"); 
	}
 
	function isViewAllowed($iFileId, $iProfileId, $sPrefix){
		$iFileId = (int)$iFileId;
		$iProfileId = (int)$iProfileId; 
 
		return $this->getOne("SELECT COUNT(`id`) FROM `" . $this->_sPrefix . "views` WHERE  `file_id`=$iFileId AND `viewer_id`=$iProfileId AND `type`='$sPrefix'"); 
	}
 
	function checkAddAccess($iOwnerId, $sPrefix, $sAction){
   
 		$bValid = $this->paidGender($iOwnerId) && $this->validAction($sPrefix, 'add');
 		if($bValid){
	  
			switch($sPrefix){
				case 'ads':
					$sAddMessage = '_modzzz_credit_insuffient_add_ad';
 				break;
				case 'bx_events':
					$sAddMessage = '_modzzz_credit_insuffient_add_event';
 				break;
				case 'bx_sites':
					$sAddMessage = '_modzzz_credit_insuffient_add_site';
 				break; 
				case 'bx_videos':
					$sAddMessage = '_modzzz_credit_insuffient_add_video';
 				break;
				case 'bx_photos':
					$sAddMessage = '_modzzz_credit_insuffient_add_photo';
 				break;
				case 'bx_files':
					$sAddMessage = '_modzzz_credit_insuffient_add_file';
 				break;
				case 'bx_sounds':
					$sAddMessage = '_modzzz_credit_insuffient_add_sound';
 				break; 
			}
   
			$iActionValue = (int)$this->getActionValue($sPrefix, $sAction);
			$iMemberCredits = (int)$this->getMemberCredits($iOwnerId);
	 
			if($iActionValue > $iMemberCredits){

				$sBuyUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_credits';

				$sCode = MsgBox(_t($sAddMessage, $iActionValue, $sBuyUrl)); 
				return $sCode;
			} 
		}
 	}
 
	function checkViewAccess($iFileId, $iViewerId, $iOwnerId, $sPrefix){
 
		if($iViewerId==$iOwnerId)
			return;
		
		if($this->isViewAllowed($iFileId, $iViewerId, $sPrefix))
			return;
 
		$bValid = $this->paidGender($iViewerId) && $this->validAction($sPrefix, 'view');

 		if($bValid){
			 
			if($sPrefix=='profile'){
				$sViewMessage = '_modzzz_credit_insuffient_view_profile';
			}else{
				$sViewMessage = '_modzzz_credit_insuffient_view_media';
			}
   
			$iActionValue = (int)$this->getActionValue($sPrefix, "view");
			$iMemberCredits = (int)$this->getMemberCredits($iViewerId);
	 
			if($iActionValue > $iMemberCredits){

				$sBuyUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_credits';

				$sCode = MsgBox(_t($sViewMessage, $iActionValue, $sBuyUrl)); 
				//$sCode = DesignBoxContent(_t('_modzzz_credit_no_view_access'), MsgBox($sMessage), 1);
				return $sCode;
			}else{
				$this->assignCredits($iViewerId, $sPrefix, 'view', 'subtract');  
				$this->markAsViewed($iFileId, $iViewerId, $sPrefix);
			} 
		}
 	}
 
	function checkMailAccess($iSenderId, $iRecipientId){
 
		//if($this->isViewAllowed($iRecipientId, $iSenderId, 'mail'))
		//	return;

 		$bValid = $this->paidGender($iSenderId);
 		if(getParam('modzzz_credit_mail_view_activated') && $bValid){ 
  
			$iActionValue = (int)$this->getActionValue('mail', "view");
			$iMemberCredits = (int)$this->getMemberCredits($iSenderId);
	 
			if($iActionValue > $iMemberCredits){

				$sBuyUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_credits';

				$sCode = MsgBox(_t('_modzzz_credit_insuffient_view_mail', $iActionValue, $sBuyUrl)); 
				return $sCode;
			}else{  
				$this->assignCredits($iSenderId, 'mail', 'view', 'subtract'); 
				return;
			} 
		}
 	}
 
	function checkSendMailAccess($iSenderId){
  
  		if(isAdmin($iSenderId))
			return;

 		$bValid = $this->paidGender($iSenderId);
 		if(getParam('modzzz_credit_mail_send_activated') && $bValid){ 
  
			$iActionValue = (int)$this->getActionValue('mail', "send");
			$iMemberCredits = (int)$this->getMemberCredits($iSenderId);
	 
			if($iActionValue > $iMemberCredits){

				$sBuyUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_credits';

				$sCode = MsgBox(_t('_modzzz_credit_insuffient_send_mail', $iActionValue, $sBuyUrl)); 
				return $sCode;
			} 
		}
		return;
 	}

	function hasSimpleMessengerAccess($iMemberId){ 
  
  		if(isAdmin($iMemberId))
			return true;

 		$bCheck = $this->paidGender($iMemberId);
 		if(getParam('modzzz_credit_simple_messenger_chat_activated') && $bCheck ){ 
  
			$iActionValue = (int)$this->getActionValue('simple_messenger', "chat");
		 
			$iCredits = (int)$this->getMemberCredits($iMemberId);
			 
			if($iActionValue > $iCredits){  
				return false;
			}   
		}
		return true;
 	}
   
	function checkSimpleMessengerAccess($iSndId=0, $iRspId=0){
  
  		$iSndId = (int)$iSndId;
		$iRspId = (int)$iRspId;
    	
  		if(isAdmin($iSndId) || isAdmin($iRspId) )
			return '';

		if(getParam('modzzz_credit_simple_messenger_chat_activated')!='on'){
			return '';
		}
 
 		$bCheckSender = $this->paidGender($iSndId);
 		$bCheckRecvd = $this->paidGender($iRspId);
 		if(($bCheckSender || $bCheckRecvd)){ 
  
			$iActionValue = (int)$this->getActionValue('simple_messenger', "chat");
			
			if($bCheckSender){
				$iSenderCredits = (int)$this->getMemberCredits($iSndId);
				if($iActionValue > $iSenderCredits){  
					$sBuyUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_credits';
					$sMessage = _t('_modzzz_credit_smessenger_buy_credits', $iActionValue, $sBuyUrl); 
					return $GLOBALS['oBxCreditModule']->_oTemplate->showSimpleMessengerBuy($sMessage); 
				}else{ 
					return $GLOBALS['oBxCreditModule']->_oTemplate->showSimpleMessengerTalk($iRspId, $iActionValue);  
				}
			}

			if($bCheckRecvd){ 
				$iRecvdCredits = (int)$this->getMemberCredits($iRspId);
				if($iActionValue > $iRecvdCredits){   
					$sCode = '&nbsp;'; 
				} 
			} 
		} 

		return '';
 	}

	function hasMessengerAccess($iSndId=0, $iRspId=0){
  
  		$iSndId = ($iSndId) ? $iSndId : (int)$_GET['sender'];
		$iRspId = ($iRspId) ? $iRspId : (int)$_GET['recipient'];

    	
  		if(isAdmin($iSndId) || isAdmin($iRspId) )
			return '';

		if(getParam('modzzz_credit_messenger_chat_activated')!='on'){
			return '';
		}

		if($_GET['accept']=='no'){
			return "<script>window.close();</script>";
		}

		if($_GET['accept']=='yes'){
			$this->assignCredits($iSndId, 'messenger', 'chat', 'subtract');   
			return '';
		}

 		$bCheckSender = $this->paidGender($iSndId);
 		$bCheckRecvd = $this->paidGender($iRspId);
 		if(($bCheckSender || $bCheckRecvd)){ 
  
			$iActionValue = (int)$this->getActionValue('messenger', "chat");
			
			if($bCheckSender){
				$iSenderCredits = (int)$this->getMemberCredits($iSndId);
				if($iActionValue > $iSenderCredits){  
					$sBuyUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_credits';
					$sCode = MsgBox(_t('_modzzz_credit_insuffient_sender_messenger', $iSenderCredits, $iActionValue, $sBuyUrl)); 
					return $GLOBALS['oBxCreditModule']->_oTemplate->showMessage($sCode); 
				}else{

					$sAcceptUrl = $_SERVER['REQUEST_URI'].'&accept=yes';
					$sRejectUrl = $_SERVER['REQUEST_URI'].'&accept=no';

					$sCode = MsgBox(_t('_modzzz_credit_your_credits', $iSenderCredits)); 

					$sCode .= MsgBox(_t('_modzzz_credit_deduct_messenger', $iActionValue, $sAcceptUrl, $sRejectUrl)); 
					return $GLOBALS['oBxCreditModule']->_oTemplate->showMessage($sCode);  
				}
			}

			if($bCheckRecvd){ 
				$iRecvdCredits = (int)$this->getMemberCredits($iRspId);
				if($iActionValue > $iRecvdCredits){   
					$sCode = MsgBox(_t('_modzzz_credit_insuffient_recv_messenger')); 
					return $GLOBALS['oBxCreditModule']->_oTemplate->showMessage($sCode); 
				} 
			} 
		} 

		return '';
 	}
  
	function processMailSending($iSenderId, $iRecipientId){
		if(isAdmin($iSenderId))
			return;

		//if($this->isViewAllowed($iRecipientId, $iSenderId, 'mail'))
		//	return;

 		$bValid = $this->paidGender($iSenderId);
 		if(getParam('modzzz_credit_mail_send_activated') && $bValid){  
			$this->assignCredits($iSenderId, 'mail', 'send', 'subtract');  
			//$this->markAsViewed($iRecipientId, $iSenderId, 'mail'); 
		} 
 	}
  
	function removeProfileEntries($iProfileId) { 
		 $this->query("DELETE FROM `" . $this->_sPrefix . "credits` WHERE `member_id` = '$iProfileId'"); 
 		 $this->query("DELETE FROM `" . $this->_sPrefix . "paypal_trans` WHERE `buyer_id` = '$iProfileId'"); 
 		 //$this->query("DELETE FROM `" . $this->_sPrefix . "memb_levels` WHERE `member_id` = '$iProfileId'"); 
 		 $this->query("DELETE FROM `" . $this->_sPrefix . "views` WHERE `viewer_id` = '$iProfileId'"); 

 		 $this->query("DELETE FROM `" . $this->_sPrefix . "history` WHERE `member_id` = '$iProfileId'"); 
	}
  
	function getMediaType($sUnit){
		switch($sUnit){
			case 'bx_videos':
				$sType = 'videos';
			break;
			case 'bx_photos':
				$sType = 'photos';
			break;
			case 'bx_sounds':
				$sType = 'sounds';
			break;
			case 'bx_files':
				$sType = 'files';
			break;
		}
		
		return $sType;
	}


	function getMembershipsBy($aParams = array()) {
	    $sMethod = "getAll";
	    $sSelectClause = $sJoinClause = $sWhereClause = "";
        if(isset($aParams['type']))
            switch($aParams['type']) {                
                case 'price_id':
                    $sMethod = "getRow";
                    $sSelectClause .= ", `tlp`.`id` AS `price_id`, `tlp`.`Days` AS `price_days`, `tlp`.`Price` AS `price_amount`";
                    $sJoinClause .= "LEFT JOIN `sys_acl_level_prices` AS `tlp` ON `tl`.`ID`=`tlp`.`IDLevel`";
                    $sWhereClause .= " AND `tl`.`Active`='yes' AND `tl`.`Purchasable`='yes' AND `tlp`.`id`='" . $aParams['id'] . "'";
                    break;
                case 'price_all':
                    $sSelectClause .= ", `tlp`.`id` AS `price_id`, `tlp`.`Days` AS `price_days`, `tlp`.`Price` AS `price_amount`";
                    $sJoinClause .= "INNER JOIN `sys_acl_level_prices` AS `tlp` ON `tl`.`ID`=`tlp`.`IDLevel`";
                    $sWhereClause = " AND `tl`.`Active`='yes' AND `tl`.`Purchasable`='yes'";
                    break;
                case 'level_id':
                    $sMethod = "getRow";
                    $sWhereClause .= " AND `tl`.`ID`='" . $aParams['id'] . "'";
                    break;
            }
        
        $sSql = "SELECT
                `tl`.`ID` AS `mem_id`,
                `tl`.`Name` AS `mem_name`,
                `tl`.`Icon` AS `mem_icon`,
                `tl`.`Description` AS `mem_description` " . $sSelectClause . "
            FROM `sys_acl_levels` AS `tl` " . $sJoinClause . "
            WHERE 1" . $sWhereClause;
	   return $this->$sMethod($sSql);
	}
	function getNameProd($iId) {  
        $prod = $this->getOne("SELECT `title` FROM `bx_store_products` WHERE `id` = '$iId' LIMIT 1");
        return $prod;
    }
    function getLastPrice($iRoom, $pid) {
        $sRecipientSQL = 'WHERE `recipient` = 0';
        $sRecipientSQL .= " AND `room` = '{$iRoom}'";
        $sRecipientSQL .= " AND `price` <> ''";
        $sSQL = "
            SELECT `price`
            FROM `a_fchat_messages`
            {$sRecipientSQL}
            ORDER BY `when` DESC
            LIMIT 1 
        ";
       if($this->getOne($sSQL)!=false){
       		return $this->getOne($sSQL);
       }else{
	       	$prod = $this->getOne("SELECT `price_range` FROM `bx_store_products` WHERE `id` = '$pid' LIMIT 1");
	        $prod = str_replace('%s', ' ', $prod);
	        return $prod;
       }
       
    }
    function getLastOrder() {
        $sSQL = "
            SELECT `order`
            FROM `bx_pmt_transactions_pending`
            ORDER BY `order` DESC
            LIMIT 1 
        ";
       return $this->getOne($sSQL);
    }
    function getFileFromPID($pid) {
        $sSQL = "
			SELECT `id`
			FROM `bx_store_product_files`
			WHERE `entry_id` = '{$pid}'
			LIMIT 1
        ";
       return $this->getOne($sSQL);
    }
}

?>