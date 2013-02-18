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
			<h3>Выбери себе персонажа</h3>
			<a class="btn btn-small btn-success" href="<?php echo $url['app_domain'] . '/?player=model'; ?>">СуперМодель</a>
			&nbsp;&nbsp;
			<a class="btn btn-small btn-danger" href="<?php echo $url['app_domain'] . '/?player=bully'; ?>">Гопник</a>			
	</div>
    <hr style="margin:15px 0px;">

    <div class="row-fluid marketing">
        <div class="span6">
            <h4>Модели</h4>
            <p>Красивые и обворожительные красотки, обученные смертельным приемам</p>

            <h4>Гопники</h4>
            <p>Жестокие и беспощадные отбросы с дна криминального мира</p>

        </div>

        <div class="span6">
            <h4>20 бойцов</h4>
            <p>Каждый боец обладает своими навыками и способностями</p>

            <h4>100+ приемов</h4>
            <p>Стойки, удары руками и ногами, броски и захваты - ваш смертельный арсенал</p>
        </div>
    </div>

    <hr>

    <div class="footer">
        <p>&copy; AGPsource.com 2005 - 2013</p>
    </div>

</div> <!-- /container -->

<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo $url['static_domain']; ?>/js/jquery/jquery-1.8.3.min.js"></script>

</body>
</html>