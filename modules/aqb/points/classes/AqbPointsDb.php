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

bx_import('BxDolModuleDb');

class AqbPointsDb extends BxDolModuleDb {	
	/*
	 * Constructor.
	 */
	
	function AqbPointsDb(&$oConfig) {
		parent::BxDolModuleDb($oConfig);
		$this -> _oConfig = &$oConfig;
	}

	function getAlertHandlerID(){
	   return (int)$this -> getOne("SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'aqb_points_assign_alert' LIMIT 1");	
	}
	
	function cancelPointsOrder($iClientId, $iItemId, $iItemCount){
		$aParams = array('points' => -(int)$iItemCount, 'title' => '_aqb_points_you_have_cancel');
	    $iResult = (int)$this -> query("DELETE FROM `{$this->_sPrefix}transactions` WHERE `id` = '{$iItemId}'");
	
		if ($this -> assignPoints($aParams, $iClientId)) return true;
		return false;
	}
	
	function getPrice($aItem){
		switch($aItem['type']){
			case 'package': return (float)$aItem['price']/$aItem['points_num'];
			case 'exchange': return $this -> _oConfig -> priceForExchangeToPoints();			
			default: return $this -> _oConfig -> getPointsPrice();
		}
	}
	
	function pointsBought($iClientId, $iItemId, $iItemCount, $sOrderId){
		$aInfo = $this -> getProfileTransactionItem($iClientId, $iItemId);		
		$aParams = array('price' => $aInfo['price'], 'points' => $iItemCount, 'title' => '_aqb_points_you_have_bought');
        $iTime = time();

		if ($aInfo['type'] == 'exchange'){
			$aParams = array('time' => $iTime, 'price' => $fPrice, 'points' => -$iItemCount, 'title' => _t('_aqb_points_you_have_exchanged_points', $this -> _oConfig -> getCurrencySign() . $fPrice));
			$iClientId =  (int)$aInfo['buyer_id'];
		}					

		$iResult = (int)$this -> query("UPDATE `{$this->_sPrefix}transactions` SET `payment_date` =  '{$iTime}', `status` = 'active',`tnx` = '{$sOrderId}'  WHERE `id` = '{$iItemId}' AND `status` = 'pending'");
		
		if ($iResult && $this -> assignPoints($aParams, $iClientId)) return array('price' => $this -> getPrice($aInfo), 'params' => array('type' => $aInfo['type'], 'member_id' => $iClientId, 'price' => $this -> _oConfig -> getCurrencySign() . $aInfo['price'], 'points' => $iItemCount));
		return false;
	}
	
	function createPendingTransaction(&$aParams){
		if (!(int)$aParams['profile'] || !(int)$aParams['module_id'] || !(int)$aParams['points'] || !(float)$aParams['price']) return false;
		
		if (isset($aParams['type'])) $sType = ", `type` = '{$aParams['type']}'";
		$iResult = $this -> query("INSERT INTO `{$this->_sPrefix}transactions` SET  `price` = '{$aParams['price']}', `date` = UNIX_TIMESTAMP(), `points_num` = '{$aParams['points']}', `buyer_id` = '{$aParams['profile']}' {$sType}	");
		return $this -> lastId();
	} 
	
	function updateProfile($iProfileID){
		if ($this -> query("UPDATE `Profiles` SET `AqbPoints`  = '" . $this -> getProfileTotalPointsNum($iProfileID) . "' WHERE `ID` = '{$iProfileID}'")) @unlink(BX_DIRECTORY_PATH_CACHE . 'user' . $iProfileID . '.php');
	}
	
	function deleteHistoryItem($iId){
		return $this -> query("DELETE FROM `{$this->_sPrefix}history` WHERE `id` = '{$iId}'");
	}
	
	function deleteMemlevelsPricing($iId){
		return $this -> query("DELETE FROM `{$this->_sPrefix}memlevels_pricing` WHERE `id` = '{$iId}'");
	}
	
	function deleteProfileHistory($iId){
		$this -> query("DELETE FROM `{$this->_sPrefix}history` WHERE `profile_id` = '{$iId}'");
		$this -> updateProfile($iId);		
		return true;
	}
	
	function deleteProfileInfo($iId){
		$this -> deleteProfileHistory($iId);
		$this -> deleteProfileTransactions($iId);
	}
	
	function cleanHistory(){
		$this -> query("TRUNCATE TABLE `{$this->_sPrefix}history`");
		$this -> query("UPDATE `Profiles` SET `AqbPoints` = '0'");
	}
	
	function getProfilePointsNum($iProfileId, &$aHistory){
		$sLimit = "";
		
		$iTotal = 0; 
		if ((int)$aHistory['from']){ 
			$sLimit = " ORDER BY `id` DESC  LIMIT {$aHistory['from']}";
			$iTotal = $this -> getProfileTotalPointsNum($iProfileId); 
		}
		
		$sWhere = "";
		if ((int)$aHistory['activities'] > 0 ) $sWhere = " AND `action_id` = '{$aHistory['activities']}'";
		if ((int)$aHistory['days'] > 0) $sWhere .= " AND `time` >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$aHistory['days']} DAY))"; 	 
	
		$iLeft = (int)$this -> getOne("SELECT SUM(`points`) FROM (SELECT `points` FROM `{$this->_sPrefix}history` WHERE `profile_id` = '{$iProfileId}' {$sWhere} {$sLimit}) as `p`");	
		return $iTotal ? $iTotal - $iLeft : $iLeft;
	}
	
	function getProfileTotalPointsNum($iProfileId){
		return (int)$this -> getOne("SELECT SUM(`points`) FROM `{$this->_sPrefix}history` WHERE `profile_id` = '{$iProfileId}' LIMIT 1");
	}
	
	function getTotalPoints24(){
		return (int)$this -> getOne("SELECT SUM(`points`) FROM `{$this->_sPrefix}history` WHERE `time` >= UNIX_TIMESTAMP(SUBDATE(NOW(), INTERVAL 24 HOUR))");
	}
	
	function getTotalPoints(){
		return (int)$this -> getOne("SELECT SUM(`points`) FROM `{$this->_sPrefix}history`");
	}	
		
	
	function getActions($sUri){
		return $this -> getAll("SELECT * FROM `{$this->_sPrefix}actions` WHERE `group` = '{$sUri}' ORDER BY `id` ASC");
	}
	
