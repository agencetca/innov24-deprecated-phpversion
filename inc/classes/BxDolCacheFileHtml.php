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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolCacheFile.php');

class BxDolCacheFileHtml extends BxDolCacheFile {

	/**
	 * constructor
	 */
	function BxDolCacheFileHtml() {
	    parent::BxDolCacheFile();
	}

	/**
	 * Get all data from the cache file.
	 *
	 * @param string $sKey - file name
     * @param int $iTTL - time to live
	 * @return the data is got from cache.
	 */
	function getData($sKey, $iTTL = false) {

	    if(!file_exists($this->sPath . $sKey))
	    	return null;

        if ($iTTL > 0 && $this->_removeFileIfTtlExpired ($this->sPath . $sKey, $iTTL))
            return null;

		return file_get_contents($this->sPath . $sKey);
	}

	/**
	 * Save all data in cache file.
	 *
	 * @param string $sKey - file name
	 * @param mixed $mixedData - the data to be cached in the file
     * @param int $iTTL - time to live
	 * @return boolean result of operation.
	 */
	function setData($sKey, $mixedData, $iTTL = false) {
	    if(file_exists($this->sPath . $sKey) && !is_writable($this->sPath . $sKey))
	       return false;

	    if(!($rHandler = fopen($this->sPath . $sKey, 'w'))) 
	       return false;

        fwrite($rHandler, $mixedData);
        fclose($rHandler);
        @chmod($this->sPath . $sKey, 0666);

        return true;
	}
}
