<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by AndrewP. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
* This product cannot be redistributed for free or a fee without written permission from AndrewP.
* This notice may not be removed from the source code.
*
***************************************************************************/
bx_import('BxDolModuleDb');

class AFChatDb extends BxDolModuleDb {
    var $_oConfig;

    /**
     * Constructor
     */
    function AFChatDb(&$oConfig) {
        parent::BxDolModuleDb($oConfig);
        $this->_oConfig = $oConfig;
    }
    function getMemberList($iLim, $bOnline = false) {
        $sOnlineSQL = ($bOnline) ? 'AND (`DateLastNav` > SUBDATE(NOW(), INTERVAL ' . $this->_oConfig->iOnlineTime . ' MINUTE))' : '';
        $sSQL = "
            SELECT `Profiles`.`ID`
            FROM `Profiles`
            WHERE 1
            {$sOnlineSQL}
            ORDER BY `DateReg` DESC
            LIMIT {$iLim} 
        ";
        return $this->getAll($sSQL);
    }
    function getMessages($iRecipient, $iRoom, $iLogged, $iSkip = 0, $iLim = 10) {
        $sRecipientSQL = 'WHERE `recipient` = 0';
        if ($iRecipient && $iLogged) {
            $sRecipientSQL = "WHERE (`sender` = '{$iRecipient}' && `recipient` = '{$iLogged}') || (`recipient` = '{$iRecipient}' && `sender` = '{$iLogged}')";
        }

        $sRecipientSQL .= " AND `room` = '{$iRoom}'";

        $sLim = ($iSkip) ? $iSkip * $iLim . ", " . $iLim : $iLim;
        $sSQL = "
            SELECT * , UNIX_TIMESTAMP() - `when` AS 'diff'
            FROM `a_fchat_messages`
            {$sRecipientSQL}
            ORDER BY `id` DESC
            LIMIT {$sLim} 
        ";
        // writeLog($sSQL);
        $aMessages = $this->getAll($sSQL);
        asort($aMessages);
        return $aMessages;
    }    
    function getLastPrice($iRoom) {
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
        // writeLog($sSQL);
       return $this->getOne($sSQL);
    }
    function acceptMessage($iPid, $sMessage, $iRoom = 0, $iRecipient = 0, $iOnlStatus = 0, $price = null) {
        $sMessage = strip_tags($sMessage);
        $pattern = array('/(http:\/\/[a-z0-9\.\/]+)/i', '/\n/i');
        $replacement = array('<a href="$1" target="_blank">$1</a>', '<br />');
        $sMessage = preg_replace($pattern, $replacement, $sMessage); 
        $sMessage = $this->escape($sMessage);
        if($price!=null){
            $product_id=(int)substr($iRoom, ($pos = strpos($iRoom, 'Q')) !== false ? $pos + 1 : 0);
            $ownerID = $this->getOne("SELECT `author_id` FROM `bx_store_products` WHERE `id` = '$product_id' LIMIT 1");
            if($ownerID==$iPid){
                $sMessage.=_t('new_price') . ' : ' .$price. ' ' . _t('credits');
                $sPrice = ", `price` = '$price'";
            }  
        }


        if ($iPid && $sMessage != '') {
            $sSQL = "
                SELECT `id`
                FROM `a_fchat_messages`
                WHERE `sender` = '{$iPid}' AND `recipient` = '{$iRecipient}' AND UNIX_TIMESTAMP() - `when` < 2
                    AND `room` = '{$iRoom}'
                LIMIT 1
            ";
            $iLastId = $this->getOne($sSQL);
            if ($iLastId) return 2; // protection

            if ($this->_oConfig->bFrMode) {
                $bFriends = is_friends($iPid, $iRecipient);
                if ($bFriends) {
                    // send to mailbox
                    if (! $iOnlStatus) {
                        $sName = $this->escape(getNickName($iPid));
                        $sName = _t('_fch_new_mail_x', $sName);
                        $this->res("INSERT INTO `sys_messages` SET `Sender` = '{$iPid}', `Recipient` = '{$iRecipient}', `Text` = '{$sMessage}', `Date` = NOW(), `Subject` = '{$sName}', `New` = '1', `Type` = 'greeting'");
                    }

                    $sStat = ($iOnlStatus) ? '' : ", `offmsg` = '1'";
                    $bRes = $this->res("INSERT INTO `a_fchat_messages` SET `sender` = '{$iPid}', `recipient` = '{$iRecipient}', `message` = '{$sMessage}', `when` = UNIX_TIMESTAMP(), `room` = '{$iRoom}' {$sStat} {$sPrice}");
                    $this->ins_spy($iPid, $iRoom);
                    return ($bRes) ? 1 : 3;
                } else { // send to mailbox only
                    $sName = $this->escape(getNickName($iPid));
                    $sName = _t('_fch_new_mail_x', $sName);
                    $this->res("INSERT INTO `sys_messages` SET `Sender` = '{$iPid}', `Recipient` = '{$iRecipient}', `Text` = '{$sMessage}', `Date` = NOW(), `Subject` = '{$sName}', `New` = '1', `Type` = 'greeting'");
                    return 1;
                }
            } else {
                // send to mailbox
                if (! $iOnlStatus) {
                    $sName = $this->escape(getNickName($iPid));
                    $sName = _t('_fch_new_mail_x', $sName);
                    $this->res("INSERT INTO `sys_messages` SET `Sender` = '{$iPid}', `Recipient` = '{$iRecipient}', `Text` = '{$sMessage}', `Date` = NOW(), `Subject` = '{$sName}', `New` = '1', `Type` = 'greeting'");
                }

                $sStat = ($iOnlStatus) ? '' : ", `offmsg` = '1'";
                $bRes = $this->res("INSERT INTO `a_fchat_messages` SET `sender` = '{$iPid}', `recipient` = '{$iRecipient}', `message` = '{$sMessage}', `when` = UNIX_TIMESTAMP(), `room` = '{$iRoom}' {$sStat} {$sPrice}");
                $this->ins_spy($iPid, $iRoom);
                return ($bRes) ? 1 : 3;
            }
        }
    }
    function getRecentMessage($iPid) {
        if ($iPid) {
            $sSQL = "
                SELECT * , UNIX_TIMESTAMP( ) - `when` AS 'diff'
                FROM `a_fchat_messages`
                WHERE `recipient` = '{$iPid}' AND `room` = 0
                ORDER BY `id` DESC
                LIMIT 1
            ";

            $aMessage = $this->getRow($sSQL);
            $iDiff = (int)$aMessage['diff'];
            $iOffMsg = (int)$aMessage['offmsg'];
            if ($iDiff < ($this->_oConfig->iResFreq * 1.5) || $iOffMsg) { // -> new or offlinemsg
                if ($iOffMsg) {
                    $iId = (int)$aMessage['id'];
                    $this->res("UPDATE `a_fchat_messages` SET `offmsg` = '0' WHERE `id` = '{$iId}' LIMIT 1");
                }
                return (int)$aMessage['sender'];
            }
            return;
        }
    }
    function getDailyLastMsgs() {
        $sSQL = "
            SELECT *
            FROM `a_fchat_messages`
            WHERE DATE(FROM_UNIXTIME(`when`)) = CURDATE() AND `room` = 0
            GROUP BY `sender`, `recipient`
            ORDER BY `id` DESC
        ";
        return $this->getAll($sSQL);
    }
    //Add by afk, Find if room allready created : private room to start deal between client / customer
    function getPrivateNego($iRoom) {
        if ($iRoom) {
            $res = $this->getAll("SELECT * FROM `a_fchat_messages` WHERE `room` = '{$iRoom}'");
            if($res){
                return 'true';
            }else{
                return 'false';
            }
        }
    }
    function getRooms() {
        return $this->getAll("SELECT * FROM `a_fchat_rooms`");
    }
    function getRoomInfo($i) {
        $sSQL = "
            SELECT * 
            FROM `a_fchat_rooms`
            WHERE `id` = '{$i}'
        ";
        $aInfos = $this->getAll($sSQL);
        return $aInfos[0];
    }
    function addRoom($sTitle) {
        $sTitle = $this->escape(strip_tags($sTitle));
        if ($sTitle) {
            $this->res("INSERT INTO `a_fchat_rooms` SET `title` = '{$sTitle}', `owner` = '0', `when` = UNIX_TIMESTAMP()");
            return $this->lastId();
        }
    }
    function deleteRoom($iRoom) {
        $iRoom = $iRoom;
        if ($iRoom) {
            $this->res("DELETE FROM `a_fchat_messages` WHERE `room` = '{$iRoom}'");
            return $this->res("DELETE FROM `a_fchat_rooms` WHERE `id` = '{$iRoom}' LIMIT 1");
        }
    }
    function getSettingsCategory() {
        return (int)$this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'FChat' LIMIT 1");
    }
    function getNameProd($iId) {  
        $prod = $this->getOne("SELECT `title` FROM `bx_store_products` WHERE `id` = '$iId' LIMIT 1");
        return $prod;
    }
    function getPriceRangeProduct($iId) {  
        $prod = $this->getOne("SELECT `price_range` FROM `bx_store_products` WHERE `id` = '$iId' LIMIT 1");
        $prod = str_replace('%s', ' ', $prod);
        return $prod;
    }

