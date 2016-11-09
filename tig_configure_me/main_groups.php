<?php

require_once(BX_DIRECTORY_PATH_ROOT.'tig_configure_me/config.php');

$memberiID = getID($_GET['ID']);

//On télécharge toutes les catégories liées au module

$all = "SELECT DISTINCT sys_categories.Category, sys_categories.Liked
FROM sys_categories
WHERE sys_categories.Type='bx_groups'";
$BigList = mysql_query($all);

//On télécharge les catégories de l'utilisateur liées au module

$specific = "SELECT afk_cfgme_interest.cat_id
FROM afk_cfgme_interest
WHERE afk_cfgme_interest.user_id=".$memberiID."";
$SmallList = mysql_query($specific);

//On met les catégories de l'utilisateur dans un tableau

$pattern1 = '(.*?)(Groups\{?.*?\})(.*\{?.*?\})';
$pattern2 = '[{}]';
$pattern3 = '^Groups';
$pattern4 = ',';

$user_categories = mysql_fetch_assoc($SmallList);

$user_category = $user_categories['cat_id'];
$user_category = preg_replace("#".$pattern1."#","$2",$user_category);
$user_category = preg_replace("#".$pattern2."#",'',$user_category);
$user_category = preg_replace("#".$pattern3."#",'',$user_category);
$user_category = preg_split("#".$pattern4."#",$user_category);


?>
<link rel="stylesheet" type="text/css" href="tig_configure_me/Vue/style.css">
<?php


include(BX_DIRECTORY_PATH_ROOT.'tig_configure_me/Vue/top_title.php');

echo'
		
		<form id="form_configureme" class="form_configureme">
			<div id="box_all_cats" class="box_all_cats">
		
		
		';

//On compare chaque élement de BigList avec le tableau utilisateur
while ($cat_list = mysql_fetch_assoc($BigList)) {

	$single = $cat_list["Category"];
	$likes = $cat_list["Liked"];
	$nam = preg_replace("#\W#",'',$single);

	if (array_keys($user_category,$single)!=NULL) {
			echo '<div class="input_configureme" ><input type="checkbox" id='.$nam.' name="cat" value="'.$single.'" onclick="configure_me(this.value);" checked><div class="single">'.$single.'</div></div><br />';
	} else {
			echo '<div class="input_configureme" ><input type="checkbox" id='.$nam.' name="cat" value="'.$single.'" onclick="configure_me(this.value);"><div class="single">'.$single.'</div></div><br />';
	}

}//fin du while			
	
echo'

			</div>
		</form>

';

include(BX_DIRECTORY_PATH_ROOT.'tig_configure_me/Vue/my_categories.php');




?>

<script src="tig_configure_me/Controleur/jquery.cookie.js"></script>

<script>
identifiant = "Groups";
</script>

<script>

a = 0;

function configure_me(string){
var string = string;

$.get('tig_configure_me/Modele/configureme.php', {string:string,identifiant:identifiant}, false);
}

$('#form_configureme').mouseover(function() {
// $('body').css( "overflow", "hidden" );
$('body').addClass('noscroll');
});

$('#form_configureme').mouseout(function() {
// $('body').css( "overflow", "scroll" );
$('body').removeClass('noscroll');
});
</script>

<script>
function nocategory(){
 if (a==0){
 $('#my_categories li:first').after('<li id="none" class="no_categories"><div class="no_categories_img"></div><div class="no_categories_txt"><?php echo _t($Nocategories);?></a></div></div></li>');
	}
}

</script>

<script>

$("input[name='cat']:checked").each(function(){
	a++;
	var original = $(this).val();
	var nam = $(this).val().replace(/\W/g, '');
	$.cookie(''+nam+'', ''+original+'');
	
	$.get('tig_configure_me/Modele/count_likes_first.php', {identifiant:identifiant,original:original}, function(response){
	if (response==0){
	$('#my_categories').append('<li id="'+nam+'" class="my_categories_element"><div class="like_cat"><div id="number'+nam+'number" class="number_likes"></div><div id="'+nam+'" class="like_button" onclick="I_like_this(id)"><div id="'+nam+'txt" class="like_txt">Like</div></div></div><div class="close_cat" id="'+nam+'" onclick="categoryclose(id)"><span class="close_cat_txt">x</span></div><div class="my_categories_text">'+original+'</div></li>');
	}else{
	$('#my_categories').append('<li id="'+nam+'" class="my_categories_element"><div class="like_cat"><div id="number'+nam+'number" class="number_likes">'+response+'</div><div id="'+nam+'" class="like_button" onclick="I_dislike_this(id)"><div id="'+nam+'txt" class="like_txt">Liked</div></div></div><div class="close_cat" id="'+nam+'" onclick="categoryclose(id)"><span class="close_cat_txt">x</span></div><div class="my_categories_text">'+original+'</div></li>');
	$('.like_cat #number'+nam+'number').css({display: "block"});
	$('.like_cat #'+nam+'txt').css({'backgroundColor' : "#333",'color' : 'white' });
	}
	
	
	});
});
nocategory();
 
