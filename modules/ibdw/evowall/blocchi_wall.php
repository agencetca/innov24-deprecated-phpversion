<?php
$idprofile=getID($_REQUEST['ID']);

//get dolphin version
$getdver="SELECT `VALUE` FROM `sys_options` WHERE `Name`='sys_tmp_version'";
$rundver=mysql_query($getdver);
$getdversion=mysql_fetch_row($rundver);
$dolphin_version=$getdversion[0];
if (strpos($dolphin_version,'7.0')!==false) $versiond="old";
else $versiond="new";
//old is for dolphin 7.0.X, new for dolphin 7.1.x
?>
<script>
pastedev=0;
function pasted(element) 
{
 setTimeout(function() {inviaurls(element.value,element.value,<?php echo $idprofile?>);}, 0);
 chiuditutto();
 pastedev=1;
}
</script>
<?php if ($UrlPlugin=='on') {
?>
<div id="frammento_url" class="blocco_wall">
<div id="url_container">
<form action="javascript:inviaurl(<?php echo $idprofile;?>);">
<input id="urlspecial" type="text" onfocus="focuss()" onblur="blurr()" value="<?php echo _t('_ibdw_evowall_urlspecial');?>" onchange="if(pastedev==0){pasted(this);} else{pastedev=0;}" onpaste="pasted(this);">
</form>

</div>
<div class="clear"></div>
</div>
<?php } ?>
<?php if ($site=='on') { ?>
<div id="frammento_siti" class="blocco_wall">
 <form  name="form_site" action="m/sites/browse/my/add" method="post" enctype="multipart/form-data" id="form_site" class="form_advanced">
 <input type="hidden" value="<?php echo $tokenfinale;?>" name="csrf_token" class="form_input_hidden">
 <div class="form_advanced_wrapper form_site_wrapper">
	 <div class="arealink">
 	  <?php echo _t('_ibdw_evowall_inseriscisito');?> <input class="sweblinks" type="text" name="url" />
      <input class="form_input_submit" type="submit" name="submit_form" value="<?php echo _t('_ibdw_evowall_sender'); ?>" />
     </div>
 </div>
 </form>
<div class="clear"></div>
</div>
<?php } 

