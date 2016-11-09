<?php require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' ); ?>
<style>
.ibdw_photo_mainphotol {
    background-color: #D8DFEA;
    background-position: center center;
    background-repeat: no-repeat;
    border: 1px solid #94C2EC;
    float: left;
    height: 96px;
    margin: 6px;
    padding: 3px;
    width: 96px; }

.ibdw_photo_sysbordatural {
border:3px solid #FFFFFF;
height:118px;
margin:-2px; }

.ibdw_photo_mainphotol:hover {
border:1px solid #3B5998;
cursor:pointer; }
</style>
 <div id="closeblur" onclick="chiudiospite();"> </div>
<div id="mainalbumstyle">
<h2><?php echo _t("_ibdw_mp_selectalbum");?></h2>
<div class="linestyle"> </div>
<div id="overalbum">
<?php
mysql_query("SET NAMES 'utf8'");
$userid = (int)$_COOKIE['memberID'];   	
$versione="SELECT Name,sys_options.VALUE FROM sys_options WHERE Name='sys_tmp_version'";
$esegui=mysql_query($versione);
$riga=mysql_fetch_assoc($esegui);
$explode=explode('.',$riga['VALUE']);
$versionedolmain=$explode[1];
$versionedol=$explode[2];
if($versionedolmain=='0') 
{
 $nomeutente = getNickName($userid);
}
else
{
 $nomeutente = getUsername($userid);
} 
$nomepredefinitoalbum = "SELECT VALUE FROM sys_options WHERE Name='bx_photos_profile_album_name'";
$eseguinome = mysql_query($nomepredefinitoalbum) or die(mysql_error());
$nomee = mysql_fetch_array($eseguinome);
$nalbumm=$nomee['VALUE'];
$nomealbum = uriFilter(str_replace("{nickname}",$nomeutente,$nalbumm));




 
  $estrazione = "SELECT ID FROM sys_albums WHERE Owner = '$userid' AND Uri='$nomealbum'";
  $esegui = mysql_query($estrazione);
  $info = mysql_fetch_assoc($esegui);
  
  $idalbum = $info['ID'];
  
  $estrazione = "SELECT sys_albums_objects.id_object, sys_albums.Caption,sys_albums_objects.obj_order, bx_photos_main.ID , bx_photos_main.Hash,bx_photos_main.Ext  FROM 
                (sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID = sys_albums_objects.id_album)
               INNER join bx_photos_main ON bx_photos_main.ID=sys_albums_objects.id_object WHERE sys_albums.ID='$idalbum' 
               ORDER BY obj_order ASC";
  $esegui = mysql_query($estrazione);
    while($foto = mysql_fetch_array($esegui)) { 
    $idfoto = $foto['ID'];
    echo '<div onclick="updateprofilephoto(\''.$userid.'\',\''.$idfoto.'\');" class="ibdw_photo_mainphotol" style="background-image: url(&quot;m/photos/get_image/browse/'.$foto['Hash'].'.'.$foto['Ext'].'&quot;);">
          </div>';   }
  
  
?>
<script>
function updateprofilephoto(idutente,idfoto) { 
  $.ajax({
   type: 'POST',
   data: "idutente=" + idutente + "&idfoto=" + idfoto,
   url: 'modules/ibdw/megaprofile/sys_albumselect_go.php',
    success: function(html) {
    aggiornamentoajax_profile_cropexe();
    }
});
}
</script>
</div>
</div>