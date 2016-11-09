<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.modloaded.com
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
* see license.txt file; if not, write to marketing@modloaded.com
***************************************************************************/

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolProfileFields.php');

class MlPagesFormSearch extends BxTemplFormView {

    function MlPagesFormSearch () {

        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
        $aCountries = array_merge (array('' => _t('_ml_pages_all_countries')), $aCountries);

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_pages',
                'action'   => '',
                'method'   => 'get',
            ),      

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
                'csrf' => array(
                    'disable' => true,
                ),
            ),
                  
            'inputs' => array(
                'Keyword' => array(
                    'type' => 'text',
                    'name' => 'Keyword',
                    'caption' => _t('_ml_pages_caption_keyword'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_ml_pages_err_keyword'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),                
                'Country' => array(
                    'type' => 'select_box',
                    'name' => 'Country',
                    'caption' => _t('_ml_pages_caption_country'),
                    'values' => $aCountries,
                    'required' => true,
                    'checker' => array (
                        'func' => 'preg',
                        'params' => array('/^[a-zA-Z]{0,2}$/'),
                        'error' => _t ('_ml_pages_err_country'),
                    ),                                        
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{0,2})/'),
                    ),                    
                ),
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => true,
                ),
            ),            
        );

        parent::BxTemplFormView ($aCustomForm);
    }
}

?>
