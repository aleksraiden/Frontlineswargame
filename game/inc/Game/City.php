<?php
/**
 * Работа с компаниями в игре
 */

 class Game_City
 {
 	/**
		Выборка всей информации по городу игрока 
	
	**/
	static public function loadMaps($p = Array(), $url = null, $isTest = false)
	{
		$db = Zend_Registry::get('db');
		
		$sql = 'SELECT city_id, city_name, owner_id, city_regions, city_level, city_buildings, city_style, 
			status, updated_at  FROM user_city_tbl WHERE city_id = '.$db->quote($p['cityid']).' LIMIT 1';
		$city = $db->fetchRow($sql);
		
		$city['regions'] = Array();
		
		if ($city['status'] == 'open')
		{
			$sql = 'SELECT 
						user_city_regions_tbl.id AS region_id, system_city_region_tbl.region_id AS region_code, status, region_level, opened_at, updated_at,
						region_name, region_desc, region_desc_short, region_type, region_background_img, region_icon_img, 
						region_buildings_place, region_base_population, region_base_profit
					FROM
						user_city_regions_tbl, system_city_region_tbl 
					WHERE
						user_city_regions_tbl.city_id = '.$db->quote($city['city_id']).' AND
						system_city_region_tbl.region_id = user_city_regions_tbl.region_id	';
						
			$city['regions'] = Array();
			
			$res = $db->fetchAll($sql);

			foreach($res as $x)
			{
				$city['regions'][ $x['region_id'] ] = $x;
			}

			//теперь добавим служебный район 
			$city['regions'][ 0 ] = Array(
				'region_id' => 0,
				'status' => 'open',
				'region_name' => 'Маркет',
				'region_desc_short' => 'Маркет. Расширяй империю, покупая новые районы',
				'region_type' => 'market',
				'region_background_img' => 'region_00_market.jpg',
				'region_icon_img' => ''
			);
		}
		
		return Array('status' => 'OK', 'data' => $city);			
	}
	
	
	/**		
		загрузка данных для маркета районов 
	**/
	static public function loadCityRegionMarket($p = Array(), $url = null, $isTest = false)
	{
		$db = Zend_Registry::get('db');
	
		$sql = 'SELECT 
					region_id, region_name, region_desc, region_desc_short, region_type, region_open_cost, region_cost_type, region_background_img, region_icon_img, 
					region_buildings_place, region_base_population, region_base_profit
				FROM  system_city_region_tbl WHERE region_status = \'open\' ';
		$res = $db->fetchAll($sql);
		
		return Array('status' => 'OK', 'data' => $res);
	}
	
	
 }