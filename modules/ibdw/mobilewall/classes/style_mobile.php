<style>
.bx-sys-mobile-padding {
    min-width: 285px;
    padding: 0;
}
.bx-sys-mobile-box-bg {
    background: none repeat scroll 0 0 transparent;
}
.bx-sys-mobile-border {
    border: medium none;
}
.bx-sys-mobile-padding2 {
}
.azione {
    background: none repeat scroll 0 0 #2A2A2A;
    border: 4px solid #242424;
    margin-top: 4px;
    padding: 10px;
    position: relative;
	overflow: hidden;
}
.mainheader {
    width: 100%;
}
.avatarleft {
    float: left;
    padding: 1px;
    width: 32px;
}
.textright {
    float: left;
    margin-left: 10px;
    width: 75%;
}
.main_index {
    width: 100%;
}
.index_1 {
    float: left;
    height: 30px;
    width: 46px;
}
.index_2 {
    float: left;
    font-size: 14px;
}
.index_3 {
    float: right;
}
.footeraction {
    color: #999999;
    font-size: 11px;
    margin-left: 44px;
    margin-top: 0;
}
.clear {
    clear: both;
}
.commentostyle {
    background: none repeat scroll 0 0 #DDDDDD;
    display: none;
    height: 100%;
    left: 1px;
    position: absolute;
    top: 1px;
    width: 100%;
}
.commentostyle input {
    border: 1px solid #666666;
    height: 45px;
    margin-left: 10px;
    width: 85%;
}
.commentostyle h2 {
    color: #333333;
    margin: 9px;
    padding: 0;
    text-transform: uppercase;
}
#subcomm {
    float: right;
    width: 10%;
}
#single_comment {
    background: none repeat scroll 0 0 #DDDDDD;
    border-radius: 10px 10px 10px 10px;
    color: #333333;
    margin-top: 5px;
    padding: 1px;
}
#single_comment img {
    float: left;
    margin-left: 10px;
    margin-right: 10px;
    margin-top: 3px;
    padding: 1px;
}
#single_comment h2 {
    color: #000000;
    font-size: 12px;
    font-weight: bold;
    margin: 0;
    padding: 0;
    width: 85%;
}
#single_comment h2 a {
    color: #000000;
}
#single_comment p {
    font-size: 11px;
    margin: 0;
    padding: 0;
    width: 85%;
}
#single_comment span {
    color: #666666;
    font-size: 11px;
}
.textright h3 {
    float: left;
    font-size: 14px;
    font-weight: bold;
    margin: 0;
    padding: 0;
}
.textright span {
    color: #999999;
}
.textright {
    font-size: 14px;
}
.textright p {
    font-size: 11px;
    margin: 5px 0 0;
    padding: 0;
}
.commentonascosto {
    display: none;
}
.fum_commenti {
    background: url("<?php echo $urlsite;?>modules/ibdw/mobilewall/templates/uni/images/comment.png") no-repeat scroll 3px 3px #666666;
    border-radius: 5px 5px 5px 5px;
    float: left;
    margin-right: 5px;
    margin-top: 8px;
    padding: 4px 7px 4px 23px;
}
.fum_like {
    background: url("<?php echo $urlsite;?>modules/ibdw/mobilewall/templates/uni/images/ilike.png") no-repeat scroll 3px 3px #666666;
    border-radius: 6px 6px 6px 6px;
    float: left;
    margin: 8px 0 5px;
    padding: 4px 7px 4px 23px;
}
#azionilaterali {
    background: url("<?php echo $urlsite;?>modules/ibdw/mobilewall/templates/uni/images/plus.png") no-repeat scroll 0 0 transparent;
    border-radius: 5px 5px 5px 5px;
    height: 16px;
    margin-top: -8px;
    padding: 2px;
    position: absolute;
    right: 10px;
    top: 20px;
    width: 16px;
}
#personal {
    background: none repeat scroll 0 0 #222222;
    border: 4px solid #000000;
    height: 30px;
    padding: 10px;
}
#fade_action {
    display: none;
    left: 15px;
    position: absolute;
    top: 33px;
    z-index: 9999;
}
#multi_action {
    background: url("<?php echo $urlsite;?>modules/ibdw/mobilewall/templates/uni/images/appl.png") no-repeat scroll center center #444444;
    border: 1px solid #999999;
    border-radius: 4px 4px 4px 4px;
    float: left;
    height: 16px;
    padding: 5px;
    width: 16px;
}
#sub_wall {
    background: url("<?php echo $urlsite;?>modules/ibdw/mobilewall/templates/uni/images/submit.png") no-repeat scroll center center #444444;
    border: 1px solid #FFFFFF;
    border-radius: 4px 4px 4px 4px;
    float: left;
    height: 16px;
    margin-left: 5px;
    padding: 5px;
    width: 16px;
}
#listalike {
    background-color: #EDEFF4;
    border-radius: 8px 8px 8px 8px;
    color: #333333;
    font-size: 11px;
    margin-top: 5px;
    padding: 3px;
    display:none;
}
#listalike img {
    left: 9px;
    position: relative;
    top: -14px;
}
.azionilaterali_action {
    background: none repeat scroll 0 0 #999999;
    border: 4px solid #DDDDDD;
    display: none;
    height: 100%;
    left: 0;
    margin: -4px;
    min-height: 80px;
    padding-right: 30px;
    position: absolute;
    text-align: center;
    top: 0;
    z-index: 9999;
}
.bt_sub {
    background: none repeat scroll 0 0 #FFFFFF;
    border-radius: 9px 9px 9px 9px;
    color: #333333;
    font-size: 24px;
    font-weight: bold;
    margin-top: 5px;
    padding: 6px;
    text-align: center;
}
.addcommento {
    background: none repeat scroll 0 0 #FFFFFF;
    border-radius: 14px 14px 14px 14px;
    color: #333333;
    float: left;
    font-size: 14px;
    font-weight: bold;
    height: 30px;
    line-height: 20px;
    margin: 7px;
    padding: 5px;
    position: relative;
    width: 100%;
}
.addcommento img {
    left: 5%;
    margin-top: -12px;
    position: absolute;
    top: 50%;
}
.addcommento p {
    font-size: 18px;
    margin: 5px 0 0;
    padding: 0;
    text-transform: uppercase;
}
.closeaction {
    float: right;
    font-weight: bold;
    position: absolute;
    right: 10px;
    top: 10px;
    z-index: 9999;
}
.commento_inserimento {
    border: 2px solid #333333;
    height: 138px;
    width: 100%;
}
.imgbox img {
    border: 1px solid #666666;
    margin: 6px 0;
    padding: 1px;
    width: 70px;
}
.bt_sub {
    font-weight: bold;
    margin: 6px auto 0;
    text-align: center;
    width: 95%;
}
#listalike a {
    color: #333333;
    font-weight: bold;
}
.contmain {
  width:100%;
}
#textarea_wall input {
    border: 1px solid #DDDDDD;
    border-radius: 3px 3px 3px 3px;
    float: left;
    height: 24px;
    margin-left: 5px;
    width: 72%;
}
.action_row {
    background: none repeat scroll 0 0 #000000;
    border: 2px solid #FFFFFF;
    font-size: 14px;
    font-weight: bold;
    margin: 4px;
    padding: 12px;
    text-align: center;
    text-transform: uppercase;
}
#altrenews {
    background: none repeat scroll 0 0 #2A2A2A;
    border: 4px solid #242424;
    font-size: 16px;
    margin-top: 10px;
    padding: 10px;
}
#altrenews a {
    font-weight: bold;
    text-decoration: none;
}
.imgboxs {
    background-position: center center !important;
    border: 1px solid #999999;
    float: left;
    height: 80px;
    margin: 1px;
    padding: 1px;
    width: 80px;
    margin-top:8px;
}
.inserimento_commento {
    background: none repeat scroll 0 0 #999999;
    display: none;
    height: 200px;
    left: 1px;
    position: absolute;
    top: 1px;
    width: 100%;
    height:100%;
    z-index: 9999;
}
.elimina_style_commento {
    color: #333333;
    float: right;
    font-weight: bold;
    margin-right: 10px;
    margin-top: -40px; }
    
.act_height {
  height:270px;
  }

.section_comment {
    margin-left: 43px; }
</style>