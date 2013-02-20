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

//загружаем инициализацию библиотек
require_once('../libs/loader.php');

//загружаем маппинг URL
require_once('../game/connectManager.php');

//для укорочения ссылок
$url = $config['Default'];

if (array_key_exists('GAME_USER', $_COOKIE))
{
	$user = $_COOKIE['GAME_USER'];
}


/**
 if (!isset($_REQUEST['login']))
     die('Invalid login to game');

 $user = $db->fetchRow('SELECT uid, user_name, user_email, user_first_name, user_last_name, user_photo, user_at, last_login_at, status FROM user_accounts_tbl WHERE user_name = '.$db->quote($_REQUEST['login']).' LIMIT 1') ;

 if (empty($user))
     die('Invalid login to game');

 $session->user = $user;
 $uid = $user['uid'];

 //инициализируем основные колоды карт
 $userDekas = 0;

 $userDekas = $db->fetchOne('SELECT COUNT(*) FROM user_dekas_tbl WHERE user_id = '.$db->quote($uid).' LIMIT 1');

  if (empty($userDekas))
  {
      //сгенерировать и заполнить деки юзеру
      $db->beginTransaction();

      $db->query('INSERT INTO user_dekas_tbl SET user_id = '.$uid.', deka_id = 1, cards_counter = 0, updated_at = UNIX_TIMESTAMP() ');
      $deka_id = $db->lastInsertId('user_dekas_tbl');
      //дать юзеру пару карт: деньги + две карточки доступов

      $_x = 5;
      while($_x > 0)
      {
          $db->query('INSERT INTO user_cards_tbl SET user_id = '.$uid.', card_id = '.$_x.', deka_id = '.$deka_id.', cards_count = 10, updated_at = UNIX_TIMESTAMP()');
          $_x--;
      }

      $_x = 10;
      while($_x > 5)
      {
          $db->query('INSERT INTO user_cards_tbl SET user_id = '.$uid.', card_id = '.$_x.', deka_id = '.$deka_id.', cards_count = 3, updated_at = UNIX_TIMESTAMP()');
          $_x--;
      }

      $db->query('INSERT INTO user_dekas_tbl SET user_id = '.$uid.', deka_id = 2, cards_counter = 0, updated_at = UNIX_TIMESTAMP() ');
      $db->query('INSERT INTO user_dekas_tbl SET user_id = '.$uid.', deka_id = 3, cards_counter = 0, updated_at = UNIX_TIMESTAMP() ');
      $db->query('INSERT INTO user_dekas_tbl SET user_id = '.$uid.', deka_id = 4, cards_counter = 0, updated_at = UNIX_TIMESTAMP() ');
      $db->query('INSERT INTO user_dekas_tbl SET user_id = '.$uid.', deka_id = 5, cards_counter = 0, updated_at = UNIX_TIMESTAMP() ');




      $db->commit();
  }

  $dekas = Array();


  $sql = 'SELECT user_dekas_tbl.deka_id, cards_counter, updated_at, deka_name, deka_max_cards, deka_type, deka_desc
         FROM  system_cards_dekas, user_dekas_tbl
         WHERE user_dekas_tbl.user_id = '.$db->quote($uid).' AND system_cards_dekas.deka_id = user_dekas_tbl.deka_id ';

  $res = $db->fetchAll($sql);

  foreach($res as $x)
  {
      $x['cards'] = Array();
      $dekas[ $x['deka_id'] ] = $x;
  }

  $sql = 'SELECT system_cards_tbl.*, user_cards_tbl.card_id, user_cards_tbl.deka_id, user_cards_tbl.cards_count FROM system_cards_tbl, user_cards_tbl
          WHERE user_cards_tbl.user_id = '.$db->quote($uid).' AND system_cards_tbl.сid = user_cards_tbl.card_id ';
  $res = $db->fetchAll($sql);


  foreach($res as $x)
  {
    $dekas[ $x['deka_id'] ]['cards'][ $x['card_id'] ] = $x;
  }

*/

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
	<link rel="icon" type="image/ico" href="<?php echo $url['static_domain']; ?>/favicon.ico" />
	
    <!-- Le styles -->
    <link href="<?php echo $url['static_domain']; ?>/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
         		  
		  .panel_main {
			padding-top:50px;
			width:100%;
			height:100%;
		  }
		  
		  .baraja-container p {
				font-size: 12px;
				font-weight: 700;
				padding: 0 10px;
				margin: 10px 3px 0;
			}

		   ul.baraja-container li {
				border-radius: 10px;
				padding: 5px;
				box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08);
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;
			}

			.baraja-container li img {
				display: block;
				margin: 0 auto;
				width: 100%;
				border-radius: 10px 10px 0 0;
			}
		  
		  
    </style>
    <link href="<?php echo $url['static_domain']; ?>/css/bootstrap-responsive.css" rel="stylesheet">
	<link href="<?php echo $url['static_domain']; ?>/css/baraja.css" rel="stylesheet">
	<link href="<?php echo $url['static_domain']; ?>/css/animate.css" rel="stylesheet">
	
	<link href="<?php echo $url['static_domain']; ?>/css/jquery.pnotify.default.css" rel="stylesheet">
	<link href="<?php echo $url['static_domain']; ?>/css/jquery.pnotify.default.icons.css" rel="stylesheet">
	

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
		
		function fbug(i){ 
			if (typeof(console) != 'undefined')
				console.log(i);
		}
		
        var gPlayer = {
			type: 'human', //bully
			user: <?php echo json_decode($user, true); ?>,
			id: 1,

			avatar: null, //карта аватара 
			deka: null, 
			
			status: 'online' 		
		};
		
		var gUrl = {
			stat: '<?php echo $url['static_domain']; ?>'		
		}
		
		
		var gGame = {
			curBattle: null,
			
			tpl: null,
			curDeka: null, //текущий елемент baraja
		
			init: function(){
				//инит темплейт кеша 
				gGame.tpl = $('#game-templates-markup');
				
				var _player2 = _.clone( gPlayer );
				
					if (gPlayer.type == 'model')
						_player2.type = 'bully';
					else
						_player2.type = 'model';
				
				
				//получим персонажа, в зависимости от выбора 
				gPlayer.avatar = gCards.getAvatars( gPlayer.type );
				_player2.avatar = gCards.getAvatars( _player2.type );
				
				gPlayer.name = gPlayer.avatar.name;
				_player2.name = _player2.avatar.name;
				
				//теперь деки карт 
				gPlayer.deka = gCards.getDeka();
				_player2.deka = gCards.getDeka();
				
				//а теперь инит сражения 
				gBattle.init(gPlayer, _player2);				
					
				
			},

						
			//срабатывает на клик по карте из деки 
			onCardClick: function(panel_id, card_id, card_el, panel_el){
				
				if (!card_el.hasClass('activeCard'))
				{
					//card_el.attr('originalZindex', card_el.css('zIndex')); //сохраним для перетасовки 
					//card_el.css('zIndex', game.curMaxZindex+1);
					card_el.removeClass('activeCard').addClass('activeCard');
					//game.curMaxZindex++;
					
					game.renderOneCard(card_id, card_el, panel_el);
				}
				
				//дальше просто код клика по карте 
			},
			
			//рендер одной карты которую выбрали кликом 
			renderOneCard: function(card_id, card_el, panel_el){
				var c = card_el.clone();
					c.css({'transform':'','transform-origin':'','transition':''});
				
				panel_el.find('.current_card_item').empty().append( c );
			
			}
		};
		

    </script>
	<!-- Игровые файлы -->
	<script src="js/utils.js"></script>
	<script src="js/cards.js"></script>
	<script src="js/battle.js"></script>

