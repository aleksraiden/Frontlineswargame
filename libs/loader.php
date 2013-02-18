<?php
/**
 * Created by JetBrains PhpStorm.
 * User: raiden
 * Date: 29.12.12
 * Time: 12:54
 * To change this template use File | Settings | File Templates.
 */

//загрузчик всех библиотек для проекта

$_domain = $_SERVER['HTTP_HOST'];

if( (strpos($_domain, 'www.') !== 0)   && (strpos($_domain, 'game.') !== 0))
{
    header('Location: http://www.' . $_domain);
    exit();
}
$_tmpd = explode('.', $_domain);
$_domain = $_tmpd[count($_tmpd)-2] . '.' . $_tmpd[count($_tmpd)-1];

// если локальная версия
$is_dev = false;
if(strstr($_domain, '.dev')){
    $is_dev = true;
}

//общий документ рут
$_SERVER['DOCUMENT_ROOT'] = '/home/' . $_domain;

set_include_path(get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT'] . '/libs' . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT'] . '/game/inc' );

require_once('Zend/Loader/Autoloader.php');
$loader = Zend_Loader_Autoloader::getInstance();

$loader->registerNamespace('Signalsy');
$loader->registerNamespace('Game');

require_once('Realplexor.php');
require_once('Exceptionizer.php');

$config = parse_ini_file( $_SERVER['DOCUMENT_ROOT'] . '/game/config.ini', true);

require_once('Zend/Loader/Autoloader.php');
$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace('Signalsy');
$loader->registerNamespace('Game');

require_once( $_SERVER['DOCUMENT_ROOT'] .  '/game/connectManager.php');

Zend_Registry::set('config', $config);

//теперь инитим основные модули
Signalsy_xRouter::_prepareSession($_domain);
Signalsy_xRouter::_prepareDb(false, false);
Signalsy_xRouter::_prepareCache();

$session = Zend_Registry::getInstance()->get('session');
$session->isLoading = true;
//$uid = $session->uid;

//выкидывает игрока из паралельных сессий
if ($config['DkLabRealplexor']['useDklab'] == true)
  require_once('Realplexor.php');

$db = Zend_Registry::getInstance()->get('db');

