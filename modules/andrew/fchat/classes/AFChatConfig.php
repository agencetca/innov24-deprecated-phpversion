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
bx_import('BxDolConfig');

class AFChatConfig extends BxDolConfig {

    var $iOnlineCnt;
    var $iOfflineCnt;
    var $iOnlineTime;
    var $sDisplay;
    var $iResFreq;
    var $iResGrFreq;
    var $bFrMode;

    /**
     * Constructor
     */
    function AFChatConfig($aModule) {
        parent::BxDolConfig($aModule);

        $this->iOnlineCnt = getParam('a_fchat_online_cnt');
        $this->iOfflineCnt = getParam('a_fchat_offline_cnt');
        $this->iOnlineTime = getParam('member_online_time');
        $this->sDisplay = getParam('a_fchat_display');
        $this->iResFreq = 1; // 2 seconds
        $this->iResGrFreq = 300; // 5 mins
        $this->bFrMode = (getParam('a_fchat_fr_mode') == 'on');
    }
}
