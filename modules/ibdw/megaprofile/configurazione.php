<script type="text/javascript" src="js/jmini.js"></script>
<?
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
$userid = (int)$_COOKIE['memberID'];
if(!isAdmin()) { exit;}
mysql_query("SET NAMES 'utf8'");
$configquery= "SELECT * FROM `megaprofile_config` LIMIT 0 , 1";
$resultac = mysql_query($configquery);
$rowconfig = mysql_fetch_assoc($resultac);  
?>
<style>
a {
    color: #000000;
    text-decoration: none;
}
a:hover {
    color: #FFFFFF;
    text-decoration: none;
}
body {
    background: none repeat scroll 0 0 #334962;
    font-family: Verdana;
    font-size: 11px;
    margin: 0;
    text-align: center;
}
.clear {
    clear: both;
}
#pagina {
    background: url("templates/base/images/spyconfiglogo.jpg") no-repeat scroll 11px 8px #283B51;
    border: 7px solid #FFFFFF;
    color: #FFFFFF;
    margin: 20px auto;
    padding: 20px;
    width: 940px;
}
#boxgeneraleconfigurazione {
    float: left;
    margin-top: 110px;
    padding: 5px;
    text-align: left;
}
.introtitle {
    float: left;
    font-size: 14px;
    font-weight: bold;
    height: 26px;
    line-height: 23px;
    margin-top: 14px;
    width: 100%;
}
.introdesc {
    color: #444;
    float: left;
    font-size: 11px;
    font-style: italic;
    line-height: 13px;
    min-height: 38px;
    width: 100%;
}
#contentbox {
    background-color: #DDDDDD;
    border: 1px solid #FFFFFF;
    float: left;
    font-size: 10px;
    line-height: 11px;
    margin: 10px 0;
    padding: 0;
    width: 926px;
}
#return {
    border: 1px solid #FFFFFF;
    color: #FFFFFF;
    float: right;
    font-size: 15px;
    height: 31px;
    line-height: 27px;
    margin-right: 15px;
    margin-top: -105px;
    padding: 0 13px;
}
#return:hover {
    background: none repeat scroll 0 0 #999999;
}
#return a {
    color: #FFFFFF;
}
.unquarto {
    float: left;
    font-size: 11px;
    margin-left: 4px;
    width: 49%;
}
.unsesto {
    float: left;
    font-size: 11px;
    
    margin-left: 3px;
    margin-top: 3px;
    width: 49%;
}
.unquarto2 {
    float: left;
    font-size: 11px;
    margin-left: 15px;
}
.unsesto2 {
    float: left;
    font-size: 11px;
    font-weight: bold;
    line-height: 14px;
    margin-left: 5px;
    margin-top: 3px;
    width: 25%;
}
.tutto {
    border-bottom: 1px dotted;
    float: left;
    margin: 6px 0;
    width: 100%;
}
.contentcon {
    border-bottom: 1px solid #FFFFFF;
    float: left;
    margin: 3px 2px 2px;
    width: 100%;
}
.box_nascosto {
    display: none;
    float: left;
    width: 100%;
}
.unquarto2 input {
    width: 200px;
}
.btup {
    background: none repeat scroll 0 0 #283B51;
    border: 1px solid #DDDDDD;
    color: #FFFFFF;
    float: left;
    font-size: 10px;
    margin: 4px;
    padding: 6px;
}
.updateform {
    background: none repeat scroll 0 0 #FFFFFF;
    border: medium none;
    clear: none;
    color: #333333;
    float: right;
    font-size: 11px;
    font-weight: bold;
    margin: 8px 8px 7px;
}
.infonamevoice {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #666666;
    color: #666655;
    font-size: 13px;
    margin: 3px 3px 3px 0;
    padding: 5px 10px;
    width: 173px;
}
.btup:hover {
    background: none repeat scroll 0 0 #FFFFFF;
    color: #333333;
    cursor: pointer;
}
.formleft_bx {
    float: left;
    font-size: 13px;
    font-weight: bold;
    line-height: 31px;
    text-align: right;
    width: 67px;
    color:#333;
}
.formright_bx {
    float: right;
    margin-left: 0;
    text-align: left;
    width: 313px;
}
.formright_bx input {
    border: 1px solid #666666;
    margin: 2px;
    padding: 5px;
    width: 304px;
}
.formright_bx textarea {
    border: 1px solid #666666;
    margin: 2px;
    padding: 5px;
    width: 304px;
}
.updateform:hover {
    color: #000000;
    cursor: pointer;
}
.newitemstyle {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 7px solid #283B51;
    display: none;
    height: 259px;
    left: 50%;
    margin-left: -220px;
    margin-top: -142px;
    padding: 20px;
    position: fixed;
    top: 50%;
    width: 400px;
}
.newitemstyle h2 {
    border-bottom: 1px solid #DDDDDD;
    color: #666666;
    font-size: 12px;
    margin: -6px 0 16px;
    padding: 0 0 6px;
}

