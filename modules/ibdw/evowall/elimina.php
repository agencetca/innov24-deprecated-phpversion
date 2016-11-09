<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');

$id=$_POST['id'];
$id_post = $_POST['idpost'];

$queryc="UPDATE bx_spy_data SET PostCommentsN=PostCommentsN-1 WHERE id=".$id_post;
$resultc = mysql_query($queryc);
  
$queryd="DELETE FROM commenti_spy_data WHERE id=".$id;
$resultqueryd=mysql_query($queryd);

$queryd2="DELETE FROM datacommenti WHERE IDCommento=".$rowquery[4];
$resultqueryd2=mysql_query($queryd2);

?>