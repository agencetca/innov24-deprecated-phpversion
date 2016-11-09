<?
bx_import('BxDolForm');

class AqbPointsFormCheckerHelper extends BxDolFormCheckerHelper{
	function checkInterval($v, $a, $b){
		if ((int)$a < 0 || (int)$b < 0) return false;
		if ((int)$a >= (int)$b) return false;
		
		if (!isset($_POST['id'])){ 
			$iRet = $GLOBALS['MySQL'] -> getOne("SELECT COUNT(*) FROM `aqb_points_levels` WHERE (`min` <= '{$a}' AND `max` >= '{$a}') OR (`min` <= '{$b}' AND `max` >= '{$b}') OR (`min` >= '{$a}' AND `max` <= '{$b}') LIMIT 1");	
			if ($iRet) return false;
		}
		
		return true;
	}	
}

?>