if ($poll=='on') { ?>            
<div id="frammento_sondaggi" class="blocco_wall">
<?php 
 if ($versiond=="old")
 {
?>
 <form  action="modules/?r=poll/&amp;action=my&amp;mode=add" method="post" name="poll_creation_form" id="poll_creation_form" class="form_advanced">
 <input type="hidden" value="<?php echo $tokenfinale;?>" name="csrf_token" class="form_input_hidden">
 <div class="form_advanced_wrapper poll_creation_form_wrapper">
 <table class="form_advanced_table" style="border:none;" cellpadding="0" cellspacing="0">
  <tbody>
   <tr>
    <td class="caption" style="white-space:inherit;">
     <span class="required">*</span> 
     <?php echo _t('_question'); ?> 
    </td>
    <td class="value">
     <div class="clear_both"></div>
     <div class="input_wrapper input_wrapper_text">
      <input  class="form_input_text" type="text" name="question" />
      <div class="input_close input_close_text"></div>
     </div>
     <img class="warn" alt="error" src="templates/base/images/icons/exclamation.png" float_info=" " />
     <div class="clear_both"></div>
    </td>
   </tr>
   <tr>
    <td class="caption" style="white-space:inherit;">
     <span class="required">*</span> 
     <?php echo _t('_bx_poll_answer'); ?> 
    </td>
    <td class="value">
     <div class="clear_both"></div>
     <div class="input_wrapper input_wrapper_text">
      <input multiplyable="true" class="form_input_text" type="text" name="answers[]" value="" />
      <div class="input_close input_close_text"></div>
     </div>
     <img class="warn" alt="error" src="templates/base/images/icons/exclamation.png" float_info=" " />
     <div class="clear_both"></div>
	 <div class="input_wrapper input_wrapper_text">
      <input  deletable="true" class="form_input_text" type="text" name="answers[]" value="" />
      <div class="input_close input_close_text"></div>
     </div>
     <div class="clear_both"></div>
    </td>
   </tr>
   <tr>
    <td class="caption" style="white-space:inherit;">
	Tag: 
    </td>
    <td class="value">
     <div class="clear_both"></div>
     <div class="input_wrapper input_wrapper_text">
      <input  class="form_input_text" type="text" name="tags" />
      <div class="input_close input_close_text"></div>
     </div>
     <img class="info" alt="info" src="templates/base/images/icons/info.gif" float_info="<?php echo _t('_bx_poll_tags_separeted'); ?> />
     <img class="warn" alt="error" src="templates/base/images/icons/exclamation.png" float_info=" " />
     <div class="clear_both"></div>
    </td>
   </tr>
   <tr>
    <td class="caption" style="white-space:inherit;">
     <?php echo _t('_bx_poll_allow_view'); ?>
    </td>
    <td class="value">
     <div class="clear_both"></div>
     <div class="input_wrapper input_wrapper_select">
      <select class="form_input_select" name="allow_view_to">
       <option value="1">Default</option>
	   <option value="2"><?php echo _t('_ibdw_evowall_soloio'); ?></option>
	   <option value="3" selected="selected"><?php echo _t('_public'); ?></option>
	   <option value="4"><?php echo _t('_People'); ?></option>
	   <option value="5"><?php echo _t('_bx_spy_friends'); ?></option>
	  </select>
      <div class="input_close input_close_select"></div>
     </div>
     <img class="warn" alt="error" src="templates/base/images/icons/exclamation.png" float_info=" " />
     <div class="clear_both"></div>
    </td>
   </tr>
   <tr>
    <td class="caption" style="white-space:inherit;">
     <?php echo _t('_bx_poll_allow_comment'); ?> 
    </td>
    <td class="value">
     <div class="clear_both"></div>
     <div class="input_wrapper input_wrapper_select">
      <select  class="form_input_select" name="allow_comment_to">
       <option value="1">Default</option>
	   <option value="2"><?php echo _t('_ibdw_evowall_soloio'); ?></option>
	   <option value="3" selected="selected"><?php echo _t('_public'); ?></option>
	   <option value="4"><?php echo _t('_People'); ?></option>
	   <option value="5"><?php echo _t('_Friends'); ?></option>
      </select>
      <div class="input_close input_close_select"></div>
     </div>
     <img class="warn" alt="error" src="templates/base/images/icons/exclamation.png" float_info=" " />
     <div class="clear_both"></div>
    </td>
   </tr>
   <tr>
    <td class="caption" style="white-space:inherit;">
     <?php echo _t('_bx_poll_allow_vote'); ?> 
    </td>
    <td class="value">
     <div class="clear_both"></div>
     <div class="input_wrapper input_wrapper_select">
      <select class="form_input_select" name="allow_vote_to">
       <option value="1">Default</option>
	   <option value="2"><?php echo _t('_ibdw_evowall_soloio'); ?></option>
	   <option value="3" selected="selected"><?php echo _t('_public'); ?></option>
	   <option value="4"><?php echo _t('_People'); ?></option>
	   <option value="5"><?php echo _t('_bx_spy_friends'); ?></option>
      </select>
      <div class="input_close input_close_select"></div>
     </div>
     <img class="warn" alt="error" src="templates/base/images/icons/exclamation.png" float_info=" " />
     <div class="clear_both"></div>
    </td>
   </tr>
   <tr>
    <td class="caption" style="white-space:inherit;">
     <span class="required">*</span> 
     <?php echo _t('_bx_poll_categories'); ?> 
    </td>
    <td class="value">
     <div class="clear_both"></div>
     <div class="input_wrapper input_wrapper_select_box">
      <select multiplyable="true" add_other="true" class="form_input_select" name="Categories[]">
       <option value="" selected="selected"><?php echo _t('_Please_Select_'); ?></option>
	     <?php $sql_category = "SELECT DISTINCT Category FROM sys_categories WHERE Type = 'bx_poll' AND Status = 'active'";
               $exe_sql_category=mysql_query($sql_category);
               while($sql_poll=mysql_fetch_array($exe_sql_category)){
               echo '<option value="'.$sql_poll['Category'].'">'.$sql_poll['Category'].'</option>';
               }
        ?>
      </select>
     </div>
     <img class="warn" alt="error" src="templates/base/images/icons/exclamation.png" float_info=" " />
     <div class="clear_both"></div>
    </td>
   </tr>
   <tr>
    <td class="caption" style="white-space:inherit;">
     &nbsp;
    </td>
    <td class="value">
     <div class="clear_both"></div>
     <div class="input_wrapper input_wrapper_submit">
      <div class="button_wrapper">
       <input  class="form_input_submit" type="submit" name="do_submit" value="Send" />
	   <div class="button_wrapper_close"></div>
      </div>
      <div class="input_close input_close_submit"></div>
     </div>
     <img class="warn" alt="error" src="templates/base/images/icons/exclamation.png" float_info=" " />
     <div class="clear_both"></div>
    </td>
   </tr>
  </tbody>
 </table>
</div>
</form>
<?
}
else
{
?>
<form class="form_advanced" id="poll_creation_form" name="poll_creation_form" method="post" action="m/poll/&amp;action=my&amp;mode=add">
 <input class="form_input_hidden bx-def-font" type="hidden" value="<?php echo $tokenfinale;?>" name="csrf_token">
 <div class="form_advanced_wrapper poll_creation_form_wrapper">
  <table cellspacing="0" cellpadding="0" class="form_advanced_table" style="border:none;" >
   <tbody>
    <tr>
     <td class="caption" style="white-space:inherit;"><span class="required">*</span><?php echo _t('_question'); ?></td>
     <td class="value">
      <div class="clear_both"></div><div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
      <input type="text" name="question" class="form_input_text bx-def-font"></div>
      <i float_info=" " class="warn sys-icon exclamation-sign"></i>
      <div class="clear_both"></div>
     </td>
     </tr>
	 <tr>
      <td class="caption" style="white-space:inherit;"><span class="required">*</span><?php echo _t('_bx_poll_answer'); ?></td>
      <td class="value">
	   <div class="clear_both"></div><div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
       <input type="text" value="" name="answers[]" class="form_input_text bx-def-font" multiplyable="true">
       </div>
       <i float_info=" " class="warn sys-icon exclamation-sign"></i><div class="clear_both"></div>
	   <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border"><input type="text" value="" name="answers[]" class="form_input_text bx-def-font" deletable="true">
       </div></i><div class="clear_both"></div>
      </td>
     </tr>
	 <tr>
      <td class="caption" style="white-space:inherit;"><span class="required">*</span><?php echo _t('_bx_poll_categories'); ?></td>
      <td class="value">
       <div class="clear_both"></div><div class="input_wrapper input_wrapper_select_box">
       <select name="Categories[]" class="form_input_select bx-def-font" add_other="true" multiplyable="true">
       <option selected="selected" value=""><?php echo _t('_Please_Select_'); ?></option>	   
	   <?php $sql_category = "SELECT DISTINCT Category FROM sys_categories WHERE Type = 'bx_poll' AND Status = 'active'";
               $exe_sql_category=mysql_query($sql_category);
               while($sql_poll=mysql_fetch_array($exe_sql_category)){
               echo '<option value="'.$sql_poll['Category'].'">'.$sql_poll['Category'].'</option>';
               }
        ?>
       </select>
       </div><i title="Add other" alt="Add other" class="multiply_other_button sys-icon folder-close"></i>
       <i float_info=" " class="warn sys-icon exclamation-sign"></i>
       <div class="clear_both"></div>
      </td>
     </tr>
	 <tr>
      <td class="caption" style="white-space:inherit;">Tags:</td>
      <td class="value"><div class="clear_both"></div><div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
       <input type="text" name="tags" class="form_input_text bx-def-font"></div>
       <i float_info="Enter a few words delimited by commas" class="info sys-icon info-sign"></i>
       <i float_info=" " class="warn sys-icon exclamation-sign"></i>
       <div class="clear_both"></div>
      </td>
     </tr>
	 <tr>
      <td class="caption" style="white-space:inherit;"><?php echo _t('_bx_poll_allow_view'); ?></td>
      <td class="value"><div class="clear_both"></div><div class="input_wrapper input_wrapper_select ">
	  <select name="allow_view_to" class="form_input_select bx-def-font">
	   <option value="1">Default</option>
	   <option value="2"><?php echo _t('_ibdw_evowall_soloio'); ?></option>
	   <option value="3" selected="selected"><?php echo _t('_public'); ?></option>
	   <option value="4"><?php echo _t('_People'); ?></option>
	   <option value="5"><?php echo _t('_bx_spy_friends'); ?></option>
      </select>
      </div>
      <i float_info=" " class="warn sys-icon exclamation-sign"></i><div class="clear_both"></div>
      </td>
     </tr>
	 <tr>
      <td class="caption" style="white-space:inherit;"><?php echo _t('_bx_poll_allow_comment'); ?></td>
	  <td class="value"><div class="clear_both"></div><div class="input_wrapper input_wrapper_select ">
	   <select name="allow_comment_to" class="form_input_select bx-def-font">
        <option value="1">Default</option>
	    <option value="2"><?php echo _t('_ibdw_evowall_soloio'); ?></option>
	    <option value="3" selected="selected"><?php echo _t('_public'); ?></option>
	    <option value="4"><?php echo _t('_People'); ?></option>
	    <option value="5"><?php echo _t('_bx_spy_friends'); ?></option>
       </select>
       </div>
       <i float_info=" " class="warn sys-icon exclamation-sign"></i><div class="clear_both"></div>
      </td>
	 </tr>
	 <tr>
	  <td class="caption" style="white-space:inherit;"><?php echo _t('_bx_poll_allow_vote'); ?></td>
	  <td class="value"><div class="clear_both"></div><div class="input_wrapper input_wrapper_select ">
	   <select name="allow_vote_to" class="form_input_select bx-def-font">
		<option value="1">Default</option>
	    <option value="2"><?php echo _t('_ibdw_evowall_soloio'); ?></option>
	    <option value="3" selected="selected"><?php echo _t('_public'); ?></option>
	    <option value="4"><?php echo _t('_People'); ?></option>
	    <option value="5"><?php echo _t('_bx_spy_friends'); ?></option>
       </select>
       </div>
       <i float_info=" " class="warn sys-icon exclamation-sign"></i><div class="clear_both"></div>
      </td>
     </tr>
	 <tr>
	  <td class="caption" style="white-space:inherit;">&nbsp;</td>
      <td class="value"><div class="clear_both"></div><div class="input_wrapper input_wrapper_submit ">
       <div class="button_wrapper"><input type="submit" value="Create" name="do_submit" class="form_input_submit bx-btn"></div></div>
        <i float_info=" " class="warn sys-icon exclamation-sign"></i><div class="clear_both"></div>
       </td>
      </tr>
     </tbody>
    </table>
   </div>
  </form>


<?
}
?>


<script type="text/javascript" language="javascript">
var lang_delete = '<?php echo _t('_delete'); ?>';
var lang_loading = '<?php echo _t('_loading ...'); ?>';
var lang_delete_message = '<?php echo _t('_bx_poll_was_deleted'); ?>';
var lang_make_it = '<?php echo _t('bx_poll_make_it'); ?>';
var lang_you_should_specify_member = '<?php echo _t('_bx_poll_specify_least'); ?>';
var iQSearchWindowWidth  = 400;
var iQSearchWindowHeight = 400;
var dpoll_progress_bar_color = "#88C86A";
var sPageReceiver = 'modules/?r=poll';
</script>

<script src="modules/boonex/poll/js/profile_poll.js" type="text/javascript"></script>

<div class="clear"></div>
</div>
<?php } 
if ($photo=='on') { 
//check if there is only an upload method enabled for Photos
$uploaderPhotosNum=0;
if ($PhotoRegularM) {$uploaderPhotosNum++;}
if ($PhotoFlashM) {$uploaderPhotosNum++;}
if ($PhotoOtherM) {$uploaderPhotosNum++;}
?>

<div id="frammento_foto" class="blocco_wall">
  <?php 
  $photoaltri=$PhotoOtherM;
  $metphotoregolare =$PhotoRegularM;
  $metphotoflash=$PhotoFlashM;
  $metodiattiviphoto= $DefaultPhotoM;
  ?>
  
<div class="boxfotog">
 <div id="frammento_foto_standard" <?php if($metphotoregolare == '' ) { echo 'style="display:none;"'; } ?>
 <?php if($metodiattiviphoto == 'Flash') {echo 'style="display:none;"';} else {'style="display:block !important;"';} ?> >
 
<?php
 if ($uploaderPhotosNum>1)
 {
?>  
 <div class="ibdw_evo_bt_list" onclick="open_bt_list('x_evo_list2');" id="fade_bt_listx_evo_list" style="right:1px;display:block">
  <input type="hidden" value="0" id="mm_setmenux_evo_list2" />
  <a class="bt_openx_evo_list" id="bt_open"><img src="<?php echo $imagepath;?>othernews.png"></a>
 </div>
<?php
}
?>
     <div class="ibdw_bt_superlist" id="lista_btx_evo_list2" style="right:1px;top:15px;">
      <?php if($photoaltri=='on') { echo '<a id="bottone_sub_elimina" href="m/photos/albums/my/add_objects/'.trim($namephotoalbum).'">'._t('_ibdw_evowall_altrimetodi').'</a>'; } ?>
      <?php if($metphotoflash =='on') { echo '<a id="bottone_sub_elimina" href="javascript:lanciaflashfoto();">'._t('_ibdw_evowall_useflash').'</a>';}?>
     </div>    
<?php 
 if ($versiond=="old")
 {
?>
  <form class="form_advanced" target="upload_file_frame" enctype="multipart/form-data" method="post" action="m/photos/albums/my/add_objects" name="upload" id="photo_upload_form">
  <input type="hidden" value="accept_upload" name="action" class="form_input_hidden">
  <input type="hidden" value="<?php echo trim($namephotoalbum);?>" name="extra_param_album" class="form_input_hidden">
  <input type="hidden" value="fAd9K75MwwCGYBisPFRj" name="csrf_token" class="form_input_hidden">
  <div class="form_advanced_wrapper photo_upload_form_wrapper">
  <table cellspacing="0" cellpadding="0" class="form_advanced_table" style="border:none;">
   <tbody>
    <tr>
     <td class="caption"><span class="required">*</span> Search:</td>
     <td class="value">
      <div class="clear_both"></div><div class="input_wrapper input_wrapper_file"><input type="file" name="file[]" class="form_input_file" onchange="parent.oPhotoUpload.onFileChangedEvent();" multiplyable="true">
      <div class="input_close input_close_file"></div></div><img float_info=" " src="templates/base/images/icons/exclamation.png" alt="error" class="warn"><div class="clear_both"></div>
     </td>
    </tr>
	<tr>
     <td class="caption"><span class="required">*</span>&nbsp;</td>
     <td class="value">
      <div class="clear_both"></div><div class="input_wrapper input_wrapper_checkbox"><input type="checkbox" id="photo_upload_form_input_agree" name="agree" class="form_input_checkbox" onchange="parent.oPhotoUpload.onFileChangedEvent();">
	  <label for="photo_upload_form_input_agree"><?php echo _t('_ibdw_evowall_iveright');?></label><div class="input_close input_close_checkbox"></div></div>
      <img float_info=" " src="templates/base/images/icons/exclamation.png" alt="error" class="warn"><div class="clear_both"></div>
	 </td>
    </tr>
	<tr>
     <td colspan="2" class="colspan">
      <div class="clear_both"></div><div class="input_wrapper input_wrapper_submit"><div class="button_wrapper">
      <input type="submit" value="<?php echo _t('_ibdw_evowall_continue');?>" name="upload" class="form_input_submit" disabled="disabled" onclick="return parent.oPhotoUpload._loading(true);">
      <div class="button_wrapper_close"></div></div><div class="input_close input_close_submit"></div></div>
      <img float_info=" " src="templates/base/images/icons/exclamation.png" alt="error" class="warn"><div class="clear_both"></div>
     </td>
    </tr>
   </tbody>
  </table>
  </div>
  </form>
 <?php 
 }
 else
 {
 ?>
 
 <form class="form_advanced" target="upload_file_frame" enctype="multipart/form-data" method="post" action="m/photos/albums/my/add_objects" name="upload" id="photo_upload_form">
  <input type="hidden" value="accept_upload" name="action" class="form_input_hidden bx-def-font">
  <input type="hidden" value="<?php echo trim($namephotoalbum);?>" name="extra_param_album" class="form_input_hidden bx-def-font">
  <input type="hidden" value="khzMiBmJYK6UeCw74j2p" name="csrf_token" class="form_input_hidden bx-def-font">
  <div class="form_advanced_wrapper photo_upload_form_wrapper">
  <table cellspacing="0" cellpadding="0" class="form_advanced_table" style="border:none;">
   <tbody>
    <tr>
     <td class="caption"><span class="required">*</span>Browse:</td>
     <td class="value">
      <div class="clear_both"></div><div class="input_wrapper input_wrapper_file"><input type="file" name="file[]" class="form_input_file bx-def-font" onchange="parent.oPhotoUpload.onFileChangedEvent();" multiplyable="true"></div>
	  <i float_info=" " class="warn sys-icon exclamation-sign"></i><div class="clear_both"></div>
     </td>
    </tr>
	<tr>
     <td class="caption"><span class="required">*</span>&nbsp;</td>
     <td class="value">
      <div class="clear_both"></div><div class="input_wrapper input_wrapper_checkbox "><input type="checkbox" id="photo_upload_form_input_agree" name="agree" class="form_input_checkbox bx-def-font" onchange="parent.oPhotoUpload.onFileChangedEvent();">
      <label for="photo_upload_form_input_agree"><?php echo _t('_ibdw_evowall_iveright');?></label></div>
      <i float_info=" " class="warn sys-icon exclamation-sign"></i><div class="clear_both"></div>
     </td>
    </tr>
	<tr>
     <td colspan="2" class="colspan">
      <div class="clear_both"></div><div class="input_wrapper input_wrapper_submit "><div class="button_wrapper">
      <input type="submit" value="Continue" name="upload" class="form_input_submit bx-btn" disabled="disabled" onclick="return parent.oPhotoUpload._loading(true);"></div></div>
      <i float_info=" " class="warn sys-icon exclamation-sign"></i><div class="clear_both"></div>
	 </td>
    </tr>
   </tbody>
  </table>
  </div>
  </form>
  
 <?php 
 }
 ?>
 </div>
 <div id="frammento_foto_flash" <?php if($metphotoflash == '' ) { echo 'style="display:none;"'; } ?> <?php if($metodiattiviphoto == 'Regular' ) { echo 'style="display:none;"'; } else { 'style="display:block !important;"'; } ?> > 
<?php
if ($uploaderPhotosNum>1)
{
?> 
  <div class="ibdw_evo_bt_list" onclick="open_bt_list('x_evo_list3');" id="fade_bt_listx_evo_list" style="right:1px;display:block">
      <input type="hidden" value="0" id="mm_setmenux_evo_list3" />
      <a class="bt_openx_evo_list" id="bt_open"><img src="<?php echo $imagepath;?>othernews.png"></a>
  </div>
<?php
}
?>
  <div class="ibdw_bt_superlist" id="lista_btx_evo_list3" style="right:1px;top:15px;">
      <?php if($photoaltri=='on') { echo '<a id="bottone_sub_elimina" href="m/photos/albums/my/add_objects/'.trim($namephotoalbum).'">'._t('_ibdw_evowall_altrimetodi').'</a>'; } ?>
      <?php if($metphotoregolare =='on' ) { echo '<a id="bottone_sub_elimina" href="javascript:lanciaregularfoto();">Use Regular</a>';}?> 
  </div> 
 
<?php 
 if ($versiond=="old")
 {
?>
 <iframe style="display:none;" name="upload_file_frame"></iframe>
  <script src="modules/boonex/photos/js/upload.js" type="text/javascript" language="javascript"></script>
  <script type="text/javascript">
   var oPhotoUpload = new BxPhotoUpload({
             iOwnerId: <?=$usercode?>
            });
  </script>

	<input  class="form_input_hidden" type="hidden" name="action" value="accept_multi_upload" />
    <div class="form_advanced_wrapper form_advanced_wrapper">
    <table  class="form_advanced_table" style="border:none;" cellpadding="0" cellspacing="0">
     <tbody >
      <tr>
       <td class="colspan" colspan="2">
        <div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_custom">
         <link href="plugins/swfupload/css/default.css" rel="stylesheet" type="text/css" />
		  
      
      
      <script type="text/javascript">
			var bx_swf_uploaders;

			if ('false' == 'true') 
			{
			 bxInitSwfUploaders();
			} 
			else 
			{
			 addEvent( window, 'load', function() { bxInitSwfUploaders() } );
			}
			/*window.onload = function()*/
    		function bxInitSwfUploaders() {
			bx_swf_uploaders = new SWFUpload({
			// Backend Settings
			upload_url: "m/photos/albums/my/add_objects",
			post_params : {"action": "accept_multi_files", "oid": "<?=$usercode?>" , "extra_param_album": "<?php echo trim($namephotoalbum); ?>"},

			// File Upload Settings
			file_size_limit : "209715200",
			file_types : "*.jpg;*.png;*.gif",
			file_types_description : "All Files",
			file_upload_limit : "10",
			file_queue_limit : "5",


			// Event Handler Settings (all my handlers are in the Handler.js file)
			file_dialog_start_handler : fileDialogStart,
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,


			// Button Settings
			button_window_mode: "opaque",
			button_image_url : "plugins/swfupload/images/XPButtonUploadText_61x22.png",
			button_placeholder_id : "spanButtonPlaceholder",
			button_width: 75,
			button_height: 25,
            button_text_top_padding : 4, 
            button_text : "<span class=\"swf_center\"> Select File </span>",
            button_text_style : ".swf_center { text-align:center;font-family:Verdana,Arial;color:#333;font-size:11px; }", 

			
			// Flash Settings
			flash_url : "plugins/swfupload/swf/swfupload.swf",
/*
			swfupload_element_id : "flashUI2",		// Setting from graceful degradation plugin
			degraded_element_id : "degradedUI2",	// Setting from graceful degradation plugin
*/

			custom_settings : {
				progressTarget : "fsUploadProgresss",
				cancelButtonId : "btnCancell"
			},


			// Debug Settings
			debug: false
		});

	 }
</script>

	<div id="content">
 	 <form id="form1" action="m/photos/albums/my/add_objects" method="post" enctype="multipart/form-data" style="margin-bottom:10px;">
     <div>
      <div class="fieldset flash" id="fsUploadProgresss"></div>
      <div>
       <span id="spanButtonPlaceholder"></span>
       <div class="button_wrapper">
       <input id="btnCancell" type="button" value="Cancel Upload" onclick="cancelQueue(bx_swf_uploaders);" disabled="disabled" class="form_input_submit" />
       <div class="button_wrapper_close"></div>
      </div>
     </div>
    </div>
    </form>
    <div id="accepted_files_blocks" class="evoblock"></div>
   </div>
   <div class="input_close input_close_custom"></div>
  </div>
  <img class="warn" alt="error" src="templates/base/images/icons/exclamation.png" float_info=" " />
  <div class="clear_both"></div>
  </td>
 </tr>
</tbody>
</table>
</div>

<div style="background-color:#ffdada;" id="accepted_files_blocks"  class="evoblock"></div>
<div id="photo_success_message" style="display:none;">
<div class="MsgBox" id="1266406470">
 <table class="MsgBox" cellpadding="0" cellspacing="0">
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_top_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
  <tr>
   <td class="msgbox_left_side"></td>
   <td class="msgbox_content">
    <div class="msgbox_text"><?php echo _t('_bx_photos_upl_succ') ?></div>
   </td>
   <td class="msgbox_right_side"></td>
  </tr>
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_bottom_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
 </table>
</div>
</div>

<div id="photo_failed_file_message" style="display:none;">
<div class="MsgBox" id="1266406470">
 <table class="MsgBox" cellpadding="0" cellspacing="0">
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_top_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
  <tr>
   <td class="msgbox_left_side"></td>
   <td class="msgbox_content">
    <div class="msgbox_text"><?php echo _t('_bx_photos_upl_file_err'); ?></div>
   </td>
   <td class="msgbox_right_side"></td>
  </tr>
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_bottom_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
 </table>
</div>
</div>

<div id="photo_failed_message" style="display:none;">
<div class="MsgBox" id="1266406470">
 <table class="MsgBox" cellpadding="0" cellspacing="0">
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_top_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
  <tr>
   <td class="msgbox_left_side"></td>
   <td class="msgbox_content">
    <div class="msgbox_text"><?php echo _t('_bx_photos_upl_file_err'); ?></div>
   </td>
   <td class="msgbox_right_side"></td>
  </tr>
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_bottom_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
 </table>
</div>
</div>

<div id="photo_embed_failed_message" style="display:none;">
<div class="MsgBox" id="1266406470">
 <table class="MsgBox" cellpadding="0" cellspacing="0">
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_top_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
  <tr>
   <td class="msgbox_left_side"></td>
   <td class="msgbox_content">
    <div class="msgbox_text"><?php echo _t('_bx_photos_emb_err'); ?></div>
   </td>
   <td class="msgbox_right_side"></td>
  </tr>
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_bottom_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
 </table>
</div>
</div>

<?
}
else
{
?>


<iframe style="display:none;" name="upload_file_frame"></iframe>
  <script src="modules/boonex/photos/js/upload.js" type="text/javascript" language="javascript"></script>
  <script type="text/javascript">
   var oPhotoUpload = new BxPhotoUpload({
             iOwnerId: <?=$usercode?>
            });
  </script>

	<input  class="form_input_hidden" type="hidden" name="action" value="accept_multi_upload" />
    <div class="form_advanced_wrapper form_advanced_wrapper">
    <table  class="form_advanced_table" style="border:none;" cellpadding="0" cellspacing="0">
     <tbody >
      <tr>
       <td class="colspan" colspan="2">
        <div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_custom">
         <link href="plugins/swfupload/css/default.css" rel="stylesheet" type="text/css" />
		  
      
      
      <script type="text/javascript">
			var bx_swf_uploaders;

			if ('false' == 'true') 
			{
			 bxInitSwfUploaders();
			} 
			else 
			{
			 addEvent( window, 'load', function() { bxInitSwfUploaders() } );
			}
			/*window.onload = function()*/
    		function bxInitSwfUploaders() {
			bx_swf_uploaders = new SWFUpload({
			// Backend Settings
			upload_url: "m/photos/albums/my/add_objects",
			post_params : {"action": "accept_multi_files", "oid": "<?=$usercode?>" , "extra_param_album": "<?php echo trim($namephotoalbum); ?>"},

			// File Upload Settings
			file_size_limit : "209715200",
			file_types : "*.jpg;*.png;*.gif",
			file_types_description : "All Files",
			file_upload_limit : "10",
			file_queue_limit : "5",


			// Event Handler Settings (all my handlers are in the Handler.js file)
			file_dialog_start_handler : fileDialogStart,
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,


			// Button Settings
			button_window_mode: "opaque",
			button_image_url : "templates/base/images/button_sprite.png",
			button_placeholder_id : "spanButtonPlaceholder",
			button_width: 136,
			button_height: 28,
            button_text_top_padding : 5, 
            button_text : "<span class=\"swf_center\">Select File</span>",
            button_text_style : ".swf_center {text-align:center; font-family:'Helvetica Neue', Helvetica, Arial, Verdana, sans-serif; color:#333; font-weight:bold; font-size:12px;}", 

			
			// Flash Settings
			flash_url : "plugins/swfupload/swf/swfupload.swf",
/*
			swfupload_element_id : "flashUI2",		// Setting from graceful degradation plugin
			degraded_element_id : "degradedUI2",	// Setting from graceful degradation plugin
*/

			custom_settings : {
				progressTarget : "fsUploadProgresss",
				cancelButtonId : "btnCancell"
			},


			// Debug Settings
			debug: false
		});

	 }
</script>
<style>
.input_wrapper_custom .swfupload {
    float: left;
}
input.form_input_submit:hover {
    border-color: #AAAAAA #999999 #888888;
    box-shadow: 0 1px 2px rgba(128, 128, 128, 0.4);
	-moz-box-shadow: 0 1px 2px rgba(128, 128, 128, 0.4);
	-webkit-box-shadow: 0 1px 2px rgba(128, 128, 128, 0.4);
    color: #000000;
    transition: all 0.2s linear 0s;
}
</style>
	<div id="content">
 	 <form id="form1" action="m/photos/albums/my/add_objects" method="post" enctype="multipart/form-data" style="margin-bottom:10px;">
     <div>
      <div class="fieldset flash" id="fsUploadProgresss"></div>
      <div>
       <span id="spanButtonPlaceholder"></span>
       <div class="button_wrapper">
       <input id="btnCancell" type="button" value="Cancel Upload" onclick="cancelQueue(bx_swf_uploaders);" disabled="disabled" class="form_input_submit" style="background: url('templates/base/images/button_sprite.png') repeat scroll 0 0 transparent;border: 0 none;color: #333333;font-family: 'Helvetica Neue',Helvetica,Arial,Verdana,sans-serif;font-size: 12px;font-weight: bold;height: 26px;width: 136px;" />
       <div class="button_wrapper_close"></div>
      </div>
     </div>
    </div>
    </form>
    <div id="accepted_files_blocks" class="evoblock"></div>
   </div>
   <div class="input_close input_close_custom"></div>
  </div>
  <i float_info=" " class="warn sys-icon exclamation-sign"></i>
  <div class="clear_both"></div>
  </td>
 </tr>
</tbody>
</table>
</div>

<div style="background-color:#ffdada;" id="accepted_files_blocks"  class="evoblock"></div>
<div id="photo_success_message" style="display:none;">
<div class="MsgBox" id="1266406470">
 <table class="MsgBox" cellpadding="0" cellspacing="0">
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_top_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
  <tr>
   <td class="msgbox_left_side"></td>
   <td class="msgbox_content">
    <div class="msgbox_text"><?php echo _t('_bx_photos_upl_succ') ?></div>
   </td>
   <td class="msgbox_right_side"></td>
  </tr>
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_bottom_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
 </table>
</div>
</div>

<div id="photo_failed_file_message" style="display:none;">
<div class="MsgBox" id="1266406470">
 <table class="MsgBox" cellpadding="0" cellspacing="0">
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_top_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
  <tr>
   <td class="msgbox_left_side"></td>
   <td class="msgbox_content">
    <div class="msgbox_text"><?php echo _t('_bx_photos_upl_file_err'); ?></div>
   </td>
   <td class="msgbox_right_side"></td>
  </tr>
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_bottom_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
 </table>
</div>
</div>

<div id="photo_failed_message" style="display:none;">
<div class="MsgBox" id="1266406470">
 <table class="MsgBox" cellpadding="0" cellspacing="0">
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_top_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
  <tr>
   <td class="msgbox_left_side"></td>
   <td class="msgbox_content">
    <div class="msgbox_text"><?php echo _t('_bx_photos_upl_file_err'); ?></div>
   </td>
   <td class="msgbox_right_side"></td>
  </tr>
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_bottom_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
 </table>
</div>
</div>

<div id="photo_embed_failed_message" style="display:none;">
<div class="MsgBox" id="1266406470">
 <table class="MsgBox" cellpadding="0" cellspacing="0">
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_top_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
  <tr>
   <td class="msgbox_left_side"></td>
   <td class="msgbox_content">
    <div class="msgbox_text"><?php echo _t('_bx_photos_emb_err'); ?></div>
   </td>
   <td class="msgbox_right_side"></td>
  </tr>
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_bottom_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
 </table>
</div>
</div>



<?
}
?>


</div>
</div>
<div class="clear"></div>
</div>
<?php } ?>

