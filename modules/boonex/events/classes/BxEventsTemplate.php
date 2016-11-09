<?
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
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
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import ('BxDolTwigTemplate');
/*
 * Events module View
 */
class BxEventsTemplate extends BxDolTwigTemplate {
    
	/**
	 * Constructor
	 */
	function BxEventsTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
        $this->_iPageIndex = 300;
    }

    function unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxEventsModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'bx_events_unit');
            return $this->parseHtmlByName('browse_unit_private', $aVars);
        }

        $sImage = '';
        if ($aData['PrimPhoto']) {
            $a = array ('ID' => $aData['ResponsibleID'], 'Avatar' => $aData['PrimPhoto']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

        $aVars = array (            
            'id' => $aData['ID'],
            'thumb_url' => $sImage ? $sImage : $this->getIconUrl('no-photo.png'),
            'event_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['EntryUri'],
            'event_title' => $aData['Title'],
            'event_start' => defineTimeInterval($aData['EventStart']),
            'author' => $aData['ResponsibleID'] ? $aData['NickName'] : _t('_bx_events_admin'),
            'author_url' => $aData['ResponsibleID'] ? getProfileLink($aData['ResponsibleID']) : 'javascript:void(0);',
            'author_title' => _t('_From'),
            'spacer' => getTemplateIcon('spacer.gif'),
            'participants' => $aData['FansCount'],
            'country_city' => _t($GLOBALS['aPreValues']['Country'][$aData['Country']]['LKey']) . (trim($aData['City']) ? ', '.$aData['City'] : ''),
			//On construit l'objet � afficher
			'type' =>_t( $GLOBALS['aPreValues']['EventType'][$aData['Type']]['LKey']),
			//'Deadline' => defineTimeInterval($aData['Deadline']),
        );        

        $aVars['rate'] = $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['ID'], $aData['Rate']) : '&#160;';

        $aVars = array_merge ($aVars, $aData);
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    // ======================= ppage compose block functions 
    
    function blockInfo (&$aEvent) {

        $aAuthor = getProfileInfo($aEvent['ResponsibleID']);

        $aVars = array (
            'author_thumb' => get_member_thumbnail($aAuthor['ID'], 'none'),
            'date' => getLocaleDate($aEvent['Date'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aEvent['Date']),
            'cats' => $this->parseCategories($aEvent['Categories']),
            'tags' => $this->parseTags($aEvent['Tags']),
            'country_city' => '<a href="' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/country/' . strtolower($aEvent['Country']) . '">' . _t($GLOBALS['aPreValues']['Country'][$aEvent['Country']]['LKey']) . '</a>' . (trim($aEvent['City']) ? ', '.$aEvent['City'] : ''),
            'flag_image' => genFlag($aEvent['Country']),
            'fields' => $this->blockFields($aEvent),
            'author_username' => $aAuthor ? $aAuthor['NickName'] : _t('_bx_events_admin'),
            'author_url' => $aAuthor ? getProfileLink($aAuthor['ID']) : 'javascript:void(0)',
        );
        return $this->parseHtmlByName('block_info', $aVars);
    }

    function blockDesc (&$aEvent) {
	$fin = strlen($aEvent['Title']);
	$coupe1 = 57;
	$coupe2= 114;
	$titre_part1=substr($aEvent['Title'],0,$coupe1);
	$titre_part2=substr($aEvent['Title'],$coupe1,$coupe1);
	$titre_part3=substr($aEvent['Title'],$coupe2,$coupe1);
	//echo strlen($aEvent['Title']); Voir la longueur de la chaine de description
		if ($fin>57){
		$aVars = array (
		'description' => '<h1><font color="#FFBEA3">'._t($GLOBALS['aPreValues']['EventType'][$aEvent['Type']]['LKey']).'</font></h1><h2><font color="#FF0000">'.$titre_part1.'<br>'.$titre_part2.'<br>'.$titre_part3.'</font></h3><p>'.$aEvent['Description'].'',);
		return $this->parseHtmlByName('block_description', $aVars);
    }
		else{
		$aVars = array (
		'description' => '<h1><font color="#FFBEA3">'._t($GLOBALS['aPreValues']['EventType'][$aEvent['Type']]['LKey']).'</font></h1><h2><font color="#FF0000">'.$aEvent['Title'].'</font></h3><p>'.$aEvent['Description'].'',);
		return $this->parseHtmlByName('block_description', $aVars);
    }}

    function blockFields (&$aEvent) {
        $sRet = '<table class="bx_events_fields">';
        bx_events_import ('FormAdd');
        $oForm = new BxEventsFormAdd ($GLOBALS['oBxEventsModule'], $this->_iProfileId);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
            $sRet .= '<tr><td class="bx_events_field_name" valign="top">' . $a['caption'] . '<td><td class="bx_events_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display'])))
                $sRet .= call_user_func_array(array($this, $a['display']), array($aEvent[$k]));
			//On affiche la langage key
			else if (0 == strcasecmp($k, 'type'))
                $sRet .= _t($GLOBALS['aPreValues']['EventType'][$aEvent[$k]]['LKey']);
            else
                $sRet .= $aEvent[$k];
            $sRet .= '<td></tr>';
        }
        $sRet .= '</table>';
        return $sRet;
    }

    // ======================= output display filters functions

    function filterDate ($i) {
        return getLocaleDate($i, BX_DOL_LOCALE_DATE) . ' ('.defineTimeInterval($i) . ')';
    }
}

?>
