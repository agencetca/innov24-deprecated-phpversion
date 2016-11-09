<?php
//IMPORTS
require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
//include 'MaConfig.php';
//VARS
$userid = (int)$_COOKIE['memberID'];
//REQUETE
mysql_query("SET NAMES 'utf8'");
$sql_PageAjax= "SELECT * FROM `Profiles` WHERE ID = ".$userid."";
$resultatAjax = mysql_query($sql_PageAjax);
$rowAjax = mysql_fetch_assoc($resultatAjax);
///TEST
$sql_PageAjax2=  "SELECT * FROM sys_pre_values WHERE sys_pre_values.Key='Country'";
$resultatAjax2 = mysql_query($sql_PageAjax2) or die ("Impossible de créer l'enregistrement :" . mysql_error());
///FIN
$sql_PageAjax3=  "SELECT * FROM sys_pre_values,Profiles WHERE sys_pre_values.Value='".$rowAjax['Country']."' AND Profiles.ID=".$userid."";
$resultatAjax3 = mysql_query($sql_PageAjax3) or die ("Impossible de créer l'enregistrement :" . mysql_error());
$rowAjax3 = mysql_fetch_assoc($resultatAjax3);
?>
<html>
<head>
</head>
<body>
<!--HEADLINE-->
<div id="Headline">
<form action="Tooltip/UpdateHeadline.php" method="POST">
<div>Headline:</div><br/> 
<div><input type="text" name="Headline" value="<?php echo $rowAjax['Headline'];?>" size="26"></div><br/>
<div><input type="submit" value="Update"></div>
</form>
</div>
<!--COUNTRY/CITY-->
<div id="CountryCity">
<form action="Tooltip/UpdateCountryCity.php" method="POST">
<div>Country:</div><br />
<select name="Country">
<option selected value="<?php echo $rowAjax3['Value'];?>"><?php echo _t($rowAjax3['LKey']);?></option>
<?php
while ($rowAjax2 = mysql_fetch_assoc($resultatAjax2)){
if($rowAjax2['Value']!=$rowAjax3['Value']){
?>
<option value="<?php echo $rowAjax2['Value'];?>">
<?php echo _t($rowAjax2['LKey']);}?>
</option>
<?php
}
?>
</select>
<div>City:</div><br/> 
<div><input type="text" name="City" value="<?php echo $rowAjax['City'];?>" size="26"></div><br/>
<div><input type="submit" value="Update"></div>
</form>
</div>
<!--COMPANY-->
<div id="Company">
<form action="Tooltip/UpdateCompany.php" method="POST">
<div>Company:</div><br/> 
<div><input type="text" name="Company" value="<?php echo $rowAjax['Company'];?>" size="26"></div><br/>
<div><input type="submit" value="Update"></div>
</form>
</div>
<!--EMAIL-->
<div id="Email">
<form action="Tooltip/UpdateEmail.php" method="POST">
<div>E-Mail:</div><br/> 
<div><input type="text" name="Email" value="<?php echo $rowAjax['Email'];?>" size="26"></div><br/>
<div><input type="submit" value="Update"></div>
</form>
</div>
<!--PHONE-->
<div id="Phone">
<form action="Tooltip/UpdatePhone.php" method="POST">
<div>Phone Number:</div><br/> 
<div><input type="text" name="phone_number" value="<?php echo $rowAjax['phone_number'];?>" size="26"></div><br/>
<div><input type="submit" value="Update"></div>
</form>
</div>
<!--MOBILE-->
<div id="Mobile">
<form action="Tooltip/UpdateMobile.php" method="POST">
<div>Mobile Number:</div><br/> 
<div><input type="text" name="mobile_number" value="<?php echo $rowAjax['mobile_number'];?>" size="26"></div><br/>
<div><input type="submit" value="Update"></div>
</form>
</div>
</body>
</html>