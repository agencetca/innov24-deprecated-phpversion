/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

AqbPointsManage = new AqbPoints();

function AqbPoints(){
	this._price = 0;
	this._points_num = 0;
}

AqbPoints.prototype.showPopup = function(sUrl) {
   var oPopupOptions = {
        fog: {color: '#fff', opacity: .7},        
    };

    $.get(sUrl, function(data) {   
		 $('#aqb_popup').remove();
		  $(data).appendTo('body').dolPopup(oPopupOptions); 
	 });

}

AqbPoints.prototype.sumUpdate = function(element, fPrice){
  try{
	  this._points_num = parseInt($(element).val());
	  this._price = parseFloat(fPrice)*this._points_num;
	  
	  if (isNaN(this._price)) this._price = 0;
		$('#aqb_price_counter').html(this._price.toFixed(2));	
	  }catch(e){
		$('#aqb_price_counter').html(0);	
   }
}

AqbPoints.prototype.onSubmitPresent = function(sWrongPoints, sConfirm, sUrl){
  try{
	   $('#aqb_present_points_button').attr('disabled', true);
	   var mybalance = parseInt($('#aqb_current_balance').val());
	   var profile_id = parseInt($('#aqb_profile_id').val());
	   var present_points = parseInt($('#aqb_points_num').val()) 
	   if (isNaN(present_points)) present_points = 0;
	   
	   if (present_points > mybalance || present_points <= 0) 
	   {
		 alert(sWrongPoints);
		 return; 
	   }
	   if (confirm(sConfirm.replace('{0}', present_points))) 
	   {
		  var oDate = new Date();
		  $.post(sUrl + profile_id + '/' + present_points,		
					{
						_t:oDate.getTime()
					},
		        function(oData){
		     		alert(oData.message);
					if (oData.code == 0) 
					{
						$('#login_div').dolPopupHide();
					}
					$('#aqb_present_points_button').attr('disabled', false);
			    },
		        'json'
		     );
		}
	  }catch(e){
   }
}

AqbPoints.prototype.onSubmitPrice = function(sUrl, sRedirect, sMessage){
	if (!this._price || !this._points_num) return false;  
	$('#aqb_buy_points_button').attr('disabled', true);
	
	$.get(sUrl + this._points_num + '/' + this._price, function(data) {   
		 if (data.length == 0) 
		 {
			alert(sMessage);
			$('#aqb_buy_points_button').attr('disabled', false);
			return;
		 }	
		 var oDate = new Date();
	
		 $.post(
				data.toString(),		
				{
					_t:oDate.getTime()
				},
	        function(oData){
	           try{ 
					alert(oData.message);
					window.location = sRedirect;
				}catch(e){}
	        },
	        'json'
	     );
	 });
}


AqbPoints.prototype.onExchangePoints = function(sUrl, sConfirm, sRedirect){
	if (!confirm(sConfirm))  return;
	var oDate = new Date();
	$.post(sUrl,		
				{
					_t:oDate.getTime()
				},
	        function(oData){
	           try{ 
					alert(oData.message);
					if (oData.code == 0) window.location = sRedirect;
				}catch(e){}
	        },
	        'json'
	     );
}

AqbPoints.prototype.onBuyPackage = function(sUrl, sRedirect){
	$('#aqb_buy_points_button, button').attr('disabled', true);
	 var oDate = new Date();
	
	$.get(sUrl, function(data) {   
		 $.post(
				data.toString(),		
				{
					_t:oDate.getTime()
				},
	        function(oData){
	           try{ 
					alert(oData.message);
					window.location = sRedirect;
				}catch(e){}
	        },
	        'json'
	     );
	 });
}

AqbPoints.prototype.onExchangePointsToMoney = function(sUrl, sConfirm, sUrlE){
	var oDate = new Date();

	$.post(sUrl,{_t:oDate.getTime()},
	        function(oData){
	           try{ 
					if (parseInt(oData.code) != 0) alert(oData.message);
					else if(parseInt(oData.code) == 0 && confirm(sConfirm)){ 
							$.post(sUrlE,{_t:oDate.getTime()},
								        function(oData){
											alert(oData.message);											
								        },
								        'json'
								  );
					}						
				}catch(e){}
	        },
	        'json'
	     );
}