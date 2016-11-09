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

bx_import ('BxDolProfileFields');
bx_import ('BxDolFormMedia');

class MlPagesFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;


    function MlPagesFormAdd ($oMain, $iProfileId, $iEntryId = 0, $iThumb = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;

        $this->_aMedia = array (
            'images' => array (
                'post' => 'ready_images',
                'upload_func' => 'uploadPhotos',
                'tag' => ML_PAGES_PHOTOS_TAG,
                'cat' => ML_PAGES_PHOTOS_CAT,
                'thumb' => 'PrimPhoto',
                'module' => 'photos',
                'title_upload_post' => 'images_titles',
                'title_upload' => _t('_ml_pages_form_caption_file_title'),
                'service_method' => 'get_photo_array',
            ),
            'videos' => array (
                'post' => 'ready_videos',
                'upload_func' => 'uploadVideos',
                'tag' => ML_PAGES_VIDEOS_TAG,
                'cat' => ML_PAGES_VIDEOS_CAT,
                'thumb' => false,
                'module' => 'videos',
                'title_upload_post' => 'videos_titles',
                'title_upload' => _t('_ml_pages_form_caption_file_title'),
                'service_method' => 'get_video_array',
            ),            
            'sounds' => array (
                'post' => 'ready_sounds',
                'upload_func' => 'uploadSounds',
                'tag' => ML_PAGES_SOUNDS_TAG,
                'cat' => ML_PAGES_SOUNDS_CAT,
                'thumb' => false,
                'module' => 'sounds',
                'title_upload_post' => 'sounds_titles',
                'title_upload' => _t('_ml_pages_form_caption_file_title'),
                'service_method' => 'get_music_array',
            ),                        
            'files' => array (
                'post' => 'ready_files',
                'upload_func' => 'uploadFiles',
                'tag' => ML_PAGES_FILES_TAG,
                'cat' => ML_PAGES_FILES_CAT,
                'thumb' => false,
                'module' => 'files',
                'title_upload_post' => 'files_titles',
                'title_upload' => _t('_ml_pages_form_caption_file_title'),
                'service_method' => 'get_file_array',
            ),                                    
        );

        bx_import('BxDolCategories');
        $oCategories = new BxDolCategories();
        $oCategories->getTagObjectConfig ();

        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
				
				$aMediaInputs = array(

              );
                
        // generate templates for form custom elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($this->_oMain->_iProfileId, $iEntryId, $iThumb);

        // privacy

        $aInputPrivacyCustom = array ();
        $aInputPrivacyCustom[] = array ('key' => '', 'value' => '----');
        $aInputPrivacyCustom[] = array ('key' => 'p', 'value' => _t('_ml_pages_privacy_fans_only'));
        $aInputPrivacyCustomPass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9p]+)$/'),
        );

        $aInputPrivacyCustom2 = array (
            array('key' => 'p', 'value' => _t('_ml_pages_privacy_fans')),
            array('key' => 'a', 'value' => _t('_ml_pages_privacy_admins_only'))
        );
        $aInputPrivacyCustom2Pass = array (
            'pass' => 'Preg', 
            'params' => array('/^([pa]+)$/'),
        );

        $aInputPrivacyViewFans = $GLOBALS['oMlPagesModule']->_oPrivacy->getGroupChooser($iProfileId, 'pages', 'view_fans');
        $aInputPrivacyViewFans['values'] = array_merge($aInputPrivacyViewFans['values'], $aInputPrivacyCustom);


        $aInputPrivacyComment = $GLOBALS['oMlPagesModule']->_oPrivacy->getGroupChooser($iProfileId, 'pages', 'comment');
        $aInputPrivacyComment['values'] = array_merge($aInputPrivacyComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $GLOBALS['oMlPagesModule']->_oPrivacy->getGroupChooser($iProfileId, 'pages', 'rate');
        $aInputPrivacyRate['values'] = array_merge($aInputPrivacyRate['values'], $aInputPrivacyCustom);
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyForum = $GLOBALS['oMlPagesModule']->_oPrivacy->getGroupChooser($iProfileId, 'pages', 'post_in_forum');
        $aInputPrivacyForum['values'] = array_merge($aInputPrivacyForum['values'], $aInputPrivacyCustom);
        $aInputPrivacyForum['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyUploadPhotos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'pages', 'upload_photos');
        $aInputPrivacyUploadPhotos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadPhotos['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadVideos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'pages', 'upload_videos');
        $aInputPrivacyUploadVideos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadVideos['db'] = $aInputPrivacyCustom2Pass;        

        $aInputPrivacyUploadSounds = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'pages', 'upload_sounds');
        $aInputPrivacyUploadSounds['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadSounds['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadFiles = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'pages', 'upload_files');
        $aInputPrivacyUploadFiles['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadFiles['db'] = $aInputPrivacyCustom2Pass;
        
				if (strpos($_SERVER['REQUEST_URI'], 'upload_photos') || strpos($_SERVER['REQUEST_URI'], 'upload_sounds')
					|| strpos($_SERVER['REQUEST_URI'], 'upload_videos') || strpos($_SERVER['REQUEST_URI'], 'files'))
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_pages',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'ml_pages_main',
                    'key' => 'ID',
                    'uri' => 'EntryUri',
                    'uri_title' => 'Title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(


                // images

                'header_images' => array(
                    'type' => 'block_header',
                    'caption' => _t('_ml_pages_form_header_images'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'PrimPhoto' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['thumb_choice'],
                    'name' => 'PrimPhoto',
                    'caption' => _t('_ml_pages_form_caption_thumb_choice'),
                    'info' => _t('_ml_pages_form_info_thumb_choice'),
                    'required' => false,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ),                
                'images_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['choice'],
                    'name' => 'images_choice[]',
                    'caption' => _t('_ml_pages_form_caption_images_choice'),
                    'info' => _t('_ml_pages_form_info_images_choice'),
                    'required' => false,
                ),
                'images_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['upload'],
                    'name' => 'images_upload[]',
                    'caption' => _t('_ml_pages_form_caption_images_upload'),
                    'info' => _t('_ml_pages_form_info_images_upload'),
                    'required' => false,
                ),

                // videos

                'header_videos' => array(
                    'type' => 'block_header',
                    'caption' => _t('_ml_pages_form_header_videos'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'videos_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['choice'],
                    'name' => 'videos_choice[]',
                    'caption' => _t('_ml_pages_form_caption_videos_choice'),
                    'info' => _t('_ml_pages_form_info_videos_choice'),
                    'required' => false,
                ),
                'videos_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['upload'],
                    'name' => 'videos_upload[]',
                    'caption' => _t('_ml_pages_form_caption_videos_upload'),
                    'info' => _t('_ml_pages_form_info_videos_upload'),
                    'required' => false,
                ),

                // sounds

                'header_sounds' => array(
                    'type' => 'block_header',
                    'caption' => _t('_ml_pages_form_header_sounds'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'sounds_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['choice'],
                    'name' => 'sounds_choice[]',
                    'caption' => _t('_ml_pages_form_caption_sounds_choice'),
                    'info' => _t('_ml_pages_form_info_sounds_choice'),
                    'required' => false,
                ),
                'sounds_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['upload'],
                    'name' => 'sounds_upload[]',
                    'caption' => _t('_ml_pages_form_caption_sounds_upload'),
                    'info' => _t('_ml_pages_form_info_sounds_upload'),
                    'required' => false,
                ),

                // files

                'header_files' => array(
                    'type' => 'block_header',
                    'caption' => _t('_ml_pages_form_header_files'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'files_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['choice'],
                    'name' => 'files_choice[]',
                    'caption' => _t('_ml_pages_form_caption_files_choice'),
                    'info' => _t('_ml_pages_form_info_files_choice'),
                    'required' => false,
                ),
                'files_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['upload'],
                    'name' => 'files_upload[]',
                    'caption' => _t('_ml_pages_form_caption_files_upload'),
                    'info' => _t('_ml_pages_form_info_files_upload'),
                    'required' => false,
                ),
            ),            
        );

				else
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_pages',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'ml_pages_main',
                    'key' => 'ID',
                    'uri' => 'EntryUri',
                    'uri_title' => 'Title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                // privacy
                
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_ml_pages_form_header_privacy'),
                ),

                'allow_view_page_to' => $GLOBALS['oMlPagesModule']->_oPrivacy->getGroupChooser($iProfileId, 'pages', 'view_page'),

                'allow_view_fans_to' => $aInputPrivacyViewFans,

                'allow_comment_to' => $aInputPrivacyComment,

                'allow_rate_to' => $aInputPrivacyRate, 

                'allow_post_in_forum_to' => $aInputPrivacyForum, 

                'allow_join_to' => $GLOBALS['oMlPagesModule']->_oPrivacy->getGroupChooser($iProfileId, 'pages', 'join'),


                'allow_upload_photos_to' => $aInputPrivacyUploadPhotos, 

                'allow_upload_videos_to' => $aInputPrivacyUploadVideos, 

                'allow_upload_sounds_to' => $aInputPrivacyUploadSounds, 

                'allow_upload_files_to' => $aInputPrivacyUploadFiles,                 
            ),            
        );

        if (!$aCustomForm['inputs']['images_choice']['content']) {
            unset ($aCustomForm['inputs']['PrimPhoto']);
            unset ($aCustomForm['inputs']['images_choice']);
        }

        if (!$aCustomForm['inputs']['videos_choice']['content'])
            unset ($aCustomForm['inputs']['videos_choice']);

        if (!$aCustomForm['inputs']['sounds_choice']['content'])
            unset ($aCustomForm['inputs']['sounds_choice']);

        if (!$aCustomForm['inputs']['files_choice']['content'])
            unset ($aCustomForm['inputs']['files_choice']);


        $aFormInputsAdminPart = array ();
        if ($GLOBALS['oMlPagesModule']->isAdmin()) {

            require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
            $aMemberships = getMemberships ();
            unset ($aMemberships[MEMBERSHIP_ID_NON_MEMBER]); // unset Non-member
            $aMemberships = array('' => _t('_ml_pages_membership_filter_none')) + $aMemberships;
            $aFormInputsAdminPart = array (
                'PageMembershipFilter' => array(
                    'type' => 'select',
                    'name' => 'PageMembershipFilter',
                    'caption' => _t('_ml_pages_caption_membership_filter'), 
                    'info' => _t('_ml_pages_info_membership_filter'), 
                    'values' => $aMemberships,
                    'value' => '', 
                    'checker' => array (
                        'func' => 'preg',
                        'params' => array('/^[0-9a-zA-Z]*$/'),
                        'error' => _t ('_ml_pages_err_membership_filter'),
                    ),                                        
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([0-9a-zA-Z]*)/'),
                    ),
                    
                ),
            );
        } 

        $aFormInputsSubmit = array (
            'Submit' => array (
                'type' => 'submit',
                'name' => 'submit_form',
                'value' => _t('_Submit'),
                'colspan' => true,
            ),            
        );
        
        $aCustomForm['inputs'] = array_merge($aCustomForm['inputs'], $aFormInputsAdminPart, $aFormInputsSubmit);

					 
        $this->processMembershipChecksForMediaUploads ($aCustomForm['inputs']);

        parent::BxDolFormMedia ($aCustomForm);
    }

}

?>