var panel_state_Groups = $.cookie('panel_state_Groups');
if (panel_state_Groups == 1) {
$('#form_configureme').fadeIn(100).css({display: "block"});
$('#open_button_top').remove();
$('#total_configure_me_container').append('<div id= "close_button_top" class="close_button_top" onclick="close_cfgme_pannel()"><?php echo _t($Close_boutton) ;?></div>');
$.cookie('panel_state_Groups', '1');
} else {
$('#form_configureme').fadeOut(100).css({display: "none"});
$('#close_button_top').remove();
$('#reset_cat').css({display: "none"});
$('#propose_a_category').css({display: "none"});
$('#my_cats_title').css({marginTop: "53px"});
$('#total_configure_me_container').append('<div id= "open_button_top" class="close_button_top" onclick="open_cfgme_pannel()"><?php echo _t($Open_boutton) ;?></div>');
}
 
</script>

<script>
$("input[name='cat']").click(function(){

	var nam = $(this).val().replace(/\W/g, '');
	var original = $(this).val();

	if ($('li#'+nam).length!=0){
	$('li#'+nam).fadeOut(300, function() { $(this).remove(); });
	a--;
	nocategory();
	}
	else {
	$('#my_categories li#none').remove();
	
	$.get('tig_configure_me/Modele/count_likes_first.php', {identifiant:identifiant,original:original}, function(response){
	
		if (response==0){
		$('#my_categories li:first').after('<li id="'+nam+'" class="my_categories_element"><div class="like_cat"><div id="number'+nam+'number" class="number_likes"></div><div id="'+nam+'" class="like_button" onclick="I_like_this(id)"><div id="'+nam+'txt" class="like_txt">Like</div></div></div><div class="close_cat" id="'+nam+'" onclick="categoryclose(id)"><span class="close_cat_txt">x</span></div><div class="my_categories_text">'+original+'</div></li>');
		}else{
		$('#my_categories li:first').after('<li id="'+nam+'" class="my_categories_element"><div class="like_cat"><div id="number'+nam+'number" class="number_likes">'+response+'</div><div id="'+nam+'" class="like_button" onclick="I_dislike_this(id)"><div id="'+nam+'txt" class="like_txt">Liked</div></div></div><div class="close_cat" id="'+nam+'" onclick="categoryclose(id)"><span class="close_cat_txt">x</span></div><div class="my_categories_text">'+original+'</div></li>');
		$('.like_cat #number'+nam+'number').css({display: "block"});
		$('.like_cat #'+nam+'txt').css({'backgroundColor' : "#333",'color' : 'white' });
		}	
		a++
	});
 }

});
</script>

<script>


function open_cfgme_pannel(){
$('#form_configureme').fadeIn(400).css({display: "block"});
$('#open_button_top').remove();
$('#reset_cat').css({display: "block"});
$('#propose_a_category').css({display: "block"});
$('#my_cats_title').css({marginTop: "25px"});
$('#total_configure_me_container').append('<div id= "close_button_top" class="close_button_top" onclick="close_cfgme_pannel()"><?php echo _t($Close_boutton) ;?></div>');
$.cookie('panel_state_Groups', '1');
}

function close_cfgme_pannel(){
$('#form_configureme').fadeOut(400).css({display: "none"});
$('#close_button_top').remove();
$('#reset_cat').css({display: "none"});
$('#propose_a_category').css({display: "none"});
$('#my_cats_title').css({marginTop: "53px"});
$('#total_configure_me_container').append('<div id= "open_button_top" class="close_button_top" onclick="open_cfgme_pannel()"><?php echo _t($Open_boutton) ;?></div>');
$.cookie('panel_state_Groups', '0');
}

</script>

<script>

function reset_cat() {

$.get('tig_configure_me/Modele/reset_my_cats.php', {identifiant:identifiant}, function(){
    $('#response_reset_cat').fadeIn();
    $('#response_reset_cat').html('Done'); 
    $('#response_reset_cat').fadeOut('slow'); 
});
}

</script>

<script>
function categoryclose(nam){
$('#my_categories #'+nam).remove();
var string = $("#form_configureme #"+nam).val();
$("#form_configureme #"+nam).removeAttr('checked');
a--;
nocategory();
configure_me(string);
}
</script>

<script>
function I_like_this(nam) {

var original = $.cookie(''+nam+'');

$.get('tig_configure_me/Modele/likethiscat.php', {identifiant:identifiant, original:original}, function(){

	$.get('tig_configure_me/Modele/count_likes.php', {identifiant:identifiant, original:original}, function(responseText){
	
    $('.like_cat #'+nam).replaceWith('<div id="'+nam+'" class="like_button" onclick="I_dislike_this(id)"><div id="'+nam+'txt" class="like_txt">Liked</div></div>');
    $('.like_cat #'+nam+'txt').css({'backgroundColor' : "#333",'color' : 'white' });
    $('.like_cat #number'+nam+'number').fadeIn(100);
    $('.like_cat #number'+nam+'number').css({display: "block"});
    $('.like_cat #number'+nam+'number').html(responseText);
    
},false);
    
},false);
}
</script>

<script>
function I_dislike_this(nam) {

var original = $.cookie(''+nam+'');

$.get('tig_configure_me/Modele/dislikethiscat.php', {identifiant:identifiant, original:original}, function(){
    
	$('.like_cat #'+nam).replaceWith('<div id="'+nam+'" class="like_button" onclick="I_like_this(id)"><div id="'+nam+'txt" class="like_txt">Liked</div></div>');
    $('.like_cat #number'+nam+'number').fadeOut(100);
    $('.like_cat #'+nam+'txt').html('Like');
	
},false);
}
</script>










