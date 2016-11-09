##This is my README

For install Delete All Cache

Change header.inc.php variable :

> -$site['url']               = "http://dev.innov24.com/";
> -$dir['root']               = "/var/www/vhosts/dev.innov24.com/httpdocs/";

by :
> -$site['url']               = "http://localhost/innov24/";
> -$dir['root']               = "/Applications/MAMP/htdocs/innov24/";

Or another thing needed :

also change if needed
> -$db['host']                = 'localhost';

Bug on page http://localhost/innov24/ once logged (CSS)

Updtade DDB with use "transfered" instead of "donated" word in credit language module (Need change in installation files ?)

Set language key (Confirm and change): <br>   
for_product : pour le produit  <br>   
default_price = Prix originale <br>   
set_new_price = Changer le prix de votre produit <br>   
buy_with_credit = Acheter <br>   
current_amount = Montant actuel du produit <br>   
new_price = Le nouveau prix est de  <br>   
title_negociate = ?? <br>   
Credit_payement_for_product = Payement par crédit <br>   
credit_quantite_a_payer = Coût du produit <br>   
confirm_credit_payement = Confirmer le payement par crédit <br>   
_modzzz_credit_payment_credits_error_price_changed = une erreur est survenu, le prix à changé depuis votre commande. <br>   
buy_credits = Vous avez besoin d'acheter des crédits <br>   