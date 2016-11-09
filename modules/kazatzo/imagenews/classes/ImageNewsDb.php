<?
/***************************************************************************
* Date				: Jun 17, 2011
* Copywrite			: (c) 2011 by kazatzo
*
* Product Name		: Image News
* Product Version	: 1.0
*
* IMPORTANT: This is a commercial product made by kazatzo
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from kazatzo
*
***************************************************************************/

bx_import('BxDolTextDb');

class ImageNewsDb extends BxDolTextDb {	
	function ImageNewsDb(&$oConfig) {
		parent::BxDolTextDb($oConfig);
	}
	
		/**
	 * Get entries.
	 */
	function getEntries($aParams = array()) {
		$sMethod = 'getAll';
		$sSelectClause = $sWhereClause = $sOrderClause = $sLimitClause = "";
	    switch($aParams['sample_type']) {
	        case 'id':	        
	            $sMethod = 'getRow';
	            $sWhereClause = " AND `te`.`id`='" . $aParams['id'] . "'";
	            $sOrderClause = "`te`.`when` DESC";
	            $sLimitClause = "LIMIT 1";
	            break;
	        case 'uri':
	            $sMethod = 'getRow';
	            $sWhereClause = " AND `te`.`uri`='" . $aParams['uri'] . "'";
	            $sOrderClause = "`te`.`when` DESC";
	            $sLimitClause = "LIMIT 1";
	            break;
            case 'view':
	            $sWhereClause = " AND `te`.`uri`='" . $aParams['uri'] . "' AND `te`.`status`='" . BX_TD_STATUS_ACTIVE . "'";
	            $sOrderClause = "`te`.`when` DESC";
	            $sLimitClause = "LIMIT 1";
	            break;
	        case 'search_unit':
	            $sWhereClause = " AND `te`.`uri`='" . $aParams['uri'] . "'";
	            $sOrderClause = "`te`.`when` DESC";
	            $sLimitClause = "LIMIT 1";
	            break;
	        case 'archive':
	            $sWhereClause = " AND `te`.`status`='" . BX_TD_STATUS_ACTIVE . "'";
	            $sOrderClause = "`te`.`when` DESC";
	            $sLimitClause = "LIMIT " . (int)$aParams['start'] . ', ' . (int)$aParams['count'];
	            break;
            case 'featured':
	            $sWhereClause = " AND `te`.`status`='" . BX_TD_STATUS_ACTIVE . "' AND `te`.`featured`='1'";
	            $sOrderClause = "`te`.`when` DESC";
	            $sLimitClause = "LIMIT " . (int)$aParams['start'] . ', ' . (int)$aParams['count'];
	            break;
            case 'top_rated':
	            $sWhereClause = " AND `te`.`status`='" . BX_TD_STATUS_ACTIVE . "'";
	            $sOrderClause = "`te`.`rate` DESC";
	            $sLimitClause = "LIMIT " . (int)$aParams['start'] . ', ' . (int)$aParams['count'];
	            break;
            case 'popular':
	            $sWhereClause = " AND `te`.`status`='" . BX_TD_STATUS_ACTIVE . "'";
	            $sOrderClause = "`te`.`view_count` DESC";
	            $sLimitClause = "LIMIT " . (int)$aParams['start'] . ', ' . (int)$aParams['count'];
	            break;	        
	        case 'admin':
	            $sWhereClause = !empty($aParams['filter_value']) ? " AND (`caption` LIKE '%" . $aParams['filter_value'] . "%' OR `content` LIKE '%" . $aParams['filter_value'] . "%' OR `tags` LIKE '%" . $aParams['filter_value'] . "%')" : "";
	            $sOrderClause = "`te`.`when` DESC";
	            $sLimitClause = "LIMIT " . $aParams['start'] . ', ' . $aParams['count'];
	            break;
            case 'all':
	            $sWhereClause = " AND `te`.`status`='" . BX_TD_STATUS_ACTIVE . "'";
	            $sOrderClause = "`te`.`when` DESC";
	            break;	            
	    }
	    $sSql = "SELECT
	               " . $sSelectClause . "
	               `te`.`id` AS `id`,
	               `te`.`caption` AS `caption`,
	               `te`.`snippet` AS `snippet`,
	               `te`.`content` AS `content`,
	               `te`.`when` AS `when_uts`,
	               DATE_FORMAT(FROM_UNIXTIME(`te`.`when`), '%Y-%m-%d %H:%i:%S') AS `when`,
	               DATE_FORMAT(FROM_UNIXTIME(`te`.`when`), '" . $this->_oConfig->getDateFormat() . "') AS `when_uf`,
	               `te`.`uri` AS `uri`,
	               `te`.`tags` AS `tags`,
	               `te`.`categories` AS `categories`,
	               `te`.`comment` AS `comment`,
	               `te`.`vote` AS `vote`,
	               `te`.`date` AS `date`,
	               `te`.`status` AS `status`,
	               `te`.`featured` AS `featured`,
				   `te`.`image` AS `image`,
	               `te`.`cmts_count` AS `cmts_count`
                FROM `" . $this->_sPrefix . "entries` AS `te`
                WHERE 1 " . $sWhereClause . "
                ORDER BY " . $sOrderClause . " " . $sLimitClause;
	    $aResult = $this->$sMethod($sSql);

	    if(!in_array($aParams['sample_type'], array('id', 'uri', 'view'))) {
	    	$iSnippetLen = $this->_oConfig->getSnippetLength();

			for($i = 0; $i < count($aResult); $i++)
				$aResult[$i]['content'] = mb_substr(str_replace(array('&nbsp;', '&lt;', '&gt;'), array(' ', '', ''), ' '), 0, $iSnippetLen);				
			}

	    return $aResult;
	}
}
?>