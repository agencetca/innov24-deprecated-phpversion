<script>
 function 
    condivisionegenerale(assegnazione,sender,recipient,lang,paramsurl,paramscaption,paramsimg,paramsdesc,paramsprezzo,paramsext,paramstitle,
                          paramsindirizzo,paramsidfoto,youtube,paramsidvideo,paramsindi) 
      {$.ajax({type: "POST", 
               url: "<?php echo $urlsite;?>modules/ibdw/mobilewall/classes/condivisione.php", 
               data: "sender=" + sender + "&recipient=" + recipient + "&lang=" + lang + "&paramsurl=" + paramsurl + "&paramscaption=" + paramscaption + 
               "&paramsimg=" + paramsimg + "&paramsdesc=" + paramsdesc + "&paramsprezzo=" + paramsprezzo + "&paramsext=" + paramsext + 
               "&paramstitle=" + paramstitle + "&paramsindirizzo=" + paramsindirizzo + "&paramsidfoto=" + paramsidfoto + "&youtube=" + youtube + 
               "&paramsidvideo=" + paramsidvideo + "&paramsindi=" + paramsindi + "&idnotizia=" + assegnazione,
                success: 
                    function(data){
                          window.location.reload();
                        }
                });
       }
</script>
 <div id="test"></div>
<?php  
function estrai_foto($idutente,$parametro,$tipologia) { 
if($tipologia == 'foto') { $query="SELECT * FROM bx_photos_main WHERE Owner='".$idutente."' AND Uri='".$parametro."'"; }
elseif($tipologia == 'video') { $query="SELECT ID,Title,Description,Owner,Source,Video,Uri,Status FROM RayVideoFiles WHERE Owner='".$idutente."' AND Uri='".$parametro."'"; }
$result = mysql_query($query);
$row = mysql_fetch_assoc($result);
if($tipologia == 'foto') { return $row['Hash']; }
elseif($tipologia == 'video') { return $row['Video']; }
}

function elimina($ident,$user_id){ 
include('urlsite.php');
return '<div id="bloccoelimina" class="addcommento" onclick="javascript:elimina('.$ident.','.$user_id.')"><img src="'.$urlsite.'modules/ibdw/mobilewall/templates/uni/images/trash.png"> <p>'._t("_ibdw_mobilewall_delete").'</p></div>';
}

function elimina_commento($ident){ 
include('urlsite.php');
return '<div class="elimina_style_commento" onclick="javascript:elimina_commento('.$ident.')">'._t("_ibdw_mobilewall_delete").'</div>';
}

