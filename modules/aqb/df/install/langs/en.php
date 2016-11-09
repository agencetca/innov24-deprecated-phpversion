<?php
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
	'_aqb_df_caption_admin' => 'Dependent Fields',
	'_aqb_df_am_item' => 'Dependent Fields',
	'_aqb_df_lists' => 'Current Dependent Values Lists',
	'_aqb_df_no_deps' => 'There are no dependent lists set so far',
	'_aqb_df_new_tl' => 'New top level list',
	'_aqb_df_new_dl' => 'New dependent list',
	'_aqb_df_notes' => 'Notes',
	'_aqb_df_name' => 'Name',
	'_aqb_df_name_error' => 'Name must be 5-64 characters length',
	'_aqb_df_parent_list' => 'Depends on',
	'_aqb_df_dependent_list' => 'Dependent List',
	'_aqb_df_dependencies' => 'Dependencies',
	'_aqb_df_no_dependency' => 'No Dependency',
	'_aqb_df_values_list' => 'Possible Values',
	'_aqb_df_values_list_info' => 'The list of all values lists which are dependent on the list which serve as a values list for the field selected above',
	'_aqb_df_proceed' => 'Proceed',
	'_aqb_df_depends_on' => 'Depends On',
	'_aqb_df_depends_on_info' => 'The list of all possible fields which can be used as parent field for this field. This list contains only fields which are of Type - `Selector`, selector control - `Select (Dropdown box)` and only those which values are taken from the predefined list which serve as a top level list in existing dependency. If you don\'t see desired field here then ensure that that field meets all dependency requirements as described above or refer to Dependent Fields manual [admin panel -> modules -> dependent fields].',
	'_aqb_df_use_ajax' => 'Enable AJAX',
	'_aqb_df_use_ajax_info' => 'AJAX is a web technology used for making requests in a background. By enabling it you will reduce the size of pages where dependent fields are appearing thus making them faster to load on a slow internet connections. But when AJAX is enabled fields loading takes some time. Concider enabling AJAX if you have a lot of values in a dependent field otherwise leave it disabled.',
	'_aqb_df_loading' => 'loading...',
	'_aqb_df_faq' => '
	<style>
	ul li {
		margin: 5px;
	}
	</style>
	<div style="text-align: justify; padding: 0px 5px;">
	<h4>A step by step manual on how to create dependent field:</h4>
	Assume that you want to create a State field which should depend on Country field.
	<ul>
		<li>You need to create dependent values list first. To do that click on a `<strong>New dependent list</strong>` link give it a name (let\'s say <strong>State</strong> and select a list on which new list will be dependent (in our case it should be <strong>Country</strong>). After that a new popup window will open allowing you to add/edit values of a new list.</li>
		<li>After dependent list is ready proceed to <strong>[admin panel -> builders -> profile fields]</strong> and add new field and click on it to bring up field properties form (or edit existing field).</li>
		<li>Set common properties of the field like name, caption, description, etc. `<strong>Type</strong>` should be `<strong>Selector</strong>`, `<strong>Selector Control</strong>` should be `<strong>Select (Dropdown Box)</strong>`.</li>
		<li>Click <strong>Dependencies</strong> tab. Read notes by moving mouse cursor over the blue exclamation marks. Select the field on which new field depends (in our example it should be <strong>Country</strong>). After that select the list of possible values (the one which we have created in first step of this manual). Decide where you need <strong>AJAX</strong> or not. Press Save.</li>
		<li>Done. Not <strong>State</strong> field depends on <strong>Country</strong> field which means that on all pages where <strong>Country/State</strong> pair appears values of <strong>State</strong> field would be loading according to <strong>Country</strong> selected.</li>
	</ul>
	<h4>Things you shouldn\'t do:</h4>
	<ul>
		<li>Do not edit list of dependent values at <strong>[admin panel -> settings -> predefined values]</strong>. Edit all dependent lists only at <strong>[admin panel -> modules -> dependent fields]</strong></li>
		<li>Do not leave dependent field on forms without parent field. For example if you have <strong>Country->State</strong> dependency then both of them should always be on all forms (join, edit, search forms).</li>
	</ul>
	<h4>Control type on search page:</h4>
	<p>By default all fields of `Select (Dropdown box)` control type appear on search form as multiselectable dropdowns. If you will ever want to remove that ability of multiselection for all dependent fields you can easily do that by editing the file <b>modules/aqb/df/classes/AqbDFConfig.php</b>: at the very top comment out the first line of code and uncomment the second line.</p>
	</div>',
);
?>