<?
require_once( '../../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/mobilewall/classes/config.php';
$userid = (int)$_COOKIE['memberID'];
if(!isAdmin()) { exit;}
mysql_query("SET NAMES 'utf8'");
$configquery= "SELECT * FROM `mobilewall_config` LIMIT 0 , 1";
$resultac = mysql_query($configquery);
$rowconfig = mysql_fetch_assoc($resultac);  
?>
<style>
.semicolumn {
    float: left;
    width: 50%;
}
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
#pagina {
    background: url("../templates/base/images/mobile_config_logo.jpg") no-repeat scroll 19px 51px #283B51;
    border: 7px solid #FFFFFF;
    color: #FFFFFF;
    margin: 20px auto;
    padding: 20px;
    width: 900px;
}
#form_invio {
    float: left;
    font-size: 15px;
    line-height: 34px;
    margin-left: 201px;
    margin-top: 44px;
    width: 500px;
}
#form_conferma {
    float: left;
    font-size: 16px;
    line-height: 45px;
    margin-left: 225px;
    margin-top: 25px;
    width: 429px;
}
.title {
    font-size: 27px;
    text-transform: uppercase;
}
.dett_activ {
    color: #FFFFFF;
    font-size: 10px;
    line-height: 15px;
}
#introright {
    float: right;
    text-align: right;
}
#notifica {
    color: #FFFFFF;
    font-size: 18px;
    margin: 135px;
}
#boxgeneraleconfigurazione {
    float: left;
    margin-top: 240px;
    padding: 5px;
    text-align: left;
}
.introtitle {
    font-size: 14px;
    font-weight: bold;
    line-height: 23px;
}
.introdesc {
    color: Yellow;
    font-size: 11px;
    font-style: italic;
    line-height: 13px;
}
#contentbox {
    background-color: #4682B4;
    border: 2px solid #FFFFFF;
    float: left;
    font-size: 10px;
    line-height: 11px;
    margin: 10px;
    padding: 10px;
    width: 398px;
}
#return {
    border: 1px solid #FFFFFF;
    color: #FFFFFF;
    float: right;
    font-size: 15px;
    height: 31px;
    line-height: 27px;
    margin-right: 15px;
    margin-top: -227px;
    padding: 0 13px;
}
#return:hover {
    background: none repeat scroll 0 0 #999999;
}
#return a {
    color: #FFFFFF;
}
.medios1 {
    float: left;
    margin-top: 4px;
    width: 50%;
}
.medios2 {
    float: left;
    margin-bottom: 4px;
    margin-top: 0;
    width: 50%;
}
.unterzo {
    float: left;
    margin-top: 4px;
    width: 30%;
}
.unquarto {
    float: left;
    padding-bottom: 6px;
    width: 33%;
}
.unquarto2 {
    float: left;
    padding-bottom: 6px;
    width: 23%;
}
.unquarto3 {
    float: left;
    padding-bottom: 6px;
    width: 27%;
}
.unquarto4 {
    float: left;
    margin-top: 4px;
    padding-bottom: 6px;
    width: 30%;
}
.unquarto5 {
    float: left;
    padding-bottom: 6px;
    width: 30%;
}
.unsesto {
    float: left;
    margin-top: 4px;
    width: 17%;
}
.dueterzi {
    float: left;
    margin-bottom: 6px;
    width: 70%;
}
.contentcon {
    border-bottom: 1px solid #FFFFFF;
    float: left;
    margin: 3px 2px 2px;
    width: 100%;
}
.spazio {
    float: left;
    height: 20px;
    width: 100%; }
