

	

	<div id="total_configure_me_container" class="total_configure_me_container">
	
		
		<div id= "open_button_top" class="close_button_top" onclick="open_cfgme_pannel()"><?php echo _t($Open_boutton);?></div>
		
	<div id="reset_cat" class="reset_cat"><a onclick="reset_cat()"><?php echo _t($Reset_boutton);?></a></div><div id="response_reset_cat" class="response_reset_cat"></div>
	<?php echo '<div id="propose_a_category" class="propose_a_category" style="margin-left:'.$Margin_propose.'px;">';?><a><?php echo _t($Propose_a_category);?></a><?php echo'</div>';?>

	<div id="my_cats_title" style="cursor:default; font-size: 17px; font-weight: bold; color:grey; margin-top: 25px; margin-left: 10px;"><?php echo _t($Categories_you_watch);?></div>
	
	<ul id="my_categories" class="list_my_categories">
	<li></li>
	</ul>
	</div>