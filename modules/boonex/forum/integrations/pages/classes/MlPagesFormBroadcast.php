<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.modloaded.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@modloaded.com
***************************************************************************/

bx_import('BxDolTwigFormBroadcast');

class MlPagesFormBroadcast extends BxDolTwigFormBroadcast {

    function MlPagesFormBroadcast () {
        parent::BxDolTwigFormBroadcast (_t('_ml_pages_form_caption_broadcast_title'), _t('_ml_pages_form_err_broadcast_title'), _t('_ml_pages_form_caption_broadcast_message'), _t('_ml_pages_form_err_broadcast_message'));
    }
}

?>
