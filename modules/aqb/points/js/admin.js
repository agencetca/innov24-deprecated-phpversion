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

var Profile = new Profiles({_sActionsUrl: '',_iCountryNumber:'', _sObjName: '',_sViewType: '', _sCtlType: '', _sFilter: '', _oCtlValue:'',_iAttemptId: '', _iStart: '',_iPerPage:'',_sOrderBy:'',_sAnimationEffect:'',_iAnimationSpeed:''});

function Profiles(oOptions) {    
    this._sActionsUrl = oOptions.sActionUrl == undefined ? '' : oOptions.sActionUrl;
	this._iCountryNumber = oOptions.iCountryNumber == undefined ? 0 : parseInt(oOptions.iCountryNumber);
    this._sObjName = oOptions.sObjName == undefined ? 'oAMS' : oOptions.sObjName;
    this._sViewType = oOptions.sViewType == undefined ? 'geeky' : oOptions.sViewType;
    this._sCtlType = oOptions.sCtlType == undefined ? 'qlinks' : oOptions.sCtlType;
	this._sFilter = '';
	this._sIP = '';
    this._oCtlValue = '';
	this._iAttemptId = 0;
	this._sShowOnly = '';
    this._iStart = oOptions.iStart == undefined ? 0 : parseInt(oOptions.iStart);
    this._iPerPage = oOptions.iPerPage == undefined ? 30 : parseInt(oOptions.iPerPage);
    this._sOrderBy = oOptions.sOrderBy == undefined ? '' : oOptions.sOrderBy;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'fade' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}

Profiles.prototype.showOnly = function(item) {
    this._sShowOnly = $(item).val();
	this._sFilter = $("[name='points-filter-input']").val();
	this.getRequests();
}

Profiles.prototype.showFilter = function (sHref) {
 
if ($('#points-search').css('display') == 'none')	
{	
	$('#points-search').css('display','block');
	$('#points-search').css('margin-bottom','10px');
	$('#filter_val').val('1');
	$(sHref).text(this._sHideFilter);	
}	
else 
{	
	$('#points-search').css('display','none');
	$('#points-search').css('margin-bottom','0');
	$('#filter_val').val('0');
	$(sHref).text(this._sShowFilter);	
}	

}

Profiles.prototype.changeFilterSearch = function () {
    var sValue = $("[name='points-filter-input']").val();    
    if(sValue.length <= 0)
        return;
		
	this._sFilter = sValue;
	this._iStart = ''; 
    this._iPerPage = ''; 
	
	if (this._sSection == 'requests') this.getRequests();
	else
    this.getMembers(function() {
        $('#points-members-form > .points-members-wrapper:hidden').html('');
    });
}
/*--- Paginate Functions ---*/
Profiles.prototype.changePage = function(iStart) {
    this._iStart = iStart;
    this.getMembers();
}
Profiles.prototype.changeOrder = function(oSelect) {
    this._sOrderBy = oSelect.value;
	if (this._sSection == 'requests') this.getRequests();
	else
    this.getMembers();
}
Profiles.prototype.changePerPage = function(oSelect) {
    this._iPerPage = parseInt(oSelect.value);

	if (this._sSection == 'requests') this.getRequests();
	else
    this.getMembers();
}

Profiles.prototype.orderByField = function(sOrderBy, sFieldName) {
    this._sViewType = sOrderBy;
	this._sOrderBy = sFieldName;

	if (this._sSection == 'requests') this.getRequests();
	else
    this.getMembers();
}

Profiles.prototype.onSubmitAdminPoints = function(sWorningNumPoints, sConfirm, sUrl) {
   try{
	   var profile_id = parseInt($('#aqb_profile_id').val());
	   var present_points = parseInt($('#aqb_points_num').val());
	   var points_reason = $('#aqb_points_reason').val();	   
	   
	   if (isNaN(present_points)) present_points = 0;
	   
	   if (present_points <= 0) 
	   {
		 alert(sWorningNumPoints);
		 return; 
	   }

	   if (points_reason.length == 0 && !confirm(sConfirm)) return; 
		  var oDate = new Date();
		  $.post(sUrl + '/' + profile_id + '/' + present_points,		
					{
						_t:oDate.getTime(),
						reason:points_reason
					},
		        function(oData){
		     		alert(oData.message);
					if (oData.code == 0) 
					{
						$('#aqb_popup').dolPopupHide();
						Profile.getMembers();
					}
			    },
		        'json'
		     );
	  }catch(e){
		alert(e.toString());
   }
}