</style>
<html>
<body>
<div id="pagina">
 <div id="boxgeneraleconfigurazione">
 <div id="return"><a href="../../../../<?php echo $admin_dir;?>"><?php echo _t("_ibdw_mobilewall_backadmin");?></a></div>
 <div class="semicolumn">
  <div id="contentbox">
   <span class="introtitle">Contents Box Botton</span><br/>
   <span class="introdesc">Turn ON/OFF the bottons that you want to enable/disable into the content box</span>
   <div class="spazio"></div>
	<form action="<?php echo BX_DOL_URL_MODULES;?>ibdw/mobilewall/classes/updateconfig.php" method="POST">
	 <div class="contentcon">
	  <div class="unsesto">Photo</div><div class="unquarto"><input type="radio" name="photo" value="ON" <?php if($rowconfig['foto']=='ON') {echo 'checked';}?> />ON <input type="radio" name="photo" value="OFF" <?php if($rowconfig['foto']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="unsesto">Video</div><div class="unquarto"><input type="radio" name="video" value="ON" <?php if($rowconfig['video']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="video" value="OFF" <?php if($rowconfig['video']=='OFF') {echo 'checked';}?>/>OFF</div>
     </div>
	 <div class="contentcon">
	  <div class="unsesto">Group</div><div class="unquarto"><input type="radio" name="group" value="ON" <?php if($rowconfig['gruppi']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="group" value="OFF" <?php if($rowconfig['gruppi']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="unsesto">Event</div><div class="unquarto"><input type="radio" name="event" value="ON" <?php if($rowconfig['eventi']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="event" value="OFF" <?php if($rowconfig['eventi']=='OFF') {echo 'checked';}?>/>OFF</div>
	 </div>
	 <div class="contentcon">
	  <div class="unsesto">Site</div><div class="unquarto"><input type="radio" name="site" value="ON" <?php if($rowconfig['siti']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="site" value="OFF" <?php if($rowconfig['siti']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="unsesto">Poll</div><div class="unquarto"><input type="radio" name="poll" value="ON" <?php if($rowconfig['sondaggi']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="poll" value="OFF" <?php if($rowconfig['sondaggi']=='OFF') {echo 'checked';}?>/>OFF</div>
	 </div>
	 <div class="contentcon">
	  <div class="unsesto">Ads</div><div class="unquarto"><input type="radio" name="ads" value="ON" <?php if($rowconfig['annunci']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="ads" value="OFF" <?php if($rowconfig['annunci']=='OFF') {echo 'checked';}?>/>OFF</div><div class="unquarto"></div><div class="unquarto"></div>
	 </div>
   <div class="spazio"></div>
   <div class="contentcon">
	<div class="unquarto">Display into the news feed:</div><div class="unquarto">
    <select name="nickname">
	 <option value="0" <?php if($rowconfig['usernameformat']=='0') {echo 'selected="selected"';}?>>the nickname</option>
	 <option value="1" <?php if($rowconfig['usernameformat']=='1') {echo 'selected="selected"';}?>>the real name</option>
	 <option value="2" <?php if($rowconfig['usernameformat']=='2') {echo 'selected="selected"';}?>>the first name</option>
	</select>
    </div>
    </div>
    
<div class="contentcon">
	<div class="unquarto">Share button (Activate only if you installed <a href="http://www.boonex.com/m/IBDW_SpyWall_1_0_like_facebook_news_feed" TARGET='_blank' >IBDW SpyWall</a>)</div><div class="unquarto">
    <select name="shareact">
	     <option value="0" <?php if($rowconfig['shareact']=='0') {echo 'selected="selected"';}?>>NO</option>
	     <option value="1" <?php if($rowconfig['shareact']=='1') {echo 'selected="selected"';}?>>YES</option>
    </select>
    </div>
</div>
<div class="contentcon">
	<div class="unquarto">Like button (Activate only if you installed <a href="http://www.boonex.com/m/IBDW_SpyWall_1_0_like_facebook_news_feed" TARGET='_blank' >IBDW SpyWall</a>)</div><div class="unquarto">
    <select name="likeact">
	     <option value="0" <?php if($rowconfig['likeact']=='0') {echo 'selected="selected"';}?>>NO</option>
	     <option value="1" <?php if($rowconfig['likeact']=='1') {echo 'selected="selected"';}?>>YES</option>
    </select>
    </div>
</div>
<div class="contentcon">
	<div class="unquarto">Comment button (Activate only if you installed <a href="http://www.boonex.com/m/IBDW_SpyWall_1_0_like_facebook_news_feed" TARGET='_blank' >IBDW SpyWall</a>)</div><div class="unquarto">
    <select name="commentact">
	     <option value="0" <?php if($rowconfig['commentact']=='0') {echo 'selected="selected"';}?>>NO</option>
	     <option value="1" <?php if($rowconfig['commentact']=='1') {echo 'selected="selected"';}?>>YES</option>
    </select>
    </div>
</div>

  </div> 
  
</div>
 <div class="semicolumn"> 
  
  <div id="contentbox">
   <span class="introtitle">News Feed</span><br/>
   <span class="introdesc">Set the news feed number to display at time</span><br/><br/>News number: <input name="limite" type="text" value="<?php echo $rowconfig['limite']; ?>"><br/><br/>
   <span class="introdesc">Display options: enable/disable to show</span><br/><br/>
   <div class="contentcon"><div class="unterzo">Profile viewed by:</div><div class="dueterzi">
    <input type="radio" name="viewpro" value="ON" <?php if($rowconfig['spywallprofileview']=='ON') {echo 'checked';}?>/>ON
	<input type="radio" name="viewpro" value="OFF" <?php if($rowconfig['spywallprofileview']=='OFF') {echo 'checked';}?>/>OFF
   </div></div>
   <div class="contentcon"><div class="unterzo">Profile's updates:</div><div class="dueterzi">
    <input type="radio" name="uppro" value="ON" <?php if($rowconfig['profileupdate']=='ON') {echo 'checked';}?>/>ON
	<input type="radio" name="uppro" value="OFF" <?php if($rowconfig['profileupdate']=='OFF') {echo 'checked';}?>/>OFF
   </div></div>
   <div class="spazio"></div>
  </div>
  
  <div id="contentbox">
   <span class="introtitle">Time Setting</span><br/>
   <span class="introdesc">Choose the date format and, only if necessary, set the time difference between the MySQL and the PHP servers (for 1 hour difference you must set 3600)</span><br/><br/>
   <div class="contentcon">
    <div class="unquarto4">Date format:</div><div class="unquarto5"><input type="radio" name="datava" value="d/m/Y H:i:s" <?php if($rowconfig['formatodata']=='d/m/Y H:i:s') {echo 'checked';}?>/>dd/mm/yyyy</div>
	<div class="unquarto5"><input type="radio" name="datava" value="m/d/Y H:i:s" <?php if($rowconfig['formatodata']=='m/d/Y H:i:s') {echo 'checked';} ?>/>mm/dd/yyyy</div>
   </div>
	Offset: 
	<input name="offset" type="text" value="<?php echo $rowconfig['offset'];?>">
  </div>
 </div>
 
 </div>
 <input type="submit" value="Save"></form>
</div>
</body>
</html>