	function getHistoryCount($iProfileId, &$aHistory){
		$sWhere = "";
		if ((int)$aHistory['activities'] > 0 ) $sWhere = " AND `action_id` = '{$aHistory['activities']}'";
		if ((int)$aHistory['days'] > 0) $sWhere .= " AND `time` >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$aHistory['days']} DAY))"; 	 
	
		return $this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}history` WHERE `profile_id` = '{$iProfileId}' {$sWhere} LIMIT 1");
	}
	
	function getProfileHistory($iProfileId, &$aHistory){
		$sLimit =  "LIMIT {$aHistory['from']}, {$aHistory['to']}";
		
		$sWhere = "";
		if ((int)$aHistory['activities'] > 0 ) $sWhere = " AND `action_id` = '{$aHistory['activities']}'";
		if ((int)$aHistory['days'] > 0) $sWhere .= " AND `time` >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$aHistory['days']} DAY))"; 	 
		
		return $this -> getAll("SELECT `id`, `action_id`, `profile_id`, `reason`, `points`, `time`, DATE_FORMAT(FROM_UNIXTIME(`time`),  '" . $this -> _oConfig-> getDateFormat() . "' ) AS `formated_date` FROM `{$this->_sPrefix}history` WHERE `profile_id` = '{$iProfileId}' {$sWhere}  ORDER BY `id` DESC {$sLimit}");
	}
	
	function getActionsCount($sUri, $bActive = false){
		$sActive = '';
		if ($bActive) $sActive = " AND `active` = 'true'"; 
		return $this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}actions` WHERE `group` = '{$sUri}' {$sActive}");
	} 
	
	function getActionsMaxId(){
		return $this -> getOne("SELECT MAX(`id`) FROM `{$this->_sPrefix}actions` LIMIT 1");
	}
	
	function unactiveActions(){
	   return $this -> query("UPDATE `{$this->_sPrefix}actions` SET `active` = 'false'");	
	}
	
	function deleteAction($iId){
	   $aLangKey = $this -> getRow("SELECT `title`,`handler`,`alerts_unit`,`group` FROM `{$this->_sPrefix}actions` WHERE `id` = '{$iId}'");	
	   if ($aLangKey['title'])
	   {
		  $this -> query("DELETE FROM `sys_localization_keys`, `sys_localization_strings` USING `sys_localization_keys`, `sys_localization_strings`
						  WHERE `sys_localization_keys`.`ID` = `sys_localization_strings`.`IDKey` AND `sys_localization_keys`.`Key` = '{$aLangKey['title']}'");
		  
		  if ($this -> query("DELETE FROM `sys_alerts` WHERE `action` = '{$aLangKey['handler']}' AND `unit` = '{$aLangKey['alerts_unit']}' AND `handler_id` = '" . $this -> getAlertHandlerID() ."'"))  $this -> clearAlertsCache();
		  
		  return $this -> query("DELETE FROM `{$this->_sPrefix}actions` WHERE `id` = '{$iId}'"); 
	   }
	   return false;
	}	
	
	function getInstalledModulesWithActions(){
		$aResult = array();
		$aResult[]= array('module_uri' => 'profile' , 'installed' => 1);
		$aResult[]= array('module_uri' => 'friend' , 'installed' => 1);
		return array_merge($aResult, $this -> getAll("SELECT  `{$this->_sPrefix}modules` . *, IF (`id` IS NULL , 0, 1) AS  `installed` FROM  `{$this->_sPrefix}modules` LEFT JOIN  `sys_modules` ON  `{$this->_sPrefix}modules`.`module_uri` = `sys_modules`.`uri` WHERE `hasactions` = 'true' ORDER BY `module_uri` ASC"));
	}
	
	function saveActions($iValue, $iLimit, $sActive, $iID){
		return $this -> query("UPDATE `{$this->_sPrefix}actions` SET `points` = '{$iValue}', `day_limit` = '{$iLimit}', `active` = '{$sActive}' WHERE `id` = '{$iID}' LIMIT 1");	
	}
	
	function isModuleActive($sModule){
	  if ($this -> _oConfig -> isNotModule($sModule)) return true;
	  return $this -> getOne("SELECT `hasactions` FROM `{$this->_sPrefix}modules` WHERE `module_uri` = LOWER('{$sModule}') LIMIT 1") == 'true';
	}
	
	function getActionInfo($sUnit, $sAction){
		$sModule = $this -> getOne("SELECT IF (`module_uri` = '', `group`, `module_uri`) as `module` FROM `{$this->_sPrefix}actions` WHERE `alerts_unit` = '{$sUnit}' LIMIT 1");
		if (!$this -> isModuleActive($sModule)) return array();
		return $this -> getAll("SELECT * FROM `{$this->_sPrefix}actions` WHERE `handler` = '{$sAction}' AND `alerts_unit` = '{$sUnit}'");
	}
	
	function getPointsIncrement($iProfileID){
		if (!(int)$iProfileID || !$this -> _oConfig -> isIncrementEnabled()) return false;
		
		$aMembershipInfo = getMemberMembershipInfo($iProfileID);
		if ((int)$aMembershipInfo['ID'] < 2) return false; 
		
		$aInfo = $this -> getMemLevelsSettings($aMembershipInfo['ID']);
		return (int)$aInfo['increment']; 
	}
	
	function assignPoints($aAction, $iProfileID){
		
		if (!(int)$iProfileID || !(int)$aAction['points'] || ((int)$aAction['id'] && (!(int)$aAction['day_limit'] || $aAction['active'] != 'true'))) return false;
		$iInñ = $this -> getPointsIncrement($iProfileID);
		$aAction['points'] = (int)$aAction['points'] + $iInñ;
		
		if (!(int)$aAction['points']) return false;

		if ((int)$aAction['id']){
			if ((int)$aAction['day_limit'] && !$this -> checkCountDuring24($aAction, $iProfileID)) return false;
			if (!$this -> checkPointsDuring24AndAllPeriod($aAction, $iProfileID)) return false;
		}
		
		if (!$aAction['time']) $sTime = 'UNIX_TIMESTAMP()'; else $sTime = $aAction['time'];
	
		$aAction['title'] = process_db_input($aAction['title']);
		
		if (isset($aAction['duplicated']) && !(int)$aAction['duplicated'] && (int)$this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}history` WHERE `action_id` = '{$aAction['id']}' AND `profile_id` = '{$iProfileID}' AND `time` = {$sTime}")) return false;
		
		if ($this -> query("INSERT INTO `{$this->_sPrefix}history` SET `action_id` = '{$aAction['id']}', `profile_id` = '{$iProfileID}', `reason` = '{$aAction['title']}', `points` = '{$aAction['points']}', `time` = {$sTime}")) 
		{	
			$this -> updateProfile($iProfileID);
			
			require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
			$oZ = new BxDolAlerts('aqb_points', 'assign', $iProfileID, $aAction['id'], $aAction);
			$oZ->alert();
			
			return true;
		}	
		
		return  false;
	}
	
	function checkCountDuring24(&$aAction, $iProfileID){
		if (count($aAction) == 0 || !(int)$iProfileID) return false;
						
		$iCount = $this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}history` WHERE `action_id` = '{$aAction['id']}' AND `time` >= UNIX_TIMESTAMP(SUBDATE(NOW(), INTERVAL 24 HOUR)) AND `profile_id` = '{$iProfileID}'");
		return (int)$iCount < (int)$aAction['day_limit'];
	}
	
	function checkPointsDuring24AndAllPeriod(&$aAction, $iProfileID){
		if (!(int)$iProfileID) return false;
		
		$aMembershipInfo = getMemberMembershipInfo($iProfileID);
		if ((int)$aMembershipInfo['ID'] < 2) return false; 
		
		$aInfo = $this -> getMemLevelsSettings($aMembershipInfo['ID']);
			
		$bMaxPerDay = !(int)$aInfo['maximum_per_day'] || ((int)$this -> getOne("SELECT SUM(`points`) FROM `{$this->_sPrefix}history` WHERE `time` >= UNIX_TIMESTAMP(SUBDATE(NOW(), INTERVAL 24 HOUR)) AND `profile_id` = '{$iProfileID}' AND `action_id` <> 0") + (int)$aAction['points']) <= (int)$aInfo['maximum_per_day'];

		if ($bMaxPerDay && !(int)$aInfo['maximum']) return true;

		$iTotal = (int)$this -> getOne("SELECT SUM(`points`) FROM `{$this->_sPrefix}history` WHERE `profile_id` = '{$iProfileID}' AND `action_id` <> 0 LIMIT 1") + (int)$aAction['points'];	
		
		return  $bMaxPerDay && $iTotal <= (int)$aInfo['maximum'];
	}
		
	function getLeadersPage(&$aDisplaySettings){
		$sLeaders = array('count' => 0, 'rData' => null);
		$iOnlineTime = (int)$this -> getParam( "member_online_time" );
		
		$aWhereParam = array();
		
		if ( $aDisplaySettings['with_photos'] ) 
		$aWhereParam[] = '`Profiles`.`Avatar` <> 0';

		if ( $aDisplaySettings['online'] )
		$aWhereParam[] = "(`Profiles`.`DateLastNav` > SUBDATE(NOW(), INTERVAL {$iOnlineTime} MINUTE)) ";

		$sWhereParam = null;
		foreach( $aWhereParam AS $sValue )
			if ( $sValue )
				$sWhereParam .= ' AND ' . $sValue;
		
		$sSql = "`Profiles`.`Status` = 'Active' and (`Profiles`.`Couple` = 0 or `Profiles`.`Couple` > `Profiles`.`ID`)";
		// make search ;
		$sQuery = "SELECT COUNT(*) AS `Cnt` FROM `Profiles` WHERE {$sSql} {$sWhereParam}";
		 
		$iTotalNum = (int)$this -> getOne($sQuery);

		if( !$iTotalNum ) return false;
		
		$sLeaders['count'] = $iTotalNum;
		// init some pagination parameters ;
		
		$iPerPage = (int)$aDisplaySettings['per_page'];
		$iCurPage = (int)$aDisplaySettings['page'];

		if( $iCurPage < 1 )
			$iCurPage = 1;

		$sLimitFrom = ( $iCurPage - 1 ) * $iPerPage;
		$sqlLimit = "LIMIT {$sLimitFrom}, {$iPerPage}";

		// select sorting parameters ;
		$sSortParam = '`points` DESC';
		if ( isset($aDisplaySettings['sort']) ) {
			switch($aDisplaySettings['sort']) {
				case 'points_up' :
					$sSortParam = ' `points` ASC';
					break;    
				default :
					$sSortParam = ' `points` DESC';
					$aDisplaySettings['sort'] = 'points_down';
					break;    
			}
		} else
			$aDisplaySettings['sort'] = 'points_down';

		// status uptimization
		
		$sIsOnlineSQL = ", if(`DateLastNav` > SUBDATE(NOW(), INTERVAL {$iOnlineTime} MINUTE ), 1, 0) AS `is_online`";

		$sQuery  = 
		"
			SELECT 
				`Profiles`.* {$sIsOnlineSQL},
				`AqbPoints` as `points`
			FROM 
				`Profiles` 
		    {$sJoin}
			WHERE 
				{$sSql}
				{$sWhereParam}
			ORDER BY
				{$sSortParam}
			{$sqlLimit}            
		";
		$sLeaders['rData'] = $this -> res($sQuery);
		return $sLeaders;
	}
	
	function getMembersForPointsLeadersBlock($iPageParam, $sMode = 'upper', $iLimit = 10){
		$aDefFields = array(
            'ID', 'NickName', 'Couple', 'Sex' 
        );
        
        $iOnlineTime = (int)$this -> getParam( "member_online_time" );
        
        $sqlMainFields = "IF (`AqbPoints` IS NULL, 0, `AqbPoints`) as `points`, ";
        
        foreach ($aDefFields as $iKey => $sValue)
             $sqlMainFields .= "`Profiles`. `$sValue`, ";
             
        $sqlMainFields .= "if(`DateLastNav` > SUBDATE(NOW(), INTERVAL {$iOnlineTime} MINUTE ), 1, 0) AS `is_online`";
       	
        // possible conditions
        $sqlCondition = "WHERE `Profiles`.`Status` = 'Active' and (`Profiles`.`Couple` = 0 or `Profiles`.`Couple` > `Profiles`.`ID`)";
       
        // top menu and sorting
        $sqlOrder = "";
        
        switch ($sMode) {
                case 'online':
                        $sqlCondition .= " AND `Profiles`.`DateLastNav` > SUBDATE(NOW(), INTERVAL ".$iOnlineTime." MINUTE)";
                        $sqlOrder = " ORDER BY `points` ASC";
                break;
                case 'lower':
                        $sqlOrder = " ORDER BY `points` ASC"; 
                break;
                case 'upper':
                        $sqlOrder = " ORDER BY `points` DESC"; 
        }
        
		$iCount = (int)$this -> getOne("SELECT COUNT(*) FROM `Profiles` {$sqlCondition}");	
	    if ($iCount) {
            $iPages = ceil($iCount / $iLimit);
            $iPage = (int)$iPageParam;
            if ($iPage < 1)
                $iPage = 1;
            if ($iPage > $iPages)
                $iPage = $iPages;

            $sqlFrom = ($iPage - 1) * $iLimit;
            $sqlLimit = "LIMIT $sqlFrom, $iLimit";
            
            $sqlQuery = "SELECT " . $sqlMainFields . " FROM `Profiles` $sqlCondition $sqlOrder $sqlLimit";
            $aData = $this -> getAll($sqlQuery);
            return array('icount' => $iCount, 'data' => $aData, 'ipage' => $iPage);
	    }    
        
	    return false;   
	}
	
   function getProfileTransactions($iProfileId){
   	   return $this -> getAll("SELECT * FROM `{$this->_sPrefix}transactions` WHERE `buyer_id` = '{$iProfileId}'");
   }

   function saveMemlevelsSettings(&$aParams){
	  return $this -> query("REPLACE INTO `{$this->_sPrefix}memlevels_settings` SET `id` = '{$aParams['ID']}', `increment` = '".(int)$aParams['points_increment']."', `maximum` = '".(int)$aParams['maximum_points']."', `maximum_per_day` = '".(int)$aParams['maximum_per_day_points']."'");	   		
   }
  
   function saveMemlevelsPricing(&$aParams){
  	 if ((int)$aParams['points']  <= 0) return false; //(int)$aParams['ID'] <= 3 || 
   	 return $this -> query("REPLACE INTO `{$this->_sPrefix}memlevels_pricing` SET `id_level` = '{$aParams['ID']}', `days` = '".(int)$aParams['days']."', `points` = '".(int)$aParams['points']."'");
   }
   
   function getMemLevelsSettings($iId){
	 return $this -> getRow("SELECT * FROM `{$this->_sPrefix}memlevels_settings` WHERE `id` = '{$iId}'");	   		
   }
   
   function getMemLevelsPricing($iId){
	  return $this -> getAll("SELECT * FROM `{$this->_sPrefix}memlevels_pricing` WHERE `id_level` = '{$iId}' ORDER BY `days` ASC");	   		
   }
   
   function getIncrementInfo(){
	  return $this -> getAll("SELECT `increment`,`Name` FROM `{$this->_sPrefix}memlevels_settings` LEFT JOIN `sys_acl_levels` ON `{$this->_sPrefix}memlevels_settings`.`id` = `sys_acl_levels`.`ID` ORDER BY `{$this->_sPrefix}memlevels_settings`.`id` ASC");	   			
   }
   
   function presentPoints($iSId, $iDId, $iPointsNum){
		$iPointsOwnerNum = (int)$this -> getProfileTotalPointsNum($iSId);
		if ($iPointsOwnerNum < $iPointsNum) return false;
		
		$aSInfo = getProfileInfo($iSId);
		$aDInfo = getProfileInfo($iDId);
	
		$iD = $this -> assignPoints(array('id' => 0, 'title' => _t('_aqb_points_present_recipient', $iPointsNum, $aSInfo['NickName'], BX_DOL_URL_ROOT. $aSInfo['NickName']), 'points' => $iPointsNum), $iDId);
		if ($iD) $iS = $this -> assignPoints(array('id' => 0, 'title' => _t('_aqb_points_present_sender', $iPointsNum, $aDInfo['NickName'], BX_DOL_URL_ROOT . $aDInfo['NickName']) , 'points' => -$iPointsNum), $iSId);

		return $iS && $iD;		
   }
   
   function getProfileTransactionItem($iProfileId, $iItem){
   	   return $this -> getRow("SELECT * FROM `{$this->_sPrefix}transactions` WHERE ((`buyer_id` = '{$iProfileId}' AND `id` ='{$iItem}' AND (`type` = 'buy' OR `type` = 'package')) OR  (`id` ='{$iItem}' AND `type` = 'exchange')) LIMIT 1");
   }
   
   function deleteProfileTransactions($iProfileId){
   	   return $this -> query("DELETE FROM `{$this->_sPrefix}transactions` WHERE `buyer_id` = '{$iProfileId}'");
   }

   function deleteTransactionsItem($iItemId){
   	  $aInfo = $this -> getTransactionInfo($iItemId);
	  
	  if ($aInfo['type'] == 'exchange'){
		$this -> removeCartInfo($aInfo['buyer_id'], $iItemId);
		$this -> query("DELETE FROM `{$this->_sPrefix}history` WHERE `profile_id` = '{$aInfo['buyer_id']}' AND `time` = '{$aInfo['payment_date']}'");
	  }
	  
	  return $this -> query("DELETE FROM `{$this->_sPrefix}transactions` WHERE `id` = '{$iItemId}'");
   }
   
   function getMembers($aParams){
	$sSelectClause = $sJoinClause = $sWhereClause = $sOrderBy = $sHaving = '';
	
	if (empty($aParams['view_order'])) $sOrderBy = '`tp`.`ID` ASC'; 
		else $sOrderBy = "`{$aParams['view_order']}` {$aParams['view_type']}"; 
   
	if (!empty($aParams['filter'])) 
	{
		
		$matches = array();
		 
			if (is_numeric($aParams['filter'])) $sPoints = " OR `tp`.`AqbPoints` = '{$aParams['filter']}'";  
		 
			$sWhereClause .= " AND (
	                `tp`.`NickName` LIKE '%" . $aParams['filter'] . "%' OR 
	                `tp`.`Email` LIKE '%" . $aParams['filter'] . "%' OR 
	                `tp`.`Headline` LIKE '%" . $aParams['filter'] . "%' OR 
	                `tp`.`DescriptionMe` LIKE '%" . $aParams['filter'] . "%' OR 
	                `tp`.`Tags` LIKE '%" . $aParams['filter'] . "%' OR 
	                `tp`.`DateReg` LIKE '%" . $aParams['filter'] . "%' OR
	                `tp`.`DateLastLogin` LIKE '%" . $aParams['filter'] . "%' {$sPoints})";
	   
	}	
	
	//--- Get Items ---//
    $sQuery = " 
			SELECT
    		`tp`.`ID` as `id`,
    		`tp`.`NickName` AS `username`,
    		`tp`.`Headline` AS `headline`,
    		`tp`.`Sex` AS `sex`,
    		`tp`.`DateOfBirth` AS `date_of_birth`,
    		`tp`.`Country` AS `country`,
    		`tp`.`City` AS `city`,
    		`tp`.`DescriptionMe` AS `description`,
		    `tp`.`Email` AS `email`,
		    `tp`.`AqbPoints` AS `points_num`,
    		DATE_FORMAT(`tp`.`DateReg`,  '" . $this -> _oConfig-> getDateFormat() . "' ) AS `registration`,
    		DATE_FORMAT(`tp`.`DateLastLogin`,  '" . $this -> _oConfig-> getDateFormat() . "' ) AS `last_login`,
    		`tp`.`Status` AS `status`
       		" . $sSelectClause . "
	    	FROM `Profiles` AS `tp`
		   	WHERE 1 AND (`tp`.`Couple`=0 OR `tp`.`Couple`>`tp`.`ID`) " . $sWhereClause . $sGroupClause . "
	    	ORDER BY  {$sOrderBy}
	    	LIMIT " . $aParams['view_start'] . ", " . $aParams['view_per_page'].'';

			$this -> _iMembersCount = (int)$this -> getOne("SELECT COUNT(`tp`.`ID`) FROM `Profiles` AS `tp` 
														" . $sJoinClause . "
														WHERE 1 AND (`tp`.`Couple` = 0 OR `tp`.`Couple`>`tp`.`ID`)" . $sWhereClause);
		return $this -> getAll($sQuery);
	}
	
	function getMembershipLevels($iId = 0){
	    $sWhere = 'WHERE `ID` > 1';
		if ((int)$iId) $sWhere = "WHERE `ID` = '{$iId}'";
		
		return $this -> getAll("SELECT * FROM `sys_acl_levels` {$sWhere}");
	}
	
	function getMemPrices(){
		$aLevels = $this -> getAll("SELECT `{$this -> _sPrefix}memlevels_pricing`.`id`, `id_level`, `Name`,`days`,`points`,`Description`,`Icon` FROM `{$this -> _sPrefix}memlevels_pricing` LEFT JOIN `sys_acl_levels` ON `{$this->_sPrefix}memlevels_pricing`.`id_level` = `sys_acl_levels`.`ID` ORDER BY `{$this->_sPrefix}memlevels_pricing`.`id` ASC");
		
		$aTmp = $aLevels;
		foreach($aLevels as $iKey => $aValue){
			if (!strlen($aValue['Name'])){ 
				$this -> deleteMemlevelsPricing((int)$aValue['id']);
				unset($aTmp[$iKey]);
			}
		}
		
		return $aTmp;
	}
	
	function getMemPrice($iLevelId){
		return $this -> getRow("SELECT * FROM `{$this -> _sPrefix}memlevels_pricing` WHERE `id` = '{$iLevelId}' LIMIT 1");
	}
	
	function exchangePointsToMemLevel($ProfileId, $iLevelId){
		if (!(int)$ProfileId || !(int)$iLevelId) return false;

		$aValue = $this -> getMemPrice($iLevelId);
		$iTotal = $this -> getProfileTotalPointsNum($ProfileId);

	 	if (!(int)$aValue['points'] || (int)$aValue['points'] > $iTotal) return false;
		
		if (!(setMembership($ProfileId, (int)$aValue['id_level'], (int)$aValue['days'], true) && $this -> assignPoints(array('id' => 0, 'title' => _t('_aqb_points_successfully_exchange_to_memlevel', $aValue['points']), 'points' => -(int)$aValue['points']), $ProfileId))) return false;
		
		return (int)$aValue['points'];
	}
	
	function getSettingsCategory () {
        return (int)$this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Global Points System' LIMIT 1");
    }
	
	function isAddedToTheModuleList($sUrl){
		return (int)$this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}modules` WHERE `module_uri` = LOWER('{$sUrl}')") == 1;
	}
	
	function addToModuleList($sName){
		if (!$sName) return;
		return $this -> query("INSERT INTO `{$this->_sPrefix}modules` SET `module_uri` = LOWER('{$sName}'), `hasactions` = 'true'");
	}
	
	function disableModule($sName){
		return $this -> query("UPDATE `{$this->_sPrefix}modules` SET `hasactions` = 'false' WHERE `module_uri` = LOWER('{$sName}')");
	}
	
	function getLangCategory(){
		return (int)$this -> getOne("SELECT `id` FROM `sys_localization_categories` WHERE `Name` = 'Global Points System' LIMIT 1");
	}
	
	function addAction(&$aValue){
		if (!strlen($aValue['title']) || !strlen($aValue['name']) || !strlen($aValue['module_uri']) || !strlen($aValue['alert_name'])) return false;
		$sName = process_db_input($aValue['name']);
		$sAlerName = process_db_input($aValue['alert_name']);
		$sTitle =  '_aqb_points_action_'.$sAlerName.'_'.$sName;
		if ($this -> query("INSERT INTO `{$this->_sPrefix}actions` SET 
																			`title` = '{$sTitle}', 
																			`handler` = '{$sName}', 
																			`module_uri` = '{$aValue['module_uri']}',
																			`alerts_unit` = '{$sAlerName}',
																			`group` = '{$aValue['module_uri']}',
																			`description` = '',		
																			`points` = '" . (int)$aValue['points'] . "',
																			`day_limit` = '" . (int)$aValue['limit'] . "' ,
																			`active` = '" . $aValue['checked'] . "'") && $this -> query("INSERT INTO `sys_alerts` SET `action` = '{$sName}', `unit` = '{$sAlerName}', `handler_id` = '" . $this -> getAlertHandlerID() . "'"))
		{
			
			$id = $this -> getLangCategory(); 
			if ($id && $aValue['title']) addStringToLanguage($sTitle, $aValue['title'], -1, $id);
				$this -> clearAlertsCache();
			return true;
		}
		return false;
	}
	
	function checkIfNewModulesInstalled(){
		$oModules = new BxDolModuleDb();
        $aModules = $oModules->getModules();
 
        $aNotInstalled = array();
        $sPath = BX_DIRECTORY_PATH_ROOT . 'modules/';
        if($rHandleVendor = opendir($sPath)) {

            while(($sVendor = readdir($rHandleVendor)) !== false) {
                if(substr($sVendor, 0, 1) == '.' || !is_dir($sPath . $sVendor)) continue;
                
                if($rHandleModule = opendir($sPath . $sVendor)) {
                    while(($sModule = readdir($rHandleModule)) !== false) {
				
                        $sConfigPath = $sPath . $sVendor . '/' . $sModule . '/install/config.php';
                        if(!file_exists($sConfigPath)) continue;

                        include($sConfigPath);
			            if($aConfig['home_uri'] && $aConfig['home_uri'] != $this -> _oConfig -> getUri() && !$this -> isAddedToTheModuleList($aConfig['home_uri']) && $this -> addToModuleList($aConfig['home_uri']))
						{
							$id = $this -> getLangCategory(); 
							if ($id && $aConfig['title']) addStringToLanguage('_aqb_module_title_'.$aConfig['home_uri'], strip_tags($aConfig['title']), -1, $id);
						}	
                    }
                    closedir($rHandleModule);
                }                
            }
            closedir($rHandleVendor); 
        }        
	}
	
	function clearAlertsCache(){
		if ((int)$GLOBALS['site']['build'] > 2) {
				 $oCache = $this -> getDbCacheObject();
		         $oCache -> removeAllByPrefix('db_sys_alerts_');
		  } else {
		         @unlink(BX_DIRECTORY_PATH_CACHE . 'sys_alerts.inc');
		  }
	}
	
	function addPackage(){
		$iPoints = (int)bx_get('points');
		$fPrice = (float)bx_get('price');
		
		if (!(int)$iPoints || !(float)$fPrice) return false;
		
		$this -> deletePackage(array((int)$iPoints));
		$sParams = $this -> getParam('aqb_points_packages');
		
		return $this -> setParam('aqb_points_packages', $sParams ? "{$sParams}|{$iPoints}-{$fPrice}" : "{$iPoints}-{$fPrice}");
	}
	
	function getPriceByPoints($iPoints){
		$aPackages = $this ->  getPackages();
		if (empty($aPackages)) return false;
		
		foreach($aPackages as $k => $v){
			if ((int)$v['points'] == (int)$iPoints) return  $v['price'];
		}
		
		return false;
	}
	
	function getPackages(){
		$sParams = $this -> getParam('aqb_points_packages');
		$aResult = array();
		if (!$sParams) return $aResult;
		
		$aValues = explode('|',$sParams);
		foreach($aValues as $k => $v){
			$aTmp = split('-', $v);
			$aResult[] = array('points' => $aTmp[0], 'price' => $aTmp[1]);
		}
		
		return $aResult;
	}
	
	function deletePackage($aData = array()){
		$sParams = $this -> getParam('aqb_points_packages');
		$aValues = explode('|',$sParams);
	
		$aResult = array();
		
		$aPackage = empty($aData) ? $_POST['packages'] : $aData;
		
		if (is_array($aPackage)){
			foreach($aPackage as $k => $v)
				foreach($aValues as $sKey => $sValue){
					$aTmp = split('-',$sValue);
					if ($aTmp[0] == $v) unset($aValues[$sKey]);
				}				
		}
		
		return $this -> setParam('aqb_points_packages', implode('|', $aValues));
	}
	function addExchangeTransaction($iMemberId){
	   	$iTotal = $this -> getProfileTotalPointsNum($iMemberId);
		
		$aDate = $this -> getRow("SELECT MAX(`time`) as `max`, MIN(`time`) as `min` FROM `{$this->_sPrefix}history` LIMIT 1");
		$fPrice = $this -> _oConfig -> priceForExchangeToPoints();
		
		$iCount = $this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}transactions` WHERE `status` = 'pending' AND `type` = 'exchange' AND `buyer_id` = '{$iMemberId}' LIMIT 1");
		
		if ($iCount)
		return $this -> query("UPDATE `{$this->_sPrefix}transactions` SET 
									  `price`= '" . ($fPrice * $iTotal) . "',
									  `points_num`= '{$iTotal}',
									  `buyer_id`= '{$iMemberId}',
									  `date` = '{$aDate['min']}',
									  `date_end` = '{$aDate['min']}',
									  `type` = 'exchange' WHERE `status` = 'pending' AND `type` = 'exchange' AND `buyer_id` = '{$iMemberId}' ");			  
									  
		return $this -> query("INSERT INTO `{$this->_sPrefix}transactions` SET 
									  `price`= '" . ($fPrice * $iTotal) . "',
									  `points_num`= '{$iTotal}',
									  `buyer_id`= '{$iMemberId}',
									  `date` = '{$aDate['min']}',
									  `date_end` = '{$aDate['min']}',
									  `type` = 'exchange'");			  
  
	}
	
	function getUnpaidRequestsNumber(){
		return (int)$this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}transactions` WHERE `status` = 'pending' AND `type` = 'exchange' LIMIT 1");
	}
	
	function getRequests($aParams){
	$sSelectClause = $sJoinClause = $sWhereClause = $sOrderBy = $sHaving = '';
	
	if (empty($aParams['view_order'])) $sOrderBy = '`tp`.`ID` ASC'; 
		else $sOrderBy = "`{$aParams['view_order']}` {$aParams['view_type']}"; 
   
   
	if (!empty($aParams['filter'])) $sProfile = "AND `tp`.`NickName` = '{$aParams['filter']}'";	
	
	if (!empty($aParams['request']) && $aParams['request'] != 'all'){
		$aParams['request'] = $aParams['request'] == 'unpaid' ? 'pending' : 'active';
		$sStatus = "AND `{$this->_sPrefix}transactions`.`status` = '{$aParams['request']}'";		
	}
	
	
	//--- Get Items ---//
    $sQuery = " 
			SELECT
    		`{$this->_sPrefix}transactions`.`id`,
			`tp`.`NickName` AS `username`,
			`tp`.`ID` AS `member_id`,
    		`tp`.`Headline` AS `headline`,
    		`tp`.`Sex` AS `sex`,
    		`tp`.`DateOfBirth` AS `date_of_birth`,
    		`tp`.`Country` AS `country`,
    		`tp`.`City` AS `city`,
    		`tp`.`DescriptionMe` AS `description`,
		    `tp`.`Email` AS `email`,
		   	DATE_FORMAT(`tp`.`DateReg`,  '" . $this -> _oConfig -> getDateFormat() . "' ) AS `registration`,
			DATE_FORMAT(FROM_UNIXTIME(`date`),  '" . $this -> _oConfig -> getDateFormat() . "' ) AS `date`,
			DATE_FORMAT(FROM_UNIXTIME(`date_end`),  '" . $this -> _oConfig -> getDateFormat() . "' ) AS `date_end`,
			IF (FORMAT(`{$this->_sPrefix}transactions`.`price`, 2) IS NULL, 0, FORMAT(`{$this->_sPrefix}transactions`.`price`,2)) as `price`,
			IF (`{$this->_sPrefix}transactions`.`points_num` IS NULL, 0, `points_num`) as `points`,
			IF (`{$this->_sPrefix}transactions`.`status` = 'pending', 'unpaid','paid') as `status`,
			`{$this->_sPrefix}transactions`.`payment_status`
	    	FROM `{$this->_sPrefix}transactions` 
			LEFT JOIN `Profiles` AS `tp` ON `tp`.`ID` = `{$this->_sPrefix}transactions`.`buyer_id`
			WHERE (`tp`.`Couple`=0 OR `tp`.`Couple`>`tp`.`ID`) AND `type` = 'exchange' {$sProfile} {$sStatus} " . $sWhereClause . $sGroupClause . "
	    	ORDER BY  {$sOrderBy}
	    	LIMIT " . $aParams['view_start'] . ", " . $aParams['view_per_page'].'';
		
		$this -> _iRequestsCount = (int)$this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}transactions` LEFT JOIN `Profiles` AS `tp` ON `{$this->_sPrefix}transactions`.`buyer_id` = `tp`.`ID`
															WHERE 1 {$sProfile} " . $sWhereClause);
		return $this -> getAll($sQuery);
	}
	
	function getSelectForAllProviders($iMemberId){
		$aPayments = $this -> getAll("SELECT `option_prefix` FROM `bx_pmt_providers`");
		if (empty($aPayments)) return false;
		
		$sValues = '';
		foreach($aPayments as $iKey => $aPayment){
		   $sValues .= " (`name` = '{$aPayment['option_prefix']}active' AND `value` = 'on') OR";
		}
		return $sValues = substr($sValues, 0, -2);
	}
	
	function isPaymentInstalled($iMemberId){
		$sValues = $this -> getSelectForAllProviders($iMemberId);
		return (int)$this -> getOne("SELECT COUNT(*) FROM `bx_pmt_user_values` as `v` LEFT JOIN `bx_pmt_providers_options` as `o` ON `v`.`option_id` = `o`.`id` WHERE `user_id` = '{$iMemberId}' AND ($sValues) LIMIT 1") > 0;
	}
	
	function getTransactionInfo($iId){
		return $this -> getRow("SELECT * FROM `{$this -> _sPrefix}transactions` WHERE `id` = '{$iId}' LIMIT 1"); 
	}
	
	function getAvailablePayament($iMemberID){
		$aPaymentProviders = $this -> getPairs("SELECT `id`,`name` FROM `bx_pmt_providers` ORDER BY `id`", 'id', 'name');
		$sValues = $this -> getSelectForAllProviders($iMemberId);
		$aAvailabel = $this -> getAll("SELECT `provider_id` FROM `bx_pmt_user_values` as `v` LEFT JOIN `bx_pmt_providers_options` as `o` ON `v`.`option_id` = `o`.`id` WHERE `user_id` = '{$iMemberID}' AND ($sValues)");

		$aResult = array();
		if (!empty($aAvailabel) && !empty($aPaymentProviders)){
			foreach($aAvailabel as $iKey => $aVal){
				$aResult[$aVal['provider_id']]= $aPaymentProviders[$aVal['provider_id']];
			}		
		}
		return $aResult;	
	}
	
	function getTransactionsPaymentProvider($sTransaction){
	    $iId = $this -> getOne("SELECT `pending_id` FROM `bx_pmt_transactions` WHERE `order_id` = '{$sTransaction}' LIMIT 1");
		$sName = $this -> getOne("SELECT `provider` FROM `bx_pmt_transactions_pending` WHERE `id` = '$iId' LIMIT 1"); 
		return $this -> getOne("SELECT `id` FROM `bx_pmt_providers` WHERE `name` = '{$sName}' LIMIT 1"); 
	}
	
	function getModuleId(){
		return $this -> getOne("SELECT `id` FROM `sys_modules` WHERE `uri` = 'aqb_points' LIMIT 1");
	}
	
	function makeUnpaid($iId){
	   return $this -> query("UPDATE `{$this -> _sPrefix}transactions` SET `status` = 'pending', `tnx` = '', `payment_date` = 0 WHERE `id` = '{$iId}'"); 
	}
	
	function paidExchange($iId, $sOrder, $iTime = 0){
		if ((int)$iTime) $sTime = $iTime; else $sTime = 'UNIX_TIMESTAMP()';
		return $this -> query("UPDATE `{$this -> _sPrefix}transactions` SET `status` = 'active', `tnx` = '{$sOrder}', `payment_date` = {$sTime} WHERE `id` = '{$iId}'"); 
	}
	
	function removeCartInfo($iMemberId, $iId){
		$sItems = "{$iMemberId}_" . $this -> getModuleId(). "_{$iId}_1"; 
		$aItems = $this -> getAll("SELECT `id` FROM `bx_pmt_transactions_pending` WHERE `items` = '{$sItems}'");
			
			if (!empty($aItems)){
				foreach($aItems as $iK => $aValue){
					$this -> query("DELETE FROM `bx_pmt_transactions` WHERE `pending_id` = '{$aValue['id']}'");
					$this -> query("DELETE FROM `bx_pmt_transactions_pending` WHERE `id` = '{$aValue['id']}'");
				}
			}
			else return false;
		
		return true;	
	}
	
	function processExchangeManyally($iId, $sTransactionID){
		$aInfo = $this -> getTransactionInfo($iId);
		$sStatus = bx_get('status');
		$iTime = time();
		
		if ($sStatus == 'unpaid' || $aInfo['status'] == 'active'){ 
			$this -> removeCartInfo($aInfo['buyer_id'], $iId);
			if ($sStatus == 'unpaid'){ 
				$this -> makeUnpaid($iId);
				return $this -> query("DELETE FROM `aqb_points_history` WHERE `profile_id` = '{$aInfo['buyer_id']}' AND `action_id` = '0' AND `time` = '{$aInfo['payment_date']}'");			
			}
			else $this -> query("UPDATE `aqb_points_history` SET `time` = '{$iTime}' WHERE `action_id` = '0' AND `time` = '{$aInfo['payment_date']}' AND `profile_id` = '{$aInfo['buyer_id']}'");
		}
		
		if ($sTransactionID){
				$aPayments = $this -> getAvailablePayament($aInfo['buyer_id']);
				$iClient = getLoggedId();				
				if ($iPayment = (int)bx_get('payments')) $sProvider = $aPayments[$iPayment];				
				
				$aParams = array(
							'client' => $iClient,
							'seller' => $aInfo['buyer_id'],
							'items' => "{$aInfo['buyer_id']}_" . $this -> getModuleId(). "_{$iId}_1",
							'amount' => $aInfo['price'],
							'order'  => $sTransactionID,
							'provider' => $sProvider,
							'item_id' => $iId,
							'item_count' => $aInfo['points_num']							
						 );
						 
				$this -> createCartItems($aParams);			
		}

		if ((int)$aInfo['points_num'] && $aInfo['status'] == 'pending'){ 
	   		$fPrice = (int)$aInfo['points_num'] * $this -> _oConfig -> priceForExchangeToPoints();
			$aAction = array('time' => $iTime, 'price' => (int)$aInfo['points_num'] * $fPrice, 'points' => -$aInfo['points_num'], 'title' => _t('_aqb_points_you_have_exchanged_points', $this -> _oConfig -> getCurrencySign() . $fPrice));
			$this -> assignPoints($aAction, (int)$aInfo['buyer_id']);
		}	
		
		$this -> paidExchange($iId, $sTransactionID, $iTime);		
		return array('type' => $aInfo['type'],'member_id' => $aInfo['buyer_id'], 'price' => $this -> _oConfig -> getCurrencySign() . $fPrice , 'points' => $aInfo['points_num']);
	}
	
	function createCartItems(&$aParams){
		if (empty($aParams)) return false;
	
		$this -> query("INSERT INTO `bx_pmt_transactions_pending` SET 
								`client_id` = '{$aParams['client']}',
								`seller_id` = '{$aParams['seller']}', 
								`items` = '{$aParams['items']}', 
								`amount` = '{$aParams['amount']}', 
								`order` = '{$aParams['order']}', 
								`error_code` = 1, 
								`error_msg` = 'Payment was successfully accepted.', 
								`provider` = '{$aParams['provider']}', 
								`date` = UNIX_TIMESTAMP()");
								
		$iGetLast = $this -> lastId();
				
		return	$iGetLast && $this -> query("INSERT INTO `bx_pmt_transactions` SET 
								`pending_id` = '{$iGetLast}',
								`client_id` = '{$aParams['client']}',
								`seller_id` = '{$aParams['seller']}', 
								`item_count` = '{$aParams['item_count']}', 
								`item_id` = '{$aParams['item_id']}',
								`amount` = '{$aParams['amount']}', 
								`module_id` = " . $this -> getModuleId() . ",
								`order_id` = '{$aParams['order']}', 
								`date` = UNIX_TIMESTAMP()");
		 
	}
	
	function createLevel($aInfo){
		$sName = process_db_input($aInfo['title']);
		$iMax = (int)$aInfo['max'];
		$iMin = (int)$aInfo['min'];
		
		$iBonus = (int)$aInfo['bonus_' . $aInfo['id']];
		$iMelevel = (int)$aInfo['memlevel_' . $aInfo['id']];
		
		$iMin = (int)$aInfo['min'];
		
		if (isset($aInfo['img'])) $sImg = "`img` = '{$aInfo['img']}', ";
		
		if ($iBonus) $sBonus = "`bonus` = '{$iBonus}',";
		if ($iMelevel) $sMLevels = "`memlevel` = '{$iMelevel}', ";
		
		if ((int)$aInfo['id']){
			if ($aInfo['img']){ 
				$aLevelInfo = $this -> getLevel($aInfo['id']);
				$sFileBasePath = $this -> _oConfig -> getLevelFolderPath();
				$sFilePath = $sFileBasePath . $aLevelInfo['img'];
				@unlink($sFilePath);
			}
			
			return $this -> query("UPDATE `{$this->_sPrefix}levels` SET {$sImg} `title` = '{$sName}', `date` = UNIX_TIMESTAMP(), `max` = '{$iMax}', `min` = '{$iMin}' WHERE `id` = '{$aInfo['id']}'"); 
		}
		
		return $this -> query("INSERT INTO `{$this->_sPrefix}levels` SET {$sId} {$sImg} `title` = '{$sName}', `date` = UNIX_TIMESTAMP(), `max` = '{$iMax}', `min` = '{$iMin}'"); 
	}
	
	function updateLevel($iId){
		$iBonus = (int)bx_get('bonus_' . $iId);
		$iMelevel = bx_get('membership_' . $iId);
	
		if ((int)$iBonus || (int)$iMelevel) return $this -> query("UPDATE `{$this->_sPrefix}levels` SET `bonus` = '{$iBonus}', `memlevel` = '{$iMelevel}' WHERE `id` = '{$iId}'"); 
	
		return false; 
	}
	
	function getLevelsList(){
		return $this -> getAll("SELECT * FROM `{$this->_sPrefix}levels` ORDER BY `max`");
	}
	
	function getLevel($iId){
		return $this -> getRow("SELECT * FROM `{$this->_sPrefix}levels` WHERE `id` = '{$iId}' LIMIT 1");
	}
	
	function deleteLevel($iId){
		$aItem = $this -> getLevel($iId);	
		if (empty($aItem)) return false;
		
		$sFileBasePath = $this -> _oConfig -> getLevelFolderPath();
		$sFilePath = $sFileBasePath . $aItem['img'];
		
		return $this -> query("DELETE FROM `{$this->_sPrefix}levels` WHERE `id` = '{$iId}' LIMIT 1") && unlink($sFilePath);
	}
	
	function getLevelByValue($iPoints){
		return $this -> getRow("SELECT * FROM `{$this->_sPrefix}levels` WHERE '{$iPoints}' BETWEEN `min` AND `max` LIMIT 1");
	}
	
	function getMyCurrentLevel($iMemberID){
		$aInfo = $this -> getRow("SELECT `AqbPoints`, `AqbPointsLevel` FROM `Profiles` WHERE `ID` = '{$iMemberID}' LIMIT 1");
	
		if (!(int)$aInfo['AqbPointsLevel']) $this -> applyLevelsOptions($iMemberID);
		else{
			$aLevel = $this -> getLevel((int)$aInfo['AqbPointsLevel']);
			if (empty($aLevel) || (!empty($aLevel) && !($aLevel['min'] <= (int)$aInfo['AqbPoints'] && $aLevel['max'] >= (int)$aInfo['AqbPoints']))) $this -> applyLevelsOptions($iMemberID);
		}		
		return $this -> getLevelByValue($aInfo['AqbPoints']);		
	}
	
	function getMinLevel(){
		return $this -> getRow("SELECT * FROM `{$this->_sPrefix}levels` ORDER BY `min` ASC LIMIT 1");
	}
	
	function applyLevelsOptions($iProfile){
		if (!(int)$iProfile) return false;
		
		$aProfileInfo = $this -> getRow("SELECT `AqbPointsLevel`, `AqbPoints` FROM `Profiles` WHERE `ID` = '{$iProfile}' LIMIT 1");
		$aLevelInfo = $this -> getLevelByValue($aProfileInfo['AqbPoints']);
		
		if ((int)$aProfileInfo['AqbPointsLevel'])
			$aOldLevelInfo = $this -> getLevel((int)$aProfileInfo['AqbPointsLevel']);
		
		if (!empty($aLevelInfo) && (!(int)$aProfileInfo['AqbPointsLevel'] || ((int)$aProfileInfo['AqbPointsLevel'] && (int)$aLevelInfo['id'] != (int)$aProfileInfo['AqbPointsLevel']))){
			$this -> query("UPDATE `Profiles` SET `AqbPointsLevel` = '{$aLevelInfo['id']}' WHERE `ID` = '{$iProfile}' LIMIT 1");
			
			if (!(int)$aProfileInfo['AqbPointsLevel'] || ((int)$aOldLevelInfo['min'] && ((int)$aOldLevelInfo['min'] < $aLevelInfo['min']))){
				if ((int)$aLevelInfo['bonus']){
					$aAction = array('title' => _t('_aqb_points_txt_level_bonus_assigned', $aLevelInfo['title']), 'points' => $aLevelInfo['bonus'], 'id' => 0);
					$this -> assignPoints($aAction, $iProfile);
				}
				
				if ($aLevelInfo['memlevel']){
					$aTmp = split(':', $aLevelInfo['memlevel']);
					setMembership($iProfile, $aTmp[0], $aTmp[1]);
				}
			}
			
			return true;
		}
		
		
		return false;	
	}
	
	function getCmtsAuthor($sUnit, $iId){
		if (!$sUnit || !$iId) return false;
		
		$aTableInfo = $this -> getRow("SELECT `TriggerFieldId`, `TriggerTable` FROM `sys_objects_cmts` WHERE `ObjectName` = '{$sUnit}' LIMIT 1");
		if ($sUnit == 'bx_wall'){ 
			$aTableInfo['TriggerTable'] = 'bx_wall_events';
			$aTableInfo['TriggerFieldId'] = 'object_id';
		}	
		
		if (empty($aTableInfo)) return false;
	
		if ($this -> _oConfig -> _aVoteOwner[$aTableInfo['TriggerTable']])
			return (int)$this -> getOne("SELECT `" . $this -> _oConfig -> _aVoteOwner[$aTableInfo['TriggerTable']] . "` FROM `{$aTableInfo['TriggerTable']}` WHERE `{$aTableInfo['TriggerFieldId']}` = '{$iId}' LIMIT 1");
		
		return false;
	}
	
	function getCmtsRateAuthor($sUnit, $iCommentId){
		if (!$sUnit || !$iCommentId) return false;
		
		$sTableName = $this -> getOne("SELECT `TableCmts` FROM `sys_objects_cmts` WHERE `ObjectName` = '{$sUnit}' LIMIT 1");
		
		if (empty($sTableName)) return false;
		
		return (int)$this -> getOne("SELECT `cmt_author_id` FROM `{$sTableName}` WHERE `cmt_id` = '{$iCommentId}' LIMIT 1");
	}
	
	function getVoteAuthor($sUnit, $iId){
		if (!$sUnit || !$iId) return false;
		
		$aTableInfo = $this -> getRow("SELECT `TriggerFieldId`, `TriggerTable` FROM `sys_objects_vote` WHERE `ObjectName` = '{$sUnit}' LIMIT 1");
		if (empty($aTableInfo)) return false;
		
		if ($this -> _oConfig -> _aVoteOwner[$aTableInfo['TriggerTable']])
			return (int)$this -> getOne("SELECT `" . $this -> _oConfig -> _aVoteOwner[$aTableInfo['TriggerTable']] . "` FROM `{$aTableInfo['TriggerTable']}` WHERE `{$aTableInfo['TriggerFieldId']}` = '{$iId}' LIMIT 1");
		
		return false;	
	}
	
	function getTopicOwner($iPostId){
		$sNickName = $this -> getOne("SELECT `user` FROM `bx_forum_post` WHERE `post_id` = '{$iPostId}' LIMIT 1");
		return $sNickName ? (int)$this -> getOne("SELECT `ID` FROM `Profiles` WHERE  LCASE(`NickName`) COLLATE utf8_general_ci = LCASE('{$sNickName}') COLLATE utf8_general_ci LIMIT 1") : false;
	}
	
	function getCurrentDate(){
		return $this -> getOne("SELECT DATE_FORMAT(NOW(),  '" . $this -> _oConfig-> getDateFormat() . "' )");
	}
}
?>