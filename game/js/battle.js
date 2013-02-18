/**
	Сражение
**/

var gBattle = {

	players: [],
	
	curPlayerPos: 0, //кто сейчас ходит 
	playerDeka: null, //барайя обьект карт юзера 
	maxCardPerRound: 3, //сколько карт максимум в раунд можно выложить 
	
	curRoundCards: [[],[]], //обоих игроков 
	curRoundDeka:[[],[]],
	
	curRoundpoints: 0, //сколько выпало на этом раунде
	
	_cardsTypeTpl: '<li class="card card_el combatTypeCard" card-type="stance" style="box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08);">' + 
						'<div style="text-align:left;"><span class="label">Стойки</span> <span class="badge badge-info pull-right">2</span></div>' + 
						'<img src="'+gUrl.stat+'/img/cards/stance_default.jpg" alt="image1" /><h4>Боевые стойки</h4><p>Начальные позиции в любой драке</p>' +
					'</li>' + 
					'<li class="card card_el combatTypeCard" card-type="strike" style="box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08);">' + 
						'<div style="text-align:left;"><span class="label label-success">Удары</span> <span class="badge badge-info pull-right">2</span></div>' + 
						'<img src="'+gUrl.stat+'/img/cards/strike_default.jpg" alt="image1" /><h4>Удары руками и ногами</h4><p>Наносите повреждения противнику</p>' + 
						'<span class="badge">карт: 2</span>' + 
					'</li>' + 
					'<li class="card card_el combatTypeCard" card-type="block" style="box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08);">'+
						'<div style="text-align:left;"><span class="label label-important">Блоки</span> <span class="badge badge-info pull-right">2</span></div>'+
						'<img src="'+gUrl.stat+'/img/cards/block_default.jpg" alt="image1" /><h4>Блоки</h4><p>Защита от ударов врага</p>'+
						'<span class="badge">карт: 2</span>' +
					'</li>',
	
	
	//начало сражения
	init: function(player1, player2){
_log(player1);
		if (player1.avatar.type == 'model')
			player1.avatar.typeName = '<span class="label label-warning" style="margin-bottom:4px;">СуперМодель</span>';
		else
			player1.avatar.typeName = '<span class="label label-inverse" style="margin-bottom:4px;">Гопник</span>';
			
		if (player2.avatar.type == 'model')
			player2.avatar.typeName = '<span class="label label-warning" style="margin-bottom:4px;">СуперМодель</span>';
		else
			player2.avatar.typeName = '<span class="label label-inverse" style="margin-bottom:4px;">Гопник</span>';
			
		//
		player1.avatar.strike = gUtils.rand(3, 16);
		player2.avatar.strike = gUtils.rand(3, 20);
		
		player1.avatar.block = gUtils.rand(2, 8);
		player2.avatar.block = gUtils.rand(3, 11);
		
		player1.avatar.flee = gUtils.rand(3, 6);
		player2.avatar.flee = gUtils.rand(1, 3);
		
	
		gBattle.players[0] = player1; //это всегда игрок текущий 
		gBattle.players[1] = player2; //это противник 
	
		$('.getMyGoPoints').on('click', function(){
			gBattle.randomGenInit();
		});
		
		$('.finishMyRound').on('click', function(){
			gBattle.goRound();
		});
		
		gBattle.cacheCube();


		//рендерим обоих игроков 
		var el = $('.player_vs_player_deka');
		

		
		
		el.find('.player_0_el').empty().append( '<li class="card card_el">' + 
			''+player1.avatar.typeName+'' + 
			'<img src="'+gUrl.stat+'/img/cards/'+ player1.avatar.type + '_' + player1.avatar.id + '.jpg" alt="image1" /><h4>'+player1.avatar.name+'</h4><p>'+player1.avatar.desc+'</p>' + 
			'<p><div>Данные: <span class="badge badge-success" rel="tooltip" title="Сила удара или приема">'+player1.avatar.strike+'</span> &nbsp;&nbsp; <span class="badge badge-important" rel="tooltip" title="Сила блока против удара">'+player1.avatar.block+'</span> &nbsp;&nbsp; <span class="badge badge-info" rel="tooltip" title="Гибкость и способность к увороту">8</span></div></p>' + 
			'<div class="progress progress-warning progress-striped"><div class="bar" style="width: 100%"></div></div><div style="text-align:center;color:green;font-weight:bold;" style="player_0_health">Жизнь: 100</div></li>' );
			
		el.find('.player_1_el').empty().append( '<li class="card card_el">' + 
			''+player2.avatar.typeName+'' + 
			'<img src="'+gUrl.stat+'/img/cards/'+ player2.avatar.type + '_' + player2.avatar.id + '.jpg" alt="image1" /><h4>'+player2.avatar.name+'</h4><p>'+player2.avatar.desc+'</p>' + 
			'<p><div>Данные: <span class="badge badge-success" rel="tooltip" title="Сила удара или приема">2</span> &nbsp;&nbsp; <span class="badge badge-important" rel="tooltip" title="Сила блока против удара">6</span> &nbsp;&nbsp; <span class="badge badge-info" rel="tooltip" title="Гибкость и способность к увороту">8</span></div></p>' + 
			'<div class="progress progress-danger progress-striped"><div class="bar" style="width: 100%"></div></div><div style="text-align:center;color:red;font-weight:bold;" style="player_0_health">Жизнь: 100</div></li>' );

		el.find('.card_block').baraja();  // baraja-container').baraja();
		el.find('[rel=tooltip]').tooltip();
		
		
		gBattle.playerDeka = $('.main_panel_el .player_card_deka').find('.baraja-container').baraja();
		
		gBattle.playerDeka.fan( {
			speed : 100,
			easing : 'ease-out',
			range : 45,
			direction : 'right',
			origin : { x : 25, y : 100 },
			center : true
		});
		
		$('.main_panel_el .player_card_deka').find('.combatTypeCard').click(function(e){
			var _type = $(e.currentTarget).attr('card-type');
				gBattle.playerCardTypeChange(gBattle.players[0], _type, true);
		});
		
	},
	
	//кеширование рисунков для кубиков 
	cacheCube: function(){
		var img = new Image();
			img.src = gUrl.stat + '/img/cube/1.png';
		
			img = new Image();
			img.src = gUrl.stat + '/img/cube/2.png';
			
			img = new Image();
			img.src = gUrl.stat + '/img/cube/3.png';
			
			img = new Image();
			img.src = gUrl.stat + '/img/cube/4.png';
			
			img = new Image();
			img.src = gUrl.stat + '/img/cube/5.png';
			
			img = new Image();
			img.src = gUrl.stat + '/img/cube/6.png';
	
	},
	
	//инит рандомного генератора (крутит кубики)
	_cubeAnimationTimer: null, 
	_cubeEl: null,
	currentPoints:[],
	randomGenInit: function(){
		//if (gBattle._cubeEl.find('.getMyGoPoints').hasClass('disabled')) return;
		
		clearInterval(gBattle._cubeAnimationTimer);
		
		gBattle._cubeEl = $('.main_panel_el');
		gBattle._cubeEl.find('.getMyGoPoints').addClass('disabled');
		
		gBattle._cubeAnimationTimer = setInterval(function(){			
			gBattle._cubeEl.find('.cube_1').prop('src', gUrl.stat + '/img/cube/'+ gUtils.rand(1, 6) +'.png');			
			gBattle._cubeEl.find('.cube_2').prop('src', gUrl.stat + '/img/cube/'+ gUtils.rand(1, 6) +'.png');		
		}, 25);	
		
		
		setTimeout(function(){
			gBattle.currentPoints = gBattle.getCurrentPoints();
			clearInterval(gBattle._cubeAnimationTimer);
			
			gBattle._cubeEl.find('.cube_1').prop('src', gUrl.stat + '/img/cube/'+ gBattle.currentPoints[0] +'.png');			
			gBattle._cubeEl.find('.cube_2').prop('src', gUrl.stat + '/img/cube/'+ gBattle.currentPoints[1] +'.png');
			
			// currentPoints
			gBattle._cubeEl.find('.currentPoints').html( gBattle.currentPoints[0] + gBattle.currentPoints[1] );
			
			gBattle._cubeEl.find('.getMyGoPoints').removeClass('disabled');
			
			gBattle.curRoundpoints = gBattle.currentPoints[0] + gBattle.currentPoints[1];
			
			$('.main_panel_el').find('.curRoundUserpoints_el').html('осталось <span class="curRoundUserpoints">'+gBattle.curRoundpoints+'</span> действий');
			//$('.main_panel_el').find('.curRoundUserpoints').html( gBattle.curRoundpoints );
			
		}, 3000);
	},
	
	//непосредственно выдает очки 
	getCurrentPoints: function(){
		var _cube = [];
		
		_cube.push( gUtils.rand(1, 6) ); 
		_cube.push( gUtils.rand(1, 6) );
		
		return _cube;
	},
	
	//игрок выбрал тип приема для хода 
	playerCardTypeChange: function(player, type, visual){
		if (typeof(visual) == 'undefined')
			visual = false; //не показывать только выбрать 
			
		if (gBattle.curRoundpoints == 0)
		{
			gBattle.error('Сначала киньте кубик!');
			return;
		}
		
		//если выбрали уже достаточно 
		
			
		//удалим, если есть, другие карты и добавим новые  - baraja.add
		gBattle.playerDeka.close();
		
		$('.main_panel_el .player_card_deka').empty();
		gBattle.playerDeka = null;
		var _str = '';

		_.each(player.deka, function(v, i){
			if (v.type == type)
			{
				_str = _str + '<li class="card card_el combatCard" card-type="combat" card-id="'+v.id+'" style="box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08);">'+
					'<div style="text-align:left;"><span class="label label-info" rel="tooltip" title="Стоимость использования карты">'+v.price+'</span>&nbsp;&nbsp;<span class="badge badge-success" rel="tooltip" title="Сила удара или приема">2</span>&nbsp;&nbsp;<span class="badge badge-important" rel="tooltip" title="Сила блока против удара">6</span></div>' +
					'<img src="'+gUrl.stat+'/img/cards/combat_default.jpg" alt="image1" /><h4>'+v.name+'</h4><p>'+v.desc+'</p>' + 
					'<div style="text-align:left;"></div></li>';
			}
		});
		
		$('.main_panel_el .player_card_deka').append( '<ul class="baraja-container" style="">' + _str + '</ul>' ); // gBattle._cardsTypeTpl +
		
		$('.main_panel_el .player_card_deka').find('.combatTypeCard').click(function(e){
			var _type = $(e.currentTarget).attr('card-type');			
				gBattle.playerCardTypeChange(gBattle.players[0], _type, true);
		});
		
		//обработчик комбат-карты 
		$('.main_panel_el .player_card_deka').find('.combatCard').click(function(e){
			var _id = $(e.currentTarget).attr('card-id');			
				gBattle.playerCardSelect(0, gBattle.players[0], _id, visual);
		});		
		
		gBattle.playerDeka = $('.main_panel_el .player_card_deka').find('.baraja-container').baraja();
	
		gBattle.playerDeka.fan( {
			speed : 200,
			easing : 'ease-out',
			range : 75,
			direction : 'right',
			origin : { x : 50, y : 200 },
			center : true 
		});	
		
		$('.main_panel_el').find('[rel=tooltip]').tooltip();	
	},
	
	playerCardSelect: function(pid, player, id, visual){
		var card = null;
		
		_.each(player.deka, function(v){
			if (v.id == id)
			{
				card = v;
				return false;
			}
		});
		
		//мы добавляем карту в деку cur_round_cards
		if ( gBattle.curRoundCards[pid].length >= gBattle.maxCardPerRound )
		{
			gBattle.error('За ход можно выложить не более 3-х карт и общей стоимостью не более, чем выпало на кубиках.');

			return;
		}
		
		var _curRoundUserpoints = 0;
		_.each(gBattle.curRoundCards[pid], function(v){ _curRoundUserpoints =  _curRoundUserpoints + v.price; });
		
		if ( (_curRoundUserpoints + card.price) > gBattle.curRoundpoints )
		{
			gBattle.error('За ход можно выложить карт общей стоимостью не более, чем выпало на кубиках.');
			return;
		}
				
		gBattle.curRoundCards[pid].push( card );
		
		//перерендерить блок с ходом 
		$('.main_panel_el .cur_round_deka').empty();
		
		var _dka = '<ul class="baraja-container ">';
			_curRoundUserpoints = 0;
		
		_.each(gBattle.curRoundCards[pid], function(v){
			_dka = _dka + '<li class="card card_el combatCard" card-type="combat" card-id="'+v.id+'" style="box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08);">'+
					'<div style="text-align:left;"><span class="label label-info" rel="tooltip" title="Стоимость использования карты">'+v.price+'</span>&nbsp;&nbsp;<span class="badge badge-success" rel="tooltip" title="Сила удара или приема">2</span>&nbsp;&nbsp;<span class="badge badge-important" rel="tooltip" title="Сила блока против удара">6</span></div>' +
					'<img src="'+gUrl.stat+'/img/cards/combat_default.jpg" alt="image1" /><h4>'+v.name+'</h4><p>'+v.desc+'</p>' + 
					'</li>';
					
			 _curRoundUserpoints =  _curRoundUserpoints + v.price;
		});
		
		gBattle.curRoundDeka[ pid ] = $('.main_panel_el .cur_round_deka').append(  _dka + '</ul>' ).find('.baraja-container').baraja();
		
		gBattle.curRoundDeka[ pid ].fan( {
			speed : 100,
			easing : 'ease-out',
			range : 1,
			direction : 'right',
			origin : { x : 50, y : 200 },
			center : true,
			translation : 100  
		});	
		
		//посчитаем сумму 
		//
		$('.main_panel_el').find('.curRoundUserpoints').html( gBattle.curRoundpoints - _curRoundUserpoints );
		
		if ((gBattle.curRoundpoints - _curRoundUserpoints) == 0)
		{
			$('.main_panel_el').find('.curRoundUserpoints_el').html('Можно ходить (кнопка Ход)'); //   осталось <span class="curRoundUserpoints">0</span> действий
		}
		
		
		_log( card ); //выбранная карта для хода 	
	},
	
	rollOutDeka: function(){
		gBattle.playerDeka.fan( {
			speed : 200,
			easing : 'ease-out',
			range : 100,
			direction : 'right',
			origin : { x : 50, y : 200 },
			center : true 
		});	
	},
	
	goRound: function(){
		if (gBattle.curRoundpoints == 0)
		{
			gBattle.error('Сначала киньте кубик!');
			return;
		}
		
		
		if  ((gBattle.curRoundDeka == null) || (gBattle.curRoundDeka[ 0 ].length == 0))
		{
			gBattle.error('Вы должны выбрать минимум 1 карту для хода.');
			return;
		}
		
		if ( gBattle.curRoundpoints == 0 )
		{
			gBattle.error('Сначала киньте кубик!');
			return;
		}
		
		alert( 'Ура! Ход! Пиф! Паф! Хуяк!!' );
		
		//теперь надо взять рандомные карты не более 3-х и не более чем очки ему выпало.
		$('.main_panel_el').find('.playerRoundName').html('Ход противника').css('color', 'red');
		
		//крутить его кубик 
		clearInterval(gBattle._cubeAnimationTimer);
		
		gBattle._cubeAnimationTimer = setInterval(function(){			
			gBattle._cubeEl.find('.cube_1').prop('src', gUrl.stat + '/img/cube/'+ gUtils.rand(1, 6) +'.png');			
			gBattle._cubeEl.find('.cube_2').prop('src', gUrl.stat + '/img/cube/'+ gUtils.rand(1, 6) +'.png');		
		}, 25);

		var player2Points = null;
				
		setTimeout(function(){
			player2Points = gBattle.getCurrentPoints();
			clearInterval(gBattle._cubeAnimationTimer);
			
			gBattle._cubeEl.find('.cube_1').prop('src', gUrl.stat + '/img/cube/'+ player2Points[0] +'.png');			
			gBattle._cubeEl.find('.cube_2').prop('src', gUrl.stat + '/img/cube/'+ player2Points[1] +'.png');
			
			$('.main_panel_el').find('.curRoundUserpoints_el').empty().html('У противника <span class="curRoundUserpoints">'+(player2Points[0]+player2Points[1])+'</span> действий');
			
			//теперь выбрать у него карты 
			var player2cards = gBattle.getRandomCardsByPoints(gBattle.players[1], (player2Points[0]+player2Points[1]));
_log('Противник выставил карты');
_log( player2cards );
		
		}, 1500);
	},
	
	//выбирает рандомного количество карт на указанную сумму или меньше.
	getRandomCardsByPoints: function(player, points){
		var _tmp = []; // массив карт со стоимостью меньше  чем очки 
_log('Player2 has ' + points + ' очек действия');		
		_.each(player.deka, function(v){
			if (v.price <= points)
				_tmp.push( v );
		});
		
		var _res = [];
		var _resSumm = 0; //стоимость карт
		//теперь самое интересное - надо выбрать карты чтобы заполнить массив
		
		_tmp = gUtils.shuffle(_tmp); //перемешаем массив 
		
		//var _i = 0;
		var c = null;
		//выбираем случайные карты 
		while (_resSumm < points)
		{
			c = _tmp[ gUtils.array_rand(_tmp, 1) ];
			
			if ((_resSumm + c.price) <= points)
			{
				_res.push(c);
				_resSumm = _resSumm + c.price;
			}
		}
		
		if (_res.length > gBattle.maxCardPerRound)
		{
			var _tmp = gUtils.shuffle(_res);
			var _i = gUtils.array_rand(_res, gBattle.maxCardPerRound);
			
			var _res = [];
			
			_.each(_i, function(v){
				_res.push( _tmp[v] );
			});		
		}
		
		
		return _res;	
	},
	
	
	error: function(txt){
	
		var modal_overlay;
			$.pnotify({
				title: "Ошибка",
				text: txt,
				type: "error",
				icon: "picon picon-object-order-raise",
				delay: 1000,
				history: false,
				stack: false,
				before_open: function(pnotify) {
					// Position this notice in the center of the screen.
					pnotify.css({
						"top": ($(window).height() / 2) - (pnotify.height() / 2),
						"left": ($(window).width() / 2) - (pnotify.width() / 2)
					});
					// Make a modal screen overlay.
					if (modal_overlay) modal_overlay.fadeIn("fast");
					else modal_overlay = $("<div />", {
						"class": "ui-widget-overlay",
						"css": {
							"display": "none",
							"position": "fixed",
							"top": "0",
							"bottom": "0",
							"right": "0",
							"left": "0"
						}
					}).appendTo("body").fadeIn("fast");
				},
				before_close: function() {
					modal_overlay.fadeOut("fast");
				}
			});
			
	},
	
	
	results: null
}