<?php 
//Ottengo il nome predefinito per l'album dell'utente
$richiestanome="SELECT VALUE FROM sys_options WHERE Name='bx_videos_profile_album_name'";
$resultrichiestanome = mysql_query($richiestanome);
$rowrichiestanome = mysql_fetch_row($resultrichiestanome); 
$metvideotube = $VideoYoutubeM;
$metvideoflash = $VideoFalshM;
$metodiattivivideo= $DefaultVideoM;
$videoaltri = $VideoOtherM;
?>
<?php if ($video=='on') {
//check if there is only an upload method enabled for Videos
$uploaderVideosNum=0;
if ($VideoYoutubeM) {$uploaderVideosNum++;}
if ($VideoFalshM) {$uploaderVideosNum++;}
if ($VideoOtherM) {$uploaderVideosNum++;}
 ?>
<div id="frammento_video" class="blocco_wall">
 <div id="box_video">
  <div id="bloccovideo_tube" <?php if($metvideotube == '' ) { echo 'style="display:none;"'; } ?> <?php if($metodiattivivideo == 'Flash' ) { echo 'style="display:none;"'; } else { 'style="display:block !important;"'; } ?>>
<?php
  if ($uploaderVideosNum>1)
 {
?> 
	 <div class="ibdw_evo_bt_list" onclick="open_bt_list('x_evo_list');" id="fade_bt_listx_evo_list" style="right:1px;display:block">
      <input type="hidden" value="0" id="mm_setmenux_evo_list" />
      <a class="bt_openx_evo_list" id="bt_open"><img src="<?php echo $imagepath;?>othernews.png"></a>
     </div>
<?php
}
?>    
     <div class="ibdw_bt_superlist" id="lista_btx_evo_list" style="right:1px;top:15px;">
      <?php if($metvideoflash == 'on' ) { echo '<a id="bottone_sub_elimina" href="javascript:lanciaclassic();">'._t('_ibdw_evowall_uploadfrompc').'</a>';}?>
      <?php if($videoaltri=='on') { echo '<a id="bottone_sub_elimina" href="m/videos/albums/my/add_objects/'.$namevideoalbum.'">'._t('_ibdw_evowall_altrimetodi').'</a>'; } ?> 
     </div> 
     <div id="bloccotubox">
     <form class="form_advanced" target="upload_file_frame" enctype="multipart/form-data" method="post" action="m/videos/albums/my/add_objects" 
name="embed" id="video_upload_form">
      <input type="hidden" value="accept_embed" name="action" class="form_input_hidden">            
      <input type="hidden" value="<?php echo $namevideoalbum;?>" name="extra_param_album" class="form_input_hidden">           
      <input type="hidden" value="k2J3/SY7BdEZjV&amp;RZHrB" name="csrf_token" class="form_input_hidden">
<div id="metodo_evo"> Select method: 

<select onchange="link_evo(this.value);" class="select_evo_list" name="source">
<?php include ("rayzembedactivated.php");?>
</select>
</div>
                        <div id="boxdiyoutube">
						<input type="text" id="textyoutube" name="embed" class="form_input_text" onclick="resettaci();if(this.value.indexOf('&')!=-1) {this.value=this.value.substring(0, this.value.indexOf('&'))}" onchange="if(this.value.indexOf('&')!=-1) {this.value=this.value.substring(0, this.value.indexOf('&'))}" value="<?echo _t('_ibdw_evowall_linktoyoutube');?>"
						 
                                        style="background-color:#FFFFFF;border:1px solid #D3DEE4;color:#666666;
                                        font-size:11px;height:22px;margin-bottom:3px;padding-left:5px;width:336px;" onblur="if (this.value==''){reimposta()};">
                                        <script>
                                              function resettaci() {
                                              $("#textyoutube").val(""); }
                                              
                                              function reimposta() {
                                              $("#textyoutube").val("<?echo _t('_ibdw_evowall_linktoyoutube');?>"); }
                                        </script>  
                        
                        <div id="youtubelink"><?php echo _t('_ibdw_evowall_example');?>: <span class="link_video_evo"><?php echo _t('_ibdw_evowall_exlinkyb');?></span></div>  
                        </div>   
                        
                <div>          
                <input type="submit" value="<?php echo _t('_ibdw_evowall_continue');?>" name="shoot" class="form_input_submit" style="margin-top:5px;">
                </div>
                </form> 
    <script>
      function link_evo(id){
      if(id=='bliptv'){ $(".link_video_evo").html('http://blip.tv/bandstandbusking/the-irrepressibles-nuclear-skies-4570027');}
      else if(id=='dailymotion'){ $(".link_video_evo").html('http://www.dailymotion.com/video/xeatzn_avengers-trailer_shortfilms');}
      else if(id=='gaywatch'){ $(".link_video_evo").html('http://gaywatch.com/view_video.php?id=17000');}								  
      else if(id=='godtube'){ $(".link_video_evo").html('http://www.godtube.com/watch/?v=FB0J1FNU');}
      else if(id=='megavideo'){ $(".link_video_evo").html('http://megavideo.com/?v=WDZQRPJL');}
      else if(id=='metacafe'){ $(".link_video_evo").html('http://www.metacafe.com/watch/5033837/ninja_iii_the_domination_movie_trailer/');}
	  else if(id=='movieclips'){ $(".link_video_evo").html('http://movieclips.com/aPSRU-john-carter-movie-trailer-2/');}
      else if(id=='myspace'){ $(".link_video_evo").html('http://www.myspace.com/video/thesidh/celtica-2012/108748468');}
      else if(id=='redtube'){ $(".link_video_evo").html('http://www.redtube.com/35562');}
      else if(id=='sextube'){ $(".link_video_evo").html('http://www.sextube.com/media/74052/Janelle_young_mom_having_her_pussy_gyno_speculum_examined/');}
      else if(id=='slutload'){ $(".link_video_evo").html('http://www.slutload.com/watch/eacQRLpKob3/4-Teens-lesbian-play-with-toys-after-basket.html');}
      else if(id=='viddler'){ $(".link_video_evo").html('http://www.viddler.com/explore/garyvaynerchuk/videos/181/');}
      else if(id=='vimeo'){ $(".link_video_evo").html('http://vimeo.com/1712769');}
      else if(id=='wattv'){ $(".link_video_evo").html('http://www.wat.tv/video/justice-interview-live-cooperative-4tzj3_2ixpf_.html');}
      else if(id=='xtube'){ $(".link_video_evo").html('http://www.xtube.com/watch.php?v=BTWWj-S549-');}
      else if(id=='xvideos'){ $(".link_video_evo").html('http://www.xvideos.com/video1106510/mutual_masturbation');}
      else if(id=='xxxbunker'){ $(".link_video_evo").html('http://xxxbunker.com/2885730');}
	  else if(id=='youku'){ $(".link_video_evo").html('http://v.youku.com/v_show/id_XMjU0NjY4OTEy.html');}
      else if(id=='youtube'){ $(".link_video_evo").html('http://www.youtube.com/watch?v=_AANZvYn5uk');}	  
      }
    </script>
    <div style="clear:both">  </div>
    </div> 
    </div>
    <div id="bloccovideo_classic" <?php if($metvideoflash == '' ) { echo 'style="display:none;"'; } ?> <?php if($metodiattivivideo == 'Youtube' ) { echo 'style="display:none;"'; } else { 'style="display:block !important;"'; } ?> > 
<?php
if ($uploaderVideosNum>1)
 {
?>    
    <div class="ibdw_evo_bt_list" onclick="open_bt_list('x_evo_list1');" id="fade_bt_listx_evo_list" style="right:1px;display:block">
      <input type="hidden" value="0" id="mm_setmenux_evo_list1" />
      <a class="bt_openx_evo_list" id="bt_open"><img src="<?php echo $imagepath;?>othernews.png"></a>
     </div>
<?php
}
?>    
     <div class="ibdw_bt_superlist" id="lista_btx_evo_list1" style="right:1px;top:15px;">
     <?php if($metvideotube == 'on' ) { echo '<a id="bottone_sub_elimina" href="javascript:lanciatube();">'._t('_ibdw_evowall_uploadfromyoutube').'</a>'; } ?>
     </div> 
     <iframe style="display:none;" name="upload_file_frame"></iframe>
      <script src="modules/boonex/videos/js/upload.js" type="text/javascript" language="javascript"></script>
      <script type="text/javascript">
       var oVideoUpload = new BxVideoUpload({
       iOwnerId: <?=$usercode?>
       });
      </script>

	  <input class="form_input_hidden" type="hidden" name="action" value="accept_multi_upload" />
      <div class="form_advanced_wrapper form_advanced_wrapper">
<?php 
 if ($versiond=="old")
 {
?>      
	  <table class="form_advanced_table" style="border:none;" cellpadding="0" cellspacing="0">
       <tbody>
        <tr>
         <td class="colspan" colspan="2">
          <div class="clear_both"></div>
          <div class="input_wrapper input_wrapper_custom">
           <link href="plugins/swfupload/css/default.css" rel="stylesheet" type="text/css" />
		   <script type="text/javascript">

			var bx_swf_uploader;
			if ('false' == 'true') 
			{
			 bxInitSwfUploader();
		    } 
			else 
			{
			 addEvent( window, 'load', function() { bxInitSwfUploader() } );
			}

	/*window.onload = function()*/
    function bxInitSwfUploader() {
		bx_swf_uploader = new SWFUpload({

			// Backend Settings
			upload_url: "m/videos/albums/my/add_objects",
			post_params : {"action": "accept_multi_files", "oid": "<?=$usercode?>" , "extra_param_album": "<?php echo $namevideoalbum; ?>" },

			// File Upload Settings
			file_size_limit : "209715200",
			file_types : "*.avi;*.flv;*.mpg;*.wmv;*.mp4;*.m4v;*.mov;*.divx;*.xvid",
			file_types_description : "Video Files",
			file_upload_limit : "10",
			file_queue_limit : "5",

			// Event Handler Settings (all my handlers are in the Handler.js file)
			file_dialog_start_handler : fileDialogStart,
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,

			// Button Settings
			button_window_mode: "opaque",
			button_image_url : "plugins/swfupload/images/XPButtonUploadText_61x22.png",
			button_placeholder_id : "spanButtonPlaceholders",
			button_width: 75,
			button_height: 25,
            button_text_top_padding : 4, 
            button_text : "<span class=\"swf_center\">Select File</span>",
            button_text_style : ".swf_center { text-align:center;font-family:Verdana,Arial;color:#333;font-size:11px; }", 

			// Flash Settings
			flash_url : "plugins/swfupload/swf/swfupload.swf",
/*
			swfupload_element_id : "flashUI2",		// Setting from graceful degradation plugin
			degraded_element_id : "degradedUI2",	// Setting from graceful degradation plugin
*/

			custom_settings : {
				progressTarget : "fsUploadProgress",
				cancelButtonId : "btnCancel"
			},

			// Debug Settings
			debug: false
		});
	 }
    </script>

    <div id="content">
     <form id="form1" action="m/videos/albums/my/add_objects" method="post" enctype="multipart/form-data" style="margin-bottom:10px;">
      <div>
       <div class="fieldset flash" id="fsUploadProgress"></div>
        <div>
         <span id="spanButtonPlaceholders"></span>
         <div class="button_wrapper">
          <input id="btnCancel" type="button" value="Cancel Upload" onclick="cancelQueue(bx_swf_uploader);" disabled="disabled" class="form_input_submit" />
	      <div class="button_wrapper_close"></div>
         </div>
        </div>
       </div>
       </form>
       <div id="accepted_files_blocks"  class="evoblock"></div>
      </div>
	  <div class="input_close input_close_custom"></div>
	 </div>
	 <img class="warn" alt="error" src="templates/base/images/icons/exclamation.png" float_info=" " />
     <div class="clear_both"></div>
    </td>
   </tr>
  </tbody>
 </table>
 <?
 }
 else
 {
 ?>
 <table class="form_advanced_table" style="border:none;" cellpadding="0" cellspacing="0">
       <tbody>
        <tr>
         <td class="colspan" colspan="2">
          <div class="clear_both"></div>
          <div class="input_wrapper input_wrapper_custom">
           <link href="plugins/swfupload/css/default.css" rel="stylesheet" type="text/css" />
		   <script type="text/javascript">

			var bx_swf_uploader;
			if ('false' == 'true') 
			{
			 bxInitSwfUploader();
		    } 
			else 
			{
			 addEvent( window, 'load', function() { bxInitSwfUploader() } );
			}

	/*window.onload = function()*/
    function bxInitSwfUploader() {
		bx_swf_uploader = new SWFUpload({

			// Backend Settings
			upload_url: "m/videos/albums/my/add_objects",
			post_params : {"action": "accept_multi_files", "oid": "<?=$usercode?>" , "extra_param_album": "<?php echo $namevideoalbum; ?>" },

			// File Upload Settings
			file_size_limit : "209715200",
			file_types : "*.avi;*.flv;*.mpg;*.wmv;*.mp4;*.m4v;*.mov;*.divx;*.xvid",
			file_types_description : "Video Files",
			file_upload_limit : "10",
			file_queue_limit : "5",

			// Event Handler Settings (all my handlers are in the Handler.js file)
			file_dialog_start_handler : fileDialogStart,
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,

			// Button Settings
			button_window_mode: "opaque",
			button_image_url : "templates/base/images/button_sprite.png",
			button_placeholder_id : "spanButtonPlaceholders",
			button_width: 136,
			button_height: 28,
            button_text_top_padding : 5, 
            button_text : "<span class=\"swf_center\">Select File</span>",
            button_text_style : ".swf_center {text-align:center; font-family:'Helvetica Neue', Helvetica, Arial, Verdana, sans-serif; color:#333; font-weight:bold; font-size:12px;}", 

			// Flash Settings
			flash_url : "plugins/swfupload/swf/swfupload.swf",
/*
			swfupload_element_id : "flashUI2",		// Setting from graceful degradation plugin
			degraded_element_id : "degradedUI2",	// Setting from graceful degradation plugin
*/

			custom_settings : {
				progressTarget : "fsUploadProgress",
				cancelButtonId : "btnCancel"
			},

			// Debug Settings
			debug: false
		});
	 }
    </script>

    <div id="content">
     <form id="form1" action="m/videos/albums/my/add_objects" method="post" enctype="multipart/form-data" style="margin-bottom:10px;">
      <div>
       <div class="fieldset flash" id="fsUploadProgress"></div>
        <div>
         <span id="spanButtonPlaceholders"></span>
         <div class="button_wrapper">
          <input id="btnCancel" type="button" value="Cancel Upload" onclick="cancelQueue(bx_swf_uploader);" disabled="disabled" class="form_input_submit" style="background: url('templates/base/images/button_sprite.png') repeat scroll 0 0 transparent;border: 0 none;color: #333333;font-family: 'Helvetica Neue',Helvetica,Arial,Verdana,sans-serif;font-size: 12px;font-weight: bold;height: 26px;width: 136px;" />
	      <div class="button_wrapper_close"></div>
         </div>
        </div>
       </div>
       </form>
       <div id="accepted_files_blocks"  class="evoblock"></div>
      </div>
	  <div class="input_close input_close_custom"></div>
	 </div>
	 <i float_info=" " class="warn sys-icon exclamation-sign"></i>
     <div class="clear_both"></div>
    </td>
   </tr>
  </tbody>
 </table>
 <?
 }
 ?>
 
 
</div>

<div style="background-color:#ffdada;" id="accepted_files_blocks"  class="evoblock"></div>
<div id="video_success_message" style="display:none;"><div class="MsgBox" id="1266485442">

<table class="MsgBox" cellpadding="0" cellspacing="0">
 <tr>
  <td class="msgbox_corner"></td>
  <td class="msgbox_top_side"></td>
  <td class="msgbox_corner"></td>
 </tr>
 <tr>
  <td class="msgbox_left_side"></td>
  <td class="msgbox_content">
   <div class="msgbox_text"><?php echo _t('_bx_videos_upl_succ'); ?></div>
  </td>
  <td class="msgbox_right_side"></td>
 </tr>
 <tr>
  <td class="msgbox_corner"></td>
  <td class="msgbox_bottom_side"></td>
  <td class="msgbox_corner"></td>
 </tr>
</table>
</div>
</div>

<div id="video_failed_file_message" style="display:none;">
 <div class="MsgBox" id="1266485442">
  <table class="MsgBox" cellpadding="0" cellspacing="0">
   <tr>
    <td class="msgbox_corner"></td>
	<td class="msgbox_top_side"></td>
	<td class="msgbox_corner"></td>
   </tr>
   <tr>
    <td class="msgbox_left_side"></td>
	<td class="msgbox_content"><div class="msgbox_text"><?php echo _t('_bx_videos_upl_file_err'); ?></div></td>
	<td class="msgbox_right_side"></td>
   </tr>
   <tr>
	<td class="msgbox_corner"></td>
	<td class="msgbox_bottom_side"></td>
	<td class="msgbox_corner"></td>
   </tr>
  </table>
</div>
</div>

<div id="video_failed_message" style="display:none;"><div class="MsgBox" id="1266485442">
 <table class="MsgBox" cellpadding="0" cellspacing="0">
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_top_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
  <tr>
   <td class="msgbox_left_side"></td>
   <td class="msgbox_content"><div class="msgbox_text"><?php echo _t('_bx_videos_upl_file_err'); ?></div></td>
   <td class="msgbox_right_side"></td>
  </tr>
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_bottom_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
 </table>
</div>
</div>

<div id="video_embed_failed_message" style="display:none;"><div class="MsgBox" id="1266485442">
 <table class="MsgBox" cellpadding="0" cellspacing="0">
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_top_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
  <tr>
   <td class="msgbox_left_side"></td>
   <td class="msgbox_content"><div class="msgbox_text"><?php echo _t('_bx_videos_emb_err'); ?></div></td>
   <td class="msgbox_right_side"></td>
  </tr>
  <tr>
   <td class="msgbox_corner"></td>
   <td class="msgbox_bottom_side"></td>
   <td class="msgbox_corner"></td>
  </tr>
 </table>
</div>
</div>
</div>
</div>
<div class="clear"></div>
</div>
<?php } ?>
<div id="accepted_files_block" class="evoblock"></div>
<div class="input_close input_close_custom"></div>
<div class="clear_both"></div>  
<div style="background-color:#ffdada;" id="accepted_files_block" class="evoblock"></div>
