function ImageNewsMain(oOptions) {
    this._sSystem = oOptions.sSystem;
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oImageNewsMain' : oOptions.sObjName;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}
ImageNewsMain.prototype.changePage = function(iStart, iPerPage, sType, sTypeParams) {
	var $this = this;
    var oDate = new Date();    
    var oParams = {
    	_t:oDate.getTime()
    }

    if(sTypeParams)
    	oParams['params'] = sTypeParams;

    if($('#imagenews-filter-chb:checked').length > 0 && $('#imagenews-filter-txt').val().length > 0)
    	oParams['filter_value'] = $('#imagenews-filter-txt').val();

    var sLoadingId = '#imagenews-' + sType + '-loading'; 
    $(sLoadingId).bx_loading();

    $.post(
        this._sActionsUrl + 'act_get_imagenews/' + (sType ? sType + '/' : '') + iStart + '/' + iPerPage + '/',
        oParams,
        function(sData) {
        	$(sLoadingId).bx_loading();

            $('.imagenews-view #imagenews-content-' + sType).bx_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                $(this).replaceWith(sData);
            });            
        },
        'html'
    );
}
ImageNewsMain.prototype.deleteEntry = function(iId) {
	var $this = this;
	
	$.post(
		this._sActionsUrl + "act_delete/",
		{id:iId},
		function(sData) {
			var iCode = parseInt(sData);
			if(iCode == 1) {
				alert(aDolLang['_imagenews_msg_success_delete']);
				window.location.href = $this._sActionsUrl
			}
			else
				alert(aDolLang['_imagenews_msg_failed_delete']);
		}
	)
}