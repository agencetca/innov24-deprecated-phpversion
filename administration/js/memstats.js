/***************************************************************************
* Date				: Jun 06, 2011
* Copywrite			: (c) 2011 by kazatzo
*
* Product Name		: Member Statistics
* Product Version	: 1.0
*
* IMPORTANT: This is a commercial product made by kazatzo
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from kazatzo
*
***************************************************************************/

function BxManageProfiles(oOptions) 
{    
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oMP' : oOptions.sObjName;
    this._sViewType = oOptions.sViewType == undefined ? 'geeky' : oOptions.sViewType;
    this._sCtlType = oOptions.sCtlType == undefined ? 'qlinks' : oOptions.sCtlType;
    this._oCtlValue = {};
    this._iStart = oOptions.iStart == undefined ? 0 : parseInt(oOptions.iStart);
    this._iPerPage = oOptions.iPerPage == undefined ? 30 : parseInt(oOptions.iPerPage);
    this._sOrderBy = oOptions.sOrderBy == undefined ? '' : oOptions.sOrderBy;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'fade' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}

BxManageProfiles.prototype.changePage = function(iStart, interval, startDate, endDate) 
{
    this._iStart = iStart;
	this._oCtlValue['interval'] = interval;
	this._oCtlValue['start_stats'] = startDate;
	this._oCtlValue['end_stats'] = endDate;
    this.getStats();
};

BxManageProfiles.prototype.changeOrder = function(oSelect, interval, startDate, endDate) 
{
    this._sOrderBy = oSelect.value;
    this._oCtlValue['interval'] = interval;
	this._oCtlValue['start_stats'] = startDate;
	this._oCtlValue['end_stats'] = endDate;
	this.getStats();

};
BxManageProfiles.prototype.changePerPage = function(oSelect, interval, startDate, endDate) 
{
    this._iPerPage = parseInt(oSelect.value);
	this._oCtlValue['interval'] = interval;
	this._oCtlValue['start_stats'] = startDate;
	this._oCtlValue['end_stats'] = endDate;
    this.getStats();
};

BxManageProfiles.prototype.getStats = function(onSuccess) 
{
    var $this = this;
    
    if (onSuccess == undefined)
        onSuccess = function(){};

    $('#adm-mp-stats-loading').bx_loading();
    
    var oOptions = {
        action: 'get_stats', 
        view_type: this._sViewType, 
        view_start: this._iStart, 
        view_per_page: this._iPerPage, 
        view_order: this._sOrderBy, 
        ctl_type: this._sCtlType,
    };

    oOptions['ctl_value[]'] = new Array();
    $.each(this._oCtlValue, function(sKey, sValue) 
	{
        oOptions['ctl_value[]'].push(sKey + '=' + sValue);
    });
    
    $.post(
        this._sActionsUrl,
        oOptions,
        function(oResult) 
		{
            $('#adm-mp-stats-loading').bx_loading();
            $('#adm-mp-stats-form > .adm-mp-stats-wrapper:visible').bx_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() 
			{
                $('#adm-mp-stats-geeky').html(oResult.content).bx_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
            });
            
            onSuccess();
        },
        'json'
    );
};