Profiles.prototype.getMembers = function(onSuccess) {
    var $this = this;
    		
    if(onSuccess == undefined)
        onSuccess = function(){}

    $('#points-members-loading').bx_loading();
    
	if ($('#points-search').css('display') == 'none') this._sFilter = '';

	
    var oOptions = {
        action: 'members', 
        view_type: this._sViewType, 
        view_start: this._iStart, 
        view_per_page: this._iPerPage, 
        view_order: this._sOrderBy, 
        ctl_type: this._sCtlType,
		filter: this._sFilter,
		ctl_value: this._oCtlValue
    }

   $.post(
        this._sActionsUrl + 'members/',
        oOptions,
        function(oResult) {
			
			$('#points-members-loading').bx_loading();
  		
            $('div.points-control-panel').css('display','block');
			$('div.admin_actions_panel').css('display','block');

			
			$('#points-members-common').bx_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                $('#points-members-common').html(oResult).bx_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
            });

            onSuccess();
        });
}

Profiles.prototype.cleanProfileHistory = function(sUrl, sMessage){
	if (!confirm(sMessage)) return;
	
	var oDate = new Date();
	$.post(sUrl,
		{
			_t:oDate.getTime()		
		},
		function(oData){
			alert(oData.message); 
			if (oData.code == 0) Profile.getMembers();
		}, 'json');	
}

Profiles.prototype.removeAction = function(id, sUrl, sConfirm){
	if (!confirm(sConfirm)) return;
	
	var oDate = new Date();
	$.post(sUrl,
		{
			_t:oDate.getTime()		
		},
		function(oData){
			alert(oData.message); 
			if (oData.code == 0) $('#' + id).fadeOut('slow');
		}, 'json');	
}

Profiles.prototype.onDeleteLevel = function(sUrl, sConfirm, sBaseUrl){
	if (!confirm(sConfirm)) return;
	
	var oDate = new Date();
	$.post(sUrl,{_t:oDate.getTime()},
		function(oData){
			alert(oData.message); 
			if (oData.code == 0) window.location.href = sBaseUrl;
		}, 'json');	
}

Profiles.prototype.onEditLevel = function(sUrl){
	var oDate = new Date();
	$.post(sUrl,
		{
			_t:oDate.getTime()		
		},
		function(oData){
			alert(oData.message); 
			if (oData.code == 0) $('#' + id).fadeOut('slow');
		}, 'json');	
}

Profiles.prototype.disableModule = function(sUrl, sConfirm, sRedirect){
	if (!confirm(sConfirm)) return;
	
	var oDate = new Date();
	$.post(sUrl,
		{
			_t:oDate.getTime()		
		},
		function(oData){
			alert(oData.message); if (oData.code == 0) window.location = sRedirect;
		}, 'json');	
}

Profiles.prototype.onSubmitAction = function(sMessageWorn, sUrl, sRedirect){
   var Name = $('#aqb_points_action_alert').val();	
   var Title = $('#aqb_points_action_title').val();
   var AlertName = $('#aqb_points_action_alert_name').val();
   
   if (!Name.length || !Title.length || !AlertName.length) 
   {		
		alert(sMessageWorn); 
		return false;
   }	
   
   var oDate = new Date();
   $.post(sUrl,		
				{
					title:Title,
					name:Name,
					alert_name:AlertName,
					points:$('#aqb_points_num').val(),
					limit:$('#aqb_points_limit').val(),
					checked:$('#aqb_points_check_id').is(':checked'),
					_t:oDate.getTime()
				},
	        function(oData){
	           try{ 
					alert(oData.message);
					if (oData.code == 0) 
					{	
						$('#aqb_popup').dolPopupHide();
						window.location = sRedirect;
					}	
				}catch(e){}
	        },
	        'json'
	     );
}

Profiles.prototype.getRequests = function(onSuccess) {
    var $this = this;
    		
    if(onSuccess == undefined)
        onSuccess = function(){}

    $('#div-loading').bx_loading();
    
	if ($('#items-search').css('display') == 'none') this._sFilter = '';

	
    var oOptions = {
        action: 'requests', 
        view_type: this._sViewType, 
        view_start: this._iStart, 
        view_per_page: this._iPerPage, 
        view_order: this._sOrderBy, 
		filter: this._sFilter,
		request:this._sShowOnly
    }

   $.post(
        this._sActionsUrl + 'requests/',
        oOptions,
        function(oResult) {
			$('#div-loading').bx_loading();
					
			$('#items-members-common').bx_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                $('#items-members-common').html(oResult).bx_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
            });

            onSuccess();
        });
}

Profiles.prototype.showAll = function(){
	this._sViewType = ''; 
    this._iStart = '';
    this._iPerPage = '';
    this._sOrderBy = ''; 
    this._sCtlType = '';
	this._sFilter = '';
    this._oCtlValue  = '';
	Profile.getMembers();
}