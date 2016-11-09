
/* * * * Main Builder Class (Profile Fields Manager) * * * */

function BxDolPFM( aAreas ) {
	this.config = {
		areas: 0,
		parserUrl: '',
		inactiveColumns: 4,
		getAreaElem : function(id) { //function to get the area element. made to customizability
			var $area = $( '#m' + id + ' > div.build_container' );
            return $area.length ? $area.get(0) : false;
		}
	};

	this.areas = new Array();
}



BxDolPFM.prototype.init = function() {
	//generate areas
    for (var iInd = 1; iInd <= this.config.areas; iInd ++) { //we will begin ID's from 1 !
        var area = new BxDolPFMArea(this, iInd);
        if (!area.element)
            continue;

        this.areas[this.areas.length] = area;
    }
}

BxDolPFM.prototype.getAreaByID = function( iAreaID ) {
	for( var iAreaInd = 0; iAreaInd < this.areas.length; iAreaInd ++ )
		if( this.areas[iAreaInd].id == iAreaID )
			return this.areas[iAreaInd];

	return false;
}

BxDolPFM.prototype.updateAreas = function( sText, iItemID, sNewName, iAreaID ) {
	switch( sText ) {
		case 'updateItem':
			for( var iAreaInd = 0; iAreaInd < this.areas.length; iAreaInd ++ )
				this.areas[iAreaInd].getItemOrBlockByID( iItemID ).updateName( sNewName );
		break;
	}
}


/* * * * Area Class * * * */

function BxDolPFMArea(parent, id) {
	this.id     = id;
	this.parent = parent;

    this.getElement();

    if (!this.element)
        return false;


	this.active_blocks      = new Array();
	this.inactive_blocks    = new Array();
	this.active_items       = new Array();
	this.inactive_items     = new Array();

	this.activeZoneElemID         = 'area_' + this.id + '_active'
	this.inactiveItemsZoneElemID  = 'area_' + this.id + '_items_inactive'
	this.inactiveBlocksZoneElemID = 'area_' + this.id + '_blocks_inactive'

	this.requestData();
}

BxDolPFMArea.prototype.getElement = function() {
	this.element = this.parent.config.getAreaElem(this.id);
}

BxDolPFMArea.prototype.requestData = function() {
	var oThisArea = this;
	$.getJSON(
		this.parent.config.parserUrl,
		{action: 'getArea', id: this.id},
		function(oAreaData){
			oThisArea.getData(oAreaData);
		}
	);
}

BxDolPFMArea.prototype.getData = function(oAreaData) {
	if( this.id != oAreaData.id )
		return false;

	for( var iBlockInd = 0; iBlockInd < oAreaData.active_blocks.length;   iBlockInd++ )    this.active_blocks[   this.active_blocks.length   ] = new BxDolPFMBlock( this, oAreaData.active_blocks[   iBlockInd ] );
	for( var iBlockInd = 0; iBlockInd < oAreaData.inactive_blocks.length; iBlockInd++ )    this.inactive_blocks[ this.inactive_blocks.length ] = new BxDolPFMBlock( this, oAreaData.inactive_blocks[ iBlockInd ] );
	for( var iItemInd  = 0; iItemInd  < oAreaData.active_items.length;    iItemInd ++ )    this.active_items[    this.active_items.length    ] = new BxDolPFMItem(  this, oAreaData.active_items[    iItemInd  ] );
	for( var iItemInd  = 0; iItemInd  < oAreaData.inactive_items.length;  iItemInd ++ )    this.inactive_items[  this.inactive_items.length  ] = new BxDolPFMItem(  this, oAreaData.inactive_items[  iItemInd  ] );

	this.draw();
}

