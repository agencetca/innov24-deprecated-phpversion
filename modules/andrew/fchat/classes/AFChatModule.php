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
bx_import('BxDolModule');
bx_import('BxBaseMenu');

class AFChatModule extends BxDolModule {

    // Variables
    var $aPageTmpl;
    var $_iVisitorID;
    var $sModuleUrl;

    // Constructor
    function AFChatModule($aModule) {
        parent::BxDolModule($aModule);

        $this->aPageTmpl = array(
            'name_index' => 7,
            'header' => _t('_fch_main'),
            'header_text' => _t('_fch_main'),
        );

        $this->_iVisitorID = $this->getUserId();
        $this->sModuleUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
    }

    function actionHome() {
        $sBdur = BX_DOL_URL_ROOT;
        $iRoom = $_GET['room'];
        $sNewRoom = '';
        if (isAdmin()) {
            $aRVars = array('room' => $iRoom);
            $sNewRoom = $this->_oTemplate->parseHtmlByName('actions.html', $aRVars);

            if ($_POST['action'] == 'add_room' && $_POST['title'] != '') {
                $iLastRoom = $this->_oDb->addRoom($_POST['title']);
                header("Location:{$this->sModuleUrl}home/&room={$iLastRoom}");
            }
            if ($_POST['action'] == 'delete_room' && $_POST['room_id']) {
                $this->_oDb->deleteRoom($_POST['room_id']);
                header("Location:{$this->sModuleUrl}home/");
            }
        }
        $negoBool=false;
        $sChatName = _t('_fch_main_room');
        if ($iRoom) {
            $aRInfo = $this->_oDb->getRoomInfo($iRoom);
            $sChatName = $aRInfo['title'];
            $arrayRoom = array(
                'product' => (int)substr($iRoom, ($pos = strpos($iRoom, 'Q')) !== false ? $pos + 1 : 0),
                'vendor' => (int)substr($iRoom, ($pos = strpos($iRoom, 'W')) !== false ? $pos + 1 : 0),
                'customer' => (int)substr($iRoom, ($pos = strpos($iRoom, 'B')) !== false ? $pos + 1 : 0),
                );
            if(strpos($iRoom, 'B')){
                $negoBool=1;
                $sChatName = getNickName($arrayRoom['vendor']). ' > ' . getNickName($arrayRoom['customer']). ' ' ._t('for_product'). ' : "'. $this->_oDb->getNameProd($arrayRoom['product']). '" | ' . _t('default_price'). ' : ' .$this->_oDb->getPriceRangeProduct($arrayRoom['product']);
            }
        }
        if($negoBool==1){
                //set cookie Credit cart | Vendor | Product_id
                // $price=();
                $text_purpuse=_t('set_new_price');
                $text_amount_accept=_t('buy_with_credit');
                $text_actual_amount=_t('current_amount');
                $text_credits=_t('credits');
            if($this->_iVisitorID==$arrayRoom['vendor']){
                $negoHTML = <<<EOF
                <label for="my_purpuse" style="float:left;"><span>{$text_actual_amount} = <span id="chat_last_price"></span> <span style="color:#b2b2b2;">|</span> {$text_purpuse} : </label><input type="text" name="my_purpuse" id="my_purpuse" style="width: 100px;">
EOF;
            }else{
            // if($this->_iVisitorID==$arrayRoom['customer']){
                $initprice=$this->_oDb->getPriceRangeProduct($arrayRoom['product']);
                $negoHTML = <<<EOF
                <span>{$text_actual_amount} = <span id="chat_last_price">{$initprice} {$text_credits}</span></span> <input type="button" value="{$text_amount_accept}" onclick="window.location='{$sBdur}m/credit/payement/{$arrayRoom['vendor']}&Q={$arrayRoom['product']}'">
EOF;
            }
        }
        $aVars = array(
            'new_room' => $sNewRoom,
            'room' => $iRoom,
            'bx_if:can_type' => array(
                'condition' => ($this->_iVisitorID),
                'content' => array(
                    'room' => $iRoom
                )
            ),
            'bx_if:nego_bool' => array(
                'condition' => ($negoBool !== false),
                'content' => array(
                    'nego_html' => $negoHTML
                )
            )
        );
        $sCode = $this->_oTemplate->parseHtmlByName('main.html', $aVars);
        if ($this->_iVisitorID) {
            if($negoBool==1)
            if($this->_oDb->getPrivateNego($iRoom)=='false'){ //auto add negociate if room does'nt exist added by AFK
            $lkeystartnegociate=_t('Start_negociate');
                        $sCode .= <<<EOF
<script>
$(document).ready(function(){
    $.post('{$sBdur}m/fchat/action/message/', { message: '{$lkeystartnegociate} ', room: $('.chat_submit_form input[name=room]').val() }, 
        function(data_res){
            if (data_res.res == 1) {
                $('.chat_submit_form .success').fadeIn('slow', function () {
                    $('.chat_submit_form input[name=message]').val('');
                    $(this).fadeOut('slow'); 
                }); 
            } else if (data_res.res == 2) {
                $('.chat_submit_form .protect').fadeIn('slow', function () {
                    $(this).fadeOut('slow'); 
                }); 
            } else {
                $('.chat_submit_form .error').fadeIn('slow', function () {
                    $(this).fadeOut('slow'); 
                }); 
            }
        }, "json"
    );
});
</script>
EOF;
        }
            $sCode .= <<<EOF
<script>
$(function() {
    $('.chat_submit_form').submit(function() {
        $.post('{$sBdur}m/fchat/action/message/', { message: $('.chat_submit_form input[name=message]').val(), room: $('.chat_submit_form input[name=room]').val(), price: $('.chat_submit_form input[name=my_purpuse]').val() }, 
            function(data_res){
                if (data_res.res == 1) {
                    $('.chat_submit_form .success').fadeIn('slow', function () {
                        $('.chat_submit_form input[name=message]').val('');
                        $('.chat_submit_form input[name=my_purpuse]').val('');
                        $(this).fadeOut('slow'); 
                    }); 
                } else if (data_res.res == 2) {
                    $('.chat_submit_form .protect').fadeIn('slow', function () {
                        $(this).fadeOut('slow'); 
                    }); 
                } else {
                    $('.chat_submit_form .error').fadeIn('slow', function () {
                        $(this).fadeOut('slow'); 
                    }); 
                }
            }, "json"
        );
        return false; 
    });

    updateLastNav = function() {
        $.getJSON('{$sBdur}m/fchat/action/update_last_nav/'+'&u='+Math.random(), function() {
            setTimeout(function(){
               updateLastNav();
            }, 180000);
        });
    }
    updateLastNav();
});
</script>
EOF;
        }

        // $aFRooms = array();
        // $aFRooms['room0'] = array('href' => $this->sModuleUrl.'home/', 'title' => _t('_fch_main_room'), 'onclick' => '', 'active' => ($iRoom == 0));

        // $aRooms = $this->_oDb->getRooms();
        // foreach ($aRooms as $i => $aRoom) {
        //     $aFRooms['room' . $aRoom['id']] = array('href' => $this->sModuleUrl.'home/&room='.$aRoom['id'], 'title' => $aRoom['title'], 'onclick' => '', 'active' => ($iRoom == $aRoom['id']));
        // }
        if($this->_iVisitorID==$arrayRoom['vendor']) {
            $aFRooms['actions'] = array('href' => 'javascript: void(0)', 'title' => _t('delete_room'), 'onclick' => "$('.delete_room_form input[type=submit]').click();", 'active' => false);
            // $aFRooms['actions'] = array('href' => 'javascript: void(0)', 'title' => 'actions', 'onclick' => "$('.fchat_actions').slideToggle(400);", 'active' => false);

        bx_import('BxDolPageView');
        $sRoomsTabs = BxDolPageView::getBlockCaptionMenu(mktime(), $aFRooms);
        }else{
            $sRoomsTabs = "";
        }

        $this->aPageTmpl['js_name'] = array('chat.js');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => DesignBoxContent(_t('_fch_room_x', $sChatName), $sCode, 1, $sRoomsTabs)));
    }

    function actionAction($sActionParam = '') {
        require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );
        $oJson = new Services_JSON();

        $iRoom = $_GET['room'];
        $sAction = process_db_input($sActionParam, BX_TAGS_STRIP);

        header('Content-type: application/json');
        switch ($sAction) {
            case 'get_private_messages':
                $iRecipient = (int)$_GET['recipient'];
                $iStatus = get_user_online_status($iRecipient);
                list($sMessages, $iCount, $iFirst) = $this->getMessages($iRecipient);
                $sOlderMsgs = ($iCount == 10) ? '<p class="pcoms" onclick="getOlderMessages('.$iRecipient.', 1, this)">'._t('_fch_get_older_msg').'</p>' : '';
                echo $oJson->encode(array('messages' => $sOlderMsgs . $sMessages, 'status' => $iStatus, 'count' => $iCount));
                break;
            case 'get_older_messages':
                $iRecipient = (int)$_GET['recipient'];
                $iSkip = (int)$_GET['iskip'];
                // $iStatus = get_user_online_status($iRecipient);
                list($sMessages, $iCount, $iFirst) = $this->getMessages($iRecipient, 0, $iSkip);
                $sOlderMsgs = ($iCount == 10) ? '<p class="pcoms" onclick="getOlderMessages('.$iRecipient.', '.($iSkip + 1).', this)">'._t('_fch_get_older_msg').'</p>' : '';
                echo $oJson->encode(array('messages' => $sOlderMsgs . $sMessages, /*'status' => $iStatus,*/ 'count' => $iCount));
                break;
            case 'get_last_messages': // for rooms
                list($sMessages, $iCount, $iFirst) = $this->getMessages(0, $iRoom);
                echo $oJson->encode(array('messages' => $sMessages, 'count' => $iCount));
                break;            
            case 'get_last_price': // for afk
                
                echo $oJson->encode(array('res' => $this->_oDb->getLastPrice($iRoom)));
                break;
            case 'priv_message':
                $iRecipient = (int)$_POST['recipient'];
                if ($iRecipient) {
                    $iO = get_user_online_status($iRecipient);
                    $sOnl = ($iO) ? '' : _t('_fch_anno_off');
                    $iRes = $this->_oDb->acceptMessage($this->_iVisitorID, $_POST['priv_message'], 0, $iRecipient, $iO);
                    echo $oJson->encode(array('result' => $iRes, 'onl' => $sOnl));
                }
                break;
            case 'message':
                $iPostRoom = $_POST['room'];
                $iRes = $this->_oDb->acceptMessage($this->_iVisitorID, $_POST['message'], $iPostRoom, 0, 0, (int)$_POST['price']);
                echo $oJson->encode(array('res' => $iRes));
                break;
            case 'update_last_nav':
                if ($this->_iVisitorID) {
                    echo $oJson->encode(array('id' => 0, 'name' => ''));
                    update_date_lastnav($this->_iVisitorID);
                }
                break;
            case 'check_new_messages':
                $iSender = $this->_oDb->getRecentMessage($this->_iVisitorID);

                if ($iSender && $this->_iVisitorID) {
                    $sName = getNickname($iSender);

                    echo $oJson->encode(array('id' => $iSender, 'name' => $sName));
                    //echo $oJson->encode(array('id' => $iSender, 'name' => _t('_fch_pchat_x', $sName)));
                } else {
                    echo $oJson->encode(array('id' => 0, 'name' => ''));
                }
                break;
            case 'refresh_mgroups':
                echo $oJson->encode(array('html' => $this->serviceFchatInjection(true)));
                break;
        }
        exit;
    }
    function actionHistory($sPid = '') {
        $iPid = (int)$sPid;
        $sNameHist = '';
        if ($iPid) {
            $sMessages = '';
            $aMessages = $this->_oDb->getMessages($iPid, 0, $this->_iVisitorID, 0, 1000);
            // echoDbg($aMessages);
            $iCount = count($aMessages);
            if ($iCount) {
                foreach ($aMessages as $i => $aMessage) {
                    $sExStyles = $sExJS = '';
                    $iDiff = (int)$aMessage['diff'];
                    /*if ($iDiff < 7) { // less than 7 seconds
                        $sExStyles = 'style="display:none;"';
                        $sExJS = "<script> $('#message_{$aMessage['id']}').fadeIn('slow'); </script>";
                    }*/

                    //$sWhen = date("H:i:s", $aMessage['when']);
                    $sWhen = defineTimeInterval($aMessage['when']);
                    $sName = getNickname($aMessage['sender']);
                    $sAvatar = $GLOBALS['oFunctions']->getMemberIcon($aMessage['sender'], 'left');
                    $sMessages .= '<div class="message" id="message_'.$aMessage['id'].'" '.$sExStyles.'>'.$sAvatar.'<b><a href="profile.php?ID='.$aMessage['sender'].'" target="_self">'.$sName . ':</a></b> ' . $this->processSmiles($aMessage['message']) . '<span>(' . $sWhen . ')</span><div class="clear_both"></div></div>' . $sExJS;
                }
                $sMessages = '<div class="chat_messages afc_emm">' . $sMessages . '</div>';
            } else {
                $sMessages = MsgBox(_t('_Empty'));
            }
            $sNameHist = getNickname($iPid);
        }
        $this->aPageTmpl['js_name'] = array('chat.js');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => DesignBoxContent(_t('_fch_history_x', $sNameHist), $this->getWrap($sMessages), 1)));
    }
    function getWrap($sCode) {
        return '<div class="dbContent">' . $sCode . '<div class="clear_both"></div></div>';
    }
    function processSmiles($sText) {
        // return $sText;
        $aEmm = array(
        ':)' => '<i class="smile"></i>',
        ':(' => '<i class="frown"></i>',
        ':P' => '<i class="tongue"></i>',
        '=D' => '<i class="grin"></i>',
        ':o' => '<i class="gasp"></i>',
        ';)' => '<i class="wink"></i>',
        ':v' => '<i class="pacman"></i>',
        '>:(' => '<i class="grumpy"></i>',
        ':-/' => '<i class="unsure"></i>',
        ':\'(' => '<i class="cry"></i>',
        '^_^' => '<i class="kiki"></i>',
        '8)' => '<i class="glasses"></i>',
        'B|' => '<i class="sunglasses"></i>',
        '^v^' => '<i class="heart"></i>',
        '3:)' => '<i class="devil"></i>',
        'O:)' => '<i class="angel"></i>',
        '-_-' => '<i class="squint"></i>',
        'o.O' => '<i class="confused"></i>',
        '>:o' => '<i class="upset"></i>',
        ':-3' => '<i class="colonthree"></i>',
        '(y)' => '<i class="like"></i>'
        );
        return str_replace(array_keys($aEmm), array_values($aEmm), $sText);
    }

    function actionAdministration($sAction = '', $sSubUnit = '', $sSubUnit2 = '') {
        if (! isAdmin()) {
            $this->_oTemplate->displayAccessDenied();
            return;
        }

        $sSettings = $this->getAdministrationSettings();
        $sCode = DesignBoxAdmin(_t('_fch_administration'), $sSettings, 1);

        $this->aPageTmpl['name_index'] = 9;
        $this->aPageTmpl['header'] = _t('_fch_administration');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode), array(), array(), true);
    }

    function getAdministrationSettings() {
        $iId = $this->_oDb->getSettingsCategory();
        if (empty($iId)) return MsgBox(_t('_sys_request_page_not_found_cpt'));

        bx_import('BxDolAdminSettings');
        $vResult = '';
        if (isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new BxDolAdminSettings($iId);
            $vResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();
        if ($vResult !== true && !empty($vResult))
            $sResult = $vResult . $sResult;
        return $sResult;
    }

    function serviceFchatInjection($bOnlyGroups = false) {
        if ($this->_iVisitorID) {
            if ($this->_oConfig->sDisplay == '') {
                return;
            }

            $aDisplay = explode(',', $this->_oConfig->sDisplay);
            $sOnlineFriends = $sOnline = $sFriends = $sLast = '';
            $aLmem1 = $aLmem2 = $aLmem3 = $aLmem4 = array();
            foreach ($aDisplay as $sType) {
                switch($sType) {
                    case 'onlinefriends':
                        list($sOnlineFriends, $aLmem1) = $this->getMemberList($this->_oConfig->iOnlineCnt, true, true);
                        break;
                    case 'online':
                        list($sOnline, $aLmem2) = $this->getMemberList($this->_oConfig->iOnlineCnt, true, false, $aLmem1);
                        break;
                    case 'friends':
                        $aLMembers3 = array_merge($aLmem1, $aLmem2); // OnlineFr + OnlMemb
                        $aLMembers3 = array_unique($aLMembers3);
                        list($sFriends, $aLmem3) = $this->getMemberList($this->_oConfig->iOfflineCnt, false, true, $aLMembers3);
                        break;
                    case 'last':
                        $aLMembers = array_merge($aLmem1, $aLmem2, $aLmem3, $aLmem4); // listed members
                        $aLMembers = array_unique($aLMembers);
                        list($sLast, $aLmem4) = $this->getMemberList($this->_oConfig->iOfflineCnt, false, false, $aLMembers);
                        break;
                }
            }

            $aPinfo = getProfileInfo($this->_iVisitorID);
            $sFchatVim = ($this->_oDb->isModule('messenger')) ? 'true' : 'false';
            $sToggLbl = _t('_fch_new_msg_x');
            $sName = getNickname($this->_iVisitorID);
            $sAvatar = $GLOBALS['oFunctions']->getMemberIcon($this->_iVisitorID, 'left');
            $sAvatar = str_replace(array("\r","\n"),"",$sAvatar);
            $sAvatar = str_replace(array("'","\'"),"",$sAvatar);
            $sJs = <<<EOF
<script language="javascript" type="text/javascript">
<!--
    var iFchatPid = "{$this->_iVisitorID}";
    var iFchatPname = "{$sName}";
    var iFchatPthumb = '{$sAvatar}';
    var iFchatPas = "{$aPinfo['Password']}";
    var bFchatVim = {$sFchatVim};
    var iFchatFreq = {$this->_oConfig->iResFreq};
    var iFchatGrFreq = {$this->_oConfig->iResGrFreq};
    var sFchatTogg = '{$sToggLbl}';
-->
</script>    
EOF;
            $sJs .= $this->_oTemplate->addJs('priv_chat.js', true);
            $sCss = $this->_oTemplate->addCss('private.css', true);

            $aVariables = array (
                'bx_if:show_onlinefriends' => array(
                    'condition' => (array_search('onlinefriends', $aDisplay) !== false),
                    'content' => array(
                        'online_friends' => $sOnlineFriends
                    )
                ),
                'bx_if:show_online' => array(
                    'condition' => (array_search('online', $aDisplay) !== false),
                    'content' => array(
                        'online_members' => $sOnline
                    )
                ),
                'bx_if:show_friends' => array(
                    'condition' => (array_search('friends', $aDisplay) !== false),
                    'content' => array(
                        'friends' => $sFriends
                    )
                ),
                'bx_if:show_last' => array(
                    'condition' => (array_search('last', $aDisplay) !== false),
                    'content' => array(
                        'last_members' => $sLast
                    )
                ),
            );
            if ($bOnlyGroups) {
                return $this->_oTemplate->parseHtmlByName('private_inner.html', $aVariables);
            }

            return $sCss . $this->_oTemplate->parseHtmlByName('private.html', $aVariables) . $sJs;
        }
    }

    function getMemberList($iLim, $bOnline = false, $bFriends = false, $aSkip = array()) {
        $sChatImg = $this->_oTemplate->getImageUrl('chat.png');

        $sCode = '';
        $a = array();
        if ($bFriends) {
            $sWhereCondition = ($bOnline) ? " AND (p.`DateLastNav` > SUBDATE(NOW(), INTERVAL " . $this->_oConfig->iOnlineTime . " MINUTE))" : '';
            $aProfiles = getMyFriendsEx($this->_iVisitorID, $sWhereCondition, 'activity', 'LIMIT '.$iLim);
            // echoDbg($aProfiles);
        } else {
            $aProfiles = $this->_oDb->getMemberList($iLim, $bOnline);
            // echoDbg($aProfiles);
        }
        if (count($aProfiles)) {
            if (count($aSkip)) {
                // echoDbg($aProfiles);
                // remove skipped items
                foreach ($aProfiles as $i => $aProfile) {
                    $iPd = (isset($aProfile['ID'])) ? $aProfile['ID'] : $i;
                    if (in_array($iPd, $aSkip)) {
                        unset($aProfiles[$i]);
                    }
                }
                // echoDbg($aProfiles);
            }
            foreach ($aProfiles as $i => $aProfile) {
                $iPid = ($bFriends) ? $i : $aProfile['ID'];
                if ($this->_iVisitorID != $iPid) {
                    $sName = getNickname($iPid);
                    $sProfileUrl = getProfileLink($iPid);
                    $sAvatar = $GLOBALS['oFunctions']->getMemberIcon($iPid, 'left');

                    $sChat = '<img id="'.$iPid.'" alt="chat" src="'.$sChatImg.'" class="pchat" title="'. $sName .'" />';
                    //$sChat = '<img id="'.$iPid.'" alt="chat" src="'.$sChatImg.'" class="pchat" title="'. _t('_fch_pchat_x', $sName) .'" />';
                    $sCode .= '<div id="'.$iPid.'" title="'.$sName.'">'.$sAvatar.'<p><a href="'.$sProfileUrl.'" target="_self">'.$sName.'</a>'.$sChat.'</p></div>';
                    array_push($a, $iPid);
                }
            }
        }
        $sCode = ($sCode) ? $sCode : MsgBox(_t('_Empty'));
        return array('<div class="profiles">' . $sCode . '</div>', $a);
    }

    function getMessages($iRecipient = 0, $iRoom = 0, $iSkip = 0) {
        $sMessages = '';
        $aMessages = $this->_oDb->getMessages($iRecipient, $iRoom, $this->_iVisitorID, $iSkip);
        $iCount = count($aMessages);
        $iFirst = 0;
        if ($iCount) {
            foreach ($aMessages as $i => $aMessage) {
                $sExStyles = $sExJS = '';
                $iDiff = (int)$aMessage['diff'];
                if ($iDiff < $this->_oConfig->iResFreq) { // less than 7 seconds
                    // $sExStyles = 'style="display:none;"';
                    $sExJS = "<script>  if ($('.priv_chat_tab#pcid{$aMessage['sender']} .priv_chat_submit_form textarea').is(':focus')) { oIncSnd.currentTime = 0; oIncSnd.play(); } </script>";
                }

                //$sWhen = date("H:i:s", $aMessage['when']);
                $sWhen = defineTimeInterval($aMessage['when']);
                $sName = getNickname($aMessage['sender']);
                $sAvatar = $GLOBALS['oFunctions']->getMemberIcon($aMessage['sender'], 'left');
                $sAvatar = str_replace(array("\r","\n", '	'),"",$sAvatar);
                $sMessages .= '<div class="message" id="message_'.$aMessage['id'].'" '.$sExStyles.'>'.$sAvatar.'<b><a href="profile.php?ID='.$aMessage['sender'].'" target="_self">'.$sName . ':</a></b> ' . $aMessage['message'] . '<span>(' . $sWhen . ')</span><div class="clear_both"></div>'.$sExJS.'</div>';
                // $sMessages .= '<div class="message" id="message_'.$aMessage['id'].'" '.$sExStyles.'>' . $aMessage['message'] . '<div class="clear_both"></div></div>';
                if (! $iFirst) {
                    $iFirst = $aMessage['id'];
                }
            }
        } else {
            if ($iRecipient) {
                $sMessages .= '<div class="message" id="message_0">'._t('_fch_mem_first').'</div>';
            } else {
                $sMessages .= '<div class="message" id="message_0">'.MsgBox(_t('_fch_chat_empty')).'</div>';
            }
        }
        
        return array($sMessages, $iCount, $iFirst);
    }

    function servicePageComposeBlock() {
        if ($this->_iVisitorID) {
            if ($this->_oConfig->sDisplay == '') {
                return;
            }

            $aDisplay = explode(',', $this->_oConfig->sDisplay);
            $sOnlineFriends = $sOnline = $sFriends = $sLast = '';
            $aLmem1 = $aLmem2 = $aLmem3 = $aLmem4 = array();
            foreach ($aDisplay as $sType) {
                switch($sType) {
                    case 'onlinefriends':
                        list($sOnlineFriends, $aLmem1) = $this->getMemberList($this->_oConfig->iOnlineCnt, true, true);
                        break;
                    case 'online':
                        list($sOnline, $aLmem2) = $this->getMemberList($this->_oConfig->iOnlineCnt, true);
                        break;
                    case 'friends':
                        list($sFriends, $aLmem3) = $this->getMemberList($this->_oConfig->iOfflineCnt, false, true);
                        break;
                    case 'last':
                        $aLMembers = array_merge ($aLmem1, $aLmem2, $aLmem3, $aLmem4); // listed members
                        $aLMembers = array_unique($aLMembers);
                        list($sLast, $aLmem4) = $this->getMemberList($this->_oConfig->iOfflineCnt, false, false, $aLMembers);
                        break;
                }
            }

            $aVariables = array (
                // 'online_members' => $this->getMemberList($this->_oConfig->iOnlineCnt, true),
                // 'friends' => $this->getMemberList($this->_oConfig->iOfflineCnt, false, true),
                // 'profiles' => $this->getMemberList($this->_oConfig->iOfflineCnt),
                'bx_if:show_online' => array(
                    'condition' => (array_search('online', $aDisplay) !== false),
                    'content' => array(
                        'online_members' => $sOnline
                    )
                ),
                'bx_if:show_friends' => array(
                    'condition' => (array_search('friends', $aDisplay) !== false),
                    'content' => array(
                        'friends' => $sFriends
                    )
                ),
                'bx_if:show_last' => array(
                    'condition' => (array_search('last', $aDisplay) !== false),
                    'content' => array(
                        'last_members' => $sLast
                    )
                ),
            );
            return '<div class="dbContent">' . $this->_oTemplate->parseHtmlByName('members.html', $aVariables)  . '</div>';
        }
    }

    /*function actionCron() {
        require_once('AFChatCron.php');
        $oCron = BxDolModule::getInstance('AFChatCron');
        $oCron->processing();

        exit;
    }*/
}