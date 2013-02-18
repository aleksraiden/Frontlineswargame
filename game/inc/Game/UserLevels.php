<?php
/**
 * Работа с уровнем игрока и переводом его уровней 
 */

 class Game_UserLevels
 {
 	
	//получает деньги юзера 
	static public function getLevel($uid = null)
	{
		if (empty($uid)) return false;
		
		$db = Zend_Registry::get('db');
		
		$sql = 'SELECT current_lvl, current_points, points_to_next_lvl, updated_at FROM user_levels_tbl WHERE user_id = '.$db->quote($uid).' LIMIT 1';		
		$res = $db->fetchRow($sql);
		
		if (empty($res))
		{
			return false;
		}
		else
			return $res;	
	}
	
	
 }