BxDolPFMArea.prototype.draw = function() {
	$( this.element ).html( '' ); //clear element
    var sActiveItemsC = _t('_adm_mbuilder_active_items');
    var sInactiveItemsC = _t('_adm_mbuilder_inactive_items');
    var sInactiveBlocksC = _t('_adm_txt_pb_inactive_blocks');

	$( this.element ).append(
		'<div class="build_zone_header">'+sActiveItemsC+'</div>' +
		'<div class="blocks_cont_bord">' +
			'<div class="blocks_container" id="' + this.activeZoneElemID + '">' +
				'<div class="build_block_fake"></div>' +
			'</div>' +
		'</div>' +
		'<br style="height:20px;clear:both;" />' +
		'<div class="build_zone_header">'+sInactiveBlocksC+'</div>' +
		'<div class="blocks_cont_bord">' +
			'<div class="blocks_container" id="' + this.inactiveBlocksZoneElemID + '">' +
				'<div class="build_block_fake"></div>' +
			'</div>' +
		'</div>' +
		'<br style="height:20px;clear:both;" />' +
		'<div class="build_zone_header">'+sInactiveItemsC+'</div>' +
		'<div class="blocks_cont_bord build_block_inactive_items">' +
			'<div class="blocks_container" id="' + this.inactiveItemsZoneElemID + '">' +
				'<div class="build_inac_items_col" id="build_inac_items_area_' + this.id + '_col_1">' +
					'<div class="build_item_fake"></div>' +
				'</div>' +
				'<div class="build_inac_items_col" id="build_inac_items_area_' + this.id + '_col_2">' +
					'<div class="build_item_fake"></div>' +
				'</div>' +
				'<div class="build_inac_items_col" id="build_inac_items_area_' + this.id + '_col_3">' +
					'<div class="build_item_fake"></div>' +
				'</div>' +
				'<div class="build_inac_items_col" id="build_inac_items_area_' + this.id + '_col_4">' +
					'<div class="build_item_fake"></div>' +
				'</div>' +
				'<div class="build_inac_items_col" id="build_inac_items_area_' + this.id + '_col_5">' +
					'<div class="build_item_fake"></div>' +
				'</div>' +
			'</div>' +
		'</div>'
	);

	this.activeZoneElement         = $( '#' + this.activeZoneElemID         ).get(0);
	this.inactiveBlocksZoneElement = $( '#' + this.inactiveBlocksZoneElemID ).get(0);
	this.inactiveItemsZoneElement  = $( '#' + this.inactiveItemsZoneElemID  ).get(0);

	//draw all active blocks
	for( var iBlockInd = 0; iBlockInd < this.active_blocks.length; iBlockInd ++ ) {
		this.active_blocks[iBlockInd].draw( this.activeZoneElement );

		//draw subitems of block
		for( var iItemInd = 0; iItemInd < this.active_items.length; iItemInd ++ )
			if( this.active_items[iItemInd].block == this.active_blocks[iBlockInd].id )
				this.active_items[iItemInd].draw( this.active_blocks[iBlockInd].element );
	}

	//append all inactive blocks
	for( var iBlockInd = 0; iBlockInd < this.inactive_blocks.length; iBlockInd ++ )
		this.inactive_blocks[iBlockInd].draw( this.inactiveBlocksZoneElement );

	//append all inactive items to 5 columns
	var iColCount = 2;
	for( var iItemInd = 0; iItemInd < this.inactive_items.length; iItemInd ++ ) {
		this.inactive_items[iItemInd].draw(
			$( '#build_inac_items_area_' + this.id + '_col_' + iColCount++ ).get(0)
		);

		if( iColCount > 5 ) iColCount = 1;
	}

	$( this.activeZoneElement         ).append( '<div class="clear_both"></div>' );
	$( this.inactiveBlocksZoneElement ).append( '<div class="clear_both"></div>' );
	$( this.inactiveItemsZoneElement  ).append( '<div class="clear_both"></div>' );

	this.fixZonesWidths();
}

BxDolPFMArea.prototype.fixZonesWidths = function() {
	if( this.active_blocks.length ) {
		//fix active area
		var el = $( this.active_blocks[0].element );

		var w1 = parseInt( el.css( 'width'        ) ) | 0;
		var w2 = parseInt( el.css( 'margin-left'  ) ) | 0;
		var w3 = parseInt( el.css( 'margin-right' ) ) | 0;
		var w = ( w1 + w2 + w3 ) * ( this.active_blocks.length + 1 ) + 20;
		$( this.activeZoneElement ).parent().width( w );
	}

	if( this.inactive_blocks.length ) {
		//fix inactive area
		var el = $( this.inactive_blocks[0].element );

		var w1 = parseInt( el.css( 'width'        ) ) | 0;
		var w2 = parseInt( el.css( 'margin-left'  ) ) | 0;
		var w3 = parseInt( el.css( 'margin-right' ) ) | 0;
		var w = ( w1 + w2 + w3 ) * ( this.inactive_blocks.length + 2 ) + 20;
		$( this.inactiveBlocksZoneElement ).parent().width( w );
	}
}

BxDolPFMArea.prototype.processSaveResult = function( sResult ) {
	if( $.trim( sResult ) != 'OK' )
		alert( sResult );
}

BxDolPFMArea.prototype.getBlockByElementID = function( getID ) {
	for( var iBlockInd = 0; iBlockInd < this.active_blocks.length; iBlockInd ++ )
		if( this.active_blocks[iBlockInd].elementID == getID )
			return this.active_blocks[iBlockInd];

	for( var iBlockInd = 0; iBlockInd < this.inactive_blocks.length; iBlockInd ++ )
		if( this.inactive_blocks[iBlockInd].elementID == getID )
			return this.inactive_blocks[iBlockInd];

	return false;
}

BxDolPFMArea.prototype.getItemByElementID = function( getID ) {
	for( var iItemInd = 0; iItemInd < this.active_items.length; iItemInd ++ )
		if( this.active_items[iItemInd].elementID == getID )
			return this.active_items[iItemInd];

	for( var iItemInd = 0; iItemInd < this.inactive_items.length; iItemInd ++ )
		if( this.inactive_items[iItemInd].elementID == getID )
			return this.inactive_items[iItemInd];

	return false;
}

