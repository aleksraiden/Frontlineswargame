<?php
/**
 * Работа с компаниями в игре
 */

 class Game_Test
 {
 	// test
	// http://game2.cmgame-dev.com/game/test?PHPSESSID=1d4c482904d85e059dbd43ba5b23b8c8&ukey=13b9485e12a733edb6b9c3692e69646b
	static public function testHandler($data = Array(), $url = null, $isTest = false)
	{
		$config = Zend_Registry::get('config');
		$VK = new vkapi($config['VK']['api_id'], $config['VK']['secret_key']);
		
		
		echo '<pre>';
		var_dump($data);
		var_dump($VK);
		
		var_dump( $VK->api('isAppUser', array('uid'=> '8244243')) );
		
		
		
		
	
		$_st = microtime(true);
		var_dump( $VK->api('users.get', array(
			'uids'=>'8244243,15863088,12447371', 
			'fields' => 'uid,first_name,last_name,screen_name,sex,bdate,timezone,photo,photo_medium,photo_big,has_mobile,online,activity,last_seen,photo_medium_rec')) );
		
		echo "\n\n\nTotalTime: " . round((microtime(true) - $_st), 4) . " sec.";
		exit(1);
		
		
		/*
		if ((!isset($data['ukey'])) || (!isset($data['ukey'])))
			return Array('status' => 'FAILURE', 'error' => 'Недостаточно параметров');
			
		$db = Zend_Registry::get('db');
		
		$sql = 'SELECT * FROM user_accounts_tbl WHERE 1 = 1 LIMIT 1';
		$res = $db->fetchAll($sql);
		
		$redis = Zend_Registry::get('redis');
		
		if (empty($res))
			return Array('status' => 'FAILURE', 'error' => 'Некорректный идентификатор игры или недопустимый статус');
		*/		
		return Array('status' => 'OK', 'data' => $res, 'redis' => $redis);			
	}
	
 }