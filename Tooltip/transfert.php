<?php
//IMPORTS
require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
//include 'MaConfig.php';
//VARS
/*************************************************
Securite Boonex
/*************************************************/
$userid = getID($_GET['ID']);
check_logged($userid);
/************************************************/
$url=$_GET['url'];
$nick=basename($url);
//REQUETES
//mysql_query("SET NAMES 'utf8'");
$sql_PageAjax= "SELECT * FROM Profiles WHERE NickName = '".$nick."'";
$resultatAjax = mysql_query($sql_PageAjax) or die ("Impossible de créer l'enregistrement :" . mysql_error());
$rowAjax = mysql_fetch_assoc($resultatAjax);

$sql_PageAjax_pages_lien= "SELECT id,uri,author_id FROM ml_pages_main";
$resultatAjax_pages_lien = mysql_query($sql_PageAjax_pages_lien) or die ("Impossible de créer l'enregistrement :" . mysql_error());
$rowAjax_pages_lien = mysql_fetch_assoc($resultatAjax_pages_lien);

$nickdata = $rowAjax['ID'];
$fonction = $rowAjax['Headline'];
$profiltype = $rowAjax['ProfileType'];
$country = _t($GLOBALS['aPreValues']['Country'][$rowAjax['Country']]['LKey']);
$flag = genFlag($rowAjax['Country']);
$city = $rowAjax['City'];
$sex = $rowAjax['Sex'];
$nickname = $rowAjax['NickName'];
$mail = $rowAjax['Email'];
// $phone = $rowAjax['phone_number'];
if ($rowAjax['phone_number']==0){
$phone = _t(_tool_nc);
}else{
$phone = $rowAjax['phone_number'];
}

// $mobile= $rowAjax['mobile_number'];
if ($rowAjax['mobile_number']==0){
$mobile= _t(_tool_nc);
}else{
$mobile= $rowAjax['mobile_number'];
}
$company = $rowAjax['Company'];
$lien_company= $rowAjax_pages_lien['uri'];
//echo $nickdata.'&nbsp; est &nbsp;'.$fonction.'son ID est : &nbsp;'.$userid;


switch($profiltype){
case 2: $profiltype=_t(_tool_journalist);
break;
case 4: $profiltype=_t(_tool_communicator);
break;
case 8: $profiltype=_t(_tool_leader);
}
//--------------GAUCHE----------------------------
echo '<div class="left">';
echo '<div class="FimgFonction"><div id="profiletype" class="Fcontenu">'.$profiltype.'</div></div>';