BxDolPFMArea.prototype.getItemOrBlockByID = function( getID ) {
	//search in active items
	for( var iItemInd = 0; iItemInd < this.active_items.length; iItemInd ++ )
		if( this.active_items[iItemInd].id == getID )
			return this.active_items[iItemInd];

	//search in inactive items
	for( var iItemInd = 0; iItemInd < this.inactive_items.length; iItemInd ++ )
		if( this.inactive_items[iItemInd].id == getID )
			return this.inactive_items[iItemInd];

	//search in active blocks
	for( var iBlockInd = 0; iBlockInd < this.active_blocks.length; iBlockInd ++ )
		if( this.active_blocks[iBlockInd].id == getID )
			return this.active_blocks[iBlockInd];

	//search in inactive blocks
	for( var iBlockInd = 0; iBlockInd < this.inactive_blocks.length; iBlockInd ++ )
		if( this.inactive_blocks[iBlockInd].id == getID )
			return this.inactive_blocks[iBlockInd];

	return false;
}

BxDolPFMArea.prototype.openFieldDialog = function( iItemID, iAreaID ) {
	$( '#fieldFormWrap' ).css({
		width: ( document.body.clientWidth + 30 ),
		height: ( ( window.innerHeight ? window.innerHeight : screen.height) + 30 ),
		left: ( this.getHorizScroll() - 30 ),
		top: ( this.getVertScroll() - 30 ),
		display: 'block'
	});

	getHtmlData( 'edit_form_cont', this.parent.config.parserUrl + '?action=loadEditForm&id=' + iItemID + '&area=' + iAreaID, function (){
        $('#edit_form_cont > div').dolPopup({
            fog: {
                color: '#fff',
                opacity: .7
            },
            closeOnOuterClick: false
        });
	});
}


BxDolPFMArea.prototype.getHorizScroll = function() {
	return (navigator.appName == "Microsoft Internet Explorer") ? document.documentElement.scrollLeft : window.pageXOffset;
}

BxDolPFMArea.prototype.getVertScroll = function() {
	return (navigator.appName == "Microsoft Internet Explorer") ? document.documentElement.scrollTop : window.pageYOffset;
}


/* * * * Block Class * * * */

function BxDolPFMBlock( parent, oBlockData ) {
	this.id        = oBlockData.id;
	this.parent    = parent;
	this.name      = oBlockData.name;
	this.elementID = 'build_block_' + this.parent.id + '_' + this.id;
}

BxDolPFMBlock.prototype.draw = function( oParentElement ) {
	var oThisBlock = this;

	$( oParentElement ).append( this.getCode() );
	$( '#' + this.elementID + ' > div.build_block_header' ).children( 'a' ).click( function(){
		oThisBlock.parent.openFieldDialog( oThisBlock.id, oThisBlock.parent.id );
	} );

	this.getElement();
}

BxDolPFMBlock.prototype.getCode = function() {
	return '<div class="build_block" id="' + this.elementID + '">' +
			'<div class="build_block_header">' +
				'<a href="javascript:void(0)">' +
					this.name +
				'</a>' +
			'</div>' +
			'<div class="build_item_fake"></div>' +
		'</div>';
}

BxDolPFMBlock.prototype.getElement = function() {
	this.element = $( '#' + this.elementID ).get(0);
}

BxDolPFMBlock.prototype.updateName = function( sNewName ) {
	$( this.element ).children( 'div.build_block_header' ).children( 'a' ).html( sNewName );
}

/* * * * Item Class * * * */

function BxDolPFMItem( parent, oItemData ) {
	this.id        = oItemData.id;
	this.parent    = parent;
	this.name      = oItemData.name;
	this.block     = oItemData.block;

	this.elementID = 'build_item_' + this.parent.id + '_' + this.id;
}

BxDolPFMItem.prototype.draw = function( oParentElement ) {
	var oThisItem = this;

	$( oParentElement ).append( this.getCode() );
	$( '#' + this.elementID ).children( 'a' ).click( function(){
		oThisItem.parent.openFieldDialog( oThisItem.id, oThisItem.parent.id );
	} );

	this.getElement();
}

BxDolPFMItem.prototype.getCode = function( oParentElement ) {
	return '<div class="build_item_active" id="' + this.elementID + '">' +
		'<a href="javascript:void(0)">' +
			this.name +
		'</a>' +
	'</div>';
}
BxDolPFMItem.prototype.getElement = function() {
	this.element = $( '#' + this.elementID ).get(0);
}

BxDolPFMItem.prototype.updateName = function( sNewName ) {
	$( this.element ).children( 'a' ).html( sNewName );
}

/* * * * Non-class functions * * * */



function hideEditForm() {
	$('#edit_form_cont > div').dolPopupHide();
}

function savePFItem(oForm) {
	$('#formItemEditLoading').bx_loading();

	var sQueryString = $(oForm).formSerialize();

	$.post(oPFM.config.parserUrl, sQueryString, function(oData){
        $('#formItemEditLoading').bx_loading();

        $('#aqb_pf_item_edit').bx_message_box(oData.message, oData.timer, function(){
			hideEditForm();
        })
    }, 'json');
}
