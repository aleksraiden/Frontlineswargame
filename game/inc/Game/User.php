<?php
/**
 * Работа с компаниями в игре
 */

 class Game_User
 {
 	// test
	// http://game2.cmgame-dev.com/game/test?PHPSESSID=1d4c482904d85e059dbd43ba5b23b8c8&ukey=13b9485e12a733edb6b9c3692e69646b
	static public function updateUserFriendsList($data = Array(), $url = null, $isTest = false)
	{
		$config = Zend_Registry::get('config');
		$db = Zend_Registry::get('db');
		$session = Zend_Registry::get('session');
		
		//получаем список id игроков (id в системе контакта)
		$myId = $session->user['uid']; //uid в системе контакта текущего юзера 
		$dataFriends = explode(',', $data['friendids']);
		
		$sql = 'SELECT id FROM user_friends_tbl WHERE uid = '.$db->quote($myId).' AND friend_uid NOT IN ('.implode(',', $dataFriends).') ';
		$res = $db->fetchAll($sql);
		
		if (!empty($res))
		{
			//var_dump($res); exit(1);
			$_tmp = Array();
			
			foreach($res as $x)
			{
				$_tmp[] = $x['id'];
			}
			
			
			$sql = 'DELETE FROM user_friends_tbl WHERE id IN ('.implode(',', $_tmp).') ';
			$db->query($sql);		
		}
		
		//теперь обработаем разницу 
		$sql = 'SELECT friend_uid FROM user_friends_tbl WHERE  uid = '.$db->quote($myId).' ';
		$res = $db->fetchAll($sql);
		
		$_friends = Array();
		
		foreach($res as $x)
		{
			$_friends[] = $x['friend_uid'];	
		}
		
		$newFriends = array_diff($dataFriends, $_friends);
		
		if (!empty($newFriends))
		{
			//этих людей добавить как друзья 
			foreach($newFriends as $x)
			{
				$sql = 'INSERT INTO user_friends_tbl SET uid = '.$db->quote($myId).', friend_uid = '.$db->quote($x).', relations_from = UNIX_TIMESTAMP(), relation_type = \'love\' ';
				
				$db->query($sql);
			}
		}
		
		
		//вернуть наверное аватарки всех друзей? 
		
		
		return Array('status' => 'OK', 'data' => Game_User::_getAllFriends($myId));			
	}
	
	
	
	//выбирает профили (в сокращенном виде) друзей и их статусы
	public static function _getAllFriends($uid = null){
		$db = Zend_Registry::get('db');
		
		$sql = 'SELECT 
					user_profiles_tbl.id AS id,
					user_profiles_tbl.uid AS uid,
					name,
					screen_name,
					first_name,
					last_name,
					photo_medium_rec,
					activity,
					last_seen_time,
					user_online_sessions_tbl.online_status 
				FROM 
					user_profiles_tbl,
					user_friends_tbl,
					user_online_sessions_tbl
				WHERE
					user_friends_tbl.uid = '.$db->quote($uid).' 
				AND
					user_profiles_tbl.uid  = user_friends_tbl.friend_uid 
				AND
					user_online_sessions_tbl.uid = user_profiles_tbl.uid 
				
				ORDER BY id DESC   ';
				
		$res = $db->fetchAll($sql);
		
		return $res;
	}
	
 }