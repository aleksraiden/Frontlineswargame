<?php
/**
 * Работа с наградами игрока
 */

 class Game_UserAwards
 {
 	
	//получает деньги юзера 
	static public function getAwards($uid = null, $onlyCounter = true)
	{
		if (empty($uid)) return false;
		
		$db = Zend_Registry::get('db');
		
		//нам надо только количество 
		if ($onlyCounter == true)
		{
			$sql = 'SELECT COUNT(id) FROM user_awards_tbl WHERE user_id = '.$db->quote($uid).' LIMIT 1';
			return intval($db->fetchOne($sql));		
		}
		else
		{
			$sql = 'SELECT 
						user_awards_tbl.award_id AS award_id,
						assigned_at,
						exclusive_award_id,
						award_title,
						award_desc,
						award_icon_img,
						award_big_img,
						award_type,
						award_is_exclusive
					FROM 
						user_awards_tbl, system_user_awards_tbl 
					WHERE
						user_awards_tbl.user_id = '.$db->quote($uid).' 
					AND
						system_user_awards_tbl.award_id = user_awards_tbl.award_id 
					ORDER BY user_awards_tbl.assigned_at DESC  ';
			$res = $db->fetchAll($sql);	

			$result = Array();
			
			foreach($res as $x)
			{
				$result[ $x['award_id'] ] = $x;
			}
			
			return $result;		
		}
		
		
	
	}
	
	
 }