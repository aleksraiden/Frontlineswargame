<?php
/**
 * Created by JetBrains PhpStorm.
 * User: raiden
 * Date: 29.12.12
 * Time: 12:54
 * To change this template use File | Settings | File Templates.
 */

@error_reporting(E_ALL);
if(!ob_start("ob_gzhandler")) ob_start();

ini_set('date.timezone', 'GMT+0');

//общий документ рут
$_domain = $_SERVER['HTTP_HOST'];

if( (strpos($_domain, 'www.') !== 0)   && (strpos($_domain, 'game.') !== 0))
{
    header('Location: http://www.' . $_domain);
    exit();
}
$_tmpd = explode('.', $_domain);
$_domain = $_tmpd[count($_tmpd)-2] . '.' . $_tmpd[count($_tmpd)-1];

$_SERVER['DOCUMENT_ROOT'] = '/home/' . $_domain;

$config = parse_ini_file( $_SERVER['DOCUMENT_ROOT'] . '/game/config.ini', true);

//для укорочения ссылок
$url = $config['Default'];


if (array_key_exists('GAME_USER', $_COOKIE))
{
	$user = json_decode($_COOKIE['GAME_USER'], true);
	
	header('Location: ' . $url['app_domain'] . '/');
	exit();
}
else
if ((array_key_exists('token', $_POST)) && (!empty($_POST['token'])))
{
	$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);

	$user = json_decode($s, true);

//$user['network'] - соц. сеть, через которую авторизовался пользователь
//$user['identity'] - уникальная строка определяющая конкретного пользователя соц. сети
//$user['first_name'] - имя пользователя
//$user['last_name'] - фамилия пользователя

	setcookie('GAME_USER', $s, time() + 6 * 3600, '/', '.' . $_domain, false, true);
}






?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $url['site_header']; ?></title>
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
    <meta name="format-detection" content="telephone=no">
    <!-- Le styles -->
    <link href="<?php echo $url['static_domain']; ?>/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
        body {
            padding-top: 5px;
            padding-bottom: 5px;
        }

            /* Custom container */
        .container-narrow {
            margin: 0 auto;
            max-width: 700px;
        }
        .container-narrow > hr {
            margin: 30px 0;
        }

            /* Main marketing message and sign up button */
        .jumbotron {
            margin: 20px 0;
            text-align: center;
        }
        .jumbotron h1 {
            font-size: 72px;
            line-height: 1;
        }
        .jumbotron .btn {
            font-size: 21px;
            padding: 14px 24px;
        }

        /* Supporting marketing content */
        .marketing {
            margin: 25px 0;
        }
        .marketing p + h4 {
            margin-top: 28px;
        }
    </style>
    <link href="<?php echo $url['static_domain']; ?>/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="<?php echo $url['static_domain']; ?>/js/libs/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $url['static_domain']; ?>/img/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $url['static_domain']; ?>/img/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $url['static_domain']; ?>/img/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo $url['static_domain']; ?>/img/ico/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="<?php echo $url['static_domain']; ?>/img/ico/favicon.png">
	
	<script>
	<?php
		if (!empty($user))
		{		
			echo 'var gUser = ' . json_encode($user) . ';  console.log(gUser); ';		
		}
	?>
	
	</script>
</head>

<body>

<div class="container-narrow">

    <div class="masthead">
        <ul class="nav nav-pills pull-right">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
        <h3 class="muted">Коллекционная карточная игра</h3>
    </div>

    <hr>
	<div style="background-image:url('<?php echo $url['static_domain']; ?>/img/main_page.jpg');height:400px;">
		<div class="jumbotron" style="position:relative;top:-68px;">
			<h1>Frontlines War</h1>
			<!--<p class="lead">Фронт войны уже здесь! Сражайся за свой дом!</p> -->
			<!-- <a class="btn btn-large btn-success" href="#">Sign up today</a> -->
		</div>	
	</div>
	<div style="text-align:center;padding-top:0px;">
			<h3>Играть!</h3>
			
			<script src="//ulogin.ru/js/ulogin.js"></script>
<div id="uLogin" data-ulogin="display=panel;fields=first_name,last_name,photo;providers=vkontakte,facebook,mailru,twitter,google,odnoklassniki;hidden=;redirect_uri=http://www.frontlineswar.com"></div>
			
			<!--
			<a class="btn btn-small btn-success" href="<?php echo $url['app_domain'] . '/?player=raiden'; ?>">Raiden</a>
			&nbsp;&nbsp;
			<a class="btn btn-small btn-danger" href="<?php echo $url['app_domain'] . '/?player=lizard'; ?>">Lizard</a>	
			-->
	</div>
    <hr style="margin:15px 0px;">

    <div class="row-fluid marketing">
        <div class="span6">
            <h4>Эпичные битвы</h4>
            <p>Человечество против неустрашимых инопланетных захватчиков</p>

            <h4>Пять фракций</h4>
            <p>Свободные стрелки, Правительство, Орден, Легион, Компания или же ты за Отступников?</p>

        </div>

        <div class="span6">
            <h4>Более 100 видов оружия</h4>
            <p>Командуй артилерией, пехотой и десатом, спецвойска и разведчики также все во внимании</p>

            <h4>Тактика и стратегия</h4>
            <p>Тактические бои за территории - это только начало!</p>
        </div>
    </div>

    <hr>

    <div class="footer">
        <p>&copy; AGPsource.com 2005 - 2013</p>
    </div>

</div> <!-- /container -->

<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo $url['static_domain']; ?>/js/jquery/jquery-1.9.1.min.js"></script>

</body>
</html>