<?php
  include('../../../../inc/header.inc.php');
  mysql_query("SET NAMES 'utf8'");
  $tipologia = $_POST['tipologia'];
  $id_azione = $_POST['id']; 
  if($tipologia == 'commento_azione')
  { 
   //il valore post di ID tramesso dalla funzione invio_form() che si trova in funzioni.php ha serializzato le informazioni IDcommento / ID_user / Commento text
   $id_azione = explode("**", $id_azione);
   $IDcommento = $id_azione[0];
   $ID_user = $id_azione[1];
   $Commentotext = $id_azione[2];
   $commentovuoto = trim($id_azione[2]);
   if($commentovuoto=='') { exit();}
   $query  = "INSERT INTO commenti_spy_data (data,user,commento) VALUES ('".$IDcommento."', '".$ID_user."', '".$Commentotext."')";
   $result = mysql_query($query) or die(mysql_error());
   $pippo=mysql_insert_id(); 
   $query2  = "INSERT INTO datacommenti (IDCommento) VALUES ('".$pippo."')";
   $result2 = mysql_query($query2);
 }
?>