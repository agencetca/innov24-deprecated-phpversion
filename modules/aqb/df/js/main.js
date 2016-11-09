function AqbDFCreateTopList(sAdminUrl) {
	var sNewList = prompt( 'Please enter name of new list' );

	if( sNewList == null )
		return false;

	sNewList = $.trim( sNewList );

	if( !sNewList.length ) {
		alert( 'You should enter correct name' );
		return false;
	}

    var win = 'width=670,height=600,left=100,top=100,copyhistory=no,directories=no,menubar=no,location=no,resizable=yes,scrollbars=yes';
    window.open(sAdminUrl + 'preValues.php?list=' + encodeURIComponent( sNewList ) + '&popup=1', 'prevalues', win);
}
function AqbDFCreateDependentList(sUrl) {
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
function AqbDFEditTopList(sAdminUrl, sPage) {
    var win = 'width=670,height=600,left=100,top=100,copyhistory=no,directories=no,menubar=no,location=no,resizable=yes,scrollbars=yes';
    var w = window.open(sAdminUrl + 'preValues.php?list=' + encodeURIComponent( sPage ) + '&popup=1', 'prevalues', win);
}
function AqbDFEditDependentList(sBaseUrl, sList, sParent) {
    var win = 'width=620,height=600,left=100,top=100,copyhistory=no,directories=no,menubar=no,location=no,resizable=yes,scrollbars=yes';
    var w = window.open(sBaseUrl + 'action_edit/' + encodeURIComponent( sList ) + '/' + encodeURIComponent( sParent ), 'prevalues', win);
}
function AqbDFRefresh(sUrl) {
	$('#aqb_df_wrapper').load(sUrl);
}
function AqbDFNewDependentListStart(oForm, sHomeUrl) {
	$('#formItemEditLoading').bx_loading();

	var sQueryString = $(oForm).formSerialize();

	$.post($(oForm).attr('action'), sQueryString, function(oData){
        $('#formItemEditLoading').bx_loading();
        if (oData.result) {
        	$('#aqb_popup_edit_form').dolPopupHide();
        	var win = 'width=670,height=600,left=100,top=100,copyhistory=no,directories=no,menubar=no,location=no,resizable=yes,scrollbars=yes';
    		window.open(oData.return_code, 'prevalues', win);
        }
        else $('#aqb_popup_edit_form').html(oData.return_code);
    }, 'json');
}