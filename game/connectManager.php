<?php
/**
 * Signalsy Platform Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@wheemplay.com so we can send you a copy immediately.
 *
 * @category   Signalsy
 * @package    Signalsy Core
 * @copyright  Copyright (c) 2009 AGPsource Team
 * @license    http://signalsy.com/license/ New BSD License
 */


class connectManager
{
	/**
		Return URL to api action map for JS 
		Used for RPC built top
		
		action -> [url, params:[name, type], type: json, cache:true/false/int]
	*/
	static public function buildMap($returnJson = false){
		$_map = Array();
		$_route = self::exportRouting();
	
		foreach($_route as $ns => $rt)
		{
			$_ns = strtolower($ns);
			
			if ($_ns == 'default')	$_ns = ''; //для дефолтного исключение

			//$_def = $rt['_default']; //дефолты 
			
			foreach($rt['url'] as $url => $opt)
			{
				if (empty($url)) continue;
				
				if (!array_key_exists('action', $opt))
				{
					// /game/add/item => gameAddItem 
					$tmp = explode('/', $url);
					
					if (count($tmp) == 1)
						$opt['action'] = $_ns . $tmp[0];
					else					
						$opt['action'] = $_ns . implode('', explode(' ', ucwords(implode(' ', $tmp))));
				}

				$opt = array_merge($rt['_default'], $opt);
				$_params = Array();
				
				foreach($opt['params'] as $_x)
				{
					if ($_x == 'PHPSESSID')	continue;
					
					$tmp = explode('|', $_x);
					$_params[] = $tmp[0];
				}				
				
				$url = $_ns . '/' . $url;
				
				if ($url[0] == '/')
					$url[0] = '';

				
				$_map[ $opt['action'] ]	= Array(
					'url' => trim($url), 
					'p' => $_params, 
					't' => $opt['type'],
					'c' => $opt['cache']
				);			
			}
		}
		
		if ($returnJson === false)
			return $_map;
		else
			return Zend_Json::encode( $_map );
	}
	
	
	/**
	 * Return array of URL and associate signals list
	 * @static
	 * @public
	 * @return Array 
	 */
	static public function exportRouting()
	{
		return Array(
			//специальный блок для индексного урла или для ненайденных
			'Default' => Array(
				//дефолтные настройки
				'_default' => Array(		
					'type' => 'json', //тип выводимой инфыормации, если не указан, дефолтный json, csv, html, xml, raw (произвольный)
					'cache' => false, //задает, кешировать ли весь урл, если тру - стандартное кеширования, фалсе - запрет, число - на сколько кешировать. Дефолт 30 сек.
					'params' => array(), //массив обязательных параметров для обработки, проверяются через empty
					'debug' => false, //выводить ли дебаг информацию
					'isTest' => false, //флаг, указывает на возможность тестирования этого URL
					'testParam' => array(), //массив параметр=>значение для тестирования
					'preDispatch' => false, //array('Signalsy_xRouter', 'testDispatch'), //или array('Class_Name', 'Method_Name'), //метод
					'disabledModules' => array('session', 'memcache', 'redis') //какие модули можно не инитить = кеш, базу, сессию и т.п.
				),
				
				'url' => Array(
					'' => array(
						'handler' => array('Default_Error', 'error_404') //непосредственно обработчик
					),
					
					'test' => array(
						'cache' => false,
						'params' => Array('user_ts|string', 'fuck', 'q|float'),
						'handler' => Array('Game_Test', 'testHandler') //непосредственно обработчик
					),					
					
					//обработчик неизвестных адресов
					'error/404' => array(
						'type' => 'html',
						'cache' => false,
						'debug' => false, 
						'handler' => array('Default_Error', 'error_404')
					),
                    //логин здесь
					'login' => array(
						'cache' => false,
						'type' => 'json',
						//'params' => Array('login_fl', 'pass_fl'),
						'handler' => array('Game_Main', 'app_LoginUlogin')
					),
                    //logout
                    'logout' => array(
                        'cache' => false,
                        'type' => 'html',
                        'handler' => array('Game_Main', 'app_Logout')
                    )
				)
		
			),
			//========================================================================================================================
			// URL начинаются с /game/
			'Game' => Array(
				//дефолтные настройки
				'_default' => Array(		
					'type' => 'json', //тип выводимой инфыормации, если не указан, дефолтный json, csv, html, xml, jsonp
					'cache' => true, //задает, кешировать ли весь урл, если тру - стандартное кеширования, фалсе - запрет, число - на сколько кешировать. Дефолт 30 сек.
					'cache_keys' => array(), //какие теги добавляються к кешу 
					'params' => Array('PHPSESSID'), //массив обязательных параметров для обработки, проверяются через empty
					'debug' => false, //выводить ли дебаг информацию
					'isTest' => false, //НЕ ВЫСТАВЛЯТЬ - отключает кеширование флаг, указывает на возможность тестирования этого URL
					'testParam' => Array(), //массив параметр=>значение для тестирования
					'preDispatch' => null, //array('Game_Main', 'isSession'),
					'disabledModules' => Array('memcache') //какие модули можно НЕ инитить = кеш, базу, сессию и т.п.			
				),
				//!ВАЖНО! URL пишутся без префикса game
				'url' => Array(

					
					//периодическое оповещение, что я онлайн
					'heartbeat' => array(
						'cache' => 10, //хердбит можно кешировать
						'params' => Array('user_ts'),
						'handler' => Array('Game_Main', 'app_UserHeartBeat')
					),
					//начальная загрузка данных
					'init/loading' => array(
						'cache' => 5,
						'action' => 'dataLoading',
						'handler' => Array('Game_Main', 'app_initDataLoader')
					),
					
					'go/out' => array(
						'cache' => false,
						'handler' => Array('Game_Games', 'app_goOutCurrentGame')					
					),
					
					//загружает всю информацию по карте 
					'map/load' => array(
						'cache' => false,
						'handler' => Array('Game_City', 'loadMaps')	
					),
					
					//загружает информацию по маркету
					'city/market/regions' => array(
						'cache' => 10,
						'handler' => Array('Game_City', 'loadCityRegionMarket')	
					)
					
					
				)			
			),
			
		
			'User' => Array(
				//дефолтные настройки
				'_default' => Array(		
					'type' => 'json', //тип выводимой инфыормации, если не указан, дефолтный json, csv, html, xml
					'cache' => true, //задает, кешировать ли весь урл, если тру - стандартное кеширования, фалсе - запрет, число - на сколько кешировать. Дефолт 30 сек.
					'cache_keys' => array(), //какие теги добавляються к кешу 
					'params' => array('PHPSESSID'), //массив обязательных параметров для обработки, проверяются через empty
					'debug' => false, //выводить ли дебаг информацию
					'isTest' => false, //НЕ ВЫСТАВЛЯТЬ - отключает кеширование флаг, указывает на возможность тестирования этого URL
					'testParam' => array(), //массив параметр=>значение для тестирования
					'preDispatch' => false,
					'disabledModules' => array('memcache', 'redis') //какие модули можно НЕ инитить = кеш, базу, сессию и т.п.			
				),
				//!ВАЖНО! URL пишутся без префикса game
				'url' => Array(
				
					'test' => array(
						'cache' => 10,
						'handler' => array('Game_Test', 'testHandler') //непосредственно обработчик
					),
					
					
					'friends/update' => array(
						'cache' => false,
						'params' => array('friendIds'),
						'action' => 'updateUserFriends',
						'handler' => array('Game_User', 'updateUserFriendsList')
					),
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
                    // FINANCE
                    //загрузка финансового лога игрока
                    'finance/exchange/gold2money' => array(
                        'cache' => false,
                        'handler' => Array('User_Finance', 'app_exchangeGold2Money')
                    ),
					//загрузка финансового лога игрока 
					'finance/log/load' => array(
						'cache' => false,
						'handler' => Array('User_Finance', 'app_getUserFinanceLog')
					),
                    // загрузить только за сегодня
                    'finance/log/loadoneday' => array(
						'cache' => false,
						'handler' => Array('User_Finance', 'app_getUserFinanceOneDayLog')
					),

					//показать актуальное состояние счетов игрока 
					'finance/accounts/list' => array(
						'cache' => 5,
						'handler' => Array('User_Finance', 'app_MyFinanceAccounts')
					),
					//установка пользовательской опции 
					'options/set' => array(
						'cache' => false,
						'params' => array('opt_id'),
						'handler' => Array('User_Options', 'app_saveUserOption')
					),
					//получение списка прав на итем
					'property/by/item' => array(
						'cache' => false,
						'params' => array('item_id', 'item_type'),
						'handler' => Array('Game_Property', 'app_GetItemsProperty')					
					),
					//список всех прав, которыми владеет пользователь 
					'finance/property/list' => array(
						'cache' => false,
						'handler' => Array('Game_Property', 'app_getUserProperty')	
					),
					//приватное сообщение игроку 
					'privatemsg' => array(
						'cache' => false,
						'params' => array('user_id', 'msg'),
						'handler' => Array('User_Main', 'app_sendPrivateMsg')	
					),
					//получение или обновление фида
					'feed' => array(
						'cache' => false,
						'handler' => Array('User_Main', 'app_loadUserFeed')
					),
                    //search user to invite
                    'invite/search' => array(
                        'cache' => false,
                        'handler' => Array('User_Invite', 'app_searchUsers')
                    ),
                    // invite/send
                    'invite/send' => array(
                        'cache' => false,
                        'handler' => Array('User_Invite', 'app_sendInvate')
                    ),
					'online' => array(
						'cache' => false,
                        'handler' => Array('User_Main', 'app_getAllOnline')					
					)
				)
			),
			
			
		
		
            'Msg' => Array(
                    //дефолтные настройки
                    '_default' => Array(
                        'type' => 'json', //тип выводимой инфыормации, если не указан, дефолтный json, csv, html, xml, jsonp
                        'cache' => true, //задает, кешировать ли весь урл, если тру - стандартное кеширования, фалсе - запрет, число - на сколько кешировать. Дефолт 30 сек.
                        'cache_keys' => array(), //какие теги добавляються к кешу
                        'params' => Array('ukey', 'PHPSESSID'), //массив обязательных параметров для обработки, проверяются через empty
                        'debug' => false, //выводить ли дебаг информацию
                        'isTest' => false, //НЕ ВЫСТАВЛЯТЬ - отключает кеширование флаг, указывает на возможность тестирования этого URL
                        'testParam' => Array(), //массив параметр=>значение для тестирования
                        'preDispatch' => array('Game_Main', 'isSession'),
                        'disabledModules' => Array('memcache') //какие модули можно НЕ инитить = кеш, базу, сессию и т.п.
                    ),
                    //!ВАЖНО! URL пишутся без префикса game
                    'url' => Array(
                        'get/inbox' => array(
                            'cache' => false,
                            'handler' => Array('User_Messages', 'app_getMsgInList') //непосредственно обработчик
                        ),
                        'get/outbox' => array(
                            'cache' => false,
                            'handler' => Array('User_Messages', 'app_getMsgOutList') //непосредственно обработчик
                        ),
                        'get/delbox' => array(
                            'cache' => false,
                            'handler' => Array('User_Messages', 'app_getMsgDelList') //непосредственно обработчик
                        ),
                        'send' => array(
                            'cache' => false,
                            'handler' => Array('User_Messages', 'app_sendMsg') //непосредственно обработчик
                        ),
                        'delete' => array(
                            'cache' => false,
                            'handler' => Array('User_Messages', 'app_deleteMsg') //непосредственно обработчик
                        ),
                        'get/tags' => array(
                            'cache' => false,
                            'handler' => Array('User_Messages', 'app_getUserTags') //непосредственно обработчик
                        ),
                    )
            ),

			//основная часть адреса - admin/<url>
			'Admin' => Array(
				//дефолтные настройки
				'_default' => Array(		
					'type' => 'json', //тип выводимой инфыормации, если не указан, дефолтный json, csv, html, xml
					'cache' => false, //задает, кешировать ли весь урл, если тру - стандартное кеширования, фалсе - запрет, число - на сколько кешировать. Дефолт 30 сек.
					'cache_keys' => array(), //какие теги добавляються к кешу 
					'params' => array(), //массив обязательных параметров для обработки, проверяются через empty
					'debug' => true, //выводить ли дебаг информацию
					'isTest' => false, //флаг, указывает на возможность тестирования этого URL
					'testParam' => array(), //массив параметр=>значение для тестирования
					//'preDispatch' => array('Admin_Main', '_checkSession'), // array('Class_Name', 'Method_Name')
					'disabledModules' => array('memcache') //какие модули можно НЕ инитить = кеш, базу, сессию и т.п.
				),
				//непосредственно URL-ы для обработки
				//настройки совпадающие с _default для блока можно опускать 
				'url' => Array(
                    //
					'login' => array(
						'handler' => array('Admin_Main', 'app_login') //непосредственно обработчик
					),
                    // грузим полезности дл админки
					'init/loading' => array(
						'handler' => array('Admin_Main', 'app_dataLoading')
					),
                    // logout
					'logout' => array(
						'handler' => array('Admin_Main', 'app_logout') //непосредственно обработчик
					),
                   


				)
			),


			
			//для этого проекта АПИ надо - урлы начинаются с /api
			'Api' => Array(
				//дефолтные настройки
				'_default' => Array(		
					'type' => 'json', //тип выводимой инфыормации, если не указан, дефолтный json, csv, html, xml
					'cache' => false, //задает, кешировать ли весь урл, если тру - стандартное кеширования, фалсе - запрет, число - на сколько кешировать. Дефолт 30 сек.
					'cache_keys' => array(), //какие теги добавляються к кешу 
					'params' => array(), //массив обязательных параметров для обработки, проверяются через empty
					'debug' => false, //выводить ли дебаг информацию
					'isTest' => true, //флаг, указывает на возможность тестирования этого URL
					'testParam' => array(), //массив параметр=>значение для тестирования
					'preDispatch' => false,
					'disabledModules' => array('session', 'memcache') //какие модули можно НЕ инитить = кеш, базу, сессию и т.п.			
				),
				'url' => Array()
			)
		
		);
	}
	
}