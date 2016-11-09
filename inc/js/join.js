function doShowHideSecondProfile( sShow, eForm ) {
    if( sShow == 'yes' ) {
        $( '.hidable').show();
        tinyMCE.execCommand('mceRemoveControl', false, 'DescriptionMe[1]');
        tinyMCE.execCommand('mceAddControl', false, 'DescriptionMe[1]');
    } else {
        $( '.hidable').hide();
    }
}

function validateJoinForm( eForm ) {
    if( !eForm )
        return false;
    
    hideJoinFormErrors( eForm );
    
    $(eForm).ajaxSubmit( {
        iframe: false, // force no iframe mode
        beforeSerialize: function() {
            if (window.tinyMCE)
                tinyMCE.triggerSave();
            return true;
        },
        success: function(sResponce) {
            try {
                var aErrors = eval(sResponce);
            } catch(e) {
                return false;
            }
            
            doShowJoinErrors( aErrors, eForm );
        }
    } );
    
    return false;
}

function hideJoinFormErrors( eForm ) {
    //$( 'img.warn', eForm ).hide();
    $( '.error', eForm ).removeClass( 'error' );
}

function doShowJoinErrors( aErrors, eForm ) {
    if( !aErrors || !eForm )
        return false;
    
    var bHaveErrors = false;
    
    for( var iInd = 0; iInd < aErrors.length; iInd ++ ) {
        var aErrorsInd = aErrors[iInd];
        for( var sField in aErrorsInd ) {
            var sError = aErrorsInd[ sField ];
            bHaveErrors = true;
            
            doShowError( eForm, sField, iInd, sError );
        }
    }
    
    if( bHaveErrors )
        doShowError( eForm, 'do_submit', 0, _t('_Errors in join form') );
    else
        eForm.submit();
}

function doShowError( eForm, sField, iInd, sError ) {
    var $Field = $( "[name='" + sField + "']", eForm ); // single (system) field
    if( !$Field.length ) // couple field
        $Field = $( "[name='" + sField + '[' + iInd + ']' + "']", eForm );
    if( !$Field.length ) // couple multi-select
        $Field = $( "[name='" + sField + '[' + iInd + '][]' + "']", eForm );
    if( !$Field.length ) // couple range (two fields)
        $Field = $( "[name='" + sField + '[' + iInd + '][0]' + "'],[name='" + sField + '[' + iInd + '][1]' + "']", eForm );
    
    //alert( sField + ' ' + $Field.length );
    
    $Field.parents('td:first').addClass( 'error' );
    
    $Field
    .parents('td:first')
        .addClass( 'error' )
        .children( 'img.warn' )
            .attr('float_info', sError)
            //.show()
            ;
}
/*---------------------------------- join_validation ----------------------------------[Start]*/
$(document).ready(function() {
    if (!$('#float_info').length) {
        $('body').prepend(
            $('<div id="float_info"></div>').css({
                display: 'none',
                position: 'absolute',
                zIndex: 1010
            })
        );
    }
    var $tip = $('#float_info');

    $('.input_wrapper').each(function() {
		var $oInfo = $(this).parent().find('img.info');
		
		if (typeof($oInfo.attr('float_info')) != 'undefined') {
			$(this).find('*').focus(function() {
				var p = $oInfo.offset();
			
				$tip.removeClass('val_error');
				showInfo($tip, $oInfo, p);
			});
			var $inp = $(this).find('input');
			if (typeof($inp.attr('name')) != 'undefined') {
				$inp.blur(function() {
					$inp.parent().removeClass('val_selected');
					_validateJoinForm($(this));
				});
				$inp.filter('.form_input_text, .form_input_password').focus(function() {
					$inp.parent().addClass('val_selected');
				});
			}
		}
    });

	function _validateJoinForm(eElem) {
		hideJoinFormErrors($('#join_form'));
		bx_loading('join_form', true);
		$('#join_form').ajaxSubmit({
			iframe: false,
			data: {v_field: eElem.attr('name')},
			success: function(sResponce) {
				bx_loading('join_form');
				var $oForm = $('#join_form');
				try {
					var aErrors = eval(sResponce);
					if (aErrors == '')
						$oForm = false;
				} catch(e) {
					return false;
				}
				doShowJoinErrors(aErrors, $oForm);
				
				if ($oForm) {
					var $oError = $('img.warn', eElem.parents('td:first')).attr('float_info').length > 1 ? $('img.warn', eElem.parents('td:first')) : eElem.parents('tr:first').prev().find('img.warn');
					var p = $oError.offset();
		
					$tip.addClass('val_error');
					showInfo($tip, $oError, p);
				}
			}
		});
		return false;
	}
	
	function showInfo(obj, info, p) {
		obj.css({
			left: p.left + 20,
			top:  p.top - 3
		}).html(info.attr('float_info')).fadeIn(300);	
	}
});
/*---------------------------------- join_validation ----------------------------------[End]*/