.bt_add {
    background: #FFF;
    float: right;
    font-size: 11px;
    font-weight: bold;
    padding: 6px;
    margin-top: -20px;
    position: relative;
    top: -7px;
    color: #666;
}
.bt_add:hover {
    cursor: pointer;
    color: #000;
}
</style>

<html>
<body>
<div id="pagina">
 <div id="boxgeneraleconfigurazione">
  <div id="return"><a href="../../../<?php echo $admin_dir;?>"><?php echo _t("_ibdw_mp_backadmin");?></a></div>
  <div id="contentbox">
	<form action="updateconfig.php" method="POST">
	
	<table width="100%" cellpadding="10">
	<tr>
	 <td width=50% valign=top style="border-right:1px solid #fff;padding:0 10px">
	  <div class="introtitle">Content box settings</div> 
	  <div class="introdesc">Enable/Disable the items to display in the menu and also set the values for your preference</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Name format</div>
	  <div class="unquarto">
	   <select name="nickname">
	    <option value="0" <?php if($rowconfig['usernameformat']=='0') {echo 'selected="selected"';}?>>Nickname</option>
	    <option value="1" <?php if($rowconfig['usernameformat']=='1') {echo 'selected="selected"';}?>>Real name</option>
	    <option value="2" <?php if($rowconfig['usernameformat']=='2') {echo 'selected="selected"';}?>>First name</option>
	   </select>
	  </div>
	  <div class="tutto"></div>   
	  <div class="unsesto">Send Message</div><div class="unquarto"><input type="radio" name="sndmessage" value="ON" <?php if($rowconfig['sndmessage']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="sndmessage" value="OFF" <?php if($rowconfig['sndmessage']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Befriend/Unfriend action</div><div class="unquarto"><input type="radio" name="friendblock" value="ON" <?php if($rowconfig['friendblock']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="friendblock" value="OFF" <?php if($rowconfig['friendblock']=='OFF') {echo 'checked';}?>/>OFF</div>
      <div class="tutto"></div>
  	  <div class="unsesto">Block/Unblock action</div><div class="unquarto"><input type="radio" name="blockblock" value="ON" <?php if($rowconfig['blockblock']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="blockblock" value="OFF" <?php if($rowconfig['blockblock']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Photo link</div><div class="unquarto"><input type="radio" name="linkphoto" value="ON" <?php if($rowconfig['linkphoto']=='ON') {echo 'checked';}?> />ON <input type="radio" name="linkphoto" value="OFF" <?php if($rowconfig['linkphoto']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
      <div class="unsesto">Video link</div><div class="unquarto"><input type="radio" name="linkvideo" value="ON" <?php if($rowconfig['linkvideo']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="linkvideo" value="OFF" <?php if($rowconfig['linkvideo']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
      <div class="unsesto">Sound link</div><div class="unquarto"><input type="radio" name="linksound" value="ON" <?php if($rowconfig['linksound']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="linksound" value="OFF" <?php if($rowconfig['linksound']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
      <div class="unsesto">Greetings link</div><div class="unquarto"><input type="radio" name="greet" value="ON" <?php if($rowconfig['greet']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="greet" value="OFF" <?php if($rowconfig['greet']=='OFF') {echo 'checked';}?>/>OFF</div>
      <div class="tutto"></div>
      <div class="unsesto">Subscribe link</div><div class="unquarto"><input type="radio" name="subscribe" value="ON" <?php if($rowconfig['subscribe']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="subscribe" value="OFF" <?php if($rowconfig['subscribe']=='OFF') {echo 'checked';}?>/>OFF</div>
      <div class="tutto"></div>      
      <div class="unsesto">Spam link</div><div class="unquarto"><input type="radio" name="rpspam" value="ON" <?php if($rowconfig['rpspam']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="rpspam" value="OFF" <?php if($rowconfig['rpspam']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Display Customize link</div><div class="unquarto"><input type="radio" name="custompro" value="ON" <?php if($rowconfig['custompro']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="custompro" value="OFF" <?php if($rowconfig['custompro']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
      <div class="unsesto">Display Fave link</div><div class="unquarto"><input type="radio" name="favepro" value="ON" <?php if($rowconfig['favepro']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="favepro" value="OFF" <?php if($rowconfig['favepro']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Display photo album block</div><div class="unquarto"><input type="radio" name="photoalbum" value="ON" <?php if($rowconfig['photoalbum']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="photoalbum" value="OFF" <?php if($rowconfig['photoalbum']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Display video album block</div><div class="unquarto"><input type="radio" name="videoalbum" value="ON" <?php if($rowconfig['videoalbum']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="videoalbum" value="OFF" <?php if($rowconfig['videoalbum']=='OFF') {echo 'checked';}?>/>OFF</div>
      <div class="tutto"></div>
      <div class="unsesto">Display friends block</div><div class="unquarto"><input type="radio" name="friend" value="ON" <?php if($rowconfig['friend']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="friend" value="OFF" <?php if($rowconfig['friend']=='OFF') {echo 'checked';}?>/>OFF</div>
      <div class="tutto"></div>
      <div class="unsesto">Display friends randomly</div><div class="unquarto"><input type="radio" name="friendsord" value="1" <?php if($rowconfig['friendsord']=='1') {echo 'checked';}?>/>ON <input type="radio" name="friendsord" value="0" <?php if($rowconfig['friendsord']=='0') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
      <div class="unsesto">Friends number (displayed in the block)</div><div class="unquarto"><input type="text" name="nfriend" value="<?php echo $rowconfig['nfriend'];?>"> </div>
	  <div class="tutto"></div>
	  <div class="unsesto">Display mutual friends block</div><div class="unquarto"><input type="radio" name="mutualfr" value="ON" <?php if($rowconfig['mutualfr']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="mutualfr" value="OFF" <?php if($rowconfig['mutualfr']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
      <div class="unsesto">Display mutual friends randomly</div><div class="unquarto"><input type="radio" name="mutualfriendord" value="1" <?php if($rowconfig['mutualfriendord']=='1') {echo 'checked';}?>/>ON <input type="radio" name="mutualfriendord" value="0" <?php if($rowconfig['mutualfriendord']=='0') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
      <div class="unsesto">Mutual friends number (displayed in the block)</div><div class="unquarto"><input type="text" name="nfriendm" value="<?php echo $rowconfig['nfriendm'];?>"> </div>
	  <div class="tutto"></div>
	  <div class="unsesto">Thumbs with name</div><div class="unquarto"><input type="radio" name="namestm" value="ON" <?php if($rowconfig['namestm']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="namestm" value="OFF" <?php if($rowconfig['namestm']=='OFF') {echo 'checked';}?>/>OFF <br />(Use <b>ON</b> only when you uses the real name, because otherwise you can get the overlapping of names)</div>
	  <div class="tutto"></div>
    
    
     <div class="unsesto">Thumbnail type</div>
	  <div class="unquarto">
	   <select name="thumbtype">
	    <option value="0" <?php if($rowconfig['thumbtype']=='0') {echo 'selected="selected"';}?>>Avatar</option>
	    <option value="1" <?php if($rowconfig['thumbtype']=='1') {echo 'selected="selected"';}?>>Member Thumb</option>
	   </select>
	  </div>
    <div class="tutto"></div>
    
	  <div class="unsesto">Allow Webcam</div><div class="unquarto"><input type="radio" name="webcam" value="ON" <?php if($rowconfig['webcam']=='ON') {echo 'checked';}?> />ON <input type="radio" name="webcam" value="OFF" <?php if($rowconfig['webcam']=='OFF') {echo 'checked';}?>/>OFF</div>
 	  <div class="tutto"></div>	
 	  <div class="unsesto">Upload max filesize (Max: <?php echo ini_get('upload_max_filesize');?>) - in bytes</div><div class="unquarto"><input type="text" name="maxsize" value="<?php echo $rowconfig['maxsize'];?>"></div>
 	  <div class="tutto"></div>	
	  <div class="unsesto">Set avatar checked by Default</div><div class="unquarto"><input type="radio" name="setavatard" value="ON" <?php if($rowconfig['setavatard']=='ON') {echo 'checked';}?> />YES<input type="radio" name="setavatard" value="OFF" <?php if($rowconfig['setavatard']=='OFF') {echo 'checked';}?>/>NO</div>
	  <div class="tutto"></div>	
	  <div class="unsesto">Use the first photo available of the profile album.<br/><span style="font-weight:normal;color:#444;">(Use this feature only if you are using the module Boonex Avatar)</span></div><div class="unquarto"><input type="radio" name="defimage" value="0" <?php if($rowconfig['defimage']=='0') {echo 'checked';}?> />YES<input type="radio" name="defimage" value="1" <?php if($rowconfig['defimage']=='1') {echo 'checked';}?>/>NO</div>
	 </td>
	 
	 
	 <td width=50% valign=top style="border-left:1px solid #fff;padding:0 10px">
	 <div class="introtitle">Profile Information settings</div> 
	 <div class="introdesc">Enable/Disable the items to display in the profile informations box</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Display information</div><div class="unquarto"><input type="radio" name="infoblock" value="ON" <?php if($rowconfig['infoblock']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="infoblock" value="OFF" <?php if($rowconfig['infoblock']=='OFF') {echo 'checked';}?>/>OFF</div>
 	  <div class="tutto"></div>
      <div class="unsesto">Description</div><div class="unquarto"><input type="radio" name="descblock" value="ON" <?php if($rowconfig['descblock']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="descblock" value="OFF" <?php if($rowconfig['descblock']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
      <div class="unsesto">Profile Description<br>Max Chars</div><div class="unquarto"><input type="text" name="crtdesk" value="<?php echo $rowconfig['crtdesk'];?>"> </div>
	  <div class="tutto"></div>
	  <div class="unsesto">Album number (displayed in the block)</div><div class="unquarto"><input type="text" name="nalbum" value="<?php echo $rowconfig['nalbum'];?>"> </div>
      <div class="tutto"></div>
	  <div class="unsesto">Albums Description<br>Max Chars</div><div class="unquarto"><input type="text" name="albumdescr" value="<?php echo $rowconfig['albumdescr'];?>"> </div>
	  <div class="tutto"></div>
	  <div class="unsesto">Date</div>
      <div class="unquarto">
	   <select name="frmdata">
	    <option value="0" <?php if($rowconfig['frmdata']=='0') {echo 'selected="selected"';}?>>European</option>
	    <option value="1" <?php if($rowconfig['frmdata']=='1') {echo 'selected="selected"';}?>>Universal</option>
	   </select>
      </div>
	  <div class="tutto"></div>
	  <div class="unsesto">Ralationship status</div><div class="unquarto"><input type="radio" name="relstatusview" value="ON" <?php if($rowconfig['relstatusview']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="relstatusview" value="OFF" <?php if($rowconfig['relstatusview']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Date of birth</div><div class="unquarto"><input type="radio" name="datebirthview" value="ON" <?php if($rowconfig['datebirthview']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="datebirthview" value="OFF" <?php if($rowconfig['datebirthview']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Display Age, date of birth or birthday?</div><div class="unquarto"><input type="radio" name="agestyle" value="1" <?php if($rowconfig['agestyle']=='1') {echo 'checked';}?>/>Age<input type="radio" name="agestyle" value="2" <?php if($rowconfig['agestyle']=='2') {echo 'checked';}?>/>Date<input type="radio" name="agestyle" value="3" <?php if($rowconfig['agestyle']=='3') {echo 'checked';}?>/>BDay</div>
	  <div class="tutto"></div>
	  <div class="unsesto">City</div><div class="unquarto"><input type="radio" name="infocityview" value="ON" <?php if($rowconfig['infocityview']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="infocityview" value="OFF" <?php if($rowconfig['infocityview']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Headline</div><div class="unquarto"><input type="radio" name="headlineview" value="ON" <?php if($rowconfig['headlineview']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="headlineview" value="OFF" <?php if($rowconfig['headlineview']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Email</div><div class="unquarto"><input type="radio" name="emailview" value="ON" <?php if($rowconfig['emailview']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="emailview" value="OFF" <?php if($rowconfig['emailview']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Sex</div><div class="unquarto"><input type="radio" name="sexview" value="ON" <?php if($rowconfig['sexview']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="sexview" value="OFF" <?php if($rowconfig['sexview']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Looking for</div><div class="unquarto"><input type="radio" name="lookingforview" value="ON" <?php if($rowconfig['lookingforview']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="lookingforview" value="OFF" <?php if($rowconfig['lookingforview']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Occupation</div><div class="unquarto"><input type="radio" name="occupationview" value="ON" <?php if($rowconfig['occupationview']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="occupationview" value="OFF" <?php if($rowconfig['occupationview']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  <div class="unsesto">Religion</div><div class="unquarto"><input type="radio" name="religionview" value="ON" <?php if($rowconfig['religionview']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="religionview" value="OFF" <?php if($rowconfig['religionview']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="tutto"></div>
	  
	  <div class="introtitle">Chat with the profile (Simple Messenger)</div> 
	  <div class="tutto"></div>
	  <div class="unsesto">Enabled for</div>
	  <div class="unquarto">
	   <select name="typeofallowed">
	    <option value="0" <?php if($rowconfig['typeofallowed']=='0') {echo 'selected="selected"';}?>>Nobody</option>
	    <option value="1" <?php if($rowconfig['typeofallowed']=='1') {echo 'selected="selected"';}?>>Friends</option>
	    <option value="2" <?php if($rowconfig['typeofallowed']=='2') {echo 'selected="selected"';}?>>Members</option>
	   </select>
	  </div>
	  <div class="tutto"></div>  
	  
	  <div class="introtitle">Report Spam System</div> 
	  <div class="tutto"></div>
	  <div class="unsesto">Select the tool</div>
	  <div class="unquarto">
	   <select name="reportspamtool">
	    <option value="0" <?php if($rowconfig['reportspamtool']=='0') {echo 'selected="selected"';}?>>Boonex Default Report</option>
	    <option value="1" <?php if($rowconfig['reportspamtool']=='1') {echo 'selected="selected"';}?>>Modzzz Reporting Tool</option>
	   </select>
	  </div>
	  <div class="tutto"></div>   
	  
	  
	  <div class="introtitle">Add the Custom Links</div> 
	  <div class="introdesc">Here you can add and edit your custom link for the Profile page (owner page) and for the ProfileView Page (the other profile's page).</div>
	  <div class="tutto"></div>
	  <div style="clear:both;"></div>
    <div class="introtitle">Add Custom Links For the owner page</div>
	  <div class="bt_add" onclick="newitemfade(0);">New item</div>
    <script>
      function newitemfade(exem){
      $(".newitemstyle").fadeIn();
      $("#modedestination").val(exem);
      }
      function newitemfadeout(){
      $(".newitemstyle").fadeOut();
      }
    </script>
    <div class="tutto"></div>
	  <?php
	  $linkmyprofile="SELECT * FROM ibdw_mega_extralink WHERE Destination=0 ORDER BY ibdw_mega_extralink.Order ASC";
	  $ottienilinkprofile=mysql_query($linkmyprofile) or die(mysql_error());
	  $identificatore = 0;
	  while($row = mysql_fetch_array($ottienilinkprofile))
      {
     $identificatore++; 
     echo '<div class="bloccoriga'.$identificatore.'">';
	   echo '<div class="bloxnascondi'.$identificatore.'"><div class="unsesto2" style="float: left; font-size: 13px; font-weight: bold; line-height: 26px; margin-top: 3px; margin-left: 26px; width: 44px;">Name</div><div class="unquarto2"><div class="infonamevoice" id="nameupdate'.$identificatore.'">'.$row['Name'].'</div></div>';
	   echo '<div class="btup" onclick="mostra_blox('.$identificatore.');">EDIT</div><div class="btup" onclick="delete_blox('.$identificatore.','.$row['ID'].');">DELETE</div></div>';
     echo '<div class="box_nascosto" id="blox_hid'.$identificatore.'">';
     echo '<div class="formleft_bx">
                        Name<br/>
                        Lang Key<br/>
                        Status</br>
                        <div style="height:18px;"></div>
                        URL</br>
                        <div style="height:18px;"></div>
                        Order
           </div>
            <div class="formright_bx"> 
            <input type="text" id="namebox'.$identificatore.'" name="Name" size="40" value="'.$row['Name'].'"/>
            <input type="text" id="langbox'.$identificatore.'" name="Name" size="40" value="'.$row['LangKey'].'"/>
            <input type="text" id="statusbox'.$identificatore.'" name="Name" size="40" value="'.$row['Status'].'"/>
            <textarea name="URL" id="urlbox'.$identificatore.'">'.$row['UrlDyn'].'</textarea>
            <input id="ordinebox'.$identificatore.'" type="text" name="Name" size="40" value="'.$row['Order'].'"/>
            <input id="destination'.$identificatore.'" type="hidden" name="Name" size="40" value="'.$row['Destination'].'"/>
            <input id="idbox'.$identificatore.'" type="hidden" name="Name" size="40" value="'.$row['ID'].'"/>
            </div>';
     echo '<div style="clear:both;"></div>';
	   echo '<div class="updateform" onclick="nascondi_blox('.$identificatore.');">Close</div>';
     echo '<div class="updateform" style="clear:none;" id="updatemod'.$identificatore.'" onclick="updatebox('.$identificatore.')">Update</div>';
	   echo '<div style="clear:both;"></div>';
     echo '</div>';
     echo '<div class="tutto"></div>';
     echo '</div>';
      }
      ?>  
    <div class="introtitle">Add Custom Links For the other page</div>
    <div class="bt_add" onclick="newitemfade(1);">New item</div>
	  <div class="tutto"></div>
	  <?php
	  $linkmyprofile="SELECT * FROM ibdw_mega_extralink WHERE Name<>'Simple Messenger' AND Destination=1 ORDER BY ibdw_mega_extralink.Order ASC";
	  $ottienilinkprofile=mysql_query($linkmyprofile) or die(mysql_error());
	  while($row = mysql_fetch_array($ottienilinkprofile))
      {
     $identificatore++; 
	   echo '<div class="bloccoriga'.$identificatore.'">';
     echo '<div class="bloxnascondi'.$identificatore.'"><div class="unsesto2" style="float: left; font-size: 13px; font-weight: bold; line-height: 26px; margin-top: 3px; margin-left: 26px; width: 44px;">Name</div><div class="unquarto2"><div class="infonamevoice" id="nameupdate'.$identificatore.'">'.$row['Name'].'</div></div>';
	   echo '<div class="btup" onclick="mostra_blox('.$identificatore.');">EDIT</div><div class="btup" onclick="delete_blox('.$identificatore.','.$row['ID'].');">DELETE</div></div>';
     echo '<div class="box_nascosto" id="blox_hid'.$identificatore.'">';
     echo '<div class="formleft_bx">
                        Name<br/>
                        Lang Key<br/>
                        Status</br>
                        <div style="height:18px;"></div>
                        URL</br>
                        <div style="height:18px;"></div>
                        Order
           </div>
            <div class="formright_bx"> 
            <input type="text" id="namebox'.$identificatore.'" name="Name" size="40" value="'.$row['Name'].'"/>
            <input type="text" id="langbox'.$identificatore.'" name="Name" size="40" value="'.$row['LangKey'].'"/>
            <input type="text" id="statusbox'.$identificatore.'" name="Name" size="40" value="'.$row['Status'].'"/>
            <textarea name="URL" id="urlbox'.$identificatore.'">'.$row['UrlDyn'].'</textarea>
            <input id="ordinebox'.$identificatore.'" type="text" name="Name" size="40" value="'.$row['Order'].'"/>
            <input id="destination'.$identificatore.'" type="hidden" name="Name" size="40" value="'.$row['Destination'].'"/>
            <input id="idbox'.$identificatore.'" type="hidden" name="Name" size="40" value="'.$row['ID'].'"/>
            </div>';
     echo '<div style="clear:both;"></div>';
	   echo '<div class="updateform" onclick="nascondi_blox('.$identificatore.');">Close</div>';
     echo '<div class="updateform" style="clear:none;" id="updatemod'.$identificatore.'" onclick="updatebox('.$identificatore.')">Update</div>';
	   echo '<div style="clear:both;"></div>';
     echo '</div>';
     echo '<div class="tutto"></div>';
     echo '</div>';
      }
      ?>
	  <script>
	   function updatebox(idblox){
	   $("#updatemod"+idblox).html("<img src='templates/uni/img/ajaxlo.gif'>");
     var namebox = $("#namebox"+idblox).val();
     var langbox = $("#langbox"+idblox).val();
     var statusbox = $("#statusbox"+idblox).val();
     var urlbox = $("#urlbox"+idblox).val();
     var ordinebox = $("#ordinebox"+idblox).val();
     var destination = $("#destination"+idblox).val();
     var idbox = $("#idbox"+idblox).val();
     $.ajax({
        type: "POST",
        url: "configurazione_rowparams.php",
        data: "namebox="+namebox+"&langbox="+langbox+"&statusbox="+statusbox+"&urlbox="+urlbox+"&ordinebox="+ordinebox+"&destination="+destination+"&idbox="+idbox,
        success: function(html){
            $("#nameupdate"+idblox).html(namebox);
            $("#updatemod"+idblox).html("Update");
        }
      });
     }
     function delete_blox(ident,idblox){
     $.ajax({
        type: "POST",
        url: "configurazione_rowparams.php",
        data: "action=delete&idblox="+idblox,
        success: function(html){
            $(".bloccoriga"+ident).fadeOut();
        }
      });
     }
	   function mostra_blox(idblox){
	    $(".bloxnascondi"+idblox).fadeOut(1); 
      $("#blox_hid"+idblox).slideDown();
     }
     function nascondi_blox(idblox){
      $(".bloxnascondi"+idblox).fadeIn(1);
      $("#blox_hid"+idblox).slideUp();
     }
    </script> 
	 </td>
	</tr>
 </table>
 
	
	
 
</div>
<center><input type="submit" value="Save configuration"></center></form>
<div class="newitemstyle">
	  <h2>New item</h2>
	       <div class="formleft_bx">
          Name<br/>
          Lang Key<br/>
          Status</br>
          <div style="height:18px;"></div>
          URL</br>
          <div style="height:18px;"></div>
          Order
         </div>
         <div class="formright_bx">
          <form method="POST" id="myform" action="configurazione_rowparams.php"> 
            <input type="text" name="name" value=""/>
            <input type="text" name="lang" value=""/>
            <input type="text" name="status" value=""/>
            <textarea name="url"></textarea>
            <input type="text" name="order">
            <input type="hidden" id="modedestination" name="destination" value="0"/>
            <input type="hidden" name="action" value="new"/>
         </div>
         <div class="clear"></div>
         <input type="submit" class="updateform" value="Add" />
         <div class="updateform" onclick="newitemfadeout();">Cancel</div>
         <div class="clear"></div>
         </form>
    </div>	
</div>
<div class="clear"></div>
</div>
</body>
</html>