</head>

<body style="background-color:#D6D0D2;">

<div class="container-fluid" style="">
    <div class="row-fluid">
			
        <div class="span12">
            <!--Body content-->
			<div style="background-color:#CFC6CA;">
				<h3>Frontlines War: Линия фронта - он-лайн карточная тактическая стратегия &nbsp;<span class="label label-important" style="position:absolute;">Gameplay preview</span></h3>
			</div>	
			 
			<div class="main_panel_el" style="">
				<div class="player_vs_player_deka" style="height:340px;text-align:center;width:100%;">
				<center>
					<table>
						<tr>
							<td class="cube_block" style="width:270px;text-align:center;">
								<center>
									<h2 class="playerRoundName" style="color:green;"> Ваш ход! </h2>
								</center>
								<center>
									<button class="btn btn-danger getMyGoPoints" style="cursor:pointer;" type="button">Кидать кубик</button>	
								</center>
<center>
								<table style="margin-top:10px;text-align:center;">
									<td style="width:50px;border-width:1px;border-color:black;border-style:solid;padding:5px;">
										<img src="<?php echo $url['static_domain']; ?>/img/cube/0.png" align="absmiddle" class="cube_1" />
									</td>
									<td style="width:50px;border-width:1px;border-color:black;border-style:solid;padding:5px;">
										<img src="<?php echo $url['static_domain']; ?>/img/cube/0.png" align="absmiddle" class="cube_2" />
									</td>
								</table>
