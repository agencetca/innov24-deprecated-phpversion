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
bx_import('BxDolCron');
require_once('AFChatModule.php');

class AFChatCron extends BxDolCron {
    var $oModule;

    function AFChatCron() {
        $this->oModule = BxDolModule::getInstance('AFChatModule');
    }

    function processing() {
        // notify members about offline messages
        $aLastMsgs = $this->oModule->_oDb->getDailyLastMsgs();

        if (is_array($aLastMsgs) && count($aLastMsgs)) {
            bx_import('BxDolEmailTemplates');
            $rEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $rEmailTemplate->getTemplate('t_fchat_notify', 1);
            $aPlus = array();

            $aRecs = array();
            foreach ($aLastMsgs as $i => $aInfo) {
                if ($aInfo['offmsg'] == '1') {
                    $aRecs[$aInfo['recipient']] = 1;
                }
            }
            foreach ($aRecs as $iPid => $i) {
                $aPinfo = getProfileInfo($iPid);
                if ($aPinfo['Email']) {
                    echo $aPinfo['Email'] . '<br />';
                    sendMail($aPinfo['Email'], $aTemplate['Subject'], $aTemplate['Body'], 0, $aPlus, 'html');
                }
            }
        }
    }
}
