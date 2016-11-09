<?php
/*************************Product owner info********************************
*
*     author               : Boonexpert 
*     contact info         : boonexpert@gmail.com
*
/*************************Product info**************************************
*
*                          Master Database Backup
*                          ------------------------
*     version              : 1.0
*     date		   : 27 December 2010
*     compability          : Dolphin 7.0.0 - 7.0.4
*     License type         : Custom
*
* IMPORTANT: This is a commercial product made by Boonexpert and cannot be modified for other than personal use. 
* This product cannot be redistributed for free or a fee without written permission from Boonexpert. 
*
*     Upgrade possibilities : All future upgrades will be added to this product package
*
****************************************************************************/

bx_import('BxDolModule');


class MdbModule extends BxDolModule {



    function MdbModule(&$aModule) {
        parent::BxDolModule($aModule);
    }

function actionAdministration () {

        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

$aPageCode = <<<DUMPER_INCLUDING
<table width=100% cellspacing=0 cellpadding=0 border=0>
  <tr>
      <td>

	<iframe style="border:none;width:100%;height:320px;" src='boonexpert/MasterBackup/classes/MdbDumper.php'></iframe>

      </td>
    </tr>
    <tr>
      <td>

	<div class="adm-db-head">
	    Files
	</div>
      </td>
    </tr>

    <tr>
      <td style='padding:0px;'>

	<iframe id='filelist_frame' name='filelist_frame' style="border:none;width:100%;height:400px;" src='boonexpert/MasterBackup/classes/MdbFiles.php'></iframe>

      </td>
    </tr>
</table>

DUMPER_INCLUDING;


        $this->_oTemplate->pageStart();

        echo DesignBoxAdmin (_t('_mbu_main_window'), $aPageCode);

        $this->_oTemplate->pageCodeAdmin (_t('_mbu_main_name'));
    }




function actionHome () {


        $this->_oTemplate->pageStart();

         $aVars = array (

		        );

        echo $this->_oTemplate->parseHtmlByName('main', $aVars);
        $this->_oTemplate->pageCode(_t('_mbu_main_name'), true);
    }


}

?>
