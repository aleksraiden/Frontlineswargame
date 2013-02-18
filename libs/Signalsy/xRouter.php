<?php
/**
 * Signalsy Platform Framework 2.0
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
 * @package    Signalsy Router
 * @copyright  Copyright (c) 2009 - 2010 AGPsource Team
 * @license    http://signalsy.com/license/ New BSD License
 */

 
 /* including Exceptions */
 include_once ( 'Signalsy/Exceptions.php' );
 include_once ( 'Signalsy/Browser.php' );

/**
 * Router is main class to provide signal's routing and preparing all data
 *
 * @category   Signalsy
 * @package    Signalsy Router 2
 * @copyright  Copyright (c) 2009 - 2010 AGPsource Team
 * @license    http://signalsy.com/license/     New BSD License
 */
 class Signalsy_xRouter
 {
 	/**
 	 * @var Array config section for this component
 	 */
 	private $config = null;


 	private $useLang = true;

 	//какие модули по дефолту инициализируются для обработчика
 	public $modules = Array('file', 'session', 'db'); //, 'memcache', 'redis', 'apc');
 	
 	
 	public $defaultURLcache = 30; //на сколько дефолтно кешировать ответы 
 	public $defaultCacheType = 'file';
 	/**
 	 * @var Object object instance for singlton
 	 * @access public
 	 * @static
 	 */
	static private $instance = null;
	
	/**
	 *@var Array Connecting table with all signals connector, from cache
	 */
	protected $__routings_table = Array();
	
	public $browser = null;

	/**
	 * @var Array Full param array to all signals calling
	 */
	private $param = Array();
	
	
	/**
	 * @var Array Avalaible datatype for http param
	 * string is limited for 2048 symbols, text up to 64Кб
	 */
	protected $_paramTypes = Array('int','float','string','text');
	
	//Bool flag, user or not param type hinting
	public $useParamTypeHinting = true; 
	
	/**
	 * *******************************************************************************************
	 */
	
	/**
	 * @access private
	 * @param Array config array
	 * @return void
	 */ 
	private function __construct($config = null)
	{
		if (empty($config))
		{
			$this->config = Zend_Registry::get('config');
		}
		else
			$this->config = $config;
		
		// Construct or getting from cache routings and connectings tables
		$this->_constructRoutesTable();
				
		//Init required params
		$this->createParam(); //
		
		//инит кеш 
		$this->_prepareCache(Array($this->defaultCacheType));
		
		$this->_prepareBrowser();
	}
	
	private function __clone(){
		throw new Signalsy_Exception('Do not clone router object!');
	}
	
	/**
	 * @access public
	 * @static
	 * @return Object  Signalsy_Router instance
	 
	   Signalsy_xRouter::getInstance
	 */
	public static function getInstance($config = null)
	{
		if (isset(self::$instance))
		return self::$instance;
		else
			{
				self::$instance = new Signalsy_xRouter($config);
				return self::$instance;
			}
	}
	
	
	/**
	 * Construct or gets from cache routing table
	 * In this, we use simple file cache or APC/XCache system (without Zend Cache)
	 * @access private
	 * @return Boolean 
	 */
	private function _constructRoutesTable()
	{
		//construct manually
		$this->__routings_table = connectManager::exportRouting();			
		
		return true;		
	}
	
	
	/**
	 * Utilite function, prepare cache if using routes cache
	 * Принимает на вход идентификатор кешей, какие создавать - массив ('memcache','file','redis','apc')
	 * 
	 * 
	 * @return Object Zend_Cache
	 */
	public function _prepareCache($caches = Array('file'))
	{
		$config = Zend_Registry::get('config');
		
		//servers 	Array 	
		$_default_frontend = array(
				'caching' => true,
				'lifetime' => 3600,
				'cache_id_prefix' => 'game_',
				'logging' => false,
				'write_сontrol' => false,
				'automatic_serialization' => true,
				'automatic_cleaning_factor' => 5,
				'ignore_user_abort' => true
		);
		
		foreach($caches as $c)
		{
			if (Zend_Registry::isRegistered('cache_' . $c) === true) continue;	
			
			if ($c == 'memcache')
			{
				$_backend_name = 'Libmemcached';
				$_backend_opt = array(
									'servers' => array(array(
										'host' => 'localhost', 
										'port' => 11211, 
										'persistent' => true, 
										'weight' => 1, 
										'timeout' => 5, 
										'retry_interval' => 15, 
										'status' => true, 
										'failure_callback' => ''
									)),
									'client' => array(
										'COMPRESSION' => true,
										'SERIALIZER' => Memcached::SERIALIZER_JSON,
										'HASH' => Memcached::HASH_MD5,
										'DISTRIBUTION' => Memcached::DISTRIBUTION_CONSISTEN,
										'LIBKETAMA_COMPATIBLE' => true,
										'BUFFER_WRITES' => false,
										'BINARY_PROTOCOL' => true,
										'NO_BLOCK' => true,
										'CACHE_LOOKUPS' => false										
									)
								);
			}
			else
			if ($c == 'file')
			{
				$_backend_name = 'File';
				
				if ((!isset($config['Cache']['cache_path'])) || (empty($config['Cache']['cache_path'])))
					$config['Cache']['cache_path'] = sys_get_temp_dir(); 
				
				$_backend_opt = Array(	'cache_dir' => $config['Cache']['cache_path'],
										'file_locking' => false,
										'read_control' => true,
										'read_control_type' => 'strlen'				
									);
			}
			
			
			try
			{
				
				if (!empty($_backend_name))
				{
					$_cache = Zend_Cache::factory('Core', $_backend_name, $_default_frontend, $_backend_opt);

					if ((!empty($_cache)) && ($_cache instanceOf Zend_Cache_Core))
					{
						//this is default cache to all system, adding them to registry
						Zend_Registry::set('cache_' . $c, $_cache);
						
						Zend_Registry::set('cache', $_cache); //дефолтный кеш
						
						return true;
					}
				}
				
			}catch(Exception $e){
				echo '<pre>';
				var_dump($e);
				exit(1);
			}			
		}
		
		return true;
	}
	
	//готовим сессию
	// если второй параметр 0 - то сессия будет пока открыт браузер, иначе запомним на 2 недели 
	public function _prepareSession($_domain = null, $rememberMe = 1209600){

		if (!empty($_domain))
		{
			Zend_Session::setOptions(
			  array(
				'cookie_domain' => '.' . $_domain,
				'cookie_httponly' => 'off'
			  )
			);		
		}		
		
		$session = new Zend_Session_Namespace('Default'); //default session ns
		
		Zend_Registry::set('session', $session);

		return true;
	}
	
	//готовим БД
 	public function _prepareDb($useCache = false, $enableProfiler = true){
		
		$config = Zend_Registry::get('config');
		$c = $config['Database'];
		
 		$options = array(
		    	Zend_Db::AUTO_QUOTE_IDENTIFIERS => true,
				Zend_Db::ALLOW_SERIALIZATION => true,
				Zend_Db::AUTO_RECONNECT_ON_UNSERIALIZE => true
 		);

		try
		{
			$db = new Zend_Db_Adapter_Mysqli(array(
				    'host'     => $c['db_host'],
				    'username' => $c['db_user'],
				    'password' => $c['db_password'],
				    'dbname'   => $c['db_database_name'],
				    'port' => $c['db_port'],
					'charset'   => 'utf8',
					'profiler' => $enableProfiler,
					'options' => $options			
			));
		}
		catch (Exception $e)
		{
		    die($e->getMessage());
		}

		if ((!is_object($db)) || ($db == FALSE) || (mysqli_connect_errno()))
		{
			die('Platform required DB connection to start. Error: ' . mysqli_connect_error());
		}

		//Мы работем ТОЛЬКО с UTF-8 кодировкой
		mysqli_set_charset($db->getConnection(), 'utf8');

		//использовать встроенное кеширование		
		
		Zend_Registry::set('db', $db);
	
		
		
		return true;
	}
	
	//Готовим простой редис-интерфейс (быстрый, через Redisent пока что
	public function _prepareRedis(){
		if (!class_exists('Redis')) return false;
		
		$config = Zend_Registry::get('config');
		
		$redis = $redis = new Redis($config['Database']['redis_host'], $config['Database']['redis_port']);
	
		Zend_Registry::set('redis', $redis);
		
		return true;
	}

	//определение платформы и браузера 
	public function _prepareBrowser(){
	
		try
		{
			$this->browser = new Browser();
			
			Zend_Registry::set('browser', $this->browser);
			
		}catch(Exception $e){}
	}
	
	/**
	 * Create required param array
	 * @return void
	 */
	private function createParam()
	{
		$this->param = Array('http' => Array(), 'url' => null);
	}
	

	/**
	 * Preparing HTTP params
	 */
	private function prepareHTTPRequest($includeServiceOpt = false, $useParamTypeHinting = false)
	{
		//exclude flash amf query: Content-type: application/x-amf
		
		if ((!isset($_SERVER['CONTENT_TYPE'])) || ((isset($_SERVER['CONTENT_TYPE'])) && ($_SERVER['CONTENT_TYPE'] != 'application/x-amf')))
		{		
		
			// из запроса убираются все параметры, которые начинаются с символа _, например, _dc
			$tmp = $_REQUEST;
			$httprequest = Array();
		
			foreach ($tmp as $name=>$item)
			{
				$name = substr(strtolower(trim($name)), 0, 1024); //максимальная длина имени переменной 1024 символа
				
				if ($_SERVER["REQUEST_METHOD"] == 'GET')
				{					
					$item = substr(trim($item), 0, 4096); //максимальная длина значения, передаваемого - 4 Кб
				}
				else
				{
					$name = substr(strtolower(trim($name)), 0, 1024); //максимальная длина имени переменной 1024 символа
				}
								
				//служебный параметр для кеширования не передается 
				if ($includeServiceOpt == false) 
				{	
					if (strpos($name, '_') === 0)    continue;
					else
						$httprequest[$name] = $item;
				}
				else
					$httprequest[$name] = $item;
			}
		}
		
		$this->param['http'] = $httprequest;
	}

	
	/**
	 * Preparing URL to routing
	 * @param string URL
	 * @return string
	 */
	public function prepareURL($url = null)
	{
		if (empty($url)) return '';
		else
			{
				//!NOTE: max URL length is 4096 symbols
				$url = strtolower(substr($url, 0, 4096));
				
				if ($url{0} == '/')
				{
					$url{0} = '';
				}
				
				if ($url{(strlen($url)-1)} == '/')
				{
					$url{(strlen($url)-1)} = '';
				}
				
				$url = trim($url);
				
				if (!empty($url)) return $url;
				else
					return '';				
			}
	}
	
	/**
	 * Utilite function - parse and prepare params with right type
	 * @param Array Http params array
	 * @param Array Defined params and types (optional)
	 */
	private function prepareParamsTypes($httpParams = null, $definedParams = Array())
	{
		$returnParams = Array();
		
		if ((empty($httpParams)) || (empty($definedParams)))
			return $returnParams;		
		
		$curParamName = null;
			
		foreach($definedParams as $x)
		{
			$_x = strtolower($x);

			//если не задан тип (или есть разделитель но нет указания типа 
			if ((strpos($_x, '|') === FALSE) || (strpos($_x, '|') == (strlen($_x)-1)))
			{
				$returnParams[ $_x ] = $httpParams[ $_x ];
				$curParamName = $_x;
			}
			else
			{
				$tmp = explode('|', $_x);
				$_x = $tmp[0];
				
				if (!in_array($tmp[1], $this->_paramTypes))
				{
					//неизвестные типы не обрабатываем 					
					$returnParams[ $_x ] = $httpParams[ $_x ];
				}
				else
				{
					if ($tmp[1] == 'int')	//целое число 
						$returnParams[ $_x ] = (int)$httpParams[ $_x ];
					else
					if ($tmp[1] == 'float')	//плавающая точка 
						$returnParams[ $_x ] = (float)$httpParams[ $_x ];
					else
					if ($tmp[1] == 'string') //строка 
						$returnParams[ $_x ] = substr((string)$httpParams[ $_x ], 0, 2 * 1024);
					else
					if ($tmp[1] == 'text') //строка 
						$returnParams[ $_x ] = substr((string)$httpParams[ $_x ], 0, 64 * 1024);				
				}

				$curParamName = $_x;
			}

			if (!array_key_exists($curParamName, $httpParams))
				throw new Signalsy_Missing_Required_Params_Exception('Missing required GET/POST params: ' . $_x);	
		}
		
		return $returnParams;	
	}
	
	
	
	/**
	 * Main function - dispatch URL and all signals'
	 * @param string URL
	 * @param Boolean в серверном варианте не обрабатывать HTTP заголовки
	 */
	public function dispatchURL($_url = null)
	{
		$_url = $this->prepareURL($_url);
		$this->param['url'] = $_url;
		
		$this->prepareHTTPRequest(true, $this->useParamTypeHinting);
//echo '<pre>';var_dump($this->__routings_table);exit(1);

		
		//processing them!
		//ищем URL, сопоставимый с одним из таблицы 
		//!TODO: быдлокод детектед	
		//может сначала искать в дефолтных? 
		if (array_key_exists($_url, $this->__routings_table['Default']['url']))
		{
			//если не нашли?
			$_ns = 'Default';				
		}
		else
		{
			$_x = explode('/', $_url);
		
			$_ns = ucwords(trim($_x[0])); //в какой части искать адрес
			
			if (!array_key_exists($_ns, $this->__routings_table))
			{
				//если не нашли?
				$_ns = 'Default';				
			}
			else
			{
				unset($_x[0]);
				$_url = implode('/', $_x); //соберем без префикса
			}
		}
		
		$_rt_def = $this->__routings_table[$_ns]['_default'];
			
		//поиск адреса
		$_handler_url = null;
		
		foreach($this->__routings_table[$_ns]['url'] as $_rt_url => $hndl)
		{
			if ($_rt_url == $_url)
			{
				$_handler_url = $hndl;
				break;					
			}			
		}
//echo '<pre>';var_dump($_url); exit(1);					
		if (empty($_handler_url))
		{
			$_handler_url = $this->__routings_table['Default']['url']['error/404']; //дефолтный пустой урл	
			$_rt_def = $this->__routings_table['Default']['_default'];			
		}			

		//теперь обработка  - НЕЛЬЗЯ использовать мерг_рекурсиве
		if (!array_key_exists('params', $_handler_url)) $_handler_url['params'] = Array();
		$__params = array_merge($_rt_def['params'], $_handler_url['params']);
	
		//теперь модули 
		if (!array_key_exists('disabledModules', $_handler_url)) $_handler_url['disabledModules'] = Array();
		$__modules = array_merge($_rt_def['disabledModules'], $_handler_url['disabledModules']);
		
		//теперь кеш кей
		if (!array_key_exists('cache_keys', $_handler_url)) $_handler_url['cache_keys'] = Array('url');
		if (!array_key_exists('cache_keys', $_rt_def)) $_rt_def['cache_keys'] = Array();
		
		$__cache_keys = array_merge($_rt_def['cache_keys'], $_handler_url['cache_keys']);
		
		//а вот пре-диспатч только общий, игнорируеться в конкретных урлах 
		
		//основной мерг
		$opt = array_merge($_rt_def, $_handler_url);
		$opt['params'] = $__params;
		$opt['disabledModules'] = $__modules;
		$opt['cache_keys'] = $__cache_keys;
		
		//теперь проверим необходимые параметры. 
		//Если нет хоть одного из обьедененного масива _default и url, выбросить ексепшин 
		//еслти тип - jsonp, то автоматом добавить в необходимые параметры еще callback
		if (($opt['type'] === 'jsonp') && (!in_array('callback', $opt['params']))) 
			$opt['params'][] = 'callback';		
		
		//обработка параметров 
		$this->param['http'] = $this->prepareParamsTypes($this->param['http'], $opt['params']);
		
		if (!array_key_exists('cache_keys', $opt))
			$opt['cache_keys'] = Array('url');
		else
			if (!in_array('url', $opt['cache_keys']))	$opt['cache_keys'][] = 'url';
				
		$opt['params'] = $this->param['http'];
//echo '<pre>';var_dump($opt['orm']);exit(1);		

		$this->process($opt);			
	}
	
	/**
	 * Manually emmit signal with optionally param
	 * special thanks Appocaliptica One for this method :)	 * 
	 * 
	 * @access public
	 * @param string|array signal
	 * @param mixed optional arguments
	 * @return mixed
	 */
	public function process($opt = null)
	{
		if (empty($opt)) return false;
		else
		{
			//режим тестирования - проверим глобальный флаг 
			if (Zend_Registry::isRegistered('isTestMode') === true)
			{
				$opt['isTest'] = Zend_Registry::get('isTestMode');												
			}
				
			if ($opt['isTest'] === true)
			{
				$opt['cache'] = false; //отключаем кеш для режима тестирования
			}
								
			if ($opt['cache'] === true)
				$opt['cache'] = $this->defaultURLcache;
			
			//0. Проверим, можно ли кешировать этот вызов 
			if ((!empty($opt['cache'])) && (Zend_Registry::isRegistered('cache_' . $this->defaultCacheType) === true))
			{
				$cache = Zend_Registry::get('cache_' . $this->defaultCacheType);
					
				if ((!empty($cache)) && ($cache instanceOf Zend_Cache_Core))
				{
					//пробуем загрузить из кеша 
					$_data = $cache->load('url_' . md5(implode(';', $opt['params']) . ';' . $this->param['url']));	
							
					if ((!empty($_data)) && ($_data !== false))
					{
						$this->output($_data, $opt, true);
						return true;						
					}						
				}
			}
				
			//1. Проверим, можно ли вызвать переданное 
			if (is_callable($opt['handler']))
			{
				//теперь генерируем, что надо
				$_modules = Array();
					
					// disabledModules
					foreach($this->modules as $_mod)
					{
						if (!in_array($_mod, $opt['disabledModules']))
						{
							$_modules[] = $_mod;
						}						
					}
					
					//try {
						
						//инит модулей
						$this->initModules($_modules);
						
						//преДиспатч 
						if ((!empty($opt['preDispatch'])) && (!is_bool($opt['preDispatch'])))
						{
							if (is_callable($opt['preDispatch']))
							{
								$preDispatch_res = call_user_func($opt['preDispatch'], $this->param['http'], $this->param['url'], $opt['isTest']);

                                //!ВАЖНО! метод должен вернуть true или строку с описанием проблемы
								if ($preDispatch_res !== true)
									throw new Signalsy_preDispatch_Exception('pre-dispatch error: ' . $preDispatch_res);
							}
						}
						
						//ну как бы и все, запускаем 
						//передаем также переменные: $db, $uid, $session, $cache, $config, $browser
						// $_modules - если доступны модули 
						$db = null;
						$db = Zend_Registry::get('db');
						
						$this->param['db'] = $db; 
						$this->param['uid'] = null;
						$this->param['session'] = null;
						$this->param['cache'] = null;
						$this->param['browser'] = null;												
							
						if (in_array('session', $_modules))
						{
							$this->param['session'] = Zend_Registry::get('session');	
							//$this->param['uid'] = $_session->uid;
						}

						if (in_array('cache', $_modules))
							$this->param['cache'] = Zend_Registry::get('cache');

						$this->param['browser'] = Zend_Registry::get('browser');

						
						//безусловно
						if (($db !== null) && ($db instanceOf Zend_Db))
							$db->beginTransaction();
						
						$result = call_user_func($opt['handler'], $this->param['http'], $this->param, $opt['isTest']); //, $_db, $_session, $_uid, $_cache, $_browser);
						$_cacheResult = false;
						
						if ((isset($result['status'])) && ($result['status'] == 'OK'))
						{						
							if (($db !== null) && ($db instanceOf Zend_Db))
								$db->commit();
							
							if ((!empty($opt['cache'])) && (Zend_Registry::isRegistered('cache_' . $this->defaultCacheType) === true))
							{
								$cache = Zend_Registry::get('cache_' . $this->defaultCacheType);
								
								if ((!empty($cache)) && ($cache instanceOf Zend_Cache_Core))
								{
									//обработаем теги 
									$user_id = null;
									//у тега будет одна макроподстановка  = user_{session_uid}
									if (Zend_Registry::isRegistered('session') === true)
									{
										$ses = Zend_Registry::get('session');
										//$user_id = $ses->uid;				
									}
			
									//! TODO: сделать так, чтобы в скобках было произвольное поле с сессии 			
									$_keys = Array();
									
									foreach($opt['cache_keys'] as $x)
									{
										if ((strrpos($x, '{session_uid}') !== false) && (!empty($user_id)))
											$x = str_replace('{session_uid}', $user_id, $x);
										
										$_keys[] = $x;
									}
									
									$_cacheResult = $cache->save($result, 'url_' . md5(implode(';', $this->param['http']) . ';' . $this->param['url']), $_keys, $opt['cache']);	
								
									if ($_cacheResult === false)
									{
										//echo 'Save cache problem!!!';
										$_cacheResult = false;
									}
									else
										$_cacheResult = true;
								}
							}
						}
						else
						{
							//отменяем транзакцию? 
							if (($_db !== null) && ($_db instanceOf Zend_Db))
								$_db->rollBack();
						}
									
						//обработаем результат 
						$this->output($result, $opt, $_cacheResult);
					
					//}catch(Exception $e)
					//{
					//	throw new Signalsy_SignalSlot_Exception('Exception by processing URL handler. MSG: ' . $e->getMessage());						
					//}
					
					return true;
					
				}
				else
					throw new Signalsy_SignalSlot_Exception('URL Handler('.implode('::', $opt['handler']).') must be callable (function or static class method)');
			}
	}
	
	
	
	
	/**
	 * Готовит нужные модули для работы обработчика 
	 * 
	 */
	public function initModules($_mods = Array())
	{
		//echo '<pre>'; var_dump($_mods); exit(1);
		//инициализировать модули  ИМЕННО в таком порядке
		$_caches = Array();
		
		if (in_array('memcache', $_mods)) $_caches[] = 'memcache';
		if (in_array('file', $_mods)) $_caches[] = 'file';
		if (in_array('apc', $_mods)) $_caches[] = 'apc';
		
		$this->_prepareCache($_caches);
		
		if (in_array('db', $_mods)) $this->_prepareDb();
		if (in_array('session', $_mods)) $this->_prepareSession();
		if (in_array('redis', $_mods)) $this->_prepareRedis();		

		return true;
	}
	
	//генерируем XML
	private function getXML($data = Array())
	{
		$writer = new XMLWriter();
		$writer->openMemory();
        $writer->startDocument('1.0', 'UTF-8');
        $writer->startElement('root');
		
		foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $key = 'key'.$key;
            }
            if (is_array($val)) {
                $writer->startElement($key);
                $this->_getXML($val, $writer);
                $writer->endElement();
            }
            else {
                $writer->writeElement($key, $val);
            }
        }
		
		$writer->endElement();
        
		return $writer->outputMemory();		
	}
	
	private function _getXML($data = Array(), $writer = null){
		
		foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $key = 'key'.$key;
            }
            if (is_array($val)) {
                $writer->startElement($key);
                $this->_getXML($val, $writer);
                $writer->endElement();
            }
            else {
                $writer->writeElement($key, $val);
            }
        }	
	}
	

	private function output($data = Array(), $opt = Array(), $isCached = false)
	{
        /**
		// если надо сделать такое вот бл...
        // сделано для опера, которая не умеет по дефолту работать с ЖСОН если отправлять через ажаск файлы на стервер
        if (isset($data) && array_key_exists('type', $data)) {
            if ($data['type'] == 'html_json')
            {
                header('Content-type: text/html; charset=UTF-8');
                echo Zend_Json::encode($data, false);
                exit;
            }
        }
		**/
		if ($opt['type'] == 'html')
		{
			header('Content-type: text/html; charset=UTF-8');
			
			if ($data['status'] == 'FAILURE')			
				echo $data['error'];
			else
				echo $data['data'];
		}
		else 
		{		
			//добавляет метку времени 
			$data['timestamp'] = time();
			$data['cached'] = $isCached;

			if (!array_key_exists('status', $data)) $data['status'] = 'OK'; //дефолтно, если дошли сюда - статус ОК	
			if (!array_key_exists('msg', $data)) $data['msg'] = ''; //дефолтно пустая строка 	
						
			if ($opt['debug'] == false)
			{
				//убрать дебаг информацию
				unset($data['debug']);
			}
			else 
			{
				//попробуем достать профайлинг запросов 
				$profiler = Zend_Registry::getInstance()->get('db')->getProfiler();
				
				if (!empty($profiler))
				{
					$p = $profiler->getQueryProfiles();
			 
					 if (!empty($p))
					 {
						foreach($p as $q)
						{
							$prof[] = Array(
								'sql' => $q->getQuery(),
								'time' => $q->getElapsedSecs()
							);
						}					 
					 
			 			$data['debug']['db'] = array(
							'num_queries' => $profiler->getTotalNumQueries(),
							'total_secs' => $profiler->getTotalElapsedSecs(),
							'profile' => $prof,
							'last' => Array(
								'sql' => $profiler->getLastQueryProfile()->getQuery(),
								'time' => $profiler->getLastQueryProfile()->getElapsedSecs()
							)
						);				
					}
				}

				$data['debug']['memoryUsage'] = memory_get_peak_usage(true);
				
				if (function_exists('sys_getloadavg') === true)
					$data['debug']['systemLoad'] = sys_getloadavg();
			}
			
			if ($opt['type'] == 'json')
			{
				//для оперы надо переопределить тип 
                if (($this->browser != null) && ($this->browser->getBrowser() == 'Opera') && (!empty($_FILES)))
                    header('Content-type: text/html; charset=UTF-8');
                elseif (($this->browser != null) && ($this->browser->getBrowser() == 'Internet Explorer') && (!empty($_FILES)))
                    header('Content-type: text/html; charset=UTF-8');
				else
					header('Content-type: application/json; charset=UTF-8');
					
				$data = Zend_Json::encode($data, false);			
			}
			else
			if ($opt['type'] == 'jsonp')
			{
				header('Content-type: text/javascript');
				$data = $opt['params']['callback'] . '(' . Zend_Json::encode($data, false) . ');';			
			}
			else 
			if ($opt['type'] == 'cvs')
			{
				header('Content-type: text/csv');
			}
			else 
			if ($opt['type'] == 'xml')
			{
                header('Content-type: application/xml; charset=UTF-8');
				$data = $this->getXML($data);
			}
		
			echo $data;
		}
		
		//выставим время обработки 
		$_signalsy_ft = microtime(true);
		$_diff = $_signalsy_ft - Zend_Registry::get('signalsy_st');
		
		header('X-Signalsy-Profiling: ' . $_diff);
		header('X-Powered-By: Signalsy Platform 2.2');	
		header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

		if ($isCached == true)
			header('X-Signalsy-Cached: ' . $opt['cache'] . ' s.');
		
		ob_end_flush();

		return true;
	}
	
 }