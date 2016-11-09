$(function() {
	$( ".deanodate" ).datepicker({
		dateFormat: 'yy-mm-dd',
		//showButtonPanel: true,
		changeMonth: true,
		changeYear: true,
        defaultDate: -8030,
        yearRange: ($(this).attr('min') ? $(this).attr('min') : '1900') + ':' + ($(this).attr('max') ? $(this).attr('max') : '2100') 
	});
});

$(function() {
	$( ".deanodatetime" ).dynDateTime({
        ifFormat: '%Y-%m-%d %H:%M:%S',
        showsTime: true
	});
});
