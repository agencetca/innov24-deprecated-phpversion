<?
/***************************************************************************
*
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
*
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
*
* This notice may not be removed from the source code.
*
***************************************************************************/

$aLangContent = array(
	'__Personal' => 'Personal',
	'_aqb_pts_am_item' => 'Profile Types Splitter',
	'_aqb_pts_caption_admin'  => 'Profile Types Splitter',
	'_aqb_pts_profile_types' => 'Profile Types',
	'_aqb_pts_new_profile_type' => 'Add Profile Type',
	'_aqb_pts_migration_tool' => 'Migration Tool',
	'_aqb_pts_no_profile_types' => 'There are no profile types defined yet',
	'_aqb_pts_type_id' => 'Type ID',
	'_aqb_pts_type_name' => 'Type Name',
	'_aqb_pts_type_members' => 'Members',
	'_aqb_pts_activate' => 'Activate',
	'_aqb_pts_deactivate' => 'Deactivate',
	'_aqb_pts_pt_name' => 'Profile Type Name',
	'_aqb_pts_pt_add' => 'Add',
	'_aqb_pts_operation_success' => 'Operation successfull',
	'_aqb_pts_for_all_profiles' => 'For all profiles with membership',
	'_aqb_pts_and_profiles' => 'and profile type',
	'_aqb_pts_set_pt' => 'set profile type',
	'_aqb_pts_any' => 'Any',
	'_aqb_pts_processed' => 'Processed',
	'_aqb_pts_migrated' => 'Migrated',
	'_aqb_pts_profiles' => 'profiles',
	'_aqb_pts_apply' => 'Apply',
	'_aqb_pts_visible_for' => 'Visible for',
	'_aqb_pts_visible_for_all' => 'All',
	'_aqb_pts_relevant_to' => 'Relevant to',
	'_aqb_pts_profile_types' => 'Profile Types',
	'_aqb_pts_relevant_to_all' => 'Relevant to All',
	'_aqb_pts_relevant_to_pt' => 'Indicates whether this field should be relevant to {0} profile type',
	'_aqb_pts_relevant_to_all_pt' => 'Indicates whether this field should be relevant to All profile types',
	'_FieldCaption_ProfileType_Join' => 'Profile Type',
	'_aqb_pts_loading' => 'Loading...',
	'_aqb_pts_profile_fields_page' => 'Profile Fields',
	'_aqb_pts_topmenu_page' => 'Top Menu',
	'_aqb_pts_membermenu_page' => 'Member Menu',
	'_aqb_pts_page_blocks_page' => 'Page Blocks',
	'_aqb_pts_saved' => 'Saved',
	'_aqb_pts_deleted' => 'Deleted',
	'_aqb_pts_initializing' => 'Initializing...',
	'_aqb_pts_pt_cant_be_edited' => 'ProfileType field can\'t be edited here',
	'_aqb_pts_notes' => 'A few notes',
	'_aqb_pts_notes_text' => '
		<ul>
			<li>Deactivated profile type would remain in the system but wouldn\'t be showing on join page and profile edit page.</li>
			<li>Only profile type without any members in it can be deleted. So if you want to delete some profile type then migrate all of it\'s member to some other profile types first. Only then you\'d be able to delete it.</li>
			<li>Do not delete profile types directly in database.</li>
			<li>Do not edit/delete values of `ProfileType` predefined list ([admin panel -> settings -> predefined values -> ProfileType). However you can reorder rows of that list.</li>
			<li>Do not remove `ProfileType` field from join page. That field should always be on the join page.</li>
			<li>Do not change name, type and values list of `ProfileType` field using fields builder. The only thing you can change is caption and/or description.</li>
			<li>After adding homepage members block you should proceed to [Admin Panel -> Builders -> Page Blocks] page find newly added block in `Inactive Blocks` section and place it on any of the columns.</li>
		</ul>
	',

	'_aqb_pts_search_result_layout' => 'Search Result',
	'_aqb_pts_avail_profile_types' => 'Available profile types',
	'_aqb_pts_avail_fields' => 'All fields that can be shown on search results for this profile type',
	'_aqb_pts_fields_name' => 'Name',
	'_aqb_pts_fields_action' => 'Action',
	'_aqb_pts_fields_col' => 'Column',
	'_aqb_pts_fields_row' => 'Row',
	'_aqb_pts_fields_both' => 'Both',
	'_aqb_pts_fields_remove' => 'Remove',
	'_aqb_pts_fields_result' => 'Resulting view',
	'_aqb_pts_fields_clear' => 'Clear',
	'_aqb_pts_fields_clear_sure' => 'Are you sure you want to empty serch result layout?',
	'_aqb_pts_fields_clear_reason' => 'If after adding/deleting fields and profile types many times you feel that search results are showing incorrectly and/or search results builder works strange you can clear out the layout with this button and build it again.',
	'_aqb_pts_fields_no' => 'There are no profile fields selected yet.',
	'_aqb_pts_hp_block' => 'Homepage members block'
);
?>