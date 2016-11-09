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

$aLangContent = array(
	'_bx_pageac' => 'Page Access Control',
	'_bx_pageac_note' => 'Notice',
	'_bx_pageac_note_text' => '<p class="pageac_notes">The characters <b>&nbsp;&nbsp;|\\{}[]()#:^$.?+*&nbsp;&nbsp;</b> are treated as special characters in regular expressions and to make them usual characters it is necessary to preceded them with a backslash <b>&nbsp;\&nbsp;</b>. That is why if you haven\'t checked "Advanced Expression" option the script is adding backslashes for you.</p>
<p class="pageac_notes">For example if you\'re setting access to <b>photos module</b> then you should write rule as<br /><b>m/photos/</b></p>
<p class="pageac_notes">Those experienced members who knows what are UNIX-style regular epxressions and how to use it may tick the "Advanced Expression" checkbox during access rule creation and use the full power of regular expressions.</p>',
	'_bx_pageac_new_rule' => 'New Access Rule',
	'_bx_pageac_current_rules' => 'Current Access Rules',
	'_bx_pageac_forbidden_groups' => 'Forbidden groups',
	'_bx_pageac_add_rule' => 'Add',
	'_bx_pageac_no_rules_admin' => 'There are no access rules defined yet',
	'_bx_pageac_url' => 'URL',
	'_bx_pageac_action' => 'Action',
	'_bx_pageac_update' => 'Update',
	'_bx_pageac_delete' => 'Delete',
	'_bx_pageac_visible_for' => 'Visible for membership levels',
	'_bx_pageac_visible_for_all' => 'All',
	'_bx_pageac_access_denied' => 'Access Denied',
	'_bx_pageac_deny_text' => 'You don\'t have permissions to access this page.',
	'_bx_pageac_rules_page' => 'Page Access',
	'_bx_pageac_topmenu_page' => 'Top Menu Access',
	'_bx_pageac_membermenu_page' => 'Member Menu Access',
	'_bx_pageac_page_blocks_page' => 'Page Blocks Access',
	'_bx_pageac_loading' => 'Loading...',
	'_bx_pageac_page_url' => 'Access URL template',
	'_bx_pageac_page_url_empty' => 'Access URL template can not be empty',
	'_bx_pageac_saved' => 'Saved',
	'_bx_pageac_deleted' => 'Deleted',
	'_bx_pageac_page_url_descr' => 'A page address relative to domain name (f.ex. <b>search.php</b> or <b>m/chat/</b>). Or a regular expression (for advanced users)',
	'_bx_pageac_advanced' => 'Advanced Expression',
	'_bx_pageac_advanced_descr' => 'If checked then it will be saved as is, without converting special characters to a regular expression format'
);
?>
