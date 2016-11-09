function AQBMlsSubmitChanges(oForm) {
	var sQueryString = $(oForm).formSerialize();

	$('#aqb_mls_acls_list').html('<center><img style="margin-top: 16px;" alt="Loading..." src="' + aDolImages['loading'] + '" /></center>');

	$.post($(oForm).attr('action'), sQueryString, function(sData){
        $('#aqb_mls_acls_list').html(sData);
    });
}