function commento($ident,$user_id){  
 include('config.php');
 //verifica installazione di photodeluxe
 $verificaphotodeluxe = "SELECT uri FROM sys_modules WHERE uri = 'photo_deluxe'";
 $eseguiverificaphotodeluxe = mysql_query($verificaphotodeluxe);
 $numerophotodeluxe = mysql_num_rows($eseguiverificaphotodeluxe);
 if($numerophotodeluxe != 0) { $photodeluxe = 1; }
 
 if($photodeluxe == 1) { 
 //verifichiamo se è abilitata l'integrazione tra i moduli 
 $integrazionepdx = "SELECT integrazionespywall FROM photodeluxe_config WHERE ind = 1";
 $eseguiintregazionepdx = mysql_query($integrazionepdx);
 $rowintegrazionepdx = mysql_fetch_assoc($eseguiintregazionepdx);
 $attivaintegrazione = $rowintegrazionepdx['integrazionespywall']; 
   }
   include('urlsite.php');
   $rowquerylikeconta = 0;
   if($likefunny !=0) { 
   if($attivaintegrazione == 1) {
      $querylike= "SELECT id_utente FROM ibdw_likethis WHERE id_notizia=".$ident." AND typelement != 'phunsign'";}
   else { 
      $querylike= "SELECT id_utente FROM ibdw_likethis WHERE id_notizia=".$ident;
   }
   $querylikeresult= mysql_query($querylike);
   $rowquerylikeconta= mysql_num_rows($querylikeresult);  }
   $eliminacommento = 0; 
   $querydelcommento="SELECT commenti_spy_data.*,datacommenti.date, Profiles.ID, Profiles.NickName, Profiles.Avatar 
   FROM (commenti_spy_data LEFT JOIN datacommenti ON commenti_spy_data.id=datacommenti.IDCommento) INNER JOIN 
   Profiles ON commenti_spy_data.user = Profiles.ID WHERE data=$ident 
   ORDER BY commenti_spy_data.id DESC LIMIT 0,20";
   $resultdelcommento = mysql_query($querydelcommento);
   $numerazione = mysql_num_rows($resultdelcommento);
   $cmnt = '<div id="fumettoazione"> 
              ';if($numerazione !=0) { $cmnt = $cmnt.'<div class="fum_commenti" onclick="slideElement(\'#commento'.$ident.'\')">'.$numerazione.' '._t("_ibdw_mobilewall_cmnt").' </div>';} 
              if($rowquerylikeconta !=0 AND $likefunny !=0) { $cmnt = $cmnt.'<div class="fum_like" onclick="slideElement(\'.listalike'.$ident.'\')">'._t("_ibdw_mobilewall_likes").': '.$rowquerylikeconta.' '._t("_ibdw_mobilewall_likes2").'</div>';}
              $cmnt = $cmnt.'<div class="clear"></div>
            </div>';
   if($rowquerylikeconta != 0 AND $likefunny !=0) { 
    $cmnt = $cmnt.'<div id="listalike" class="listalike'.$ident.'">';
    $cmnt = $cmnt.'<img src="'.$urlsite.'modules/ibdw/mobilewall/templates/uni/images/noty.png" />';
    $cmnt = $cmnt.' '._t("_ibdw_mobilewall_likes").' '; 
    while($rowquerylike = mysql_fetch_array($querylikeresult)) {
    $id_utente_like = $rowquerylike['id_utente'];
    $nick_like = estrainick($id_utente_like,$usernameformat);
    $userprofile = estrainick($id_utente_like,0);   
    $nick_like = '<a href="bxprofile:'.$userprofile.'">'.$nick_like.'</a> ';
    $cmnt = $cmnt.$nick_like;
    }
    $cmnt = $cmnt.'</div>';
    }
   $cmnt = $cmnt.' 
   <div id="commento'.$ident.'" class="commentonascosto">';
   while($rowdelcommento = mysql_fetch_array($resultdelcommento))
   { 
     $proprietariosx = "SELECT bx_spy_data.id,sender_id FROM bx_spy_data WHERE bx_spy_data.id = '$ident' LIMIT 0 , 1";
     $exe_propsx = mysql_query($proprietariosx);
     $assoc_prop = mysql_fetch_assoc($exe_propsx);
     $proprietarioevento = $assoc_prop['sender_id'];
        if ($rowdelcommento[ID]==$user_id OR $proprietarioevento == $user_id) { $eliminacommento = 1;}
     
     $cmn=$rowdelcommento['commento'];
     $idcommento = $rowdelcommento['id'];
     $proprietario = $rowdelcommento['user'];
		 $cmn = strip_tags($cmn);
		 $cmn=str_replace("`", "'", $cmn);
		 $miadatac=TempoPost($rowdelcommento['date'],$seldate,$offset);
		 
		 $avatar = avatar($proprietario);
		 $nickname = estrainick($proprietario,$usernameformat);
		 $userprofile = estrainick($proprietario,0);    
		 
		 $cmnt = $cmnt.'<div id="single_comment" class="commento'.$rowdelcommento['id'].'">'.$avatar.'
                      <h2><a href="bxprofile:'.$userprofile.'">'.$nickname.'</a></h2>
                      <p>'.$cmn.'</p>
                      <span>'.$miadatac.'</span>
                      <div class="clear"></div>'; if($user_id == $proprietario) { $cmnt = $cmnt.elimina_commento($idcommento); }
                     $cmnt = $cmnt.'</div>';
   } 
   $grafica = '</div>';
   $graficas = $cmnt.$grafica;
   return $graficas; 
  }
  
  function inserisci_commento($ident,$user_id) { 
    include('urlsite.php');
    $serializzaid = $ident;
    $inserimento = '
    <div class="addcommento" id="intro_'.$ident.'" onclick="javascript:fadeElement(\'#comm_'.$ident.'\')"><img src="'.$urlsite.'modules/ibdw/mobilewall/templates/uni/images/cm.png"> <p>'._t("_ibdw_mobilewall_insertcmnt").'</p></div>';
    return $inserimento;
  }
  
  function inserisci_commento_sub($ident,$user_id) { 
    include('urlsite.php');
    $serializzaid = $ident;
    $inserimento = '
    <div class="inserimento_commento" id="comm_'.$ident.'">
    <textarea class="commento_inserimento" id="commento_'.$ident.'"></textarea> 
    <div class="bt_sub" onclick="invio_form(\'commento_azione\',\''.$serializzaid.'\');"> '._t("_ibdw_mobilewall_send").' </div>
    <div class="bt_sub" onclick="javascript:fadeElementOut(\'#comm_'.$ident.'\')"> '._t("_ibdw_mobilewall_close").' </div>
    </div>';
    return $inserimento;
  }
  
  function like_action($ident,$user_id) { 
    include('urlsite.php');
    $controllo="SELECT * FROM ibdw_likethis WHERE id_notizia='$ident' AND id_utente='$user_id'";
    $esegui=mysql_query($controllo);
    $numeri=mysql_num_rows($esegui);
    if($numeri != 0) {
    $inserimento = '<div class="addcommento" id="like_'.$ident.'" onclick="likethis('.$ident.','.$user_id.',0)"><img src="'.$urlsite.'modules/ibdw/mobilewall/templates/uni/images/unlk.png"> <p>'._t("_ibdw_mobilewall_unlike").'</p></div>';
    }
    else { 
    $inserimento = '<div class="addcommento" id="like_'.$ident.'" onclick="likethis('.$ident.','.$user_id.',1)"><img src="'.$urlsite.'modules/ibdw/mobilewall/templates/uni/images/lk.png"> <p>'._t("_ibdw_mobilewall_likes").'</p></div>';
    }
    return $inserimento;
  }
    
  function stampa_azione($cd,$at,$txtr,$in2,$dt,$iduser,$sid,$share) 
  { 
    include('urlsite.php');
    include('config.php');
    $taskelimina = 0;
    $shareexplode = 0;
    if($iduser == $sid) { $taskelimina = 1;}
	else {$taskspam=1;}
    if($sharefunny == 0) { $share='';}  
    if($share!=''){ $shareexplode = 1;}
    if($likefunny == 0) { $like='';}  
    if($like!=''){ $likeexplode = 1;}
    $userprofile = estrainick($sid,0);              
    $generatesto = '<h3><a href="bxprofile:'.$userprofile.'">'.$txtr.'</a></h3>: <span>'.strip_tags($in2,'<img><div><a>').'</span> <p>'.$dt.'</p>';
    $parteintroduttiva='
        <div class="azione" id="act_'.$cd.'">
          <div class="mainheader">
            <div class="avatarleft">'.$at.'</div>
            <div class="textright">'.$generatesto.'</div>
            <div class="clear"></div>
            <div class="main_index">
              <div class="clear"></div> 
            </div>
          </div>
          <div id="azionilaterali" class="bt_azione_'.$cd.'"';
          
          if($sharefunny == 0 OR $shareexplode == 0) { 
          if($taskelimina==1){$parteintroduttiva = $parteintroduttiva.' onclick="fadeAzioni_dlt(\''.$cd.'\')"';}
          else{$parteintroduttiva = $parteintroduttiva.' onclick="fadeAzioni(\''.$cd.'\')"';} 
          }
          
          else { 
          if($taskelimina==1){$parteintroduttiva = $parteintroduttiva.' onclick="fadeAzioni_dlt_sh(\''.$cd.'\')"';}
          else{$parteintroduttiva = $parteintroduttiva.' onclick="fadeAzioni_sh(\''.$cd.'\')"';} 
          }
          
          
          $parteintroduttiva = $parteintroduttiva.'></div>
          <div id="azionilaterali_action'.$cd.'" class="azionilaterali_action">
          <div class="contmain">';if($commentfunny==1){$parteintroduttiva = $parteintroduttiva.inserisci_commento($cd,$iduser);} if($likefunny == 1){$parteintroduttiva = $parteintroduttiva.like_action($cd,$iduser);} 
          $parteintroduttiva = $parteintroduttiva.$share;
		  
		  if($taskspam == 1) 
          {
		   //$cd is the id of the post
		   //$iduser is the id of the author of the spam
		   //$sid is the id of the report's sender
		   $parteintroduttiva = $parteintroduttiva.'<div class="addcommento" id="spam_'.$cd.'" onclick="spam_action('.$cd.','.$iduser.','.$sid.','.$usernameformat.');"><img src="'.$urlsite.'modules/ibdw/mobilewall/templates/uni/images/spam.png"><p>'._t("_Spam report").'</p></div>';
		  }
          if($taskelimina == 1) 
          { $parteintroduttiva = $parteintroduttiva.elimina($cd,$iduser); } $parteintroduttiva = $parteintroduttiva.'
          <div class="addcommento" onclick="fadeOutAzioni(\''.$cd.'\')">
          <img src="'.$urlsite.'modules/ibdw/mobilewall/templates/uni/images/dlt.png"> <p>'._t("_ibdw_mobilewall_close").'</p>
          </div><div class="clear"></div></div></div>';
          if($commentfunny==1){ 
          $parteintroduttiva = $parteintroduttiva.inserisci_commento_sub($cd,$iduser).'
          <div class="section_comment">'.commento($cd,$iduser); 
          $parteintroduttiva = $parteintroduttiva.'
          </div>';}
          $parteintroduttiva = $parteintroduttiva.'<input type="hidden" id="act_altezza'.$cd.'" value="" />
         </div>';
         return $parteintroduttiva; 
  }
  
  //per il messaggio personale verso un'altro utente
  function stampa_azione2($cd,$at,$txtr,$in2,$dt,$iduser,$sid,$share,$destinatario) 
  { 
    include('urlsite.php');
    include('config.php');
    $taskelimina = 0;
    $shareexplode = 0;
    if($iduser == $sid) { $taskelimina = 1; }
    if($sharefunny == 0) { $share='';}  
    if($share!=''){ $shareexplode = 1;}
    if($likefunny == 0) { $like='';}  
    if($like!=''){ $likeexplode = 1;}
    $userprofile = estrainick($sid,0);         
    $generatesto = '<h3><a href="bxprofile:'.$userprofile.'">'.$txtr.'</a> > <a href="bxprofile:'.$destinatario.'">'.$destinatario.'</a></h3>: <span>'.strip_tags($in2,'<img><div><a>').'</span> <p>'.$dt.'</p>';
    $parteintroduttiva='
        <div class="azione" id="act_'.$cd.'">
          <div class="mainheader">
            <div class="avatarleft">'.$at.'</div>
            <div class="textright">'.$generatesto.'</div>
            <div class="clear"></div>
            <div class="main_index">
              <div class="clear"></div> 
            </div>
          </div>
          <div id="azionilaterali" class="bt_azione_'.$cd.'"';
          
          if($sharefunny == 0 OR $shareexplode == 0) { 
          if($taskelimina==1){$parteintroduttiva = $parteintroduttiva.' onclick="fadeAzioni_dlt(\''.$cd.'\')"';}
          else{$parteintroduttiva = $parteintroduttiva.' onclick="fadeAzioni(\''.$cd.'\')"';} 
          }
          
          else { 
          if($taskelimina==1){$parteintroduttiva = $parteintroduttiva.' onclick="fadeAzioni_dlt_sh(\''.$cd.'\')"';}
          else{$parteintroduttiva = $parteintroduttiva.' onclick="fadeAzioni_sh(\''.$cd.'\')"';} 
          }
          
          
          $parteintroduttiva = $parteintroduttiva.'></div>
          <div id="azionilaterali_action'.$cd.'" class="azionilaterali_action">
          <div class="contmain">';if($commentfunny==1){$parteintroduttiva = $parteintroduttiva.inserisci_commento($cd,$iduser);} if($likefunny == 1){$parteintroduttiva = $parteintroduttiva.like_action($cd,$iduser);} 
          $parteintroduttiva = $parteintroduttiva.$share;
          if($taskelimina == 1) 
          { $parteintroduttiva = $parteintroduttiva.elimina($cd,$iduser); } $parteintroduttiva = $parteintroduttiva.'
          <div class="addcommento" onclick="fadeOutAzioni(\''.$cd.'\')">
          <img src="'.$urlsite.'modules/ibdw/mobilewall/templates/uni/images/dlt.png"> <p>'._t("_ibdw_mobilewall_close").'</p>
          </div><div class="clear"></div></div></div>';
          if($commentfunny==1){ 
          $parteintroduttiva = $parteintroduttiva.inserisci_commento_sub($cd,$iduser).'
          <div class="section_comment">'.commento($cd,$iduser); 
          $parteintroduttiva = $parteintroduttiva.'
          </div>';}
          $parteintroduttiva = $parteintroduttiva.'<input type="hidden" id="act_altezza'.$cd.'" value="" />
         </div>';
         return $parteintroduttiva; 
  }
  
  function estrainick($id,$stile) { 
  $selezione = "SELECT NickName,FirstName,LastName FROM Profiles WHERE ID = $id";
  $esegui = mysql_query($selezione);
  $nickrow = mysql_fetch_assoc($esegui);
    if($stile == 0) { $nick = $nickrow['NickName'];  }
    elseif($stile == 1) { $nick = $nickrow['FirstName'].' '.$nickrow['LastName'];  }
    elseif($stile == 2) { $nick = $nickrow['FirstName'];  }
    return $nick;
  } 
  
  function avatar($id) { 
  include('urlsite.php');
  $selezione = "SELECT Avatar,Sex FROM Profiles WHERE ID = $id";
  $esegui = mysql_query($selezione);
  $nickrow = mysql_fetch_assoc($esegui);
  if($nickrow['Avatar'] != 0) {  
  $avatar = '<img src="'.$urlsite.'modules/boonex/avatar/data/images/'.$nickrow['Avatar'].'i.jpg" />'; }
  else { 
    if($nickrow['Sex'] == 'male') { $avatar = '<img src="'.$urlsite.'templates/base/images/icons/visitor_small.gif">'; }
    else { $avatar = '<img src="'.$urlsite.'templates/base/images/icons/woman_small.gif">'; }
  }
  return $avatar;
  }
  
  
  //GESTIONE PRIVACY
//visualizza la new se la privacy è amici oppure se la privacy è fave ed io sono un fave o se la privacy è quella di default ed è uno di questi due valori allora visualizza
//$allowvalue è il valore per l'album, $toc è il tipo di contenuto (video, sito, ecc..), $author è il sender, userid è l'utente connesso.

//SITI: $tov='view',$toc='bx_sites'
//ADS: $tov='view',$toc='ads'
//FOTO: $tov=NULL, $toc='photos'
//VIDEO: $tov=NULL,$toc='videos'
//GRUPPI: $tov='view_group', $toc='groups'
//EVENTI: $tov='view_event',$toc='events'

function privstate($allowvalue,$toc, $author, $profileid, $num_fave, $tov)
{
 if ((((($allowvalue==5 and is_friends($author,$profileid)) OR ($allowvalue==6 and $num_fave==1)) OR (($allowvalue==3))) OR ($allowvalue==4)) OR ($author==$profileid)) {$allowview=1;}
 elseif ($allowvalue==1)
 {
  //ottengo la privacy predefinita per quell'album di quell'utente. group_id corrisponde al livello di privacy predefinita per quell'album di quell'utente
  
  $privdefault="select group_id from sys_privacy_defaults inner join sys_privacy_actions on sys_privacy_defaults.action_id=sys_privacy_actions.id where (sys_privacy_actions.module_uri='".$toc."'";
  if ($tov<>"") {$privdefault=$privdefault." and sys_privacy_actions.name='".$tov."'";}
  $privdefault=$privdefault.") and sys_privacy_defaults.owner_id=".$author;
  $resultdefault = mysql_query($privdefault) or die(mysql_error());
  $rowdefpriv = mysql_fetch_row($resultdefault);
  //se la privacy è 5 visualizza oppure se è 6 controlla se io sono nella lista dei suoi favoriti prima di visualizzare
  if ((((($rowdefpriv[0]==5 and is_friends($author,$profileid)) OR ($rowdefpriv[0]==6 and $num_fave==1)) OR ($rowdefpriv[0]==3)) OR ($rowdefpriv[0]==4)) OR ($author==$profileid)) {$allowview=1;}
 }
 else $allowview=0;
 return $allowview;
}
  
  
  function TempoPost($inputdata,$datadaconf,$offset)
  { 
      $differenza=intval((time()-(strtotime ($inputdata)+$offset))/60);
  		if ($differenza<60)
  		{
   			switch ($differenza)
			{
			 case 0: $miadata=_t('_ibdw_mobilewall_now');
			 case 1: $miadata=_t('_ibdw_mobilewall_oneminute');
			 break;
			 default: $miadata=_t('_ibdw_mobilewall_plusminutefirst')."$differenza"._t('_ibdw_mobilewall_plusminutesecond'); 
			 break;
   			}
  		}
  		else
  		{
   		 $ore=intval($differenza/60);
   		 if ($ore>=1 && $ore<2) {$miadata=_t('_ibdw_mobilewall_onehour');}
   		 elseif ($ore>=2 && $ore<25) {$miadata=_t('_ibdw_mobilewall_plushourfirst').$ore._t('_ibdw_mobilewall_plushoursecond');}
   		 elseif ($differenza>=1440 && $differenza<1500) {$miadata=_t('_ibdw_mobilewall_today');}
   		 elseif ($differenza>=1500 && $differenza<2880) {$miadata=_t('_ibdw_mobilewall_yesterday');}
   		 else 
		 {
		  if ($inputdata==NULL)
		  {$miadata=_t('_ibdw_mobilewall_dateprev');}
		  else {$miadata=_t('_ibdw_mobilewall_otherday').date($datadaconf, strtotime ($inputdata));}
		 }
  		}
  		$miadata='<span>'.$miadata.'</span>';
		return $miadata;
  }
?>



<script>

function likethis(id_like,user,valx){ 
  $.ajax({
   type: "POST",
   url:  "<?php echo $urlsite;?>modules/ibdw/mobilewall/classes/likethis.php",
   data: "id_like="+id_like+"&user="+user + "&valx=" + valx,
   success: function(data){
   window.location.reload();
   }
 });
}

function spam_action(id_action,spammer,reporter,tofNick){
  $.ajax({
   type: "POST",
   url:  "<?php echo $urlsite;?>modules/ibdw/mobilewall/classes/reportspam.php",
   data: "id_action="+id_action+"&spammer="+spammer + "&reporter=" + reporter + "&tofNick" + tofNick,
   success: function(data){
   window.location.reload();
   }
 });
}

function invio_form(tipologia,id){ 
  var testo_form = $("#commento_"+id).val();
  var id_def = id+"**<?php echo $mioid;?>**"+testo_form;
  $.ajax({
   type: "POST",
   url:  "<?php echo $urlsite;?>modules/ibdw/mobilewall/classes/elabora_form.php",
   data: "tipologia="+tipologia+"&id="+id_def,
   success: function(data){
   window.location.reload();
   }
 });
}

function elimina(entry,iduser){ 
  $.ajax({
   type: "POST",
   url:  "<?php echo $urlsite;?>modules/ibdw/mobilewall/classes/elimina.php",
   data: "entry="+entry+"&iduser="+iduser,
   success: function(data){
   window.location.reload();
   }
 });
}

function elimina_commento(entry){ 
  $.ajax({
   type: "POST",
   url:  "<?php echo $urlsite;?>modules/ibdw/mobilewall/classes/elimina_commento.php",
   data: "entry="+entry,
   success: function(data){
   window.location.reload();
   }
 });
}

function ajax() { 

  $.ajax({
   type: "POST",
   url:  "<?php echo $urlsite;?>modules/ibdw/mobilewall/classes/elabora_form.php",
   data: "tipologia="+tipologia+"&id="+id_def,
   success: function(data){
   }
 });

}

function fadeElement(ide) { 
    $(ide).fadeIn('fast');
  }
  
  function fadeElementOut(ide) { 
    $(ide).fadeOut('fast');
  }
  
  function slideElement(ide) { 
    $(ide).slideToggle('fast');
  }
                              
  function fadeAzioni(ide) { 
    var altezza = $("#act_"+ide).height();
    var larghezza = $("#act_"+ide).width();
    var larghezza = larghezza-10;
    var larghezza_addcommento = larghezza/4;
    var larghezza_contcommento = larghezza_addcommento+larghezza_addcommento+48;  
    var larghezza_contcommentoleft = larghezza_contcommento/2;
    var larghezza_val_left = "-"+larghezza_contcommentoleft;
    $("#azionilaterali_action"+ide).css("width",+larghezza);
    $(".bt_azione_"+ide).fadeOut();
    $("#act_altezza"+ide).val(altezza);
    $("#act_"+ide).animate({ height: '+196px'},1);
    $("#azionilaterali_action"+ide).fadeIn('fast');
    var larghezza_commento = $(".inserimento_commento").width();
    var larg_comment = larghezza_commento-10;
    $(".commento_inserimento").css("width",+larg_comment);
    $(".commento_inserimento").css("height","48px");
  }
  
  function fadeAzioni_dlt(ide) { 
    var altezza = $("#act_"+ide).height();
    var larghezza = $("#act_"+ide).width();
    var larghezza = larghezza-10;
    var larghezza_addcommento = larghezza/4;
    var larghezza_contcommento = larghezza_addcommento+larghezza_addcommento+48;  
    var larghezza_contcommentoleft = larghezza_contcommento/2;
    var larghezza_val_left = "-"+larghezza_contcommentoleft;
    $("#azionilaterali_action"+ide).css("width",+larghezza);
    $(".bt_azione_"+ide).fadeOut();
    $("#act_altezza"+ide).val(altezza);
    $("#act_"+ide).animate({ height: '+198px'},1);
    $("#azionilaterali_action"+ide).fadeIn('fast');
    var larghezza_commento = $(".inserimento_commento").width();
    var larg_comment = larghezza_commento-10;
    $(".commento_inserimento").css("width",+larg_comment);
    $(".commento_inserimento").css("height","99px");
  }
  
  
    function fadeAzioni_sh(ide) { 
    var altezza = $("#act_"+ide).height();
    var larghezza = $("#act_"+ide).width();
    var larghezza = larghezza-10;
    var larghezza_addcommento = larghezza/4;
    var larghezza_contcommento = larghezza_addcommento+larghezza_addcommento+48;  
    var larghezza_contcommentoleft = larghezza_contcommento/2;
    var larghezza_val_left = "-"+larghezza_contcommentoleft;
    $("#azionilaterali_action"+ide).css("width",+larghezza);
    $(".bt_azione_"+ide).fadeOut();
    $("#act_altezza"+ide).val(altezza);
    $("#act_"+ide).animate({ height: '+248px'},1);
    $("#azionilaterali_action"+ide).fadeIn('fast');
    var larghezza_commento = $(".inserimento_commento").width();
    var larg_comment = larghezza_commento-10;
    $(".commento_inserimento").css("width",+larg_comment);
     $(".commento_inserimento").css("height","100px");
  }
  
  function fadeAzioni_dlt_sh(ide) { 
    var altezza = $("#act_"+ide).height();
    var larghezza = $("#act_"+ide).width();
    var larghezza = larghezza-10;
    var larghezza_addcommento = larghezza/4;
    var larghezza_contcommento = larghezza_addcommento+larghezza_addcommento+48;  
    var larghezza_contcommentoleft = larghezza_contcommento/2;
    var larghezza_val_left = "-"+larghezza_contcommentoleft;
    $("#azionilaterali_action"+ide).css("width",+larghezza);
    $(".bt_azione_"+ide).fadeOut();
    $("#act_altezza"+ide).val(altezza);
    $("#act_"+ide).animate({ height: '+251px'},1);
    $("#azionilaterali_action"+ide).fadeIn('fast');
    var larghezza_commento = $(".inserimento_commento").width();
    var larg_comment = larghezza_commento-10;
    $(".commento_inserimento").css("width",+larg_comment);
    $(".commento_inserimento").css("height","154px");
  }
  
  function fadeOutAzioni(ide) { 
    var altezza = $("#act_altezza"+ide).val();
    $("#act_"+ide).animate({ height: "100%"},1);
    $("#azionilaterali_action"+ide).fadeOut('fast');
    $(".bt_azione_"+ide).fadeIn();
  }

</script>