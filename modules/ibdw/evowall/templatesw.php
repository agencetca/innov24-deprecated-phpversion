<?php
if ($_GET['skin']<>"")
{
 if(file_exists(BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/templates/'.$_GET['skin'].'/css/evowallstyleUNI.css'))
 {
  $mytemplatepath='templates/'.$_GET['skin'].'/css/';
  $imagepath='modules/ibdw/evowall/templates/'.$_GET['skin'].'/css/immagini/';
 }
 else
 {
  $mytemplatepath='templates/uni/css/';
  $imagepath='modules/ibdw/evowall/templates/uni/css/immagini/';
 }
}
elseif (isset($_COOKIE['skin']))
{
 if(file_exists(BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/templates/'.$_COOKIE['skin'].'/css/evowallstyleUNI.css'))
 {
  $mytemplatepath='templates/'.$_COOKIE['skin'].'/css/';
  $imagepath='modules/ibdw/evowall/templates/'.$_COOKIE['skin'].'/css/immagini/';
 }
 else
 {
  $mytemplatepath='templates/uni/css/';
  $imagepath='modules/ibdw/evowall/templates/uni/css/immagini/';
 }
}
else
{
$myquerytemplate="SELECT VALUE from sys_options WHERE Name='template'";
$risultempl = mysql_query($myquerytemplate);
$estratempl = mysql_fetch_assoc($risultempl);
$mytemplatepath='templates/'.$estratempl['VALUE'].'/css/';
 if(file_exists(BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/'.$mytemplatepath.'evowallstyleUNI.css'))
 {
  $imagepath='modules/ibdw/evowall/templates/'.$estratempl['VALUE'].'/css/immagini/';
 }
 else
 {
  $mytemplatepath='templates/uni/css/';
  $imagepath='modules/ibdw/evowall/templates/uni/css/immagini/';
 }
}
?>
