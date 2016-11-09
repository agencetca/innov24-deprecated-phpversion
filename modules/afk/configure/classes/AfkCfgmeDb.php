<?
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

bx_import('BxDolModuleDb');

class AfkCfgmeDb extends BxDolModuleDb {

	function AfkCfgmeDb(&$oConfig) {
		parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }
    
    function getEntryById ($iEntryId) {
        return $this->getRow("SELECT * FROM `" . $this->_sPrefix . "interest` WHERE `id` = '$iEntryId'");
    }

    function getAllPosts ($iLimit) {
        return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "interest`");
    }    
    function getAllInterest ($uid) {
        return $this->getRow("SELECT * FROM `" . $this->_sPrefix . "interest` WHERE user_id=$uid");
    }    
    function updateInterest($uid, $updata) {
        if(mysql_query("UPDATE `afk_cfgme_interest` SET `cat_id` = '$updata' WHERE `id` = '$uid'"))
            return true;
    }
    /**
     * [getCatArray description]
     * @return $res Array of all category name and result
     */
    function getCatArray () {
        /*
         bx_events
         bx_blogs //news
         //jobs
         bx_videos
         bx_groups
         modzzz_gigs
         ml_pages
         bx_photos
         imagenews
         bx_sounds
         bx_store
         modzzz_opinions
         bx_files
         */
        //Events
        $bx_events=$this->getAll("SELECT Distinct(Category) as Categories FROM `sys_categories` WHERE `Type`='bx_events'");
        $bx_blogs=$this->getAll("SELECT Distinct(Category) as Categories FROM `sys_categories` WHERE `Type`='bx_blogs'"); 
        $jobsCat=$this->getAll("SELECT Distinct(Name) as Categories FROM `bx_ads_category`");
        //$bx_videos=$this->getAll("SELECT Distinct(Category) as Categories FROM `sys_categories` WHERE `Type`='bx_videos'");
        $bx_groups=$this->getAll("SELECT Distinct(Category) as Categories FROM `sys_categories` WHERE `Type`='bx_groups'");
        //$modzzz_gigs=$this->getAll("SELECT Distinct(Category) as Categories FROM `sys_categories` WHERE `Type`='modzzz_gigs'");
        // $ml_pages=$this->getAll("SELECT Distinct(Category) as Categories FROM `sys_categories` WHERE `Type`='ml_pages'");
        //$bx_photos=$this->getAll("SELECT Distinct(Category) as Categories FROM `sys_categories` WHERE `Type`='bx_photos'");
        // $imagenews=$this->getAll("SELECT Distinct(Category) as Categories FROM `sys_categories` WHERE `Type`='imagenews'");
        //$bx_sounds=$this->getAll("SELECT Distinct(Category)as Categories FROM `sys_categories` WHERE `Type`='bx_sounds'");
        $bx_store=$this->getAll("SELECT Distinct(Category) as Categories FROM `sys_categories` WHERE `Type`='bx_store'");
        //$modzzz_opinions=$this->getAll("SELECT Distinct(Category) as Categories FROM `sys_categories` WHERE `Type`='modzzz_opinions'");
        //$bx_files=$this->getAll("SELECT Distinct(Category) as Categories FROM `sys_categories` WHERE `Type`='bx_files'");

        $res = array(
            'Events' => $bx_events,
            'News' => $bx_blogs,
            'Jobs' => $jobsCat,
            //'Videos' => $bx_videos,
            'Groups' => $bx_groups,
            //'Gigs' => $modzzz_gigs,
            // 'Pages' => $ml_pages,
            //'Photos' => $bx_photos,
            // 'ImageNews' => $imagenews,
            //'Sounds' => $bx_sounds,
            'Store' => $bx_store,
            //'Opinions' => $modzzz_opinions,
            //'Files' => $bx_files
        );
        return $res;
        
                $res2 = array(
            'Events' => $bx_events,
        );
        return $res2;
    }
    
    
     function getCatArrayEvents () {
        //Events
        $bx_events=$this->getAll("SELECT Distinct(Category) as Categories FROM `sys_categories` WHERE `Type`='bx_events'");
        $res = array(
            'Events' => $bx_events
        );
        return $res;
    }
    
}

?>