if($nickdata==$userid){
//------------TOOLS DE GAUCHE(Mon profil)---------------
if($city!=null){
echo '<div id="tool1">
		<a class="tooltip" href="Tooltip/PageAjax.php#CountryCity" onmouseover="tooltip.ajax(this,\'Tooltip/PageAjax.php#CountryCity\');" onclick="return false;">
			<div class="Fimgtool1">
			<div class="FcontenuPays">'.$flag.'&nbsp'.$country.',&nbsp'.$city.'</div>
		</a>
	  </div></div>';}
else{
echo '<div id="tool1">
		<a class="tooltip" href="Tooltip/PageAjax.php#CountryCity" onmouseover="tooltip.ajax(this,\'Tooltip/PageAjax.php#CountryCity\');" onclick="return false;">
			<div class="Fimgtool1">
			<div class="FcontenuPays">'.$flag.'&nbsp'.$country.'</div>
		</a>
	  </div></div>';
}
if($fonction!=null){
echo '<div id="tool2">
		<a class="tooltip" href="Tooltip/PageAjax.php#Headline" onmouseover="tooltip.ajax(this,\'Tooltip/PageAjax.php#Headline\');" onclick="return false;">
			<div class="FimgJob">
				<div class="Fcontenu">'.$fonction.'</div>
		</a>
			</div>
	  </div>';}
else{
	  echo '<div id="tool2">
		<a class="tooltip" href="Tooltip/PageAjax.php#Headline" onmouseover="tooltip.ajax(this,\'Tooltip/PageAjax.php#Headline\');" onclick="return false;">
			<div class="FimgJob">
				<div class="Fcontenu">'._t(_tool_nc).'</div>
		</a>
			</div>
	  </div>';}
if($company!=null){
echo '<div id="tool3">
		<a class="tooltip" href="Tooltip/PageAjax.php#Company" onmouseover="tooltip.ajax(this,\'Tooltip/PageAjax.php#Company\');" onmouseclick="redirect();" onclick="return false;">
			<div class="FimgCompany">
				<div class="Fcontenu">'.$company.'</div>
		</a>
			</div>
	  </div>';}
else{
echo '<div id="tool3">
		<a class="tooltip" href="Tooltip/PageAjax.php#Company" onmouseover="tooltip.ajax(this,\'Tooltip/PageAjax.php#Company\');" onmouseclick="redirect();" onclick="return false;">
			<div class="FimgCompany">
				<div class="Fcontenu">'._t(_tool_nc).'</div>
		</a>
			</div>
	  </div>';}
//-----------------FIN--------------
}
else{
//---------AFFICHAGES SIMPLES GAUCHE-----------
if($city!=null){
echo '<div id="Fimgtool1"><div id="country">'.$flag.'&nbsp'.$country.',&nbsp'.$city.'</div></div>';
}
else{
echo '<div id="Fimgtool1"><div id="country">'.$flag.'&nbsp'.$country.'</div></div>';
}
if($fonction!=null){
echo '<div class="FimgJob"><div id="headline" class="Fcontenu">'.$fonction.'</div></div>';
}
else{
echo '<div class="FimgJob"><div id="headline" class="Fcontenu">'._t(_tool_nc).'</div></div>';
}
if($company!=null){
echo '<div class="FimgCompany"><div id="company" class="Fcontenu">'.$company.'</div></div>';
}
else{
echo '<div class="FimgCompany"><div id="company" class="Fcontenu">'._t(_tool_nc).'</div></div>';
}

//echo '<div class="Fimgtool3"><div id="company" class="Fcontenu">'.$company.'</div></div>';
//-----------fin------------
}
echo '</div>';
//-----------------------DROITE------------------------
echo '<div class="right">';
if($rowAjax['Sex']=="male"){
echo '<div class="FimgMale"><div id="sexname" class="Fcontenu">&nbsp'.$nickname.'</div></div>';
}
else{
echo '<div class="FimgFemale"><div id="sexname" class="Fcontenu">&nbsp'.$nickname.'</div></div>';
}
if($nickdata==$userid){
//TOOLS DROITE
if($mail!=null){
echo '<div id="tool4">
		<a class="tooltip" href="Tooltip/PageAjax.php#Email" onmouseover="tooltip.ajax(this,\'Tooltip/PageAjax.php#Email\');" onclick="return false;">
			<div class="FimgEmail">
				<div class="Fcontenu">'.$mail.'</div>
		</a>
			</div>
	  </div>';}
else{
echo '<div id="tool4">
		<a class="tooltip" href="Tooltip/PageAjax.php#Email" onmouseover="tooltip.ajax(this,\'Tooltip/PageAjax.php#Email\');" onclick="return false;">
			<div class="FimgEmail">
				<div class="Fcontenu">'._t(_tool_nc).'</div>
		</a>
			</div>
	  </div>';}

echo '<div id="tool5">
		<a class="tooltip" href="Tooltip/PageAjax.php#Phone" onmouseover="tooltip.ajax(this,\'Tooltip/PageAjax.php#Phone\');" onclick="return false;">
			<div class="FimgPhone">
				<div class="Fcontenu">'.$phone.'</div>
		</a>
			</div>
	  </div>';
echo '<div id="tool6">
		<a class="tooltip" href="Tooltip/PageAjax.php#Mobile" onmouseover="tooltip.ajax(this,\'Tooltip/PageAjax.php#Mobile\');" onclick="return false;">
			<div class="FimgMobile">
				<div class="Fcontenu">'.$mobile.'</div>
		</a>
			</div>
	  </div>';
}
else{
//SIMPLE DROITE
if($mail!=null){
echo '<div class="FimgEmail"><div id="email" class="Fcontenu">&nbsp'.$mail.'</div></div>';
}
else{
echo '<div class="FimgEmail"><div id="email" class="Fcontenu">&nbsp'._t(_tool_nc).'</div></div>';
}
echo '<div class="FimgPhone"><div id="phone" class="Fcontenu">&nbsp'.$phone.'</div></div>';
echo '<div class="FimgMobile"><div id="mobile" class="Fcontenu">&nbsp'.$mobile.'</div></div>';
}
//Fin droite
echo'</div>';
?>

<script type="text/javascript">
function redirect(){
window.location = "http://www.yoururl.com";
}
</script>