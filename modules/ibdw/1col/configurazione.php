<?
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/1col/myconfig.php';
$userid = (int)$_COOKIE['memberID'];
if(!isAdmin()) { exit;}
mysql_query("SET NAMES 'utf8'");
$configquery= "SELECT * FROM `1col_config` LIMIT 0 , 1";
$resultac = mysql_query($configquery);
$rowconfig = mysql_fetch_assoc($resultac);  
?>
<style>
.semicolumn
{
 width:50%;
 float:left;
}
a
{
color:#000000;
text-decoration:none;
}
a:hover 
{
color:#FFFFFF;
text-decoration:none;
}
body
{
background:none repeat scroll 0 0 #334962;
font-family:Verdana;
font-size:11px;
margin:0;
text-align:center; 
}
#pagina
{
background:url("templates/base/images/spyconfiglogo.jpg") no-repeat scroll 35px 22px #283B51;
border:7px solid #FFFFFF;
color:#FFFFFF;
margin:20px auto 20px auto;
padding:20px;
width:900px;
height:auto;
}
#form_invio
{
float:left;
font-size:15px;
line-height:34px;
margin-left:201px;
margin-top:44px;
width:500px;
}
#form_conferma
{
float:left;
font-size:16px;
line-height:45px;
margin-left:225px;
margin-top:25px;
width:429px;
}
.title
{
font-size:27px;
text-transform:uppercase;
}
.dett_activ
{
color:#FFFFFF;
font-size:10px;
line-height:15px;
}
#introright
{
float:right;
text-align:right;
}
#notifica
{
color:#FFFFFF;
font-size:18px;
margin:135px;
}
#boxgeneraleconfigurazione
{
float:left;
margin-top:110px;
padding:5px;
text-align:left;
}
.introtitle
{
font-size:14px;
font-weight:bold;
line-height:23px;
}
.introdesc
{
color:Yellow;
font-size:11px;
font-style:italic;
line-height:13px;
}
#contentbox
{
border:2px solid #FFFFFF;
float:left;
font-size:10px;
line-height:11px;
margin:10px;
padding:10px;
width:398px;
background-color:#4682B4;
}
#return
{
border:1px solid #FFFFFF;
color:#FFFFFF;
float:right;
font-size:15px;
height:31px;
line-height:27px;
margin-right:15px;
margin-top:-105px;
padding:0 13px;
}
#return:hover
{
background:none repeat scroll 0 0 #999999;
}
#return a
{
color:#FFF;
}
.medios1
{
 float:left;
 width:50%;
 margin-top:4px;
}
.medios2 {
float:left;
margin-bottom:4px;
margin-top:0;
width:50%;
}
.unterzo {
float:left;
margin-top:4px;
width:30%;
}
.unquarto {
float:left;
padding-bottom:6px;
width:33%;
}
.unquarto2 {
float:left;
padding-bottom:6px;
width:23%;
}
.unquarto3 {
float:left;
padding-bottom:6px;
width:27%;
}
.unquarto4 {
float:left;
margin-top:4px;
padding-bottom:6px;
width:30%;
}
.unquarto5 {
float:left;
padding-bottom:6px;
width:30%;
}
.unsesto {
float:left;
margin-top:4px;
width:15%;
}
.dueterzi {
float:left;
margin-bottom:6px;
width:70%;
}
.contentcon {
border-bottom:1px solid #FFFFFF;
float:left;
margin:3px 2px 2px;
width:100%;
}
.spazio
{
float:left;
height:20px;
width:100%;
}
.conteineruri {
    float: left;
    margin: 10px;
}
.titolouri {
    float: left;
    padding: 6px 0;
}
.inputright
{
 float: right;
}
.ilinef {
    border-bottom: 1px solid;
    float: left;
    padding: 4px 0;
    width: 100%;
}
.inputright input {
    min-width: 260px;
}
</style>
<html>
<body>
<div id="pagina">
 <div id="boxgeneraleconfigurazione">
 <div id="return"><a href="../../../<?php echo $admin_dir;?>"><?php echo _t("_ibdw_1col_backadmin");?></a></div>
 <div class="semicolumn">
 
  <div id="contentbox">
   <span class="introtitle">Contents Box</span><br/> 
   <span class="introdesc">Enable/Disable the bottons of the modules installed not installed</span>
   <div class="spazio"></div>
	<form action="updateconfig.php" method="POST">
	 <div class="contentcon">
	  <div class="unsesto">Sites</div><div class="unquarto"><input type="radio" name="site" value="ON" <?php if($rowconfig['siti']=='ON') {echo 'checked';}?> />ON <input type="radio" name="site" value="OFF" <?php if($rowconfig['siti']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="unsesto">Ads</div><div class="unquarto"><input type="radio" name="ads" value="ON" <?php if($rowconfig['annunci']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="ads" value="OFF" <?php if($rowconfig['annunci']=='OFF') {echo 'checked';}?>/>OFF</div>
     </div>

	 <div class="contentcon">
	  <div class="unsesto">Groups</div><div class="unquarto"><input type="radio" name="group" value="ON" <?php if($rowconfig['gruppi']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="group" value="OFF" <?php if($rowconfig['gruppi']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="unsesto">Events</div><div class="unquarto"><input type="radio" name="event" value="ON" <?php if($rowconfig['eventi']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="event" value="OFF" <?php if($rowconfig['eventi']=='OFF') {echo 'checked';}?>/>OFF</div>
	 </div>
	 <div class="contentcon">
	  <div class="unsesto">Photos</div><div class="unquarto"><input type="radio" name="photo" value="ON" <?php if($rowconfig['foto']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="photo" value="OFF" <?php if($rowconfig['foto']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="unsesto">Polls</div><div class="unquarto"><input type="radio" name="poll" value="ON" <?php if($rowconfig['sondaggi']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="poll" value="OFF" <?php if($rowconfig['sondaggi']=='OFF') {echo 'checked';}?>/>OFF</div>
	 </div>
	 <div class="contentcon">
	  <div class="unsesto">Videos</div><div class="unquarto"><input type="radio" name="video" value="ON" <?php if($rowconfig['video']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="video" value="OFF" <?php if($rowconfig['video']=='OFF') {echo 'checked';}?>/>OFF</div>
    <div class="unsesto">Pages</div><div class="unquarto"><input type="radio" name="page" value="ON" <?php if($rowconfig['pagine']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="page" value="OFF" <?php if($rowconfig['pagine']=='OFF') {echo 'checked';}?>/>OFF</div>
	 </div>
	 	<div class="contentcon">
	  <div class="unsesto">Store</div><div class="unquarto"><input type="radio" name="storevar" value="ON" <?php if($rowconfig['storevar']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="storevar" value="OFF" <?php if($rowconfig['storevar']=='OFF') {echo 'checked';}?>/>OFF</div>
	  <div class="unsesto">Files</div><div class="unquarto"><input type="radio" name="file" value="ON" <?php if($rowconfig['file']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="file" value="OFF" <?php if($rowconfig['file']=='OFF') {echo 'checked';}?>/>OFF</div>
	 </div>
	 <div class="contentcon">
	  <div class="unsesto">Sounds</div><div class="unquarto"><input type="radio" name="sound" value="ON" <?php if($rowconfig['suoni']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="sound" value="OFF" <?php if($rowconfig['suoni']=='OFF') {echo 'checked';}?>/>OFF</div>
    <div class="unquarto"></div>
    <div class="unquarto"></div>
	 </div>
	 <div class="spazio"></div>
	 
   
   <span class="introtitle">Preferences</span><br/>
   <span class="introdesc">Choose what should be appear</span>
   <div class="spazio"></div>
   <div class="contentcon"> 
    
     <div class="unsesto">Show Email:</div>
     <div class="unquarto">
      <input type="radio" name="emailad" value="ON" <?php if($rowconfig['emailad']=='ON') {echo 'checked';}?>/>ON 
      <input type="radio" name="emailad" value="OFF" <?php if($rowconfig['emailad']=='OFF') {echo 'checked';}?>/>OFF       
     </div>
     <div class="unsesto">Display Status</div>
     <div class="unquarto">
      <input type="radio" name="status" value="ON" <?php if($rowconfig['status']=='ON') {echo 'checked';}?>/>ON
      <input type="radio" name="status" value="OFF" <?php if($rowconfig['status']=='OFF') {echo 'checked';}?>/>OFF
     </div>
     <div class="spazio"></div>
     <div class="unsesto">Show City:</div>
     <div class="unquarto">
      <input type="radio" name="city" value="ON" <?php if($rowconfig['city']=='ON') {echo 'checked';}?>/>ON 
      <input type="radio" name="city" value="OFF" <?php if($rowconfig['city']=='OFF') {echo 'checked';}?>/>OFF
     </div> 
     <div class="unsesto">Implode more link:</div>
     <div class="unquarto">
      <input type="radio" name="slide" value="ON" <?php if($rowconfig['slide']=='ON') {echo 'checked';}?>/>ON 
      <input type="radio" name="slide" value="OFF" <?php if($rowconfig['slide']=='OFF') {echo 'checked';}?>/>OFF
     </div>
     <div class="spazio"></div>          
     <div class="unsesto">Fade duration:</div>
     <div class="unquarto">
      <input type="text" name="slidevelocity" value="<?php echo $rowconfig['slidevelocity'];?>" size="4"/> (Milliseconds)
     </div>
     <div class="unsesto">Max num of online friends:</div>
     <div class="unquarto">
      <input type="text" name="numbermaxfriend" value="<?php echo $rowconfig['numbermaxfriend'];?>" size="3" />
     </div>
     <div class="spazio"></div>  
     <div class="unsesto">Time reload:</div>
     <div class="unquarto">
      <input type="text" name="timereload" value="<?php echo $rowconfig['timereload'];?>" size="6" />
     </div>
      
                
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
  
  </div>
  
  <div id="contentbox" style="line-height: 31px;">
   <span class="introtitle">Custom Link</span><br/>
   <span class="introdesc">Here you can add max five custom links that will be displayed in the "News Feed Section".<br/>
The links icon can be customized. To change the icons you can edit the files link1.png, link2.png, ecc... in the folder 1col/templates/base/images/icon/</span> <br/><br/>

      1 link. <input type="text" name="customlink_1" value="<?php echo $rowconfig['customlink1'];?>" /> <span> (Example: http://www.boonex.com)</span>  <br/>
      2 link. <input type="text" name="customlink_2" value="<?php echo $rowconfig['customlink2'];?>" />  <br/>
      3 link. <input type="text" name="customlink_3" value="<?php echo $rowconfig['customlink3'];?>" />  <br/>
      4 link. <input type="text" name="customlink_4" value="<?php echo $rowconfig['customlink4'];?>" />  <br/>
      5 link. <input type="text" name="customlink_5" value="<?php echo $rowconfig['customlink5'];?>" />  <br/>
      
  </div>
  
 <div id="contentbox" style="line-height: 31px;">
   <span class="introtitle">Custom Section</span><br/>
      <span class="introdesc">Here you can create a custom section to link to other modules (es. "Application")<br/>
		The links icon can be customized. To change the icons you can edit the files sect1.png, sect2.png, ecc... in the folder 1col/templates/base/images/icon/<br/>
		Also to customize the icon of the section you must edit the file sectname.png</span>
		<br/>
		<br/>
		
      1 link. <input type="text" name="customlinksect1" value="<?php echo $rowconfig['customsect1'];?>" />
		<input type="checkbox" name="Journalist1" value="ON" <?php if($rowconfig['Journalist1']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator1" value="ON" <?php if($rowconfig['Communicator1']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader1" value="ON" <?php if($rowconfig['Leader1']=='ON') {echo 'checked';}?> />L
		<br /><span>LKEY to modify : _ibdw_1col_customsect1</span><span> - </span><span>"<?php echo _t('_ibdw_1col_customsect1'); ?>"</span><br />
      2 link. <input type="text" name="customlinksect2" value="<?php echo $rowconfig['customsect2'];?>" />  
		<input type="checkbox" name="Journalist2" value="ON" <?php if($rowconfig['Journalist2']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator2" value="ON" <?php if($rowconfig['Communicator2']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader1" value="ON" <?php if($rowconfig['Leader2']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect2</span><br />
      3 link. <input type="text" name="customlinksect3" value="<?php echo $rowconfig['customsect3'];?>" />  
		<input type="checkbox" name="Journalist3" value="ON" <?php if($rowconfig['Journalist3']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator3" value="ON" <?php if($rowconfig['Communicator3']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader3" value="ON" <?php if($rowconfig['Leader3']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect3</span><br />
      4 link. <input type="text" name="customlinksect4" value="<?php echo $rowconfig['customsect4'];?>" />  
		<input type="checkbox" name="Journalist4" value="ON" <?php if($rowconfig['Journalist4']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator4" value="ON" <?php if($rowconfig['Communicator4']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader4" value="ON" <?php if($rowconfig['Leader4']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect4</span><br />
      5 link. <input type="text" name="customlinksect5" value="<?php echo $rowconfig['customsect5'];?>" />  
		<input type="checkbox" name="Journalist5" value="ON" <?php if($rowconfig['Journalist5']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator5" value="ON" <?php if($rowconfig['Communicator5']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader5" value="ON" <?php if($rowconfig['Leader5']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect5</span><br />
	  6 link. <input type="text" name="customlinksect6" value="<?php echo $rowconfig['customsect6'];?>" /> 
		<input type="checkbox" name="Journalist6" value="ON" <?php if($rowconfig['Journalist6']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator6" value="ON" <?php if($rowconfig['Communicator6']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader6" value="ON" <?php if($rowconfig['Leader6']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect6</span><br />
      7 link. <input type="text" name="customlinksect7" value="<?php echo $rowconfig['customsect7'];?>" />  
		<input type="checkbox" name="Journalist7" value="ON" <?php if($rowconfig['Journalist7']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator7" value="ON" <?php if($rowconfig['Communicator7']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader7" value="ON" <?php if($rowconfig['Leader7']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect7</span><br />
      8 link. <input type="text" name="customlinksect8" value="<?php echo $rowconfig['customsect8'];?>" />  
		<input type="checkbox" name="Journalist8" value="ON" <?php if($rowconfig['Journalist8']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator8" value="ON" <?php if($rowconfig['Communicator8']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader8" value="ON" <?php if($rowconfig['Leader8']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect8</span><br />
      9 link. <input type="text" name="customlinksect9" value="<?php echo $rowconfig['customsect9'];?>" />  
		<input type="checkbox" name="Journalist9" value="ON" <?php if($rowconfig['Journalist9']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator9" value="ON" <?php if($rowconfig['Communicator9']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader9" value="ON" <?php if($rowconfig['Leader9']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect9</span><br />
      10 link. <input type="text" name="customlinksect10" value="<?php echo $rowconfig['customsect10'];?>" />  
		<input type="checkbox" name="Journalist10" value="ON" <?php if($rowconfig['Journalist10']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator10" value="ON" <?php if($rowconfig['Communicator10']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader10" value="ON" <?php if($rowconfig['Leader10']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect10</span><br />
	  11 link. <input type="text" name="customlinksect11" value="<?php echo $rowconfig['customsect11'];?>" /> 
		<input type="checkbox" name="Journalist11" value="ON" <?php if($rowconfig['Journalist11']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator11" value="ON" <?php if($rowconfig['Communicator11']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader11" value="ON" <?php if($rowconfig['Leader11']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect11</span><br />
      12 link. <input type="text" name="customlinksect12" value="<?php echo $rowconfig['customsect12'];?>" />  
		<input type="checkbox" name="Journalist12" value="ON" <?php if($rowconfig['Journalist12']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator12" value="ON" <?php if($rowconfig['Communicator12']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader12" value="ON" <?php if($rowconfig['Leader12']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect12</span><br />
      13 link. <input type="text" name="customlinksect13" value="<?php echo $rowconfig['customsect13'];?>" />  
		<input type="checkbox" name="Journalist13" value="ON" <?php if($rowconfig['Journalist13']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator13" value="ON" <?php if($rowconfig['Communicator13']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader13" value="ON" <?php if($rowconfig['Leader13']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect13</span><br />
      14 link. <input type="text" name="customlinksect14" value="<?php echo $rowconfig['customsect14'];?>" />  
		<input type="checkbox" name="Journalist14" value="ON" <?php if($rowconfig['Journalist14']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator14" value="ON" <?php if($rowconfig['Communicator14']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader14" value="ON" <?php if($rowconfig['Leader14']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect14</span><br />
      15 link. <input type="text" name="customlinksect15" value="<?php echo $rowconfig['customsect15'];?>" />  
		<input type="checkbox" name="Journalist15" value="ON" <?php if($rowconfig['Journalist15']=='ON') {echo 'checked';}?> />J&nbsp;
		<input type="checkbox" name="Communicator15" value="ON" <?php if($rowconfig['Communicator15']=='ON') {echo 'checked';}?> />C&nbsp;
		<input type="checkbox" name="Leader15" value="ON" <?php if($rowconfig['Leader15']=='ON') {echo 'checked';}?> />L
	  <br /><span>language key : _ibdw_1col_customsect15</span><br />
  </div>

 </div>
 <div class="semicolumn"> 
  
  <div id="contentbox">
   
   <span class="introtitle">Display the main links section</span><br/>
    <input type="radio" name="mainmenuvar" value="ON" <?php if($rowconfig['mainmenuvar']=='ON') {echo 'checked';}?>/>ON
	 <input type="radio" name="mainmenuvar" value="OFF" <?php if($rowconfig['mainmenuvar']=='OFF') {echo 'checked';}?>/>OFF
    <br/><br/>
      <span class="introtitle">Display the media links section</span><br/>
    <input type="radio" name="mediavar" value="ON" <?php if($rowconfig['mediavar']=='ON') {echo 'checked';}?>/>ON
	<input type="radio" name="mediavar" value="OFF" <?php if($rowconfig['mediavar']=='OFF') {echo 'checked';}?>/>OFF

        <br/><br/>
         <span class="introtitle">Display the profile settings section</span><br/>
    <input type="radio" name="acceditvar" value="ON" <?php if($rowconfig['acceditvar']=='ON') {echo 'checked';}?>/>ON
	<input type="radio" name="acceditvar" value="OFF" <?php if($rowconfig['acceditvar']=='OFF') {echo 'checked';}?>/>OFF

         <br/><br/>
   <span class="introtitle">Display the online friends section</span><br/>
    <input type="radio" name="onlinefriendvar" value="ON" <?php if($rowconfig['onlinefriendvar']=='ON') {echo 'checked';}?>/>ON
	<input type="radio" name="onlinefriendvar" value="OFF" <?php if($rowconfig['onlinefriendvar']=='OFF') {echo 'checked';}?>/>OFF

         <br/><br/>
         
    <span class="introtitle">Show the subscriptions button</span><br/>
    <input type="radio" name="sottoscrizione" value="ON" <?php if($rowconfig['sottoscrizione']=='ON') {echo 'checked';}?>/>ON
	<input type="radio" name="sottoscrizione" value="OFF" <?php if($rowconfig['sottoscrizione']=='OFF') {echo 'checked';}?>/>OFF
	          <br/><br/>
	      <span class="introtitle">Show the privacy button</span><br/>
    <input type="radio" name="privasett" value="ON" <?php if($rowconfig['privasett']=='ON') {echo 'checked';}?>/>ON
	<input type="radio" name="privasett" value="OFF" <?php if($rowconfig['privasett']=='OFF') {echo 'checked';}?>/>OFF
	         <br/><br/>
	      <span class="introtitle">Show the avatar button</span><br/>
    <input type="radio" name="avaset" value="ON" <?php if($rowconfig['avaset']=='ON') {echo 'checked';}?>/>ON
	<input type="radio" name="avaset" value="OFF" <?php if($rowconfig['avaset']=='OFF') {echo 'checked';}?>/>OFF
	          <br/><br/>
	      <span class="introtitle">Show the friends button</span><br/>
    <input type="radio" name="amiciset" value="ON" <?php if($rowconfig['amiciset']=='ON') {echo 'checked';}?>/>ON
	<input type="radio" name="amiciset" value="OFF" <?php if($rowconfig['amiciset']=='OFF') {echo 'checked';}?>/>OFF
	          <br/><br/>
	      <span class="introtitle">Show the mail button</span><br/>
    <input type="radio" name="mailset" value="ON" <?php if($rowconfig['mailset']=='ON') {echo 'checked';}?>/>ON
	<input type="radio" name="mailset" value="OFF" <?php if($rowconfig['mailset']=='OFF') {echo 'checked';}?>/>OFF
	        <br/><br/>
	
      <span class="introtitle">Show the delete account button</span><br/>
    <input type="radio" name="deletebutton" value="ON" <?php if($rowconfig['deletebutton']=='ON') {echo 'checked';}?>/>ON
	<input type="radio" name="deletebutton" value="OFF" <?php if($rowconfig['deletebutton']=='OFF') {echo 'checked';}?>/>OFF
      <br/>
             <div class="spazio"></div>
             
        <span class="introtitle">Manage URL</span>
        
        <div class="conteineruri">
         <div class="ilinef"><div class="titolouri">Mail:</div><div class="inputright"><input type="text" name="mailurl" value="<?php echo $rowconfig['mailurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Store:</div><div class="inputright"><input type="text" name="storeurl" value="<?php echo $rowconfig['storeurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Store Services:</div><div class="inputright"><input type="text" name="storeserv" value="<?php echo $rowconfig['storeserv'];?>" size="26"></div></div>    
         <div class="ilinef"><div class="titolouri">Group:</div><div class="inputright"><input type="text" name="groupurl" value="<?php echo $rowconfig['groupurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Add group:</div><div class="inputright"><input type="text" name="addgroupurl" value="<?php echo $rowconfig['addgroupurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Event:</div><div class="inputright"><input type="text" name="eventurl" value="<?php echo $rowconfig['eventurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Add event:</div><div class="inputright"><input type="text" name="addeventurl" value="<?php echo $rowconfig['addeventurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Poll:</div><div class="inputright"><input type="text" name="pollurl" value="<?php echo $rowconfig['pollurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Add poll:</div><div class="inputright"><input type="text" name="addpollurl" value="<?php echo $rowconfig['addpollurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Ads:</div><div class="inputright"><input type="text" name="adsurl" value="<?php echo $rowconfig['adsurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Add ads:</div><div class="inputright"><input type="text" name="addadsurl" value="<?php echo $rowconfig['addadsurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Site:</div><div class="inputright"><input type="text" name="siteurl" value="<?php echo $rowconfig['siteurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Add site:</div><div class="inputright"><input type="text" name="addsiteurl" value="<?php echo $rowconfig['addsiteurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">File:</div><div class="inputright"><input type="text" name="fileurl" value="<?php echo $rowconfig['fileurl'];?>" size="26"></div></div> 
         <div class="ilinef"><div class="titolouri">Add file:</div><div class="inputright"><input type="text" name="addfileurl" value="<?php echo $rowconfig['addfileurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Pages:</div><div class="inputright"><input type="text" name="pageurl" value="<?php echo $rowconfig['pageurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Add page:</div><div class="inputright"><input type="text" name="addpageurl" value="<?php echo $rowconfig['addpageurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Photo:</div><div class="inputright"><input type="text" name="photourl" value="<?php echo $rowconfig['photourl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Video:</div><div class="inputright"><input type="text" name="videourl" value="<?php echo $rowconfig['videourl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Sound:</div><div class="inputright"><input type="text" name="soundurl" value="<?php echo $rowconfig['soundurl'];?>" size="26"></div></div>
         <div class="ilinef"><div class="titolouri">Avatar:</div><div class="inputright"><input type="text" name="avatarurl" value="<?php echo $rowconfig['avatarurl'];?>" size="26"></div></div>
        </div>
 </div>
</div>
</div>
<input type="submit" value="Save">
</form>
</div>
</body>
</html>