<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
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

bx_import('BxDolModule');

class AfkCfgmeModule extends BxDolModule {

    function AfkCfgmeModule(&$aModule) {        
        parent::BxDolModule($aModule);
        // Need to be the same sort than return array of moduleDB getCatArray()
        //             'Events' => $bx_events,
       
        $this->interestCat = array(
            'Events',
            'News'      ,
            'Jobs'      ,
            //'Videos'    ,
            'Groups'    ,
            //'Gigs'      ,
            // 'Pages'     ,
            //'Photos'    ,
            // 'ImageNews' ,
            //'Sounds'    ,
            'Store'     ,
            //'Opinions'  ,
            //'Files'     ,
        );
        $this->aForm = array(

            'form_attrs' => array(
                'name'     => 'form_cfgme',
                'action'   => '',
                'method'   => 'post',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'afk_cfgme_interest',
                    'key' => 'id',
                    'submit_name' => 'submit_form',
                ),
            ),
            'inputs' => array(
                'submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Save'),
                    'colspan' => true,
                ),            
            ),            
        );

        $this->catArray=$interest=$this->_oDb->getCatArray();
        $tab=array();
        foreach ($this->catArray as $postType => $catName) {
            $temp=array(
                    'header_cat_'.$postType => array(
                        'type' => 'block_header',
                        'caption' => $postType,
                        'collapsable' => true,
                        'collapsed' => false, 
                    ),
            );
            $tab=array_merge($tab ,$temp);
            $values=array();
            foreach ($catName as $cat => $catNme) {
                $values[$cat]=$catNme['Categories'];
            }
            $temp=array(
                'cat_'.$postType => array(
                    'type' => 'checkbox_set',
                    'name' => $postType,
                    'caption' => $postType,
                    'values' => $values,
                    'db' => array(
                        'pass' => 'Set', 
                    ),
                ));
            $tab=array_merge($tab ,$temp);
        }
        $this->aForm['inputs'] = array_merge($tab,$this->aForm['inputs']);
        $interest=$this->_oDb->getAllInterest($_COOKIE['memberID']);

        $allinterest=explode('}', $interest['cat_id']);
        $this->myInterest=array();
        foreach ($allinterest as $key => $value) {
            $tempInt=explode('{', $allinterest[$key]); //$tempInt[0]= Events | $tempInt[1] = Expedition,birthday,etc.
            $this->myInterest[$key]['title'] = $tempInt[0];
            $this->myInterest[$key]['catName'] = explode(',',$tempInt[1]);
        }
        $this->myInterest=array_slice($this->myInterest, 0, -1); //remove last row over generated
    }
    /**
     * [actionHome is a main page showed up]
     */
    function actionHome () {
	

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the user design
        echo 'this page exist for setup your account, choose all your interest point.';

        $interest=$this->_oDb->getAllInterest($_COOKIE['memberID']);
        if(empty($interest)){
            echo "You have not yet configure, your profile interest, please make this and save to order any information you will receive";
            echo '<br/><br/> To add setting <a href="'.BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'add/">click here</a>';

        }else{

            $interest['author'] = getNickName($interest['user_id']);

            $interest['cat_id']; 
            $allinterest=explode('}', $interest['cat_id']);
            $myInterest=array();

            foreach ($allinterest as $key => $value) {
                $tempInt=explode('{', $allinterest[$key]); //$tempInt[0]= Events | $tempInt[1] = Expedition,birthday,etc.
                $myInterest[$key]['title'] = $tempInt[0];
                $myInterest[$key]['catName'] = explode(',',$tempInt[1]);
                $myInterest[$key]['url'] = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $interest['id'];
            }
            $myInterest=array_slice($myInterest, 0, -1); //remove last row over generated

            echo "<h1>Your interest center : </h1>";
            foreach ($myInterest as $key) {
                if(!empty($key['catName'][0])){
                    echo '<h2>'.$key['title']. ' : </h2>';
                    foreach ($key['catName'] as $key => $value) {
                        echo '- '.$value.'<br/>';
                    }
                }
            }

            echo '<br/><br/> To edit this <a href="'.BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $interest['id'].'">click here</a>';
            // var_dump($myInterest);
        }

        $aVars = array( // define template variables
            //'bx_repeat:page' => $myInterest,
        );

        echo $this->_oTemplate->parseHtmlByName('main', $aVars); // output posts list

        $this->_oTemplate->pageCode(_t('afk_cfgme'), true); // output is completed, display all output above data wrapped by user design

    }

    function actionView ($iEntryId) {

        $aEntry = $this->_oDb->getEntryById ((int)$iEntryId);
        if (!$aEntry) { // check if entry exists
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the user design

        $aVars = array (
            'title' => $aEntry['title'],
            'text' => $aEntry['text'],
            'author' => getNickName($aEntry['author_id']),
            'added' => defineTimeInterval($aEntry['added']),
            'bx_if:edit' => array(
                'condition' => ($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aEntry['author_id'] == $_COOKIE['memberID'],
                'content' => array(
                    'edit_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $aEntry['id'],
                ),
            ),        
        );
        echo $this->_oTemplate->parseHtmlByName('view', $aVars); // display post 

        $this->_oTemplate->pageCode($aEntry['title'], true); // output is completed, display all output above data wrapped by user design
    }

    function actionAdd () {

        if (!$GLOBALS['logged']['member'] && !$GLOBALS['logged']['admin']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the user design

        bx_import ('BxTemplFormView'); // import forms class

        $oForm = new BxTemplFormView ($this->aForm); // create foprms class
        $oForm->initChecker(); // init form checker

        if ($oForm->isSubmittedAndValid ()) { // if form is submitted and not form errors were found, save form data
            $add='';
            foreach ($this->interestCat as $key => $value) { //Hack DB formating to own format
                $curData = $oForm->getCleanValue($value);
                $add.= $value.'{';
                unset($this->aForm['inputs']['cat_'.$value]);
                foreach ($curData as $data) {
                    $data=$this->catArray[$value][$data]['Categories']; //get Name of cat hack
                    $add.=$data.',';
                }
                $add=substr($add, 0, -1);
                $add.='}';
            }
            $aValsAdd = array ( // add additional fields
                'cat_id' => $add,
                'user_id' => $_COOKIE['memberID'],
            );
            $oForm = new BxTemplFormView ($this->aForm); // create forms class         
            $iEntryId = $oForm->insert ($aValsAdd); // insert data to database

            if ($iEntryId) { // if post was successfully added
                $sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $iEntryId; 
                header ('Location:' . $sRedirectUrl); // redirect to created post view page
                exit;
            } else {
                MsgBox(_t('_Error Occured')); // if error occured display erroro message
            }                

        } else {

            echo $oForm->getCode (); // display form, if the form is not submitted or data is invalid

        }

        $this->_oTemplate->pageCode(_t('Configure me : First Time'), true); // output is completed, display all output above data wrapped by user design
    }
	
	
	
	
	
	
	
	
	
	
	
	
	function actionEpic(){
	$interest=$this->_oDb->getAllInterest($_COOKIE['memberID']);
	$iEntryId = $interest['id'];
	if ($iEntryId != null){
	$aEntry = $this->_oDb->getEntryById ((int)$iEntryId);
        if (!$aEntry) { // check if entry exists
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if ((!$GLOBALS['logged']['member'] && !$GLOBALS['logged']['admin']) || $aEntry['user_id'] != $_COOKIE['memberID']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        //$this->_oTemplate->pageStart(); // all the code below will be wrapped by the user design

        bx_import ('BxTemplFormView'); // import forms class

        foreach ($this->myInterest as $tempInt) { //retrieve checked case
            foreach ($this->catArray as $postType => $catName) {
                $tempArrayCat=array();
                $countcat=0;
                if($postType==$tempInt['title'])
                foreach ($catName as $cat => $catNme) {

                    foreach ($tempInt['catName'] as $tempcat) {
                        if($catNme['Categories']==$tempcat){
                        array_push($tempArrayCat, $countcat);
                        }
                    }
                    $countcat++;
                    $this->aForm['inputs']['cat_'.$postType]['value']=$tempArrayCat;
                }
            }
            
        }

        $oForm = new BxTemplFormView ($this->aForm); // create forms class

        // $oForm->initChecker($aEntry); // init form checker

        if ($oForm->isSubmittedAndValid ()) { // if form is submitted and not form errors were found, save form data
            $add='';
            foreach ($this->interestCat as $key => $value) { //Hack DB formating to own format
                $curData = $oForm->getCleanValue($value);
                $add.= $value.'{';
                // unset($this->aForm['inputs']['cat_'.$value]);
                foreach ($curData as $data) {
                    $data=$this->catArray[$value][$data]['Categories']; //get Name of cat hack
                    $add.=$data.',';
                }
                $add=substr($add, 0, -1);
                $add.='}';
            }
            $aValsAdd = array(
                'cat_id' => array(
                    'type' => 'text',
                    'name' => 'cat_id',
                    'caption' => 'cat_id',
                    'value' => $add,
                    'db' => array(
                        'pass' => 'All', 
                    ),
                )
            );
            $this->aForm['inputs']=array_merge($aValsAdd ,$this->aForm['inputs']);

            $oForm = new BxTemplFormView ($this->aForm); // create forms class
            $oForm->initChecker($aEntry); // init form checker         

            $iRes=$this->_oDb->updateInterest($iEntryId,$add);
            // $iRes = $oForm->update($iEntryId); // insert data to database



            if ($iRes) { // if post was successfully added
                $sRedirectUrl = BX_DOL_URL_ROOT. 'pedit.php?ID=' . $_COOKIE['memberID']; 
                header ('Location:' . $sRedirectUrl); // redirect to updated post view page
                exit;
            } else {
                MsgBox(_t('_Error Occured')); // if error occured display erroro message
            }                

        } else {

            echo $oForm->getCode (); // display form, if the form is not submitted or data is invalid

        }

		}
		else{
			if (!$GLOBALS['logged']['member'] && !$GLOBALS['logged']['admin']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        //$this->_oTemplate->pageStart(); // all the code below will be wrapped by the user design

        bx_import ('BxTemplFormView'); // import forms class

        $oForm = new BxTemplFormView ($this->aForm); // create foprms class
        $oForm->initChecker(); // init form checker

        if ($oForm->isSubmittedAndValid ()) { // if form is submitted and not form errors were found, save form data
            $add='';
            foreach ($this->interestCat as $key => $value) { //Hack DB formating to own format
                $curData = $oForm->getCleanValue($value);
                $add.= $value.'{';
                unset($this->aForm['inputs']['cat_'.$value]);
                foreach ($curData as $data) {
                    $data=$this->catArray[$value][$data]['Categories']; //get Name of cat hack
                    $add.=$data.',';
                }
                $add=substr($add, 0, -1);
                $add.='}';
            }
            $aValsAdd = array ( // add additional fields
                'cat_id' => $add,
                'user_id' => $_COOKIE['memberID'],
            );
            $oForm = new BxTemplFormView ($this->aForm); // create forms class         
            $iEntryId = $oForm->insert ($aValsAdd); // insert data to database

            if ($iEntryId) { // if post was successfully added
                $sRedirectUrl = BX_DOL_URL_ROOT. 'pedit.php?ID=' . $_COOKIE['memberID']; 
                header ('Location:' . $sRedirectUrl); // redirect to created post view page
                exit;
            } else {
                MsgBox(_t('_Error Occured')); // if error occured display erroro message
            }                

        } else {

            echo $oForm->getCode (); // display form, if the form is not submitted or data is invalid

        }

        //$this->_oTemplate->pageCode(_t('Configure me : First Time'), true); // output is completed, display all output above data wrapped by user design
    }
			
			
			
			//$this->_oTemplate->pageCode(_t('Configure Me : Edit'), true); // output is completed, display all output above data wrapped by user design
    }
	
	
	
	
	
	
	
	
	
	function actionEpic2(){
	
	$this->interestCat = array(
            'Events',
        );
        $this->aForm = array(

            'form_attrs' => array(
                'name'     => 'form_cfgme',
                'action'   => '',
                'method'   => 'post',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'afk_cfgme_interest',
                    'key' => 'id',
                    'submit_name' => 'submit_form',
                ),
            ),
            'inputs' => array(
                'submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Save'),
                    'colspan' => true,
                ),            
            ),            
        );

        $this->catArray=$interest=$this->_oDb->getCatArrayEvents();
        $tab=array();
        foreach ($this->catArray as $postType => $catName) {
            $temp=array(
                    'header_cat_'.$postType => array(
                        'type' => 'block_header',
                        'caption' => $postType,
                        'collapsable' => true,
                        'collapsed' => false, 
                    ),
            );
            $tab=array_merge($tab ,$temp);
            $values=array();
            foreach ($catName as $cat => $catNme) {
                $values[$cat]=$catNme['Categories'];
            }
            $temp=array(
                'cat_'.$postType => array(
                    'type' => 'checkbox_set',
                    'name' => $postType,
                    'caption' => $postType,
                    'values' => $values,
                    'db' => array(
                        'pass' => 'Set', 
                    ),
                ));
            $tab=array_merge($tab ,$temp);
        }
        $this->aForm['inputs'] = array_merge($tab,$this->aForm['inputs']);
        $interest=$this->_oDb->getAllInterest($_COOKIE['memberID']);

        $allinterest=explode('}', $interest['cat_id']);
        $this->myInterest=array();
        foreach ($allinterest as $key => $value) {
            $tempInt=explode('{', $allinterest[$key]); //$tempInt[0]= Events | $tempInt[1] = Expedition,birthday,etc.
            $this->myInterest[$key]['title'] = $tempInt[0];
            $this->myInterest[$key]['catName'] = explode(',',$tempInt[1]);
        }
        $this->myInterest=array_slice($this->myInterest, 0, -1); //remove last row over generated
	
	$iEntryId = $interest['id'];
	if ($iEntryId != null){
	$aEntry = $this->_oDb->getEntryById ((int)$iEntryId);
        if (!$aEntry) { // check if entry exists
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if ((!$GLOBALS['logged']['member'] && !$GLOBALS['logged']['admin']) || $aEntry['user_id'] != $_COOKIE['memberID']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        //$this->_oTemplate->pageStart(); // all the code below will be wrapped by the user design

        bx_import ('BxTemplFormView'); // import forms class

        foreach ($this->myInterest as $tempInt) { //retrieve checked case
            foreach ($this->catArray as $postType => $catName) {
                $tempArrayCat=array();
                $countcat=0;
                if($postType==$tempInt['title'])
                foreach ($catName as $cat => $catNme) {

                    foreach ($tempInt['catName'] as $tempcat) {
                        if($catNme['Categories']==$tempcat){
                        array_push($tempArrayCat, $countcat);
                        }
                    }
                    $countcat++;
                    $this->aForm['inputs']['cat_'.$postType]['value']=$tempArrayCat;
                }
            }
            
        }

        $oForm = new BxTemplFormView ($this->aForm); // create forms class

        // $oForm->initChecker($aEntry); // init form checker

        if ($oForm->isSubmittedAndValid ()) { // if form is submitted and not form errors were found, save form data
            $add='';
            foreach ($this->interestCat as $key => $value) { //Hack DB formating to own format
                $curData = $oForm->getCleanValue($value);
                $add.= $value.'{';
                // unset($this->aForm['inputs']['cat_'.$value]);
                foreach ($curData as $data) {
                    $data=$this->catArray[$value][$data]['Categories']; //get Name of cat hack
                    $add.=$data.',';
                }
                $add=substr($add, 0, -1);
                $add.='}';
            }
            $aValsAdd = array(
                'cat_id' => array(
                    'type' => 'text',
                    'name' => 'cat_id',
                    'caption' => 'cat_id',
                    'value' => $add,
                    'db' => array(
                        'pass' => 'All', 
                    ),
                )
            );
            $this->aForm['inputs']=array_merge($aValsAdd ,$this->aForm['inputs']);

            $oForm = new BxTemplFormView ($this->aForm); // create forms class
            $oForm->initChecker($aEntry); // init form checker         

            $iRes=$this->_oDb->updateInterest($iEntryId,$add);
            // $iRes = $oForm->update($iEntryId); // insert data to database



            if ($iRes) { // if post was successfully added
                $sRedirectUrl = BX_DOL_URL_ROOT. 'pedit.php?ID=' . $_COOKIE['memberID']; 
                header ('Location:' . $sRedirectUrl); // redirect to updated post view page
                exit;
            } else {
                MsgBox(_t('_Error Occured')); // if error occured display erroro message
            }                

        } else {

            echo $oForm->getCode (); // display form, if the form is not submitted or data is invalid

        }

		}
		else{
			if (!$GLOBALS['logged']['member'] && !$GLOBALS['logged']['admin']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        //$this->_oTemplate->pageStart(); // all the code below will be wrapped by the user design

        bx_import ('BxTemplFormView'); // import forms class

        $oForm = new BxTemplFormView ($this->aForm); // create foprms class
        $oForm->initChecker(); // init form checker

        if ($oForm->isSubmittedAndValid ()) { // if form is submitted and not form errors were found, save form data
            $add='';
            foreach ($this->interestCat as $key => $value) { //Hack DB formating to own format
                $curData = $oForm->getCleanValue($value);
                $add.= $value.'{';
                unset($this->aForm['inputs']['cat_'.$value]);
                foreach ($curData as $data) {
                    $data=$this->catArray[$value][$data]['Categories']; //get Name of cat hack
                    $add.=$data.',';
                }
                $add=substr($add, 0, -1);
                $add.='}';
            }
            $aValsAdd = array ( // add additional fields
                'cat_id' => $add,
                'user_id' => $_COOKIE['memberID'],
            );
            $oForm = new BxTemplFormView ($this->aForm); // create forms class         
            $iEntryId = $oForm->insert ($aValsAdd); // insert data to database

            if ($iEntryId) { // if post was successfully added
                $sRedirectUrl = BX_DOL_URL_ROOT. 'pedit.php?ID=' . $_COOKIE['memberID']; 
                header ('Location:' . $sRedirectUrl); // redirect to created post view page
                exit;
            } else {
                MsgBox(_t('_Error Occured')); // if error occured display erroro message
            }                

        } else {

            echo $oForm->getCode (); // display form, if the form is not submitted or data is invalid

        }

        //$this->_oTemplate->pageCode(_t('Configure me : First Time'), true); // output is completed, display all output above data wrapped by user design
    }
			
			
			
			//$this->_oTemplate->pageCode(_t('Configure Me : Edit'), true); // output is completed, display all output above data wrapped by user design
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
    function actionEdit ($iEntryId) {
	
	

        $aEntry = $this->_oDb->getEntryById ((int)$iEntryId);
        if (!$aEntry) { // check if entry exists
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if ((!$GLOBALS['logged']['member'] && !$GLOBALS['logged']['admin']) || $aEntry['user_id'] != $_COOKIE['memberID']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the user design

        bx_import ('BxTemplFormView'); // import forms class

        foreach ($this->myInterest as $tempInt) { //retrieve checked case
            foreach ($this->catArray as $postType => $catName) {
                $tempArrayCat=array();
                $countcat=0;
                if($postType==$tempInt['title'])
                foreach ($catName as $cat => $catNme) {

                    foreach ($tempInt['catName'] as $tempcat) {
                        if($catNme['Categories']==$tempcat){
                        array_push($tempArrayCat, $countcat);
                        }
                    }
                    $countcat++;
                    $this->aForm['inputs']['cat_'.$postType]['value']=$tempArrayCat;
                }
            }
            
        }

        $oForm = new BxTemplFormView ($this->aForm); // create forms class

        // $oForm->initChecker($aEntry); // init form checker

        if ($oForm->isSubmittedAndValid ()) { // if form is submitted and not form errors were found, save form data
            $add='';
            foreach ($this->interestCat as $key => $value) { //Hack DB formating to own format
                $curData = $oForm->getCleanValue($value);
                $add.= $value.'{';
                // unset($this->aForm['inputs']['cat_'.$value]);
                foreach ($curData as $data) {
                    $data=$this->catArray[$value][$data]['Categories']; //get Name of cat hack
                    $add.=$data.',';
                }
                $add=substr($add, 0, -1);
                $add.='}';
            }
            $aValsAdd = array(
                'cat_id' => array(
                    'type' => 'text',
                    'name' => 'cat_id',
                    'caption' => 'cat_id',
                    'value' => $add,
                    'db' => array(
                        'pass' => 'All', 
                    ),
                )
            );
            $this->aForm['inputs']=array_merge($aValsAdd ,$this->aForm['inputs']);

            $oForm = new BxTemplFormView ($this->aForm); // create forms class
            $oForm->initChecker($aEntry); // init form checker         

            $iRes=$this->_oDb->updateInterest($iEntryId,$add);
            // $iRes = $oForm->update($iEntryId); // insert data to database



            if ($iRes) { // if post was successfully added
                $sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $iEntryId; 
                header ('Location:' . $sRedirectUrl); // redirect to updated post view page
                exit;
            } else {
                MsgBox(_t('_Error Occured')); // if error occured display erroro message
            }                

        } else {

            echo $oForm->getCode (); // display form, if the form is not submitted or data is invalid

        }

        $this->_oTemplate->pageCode(_t('Configure Me : Edit'), true); // output is completed, display all output above data wrapped by user design
    }

    function actionAdministration () {

        if (!$GLOBALS['logged']['admin']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the admin design

	    $iId = $this->_oDb->getSettingsCategory(); // get our setting category id
	    if(empty($iId)) { // if category is not found display page not found
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_afk_cgfme'));
            return;
        }

        bx_import('BxDolAdminSettings'); // import class

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) { // save settings
	        $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId); // get display form code
        $sResult = $oSettings->getForm();
        	       
        if($mixedResult !== true && !empty($mixedResult)) // attach any resulted messages at the form beginning
            $sResult = $mixedResult . $sResult;

        echo DesignBoxAdmin (_t('_afk_cfgme'), $sResult); // dsiplay box
        
        $this->_oTemplate->pageCodeAdmin (_t('_afk_cfgme')); // output is completed, admin page will be displaed here
    }
}

?>
