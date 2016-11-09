function AqbPTSMain(oOptions) {
	this._sBaseURL = oOptions.sBaseURL;
	this._iMigratePerOperation = oOptions.iMigratePerOperation;
}

AqbPTSMain.prototype.toggleTypeStatus = function (iType) {
	var $this = this;

	$.post($this._sBaseURL + 'action_toggle_status/' + iType, function() {
		$('#types_list').load($this._sBaseURL + 'action_get_profile_types');
	}, 'html');
}
AqbPTSMain.prototype.deleteType = function (iType) {
	var $this = this;

	$.post($this._sBaseURL + 'action_delete_type/' + iType, function() {
		$('#types_list').load($this._sBaseURL + 'action_get_profile_types');
	}, 'html');
}
AqbPTSMain.prototype.toggleHPStatus = function (iType) {
	var $this = this;

	$.post($this._sBaseURL + 'action_toggle_hp_status/' + iType, function() {
		$('#types_list').load($this._sBaseURL + 'action_get_profile_types');
	}, 'html');
}
AqbPTSMain.prototype.addNewType = function (oForm) {
	var $this = this;

	$(oForm).ajaxSubmit(function(sResponce) {
        try {
            if (sResponce.length) {
            	alert(sResponce)
            } else {
            	$('#types_list').load($this._sBaseURL + 'action_get_profile_types');
            }
        } catch(e) {}
    });
}

AqbPTSMain.prototype.migrate = function (oForm) {
	var $this = this;

	$this._iMigrated = 0;
	$this._iTotal = 0;
	$this._iStartFrom = 0;

	var iMemLevel = $('select[name="membership_type"]', oForm).val();
	var iPType = $('select[name="profile_type"]', oForm).val();
	var iSetType = $('select[name="set_profile_type"]', oForm).val();

	$('input[name=migrate]', oForm).attr('disabled', 'disabled');

	var elProgress = $('#migrate_progress');
	elProgress.html(_t('_aqb_pts_initializing'));
	elProgress.show();

	oAqbPTSMain._doMigrate(iMemLevel, iPType, iSetType);
}
AqbPTSMain.prototype._doMigrate = function (iMemLevel, iPType, iSetType) {
	var $this = this;

	var oDate = new Date();

	$.post(
		$this._sBaseURL + 'action_migrate/' + iMemLevel + '/' + iPType + '/' + iSetType + '/' + $this._iStartFrom + '/' + $this._iMigratePerOperation,
		{
		    _t:oDate.getTime()
		},
		function(oResult){
			if (!oResult.bEnd) {
				$this._iMigrated += parseInt(oResult.iMigrated);
            	if ($this._iStartFrom == 0) $this._iTotal = oResult.iTotal;
            	$this._iStartFrom += $this._iMigratePerOperation;
            	$('#migrate_progress').html(_t('_aqb_pts_processed')+' '+$this._iStartFrom+'/'+$this._iTotal+' '+_t('_aqb_pts_profiles')+'<br />'+_t('_aqb_pts_migrated')+': '+$this._iMigrated+' '+_t('_aqb_pts_profiles')+'...');
            	setTimeout('oAqbPTSMain._doMigrate('+iMemLevel+', '+iPType+', '+iSetType+')', 500);
			} else {
				$('#migrate_progress').hide();
				$('input[name=migrate]').attr('disabled', '');
				$('#types_list').load($this._sBaseURL + 'action_get_profile_types');
			}
		},
		'json'
	);
}



//composer functions
function showMenuEditForm(id) {
	showPopupEditForm(sParserURL+'edit/'+id)
}
function showPopupEditForm(sUrl) {
	if (!$('#aqb_popup_edit_form').length) {
        $('<div id="aqb_popup_edit_form" style="display:none;"></div>').prependTo('body');
    }

    var oPopupOptions = {
        fog: {color: '#fff', opacity: .7}
    };

	$('#aqb_popup_edit_form').load(
		sUrl,
		function() {
			$(this).dolPopup(oPopupOptions);
		}
	);
}
function saveMenuItem(oForm) {
	$('#formItemEditLoading').bx_loading();

	var sQueryString = $(oForm).formSerialize();

	$.post($(oForm).attr('action'), sQueryString, function(oData){
        $('#formItemEditLoading').bx_loading();

        $('#aqb_menu_item_edit').bx_message_box(oData.message, oData.timer, function(){
			$('#aqb_popup_edit_form').dolPopupHide();
        })
    }, 'json');
}
function refreshPagesBuilder(sURL, sPage) {
	$('#page_builder_zone').html('<center><img style="margin-top: 16px;" alt="Loading..." src="' + aDolImages['loading'] + '" /></center>');
	$('#page_builder_zone').load(sURL + sPage);
}
function refreshSearchLayout(sURL, iProfileType) {
	$('#search_layout_zone').html('<center><img style="margin-top: 16px;" alt="Loading..." src="' + aDolImages['loading'] + '" /></center>');
	$('#search_layout_zone').load(sURL + iProfileType + '/1');
}
function showPageEditForm(id) {
	showPopupEditForm(sParserURL+'edit/'+id)
}
function changeSearchFieldLayout(oForm) {
	var sQueryString = $(oForm).formSerialize();

	$('#search_layout_zone').html('<center><img style="margin-top: 16px;" alt="Loading..." src="' + aDolImages['loading'] + '" /></center>');

	$.post($(oForm).attr('action'), sQueryString, function(sData){
        $('#search_layout_zone').html(sData);
    });
}