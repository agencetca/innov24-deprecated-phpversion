<?php
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

bx_import ('BxDolProfileFields');
bx_import ('BxDolFormMedia');

class BxAlertsFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxAlertsFormAdd ($oMain, $iProfileId, $iEntryId = 0, $iThumb = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
   
   		$sDefaultTitle = stripslashes($_REQUEST['title']);

        $aProfile = getProfileInfo($oMain->_iProfileId);
		$sEmail = $aProfile['Email'];
 
		$aTypeOptions = $this->_oDb->getUnits();

		$aNotifyOptions = array(
			'now' => _t('_modzzz_alerts_notify_now'),
			'daily' => _t('_modzzz_alerts_notify_daily'),
			'weekly' => _t('_modzzz_alerts_notify_weekly')  
		);

		$aSearchOptions = array(
			'both' => _t('_modzzz_alerts_notify_both'),
			'title' => _t('_modzzz_alerts_notify_topic_only'),
			'desc' => _t('_modzzz_alerts_notify_desc_only')  
		);


        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_alerts',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_alerts_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'phrase',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_alerts_form_header_info')
                ),                
 
                'deliver' => array(
                    'type' => 'text',
                    'name' => 'deliver',
                    'caption' => _t('_modzzz_alerts_form_caption_deliver_to'), 
                    'value' => $sEmail,
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,200),
                        'error' => _t ('_modzzz_alerts_form_err_deliver_to'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true, 
                ),  
                'phrase' => array(
                    'type' => 'text',
                    'name' => 'phrase',
                    'caption' => _t('_modzzz_alerts_form_caption_phrase'),
                    'info' => _t('_modzzz_alerts_form_info_phrase'),
					'value' => $sDefaultTitle, 
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_alerts_form_err_phrase'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  
                'unit' => array(
                    'type' => 'select_box',
                    'name' => 'unit',
                    'caption' => _t('_modzzz_alerts_form_caption_type'),
                    'info' => _t('_modzzz_alerts_form_info_type'),
                    'values' => $aTypeOptions,
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_alerts_form_err_type'),
                    ),
                    'db' => array (
						'pass' => 'Categories', 
                    ),
                    'display' => true,
                ), 
                'search' => array(
                    'type' => 'select',
                    'name' => 'search',
                    'caption' => _t('_modzzz_alerts_form_caption_search'),
                    'info' => _t('_modzzz_alerts_form_info_search'),
                    'values' => $aSearchOptions,
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_alerts_form_err_search'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                 ),					
                 'notify' => array(
                    'type' => 'select',
                    'name' => 'notify',
                    'caption' => _t('_modzzz_alerts_form_caption_notify'),
                    'info' => _t('_modzzz_alerts_form_info_notify'),
                    'values' => $aNotifyOptions,
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_alerts_form_err_notify'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => false,
                ),                            
            ),            
        );
 
        parent::BxDolFormMedia ($aCustomForm);
    }

}

?>