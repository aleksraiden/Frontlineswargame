/*
	Общий список карт 
	
	Карты для двух расс:
	
	- хаггеры (NPC инопланетяни)
	- люди
*/

var gCards = {
	types: ['hagger','human'],
	
	//юнити обоих сторон 
	units: {
		'hagger' : [
			{
				id: 1,
				name: 'Пехотинец', //имя
				type: 'hagger', //тип карты 
				desc: 'Рядовой воин харрерской армии. Не очень опасный, если один', //краткое описание
				rarity: 1, //по шкале от 1 до 100 уникальность карты. Влияет на распределение в деках 
				
				health: 100, //общий уровень жизни 
				strike: 10, //обобщенный уровень атаки 
				block: 2, //обобщенный уровень защиты
				flee: 2 //маневренность
			},
			{
				id: 2,
				name: 'Сержант',
				type: 'hagger',  
				desc: 'Тренированный и закаленный воин. Превосходный стрелок и яростный защитник владений своей рассы',
				rarity: 3,
				
				
				health: 100, 
				strike: 14, 
				block: 4, 
				flee: 3 
			},
			{
				id: 3,
				name: 'Гвардеец',
				type: 'hagger',  
				desc: 'Элитные воины, охраняющие самые ценные земли и обьекты. Не стоит их недооценивать',
				rarity: 5,
				
				
				health: 100, 
				strike: 14, 
				block: 4, 
				flee: 3 
			},
			{
				id: 4,
				name: 'Легат',
				type: 'hagger',  
				desc: 'Специальный генно-модифицированный пехотинец с дополнительной защитой',
				rarity: 7,
				
				health: 100, 
				strike: 14, 
				block: 4, 
				flee: 3 
			},
			{
				id: 5,
				name: 'Легионер',
				type: 'hagger',  
				desc: 'Более совершенный легат, в одиночку превосходящий своей мощью обычные пехотные отряды',
				rarity: 10,
				
				health: 100, 
				strike: 14, 
				block: 4, 
				flee: 3 
			},
			{
				id: 6,
				name: 'Герник',
				type: 'hagger',  
				desc: 'Жестокие и беспощадные солдаты, которых сложно назвать даже ксеноморфом',
				rarity: 12,
				
				health: 100, 
				strike: 14, 
				block: 4, 
				flee: 3 
			},
			{
				id: 7,
				name: 'Морф',
				type: 'hagger',  
				desc: 'Результат дальнейших эксперементов с генетикой и попытка воссоздать древнюю жестокость животных',
				rarity: 18,
				
				health: 100, 
				strike: 14, 
				block: 4, 
				flee: 3 
			},
			{
				id: 8,
				name: 'Скаут',
				type: 'hagger',  
				desc: 'Очень быстрый и коварный противник. В одиночку против него нет шансов. Те, кто выжил - легенды',
				rarity: 25,
				
				health: 100, 
				strike: 14, 
				block: 4, 
				flee: 3 
			},
			{
				id: 9,
				name: 'Проскрипт',
				type: 'hagger',  
				desc: 'Неповоротливый и мощный противник, защищающий свои владения до самого конца',
				rarity: 30,
				
				health: 100, 
				strike: 14, 
				block: 4, 
				flee: 3 
			},
			{
				id: 10,
				name: 'Хищник',
				type: 'hagger',  
				desc: 'Идеальный воин, гроза для любого воителя. Хорошо, что их не так много',
				rarity: 50,
				
				health: 100, 
				strike: 14, 
				block: 4, 
				flee: 3 
			},
			{
				id: 11,
				name: 'Марианец',
				type: 'hagger',  
				desc: 'Ассимилировав другую рассу, марианцев, Хаггеры переняли их воинские обычаи и технологии',
				rarity: 55,
				
				health: 100, 
				strike: 14, 
				block: 4, 
				flee: 3 
			},
			{
				id: 12,
				name: 'Преторианец',
				type: 'hagger',  
				desc: 'Устрашающая смесь машины и существа, рожденный в пороке сын технологий за пределами этики',
				rarity: 70,
				
				health: 100, 
				strike: 14, 
				block: 4, 
				flee: 3 
			},
			{
				id: 13,
				name: 'Тессеракт',
				type: 'hagger',  
				desc: 'Попытавшись усвоить опыт внеземных расс, даже Хаггеры иногда сходили с ума. Вижившие стали безумными воинами',
				rarity: 90,
				
				health: 100, 
				strike: 14, 
				block: 4, 
				flee: 3 
			}
		],
		'human':[
			{
				id: 1,
				name: 'Легкий пехотинец', //имя
				type: 'human', //тип карты 
				desc: 'Рядовой солдат пехотных войск', //краткое описание
				rarity: 1, //по шкале от 1 до 100 уникальность карты. Влияет на распределение в деках 
				
				health: 100, //общий уровень жизни 
				strike: 11, //обобщенный уровень атаки 
				block: 2, //обобщенный уровень защиты
				flee: 2 //маневренность
			},
			{
				id: 2,
				name: 'Тяжелый пехотинец', 
				type: 'human', 
				desc: 'Рядовой солдат пехотных войск', 
				rarity: 3, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 3,
				name: 'Спецназовец', 
				type: 'human', 
				desc: 'Отлично подготовленный и вооруженный солдат морской пехоты', 
				rarity: 5, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 4,
				name: 'Штурмовик', 
				type: 'human', 
				desc: 'Специальне войска для штурма хорошо укрепленных рубежей противника', 
				rarity: 10,
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 5,
				name: 'Тяжелый штурмовик', 
				type: 'human', 
				desc: 'Суперсолдат на поле боя, способный собой заменить небольшой пехотный отряд в полной выкладке', 
				rarity: 12, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 6,
				name: 'Легкий танк', 
				type: 'human', 
				desc: 'Легкая бронированная машина для подавления пехоты противника', 
				rarity: 20, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 7,
				name: 'Средний танк', 
				type: 'human', 
				desc: 'Хорошо вооруженный юнит для подавления серьезных сил противника', 
				rarity: 25, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 8,
				name: 'Разрушитель', 
				type: 'human', 
				desc: 'Тяжелый танк, настоящая бронированная крепость для сложных операций', 
				rarity: 50, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 9,
				name: 'Легкий ховер', 
				type: 'human', 
				desc: 'Причудливая но эффективная помесь вертолета и танка, двух лучших машин войны', 
				rarity: 5, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 10,
				name: 'Тяжелый ховер', 
				type: 'human', 
				desc: 'Серьезная угроза для наземных и летающих врагов, также может нести десантом отряд спецназа', 
				rarity: 15, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 10,
				name: 'Легкий Ган-трак', 
				type: 'human', 
				desc: 'Быстрый и маневренный джип с мощной пушкой. Самое эффективное оружие, если нет тяжелых сил', 
				rarity: 3, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 12,
				name: 'Ховер Ган-трак', 
				type: 'human', 
				desc: 'Ховер на магнито-силовой подушке и несколько скорострельных орудия - лучше не попадаться ему на пути', 
				rarity: 15, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 13,
				name: 'Система Ураган', 
				type: 'human', 
				desc: 'Системы залпового огня просто вижигают всю поверхность, не давай врагам ни шанса на спасения', 
				rarity: 75, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 14,
				name: 'Легкий дрон', 
				type: 'human', 
				desc: 'Автономный БПЛА со слабым вооружением и большой заметностью', 
				rarity: 4, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 15,
				name: 'Тактический штурмовик', 
				type: 'human', 
				desc: 'Одно из самых грозных видов оружия', 
				rarity: 75, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			},
			{
				id: 16,
				name: 'Истребитель', 
				type: 'human', 
				desc: 'Редкий гость на поверхности, более предпочитающий наносить далекие удары из космического пространства', 
				rarity: 95, 
				
				health: 100, 
				strike: 11,
				block: 2,
				flee: 2
			}			
		]
	},

	//выбирает рандомного аватара
	getAvatars: function(){
		var _id = null;
			_id = gUtils.array_rand(gCards.avatars[ type ], 1);			
			
		if (_id != null)
			return _.clone(gCards.avatars[ type ][ _id ]);
		else
			return true;	
	},
	
	//шаблон дефолной деки - каких сколько карт 
	_defaultDeka:{
		stance: 5,
		block: 6,
		strike: 8
	},
	
	//согласно шаблону возвращает набор карт
	getDeka: function(dekaTpl){
		if ((typeof(dekaTpl) == 'undefined') || (dekaTpl == null))
			dekaTpl = gCards._defaultDeka;
		
		

		
		var _deka = []; //массив карт в деке 
		
		_.each(dekaTpl, function(i, ct){
			
			//_log( ct );
			//_log( gCards.combats[ ct ] );
			//_log( dekaTpl[ ct ] );
			//return;
			
			var tmp = gUtils.array_rand(gCards.combats[ ct ], dekaTpl[ ct ]); //получаем ид
			
			_.each(tmp, function(ci){
				_deka.push( _.clone(gCards.combats[ ct ][ ci ]) );
			});
		});
		
		return _deka;	
	},
	
	renderDeka: function(deka, el){
	
	
	}





}