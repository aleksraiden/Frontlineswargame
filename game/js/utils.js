/*
	Разные утилиты
*/

var gUtils = {
	
	//случайный индекс с массива
	array_rand: function(input, num_req) {
	  var indexes = [];
	  var ticks = num_req || 1;
	  var checkDuplicate = function (input, value) {
		var exist = false,
		  index = 0,
		  il = input.length;
		while (index < il) {
		  if (input[index] === value) {
			exist = true;
			break;
		  }
		  index++;
		}
		return exist;
	  };

	  if (Object.prototype.toString.call(input) === '[object Array]' && ticks <= input.length) {
		while (true) {
		  var rand = Math.floor((Math.random() * input.length));
		  if (indexes.length === ticks) {
			break;
		  }
		  if (!checkDuplicate(indexes, rand)) {
			indexes.push(rand);
		  }
		}
	  } else {
		indexes = null;
	  }

	  return ((ticks == 1) ? indexes.join() : indexes);
	},
	
	
	rand: function(min, max) {
	  // http://kevin.vanzonneveld.net
	  // +   original by: Leslie Hoare
	  // +   bugfixed by: Onno Marsman
	  // %          note 1: See the commented out code below for a version which will work with our experimental (though probably unnecessary) srand() function)
	  // *     example 1: rand(1, 1);
	  // *     returns 1: 1
	  var argc = arguments.length;
	  if (argc === 0) {
		min = 0;
		max = 2147483647;
	  } else if (argc === 1) {
		throw new Error('Warning: rand() expects exactly 2 parameters, 1 given');
	  }
	  return Math.floor(Math.random() * (max - min + 1)) + min;
	},
	
	
	shuffle: function(inputArr) {
	  // http://kevin.vanzonneveld.net
	  // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	  // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // +    revised by: Brett Zamir (http://brett-zamir.me)
	  // +   improved by: Brett Zamir (http://brett-zamir.me)
	  // %        note 1: This function deviates from PHP in returning a copy of the array instead
	  // %        note 1: of acting by reference and returning true; this was necessary because
	  // %        note 1: IE does not allow deleting and re-adding of properties without caching
	  // %        note 1: of property position; you can set the ini of "phpjs.strictForIn" to true to
	  // %        note 1: get the PHP behavior, but use this only if you are in an environment
	  // %        note 1: such as Firefox extensions where for-in iteration order is fixed and true
	  // %        note 1: property deletion is supported. Note that we intend to implement the PHP
	  // %        note 1: behavior by default if IE ever does allow it; only gives shallow copy since
	  // %        note 1: is by reference in PHP anyways
	  // *     example 1: ini_set('phpjs.strictForIn', true);
	  // *     example 1: shuffle({5:'a', 2:'3', 3:'c', 4:5, 'q':5});
	  // *     returns 1: {5:'a', 4:5, 'q':5, 3:'c', 2:'3'}
	  // *     example 2: ini_set('phpjs.strictForIn', true);
	  // *     example 2: var data = {5:'a', 2:'3', 3:'c', 4:5, 'q':5};
	  // *     example 2: shuffle(data);
	  // *     results 2: {5:'a', 'q':5, 3:'c', 2:'3', 4:5}
	  // *     returns 2: true
	  var valArr = [],
		k = '',
		i = 0,
		strictForIn = false,
		populateArr = [];

	  for (k in inputArr) { // Get key and value arrays
		if (inputArr.hasOwnProperty(k)) {
		  valArr.push(inputArr[k]);
		  if (strictForIn) {
			delete inputArr[k];
		  }
		}
	  }
	  valArr.sort(function () {
		return 0.5 - Math.random();
	  });

	  // BEGIN REDUNDANT
	  this.php_js = this.php_js || {};
	  this.php_js.ini = this.php_js.ini || {};
	  // END REDUNDANT
	  strictForIn = this.php_js.ini['phpjs.strictForIn'] && this.php_js.ini['phpjs.strictForIn'].local_value && this.php_js.ini['phpjs.strictForIn'].local_value !== 'off';
	  populateArr = strictForIn ? inputArr : populateArr;

	  for (i = 0; i < valArr.length; i++) { // Repopulate the old array
		populateArr[i] = valArr[i];
	  }

	  return strictForIn || populateArr;
	}








}