    function ins_spy($iPid, $iRoom){
        if(strpos($iRoom, 'B')){// if message send, send notification
            $arrayRoom = array(
                'product' => (int)substr($iRoom, ($pos = strpos($iRoom, 'Q')) !== false ? $pos + 1 : 0),
                'vendor' => (int)substr($iRoom, ($pos = strpos($iRoom, 'W')) !== false ? $pos + 1 : 0),
                'customer' => (int)substr($iRoom, ($pos = strpos($iRoom, 'B')) !== false ? $pos + 1 : 0),
            );
            if($iPid==$arrayRoom['vendor']){
                $iRecipient=$arrayRoom['customer'];
            }else{
                $iRecipient=$arrayRoom['vendor'];
            }
            $nmprod=$this->getNameProd($arrayRoom['product']);
            $lnmprod=strlen($nmprod);
            $usname=$this->escape(getNickName($iPid));
            $lusname=strlen($usname);
            $plink=BX_DOL_URL_ROOT.$usname;
            $lplink=strlen($plink);
            $entry_u=BX_DOL_URL_ROOT.'m/fchat/&room='.$iRoom;
            $lentry_u=strlen($entry_u);
            //<a href="{profile_link}">{profile_nick}</a> Send message for product negociate <a href="{entry_url}">{entry_title}</a>
            $lks='a:4:{s:12:"profile_link";s:'.$lplink.':"'.$plink.'";s:12:"profile_nick";s:'.$lusname.':"'.$usname.'";s:9:"entry_url";s:'.$lentry_u.':"'.$entry_u.'";s:11:"entry_title";s:'.$lnmprod.':"'.$nmprod.'";}';
            $qry_spy="INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params,type) VALUES ('".$iPid."','".$iRecipient."','_bx_store_negociate','".$lks."','content_activity')";
            $result_spy=mysql_query($qry_spy)or die(mysql_error());
        }
    }
}
