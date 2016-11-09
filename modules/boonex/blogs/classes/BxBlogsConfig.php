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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolConfig.php');

class BxBlogsConfig extends BxDolConfig {

	var $_iAnimationSpeed;

	var $sUserExFile;
	var $sAdminExFile;
	var $sUserExPermalink;

	var $iPerPageElements;

	var $iTopTagsCnt;

	// SQL tables
	var $sSQLCategoriesTable;
	var $sSQLPostsTable;
	var $sSQLBlogsTable;

	var $_sCommentSystemName;
    var $_sRateSystemName;
    var $_sViewSystemName;

	/*
	* Constructor.
	*/
	function BxBlogsConfig($aModule) {
		parent::BxDolConfig($aModule);

		$this->_iAnimationSpeed = 'normal';

		$this->sUserExFile = 'blogs.php';
		$this->sAdminExFile = 'post_mod_blog.php';
		$this->sUserExPermalink = 'blogs/';

		$this->iTopTagsCnt = 20;

		$this->iPerPageElements = (int)getParam('blog_step');

		$this->sSQLCategoriesTable = 'sys_categories';
		$this->sSQLPostsTable = 'bx_blogs_posts';
		$this->sSQLBlogsTable = 'bx_blogs_main';

		$this->_sCommentSystemName = $this -> _sRateSystemName = $this -> _sViewSystemName = 'bx_blogs';
	}

	function getRateSystemName() {
	    return $this->_sRateSystemName;
	}

	function getCommentSystemName() {
	    return $this->_sCommentSystemName;
	}

	function getViewSystemName() {
	    return $this->_sViewSystemName;
	}
}

?>