</center>								
								<div style="text-align:center;margin-top:30px;">
									<h1 class="currentPoints" style="line-height:0px;">-</h1>
									<div style="font-size:11px;">действий на ход</div>
								</div>
								
								<center>
									<button class="btn btn-success finishMyRound" style="cursor:pointer;" type="button">Ходить!</button>	
								</center>

							</td>
							<td class="card_block" style="width:210px;">
								<ul class="baraja-container player_0_el"></ul>
							</td>
							<td style="width:50px;padding:10px;text-align:center;">
								<h2>vs</h2>
							</td>
							<td class="card_block" style="width:210px;">
								<ul class="baraja-container player_1_el"></ul>
							</td>
							<td class="card_block cur_round_deka" style="width:210px;padding-left:10px;">
								<ul class="baraja-container "></ul>
							</td>
						</tr>
					</table>
				</div>
				<div>
					<h3 style="margin-top:0px;padding-bottom:0px;">					
					    <ul class="nav nav-pills">
							<li>
								<a href="javascript: void;"> Мои карты: </a>
							</li>
							<li class="active">
								<a href="javascript: void gBattle.playerDeka.previous();"> < </a>
							</li>
							<li class="">
								<a href="javascript: void gBattle.rollOutDeka();"> Показать </a>
							</li>
							<li class="active">
								<a href="javascript: void gBattle.playerDeka.next();"> > </a>
							</li>
							
							<li class="active">
								<a href="javascript: void gBattle.playerCardTypeChange(gBattle.players[0], 'stance', true);"> Стойки </a>
							</li>
							<li class="active">
								<a href="javascript: void gBattle.playerCardTypeChange(gBattle.players[0], 'strike', true);"> Удары </a>
							</li>
							<li class="active">
								<a href="javascript: void gBattle.playerCardTypeChange(gBattle.players[0], 'block', true);"> Блоки </a>
							</li>
							
							<li class="pull-right curRoundUserpoints_el">
								осталось <span class="curRoundUserpoints">0</span> действий
							</li>
						</ul>
						
					</h3>
				</div>
				<div style="text-align:center;" class="player_card_deka">
						
						<ul class="baraja-container" style="">
							<li class="card card_el combatTypeCard" card-type="stance" style="box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08);">
								<div style="text-align:left;"><span class="label">Стойки</span> <span class="badge badge-info pull-right">2</span></div>
								<img src="<?php echo $url['static_domain']; ?>/img/cards/stance_default.jpg" alt="image1" /><h4>Боевые стойки</h4><p>Начальные позиции в любой драке</p>
								
							</li>
							<li class="card card_el combatTypeCard" card-type="strike" style="box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08);">
								<div style="text-align:left;"><span class="label label-success">Удары</span> <span class="badge badge-info pull-right">2</span></div>
								<img src="<?php echo $url['static_domain']; ?>/img/cards/strike_default.jpg" alt="image1" /><h4>Удары руками и ногами</h4><p>Наносите повреждения противнику</p>
								<span class="badge">карт: 2</span>
							</li>	
							<li class="card card_el combatTypeCard" card-type="block" style="box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08);">
								<div style="text-align:left;"><span class="label label-important">Блоки</span> <span class="badge badge-info pull-right">2</span></div>
								<img src="<?php echo $url['static_domain']; ?>/img/cards/block_default.jpg" alt="image1" /><h4>Блоки</h4><p>Защита от ударов врага</p>
								<span class="badge">карт: 2</span>
							</li>	
						</ul>
			
							
				</div>
				</center>
				</div>

			</div>
			<!--<div class="span3">
				<div style="margin-top:30px;">
					<h3>Текущий ход</h3>
				</div>
			</div> -->
			
        </div>

    </div>
</div>

<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo $url['static_domain']; ?>/js/jquery/jquery-1.9.1.min.js"></script>
<script src="<?php echo $url['static_domain']; ?>/js/libs/bootstrap.min.js"></script>
<script src="<?php echo $url['static_domain']; ?>/js/libs/modernizr.custom.js"></script>
<script src="<?php echo $url['static_domain']; ?>/js/libs/lodash.min.js"></script>
<script src="<?php echo $url['static_domain']; ?>/js/plugins/jquery.baraja.js"></script>
<script src="<?php echo $url['static_domain']; ?>/js/plugins/jquery.easing.js"></script>
<script src="<?php echo $url['static_domain']; ?>/js/plugins/jquery.pnotify.js"></script>


<script>
$( document ).ready( function(){

	$.pnotify.defaults.history = false;
	
	gGame.init();


});
</script>

<!-- templates -->
<div id="game-templates-markup" style="visibility:hidden;display:none;">
				
	<!-- набор карт VIP доступов -->
	<div class="base_deka_cards">
		<!-- набор карт VIP доступов -->
		<ul class="baraja-container">
			<li class="card card_el" card-id="0">
				<span class="label label-warning">Стойки</span>
				<img src="<?php echo $url['static_domain']; ?>/img/cards/stance_default.jpg" alt="image1" /><h4>VISA Platinum</h4><p>Банковская карта премиум класса</p>
				<span class="badge">карт: 2</span>
			</li>
			<li class="card card_el" card-id="1">
				<span class="label label-warning">Удары</span>
				<img src="<?php echo $url['static_domain']; ?>/img/cards/strike_default.jpg" alt="image1" /><h4>VISA Platinum</h4><p>Банковская карта премиум класса</p>
				<span class="badge">карт: 2</span>
			</li>	
			<li class="card card_el" card-id="2">
				<span class="label label-warning">Блоки</span>
				<img src="<?php echo $url['static_domain']; ?>/img/cards/block_default.jpg" alt="image1" /><h4>VISA Platinum</h4><p>Банковская карта премиум класса</p>
				<span class="badge">карт: 2</span>
			</li>	
		</ul>
	</div>


</div>
</body>
</html>