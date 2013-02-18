<?php
/**
 * Общие функции
 */

class Game_Main
{
    /**
     * @static
     * @return mixed
     */
    static public function isSession()
    {
        $config = Zend_Registry::getInstance()->get('config');
        if(Zend_Registry::isRegistered('session'))
        {
            $ses = Zend_Registry::getInstance()->get('session');
            $uid = $ses->uid;
            if(empty($uid))
            {
                header('Content-type: text/plain; charset=UTF-8');
                echo '<pre>';
                echo '$_SESSION';
                print_r($_SESSION);

                echo 'Zend_Registry::getInstance()->get(session)';
                print_r($ses);


                die($config['Default']['site_domain'].' empty-uid PHPSESSION:'.Zend_Session::getId());
            }
        }
        else
        {
            header('Content-type: text/plain; charset=UTF-8');
            die($config['Default']['site_domain'.' empty-session']);
        }
        return true;
    }



   //интерфейс с удаленным реалплексором 
   //@return Boolean 
   static public function push($to = Array(), $data = null)
   {
		if ((empty($to)) || (empty($data)))	return false;
		
		if ($_SERVER['HTTP_HOST'] == 'game.agpsource.com')
			return Game_Main::pushNative($to, $data);
		
		
		
		$rpl = new Zend_Http_Client('http://cinema-manager.com/rpl/index.php', array(
			'maxredirects' => 1,
			'timeout'      => 30,			
			'keepalive'    => true,
			'adapter'	   => new Zend_Http_Client_Adapter_Socket()
		));
		
		$rpl->setMethod(Zend_Http_Client::POST);
		$rpl->setHeaders(array('X-Powered-By' => 'Frontlines:Battleplan API'));
		
		//задаем параметры 
		$rpl->setParameterPost('secretKey', '893ad5cb13f18ec2$1469b3061b2872e');
		$rpl->setParameterPost('action', 'send');
		
		if ($_SERVER['HTTP_HOST'] == 'game.frontwargame.dev')
			$rpl->setParameterPost('server', 'dev');
		else
			$rpl->setParameterPost('server', 'com');
		
		$rpl->setParameterPost('msg', Zend_Json::encode(Array('to' => $to, 'data' => $data)));

//var_dump( $rpl->getUri(true) );
		
		$response = $rpl->request();
//var_dump( $response );
		if (!($response instanceof Zend_Http_Response))
			return false;
		else
		{
			//!TODO: а что с редиректами делать? 
			if (($response->isSuccessful()) || ($response->isRedirect()))
			{
				$_resp = Zend_Json::encode($response->getBody());
//var_dump($_resp);
				if ((!empty($_resp)) && (is_array($_resp)))
				{
					if ((array_key_exists('status')) && ($_resp['status'] == 'OK'))
						return true;
					else
						return $_resp['error'];				
				}
				else
					return false;
			}			
			else
				if ($response->isError())
					return false;		
		}
		
		return false;  
   }


   static public function pushNative($to = Array(), $data = null)
   {
		if ((empty($to)) || (empty($data)))	return false;
		
		if (!Zend_Registry::isRegistered('comet'))
		{		
			$config = Array(
				'rpl_host' => 'localhost',
				'rpl_port' => 10010,
				'rpl_ns' => 'com_frontwar_'		
			);
			
			$rpl = new Dklab_Realplexor($config['rpl_host'], $config['rpl_port'], $config['rpl_ns']);
		
			Zend_Registry::set('comet', $rpl);
		}
		else
			$rpl = Zend_Registry::getInstance()->get('comet'); 
		
		//try
		//{
			$rpl->send( $to, $data );

	}	
   
	
	
	
    /*
		Начальная загрузка данных игрока
		кешируется на 5 сек.
	*/
	static public function app_initDataLoader($p = null)
	{
		$db = Zend_Registry::get('db');
		$session = Zend_Registry::getInstance()->get('session');
		 
		$json = Array('status' => 'OK', 'data' => Array());
		$user_id = $session->user_id;
		
		//загрузка города юзера 
		
		//загрузка денег юзера
		$json['data']['finance'] = Game_Finance::getMoney($user_id);
		
		
		//загрузка наград юзера (вопрос - только общие наверное пока что)
		$json['data']['awardsCounter'] = Game_UserAwards::getAwards($user_id, true);
		
		//загрузка уровня юзера
		$json['data']['level'] = Game_UserLevels::getLevel($user_id);
		
		
		//загрузка сообщений юзера (ленты) 
		
		
		//загрузка маркета 
		
	
		return $json;
	}
	
}