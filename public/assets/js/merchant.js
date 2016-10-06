/**
 * sifter.js
 * Copyright (c) 2013 Brian Reavis & contributors
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this
 * file except in compliance with the License. You may obtain a copy of the License at:
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under
 * the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF
 * ANY KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 *
 * @author Brian Reavis <brian@thirdroute.com>
 */

(function(root, factory) {
	if (typeof define === 'function' && define.amd) {
		define('sifter', factory);
	} else if (typeof exports === 'object') {
		module.exports = factory();
	} else {
		root.Sifter = factory();
	}
}(this, function() {

	/**
	 * Textually searches arrays and hashes of objects
	 * by property (or multiple properties). Designed
	 * specifically for autocomplete.
	 *
	 * @constructor
	 * @param {array|object} items
	 * @param {object} items
	 */
	var Sifter = function(items, settings) {
		this.items = items;
		this.settings = settings || {diacritics: true};
	};

	/**
	 * Splits a search string into an array of individual
	 * regexps to be used to match results.
	 *
	 * @param {string} query
	 * @returns {array}
	 */
	Sifter.prototype.tokenize = function(query) {
		query = trim(String(query || '').toLowerCase());
		if (!query || !query.length) return [];

		var i, n, regex, letter;
		var tokens = [];
		var words = query.split(/ +/);

		for (i = 0, n = words.length; i < n; i++) {
			regex = escape_regex(words[i]);
			if (this.settings.diacritics) {
				for (letter in DIACRITICS) {
					if (DIACRITICS.hasOwnProperty(letter)) {
						regex = regex.replace(new RegExp(letter, 'g'), DIACRITICS[letter]);
					}
				}
			}
			tokens.push({
				string : words[i],
				regex  : new RegExp(regex, 'i')
			});
		}

		return tokens;
	};

	/**
	 * Iterates over arrays and hashes.
	 *
	 * ```
	 * this.iterator(this.items, function(item, id) {
	 *    // invoked for each item
	 * });
	 * ```
	 *
	 * @param {array|object} object
	 */
	Sifter.prototype.iterator = function(object, callback) {
		var iterator;
		if (is_array(object)) {
			iterator = Array.prototype.forEach || function(callback) {
				for (var i = 0, n = this.length; i < n; i++) {
					callback(this[i], i, this);
				}
			};
		} else {
			iterator = function(callback) {
				for (var key in this) {
					if (this.hasOwnProperty(key)) {
						callback(this[key], key, this);
					}
				}
			};
		}

		iterator.apply(object, [callback]);
	};

	/**
	 * Returns a function to be used to score individual results.
	 *
	 * Good matches will have a higher score than poor matches.
	 * If an item is not a match, 0 will be returned by the function.
	 *
	 * @param {object|string} search
	 * @param {object} options (optional)
	 * @returns {function}
	 */
	Sifter.prototype.getScoreFunction = function(search, options) {
		var self, fields, tokens, token_count, nesting;

		self        = this;
		search      = self.prepareSearch(search, options);
		tokens      = search.tokens;
		fields      = search.options.fields;
		token_count = tokens.length;
		nesting     = search.options.nesting;

		/**
		 * Calculates how close of a match the
		 * given value is against a search token.
		 *
		 * @param {mixed} value
		 * @param {object} token
		 * @return {number}
		 */
		var scoreValue = function(value, token) {
			var score, pos;

			if (!value) return 0;
			value = String(value || '');
			pos = value.search(token.regex);
			if (pos === -1) return 0;
			score = token.string.length / value.length;
			if (pos === 0) score += 0.5;
			return score;
		};

		/**
		 * Calculates the score of an object
		 * against the search query.
		 *
		 * @param {object} token
		 * @param {object} data
		 * @return {number}
		 */
		var scoreObject = (function() {
			var field_count = fields.length;
			if (!field_count) {
				return function() { return 0; };
			}
			if (field_count === 1) {
				return function(token, data) {
					return scoreValue(getattr(data, fields[0], nesting), token);
				};
			}
			return function(token, data) {
				for (var i = 0, sum = 0; i < field_count; i++) {
					sum += scoreValue(getattr(data, fields[i], nesting), token);
				}
				return sum / field_count;
			};
		})();

		if (!token_count) {
			return function() { return 0; };
		}
		if (token_count === 1) {
			return function(data) {
				return scoreObject(tokens[0], data);
			};
		}

		if (search.options.conjunction === 'and') {
			return function(data) {
				var score;
				for (var i = 0, sum = 0; i < token_count; i++) {
					score = scoreObject(tokens[i], data);
					if (score <= 0) return 0;
					sum += score;
				}
				return sum / token_count;
			};
		} else {
			return function(data) {
				for (var i = 0, sum = 0; i < token_count; i++) {
					sum += scoreObject(tokens[i], data);
				}
				return sum / token_count;
			};
		}
	};

	/**
	 * Returns a function that can be used to compare two
	 * results, for sorting purposes. If no sorting should
	 * be performed, `null` will be returned.
	 *
	 * @param {string|object} search
	 * @param {object} options
	 * @return function(a,b)
	 */
	Sifter.prototype.getSortFunction = function(search, options) {
		var i, n, self, field, fields, fields_count, multiplier, multipliers, get_field, implicit_score, sort;

		self   = this;
		search = self.prepareSearch(search, options);
		sort   = (!search.query && options.sort_empty) || options.sort;

		/**
		 * Fetches the specified sort field value
		 * from a search result item.
		 *
		 * @param  {string} name
		 * @param  {object} result
		 * @return {mixed}
		 */
		get_field = function(name, result) {
			if (name === '$score') return result.score;
			return getattr(self.items[result.id], name, options.nesting);
		};

		// parse options
		fields = [];
		if (sort) {
			for (i = 0, n = sort.length; i < n; i++) {
				if (search.query || sort[i].field !== '$score') {
					fields.push(sort[i]);
				}
			}
		}

		// the "$score" field is implied to be the primary
		// sort field, unless it's manually specified
		if (search.query) {
			implicit_score = true;
			for (i = 0, n = fields.length; i < n; i++) {
				if (fields[i].field === '$score') {
					implicit_score = false;
					break;
				}
			}
			if (implicit_score) {
				fields.unshift({field: '$score', direction: 'desc'});
			}
		} else {
			for (i = 0, n = fields.length; i < n; i++) {
				if (fields[i].field === '$score') {
					fields.splice(i, 1);
					break;
				}
			}
		}

		multipliers = [];
		for (i = 0, n = fields.length; i < n; i++) {
			multipliers.push(fields[i].direction === 'desc' ? -1 : 1);
		}

		// build function
		fields_count = fields.length;
		if (!fields_count) {
			return null;
		} else if (fields_count === 1) {
			field = fields[0].field;
			multiplier = multipliers[0];
			return function(a, b) {
				return multiplier * cmp(
					get_field(field, a),
					get_field(field, b)
				);
			};
		} else {
			return function(a, b) {
				var i, result, a_value, b_value, field;
				for (i = 0; i < fields_count; i++) {
					field = fields[i].field;
					result = multipliers[i] * cmp(
						get_field(field, a),
						get_field(field, b)
					);
					if (result) return result;
				}
				return 0;
			};
		}
	};

	/**
	 * Parses a search query and returns an object
	 * with tokens and fields ready to be populated
	 * with results.
	 *
	 * @param {string} query
	 * @param {object} options
	 * @returns {object}
	 */
	Sifter.prototype.prepareSearch = function(query, options) {
		if (typeof query === 'object') return query;

		options = extend({}, options);

		var option_fields     = options.fields;
		var option_sort       = options.sort;
		var option_sort_empty = options.sort_empty;

		if (option_fields && !is_array(option_fields)) options.fields = [option_fields];
		if (option_sort && !is_array(option_sort)) options.sort = [option_sort];
		if (option_sort_empty && !is_array(option_sort_empty)) options.sort_empty = [option_sort_empty];

		return {
			options : options,
			query   : String(query || '').toLowerCase(),
			tokens  : this.tokenize(query),
			total   : 0,
			items   : []
		};
	};

	/**
	 * Searches through all items and returns a sorted array of matches.
	 *
	 * The `options` parameter can contain:
	 *
	 *   - fields {string|array}
	 *   - sort {array}
	 *   - score {function}
	 *   - filter {bool}
	 *   - limit {integer}
	 *
	 * Returns an object containing:
	 *
	 *   - options {object}
	 *   - query {string}
	 *   - tokens {array}
	 *   - total {int}
	 *   - items {array}
	 *
	 * @param {string} query
	 * @param {object} options
	 * @returns {object}
	 */
	Sifter.prototype.search = function(query, options) {
		var self = this, value, score, search, calculateScore;
		var fn_sort;
		var fn_score;

		search  = this.prepareSearch(query, options);
		options = search.options;
		query   = search.query;

		// generate result scoring function
		fn_score = options.score || self.getScoreFunction(search);

		// perform search and sort
		if (query.length) {
			self.iterator(self.items, function(item, id) {
				score = fn_score(item);
				if (options.filter === false || score > 0) {
					search.items.push({'score': score, 'id': id});
				}
			});
		} else {
			self.iterator(self.items, function(item, id) {
				search.items.push({'score': 1, 'id': id});
			});
		}

		fn_sort = self.getSortFunction(search, options);
		if (fn_sort) search.items.sort(fn_sort);

		// apply limits
		search.total = search.items.length;
		if (typeof options.limit === 'number') {
			search.items = search.items.slice(0, options.limit);
		}

		return search;
	};

	// utilities
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

	var cmp = function(a, b) {
		if (typeof a === 'number' && typeof b === 'number') {
			return a > b ? 1 : (a < b ? -1 : 0);
		}
		a = asciifold(String(a || ''));
		b = asciifold(String(b || ''));
		if (a > b) return 1;
		if (b > a) return -1;
		return 0;
	};

	var extend = function(a, b) {
		var i, n, k, object;
		for (i = 1, n = arguments.length; i < n; i++) {
			object = arguments[i];
			if (!object) continue;
			for (k in object) {
				if (object.hasOwnProperty(k)) {
					a[k] = object[k];
				}
			}
		}
		return a;
	};

	/**
	 * A property getter resolving dot-notation
	 * @param  {Object}  obj     The root object to fetch property on
	 * @param  {String}  name    The optionally dotted property name to fetch
	 * @param  {Boolean} nesting Handle nesting or not
	 * @return {Object}          The resolved property value
	 */
	var getattr = function(obj, name, nesting) {
	    if (!obj || !name) return;
	    if (!nesting) return obj[name];
	    var names = name.split(".");
	    while(names.length && (obj = obj[names.shift()]));
	    return obj;
	};

	var trim = function(str) {
		return (str + '').replace(/^\s+|\s+$|/g, '');
	};

	var escape_regex = function(str) {
		return (str + '').replace(/([.?*+^$[\]\\(){}|-])/g, '\\$1');
	};

	var is_array = Array.isArray || (typeof $ !== 'undefined' && $.isArray) || function(object) {
		return Object.prototype.toString.call(object) === '[object Array]';
	};

	var DIACRITICS = {
		'a': '[aḀḁĂăÂâǍǎȺⱥȦȧẠạÄäÀàÁáĀāÃãÅåąĄÃąĄ]',
		'b': '[b␢βΒB฿𐌁ᛒ]',
		'c': '[cĆćĈĉČčĊċC̄c̄ÇçḈḉȻȼƇƈɕᴄＣｃ]',
		'd': '[dĎďḊḋḐḑḌḍḒḓḎḏĐđD̦d̦ƉɖƊɗƋƌᵭᶁᶑȡᴅＤｄð]',
		'e': '[eÉéÈèÊêḘḙĚěĔĕẼẽḚḛẺẻĖėËëĒēȨȩĘęᶒɆɇȄȅẾếỀềỄễỂểḜḝḖḗḔḕȆȇẸẹỆệⱸᴇＥｅɘǝƏƐε]',
		'f': '[fƑƒḞḟ]',
		'g': '[gɢ₲ǤǥĜĝĞğĢģƓɠĠġ]',
		'h': '[hĤĥĦħḨḩẖẖḤḥḢḣɦʰǶƕ]',
		'i': '[iÍíÌìĬĭÎîǏǐÏïḮḯĨĩĮįĪīỈỉȈȉȊȋỊịḬḭƗɨɨ̆ᵻᶖİiIıɪＩｉ]',
		'j': '[jȷĴĵɈɉʝɟʲ]',
		'k': '[kƘƙꝀꝁḰḱǨǩḲḳḴḵκϰ₭]',
		'l': '[lŁłĽľĻļĹĺḶḷḸḹḼḽḺḻĿŀȽƚⱠⱡⱢɫɬᶅɭȴʟＬｌ]',
		'n': '[nŃńǸǹŇňÑñṄṅŅņṆṇṊṋṈṉN̈n̈ƝɲȠƞᵰᶇɳȵɴＮｎŊŋ]',
		'o': '[oØøÖöÓóÒòÔôǑǒŐőŎŏȮȯỌọƟɵƠơỎỏŌōÕõǪǫȌȍՕօ]',
		'p': '[pṔṕṖṗⱣᵽƤƥᵱ]',
		'q': '[qꝖꝗʠɊɋꝘꝙq̃]',
		'r': '[rŔŕɌɍŘřŖŗṘṙȐȑȒȓṚṛⱤɽ]',
		's': '[sŚśṠṡṢṣꞨꞩŜŝŠšŞşȘșS̈s̈]',
		't': '[tŤťṪṫŢţṬṭƮʈȚțṰṱṮṯƬƭ]',
		'u': '[uŬŭɄʉỤụÜüÚúÙùÛûǓǔŰűŬŭƯưỦủŪūŨũŲųȔȕ∪]',
		'v': '[vṼṽṾṿƲʋꝞꝟⱱʋ]',
		'w': '[wẂẃẀẁŴŵẄẅẆẇẈẉ]',
		'x': '[xẌẍẊẋχ]',
		'y': '[yÝýỲỳŶŷŸÿỸỹẎẏỴỵɎɏƳƴ]',
		'z': '[zŹźẐẑŽžŻżẒẓẔẕƵƶ]'
	};

	var asciifold = (function() {
		var i, n, k, chunk;
		var foreignletters = '';
		var lookup = {};
		for (k in DIACRITICS) {
			if (DIACRITICS.hasOwnProperty(k)) {
				chunk = DIACRITICS[k].substring(2, DIACRITICS[k].length - 1);
				foreignletters += chunk;
				for (i = 0, n = chunk.length; i < n; i++) {
					lookup[chunk.charAt(i)] = k;
				}
			}
		}
		var regexp = new RegExp('[' +  foreignletters + ']', 'g');
		return function(str) {
			return str.replace(regexp, function(foreignletter) {
				return lookup[foreignletter];
			}).toLowerCase();
		};
	})();


	// export
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

	return Sifter;
}));



/**
 * microplugin.js
 * Copyright (c) 2013 Brian Reavis & contributors
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this
 * file except in compliance with the License. You may obtain a copy of the License at:
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under
 * the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF
 * ANY KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 *
 * @author Brian Reavis <brian@thirdroute.com>
 */

(function(root, factory) {
	if (typeof define === 'function' && define.amd) {
		define('microplugin', factory);
	} else if (typeof exports === 'object') {
		module.exports = factory();
	} else {
		root.MicroPlugin = factory();
	}
}(this, function() {
	var MicroPlugin = {};

	MicroPlugin.mixin = function(Interface) {
		Interface.plugins = {};

		/**
		 * Initializes the listed plugins (with options).
		 * Acceptable formats:
		 *
		 * List (without options):
		 *   ['a', 'b', 'c']
		 *
		 * List (with options):
		 *   [{'name': 'a', options: {}}, {'name': 'b', options: {}}]
		 *
		 * Hash (with options):
		 *   {'a': { ... }, 'b': { ... }, 'c': { ... }}
		 *
		 * @param {mixed} plugins
		 */
		Interface.prototype.initializePlugins = function(plugins) {
			var i, n, key;
			var self  = this;
			var queue = [];

			self.plugins = {
				names     : [],
				settings  : {},
				requested : {},
				loaded    : {}
			};

			if (utils.isArray(plugins)) {
				for (i = 0, n = plugins.length; i < n; i++) {
					if (typeof plugins[i] === 'string') {
						queue.push(plugins[i]);
					} else {
						self.plugins.settings[plugins[i].name] = plugins[i].options;
						queue.push(plugins[i].name);
					}
				}
			} else if (plugins) {
				for (key in plugins) {
					if (plugins.hasOwnProperty(key)) {
						self.plugins.settings[key] = plugins[key];
						queue.push(key);
					}
				}
			}

			while (queue.length) {
				self.require(queue.shift());
			}
		};

		Interface.prototype.loadPlugin = function(name) {
			var self    = this;
			var plugins = self.plugins;
			var plugin  = Interface.plugins[name];

			if (!Interface.plugins.hasOwnProperty(name)) {
				throw new Error('Unable to find "' +  name + '" plugin');
			}

			plugins.requested[name] = true;
			plugins.loaded[name] = plugin.fn.apply(self, [self.plugins.settings[name] || {}]);
			plugins.names.push(name);
		};

		/**
		 * Initializes a plugin.
		 *
		 * @param {string} name
		 */
		Interface.prototype.require = function(name) {
			var self = this;
			var plugins = self.plugins;

			if (!self.plugins.loaded.hasOwnProperty(name)) {
				if (plugins.requested[name]) {
					throw new Error('Plugin has circular dependency ("' + name + '")');
				}
				self.loadPlugin(name);
			}

			return plugins.loaded[name];
		};

		/**
		 * Registers a plugin.
		 *
		 * @param {string} name
		 * @param {function} fn
		 */
		Interface.define = function(name, fn) {
			Interface.plugins[name] = {
				'name' : name,
				'fn'   : fn
			};
		};
	};

	var utils = {
		isArray: Array.isArray || function(vArg) {
			return Object.prototype.toString.call(vArg) === '[object Array]';
		}
	};

	return MicroPlugin;
}));

/**
 * selectize.js (v0.12.2)
 * Copyright (c) 2013–2015 Brian Reavis & contributors
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this
 * file except in compliance with the License. You may obtain a copy of the License at:
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under
 * the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF
 * ANY KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 *
 * @author Brian Reavis <brian@thirdroute.com>
 */

/*jshint curly:false */
/*jshint browser:true */

(function(root, factory) {
	if (typeof define === 'function' && define.amd) {
		define('selectize', ['jquery','sifter','microplugin'], factory);
	} else if (typeof exports === 'object') {
		module.exports = factory(require('jquery'), require('sifter'), require('microplugin'));
	} else {
		root.Selectize = factory(root.jQuery, root.Sifter, root.MicroPlugin);
	}
}(this, function($, Sifter, MicroPlugin) {
	'use strict';

	var highlight = function($element, pattern) {
		if (typeof pattern === 'string' && !pattern.length) return;
		var regex = (typeof pattern === 'string') ? new RegExp(pattern, 'i') : pattern;
	
		var highlight = function(node) {
			var skip = 0;
			if (node.nodeType === 3) {
				var pos = node.data.search(regex);
				if (pos >= 0 && node.data.length > 0) {
					var match = node.data.match(regex);
					var spannode = document.createElement('span');
					spannode.className = 'highlight';
					var middlebit = node.splitText(pos);
					var endbit = middlebit.splitText(match[0].length);
					var middleclone = middlebit.cloneNode(true);
					spannode.appendChild(middleclone);
					middlebit.parentNode.replaceChild(spannode, middlebit);
					skip = 1;
				}
			} else if (node.nodeType === 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
				for (var i = 0; i < node.childNodes.length; ++i) {
					i += highlight(node.childNodes[i]);
				}
			}
			return skip;
		};
	
		return $element.each(function() {
			highlight(this);
		});
	};
	
	var MicroEvent = function() {};
	MicroEvent.prototype = {
		on: function(event, fct){
			this._events = this._events || {};
			this._events[event] = this._events[event] || [];
			this._events[event].push(fct);
		},
		off: function(event, fct){
			var n = arguments.length;
			if (n === 0) return delete this._events;
			if (n === 1) return delete this._events[event];
	
			this._events = this._events || {};
			if (event in this._events === false) return;
			this._events[event].splice(this._events[event].indexOf(fct), 1);
		},
		trigger: function(event /* , args... */){
			this._events = this._events || {};
			if (event in this._events === false) return;
			for (var i = 0; i < this._events[event].length; i++){
				this._events[event][i].apply(this, Array.prototype.slice.call(arguments, 1));
			}
		}
	};
	
	/**
	 * Mixin will delegate all MicroEvent.js function in the destination object.
	 *
	 * - MicroEvent.mixin(Foobar) will make Foobar able to use MicroEvent
	 *
	 * @param {object} the object which will support MicroEvent
	 */
	MicroEvent.mixin = function(destObject){
		var props = ['on', 'off', 'trigger'];
		for (var i = 0; i < props.length; i++){
			destObject.prototype[props[i]] = MicroEvent.prototype[props[i]];
		}
	};
	
	var IS_MAC        = /Mac/.test(navigator.userAgent);
	
	var KEY_A         = 65;
	var KEY_COMMA     = 188;
	var KEY_RETURN    = 13;
	var KEY_ESC       = 27;
	var KEY_LEFT      = 37;
	var KEY_UP        = 38;
	var KEY_P         = 80;
	var KEY_RIGHT     = 39;
	var KEY_DOWN      = 40;
	var KEY_N         = 78;
	var KEY_BACKSPACE = 8;
	var KEY_DELETE    = 46;
	var KEY_SHIFT     = 16;
	var KEY_CMD       = IS_MAC ? 91 : 17;
	var KEY_CTRL      = IS_MAC ? 18 : 17;
	var KEY_TAB       = 9;
	
	var TAG_SELECT    = 1;
	var TAG_INPUT     = 2;
	
	// for now, android support in general is too spotty to support validity
	var SUPPORTS_VALIDITY_API = !/android/i.test(window.navigator.userAgent) && !!document.createElement('form').validity;
	
	var isset = function(object) {
		return typeof object !== 'undefined';
	};
	
	/**
	 * Converts a scalar to its best string representation
	 * for hash keys and HTML attribute values.
	 *
	 * Transformations:
	 *   'str'     -> 'str'
	 *   null      -> ''
	 *   undefined -> ''
	 *   true      -> '1'
	 *   false     -> '0'
	 *   0         -> '0'
	 *   1         -> '1'
	 *
	 * @param {string} value
	 * @returns {string|null}
	 */
	var hash_key = function(value) {
		if (typeof value === 'undefined' || value === null) return null;
		if (typeof value === 'boolean') return value ? '1' : '0';
		return value + '';
	};
	
	/**
	 * Escapes a string for use within HTML.
	 *
	 * @param {string} str
	 * @returns {string}
	 */
	var escape_html = function(str) {
		return (str + '')
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	};
	
	/**
	 * Escapes "$" characters in replacement strings.
	 *
	 * @param {string} str
	 * @returns {string}
	 */
	var escape_replace = function(str) {
		return (str + '').replace(/\$/g, '$$$$');
	};
	
	var hook = {};
	
	/**
	 * Wraps `method` on `self` so that `fn`
	 * is invoked before the original method.
	 *
	 * @param {object} self
	 * @param {string} method
	 * @param {function} fn
	 */
	hook.before = function(self, method, fn) {
		var original = self[method];
		self[method] = function() {
			fn.apply(self, arguments);
			return original.apply(self, arguments);
		};
	};
	
	/**
	 * Wraps `method` on `self` so that `fn`
	 * is invoked after the original method.
	 *
	 * @param {object} self
	 * @param {string} method
	 * @param {function} fn
	 */
	hook.after = function(self, method, fn) {
		var original = self[method];
		self[method] = function() {
			var result = original.apply(self, arguments);
			fn.apply(self, arguments);
			return result;
		};
	};
	
	/**
	 * Wraps `fn` so that it can only be invoked once.
	 *
	 * @param {function} fn
	 * @returns {function}
	 */
	var once = function(fn) {
		var called = false;
		return function() {
			if (called) return;
			called = true;
			fn.apply(this, arguments);
		};
	};
	
	/**
	 * Wraps `fn` so that it can only be called once
	 * every `delay` milliseconds (invoked on the falling edge).
	 *
	 * @param {function} fn
	 * @param {int} delay
	 * @returns {function}
	 */
	var debounce = function(fn, delay) {
		var timeout;
		return function() {
			var self = this;
			var args = arguments;
			window.clearTimeout(timeout);
			timeout = window.setTimeout(function() {
				fn.apply(self, args);
			}, delay);
		};
	};
	
	/**
	 * Debounce all fired events types listed in `types`
	 * while executing the provided `fn`.
	 *
	 * @param {object} self
	 * @param {array} types
	 * @param {function} fn
	 */
	var debounce_events = function(self, types, fn) {
		var type;
		var trigger = self.trigger;
		var event_args = {};
	
		// override trigger method
		self.trigger = function() {
			var type = arguments[0];
			if (types.indexOf(type) !== -1) {
				event_args[type] = arguments;
			} else {
				return trigger.apply(self, arguments);
			}
		};
	
		// invoke provided function
		fn.apply(self, []);
		self.trigger = trigger;
	
		// trigger queued events
		for (type in event_args) {
			if (event_args.hasOwnProperty(type)) {
				trigger.apply(self, event_args[type]);
			}
		}
	};
	
	/**
	 * A workaround for http://bugs.jquery.com/ticket/6696
	 *
	 * @param {object} $parent - Parent element to listen on.
	 * @param {string} event - Event name.
	 * @param {string} selector - Descendant selector to filter by.
	 * @param {function} fn - Event handler.
	 */
	var watchChildEvent = function($parent, event, selector, fn) {
		$parent.on(event, selector, function(e) {
			var child = e.target;
			while (child && child.parentNode !== $parent[0]) {
				child = child.parentNode;
			}
			e.currentTarget = child;
			return fn.apply(this, [e]);
		});
	};
	
	/**
	 * Determines the current selection within a text input control.
	 * Returns an object containing:
	 *   - start
	 *   - length
	 *
	 * @param {object} input
	 * @returns {object}
	 */
	var getSelection = function(input) {
		var result = {};
		if ('selectionStart' in input) {
			result.start = input.selectionStart;
			result.length = input.selectionEnd - result.start;
		} else if (document.selection) {
			input.focus();
			var sel = document.selection.createRange();
			var selLen = document.selection.createRange().text.length;
			sel.moveStart('character', -input.value.length);
			result.start = sel.text.length - selLen;
			result.length = selLen;
		}
		return result;
	};
	
	/**
	 * Copies CSS properties from one element to another.
	 *
	 * @param {object} $from
	 * @param {object} $to
	 * @param {array} properties
	 */
	var transferStyles = function($from, $to, properties) {
		var i, n, styles = {};
		if (properties) {
			for (i = 0, n = properties.length; i < n; i++) {
				styles[properties[i]] = $from.css(properties[i]);
			}
		} else {
			styles = $from.css();
		}
		$to.css(styles);
	};
	
	/**
	 * Measures the width of a string within a
	 * parent element (in pixels).
	 *
	 * @param {string} str
	 * @param {object} $parent
	 * @returns {int}
	 */
	var measureString = function(str, $parent) {
		if (!str) {
			return 0;
		}
	
		var $test = $('<test>').css({
			position: 'absolute',
			top: -99999,
			left: -99999,
			width: 'auto',
			padding: 0,
			whiteSpace: 'pre'
		}).text(str).appendTo('body');
	
		transferStyles($parent, $test, [
			'letterSpacing',
			'fontSize',
			'fontFamily',
			'fontWeight',
			'textTransform'
		]);
	
		var width = $test.width();
		$test.remove();
	
		return width;
	};
	
	/**
	 * Sets up an input to grow horizontally as the user
	 * types. If the value is changed manually, you can
	 * trigger the "update" handler to resize:
	 *
	 * $input.trigger('update');
	 *
	 * @param {object} $input
	 */
	var autoGrow = function($input) {
		var currentWidth = null;
	
		var update = function(e, options) {
			var value, keyCode, printable, placeholder, width;
			var shift, character, selection;
			e = e || window.event || {};
			options = options || {};
	
			if (e.metaKey || e.altKey) return;
			if (!options.force && $input.data('grow') === false) return;
	
			value = $input.val();
			if (e.type && e.type.toLowerCase() === 'keydown') {
				keyCode = e.keyCode;
				printable = (
					(keyCode >= 97 && keyCode <= 122) || // a-z
					(keyCode >= 65 && keyCode <= 90)  || // A-Z
					(keyCode >= 48 && keyCode <= 57)  || // 0-9
					keyCode === 32 // space
				);
	
				if (keyCode === KEY_DELETE || keyCode === KEY_BACKSPACE) {
					selection = getSelection($input[0]);
					if (selection.length) {
						value = value.substring(0, selection.start) + value.substring(selection.start + selection.length);
					} else if (keyCode === KEY_BACKSPACE && selection.start) {
						value = value.substring(0, selection.start - 1) + value.substring(selection.start + 1);
					} else if (keyCode === KEY_DELETE && typeof selection.start !== 'undefined') {
						value = value.substring(0, selection.start) + value.substring(selection.start + 1);
					}
				} else if (printable) {
					shift = e.shiftKey;
					character = String.fromCharCode(e.keyCode);
					if (shift) character = character.toUpperCase();
					else character = character.toLowerCase();
					value += character;
				}
			}
	
			placeholder = $input.attr('placeholder');
			if (!value && placeholder) {
				value = placeholder;
			}
	
			width = measureString(value, $input) + 4;
			if (width !== currentWidth) {
				currentWidth = width;
				$input.width(width);
				$input.triggerHandler('resize');
			}
		};
	
		$input.on('keydown keyup update blur', update);
		update();
	};
	
	var domToString = function(d) {
		var tmp = document.createElement('div');
	
		tmp.appendChild(d.cloneNode(true));
	
		return tmp.innerHTML;
	};
	
	
	var Selectize = function($input, settings) {
		var key, i, n, dir, input, self = this;
		input = $input[0];
		input.selectize = self;
	
		// detect rtl environment
		var computedStyle = window.getComputedStyle && window.getComputedStyle(input, null);
		dir = computedStyle ? computedStyle.getPropertyValue('direction') : input.currentStyle && input.currentStyle.direction;
		dir = dir || $input.parents('[dir]:first').attr('dir') || '';
	
		// setup default state
		$.extend(self, {
			order            : 0,
			settings         : settings,
			$input           : $input,
			tabIndex         : $input.attr('tabindex') || '',
			tagType          : input.tagName.toLowerCase() === 'select' ? TAG_SELECT : TAG_INPUT,
			rtl              : /rtl/i.test(dir),
	
			eventNS          : '.selectize' + (++Selectize.count),
			highlightedValue : null,
			isOpen           : false,
			isDisabled       : false,
			isRequired       : $input.is('[required]'),
			isInvalid        : false,
			isLocked         : false,
			isFocused        : false,
			isInputHidden    : false,
			isSetup          : false,
			isShiftDown      : false,
			isCmdDown        : false,
			isCtrlDown       : false,
			ignoreFocus      : false,
			ignoreBlur       : false,
			ignoreHover      : false,
			hasOptions       : false,
			currentResults   : null,
			lastValue        : '',
			caretPos         : 0,
			loading          : 0,
			loadedSearches   : {},
	
			$activeOption    : null,
			$activeItems     : [],
	
			optgroups        : {},
			options          : {},
			userOptions      : {},
			items            : [],
			renderCache      : {},
			onSearchChange   : settings.loadThrottle === null ? self.onSearchChange : debounce(self.onSearchChange, settings.loadThrottle)
		});
	
		// search system
		self.sifter = new Sifter(this.options, {diacritics: settings.diacritics});
	
		// build options table
		if (self.settings.options) {
			for (i = 0, n = self.settings.options.length; i < n; i++) {
				self.registerOption(self.settings.options[i]);
			}
			delete self.settings.options;
		}
	
		// build optgroup table
		if (self.settings.optgroups) {
			for (i = 0, n = self.settings.optgroups.length; i < n; i++) {
				self.registerOptionGroup(self.settings.optgroups[i]);
			}
			delete self.settings.optgroups;
		}
	
		// option-dependent defaults
		self.settings.mode = self.settings.mode || (self.settings.maxItems === 1 ? 'single' : 'multi');
		if (typeof self.settings.hideSelected !== 'boolean') {
			self.settings.hideSelected = self.settings.mode === 'multi';
		}
	
		self.initializePlugins(self.settings.plugins);
		self.setupCallbacks();
		self.setupTemplates();
		self.setup();
	};
	
	// mixins
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	MicroEvent.mixin(Selectize);
	MicroPlugin.mixin(Selectize);
	
	// methods
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	$.extend(Selectize.prototype, {
	
		/**
		 * Creates all elements and sets up event bindings.
		 */
		setup: function() {
			var self      = this;
			var settings  = self.settings;
			var eventNS   = self.eventNS;
			var $window   = $(window);
			var $document = $(document);
			var $input    = self.$input;
	
			var $wrapper;
			var $control;
			var $control_input;
			var $dropdown;
			var $dropdown_content;
			var $dropdown_parent;
			var inputMode;
			var timeout_blur;
			var timeout_focus;
			var classes;
			var classes_plugins;
	
			inputMode         = self.settings.mode;
			classes           = $input.attr('class') || '';
	
			$wrapper          = $('<div>').addClass(settings.wrapperClass).addClass(classes).addClass(inputMode);
			$control          = $('<div>').addClass(settings.inputClass).addClass('items').appendTo($wrapper);
			$control_input    = $('<input type="text" autocomplete="off" />').appendTo($control).attr('tabindex', $input.is(':disabled') ? '-1' : self.tabIndex);
			$dropdown_parent  = $(settings.dropdownParent || $wrapper);
			$dropdown         = $('<div>').addClass(settings.dropdownClass).addClass(inputMode).hide().appendTo($dropdown_parent);
			$dropdown_content = $('<div>').addClass(settings.dropdownContentClass).appendTo($dropdown);
	
			if(self.settings.copyClassesToDropdown) {
				$dropdown.addClass(classes);
			}
	
			$wrapper.css({
				width: $input[0].style.width
			});
	
			if (self.plugins.names.length) {
				classes_plugins = 'plugin-' + self.plugins.names.join(' plugin-');
				$wrapper.addClass(classes_plugins);
				$dropdown.addClass(classes_plugins);
			}
	
			if ((settings.maxItems === null || settings.maxItems > 1) && self.tagType === TAG_SELECT) {
				$input.attr('multiple', 'multiple');
			}
	
			if (self.settings.placeholder) {
				$control_input.attr('placeholder', settings.placeholder);
			}
	
			// if splitOn was not passed in, construct it from the delimiter to allow pasting universally
			if (!self.settings.splitOn && self.settings.delimiter) {
				var delimiterEscaped = self.settings.delimiter.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
				self.settings.splitOn = new RegExp('\\s*' + delimiterEscaped + '+\\s*');
			}
	
			if ($input.attr('autocorrect')) {
				$control_input.attr('autocorrect', $input.attr('autocorrect'));
			}
	
			if ($input.attr('autocapitalize')) {
				$control_input.attr('autocapitalize', $input.attr('autocapitalize'));
			}
	
			self.$wrapper          = $wrapper;
			self.$control          = $control;
			self.$control_input    = $control_input;
			self.$dropdown         = $dropdown;
			self.$dropdown_content = $dropdown_content;
	
			$dropdown.on('mouseenter', '[data-selectable]', function() { return self.onOptionHover.apply(self, arguments); });
			$dropdown.on('mousedown click', '[data-selectable]', function() { return self.onOptionSelect.apply(self, arguments); });
			watchChildEvent($control, 'mousedown', '*:not(input)', function() { return self.onItemSelect.apply(self, arguments); });
			autoGrow($control_input);
	
			$control.on({
				mousedown : function() { return self.onMouseDown.apply(self, arguments); },
				click     : function() { return self.onClick.apply(self, arguments); }
			});
	
			$control_input.on({
				mousedown : function(e) { e.stopPropagation(); },
				keydown   : function() { return self.onKeyDown.apply(self, arguments); },
				keyup     : function() { return self.onKeyUp.apply(self, arguments); },
				keypress  : function() { return self.onKeyPress.apply(self, arguments); },
				resize    : function() { self.positionDropdown.apply(self, []); },
				blur      : function() { return self.onBlur.apply(self, arguments); },
				focus     : function() { self.ignoreBlur = false; return self.onFocus.apply(self, arguments); },
				paste     : function() { return self.onPaste.apply(self, arguments); }
			});
	
			$document.on('keydown' + eventNS, function(e) {
				self.isCmdDown = e[IS_MAC ? 'metaKey' : 'ctrlKey'];
				self.isCtrlDown = e[IS_MAC ? 'altKey' : 'ctrlKey'];
				self.isShiftDown = e.shiftKey;
			});
	
			$document.on('keyup' + eventNS, function(e) {
				if (e.keyCode === KEY_CTRL) self.isCtrlDown = false;
				if (e.keyCode === KEY_SHIFT) self.isShiftDown = false;
				if (e.keyCode === KEY_CMD) self.isCmdDown = false;
			});
	
			$document.on('mousedown' + eventNS, function(e) {
				if (self.isFocused) {
					// prevent events on the dropdown scrollbar from causing the control to blur
					if (e.target === self.$dropdown[0] || e.target.parentNode === self.$dropdown[0]) {
						return false;
					}
					// blur on click outside
					if (!self.$control.has(e.target).length && e.target !== self.$control[0]) {
						self.blur(e.target);
					}
				}
			});
	
			$window.on(['scroll' + eventNS, 'resize' + eventNS].join(' '), function() {
				if (self.isOpen) {
					self.positionDropdown.apply(self, arguments);
				}
			});
			$window.on('mousemove' + eventNS, function() {
				self.ignoreHover = false;
			});
	
			// store original children and tab index so that they can be
			// restored when the destroy() method is called.
			this.revertSettings = {
				$children : $input.children().detach(),
				tabindex  : $input.attr('tabindex')
			};
	
			$input.attr('tabindex', -1).hide().after(self.$wrapper);
	
			if ($.isArray(settings.items)) {
				self.setValue(settings.items);
				delete settings.items;
			}
	
			// feature detect for the validation API
			if (SUPPORTS_VALIDITY_API) {
				$input.on('invalid' + eventNS, function(e) {
					e.preventDefault();
					self.isInvalid = true;
					self.refreshState();
				});
			}
	
			self.updateOriginalInput();
			self.refreshItems();
			self.refreshState();
			self.updatePlaceholder();
			self.isSetup = true;
	
			if ($input.is(':disabled')) {
				self.disable();
			}
	
			self.on('change', this.onChange);
	
			$input.data('selectize', self);
			$input.addClass('selectized');
			self.trigger('initialize');
	
			// preload options
			if (settings.preload === true) {
				self.onSearchChange('');
			}
	
		},
	
		/**
		 * Sets up default rendering functions.
		 */
		setupTemplates: function() {
			var self = this;
			var field_label = self.settings.labelField;
			var field_optgroup = self.settings.optgroupLabelField;
	
			var templates = {
				'optgroup': function(data) {
					return '<div class="optgroup">' + data.html + '</div>';
				},
				'optgroup_header': function(data, escape) {
					return '<div class="optgroup-header">' + escape(data[field_optgroup]) + '</div>';
				},
				'option': function(data, escape) {
					return '<div class="option">' + escape(data[field_label]) + '</div>';
				},
				'item': function(data, escape) {
					return '<div class="item">' + escape(data[field_label]) + '</div>';
				},
				'option_create': function(data, escape) {
					return '<div class="create">Add <strong>' + escape(data.input) + '</strong>&hellip;</div>';
				}
			};
	
			self.settings.render = $.extend({}, templates, self.settings.render);
		},
	
		/**
		 * Maps fired events to callbacks provided
		 * in the settings used when creating the control.
		 */
		setupCallbacks: function() {
			var key, fn, callbacks = {
				'initialize'      : 'onInitialize',
				'change'          : 'onChange',
				'item_add'        : 'onItemAdd',
				'item_remove'     : 'onItemRemove',
				'clear'           : 'onClear',
				'option_add'      : 'onOptionAdd',
				'option_remove'   : 'onOptionRemove',
				'option_clear'    : 'onOptionClear',
				'optgroup_add'    : 'onOptionGroupAdd',
				'optgroup_remove' : 'onOptionGroupRemove',
				'optgroup_clear'  : 'onOptionGroupClear',
				'dropdown_open'   : 'onDropdownOpen',
				'dropdown_close'  : 'onDropdownClose',
				'type'            : 'onType',
				'load'            : 'onLoad',
				'focus'           : 'onFocus',
				'blur'            : 'onBlur'
			};
	
			for (key in callbacks) {
				if (callbacks.hasOwnProperty(key)) {
					fn = this.settings[callbacks[key]];
					if (fn) this.on(key, fn);
				}
			}
		},
	
		/**
		 * Triggered when the main control element
		 * has a click event.
		 *
		 * @param {object} e
		 * @return {boolean}
		 */
		onClick: function(e) {
			var self = this;
	
			// necessary for mobile webkit devices (manual focus triggering
			// is ignored unless invoked within a click event)
			if (!self.isFocused) {
				self.focus();
				e.preventDefault();
			}
		},
	
		/**
		 * Triggered when the main control element
		 * has a mouse down event.
		 *
		 * @param {object} e
		 * @return {boolean}
		 */
		onMouseDown: function(e) {
			var self = this;
			var defaultPrevented = e.isDefaultPrevented();
			var $target = $(e.target);
	
			if (self.isFocused) {
				// retain focus by preventing native handling. if the
				// event target is the input it should not be modified.
				// otherwise, text selection within the input won't work.
				if (e.target !== self.$control_input[0]) {
					if (self.settings.mode === 'single') {
						// toggle dropdown
						self.isOpen ? self.close() : self.open();
					} else if (!defaultPrevented) {
						self.setActiveItem(null);
					}
					return false;
				}
			} else {
				// give control focus
				if (!defaultPrevented) {
					window.setTimeout(function() {
						self.focus();
					}, 0);
				}
			}
		},
	
		/**
		 * Triggered when the value of the control has been changed.
		 * This should propagate the event to the original DOM
		 * input / select element.
		 */
		onChange: function() {
			this.$input.trigger('change');
		},
	
		/**
		 * Triggered on <input> paste.
		 *
		 * @param {object} e
		 * @returns {boolean}
		 */
		onPaste: function(e) {
			var self = this;
			if (self.isFull() || self.isInputHidden || self.isLocked) {
				e.preventDefault();
			} else {
				// If a regex or string is included, this will split the pasted
				// input and create Items for each separate value
				if (self.settings.splitOn) {
					setTimeout(function() {
						var splitInput = $.trim(self.$control_input.val() || '').split(self.settings.splitOn);
						for (var i = 0, n = splitInput.length; i < n; i++) {
							self.createItem(splitInput[i]);
						}
					}, 0);
				}
			}
		},
	
		/**
		 * Triggered on <input> keypress.
		 *
		 * @param {object} e
		 * @returns {boolean}
		 */
		onKeyPress: function(e) {
			if (this.isLocked) return e && e.preventDefault();
			var character = String.fromCharCode(e.keyCode || e.which);
			if (this.settings.create && this.settings.mode === 'multi' && character === this.settings.delimiter) {
				this.createItem();
				e.preventDefault();
				return false;
			}
		},
	
		/**
		 * Triggered on <input> keydown.
		 *
		 * @param {object} e
		 * @returns {boolean}
		 */
		onKeyDown: function(e) {
			var isInput = e.target === this.$control_input[0];
			var self = this;
	
			if (self.isLocked) {
				if (e.keyCode !== KEY_TAB) {
					e.preventDefault();
				}
				return;
			}
	
			switch (e.keyCode) {
				case KEY_A:
					if (self.isCmdDown) {
						self.selectAll();
						return;
					}
					break;
				case KEY_ESC:
					if (self.isOpen) {
						e.preventDefault();
						e.stopPropagation();
						self.close();
					}
					return;
				case KEY_N:
					if (!e.ctrlKey || e.altKey) break;
				case KEY_DOWN:
					if (!self.isOpen && self.hasOptions) {
						self.open();
					} else if (self.$activeOption) {
						self.ignoreHover = true;
						var $next = self.getAdjacentOption(self.$activeOption, 1);
						if ($next.length) self.setActiveOption($next, true, true);
					}
					e.preventDefault();
					return;
				case KEY_P:
					if (!e.ctrlKey || e.altKey) break;
				case KEY_UP:
					if (self.$activeOption) {
						self.ignoreHover = true;
						var $prev = self.getAdjacentOption(self.$activeOption, -1);
						if ($prev.length) self.setActiveOption($prev, true, true);
					}
					e.preventDefault();
					return;
				case KEY_RETURN:
					if (self.isOpen && self.$activeOption) {
						self.onOptionSelect({currentTarget: self.$activeOption});
						e.preventDefault();
					}
					return;
				case KEY_LEFT:
					self.advanceSelection(-1, e);
					return;
				case KEY_RIGHT:
					self.advanceSelection(1, e);
					return;
				case KEY_TAB:
					if (self.settings.selectOnTab && self.isOpen && self.$activeOption) {
						self.onOptionSelect({currentTarget: self.$activeOption});
	
						// Default behaviour is to jump to the next field, we only want this
						// if the current field doesn't accept any more entries
						if (!self.isFull()) {
							e.preventDefault();
						}
					}
					if (self.settings.create && self.createItem()) {
						e.preventDefault();
					}
					return;
				case KEY_BACKSPACE:
				case KEY_DELETE:
					self.deleteSelection(e);
					return;
			}
	
			if ((self.isFull() || self.isInputHidden) && !(IS_MAC ? e.metaKey : e.ctrlKey)) {
				e.preventDefault();
				return;
			}
		},
	
		/**
		 * Triggered on <input> keyup.
		 *
		 * @param {object} e
		 * @returns {boolean}
		 */
		onKeyUp: function(e) {
			var self = this;
	
			if (self.isLocked) return e && e.preventDefault();
			var value = self.$control_input.val() || '';
			if (self.lastValue !== value) {
				self.lastValue = value;
				self.onSearchChange(value);
				self.refreshOptions();
				self.trigger('type', value);
			}
		},
	
		/**
		 * Invokes the user-provide option provider / loader.
		 *
		 * Note: this function is debounced in the Selectize
		 * constructor (by `settings.loadThrottle` milliseconds)
		 *
		 * @param {string} value
		 */
		onSearchChange: function(value) {
			var self = this;
			var fn = self.settings.load;
			if (!fn) return;
			if (self.loadedSearches.hasOwnProperty(value)) return;
			self.loadedSearches[value] = true;
			self.load(function(callback) {
				fn.apply(self, [value, callback]);
			});
		},
	
		/**
		 * Triggered on <input> focus.
		 *
		 * @param {object} e (optional)
		 * @returns {boolean}
		 */
		onFocus: function(e) {
			var self = this;
			var wasFocused = self.isFocused;
	
			if (self.isDisabled) {
				self.blur();
				e && e.preventDefault();
				return false;
			}
	
			if (self.ignoreFocus) return;
			self.isFocused = true;
			if (self.settings.preload === 'focus') self.onSearchChange('');
	
			if (!wasFocused) self.trigger('focus');
	
			if (!self.$activeItems.length) {
				self.showInput();
				self.setActiveItem(null);
				self.refreshOptions(!!self.settings.openOnFocus);
			}
	
			self.refreshState();
		},
	
		/**
		 * Triggered on <input> blur.
		 *
		 * @param {object} e
		 * @param {Element} dest
		 */
		onBlur: function(e, dest) {
			var self = this;
			if (!self.isFocused) return;
			self.isFocused = false;
	
			if (self.ignoreFocus) {
				return;
			} else if (!self.ignoreBlur && document.activeElement === self.$dropdown_content[0]) {
				// necessary to prevent IE closing the dropdown when the scrollbar is clicked
				self.ignoreBlur = true;
				self.onFocus(e);
				return;
			}
	
			var deactivate = function() {
				self.close();
				self.setTextboxValue('');
				self.setActiveItem(null);
				self.setActiveOption(null);
				self.setCaret(self.items.length);
				self.refreshState();
	
				// IE11 bug: element still marked as active
				dest && dest.focus();
	
				self.ignoreFocus = false;
				self.trigger('blur');
			};
	
			self.ignoreFocus = true;
			if (self.settings.create && self.settings.createOnBlur) {
				self.createItem(null, false, deactivate);
			} else {
				deactivate();
			}
		},
	
		/**
		 * Triggered when the user rolls over
		 * an option in the autocomplete dropdown menu.
		 *
		 * @param {object} e
		 * @returns {boolean}
		 */
		onOptionHover: function(e) {
			if (this.ignoreHover) return;
			this.setActiveOption(e.currentTarget, false);
		},
	
		/**
		 * Triggered when the user clicks on an option
		 * in the autocomplete dropdown menu.
		 *
		 * @param {object} e
		 * @returns {boolean}
		 */
		onOptionSelect: function(e) {
			var value, $target, $option, self = this;
	
			if (e.preventDefault) {
				e.preventDefault();
				e.stopPropagation();
			}
	
			$target = $(e.currentTarget);
			if ($target.hasClass('create')) {
				self.createItem(null, function() {
					if (self.settings.closeAfterSelect) {
						self.close();
					}
				});
			} else {
				value = $target.attr('data-value');
				if (typeof value !== 'undefined') {
					self.lastQuery = null;
					self.setTextboxValue('');
					self.addItem(value);
					if (self.settings.closeAfterSelect) {
						self.close();
					} else if (!self.settings.hideSelected && e.type && /mouse/.test(e.type)) {
						self.setActiveOption(self.getOption(value));
					}
				}
			}
		},
	
		/**
		 * Triggered when the user clicks on an item
		 * that has been selected.
		 *
		 * @param {object} e
		 * @returns {boolean}
		 */
		onItemSelect: function(e) {
			var self = this;
	
			if (self.isLocked) return;
			if (self.settings.mode === 'multi') {
				e.preventDefault();
				self.setActiveItem(e.currentTarget, e);
			}
		},
	
		/**
		 * Invokes the provided method that provides
		 * results to a callback---which are then added
		 * as options to the control.
		 *
		 * @param {function} fn
		 */
		load: function(fn) {
			var self = this;
			var $wrapper = self.$wrapper.addClass(self.settings.loadingClass);
	
			self.loading++;
			fn.apply(self, [function(results) {
				self.loading = Math.max(self.loading - 1, 0);
				if (results && results.length) {
					self.addOption(results);
					self.refreshOptions(self.isFocused && !self.isInputHidden);
				}
				if (!self.loading) {
					$wrapper.removeClass(self.settings.loadingClass);
				}
				self.trigger('load', results);
			}]);
		},
	
		/**
		 * Sets the input field of the control to the specified value.
		 *
		 * @param {string} value
		 */
		setTextboxValue: function(value) {
			var $input = this.$control_input;
			var changed = $input.val() !== value;
			if (changed) {
				$input.val(value).triggerHandler('update');
				this.lastValue = value;
			}
		},
	
		/**
		 * Returns the value of the control. If multiple items
		 * can be selected (e.g. <select multiple>), this returns
		 * an array. If only one item can be selected, this
		 * returns a string.
		 *
		 * @returns {mixed}
		 */
		getValue: function() {
			if (this.tagType === TAG_SELECT && this.$input.attr('multiple')) {
				return this.items;
			} else {
				return this.items.join(this.settings.delimiter);
			}
		},
	
		/**
		 * Resets the selected items to the given value.
		 *
		 * @param {mixed} value
		 */
		setValue: function(value, silent) {
			var events = silent ? [] : ['change'];
	
			debounce_events(this, events, function() {
				this.clear(silent);
				this.addItems(value, silent);
			});
		},
	
		/**
		 * Sets the selected item.
		 *
		 * @param {object} $item
		 * @param {object} e (optional)
		 */
		setActiveItem: function($item, e) {
			var self = this;
			var eventName;
			var i, idx, begin, end, item, swap;
			var $last;
	
			if (self.settings.mode === 'single') return;
			$item = $($item);
	
			// clear the active selection
			if (!$item.length) {
				$(self.$activeItems).removeClass('active');
				self.$activeItems = [];
				if (self.isFocused) {
					self.showInput();
				}
				return;
			}
	
			// modify selection
			eventName = e && e.type.toLowerCase();
	
			if (eventName === 'mousedown' && self.isShiftDown && self.$activeItems.length) {
				$last = self.$control.children('.active:last');
				begin = Array.prototype.indexOf.apply(self.$control[0].childNodes, [$last[0]]);
				end   = Array.prototype.indexOf.apply(self.$control[0].childNodes, [$item[0]]);
				if (begin > end) {
					swap  = begin;
					begin = end;
					end   = swap;
				}
				for (i = begin; i <= end; i++) {
					item = self.$control[0].childNodes[i];
					if (self.$activeItems.indexOf(item) === -1) {
						$(item).addClass('active');
						self.$activeItems.push(item);
					}
				}
				e.preventDefault();
			} else if ((eventName === 'mousedown' && self.isCtrlDown) || (eventName === 'keydown' && this.isShiftDown)) {
				if ($item.hasClass('active')) {
					idx = self.$activeItems.indexOf($item[0]);
					self.$activeItems.splice(idx, 1);
					$item.removeClass('active');
				} else {
					self.$activeItems.push($item.addClass('active')[0]);
				}
			} else {
				$(self.$activeItems).removeClass('active');
				self.$activeItems = [$item.addClass('active')[0]];
			}
	
			// ensure control has focus
			self.hideInput();
			if (!this.isFocused) {
				self.focus();
			}
		},
	
		/**
		 * Sets the selected item in the dropdown menu
		 * of available options.
		 *
		 * @param {object} $object
		 * @param {boolean} scroll
		 * @param {boolean} animate
		 */
		setActiveOption: function($option, scroll, animate) {
			var height_menu, height_item, y;
			var scroll_top, scroll_bottom;
			var self = this;
	
			if (self.$activeOption) self.$activeOption.removeClass('active');
			self.$activeOption = null;
	
			$option = $($option);
			if (!$option.length) return;
	
			self.$activeOption = $option.addClass('active');
	
			if (scroll || !isset(scroll)) {
	
				height_menu   = self.$dropdown_content.height();
				height_item   = self.$activeOption.outerHeight(true);
				scroll        = self.$dropdown_content.scrollTop() || 0;
				y             = self.$activeOption.offset().top - self.$dropdown_content.offset().top + scroll;
				scroll_top    = y;
				scroll_bottom = y - height_menu + height_item;
	
				if (y + height_item > height_menu + scroll) {
					self.$dropdown_content.stop().animate({scrollTop: scroll_bottom}, animate ? self.settings.scrollDuration : 0);
				} else if (y < scroll) {
					self.$dropdown_content.stop().animate({scrollTop: scroll_top}, animate ? self.settings.scrollDuration : 0);
				}
	
			}
		},
	
		/**
		 * Selects all items (CTRL + A).
		 */
		selectAll: function() {
			var self = this;
			if (self.settings.mode === 'single') return;
	
			self.$activeItems = Array.prototype.slice.apply(self.$control.children(':not(input)').addClass('active'));
			if (self.$activeItems.length) {
				self.hideInput();
				self.close();
			}
			self.focus();
		},
	
		/**
		 * Hides the input element out of view, while
		 * retaining its focus.
		 */
		hideInput: function() {
			var self = this;
	
			self.setTextboxValue('');
			self.$control_input.css({opacity: 0, position: 'absolute', left: self.rtl ? 10000 : -10000});
			self.isInputHidden = true;
		},
	
		/**
		 * Restores input visibility.
		 */
		showInput: function() {
			this.$control_input.css({opacity: 1, position: 'relative', left: 0});
			this.isInputHidden = false;
		},
	
		/**
		 * Gives the control focus.
		 */
		focus: function() {
			var self = this;
			if (self.isDisabled) return;
	
			self.ignoreFocus = true;
			self.$control_input[0].focus();
			window.setTimeout(function() {
				self.ignoreFocus = false;
				self.onFocus();
			}, 0);
		},
	
		/**
		 * Forces the control out of focus.
		 *
		 * @param {Element} dest
		 */
		blur: function(dest) {
			this.$control_input[0].blur();
			this.onBlur(null, dest);
		},
	
		/**
		 * Returns a function that scores an object
		 * to show how good of a match it is to the
		 * provided query.
		 *
		 * @param {string} query
		 * @param {object} options
		 * @return {function}
		 */
		getScoreFunction: function(query) {
			return this.sifter.getScoreFunction(query, this.getSearchOptions());
		},
	
		/**
		 * Returns search options for sifter (the system
		 * for scoring and sorting results).
		 *
		 * @see https://github.com/brianreavis/sifter.js
		 * @return {object}
		 */
		getSearchOptions: function() {
			var settings = this.settings;
			var sort = settings.sortField;
			if (typeof sort === 'string') {
				sort = [{field: sort}];
			}
	
			return {
				fields      : settings.searchField,
				conjunction : settings.searchConjunction,
				sort        : sort
			};
		},
	
		/**
		 * Searches through available options and returns
		 * a sorted array of matches.
		 *
		 * Returns an object containing:
		 *
		 *   - query {string}
		 *   - tokens {array}
		 *   - total {int}
		 *   - items {array}
		 *
		 * @param {string} query
		 * @returns {object}
		 */
		search: function(query) {
			var i, value, score, result, calculateScore;
			var self     = this;
			var settings = self.settings;
			var options  = this.getSearchOptions();
	
			// validate user-provided result scoring function
			if (settings.score) {
				calculateScore = self.settings.score.apply(this, [query]);
				if (typeof calculateScore !== 'function') {
					throw new Error('Selectize "score" setting must be a function that returns a function');
				}
			}
	
			// perform search
			if (query !== self.lastQuery) {
				self.lastQuery = query;
				result = self.sifter.search(query, $.extend(options, {score: calculateScore}));
				self.currentResults = result;
			} else {
				result = $.extend(true, {}, self.currentResults);
			}
	
			// filter out selected items
			if (settings.hideSelected) {
				for (i = result.items.length - 1; i >= 0; i--) {
					if (self.items.indexOf(hash_key(result.items[i].id)) !== -1) {
						result.items.splice(i, 1);
					}
				}
			}
	
			return result;
		},
	
		/**
		 * Refreshes the list of available options shown
		 * in the autocomplete dropdown menu.
		 *
		 * @param {boolean} triggerDropdown
		 */
		refreshOptions: function(triggerDropdown) {
			var i, j, k, n, groups, groups_order, option, option_html, optgroup, optgroups, html, html_children, has_create_option;
			var $active, $active_before, $create;
	
			if (typeof triggerDropdown === 'undefined') {
				triggerDropdown = true;
			}
	
			var self              = this;
			var query             = $.trim(self.$control_input.val());
			var results           = self.search(query);
			var $dropdown_content = self.$dropdown_content;
			var active_before     = self.$activeOption && hash_key(self.$activeOption.attr('data-value'));
	
			// build markup
			n = results.items.length;
			if (typeof self.settings.maxOptions === 'number') {
				n = Math.min(n, self.settings.maxOptions);
			}
	
			// render and group available options individually
			groups = {};
			groups_order = [];
	
			for (i = 0; i < n; i++) {
				option      = self.options[results.items[i].id];
				option_html = self.render('option', option);
				optgroup    = option[self.settings.optgroupField] || '';
				optgroups   = $.isArray(optgroup) ? optgroup : [optgroup];
	
				for (j = 0, k = optgroups && optgroups.length; j < k; j++) {
					optgroup = optgroups[j];
					if (!self.optgroups.hasOwnProperty(optgroup)) {
						optgroup = '';
					}
					if (!groups.hasOwnProperty(optgroup)) {
						groups[optgroup] = document.createDocumentFragment();
						groups_order.push(optgroup);
					}
					groups[optgroup].appendChild(option_html);
				}
			}
	
			// sort optgroups
			if (this.settings.lockOptgroupOrder) {
				groups_order.sort(function(a, b) {
					var a_order = self.optgroups[a].$order || 0;
					var b_order = self.optgroups[b].$order || 0;
					return a_order - b_order;
				});
			}
	
			// render optgroup headers & join groups
			html = document.createDocumentFragment();
			for (i = 0, n = groups_order.length; i < n; i++) {
				optgroup = groups_order[i];
				if (self.optgroups.hasOwnProperty(optgroup) && groups[optgroup].childNodes.length) {
					// render the optgroup header and options within it,
					// then pass it to the wrapper template
					html_children = document.createDocumentFragment();
					html_children.appendChild(self.render('optgroup_header', self.optgroups[optgroup]));
					html_children.appendChild(groups[optgroup]);
	
					html.appendChild(self.render('optgroup', $.extend({}, self.optgroups[optgroup], {
						html: domToString(html_children),
						dom:  html_children
					})));
				} else {
					html.appendChild(groups[optgroup]);
				}
			}
	
			$dropdown_content.html(html);
	
			// highlight matching terms inline
			if (self.settings.highlight && results.query.length && results.tokens.length) {
				for (i = 0, n = results.tokens.length; i < n; i++) {
					highlight($dropdown_content, results.tokens[i].regex);
				}
			}
	
			// add "selected" class to selected options
			if (!self.settings.hideSelected) {
				for (i = 0, n = self.items.length; i < n; i++) {
					self.getOption(self.items[i]).addClass('selected');
				}
			}
	
			// add create option
			has_create_option = self.canCreate(query);
			if (has_create_option) {
				$dropdown_content.prepend(self.render('option_create', {input: query}));
				$create = $($dropdown_content[0].childNodes[0]);
			}
	
			// activate
			self.hasOptions = results.items.length > 0 || has_create_option;
			if (self.hasOptions) {
				if (results.items.length > 0) {
					$active_before = active_before && self.getOption(active_before);
					if ($active_before && $active_before.length) {
						$active = $active_before;
					} else if (self.settings.mode === 'single' && self.items.length) {
						$active = self.getOption(self.items[0]);
					}
					if (!$active || !$active.length) {
						if ($create && !self.settings.addPrecedence) {
							$active = self.getAdjacentOption($create, 1);
						} else {
							$active = $dropdown_content.find('[data-selectable]:first');
						}
					}
				} else {
					$active = $create;
				}
				self.setActiveOption($active);
				if (triggerDropdown && !self.isOpen) { self.open(); }
			} else {
				self.setActiveOption(null);
				if (triggerDropdown && self.isOpen) { self.close(); }
			}
		},
	
		/**
		 * Adds an available option. If it already exists,
		 * nothing will happen. Note: this does not refresh
		 * the options list dropdown (use `refreshOptions`
		 * for that).
		 *
		 * Usage:
		 *
		 *   this.addOption(data)
		 *
		 * @param {object|array} data
		 */
		addOption: function(data) {
			var i, n, value, self = this;
	
			if ($.isArray(data)) {
				for (i = 0, n = data.length; i < n; i++) {
					self.addOption(data[i]);
				}
				return;
			}
	
			if (value = self.registerOption(data)) {
				self.userOptions[value] = true;
				self.lastQuery = null;
				self.trigger('option_add', value, data);
			}
		},
	
		/**
		 * Registers an option to the pool of options.
		 *
		 * @param {object} data
		 * @return {boolean|string}
		 */
		registerOption: function(data) {
			var key = hash_key(data[this.settings.valueField]);
			if (typeof key === 'undefined' || key === null || this.options.hasOwnProperty(key)) return false;
			data.$order = data.$order || ++this.order;
			this.options[key] = data;
			return key;
		},
	
		/**
		 * Registers an option group to the pool of option groups.
		 *
		 * @param {object} data
		 * @return {boolean|string}
		 */
		registerOptionGroup: function(data) {
			var key = hash_key(data[this.settings.optgroupValueField]);
			if (!key) return false;
	
			data.$order = data.$order || ++this.order;
			this.optgroups[key] = data;
			return key;
		},
	
		/**
		 * Registers a new optgroup for options
		 * to be bucketed into.
		 *
		 * @param {string} id
		 * @param {object} data
		 */
		addOptionGroup: function(id, data) {
			data[this.settings.optgroupValueField] = id;
			if (id = this.registerOptionGroup(data)) {
				this.trigger('optgroup_add', id, data);
			}
		},
	
		/**
		 * Removes an existing option group.
		 *
		 * @param {string} id
		 */
		removeOptionGroup: function(id) {
			if (this.optgroups.hasOwnProperty(id)) {
				delete this.optgroups[id];
				this.renderCache = {};
				this.trigger('optgroup_remove', id);
			}
		},
	
		/**
		 * Clears all existing option groups.
		 */
		clearOptionGroups: function() {
			this.optgroups = {};
			this.renderCache = {};
			this.trigger('optgroup_clear');
		},
	
		/**
		 * Updates an option available for selection. If
		 * it is visible in the selected items or options
		 * dropdown, it will be re-rendered automatically.
		 *
		 * @param {string} value
		 * @param {object} data
		 */
		updateOption: function(value, data) {
			var self = this;
			var $item, $item_new;
			var value_new, index_item, cache_items, cache_options, order_old;
	
			value     = hash_key(value);
			value_new = hash_key(data[self.settings.valueField]);
	
			// sanity checks
			if (value === null) return;
			if (!self.options.hasOwnProperty(value)) return;
			if (typeof value_new !== 'string') throw new Error('Value must be set in option data');
	
			order_old = self.options[value].$order;
	
			// update references
			if (value_new !== value) {
				delete self.options[value];
				index_item = self.items.indexOf(value);
				if (index_item !== -1) {
					self.items.splice(index_item, 1, value_new);
				}
			}
			data.$order = data.$order || order_old;
			self.options[value_new] = data;
	
			// invalidate render cache
			cache_items = self.renderCache['item'];
			cache_options = self.renderCache['option'];
	
			if (cache_items) {
				delete cache_items[value];
				delete cache_items[value_new];
			}
			if (cache_options) {
				delete cache_options[value];
				delete cache_options[value_new];
			}
	
			// update the item if it's selected
			if (self.items.indexOf(value_new) !== -1) {
				$item = self.getItem(value);
				$item_new = $(self.render('item', data));
				if ($item.hasClass('active')) $item_new.addClass('active');
				$item.replaceWith($item_new);
			}
	
			// invalidate last query because we might have updated the sortField
			self.lastQuery = null;
	
			// update dropdown contents
			if (self.isOpen) {
				self.refreshOptions(false);
			}
		},
	
		/**
		 * Removes a single option.
		 *
		 * @param {string} value
		 * @param {boolean} silent
		 */
		removeOption: function(value, silent) {
			var self = this;
			value = hash_key(value);
	
			var cache_items = self.renderCache['item'];
			var cache_options = self.renderCache['option'];
			if (cache_items) delete cache_items[value];
			if (cache_options) delete cache_options[value];
	
			delete self.userOptions[value];
			delete self.options[value];
			self.lastQuery = null;
			self.trigger('option_remove', value);
			self.removeItem(value, silent);
		},
	
		/**
		 * Clears all options.
		 */
		clearOptions: function() {
			var self = this;
	
			self.loadedSearches = {};
			self.userOptions = {};
			self.renderCache = {};
			self.options = self.sifter.items = {};
			self.lastQuery = null;
			self.trigger('option_clear');
			self.clear();
		},
	
		/**
		 * Returns the jQuery element of the option
		 * matching the given value.
		 *
		 * @param {string} value
		 * @returns {object}
		 */
		getOption: function(value) {
			return this.getElementWithValue(value, this.$dropdown_content.find('[data-selectable]'));
		},
	
		/**
		 * Returns the jQuery element of the next or
		 * previous selectable option.
		 *
		 * @param {object} $option
		 * @param {int} direction  can be 1 for next or -1 for previous
		 * @return {object}
		 */
		getAdjacentOption: function($option, direction) {
			var $options = this.$dropdown.find('[data-selectable]');
			var index    = $options.index($option) + direction;
	
			return index >= 0 && index < $options.length ? $options.eq(index) : $();
		},
	
		/**
		 * Finds the first element with a "data-value" attribute
		 * that matches the given value.
		 *
		 * @param {mixed} value
		 * @param {object} $els
		 * @return {object}
		 */
		getElementWithValue: function(value, $els) {
			value = hash_key(value);
	
			if (typeof value !== 'undefined' && value !== null) {
				for (var i = 0, n = $els.length; i < n; i++) {
					if ($els[i].getAttribute('data-value') === value) {
						return $($els[i]);
					}
				}
			}
	
			return $();
		},
	
		/**
		 * Returns the jQuery element of the item
		 * matching the given value.
		 *
		 * @param {string} value
		 * @returns {object}
		 */
		getItem: function(value) {
			return this.getElementWithValue(value, this.$control.children());
		},
	
		/**
		 * "Selects" multiple items at once. Adds them to the list
		 * at the current caret position.
		 *
		 * @param {string} value
		 * @param {boolean} silent
		 */
		addItems: function(values, silent) {
			var items = $.isArray(values) ? values : [values];
			for (var i = 0, n = items.length; i < n; i++) {
				this.isPending = (i < n - 1);
				this.addItem(items[i], silent);
			}
		},
	
		/**
		 * "Selects" an item. Adds it to the list
		 * at the current caret position.
		 *
		 * @param {string} value
		 * @param {boolean} silent
		 */
		addItem: function(value, silent) {
			var events = silent ? [] : ['change'];
	
			debounce_events(this, events, function() {
				var $item, $option, $options;
				var self = this;
				var inputMode = self.settings.mode;
				var i, active, value_next, wasFull;
				value = hash_key(value);
	
				if (self.items.indexOf(value) !== -1) {
					if (inputMode === 'single') self.close();
					return;
				}
	
				if (!self.options.hasOwnProperty(value)) return;
				if (inputMode === 'single') self.clear(silent);
				if (inputMode === 'multi' && self.isFull()) return;
	
				$item = $(self.render('item', self.options[value]));
				wasFull = self.isFull();
				self.items.splice(self.caretPos, 0, value);
				self.insertAtCaret($item);
				if (!self.isPending || (!wasFull && self.isFull())) {
					self.refreshState();
				}
	
				if (self.isSetup) {
					$options = self.$dropdown_content.find('[data-selectable]');
	
					// update menu / remove the option (if this is not one item being added as part of series)
					if (!self.isPending) {
						$option = self.getOption(value);
						value_next = self.getAdjacentOption($option, 1).attr('data-value');
						self.refreshOptions(self.isFocused && inputMode !== 'single');
						if (value_next) {
							self.setActiveOption(self.getOption(value_next));
						}
					}
	
					// hide the menu if the maximum number of items have been selected or no options are left
					if (!$options.length || self.isFull()) {
						self.close();
					} else {
						self.positionDropdown();
					}
	
					self.updatePlaceholder();
					self.trigger('item_add', value, $item);
					self.updateOriginalInput({silent: silent});
				}
			});
		},
	
		/**
		 * Removes the selected item matching
		 * the provided value.
		 *
		 * @param {string} value
		 */
		removeItem: function(value, silent) {
			var self = this;
			var $item, i, idx;
	
			$item = (value instanceof $) ? value : self.getItem(value);
			value = hash_key($item.attr('data-value'));
			i = self.items.indexOf(value);
	
			if (i !== -1) {
				$item.remove();
				if ($item.hasClass('active')) {
					idx = self.$activeItems.indexOf($item[0]);
					self.$activeItems.splice(idx, 1);
				}
	
				self.items.splice(i, 1);
				self.lastQuery = null;
				if (!self.settings.persist && self.userOptions.hasOwnProperty(value)) {
					self.removeOption(value, silent);
				}
	
				if (i < self.caretPos) {
					self.setCaret(self.caretPos - 1);
				}
	
				self.refreshState();
				self.updatePlaceholder();
				self.updateOriginalInput({silent: silent});
				self.positionDropdown();
				self.trigger('item_remove', value, $item);
			}
		},
	
		/**
		 * Invokes the `create` method provided in the
		 * selectize options that should provide the data
		 * for the new item, given the user input.
		 *
		 * Once this completes, it will be added
		 * to the item list.
		 *
		 * @param {string} value
		 * @param {boolean} [triggerDropdown]
		 * @param {function} [callback]
		 * @return {boolean}
		 */
		createItem: function(input, triggerDropdown) {
			var self  = this;
			var caret = self.caretPos;
			input = input || $.trim(self.$control_input.val() || '');
	
			var callback = arguments[arguments.length - 1];
			if (typeof callback !== 'function') callback = function() {};
	
			if (typeof triggerDropdown !== 'boolean') {
				triggerDropdown = true;
			}
	
			if (!self.canCreate(input)) {
				callback();
				return false;
			}
	
			self.lock();
	
			var setup = (typeof self.settings.create === 'function') ? this.settings.create : function(input) {
				var data = {};
				data[self.settings.labelField] = input;
				data[self.settings.valueField] = input;
				return data;
			};
	
			var create = once(function(data) {
				self.unlock();
	
				if (!data || typeof data !== 'object') return callback();
				var value = hash_key(data[self.settings.valueField]);
				if (typeof value !== 'string') return callback();
	
				self.setTextboxValue('');
				self.addOption(data);
				self.setCaret(caret);
				self.addItem(value);
				self.refreshOptions(triggerDropdown && self.settings.mode !== 'single');
				callback(data);
			});
	
			var output = setup.apply(this, [input, create]);
			if (typeof output !== 'undefined') {
				create(output);
			}
	
			return true;
		},
	
		/**
		 * Re-renders the selected item lists.
		 */
		refreshItems: function() {
			this.lastQuery = null;
	
			if (this.isSetup) {
				this.addItem(this.items);
			}
	
			this.refreshState();
			this.updateOriginalInput();
		},
	
		/**
		 * Updates all state-dependent attributes
		 * and CSS classes.
		 */
		refreshState: function() {
			var invalid, self = this;
			if (self.isRequired) {
				if (self.items.length) self.isInvalid = false;
				self.$control_input.prop('required', invalid);
			}
			self.refreshClasses();
		},
	
		/**
		 * Updates all state-dependent CSS classes.
		 */
		refreshClasses: function() {
			var self     = this;
			var isFull   = self.isFull();
			var isLocked = self.isLocked;
	
			self.$wrapper
				.toggleClass('rtl', self.rtl);
	
			self.$control
				.toggleClass('focus', self.isFocused)
				.toggleClass('disabled', self.isDisabled)
				.toggleClass('required', self.isRequired)
				.toggleClass('invalid', self.isInvalid)
				.toggleClass('locked', isLocked)
				.toggleClass('full', isFull).toggleClass('not-full', !isFull)
				.toggleClass('input-active', self.isFocused && !self.isInputHidden)
				.toggleClass('dropdown-active', self.isOpen)
				.toggleClass('has-options', !$.isEmptyObject(self.options))
				.toggleClass('has-items', self.items.length > 0);
	
			self.$control_input.data('grow', !isFull && !isLocked);
		},
	
		/**
		 * Determines whether or not more items can be added
		 * to the control without exceeding the user-defined maximum.
		 *
		 * @returns {boolean}
		 */
		isFull: function() {
			return this.settings.maxItems !== null && this.items.length >= this.settings.maxItems;
		},
	
		/**
		 * Refreshes the original <select> or <input>
		 * element to reflect the current state.
		 */
		updateOriginalInput: function(opts) {
			var i, n, options, label, self = this;
			opts = opts || {};
	
			if (self.tagType === TAG_SELECT) {
				options = [];
				for (i = 0, n = self.items.length; i < n; i++) {
					label = self.options[self.items[i]][self.settings.labelField] || '';
					options.push('<option value="' + escape_html(self.items[i]) + '" selected="selected">' + escape_html(label) + '</option>');
				}
				if (!options.length && !this.$input.attr('multiple')) {
					options.push('<option value="" selected="selected"></option>');
				}
				self.$input.html(options.join(''));
			} else {
				self.$input.val(self.getValue());
				self.$input.attr('value',self.$input.val());
			}
	
			if (self.isSetup) {
				if (!opts.silent) {
					self.trigger('change', self.$input.val());
				}
			}
		},
	
		/**
		 * Shows/hide the input placeholder depending
		 * on if there items in the list already.
		 */
		updatePlaceholder: function() {
			if (!this.settings.placeholder) return;
			var $input = this.$control_input;
	
			if (this.items.length) {
				$input.removeAttr('placeholder');
			} else {
				$input.attr('placeholder', this.settings.placeholder);
			}
			$input.triggerHandler('update', {force: true});
		},
	
		/**
		 * Shows the autocomplete dropdown containing
		 * the available options.
		 */
		open: function() {
			var self = this;
	
			if (self.isLocked || self.isOpen || (self.settings.mode === 'multi' && self.isFull())) return;
			self.focus();
			self.isOpen = true;
			self.refreshState();
			self.$dropdown.css({visibility: 'hidden', display: 'block'});
			self.positionDropdown();
			self.$dropdown.css({visibility: 'visible'});
			self.trigger('dropdown_open', self.$dropdown);
		},
	
		/**
		 * Closes the autocomplete dropdown menu.
		 */
		close: function() {
			var self = this;
			var trigger = self.isOpen;
	
			if (self.settings.mode === 'single' && self.items.length) {
				self.hideInput();
			}
	
			self.isOpen = false;
			self.$dropdown.hide();
			self.setActiveOption(null);
			self.refreshState();
	
			if (trigger) self.trigger('dropdown_close', self.$dropdown);
		},
	
		/**
		 * Calculates and applies the appropriate
		 * position of the dropdown.
		 */
		positionDropdown: function() {
			var $control = this.$control;
			var offset = this.settings.dropdownParent === 'body' ? $control.offset() : $control.position();
			offset.top += $control.outerHeight(true);
	
			this.$dropdown.css({
				width : $control.outerWidth(),
				top   : offset.top,
				left  : offset.left
			});
		},
	
		/**
		 * Resets / clears all selected items
		 * from the control.
		 *
		 * @param {boolean} silent
		 */
		clear: function(silent) {
			var self = this;
	
			if (!self.items.length) return;
			self.$control.children(':not(input)').remove();
			self.items = [];
			self.lastQuery = null;
			self.setCaret(0);
			self.setActiveItem(null);
			self.updatePlaceholder();
			self.updateOriginalInput({silent: silent});
			self.refreshState();
			self.showInput();
			self.trigger('clear');
		},
	
		/**
		 * A helper method for inserting an element
		 * at the current caret position.
		 *
		 * @param {object} $el
		 */
		insertAtCaret: function($el) {
			var caret = Math.min(this.caretPos, this.items.length);
			if (caret === 0) {
				this.$control.prepend($el);
			} else {
				$(this.$control[0].childNodes[caret]).before($el);
			}
			this.setCaret(caret + 1);
		},
	
		/**
		 * Removes the current selected item(s).
		 *
		 * @param {object} e (optional)
		 * @returns {boolean}
		 */
		deleteSelection: function(e) {
			var i, n, direction, selection, values, caret, option_select, $option_select, $tail;
			var self = this;
	
			direction = (e && e.keyCode === KEY_BACKSPACE) ? -1 : 1;
			selection = getSelection(self.$control_input[0]);
	
			if (self.$activeOption && !self.settings.hideSelected) {
				option_select = self.getAdjacentOption(self.$activeOption, -1).attr('data-value');
			}
	
			// determine items that will be removed
			values = [];
	
			if (self.$activeItems.length) {
				$tail = self.$control.children('.active:' + (direction > 0 ? 'last' : 'first'));
				caret = self.$control.children(':not(input)').index($tail);
				if (direction > 0) { caret++; }
	
				for (i = 0, n = self.$activeItems.length; i < n; i++) {
					values.push($(self.$activeItems[i]).attr('data-value'));
				}
				if (e) {
					e.preventDefault();
					e.stopPropagation();
				}
			} else if ((self.isFocused || self.settings.mode === 'single') && self.items.length) {
				if (direction < 0 && selection.start === 0 && selection.length === 0) {
					values.push(self.items[self.caretPos - 1]);
				} else if (direction > 0 && selection.start === self.$control_input.val().length) {
					values.push(self.items[self.caretPos]);
				}
			}
	
			// allow the callback to abort
			if (!values.length || (typeof self.settings.onDelete === 'function' && self.settings.onDelete.apply(self, [values]) === false)) {
				return false;
			}
	
			// perform removal
			if (typeof caret !== 'undefined') {
				self.setCaret(caret);
			}
			while (values.length) {
				self.removeItem(values.pop());
			}
	
			self.showInput();
			self.positionDropdown();
			self.refreshOptions(true);
	
			// select previous option
			if (option_select) {
				$option_select = self.getOption(option_select);
				if ($option_select.length) {
					self.setActiveOption($option_select);
				}
			}
	
			return true;
		},
	
		/**
		 * Selects the previous / next item (depending
		 * on the `direction` argument).
		 *
		 * > 0 - right
		 * < 0 - left
		 *
		 * @param {int} direction
		 * @param {object} e (optional)
		 */
		advanceSelection: function(direction, e) {
			var tail, selection, idx, valueLength, cursorAtEdge, $tail;
			var self = this;
	
			if (direction === 0) return;
			if (self.rtl) direction *= -1;
	
			tail = direction > 0 ? 'last' : 'first';
			selection = getSelection(self.$control_input[0]);
	
			if (self.isFocused && !self.isInputHidden) {
				valueLength = self.$control_input.val().length;
				cursorAtEdge = direction < 0
					? selection.start === 0 && selection.length === 0
					: selection.start === valueLength;
	
				if (cursorAtEdge && !valueLength) {
					self.advanceCaret(direction, e);
				}
			} else {
				$tail = self.$control.children('.active:' + tail);
				if ($tail.length) {
					idx = self.$control.children(':not(input)').index($tail);
					self.setActiveItem(null);
					self.setCaret(direction > 0 ? idx + 1 : idx);
				}
			}
		},
	
		/**
		 * Moves the caret left / right.
		 *
		 * @param {int} direction
		 * @param {object} e (optional)
		 */
		advanceCaret: function(direction, e) {
			var self = this, fn, $adj;
	
			if (direction === 0) return;
	
			fn = direction > 0 ? 'next' : 'prev';
			if (self.isShiftDown) {
				$adj = self.$control_input[fn]();
				if ($adj.length) {
					self.hideInput();
					self.setActiveItem($adj);
					e && e.preventDefault();
				}
			} else {
				self.setCaret(self.caretPos + direction);
			}
		},
	
		/**
		 * Moves the caret to the specified index.
		 *
		 * @param {int} i
		 */
		setCaret: function(i) {
			var self = this;
	
			if (self.settings.mode === 'single') {
				i = self.items.length;
			} else {
				i = Math.max(0, Math.min(self.items.length, i));
			}
	
			if(!self.isPending) {
				// the input must be moved by leaving it in place and moving the
				// siblings, due to the fact that focus cannot be restored once lost
				// on mobile webkit devices
				var j, n, fn, $children, $child;
				$children = self.$control.children(':not(input)');
				for (j = 0, n = $children.length; j < n; j++) {
					$child = $($children[j]).detach();
					if (j <  i) {
						self.$control_input.before($child);
					} else {
						self.$control.append($child);
					}
				}
			}
	
			self.caretPos = i;
		},
	
		/**
		 * Disables user input on the control. Used while
		 * items are being asynchronously created.
		 */
		lock: function() {
			this.close();
			this.isLocked = true;
			this.refreshState();
		},
	
		/**
		 * Re-enables user input on the control.
		 */
		unlock: function() {
			this.isLocked = false;
			this.refreshState();
		},
	
		/**
		 * Disables user input on the control completely.
		 * While disabled, it cannot receive focus.
		 */
		disable: function() {
			var self = this;
			self.$input.prop('disabled', true);
			self.$control_input.prop('disabled', true).prop('tabindex', -1);
			self.isDisabled = true;
			self.lock();
		},
	
		/**
		 * Enables the control so that it can respond
		 * to focus and user input.
		 */
		enable: function() {
			var self = this;
			self.$input.prop('disabled', false);
			self.$control_input.prop('disabled', false).prop('tabindex', self.tabIndex);
			self.isDisabled = false;
			self.unlock();
		},
	
		/**
		 * Completely destroys the control and
		 * unbinds all event listeners so that it can
		 * be garbage collected.
		 */
		destroy: function() {
			var self = this;
			var eventNS = self.eventNS;
			var revertSettings = self.revertSettings;
	
			self.trigger('destroy');
			self.off();
			self.$wrapper.remove();
			self.$dropdown.remove();
	
			self.$input
				.html('')
				.append(revertSettings.$children)
				.removeAttr('tabindex')
				.removeClass('selectized')
				.attr({tabindex: revertSettings.tabindex})
				.show();
	
			self.$control_input.removeData('grow');
			self.$input.removeData('selectize');
	
			$(window).off(eventNS);
			$(document).off(eventNS);
			$(document.body).off(eventNS);
	
			delete self.$input[0].selectize;
		},
	
		/**
		 * A helper method for rendering "item" and
		 * "option" templates, given the data.
		 *
		 * @param {string} templateName
		 * @param {object} data
		 * @returns {string}
		 */
		render: function(templateName, data) {
			var value, id, label;
			var html = '';
			var cache = false;
			var self = this;
			var regex_tag = /^[\t \r\n]*<([a-z][a-z0-9\-_]*(?:\:[a-z][a-z0-9\-_]*)?)/i;
	
			if (templateName === 'option' || templateName === 'item') {
				value = hash_key(data[self.settings.valueField]);
				cache = !!value;
			}
	
			// pull markup from cache if it exists
			if (cache) {
				if (!isset(self.renderCache[templateName])) {
					self.renderCache[templateName] = {};
				}
				if (self.renderCache[templateName].hasOwnProperty(value)) {
					return self.renderCache[templateName][value];
				}
			}
	
			// render markup
			html = $(self.settings.render[templateName].apply(this, [data, escape_html]));
	
			// add mandatory attributes
			if (templateName === 'option' || templateName === 'option_create') {
				html.attr('data-selectable', '');
			}
			else if (templateName === 'optgroup') {
				id = data[self.settings.optgroupValueField] || '';
				html.attr('data-group', id);
			}
			if (templateName === 'option' || templateName === 'item') {
				html.attr('data-value', value || '');
			}
	
			// update cache
			if (cache) {
				self.renderCache[templateName][value] = html[0];
			}
	
			return html[0];
		},
	
		/**
		 * Clears the render cache for a template. If
		 * no template is given, clears all render
		 * caches.
		 *
		 * @param {string} templateName
		 */
		clearCache: function(templateName) {
			var self = this;
			if (typeof templateName === 'undefined') {
				self.renderCache = {};
			} else {
				delete self.renderCache[templateName];
			}
		},
	
		/**
		 * Determines whether or not to display the
		 * create item prompt, given a user input.
		 *
		 * @param {string} input
		 * @return {boolean}
		 */
		canCreate: function(input) {
			var self = this;
			if (!self.settings.create) return false;
			var filter = self.settings.createFilter;
			return input.length
				&& (typeof filter !== 'function' || filter.apply(self, [input]))
				&& (typeof filter !== 'string' || new RegExp(filter).test(input))
				&& (!(filter instanceof RegExp) || filter.test(input));
		}
	
	});
	
	
	Selectize.count = 0;
	Selectize.defaults = {
		options: [],
		optgroups: [],
	
		plugins: [],
		delimiter: ',',
		splitOn: null, // regexp or string for splitting up values from a paste command
		persist: true,
		diacritics: true,
		create: false,
		createOnBlur: false,
		createFilter: null,
		highlight: true,
		openOnFocus: true,
		maxOptions: 1000,
		maxItems: null,
		hideSelected: null,
		addPrecedence: false,
		selectOnTab: false,
		preload: false,
		allowEmptyOption: false,
		closeAfterSelect: false,
	
		scrollDuration: 60,
		loadThrottle: 300,
		loadingClass: 'loading',
	
		dataAttr: 'data-data',
		optgroupField: 'optgroup',
		valueField: 'value',
		labelField: 'text',
		optgroupLabelField: 'label',
		optgroupValueField: 'value',
		lockOptgroupOrder: false,
	
		sortField: '$order',
		searchField: ['text'],
		searchConjunction: 'and',
	
		mode: null,
		wrapperClass: 'selectize-control',
		inputClass: 'selectize-input',
		dropdownClass: 'selectize-dropdown',
		dropdownContentClass: 'selectize-dropdown-content',
	
		dropdownParent: null,
	
		copyClassesToDropdown: true,
	
		/*
		load                 : null, // function(query, callback) { ... }
		score                : null, // function(search) { ... }
		onInitialize         : null, // function() { ... }
		onChange             : null, // function(value) { ... }
		onItemAdd            : null, // function(value, $item) { ... }
		onItemRemove         : null, // function(value) { ... }
		onClear              : null, // function() { ... }
		onOptionAdd          : null, // function(value, data) { ... }
		onOptionRemove       : null, // function(value) { ... }
		onOptionClear        : null, // function() { ... }
		onOptionGroupAdd     : null, // function(id, data) { ... }
		onOptionGroupRemove  : null, // function(id) { ... }
		onOptionGroupClear   : null, // function() { ... }
		onDropdownOpen       : null, // function($dropdown) { ... }
		onDropdownClose      : null, // function($dropdown) { ... }
		onType               : null, // function(str) { ... }
		onDelete             : null, // function(values) { ... }
		*/
	
		render: {
			/*
			item: null,
			optgroup: null,
			optgroup_header: null,
			option: null,
			option_create: null
			*/
		}
	};
	
	
	$.fn.selectize = function(settings_user) {
		var defaults             = $.fn.selectize.defaults;
		var settings             = $.extend({}, defaults, settings_user);
		var attr_data            = settings.dataAttr;
		var field_label          = settings.labelField;
		var field_value          = settings.valueField;
		var field_optgroup       = settings.optgroupField;
		var field_optgroup_label = settings.optgroupLabelField;
		var field_optgroup_value = settings.optgroupValueField;
	
		/**
		 * Initializes selectize from a <input type="text"> element.
		 *
		 * @param {object} $input
		 * @param {object} settings_element
		 */
		var init_textbox = function($input, settings_element) {
			var i, n, values, option;
	
			var data_raw = $input.attr(attr_data);
	
			if (!data_raw) {
				var value = $.trim($input.val() || '');
				if (!settings.allowEmptyOption && !value.length) return;
				values = value.split(settings.delimiter);
				for (i = 0, n = values.length; i < n; i++) {
					option = {};
					option[field_label] = values[i];
					option[field_value] = values[i];
					settings_element.options.push(option);
				}
				settings_element.items = values;
			} else {
				settings_element.options = JSON.parse(data_raw);
				for (i = 0, n = settings_element.options.length; i < n; i++) {
					settings_element.items.push(settings_element.options[i][field_value]);
				}
			}
		};
	
		/**
		 * Initializes selectize from a <select> element.
		 *
		 * @param {object} $input
		 * @param {object} settings_element
		 */
		var init_select = function($input, settings_element) {
			var i, n, tagName, $children, order = 0;
			var options = settings_element.options;
			var optionsMap = {};
	
			var readData = function($el) {
				var data = attr_data && $el.attr(attr_data);
				if (typeof data === 'string' && data.length) {
					return JSON.parse(data);
				}
				return null;
			};
	
			var addOption = function($option, group) {
				$option = $($option);
	
				var value = hash_key($option.val());
				if (!value && !settings.allowEmptyOption) return;
	
				// if the option already exists, it's probably been
				// duplicated in another optgroup. in this case, push
				// the current group to the "optgroup" property on the
				// existing option so that it's rendered in both places.
				if (optionsMap.hasOwnProperty(value)) {
					if (group) {
						var arr = optionsMap[value][field_optgroup];
						if (!arr) {
							optionsMap[value][field_optgroup] = group;
						} else if (!$.isArray(arr)) {
							optionsMap[value][field_optgroup] = [arr, group];
						} else {
							arr.push(group);
						}
					}
					return;
				}
	
				var option             = readData($option) || {};
				option[field_label]    = option[field_label] || $option.text();
				option[field_value]    = option[field_value] || value;
				option[field_optgroup] = option[field_optgroup] || group;
	
				optionsMap[value] = option;
				options.push(option);
	
				if ($option.is(':selected')) {
					settings_element.items.push(value);
				}
			};
	
			var addGroup = function($optgroup) {
				var i, n, id, optgroup, $options;
	
				$optgroup = $($optgroup);
				id = $optgroup.attr('label');
	
				if (id) {
					optgroup = readData($optgroup) || {};
					optgroup[field_optgroup_label] = id;
					optgroup[field_optgroup_value] = id;
					settings_element.optgroups.push(optgroup);
				}
	
				$options = $('option', $optgroup);
				for (i = 0, n = $options.length; i < n; i++) {
					addOption($options[i], id);
				}
			};
	
			settings_element.maxItems = $input.attr('multiple') ? null : 1;
	
			$children = $input.children();
			for (i = 0, n = $children.length; i < n; i++) {
				tagName = $children[i].tagName.toLowerCase();
				if (tagName === 'optgroup') {
					addGroup($children[i]);
				} else if (tagName === 'option') {
					addOption($children[i]);
				}
			}
		};
	
		return this.each(function() {
			if (this.selectize) return;
	
			var instance;
			var $input = $(this);
			var tag_name = this.tagName.toLowerCase();
			var placeholder = $input.attr('placeholder') || $input.attr('data-placeholder');
			if (!placeholder && !settings.allowEmptyOption) {
				placeholder = $input.children('option[value=""]').text();
			}
	
			var settings_element = {
				'placeholder' : placeholder,
				'options'     : [],
				'optgroups'   : [],
				'items'       : []
			};
	
			if (tag_name === 'select') {
				init_select($input, settings_element);
			} else {
				init_textbox($input, settings_element);
			}
	
			instance = new Selectize($input, $.extend(true, {}, defaults, settings_element, settings_user));
		});
	};
	
	$.fn.selectize.defaults = Selectize.defaults;
	$.fn.selectize.support = {
		validity: SUPPORTS_VALIDITY_API
	};
	
	
	Selectize.define('drag_drop', function(options) {
		if (!$.fn.sortable) throw new Error('The "drag_drop" plugin requires jQuery UI "sortable".');
		if (this.settings.mode !== 'multi') return;
		var self = this;
	
		self.lock = (function() {
			var original = self.lock;
			return function() {
				var sortable = self.$control.data('sortable');
				if (sortable) sortable.disable();
				return original.apply(self, arguments);
			};
		})();
	
		self.unlock = (function() {
			var original = self.unlock;
			return function() {
				var sortable = self.$control.data('sortable');
				if (sortable) sortable.enable();
				return original.apply(self, arguments);
			};
		})();
	
		self.setup = (function() {
			var original = self.setup;
			return function() {
				original.apply(this, arguments);
	
				var $control = self.$control.sortable({
					items: '[data-value]',
					forcePlaceholderSize: true,
					disabled: self.isLocked,
					start: function(e, ui) {
						ui.placeholder.css('width', ui.helper.css('width'));
						$control.css({overflow: 'visible'});
					},
					stop: function() {
						$control.css({overflow: 'hidden'});
						var active = self.$activeItems ? self.$activeItems.slice() : null;
						var values = [];
						$control.children('[data-value]').each(function() {
							values.push($(this).attr('data-value'));
						});
						self.setValue(values);
						self.setActiveItem(active);
					}
				});
			};
		})();
	
	});
	
	Selectize.define('dropdown_header', function(options) {
		var self = this;
	
		options = $.extend({
			title         : 'Untitled',
			headerClass   : 'selectize-dropdown-header',
			titleRowClass : 'selectize-dropdown-header-title',
			labelClass    : 'selectize-dropdown-header-label',
			closeClass    : 'selectize-dropdown-header-close',
	
			html: function(data) {
				return (
					'<div class="' + data.headerClass + '">' +
						'<div class="' + data.titleRowClass + '">' +
							'<span class="' + data.labelClass + '">' + data.title + '</span>' +
							'<a href="javascript:void(0)" class="' + data.closeClass + '">&times;</a>' +
						'</div>' +
					'</div>'
				);
			}
		}, options);
	
		self.setup = (function() {
			var original = self.setup;
			return function() {
				original.apply(self, arguments);
				self.$dropdown_header = $(options.html(options));
				self.$dropdown.prepend(self.$dropdown_header);
			};
		})();
	
	});
	
	Selectize.define('optgroup_columns', function(options) {
		var self = this;
	
		options = $.extend({
			equalizeWidth  : true,
			equalizeHeight : true
		}, options);
	
		this.getAdjacentOption = function($option, direction) {
			var $options = $option.closest('[data-group]').find('[data-selectable]');
			var index    = $options.index($option) + direction;
	
			return index >= 0 && index < $options.length ? $options.eq(index) : $();
		};
	
		this.onKeyDown = (function() {
			var original = self.onKeyDown;
			return function(e) {
				var index, $option, $options, $optgroup;
	
				if (this.isOpen && (e.keyCode === KEY_LEFT || e.keyCode === KEY_RIGHT)) {
					self.ignoreHover = true;
					$optgroup = this.$activeOption.closest('[data-group]');
					index = $optgroup.find('[data-selectable]').index(this.$activeOption);
	
					if(e.keyCode === KEY_LEFT) {
						$optgroup = $optgroup.prev('[data-group]');
					} else {
						$optgroup = $optgroup.next('[data-group]');
					}
	
					$options = $optgroup.find('[data-selectable]');
					$option  = $options.eq(Math.min($options.length - 1, index));
					if ($option.length) {
						this.setActiveOption($option);
					}
					return;
				}
	
				return original.apply(this, arguments);
			};
		})();
	
		var getScrollbarWidth = function() {
			var div;
			var width = getScrollbarWidth.width;
			var doc = document;
	
			if (typeof width === 'undefined') {
				div = doc.createElement('div');
				div.innerHTML = '<div style="width:50px;height:50px;position:absolute;left:-50px;top:-50px;overflow:auto;"><div style="width:1px;height:100px;"></div></div>';
				div = div.firstChild;
				doc.body.appendChild(div);
				width = getScrollbarWidth.width = div.offsetWidth - div.clientWidth;
				doc.body.removeChild(div);
			}
			return width;
		};
	
		var equalizeSizes = function() {
			var i, n, height_max, width, width_last, width_parent, $optgroups;
	
			$optgroups = $('[data-group]', self.$dropdown_content);
			n = $optgroups.length;
			if (!n || !self.$dropdown_content.width()) return;
	
			if (options.equalizeHeight) {
				height_max = 0;
				for (i = 0; i < n; i++) {
					height_max = Math.max(height_max, $optgroups.eq(i).height());
				}
				$optgroups.css({height: height_max});
			}
	
			if (options.equalizeWidth) {
				width_parent = self.$dropdown_content.innerWidth() - getScrollbarWidth();
				width = Math.round(width_parent / n);
				$optgroups.css({width: width});
				if (n > 1) {
					width_last = width_parent - width * (n - 1);
					$optgroups.eq(n - 1).css({width: width_last});
				}
			}
		};
	
		if (options.equalizeHeight || options.equalizeWidth) {
			hook.after(this, 'positionDropdown', equalizeSizes);
			hook.after(this, 'refreshOptions', equalizeSizes);
		}
	
	
	});
	
	Selectize.define('remove_button', function(options) {
		options = $.extend({
				label     : '&times;',
				title     : 'Remove',
				className : 'remove',
				append    : true
			}, options);
	
			var singleClose = function(thisRef, options) {
	
				options.className = 'remove-single';
	
				var self = thisRef;
				var html = '<a href="javascript:void(0)" class="' + options.className + '" tabindex="-1" title="' + escape_html(options.title) + '">' + options.label + '</a>';
	
				/**
				 * Appends an element as a child (with raw HTML).
				 *
				 * @param {string} html_container
				 * @param {string} html_element
				 * @return {string}
				 */
				var append = function(html_container, html_element) {
					return html_container + html_element;
				};
	
				thisRef.setup = (function() {
					var original = self.setup;
					return function() {
						// override the item rendering method to add the button to each
						if (options.append) {
							var id = $(self.$input.context).attr('id');
							var selectizer = $('#'+id);
	
							var render_item = self.settings.render.item;
							self.settings.render.item = function(data) {
								return append(render_item.apply(thisRef, arguments), html);
							};
						}
	
						original.apply(thisRef, arguments);
	
						// add event listener
						thisRef.$control.on('click', '.' + options.className, function(e) {
							e.preventDefault();
							if (self.isLocked) return;
	
							self.clear();
						});
	
					};
				})();
			};
	
			var multiClose = function(thisRef, options) {
	
				var self = thisRef;
				var html = '<a href="javascript:void(0)" class="' + options.className + '" tabindex="-1" title="' + escape_html(options.title) + '">' + options.label + '</a>';
	
				/**
				 * Appends an element as a child (with raw HTML).
				 *
				 * @param {string} html_container
				 * @param {string} html_element
				 * @return {string}
				 */
				var append = function(html_container, html_element) {
					var pos = html_container.search(/(<\/[^>]+>\s*)$/);
					return html_container.substring(0, pos) + html_element + html_container.substring(pos);
				};
	
				thisRef.setup = (function() {
					var original = self.setup;
					return function() {
						// override the item rendering method to add the button to each
						if (options.append) {
							var render_item = self.settings.render.item;
							self.settings.render.item = function(data) {
								return append(render_item.apply(thisRef, arguments), html);
							};
						}
	
						original.apply(thisRef, arguments);
	
						// add event listener
						thisRef.$control.on('click', '.' + options.className, function(e) {
							e.preventDefault();
							if (self.isLocked) return;
	
							var $item = $(e.currentTarget).parent();
							self.setActiveItem($item);
							if (self.deleteSelection()) {
								self.setCaret(self.items.length);
							}
						});
	
					};
				})();
			};
	
			if (this.settings.mode === 'single') {
				singleClose(this, options);
				return;
			} else {
				multiClose(this, options);
			}
	});
	
	
	Selectize.define('restore_on_backspace', function(options) {
		var self = this;
	
		options.text = options.text || function(option) {
			return option[this.settings.labelField];
		};
	
		this.onKeyDown = (function() {
			var original = self.onKeyDown;
			return function(e) {
				var index, option;
				if (e.keyCode === KEY_BACKSPACE && this.$control_input.val() === '' && !this.$activeItems.length) {
					index = this.caretPos - 1;
					if (index >= 0 && index < this.items.length) {
						option = this.options[this.items[index]];
						if (this.deleteSelection(e)) {
							this.setTextboxValue(options.text.apply(this, [option]));
							this.refreshOptions(true);
						}
						e.preventDefault();
						return;
					}
				}
				return original.apply(this, arguments);
			};
		})();
	});
	

	return Selectize;
}));
(function($) {
  'use strict';

  var _currentSpinnerId = 0;

  function _scopedEventName(name, id) {
    return name + '.touchspin_' + id;
  }

  function _scopeEventNames(names, id) {
    return $.map(names, function(name) {
      return _scopedEventName(name, id);
    });
  }

  $.fn.TouchSpin = function(options) {

    if (options === 'destroy') {
      this.each(function() {
        var originalinput = $(this),
            originalinput_data = originalinput.data();
        $(document).off(_scopeEventNames([
          'mouseup',
          'touchend',
          'touchcancel',
          'mousemove',
          'touchmove',
          'scroll',
          'scrollstart'], originalinput_data.spinnerid).join(' '));
      });
      return;
    }

    var defaults = {
      min: 0,
      max: 100,
      initval: '',
      replacementval: '',
      step: 1,
      decimals: 0,
      stepinterval: 100,
      forcestepdivisibility: 'round', // none | floor | round | ceil
      stepintervaldelay: 500,
      verticalbuttons: false,
      verticalupclass: 'glyphicon glyphicon-chevron-up',
      verticaldownclass: 'glyphicon glyphicon-chevron-down',
      prefix: '',
      postfix: '',
      prefix_extraclass: '',
      postfix_extraclass: '',
      booster: true,
      boostat: 10,
      maxboostedstep: false,
      mousewheel: true,
      buttondown_class: 'btn btn-default',
      buttonup_class: 'btn btn-default',
      buttondown_txt: '-',
      buttonup_txt: '+'
    };

    var attributeMap = {
      min: 'min',
      max: 'max',
      initval: 'init-val',
      replacementval: 'replacement-val',
      step: 'step',
      decimals: 'decimals',
      stepinterval: 'step-interval',
      verticalbuttons: 'vertical-buttons',
      verticalupclass: 'vertical-up-class',
      verticaldownclass: 'vertical-down-class',
      forcestepdivisibility: 'force-step-divisibility',
      stepintervaldelay: 'step-interval-delay',
      prefix: 'prefix',
      postfix: 'postfix',
      prefix_extraclass: 'prefix-extra-class',
      postfix_extraclass: 'postfix-extra-class',
      booster: 'booster',
      boostat: 'boostat',
      maxboostedstep: 'max-boosted-step',
      mousewheel: 'mouse-wheel',
      buttondown_class: 'button-down-class',
      buttonup_class: 'button-up-class',
      buttondown_txt: 'button-down-txt',
      buttonup_txt: 'button-up-txt'
    };

    return this.each(function() {

      var settings,
          originalinput = $(this),
          originalinput_data = originalinput.data(),
          container,
          elements,
          value,
          downSpinTimer,
          upSpinTimer,
          downDelayTimeout,
          upDelayTimeout,
          spincount = 0,
          spinning = false;

      init();


      function init() {
        if (originalinput.data('alreadyinitialized')) {
          return;
        }

        originalinput.data('alreadyinitialized', true);
        _currentSpinnerId += 1;
        originalinput.data('spinnerid', _currentSpinnerId);


        if (!originalinput.is('input')) {
          console.log('Must be an input.');
          return;
        }

        _initSettings();
        _setInitval();
        _checkValue();
        _buildHtml();
        _initElements();
        _hideEmptyPrefixPostfix();
        _bindEvents();
        _bindEventsInterface();
        elements.input.css('display', 'block');
      }

      function _setInitval() {
        if (settings.initval !== '' && originalinput.val() === '') {
          originalinput.val(settings.initval);
        }
      }

      function changeSettings(newsettings) {
        _updateSettings(newsettings);
        _checkValue();

        var value = elements.input.val();

        if (value !== '') {
          value = Number(elements.input.val());
          elements.input.val(value.toFixed(settings.decimals));
        }
      }

      function _initSettings() {
        settings = $.extend({}, defaults, originalinput_data, _parseAttributes(), options);
      }

      function _parseAttributes() {
        var data = {};
        $.each(attributeMap, function(key, value) {
          var attrName = 'bts-' + value + '';
          if (originalinput.is('[data-' + attrName + ']')) {
            data[key] = originalinput.data(attrName);
          }
        });
        return data;
      }

      function _updateSettings(newsettings) {
        settings = $.extend({}, settings, newsettings);

        // Update postfix and prefix texts if those settings were changed.
        if (newsettings.postfix) {
          originalinput.parent().find('.bootstrap-touchspin-postfix').text(newsettings.postfix);
        }
        if (newsettings.prefix) {
          originalinput.parent().find('.bootstrap-touchspin-prefix').text(newsettings.prefix);
        }
      }

      function _buildHtml() {
        var initval = originalinput.val(),
            parentelement = originalinput.parent();

        if (initval !== '') {
          initval = Number(initval).toFixed(settings.decimals);
        }

        originalinput.data('initvalue', initval).val(initval);
        originalinput.addClass('form-control');

        if (parentelement.hasClass('input-group')) {
          _advanceInputGroup(parentelement);
        }
        else {
          _buildInputGroup();
        }
      }

      function _advanceInputGroup(parentelement) {
        parentelement.addClass('bootstrap-touchspin');

        var prev = originalinput.prev(),
            next = originalinput.next();

        var downhtml,
            uphtml,
            prefixhtml = '<span class="input-group-addon bootstrap-touchspin-prefix">' + settings.prefix + '</span>',
            postfixhtml = '<span class="input-group-addon bootstrap-touchspin-postfix">' + settings.postfix + '</span>';

        if (prev.hasClass('input-group-btn')) {
          downhtml = '<button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">' + settings.buttondown_txt + '</button>';
          prev.append(downhtml);
        }
        else {
          downhtml = '<span class="input-group-btn"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">' + settings.buttondown_txt + '</button></span>';
          $(downhtml).insertBefore(originalinput);
        }

        if (next.hasClass('input-group-btn')) {
          uphtml = '<button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">' + settings.buttonup_txt + '</button>';
          next.prepend(uphtml);
        }
        else {
          uphtml = '<span class="input-group-btn"><button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">' + settings.buttonup_txt + '</button></span>';
          $(uphtml).insertAfter(originalinput);
        }

        $(prefixhtml).insertBefore(originalinput);
        $(postfixhtml).insertAfter(originalinput);

        container = parentelement;
      }

      function _buildInputGroup() {
        var html;

        if (settings.verticalbuttons) {
          html = '<div class="input-group bootstrap-touchspin"><span class="input-group-addon bootstrap-touchspin-prefix">' + settings.prefix + '</span><span class="input-group-addon bootstrap-touchspin-postfix">' + settings.postfix + '</span><span class="input-group-btn-vertical"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-up" type="button"><i class="' + settings.verticalupclass + '"></i></button><button class="' + settings.buttonup_class + ' bootstrap-touchspin-down" type="button"><i class="' + settings.verticaldownclass + '"></i></button></span></div>';
        }
        else {
          html = '<div class="input-group bootstrap-touchspin"><span class="input-group-btn"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">' + settings.buttondown_txt + '</button></span><span class="input-group-addon bootstrap-touchspin-prefix">' + settings.prefix + '</span><span class="input-group-addon bootstrap-touchspin-postfix">' + settings.postfix + '</span><span class="input-group-btn"><button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">' + settings.buttonup_txt + '</button></span></div>';
        }

        container = $(html).insertBefore(originalinput);

        $('.bootstrap-touchspin-prefix', container).after(originalinput);

        if (originalinput.hasClass('input-sm')) {
          container.addClass('input-group-sm');
        }
        else if (originalinput.hasClass('input-lg')) {
          container.addClass('input-group-lg');
        }
      }

      function _initElements() {
        elements = {
          down: $('.bootstrap-touchspin-down', container),
          up: $('.bootstrap-touchspin-up', container),
          input: $('input', container),
          prefix: $('.bootstrap-touchspin-prefix', container).addClass(settings.prefix_extraclass),
          postfix: $('.bootstrap-touchspin-postfix', container).addClass(settings.postfix_extraclass)
        };
      }

      function _hideEmptyPrefixPostfix() {
        if (settings.prefix === '') {
          elements.prefix.hide();
        }

        if (settings.postfix === '') {
          elements.postfix.hide();
        }
      }

      function _bindEvents() {
        originalinput.on('keydown', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 38) {
            if (spinning !== 'up') {
              upOnce();
              startUpSpin();
            }
            ev.preventDefault();
          }
          else if (code === 40) {
            if (spinning !== 'down') {
              downOnce();
              startDownSpin();
            }
            ev.preventDefault();
          }
        });

        originalinput.on('keyup', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 38) {
            stopSpin();
          }
          else if (code === 40) {
            stopSpin();
          }
        });

        originalinput.on('blur', function() {
          _checkValue();
        });

        elements.down.on('keydown', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            if (spinning !== 'down') {
              downOnce();
              startDownSpin();
            }
            ev.preventDefault();
          }
        });

        elements.down.on('keyup', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            stopSpin();
          }
        });

        elements.up.on('keydown', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            if (spinning !== 'up') {
              upOnce();
              startUpSpin();
            }
            ev.preventDefault();
          }
        });

        elements.up.on('keyup', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            stopSpin();
          }
        });

        elements.down.on('mousedown.touchspin', function(ev) {
          elements.down.off('touchstart.touchspin');  // android 4 workaround

          if (originalinput.is(':disabled')) {
            return;
          }

          downOnce();
          startDownSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.down.on('touchstart.touchspin', function(ev) {
          elements.down.off('mousedown.touchspin');  // android 4 workaround

          if (originalinput.is(':disabled')) {
            return;
          }

          downOnce();
          startDownSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.up.on('mousedown.touchspin', function(ev) {
          elements.up.off('touchstart.touchspin');  // android 4 workaround

          if (originalinput.is(':disabled')) {
            return;
          }

          upOnce();
          startUpSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.up.on('touchstart.touchspin', function(ev) {
          elements.up.off('mousedown.touchspin');  // android 4 workaround

          if (originalinput.is(':disabled')) {
            return;
          }

          upOnce();
          startUpSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.up.on('mouseout touchleave touchend touchcancel', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          stopSpin();
        });

        elements.down.on('mouseout touchleave touchend touchcancel', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          stopSpin();
        });

        elements.down.on('mousemove touchmove', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          ev.preventDefault();
        });

        elements.up.on('mousemove touchmove', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          ev.preventDefault();
        });

        $(document).on(_scopeEventNames(['mouseup', 'touchend', 'touchcancel'], _currentSpinnerId).join(' '), function(ev) {
          if (!spinning) {
            return;
          }

          ev.preventDefault();
          stopSpin();
        });

        $(document).on(_scopeEventNames(['mousemove', 'touchmove', 'scroll', 'scrollstart'], _currentSpinnerId).join(' '), function(ev) {
          if (!spinning) {
            return;
          }

          ev.preventDefault();
          stopSpin();
        });

        originalinput.on('mousewheel DOMMouseScroll', function(ev) {
          if (!settings.mousewheel || !originalinput.is(':focus')) {
            return;
          }

          var delta = ev.originalEvent.wheelDelta || -ev.originalEvent.deltaY || -ev.originalEvent.detail;

          ev.stopPropagation();
          ev.preventDefault();

          if (delta < 0) {
            downOnce();
          }
          else {
            upOnce();
          }
        });
      }

      function _bindEventsInterface() {
        originalinput.on('touchspin.uponce', function() {
          stopSpin();
          upOnce();
        });

        originalinput.on('touchspin.downonce', function() {
          stopSpin();
          downOnce();
        });

        originalinput.on('touchspin.startupspin', function() {
          startUpSpin();
        });

        originalinput.on('touchspin.startdownspin', function() {
          startDownSpin();
        });

        originalinput.on('touchspin.stopspin', function() {
          stopSpin();
        });

        originalinput.on('touchspin.updatesettings', function(e, newsettings) {
          changeSettings(newsettings);
        });
      }

      function _forcestepdivisibility(value) {
        switch (settings.forcestepdivisibility) {
          case 'round':
            return (Math.round(value / settings.step) * settings.step).toFixed(settings.decimals);
          case 'floor':
            return (Math.floor(value / settings.step) * settings.step).toFixed(settings.decimals);
          case 'ceil':
            return (Math.ceil(value / settings.step) * settings.step).toFixed(settings.decimals);
          default:
            return value;
        }
      }

      function _checkValue() {
        var val, parsedval, returnval;

        val = originalinput.val();

        if (val === '') {
          if (settings.replacementval !== '') {
            originalinput.val(settings.replacementval);
            originalinput.trigger('change');
          }
          return;
        }

        if (settings.decimals > 0 && val === '.') {
          return;
        }

        parsedval = parseFloat(val);

        if (isNaN(parsedval)) {
          if (settings.replacementval !== '') {
            parsedval = settings.replacementval;
          }
          else {
            parsedval = 0;
          }
        }

        returnval = parsedval;

        if (parsedval.toString() !== val) {
          returnval = parsedval;
        }

        if (parsedval < settings.min) {
          returnval = settings.min;
        }

        if (parsedval > settings.max) {
          returnval = settings.max;
        }

        returnval = _forcestepdivisibility(returnval);

        if (Number(val).toString() !== returnval.toString()) {
          originalinput.val(returnval);
          originalinput.trigger('change');
        }
      }

      function _getBoostedStep() {
        if (!settings.booster) {
          return settings.step;
        }
        else {
          var boosted = Math.pow(2, Math.floor(spincount / settings.boostat)) * settings.step;

          if (settings.maxboostedstep) {
            if (boosted > settings.maxboostedstep) {
              boosted = settings.maxboostedstep;
              value = Math.round((value / boosted)) * boosted;
            }
          }

          return Math.max(settings.step, boosted);
        }
      }

      function upOnce() {
        _checkValue();

        value = parseFloat(elements.input.val());
        if (isNaN(value)) {
          value = 0;
        }

        var initvalue = value,
            boostedstep = _getBoostedStep();

        value = value + boostedstep;

        if (value > settings.max) {
          value = settings.max;
          originalinput.trigger('touchspin.on.max');
          stopSpin();
        }

        elements.input.val(Number(value).toFixed(settings.decimals));

        if (initvalue !== value) {
          originalinput.trigger('change');
        }
      }

      function downOnce() {
        _checkValue();

        value = parseFloat(elements.input.val());
        if (isNaN(value)) {
          value = 0;
        }

        var initvalue = value,
            boostedstep = _getBoostedStep();

        value = value - boostedstep;

        if (value < settings.min) {
          value = settings.min;
          originalinput.trigger('touchspin.on.min');
          stopSpin();
        }

        elements.input.val(value.toFixed(settings.decimals));

        if (initvalue !== value) {
          originalinput.trigger('change');
        }
      }

      function startDownSpin() {
        stopSpin();

        spincount = 0;
        spinning = 'down';

        originalinput.trigger('touchspin.on.startspin');
        originalinput.trigger('touchspin.on.startdownspin');

        downDelayTimeout = setTimeout(function() {
          downSpinTimer = setInterval(function() {
            spincount++;
            downOnce();
          }, settings.stepinterval);
        }, settings.stepintervaldelay);
      }

      function startUpSpin() {
        stopSpin();

        spincount = 0;
        spinning = 'up';

        originalinput.trigger('touchspin.on.startspin');
        originalinput.trigger('touchspin.on.startupspin');

        upDelayTimeout = setTimeout(function() {
          upSpinTimer = setInterval(function() {
            spincount++;
            upOnce();
          }, settings.stepinterval);
        }, settings.stepintervaldelay);
      }

      function stopSpin() {
        clearTimeout(downDelayTimeout);
        clearTimeout(upDelayTimeout);
        clearInterval(downSpinTimer);
        clearInterval(upSpinTimer);

        switch (spinning) {
          case 'up':
            originalinput.trigger('touchspin.on.stopupspin');
            originalinput.trigger('touchspin.on.stopspin');
            break;
          case 'down':
            originalinput.trigger('touchspin.on.stopdownspin');
            originalinput.trigger('touchspin.on.stopspin');
            break;
        }

        spincount = 0;
        spinning = false;
      }

    });

  };

})(jQuery);

;
/**
 *
 */
(function($, window, document, undefined){

    /**
     *
     * @param element
     * @param options
     * @returns {S3Uploader}
     * @constructor
     */
    function S3Uploader(element, options) {
        this.element = element;
        this.$element = $(element);
        this.options = $.extend({}, $.fn.s3uploader.defaults, options);
        this.options = $.extend({}, this.options, this._parseHtmlDataAttributes(this.$element));
        this.jqXHRCollection = [];
        this.templateElements = {
            progress_container: '.js-fileupload-progress',
            progress_bar: '.progress',
            add_file_button: '.fileinput-button',
            file_upload_target: '.js-s3_fileupload',
            cancel_button: '.js-cancel_button',
            showExtendedBool: true
        };
        this._defaults = $.fn.s3uploader.defaults;
        this.init();
    }

    /**
     *
     * @type {{init: Function, setTemplate: Function, initFileUpload: Function, setProgress: Function, buttonToggler: Function, throwException: Function, log: Function, _parseHtmlDataAttributes: Function}}
     */
    S3Uploader.prototype = {
        /**
         *
         * @returns {S3Uploader}
         * @private
         */
        init: function(){
            if (!$.fn.fileupload) {
                this.throwException('missing-dependency', 'fileupload plugin required.');
            }
            this.setTemplate(this.options.templateEl);
            this.initFileUpload();
            return this;
        },
        /**
         *
         */
        setTemplate: function($el){
            this.$element.html(
                $el ? this.$element.parent().find($el) : Template.getTemplate(this.options.multiple, this.options.button_name, this.options.extended_upload_info)
            , true);
        },
        /**
         *
         */
        initFileUpload: function(){
            var that = this;
            var fileUpload = {
                url: "https://" + that.options.s3_bucket + ".s3.amazonaws.com",
                dataType: "xml",
                type: "POST",
                dropZone: that.options.drop_zone,
                add: function (e, data) {
                    //since we are overriding the add function, in order for image-resize to work we must call the parent add()
                    $.blueimp.fileupload.prototype.options.add.call(this, e, data);
                    if(!that.options.on_file_add(e, data)){
                        return false;
                    }
                    that.buttonToggler(true);
                    that.$element.find(that.templateElements.progress_container).show();
                    var hash = Math.random().toString(36).substr(2, 5);
                    var timestamp = Math.floor(new Date().getTime() / 1000);
                    var ajaxData = that.options.s3_key_payload;
                    ajaxData.filename = timestamp + '_' + data.files[0].name;
                    that.jqXHRCollection.push($.ajax({
                        url: that.options.s3_key_url,
                        dataType: 'JSON',
                        type: 'GET',
                        data: ajaxData,
                        success: function (response) {
                            $(document).trigger('s3uploader.s3_key_retrieved', response);
                            that.log('api.files.s3key: done', response);
                            data.formData = {
                                AWSAccessKeyId:         response.data.AWSAccessKeyId,
                                acl:                    response.data.acl,
                                key:                    response.data.key,
                                policy:                 response.data.policy,
                                success_action_status:  201,
                                signature:              response.data.signature
                            };
                            that.options.response = response;
                            that.options.file = data.files[0];
                            that.jqXHRCollection.push(data.submit());
                        },
                        fail: function (e, data, error) {
                            that.throwException(e.responseText, error);
                            that.log('api.files.s3key: fail', error);
                            that.buttonToggler(false);
                        }
                    }));
                },
                formData: {},
                success: function (data, textStatus, jqXHR) {
                    that.options.on_s3_upload(data, textStatus, jqXHR);
                },
                done: function (e, data) {},
                fail: function(e, data, error){
                    that.throwException(e.responseText, error);
                },
                always: function() {
                },
                progress: function (e, data) {
                    if (e.isDefaultPrevented()) {
                        return false;
                    }
                    var progress = Math.floor(data.loaded / data.total * 100);
                    if (data.context) {
                        that.setProgress(progress);
                    }
                },
                stop: function () {
                    that.resetProgress();
                    that.buttonToggler(false);
                }
            };
            fileUpload = $.extend(fileUpload, this.options.fileupload_options);
            that.$element.find(that.templateElements.file_upload_target).fileupload(fileUpload);
            $(this.templateElements.cancel_button).on('click', $.proxy(function(e){
                that = this;
                that.cancelAll();
            }, that));
        },
        /**
         *
         * @param percent
         */
        setProgress: function(percent) {
            var that = this;
            that.$element.find(that.templateElements.progress_bar)
                .attr('aria-valuenow', percent).children().first()
                .css('width', percent + '%');
        },

        /**
         *
         */
        resetProgress: function() {
            var that = this;
            setTimeout(function(){
                that.$element.find(that.templateElements.progress_bar)
                    .attr('aria-valuenow', 5).children().first()
                    .css('width', '5%');
            }, 1000);
        },
        /**
         *
         * @param enable
         */
        buttonToggler: function(enable) {
            var that = this;
            var addFileButton = that.$element.find(that.templateElements.add_file_button);
            var cancelButton = that.$element.find(that.templateElements.cancel_button);
            if(enable) {
                addFileButton.hide();
                cancelButton.show();
            } else {
                cancelButton.hide();
                addFileButton.show();
            }
        },
        cancelAll: function(){
            var that = this;
            $.each(that.jqXHRCollection, function(key, jqXHR){
                jqXHR.abort();
            });
            that.buttonToggler(false);
        },
        /**
         *
         * @param exception
         * @param error
         */
        throwException: function(exception, error){
            if(typeof Bugsnag != 'undefined') {
                Bugsnag.notify("S3Uploader", exception);
            }
            this.log(exception + ' exception:', error);
        },
        /**
         *
         * @param title
         * @param data
         */
        log: function(title, data) {
            if(this.options.debug) {
                console.log('S3Uploader: ' + title);
                console.log(data);
            }
        },
        /**
         *
         * @param el
         * @returns {Array}
         * @private
         */
        _parseHtmlDataAttributes: function(el) {
            var keys = [],
                elDataAttributes = el.data();
            for (var key in this.options) {
                if (this.options.hasOwnProperty(key) && elDataAttributes.hasOwnProperty(key)) {
                    keys[key] = elDataAttributes[key];
                }
            }
            return keys;
        }
    };

    var Template = {
        /**
         * @param multipleBool
         * @param customButtonName
         * @param showExtendedBool
         * @returns {string}
         */
        getTemplate: function(multipleBool, customButtonName, showExtendedBool){
            var name = 'file';
            var multiple = '';
            var buttonName = 'Add File';
            if(multipleBool) {
                name = 'file';
                multiple = ' multiple';
                buttonName = 'Add Files';
            }
            if(customButtonName) {
                buttonName = customButtonName;
            }
            var template = '' +
                '<div class="row">' +
                '    <div class="col-sm-4">' +
                '        <span class="btn btn-primary btn-sm fileinput-button">' +
                '            <i class="fa fa-plus"></i>&nbsp;' +
                             buttonName +
                '            <input type="file" name="' + name + '" class="js-s3_fileupload" ' + multiple + '>' +
                '        </span>' +
                '        <span class="btn btn-danger js-cancel_button btn-sm" style="display:none;">' +
                '            <i class="fa fa-spin fa-spinner"></i>' +
                '            Cancel' +
                '        </span>' +
                '    </div>' +
                '    <div class="col-sm-8">' +
                '        <div class="js-fileupload-progress fileupload-progress" style="display:none;">' +
                '            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">' +
                '                <div class="progress-bar progress-bar-success" style="width:0%;"></div>' +
                '            </div>';
            if(showExtendedBool) {
                template += '<div class="progress-extended">&nbsp;</div>';
            }
            template += '' +
                '        </div>' +
                '    </div>' +
                '</div>';
            return template;
        }
    };

    /**
     *
     * @param options
     * @returns {*}
     */
    $.fn.s3uploader = function(options){
        return this.each(function(){
            // console.log('new uploader init');
            return new S3Uploader(this, options);
        });
    };
    $.fn.s3uploader.defaults = {
        // required, can use $.fn.s3uploader.setDefaults({});
        s3_key_url: '',
        s3_bucket: '',
        // optional
        s3_key_payload: {},
        fileupload_options: {},
        multiple: false,
        debug: false,
        optional_s3_folder: '',
        video_tags: [],
        extended_upload_info: true,
        button_name: '',
        drop_zone: '',
        templateEl : null,
        maxChunkSize: 10000000,
        on_file_add: function (element, data) {},
        on_file_saved: function ($element, data) {},
        on_s3_upload: function (data) {}
    };
    $.fn.s3uploader.setDefaults = function(options){
        $.each(options, function(key, val){
            $.fn.s3uploader.defaults[ key ] = val;
        });
    };

})(jQuery, window, document);
/*! emojione 15-07-2016 */
!function(a){a.emojioneList={":kiss_ww:":{unicode:["1f469-200d-2764-fe0f-200d-1f48b-200d-1f469","1f469-2764-1f48b-1f469"],isCanonical:!0},":couplekiss_ww:":{unicode:["1f469-200d-2764-fe0f-200d-1f48b-200d-1f469","1f469-2764-1f48b-1f469"],isCanonical:!1},":kiss_mm:":{unicode:["1f468-200d-2764-fe0f-200d-1f48b-200d-1f468","1f468-2764-1f48b-1f468"],isCanonical:!0},":couplekiss_mm:":{unicode:["1f468-200d-2764-fe0f-200d-1f48b-200d-1f468","1f468-2764-1f48b-1f468"],isCanonical:!1},":family_mmbb:":{unicode:["1f468-200d-1f468-200d-1f466-200d-1f466","1f468-1f468-1f466-1f466"],isCanonical:!0},":family_mmgb:":{unicode:["1f468-200d-1f468-200d-1f467-200d-1f466","1f468-1f468-1f467-1f466"],isCanonical:!0},":family_mmgg:":{unicode:["1f468-200d-1f468-200d-1f467-200d-1f467","1f468-1f468-1f467-1f467"],isCanonical:!0},":family_mwbb:":{unicode:["1f468-200d-1f469-200d-1f466-200d-1f466","1f468-1f469-1f466-1f466"],isCanonical:!0},":family_mwgb:":{unicode:["1f468-200d-1f469-200d-1f467-200d-1f466","1f468-1f469-1f467-1f466"],isCanonical:!0},":family_mwgg:":{unicode:["1f468-200d-1f469-200d-1f467-200d-1f467","1f468-1f469-1f467-1f467"],isCanonical:!0},":family_wwbb:":{unicode:["1f469-200d-1f469-200d-1f466-200d-1f466","1f469-1f469-1f466-1f466"],isCanonical:!0},":family_wwgb:":{unicode:["1f469-200d-1f469-200d-1f467-200d-1f466","1f469-1f469-1f467-1f466"],isCanonical:!0},":family_wwgg:":{unicode:["1f469-200d-1f469-200d-1f467-200d-1f467","1f469-1f469-1f467-1f467"],isCanonical:!0},":couple_ww:":{unicode:["1f469-200d-2764-fe0f-200d-1f469","1f469-2764-1f469"],isCanonical:!0},":couple_with_heart_ww:":{unicode:["1f469-200d-2764-fe0f-200d-1f469","1f469-2764-1f469"],isCanonical:!1},":couple_mm:":{unicode:["1f468-200d-2764-fe0f-200d-1f468","1f468-2764-1f468"],isCanonical:!0},":couple_with_heart_mm:":{unicode:["1f468-200d-2764-fe0f-200d-1f468","1f468-2764-1f468"],isCanonical:!1},":family_mmb:":{unicode:["1f468-200d-1f468-200d-1f466","1f468-1f468-1f466"],isCanonical:!0},":family_mmg:":{unicode:["1f468-200d-1f468-200d-1f467","1f468-1f468-1f467"],isCanonical:!0},":family_mwg:":{unicode:["1f468-200d-1f469-200d-1f467","1f468-1f469-1f467"],isCanonical:!0},":family_wwb:":{unicode:["1f469-200d-1f469-200d-1f466","1f469-1f469-1f466"],isCanonical:!0},":family_wwg:":{unicode:["1f469-200d-1f469-200d-1f467","1f469-1f469-1f467"],isCanonical:!0},":eye_in_speech_bubble:":{unicode:["1f441-200d-1f5e8","1f441-1f5e8"],isCanonical:!0},":hash:":{unicode:["0023-fe0f-20e3","0023-20e3"],isCanonical:!0},":zero:":{unicode:["0030-fe0f-20e3","0030-20e3"],isCanonical:!0},":one:":{unicode:["0031-fe0f-20e3","0031-20e3"],isCanonical:!0},":two:":{unicode:["0032-fe0f-20e3","0032-20e3"],isCanonical:!0},":three:":{unicode:["0033-fe0f-20e3","0033-20e3"],isCanonical:!0},":four:":{unicode:["0034-fe0f-20e3","0034-20e3"],isCanonical:!0},":five:":{unicode:["0035-fe0f-20e3","0035-20e3"],isCanonical:!0},":six:":{unicode:["0036-fe0f-20e3","0036-20e3"],isCanonical:!0},":seven:":{unicode:["0037-fe0f-20e3","0037-20e3"],isCanonical:!0},":eight:":{unicode:["0038-fe0f-20e3","0038-20e3"],isCanonical:!0},":nine:":{unicode:["0039-fe0f-20e3","0039-20e3"],isCanonical:!0},":asterisk:":{unicode:["002a-fe0f-20e3","002a-20e3"],isCanonical:!0},":keycap_asterisk:":{unicode:["002a-fe0f-20e3","002a-20e3"],isCanonical:!1},":handball_tone5:":{unicode:["1f93e-1f3ff"],isCanonical:!0},":handball_tone4:":{unicode:["1f93e-1f3fe"],isCanonical:!0},":handball_tone3:":{unicode:["1f93e-1f3fd"],isCanonical:!0},":handball_tone2:":{unicode:["1f93e-1f3fc"],isCanonical:!0},":handball_tone1:":{unicode:["1f93e-1f3fb"],isCanonical:!0},":water_polo_tone5:":{unicode:["1f93d-1f3ff"],isCanonical:!0},":water_polo_tone4:":{unicode:["1f93d-1f3fe"],isCanonical:!0},":water_polo_tone3:":{unicode:["1f93d-1f3fd"],isCanonical:!0},":water_polo_tone2:":{unicode:["1f93d-1f3fc"],isCanonical:!0},":water_polo_tone1:":{unicode:["1f93d-1f3fb"],isCanonical:!0},":wrestlers_tone5:":{unicode:["1f93c-1f3ff"],isCanonical:!0},":wrestling_tone5:":{unicode:["1f93c-1f3ff"],isCanonical:!1},":wrestlers_tone4:":{unicode:["1f93c-1f3fe"],isCanonical:!0},":wrestling_tone4:":{unicode:["1f93c-1f3fe"],isCanonical:!1},":wrestlers_tone3:":{unicode:["1f93c-1f3fd"],isCanonical:!0},":wrestling_tone3:":{unicode:["1f93c-1f3fd"],isCanonical:!1},":wrestlers_tone2:":{unicode:["1f93c-1f3fc"],isCanonical:!0},":wrestling_tone2:":{unicode:["1f93c-1f3fc"],isCanonical:!1},":wrestlers_tone1:":{unicode:["1f93c-1f3fb"],isCanonical:!0},":wrestling_tone1:":{unicode:["1f93c-1f3fb"],isCanonical:!1},":juggling_tone5:":{unicode:["1f939-1f3ff"],isCanonical:!0},":juggler_tone5:":{unicode:["1f939-1f3ff"],isCanonical:!1},":juggling_tone4:":{unicode:["1f939-1f3fe"],isCanonical:!0},":juggler_tone4:":{unicode:["1f939-1f3fe"],isCanonical:!1},":juggling_tone3:":{unicode:["1f939-1f3fd"],isCanonical:!0},":juggler_tone3:":{unicode:["1f939-1f3fd"],isCanonical:!1},":juggling_tone2:":{unicode:["1f939-1f3fc"],isCanonical:!0},":juggler_tone2:":{unicode:["1f939-1f3fc"],isCanonical:!1},":juggling_tone1:":{unicode:["1f939-1f3fb"],isCanonical:!0},":juggler_tone1:":{unicode:["1f939-1f3fb"],isCanonical:!1},":cartwheel_tone5:":{unicode:["1f938-1f3ff"],isCanonical:!0},":person_doing_cartwheel_tone5:":{unicode:["1f938-1f3ff"],isCanonical:!1},":cartwheel_tone4:":{unicode:["1f938-1f3fe"],isCanonical:!0},":person_doing_cartwheel_tone4:":{unicode:["1f938-1f3fe"],isCanonical:!1},":cartwheel_tone3:":{unicode:["1f938-1f3fd"],isCanonical:!0},":person_doing_cartwheel_tone3:":{unicode:["1f938-1f3fd"],isCanonical:!1},":cartwheel_tone2:":{unicode:["1f938-1f3fc"],isCanonical:!0},":person_doing_cartwheel_tone2:":{unicode:["1f938-1f3fc"],isCanonical:!1},":cartwheel_tone1:":{unicode:["1f938-1f3fb"],isCanonical:!0},":person_doing_cartwheel_tone1:":{unicode:["1f938-1f3fb"],isCanonical:!1},":shrug_tone5:":{unicode:["1f937-1f3ff"],isCanonical:!0},":shrug_tone4:":{unicode:["1f937-1f3fe"],isCanonical:!0},":shrug_tone3:":{unicode:["1f937-1f3fd"],isCanonical:!0},":shrug_tone2:":{unicode:["1f937-1f3fc"],isCanonical:!0},":shrug_tone1:":{unicode:["1f937-1f3fb"],isCanonical:!0},":mrs_claus_tone5:":{unicode:["1f936-1f3ff"],isCanonical:!0},":mother_christmas_tone5:":{unicode:["1f936-1f3ff"],isCanonical:!1},":mrs_claus_tone4:":{unicode:["1f936-1f3fe"],isCanonical:!0},":mother_christmas_tone4:":{unicode:["1f936-1f3fe"],isCanonical:!1},":mrs_claus_tone3:":{unicode:["1f936-1f3fd"],isCanonical:!0},":mother_christmas_tone3:":{unicode:["1f936-1f3fd"],isCanonical:!1},":mrs_claus_tone2:":{unicode:["1f936-1f3fc"],isCanonical:!0},":mother_christmas_tone2:":{unicode:["1f936-1f3fc"],isCanonical:!1},":mrs_claus_tone1:":{unicode:["1f936-1f3fb"],isCanonical:!0},":mother_christmas_tone1:":{unicode:["1f936-1f3fb"],isCanonical:!1},":man_in_tuxedo_tone5:":{unicode:["1f935-1f3ff"],isCanonical:!0},":tuxedo_tone5:":{unicode:["1f935-1f3ff"],isCanonical:!1},":man_in_tuxedo_tone4:":{unicode:["1f935-1f3fe"],isCanonical:!0},":tuxedo_tone4:":{unicode:["1f935-1f3fe"],isCanonical:!1},":man_in_tuxedo_tone3:":{unicode:["1f935-1f3fd"],isCanonical:!0},":tuxedo_tone3:":{unicode:["1f935-1f3fd"],isCanonical:!1},":man_in_tuxedo_tone2:":{unicode:["1f935-1f3fc"],isCanonical:!0},":tuxedo_tone2:":{unicode:["1f935-1f3fc"],isCanonical:!1},":man_in_tuxedo_tone1:":{unicode:["1f935-1f3fb"],isCanonical:!0},":tuxedo_tone1:":{unicode:["1f935-1f3fb"],isCanonical:!1},":prince_tone5:":{unicode:["1f934-1f3ff"],isCanonical:!0},":prince_tone4:":{unicode:["1f934-1f3fe"],isCanonical:!0},":prince_tone3:":{unicode:["1f934-1f3fd"],isCanonical:!0},":prince_tone2:":{unicode:["1f934-1f3fc"],isCanonical:!0},":prince_tone1:":{unicode:["1f934-1f3fb"],isCanonical:!0},":selfie_tone5:":{unicode:["1f933-1f3ff"],isCanonical:!0},":selfie_tone4:":{unicode:["1f933-1f3fe"],isCanonical:!0},":selfie_tone3:":{unicode:["1f933-1f3fd"],isCanonical:!0},":selfie_tone2:":{unicode:["1f933-1f3fc"],isCanonical:!0},":selfie_tone1:":{unicode:["1f933-1f3fb"],isCanonical:!0},":pregnant_woman_tone5:":{unicode:["1f930-1f3ff"],isCanonical:!0},":expecting_woman_tone5:":{unicode:["1f930-1f3ff"],isCanonical:!1},":pregnant_woman_tone4:":{unicode:["1f930-1f3fe"],isCanonical:!0},":expecting_woman_tone4:":{unicode:["1f930-1f3fe"],isCanonical:!1},":pregnant_woman_tone3:":{unicode:["1f930-1f3fd"],isCanonical:!0},":expecting_woman_tone3:":{unicode:["1f930-1f3fd"],isCanonical:!1},":pregnant_woman_tone2:":{unicode:["1f930-1f3fc"],isCanonical:!0},":expecting_woman_tone2:":{unicode:["1f930-1f3fc"],isCanonical:!1},":pregnant_woman_tone1:":{unicode:["1f930-1f3fb"],isCanonical:!0},":expecting_woman_tone1:":{unicode:["1f930-1f3fb"],isCanonical:!1},":face_palm_tone5:":{unicode:["1f926-1f3ff"],isCanonical:!0},":facepalm_tone5:":{unicode:["1f926-1f3ff"],isCanonical:!1},":face_palm_tone4:":{unicode:["1f926-1f3fe"],isCanonical:!0},":facepalm_tone4:":{unicode:["1f926-1f3fe"],isCanonical:!1},":face_palm_tone3:":{unicode:["1f926-1f3fd"],isCanonical:!0},":facepalm_tone3:":{unicode:["1f926-1f3fd"],isCanonical:!1},":face_palm_tone2:":{unicode:["1f926-1f3fc"],isCanonical:!0},":facepalm_tone2:":{unicode:["1f926-1f3fc"],isCanonical:!1},":face_palm_tone1:":{unicode:["1f926-1f3fb"],isCanonical:!0},":facepalm_tone1:":{unicode:["1f926-1f3fb"],isCanonical:!1},":fingers_crossed_tone5:":{unicode:["1f91e-1f3ff"],isCanonical:!0},":hand_with_index_and_middle_fingers_crossed_tone5:":{unicode:["1f91e-1f3ff"],isCanonical:!1},":fingers_crossed_tone4:":{unicode:["1f91e-1f3fe"],isCanonical:!0},":hand_with_index_and_middle_fingers_crossed_tone4:":{unicode:["1f91e-1f3fe"],isCanonical:!1},":fingers_crossed_tone3:":{unicode:["1f91e-1f3fd"],isCanonical:!0},":hand_with_index_and_middle_fingers_crossed_tone3:":{unicode:["1f91e-1f3fd"],isCanonical:!1},":fingers_crossed_tone2:":{unicode:["1f91e-1f3fc"],isCanonical:!0},":hand_with_index_and_middle_fingers_crossed_tone2:":{unicode:["1f91e-1f3fc"],isCanonical:!1},":fingers_crossed_tone1:":{unicode:["1f91e-1f3fb"],isCanonical:!0},":hand_with_index_and_middle_fingers_crossed_tone1:":{unicode:["1f91e-1f3fb"],isCanonical:!1},":handshake_tone5:":{unicode:["1f91d-1f3ff"],isCanonical:!0},":shaking_hands_tone5:":{unicode:["1f91d-1f3ff"],isCanonical:!1},":handshake_tone4:":{unicode:["1f91d-1f3fe"],isCanonical:!0},":shaking_hands_tone4:":{unicode:["1f91d-1f3fe"],isCanonical:!1},":handshake_tone3:":{unicode:["1f91d-1f3fd"],isCanonical:!0},":shaking_hands_tone3:":{unicode:["1f91d-1f3fd"],isCanonical:!1},":handshake_tone2:":{unicode:["1f91d-1f3fc"],isCanonical:!0},":shaking_hands_tone2:":{unicode:["1f91d-1f3fc"],isCanonical:!1},":handshake_tone1:":{unicode:["1f91d-1f3fb"],isCanonical:!0},":shaking_hands_tone1:":{unicode:["1f91d-1f3fb"],isCanonical:!1},":right_facing_fist_tone5:":{unicode:["1f91c-1f3ff"],isCanonical:!0},":right_fist_tone5:":{unicode:["1f91c-1f3ff"],isCanonical:!1},":right_facing_fist_tone4:":{unicode:["1f91c-1f3fe"],isCanonical:!0},":right_fist_tone4:":{unicode:["1f91c-1f3fe"],isCanonical:!1},":right_facing_fist_tone3:":{unicode:["1f91c-1f3fd"],isCanonical:!0},":right_fist_tone3:":{unicode:["1f91c-1f3fd"],isCanonical:!1},":right_facing_fist_tone2:":{unicode:["1f91c-1f3fc"],isCanonical:!0},":right_fist_tone2:":{unicode:["1f91c-1f3fc"],isCanonical:!1},":right_facing_fist_tone1:":{unicode:["1f91c-1f3fb"],isCanonical:!0},":right_fist_tone1:":{unicode:["1f91c-1f3fb"],isCanonical:!1},":left_facing_fist_tone5:":{unicode:["1f91b-1f3ff"],isCanonical:!0},":left_fist_tone5:":{unicode:["1f91b-1f3ff"],isCanonical:!1},":left_facing_fist_tone4:":{unicode:["1f91b-1f3fe"],isCanonical:!0},":left_fist_tone4:":{unicode:["1f91b-1f3fe"],isCanonical:!1},":left_facing_fist_tone3:":{unicode:["1f91b-1f3fd"],isCanonical:!0},":left_fist_tone3:":{unicode:["1f91b-1f3fd"],isCanonical:!1},":left_facing_fist_tone2:":{unicode:["1f91b-1f3fc"],isCanonical:!0},":left_fist_tone2:":{unicode:["1f91b-1f3fc"],isCanonical:!1},":left_facing_fist_tone1:":{unicode:["1f91b-1f3fb"],isCanonical:!0},":left_fist_tone1:":{unicode:["1f91b-1f3fb"],isCanonical:!1},":raised_back_of_hand_tone5:":{unicode:["1f91a-1f3ff"],isCanonical:!0},":back_of_hand_tone5:":{unicode:["1f91a-1f3ff"],isCanonical:!1},":raised_back_of_hand_tone4:":{unicode:["1f91a-1f3fe"],isCanonical:!0},":back_of_hand_tone4:":{unicode:["1f91a-1f3fe"],isCanonical:!1},":raised_back_of_hand_tone3:":{unicode:["1f91a-1f3fd"],isCanonical:!0},":back_of_hand_tone3:":{unicode:["1f91a-1f3fd"],isCanonical:!1},":raised_back_of_hand_tone2:":{unicode:["1f91a-1f3fc"],isCanonical:!0},":back_of_hand_tone2:":{unicode:["1f91a-1f3fc"],isCanonical:!1},":raised_back_of_hand_tone1:":{unicode:["1f91a-1f3fb"],isCanonical:!0},":back_of_hand_tone1:":{unicode:["1f91a-1f3fb"],isCanonical:!1},":call_me_tone5:":{unicode:["1f919-1f3ff"],isCanonical:!0},":call_me_hand_tone5:":{unicode:["1f919-1f3ff"],isCanonical:!1},":call_me_tone4:":{unicode:["1f919-1f3fe"],isCanonical:!0},":call_me_hand_tone4:":{unicode:["1f919-1f3fe"],isCanonical:!1},":call_me_tone3:":{unicode:["1f919-1f3fd"],isCanonical:!0},":call_me_hand_tone3:":{unicode:["1f919-1f3fd"],isCanonical:!1},":call_me_tone2:":{unicode:["1f919-1f3fc"],isCanonical:!0},":call_me_hand_tone2:":{unicode:["1f919-1f3fc"],isCanonical:!1},":call_me_tone1:":{unicode:["1f919-1f3fb"],isCanonical:!0},":call_me_hand_tone1:":{unicode:["1f919-1f3fb"],isCanonical:!1},":metal_tone5:":{unicode:["1f918-1f3ff"],isCanonical:!0},":sign_of_the_horns_tone5:":{unicode:["1f918-1f3ff"],isCanonical:!1},":metal_tone4:":{unicode:["1f918-1f3fe"],isCanonical:!0},":sign_of_the_horns_tone4:":{unicode:["1f918-1f3fe"],isCanonical:!1},":metal_tone3:":{unicode:["1f918-1f3fd"],isCanonical:!0},":sign_of_the_horns_tone3:":{unicode:["1f918-1f3fd"],isCanonical:!1},":metal_tone2:":{unicode:["1f918-1f3fc"],isCanonical:!0},":sign_of_the_horns_tone2:":{unicode:["1f918-1f3fc"],isCanonical:!1},":metal_tone1:":{unicode:["1f918-1f3fb"],isCanonical:!0},":sign_of_the_horns_tone1:":{unicode:["1f918-1f3fb"],isCanonical:!1},":bath_tone5:":{unicode:["1f6c0-1f3ff"],isCanonical:!0},":bath_tone4:":{unicode:["1f6c0-1f3fe"],isCanonical:!0},":bath_tone3:":{unicode:["1f6c0-1f3fd"],isCanonical:!0},":bath_tone2:":{unicode:["1f6c0-1f3fc"],isCanonical:!0},":bath_tone1:":{unicode:["1f6c0-1f3fb"],isCanonical:!0},":walking_tone5:":{unicode:["1f6b6-1f3ff"],isCanonical:!0},":walking_tone4:":{unicode:["1f6b6-1f3fe"],isCanonical:!0},":walking_tone3:":{unicode:["1f6b6-1f3fd"],isCanonical:!0},":walking_tone2:":{unicode:["1f6b6-1f3fc"],isCanonical:!0},":walking_tone1:":{unicode:["1f6b6-1f3fb"],isCanonical:!0},":mountain_bicyclist_tone5:":{unicode:["1f6b5-1f3ff"],isCanonical:!0},":mountain_bicyclist_tone4:":{unicode:["1f6b5-1f3fe"],isCanonical:!0},":mountain_bicyclist_tone3:":{unicode:["1f6b5-1f3fd"],isCanonical:!0},":mountain_bicyclist_tone2:":{unicode:["1f6b5-1f3fc"],isCanonical:!0},":mountain_bicyclist_tone1:":{unicode:["1f6b5-1f3fb"],isCanonical:!0},":bicyclist_tone5:":{unicode:["1f6b4-1f3ff"],isCanonical:!0},":bicyclist_tone4:":{unicode:["1f6b4-1f3fe"],isCanonical:!0},":bicyclist_tone3:":{unicode:["1f6b4-1f3fd"],isCanonical:!0},":bicyclist_tone2:":{unicode:["1f6b4-1f3fc"],isCanonical:!0},":bicyclist_tone1:":{unicode:["1f6b4-1f3fb"],isCanonical:!0},":rowboat_tone5:":{unicode:["1f6a3-1f3ff"],isCanonical:!0},":rowboat_tone4:":{unicode:["1f6a3-1f3fe"],isCanonical:!0},":rowboat_tone3:":{unicode:["1f6a3-1f3fd"],isCanonical:!0},":rowboat_tone2:":{unicode:["1f6a3-1f3fc"],isCanonical:!0},":rowboat_tone1:":{unicode:["1f6a3-1f3fb"],isCanonical:!0},":pray_tone5:":{unicode:["1f64f-1f3ff"],isCanonical:!0},":pray_tone4:":{unicode:["1f64f-1f3fe"],isCanonical:!0},":pray_tone3:":{unicode:["1f64f-1f3fd"],isCanonical:!0},":pray_tone2:":{unicode:["1f64f-1f3fc"],isCanonical:!0},":pray_tone1:":{unicode:["1f64f-1f3fb"],isCanonical:!0},":person_with_pouting_face_tone5:":{unicode:["1f64e-1f3ff"],isCanonical:!0},":person_with_pouting_face_tone4:":{unicode:["1f64e-1f3fe"],isCanonical:!0},":person_with_pouting_face_tone3:":{unicode:["1f64e-1f3fd"],isCanonical:!0},":person_with_pouting_face_tone2:":{unicode:["1f64e-1f3fc"],isCanonical:!0},":person_with_pouting_face_tone1:":{unicode:["1f64e-1f3fb"],isCanonical:!0},":person_frowning_tone5:":{unicode:["1f64d-1f3ff"],isCanonical:!0},":person_frowning_tone4:":{unicode:["1f64d-1f3fe"],isCanonical:!0},":person_frowning_tone3:":{unicode:["1f64d-1f3fd"],isCanonical:!0},":person_frowning_tone2:":{unicode:["1f64d-1f3fc"],isCanonical:!0},":person_frowning_tone1:":{unicode:["1f64d-1f3fb"],isCanonical:!0},":raised_hands_tone5:":{unicode:["1f64c-1f3ff"],isCanonical:!0},":raised_hands_tone4:":{unicode:["1f64c-1f3fe"],isCanonical:!0},":raised_hands_tone3:":{unicode:["1f64c-1f3fd"],isCanonical:!0},":raised_hands_tone2:":{unicode:["1f64c-1f3fc"],isCanonical:!0},":raised_hands_tone1:":{unicode:["1f64c-1f3fb"],isCanonical:!0},":raising_hand_tone5:":{unicode:["1f64b-1f3ff"],isCanonical:!0},":raising_hand_tone4:":{unicode:["1f64b-1f3fe"],isCanonical:!0},":raising_hand_tone3:":{unicode:["1f64b-1f3fd"],isCanonical:!0},":raising_hand_tone2:":{unicode:["1f64b-1f3fc"],isCanonical:!0},":raising_hand_tone1:":{unicode:["1f64b-1f3fb"],isCanonical:!0},":bow_tone5:":{unicode:["1f647-1f3ff"],isCanonical:!0},":bow_tone4:":{unicode:["1f647-1f3fe"],isCanonical:!0},":bow_tone3:":{unicode:["1f647-1f3fd"],isCanonical:!0},":bow_tone2:":{unicode:["1f647-1f3fc"],isCanonical:!0},":bow_tone1:":{unicode:["1f647-1f3fb"],isCanonical:!0},":ok_woman_tone5:":{unicode:["1f646-1f3ff"],isCanonical:!0},":ok_woman_tone4:":{unicode:["1f646-1f3fe"],isCanonical:!0},":ok_woman_tone3:":{unicode:["1f646-1f3fd"],isCanonical:!0},":ok_woman_tone2:":{unicode:["1f646-1f3fc"],isCanonical:!0},":ok_woman_tone1:":{unicode:["1f646-1f3fb"],isCanonical:!0},":no_good_tone5:":{unicode:["1f645-1f3ff"],isCanonical:!0},":no_good_tone4:":{unicode:["1f645-1f3fe"],isCanonical:!0},":no_good_tone3:":{unicode:["1f645-1f3fd"],isCanonical:!0},":no_good_tone2:":{unicode:["1f645-1f3fc"],isCanonical:!0},":no_good_tone1:":{unicode:["1f645-1f3fb"],isCanonical:!0},":vulcan_tone5:":{unicode:["1f596-1f3ff"],isCanonical:!0},":raised_hand_with_part_between_middle_and_ring_fingers_tone5:":{unicode:["1f596-1f3ff"],isCanonical:!1},":vulcan_tone4:":{unicode:["1f596-1f3fe"],isCanonical:!0},":raised_hand_with_part_between_middle_and_ring_fingers_tone4:":{unicode:["1f596-1f3fe"],isCanonical:!1},":vulcan_tone3:":{unicode:["1f596-1f3fd"],isCanonical:!0},":raised_hand_with_part_between_middle_and_ring_fingers_tone3:":{unicode:["1f596-1f3fd"],isCanonical:!1},":vulcan_tone2:":{unicode:["1f596-1f3fc"],isCanonical:!0},":raised_hand_with_part_between_middle_and_ring_fingers_tone2:":{unicode:["1f596-1f3fc"],isCanonical:!1},":vulcan_tone1:":{unicode:["1f596-1f3fb"],isCanonical:!0},":raised_hand_with_part_between_middle_and_ring_fingers_tone1:":{unicode:["1f596-1f3fb"],isCanonical:!1},":middle_finger_tone5:":{unicode:["1f595-1f3ff"],isCanonical:!0},":reversed_hand_with_middle_finger_extended_tone5:":{unicode:["1f595-1f3ff"],isCanonical:!1},":middle_finger_tone4:":{unicode:["1f595-1f3fe"],isCanonical:!0},":reversed_hand_with_middle_finger_extended_tone4:":{unicode:["1f595-1f3fe"],isCanonical:!1},":middle_finger_tone3:":{unicode:["1f595-1f3fd"],isCanonical:!0},":reversed_hand_with_middle_finger_extended_tone3:":{unicode:["1f595-1f3fd"],isCanonical:!1},":middle_finger_tone2:":{unicode:["1f595-1f3fc"],isCanonical:!0},":reversed_hand_with_middle_finger_extended_tone2:":{unicode:["1f595-1f3fc"],isCanonical:!1},":middle_finger_tone1:":{unicode:["1f595-1f3fb"],isCanonical:!0},":reversed_hand_with_middle_finger_extended_tone1:":{unicode:["1f595-1f3fb"],isCanonical:!1},":hand_splayed_tone5:":{unicode:["1f590-1f3ff"],isCanonical:!0},":raised_hand_with_fingers_splayed_tone5:":{unicode:["1f590-1f3ff"],isCanonical:!1},":hand_splayed_tone4:":{unicode:["1f590-1f3fe"],isCanonical:!0},":raised_hand_with_fingers_splayed_tone4:":{unicode:["1f590-1f3fe"],isCanonical:!1},":hand_splayed_tone3:":{unicode:["1f590-1f3fd"],isCanonical:!0},":raised_hand_with_fingers_splayed_tone3:":{unicode:["1f590-1f3fd"],isCanonical:!1},":hand_splayed_tone2:":{unicode:["1f590-1f3fc"],isCanonical:!0},":raised_hand_with_fingers_splayed_tone2:":{unicode:["1f590-1f3fc"],isCanonical:!1},":hand_splayed_tone1:":{unicode:["1f590-1f3fb"],isCanonical:!0},":raised_hand_with_fingers_splayed_tone1:":{unicode:["1f590-1f3fb"],isCanonical:!1},":man_dancing_tone5:":{unicode:["1f57a-1f3ff"],isCanonical:!0},":male_dancer_tone5:":{unicode:["1f57a-1f3ff"],isCanonical:!1},":man_dancing_tone4:":{unicode:["1f57a-1f3fe"],isCanonical:!0},":male_dancer_tone4:":{unicode:["1f57a-1f3fe"],isCanonical:!1},":man_dancing_tone3:":{unicode:["1f57a-1f3fd"],isCanonical:!0},":male_dancer_tone3:":{unicode:["1f57a-1f3fd"],isCanonical:!1},":man_dancing_tone2:":{unicode:["1f57a-1f3fc"],isCanonical:!0},":male_dancer_tone2:":{unicode:["1f57a-1f3fc"],isCanonical:!1},":man_dancing_tone1:":{unicode:["1f57a-1f3fb"],isCanonical:!0},":male_dancer_tone1:":{unicode:["1f57a-1f3fb"],isCanonical:!1},":spy_tone5:":{unicode:["1f575-1f3ff"],isCanonical:!0},":sleuth_or_spy_tone5:":{unicode:["1f575-1f3ff"],isCanonical:!1},":spy_tone4:":{unicode:["1f575-1f3fe"],isCanonical:!0},":sleuth_or_spy_tone4:":{unicode:["1f575-1f3fe"],isCanonical:!1},":spy_tone3:":{unicode:["1f575-1f3fd"],isCanonical:!0},":sleuth_or_spy_tone3:":{unicode:["1f575-1f3fd"],isCanonical:!1},":spy_tone2:":{unicode:["1f575-1f3fc"],isCanonical:!0},":sleuth_or_spy_tone2:":{unicode:["1f575-1f3fc"],isCanonical:!1},":spy_tone1:":{unicode:["1f575-1f3fb"],isCanonical:!0},":sleuth_or_spy_tone1:":{unicode:["1f575-1f3fb"],isCanonical:!1},":muscle_tone5:":{unicode:["1f4aa-1f3ff"],isCanonical:!0},":muscle_tone4:":{unicode:["1f4aa-1f3fe"],isCanonical:!0},":muscle_tone3:":{unicode:["1f4aa-1f3fd"],isCanonical:!0},":muscle_tone2:":{unicode:["1f4aa-1f3fc"],isCanonical:!0},":muscle_tone1:":{unicode:["1f4aa-1f3fb"],isCanonical:!0},":haircut_tone5:":{unicode:["1f487-1f3ff"],isCanonical:!0},":haircut_tone4:":{unicode:["1f487-1f3fe"],isCanonical:!0},":haircut_tone3:":{unicode:["1f487-1f3fd"],isCanonical:!0},":haircut_tone2:":{unicode:["1f487-1f3fc"],isCanonical:!0},":haircut_tone1:":{unicode:["1f487-1f3fb"],isCanonical:!0},":massage_tone5:":{unicode:["1f486-1f3ff"],isCanonical:!0},":massage_tone4:":{unicode:["1f486-1f3fe"],isCanonical:!0},":massage_tone3:":{unicode:["1f486-1f3fd"],isCanonical:!0},":massage_tone2:":{unicode:["1f486-1f3fc"],isCanonical:!0},":massage_tone1:":{unicode:["1f486-1f3fb"],isCanonical:!0},":nail_care_tone5:":{unicode:["1f485-1f3ff"],isCanonical:!0},":nail_care_tone4:":{unicode:["1f485-1f3fe"],isCanonical:!0},":nail_care_tone3:":{unicode:["1f485-1f3fd"],isCanonical:!0},":nail_care_tone2:":{unicode:["1f485-1f3fc"],isCanonical:!0},":nail_care_tone1:":{unicode:["1f485-1f3fb"],isCanonical:!0},":dancer_tone5:":{unicode:["1f483-1f3ff"],isCanonical:!0},":dancer_tone4:":{unicode:["1f483-1f3fe"],isCanonical:!0},":dancer_tone3:":{unicode:["1f483-1f3fd"],isCanonical:!0},":dancer_tone2:":{unicode:["1f483-1f3fc"],isCanonical:!0},":dancer_tone1:":{unicode:["1f483-1f3fb"],isCanonical:!0},":guardsman_tone5:":{unicode:["1f482-1f3ff"],isCanonical:!0},":guardsman_tone4:":{unicode:["1f482-1f3fe"],isCanonical:!0},":guardsman_tone3:":{unicode:["1f482-1f3fd"],isCanonical:!0},":guardsman_tone2:":{unicode:["1f482-1f3fc"],isCanonical:!0},":guardsman_tone1:":{unicode:["1f482-1f3fb"],isCanonical:!0},":information_desk_person_tone5:":{unicode:["1f481-1f3ff"],isCanonical:!0},":information_desk_person_tone4:":{unicode:["1f481-1f3fe"],isCanonical:!0},":information_desk_person_tone3:":{unicode:["1f481-1f3fd"],isCanonical:!0},":information_desk_person_tone2:":{unicode:["1f481-1f3fc"],isCanonical:!0},":information_desk_person_tone1:":{unicode:["1f481-1f3fb"],isCanonical:!0},":angel_tone5:":{unicode:["1f47c-1f3ff"],isCanonical:!0},":angel_tone4:":{unicode:["1f47c-1f3fe"],isCanonical:!0},":angel_tone3:":{unicode:["1f47c-1f3fd"],isCanonical:!0},":angel_tone2:":{unicode:["1f47c-1f3fc"],isCanonical:!0},":angel_tone1:":{unicode:["1f47c-1f3fb"],isCanonical:!0},":princess_tone5:":{unicode:["1f478-1f3ff"],isCanonical:!0},":princess_tone4:":{unicode:["1f478-1f3fe"],isCanonical:!0},":princess_tone3:":{unicode:["1f478-1f3fd"],isCanonical:!0},":princess_tone2:":{unicode:["1f478-1f3fc"],isCanonical:!0},":princess_tone1:":{unicode:["1f478-1f3fb"],isCanonical:!0},":construction_worker_tone5:":{unicode:["1f477-1f3ff"],isCanonical:!0},":construction_worker_tone4:":{unicode:["1f477-1f3fe"],isCanonical:!0},":construction_worker_tone3:":{unicode:["1f477-1f3fd"],isCanonical:!0},":construction_worker_tone2:":{unicode:["1f477-1f3fc"],isCanonical:!0},":construction_worker_tone1:":{unicode:["1f477-1f3fb"],isCanonical:!0},":baby_tone5:":{unicode:["1f476-1f3ff"],isCanonical:!0},":baby_tone4:":{unicode:["1f476-1f3fe"],isCanonical:!0},":baby_tone3:":{unicode:["1f476-1f3fd"],isCanonical:!0},":baby_tone2:":{unicode:["1f476-1f3fc"],isCanonical:!0},":baby_tone1:":{unicode:["1f476-1f3fb"],isCanonical:!0},":older_woman_tone5:":{unicode:["1f475-1f3ff"],isCanonical:!0},":grandma_tone5:":{unicode:["1f475-1f3ff"],isCanonical:!1},":older_woman_tone4:":{unicode:["1f475-1f3fe"],isCanonical:!0},":grandma_tone4:":{unicode:["1f475-1f3fe"],isCanonical:!1},":older_woman_tone3:":{unicode:["1f475-1f3fd"],isCanonical:!0},":grandma_tone3:":{unicode:["1f475-1f3fd"],isCanonical:!1},":older_woman_tone2:":{unicode:["1f475-1f3fc"],isCanonical:!0},":grandma_tone2:":{unicode:["1f475-1f3fc"],isCanonical:!1},":older_woman_tone1:":{unicode:["1f475-1f3fb"],isCanonical:!0},":grandma_tone1:":{unicode:["1f475-1f3fb"],isCanonical:!1},":older_man_tone5:":{unicode:["1f474-1f3ff"],isCanonical:!0},":older_man_tone4:":{unicode:["1f474-1f3fe"],isCanonical:!0},":older_man_tone3:":{unicode:["1f474-1f3fd"],isCanonical:!0},":older_man_tone2:":{unicode:["1f474-1f3fc"],isCanonical:!0},":older_man_tone1:":{unicode:["1f474-1f3fb"],isCanonical:!0},":man_with_turban_tone5:":{unicode:["1f473-1f3ff"],isCanonical:!0},":man_with_turban_tone4:":{unicode:["1f473-1f3fe"],isCanonical:!0},":man_with_turban_tone3:":{unicode:["1f473-1f3fd"],isCanonical:!0},":man_with_turban_tone2:":{unicode:["1f473-1f3fc"],isCanonical:!0},":man_with_turban_tone1:":{unicode:["1f473-1f3fb"],isCanonical:!0},":man_with_gua_pi_mao_tone5:":{unicode:["1f472-1f3ff"],isCanonical:!0},":man_with_gua_pi_mao_tone4:":{unicode:["1f472-1f3fe"],isCanonical:!0},":man_with_gua_pi_mao_tone3:":{unicode:["1f472-1f3fd"],isCanonical:!0},":man_with_gua_pi_mao_tone2:":{unicode:["1f472-1f3fc"],isCanonical:!0},":man_with_gua_pi_mao_tone1:":{unicode:["1f472-1f3fb"],isCanonical:!0},":person_with_blond_hair_tone5:":{unicode:["1f471-1f3ff"],isCanonical:!0},":person_with_blond_hair_tone4:":{unicode:["1f471-1f3fe"],isCanonical:!0},":person_with_blond_hair_tone3:":{unicode:["1f471-1f3fd"],isCanonical:!0},":person_with_blond_hair_tone2:":{unicode:["1f471-1f3fc"],isCanonical:!0},":person_with_blond_hair_tone1:":{unicode:["1f471-1f3fb"],isCanonical:!0},":bride_with_veil_tone5:":{unicode:["1f470-1f3ff"],isCanonical:!0},":bride_with_veil_tone4:":{unicode:["1f470-1f3fe"],isCanonical:!0},":bride_with_veil_tone3:":{unicode:["1f470-1f3fd"],isCanonical:!0},":bride_with_veil_tone2:":{unicode:["1f470-1f3fc"],isCanonical:!0},":bride_with_veil_tone1:":{unicode:["1f470-1f3fb"],isCanonical:!0},":cop_tone5:":{unicode:["1f46e-1f3ff"],isCanonical:!0},":cop_tone4:":{unicode:["1f46e-1f3fe"],isCanonical:!0},":cop_tone3:":{unicode:["1f46e-1f3fd"],isCanonical:!0},":cop_tone2:":{unicode:["1f46e-1f3fc"],isCanonical:!0},":cop_tone1:":{unicode:["1f46e-1f3fb"],isCanonical:!0},":woman_tone5:":{unicode:["1f469-1f3ff"],isCanonical:!0},":woman_tone4:":{unicode:["1f469-1f3fe"],isCanonical:!0},":woman_tone3:":{unicode:["1f469-1f3fd"],isCanonical:!0},":woman_tone2:":{unicode:["1f469-1f3fc"],isCanonical:!0},":woman_tone1:":{unicode:["1f469-1f3fb"],isCanonical:!0},":man_tone5:":{unicode:["1f468-1f3ff"],isCanonical:!0},":man_tone4:":{unicode:["1f468-1f3fe"],isCanonical:!0},":man_tone3:":{unicode:["1f468-1f3fd"],isCanonical:!0},":man_tone2:":{unicode:["1f468-1f3fc"],isCanonical:!0},":man_tone1:":{unicode:["1f468-1f3fb"],isCanonical:!0},":girl_tone5:":{unicode:["1f467-1f3ff"],isCanonical:!0},":girl_tone4:":{unicode:["1f467-1f3fe"],isCanonical:!0},":girl_tone3:":{unicode:["1f467-1f3fd"],isCanonical:!0},":girl_tone2:":{unicode:["1f467-1f3fc"],isCanonical:!0},":girl_tone1:":{unicode:["1f467-1f3fb"],isCanonical:!0},":boy_tone5:":{unicode:["1f466-1f3ff"],isCanonical:!0},":boy_tone4:":{unicode:["1f466-1f3fe"],isCanonical:!0},":boy_tone3:":{unicode:["1f466-1f3fd"],isCanonical:!0},":boy_tone2:":{unicode:["1f466-1f3fc"],isCanonical:!0},":boy_tone1:":{unicode:["1f466-1f3fb"],isCanonical:!0},":open_hands_tone5:":{unicode:["1f450-1f3ff"],isCanonical:!0},":open_hands_tone4:":{unicode:["1f450-1f3fe"],isCanonical:!0},":open_hands_tone3:":{unicode:["1f450-1f3fd"],isCanonical:!0},":open_hands_tone2:":{unicode:["1f450-1f3fc"],isCanonical:!0},":open_hands_tone1:":{unicode:["1f450-1f3fb"],isCanonical:!0},":clap_tone5:":{unicode:["1f44f-1f3ff"],isCanonical:!0},":clap_tone4:":{unicode:["1f44f-1f3fe"],isCanonical:!0},":clap_tone3:":{unicode:["1f44f-1f3fd"],isCanonical:!0},":clap_tone2:":{unicode:["1f44f-1f3fc"],isCanonical:!0},":clap_tone1:":{unicode:["1f44f-1f3fb"],isCanonical:!0},":thumbsdown_tone5:":{unicode:["1f44e-1f3ff"],isCanonical:!0},":-1_tone5:":{unicode:["1f44e-1f3ff"],isCanonical:!1},":thumbdown_tone5:":{unicode:["1f44e-1f3ff"],isCanonical:!1},":thumbsdown_tone4:":{unicode:["1f44e-1f3fe"],isCanonical:!0},":-1_tone4:":{unicode:["1f44e-1f3fe"],isCanonical:!1},":thumbdown_tone4:":{unicode:["1f44e-1f3fe"],isCanonical:!1},":thumbsdown_tone3:":{unicode:["1f44e-1f3fd"],isCanonical:!0},":-1_tone3:":{unicode:["1f44e-1f3fd"],isCanonical:!1},":thumbdown_tone3:":{unicode:["1f44e-1f3fd"],isCanonical:!1},":thumbsdown_tone2:":{unicode:["1f44e-1f3fc"],isCanonical:!0},":-1_tone2:":{unicode:["1f44e-1f3fc"],isCanonical:!1},":thumbdown_tone2:":{unicode:["1f44e-1f3fc"],isCanonical:!1},":thumbsdown_tone1:":{unicode:["1f44e-1f3fb"],isCanonical:!0},":-1_tone1:":{unicode:["1f44e-1f3fb"],isCanonical:!1},":thumbdown_tone1:":{unicode:["1f44e-1f3fb"],isCanonical:!1},":thumbsup_tone5:":{unicode:["1f44d-1f3ff"],isCanonical:!0},":+1_tone5:":{unicode:["1f44d-1f3ff"],isCanonical:!1},":thumbup_tone5:":{unicode:["1f44d-1f3ff"],isCanonical:!1},":thumbsup_tone4:":{unicode:["1f44d-1f3fe"],isCanonical:!0},":+1_tone4:":{unicode:["1f44d-1f3fe"],isCanonical:!1},":thumbup_tone4:":{unicode:["1f44d-1f3fe"],isCanonical:!1},":thumbsup_tone3:":{unicode:["1f44d-1f3fd"],isCanonical:!0},":+1_tone3:":{unicode:["1f44d-1f3fd"],isCanonical:!1},":thumbup_tone3:":{unicode:["1f44d-1f3fd"],isCanonical:!1},":thumbsup_tone2:":{unicode:["1f44d-1f3fc"],isCanonical:!0},":+1_tone2:":{unicode:["1f44d-1f3fc"],isCanonical:!1},":thumbup_tone2:":{unicode:["1f44d-1f3fc"],isCanonical:!1},":thumbsup_tone1:":{unicode:["1f44d-1f3fb"],isCanonical:!0},":+1_tone1:":{unicode:["1f44d-1f3fb"],isCanonical:!1},":thumbup_tone1:":{unicode:["1f44d-1f3fb"],isCanonical:!1},":ok_hand_tone5:":{unicode:["1f44c-1f3ff"],isCanonical:!0},":ok_hand_tone4:":{unicode:["1f44c-1f3fe"],isCanonical:!0},":ok_hand_tone3:":{unicode:["1f44c-1f3fd"],isCanonical:!0},":ok_hand_tone2:":{unicode:["1f44c-1f3fc"],isCanonical:!0},":ok_hand_tone1:":{unicode:["1f44c-1f3fb"],isCanonical:!0},":wave_tone5:":{unicode:["1f44b-1f3ff"],isCanonical:!0},":wave_tone4:":{unicode:["1f44b-1f3fe"],isCanonical:!0},":wave_tone3:":{unicode:["1f44b-1f3fd"],isCanonical:!0},":wave_tone2:":{unicode:["1f44b-1f3fc"],isCanonical:!0},":wave_tone1:":{unicode:["1f44b-1f3fb"],isCanonical:!0},":punch_tone5:":{unicode:["1f44a-1f3ff"],isCanonical:!0},":punch_tone4:":{unicode:["1f44a-1f3fe"],isCanonical:!0},":punch_tone3:":{unicode:["1f44a-1f3fd"],isCanonical:!0},":punch_tone2:":{unicode:["1f44a-1f3fc"],isCanonical:!0},":punch_tone1:":{unicode:["1f44a-1f3fb"],isCanonical:!0},":point_right_tone5:":{unicode:["1f449-1f3ff"],isCanonical:!0},":point_right_tone4:":{unicode:["1f449-1f3fe"],isCanonical:!0},":point_right_tone3:":{unicode:["1f449-1f3fd"],isCanonical:!0},":point_right_tone2:":{unicode:["1f449-1f3fc"],isCanonical:!0},":point_right_tone1:":{unicode:["1f449-1f3fb"],isCanonical:!0
},":point_left_tone5:":{unicode:["1f448-1f3ff"],isCanonical:!0},":point_left_tone4:":{unicode:["1f448-1f3fe"],isCanonical:!0},":point_left_tone3:":{unicode:["1f448-1f3fd"],isCanonical:!0},":point_left_tone2:":{unicode:["1f448-1f3fc"],isCanonical:!0},":point_left_tone1:":{unicode:["1f448-1f3fb"],isCanonical:!0},":point_down_tone5:":{unicode:["1f447-1f3ff"],isCanonical:!0},":point_down_tone4:":{unicode:["1f447-1f3fe"],isCanonical:!0},":point_down_tone3:":{unicode:["1f447-1f3fd"],isCanonical:!0},":point_down_tone2:":{unicode:["1f447-1f3fc"],isCanonical:!0},":point_down_tone1:":{unicode:["1f447-1f3fb"],isCanonical:!0},":point_up_2_tone5:":{unicode:["1f446-1f3ff"],isCanonical:!0},":point_up_2_tone4:":{unicode:["1f446-1f3fe"],isCanonical:!0},":point_up_2_tone3:":{unicode:["1f446-1f3fd"],isCanonical:!0},":point_up_2_tone2:":{unicode:["1f446-1f3fc"],isCanonical:!0},":point_up_2_tone1:":{unicode:["1f446-1f3fb"],isCanonical:!0},":nose_tone5:":{unicode:["1f443-1f3ff"],isCanonical:!0},":nose_tone4:":{unicode:["1f443-1f3fe"],isCanonical:!0},":nose_tone3:":{unicode:["1f443-1f3fd"],isCanonical:!0},":nose_tone2:":{unicode:["1f443-1f3fc"],isCanonical:!0},":nose_tone1:":{unicode:["1f443-1f3fb"],isCanonical:!0},":ear_tone5:":{unicode:["1f442-1f3ff"],isCanonical:!0},":ear_tone4:":{unicode:["1f442-1f3fe"],isCanonical:!0},":ear_tone3:":{unicode:["1f442-1f3fd"],isCanonical:!0},":ear_tone2:":{unicode:["1f442-1f3fc"],isCanonical:!0},":ear_tone1:":{unicode:["1f442-1f3fb"],isCanonical:!0},":gay_pride_flag:":{unicode:["1f3f3-1f308"],isCanonical:!0},":rainbow_flag:":{unicode:["1f3f3-1f308"],isCanonical:!1},":lifter_tone5:":{unicode:["1f3cb-1f3ff"],isCanonical:!0},":weight_lifter_tone5:":{unicode:["1f3cb-1f3ff"],isCanonical:!1},":lifter_tone4:":{unicode:["1f3cb-1f3fe"],isCanonical:!0},":weight_lifter_tone4:":{unicode:["1f3cb-1f3fe"],isCanonical:!1},":lifter_tone3:":{unicode:["1f3cb-1f3fd"],isCanonical:!0},":weight_lifter_tone3:":{unicode:["1f3cb-1f3fd"],isCanonical:!1},":lifter_tone2:":{unicode:["1f3cb-1f3fc"],isCanonical:!0},":weight_lifter_tone2:":{unicode:["1f3cb-1f3fc"],isCanonical:!1},":lifter_tone1:":{unicode:["1f3cb-1f3fb"],isCanonical:!0},":weight_lifter_tone1:":{unicode:["1f3cb-1f3fb"],isCanonical:!1},":swimmer_tone5:":{unicode:["1f3ca-1f3ff"],isCanonical:!0},":swimmer_tone4:":{unicode:["1f3ca-1f3fe"],isCanonical:!0},":swimmer_tone3:":{unicode:["1f3ca-1f3fd"],isCanonical:!0},":swimmer_tone2:":{unicode:["1f3ca-1f3fc"],isCanonical:!0},":swimmer_tone1:":{unicode:["1f3ca-1f3fb"],isCanonical:!0},":horse_racing_tone5:":{unicode:["1f3c7-1f3ff"],isCanonical:!0},":horse_racing_tone4:":{unicode:["1f3c7-1f3fe"],isCanonical:!0},":horse_racing_tone3:":{unicode:["1f3c7-1f3fd"],isCanonical:!0},":horse_racing_tone2:":{unicode:["1f3c7-1f3fc"],isCanonical:!0},":horse_racing_tone1:":{unicode:["1f3c7-1f3fb"],isCanonical:!0},":surfer_tone5:":{unicode:["1f3c4-1f3ff"],isCanonical:!0},":surfer_tone4:":{unicode:["1f3c4-1f3fe"],isCanonical:!0},":surfer_tone3:":{unicode:["1f3c4-1f3fd"],isCanonical:!0},":surfer_tone2:":{unicode:["1f3c4-1f3fc"],isCanonical:!0},":surfer_tone1:":{unicode:["1f3c4-1f3fb"],isCanonical:!0},":runner_tone5:":{unicode:["1f3c3-1f3ff"],isCanonical:!0},":runner_tone4:":{unicode:["1f3c3-1f3fe"],isCanonical:!0},":runner_tone3:":{unicode:["1f3c3-1f3fd"],isCanonical:!0},":runner_tone2:":{unicode:["1f3c3-1f3fc"],isCanonical:!0},":runner_tone1:":{unicode:["1f3c3-1f3fb"],isCanonical:!0},":santa_tone5:":{unicode:["1f385-1f3ff"],isCanonical:!0},":santa_tone4:":{unicode:["1f385-1f3fe"],isCanonical:!0},":santa_tone3:":{unicode:["1f385-1f3fd"],isCanonical:!0},":santa_tone2:":{unicode:["1f385-1f3fc"],isCanonical:!0},":santa_tone1:":{unicode:["1f385-1f3fb"],isCanonical:!0},":flag_zw:":{unicode:["1f1ff-1f1fc"],isCanonical:!0},":zw:":{unicode:["1f1ff-1f1fc"],isCanonical:!1},":flag_zm:":{unicode:["1f1ff-1f1f2"],isCanonical:!0},":zm:":{unicode:["1f1ff-1f1f2"],isCanonical:!1},":flag_za:":{unicode:["1f1ff-1f1e6"],isCanonical:!0},":za:":{unicode:["1f1ff-1f1e6"],isCanonical:!1},":flag_yt:":{unicode:["1f1fe-1f1f9"],isCanonical:!0},":yt:":{unicode:["1f1fe-1f1f9"],isCanonical:!1},":flag_ye:":{unicode:["1f1fe-1f1ea"],isCanonical:!0},":ye:":{unicode:["1f1fe-1f1ea"],isCanonical:!1},":flag_xk:":{unicode:["1f1fd-1f1f0"],isCanonical:!0},":xk:":{unicode:["1f1fd-1f1f0"],isCanonical:!1},":flag_ws:":{unicode:["1f1fc-1f1f8"],isCanonical:!0},":ws:":{unicode:["1f1fc-1f1f8"],isCanonical:!1},":flag_wf:":{unicode:["1f1fc-1f1eb"],isCanonical:!0},":wf:":{unicode:["1f1fc-1f1eb"],isCanonical:!1},":flag_vu:":{unicode:["1f1fb-1f1fa"],isCanonical:!0},":vu:":{unicode:["1f1fb-1f1fa"],isCanonical:!1},":flag_vn:":{unicode:["1f1fb-1f1f3"],isCanonical:!0},":vn:":{unicode:["1f1fb-1f1f3"],isCanonical:!1},":flag_vi:":{unicode:["1f1fb-1f1ee"],isCanonical:!0},":vi:":{unicode:["1f1fb-1f1ee"],isCanonical:!1},":flag_vg:":{unicode:["1f1fb-1f1ec"],isCanonical:!0},":vg:":{unicode:["1f1fb-1f1ec"],isCanonical:!1},":flag_ve:":{unicode:["1f1fb-1f1ea"],isCanonical:!0},":ve:":{unicode:["1f1fb-1f1ea"],isCanonical:!1},":flag_vc:":{unicode:["1f1fb-1f1e8"],isCanonical:!0},":vc:":{unicode:["1f1fb-1f1e8"],isCanonical:!1},":flag_va:":{unicode:["1f1fb-1f1e6"],isCanonical:!0},":va:":{unicode:["1f1fb-1f1e6"],isCanonical:!1},":flag_uz:":{unicode:["1f1fa-1f1ff"],isCanonical:!0},":uz:":{unicode:["1f1fa-1f1ff"],isCanonical:!1},":flag_uy:":{unicode:["1f1fa-1f1fe"],isCanonical:!0},":uy:":{unicode:["1f1fa-1f1fe"],isCanonical:!1},":flag_us:":{unicode:["1f1fa-1f1f8"],isCanonical:!0},":us:":{unicode:["1f1fa-1f1f8"],isCanonical:!1},":flag_um:":{unicode:["1f1fa-1f1f2"],isCanonical:!0},":um:":{unicode:["1f1fa-1f1f2"],isCanonical:!1},":flag_ug:":{unicode:["1f1fa-1f1ec"],isCanonical:!0},":ug:":{unicode:["1f1fa-1f1ec"],isCanonical:!1},":flag_ua:":{unicode:["1f1fa-1f1e6"],isCanonical:!0},":ua:":{unicode:["1f1fa-1f1e6"],isCanonical:!1},":flag_tz:":{unicode:["1f1f9-1f1ff"],isCanonical:!0},":tz:":{unicode:["1f1f9-1f1ff"],isCanonical:!1},":flag_tw:":{unicode:["1f1f9-1f1fc"],isCanonical:!0},":tw:":{unicode:["1f1f9-1f1fc"],isCanonical:!1},":flag_tv:":{unicode:["1f1f9-1f1fb"],isCanonical:!0},":tuvalu:":{unicode:["1f1f9-1f1fb"],isCanonical:!1},":flag_tt:":{unicode:["1f1f9-1f1f9"],isCanonical:!0},":tt:":{unicode:["1f1f9-1f1f9"],isCanonical:!1},":flag_tr:":{unicode:["1f1f9-1f1f7"],isCanonical:!0},":tr:":{unicode:["1f1f9-1f1f7"],isCanonical:!1},":flag_to:":{unicode:["1f1f9-1f1f4"],isCanonical:!0},":to:":{unicode:["1f1f9-1f1f4"],isCanonical:!1},":flag_tn:":{unicode:["1f1f9-1f1f3"],isCanonical:!0},":tn:":{unicode:["1f1f9-1f1f3"],isCanonical:!1},":flag_tm:":{unicode:["1f1f9-1f1f2"],isCanonical:!0},":turkmenistan:":{unicode:["1f1f9-1f1f2"],isCanonical:!1},":flag_tl:":{unicode:["1f1f9-1f1f1"],isCanonical:!0},":tl:":{unicode:["1f1f9-1f1f1"],isCanonical:!1},":flag_tk:":{unicode:["1f1f9-1f1f0"],isCanonical:!0},":tk:":{unicode:["1f1f9-1f1f0"],isCanonical:!1},":flag_tj:":{unicode:["1f1f9-1f1ef"],isCanonical:!0},":tj:":{unicode:["1f1f9-1f1ef"],isCanonical:!1},":flag_th:":{unicode:["1f1f9-1f1ed"],isCanonical:!0},":th:":{unicode:["1f1f9-1f1ed"],isCanonical:!1},":flag_tg:":{unicode:["1f1f9-1f1ec"],isCanonical:!0},":tg:":{unicode:["1f1f9-1f1ec"],isCanonical:!1},":flag_tf:":{unicode:["1f1f9-1f1eb"],isCanonical:!0},":tf:":{unicode:["1f1f9-1f1eb"],isCanonical:!1},":flag_td:":{unicode:["1f1f9-1f1e9"],isCanonical:!0},":td:":{unicode:["1f1f9-1f1e9"],isCanonical:!1},":flag_tc:":{unicode:["1f1f9-1f1e8"],isCanonical:!0},":tc:":{unicode:["1f1f9-1f1e8"],isCanonical:!1},":flag_ta:":{unicode:["1f1f9-1f1e6"],isCanonical:!0},":ta:":{unicode:["1f1f9-1f1e6"],isCanonical:!1},":flag_sz:":{unicode:["1f1f8-1f1ff"],isCanonical:!0},":sz:":{unicode:["1f1f8-1f1ff"],isCanonical:!1},":flag_sy:":{unicode:["1f1f8-1f1fe"],isCanonical:!0},":sy:":{unicode:["1f1f8-1f1fe"],isCanonical:!1},":flag_sx:":{unicode:["1f1f8-1f1fd"],isCanonical:!0},":sx:":{unicode:["1f1f8-1f1fd"],isCanonical:!1},":flag_sv:":{unicode:["1f1f8-1f1fb"],isCanonical:!0},":sv:":{unicode:["1f1f8-1f1fb"],isCanonical:!1},":flag_st:":{unicode:["1f1f8-1f1f9"],isCanonical:!0},":st:":{unicode:["1f1f8-1f1f9"],isCanonical:!1},":flag_ss:":{unicode:["1f1f8-1f1f8"],isCanonical:!0},":ss:":{unicode:["1f1f8-1f1f8"],isCanonical:!1},":flag_sr:":{unicode:["1f1f8-1f1f7"],isCanonical:!0},":sr:":{unicode:["1f1f8-1f1f7"],isCanonical:!1},":flag_so:":{unicode:["1f1f8-1f1f4"],isCanonical:!0},":so:":{unicode:["1f1f8-1f1f4"],isCanonical:!1},":flag_sn:":{unicode:["1f1f8-1f1f3"],isCanonical:!0},":sn:":{unicode:["1f1f8-1f1f3"],isCanonical:!1},":flag_sm:":{unicode:["1f1f8-1f1f2"],isCanonical:!0},":sm:":{unicode:["1f1f8-1f1f2"],isCanonical:!1},":flag_sl:":{unicode:["1f1f8-1f1f1"],isCanonical:!0},":sl:":{unicode:["1f1f8-1f1f1"],isCanonical:!1},":flag_sk:":{unicode:["1f1f8-1f1f0"],isCanonical:!0},":sk:":{unicode:["1f1f8-1f1f0"],isCanonical:!1},":flag_sj:":{unicode:["1f1f8-1f1ef"],isCanonical:!0},":sj:":{unicode:["1f1f8-1f1ef"],isCanonical:!1},":flag_si:":{unicode:["1f1f8-1f1ee"],isCanonical:!0},":si:":{unicode:["1f1f8-1f1ee"],isCanonical:!1},":flag_sh:":{unicode:["1f1f8-1f1ed"],isCanonical:!0},":sh:":{unicode:["1f1f8-1f1ed"],isCanonical:!1},":flag_sg:":{unicode:["1f1f8-1f1ec"],isCanonical:!0},":sg:":{unicode:["1f1f8-1f1ec"],isCanonical:!1},":flag_se:":{unicode:["1f1f8-1f1ea"],isCanonical:!0},":se:":{unicode:["1f1f8-1f1ea"],isCanonical:!1},":flag_sd:":{unicode:["1f1f8-1f1e9"],isCanonical:!0},":sd:":{unicode:["1f1f8-1f1e9"],isCanonical:!1},":flag_sc:":{unicode:["1f1f8-1f1e8"],isCanonical:!0},":sc:":{unicode:["1f1f8-1f1e8"],isCanonical:!1},":flag_sb:":{unicode:["1f1f8-1f1e7"],isCanonical:!0},":sb:":{unicode:["1f1f8-1f1e7"],isCanonical:!1},":flag_sa:":{unicode:["1f1f8-1f1e6"],isCanonical:!0},":saudiarabia:":{unicode:["1f1f8-1f1e6"],isCanonical:!1},":saudi:":{unicode:["1f1f8-1f1e6"],isCanonical:!1},":flag_rw:":{unicode:["1f1f7-1f1fc"],isCanonical:!0},":rw:":{unicode:["1f1f7-1f1fc"],isCanonical:!1},":flag_ru:":{unicode:["1f1f7-1f1fa"],isCanonical:!0},":ru:":{unicode:["1f1f7-1f1fa"],isCanonical:!1},":flag_rs:":{unicode:["1f1f7-1f1f8"],isCanonical:!0},":rs:":{unicode:["1f1f7-1f1f8"],isCanonical:!1},":flag_ro:":{unicode:["1f1f7-1f1f4"],isCanonical:!0},":ro:":{unicode:["1f1f7-1f1f4"],isCanonical:!1},":flag_re:":{unicode:["1f1f7-1f1ea"],isCanonical:!0},":re:":{unicode:["1f1f7-1f1ea"],isCanonical:!1},":flag_qa:":{unicode:["1f1f6-1f1e6"],isCanonical:!0},":qa:":{unicode:["1f1f6-1f1e6"],isCanonical:!1},":flag_py:":{unicode:["1f1f5-1f1fe"],isCanonical:!0},":py:":{unicode:["1f1f5-1f1fe"],isCanonical:!1},":flag_pw:":{unicode:["1f1f5-1f1fc"],isCanonical:!0},":pw:":{unicode:["1f1f5-1f1fc"],isCanonical:!1},":flag_pt:":{unicode:["1f1f5-1f1f9"],isCanonical:!0},":pt:":{unicode:["1f1f5-1f1f9"],isCanonical:!1},":flag_ps:":{unicode:["1f1f5-1f1f8"],isCanonical:!0},":ps:":{unicode:["1f1f5-1f1f8"],isCanonical:!1},":flag_pr:":{unicode:["1f1f5-1f1f7"],isCanonical:!0},":pr:":{unicode:["1f1f5-1f1f7"],isCanonical:!1},":flag_pn:":{unicode:["1f1f5-1f1f3"],isCanonical:!0},":pn:":{unicode:["1f1f5-1f1f3"],isCanonical:!1},":flag_pm:":{unicode:["1f1f5-1f1f2"],isCanonical:!0},":pm:":{unicode:["1f1f5-1f1f2"],isCanonical:!1},":flag_pl:":{unicode:["1f1f5-1f1f1"],isCanonical:!0},":pl:":{unicode:["1f1f5-1f1f1"],isCanonical:!1},":flag_pk:":{unicode:["1f1f5-1f1f0"],isCanonical:!0},":pk:":{unicode:["1f1f5-1f1f0"],isCanonical:!1},":flag_ph:":{unicode:["1f1f5-1f1ed"],isCanonical:!0},":ph:":{unicode:["1f1f5-1f1ed"],isCanonical:!1},":flag_pg:":{unicode:["1f1f5-1f1ec"],isCanonical:!0},":pg:":{unicode:["1f1f5-1f1ec"],isCanonical:!1},":flag_pf:":{unicode:["1f1f5-1f1eb"],isCanonical:!0},":pf:":{unicode:["1f1f5-1f1eb"],isCanonical:!1},":flag_pe:":{unicode:["1f1f5-1f1ea"],isCanonical:!0},":pe:":{unicode:["1f1f5-1f1ea"],isCanonical:!1},":flag_pa:":{unicode:["1f1f5-1f1e6"],isCanonical:!0},":pa:":{unicode:["1f1f5-1f1e6"],isCanonical:!1},":flag_om:":{unicode:["1f1f4-1f1f2"],isCanonical:!0},":om:":{unicode:["1f1f4-1f1f2"],isCanonical:!1},":flag_nz:":{unicode:["1f1f3-1f1ff"],isCanonical:!0},":nz:":{unicode:["1f1f3-1f1ff"],isCanonical:!1},":flag_nu:":{unicode:["1f1f3-1f1fa"],isCanonical:!0},":nu:":{unicode:["1f1f3-1f1fa"],isCanonical:!1},":flag_nr:":{unicode:["1f1f3-1f1f7"],isCanonical:!0},":nr:":{unicode:["1f1f3-1f1f7"],isCanonical:!1},":flag_np:":{unicode:["1f1f3-1f1f5"],isCanonical:!0},":np:":{unicode:["1f1f3-1f1f5"],isCanonical:!1},":flag_no:":{unicode:["1f1f3-1f1f4"],isCanonical:!0},":no:":{unicode:["1f1f3-1f1f4"],isCanonical:!1},":flag_nl:":{unicode:["1f1f3-1f1f1"],isCanonical:!0},":nl:":{unicode:["1f1f3-1f1f1"],isCanonical:!1},":flag_ni:":{unicode:["1f1f3-1f1ee"],isCanonical:!0},":ni:":{unicode:["1f1f3-1f1ee"],isCanonical:!1},":flag_ng:":{unicode:["1f1f3-1f1ec"],isCanonical:!0},":nigeria:":{unicode:["1f1f3-1f1ec"],isCanonical:!1},":flag_nf:":{unicode:["1f1f3-1f1eb"],isCanonical:!0},":nf:":{unicode:["1f1f3-1f1eb"],isCanonical:!1},":flag_ne:":{unicode:["1f1f3-1f1ea"],isCanonical:!0},":ne:":{unicode:["1f1f3-1f1ea"],isCanonical:!1},":flag_nc:":{unicode:["1f1f3-1f1e8"],isCanonical:!0},":nc:":{unicode:["1f1f3-1f1e8"],isCanonical:!1},":flag_na:":{unicode:["1f1f3-1f1e6"],isCanonical:!0},":na:":{unicode:["1f1f3-1f1e6"],isCanonical:!1},":flag_mz:":{unicode:["1f1f2-1f1ff"],isCanonical:!0},":mz:":{unicode:["1f1f2-1f1ff"],isCanonical:!1},":flag_my:":{unicode:["1f1f2-1f1fe"],isCanonical:!0},":my:":{unicode:["1f1f2-1f1fe"],isCanonical:!1},":flag_mx:":{unicode:["1f1f2-1f1fd"],isCanonical:!0},":mx:":{unicode:["1f1f2-1f1fd"],isCanonical:!1},":flag_mw:":{unicode:["1f1f2-1f1fc"],isCanonical:!0},":mw:":{unicode:["1f1f2-1f1fc"],isCanonical:!1},":flag_mv:":{unicode:["1f1f2-1f1fb"],isCanonical:!0},":mv:":{unicode:["1f1f2-1f1fb"],isCanonical:!1},":flag_mu:":{unicode:["1f1f2-1f1fa"],isCanonical:!0},":mu:":{unicode:["1f1f2-1f1fa"],isCanonical:!1},":flag_mt:":{unicode:["1f1f2-1f1f9"],isCanonical:!0},":mt:":{unicode:["1f1f2-1f1f9"],isCanonical:!1},":flag_ms:":{unicode:["1f1f2-1f1f8"],isCanonical:!0},":ms:":{unicode:["1f1f2-1f1f8"],isCanonical:!1},":flag_mr:":{unicode:["1f1f2-1f1f7"],isCanonical:!0},":mr:":{unicode:["1f1f2-1f1f7"],isCanonical:!1},":flag_mq:":{unicode:["1f1f2-1f1f6"],isCanonical:!0},":mq:":{unicode:["1f1f2-1f1f6"],isCanonical:!1},":flag_mp:":{unicode:["1f1f2-1f1f5"],isCanonical:!0},":mp:":{unicode:["1f1f2-1f1f5"],isCanonical:!1},":flag_mo:":{unicode:["1f1f2-1f1f4"],isCanonical:!0},":mo:":{unicode:["1f1f2-1f1f4"],isCanonical:!1},":flag_mn:":{unicode:["1f1f2-1f1f3"],isCanonical:!0},":mn:":{unicode:["1f1f2-1f1f3"],isCanonical:!1},":flag_mm:":{unicode:["1f1f2-1f1f2"],isCanonical:!0},":mm:":{unicode:["1f1f2-1f1f2"],isCanonical:!1},":flag_ml:":{unicode:["1f1f2-1f1f1"],isCanonical:!0},":ml:":{unicode:["1f1f2-1f1f1"],isCanonical:!1},":flag_mk:":{unicode:["1f1f2-1f1f0"],isCanonical:!0},":mk:":{unicode:["1f1f2-1f1f0"],isCanonical:!1},":flag_mh:":{unicode:["1f1f2-1f1ed"],isCanonical:!0},":mh:":{unicode:["1f1f2-1f1ed"],isCanonical:!1},":flag_mg:":{unicode:["1f1f2-1f1ec"],isCanonical:!0},":mg:":{unicode:["1f1f2-1f1ec"],isCanonical:!1},":flag_mf:":{unicode:["1f1f2-1f1eb"],isCanonical:!0},":mf:":{unicode:["1f1f2-1f1eb"],isCanonical:!1},":flag_me:":{unicode:["1f1f2-1f1ea"],isCanonical:!0},":me:":{unicode:["1f1f2-1f1ea"],isCanonical:!1},":flag_md:":{unicode:["1f1f2-1f1e9"],isCanonical:!0},":md:":{unicode:["1f1f2-1f1e9"],isCanonical:!1},":flag_mc:":{unicode:["1f1f2-1f1e8"],isCanonical:!0},":mc:":{unicode:["1f1f2-1f1e8"],isCanonical:!1},":flag_ma:":{unicode:["1f1f2-1f1e6"],isCanonical:!0},":ma:":{unicode:["1f1f2-1f1e6"],isCanonical:!1},":flag_ly:":{unicode:["1f1f1-1f1fe"],isCanonical:!0},":ly:":{unicode:["1f1f1-1f1fe"],isCanonical:!1},":flag_lv:":{unicode:["1f1f1-1f1fb"],isCanonical:!0},":lv:":{unicode:["1f1f1-1f1fb"],isCanonical:!1},":flag_lu:":{unicode:["1f1f1-1f1fa"],isCanonical:!0},":lu:":{unicode:["1f1f1-1f1fa"],isCanonical:!1},":flag_lt:":{unicode:["1f1f1-1f1f9"],isCanonical:!0},":lt:":{unicode:["1f1f1-1f1f9"],isCanonical:!1},":flag_ls:":{unicode:["1f1f1-1f1f8"],isCanonical:!0},":ls:":{unicode:["1f1f1-1f1f8"],isCanonical:!1},":flag_lr:":{unicode:["1f1f1-1f1f7"],isCanonical:!0},":lr:":{unicode:["1f1f1-1f1f7"],isCanonical:!1},":flag_lk:":{unicode:["1f1f1-1f1f0"],isCanonical:!0},":lk:":{unicode:["1f1f1-1f1f0"],isCanonical:!1},":flag_li:":{unicode:["1f1f1-1f1ee"],isCanonical:!0},":li:":{unicode:["1f1f1-1f1ee"],isCanonical:!1},":flag_lc:":{unicode:["1f1f1-1f1e8"],isCanonical:!0},":lc:":{unicode:["1f1f1-1f1e8"],isCanonical:!1},":flag_lb:":{unicode:["1f1f1-1f1e7"],isCanonical:!0},":lb:":{unicode:["1f1f1-1f1e7"],isCanonical:!1},":flag_la:":{unicode:["1f1f1-1f1e6"],isCanonical:!0},":la:":{unicode:["1f1f1-1f1e6"],isCanonical:!1},":flag_kz:":{unicode:["1f1f0-1f1ff"],isCanonical:!0},":kz:":{unicode:["1f1f0-1f1ff"],isCanonical:!1},":flag_ky:":{unicode:["1f1f0-1f1fe"],isCanonical:!0},":ky:":{unicode:["1f1f0-1f1fe"],isCanonical:!1},":flag_kw:":{unicode:["1f1f0-1f1fc"],isCanonical:!0},":kw:":{unicode:["1f1f0-1f1fc"],isCanonical:!1},":flag_kr:":{unicode:["1f1f0-1f1f7"],isCanonical:!0},":kr:":{unicode:["1f1f0-1f1f7"],isCanonical:!1},":flag_kp:":{unicode:["1f1f0-1f1f5"],isCanonical:!0},":kp:":{unicode:["1f1f0-1f1f5"],isCanonical:!1},":flag_kn:":{unicode:["1f1f0-1f1f3"],isCanonical:!0},":kn:":{unicode:["1f1f0-1f1f3"],isCanonical:!1},":flag_km:":{unicode:["1f1f0-1f1f2"],isCanonical:!0},":km:":{unicode:["1f1f0-1f1f2"],isCanonical:!1},":flag_ki:":{unicode:["1f1f0-1f1ee"],isCanonical:!0},":ki:":{unicode:["1f1f0-1f1ee"],isCanonical:!1},":flag_kh:":{unicode:["1f1f0-1f1ed"],isCanonical:!0},":kh:":{unicode:["1f1f0-1f1ed"],isCanonical:!1},":flag_kg:":{unicode:["1f1f0-1f1ec"],isCanonical:!0},":kg:":{unicode:["1f1f0-1f1ec"],isCanonical:!1},":flag_ke:":{unicode:["1f1f0-1f1ea"],isCanonical:!0},":ke:":{unicode:["1f1f0-1f1ea"],isCanonical:!1},":flag_jp:":{unicode:["1f1ef-1f1f5"],isCanonical:!0},":jp:":{unicode:["1f1ef-1f1f5"],isCanonical:!1},":flag_jo:":{unicode:["1f1ef-1f1f4"],isCanonical:!0},":jo:":{unicode:["1f1ef-1f1f4"],isCanonical:!1},":flag_jm:":{unicode:["1f1ef-1f1f2"],isCanonical:!0},":jm:":{unicode:["1f1ef-1f1f2"],isCanonical:!1},":flag_je:":{unicode:["1f1ef-1f1ea"],isCanonical:!0},":je:":{unicode:["1f1ef-1f1ea"],isCanonical:!1},":flag_it:":{unicode:["1f1ee-1f1f9"],isCanonical:!0},":it:":{unicode:["1f1ee-1f1f9"],isCanonical:!1},":flag_is:":{unicode:["1f1ee-1f1f8"],isCanonical:!0},":is:":{unicode:["1f1ee-1f1f8"],isCanonical:!1},":flag_ir:":{unicode:["1f1ee-1f1f7"],isCanonical:!0},":ir:":{unicode:["1f1ee-1f1f7"],isCanonical:!1},":flag_iq:":{unicode:["1f1ee-1f1f6"],isCanonical:!0},":iq:":{unicode:["1f1ee-1f1f6"],isCanonical:!1},":flag_io:":{unicode:["1f1ee-1f1f4"],isCanonical:!0},":io:":{unicode:["1f1ee-1f1f4"],isCanonical:!1},":flag_in:":{unicode:["1f1ee-1f1f3"],isCanonical:!0},":in:":{unicode:["1f1ee-1f1f3"],isCanonical:!1},":flag_im:":{unicode:["1f1ee-1f1f2"],isCanonical:!0},":im:":{unicode:["1f1ee-1f1f2"],isCanonical:!1},":flag_il:":{unicode:["1f1ee-1f1f1"],isCanonical:!0},":il:":{unicode:["1f1ee-1f1f1"],isCanonical:!1},":flag_ie:":{unicode:["1f1ee-1f1ea"],isCanonical:!0},":ie:":{unicode:["1f1ee-1f1ea"],isCanonical:!1},":flag_id:":{unicode:["1f1ee-1f1e9"],isCanonical:!0},":indonesia:":{unicode:["1f1ee-1f1e9"],isCanonical:!1},":flag_ic:":{unicode:["1f1ee-1f1e8"],isCanonical:!0},":ic:":{unicode:["1f1ee-1f1e8"],isCanonical:!1},":flag_hu:":{unicode:["1f1ed-1f1fa"],isCanonical:!0},":hu:":{unicode:["1f1ed-1f1fa"],isCanonical:!1},":flag_ht:":{unicode:["1f1ed-1f1f9"],isCanonical:!0},":ht:":{unicode:["1f1ed-1f1f9"],isCanonical:!1},":flag_hr:":{unicode:["1f1ed-1f1f7"],isCanonical:!0},":hr:":{unicode:["1f1ed-1f1f7"],isCanonical:!1},":flag_hn:":{unicode:["1f1ed-1f1f3"],isCanonical:!0},":hn:":{unicode:["1f1ed-1f1f3"],isCanonical:!1},":flag_hm:":{unicode:["1f1ed-1f1f2"],isCanonical:!0},":hm:":{unicode:["1f1ed-1f1f2"],isCanonical:!1},":flag_hk:":{unicode:["1f1ed-1f1f0"],isCanonical:!0},":hk:":{unicode:["1f1ed-1f1f0"],isCanonical:!1},":flag_gy:":{unicode:["1f1ec-1f1fe"],isCanonical:!0},":gy:":{unicode:["1f1ec-1f1fe"],isCanonical:!1},":flag_gw:":{unicode:["1f1ec-1f1fc"],isCanonical:!0},":gw:":{unicode:["1f1ec-1f1fc"],isCanonical:!1},":flag_gu:":{unicode:["1f1ec-1f1fa"],isCanonical:!0},":gu:":{unicode:["1f1ec-1f1fa"],isCanonical:!1},":flag_gt:":{unicode:["1f1ec-1f1f9"],isCanonical:!0},":gt:":{unicode:["1f1ec-1f1f9"],isCanonical:!1},":flag_gs:":{unicode:["1f1ec-1f1f8"],isCanonical:!0},":gs:":{unicode:["1f1ec-1f1f8"],isCanonical:!1},":flag_gr:":{unicode:["1f1ec-1f1f7"],isCanonical:!0},":gr:":{unicode:["1f1ec-1f1f7"],isCanonical:!1},":flag_gq:":{unicode:["1f1ec-1f1f6"],isCanonical:!0},":gq:":{unicode:["1f1ec-1f1f6"],isCanonical:!1},":flag_gp:":{unicode:["1f1ec-1f1f5"],isCanonical:!0},":gp:":{unicode:["1f1ec-1f1f5"],isCanonical:!1},":flag_gn:":{unicode:["1f1ec-1f1f3"],isCanonical:!0},":gn:":{unicode:["1f1ec-1f1f3"],isCanonical:!1},":flag_gm:":{unicode:["1f1ec-1f1f2"],isCanonical:!0},":gm:":{unicode:["1f1ec-1f1f2"],isCanonical:!1},":flag_gl:":{unicode:["1f1ec-1f1f1"],isCanonical:!0},":gl:":{unicode:["1f1ec-1f1f1"],isCanonical:!1},":flag_gi:":{unicode:["1f1ec-1f1ee"],isCanonical:!0},":gi:":{unicode:["1f1ec-1f1ee"],isCanonical:!1},":flag_gh:":{unicode:["1f1ec-1f1ed"],isCanonical:!0},":gh:":{unicode:["1f1ec-1f1ed"],isCanonical:!1},":flag_gg:":{unicode:["1f1ec-1f1ec"],isCanonical:!0},":gg:":{unicode:["1f1ec-1f1ec"],isCanonical:!1},":flag_gf:":{unicode:["1f1ec-1f1eb"],isCanonical:!0},":gf:":{unicode:["1f1ec-1f1eb"],isCanonical:!1},":flag_ge:":{unicode:["1f1ec-1f1ea"],isCanonical:!0},":ge:":{unicode:["1f1ec-1f1ea"],isCanonical:!1},":flag_gd:":{unicode:["1f1ec-1f1e9"],isCanonical:!0},":gd:":{unicode:["1f1ec-1f1e9"],isCanonical:!1},":flag_gb:":{unicode:["1f1ec-1f1e7"],isCanonical:!0},":gb:":{unicode:["1f1ec-1f1e7"],isCanonical:!1},":flag_ga:":{unicode:["1f1ec-1f1e6"],isCanonical:!0},":ga:":{unicode:["1f1ec-1f1e6"],isCanonical:!1},":flag_fr:":{unicode:["1f1eb-1f1f7"],isCanonical:!0},":fr:":{unicode:["1f1eb-1f1f7"],isCanonical:!1},":flag_fo:":{unicode:["1f1eb-1f1f4"],isCanonical:!0},":fo:":{unicode:["1f1eb-1f1f4"],isCanonical:!1},":flag_fm:":{unicode:["1f1eb-1f1f2"],isCanonical:!0},":fm:":{unicode:["1f1eb-1f1f2"],isCanonical:!1},":flag_fk:":{unicode:["1f1eb-1f1f0"],isCanonical:!0},":fk:":{unicode:["1f1eb-1f1f0"],isCanonical:!1},":flag_fj:":{unicode:["1f1eb-1f1ef"],isCanonical:!0},":fj:":{unicode:["1f1eb-1f1ef"],isCanonical:!1},":flag_fi:":{unicode:["1f1eb-1f1ee"],isCanonical:!0},":fi:":{unicode:["1f1eb-1f1ee"],isCanonical:!1},":flag_eu:":{unicode:["1f1ea-1f1fa"],isCanonical:!0},":eu:":{unicode:["1f1ea-1f1fa"],isCanonical:!1},":flag_et:":{unicode:["1f1ea-1f1f9"],isCanonical:!0},":et:":{unicode:["1f1ea-1f1f9"],isCanonical:!1},":flag_es:":{unicode:["1f1ea-1f1f8"],isCanonical:!0},":es:":{unicode:["1f1ea-1f1f8"],isCanonical:!1},":flag_er:":{unicode:["1f1ea-1f1f7"],isCanonical:!0},":er:":{unicode:["1f1ea-1f1f7"],isCanonical:!1},":flag_eh:":{unicode:["1f1ea-1f1ed"],isCanonical:!0},":eh:":{unicode:["1f1ea-1f1ed"],isCanonical:!1},":flag_eg:":{unicode:["1f1ea-1f1ec"],isCanonical:!0},":eg:":{unicode:["1f1ea-1f1ec"],isCanonical:!1},":flag_ee:":{unicode:["1f1ea-1f1ea"],isCanonical:!0},":ee:":{unicode:["1f1ea-1f1ea"],isCanonical:!1},":flag_ec:":{unicode:["1f1ea-1f1e8"],isCanonical:!0},":ec:":{unicode:["1f1ea-1f1e8"],isCanonical:!1},":flag_ea:":{unicode:["1f1ea-1f1e6"],isCanonical:!0},":ea:":{unicode:["1f1ea-1f1e6"],isCanonical:!1},":flag_dz:":{unicode:["1f1e9-1f1ff"],isCanonical:!0},":dz:":{unicode:["1f1e9-1f1ff"],isCanonical:!1},":flag_do:":{unicode:["1f1e9-1f1f4"],isCanonical:!0},":do:":{unicode:["1f1e9-1f1f4"],isCanonical:!1},":flag_dm:":{unicode:["1f1e9-1f1f2"],isCanonical:!0},":dm:":{unicode:["1f1e9-1f1f2"],isCanonical:!1},":flag_dk:":{unicode:["1f1e9-1f1f0"],isCanonical:!0},":dk:":{unicode:["1f1e9-1f1f0"],isCanonical:!1},":flag_dj:":{unicode:["1f1e9-1f1ef"],isCanonical:!0},":dj:":{unicode:["1f1e9-1f1ef"],isCanonical:!1},":flag_dg:":{unicode:["1f1e9-1f1ec"],isCanonical:!0},":dg:":{unicode:["1f1e9-1f1ec"],isCanonical:!1},":flag_de:":{unicode:["1f1e9-1f1ea"],isCanonical:!0},":de:":{unicode:["1f1e9-1f1ea"],isCanonical:!1},":flag_cz:":{unicode:["1f1e8-1f1ff"],isCanonical:!0},":cz:":{unicode:["1f1e8-1f1ff"],isCanonical:!1},":flag_cy:":{unicode:["1f1e8-1f1fe"],isCanonical:!0},":cy:":{unicode:["1f1e8-1f1fe"],isCanonical:!1},":flag_cx:":{unicode:["1f1e8-1f1fd"],isCanonical:!0},":cx:":{unicode:["1f1e8-1f1fd"],isCanonical:!1},":flag_cw:":{unicode:["1f1e8-1f1fc"],isCanonical:!0},":cw:":{unicode:["1f1e8-1f1fc"],isCanonical:!1},":flag_cv:":{unicode:["1f1e8-1f1fb"],isCanonical:!0},":cv:":{unicode:["1f1e8-1f1fb"],isCanonical:!1},":flag_cu:":{unicode:["1f1e8-1f1fa"],isCanonical:!0},":cu:":{unicode:["1f1e8-1f1fa"],isCanonical:!1},":flag_cr:":{unicode:["1f1e8-1f1f7"],isCanonical:!0},":cr:":{unicode:["1f1e8-1f1f7"],isCanonical:!1},":flag_cp:":{unicode:["1f1e8-1f1f5"],isCanonical:!0},":cp:":{unicode:["1f1e8-1f1f5"],isCanonical:!1},":flag_co:":{unicode:["1f1e8-1f1f4"],isCanonical:!0},":co:":{unicode:["1f1e8-1f1f4"],isCanonical:!1},":flag_cn:":{unicode:["1f1e8-1f1f3"],isCanonical:!0},":cn:":{unicode:["1f1e8-1f1f3"],isCanonical:!1},":flag_cm:":{unicode:["1f1e8-1f1f2"],isCanonical:!0},":cm:":{unicode:["1f1e8-1f1f2"],isCanonical:!1},":flag_cl:":{unicode:["1f1e8-1f1f1"],isCanonical:!0},":chile:":{unicode:["1f1e8-1f1f1"],isCanonical:!1},":flag_ck:":{unicode:["1f1e8-1f1f0"],isCanonical:!0},":ck:":{unicode:["1f1e8-1f1f0"],isCanonical:!1},":flag_ci:":{unicode:["1f1e8-1f1ee"],isCanonical:!0},":ci:":{unicode:["1f1e8-1f1ee"],isCanonical:!1},":flag_ch:":{unicode:["1f1e8-1f1ed"],isCanonical:!0},":ch:":{unicode:["1f1e8-1f1ed"],isCanonical:!1},":flag_cg:":{unicode:["1f1e8-1f1ec"],isCanonical:!0},":cg:":{unicode:["1f1e8-1f1ec"],isCanonical:!1},":flag_cf:":{unicode:["1f1e8-1f1eb"],isCanonical:!0},":cf:":{unicode:["1f1e8-1f1eb"],isCanonical:!1},":flag_cd:":{unicode:["1f1e8-1f1e9"],isCanonical:!0},":congo:":{unicode:["1f1e8-1f1e9"],isCanonical:!1},":flag_cc:":{unicode:["1f1e8-1f1e8"],isCanonical:!0},":cc:":{unicode:["1f1e8-1f1e8"],isCanonical:!1},":flag_ca:":{unicode:["1f1e8-1f1e6"],isCanonical:!0},":ca:":{unicode:["1f1e8-1f1e6"],isCanonical:!1},":flag_bz:":{unicode:["1f1e7-1f1ff"],isCanonical:!0},":bz:":{unicode:["1f1e7-1f1ff"],isCanonical:!1},":flag_by:":{unicode:["1f1e7-1f1fe"],isCanonical:!0},":by:":{unicode:["1f1e7-1f1fe"],isCanonical:!1},":flag_bw:":{unicode:["1f1e7-1f1fc"],isCanonical:!0},":bw:":{unicode:["1f1e7-1f1fc"],isCanonical:!1},":flag_bv:":{unicode:["1f1e7-1f1fb"],isCanonical:!0},":bv:":{unicode:["1f1e7-1f1fb"],isCanonical:!1},":flag_bt:":{unicode:["1f1e7-1f1f9"],isCanonical:!0},":bt:":{unicode:["1f1e7-1f1f9"],isCanonical:!1},":flag_bs:":{unicode:["1f1e7-1f1f8"],isCanonical:!0},":bs:":{unicode:["1f1e7-1f1f8"],isCanonical:!1},":flag_br:":{unicode:["1f1e7-1f1f7"],isCanonical:!0},":br:":{unicode:["1f1e7-1f1f7"],isCanonical:!1},":flag_bq:":{unicode:["1f1e7-1f1f6"],isCanonical:!0},":bq:":{unicode:["1f1e7-1f1f6"],isCanonical:!1},":flag_bo:":{unicode:["1f1e7-1f1f4"],isCanonical:!0},":bo:":{unicode:["1f1e7-1f1f4"],isCanonical:!1},":flag_bn:":{unicode:["1f1e7-1f1f3"],isCanonical:!0},":bn:":{unicode:["1f1e7-1f1f3"],isCanonical:!1},":flag_bm:":{unicode:["1f1e7-1f1f2"],isCanonical:!0},":bm:":{unicode:["1f1e7-1f1f2"],isCanonical:!1},":flag_bl:":{unicode:["1f1e7-1f1f1"],isCanonical:!0},":bl:":{unicode:["1f1e7-1f1f1"],isCanonical:!1},":flag_bj:":{unicode:["1f1e7-1f1ef"],isCanonical:!0},":bj:":{unicode:["1f1e7-1f1ef"],isCanonical:!1},":flag_bi:":{unicode:["1f1e7-1f1ee"],isCanonical:!0},":bi:":{unicode:["1f1e7-1f1ee"],isCanonical:!1},":flag_bh:":{unicode:["1f1e7-1f1ed"],isCanonical:!0},":bh:":{unicode:["1f1e7-1f1ed"],isCanonical:!1},":flag_bg:":{unicode:["1f1e7-1f1ec"],isCanonical:!0},":bg:":{unicode:["1f1e7-1f1ec"],isCanonical:!1},":flag_bf:":{unicode:["1f1e7-1f1eb"],isCanonical:!0},":bf:":{unicode:["1f1e7-1f1eb"],isCanonical:!1},":flag_be:":{unicode:["1f1e7-1f1ea"],isCanonical:!0},":be:":{unicode:["1f1e7-1f1ea"],isCanonical:!1},":flag_bd:":{unicode:["1f1e7-1f1e9"],isCanonical:!0},":bd:":{unicode:["1f1e7-1f1e9"],isCanonical:!1},":flag_bb:":{unicode:["1f1e7-1f1e7"],isCanonical:!0},":bb:":{unicode:["1f1e7-1f1e7"],isCanonical:!1},":flag_ba:":{unicode:["1f1e7-1f1e6"],isCanonical:!0},":ba:":{unicode:["1f1e7-1f1e6"],isCanonical:!1},":flag_az:":{unicode:["1f1e6-1f1ff"],isCanonical:!0},":az:":{unicode:["1f1e6-1f1ff"],isCanonical:!1},":flag_ax:":{unicode:["1f1e6-1f1fd"],isCanonical:!0},":ax:":{unicode:["1f1e6-1f1fd"],isCanonical:!1},":flag_aw:":{unicode:["1f1e6-1f1fc"],isCanonical:!0},":aw:":{unicode:["1f1e6-1f1fc"],isCanonical:!1},":flag_au:":{unicode:["1f1e6-1f1fa"],isCanonical:!0},":au:":{unicode:["1f1e6-1f1fa"],isCanonical:!1},":flag_at:":{unicode:["1f1e6-1f1f9"],isCanonical:!0},":at:":{unicode:["1f1e6-1f1f9"],isCanonical:!1},":flag_as:":{unicode:["1f1e6-1f1f8"],isCanonical:!0},":as:":{unicode:["1f1e6-1f1f8"],isCanonical:!1},":flag_ar:":{unicode:["1f1e6-1f1f7"],isCanonical:!0},":ar:":{unicode:["1f1e6-1f1f7"],isCanonical:!1},":flag_aq:":{unicode:["1f1e6-1f1f6"],isCanonical:!0},":aq:":{unicode:["1f1e6-1f1f6"],isCanonical:!1},":flag_ao:":{unicode:["1f1e6-1f1f4"],isCanonical:!0},":ao:":{unicode:["1f1e6-1f1f4"],isCanonical:!1},":flag_am:":{unicode:["1f1e6-1f1f2"],isCanonical:!0},":am:":{unicode:["1f1e6-1f1f2"],isCanonical:!1},":flag_al:":{unicode:["1f1e6-1f1f1"],isCanonical:!0},":al:":{unicode:["1f1e6-1f1f1"],isCanonical:!1},":flag_ai:":{unicode:["1f1e6-1f1ee"],isCanonical:!0},":ai:":{unicode:["1f1e6-1f1ee"],isCanonical:!1},":flag_ag:":{unicode:["1f1e6-1f1ec"],isCanonical:!0},":ag:":{unicode:["1f1e6-1f1ec"],isCanonical:!1},":flag_af:":{unicode:["1f1e6-1f1eb"],isCanonical:!0},":af:":{unicode:["1f1e6-1f1eb"],isCanonical:!1},":flag_ae:":{unicode:["1f1e6-1f1ea"],isCanonical:!0},":ae:":{unicode:["1f1e6-1f1ea"],isCanonical:!1},":flag_ad:":{unicode:["1f1e6-1f1e9"],isCanonical:!0},":ad:":{unicode:["1f1e6-1f1e9"],isCanonical:!1},":flag_ac:":{unicode:["1f1e6-1f1e8"],isCanonical:!0},":ac:":{unicode:["1f1e6-1f1e8"],isCanonical:!1},":mahjong:":{unicode:["1f004-fe0f","1f004"],isCanonical:!0},":parking:":{unicode:["1f17f-fe0f","1f17f"],isCanonical:!0},":sa:":{unicode:["1f202-fe0f","1f202"],isCanonical:!0},":u7121:":{unicode:["1f21a-fe0f","1f21a"],isCanonical:!0},":u6307:":{unicode:["1f22f-fe0f","1f22f"],isCanonical:!0},":u6708:":{unicode:["1f237-fe0f","1f237"],isCanonical:!0},":film_frames:":{unicode:["1f39e-fe0f","1f39e"],isCanonical:!0},":tickets:":{unicode:["1f39f-fe0f","1f39f"],isCanonical:!0},":admission_tickets:":{unicode:["1f39f-fe0f","1f39f"],isCanonical:!1},":lifter:":{unicode:["1f3cb-fe0f","1f3cb"],isCanonical:!0},":weight_lifter:":{unicode:["1f3cb-fe0f","1f3cb"],isCanonical:!1},":golfer:":{unicode:["1f3cc-fe0f","1f3cc"],isCanonical:!0},":motorcycle:":{unicode:["1f3cd-fe0f","1f3cd"],isCanonical:!0},":racing_motorcycle:":{unicode:["1f3cd-fe0f","1f3cd"],isCanonical:!1},":race_car:":{unicode:["1f3ce-fe0f","1f3ce"],isCanonical:!0},":racing_car:":{unicode:["1f3ce-fe0f","1f3ce"],isCanonical:!1},":military_medal:":{unicode:["1f396-fe0f","1f396"],isCanonical:!0},":reminder_ribbon:":{unicode:["1f397-fe0f","1f397"],isCanonical:!0},":hot_pepper:":{unicode:["1f336-fe0f","1f336"],isCanonical:!0},":cloud_rain:":{unicode:["1f327-fe0f","1f327"],isCanonical:!0},":cloud_with_rain:":{unicode:["1f327-fe0f","1f327"],isCanonical:!1},":cloud_snow:":{unicode:["1f328-fe0f","1f328"],isCanonical:!0},":cloud_with_snow:":{unicode:["1f328-fe0f","1f328"],isCanonical:!1},":cloud_lightning:":{unicode:["1f329-fe0f","1f329"],isCanonical:!0},":cloud_with_lightning:":{unicode:["1f329-fe0f","1f329"],isCanonical:!1},":cloud_tornado:":{unicode:["1f32a-fe0f","1f32a"],isCanonical:!0},":cloud_with_tornado:":{unicode:["1f32a-fe0f","1f32a"],isCanonical:!1},":fog:":{unicode:["1f32b-fe0f","1f32b"],isCanonical:!0},":wind_blowing_face:":{unicode:["1f32c-fe0f","1f32c"],isCanonical:!0},":chipmunk:":{unicode:["1f43f-fe0f","1f43f"],isCanonical:!0},":spider:":{unicode:["1f577-fe0f","1f577"],isCanonical:!0},":spider_web:":{unicode:["1f578-fe0f","1f578"],isCanonical:!0},":thermometer:":{unicode:["1f321-fe0f","1f321"],isCanonical:!0},":microphone2:":{unicode:["1f399-fe0f","1f399"],isCanonical:!0},":studio_microphone:":{unicode:["1f399-fe0f","1f399"],isCanonical:!1},":level_slider:":{unicode:["1f39a-fe0f","1f39a"],
    isCanonical:!0},":control_knobs:":{unicode:["1f39b-fe0f","1f39b"],isCanonical:!0},":flag_white:":{unicode:["1f3f3-fe0f","1f3f3"],isCanonical:!0},":waving_white_flag:":{unicode:["1f3f3-fe0f","1f3f3"],isCanonical:!1},":rosette:":{unicode:["1f3f5-fe0f","1f3f5"],isCanonical:!0},":label:":{unicode:["1f3f7-fe0f","1f3f7"],isCanonical:!0},":projector:":{unicode:["1f4fd-fe0f","1f4fd"],isCanonical:!0},":film_projector:":{unicode:["1f4fd-fe0f","1f4fd"],isCanonical:!1},":om_symbol:":{unicode:["1f549-fe0f","1f549"],isCanonical:!0},":dove:":{unicode:["1f54a-fe0f","1f54a"],isCanonical:!0},":dove_of_peace:":{unicode:["1f54a-fe0f","1f54a"],isCanonical:!1},":candle:":{unicode:["1f56f-fe0f","1f56f"],isCanonical:!0},":clock:":{unicode:["1f570-fe0f","1f570"],isCanonical:!0},":mantlepiece_clock:":{unicode:["1f570-fe0f","1f570"],isCanonical:!1},":hole:":{unicode:["1f573-fe0f","1f573"],isCanonical:!0},":dark_sunglasses:":{unicode:["1f576-fe0f","1f576"],isCanonical:!0},":joystick:":{unicode:["1f579-fe0f","1f579"],isCanonical:!0},":paperclips:":{unicode:["1f587-fe0f","1f587"],isCanonical:!0},":linked_paperclips:":{unicode:["1f587-fe0f","1f587"],isCanonical:!1},":pen_ballpoint:":{unicode:["1f58a-fe0f","1f58a"],isCanonical:!0},":lower_left_ballpoint_pen:":{unicode:["1f58a-fe0f","1f58a"],isCanonical:!1},":pen_fountain:":{unicode:["1f58b-fe0f","1f58b"],isCanonical:!0},":lower_left_fountain_pen:":{unicode:["1f58b-fe0f","1f58b"],isCanonical:!1},":paintbrush:":{unicode:["1f58c-fe0f","1f58c"],isCanonical:!0},":lower_left_paintbrush:":{unicode:["1f58c-fe0f","1f58c"],isCanonical:!1},":crayon:":{unicode:["1f58d-fe0f","1f58d"],isCanonical:!0},":lower_left_crayon:":{unicode:["1f58d-fe0f","1f58d"],isCanonical:!1},":desktop:":{unicode:["1f5a5-fe0f","1f5a5"],isCanonical:!0},":desktop_computer:":{unicode:["1f5a5-fe0f","1f5a5"],isCanonical:!1},":printer:":{unicode:["1f5a8-fe0f","1f5a8"],isCanonical:!0},":trackball:":{unicode:["1f5b2-fe0f","1f5b2"],isCanonical:!0},":frame_photo:":{unicode:["1f5bc-fe0f","1f5bc"],isCanonical:!0},":frame_with_picture:":{unicode:["1f5bc-fe0f","1f5bc"],isCanonical:!1},":dividers:":{unicode:["1f5c2-fe0f","1f5c2"],isCanonical:!0},":card_index_dividers:":{unicode:["1f5c2-fe0f","1f5c2"],isCanonical:!1},":card_box:":{unicode:["1f5c3-fe0f","1f5c3"],isCanonical:!0},":card_file_box:":{unicode:["1f5c3-fe0f","1f5c3"],isCanonical:!1},":file_cabinet:":{unicode:["1f5c4-fe0f","1f5c4"],isCanonical:!0},":wastebasket:":{unicode:["1f5d1-fe0f","1f5d1"],isCanonical:!0},":notepad_spiral:":{unicode:["1f5d2-fe0f","1f5d2"],isCanonical:!0},":spiral_note_pad:":{unicode:["1f5d2-fe0f","1f5d2"],isCanonical:!1},":calendar_spiral:":{unicode:["1f5d3-fe0f","1f5d3"],isCanonical:!0},":spiral_calendar_pad:":{unicode:["1f5d3-fe0f","1f5d3"],isCanonical:!1},":compression:":{unicode:["1f5dc-fe0f","1f5dc"],isCanonical:!0},":key2:":{unicode:["1f5dd-fe0f","1f5dd"],isCanonical:!0},":old_key:":{unicode:["1f5dd-fe0f","1f5dd"],isCanonical:!1},":newspaper2:":{unicode:["1f5de-fe0f","1f5de"],isCanonical:!0},":rolled_up_newspaper:":{unicode:["1f5de-fe0f","1f5de"],isCanonical:!1},":dagger:":{unicode:["1f5e1-fe0f","1f5e1"],isCanonical:!0},":dagger_knife:":{unicode:["1f5e1-fe0f","1f5e1"],isCanonical:!1},":speaking_head:":{unicode:["1f5e3-fe0f","1f5e3"],isCanonical:!0},":speaking_head_in_silhouette:":{unicode:["1f5e3-fe0f","1f5e3"],isCanonical:!1},":speech_left:":{unicode:["1f5e8-fe0f","1f5e8"],isCanonical:!0},":left_speech_bubble:":{unicode:["1f5e8-fe0f","1f5e8"],isCanonical:!1},":anger_right:":{unicode:["1f5ef-fe0f","1f5ef"],isCanonical:!0},":right_anger_bubble:":{unicode:["1f5ef-fe0f","1f5ef"],isCanonical:!1},":ballot_box:":{unicode:["1f5f3-fe0f","1f5f3"],isCanonical:!0},":ballot_box_with_ballot:":{unicode:["1f5f3-fe0f","1f5f3"],isCanonical:!1},":map:":{unicode:["1f5fa-fe0f","1f5fa"],isCanonical:!0},":world_map:":{unicode:["1f5fa-fe0f","1f5fa"],isCanonical:!1},":tools:":{unicode:["1f6e0-fe0f","1f6e0"],isCanonical:!0},":hammer_and_wrench:":{unicode:["1f6e0-fe0f","1f6e0"],isCanonical:!1},":shield:":{unicode:["1f6e1-fe0f","1f6e1"],isCanonical:!0},":oil:":{unicode:["1f6e2-fe0f","1f6e2"],isCanonical:!0},":oil_drum:":{unicode:["1f6e2-fe0f","1f6e2"],isCanonical:!1},":satellite_orbital:":{unicode:["1f6f0-fe0f","1f6f0"],isCanonical:!0},":fork_knife_plate:":{unicode:["1f37d-fe0f","1f37d"],isCanonical:!0},":fork_and_knife_with_plate:":{unicode:["1f37d-fe0f","1f37d"],isCanonical:!1},":eye:":{unicode:["1f441-fe0f","1f441"],isCanonical:!0},":levitate:":{unicode:["1f574-fe0f","1f574"],isCanonical:!0},":man_in_business_suit_levitating:":{unicode:["1f574-fe0f","1f574"],isCanonical:!1},":spy:":{unicode:["1f575-fe0f","1f575"],isCanonical:!0},":sleuth_or_spy:":{unicode:["1f575-fe0f","1f575"],isCanonical:!1},":hand_splayed:":{unicode:["1f590-fe0f","1f590"],isCanonical:!0},":raised_hand_with_fingers_splayed:":{unicode:["1f590-fe0f","1f590"],isCanonical:!1},":mountain_snow:":{unicode:["1f3d4-fe0f","1f3d4"],isCanonical:!0},":snow_capped_mountain:":{unicode:["1f3d4-fe0f","1f3d4"],isCanonical:!1},":camping:":{unicode:["1f3d5-fe0f","1f3d5"],isCanonical:!0},":beach:":{unicode:["1f3d6-fe0f","1f3d6"],isCanonical:!0},":beach_with_umbrella:":{unicode:["1f3d6-fe0f","1f3d6"],isCanonical:!1},":construction_site:":{unicode:["1f3d7-fe0f","1f3d7"],isCanonical:!0},":building_construction:":{unicode:["1f3d7-fe0f","1f3d7"],isCanonical:!1},":homes:":{unicode:["1f3d8-fe0f","1f3d8"],isCanonical:!0},":house_buildings:":{unicode:["1f3d8-fe0f","1f3d8"],isCanonical:!1},":cityscape:":{unicode:["1f3d9-fe0f","1f3d9"],isCanonical:!0},":house_abandoned:":{unicode:["1f3da-fe0f","1f3da"],isCanonical:!0},":derelict_house_building:":{unicode:["1f3da-fe0f","1f3da"],isCanonical:!1},":classical_building:":{unicode:["1f3db-fe0f","1f3db"],isCanonical:!0},":desert:":{unicode:["1f3dc-fe0f","1f3dc"],isCanonical:!0},":island:":{unicode:["1f3dd-fe0f","1f3dd"],isCanonical:!0},":desert_island:":{unicode:["1f3dd-fe0f","1f3dd"],isCanonical:!1},":park:":{unicode:["1f3de-fe0f","1f3de"],isCanonical:!0},":national_park:":{unicode:["1f3de-fe0f","1f3de"],isCanonical:!1},":stadium:":{unicode:["1f3df-fe0f","1f3df"],isCanonical:!0},":couch:":{unicode:["1f6cb-fe0f","1f6cb"],isCanonical:!0},":couch_and_lamp:":{unicode:["1f6cb-fe0f","1f6cb"],isCanonical:!1},":shopping_bags:":{unicode:["1f6cd-fe0f","1f6cd"],isCanonical:!0},":bellhop:":{unicode:["1f6ce-fe0f","1f6ce"],isCanonical:!0},":bellhop_bell:":{unicode:["1f6ce-fe0f","1f6ce"],isCanonical:!1},":bed:":{unicode:["1f6cf-fe0f","1f6cf"],isCanonical:!0},":motorway:":{unicode:["1f6e3-fe0f","1f6e3"],isCanonical:!0},":railway_track:":{unicode:["1f6e4-fe0f","1f6e4"],isCanonical:!0},":railroad_track:":{unicode:["1f6e4-fe0f","1f6e4"],isCanonical:!1},":motorboat:":{unicode:["1f6e5-fe0f","1f6e5"],isCanonical:!0},":airplane_small:":{unicode:["1f6e9-fe0f","1f6e9"],isCanonical:!0},":small_airplane:":{unicode:["1f6e9-fe0f","1f6e9"],isCanonical:!1},":cruise_ship:":{unicode:["1f6f3-fe0f","1f6f3"],isCanonical:!0},":passenger_ship:":{unicode:["1f6f3-fe0f","1f6f3"],isCanonical:!1},":white_sun_small_cloud:":{unicode:["1f324-fe0f","1f324"],isCanonical:!0},":white_sun_with_small_cloud:":{unicode:["1f324-fe0f","1f324"],isCanonical:!1},":white_sun_cloud:":{unicode:["1f325-fe0f","1f325"],isCanonical:!0},":white_sun_behind_cloud:":{unicode:["1f325-fe0f","1f325"],isCanonical:!1},":white_sun_rain_cloud:":{unicode:["1f326-fe0f","1f326"],isCanonical:!0},":white_sun_behind_cloud_with_rain:":{unicode:["1f326-fe0f","1f326"],isCanonical:!1},":mouse_three_button:":{unicode:["1f5b1-fe0f","1f5b1"],isCanonical:!0},":three_button_mouse:":{unicode:["1f5b1-fe0f","1f5b1"],isCanonical:!1},":point_up_tone1:":{unicode:["261d-1f3fb"],isCanonical:!0},":point_up_tone2:":{unicode:["261d-1f3fc"],isCanonical:!0},":point_up_tone3:":{unicode:["261d-1f3fd"],isCanonical:!0},":point_up_tone4:":{unicode:["261d-1f3fe"],isCanonical:!0},":point_up_tone5:":{unicode:["261d-1f3ff"],isCanonical:!0},":v_tone1:":{unicode:["270c-1f3fb"],isCanonical:!0},":v_tone2:":{unicode:["270c-1f3fc"],isCanonical:!0},":v_tone3:":{unicode:["270c-1f3fd"],isCanonical:!0},":v_tone4:":{unicode:["270c-1f3fe"],isCanonical:!0},":v_tone5:":{unicode:["270c-1f3ff"],isCanonical:!0},":fist_tone1:":{unicode:["270a-1f3fb"],isCanonical:!0},":fist_tone2:":{unicode:["270a-1f3fc"],isCanonical:!0},":fist_tone3:":{unicode:["270a-1f3fd"],isCanonical:!0},":fist_tone4:":{unicode:["270a-1f3fe"],isCanonical:!0},":fist_tone5:":{unicode:["270a-1f3ff"],isCanonical:!0},":raised_hand_tone1:":{unicode:["270b-1f3fb"],isCanonical:!0},":raised_hand_tone2:":{unicode:["270b-1f3fc"],isCanonical:!0},":raised_hand_tone3:":{unicode:["270b-1f3fd"],isCanonical:!0},":raised_hand_tone4:":{unicode:["270b-1f3fe"],isCanonical:!0},":raised_hand_tone5:":{unicode:["270b-1f3ff"],isCanonical:!0},":writing_hand_tone1:":{unicode:["270d-1f3fb"],isCanonical:!0},":writing_hand_tone2:":{unicode:["270d-1f3fc"],isCanonical:!0},":writing_hand_tone3:":{unicode:["270d-1f3fd"],isCanonical:!0},":writing_hand_tone4:":{unicode:["270d-1f3fe"],isCanonical:!0},":writing_hand_tone5:":{unicode:["270d-1f3ff"],isCanonical:!0},":basketball_player_tone1:":{unicode:["26f9-1f3fb"],isCanonical:!0},":person_with_ball_tone1:":{unicode:["26f9-1f3fb"],isCanonical:!1},":basketball_player_tone2:":{unicode:["26f9-1f3fc"],isCanonical:!0},":person_with_ball_tone2:":{unicode:["26f9-1f3fc"],isCanonical:!1},":basketball_player_tone3:":{unicode:["26f9-1f3fd"],isCanonical:!0},":person_with_ball_tone3:":{unicode:["26f9-1f3fd"],isCanonical:!1},":basketball_player_tone4:":{unicode:["26f9-1f3fe"],isCanonical:!0},":person_with_ball_tone4:":{unicode:["26f9-1f3fe"],isCanonical:!1},":basketball_player_tone5:":{unicode:["26f9-1f3ff"],isCanonical:!0},":person_with_ball_tone5:":{unicode:["26f9-1f3ff"],isCanonical:!1},":copyright:":{unicode:["00a9-fe0f","00a9"],isCanonical:!0},":registered:":{unicode:["00ae-fe0f","00ae"],isCanonical:!0},":bangbang:":{unicode:["203c-fe0f","203c"],isCanonical:!0},":interrobang:":{unicode:["2049-fe0f","2049"],isCanonical:!0},":tm:":{unicode:["2122-fe0f","2122"],isCanonical:!0},":information_source:":{unicode:["2139-fe0f","2139"],isCanonical:!0},":left_right_arrow:":{unicode:["2194-fe0f","2194"],isCanonical:!0},":arrow_up_down:":{unicode:["2195-fe0f","2195"],isCanonical:!0},":arrow_upper_left:":{unicode:["2196-fe0f","2196"],isCanonical:!0},":arrow_upper_right:":{unicode:["2197-fe0f","2197"],isCanonical:!0},":arrow_lower_right:":{unicode:["2198-fe0f","2198"],isCanonical:!0},":arrow_lower_left:":{unicode:["2199-fe0f","2199"],isCanonical:!0},":leftwards_arrow_with_hook:":{unicode:["21a9-fe0f","21a9"],isCanonical:!0},":arrow_right_hook:":{unicode:["21aa-fe0f","21aa"],isCanonical:!0},":watch:":{unicode:["231a-fe0f","231a"],isCanonical:!0},":hourglass:":{unicode:["231b-fe0f","231b"],isCanonical:!0},":m:":{unicode:["24c2-fe0f","24c2"],isCanonical:!0},":black_small_square:":{unicode:["25aa-fe0f","25aa"],isCanonical:!0},":white_small_square:":{unicode:["25ab-fe0f","25ab"],isCanonical:!0},":arrow_forward:":{unicode:["25b6-fe0f","25b6"],isCanonical:!0},":arrow_backward:":{unicode:["25c0-fe0f","25c0"],isCanonical:!0},":white_medium_square:":{unicode:["25fb-fe0f","25fb"],isCanonical:!0},":black_medium_square:":{unicode:["25fc-fe0f","25fc"],isCanonical:!0},":white_medium_small_square:":{unicode:["25fd-fe0f","25fd"],isCanonical:!0},":black_medium_small_square:":{unicode:["25fe-fe0f","25fe"],isCanonical:!0},":sunny:":{unicode:["2600-fe0f","2600"],isCanonical:!0},":cloud:":{unicode:["2601-fe0f","2601"],isCanonical:!0},":telephone:":{unicode:["260e-fe0f","260e"],isCanonical:!0},":ballot_box_with_check:":{unicode:["2611-fe0f","2611"],isCanonical:!0},":umbrella:":{unicode:["2614-fe0f","2614"],isCanonical:!0},":coffee:":{unicode:["2615-fe0f","2615"],isCanonical:!0},":point_up:":{unicode:["261d-fe0f","261d"],isCanonical:!0},":relaxed:":{unicode:["263a-fe0f","263a"],isCanonical:!0},":aries:":{unicode:["2648-fe0f","2648"],isCanonical:!0},":taurus:":{unicode:["2649-fe0f","2649"],isCanonical:!0},":gemini:":{unicode:["264a-fe0f","264a"],isCanonical:!0},":cancer:":{unicode:["264b-fe0f","264b"],isCanonical:!0},":leo:":{unicode:["264c-fe0f","264c"],isCanonical:!0},":virgo:":{unicode:["264d-fe0f","264d"],isCanonical:!0},":libra:":{unicode:["264e-fe0f","264e"],isCanonical:!0},":scorpius:":{unicode:["264f-fe0f","264f"],isCanonical:!0},":sagittarius:":{unicode:["2650-fe0f","2650"],isCanonical:!0},":capricorn:":{unicode:["2651-fe0f","2651"],isCanonical:!0},":aquarius:":{unicode:["2652-fe0f","2652"],isCanonical:!0},":pisces:":{unicode:["2653-fe0f","2653"],isCanonical:!0},":spades:":{unicode:["2660-fe0f","2660"],isCanonical:!0},":clubs:":{unicode:["2663-fe0f","2663"],isCanonical:!0},":hearts:":{unicode:["2665-fe0f","2665"],isCanonical:!0},":diamonds:":{unicode:["2666-fe0f","2666"],isCanonical:!0},":hotsprings:":{unicode:["2668-fe0f","2668"],isCanonical:!0},":recycle:":{unicode:["267b-fe0f","267b"],isCanonical:!0},":wheelchair:":{unicode:["267f-fe0f","267f"],isCanonical:!0},":anchor:":{unicode:["2693-fe0f","2693"],isCanonical:!0},":warning:":{unicode:["26a0-fe0f","26a0"],isCanonical:!0},":zap:":{unicode:["26a1-fe0f","26a1"],isCanonical:!0},":white_circle:":{unicode:["26aa-fe0f","26aa"],isCanonical:!0},":black_circle:":{unicode:["26ab-fe0f","26ab"],isCanonical:!0},":soccer:":{unicode:["26bd-fe0f","26bd"],isCanonical:!0},":baseball:":{unicode:["26be-fe0f","26be"],isCanonical:!0},":snowman:":{unicode:["26c4-fe0f","26c4"],isCanonical:!0},":partly_sunny:":{unicode:["26c5-fe0f","26c5"],isCanonical:!0},":no_entry:":{unicode:["26d4-fe0f","26d4"],isCanonical:!0},":church:":{unicode:["26ea-fe0f","26ea"],isCanonical:!0},":fountain:":{unicode:["26f2-fe0f","26f2"],isCanonical:!0},":golf:":{unicode:["26f3-fe0f","26f3"],isCanonical:!0},":sailboat:":{unicode:["26f5-fe0f","26f5"],isCanonical:!0},":tent:":{unicode:["26fa-fe0f","26fa"],isCanonical:!0},":fuelpump:":{unicode:["26fd-fe0f","26fd"],isCanonical:!0},":scissors:":{unicode:["2702-fe0f","2702"],isCanonical:!0},":airplane:":{unicode:["2708-fe0f","2708"],isCanonical:!0},":envelope:":{unicode:["2709-fe0f","2709"],isCanonical:!0},":v:":{unicode:["270c-fe0f","270c"],isCanonical:!0},":pencil2:":{unicode:["270f-fe0f","270f"],isCanonical:!0},":black_nib:":{unicode:["2712-fe0f","2712"],isCanonical:!0},":heavy_check_mark:":{unicode:["2714-fe0f","2714"],isCanonical:!0},":heavy_multiplication_x:":{unicode:["2716-fe0f","2716"],isCanonical:!0},":eight_spoked_asterisk:":{unicode:["2733-fe0f","2733"],isCanonical:!0},":eight_pointed_black_star:":{unicode:["2734-fe0f","2734"],isCanonical:!0},":snowflake:":{unicode:["2744-fe0f","2744"],isCanonical:!0},":sparkle:":{unicode:["2747-fe0f","2747"],isCanonical:!0},":exclamation:":{unicode:["2757-fe0f","2757"],isCanonical:!0},":heart:":{unicode:["2764-fe0f","2764"],isCanonical:!0},":arrow_right:":{unicode:["27a1-fe0f","27a1"],isCanonical:!0},":arrow_heading_up:":{unicode:["2934-fe0f","2934"],isCanonical:!0},":arrow_heading_down:":{unicode:["2935-fe0f","2935"],isCanonical:!0},":arrow_left:":{unicode:["2b05-fe0f","2b05"],isCanonical:!0},":arrow_up:":{unicode:["2b06-fe0f","2b06"],isCanonical:!0},":arrow_down:":{unicode:["2b07-fe0f","2b07"],isCanonical:!0},":black_large_square:":{unicode:["2b1b-fe0f","2b1b"],isCanonical:!0},":white_large_square:":{unicode:["2b1c-fe0f","2b1c"],isCanonical:!0},":star:":{unicode:["2b50-fe0f","2b50"],isCanonical:!0},":o:":{unicode:["2b55-fe0f","2b55"],isCanonical:!0},":wavy_dash:":{unicode:["3030-fe0f","3030"],isCanonical:!0},":part_alternation_mark:":{unicode:["303d-fe0f","303d"],isCanonical:!0},":congratulations:":{unicode:["3297-fe0f","3297"],isCanonical:!0},":secret:":{unicode:["3299-fe0f","3299"],isCanonical:!0},":cross:":{unicode:["271d-fe0f","271d"],isCanonical:!0},":latin_cross:":{unicode:["271d-fe0f","271d"],isCanonical:!1},":keyboard:":{unicode:["2328-fe0f","2328"],isCanonical:!0},":writing_hand:":{unicode:["270d-fe0f","270d"],isCanonical:!0},":eject:":{unicode:["23cf-fe0f","23cf"],isCanonical:!0},":eject_symbol:":{unicode:["23cf-fe0f","23cf"],isCanonical:!1},":track_next:":{unicode:["23ed-fe0f","23ed"],isCanonical:!0},":next_track:":{unicode:["23ed-fe0f","23ed"],isCanonical:!1},":track_previous:":{unicode:["23ee-fe0f","23ee"],isCanonical:!0},":previous_track:":{unicode:["23ee-fe0f","23ee"],isCanonical:!1},":play_pause:":{unicode:["23ef-fe0f","23ef"],isCanonical:!0},":stopwatch:":{unicode:["23f1-fe0f","23f1"],isCanonical:!0},":timer:":{unicode:["23f2-fe0f","23f2"],isCanonical:!0},":timer_clock:":{unicode:["23f2-fe0f","23f2"],isCanonical:!1},":pause_button:":{unicode:["23f8-fe0f","23f8"],isCanonical:!0},":double_vertical_bar:":{unicode:["23f8-fe0f","23f8"],isCanonical:!1},":stop_button:":{unicode:["23f9-fe0f","23f9"],isCanonical:!0},":record_button:":{unicode:["23fa-fe0f","23fa"],isCanonical:!0},":umbrella2:":{unicode:["2602-fe0f","2602"],isCanonical:!0},":snowman2:":{unicode:["2603-fe0f","2603"],isCanonical:!0},":comet:":{unicode:["2604-fe0f","2604"],isCanonical:!0},":shamrock:":{unicode:["2618-fe0f","2618"],isCanonical:!0},":skull_crossbones:":{unicode:["2620-fe0f","2620"],isCanonical:!0},":skull_and_crossbones:":{unicode:["2620-fe0f","2620"],isCanonical:!1},":radioactive:":{unicode:["2622-fe0f","2622"],isCanonical:!0},":radioactive_sign:":{unicode:["2622-fe0f","2622"],isCanonical:!1},":biohazard:":{unicode:["2623-fe0f","2623"],isCanonical:!0},":biohazard_sign:":{unicode:["2623-fe0f","2623"],isCanonical:!1},":orthodox_cross:":{unicode:["2626-fe0f","2626"],isCanonical:!0},":star_and_crescent:":{unicode:["262a-fe0f","262a"],isCanonical:!0},":peace:":{unicode:["262e-fe0f","262e"],isCanonical:!0},":peace_symbol:":{unicode:["262e-fe0f","262e"],isCanonical:!1},":yin_yang:":{unicode:["262f-fe0f","262f"],isCanonical:!0},":wheel_of_dharma:":{unicode:["2638-fe0f","2638"],isCanonical:!0},":frowning2:":{unicode:["2639-fe0f","2639"],isCanonical:!0},":white_frowning_face:":{unicode:["2639-fe0f","2639"],isCanonical:!1},":hammer_pick:":{unicode:["2692-fe0f","2692"],isCanonical:!0},":hammer_and_pick:":{unicode:["2692-fe0f","2692"],isCanonical:!1},":crossed_swords:":{unicode:["2694-fe0f","2694"],isCanonical:!0},":scales:":{unicode:["2696-fe0f","2696"],isCanonical:!0},":alembic:":{unicode:["2697-fe0f","2697"],isCanonical:!0},":gear:":{unicode:["2699-fe0f","2699"],isCanonical:!0},":atom:":{unicode:["269b-fe0f","269b"],isCanonical:!0},":atom_symbol:":{unicode:["269b-fe0f","269b"],isCanonical:!1},":fleur-de-lis:":{unicode:["269c-fe0f","269c"],isCanonical:!0},":coffin:":{unicode:["26b0-fe0f","26b0"],isCanonical:!0},":urn:":{unicode:["26b1-fe0f","26b1"],isCanonical:!0},":funeral_urn:":{unicode:["26b1-fe0f","26b1"],isCanonical:!1},":thunder_cloud_rain:":{unicode:["26c8-fe0f","26c8"],isCanonical:!0},":thunder_cloud_and_rain:":{unicode:["26c8-fe0f","26c8"],isCanonical:!1},":pick:":{unicode:["26cf-fe0f","26cf"],isCanonical:!0},":helmet_with_cross:":{unicode:["26d1-fe0f","26d1"],isCanonical:!0},":helmet_with_white_cross:":{unicode:["26d1-fe0f","26d1"],isCanonical:!1},":chains:":{unicode:["26d3-fe0f","26d3"],isCanonical:!0},":shinto_shrine:":{unicode:["26e9-fe0f","26e9"],isCanonical:!0},":mountain:":{unicode:["26f0-fe0f","26f0"],isCanonical:!0},":beach_umbrella:":{unicode:["26f1-fe0f","26f1"],isCanonical:!0},":umbrella_on_ground:":{unicode:["26f1-fe0f","26f1"],isCanonical:!1},":ferry:":{unicode:["26f4-fe0f","26f4"],isCanonical:!0},":skier:":{unicode:["26f7-fe0f","26f7"],isCanonical:!0},":ice_skate:":{unicode:["26f8-fe0f","26f8"],isCanonical:!0},":basketball_player:":{unicode:["26f9-fe0f","26f9"],isCanonical:!0},":person_with_ball:":{unicode:["26f9-fe0f","26f9"],isCanonical:!1},":star_of_david:":{unicode:["2721-fe0f","2721"],isCanonical:!0},":heart_exclamation:":{unicode:["2763-fe0f","2763"],isCanonical:!0},":heavy_heart_exclamation_mark_ornament:":{unicode:["2763-fe0f","2763"],isCanonical:!1},":third_place:":{unicode:["1f949"],isCanonical:!0},":third_place_medal:":{unicode:["1f949"],isCanonical:!1},":second_place:":{unicode:["1f948"],isCanonical:!0},":second_place_medal:":{unicode:["1f948"],isCanonical:!1},":first_place:":{unicode:["1f947"],isCanonical:!0},":first_place_medal:":{unicode:["1f947"],isCanonical:!1},":fencer:":{unicode:["1f93a"],isCanonical:!0},":fencing:":{unicode:["1f93a"],isCanonical:!1},":goal:":{unicode:["1f945"],isCanonical:!0},":goal_net:":{unicode:["1f945"],isCanonical:!1},":handball:":{unicode:["1f93e"],isCanonical:!0},":regional_indicator_z:":{unicode:["1f1ff"],isCanonical:!0},":water_polo:":{unicode:["1f93d"],isCanonical:!0},":martial_arts_uniform:":{unicode:["1f94b"],isCanonical:!0},":karate_uniform:":{unicode:["1f94b"],isCanonical:!1},":boxing_glove:":{unicode:["1f94a"],isCanonical:!0},":boxing_gloves:":{unicode:["1f94a"],isCanonical:!1},":wrestlers:":{unicode:["1f93c"],isCanonical:!0},":wrestling:":{unicode:["1f93c"],isCanonical:!1},":juggling:":{unicode:["1f939"],isCanonical:!0},":juggler:":{unicode:["1f939"],isCanonical:!1},":cartwheel:":{unicode:["1f938"],isCanonical:!0},":person_doing_cartwheel:":{unicode:["1f938"],isCanonical:!1},":canoe:":{unicode:["1f6f6"],isCanonical:!0},":kayak:":{unicode:["1f6f6"],isCanonical:!1},":motor_scooter:":{unicode:["1f6f5"],isCanonical:!0},":motorbike:":{unicode:["1f6f5"],isCanonical:!1},":scooter:":{unicode:["1f6f4"],isCanonical:!0},":shopping_cart:":{unicode:["1f6d2"],isCanonical:!0},":shopping_trolley:":{unicode:["1f6d2"],isCanonical:!1},":black_joker:":{unicode:["1f0cf"],isCanonical:!0},":a:":{unicode:["1f170"],isCanonical:!0},":b:":{unicode:["1f171"],isCanonical:!0},":o2:":{unicode:["1f17e"],isCanonical:!0},":octagonal_sign:":{unicode:["1f6d1"],isCanonical:!0},":stop_sign:":{unicode:["1f6d1"],isCanonical:!1},":ab:":{unicode:["1f18e"],isCanonical:!0},":cl:":{unicode:["1f191"],isCanonical:!0},":regional_indicator_y:":{unicode:["1f1fe"],isCanonical:!0},":cool:":{unicode:["1f192"],isCanonical:!0},":free:":{unicode:["1f193"],isCanonical:!0},":id:":{unicode:["1f194"],isCanonical:!0},":new:":{unicode:["1f195"],isCanonical:!0},":ng:":{unicode:["1f196"],isCanonical:!0},":ok:":{unicode:["1f197"],isCanonical:!0},":sos:":{unicode:["1f198"],isCanonical:!0},":spoon:":{unicode:["1f944"],isCanonical:!0},":up:":{unicode:["1f199"],isCanonical:!0},":vs:":{unicode:["1f19a"],isCanonical:!0},":champagne_glass:":{unicode:["1f942"],isCanonical:!0},":clinking_glass:":{unicode:["1f942"],isCanonical:!1},":tumbler_glass:":{unicode:["1f943"],isCanonical:!0},":whisky:":{unicode:["1f943"],isCanonical:!1},":koko:":{unicode:["1f201"],isCanonical:!0},":stuffed_flatbread:":{unicode:["1f959"],isCanonical:!0},":stuffed_pita:":{unicode:["1f959"],isCanonical:!1},":u7981:":{unicode:["1f232"],isCanonical:!0},":u7a7a:":{unicode:["1f233"],isCanonical:!0},":u5408:":{unicode:["1f234"],isCanonical:!0},":u6e80:":{unicode:["1f235"],isCanonical:!0},":u6709:":{unicode:["1f236"],isCanonical:!0},":shallow_pan_of_food:":{unicode:["1f958"],isCanonical:!0},":paella:":{unicode:["1f958"],isCanonical:!1},":u7533:":{unicode:["1f238"],isCanonical:!0},":u5272:":{unicode:["1f239"],isCanonical:!0},":salad:":{unicode:["1f957"],isCanonical:!0},":green_salad:":{unicode:["1f957"],isCanonical:!1},":u55b6:":{unicode:["1f23a"],isCanonical:!0},":ideograph_advantage:":{unicode:["1f250"],isCanonical:!0},":accept:":{unicode:["1f251"],isCanonical:!0},":cyclone:":{unicode:["1f300"],isCanonical:!0},":french_bread:":{unicode:["1f956"],isCanonical:!0},":baguette_bread:":{unicode:["1f956"],isCanonical:!1},":foggy:":{unicode:["1f301"],isCanonical:!0},":closed_umbrella:":{unicode:["1f302"],isCanonical:!0},":night_with_stars:":{unicode:["1f303"],isCanonical:!0},":sunrise_over_mountains:":{unicode:["1f304"],isCanonical:!0},":sunrise:":{unicode:["1f305"],isCanonical:!0},":city_dusk:":{unicode:["1f306"],isCanonical:!0},":carrot:":{unicode:["1f955"],isCanonical:!0},":city_sunset:":{unicode:["1f307"],isCanonical:!0},":city_sunrise:":{unicode:["1f307"],isCanonical:!1},":rainbow:":{unicode:["1f308"],isCanonical:!0},":potato:":{unicode:["1f954"],isCanonical:!0},":bridge_at_night:":{unicode:["1f309"],isCanonical:!0},":ocean:":{unicode:["1f30a"],isCanonical:!0},":volcano:":{unicode:["1f30b"],isCanonical:!0},":milky_way:":{unicode:["1f30c"],isCanonical:!0},":earth_asia:":{unicode:["1f30f"],isCanonical:!0},":new_moon:":{unicode:["1f311"],isCanonical:!0},":bacon:":{unicode:["1f953"],isCanonical:!0},":first_quarter_moon:":{unicode:["1f313"],isCanonical:!0},":waxing_gibbous_moon:":{unicode:["1f314"],isCanonical:!0},":full_moon:":{unicode:["1f315"],isCanonical:!0},":crescent_moon:":{unicode:["1f319"],isCanonical:!0},":first_quarter_moon_with_face:":{unicode:["1f31b"],isCanonical:!0},":star2:":{unicode:["1f31f"],isCanonical:!0},":cucumber:":{unicode:["1f952"],isCanonical:!0},":stars:":{unicode:["1f320"],isCanonical:!0},":chestnut:":{unicode:["1f330"],isCanonical:!0},":avocado:":{unicode:["1f951"],isCanonical:!0},":seedling:":{unicode:["1f331"],isCanonical:!0},":palm_tree:":{unicode:["1f334"],isCanonical:!0},":cactus:":{unicode:["1f335"],isCanonical:!0},":tulip:":{unicode:["1f337"],isCanonical:!0},":cherry_blossom:":{unicode:["1f338"],isCanonical:!0},":rose:":{unicode:["1f339"],isCanonical:!0},":hibiscus:":{unicode:["1f33a"],isCanonical:!0},":sunflower:":{unicode:["1f33b"],isCanonical:!0},":blossom:":{unicode:["1f33c"],isCanonical:!0},":corn:":{unicode:["1f33d"],isCanonical:!0},":croissant:":{unicode:["1f950"],isCanonical:!0},":ear_of_rice:":{unicode:["1f33e"],isCanonical:!0},":herb:":{unicode:["1f33f"],isCanonical:!0},":four_leaf_clover:":{unicode:["1f340"],isCanonical:!0},":maple_leaf:":{unicode:["1f341"],isCanonical:!0},":fallen_leaf:":{unicode:["1f342"],isCanonical:!0},":leaves:":{unicode:["1f343"],isCanonical:!0},":mushroom:":{unicode:["1f344"],isCanonical:!0},":tomato:":{unicode:["1f345"],isCanonical:!0},":eggplant:":{unicode:["1f346"],isCanonical:!0},":grapes:":{unicode:["1f347"],isCanonical:!0},":melon:":{unicode:["1f348"],isCanonical:!0},":watermelon:":{unicode:["1f349"],isCanonical:!0},":tangerine:":{unicode:["1f34a"],isCanonical:!0},":wilted_rose:":{unicode:["1f940"],isCanonical:!0},":wilted_flower:":{unicode:["1f940"],isCanonical:!1},":banana:":{unicode:["1f34c"],isCanonical:!0},":pineapple:":{unicode:["1f34d"],isCanonical:!0},":apple:":{unicode:["1f34e"],isCanonical:!0},":green_apple:":{unicode:["1f34f"],isCanonical:!0},":peach:":{unicode:["1f351"],isCanonical:!0},":cherries:":{unicode:["1f352"],isCanonical:!0},":strawberry:":{unicode:["1f353"],isCanonical:!0},":rhino:":{unicode:["1f98f"],isCanonical:!0},":rhinoceros:":{unicode:["1f98f"],isCanonical:!1},":hamburger:":{unicode:["1f354"],isCanonical:!0},":pizza:":{unicode:["1f355"],isCanonical:!0},":meat_on_bone:":{unicode:["1f356"],isCanonical:!0},":lizard:":{unicode:["1f98e"],isCanonical:!0},":poultry_leg:":{unicode:["1f357"],isCanonical:!0},":rice_cracker:":{unicode:["1f358"],isCanonical:!0},":rice_ball:":{unicode:["1f359"],isCanonical:!0},":gorilla:":{unicode:["1f98d"],isCanonical:!0},":rice:":{unicode:["1f35a"],isCanonical:!0},":curry:":{unicode:["1f35b"],isCanonical:!0},":deer:":{unicode:["1f98c"],isCanonical:!0},":ramen:":{unicode:["1f35c"],isCanonical:!0},":spaghetti:":{unicode:["1f35d"],isCanonical:!0},":bread:":{unicode:["1f35e"],isCanonical:!0},":fries:":{unicode:["1f35f"],isCanonical:!0},":butterfly:":{unicode:["1f98b"],isCanonical:!0},":sweet_potato:":{unicode:["1f360"],isCanonical:!0},":dango:":{unicode:["1f361"],isCanonical:!0},":fox:":{unicode:["1f98a"],isCanonical:!0},":fox_face:":{unicode:["1f98a"],isCanonical:!1},":oden:":{unicode:["1f362"],isCanonical:!0},":sushi:":{unicode:["1f363"],isCanonical:!0},":owl:":{unicode:["1f989"],isCanonical:!0},":fried_shrimp:":{unicode:["1f364"],isCanonical:!0},":fish_cake:":{unicode:["1f365"],isCanonical:!0},":shark:":{unicode:["1f988"],isCanonical:!0},":icecream:":{unicode:["1f366"],isCanonical:!0},":bat:":{unicode:["1f987"],isCanonical:!0},":shaved_ice:":{unicode:["1f367"],isCanonical:!0},":regional_indicator_x:":{unicode:["1f1fd"],isCanonical:!0},":ice_cream:":{unicode:["1f368"],isCanonical:!0},":duck:":{unicode:["1f986"],isCanonical:!0},":doughnut:":{unicode:["1f369"],isCanonical:!0},":eagle:":{unicode:["1f985"],isCanonical:!0},":cookie:":{unicode:["1f36a"],isCanonical:!0},":black_heart:":{unicode:["1f5a4"],isCanonical:!0},":chocolate_bar:":{unicode:["1f36b"],isCanonical:!0},":candy:":{unicode:["1f36c"],isCanonical:!0},":lollipop:":{unicode:["1f36d"],isCanonical:!0},":custard:":{unicode:["1f36e"],isCanonical:!0},":pudding:":{unicode:["1f36e"],isCanonical:!1},":flan:":{unicode:["1f36e"],isCanonical:!1},":honey_pot:":{unicode:["1f36f"],isCanonical:!0},":fingers_crossed:":{unicode:["1f91e"],isCanonical:!0},":hand_with_index_and_middle_finger_crossed:":{unicode:["1f91e"],isCanonical:!1},":cake:":{unicode:["1f370"],isCanonical:!0},":bento:":{unicode:["1f371"],isCanonical:!0},":stew:":{unicode:["1f372"],isCanonical:!0},":handshake:":{unicode:["1f91d"],isCanonical:!0},":shaking_hands:":{unicode:["1f91d"],isCanonical:!1},":cooking:":{unicode:["1f373"],isCanonical:!0},":fork_and_knife:":{unicode:["1f374"],isCanonical:!0},":tea:":{unicode:["1f375"],isCanonical:!0},":sake:":{unicode:["1f376"],isCanonical:!0},":wine_glass:":{unicode:["1f377"],isCanonical:!0},":cocktail:":{unicode:["1f378"],isCanonical:!0},":tropical_drink:":{unicode:["1f379"],isCanonical:!0},":beer:":{unicode:["1f37a"],isCanonical:!0},":beers:":{unicode:["1f37b"],isCanonical:!0},":ribbon:":{unicode:["1f380"],isCanonical:!0},":gift:":{unicode:["1f381"],isCanonical:!0},":birthday:":{unicode:["1f382"],isCanonical:!0},":jack_o_lantern:":{unicode:["1f383"],isCanonical:!0},":left_facing_fist:":{unicode:["1f91b"],isCanonical:!0},":left_fist:":{unicode:["1f91b"],isCanonical:!1},":right_facing_fist:":{unicode:["1f91c"],isCanonical:!0},":right_fist:":{unicode:["1f91c"],isCanonical:!1},":christmas_tree:":{unicode:["1f384"],isCanonical:!0},":santa:":{unicode:["1f385"],isCanonical:!0},":fireworks:":{unicode:["1f386"],isCanonical:!0},":raised_back_of_hand:":{unicode:["1f91a"],isCanonical:!0},":back_of_hand:":{unicode:["1f91a"],isCanonical:!1},":sparkler:":{unicode:["1f387"],isCanonical:!0},":balloon:":{unicode:["1f388"],isCanonical:!0},":tada:":{unicode:["1f389"],isCanonical:!0},":confetti_ball:":{unicode:["1f38a"],isCanonical:!0},":tanabata_tree:":{unicode:["1f38b"],isCanonical:!0},":crossed_flags:":{unicode:["1f38c"],isCanonical:!0},":call_me:":{unicode:["1f919"],isCanonical:!0},":call_me_hand:":{unicode:["1f919"],isCanonical:!1},":bamboo:":{unicode:["1f38d"],isCanonical:!0},":man_dancing:":{unicode:["1f57a"],isCanonical:!0},":male_dancer:":{unicode:["1f57a"],isCanonical:!1},":dolls:":{unicode:["1f38e"],isCanonical:!0},":selfie:":{unicode:["1f933"],isCanonical:!0},":flags:":{unicode:["1f38f"],isCanonical:!0},":pregnant_woman:":{unicode:["1f930"],isCanonical:!0},":expecting_woman:":{unicode:["1f930"],isCanonical:!1},":wind_chime:":{unicode:["1f390"],isCanonical:!0},":face_palm:":{unicode:["1f926"],isCanonical:!0},":facepalm:":{unicode:["1f926"],isCanonical:!1},":shrug:":{unicode:["1f937"],isCanonical:!0},":rice_scene:":{unicode:["1f391"],isCanonical:!0},":school_satchel:":{unicode:["1f392"],isCanonical:!0},":mortar_board:":{unicode:["1f393"],isCanonical:!0},":carousel_horse:":{unicode:["1f3a0"],isCanonical:!0},":ferris_wheel:":{unicode:["1f3a1"],isCanonical:!0},":roller_coaster:":{unicode:["1f3a2"],isCanonical:!0},":fishing_pole_and_fish:":{unicode:["1f3a3"],isCanonical:!0},":microphone:":{unicode:["1f3a4"],isCanonical:!0},":movie_camera:":{unicode:["1f3a5"],isCanonical:!0},":cinema:":{unicode:["1f3a6"],isCanonical:!0},":headphones:":{unicode:["1f3a7"],isCanonical:!0},":mrs_claus:":{unicode:["1f936"],isCanonical:!0},":mother_christmas:":{unicode:["1f936"],isCanonical:!1},":art:":{unicode:["1f3a8"],isCanonical:!0},":man_in_tuxedo:":{unicode:["1f935"],isCanonical:!0},":tophat:":{unicode:["1f3a9"],isCanonical:!0},":circus_tent:":{unicode:["1f3aa"],isCanonical:!0},":prince:":{unicode:["1f934"],isCanonical:!0},":ticket:":{unicode:["1f3ab"],isCanonical:!0},":clapper:":{unicode:["1f3ac"],isCanonical:!0},":performing_arts:":{unicode:["1f3ad"],isCanonical:!0},":sneezing_face:":{unicode:["1f927"],
    isCanonical:!0},":sneeze:":{unicode:["1f927"],isCanonical:!1},":video_game:":{unicode:["1f3ae"],isCanonical:!0},":dart:":{unicode:["1f3af"],isCanonical:!0},":slot_machine:":{unicode:["1f3b0"],isCanonical:!0},":8ball:":{unicode:["1f3b1"],isCanonical:!0},":game_die:":{unicode:["1f3b2"],isCanonical:!0},":bowling:":{unicode:["1f3b3"],isCanonical:!0},":flower_playing_cards:":{unicode:["1f3b4"],isCanonical:!0},":lying_face:":{unicode:["1f925"],isCanonical:!0},":liar:":{unicode:["1f925"],isCanonical:!1},":musical_note:":{unicode:["1f3b5"],isCanonical:!0},":notes:":{unicode:["1f3b6"],isCanonical:!0},":saxophone:":{unicode:["1f3b7"],isCanonical:!0},":drooling_face:":{unicode:["1f924"],isCanonical:!0},":drool:":{unicode:["1f924"],isCanonical:!1},":guitar:":{unicode:["1f3b8"],isCanonical:!0},":musical_keyboard:":{unicode:["1f3b9"],isCanonical:!0},":trumpet:":{unicode:["1f3ba"],isCanonical:!0},":rofl:":{unicode:["1f923"],isCanonical:!0},":rolling_on_the_floor_laughing:":{unicode:["1f923"],isCanonical:!1},":violin:":{unicode:["1f3bb"],isCanonical:!0},":musical_score:":{unicode:["1f3bc"],isCanonical:!0},":running_shirt_with_sash:":{unicode:["1f3bd"],isCanonical:!0},":nauseated_face:":{unicode:["1f922"],isCanonical:!0},":sick:":{unicode:["1f922"],isCanonical:!1},":tennis:":{unicode:["1f3be"],isCanonical:!0},":ski:":{unicode:["1f3bf"],isCanonical:!0},":basketball:":{unicode:["1f3c0"],isCanonical:!0},":checkered_flag:":{unicode:["1f3c1"],isCanonical:!0},":clown:":{unicode:["1f921"],isCanonical:!0},":clown_face:":{unicode:["1f921"],isCanonical:!1},":snowboarder:":{unicode:["1f3c2"],isCanonical:!0},":runner:":{unicode:["1f3c3"],isCanonical:!0},":surfer:":{unicode:["1f3c4"],isCanonical:!0},":trophy:":{unicode:["1f3c6"],isCanonical:!0},":football:":{unicode:["1f3c8"],isCanonical:!0},":swimmer:":{unicode:["1f3ca"],isCanonical:!0},":house:":{unicode:["1f3e0"],isCanonical:!0},":house_with_garden:":{unicode:["1f3e1"],isCanonical:!0},":office:":{unicode:["1f3e2"],isCanonical:!0},":post_office:":{unicode:["1f3e3"],isCanonical:!0},":hospital:":{unicode:["1f3e5"],isCanonical:!0},":bank:":{unicode:["1f3e6"],isCanonical:!0},":atm:":{unicode:["1f3e7"],isCanonical:!0},":hotel:":{unicode:["1f3e8"],isCanonical:!0},":love_hotel:":{unicode:["1f3e9"],isCanonical:!0},":convenience_store:":{unicode:["1f3ea"],isCanonical:!0},":school:":{unicode:["1f3eb"],isCanonical:!0},":department_store:":{unicode:["1f3ec"],isCanonical:!0},":cowboy:":{unicode:["1f920"],isCanonical:!0},":face_with_cowboy_hat:":{unicode:["1f920"],isCanonical:!1},":factory:":{unicode:["1f3ed"],isCanonical:!0},":izakaya_lantern:":{unicode:["1f3ee"],isCanonical:!0},":japanese_castle:":{unicode:["1f3ef"],isCanonical:!0},":european_castle:":{unicode:["1f3f0"],isCanonical:!0},":snail:":{unicode:["1f40c"],isCanonical:!0},":snake:":{unicode:["1f40d"],isCanonical:!0},":racehorse:":{unicode:["1f40e"],isCanonical:!0},":sheep:":{unicode:["1f411"],isCanonical:!0},":monkey:":{unicode:["1f412"],isCanonical:!0},":chicken:":{unicode:["1f414"],isCanonical:!0},":boar:":{unicode:["1f417"],isCanonical:!0},":elephant:":{unicode:["1f418"],isCanonical:!0},":octopus:":{unicode:["1f419"],isCanonical:!0},":shell:":{unicode:["1f41a"],isCanonical:!0},":bug:":{unicode:["1f41b"],isCanonical:!0},":ant:":{unicode:["1f41c"],isCanonical:!0},":bee:":{unicode:["1f41d"],isCanonical:!0},":beetle:":{unicode:["1f41e"],isCanonical:!0},":fish:":{unicode:["1f41f"],isCanonical:!0},":tropical_fish:":{unicode:["1f420"],isCanonical:!0},":blowfish:":{unicode:["1f421"],isCanonical:!0},":turtle:":{unicode:["1f422"],isCanonical:!0},":hatching_chick:":{unicode:["1f423"],isCanonical:!0},":baby_chick:":{unicode:["1f424"],isCanonical:!0},":hatched_chick:":{unicode:["1f425"],isCanonical:!0},":bird:":{unicode:["1f426"],isCanonical:!0},":penguin:":{unicode:["1f427"],isCanonical:!0},":koala:":{unicode:["1f428"],isCanonical:!0},":poodle:":{unicode:["1f429"],isCanonical:!0},":camel:":{unicode:["1f42b"],isCanonical:!0},":dolphin:":{unicode:["1f42c"],isCanonical:!0},":mouse:":{unicode:["1f42d"],isCanonical:!0},":cow:":{unicode:["1f42e"],isCanonical:!0},":tiger:":{unicode:["1f42f"],isCanonical:!0},":rabbit:":{unicode:["1f430"],isCanonical:!0},":cat:":{unicode:["1f431"],isCanonical:!0},":dragon_face:":{unicode:["1f432"],isCanonical:!0},":whale:":{unicode:["1f433"],isCanonical:!0},":horse:":{unicode:["1f434"],isCanonical:!0},":monkey_face:":{unicode:["1f435"],isCanonical:!0},":dog:":{unicode:["1f436"],isCanonical:!0},":pig:":{unicode:["1f437"],isCanonical:!0},":frog:":{unicode:["1f438"],isCanonical:!0},":hamster:":{unicode:["1f439"],isCanonical:!0},":wolf:":{unicode:["1f43a"],isCanonical:!0},":bear:":{unicode:["1f43b"],isCanonical:!0},":panda_face:":{unicode:["1f43c"],isCanonical:!0},":pig_nose:":{unicode:["1f43d"],isCanonical:!0},":feet:":{unicode:["1f43e"],isCanonical:!0},":paw_prints:":{unicode:["1f43e"],isCanonical:!1},":eyes:":{unicode:["1f440"],isCanonical:!0},":ear:":{unicode:["1f442"],isCanonical:!0},":nose:":{unicode:["1f443"],isCanonical:!0},":lips:":{unicode:["1f444"],isCanonical:!0},":tongue:":{unicode:["1f445"],isCanonical:!0},":point_up_2:":{unicode:["1f446"],isCanonical:!0},":point_down:":{unicode:["1f447"],isCanonical:!0},":point_left:":{unicode:["1f448"],isCanonical:!0},":point_right:":{unicode:["1f449"],isCanonical:!0},":punch:":{unicode:["1f44a"],isCanonical:!0},":wave:":{unicode:["1f44b"],isCanonical:!0},":ok_hand:":{unicode:["1f44c"],isCanonical:!0},":thumbsup:":{unicode:["1f44d"],isCanonical:!0},":+1:":{unicode:["1f44d"],isCanonical:!1},":thumbup:":{unicode:["1f44d"],isCanonical:!1},":thumbsdown:":{unicode:["1f44e"],isCanonical:!0},":-1:":{unicode:["1f44e"],isCanonical:!1},":thumbdown:":{unicode:["1f44e"],isCanonical:!1},":clap:":{unicode:["1f44f"],isCanonical:!0},":open_hands:":{unicode:["1f450"],isCanonical:!0},":crown:":{unicode:["1f451"],isCanonical:!0},":womans_hat:":{unicode:["1f452"],isCanonical:!0},":eyeglasses:":{unicode:["1f453"],isCanonical:!0},":necktie:":{unicode:["1f454"],isCanonical:!0},":shirt:":{unicode:["1f455"],isCanonical:!0},":jeans:":{unicode:["1f456"],isCanonical:!0},":dress:":{unicode:["1f457"],isCanonical:!0},":kimono:":{unicode:["1f458"],isCanonical:!0},":bikini:":{unicode:["1f459"],isCanonical:!0},":womans_clothes:":{unicode:["1f45a"],isCanonical:!0},":purse:":{unicode:["1f45b"],isCanonical:!0},":handbag:":{unicode:["1f45c"],isCanonical:!0},":pouch:":{unicode:["1f45d"],isCanonical:!0},":mans_shoe:":{unicode:["1f45e"],isCanonical:!0},":athletic_shoe:":{unicode:["1f45f"],isCanonical:!0},":high_heel:":{unicode:["1f460"],isCanonical:!0},":sandal:":{unicode:["1f461"],isCanonical:!0},":boot:":{unicode:["1f462"],isCanonical:!0},":footprints:":{unicode:["1f463"],isCanonical:!0},":bust_in_silhouette:":{unicode:["1f464"],isCanonical:!0},":boy:":{unicode:["1f466"],isCanonical:!0},":girl:":{unicode:["1f467"],isCanonical:!0},":man:":{unicode:["1f468"],isCanonical:!0},":woman:":{unicode:["1f469"],isCanonical:!0},":family:":{unicode:["1f46a"],isCanonical:!0},":couple:":{unicode:["1f46b"],isCanonical:!0},":cop:":{unicode:["1f46e"],isCanonical:!0},":dancers:":{unicode:["1f46f"],isCanonical:!0},":bride_with_veil:":{unicode:["1f470"],isCanonical:!0},":person_with_blond_hair:":{unicode:["1f471"],isCanonical:!0},":man_with_gua_pi_mao:":{unicode:["1f472"],isCanonical:!0},":man_with_turban:":{unicode:["1f473"],isCanonical:!0},":older_man:":{unicode:["1f474"],isCanonical:!0},":older_woman:":{unicode:["1f475"],isCanonical:!0},":grandma:":{unicode:["1f475"],isCanonical:!1},":baby:":{unicode:["1f476"],isCanonical:!0},":construction_worker:":{unicode:["1f477"],isCanonical:!0},":princess:":{unicode:["1f478"],isCanonical:!0},":japanese_ogre:":{unicode:["1f479"],isCanonical:!0},":japanese_goblin:":{unicode:["1f47a"],isCanonical:!0},":ghost:":{unicode:["1f47b"],isCanonical:!0},":angel:":{unicode:["1f47c"],isCanonical:!0},":alien:":{unicode:["1f47d"],isCanonical:!0},":space_invader:":{unicode:["1f47e"],isCanonical:!0},":imp:":{unicode:["1f47f"],isCanonical:!0},":skull:":{unicode:["1f480"],isCanonical:!0},":skeleton:":{unicode:["1f480"],isCanonical:!1},":card_index:":{unicode:["1f4c7"],isCanonical:!0},":information_desk_person:":{unicode:["1f481"],isCanonical:!0},":guardsman:":{unicode:["1f482"],isCanonical:!0},":dancer:":{unicode:["1f483"],isCanonical:!0},":lipstick:":{unicode:["1f484"],isCanonical:!0},":nail_care:":{unicode:["1f485"],isCanonical:!0},":ledger:":{unicode:["1f4d2"],isCanonical:!0},":massage:":{unicode:["1f486"],isCanonical:!0},":notebook:":{unicode:["1f4d3"],isCanonical:!0},":haircut:":{unicode:["1f487"],isCanonical:!0},":notebook_with_decorative_cover:":{unicode:["1f4d4"],isCanonical:!0},":barber:":{unicode:["1f488"],isCanonical:!0},":closed_book:":{unicode:["1f4d5"],isCanonical:!0},":syringe:":{unicode:["1f489"],isCanonical:!0},":book:":{unicode:["1f4d6"],isCanonical:!0},":pill:":{unicode:["1f48a"],isCanonical:!0},":green_book:":{unicode:["1f4d7"],isCanonical:!0},":kiss:":{unicode:["1f48b"],isCanonical:!0},":blue_book:":{unicode:["1f4d8"],isCanonical:!0},":love_letter:":{unicode:["1f48c"],isCanonical:!0},":orange_book:":{unicode:["1f4d9"],isCanonical:!0},":ring:":{unicode:["1f48d"],isCanonical:!0},":books:":{unicode:["1f4da"],isCanonical:!0},":gem:":{unicode:["1f48e"],isCanonical:!0},":name_badge:":{unicode:["1f4db"],isCanonical:!0},":couplekiss:":{unicode:["1f48f"],isCanonical:!0},":scroll:":{unicode:["1f4dc"],isCanonical:!0},":bouquet:":{unicode:["1f490"],isCanonical:!0},":pencil:":{unicode:["1f4dd"],isCanonical:!0},":couple_with_heart:":{unicode:["1f491"],isCanonical:!0},":telephone_receiver:":{unicode:["1f4de"],isCanonical:!0},":wedding:":{unicode:["1f492"],isCanonical:!0},":pager:":{unicode:["1f4df"],isCanonical:!0},":fax:":{unicode:["1f4e0"],isCanonical:!0},":heartbeat:":{unicode:["1f493"],isCanonical:!0},":satellite:":{unicode:["1f4e1"],isCanonical:!0},":loudspeaker:":{unicode:["1f4e2"],isCanonical:!0},":broken_heart:":{unicode:["1f494"],isCanonical:!0},":mega:":{unicode:["1f4e3"],isCanonical:!0},":outbox_tray:":{unicode:["1f4e4"],isCanonical:!0},":two_hearts:":{unicode:["1f495"],isCanonical:!0},":inbox_tray:":{unicode:["1f4e5"],isCanonical:!0},":package:":{unicode:["1f4e6"],isCanonical:!0},":sparkling_heart:":{unicode:["1f496"],isCanonical:!0},":e-mail:":{unicode:["1f4e7"],isCanonical:!0},":email:":{unicode:["1f4e7"],isCanonical:!1},":incoming_envelope:":{unicode:["1f4e8"],isCanonical:!0},":heartpulse:":{unicode:["1f497"],isCanonical:!0},":envelope_with_arrow:":{unicode:["1f4e9"],isCanonical:!0},":mailbox_closed:":{unicode:["1f4ea"],isCanonical:!0},":cupid:":{unicode:["1f498"],isCanonical:!0},":mailbox:":{unicode:["1f4eb"],isCanonical:!0},":postbox:":{unicode:["1f4ee"],isCanonical:!0},":blue_heart:":{unicode:["1f499"],isCanonical:!0},":newspaper:":{unicode:["1f4f0"],isCanonical:!0},":iphone:":{unicode:["1f4f1"],isCanonical:!0},":green_heart:":{unicode:["1f49a"],isCanonical:!0},":calling:":{unicode:["1f4f2"],isCanonical:!0},":vibration_mode:":{unicode:["1f4f3"],isCanonical:!0},":yellow_heart:":{unicode:["1f49b"],isCanonical:!0},":mobile_phone_off:":{unicode:["1f4f4"],isCanonical:!0},":signal_strength:":{unicode:["1f4f6"],isCanonical:!0},":purple_heart:":{unicode:["1f49c"],isCanonical:!0},":camera:":{unicode:["1f4f7"],isCanonical:!0},":video_camera:":{unicode:["1f4f9"],isCanonical:!0},":gift_heart:":{unicode:["1f49d"],isCanonical:!0},":tv:":{unicode:["1f4fa"],isCanonical:!0},":radio:":{unicode:["1f4fb"],isCanonical:!0},":revolving_hearts:":{unicode:["1f49e"],isCanonical:!0},":vhs:":{unicode:["1f4fc"],isCanonical:!0},":arrows_clockwise:":{unicode:["1f503"],isCanonical:!0},":heart_decoration:":{unicode:["1f49f"],isCanonical:!0},":loud_sound:":{unicode:["1f50a"],isCanonical:!0},":battery:":{unicode:["1f50b"],isCanonical:!0},":diamond_shape_with_a_dot_inside:":{unicode:["1f4a0"],isCanonical:!0},":electric_plug:":{unicode:["1f50c"],isCanonical:!0},":mag:":{unicode:["1f50d"],isCanonical:!0},":bulb:":{unicode:["1f4a1"],isCanonical:!0},":mag_right:":{unicode:["1f50e"],isCanonical:!0},":lock_with_ink_pen:":{unicode:["1f50f"],isCanonical:!0},":anger:":{unicode:["1f4a2"],isCanonical:!0},":closed_lock_with_key:":{unicode:["1f510"],isCanonical:!0},":key:":{unicode:["1f511"],isCanonical:!0},":bomb:":{unicode:["1f4a3"],isCanonical:!0},":lock:":{unicode:["1f512"],isCanonical:!0},":unlock:":{unicode:["1f513"],isCanonical:!0},":zzz:":{unicode:["1f4a4"],isCanonical:!0},":bell:":{unicode:["1f514"],isCanonical:!0},":bookmark:":{unicode:["1f516"],isCanonical:!0},":boom:":{unicode:["1f4a5"],isCanonical:!0},":link:":{unicode:["1f517"],isCanonical:!0},":radio_button:":{unicode:["1f518"],isCanonical:!0},":sweat_drops:":{unicode:["1f4a6"],isCanonical:!0},":back:":{unicode:["1f519"],isCanonical:!0},":end:":{unicode:["1f51a"],isCanonical:!0},":droplet:":{unicode:["1f4a7"],isCanonical:!0},":on:":{unicode:["1f51b"],isCanonical:!0},":soon:":{unicode:["1f51c"],isCanonical:!0},":dash:":{unicode:["1f4a8"],isCanonical:!0},":top:":{unicode:["1f51d"],isCanonical:!0},":underage:":{unicode:["1f51e"],isCanonical:!0},":poop:":{unicode:["1f4a9"],isCanonical:!0},":shit:":{unicode:["1f4a9"],isCanonical:!1},":hankey:":{unicode:["1f4a9"],isCanonical:!1},":poo:":{unicode:["1f4a9"],isCanonical:!1},":keycap_ten:":{unicode:["1f51f"],isCanonical:!0},":muscle:":{unicode:["1f4aa"],isCanonical:!0},":capital_abcd:":{unicode:["1f520"],isCanonical:!0},":abcd:":{unicode:["1f521"],isCanonical:!0},":dizzy:":{unicode:["1f4ab"],isCanonical:!0},":1234:":{unicode:["1f522"],isCanonical:!0},":symbols:":{unicode:["1f523"],isCanonical:!0},":speech_balloon:":{unicode:["1f4ac"],isCanonical:!0},":abc:":{unicode:["1f524"],isCanonical:!0},":fire:":{unicode:["1f525"],isCanonical:!0},":flame:":{unicode:["1f525"],isCanonical:!1},":white_flower:":{unicode:["1f4ae"],isCanonical:!0},":flashlight:":{unicode:["1f526"],isCanonical:!0},":wrench:":{unicode:["1f527"],isCanonical:!0},":100:":{unicode:["1f4af"],isCanonical:!0},":hammer:":{unicode:["1f528"],isCanonical:!0},":nut_and_bolt:":{unicode:["1f529"],isCanonical:!0},":moneybag:":{unicode:["1f4b0"],isCanonical:!0},":knife:":{unicode:["1f52a"],isCanonical:!0},":gun:":{unicode:["1f52b"],isCanonical:!0},":currency_exchange:":{unicode:["1f4b1"],isCanonical:!0},":crystal_ball:":{unicode:["1f52e"],isCanonical:!0},":heavy_dollar_sign:":{unicode:["1f4b2"],isCanonical:!0},":six_pointed_star:":{unicode:["1f52f"],isCanonical:!0},":credit_card:":{unicode:["1f4b3"],isCanonical:!0},":beginner:":{unicode:["1f530"],isCanonical:!0},":trident:":{unicode:["1f531"],isCanonical:!0},":yen:":{unicode:["1f4b4"],isCanonical:!0},":black_square_button:":{unicode:["1f532"],isCanonical:!0},":white_square_button:":{unicode:["1f533"],isCanonical:!0},":dollar:":{unicode:["1f4b5"],isCanonical:!0},":red_circle:":{unicode:["1f534"],isCanonical:!0},":large_blue_circle:":{unicode:["1f535"],isCanonical:!0},":money_with_wings:":{unicode:["1f4b8"],isCanonical:!0},":large_orange_diamond:":{unicode:["1f536"],isCanonical:!0},":large_blue_diamond:":{unicode:["1f537"],isCanonical:!0},":chart:":{unicode:["1f4b9"],isCanonical:!0},":small_orange_diamond:":{unicode:["1f538"],isCanonical:!0},":small_blue_diamond:":{unicode:["1f539"],isCanonical:!0},":seat:":{unicode:["1f4ba"],isCanonical:!0},":small_red_triangle:":{unicode:["1f53a"],isCanonical:!0},":small_red_triangle_down:":{unicode:["1f53b"],isCanonical:!0},":computer:":{unicode:["1f4bb"],isCanonical:!0},":arrow_up_small:":{unicode:["1f53c"],isCanonical:!0},":briefcase:":{unicode:["1f4bc"],isCanonical:!0},":arrow_down_small:":{unicode:["1f53d"],isCanonical:!0},":clock1:":{unicode:["1f550"],isCanonical:!0},":minidisc:":{unicode:["1f4bd"],isCanonical:!0},":clock2:":{unicode:["1f551"],isCanonical:!0},":floppy_disk:":{unicode:["1f4be"],isCanonical:!0},":clock3:":{unicode:["1f552"],isCanonical:!0},":cd:":{unicode:["1f4bf"],isCanonical:!0},":clock4:":{unicode:["1f553"],isCanonical:!0},":dvd:":{unicode:["1f4c0"],isCanonical:!0},":clock5:":{unicode:["1f554"],isCanonical:!0},":clock6:":{unicode:["1f555"],isCanonical:!0},":file_folder:":{unicode:["1f4c1"],isCanonical:!0},":clock7:":{unicode:["1f556"],isCanonical:!0},":clock8:":{unicode:["1f557"],isCanonical:!0},":open_file_folder:":{unicode:["1f4c2"],isCanonical:!0},":clock9:":{unicode:["1f558"],isCanonical:!0},":clock10:":{unicode:["1f559"],isCanonical:!0},":page_with_curl:":{unicode:["1f4c3"],isCanonical:!0},":clock11:":{unicode:["1f55a"],isCanonical:!0},":clock12:":{unicode:["1f55b"],isCanonical:!0},":page_facing_up:":{unicode:["1f4c4"],isCanonical:!0},":mount_fuji:":{unicode:["1f5fb"],isCanonical:!0},":tokyo_tower:":{unicode:["1f5fc"],isCanonical:!0},":date:":{unicode:["1f4c5"],isCanonical:!0},":statue_of_liberty:":{unicode:["1f5fd"],isCanonical:!0},":japan:":{unicode:["1f5fe"],isCanonical:!0},":calendar:":{unicode:["1f4c6"],isCanonical:!0},":moyai:":{unicode:["1f5ff"],isCanonical:!0},":grin:":{unicode:["1f601"],isCanonical:!0},":joy:":{unicode:["1f602"],isCanonical:!0},":smiley:":{unicode:["1f603"],isCanonical:!0},":chart_with_upwards_trend:":{unicode:["1f4c8"],isCanonical:!0},":smile:":{unicode:["1f604"],isCanonical:!0},":sweat_smile:":{unicode:["1f605"],isCanonical:!0},":chart_with_downwards_trend:":{unicode:["1f4c9"],isCanonical:!0},":laughing:":{unicode:["1f606"],isCanonical:!0},":satisfied:":{unicode:["1f606"],isCanonical:!1},":wink:":{unicode:["1f609"],isCanonical:!0},":bar_chart:":{unicode:["1f4ca"],isCanonical:!0},":blush:":{unicode:["1f60a"],isCanonical:!0},":yum:":{unicode:["1f60b"],isCanonical:!0},":clipboard:":{unicode:["1f4cb"],isCanonical:!0},":relieved:":{unicode:["1f60c"],isCanonical:!0},":heart_eyes:":{unicode:["1f60d"],isCanonical:!0},":pushpin:":{unicode:["1f4cc"],isCanonical:!0},":smirk:":{unicode:["1f60f"],isCanonical:!0},":unamused:":{unicode:["1f612"],isCanonical:!0},":round_pushpin:":{unicode:["1f4cd"],isCanonical:!0},":sweat:":{unicode:["1f613"],isCanonical:!0},":pensive:":{unicode:["1f614"],isCanonical:!0},":paperclip:":{unicode:["1f4ce"],isCanonical:!0},":confounded:":{unicode:["1f616"],isCanonical:!0},":kissing_heart:":{unicode:["1f618"],isCanonical:!0},":straight_ruler:":{unicode:["1f4cf"],isCanonical:!0},":kissing_closed_eyes:":{unicode:["1f61a"],isCanonical:!0},":stuck_out_tongue_winking_eye:":{unicode:["1f61c"],isCanonical:!0},":triangular_ruler:":{unicode:["1f4d0"],isCanonical:!0},":stuck_out_tongue_closed_eyes:":{unicode:["1f61d"],isCanonical:!0},":disappointed:":{unicode:["1f61e"],isCanonical:!0},":bookmark_tabs:":{unicode:["1f4d1"],isCanonical:!0},":angry:":{unicode:["1f620"],isCanonical:!0},":rage:":{unicode:["1f621"],isCanonical:!0},":cry:":{unicode:["1f622"],isCanonical:!0},":persevere:":{unicode:["1f623"],isCanonical:!0},":triumph:":{unicode:["1f624"],isCanonical:!0},":disappointed_relieved:":{unicode:["1f625"],isCanonical:!0},":fearful:":{unicode:["1f628"],isCanonical:!0},":weary:":{unicode:["1f629"],isCanonical:!0},":sleepy:":{unicode:["1f62a"],isCanonical:!0},":tired_face:":{unicode:["1f62b"],isCanonical:!0},":sob:":{unicode:["1f62d"],isCanonical:!0},":cold_sweat:":{unicode:["1f630"],isCanonical:!0},":scream:":{unicode:["1f631"],isCanonical:!0},":astonished:":{unicode:["1f632"],isCanonical:!0},":flushed:":{unicode:["1f633"],isCanonical:!0},":dizzy_face:":{unicode:["1f635"],isCanonical:!0},":mask:":{unicode:["1f637"],isCanonical:!0},":smile_cat:":{unicode:["1f638"],isCanonical:!0},":joy_cat:":{unicode:["1f639"],isCanonical:!0},":smiley_cat:":{unicode:["1f63a"],isCanonical:!0},":heart_eyes_cat:":{unicode:["1f63b"],isCanonical:!0},":smirk_cat:":{unicode:["1f63c"],isCanonical:!0},":kissing_cat:":{unicode:["1f63d"],isCanonical:!0},":pouting_cat:":{unicode:["1f63e"],isCanonical:!0},":crying_cat_face:":{unicode:["1f63f"],isCanonical:!0},":scream_cat:":{unicode:["1f640"],isCanonical:!0},":no_good:":{unicode:["1f645"],isCanonical:!0},":ok_woman:":{unicode:["1f646"],isCanonical:!0},":bow:":{unicode:["1f647"],isCanonical:!0},":see_no_evil:":{unicode:["1f648"],isCanonical:!0},":hear_no_evil:":{unicode:["1f649"],isCanonical:!0},":speak_no_evil:":{unicode:["1f64a"],isCanonical:!0},":raising_hand:":{unicode:["1f64b"],isCanonical:!0},":raised_hands:":{unicode:["1f64c"],isCanonical:!0},":person_frowning:":{unicode:["1f64d"],isCanonical:!0},":person_with_pouting_face:":{unicode:["1f64e"],isCanonical:!0},":pray:":{unicode:["1f64f"],isCanonical:!0},":rocket:":{unicode:["1f680"],isCanonical:!0},":railway_car:":{unicode:["1f683"],isCanonical:!0},":bullettrain_side:":{unicode:["1f684"],isCanonical:!0},":bullettrain_front:":{unicode:["1f685"],isCanonical:!0},":metro:":{unicode:["1f687"],isCanonical:!0},":station:":{unicode:["1f689"],isCanonical:!0},":bus:":{unicode:["1f68c"],isCanonical:!0},":busstop:":{unicode:["1f68f"],isCanonical:!0},":ambulance:":{unicode:["1f691"],isCanonical:!0},":fire_engine:":{unicode:["1f692"],isCanonical:!0},":police_car:":{unicode:["1f693"],isCanonical:!0},":taxi:":{unicode:["1f695"],isCanonical:!0},":red_car:":{unicode:["1f697"],isCanonical:!0},":blue_car:":{unicode:["1f699"],isCanonical:!0},":truck:":{unicode:["1f69a"],isCanonical:!0},":ship:":{unicode:["1f6a2"],isCanonical:!0},":speedboat:":{unicode:["1f6a4"],isCanonical:!0},":traffic_light:":{unicode:["1f6a5"],isCanonical:!0},":construction:":{unicode:["1f6a7"],isCanonical:!0},":rotating_light:":{unicode:["1f6a8"],isCanonical:!0},":triangular_flag_on_post:":{unicode:["1f6a9"],isCanonical:!0},":door:":{unicode:["1f6aa"],isCanonical:!0},":no_entry_sign:":{unicode:["1f6ab"],isCanonical:!0},":smoking:":{unicode:["1f6ac"],isCanonical:!0},":no_smoking:":{unicode:["1f6ad"],isCanonical:!0},":bike:":{unicode:["1f6b2"],isCanonical:!0},":walking:":{unicode:["1f6b6"],isCanonical:!0},":mens:":{unicode:["1f6b9"],isCanonical:!0},":womens:":{unicode:["1f6ba"],isCanonical:!0},":restroom:":{unicode:["1f6bb"],isCanonical:!0},":baby_symbol:":{unicode:["1f6bc"],isCanonical:!0},":toilet:":{unicode:["1f6bd"],isCanonical:!0},":wc:":{unicode:["1f6be"],isCanonical:!0},":bath:":{unicode:["1f6c0"],isCanonical:!0},":metal:":{unicode:["1f918"],isCanonical:!0},":sign_of_the_horns:":{unicode:["1f918"],isCanonical:!1},":grinning:":{unicode:["1f600"],isCanonical:!0},":innocent:":{unicode:["1f607"],isCanonical:!0},":smiling_imp:":{unicode:["1f608"],isCanonical:!0},":sunglasses:":{unicode:["1f60e"],isCanonical:!0},":neutral_face:":{unicode:["1f610"],isCanonical:!0},":expressionless:":{unicode:["1f611"],isCanonical:!0},":confused:":{unicode:["1f615"],isCanonical:!0},":kissing:":{unicode:["1f617"],isCanonical:!0},":kissing_smiling_eyes:":{unicode:["1f619"],isCanonical:!0},":stuck_out_tongue:":{unicode:["1f61b"],isCanonical:!0},":worried:":{unicode:["1f61f"],isCanonical:!0},":frowning:":{unicode:["1f626"],isCanonical:!0},":anguished:":{unicode:["1f627"],isCanonical:!0},":grimacing:":{unicode:["1f62c"],isCanonical:!0},":open_mouth:":{unicode:["1f62e"],isCanonical:!0},":hushed:":{unicode:["1f62f"],isCanonical:!0},":sleeping:":{unicode:["1f634"],isCanonical:!0},":no_mouth:":{unicode:["1f636"],isCanonical:!0},":helicopter:":{unicode:["1f681"],isCanonical:!0},":steam_locomotive:":{unicode:["1f682"],isCanonical:!0},":train2:":{unicode:["1f686"],isCanonical:!0},":light_rail:":{unicode:["1f688"],isCanonical:!0},":tram:":{unicode:["1f68a"],isCanonical:!0},":oncoming_bus:":{unicode:["1f68d"],isCanonical:!0},":trolleybus:":{unicode:["1f68e"],isCanonical:!0},":minibus:":{unicode:["1f690"],isCanonical:!0},":oncoming_police_car:":{unicode:["1f694"],isCanonical:!0},":oncoming_taxi:":{unicode:["1f696"],isCanonical:!0},":oncoming_automobile:":{unicode:["1f698"],isCanonical:!0},":articulated_lorry:":{unicode:["1f69b"],isCanonical:!0},":tractor:":{unicode:["1f69c"],isCanonical:!0},":monorail:":{unicode:["1f69d"],isCanonical:!0},":mountain_railway:":{unicode:["1f69e"],isCanonical:!0},":suspension_railway:":{unicode:["1f69f"],isCanonical:!0},":mountain_cableway:":{unicode:["1f6a0"],isCanonical:!0},":aerial_tramway:":{unicode:["1f6a1"],isCanonical:!0},":rowboat:":{unicode:["1f6a3"],isCanonical:!0},":vertical_traffic_light:":{unicode:["1f6a6"],isCanonical:!0},":put_litter_in_its_place:":{unicode:["1f6ae"],isCanonical:!0},":do_not_litter:":{unicode:["1f6af"],isCanonical:!0},":potable_water:":{unicode:["1f6b0"],isCanonical:!0},":non-potable_water:":{unicode:["1f6b1"],isCanonical:!0},":no_bicycles:":{unicode:["1f6b3"],isCanonical:!0},":bicyclist:":{unicode:["1f6b4"],isCanonical:!0},":mountain_bicyclist:":{unicode:["1f6b5"],isCanonical:!0},":no_pedestrians:":{unicode:["1f6b7"],isCanonical:!0},":children_crossing:":{unicode:["1f6b8"],isCanonical:!0},":shower:":{unicode:["1f6bf"],isCanonical:!0},":bathtub:":{unicode:["1f6c1"],isCanonical:!0},":passport_control:":{unicode:["1f6c2"],isCanonical:!0},":customs:":{unicode:["1f6c3"],isCanonical:!0},":baggage_claim:":{unicode:["1f6c4"],isCanonical:!0},":left_luggage:":{unicode:["1f6c5"],isCanonical:!0},":earth_africa:":{unicode:["1f30d"],isCanonical:!0},":earth_americas:":{unicode:["1f30e"],isCanonical:!0},":globe_with_meridians:":{unicode:["1f310"],isCanonical:!0},":waxing_crescent_moon:":{unicode:["1f312"],isCanonical:!0},":waning_gibbous_moon:":{unicode:["1f316"],isCanonical:!0},":last_quarter_moon:":{unicode:["1f317"],isCanonical:!0},":waning_crescent_moon:":{unicode:["1f318"],isCanonical:!0},":new_moon_with_face:":{unicode:["1f31a"],isCanonical:!0},":last_quarter_moon_with_face:":{unicode:["1f31c"],isCanonical:!0},":full_moon_with_face:":{unicode:["1f31d"],isCanonical:!0},":sun_with_face:":{unicode:["1f31e"],isCanonical:!0},":evergreen_tree:":{unicode:["1f332"],isCanonical:!0},":deciduous_tree:":{unicode:["1f333"],isCanonical:!0},":lemon:":{unicode:["1f34b"],isCanonical:!0},":pear:":{unicode:["1f350"],isCanonical:!0},":baby_bottle:":{unicode:["1f37c"],isCanonical:!0},":horse_racing:":{unicode:["1f3c7"],isCanonical:!0},":rugby_football:":{unicode:["1f3c9"],isCanonical:!0},":european_post_office:":{unicode:["1f3e4"],isCanonical:!0},":rat:":{unicode:["1f400"],isCanonical:!0},":mouse2:":{unicode:["1f401"],isCanonical:!0},":ox:":{unicode:["1f402"],isCanonical:!0},":water_buffalo:":{unicode:["1f403"],isCanonical:!0},":cow2:":{unicode:["1f404"],isCanonical:!0},":tiger2:":{unicode:["1f405"],isCanonical:!0},":leopard:":{unicode:["1f406"],isCanonical:!0},":rabbit2:":{unicode:["1f407"],isCanonical:!0},":cat2:":{unicode:["1f408"],isCanonical:!0},":dragon:":{unicode:["1f409"],isCanonical:!0},":crocodile:":{unicode:["1f40a"],isCanonical:!0},":whale2:":{unicode:["1f40b"],isCanonical:!0},":ram:":{unicode:["1f40f"],isCanonical:!0},":goat:":{unicode:["1f410"],isCanonical:!0},":rooster:":{unicode:["1f413"],isCanonical:!0},":dog2:":{unicode:["1f415"],isCanonical:!0},":pig2:":{unicode:["1f416"],isCanonical:!0},":dromedary_camel:":{unicode:["1f42a"],isCanonical:!0},":busts_in_silhouette:":{unicode:["1f465"],isCanonical:!0},":two_men_holding_hands:":{unicode:["1f46c"],isCanonical:!0},":two_women_holding_hands:":{unicode:["1f46d"],isCanonical:!0},":thought_balloon:":{unicode:["1f4ad"],isCanonical:!0},":euro:":{unicode:["1f4b6"],isCanonical:!0},":pound:":{unicode:["1f4b7"],isCanonical:!0},":mailbox_with_mail:":{unicode:["1f4ec"],isCanonical:!0},":mailbox_with_no_mail:":{unicode:["1f4ed"],isCanonical:!0},":postal_horn:":{unicode:["1f4ef"],isCanonical:!0},":no_mobile_phones:":{unicode:["1f4f5"],isCanonical:!0},":twisted_rightwards_arrows:":{unicode:["1f500"],isCanonical:!0},":repeat:":{unicode:["1f501"],isCanonical:!0},":repeat_one:":{unicode:["1f502"],isCanonical:!0},":arrows_counterclockwise:":{unicode:["1f504"],isCanonical:!0},":low_brightness:":{unicode:["1f505"],isCanonical:!0},":high_brightness:":{unicode:["1f506"],isCanonical:!0},":mute:":{unicode:["1f507"],isCanonical:!0},":sound:":{unicode:["1f509"],isCanonical:!0},":no_bell:":{unicode:["1f515"],isCanonical:!0},":microscope:":{unicode:["1f52c"],isCanonical:!0},":telescope:":{unicode:["1f52d"],isCanonical:!0},":clock130:":{unicode:["1f55c"],isCanonical:!0},":clock230:":{unicode:["1f55d"],isCanonical:!0},":clock330:":{unicode:["1f55e"],isCanonical:!0},":clock430:":{unicode:["1f55f"],isCanonical:!0},":clock530:":{unicode:["1f560"],isCanonical:!0},":clock630:":{unicode:["1f561"],isCanonical:!0},":clock730:":{unicode:["1f562"],isCanonical:!0},":clock830:":{unicode:["1f563"],isCanonical:!0},":clock930:":{unicode:["1f564"],isCanonical:!0},":clock1030:":{unicode:["1f565"],isCanonical:!0},":clock1130:":{unicode:["1f566"],isCanonical:!0},":clock1230:":{unicode:["1f567"],isCanonical:!0},":speaker:":{unicode:["1f508"],isCanonical:!0},":train:":{unicode:["1f68b"],isCanonical:!0},":medal:":{unicode:["1f3c5"],isCanonical:!0},":sports_medal:":{unicode:["1f3c5"],isCanonical:!1},":flag_black:":{unicode:["1f3f4"],isCanonical:!0},":waving_black_flag:":{unicode:["1f3f4"],isCanonical:!1},":camera_with_flash:":{unicode:["1f4f8"],isCanonical:!0},":sleeping_accommodation:":{unicode:["1f6cc"],isCanonical:!0},":middle_finger:":{unicode:["1f595"],isCanonical:!0},":reversed_hand_with_middle_finger_extended:":{unicode:["1f595"],isCanonical:!1},":vulcan:":{unicode:["1f596"],isCanonical:!0},":raised_hand_with_part_between_middle_and_ring_fingers:":{unicode:["1f596"],isCanonical:!1},":slight_frown:":{unicode:["1f641"],isCanonical:!0},":slightly_frowning_face:":{unicode:["1f641"],isCanonical:!1},":slight_smile:":{unicode:["1f642"],isCanonical:!0},":slightly_smiling_face:":{unicode:["1f642"],isCanonical:!1},":airplane_departure:":{unicode:["1f6eb"],isCanonical:!0},":airplane_arriving:":{unicode:["1f6ec"],isCanonical:!0},":tone1:":{unicode:["1f3fb"],isCanonical:!0},":tone2:":{unicode:["1f3fc"],isCanonical:!0},":tone3:":{unicode:["1f3fd"],isCanonical:!0},":tone4:":{unicode:["1f3fe"],isCanonical:!0},":tone5:":{unicode:["1f3ff"],isCanonical:!0},":upside_down:":{unicode:["1f643"],isCanonical:!0},":upside_down_face:":{unicode:["1f643"],isCanonical:!1},":money_mouth:":{unicode:["1f911"],isCanonical:!0},":money_mouth_face:":{unicode:["1f911"],isCanonical:!1},":nerd:":{unicode:["1f913"],isCanonical:!0},":nerd_face:":{unicode:["1f913"],isCanonical:!1},":hugging:":{unicode:["1f917"],isCanonical:!0},":hugging_face:":{unicode:["1f917"],isCanonical:!1},":rolling_eyes:":{unicode:["1f644"],isCanonical:!0},":face_with_rolling_eyes:":{unicode:["1f644"],isCanonical:!1},":thinking:":{unicode:["1f914"],isCanonical:!0},":thinking_face:":{unicode:["1f914"],isCanonical:!1},":zipper_mouth:":{unicode:["1f910"],isCanonical:!0},":zipper_mouth_face:":{unicode:["1f910"],isCanonical:!1},":thermometer_face:":{unicode:["1f912"],isCanonical:!0},":face_with_thermometer:":{unicode:["1f912"],isCanonical:!1},":head_bandage:":{unicode:["1f915"],isCanonical:!0},":face_with_head_bandage:":{unicode:["1f915"],isCanonical:!1},":robot:":{unicode:["1f916"],isCanonical:!0},":robot_face:":{unicode:["1f916"],isCanonical:!1},":lion_face:":{unicode:["1f981"],isCanonical:!0},":lion:":{unicode:["1f981"],isCanonical:!1},":unicorn:":{unicode:["1f984"],isCanonical:!0},":unicorn_face:":{unicode:["1f984"],isCanonical:!1},":scorpion:":{unicode:["1f982"],isCanonical:!0},":crab:":{unicode:["1f980"],isCanonical:!0},":turkey:":{unicode:["1f983"],isCanonical:!0},":cheese:":{unicode:["1f9c0"],isCanonical:!0},":cheese_wedge:":{unicode:["1f9c0"],isCanonical:!1},":hotdog:":{unicode:["1f32d"],isCanonical:!0},":hot_dog:":{unicode:["1f32d"],isCanonical:!1},":taco:":{unicode:["1f32e"],isCanonical:!0},":burrito:":{unicode:["1f32f"],isCanonical:!0},":popcorn:":{unicode:["1f37f"],isCanonical:!0},":champagne:":{unicode:["1f37e"],isCanonical:!0},":bottle_with_popping_cork:":{unicode:["1f37e"],isCanonical:!1},":bow_and_arrow:":{unicode:["1f3f9"],isCanonical:!0},":archery:":{unicode:["1f3f9"],isCanonical:!1},":amphora:":{unicode:["1f3fa"],isCanonical:!0},":place_of_worship:":{unicode:["1f6d0"],isCanonical:!0},":worship_symbol:":{unicode:["1f6d0"],isCanonical:!1},":kaaba:":{unicode:["1f54b"],isCanonical:!0},":mosque:":{unicode:["1f54c"],isCanonical:!0},":synagogue:":{unicode:["1f54d"],isCanonical:!0},":menorah:":{unicode:["1f54e"],isCanonical:!0},":prayer_beads:":{unicode:["1f4ff"],isCanonical:!0},":cricket:":{unicode:["1f3cf"],isCanonical:!0},":cricket_bat_ball:":{
    unicode:["1f3cf"],isCanonical:!1},":volleyball:":{unicode:["1f3d0"],isCanonical:!0},":field_hockey:":{unicode:["1f3d1"],isCanonical:!0},":hockey:":{unicode:["1f3d2"],isCanonical:!0},":ping_pong:":{unicode:["1f3d3"],isCanonical:!0},":table_tennis:":{unicode:["1f3d3"],isCanonical:!1},":badminton:":{unicode:["1f3f8"],isCanonical:!0},":drum:":{unicode:["1f941"],isCanonical:!0},":drum_with_drumsticks:":{unicode:["1f941"],isCanonical:!1},":shrimp:":{unicode:["1f990"],isCanonical:!0},":squid:":{unicode:["1f991"],isCanonical:!0},":egg:":{unicode:["1f95a"],isCanonical:!0},":milk:":{unicode:["1f95b"],isCanonical:!0},":glass_of_milk:":{unicode:["1f95b"],isCanonical:!1},":peanuts:":{unicode:["1f95c"],isCanonical:!0},":shelled_peanut:":{unicode:["1f95c"],isCanonical:!1},":kiwi:":{unicode:["1f95d"],isCanonical:!0},":kiwifruit:":{unicode:["1f95d"],isCanonical:!1},":pancakes:":{unicode:["1f95e"],isCanonical:!0},":regional_indicator_w:":{unicode:["1f1fc"],isCanonical:!0},":regional_indicator_v:":{unicode:["1f1fb"],isCanonical:!0},":regional_indicator_u:":{unicode:["1f1fa"],isCanonical:!0},":regional_indicator_t:":{unicode:["1f1f9"],isCanonical:!0},":regional_indicator_s:":{unicode:["1f1f8"],isCanonical:!0},":regional_indicator_r:":{unicode:["1f1f7"],isCanonical:!0},":regional_indicator_q:":{unicode:["1f1f6"],isCanonical:!0},":regional_indicator_p:":{unicode:["1f1f5"],isCanonical:!0},":regional_indicator_o:":{unicode:["1f1f4"],isCanonical:!0},":regional_indicator_n:":{unicode:["1f1f3"],isCanonical:!0},":regional_indicator_m:":{unicode:["1f1f2"],isCanonical:!0},":regional_indicator_l:":{unicode:["1f1f1"],isCanonical:!0},":regional_indicator_k:":{unicode:["1f1f0"],isCanonical:!0},":regional_indicator_j:":{unicode:["1f1ef"],isCanonical:!0},":regional_indicator_i:":{unicode:["1f1ee"],isCanonical:!0},":regional_indicator_h:":{unicode:["1f1ed"],isCanonical:!0},":regional_indicator_g:":{unicode:["1f1ec"],isCanonical:!0},":regional_indicator_f:":{unicode:["1f1eb"],isCanonical:!0},":regional_indicator_e:":{unicode:["1f1ea"],isCanonical:!0},":regional_indicator_d:":{unicode:["1f1e9"],isCanonical:!0},":regional_indicator_c:":{unicode:["1f1e8"],isCanonical:!0},":regional_indicator_b:":{unicode:["1f1e7"],isCanonical:!0},":regional_indicator_a:":{unicode:["1f1e6"],isCanonical:!0},":fast_forward:":{unicode:["23e9"],isCanonical:!0},":rewind:":{unicode:["23ea"],isCanonical:!0},":arrow_double_up:":{unicode:["23eb"],isCanonical:!0},":arrow_double_down:":{unicode:["23ec"],isCanonical:!0},":alarm_clock:":{unicode:["23f0"],isCanonical:!0},":hourglass_flowing_sand:":{unicode:["23f3"],isCanonical:!0},":ophiuchus:":{unicode:["26ce"],isCanonical:!0},":white_check_mark:":{unicode:["2705"],isCanonical:!0},":fist:":{unicode:["270a"],isCanonical:!0},":raised_hand:":{unicode:["270b"],isCanonical:!0},":sparkles:":{unicode:["2728"],isCanonical:!0},":x:":{unicode:["274c"],isCanonical:!0},":negative_squared_cross_mark:":{unicode:["274e"],isCanonical:!0},":question:":{unicode:["2753"],isCanonical:!0},":grey_question:":{unicode:["2754"],isCanonical:!0},":grey_exclamation:":{unicode:["2755"],isCanonical:!0},":heavy_plus_sign:":{unicode:["2795"],isCanonical:!0},":heavy_minus_sign:":{unicode:["2796"],isCanonical:!0},":heavy_division_sign:":{unicode:["2797"],isCanonical:!0},":curly_loop:":{unicode:["27b0"],isCanonical:!0},":loop:":{unicode:["27bf"],isCanonical:!0}};var b,c=[];for(b in a.emojioneList)a.emojioneList.hasOwnProperty(b)&&c.push(b.replace(/[+]/g,"\\$&"));a.shortnames=c.join("|"),a.asciiList={"<3":"2764","</3":"1f494",":')":"1f602",":'-)":"1f602",":D":"1f603",":-D":"1f603","=D":"1f603",":)":"1f642",":-)":"1f642","=]":"1f642","=)":"1f642",":]":"1f642","':)":"1f605","':-)":"1f605","'=)":"1f605","':D":"1f605","':-D":"1f605","'=D":"1f605",">:)":"1f606",">;)":"1f606",">:-)":"1f606",">=)":"1f606",";)":"1f609",";-)":"1f609","*-)":"1f609","*)":"1f609",";-]":"1f609",";]":"1f609",";D":"1f609",";^)":"1f609","':(":"1f613","':-(":"1f613","'=(":"1f613",":*":"1f618",":-*":"1f618","=*":"1f618",":^*":"1f618",">:P":"1f61c","X-P":"1f61c","x-p":"1f61c",">:[":"1f61e",":-(":"1f61e",":(":"1f61e",":-[":"1f61e",":[":"1f61e","=(":"1f61e",">:(":"1f620",">:-(":"1f620",":@":"1f620",":'(":"1f622",":'-(":"1f622",";(":"1f622",";-(":"1f622",">.<":"1f623","D:":"1f628",":$":"1f633","=$":"1f633","#-)":"1f635","#)":"1f635","%-)":"1f635","%)":"1f635","X)":"1f635","X-)":"1f635","*\\0/*":"1f646","\\0/":"1f646","*\\O/*":"1f646","\\O/":"1f646","O:-)":"1f607","0:-3":"1f607","0:3":"1f607","0:-)":"1f607","0:)":"1f607","0;^)":"1f607","O:)":"1f607","O;-)":"1f607","O=)":"1f607","0;-)":"1f607","O:-3":"1f607","O:3":"1f607","B-)":"1f60e","B)":"1f60e","8)":"1f60e","8-)":"1f60e","B-D":"1f60e","8-D":"1f60e","-_-":"1f611","-__-":"1f611","-___-":"1f611",">:\\":"1f615",">:/":"1f615",":-/":"1f615",":-.":"1f615",":/":"1f615",":\\":"1f615","=/":"1f615","=\\":"1f615",":L":"1f615","=L":"1f615",":P":"1f61b",":-P":"1f61b","=P":"1f61b",":-p":"1f61b",":p":"1f61b","=p":"1f61b",":-Þ":"1f61b",":Þ":"1f61b",":þ":"1f61b",":-þ":"1f61b",":-b":"1f61b",":b":"1f61b","d:":"1f61b",":-O":"1f62e",":O":"1f62e",":-o":"1f62e",":o":"1f62e",O_O:"1f62e",">:O":"1f62e",":-X":"1f636",":X":"1f636",":-#":"1f636",":#":"1f636","=X":"1f636","=x":"1f636",":x":"1f636",":-x":"1f636","=#":"1f636"},a.asciiRegexp="(\\<3|&lt;3|\\<\\/3|&lt;\\/3|\\:'\\)|\\:'\\-\\)|\\:D|\\:\\-D|\\=D|\\:\\)|\\:\\-\\)|\\=\\]|\\=\\)|\\:\\]|'\\:\\)|'\\:\\-\\)|'\\=\\)|'\\:D|'\\:\\-D|'\\=D|\\>\\:\\)|&gt;\\:\\)|\\>;\\)|&gt;;\\)|\\>\\:\\-\\)|&gt;\\:\\-\\)|\\>\\=\\)|&gt;\\=\\)|;\\)|;\\-\\)|\\*\\-\\)|\\*\\)|;\\-\\]|;\\]|;D|;\\^\\)|'\\:\\(|'\\:\\-\\(|'\\=\\(|\\:\\*|\\:\\-\\*|\\=\\*|\\:\\^\\*|\\>\\:P|&gt;\\:P|X\\-P|x\\-p|\\>\\:\\[|&gt;\\:\\[|\\:\\-\\(|\\:\\(|\\:\\-\\[|\\:\\[|\\=\\(|\\>\\:\\(|&gt;\\:\\(|\\>\\:\\-\\(|&gt;\\:\\-\\(|\\:@|\\:'\\(|\\:'\\-\\(|;\\(|;\\-\\(|\\>\\.\\<|&gt;\\.&lt;|D\\:|\\:\\$|\\=\\$|#\\-\\)|#\\)|%\\-\\)|%\\)|X\\)|X\\-\\)|\\*\\\\0\\/\\*|\\\\0\\/|\\*\\\\O\\/\\*|\\\\O\\/|O\\:\\-\\)|0\\:\\-3|0\\:3|0\\:\\-\\)|0\\:\\)|0;\\^\\)|O\\:\\-\\)|O\\:\\)|O;\\-\\)|O\\=\\)|0;\\-\\)|O\\:\\-3|O\\:3|B\\-\\)|B\\)|8\\)|8\\-\\)|B\\-D|8\\-D|\\-_\\-|\\-__\\-|\\-___\\-|\\>\\:\\\\|&gt;\\:\\\\|\\>\\:\\/|&gt;\\:\\/|\\:\\-\\/|\\:\\-\\.|\\:\\/|\\:\\\\|\\=\\/|\\=\\\\|\\:L|\\=L|\\:P|\\:\\-P|\\=P|\\:\\-p|\\:p|\\=p|\\:\\-Þ|\\:\\-&THORN;|\\:Þ|\\:&THORN;|\\:þ|\\:&thorn;|\\:\\-þ|\\:\\-&thorn;|\\:\\-b|\\:b|d\\:|\\:\\-O|\\:O|\\:\\-o|\\:o|O_O|\\>\\:O|&gt;\\:O|\\:\\-X|\\:X|\\:\\-#|\\:#|\\=X|\\=x|\\:x|\\:\\-x|\\=#)",a.unicodeRegexp="(\\uD83D\\uDC69\\u200D\\u2764\\uFE0F\\u200D\\uD83D\\uDC8B\\u200D\\uD83D\\uDC69|\\uD83D\\uDC68\\u200D\\u2764\\uFE0F\\u200D\\uD83D\\uDC8B\\u200D\\uD83D\\uDC68|\\uD83D\\uDC68\\u200D\\uD83D\\uDC68\\u200D\\uD83D\\uDC67\\u200D\\uD83D\\uDC66|\\uD83D\\uDC68\\u200D\\uD83D\\uDC68\\u200D\\uD83D\\uDC67\\u200D\\uD83D\\uDC67|\\uD83D\\uDC68\\u200D\\uD83D\\uDC69\\u200D\\uD83D\\uDC66\\u200D\\uD83D\\uDC66|\\uD83D\\uDC68\\u200D\\uD83D\\uDC69\\u200D\\uD83D\\uDC67\\u200D\\uD83D\\uDC66|\\uD83D\\uDC68\\u200D\\uD83D\\uDC69\\u200D\\uD83D\\uDC67\\u200D\\uD83D\\uDC67|\\uD83D\\uDC69\\u200D\\uD83D\\uDC69\\u200D\\uD83D\\uDC66\\u200D\\uD83D\\uDC66|\\uD83D\\uDC69\\u200D\\uD83D\\uDC69\\u200D\\uD83D\\uDC67\\u200D\\uD83D\\uDC66|\\uD83D\\uDC69\\u200D\\uD83D\\uDC69\\u200D\\uD83D\\uDC67\\u200D\\uD83D\\uDC67|\\uD83D\\uDC68\\u200D\\uD83D\\uDC68\\u200D\\uD83D\\uDC66\\u200D\\uD83D\\uDC66|\\uD83D\\uDC68\\u200D\\u2764\\uFE0F\\u200D\\uD83D\\uDC68|\\uD83D\\uDC68\\u200D\\uD83D\\uDC68\\u200D\\uD83D\\uDC67|\\uD83D\\uDC68\\u200D\\uD83D\\uDC69\\u200D\\uD83D\\uDC67|\\uD83D\\uDC69\\u200D\\uD83D\\uDC69\\u200D\\uD83D\\uDC66|\\uD83D\\uDC69\\u200D\\uD83D\\uDC69\\u200D\\uD83D\\uDC67|\\uD83D\\uDC69\\u200D\\u2764\\uFE0F\\u200D\\uD83D\\uDC69|\\uD83D\\uDC68\\u200D\\uD83D\\uDC68\\u200D\\uD83D\\uDC66|\\uD83D\\uDC41\\u200D\\uD83D\\uDDE8|\\uD83C\\uDDE6\\uD83C\\uDDE9|\\uD83C\\uDDE6\\uD83C\\uDDEA|\\uD83C\\uDDE6\\uD83C\\uDDEB|\\uD83C\\uDDE6\\uD83C\\uDDEC|\\uD83C\\uDDE6\\uD83C\\uDDEE|\\uD83C\\uDDE6\\uD83C\\uDDF1|\\uD83C\\uDDE6\\uD83C\\uDDF2|\\uD83C\\uDDE6\\uD83C\\uDDF4|\\uD83C\\uDDE6\\uD83C\\uDDF6|\\uD83C\\uDDE6\\uD83C\\uDDF7|\\uD83C\\uDDE6\\uD83C\\uDDF8|\\uD83E\\uDD18\\uD83C\\uDFFF|\\uD83E\\uDD18\\uD83C\\uDFFE|\\uD83E\\uDD18\\uD83C\\uDFFD|\\uD83E\\uDD18\\uD83C\\uDFFC|\\uD83E\\uDD18\\uD83C\\uDFFB|\\uD83D\\uDEC0\\uD83C\\uDFFF|\\uD83D\\uDEC0\\uD83C\\uDFFE|\\uD83D\\uDEC0\\uD83C\\uDFFD|\\uD83D\\uDEC0\\uD83C\\uDFFC|\\uD83D\\uDEC0\\uD83C\\uDFFB|\\uD83D\\uDEB6\\uD83C\\uDFFF|\\uD83D\\uDEB6\\uD83C\\uDFFE|\\uD83D\\uDEB6\\uD83C\\uDFFD|\\uD83D\\uDEB6\\uD83C\\uDFFC|\\uD83D\\uDEB6\\uD83C\\uDFFB|\\uD83D\\uDEB5\\uD83C\\uDFFF|\\uD83D\\uDEB5\\uD83C\\uDFFE|\\uD83D\\uDEB5\\uD83C\\uDFFD|\\uD83D\\uDEB5\\uD83C\\uDFFC|\\uD83D\\uDEB5\\uD83C\\uDFFB|\\uD83D\\uDEB4\\uD83C\\uDFFF|\\uD83D\\uDEB4\\uD83C\\uDFFE|\\uD83D\\uDEB4\\uD83C\\uDFFD|\\uD83D\\uDEB4\\uD83C\\uDFFC|\\uD83D\\uDEB4\\uD83C\\uDFFB|\\uD83D\\uDEA3\\uD83C\\uDFFF|\\uD83D\\uDEA3\\uD83C\\uDFFE|\\uD83D\\uDEA3\\uD83C\\uDFFD|\\uD83D\\uDEA3\\uD83C\\uDFFC|\\uD83D\\uDEA3\\uD83C\\uDFFB|\\uD83D\\uDE4F\\uD83C\\uDFFF|\\uD83D\\uDE4F\\uD83C\\uDFFE|\\uD83D\\uDE4F\\uD83C\\uDFFD|\\uD83D\\uDE4F\\uD83C\\uDFFC|\\uD83D\\uDE4F\\uD83C\\uDFFB|\\uD83D\\uDE4E\\uD83C\\uDFFF|\\uD83D\\uDE4E\\uD83C\\uDFFE|\\uD83D\\uDE4E\\uD83C\\uDFFD|\\uD83D\\uDE4E\\uD83C\\uDFFC|\\uD83D\\uDE4E\\uD83C\\uDFFB|\\uD83D\\uDE4D\\uD83C\\uDFFF|\\uD83D\\uDE4D\\uD83C\\uDFFE|\\uD83D\\uDE4D\\uD83C\\uDFFD|\\uD83D\\uDE4D\\uD83C\\uDFFC|\\uD83D\\uDE4D\\uD83C\\uDFFB|\\uD83D\\uDE4C\\uD83C\\uDFFF|\\uD83D\\uDE4C\\uD83C\\uDFFE|\\uD83D\\uDE4C\\uD83C\\uDFFD|\\uD83D\\uDE4C\\uD83C\\uDFFC|\\uD83D\\uDE4C\\uD83C\\uDFFB|\\uD83D\\uDE4B\\uD83C\\uDFFF|\\uD83D\\uDE4B\\uD83C\\uDFFE|\\uD83D\\uDE4B\\uD83C\\uDFFD|\\uD83D\\uDE4B\\uD83C\\uDFFC|\\uD83D\\uDE4B\\uD83C\\uDFFB|\\uD83D\\uDE47\\uD83C\\uDFFF|\\uD83D\\uDE47\\uD83C\\uDFFE|\\uD83D\\uDE47\\uD83C\\uDFFD|\\uD83D\\uDE47\\uD83C\\uDFFC|\\uD83D\\uDE47\\uD83C\\uDFFB|\\uD83D\\uDE46\\uD83C\\uDFFF|\\uD83D\\uDE46\\uD83C\\uDFFE|\\uD83D\\uDE46\\uD83C\\uDFFD|\\uD83D\\uDE46\\uD83C\\uDFFC|\\uD83D\\uDE46\\uD83C\\uDFFB|\\uD83D\\uDE45\\uD83C\\uDFFF|\\uD83D\\uDE45\\uD83C\\uDFFE|\\uD83D\\uDE45\\uD83C\\uDFFD|\\uD83D\\uDE45\\uD83C\\uDFFC|\\uD83D\\uDE45\\uD83C\\uDFFB|\\uD83D\\uDD96\\uD83C\\uDFFF|\\uD83D\\uDD96\\uD83C\\uDFFE|\\uD83D\\uDD96\\uD83C\\uDFFD|\\uD83D\\uDD96\\uD83C\\uDFFC|\\uD83D\\uDD96\\uD83C\\uDFFB|\\uD83D\\uDD95\\uD83C\\uDFFF|\\uD83D\\uDD95\\uD83C\\uDFFE|\\uD83D\\uDD95\\uD83C\\uDFFD|\\uD83D\\uDD95\\uD83C\\uDFFC|\\uD83D\\uDD95\\uD83C\\uDFFB|\\uD83D\\uDD90\\uD83C\\uDFFF|\\uD83D\\uDD90\\uD83C\\uDFFE|\\uD83D\\uDD90\\uD83C\\uDFFD|\\uD83D\\uDD90\\uD83C\\uDFFC|\\uD83D\\uDD90\\uD83C\\uDFFB|\\uD83D\\uDD75\\uD83C\\uDFFF|\\uD83D\\uDD75\\uD83C\\uDFFE|\\uD83D\\uDD75\\uD83C\\uDFFD|\\uD83D\\uDD75\\uD83C\\uDFFC|\\uD83D\\uDD75\\uD83C\\uDFFB|\\uD83D\\uDCAA\\uD83C\\uDFFF|\\uD83D\\uDCAA\\uD83C\\uDFFE|\\uD83D\\uDCAA\\uD83C\\uDFFD|\\uD83D\\uDCAA\\uD83C\\uDFFC|\\uD83D\\uDCAA\\uD83C\\uDFFB|\\uD83D\\uDC87\\uD83C\\uDFFF|\\uD83D\\uDC87\\uD83C\\uDFFE|\\uD83D\\uDC87\\uD83C\\uDFFD|\\uD83D\\uDC87\\uD83C\\uDFFC|\\uD83D\\uDC87\\uD83C\\uDFFB|\\uD83D\\uDC86\\uD83C\\uDFFF|\\uD83D\\uDC86\\uD83C\\uDFFE|\\uD83D\\uDC86\\uD83C\\uDFFD|\\uD83D\\uDC86\\uD83C\\uDFFC|\\uD83D\\uDC86\\uD83C\\uDFFB|\\uD83D\\uDC85\\uD83C\\uDFFF|\\uD83D\\uDC85\\uD83C\\uDFFE|\\uD83D\\uDC85\\uD83C\\uDFFD|\\uD83D\\uDC85\\uD83C\\uDFFC|\\uD83D\\uDC85\\uD83C\\uDFFB|\\uD83D\\uDC83\\uD83C\\uDFFF|\\uD83D\\uDC83\\uD83C\\uDFFE|\\uD83D\\uDC83\\uD83C\\uDFFD|\\uD83D\\uDC83\\uD83C\\uDFFC|\\uD83D\\uDC83\\uD83C\\uDFFB|\\uD83D\\uDC82\\uD83C\\uDFFF|\\uD83D\\uDC82\\uD83C\\uDFFE|\\uD83D\\uDC82\\uD83C\\uDFFD|\\uD83D\\uDC82\\uD83C\\uDFFC|\\uD83D\\uDC82\\uD83C\\uDFFB|\\uD83D\\uDC81\\uD83C\\uDFFF|\\uD83D\\uDC81\\uD83C\\uDFFE|\\uD83D\\uDC81\\uD83C\\uDFFD|\\uD83D\\uDC81\\uD83C\\uDFFC|\\uD83D\\uDC81\\uD83C\\uDFFB|\\uD83D\\uDC7C\\uD83C\\uDFFF|\\uD83D\\uDC7C\\uD83C\\uDFFE|\\uD83D\\uDC7C\\uD83C\\uDFFD|\\uD83D\\uDC7C\\uD83C\\uDFFC|\\uD83D\\uDC7C\\uD83C\\uDFFB|\\uD83D\\uDC78\\uD83C\\uDFFF|\\uD83D\\uDC78\\uD83C\\uDFFE|\\uD83D\\uDC78\\uD83C\\uDFFD|\\uD83D\\uDC78\\uD83C\\uDFFC|\\uD83D\\uDC78\\uD83C\\uDFFB|\\uD83D\\uDC77\\uD83C\\uDFFF|\\uD83D\\uDC77\\uD83C\\uDFFE|\\uD83D\\uDC77\\uD83C\\uDFFD|\\uD83D\\uDC77\\uD83C\\uDFFC|\\uD83D\\uDC77\\uD83C\\uDFFB|\\uD83D\\uDC76\\uD83C\\uDFFF|\\uD83D\\uDC76\\uD83C\\uDFFE|\\uD83D\\uDC76\\uD83C\\uDFFD|\\uD83D\\uDC76\\uD83C\\uDFFC|\\uD83D\\uDC76\\uD83C\\uDFFB|\\uD83D\\uDC75\\uD83C\\uDFFF|\\uD83D\\uDC75\\uD83C\\uDFFE|\\uD83D\\uDC75\\uD83C\\uDFFD|\\uD83D\\uDC75\\uD83C\\uDFFC|\\uD83D\\uDC75\\uD83C\\uDFFB|\\uD83D\\uDC74\\uD83C\\uDFFF|\\uD83D\\uDC74\\uD83C\\uDFFE|\\uD83D\\uDC74\\uD83C\\uDFFD|\\uD83D\\uDC74\\uD83C\\uDFFC|\\uD83D\\uDC74\\uD83C\\uDFFB|\\uD83D\\uDC73\\uD83C\\uDFFF|\\uD83D\\uDC73\\uD83C\\uDFFE|\\uD83D\\uDC73\\uD83C\\uDFFD|\\uD83D\\uDC73\\uD83C\\uDFFC|\\uD83D\\uDC73\\uD83C\\uDFFB|\\uD83D\\uDC72\\uD83C\\uDFFF|\\uD83D\\uDC72\\uD83C\\uDFFE|\\uD83D\\uDC72\\uD83C\\uDFFD|\\uD83D\\uDC72\\uD83C\\uDFFC|\\uD83D\\uDC72\\uD83C\\uDFFB|\\uD83D\\uDC71\\uD83C\\uDFFF|\\uD83D\\uDC71\\uD83C\\uDFFE|\\uD83D\\uDC71\\uD83C\\uDFFD|\\uD83D\\uDC71\\uD83C\\uDFFC|\\uD83D\\uDC71\\uD83C\\uDFFB|\\uD83D\\uDC70\\uD83C\\uDFFF|\\uD83D\\uDC70\\uD83C\\uDFFE|\\uD83D\\uDC70\\uD83C\\uDFFD|\\uD83D\\uDC70\\uD83C\\uDFFC|\\uD83D\\uDC70\\uD83C\\uDFFB|\\uD83D\\uDC6E\\uD83C\\uDFFF|\\uD83D\\uDC6E\\uD83C\\uDFFE|\\uD83D\\uDC6E\\uD83C\\uDFFD|\\uD83D\\uDC6E\\uD83C\\uDFFC|\\uD83D\\uDC6E\\uD83C\\uDFFB|\\uD83D\\uDC69\\uD83C\\uDFFF|\\uD83D\\uDC69\\uD83C\\uDFFE|\\uD83D\\uDC69\\uD83C\\uDFFD|\\uD83D\\uDC69\\uD83C\\uDFFC|\\uD83D\\uDC69\\uD83C\\uDFFB|\\uD83D\\uDC68\\uD83C\\uDFFF|\\uD83D\\uDC68\\uD83C\\uDFFE|\\uD83D\\uDC68\\uD83C\\uDFFD|\\uD83D\\uDC68\\uD83C\\uDFFC|\\uD83D\\uDC68\\uD83C\\uDFFB|\\uD83D\\uDC67\\uD83C\\uDFFF|\\uD83D\\uDC67\\uD83C\\uDFFE|\\uD83D\\uDC67\\uD83C\\uDFFD|\\uD83D\\uDC67\\uD83C\\uDFFC|\\uD83D\\uDC67\\uD83C\\uDFFB|\\uD83D\\uDC66\\uD83C\\uDFFF|\\uD83D\\uDC66\\uD83C\\uDFFE|\\uD83D\\uDC66\\uD83C\\uDFFD|\\uD83D\\uDC66\\uD83C\\uDFFC|\\uD83D\\uDC66\\uD83C\\uDFFB|\\uD83D\\uDC50\\uD83C\\uDFFF|\\uD83D\\uDC50\\uD83C\\uDFFE|\\uD83D\\uDC50\\uD83C\\uDFFD|\\uD83D\\uDC50\\uD83C\\uDFFC|\\uD83D\\uDC50\\uD83C\\uDFFB|\\uD83D\\uDC4F\\uD83C\\uDFFF|\\uD83D\\uDC4F\\uD83C\\uDFFE|\\uD83D\\uDC4F\\uD83C\\uDFFD|\\uD83D\\uDC4F\\uD83C\\uDFFC|\\uD83D\\uDC4F\\uD83C\\uDFFB|\\uD83D\\uDC4E\\uD83C\\uDFFF|\\uD83D\\uDC4E\\uD83C\\uDFFE|\\uD83D\\uDC4E\\uD83C\\uDFFD|\\uD83D\\uDC4E\\uD83C\\uDFFC|\\uD83D\\uDC4E\\uD83C\\uDFFB|\\uD83D\\uDC4D\\uD83C\\uDFFF|\\uD83D\\uDC4D\\uD83C\\uDFFE|\\uD83D\\uDC4D\\uD83C\\uDFFD|\\uD83D\\uDC4D\\uD83C\\uDFFC|\\uD83D\\uDC4D\\uD83C\\uDFFB|\\uD83D\\uDC4C\\uD83C\\uDFFF|\\uD83D\\uDC4C\\uD83C\\uDFFE|\\uD83D\\uDC4C\\uD83C\\uDFFD|\\uD83D\\uDC4C\\uD83C\\uDFFC|\\uD83D\\uDC4C\\uD83C\\uDFFB|\\uD83D\\uDC4B\\uD83C\\uDFFF|\\uD83D\\uDC4B\\uD83C\\uDFFE|\\uD83D\\uDC4B\\uD83C\\uDFFD|\\uD83D\\uDC4B\\uD83C\\uDFFC|\\uD83D\\uDC4B\\uD83C\\uDFFB|\\uD83D\\uDC4A\\uD83C\\uDFFF|\\uD83D\\uDC4A\\uD83C\\uDFFE|\\uD83D\\uDC4A\\uD83C\\uDFFD|\\uD83D\\uDC4A\\uD83C\\uDFFC|\\uD83D\\uDC4A\\uD83C\\uDFFB|\\uD83D\\uDC49\\uD83C\\uDFFF|\\uD83D\\uDC49\\uD83C\\uDFFE|\\uD83D\\uDC49\\uD83C\\uDFFD|\\uD83D\\uDC49\\uD83C\\uDFFC|\\uD83D\\uDC49\\uD83C\\uDFFB|\\uD83D\\uDC48\\uD83C\\uDFFF|\\uD83D\\uDC48\\uD83C\\uDFFE|\\uD83D\\uDC48\\uD83C\\uDFFD|\\uD83D\\uDC48\\uD83C\\uDFFC|\\uD83D\\uDC48\\uD83C\\uDFFB|\\uD83D\\uDC47\\uD83C\\uDFFF|\\uD83D\\uDC47\\uD83C\\uDFFE|\\uD83D\\uDC47\\uD83C\\uDFFD|\\uD83D\\uDC47\\uD83C\\uDFFC|\\uD83D\\uDC47\\uD83C\\uDFFB|\\uD83D\\uDC46\\uD83C\\uDFFF|\\uD83D\\uDC46\\uD83C\\uDFFE|\\uD83D\\uDC46\\uD83C\\uDFFD|\\uD83D\\uDC46\\uD83C\\uDFFC|\\uD83D\\uDC46\\uD83C\\uDFFB|\\uD83D\\uDC43\\uD83C\\uDFFF|\\uD83D\\uDC43\\uD83C\\uDFFE|\\uD83D\\uDC43\\uD83C\\uDFFD|\\uD83D\\uDC43\\uD83C\\uDFFC|\\uD83D\\uDC43\\uD83C\\uDFFB|\\uD83D\\uDC42\\uD83C\\uDFFF|\\uD83D\\uDC42\\uD83C\\uDFFE|\\uD83D\\uDC42\\uD83C\\uDFFD|\\uD83D\\uDC42\\uD83C\\uDFFC|\\uD83D\\uDC42\\uD83C\\uDFFB|\\uD83C\\uDFCB\\uD83C\\uDFFF|\\uD83C\\uDFCB\\uD83C\\uDFFE|\\uD83C\\uDFCB\\uD83C\\uDFFD|\\uD83C\\uDFCB\\uD83C\\uDFFC|\\uD83C\\uDFCB\\uD83C\\uDFFB|\\uD83C\\uDFCA\\uD83C\\uDFFF|\\uD83C\\uDFCA\\uD83C\\uDFFE|\\uD83C\\uDFCA\\uD83C\\uDFFD|\\uD83C\\uDFCA\\uD83C\\uDFFC|\\uD83C\\uDFCA\\uD83C\\uDFFB|\\uD83C\\uDFC7\\uD83C\\uDFFF|\\uD83C\\uDFC7\\uD83C\\uDFFE|\\uD83C\\uDFC7\\uD83C\\uDFFD|\\uD83C\\uDFC7\\uD83C\\uDFFC|\\uD83C\\uDFC7\\uD83C\\uDFFB|\\uD83C\\uDFC4\\uD83C\\uDFFF|\\uD83C\\uDFC4\\uD83C\\uDFFE|\\uD83C\\uDFC4\\uD83C\\uDFFD|\\uD83C\\uDFC4\\uD83C\\uDFFC|\\uD83C\\uDFC4\\uD83C\\uDFFB|\\uD83C\\uDFC3\\uD83C\\uDFFF|\\uD83C\\uDFC3\\uD83C\\uDFFE|\\uD83C\\uDFC3\\uD83C\\uDFFD|\\uD83C\\uDFC3\\uD83C\\uDFFC|\\uD83C\\uDFC3\\uD83C\\uDFFB|\\uD83C\\uDF85\\uD83C\\uDFFF|\\uD83C\\uDF85\\uD83C\\uDFFE|\\uD83C\\uDF85\\uD83C\\uDFFD|\\uD83C\\uDF85\\uD83C\\uDFFC|\\uD83C\\uDF85\\uD83C\\uDFFB|\\uD83C\\uDDFF\\uD83C\\uDDFC|\\uD83C\\uDDFF\\uD83C\\uDDF2|\\uD83C\\uDDFF\\uD83C\\uDDE6|\\uD83C\\uDDFE\\uD83C\\uDDF9|\\uD83C\\uDDFE\\uD83C\\uDDEA|\\uD83C\\uDDFD\\uD83C\\uDDF0|\\uD83C\\uDDFC\\uD83C\\uDDF8|\\uD83C\\uDDFC\\uD83C\\uDDEB|\\uD83C\\uDDFB\\uD83C\\uDDFA|\\uD83C\\uDDFB\\uD83C\\uDDF3|\\uD83C\\uDDFB\\uD83C\\uDDEE|\\uD83C\\uDDFB\\uD83C\\uDDEC|\\uD83C\\uDDFB\\uD83C\\uDDEA|\\uD83C\\uDDFB\\uD83C\\uDDE8|\\uD83C\\uDDFB\\uD83C\\uDDE6|\\uD83C\\uDDFA\\uD83C\\uDDFF|\\uD83C\\uDDFA\\uD83C\\uDDFE|\\uD83C\\uDDFA\\uD83C\\uDDF8|\\uD83C\\uDDFA\\uD83C\\uDDF2|\\uD83C\\uDDFA\\uD83C\\uDDEC|\\uD83C\\uDDFA\\uD83C\\uDDE6|\\uD83C\\uDDF9\\uD83C\\uDDFF|\\uD83C\\uDDF9\\uD83C\\uDDFC|\\uD83C\\uDDF9\\uD83C\\uDDFB|\\uD83C\\uDDF9\\uD83C\\uDDF9|\\uD83C\\uDDF9\\uD83C\\uDDF7|\\uD83C\\uDDF9\\uD83C\\uDDF4|\\uD83C\\uDDF9\\uD83C\\uDDF3|\\uD83C\\uDDF9\\uD83C\\uDDF2|\\uD83C\\uDDF9\\uD83C\\uDDF1|\\uD83C\\uDDF9\\uD83C\\uDDF0|\\uD83C\\uDDF9\\uD83C\\uDDEF|\\uD83C\\uDDF9\\uD83C\\uDDED|\\uD83C\\uDDF9\\uD83C\\uDDEC|\\uD83C\\uDDF9\\uD83C\\uDDEB|\\uD83C\\uDDE6\\uD83C\\uDDE8|\\uD83C\\uDDF9\\uD83C\\uDDE8|\\uD83C\\uDDF9\\uD83C\\uDDE6|\\uD83C\\uDDF8\\uD83C\\uDDFF|\\uD83C\\uDDF8\\uD83C\\uDDFE|\\uD83C\\uDDF8\\uD83C\\uDDFD|\\uD83C\\uDDF8\\uD83C\\uDDFB|\\uD83C\\uDDF8\\uD83C\\uDDF9|\\uD83C\\uDDF8\\uD83C\\uDDF8|\\uD83C\\uDDF8\\uD83C\\uDDF7|\\uD83C\\uDDF8\\uD83C\\uDDF4|\\uD83C\\uDDF8\\uD83C\\uDDF3|\\uD83C\\uDDF8\\uD83C\\uDDF2|\\uD83C\\uDDF8\\uD83C\\uDDF1|\\uD83C\\uDDF8\\uD83C\\uDDF0|\\uD83C\\uDDF8\\uD83C\\uDDEF|\\uD83C\\uDDF8\\uD83C\\uDDEE|\\uD83C\\uDDF8\\uD83C\\uDDED|\\uD83C\\uDDF8\\uD83C\\uDDEC|\\uD83C\\uDDF8\\uD83C\\uDDEA|\\uD83C\\uDDF8\\uD83C\\uDDE9|\\uD83C\\uDDF8\\uD83C\\uDDE8|\\uD83C\\uDDF8\\uD83C\\uDDE7|\\uD83C\\uDDF8\\uD83C\\uDDE6|\\uD83C\\uDDF7\\uD83C\\uDDFC|\\uD83C\\uDDF7\\uD83C\\uDDFA|\\uD83C\\uDDF7\\uD83C\\uDDF8|\\uD83C\\uDDF7\\uD83C\\uDDF4|\\uD83C\\uDDF7\\uD83C\\uDDEA|\\uD83C\\uDDF6\\uD83C\\uDDE6|\\uD83C\\uDDF5\\uD83C\\uDDFE|\\uD83C\\uDDF5\\uD83C\\uDDFC|\\uD83C\\uDDF5\\uD83C\\uDDF9|\\uD83C\\uDDF5\\uD83C\\uDDF8|\\uD83C\\uDDF5\\uD83C\\uDDF7|\\uD83C\\uDDF5\\uD83C\\uDDF3|\\uD83C\\uDDF5\\uD83C\\uDDF2|\\uD83C\\uDDF5\\uD83C\\uDDF1|\\uD83C\\uDDF5\\uD83C\\uDDF0|\\uD83C\\uDDF5\\uD83C\\uDDED|\\uD83C\\uDDF5\\uD83C\\uDDEC|\\uD83C\\uDDF5\\uD83C\\uDDEB|\\uD83C\\uDDF5\\uD83C\\uDDEA|\\uD83C\\uDDF5\\uD83C\\uDDE6|\\uD83C\\uDDF4\\uD83C\\uDDF2|\\uD83C\\uDDF3\\uD83C\\uDDFF|\\uD83C\\uDDF3\\uD83C\\uDDFA|\\uD83C\\uDDF3\\uD83C\\uDDF7|\\uD83C\\uDDF3\\uD83C\\uDDF5|\\uD83C\\uDDF3\\uD83C\\uDDF4|\\uD83C\\uDDF3\\uD83C\\uDDF1|\\uD83C\\uDDF3\\uD83C\\uDDEE|\\uD83C\\uDDF3\\uD83C\\uDDEC|\\uD83C\\uDDF3\\uD83C\\uDDEB|\\uD83C\\uDDF3\\uD83C\\uDDEA|\\uD83C\\uDDF3\\uD83C\\uDDE8|\\uD83C\\uDDF3\\uD83C\\uDDE6|\\uD83C\\uDDF2\\uD83C\\uDDFF|\\uD83C\\uDDF2\\uD83C\\uDDFE|\\uD83C\\uDDF2\\uD83C\\uDDFD|\\uD83C\\uDDF2\\uD83C\\uDDFC|\\uD83C\\uDDF2\\uD83C\\uDDFB|\\uD83C\\uDDF2\\uD83C\\uDDFA|\\uD83C\\uDDF2\\uD83C\\uDDF9|\\uD83C\\uDDF2\\uD83C\\uDDF8|\\uD83C\\uDDF2\\uD83C\\uDDF7|\\uD83C\\uDDF2\\uD83C\\uDDF6|\\uD83C\\uDDF2\\uD83C\\uDDF5|\\uD83C\\uDDF2\\uD83C\\uDDF4|\\uD83C\\uDDF2\\uD83C\\uDDF3|\\uD83C\\uDDF2\\uD83C\\uDDF2|\\uD83C\\uDDF2\\uD83C\\uDDF1|\\uD83C\\uDDF2\\uD83C\\uDDF0|\\uD83C\\uDDF2\\uD83C\\uDDED|\\uD83C\\uDDF2\\uD83C\\uDDEC|\\uD83C\\uDDF2\\uD83C\\uDDEB|\\uD83C\\uDDF2\\uD83C\\uDDEA|\\uD83C\\uDDF2\\uD83C\\uDDE9|\\uD83C\\uDDF2\\uD83C\\uDDE8|\\uD83C\\uDDF2\\uD83C\\uDDE6|\\uD83C\\uDDF1\\uD83C\\uDDFE|\\uD83C\\uDDF1\\uD83C\\uDDFB|\\uD83C\\uDDF1\\uD83C\\uDDFA|\\uD83C\\uDDF1\\uD83C\\uDDF9|\\uD83C\\uDDF1\\uD83C\\uDDF8|\\uD83C\\uDDF1\\uD83C\\uDDF7|\\uD83C\\uDDF1\\uD83C\\uDDF0|\\uD83C\\uDDF1\\uD83C\\uDDEE|\\uD83C\\uDDF1\\uD83C\\uDDE8|\\uD83C\\uDDF1\\uD83C\\uDDE7|\\uD83C\\uDDF1\\uD83C\\uDDE6|\\uD83C\\uDDF0\\uD83C\\uDDFF|\\uD83C\\uDDF0\\uD83C\\uDDFE|\\uD83C\\uDDF0\\uD83C\\uDDFC|\\uD83C\\uDDF0\\uD83C\\uDDF7|\\uD83C\\uDDF0\\uD83C\\uDDF5|\\uD83C\\uDDF0\\uD83C\\uDDF3|\\uD83C\\uDDF0\\uD83C\\uDDF2|\\uD83C\\uDDF0\\uD83C\\uDDEE|\\uD83C\\uDDF0\\uD83C\\uDDED|\\uD83C\\uDDF0\\uD83C\\uDDEC|\\uD83C\\uDDF0\\uD83C\\uDDEA|\\uD83C\\uDDEF\\uD83C\\uDDF5|\\uD83C\\uDDEF\\uD83C\\uDDF4|\\uD83C\\uDDEF\\uD83C\\uDDF2|\\uD83C\\uDDEF\\uD83C\\uDDEA|\\uD83C\\uDDEE\\uD83C\\uDDF9|\\uD83C\\uDDEE\\uD83C\\uDDF8|\\uD83C\\uDDEE\\uD83C\\uDDF7|\\uD83C\\uDDEE\\uD83C\\uDDF6|\\uD83C\\uDDEE\\uD83C\\uDDF4|\\uD83C\\uDDEE\\uD83C\\uDDF3|\\uD83C\\uDDEE\\uD83C\\uDDF2|\\uD83C\\uDDEE\\uD83C\\uDDF1|\\uD83C\\uDDEE\\uD83C\\uDDEA|\\uD83C\\uDDEE\\uD83C\\uDDE9|\\uD83C\\uDDEE\\uD83C\\uDDE8|\\uD83C\\uDDED\\uD83C\\uDDFA|\\uD83C\\uDDED\\uD83C\\uDDF9|\\uD83C\\uDDED\\uD83C\\uDDF7|\\uD83C\\uDDED\\uD83C\\uDDF3|\\uD83C\\uDDED\\uD83C\\uDDF2|\\uD83C\\uDDED\\uD83C\\uDDF0|\\uD83C\\uDDEC\\uD83C\\uDDFE|\\uD83C\\uDDEC\\uD83C\\uDDFC|\\uD83C\\uDDEC\\uD83C\\uDDFA|\\uD83C\\uDDEC\\uD83C\\uDDF9|\\uD83C\\uDDEC\\uD83C\\uDDF8|\\uD83C\\uDDEC\\uD83C\\uDDF7|\\uD83C\\uDDEC\\uD83C\\uDDF6|\\uD83C\\uDDEC\\uD83C\\uDDF5|\\uD83C\\uDDEC\\uD83C\\uDDF3|\\uD83C\\uDDEC\\uD83C\\uDDF2|\\uD83C\\uDDEC\\uD83C\\uDDF1|\\uD83C\\uDDEC\\uD83C\\uDDEE|\\uD83C\\uDDEC\\uD83C\\uDDED|\\uD83C\\uDDEC\\uD83C\\uDDEC|\\uD83C\\uDDEC\\uD83C\\uDDEB|\\uD83C\\uDDEC\\uD83C\\uDDEA|\\uD83C\\uDDEC\\uD83C\\uDDE9|\\uD83C\\uDDEC\\uD83C\\uDDE7|\\uD83C\\uDDEC\\uD83C\\uDDE6|\\uD83C\\uDDEB\\uD83C\\uDDF7|\\uD83C\\uDDEB\\uD83C\\uDDF4|\\uD83C\\uDDEB\\uD83C\\uDDF2|\\uD83C\\uDDEB\\uD83C\\uDDF0|\\uD83C\\uDDEB\\uD83C\\uDDEF|\\uD83C\\uDDEB\\uD83C\\uDDEE|\\uD83C\\uDDEA\\uD83C\\uDDFA|\\uD83C\\uDDEA\\uD83C\\uDDF9|\\uD83C\\uDDEA\\uD83C\\uDDF8|\\uD83C\\uDDEA\\uD83C\\uDDF7|\\uD83C\\uDDEA\\uD83C\\uDDED|\\uD83C\\uDDEA\\uD83C\\uDDEC|\\uD83C\\uDDEA\\uD83C\\uDDEA|\\uD83C\\uDDEA\\uD83C\\uDDE8|\\uD83C\\uDDEA\\uD83C\\uDDE6|\\uD83C\\uDDE9\\uD83C\\uDDFF|\\uD83C\\uDDE9\\uD83C\\uDDF4|\\uD83C\\uDDE9\\uD83C\\uDDF2|\\uD83C\\uDDE9\\uD83C\\uDDF0|\\uD83C\\uDDE9\\uD83C\\uDDEF|\\uD83C\\uDDE9\\uD83C\\uDDEC|\\uD83C\\uDDE9\\uD83C\\uDDEA|\\uD83C\\uDDE8\\uD83C\\uDDFF|\\uD83C\\uDDE8\\uD83C\\uDDFE|\\uD83C\\uDDE8\\uD83C\\uDDFD|\\uD83C\\uDDE8\\uD83C\\uDDFC|\\uD83C\\uDDE8\\uD83C\\uDDFB|\\uD83C\\uDDE8\\uD83C\\uDDFA|\\uD83C\\uDDE8\\uD83C\\uDDF7|\\uD83C\\uDDE8\\uD83C\\uDDF5|\\uD83C\\uDDE8\\uD83C\\uDDF4|\\uD83C\\uDDE8\\uD83C\\uDDF3|\\uD83C\\uDDE8\\uD83C\\uDDF2|\\uD83C\\uDDE8\\uD83C\\uDDF1|\\uD83C\\uDDE8\\uD83C\\uDDF0|\\uD83C\\uDDE8\\uD83C\\uDDEE|\\uD83C\\uDDE8\\uD83C\\uDDED|\\uD83C\\uDDE8\\uD83C\\uDDEC|\\uD83C\\uDDE8\\uD83C\\uDDEB|\\uD83C\\uDDE8\\uD83C\\uDDE9|\\uD83C\\uDDE8\\uD83C\\uDDE8|\\uD83C\\uDDE8\\uD83C\\uDDE6|\\uD83C\\uDDE7\\uD83C\\uDDFF|\\uD83C\\uDDE7\\uD83C\\uDDFE|\\uD83C\\uDDE7\\uD83C\\uDDFC|\\uD83C\\uDDE7\\uD83C\\uDDFB|\\uD83C\\uDDE7\\uD83C\\uDDF9|\\uD83C\\uDDE7\\uD83C\\uDDF8|\\uD83C\\uDDE7\\uD83C\\uDDF7|\\uD83C\\uDDE7\\uD83C\\uDDF6|\\uD83C\\uDDE7\\uD83C\\uDDF4|\\uD83C\\uDDE7\\uD83C\\uDDF3|\\uD83C\\uDDE7\\uD83C\\uDDF2|\\uD83C\\uDDE7\\uD83C\\uDDF1|\\uD83C\\uDDE7\\uD83C\\uDDEF|\\uD83C\\uDDE7\\uD83C\\uDDEE|\\uD83C\\uDDE7\\uD83C\\uDDED|\\uD83C\\uDDE7\\uD83C\\uDDEC|\\uD83C\\uDDE7\\uD83C\\uDDEB|\\uD83C\\uDDE7\\uD83C\\uDDEA|\\uD83C\\uDDE7\\uD83C\\uDDE9|\\uD83C\\uDDE7\\uD83C\\uDDE7|\\uD83C\\uDDE7\\uD83C\\uDDE6|\\uD83C\\uDDE6\\uD83C\\uDDFF|\\uD83C\\uDDE6\\uD83C\\uDDFD|\\uD83C\\uDDE6\\uD83C\\uDDFC|\\uD83C\\uDDE6\\uD83C\\uDDFA|\\uD83C\\uDDE6\\uD83C\\uDDF9|\\uD83C\\uDDF9\\uD83C\\uDDE9|\\uD83D\\uDDE1\\uFE0F|\\u26F9\\uD83C\\uDFFF|\\u26F9\\uD83C\\uDFFE|\\u26F9\\uD83C\\uDFFD|\\u26F9\\uD83C\\uDFFC|\\u26F9\\uD83C\\uDFFB|\\u270D\\uD83C\\uDFFF|\\u270D\\uD83C\\uDFFE|\\u270D\\uD83C\\uDFFD|\\u270D\\uD83C\\uDFFC|\\u270D\\uD83C\\uDFFB|\\uD83C\\uDC04\\uFE0F|\\uD83C\\uDD7F\\uFE0F|\\uD83C\\uDE02\\uFE0F|\\uD83C\\uDE1A\\uFE0F|\\uD83C\\uDE2F\\uFE0F|\\uD83C\\uDE37\\uFE0F|\\uD83C\\uDF9E\\uFE0F|\\uD83C\\uDF9F\\uFE0F|\\uD83C\\uDFCB\\uFE0F|\\uD83C\\uDFCC\\uFE0F|\\uD83C\\uDFCD\\uFE0F|\\uD83C\\uDFCE\\uFE0F|\\uD83C\\uDF96\\uFE0F|\\uD83C\\uDF97\\uFE0F|\\uD83C\\uDF36\\uFE0F|\\uD83C\\uDF27\\uFE0F|\\uD83C\\uDF28\\uFE0F|\\uD83C\\uDF29\\uFE0F|\\uD83C\\uDF2A\\uFE0F|\\uD83C\\uDF2B\\uFE0F|\\uD83C\\uDF2C\\uFE0F|\\uD83D\\uDC3F\\uFE0F|\\uD83D\\uDD77\\uFE0F|\\uD83D\\uDD78\\uFE0F|\\uD83C\\uDF21\\uFE0F|\\uD83C\\uDF99\\uFE0F|\\uD83C\\uDF9A\\uFE0F|\\uD83C\\uDF9B\\uFE0F|\\uD83C\\uDFF3\\uFE0F|\\uD83C\\uDFF5\\uFE0F|\\uD83C\\uDFF7\\uFE0F|\\uD83D\\uDCFD\\uFE0F|\\uD83D\\uDD49\\uFE0F|\\uD83D\\uDD4A\\uFE0F|\\uD83D\\uDD6F\\uFE0F|\\uD83D\\uDD70\\uFE0F|\\uD83D\\uDD73\\uFE0F|\\uD83D\\uDD76\\uFE0F|\\uD83D\\uDD79\\uFE0F|\\uD83D\\uDD87\\uFE0F|\\uD83D\\uDD8A\\uFE0F|\\uD83D\\uDD8B\\uFE0F|\\uD83D\\uDD8C\\uFE0F|\\uD83D\\uDD8D\\uFE0F|\\uD83D\\uDDA5\\uFE0F|\\uD83D\\uDDA8\\uFE0F|\\uD83D\\uDDB2\\uFE0F|\\uD83D\\uDDBC\\uFE0F|\\uD83D\\uDDC2\\uFE0F|\\uD83D\\uDDC3\\uFE0F|\\uD83D\\uDDC4\\uFE0F|\\uD83D\\uDDD1\\uFE0F|\\uD83D\\uDDD2\\uFE0F|\\uD83D\\uDDD3\\uFE0F|\\uD83D\\uDDDC\\uFE0F|\\uD83D\\uDDDD\\uFE0F|\\uD83D\\uDDDE\\uFE0F|\\u270B\\uD83C\\uDFFF|\\uD83D\\uDDE3\\uFE0F|\\uD83D\\uDDEF\\uFE0F|\\uD83D\\uDDF3\\uFE0F|\\uD83D\\uDDFA\\uFE0F|\\uD83D\\uDEE0\\uFE0F|\\uD83D\\uDEE1\\uFE0F|\\uD83D\\uDEE2\\uFE0F|\\uD83D\\uDEF0\\uFE0F|\\uD83C\\uDF7D\\uFE0F|\\uD83D\\uDC41\\uFE0F|\\uD83D\\uDD74\\uFE0F|\\uD83D\\uDD75\\uFE0F|\\uD83D\\uDD90\\uFE0F|\\uD83C\\uDFD4\\uFE0F|\\uD83C\\uDFD5\\uFE0F|\\uD83C\\uDFD6\\uFE0F|\\uD83C\\uDFD7\\uFE0F|\\uD83C\\uDFD8\\uFE0F|\\uD83C\\uDFD9\\uFE0F|\\uD83C\\uDFDA\\uFE0F|\\uD83C\\uDFDB\\uFE0F|\\uD83C\\uDFDC\\uFE0F|\\uD83C\\uDFDD\\uFE0F|\\uD83C\\uDFDE\\uFE0F|\\uD83C\\uDFDF\\uFE0F|\\uD83D\\uDECB\\uFE0F|\\uD83D\\uDECD\\uFE0F|\\uD83D\\uDECE\\uFE0F|\\uD83D\\uDECF\\uFE0F|\\uD83D\\uDEE3\\uFE0F|\\uD83D\\uDEE4\\uFE0F|\\uD83D\\uDEE5\\uFE0F|\\uD83D\\uDEE9\\uFE0F|\\uD83D\\uDEF3\\uFE0F|\\uD83C\\uDF24\\uFE0F|\\uD83C\\uDF25\\uFE0F|\\uD83C\\uDF26\\uFE0F|\\uD83D\\uDDB1\\uFE0F|\\u261D\\uD83C\\uDFFB|\\u261D\\uD83C\\uDFFC|\\u261D\\uD83C\\uDFFD|\\u261D\\uD83C\\uDFFE|\\u261D\\uD83C\\uDFFF|\\u270C\\uD83C\\uDFFB|\\u270C\\uD83C\\uDFFC|\\u270C\\uD83C\\uDFFD|\\u270C\\uD83C\\uDFFE|\\u270C\\uD83C\\uDFFF|\\u270A\\uD83C\\uDFFB|\\u270A\\uD83C\\uDFFC|\\u270A\\uD83C\\uDFFD|\\u270A\\uD83C\\uDFFE|\\u270A\\uD83C\\uDFFF|\\u270B\\uD83C\\uDFFB|\\u270B\\uD83C\\uDFFC|\\u270B\\uD83C\\uDFFD|\\u270B\\uD83C\\uDFFE|4\\uFE0F\\u20E3|9\\uFE0F\\u20E3|0\\uFE0F\\u20E3|1\\uFE0F\\u20E3|2\\uFE0F\\u20E3|3\\uFE0F\\u20E3|#\\uFE0F\\u20E3|5\\uFE0F\\u20E3|6\\uFE0F\\u20E3|7\\uFE0F\\u20E3|8\\uFE0F\\u20E3|\\*\\uFE0F\\u20E3|\\u00A9\\uFE0F|\\u00AE\\uFE0F|\\u203C\\uFE0F|\\u2049\\uFE0F|\\u2122\\uFE0F|\\u2139\\uFE0F|\\u2194\\uFE0F|\\u2195\\uFE0F|\\u2196\\uFE0F|\\u2197\\uFE0F|\\u2198\\uFE0F|\\u2199\\uFE0F|\\u21A9\\uFE0F|\\u21AA\\uFE0F|\\u231A\\uFE0F|\\u231B\\uFE0F|\\u24C2\\uFE0F|\\u25AA\\uFE0F|\\u25AB\\uFE0F|\\u25B6\\uFE0F|\\u25C0\\uFE0F|\\u25FB\\uFE0F|\\u25FC\\uFE0F|\\u25FD\\uFE0F|\\u25FE\\uFE0F|\\u2600\\uFE0F|\\u2601\\uFE0F|\\u260E\\uFE0F|\\u2611\\uFE0F|\\u2614\\uFE0F|\\u2615\\uFE0F|\\u261D\\uFE0F|\\u263A\\uFE0F|\\u2648\\uFE0F|\\u2649\\uFE0F|\\u264A\\uFE0F|\\u264B\\uFE0F|\\u264C\\uFE0F|\\u264D\\uFE0F|\\u264E\\uFE0F|\\u264F\\uFE0F|\\u2650\\uFE0F|\\u2651\\uFE0F|\\u2652\\uFE0F|\\u2653\\uFE0F|\\u2660\\uFE0F|\\u2663\\uFE0F|\\u2665\\uFE0F|\\u2666\\uFE0F|\\u2668\\uFE0F|\\u267B\\uFE0F|\\u267F\\uFE0F|\\u2693\\uFE0F|\\u26A0\\uFE0F|\\u26A1\\uFE0F|\\u26AA\\uFE0F|\\u26AB\\uFE0F|\\u26BD\\uFE0F|\\u26BE\\uFE0F|\\u26C4\\uFE0F|\\u26C5\\uFE0F|\\u26D4\\uFE0F|\\u26EA\\uFE0F|\\u26F2\\uFE0F|\\u26F3\\uFE0F|\\u26F5\\uFE0F|\\u26FA\\uFE0F|\\u26FD\\uFE0F|\\u2702\\uFE0F|\\u2708\\uFE0F|\\u2709\\uFE0F|\\u270C\\uFE0F|\\u270F\\uFE0F|\\u2712\\uFE0F|\\u2714\\uFE0F|\\u2716\\uFE0F|\\u2733\\uFE0F|\\u2734\\uFE0F|\\u2744\\uFE0F|\\u2747\\uFE0F|\\u2757\\uFE0F|\\u2764\\uFE0F|\\u27A1\\uFE0F|\\u2934\\uFE0F|\\u2935\\uFE0F|\\u2B05\\uFE0F|\\u2B06\\uFE0F|\\u2B07\\uFE0F|\\u2B1B\\uFE0F|\\u2B1C\\uFE0F|\\u2B50\\uFE0F|\\u2B55\\uFE0F|\\u3030\\uFE0F|\\u303D\\uFE0F|\\u3297\\uFE0F|\\u3299\\uFE0F|\\u271D\\uFE0F|\\u2328\\uFE0F|\\u270D\\uFE0F|\\u23ED\\uFE0F|\\u23EE\\uFE0F|\\u23EF\\uFE0F|\\u23F1\\uFE0F|\\u23F2\\uFE0F|\\u23F8\\uFE0F|\\u23F9\\uFE0F|\\u23FA\\uFE0F|\\u2602\\uFE0F|\\u2603\\uFE0F|\\u2604\\uFE0F|\\u2618\\uFE0F|\\u2620\\uFE0F|\\u2622\\uFE0F|\\u2623\\uFE0F|\\u2626\\uFE0F|\\u262A\\uFE0F|\\u262E\\uFE0F|\\u262F\\uFE0F|\\u2638\\uFE0F|\\u2639\\uFE0F|\\u2692\\uFE0F|\\u2694\\uFE0F|\\u2696\\uFE0F|\\u2697\\uFE0F|\\u2699\\uFE0F|\\u269B\\uFE0F|\\u269C\\uFE0F|\\u26B0\\uFE0F|\\u26B1\\uFE0F|\\u26C8\\uFE0F|\\u26CF\\uFE0F|\\u26D1\\uFE0F|\\u26D3\\uFE0F|\\u26E9\\uFE0F|\\u26F0\\uFE0F|\\u26F1\\uFE0F|\\u26F4\\uFE0F|\\u26F7\\uFE0F|\\u26F8\\uFE0F|\\u26F9\\uFE0F|\\u2721\\uFE0F|\\u2763\\uFE0F|\\uD83C\\uDCCF|\\uD83C\\uDD70|\\uD83C\\uDD71|\\uD83C\\uDD7E|\\uD83C\\uDD8E|\\uD83C\\uDD91|\\uD83C\\uDD92|\\uD83C\\uDD93|\\uD83C\\uDD94|\\uD83C\\uDD95|\\uD83C\\uDD96|\\uD83C\\uDD97|\\uD83C\\uDD98|\\uD83C\\uDD99|\\uD83C\\uDD9A|\\uD83C\\uDE01|\\uD83C\\uDE32|\\uD83C\\uDE33|\\uD83C\\uDE34|\\uD83C\\uDE35|\\uD83C\\uDE36|\\uD83C\\uDE38|\\uD83C\\uDE39|\\uD83C\\uDE3A|\\uD83C\\uDE50|\\uD83C\\uDE51|\\uD83C\\uDF00|\\uD83C\\uDF01|\\uD83C\\uDF02|\\uD83C\\uDF03|\\uD83C\\uDF04|\\uD83C\\uDF05|\\uD83C\\uDF06|\\uD83C\\uDF07|\\uD83C\\uDF08|\\uD83C\\uDF09|\\uD83C\\uDF0A|\\uD83C\\uDF0B|\\uD83C\\uDF0C|\\uD83C\\uDF0F|\\uD83C\\uDF11|\\uD83C\\uDF13|\\uD83C\\uDF14|\\uD83C\\uDF15|\\uD83C\\uDF19|\\uD83C\\uDF1B|\\uD83C\\uDF1F|\\uD83C\\uDF20|\\uD83C\\uDF30|\\uD83C\\uDF31|\\uD83C\\uDF34|\\uD83C\\uDF35|\\uD83C\\uDF37|\\uD83C\\uDF38|\\uD83C\\uDF39|\\uD83C\\uDF3A|\\uD83C\\uDF3B|\\uD83C\\uDF3C|\\uD83C\\uDF3D|\\uD83C\\uDF3E|\\uD83C\\uDF3F|\\uD83C\\uDF40|\\uD83C\\uDF41|\\uD83C\\uDF42|\\uD83C\\uDF43|\\uD83C\\uDF44|\\uD83C\\uDF45|\\uD83C\\uDF46|\\uD83C\\uDF47|\\uD83C\\uDF48|\\uD83C\\uDF49|\\uD83C\\uDF4A|\\uD83C\\uDF4C|\\uD83C\\uDF4D|\\uD83C\\uDF4E|\\uD83C\\uDF4F|\\uD83C\\uDF51|\\uD83C\\uDF52|\\uD83C\\uDF53|\\uD83C\\uDF54|\\uD83C\\uDF55|\\uD83C\\uDF56|\\uD83C\\uDF57|\\uD83C\\uDF58|\\uD83C\\uDF59|\\uD83C\\uDF5A|\\uD83C\\uDF5B|\\uD83C\\uDF5C|\\uD83C\\uDF5D|\\uD83C\\uDF5E|\\uD83C\\uDF5F|\\uD83C\\uDF60|\\uD83C\\uDF61|\\uD83C\\uDF62|\\uD83C\\uDF63|\\uD83C\\uDF64|\\uD83C\\uDF65|\\uD83C\\uDF66|\\uD83C\\uDF67|\\uD83C\\uDF68|\\uD83C\\uDF69|\\uD83C\\uDF6A|\\uD83C\\uDF6B|\\uD83C\\uDF6C|\\uD83C\\uDF6D|\\uD83C\\uDF6E|\\uD83C\\uDF6F|\\uD83C\\uDF70|\\uD83C\\uDF71|\\uD83C\\uDF72|\\uD83C\\uDF73|\\uD83C\\uDF74|\\uD83C\\uDF75|\\uD83C\\uDF76|\\uD83C\\uDF77|\\uD83C\\uDF78|\\uD83C\\uDF79|\\uD83C\\uDF7A|\\uD83C\\uDF7B|\\uD83C\\uDF80|\\uD83C\\uDF81|\\uD83C\\uDF82|\\uD83C\\uDF83|\\uD83C\\uDF84|\\uD83C\\uDF85|\\uD83C\\uDF86|\\uD83C\\uDF87|\\uD83C\\uDF88|\\uD83C\\uDF89|\\uD83C\\uDF8A|\\uD83C\\uDF8B|\\uD83C\\uDF8C|\\uD83C\\uDF8D|\\uD83C\\uDF8E|\\uD83C\\uDF8F|\\uD83C\\uDF90|\\uD83C\\uDF91|\\uD83C\\uDF92|\\uD83C\\uDF93|\\uD83C\\uDFA0|\\uD83C\\uDFA1|\\uD83C\\uDFA2|\\uD83C\\uDFA3|\\uD83C\\uDFA4|\\uD83C\\uDFA5|\\uD83C\\uDFA6|\\uD83C\\uDFA7|\\uD83C\\uDFA8|\\uD83C\\uDFA9|\\uD83C\\uDFAA|\\uD83C\\uDFAB|\\uD83C\\uDFAC|\\uD83C\\uDFAD|\\uD83C\\uDFAE|\\uD83C\\uDFAF|\\uD83C\\uDFB0|\\uD83C\\uDFB1|\\uD83C\\uDFB2|\\uD83C\\uDFB3|\\uD83C\\uDFB4|\\uD83C\\uDFB5|\\uD83C\\uDFB6|\\uD83C\\uDFB7|\\uD83C\\uDFB8|\\uD83C\\uDFB9|\\uD83C\\uDFBA|\\uD83C\\uDFBB|\\uD83C\\uDFBC|\\uD83C\\uDFBD|\\uD83C\\uDFBE|\\uD83C\\uDFBF|\\uD83C\\uDFC0|\\uD83C\\uDFC1|\\uD83C\\uDFC2|\\uD83C\\uDFC3|\\uD83C\\uDFC4|\\uD83C\\uDFC6|\\uD83C\\uDFC8|\\uD83C\\uDFCA|\\uD83C\\uDFE0|\\uD83D\\uDDB1|\\uD83C\\uDFE2|\\uD83C\\uDFE3|\\uD83C\\uDFE5|\\uD83C\\uDFE6|\\uD83C\\uDFE7|\\uD83C\\uDFE8|\\uD83C\\uDFE9|\\uD83C\\uDFEA|\\uD83C\\uDFEB|\\uD83C\\uDFEC|\\uD83C\\uDFED|\\uD83C\\uDFEE|\\uD83C\\uDFEF|\\uD83C\\uDFF0|\\uD83D\\uDC0C|\\uD83D\\uDC0D|\\uD83D\\uDC0E|\\uD83D\\uDC11|\\uD83D\\uDC12|\\uD83D\\uDC14|\\uD83D\\uDC17|\\uD83D\\uDC18|\\uD83D\\uDC19|\\uD83D\\uDC1A|\\uD83D\\uDC1B|\\uD83D\\uDC1C|\\uD83D\\uDC1D|\\uD83D\\uDC1E|\\uD83D\\uDC1F|\\uD83D\\uDC20|\\uD83D\\uDC21|\\uD83D\\uDC22|\\uD83D\\uDC23|\\uD83D\\uDC24|\\uD83D\\uDC25|\\uD83D\\uDC26|\\uD83D\\uDC27|\\uD83D\\uDC28|\\uD83D\\uDC29|\\uD83D\\uDC2B|\\uD83D\\uDC2C|\\uD83D\\uDC2D|\\uD83D\\uDC2E|\\uD83D\\uDC2F|\\uD83D\\uDC30|\\uD83D\\uDC31|\\uD83D\\uDC32|\\uD83D\\uDC33|\\uD83D\\uDC34|\\uD83D\\uDC35|\\uD83D\\uDC36|\\uD83D\\uDC37|\\uD83D\\uDC38|\\uD83D\\uDC39|\\uD83D\\uDC3A|\\uD83D\\uDC3B|\\uD83D\\uDC3C|\\uD83D\\uDC3D|\\uD83D\\uDC3E|\\uD83D\\uDC40|\\uD83D\\uDC42|\\uD83D\\uDC43|\\uD83D\\uDC44|\\uD83D\\uDC45|\\uD83D\\uDC46|\\uD83D\\uDC47|\\uD83D\\uDC48|\\uD83D\\uDC49|\\uD83D\\uDC4A|\\uD83D\\uDC4B|\\uD83D\\uDC4C|\\uD83D\\uDC4D|\\uD83D\\uDC4E|\\uD83D\\uDC4F|\\uD83D\\uDC50|\\uD83D\\uDC51|\\uD83D\\uDC52|\\uD83D\\uDC53|\\uD83D\\uDC54|\\uD83D\\uDC55|\\uD83D\\uDC56|\\uD83D\\uDC57|\\uD83D\\uDC58|\\uD83D\\uDC59|\\uD83D\\uDC5A|\\uD83D\\uDC5B|\\uD83D\\uDC5C|\\uD83D\\uDC5D|\\uD83D\\uDC5E|\\uD83D\\uDC5F|\\uD83D\\uDC60|\\uD83D\\uDC61|\\uD83D\\uDC62|\\uD83D\\uDC63|\\uD83D\\uDC64|\\uD83D\\uDC66|\\uD83D\\uDC67|\\uD83D\\uDC68|\\uD83D\\uDC69|\\uD83D\\uDC6A|\\uD83D\\uDC6B|\\uD83D\\uDC6E|\\uD83D\\uDC6F|\\uD83D\\uDC70|\\uD83D\\uDC71|\\uD83D\\uDC72|\\uD83D\\uDC73|\\uD83D\\uDC74|\\uD83D\\uDC75|\\uD83D\\uDC76|\\uD83D\\uDC77|\\uD83D\\uDC78|\\uD83D\\uDC79|\\uD83D\\uDC7A|\\uD83D\\uDC7B|\\uD83D\\uDC7C|\\uD83D\\uDC7D|\\uD83D\\uDC7E|\\uD83D\\uDC7F|\\uD83D\\uDC80|\\uD83D\\uDCC7|\\uD83D\\uDC81|\\uD83D\\uDC82|\\uD83D\\uDC83|\\uD83D\\uDC84|\\uD83D\\uDC85|\\uD83D\\uDCD2|\\uD83D\\uDC86|\\uD83D\\uDCD3|\\uD83D\\uDC87|\\uD83D\\uDCD4|\\uD83D\\uDC88|\\uD83D\\uDCD5|\\uD83D\\uDC89|\\uD83D\\uDCD6|\\uD83D\\uDC8A|\\uD83D\\uDCD7|\\uD83D\\uDC8B|\\uD83D\\uDCD8|\\uD83D\\uDC8C|\\uD83D\\uDCD9|\\uD83D\\uDC8D|\\uD83D\\uDCDA|\\uD83D\\uDC8E|\\uD83D\\uDCDB|\\uD83D\\uDC8F|\\uD83D\\uDCDC|\\uD83D\\uDC90|\\uD83D\\uDCDD|\\uD83D\\uDC91|\\uD83D\\uDCDE|\\uD83D\\uDC92|\\uD83D\\uDCDF|\\uD83D\\uDCE0|\\uD83D\\uDC93|\\uD83D\\uDCE1|\\uD83D\\uDCE2|\\uD83D\\uDC94|\\uD83D\\uDCE3|\\uD83D\\uDCE4|\\uD83D\\uDC95|\\uD83D\\uDCE5|\\uD83D\\uDCE6|\\uD83D\\uDC96|\\uD83D\\uDCE7|\\uD83D\\uDCE8|\\uD83D\\uDC97|\\uD83D\\uDCE9|\\uD83D\\uDCEA|\\uD83D\\uDC98|\\uD83D\\uDCEB|\\uD83D\\uDCEE|\\uD83D\\uDC99|\\uD83D\\uDCF0|\\uD83D\\uDCF1|\\uD83D\\uDC9A|\\uD83D\\uDCF2|\\uD83D\\uDCF3|\\uD83D\\uDC9B|\\uD83D\\uDCF4|\\uD83D\\uDCF6|\\uD83D\\uDC9C|\\uD83D\\uDCF7|\\uD83D\\uDCF9|\\uD83D\\uDC9D|\\uD83D\\uDCFA|\\uD83D\\uDCFB|\\uD83D\\uDC9E|\\uD83D\\uDCFC|\\uD83D\\uDD03|\\uD83D\\uDC9F|\\uD83D\\uDD0A|\\uD83D\\uDD0B|\\uD83D\\uDCA0|\\uD83D\\uDD0C|\\uD83D\\uDD0D|\\uD83D\\uDCA1|\\uD83D\\uDD0E|\\uD83D\\uDD0F|\\uD83D\\uDCA2|\\uD83D\\uDD10|\\uD83D\\uDD11|\\uD83D\\uDCA3|\\uD83D\\uDD12|\\uD83D\\uDD13|\\uD83D\\uDCA4|\\uD83D\\uDD14|\\uD83D\\uDD16|\\uD83D\\uDCA5|\\uD83D\\uDD17|\\uD83D\\uDD18|\\uD83D\\uDCA6|\\uD83D\\uDD19|\\uD83D\\uDD1A|\\uD83D\\uDCA7|\\uD83D\\uDD1B|\\uD83D\\uDD1C|\\uD83D\\uDCA8|\\uD83D\\uDD1D|\\uD83D\\uDD1E|\\uD83D\\uDCA9|\\uD83D\\uDD1F|\\uD83D\\uDCAA|\\uD83D\\uDD20|\\uD83D\\uDD21|\\uD83D\\uDCAB|\\uD83D\\uDD22|\\uD83D\\uDD23|\\uD83D\\uDCAC|\\uD83D\\uDD24|\\uD83D\\uDD25|\\uD83D\\uDCAE|\\uD83D\\uDD26|\\uD83D\\uDD27|\\uD83D\\uDCAF|\\uD83D\\uDD28|\\uD83D\\uDD29|\\uD83D\\uDCB0|\\uD83D\\uDD2A|\\uD83D\\uDD2B|\\uD83D\\uDCB1|\\uD83D\\uDD2E|\\uD83D\\uDCB2|\\uD83D\\uDD2F|\\uD83D\\uDCB3|\\uD83D\\uDD30|\\uD83D\\uDD31|\\uD83D\\uDCB4|\\uD83D\\uDD32|\\uD83D\\uDD33|\\uD83D\\uDCB5|\\uD83D\\uDD34|\\uD83D\\uDD35|\\uD83D\\uDCB8|\\uD83D\\uDD36|\\uD83D\\uDD37|\\uD83D\\uDCB9|\\uD83D\\uDD38|\\uD83D\\uDD39|\\uD83D\\uDCBA|\\uD83D\\uDD3A|\\uD83D\\uDD3B|\\uD83D\\uDCBB|\\uD83D\\uDD3C|\\uD83D\\uDCBC|\\uD83D\\uDD3D|\\uD83D\\uDD50|\\uD83D\\uDCBD|\\uD83D\\uDD51|\\uD83D\\uDCBE|\\uD83D\\uDD52|\\uD83D\\uDCBF|\\uD83D\\uDD53|\\uD83D\\uDCC0|\\uD83D\\uDD54|\\uD83D\\uDD55|\\uD83D\\uDCC1|\\uD83D\\uDD56|\\uD83D\\uDD57|\\uD83D\\uDCC2|\\uD83D\\uDD58|\\uD83D\\uDD59|\\uD83D\\uDCC3|\\uD83D\\uDD5A|\\uD83D\\uDD5B|\\uD83D\\uDCC4|\\uD83D\\uDDFB|\\uD83D\\uDDFC|\\uD83D\\uDCC5|\\uD83D\\uDDFD|\\uD83D\\uDDFE|\\uD83D\\uDCC6|\\uD83D\\uDDFF|\\uD83D\\uDE01|\\uD83D\\uDE02|\\uD83D\\uDE03|\\uD83D\\uDCC8|\\uD83D\\uDE04|\\uD83D\\uDE05|\\uD83D\\uDCC9|\\uD83D\\uDE06|\\uD83D\\uDE09|\\uD83D\\uDCCA|\\uD83D\\uDE0A|\\uD83D\\uDE0B|\\uD83D\\uDCCB|\\uD83D\\uDE0C|\\uD83D\\uDE0D|\\uD83D\\uDCCC|\\uD83D\\uDE0F|\\uD83D\\uDE12|\\uD83D\\uDCCD|\\uD83D\\uDE13|\\uD83D\\uDE14|\\uD83D\\uDCCE|\\uD83D\\uDE16|\\uD83D\\uDE18|\\uD83D\\uDCCF|\\uD83D\\uDE1A|\\uD83D\\uDE1C|\\uD83D\\uDCD0|\\uD83D\\uDE1D|\\uD83D\\uDE1E|\\uD83D\\uDCD1|\\uD83D\\uDE20|\\uD83D\\uDE21|\\uD83D\\uDE22|\\uD83D\\uDE23|\\uD83D\\uDE24|\\uD83D\\uDE25|\\uD83D\\uDE28|\\uD83D\\uDE29|\\uD83D\\uDE2A|\\uD83D\\uDE2B|\\uD83D\\uDE2D|\\uD83D\\uDE30|\\uD83D\\uDE31|\\uD83D\\uDE32|\\uD83D\\uDE33|\\uD83D\\uDE35|\\uD83D\\uDE37|\\uD83D\\uDE38|\\uD83D\\uDE39|\\uD83D\\uDE3A|\\uD83D\\uDE3B|\\uD83D\\uDE3C|\\uD83D\\uDE3D|\\uD83D\\uDE3E|\\uD83D\\uDE3F|\\uD83D\\uDE40|\\uD83D\\uDE45|\\uD83D\\uDE46|\\uD83D\\uDE47|\\uD83D\\uDE48|\\uD83D\\uDE49|\\uD83D\\uDE4A|\\uD83D\\uDE4B|\\uD83D\\uDE4C|\\uD83D\\uDE4D|\\uD83D\\uDE4E|\\uD83D\\uDE4F|\\uD83D\\uDE80|\\uD83D\\uDE83|\\uD83D\\uDE84|\\uD83D\\uDE85|\\uD83D\\uDE87|\\uD83D\\uDE89|\\uD83D\\uDE8C|\\uD83D\\uDE8F|\\uD83D\\uDE91|\\uD83D\\uDE92|\\uD83D\\uDE93|\\uD83D\\uDE95|\\uD83D\\uDE97|\\uD83D\\uDE99|\\uD83D\\uDE9A|\\uD83D\\uDEA2|\\uD83D\\uDEA4|\\uD83D\\uDEA5|\\uD83D\\uDEA7|\\uD83D\\uDEA8|\\uD83D\\uDEA9|\\uD83D\\uDEAA|\\uD83D\\uDEAB|\\uD83D\\uDEAC|\\uD83D\\uDEAD|\\uD83D\\uDEB2|\\uD83D\\uDEB6|\\uD83D\\uDEB9|\\uD83D\\uDEBA|\\uD83D\\uDEBB|\\uD83D\\uDEBC|\\uD83D\\uDEBD|\\uD83D\\uDEBE|\\uD83D\\uDEC0|\\uD83E\\uDD18|\\uD83D\\uDE00|\\uD83D\\uDE07|\\uD83D\\uDE08|\\uD83D\\uDE0E|\\uD83D\\uDE10|\\uD83D\\uDE11|\\uD83D\\uDE15|\\uD83D\\uDE17|\\uD83D\\uDE19|\\uD83D\\uDE1B|\\uD83D\\uDE1F|\\uD83D\\uDE26|\\uD83D\\uDE27|\\uD83D\\uDE2C|\\uD83D\\uDE2E|\\uD83D\\uDE2F|\\uD83D\\uDE34|\\uD83D\\uDE36|\\uD83D\\uDE81|\\uD83D\\uDE82|\\uD83D\\uDE86|\\uD83D\\uDE88|\\uD83D\\uDE8A|\\uD83D\\uDE8D|\\uD83D\\uDE8E|\\uD83D\\uDE90|\\uD83D\\uDE94|\\uD83D\\uDE96|\\uD83D\\uDE98|\\uD83D\\uDE9B|\\uD83D\\uDE9C|\\uD83D\\uDE9D|\\uD83D\\uDE9E|\\uD83D\\uDE9F|\\uD83D\\uDEA0|\\uD83D\\uDEA1|\\uD83D\\uDEA3|\\uD83D\\uDEA6|\\uD83D\\uDEAE|\\uD83D\\uDEAF|\\uD83D\\uDEB0|\\uD83D\\uDEB1|\\uD83D\\uDEB3|\\uD83D\\uDEB4|\\uD83D\\uDEB5|\\uD83D\\uDEB7|\\uD83D\\uDEB8|\\uD83D\\uDEBF|\\uD83D\\uDEC1|\\uD83D\\uDEC2|\\uD83D\\uDEC3|\\uD83D\\uDEC4|\\uD83D\\uDEC5|\\uD83C\\uDF0D|\\uD83C\\uDF0E|\\uD83C\\uDF10|\\uD83C\\uDF12|\\uD83C\\uDF16|\\uD83C\\uDF17|\\uD83C\\uDF18|\\uD83C\\uDF1A|\\uD83C\\uDF1C|\\uD83C\\uDF1D|\\uD83C\\uDF1E|\\uD83C\\uDF32|\\uD83C\\uDF33|\\uD83C\\uDF4B|\\uD83C\\uDF50|\\uD83C\\uDF7C|\\uD83C\\uDFC7|\\uD83C\\uDFC9|\\uD83C\\uDFE4|\\uD83D\\uDC00|\\uD83D\\uDC01|\\uD83D\\uDC02|\\uD83D\\uDC03|\\uD83D\\uDC04|\\uD83D\\uDC05|\\uD83D\\uDC06|\\uD83D\\uDC07|\\uD83D\\uDC08|\\uD83D\\uDC09|\\uD83D\\uDC0A|\\uD83D\\uDC0B|\\uD83D\\uDC0F|\\uD83D\\uDC10|\\uD83D\\uDC13|\\uD83D\\uDC15|\\uD83D\\uDC16|\\uD83D\\uDC2A|\\uD83D\\uDC65|\\uD83D\\uDC6C|\\uD83D\\uDC6D|\\uD83D\\uDCAD|\\uD83D\\uDCB6|\\uD83D\\uDCB7|\\uD83D\\uDCEC|\\uD83D\\uDCED|\\uD83D\\uDCEF|\\uD83D\\uDCF5|\\uD83D\\uDD00|\\uD83D\\uDD01|\\uD83D\\uDD02|\\uD83D\\uDD04|\\uD83D\\uDD05|\\uD83D\\uDD06|\\uD83D\\uDD07|\\uD83D\\uDD09|\\uD83D\\uDD15|\\uD83D\\uDD2C|\\uD83D\\uDD2D|\\uD83D\\uDD5C|\\uD83D\\uDD5D|\\uD83D\\uDD5E|\\uD83D\\uDD5F|\\uD83D\\uDD60|\\uD83D\\uDD61|\\uD83D\\uDD62|\\uD83D\\uDD63|\\uD83D\\uDD64|\\uD83D\\uDD65|\\uD83D\\uDD66|\\uD83D\\uDD67|\\uD83D\\uDD08|\\uD83D\\uDE8B|\\uD83C\\uDFC5|\\uD83C\\uDFF4|\\uD83D\\uDCF8|\\uD83D\\uDECC|\\uD83D\\uDD95|\\uD83D\\uDD96|\\uD83D\\uDE41|\\uD83D\\uDE42|\\uD83D\\uDEEB|\\uD83D\\uDEEC|\\uD83C\\uDFFB|\\uD83C\\uDFFC|\\uD83C\\uDFFD|\\uD83C\\uDFFE|\\uD83C\\uDFFF|\\uD83D\\uDE43|\\uD83E\\uDD11|\\uD83E\\uDD13|\\uD83E\\uDD17|\\uD83D\\uDE44|\\uD83E\\uDD14|\\uD83E\\uDD10|\\uD83E\\uDD12|\\uD83E\\uDD15|\\uD83E\\uDD16|\\uD83E\\uDD81|\\uD83E\\uDD84|\\uD83E\\uDD82|\\uD83E\\uDD80|\\uD83E\\uDD83|\\uD83E\\uDDC0|\\uD83C\\uDF2D|\\uD83C\\uDF2E|\\uD83C\\uDF2F|\\uD83C\\uDF7F|\\uD83C\\uDF7E|\\uD83C\\uDFF9|\\uD83C\\uDFFA|\\uD83D\\uDED0|\\uD83D\\uDD4B|\\uD83D\\uDD4C|\\uD83D\\uDD4D|\\uD83D\\uDD4E|\\uD83D\\uDCFF|\\uD83C\\uDFCF|\\uD83C\\uDFD0|\\uD83C\\uDFD1|\\uD83C\\uDFD2|\\uD83C\\uDFD3|\\uD83C\\uDFF8|\\uD83C\\uDF26|\\uD83C\\uDF25|\\uD83C\\uDF24|\\uD83D\\uDEF3|\\uD83D\\uDEE9|\\uD83D\\uDEE5|\\uD83D\\uDEE4|\\uD83D\\uDEE3|\\uD83D\\uDECF|\\uD83D\\uDECE|\\uD83D\\uDECD|\\uD83D\\uDECB|\\uD83C\\uDFDF|\\uD83C\\uDFDE|\\uD83C\\uDFDD|\\uD83C\\uDFDC|\\uD83C\\uDFDB|\\uD83C\\uDFDA|\\uD83C\\uDFD9|\\uD83C\\uDFD8|\\uD83C\\uDFD7|\\uD83C\\uDFD6|\\uD83C\\uDFD5|\\uD83C\\uDFD4|\\uD83D\\uDD90|\\uD83D\\uDD75|\\uD83D\\uDD74|\\uD83D\\uDC41|\\uD83C\\uDF7D|\\uD83D\\uDEF0|\\uD83D\\uDEE2|\\uD83D\\uDEE1|\\uD83D\\uDEE0|\\uD83D\\uDDFA|\\uD83D\\uDDF3|\\uD83D\\uDDEF|\\uD83D\\uDDE3|\\uD83D\\uDDE1|\\uD83D\\uDDDE|\\uD83D\\uDDDD|\\uD83D\\uDDDC|\\uD83D\\uDDD3|\\uD83D\\uDDD2|\\uD83D\\uDDD1|\\uD83D\\uDDC4|\\uD83D\\uDDC3|\\uD83D\\uDDC2|\\uD83D\\uDDBC|\\uD83D\\uDDB2|\\uD83D\\uDDA8|\\uD83D\\uDDA5|\\uD83D\\uDD8D|\\uD83D\\uDD8C|\\uD83D\\uDD8B|\\uD83D\\uDD8A|\\uD83D\\uDD87|\\uD83D\\uDD79|\\uD83D\\uDD76|\\uD83D\\uDD73|\\uD83D\\uDD70|\\uD83D\\uDD6F|\\uD83D\\uDD4A|\\uD83D\\uDD49|\\uD83D\\uDCFD|\\uD83C\\uDFF7|\\uD83C\\uDFF5|\\uD83C\\uDFF3|\\uD83C\\uDF9B|\\uD83C\\uDF9A|\\uD83C\\uDF99|\\uD83C\\uDF21|\\uD83D\\uDD78|\\uD83D\\uDD77|\\uD83D\\uDC3F|\\uD83C\\uDF2C|\\uD83C\\uDF2B|\\uD83C\\uDF2A|\\uD83C\\uDF29|\\uD83C\\uDF28|\\uD83C\\uDF27|\\uD83C\\uDF36|\\uD83C\\uDF97|\\uD83C\\uDF96|\\uD83C\\uDFCE|\\uD83C\\uDFCD|\\uD83C\\uDFCC|\\uD83C\\uDFCB|\\uD83C\\uDF9F|\\uD83C\\uDF9E|\\uD83C\\uDE37|\\uD83C\\uDE2F|\\uD83C\\uDE1A|\\uD83C\\uDE02|\\uD83C\\uDD7F|\\uD83C\\uDC04|\\uD83C\\uDFE1|\\u2714|\\u2733|\\u2734|\\u2744|\\u2747|\\u2757|\\u2764|\\u27A1|\\u2934|\\u2935|\\u2B05|\\u2B06|\\u2B07|\\u2B1B|\\u2B1C|\\u2B50|\\u2B55|\\u3030|\\u303D|\\u3297|\\u3299|\\u2712|\\u270F|\\u270C|\\u2709|\\u2708|\\u2702|\\u26FD|\\u26FA|\\u26F5|\\u26F3|\\u26F2|\\u26EA|\\u26D4|\\u26C5|\\u26C4|\\u26BE|\\u26BD|\\u26AB|\\u26AA|\\u26A1|\\u26A0|\\u2693|\\u267F|\\u267B|\\u2668|\\u2666|\\u2665|\\u2663|\\u2660|\\u2653|\\u2652|\\u2651|\\u271D|\\u2650|\\u264F|\\u264E|\\u264D|\\u264C|\\u264B|\\u264A|\\u2649|\\u2648|\\u263A|\\u261D|\\u2615|\\u2614|\\u2611|\\u2328|\\u260E|\\u2601|\\u2600|\\u25FE|\\u25FD|\\u25FC|\\u25FB|\\u25C0|\\u25B6|\\u25AB|\\u25AA|\\u24C2|\\u2716|\\u231A|\\u21AA|\\u21A9|\\u2199|\\u2198|\\u2197|\\u2196|\\u2195|\\u2194|\\u2139|\\u2122|\\u270D|\\u2049|\\u203C|\\u00AE|\\u00A9|\\u27BF|\\u27B0|\\u2797|\\u2796|\\u2795|\\u2755|\\u2754|\\u2753|\\u274E|\\u274C|\\u2728|\\u270B|\\u270A|\\u2705|\\u26CE|\\u23F3|\\u23F0|\\u23EC|\\u23ED|\\u23EE|\\u23EF|\\u23F1|\\u23F2|\\u23F8|\\u23F9|\\u23FA|\\u2602|\\u2603|\\u2604|\\u2618|\\u2620|\\u2622|\\u2623|\\u2626|\\u262A|\\u262E|\\u262F|\\u2638|\\u2639|\\u2692|\\u2694|\\u2696|\\u2697|\\u2699|\\u269B|\\u269C|\\u26B0|\\u26B1|\\u26C8|\\u26CF|\\u26D1|\\u26D3|\\u26E9|\\u26F0|\\u26F1|\\u26F4|\\u26F7|\\u26F8|\\u26F9|\\u2721|\\u2763|\\u23EB|\\u23EA|\\u23E9|\\u231B|\\uD83E\\uDD19\\uD83C\\uDFFE|\\uD83E\\uDD34\\uD83C\\uDFFB|\\uD83E\\uDD34\\uD83C\\uDFFE|\\uD83E\\uDD34\\uD83C\\uDFFF|\\uD83E\\uDD36\\uD83C\\uDFFB|\\uD83E\\uDD36\\uD83C\\uDFFC|\\uD83E\\uDD36\\uD83C\\uDFFD|\\uD83E\\uDD36\\uD83C\\uDFFE|\\uD83E\\uDD36\\uD83C\\uDFFF|\\uD83E\\uDD35\\uD83C\\uDFFB|\\uD83E\\uDD35\\uD83C\\uDFFC|\\uD83E\\uDD35\\uD83C\\uDFFD|\\uD83E\\uDD35\\uD83C\\uDFFE|\\uD83E\\uDD35\\uD83C\\uDFFF|\\uD83E\\uDD37\\uD83C\\uDFFB|\\uD83E\\uDD37\\uD83C\\uDFFC|\\uD83E\\uDD37\\uD83C\\uDFFD|\\uD83E\\uDD37\\uD83C\\uDFFE|\\uD83E\\uDD37\\uD83C\\uDFFF|\\uD83E\\uDD26\\uD83C\\uDFFB|\\uD83E\\uDD26\\uD83C\\uDFFC|\\uD83E\\uDD26\\uD83C\\uDFFD|\\uD83E\\uDD26\\uD83C\\uDFFE|\\uD83E\\uDD26\\uD83C\\uDFFF|\\uD83E\\uDD30\\uD83C\\uDFFB|\\uD83E\\uDD30\\uD83C\\uDFFC|\\uD83E\\uDD30\\uD83C\\uDFFD|\\uD83E\\uDD30\\uD83C\\uDFFE|\\uD83E\\uDD30\\uD83C\\uDFFF|\\uD83D\\uDD7A\\uD83C\\uDFFB|\\uD83D\\uDD7A\\uD83C\\uDFFC|\\uD83D\\uDD7A\\uD83C\\uDFFD|\\uD83D\\uDD7A\\uD83C\\uDFFE|\\uD83D\\uDD7A\\uD83C\\uDFFF|\\uD83E\\uDD33\\uD83C\\uDFFB|\\uD83E\\uDD33\\uD83C\\uDFFC|\\uD83E\\uDD33\\uD83C\\uDFFD|\\uD83E\\uDD33\\uD83C\\uDFFE|\\uD83E\\uDD33\\uD83C\\uDFFF|\\uD83E\\uDD1E\\uD83C\\uDFFB|\\uD83E\\uDD1E\\uD83C\\uDFFC|\\uD83E\\uDD1E\\uD83C\\uDFFD|\\uD83E\\uDD1E\\uD83C\\uDFFE|\\uD83E\\uDD1E\\uD83C\\uDFFF|\\uD83E\\uDD19\\uD83C\\uDFFB|\\uD83E\\uDD19\\uD83C\\uDFFC|\\uD83E\\uDD19\\uD83C\\uDFFD|\\uD83E\\uDD34\\uD83C\\uDFFD|\\uD83E\\uDD19\\uD83C\\uDFFF|\\uD83E\\uDD1B\\uD83C\\uDFFB|\\uD83E\\uDD1B\\uD83C\\uDFFC|\\uD83E\\uDD1B\\uD83C\\uDFFD|\\uD83E\\uDD1B\\uD83C\\uDFFE|\\uD83E\\uDD1B\\uD83C\\uDFFF|\\uD83E\\uDD1C\\uD83C\\uDFFB|\\uD83E\\uDD1C\\uD83C\\uDFFC|\\uD83E\\uDD1C\\uD83C\\uDFFD|\\uD83E\\uDD1C\\uD83C\\uDFFE|\\uD83E\\uDD1C\\uD83C\\uDFFF|\\uD83E\\uDD1A\\uD83C\\uDFFB|\\uD83E\\uDD1A\\uD83C\\uDFFC|\\uD83E\\uDD1A\\uD83C\\uDFFD|\\uD83E\\uDD1A\\uD83C\\uDFFE|\\uD83E\\uDD1A\\uD83C\\uDFFF|\\uD83E\\uDD1D\\uD83C\\uDFFB|\\uD83E\\uDD1D\\uD83C\\uDFFC|\\uD83E\\uDD1D\\uD83C\\uDFFD|\\uD83E\\uDD1D\\uD83C\\uDFFE|\\uD83E\\uDD1D\\uD83C\\uDFFF|\\uD83E\\uDD38\\uD83C\\uDFFB|\\uD83E\\uDD38\\uD83C\\uDFFC|\\uD83E\\uDD38\\uD83C\\uDFFD|\\uD83E\\uDD38\\uD83C\\uDFFE|\\uD83E\\uDD38\\uD83C\\uDFFF|\\uD83E\\uDD3C\\uD83C\\uDFFC|\\uD83E\\uDD3C\\uD83C\\uDFFB|\\uD83E\\uDD3C\\uD83C\\uDFFD|\\uD83E\\uDD3C\\uD83C\\uDFFE|\\uD83E\\uDD3C\\uD83C\\uDFFF|\\uD83E\\uDD3D\\uD83C\\uDFFB|\\uD83E\\uDD3D\\uD83C\\uDFFC|\\uD83E\\uDD3D\\uD83C\\uDFFD|\\uD83E\\uDD3D\\uD83C\\uDFFE|\\uD83E\\uDD3D\\uD83C\\uDFFF|\\uD83E\\uDD3E\\uD83C\\uDFFB|\\uD83E\\uDD3E\\uD83C\\uDFFC|\\uD83E\\uDD3E\\uD83C\\uDFFD|\\uD83E\\uDD3E\\uD83C\\uDFFE|\\uD83E\\uDD3E\\uD83C\\uDFFF|\\uD83E\\uDD39\\uD83C\\uDFFB|\\uD83E\\uDD39\\uD83C\\uDFFC|\\uD83E\\uDD39\\uD83C\\uDFFD|\\uD83E\\uDD39\\uD83C\\uDFFE|\\uD83E\\uDD39\\uD83C\\uDFFF|\\uD83E\\uDD34\\uD83C\\uDFFC|\\uD83E\\uDD49|\\uD83E\\uDD48|\\uD83E\\uDD47|\\uD83E\\uDD3A|\\uD83E\\uDD45|\\uD83E\\uDD3E|\\uD83C\\uDDFF|\\uD83E\\uDD3D|\\uD83E\\uDD4B|\\uD83E\\uDD4A|\\uD83E\\uDD3C|\\uD83E\\uDD39|\\uD83E\\uDD38|\\uD83D\\uDEF6|\\uD83D\\uDEF5|\\uD83D\\uDEF4|\\uD83D\\uDED2|\\uD83D\\uDED1|\\uD83C\\uDDFE|\\uD83E\\uDD44|\\uD83E\\uDD42|\\uD83E\\uDD43|\\uD83E\\uDD59|\\uD83E\\uDD58|\\uD83E\\uDD57|\\uD83E\\uDD56|\\uD83E\\uDD55|\\uD83E\\uDD54|\\uD83E\\uDD53|\\uD83E\\uDD52|\\uD83E\\uDD51|\\uD83E\\uDD50|\\uD83E\\uDD40|\\uD83E\\uDD8F|\\uD83E\\uDD8E|\\uD83E\\uDD8D|\\uD83E\\uDD8C|\\uD83E\\uDD8B|\\uD83E\\uDD8A|\\uD83E\\uDD89|\\uD83E\\uDD88|\\uD83E\\uDD87|\\uD83C\\uDDFD|\\uD83E\\uDD86|\\uD83E\\uDD85|\\uD83D\\uDDA4|\\uD83E\\uDD1E|\\uD83E\\uDD1D|\\uD83E\\uDD1B|\\uD83E\\uDD1C|\\uD83E\\uDD1A|\\uD83E\\uDD19|\\uD83D\\uDD7A|\\uD83E\\uDD33|\\uD83E\\uDD30|\\uD83E\\uDD26|\\uD83E\\uDD37|\\uD83E\\uDD36|\\uD83E\\uDD35|\\uD83E\\uDD34|\\uD83E\\uDD27|\\uD83E\\uDD25|\\uD83E\\uDD24|\\uD83E\\uDD23|\\uD83E\\uDD22|\\uD83E\\uDD21|\\uD83E\\uDD20|\\uD83E\\uDD41|\\uD83E\\uDD90|\\uD83E\\uDD91|\\uD83E\\uDD5A|\\uD83E\\uDD5B|\\uD83E\\uDD5C|\\uD83E\\uDD5D|\\uD83E\\uDD5E|\\uD83C\\uDDFC|\\uD83C\\uDDFB|\\uD83C\\uDDFA|\\uD83C\\uDDF9|\\uD83C\\uDDF8|\\uD83C\\uDDF7|\\uD83C\\uDDF6|\\uD83C\\uDDF5|\\uD83C\\uDDF4|\\uD83C\\uDDF3|\\uD83C\\uDDF2|\\uD83C\\uDDF1|\\uD83C\\uDDF0|\\uD83C\\uDDEF|\\uD83C\\uDDEE|\\uD83C\\uDDED|\\uD83C\\uDDEC|\\uD83C\\uDDEB|\\uD83C\\uDDEA|\\uD83C\\uDDE9|\\uD83C\\uDDE8|\\uD83C\\uDDE7|\\uD83C\\uDDE6|\\uD83C\\uDF26|\\uD83C\\uDF25|\\uD83C\\uDF24|\\uD83D\\uDEF3|\\uD83D\\uDEE9|\\uD83D\\uDEE5|\\uD83D\\uDEE4|\\uD83D\\uDEE3|\\uD83D\\uDECF|\\uD83D\\uDECE|\\uD83D\\uDECD|\\uD83D\\uDECB|\\uD83C\\uDFDF|\\uD83C\\uDFDE|\\uD83C\\uDFDD|\\uD83C\\uDFDC|\\uD83C\\uDFDB|\\uD83C\\uDFDA|\\uD83C\\uDFD9|\\uD83C\\uDFD8|\\uD83C\\uDFD7|\\uD83C\\uDFD6|\\uD83C\\uDFD5|\\uD83C\\uDFD4|\\uD83D\\uDD90|\\uD83D\\uDD75|\\uD83D\\uDD74|\\uD83D\\uDC41|\\uD83C\\uDF7D|\\uD83D\\uDDB1|\\uD83D\\uDEF0|\\uD83D\\uDEE2|\\uD83D\\uDEE1|\\uD83D\\uDEE0|\\uD83D\\uDDFA|\\uD83D\\uDDF3|\\uD83D\\uDDEF|\\uD83D\\uDDE8|\\uD83D\\uDDE3|\\uD83D\\uDDE1|\\uD83D\\uDDDE|\\uD83D\\uDDDD|\\uD83D\\uDDDC|\\uD83D\\uDDD3|\\uD83D\\uDDD2|\\uD83D\\uDDD1|\\uD83D\\uDDC4|\\uD83D\\uDDC3|\\uD83D\\uDDC2|\\uD83D\\uDDBC|\\uD83D\\uDDB2|\\uD83D\\uDDA8|\\uD83D\\uDDA5|\\uD83D\\uDD8D|\\uD83D\\uDD8C|\\uD83D\\uDD8B|\\uD83D\\uDD8A|\\uD83D\\uDD87|\\uD83D\\uDD79|\\uD83D\\uDD76|\\uD83D\\uDD73|\\uD83D\\uDD70|\\uD83D\\uDD6F|\\uD83D\\uDD4A|\\uD83D\\uDD49|\\uD83D\\uDCFD|\\uD83C\\uDFF7|\\uD83C\\uDFF5|\\uD83C\\uDFF3|\\uD83C\\uDF9B|\\uD83C\\uDF9A|\\uD83C\\uDF99|\\uD83C\\uDF21|\\uD83D\\uDD78|\\uD83D\\uDD77|\\uD83D\\uDC3F|\\uD83C\\uDF2C|\\uD83C\\uDF2B|\\uD83C\\uDF2A|\\uD83C\\uDF29|\\uD83C\\uDF28|\\uD83C\\uDF27|\\uD83C\\uDF36|\\uD83C\\uDF97|\\uD83C\\uDF96|\\uD83C\\uDFCE|\\uD83C\\uDFCD|\\uD83C\\uDFCC|\\uD83C\\uDFCB|\\uD83C\\uDF9F|\\uD83C\\uDF9E|\\uD83C\\uDE37|\\uD83C\\uDE2F|\\uD83C\\uDE1A|\\uD83C\\uDE02|\\uD83C\\uDD7F|\\uD83C\\uDC04|\\u25C0|\\u2B05|\\u2B07|\\u2B1B|\\u2B1C|\\u2B50|\\u2B55|\\u3030|\\u303D|\\u3297|\\u3299|\\u2935|\\u2934|\\u27A1|\\u2764|\\u2757|\\u2747|\\u2744|\\u2734|\\u2733|\\u2716|\\u2714|\\u2712|\\u270F|\\u270C|\\u2709|\\u2708|\\u2702|\\u26FD|\\u26FA|\\u26F5|\\u26F3|\\u26F2|\\u26EA|\\u26D4|\\u26C5|\\u26C4|\\u26BE|\\u26BD|\\u26AB|\\u26AA|\\u26A1|\\u26A0|\\u271D|\\u2693|\\u267F|\\u267B|\\u2668|\\u2666|\\u2665|\\u2663|\\u2660|\\u2653|\\u2652|\\u2651|\\u2650|\\u264F|\\u264E|\\u2328|\\u264D|\\u264C|\\u264B|\\u264A|\\u2649|\\u2648|\\u263A|\\u261D|\\u2615|\\u2614|\\u2611|\\u260E|\\u2601|\\u2600|\\u25FE|\\u25FD|\\u25FC|\\u25FB|\\u2B06|\\u25B6|\\u25AB|\\u24C2|\\u231B|\\u231A|\\u21AA|\\u270D|\\u21A9|\\u2199|\\u2198|\\u2197|\\u2196|\\u2195|\\u2194|\\u2139|\\u2122|\\u2049|\\u203C|\\u00AE|\\u00A9|\\u2763|\\u2721|\\u26F9|\\u26F8|\\u26F7|\\u26F4|\\u26F1|\\u26F0|\\u26E9|\\u23CF|\\u23ED|\\u23EE|\\u23EF|\\u23F1|\\u23F2|\\u23F8|\\u23F9|\\u23FA|\\u2602|\\u2603|\\u2604|\\u2618|\\u2620|\\u2622|\\u2623|\\u2626|\\u262A|\\u262E|\\u262F|\\u2638|\\u2639|\\u2692|\\u2694|\\u2696|\\u2697|\\u2699|\\u269B|\\u269C|\\u26B0|\\u26B1|\\u26C8|\\u26CF|\\u26D1|\\u26D3|\\u25AA)",
    a.jsEscapeMap={"👩❤💋👩":"1f469-2764-1f48b-1f469","👨❤💋👨":"1f468-2764-1f48b-1f468","👨👨👦👦":"1f468-1f468-1f466-1f466","👨👨👧👦":"1f468-1f468-1f467-1f466","👨👨👧👧":"1f468-1f468-1f467-1f467","👨👩👦👦":"1f468-1f469-1f466-1f466","👨👩👧👦":"1f468-1f469-1f467-1f466","👨👩👧👧":"1f468-1f469-1f467-1f467","👩👩👦👦":"1f469-1f469-1f466-1f466","👩👩👧👦":"1f469-1f469-1f467-1f466","👩👩👧👧":"1f469-1f469-1f467-1f467","👩❤👩":"1f469-2764-1f469","👨❤👨":"1f468-2764-1f468","👨👨👦":"1f468-1f468-1f466","👨👨👧":"1f468-1f468-1f467","👨👩👧":"1f468-1f469-1f467","👩👩👦":"1f469-1f469-1f466","👩👩👧":"1f469-1f469-1f467","👁🗨":"1f441-1f5e8","#⃣":"0023-20e3","0⃣":"0030-20e3","1⃣":"0031-20e3","2⃣":"0032-20e3","3⃣":"0033-20e3","4⃣":"0034-20e3","5⃣":"0035-20e3","6⃣":"0036-20e3","7⃣":"0037-20e3","8⃣":"0038-20e3","9⃣":"0039-20e3","*⃣":"002a-20e3","🤾🏿":"1f93e-1f3ff","🤾🏾":"1f93e-1f3fe","🤾🏽":"1f93e-1f3fd","🤾🏼":"1f93e-1f3fc","🤾🏻":"1f93e-1f3fb","🤽🏿":"1f93d-1f3ff","🤽🏾":"1f93d-1f3fe","🤽🏽":"1f93d-1f3fd","🤽🏼":"1f93d-1f3fc","🤽🏻":"1f93d-1f3fb","🤼🏿":"1f93c-1f3ff","🤼🏾":"1f93c-1f3fe","🤼🏽":"1f93c-1f3fd","🤼🏼":"1f93c-1f3fc","🤼🏻":"1f93c-1f3fb","🤹🏿":"1f939-1f3ff","🤹🏾":"1f939-1f3fe","🤹🏽":"1f939-1f3fd","🤹🏼":"1f939-1f3fc","🤹🏻":"1f939-1f3fb","🤸🏿":"1f938-1f3ff","🤸🏾":"1f938-1f3fe","🤸🏽":"1f938-1f3fd","🤸🏼":"1f938-1f3fc","🤸🏻":"1f938-1f3fb","🤷🏿":"1f937-1f3ff","🤷🏾":"1f937-1f3fe","🤷🏽":"1f937-1f3fd","🤷🏼":"1f937-1f3fc","🤷🏻":"1f937-1f3fb","🤶🏿":"1f936-1f3ff","🤶🏾":"1f936-1f3fe","🤶🏽":"1f936-1f3fd","🤶🏼":"1f936-1f3fc","🤶🏻":"1f936-1f3fb","🤵🏿":"1f935-1f3ff","🤵🏾":"1f935-1f3fe","🤵🏽":"1f935-1f3fd","🤵🏼":"1f935-1f3fc","🤵🏻":"1f935-1f3fb","🤴🏿":"1f934-1f3ff","🤴🏾":"1f934-1f3fe","🤴🏽":"1f934-1f3fd","🤴🏼":"1f934-1f3fc","🤴🏻":"1f934-1f3fb","🤳🏿":"1f933-1f3ff","🤳🏾":"1f933-1f3fe","🤳🏽":"1f933-1f3fd","🤳🏼":"1f933-1f3fc","🤳🏻":"1f933-1f3fb","🤰🏿":"1f930-1f3ff","🤰🏾":"1f930-1f3fe","🤰🏽":"1f930-1f3fd","🤰🏼":"1f930-1f3fc","🤰🏻":"1f930-1f3fb","🤦🏿":"1f926-1f3ff","🤦🏾":"1f926-1f3fe","🤦🏽":"1f926-1f3fd","🤦🏼":"1f926-1f3fc","🤦🏻":"1f926-1f3fb","🤞🏿":"1f91e-1f3ff","🤞🏾":"1f91e-1f3fe","🤞🏽":"1f91e-1f3fd","🤞🏼":"1f91e-1f3fc","🤞🏻":"1f91e-1f3fb","🤝🏿":"1f91d-1f3ff","🤝🏾":"1f91d-1f3fe","🤝🏽":"1f91d-1f3fd","🤝🏼":"1f91d-1f3fc","🤝🏻":"1f91d-1f3fb","🤜🏿":"1f91c-1f3ff","🤜🏾":"1f91c-1f3fe","🤜🏽":"1f91c-1f3fd","🤜🏼":"1f91c-1f3fc","🤜🏻":"1f91c-1f3fb","🤛🏿":"1f91b-1f3ff","🤛🏾":"1f91b-1f3fe","🤛🏽":"1f91b-1f3fd","🤛🏼":"1f91b-1f3fc","🤛🏻":"1f91b-1f3fb","🤚🏿":"1f91a-1f3ff","🤚🏾":"1f91a-1f3fe","🤚🏽":"1f91a-1f3fd","🤚🏼":"1f91a-1f3fc","🤚🏻":"1f91a-1f3fb","🤙🏿":"1f919-1f3ff","🤙🏾":"1f919-1f3fe","🤙🏽":"1f919-1f3fd","🤙🏼":"1f919-1f3fc","🤙🏻":"1f919-1f3fb","🤘🏿":"1f918-1f3ff","🤘🏾":"1f918-1f3fe","🤘🏽":"1f918-1f3fd","🤘🏼":"1f918-1f3fc","🤘🏻":"1f918-1f3fb","🛀🏿":"1f6c0-1f3ff","🛀🏾":"1f6c0-1f3fe","🛀🏽":"1f6c0-1f3fd","🛀🏼":"1f6c0-1f3fc","🛀🏻":"1f6c0-1f3fb","🚶🏿":"1f6b6-1f3ff","🚶🏾":"1f6b6-1f3fe","🚶🏽":"1f6b6-1f3fd","🚶🏼":"1f6b6-1f3fc","🚶🏻":"1f6b6-1f3fb","🚵🏿":"1f6b5-1f3ff","🚵🏾":"1f6b5-1f3fe","🚵🏽":"1f6b5-1f3fd","🚵🏼":"1f6b5-1f3fc","🚵🏻":"1f6b5-1f3fb","🚴🏿":"1f6b4-1f3ff","🚴🏾":"1f6b4-1f3fe","🚴🏽":"1f6b4-1f3fd","🚴🏼":"1f6b4-1f3fc","🚴🏻":"1f6b4-1f3fb","🚣🏿":"1f6a3-1f3ff","🚣🏾":"1f6a3-1f3fe","🚣🏽":"1f6a3-1f3fd","🚣🏼":"1f6a3-1f3fc","🚣🏻":"1f6a3-1f3fb","🙏🏿":"1f64f-1f3ff","🙏🏾":"1f64f-1f3fe","🙏🏽":"1f64f-1f3fd","🙏🏼":"1f64f-1f3fc","🙏🏻":"1f64f-1f3fb","🙎🏿":"1f64e-1f3ff","🙎🏾":"1f64e-1f3fe","🙎🏽":"1f64e-1f3fd","🙎🏼":"1f64e-1f3fc","🙎🏻":"1f64e-1f3fb","🙍🏿":"1f64d-1f3ff","🙍🏾":"1f64d-1f3fe","🙍🏽":"1f64d-1f3fd","🙍🏼":"1f64d-1f3fc","🙍🏻":"1f64d-1f3fb","🙌🏿":"1f64c-1f3ff","🙌🏾":"1f64c-1f3fe","🙌🏽":"1f64c-1f3fd","🙌🏼":"1f64c-1f3fc","🙌🏻":"1f64c-1f3fb","🙋🏿":"1f64b-1f3ff","🙋🏾":"1f64b-1f3fe","🙋🏽":"1f64b-1f3fd","🙋🏼":"1f64b-1f3fc","🙋🏻":"1f64b-1f3fb","🙇🏿":"1f647-1f3ff","🙇🏾":"1f647-1f3fe","🙇🏽":"1f647-1f3fd","🙇🏼":"1f647-1f3fc","🙇🏻":"1f647-1f3fb","🙆🏿":"1f646-1f3ff","🙆🏾":"1f646-1f3fe","🙆🏽":"1f646-1f3fd","🙆🏼":"1f646-1f3fc","🙆🏻":"1f646-1f3fb","🙅🏿":"1f645-1f3ff","🙅🏾":"1f645-1f3fe","🙅🏽":"1f645-1f3fd","🙅🏼":"1f645-1f3fc","🙅🏻":"1f645-1f3fb","🖖🏿":"1f596-1f3ff","🖖🏾":"1f596-1f3fe","🖖🏽":"1f596-1f3fd","🖖🏼":"1f596-1f3fc","🖖🏻":"1f596-1f3fb","🖕🏿":"1f595-1f3ff","🖕🏾":"1f595-1f3fe","🖕🏽":"1f595-1f3fd","🖕🏼":"1f595-1f3fc","🖕🏻":"1f595-1f3fb","🖐🏿":"1f590-1f3ff","🖐🏾":"1f590-1f3fe","🖐🏽":"1f590-1f3fd","🖐🏼":"1f590-1f3fc","🖐🏻":"1f590-1f3fb","🕺🏿":"1f57a-1f3ff","🕺🏾":"1f57a-1f3fe","🕺🏽":"1f57a-1f3fd","🕺🏼":"1f57a-1f3fc","🕺🏻":"1f57a-1f3fb","🕵🏿":"1f575-1f3ff","🕵🏾":"1f575-1f3fe","🕵🏽":"1f575-1f3fd","🕵🏼":"1f575-1f3fc","🕵🏻":"1f575-1f3fb","💪🏿":"1f4aa-1f3ff","💪🏾":"1f4aa-1f3fe","💪🏽":"1f4aa-1f3fd","💪🏼":"1f4aa-1f3fc","💪🏻":"1f4aa-1f3fb","💇🏿":"1f487-1f3ff","💇🏾":"1f487-1f3fe","💇🏽":"1f487-1f3fd","💇🏼":"1f487-1f3fc","💇🏻":"1f487-1f3fb","💆🏿":"1f486-1f3ff","💆🏾":"1f486-1f3fe","💆🏽":"1f486-1f3fd","💆🏼":"1f486-1f3fc","💆🏻":"1f486-1f3fb","💅🏿":"1f485-1f3ff","💅🏾":"1f485-1f3fe","💅🏽":"1f485-1f3fd","💅🏼":"1f485-1f3fc","💅🏻":"1f485-1f3fb","💃🏿":"1f483-1f3ff","💃🏾":"1f483-1f3fe","💃🏽":"1f483-1f3fd","💃🏼":"1f483-1f3fc","💃🏻":"1f483-1f3fb","💂🏿":"1f482-1f3ff","💂🏾":"1f482-1f3fe","💂🏽":"1f482-1f3fd","💂🏼":"1f482-1f3fc","💂🏻":"1f482-1f3fb","💁🏿":"1f481-1f3ff","💁🏾":"1f481-1f3fe","💁🏽":"1f481-1f3fd","💁🏼":"1f481-1f3fc","💁🏻":"1f481-1f3fb","👼🏿":"1f47c-1f3ff","👼🏾":"1f47c-1f3fe","👼🏽":"1f47c-1f3fd","👼🏼":"1f47c-1f3fc","👼🏻":"1f47c-1f3fb","👸🏿":"1f478-1f3ff","👸🏾":"1f478-1f3fe","👸🏽":"1f478-1f3fd","👸🏼":"1f478-1f3fc","👸🏻":"1f478-1f3fb","👷🏿":"1f477-1f3ff","👷🏾":"1f477-1f3fe","👷🏽":"1f477-1f3fd","👷🏼":"1f477-1f3fc","👷🏻":"1f477-1f3fb","👶🏿":"1f476-1f3ff","👶🏾":"1f476-1f3fe","👶🏽":"1f476-1f3fd","👶🏼":"1f476-1f3fc","👶🏻":"1f476-1f3fb","👵🏿":"1f475-1f3ff","👵🏾":"1f475-1f3fe","👵🏽":"1f475-1f3fd","👵🏼":"1f475-1f3fc","👵🏻":"1f475-1f3fb","👴🏿":"1f474-1f3ff","👴🏾":"1f474-1f3fe","👴🏽":"1f474-1f3fd","👴🏼":"1f474-1f3fc","👴🏻":"1f474-1f3fb","👳🏿":"1f473-1f3ff","👳🏾":"1f473-1f3fe","👳🏽":"1f473-1f3fd","👳🏼":"1f473-1f3fc","👳🏻":"1f473-1f3fb","👲🏿":"1f472-1f3ff","👲🏾":"1f472-1f3fe","👲🏽":"1f472-1f3fd","👲🏼":"1f472-1f3fc","👲🏻":"1f472-1f3fb","👱🏿":"1f471-1f3ff","👱🏾":"1f471-1f3fe","👱🏽":"1f471-1f3fd","👱🏼":"1f471-1f3fc","👱🏻":"1f471-1f3fb","👰🏿":"1f470-1f3ff","👰🏾":"1f470-1f3fe","👰🏽":"1f470-1f3fd","👰🏼":"1f470-1f3fc","👰🏻":"1f470-1f3fb","👮🏿":"1f46e-1f3ff","👮🏾":"1f46e-1f3fe","👮🏽":"1f46e-1f3fd","👮🏼":"1f46e-1f3fc","👮🏻":"1f46e-1f3fb","👩🏿":"1f469-1f3ff","👩🏾":"1f469-1f3fe","👩🏽":"1f469-1f3fd","👩🏼":"1f469-1f3fc","👩🏻":"1f469-1f3fb","👨🏿":"1f468-1f3ff","👨🏾":"1f468-1f3fe","👨🏽":"1f468-1f3fd","👨🏼":"1f468-1f3fc","👨🏻":"1f468-1f3fb","👧🏿":"1f467-1f3ff","👧🏾":"1f467-1f3fe","👧🏽":"1f467-1f3fd","👧🏼":"1f467-1f3fc","👧🏻":"1f467-1f3fb","👦🏿":"1f466-1f3ff","👦🏾":"1f466-1f3fe","👦🏽":"1f466-1f3fd","👦🏼":"1f466-1f3fc","👦🏻":"1f466-1f3fb","👐🏿":"1f450-1f3ff","👐🏾":"1f450-1f3fe","👐🏽":"1f450-1f3fd","👐🏼":"1f450-1f3fc","👐🏻":"1f450-1f3fb","👏🏿":"1f44f-1f3ff","👏🏾":"1f44f-1f3fe","👏🏽":"1f44f-1f3fd","👏🏼":"1f44f-1f3fc","👏🏻":"1f44f-1f3fb","👎🏿":"1f44e-1f3ff","👎🏾":"1f44e-1f3fe","👎🏽":"1f44e-1f3fd","👎🏼":"1f44e-1f3fc","👎🏻":"1f44e-1f3fb","👍🏿":"1f44d-1f3ff","👍🏾":"1f44d-1f3fe","👍🏽":"1f44d-1f3fd","👍🏼":"1f44d-1f3fc","👍🏻":"1f44d-1f3fb","👌🏿":"1f44c-1f3ff","👌🏾":"1f44c-1f3fe","👌🏽":"1f44c-1f3fd","👌🏼":"1f44c-1f3fc","👌🏻":"1f44c-1f3fb","👋🏿":"1f44b-1f3ff","👋🏾":"1f44b-1f3fe","👋🏽":"1f44b-1f3fd","👋🏼":"1f44b-1f3fc","👋🏻":"1f44b-1f3fb","👊🏿":"1f44a-1f3ff","👊🏾":"1f44a-1f3fe","👊🏽":"1f44a-1f3fd","👊🏼":"1f44a-1f3fc","👊🏻":"1f44a-1f3fb","👉🏿":"1f449-1f3ff","👉🏾":"1f449-1f3fe","👉🏽":"1f449-1f3fd","👉🏼":"1f449-1f3fc","👉🏻":"1f449-1f3fb","👈🏿":"1f448-1f3ff","👈🏾":"1f448-1f3fe","👈🏽":"1f448-1f3fd","👈🏼":"1f448-1f3fc","👈🏻":"1f448-1f3fb","👇🏿":"1f447-1f3ff","👇🏾":"1f447-1f3fe","👇🏽":"1f447-1f3fd","👇🏼":"1f447-1f3fc","👇🏻":"1f447-1f3fb","👆🏿":"1f446-1f3ff","👆🏾":"1f446-1f3fe","👆🏽":"1f446-1f3fd","👆🏼":"1f446-1f3fc","👆🏻":"1f446-1f3fb","👃🏿":"1f443-1f3ff","👃🏾":"1f443-1f3fe","👃🏽":"1f443-1f3fd","👃🏼":"1f443-1f3fc","👃🏻":"1f443-1f3fb","👂🏿":"1f442-1f3ff","👂🏾":"1f442-1f3fe","👂🏽":"1f442-1f3fd","👂🏼":"1f442-1f3fc","👂🏻":"1f442-1f3fb","🏳🌈":"1f3f3-1f308","🏋🏿":"1f3cb-1f3ff","🏋🏾":"1f3cb-1f3fe","🏋🏽":"1f3cb-1f3fd","🏋🏼":"1f3cb-1f3fc","🏋🏻":"1f3cb-1f3fb","🏊🏿":"1f3ca-1f3ff","🏊🏾":"1f3ca-1f3fe","🏊🏽":"1f3ca-1f3fd","🏊🏼":"1f3ca-1f3fc","🏊🏻":"1f3ca-1f3fb","🏇🏿":"1f3c7-1f3ff","🏇🏾":"1f3c7-1f3fe","🏇🏽":"1f3c7-1f3fd","🏇🏼":"1f3c7-1f3fc","🏇🏻":"1f3c7-1f3fb","🏄🏿":"1f3c4-1f3ff","🏄🏾":"1f3c4-1f3fe","🏄🏽":"1f3c4-1f3fd","🏄🏼":"1f3c4-1f3fc","🏄🏻":"1f3c4-1f3fb","🏃🏿":"1f3c3-1f3ff","🏃🏾":"1f3c3-1f3fe","🏃🏽":"1f3c3-1f3fd","🏃🏼":"1f3c3-1f3fc","🏃🏻":"1f3c3-1f3fb","🎅🏿":"1f385-1f3ff","🎅🏾":"1f385-1f3fe","🎅🏽":"1f385-1f3fd","🎅🏼":"1f385-1f3fc","🎅🏻":"1f385-1f3fb","🇿🇼":"1f1ff-1f1fc","🇿🇲":"1f1ff-1f1f2","🇿🇦":"1f1ff-1f1e6","🇾🇹":"1f1fe-1f1f9","🇾🇪":"1f1fe-1f1ea","🇽🇰":"1f1fd-1f1f0","🇼🇸":"1f1fc-1f1f8","🇼🇫":"1f1fc-1f1eb","🇻🇺":"1f1fb-1f1fa","🇻🇳":"1f1fb-1f1f3","🇻🇮":"1f1fb-1f1ee","🇻🇬":"1f1fb-1f1ec","🇻🇪":"1f1fb-1f1ea","🇻🇨":"1f1fb-1f1e8","🇻🇦":"1f1fb-1f1e6","🇺🇿":"1f1fa-1f1ff","🇺🇾":"1f1fa-1f1fe","🇺🇸":"1f1fa-1f1f8","🇺🇲":"1f1fa-1f1f2","🇺🇬":"1f1fa-1f1ec","🇺🇦":"1f1fa-1f1e6","🇹🇿":"1f1f9-1f1ff","🇹🇼":"1f1f9-1f1fc","🇹🇻":"1f1f9-1f1fb","🇹🇹":"1f1f9-1f1f9","🇹🇷":"1f1f9-1f1f7","🇹🇴":"1f1f9-1f1f4","🇹🇳":"1f1f9-1f1f3","🇹🇲":"1f1f9-1f1f2","🇹🇱":"1f1f9-1f1f1","🇹🇰":"1f1f9-1f1f0","🇹🇯":"1f1f9-1f1ef","🇹🇭":"1f1f9-1f1ed","🇹🇬":"1f1f9-1f1ec","🇹🇫":"1f1f9-1f1eb","🇹🇩":"1f1f9-1f1e9","🇹🇨":"1f1f9-1f1e8","🇹🇦":"1f1f9-1f1e6","🇸🇿":"1f1f8-1f1ff","🇸🇾":"1f1f8-1f1fe","🇸🇽":"1f1f8-1f1fd","🇸🇻":"1f1f8-1f1fb","🇸🇹":"1f1f8-1f1f9","🇸🇸":"1f1f8-1f1f8","🇸🇷":"1f1f8-1f1f7","🇸🇴":"1f1f8-1f1f4","🇸🇳":"1f1f8-1f1f3","🇸🇲":"1f1f8-1f1f2","🇸🇱":"1f1f8-1f1f1","🇸🇰":"1f1f8-1f1f0","🇸🇯":"1f1f8-1f1ef","🇸🇮":"1f1f8-1f1ee","🇸🇭":"1f1f8-1f1ed","🇸🇬":"1f1f8-1f1ec","🇸🇪":"1f1f8-1f1ea","🇸🇩":"1f1f8-1f1e9","🇸🇨":"1f1f8-1f1e8","🇸🇧":"1f1f8-1f1e7","🇸🇦":"1f1f8-1f1e6","🇷🇼":"1f1f7-1f1fc","🇷🇺":"1f1f7-1f1fa","🇷🇸":"1f1f7-1f1f8","🇷🇴":"1f1f7-1f1f4","🇷🇪":"1f1f7-1f1ea","🇶🇦":"1f1f6-1f1e6","🇵🇾":"1f1f5-1f1fe","🇵🇼":"1f1f5-1f1fc","🇵🇹":"1f1f5-1f1f9","🇵🇸":"1f1f5-1f1f8","🇵🇷":"1f1f5-1f1f7","🇵🇳":"1f1f5-1f1f3","🇵🇲":"1f1f5-1f1f2","🇵🇱":"1f1f5-1f1f1","🇵🇰":"1f1f5-1f1f0","🇵🇭":"1f1f5-1f1ed","🇵🇬":"1f1f5-1f1ec","🇵🇫":"1f1f5-1f1eb","🇵🇪":"1f1f5-1f1ea","🇵🇦":"1f1f5-1f1e6","🇴🇲":"1f1f4-1f1f2","🇳🇿":"1f1f3-1f1ff","🇳🇺":"1f1f3-1f1fa","🇳🇷":"1f1f3-1f1f7","🇳🇵":"1f1f3-1f1f5","🇳🇴":"1f1f3-1f1f4","🇳🇱":"1f1f3-1f1f1","🇳🇮":"1f1f3-1f1ee","🇳🇬":"1f1f3-1f1ec","🇳🇫":"1f1f3-1f1eb","🇳🇪":"1f1f3-1f1ea","🇳🇨":"1f1f3-1f1e8","🇳🇦":"1f1f3-1f1e6","🇲🇿":"1f1f2-1f1ff","🇲🇾":"1f1f2-1f1fe","🇲🇽":"1f1f2-1f1fd","🇲🇼":"1f1f2-1f1fc","🇲🇻":"1f1f2-1f1fb","🇲🇺":"1f1f2-1f1fa","🇲🇹":"1f1f2-1f1f9","🇲🇸":"1f1f2-1f1f8","🇲🇷":"1f1f2-1f1f7","🇲🇶":"1f1f2-1f1f6","🇲🇵":"1f1f2-1f1f5","🇲🇴":"1f1f2-1f1f4","🇲🇳":"1f1f2-1f1f3","🇲🇲":"1f1f2-1f1f2","🇲🇱":"1f1f2-1f1f1","🇲🇰":"1f1f2-1f1f0","🇲🇭":"1f1f2-1f1ed","🇲🇬":"1f1f2-1f1ec","🇲🇫":"1f1f2-1f1eb","🇲🇪":"1f1f2-1f1ea","🇲🇩":"1f1f2-1f1e9","🇲🇨":"1f1f2-1f1e8","🇲🇦":"1f1f2-1f1e6","🇱🇾":"1f1f1-1f1fe","🇱🇻":"1f1f1-1f1fb","🇱🇺":"1f1f1-1f1fa","🇱🇹":"1f1f1-1f1f9","🇱🇸":"1f1f1-1f1f8","🇱🇷":"1f1f1-1f1f7","🇱🇰":"1f1f1-1f1f0","🇱🇮":"1f1f1-1f1ee","🇱🇨":"1f1f1-1f1e8","🇱🇧":"1f1f1-1f1e7","🇱🇦":"1f1f1-1f1e6","🇰🇿":"1f1f0-1f1ff","🇰🇾":"1f1f0-1f1fe","🇰🇼":"1f1f0-1f1fc","🇰🇷":"1f1f0-1f1f7","🇰🇵":"1f1f0-1f1f5","🇰🇳":"1f1f0-1f1f3","🇰🇲":"1f1f0-1f1f2","🇰🇮":"1f1f0-1f1ee","🇰🇭":"1f1f0-1f1ed","🇰🇬":"1f1f0-1f1ec","🇰🇪":"1f1f0-1f1ea","🇯🇵":"1f1ef-1f1f5","🇯🇴":"1f1ef-1f1f4","🇯🇲":"1f1ef-1f1f2","🇯🇪":"1f1ef-1f1ea","🇮🇹":"1f1ee-1f1f9","🇮🇸":"1f1ee-1f1f8","🇮🇷":"1f1ee-1f1f7","🇮🇶":"1f1ee-1f1f6","🇮🇴":"1f1ee-1f1f4","🇮🇳":"1f1ee-1f1f3","🇮🇲":"1f1ee-1f1f2","🇮🇱":"1f1ee-1f1f1","🇮🇪":"1f1ee-1f1ea","🇮🇩":"1f1ee-1f1e9","🇮🇨":"1f1ee-1f1e8","🇭🇺":"1f1ed-1f1fa","🇭🇹":"1f1ed-1f1f9","🇭🇷":"1f1ed-1f1f7","🇭🇳":"1f1ed-1f1f3","🇭🇲":"1f1ed-1f1f2","🇭🇰":"1f1ed-1f1f0","🇬🇾":"1f1ec-1f1fe","🇬🇼":"1f1ec-1f1fc","🇬🇺":"1f1ec-1f1fa","🇬🇹":"1f1ec-1f1f9","🇬🇸":"1f1ec-1f1f8","🇬🇷":"1f1ec-1f1f7","🇬🇶":"1f1ec-1f1f6","🇬🇵":"1f1ec-1f1f5","🇬🇳":"1f1ec-1f1f3","🇬🇲":"1f1ec-1f1f2","🇬🇱":"1f1ec-1f1f1","🇬🇮":"1f1ec-1f1ee","🇬🇭":"1f1ec-1f1ed","🇬🇬":"1f1ec-1f1ec","🇬🇫":"1f1ec-1f1eb","🇬🇪":"1f1ec-1f1ea","🇬🇩":"1f1ec-1f1e9","🇬🇧":"1f1ec-1f1e7","🇬🇦":"1f1ec-1f1e6","🇫🇷":"1f1eb-1f1f7","🇫🇴":"1f1eb-1f1f4","🇫🇲":"1f1eb-1f1f2","🇫🇰":"1f1eb-1f1f0","🇫🇯":"1f1eb-1f1ef","🇫🇮":"1f1eb-1f1ee","🇪🇺":"1f1ea-1f1fa","🇪🇹":"1f1ea-1f1f9","🇪🇸":"1f1ea-1f1f8","🇪🇷":"1f1ea-1f1f7","🇪🇭":"1f1ea-1f1ed","🇪🇬":"1f1ea-1f1ec","🇪🇪":"1f1ea-1f1ea","🇪🇨":"1f1ea-1f1e8","🇪🇦":"1f1ea-1f1e6","🇩🇿":"1f1e9-1f1ff","🇩🇴":"1f1e9-1f1f4","🇩🇲":"1f1e9-1f1f2","🇩🇰":"1f1e9-1f1f0","🇩🇯":"1f1e9-1f1ef","🇩🇬":"1f1e9-1f1ec","🇩🇪":"1f1e9-1f1ea","🇨🇿":"1f1e8-1f1ff","🇨🇾":"1f1e8-1f1fe","🇨🇽":"1f1e8-1f1fd","🇨🇼":"1f1e8-1f1fc","🇨🇻":"1f1e8-1f1fb","🇨🇺":"1f1e8-1f1fa","🇨🇷":"1f1e8-1f1f7","🇨🇵":"1f1e8-1f1f5","🇨🇴":"1f1e8-1f1f4","🇨🇳":"1f1e8-1f1f3","🇨🇲":"1f1e8-1f1f2","🇨🇱":"1f1e8-1f1f1","🇨🇰":"1f1e8-1f1f0","🇨🇮":"1f1e8-1f1ee","🇨🇭":"1f1e8-1f1ed","🇨🇬":"1f1e8-1f1ec","🇨🇫":"1f1e8-1f1eb","🇨🇩":"1f1e8-1f1e9","🇨🇨":"1f1e8-1f1e8","🇨🇦":"1f1e8-1f1e6","🇧🇿":"1f1e7-1f1ff","🇧🇾":"1f1e7-1f1fe","🇧🇼":"1f1e7-1f1fc","🇧🇻":"1f1e7-1f1fb","🇧🇹":"1f1e7-1f1f9","🇧🇸":"1f1e7-1f1f8","🇧🇷":"1f1e7-1f1f7","🇧🇶":"1f1e7-1f1f6","🇧🇴":"1f1e7-1f1f4","🇧🇳":"1f1e7-1f1f3","🇧🇲":"1f1e7-1f1f2","🇧🇱":"1f1e7-1f1f1","🇧🇯":"1f1e7-1f1ef","🇧🇮":"1f1e7-1f1ee","🇧🇭":"1f1e7-1f1ed","🇧🇬":"1f1e7-1f1ec","🇧🇫":"1f1e7-1f1eb","🇧🇪":"1f1e7-1f1ea","🇧🇩":"1f1e7-1f1e9","🇧🇧":"1f1e7-1f1e7","🇧🇦":"1f1e7-1f1e6","🇦🇿":"1f1e6-1f1ff","🇦🇽":"1f1e6-1f1fd","🇦🇼":"1f1e6-1f1fc","🇦🇺":"1f1e6-1f1fa","🇦🇹":"1f1e6-1f1f9","🇦🇸":"1f1e6-1f1f8","🇦🇷":"1f1e6-1f1f7","🇦🇶":"1f1e6-1f1f6","🇦🇴":"1f1e6-1f1f4","🇦🇲":"1f1e6-1f1f2","🇦🇱":"1f1e6-1f1f1","🇦🇮":"1f1e6-1f1ee","🇦🇬":"1f1e6-1f1ec","🇦🇫":"1f1e6-1f1eb","🇦🇪":"1f1e6-1f1ea","🇦🇩":"1f1e6-1f1e9","🇦🇨":"1f1e6-1f1e8","🀄":"1f004","🅿":"1f17f","🈂":"1f202","🈚":"1f21a","🈯":"1f22f","🈷":"1f237","🎞":"1f39e","🎟":"1f39f","🏋":"1f3cb","🏌":"1f3cc","🏍":"1f3cd","🏎":"1f3ce","🎖":"1f396","🎗":"1f397","🌶":"1f336","🌧":"1f327","🌨":"1f328","🌩":"1f329","🌪":"1f32a","🌫":"1f32b","🌬":"1f32c","🐿":"1f43f","🕷":"1f577","🕸":"1f578","🌡":"1f321","🎙":"1f399","🎚":"1f39a","🎛":"1f39b","🏳":"1f3f3","🏵":"1f3f5","🏷":"1f3f7","📽":"1f4fd","🕉":"1f549","🕊":"1f54a","🕯":"1f56f","🕰":"1f570","🕳":"1f573","🕶":"1f576","🕹":"1f579","🖇":"1f587","🖊":"1f58a","🖋":"1f58b","🖌":"1f58c","🖍":"1f58d","🖥":"1f5a5","🖨":"1f5a8","🖲":"1f5b2","🖼":"1f5bc","🗂":"1f5c2","🗃":"1f5c3","🗄":"1f5c4","🗑":"1f5d1","🗒":"1f5d2","🗓":"1f5d3","🗜":"1f5dc","🗝":"1f5dd","🗞":"1f5de","🗡":"1f5e1","🗣":"1f5e3","🗨":"1f5e8","🗯":"1f5ef","🗳":"1f5f3","🗺":"1f5fa","🛠":"1f6e0","🛡":"1f6e1","🛢":"1f6e2","🛰":"1f6f0","🍽":"1f37d","👁":"1f441","🕴":"1f574","🕵":"1f575","🖐":"1f590","🏔":"1f3d4","🏕":"1f3d5","🏖":"1f3d6","🏗":"1f3d7","🏘":"1f3d8","🏙":"1f3d9","🏚":"1f3da","🏛":"1f3db","🏜":"1f3dc","🏝":"1f3dd","🏞":"1f3de","🏟":"1f3df","🛋":"1f6cb","🛍":"1f6cd","🛎":"1f6ce","🛏":"1f6cf","🛣":"1f6e3","🛤":"1f6e4","🛥":"1f6e5","🛩":"1f6e9","🛳":"1f6f3","🌤":"1f324","🌥":"1f325","🌦":"1f326","🖱":"1f5b1","☝🏻":"261d-1f3fb","☝🏼":"261d-1f3fc","☝🏽":"261d-1f3fd","☝🏾":"261d-1f3fe","☝🏿":"261d-1f3ff","✌🏻":"270c-1f3fb","✌🏼":"270c-1f3fc","✌🏽":"270c-1f3fd","✌🏾":"270c-1f3fe","✌🏿":"270c-1f3ff","✊🏻":"270a-1f3fb","✊🏼":"270a-1f3fc","✊🏽":"270a-1f3fd","✊🏾":"270a-1f3fe","✊🏿":"270a-1f3ff","✋🏻":"270b-1f3fb","✋🏼":"270b-1f3fc","✋🏽":"270b-1f3fd","✋🏾":"270b-1f3fe","✋🏿":"270b-1f3ff","✍🏻":"270d-1f3fb","✍🏼":"270d-1f3fc","✍🏽":"270d-1f3fd","✍🏾":"270d-1f3fe","✍🏿":"270d-1f3ff","⛹🏻":"26f9-1f3fb","⛹🏼":"26f9-1f3fc","⛹🏽":"26f9-1f3fd","⛹🏾":"26f9-1f3fe","⛹🏿":"26f9-1f3ff","©":"00a9","®":"00ae","‼":"203c","⁉":"2049","™":"2122","ℹ":"2139","↔":"2194","↕":"2195","↖":"2196","↗":"2197","↘":"2198","↙":"2199","↩":"21a9","↪":"21aa","⌚":"231a","⌛":"231b","Ⓜ":"24c2","▪":"25aa","▫":"25ab","▶":"25b6","◀":"25c0","◻":"25fb","◼":"25fc","◽":"25fd","◾":"25fe","☀":"2600","☁":"2601","☎":"260e","☑":"2611","☔":"2614","☕":"2615","☝":"261d","☺":"263a","♈":"2648","♉":"2649","♊":"264a","♋":"264b","♌":"264c","♍":"264d","♎":"264e","♏":"264f","♐":"2650","♑":"2651","♒":"2652","♓":"2653","♠":"2660","♣":"2663","♥":"2665","♦":"2666","♨":"2668","♻":"267b","♿":"267f","⚓":"2693","⚠":"26a0","⚡":"26a1","⚪":"26aa","⚫":"26ab","⚽":"26bd","⚾":"26be","⛄":"26c4","⛅":"26c5","⛔":"26d4","⛪":"26ea","⛲":"26f2","⛳":"26f3","⛵":"26f5","⛺":"26fa","⛽":"26fd","✂":"2702","✈":"2708","✉":"2709","✌":"270c","✏":"270f","✒":"2712","✔":"2714","✖":"2716","✳":"2733","✴":"2734","❄":"2744","❇":"2747","❗":"2757","❤":"2764","➡":"27a1","⤴":"2934","⤵":"2935","⬅":"2b05","⬆":"2b06","⬇":"2b07","⬛":"2b1b","⬜":"2b1c","⭐":"2b50","⭕":"2b55","〰":"3030","〽":"303d","㊗":"3297","㊙":"3299","✝":"271d","⌨":"2328","✍":"270d","⏏":"23cf","⏭":"23ed","⏮":"23ee","⏯":"23ef","⏱":"23f1","⏲":"23f2","⏸":"23f8","⏹":"23f9","⏺":"23fa","☂":"2602","☃":"2603","☄":"2604","☘":"2618","☠":"2620","☢":"2622","☣":"2623","☦":"2626","☪":"262a","☮":"262e","☯":"262f","☸":"2638","☹":"2639","⚒":"2692","⚔":"2694","⚖":"2696","⚗":"2697","⚙":"2699","⚛":"269b","⚜":"269c","⚰":"26b0","⚱":"26b1","⛈":"26c8","⛏":"26cf","⛑":"26d1","⛓":"26d3","⛩":"26e9","⛰":"26f0","⛱":"26f1","⛴":"26f4","⛷":"26f7","⛸":"26f8","⛹":"26f9","✡":"2721","❣":"2763","🥉":"1f949","🥈":"1f948","🥇":"1f947","🤺":"1f93a","🥅":"1f945","🤾":"1f93e","🇿":"1f1ff","🤽":"1f93d","🥋":"1f94b","🥊":"1f94a","🤼":"1f93c","🤹":"1f939","🤸":"1f938","🛶":"1f6f6","🛵":"1f6f5","🛴":"1f6f4","🛒":"1f6d2","🃏":"1f0cf","🅰":"1f170","🅱":"1f171","🅾":"1f17e","🛑":"1f6d1","🆎":"1f18e","🆑":"1f191","🇾":"1f1fe","🆒":"1f192","🆓":"1f193","🆔":"1f194","🆕":"1f195","🆖":"1f196","🆗":"1f197","🆘":"1f198","🥄":"1f944","🆙":"1f199","🆚":"1f19a","🥂":"1f942","🥃":"1f943","🈁":"1f201","🥙":"1f959","🈲":"1f232","🈳":"1f233","🈴":"1f234","🈵":"1f235","🈶":"1f236","🥘":"1f958","🈸":"1f238","🈹":"1f239","🥗":"1f957","🈺":"1f23a","🉐":"1f250","🉑":"1f251","🌀":"1f300","🥖":"1f956","🌁":"1f301","🌂":"1f302","🌃":"1f303","🌄":"1f304","🌅":"1f305","🌆":"1f306","🥕":"1f955","🌇":"1f307","🌈":"1f308","🥔":"1f954","🌉":"1f309","🌊":"1f30a","🌋":"1f30b","🌌":"1f30c","🌏":"1f30f","🌑":"1f311","🥓":"1f953","🌓":"1f313","🌔":"1f314","🌕":"1f315","🌙":"1f319","🌛":"1f31b","🌟":"1f31f","🥒":"1f952","🌠":"1f320","🌰":"1f330","🥑":"1f951","🌱":"1f331","🌴":"1f334","🌵":"1f335","🌷":"1f337","🌸":"1f338","🌹":"1f339","🌺":"1f33a","🌻":"1f33b","🌼":"1f33c","🌽":"1f33d","🥐":"1f950","🌾":"1f33e","🌿":"1f33f","🍀":"1f340","🍁":"1f341","🍂":"1f342","🍃":"1f343","🍄":"1f344","🍅":"1f345","🍆":"1f346","🍇":"1f347","🍈":"1f348","🍉":"1f349","🍊":"1f34a","🥀":"1f940","🍌":"1f34c","🍍":"1f34d","🍎":"1f34e","🍏":"1f34f","🍑":"1f351","🍒":"1f352","🍓":"1f353","🦏":"1f98f","🍔":"1f354","🍕":"1f355","🍖":"1f356","🦎":"1f98e","🍗":"1f357","🍘":"1f358","🍙":"1f359","🦍":"1f98d","🍚":"1f35a","🍛":"1f35b","🦌":"1f98c","🍜":"1f35c","🍝":"1f35d","🍞":"1f35e","🍟":"1f35f","🦋":"1f98b","🍠":"1f360","🍡":"1f361","🦊":"1f98a","🍢":"1f362","🍣":"1f363","🦉":"1f989","🍤":"1f364","🍥":"1f365","🦈":"1f988","🍦":"1f366","🦇":"1f987","🍧":"1f367","🇽":"1f1fd","🍨":"1f368","🦆":"1f986","🍩":"1f369","🦅":"1f985","🍪":"1f36a","🖤":"1f5a4","🍫":"1f36b","🍬":"1f36c","🍭":"1f36d","🍮":"1f36e","🍯":"1f36f","🤞":"1f91e","🍰":"1f370","🍱":"1f371","🍲":"1f372","🤝":"1f91d","🍳":"1f373","🍴":"1f374","🍵":"1f375","🍶":"1f376","🍷":"1f377","🍸":"1f378","🍹":"1f379","🍺":"1f37a","🍻":"1f37b","🎀":"1f380","🎁":"1f381","🎂":"1f382","🎃":"1f383","🤛":"1f91b","🤜":"1f91c","🎄":"1f384","🎅":"1f385","🎆":"1f386","🤚":"1f91a","🎇":"1f387","🎈":"1f388","🎉":"1f389","🎊":"1f38a","🎋":"1f38b","🎌":"1f38c","🤙":"1f919","🎍":"1f38d","🕺":"1f57a","🎎":"1f38e","🤳":"1f933","🎏":"1f38f","🤰":"1f930","🎐":"1f390","🤦":"1f926","🤷":"1f937","🎑":"1f391","🎒":"1f392","🎓":"1f393","🎠":"1f3a0","🎡":"1f3a1","🎢":"1f3a2","🎣":"1f3a3","🎤":"1f3a4","🎥":"1f3a5","🎦":"1f3a6","🎧":"1f3a7","🤶":"1f936","🎨":"1f3a8","🤵":"1f935","🎩":"1f3a9","🎪":"1f3aa","🤴":"1f934","🎫":"1f3ab","🎬":"1f3ac","🎭":"1f3ad","🤧":"1f927","🎮":"1f3ae","🎯":"1f3af","🎰":"1f3b0","🎱":"1f3b1","🎲":"1f3b2","🎳":"1f3b3","🎴":"1f3b4","🤥":"1f925","🎵":"1f3b5","🎶":"1f3b6","🎷":"1f3b7","🤤":"1f924","🎸":"1f3b8","🎹":"1f3b9","🎺":"1f3ba","🤣":"1f923","🎻":"1f3bb","🎼":"1f3bc","🎽":"1f3bd","🤢":"1f922","🎾":"1f3be","🎿":"1f3bf","🏀":"1f3c0","🏁":"1f3c1","🤡":"1f921","🏂":"1f3c2","🏃":"1f3c3","🏄":"1f3c4","🏆":"1f3c6","🏈":"1f3c8","🏊":"1f3ca","🏠":"1f3e0","🏡":"1f3e1","🏢":"1f3e2","🏣":"1f3e3","🏥":"1f3e5","🏦":"1f3e6","🏧":"1f3e7","🏨":"1f3e8","🏩":"1f3e9","🏪":"1f3ea","🏫":"1f3eb","🏬":"1f3ec","🤠":"1f920","🏭":"1f3ed","🏮":"1f3ee","🏯":"1f3ef","🏰":"1f3f0","🐌":"1f40c","🐍":"1f40d","🐎":"1f40e","🐑":"1f411","🐒":"1f412","🐔":"1f414","🐗":"1f417","🐘":"1f418","🐙":"1f419","🐚":"1f41a","🐛":"1f41b","🐜":"1f41c","🐝":"1f41d","🐞":"1f41e","🐟":"1f41f","🐠":"1f420","🐡":"1f421","🐢":"1f422","🐣":"1f423","🐤":"1f424","🐥":"1f425","🐦":"1f426","🐧":"1f427","🐨":"1f428","🐩":"1f429","🐫":"1f42b","🐬":"1f42c","🐭":"1f42d","🐮":"1f42e","🐯":"1f42f","🐰":"1f430","🐱":"1f431","🐲":"1f432","🐳":"1f433","🐴":"1f434","🐵":"1f435","🐶":"1f436","🐷":"1f437","🐸":"1f438","🐹":"1f439","🐺":"1f43a","🐻":"1f43b","🐼":"1f43c","🐽":"1f43d","🐾":"1f43e","👀":"1f440","👂":"1f442","👃":"1f443","👄":"1f444","👅":"1f445","👆":"1f446","👇":"1f447","👈":"1f448","👉":"1f449","👊":"1f44a","👋":"1f44b","👌":"1f44c","👍":"1f44d","👎":"1f44e","👏":"1f44f","👐":"1f450","👑":"1f451","👒":"1f452","👓":"1f453","👔":"1f454","👕":"1f455","👖":"1f456","👗":"1f457","👘":"1f458","👙":"1f459","👚":"1f45a","👛":"1f45b","👜":"1f45c","👝":"1f45d","👞":"1f45e","👟":"1f45f","👠":"1f460","👡":"1f461","👢":"1f462","👣":"1f463","👤":"1f464","👦":"1f466","👧":"1f467","👨":"1f468","👩":"1f469","👪":"1f46a","👫":"1f46b","👮":"1f46e","👯":"1f46f","👰":"1f470","👱":"1f471","👲":"1f472","👳":"1f473","👴":"1f474","👵":"1f475","👶":"1f476","👷":"1f477","👸":"1f478","👹":"1f479","👺":"1f47a","👻":"1f47b","👼":"1f47c","👽":"1f47d","👾":"1f47e","👿":"1f47f","💀":"1f480","📇":"1f4c7","💁":"1f481","💂":"1f482","💃":"1f483","💄":"1f484","💅":"1f485","📒":"1f4d2","💆":"1f486","📓":"1f4d3","💇":"1f487","📔":"1f4d4","💈":"1f488","📕":"1f4d5","💉":"1f489","📖":"1f4d6","💊":"1f48a","📗":"1f4d7","💋":"1f48b","📘":"1f4d8","💌":"1f48c","📙":"1f4d9","💍":"1f48d","📚":"1f4da","💎":"1f48e","📛":"1f4db","💏":"1f48f","📜":"1f4dc","💐":"1f490","📝":"1f4dd","💑":"1f491","📞":"1f4de","💒":"1f492","📟":"1f4df","📠":"1f4e0","💓":"1f493","📡":"1f4e1","📢":"1f4e2","💔":"1f494","📣":"1f4e3","📤":"1f4e4","💕":"1f495","📥":"1f4e5","📦":"1f4e6","💖":"1f496","📧":"1f4e7","📨":"1f4e8","💗":"1f497","📩":"1f4e9","📪":"1f4ea","💘":"1f498","📫":"1f4eb","📮":"1f4ee","💙":"1f499","📰":"1f4f0","📱":"1f4f1","💚":"1f49a","📲":"1f4f2","📳":"1f4f3","💛":"1f49b","📴":"1f4f4","📶":"1f4f6","💜":"1f49c","📷":"1f4f7","📹":"1f4f9","💝":"1f49d","📺":"1f4fa","📻":"1f4fb","💞":"1f49e","📼":"1f4fc","🔃":"1f503","💟":"1f49f","🔊":"1f50a","🔋":"1f50b","💠":"1f4a0","🔌":"1f50c","🔍":"1f50d","💡":"1f4a1","🔎":"1f50e","🔏":"1f50f","💢":"1f4a2","🔐":"1f510","🔑":"1f511","💣":"1f4a3","🔒":"1f512","🔓":"1f513","💤":"1f4a4","🔔":"1f514","🔖":"1f516","💥":"1f4a5","🔗":"1f517","🔘":"1f518","💦":"1f4a6","🔙":"1f519","🔚":"1f51a","💧":"1f4a7","🔛":"1f51b","🔜":"1f51c","💨":"1f4a8","🔝":"1f51d","🔞":"1f51e","💩":"1f4a9","🔟":"1f51f","💪":"1f4aa","🔠":"1f520","🔡":"1f521","💫":"1f4ab","🔢":"1f522","🔣":"1f523","💬":"1f4ac","🔤":"1f524","🔥":"1f525","💮":"1f4ae","🔦":"1f526","🔧":"1f527","💯":"1f4af","🔨":"1f528","🔩":"1f529","💰":"1f4b0","🔪":"1f52a","🔫":"1f52b","💱":"1f4b1","🔮":"1f52e","💲":"1f4b2","🔯":"1f52f","💳":"1f4b3","🔰":"1f530","🔱":"1f531","💴":"1f4b4","🔲":"1f532","🔳":"1f533","💵":"1f4b5","🔴":"1f534","🔵":"1f535","💸":"1f4b8","🔶":"1f536","🔷":"1f537","💹":"1f4b9","🔸":"1f538","🔹":"1f539","💺":"1f4ba","🔺":"1f53a","🔻":"1f53b","💻":"1f4bb","🔼":"1f53c","💼":"1f4bc","🔽":"1f53d","🕐":"1f550","💽":"1f4bd","🕑":"1f551","💾":"1f4be","🕒":"1f552","💿":"1f4bf","🕓":"1f553","📀":"1f4c0","🕔":"1f554","🕕":"1f555","📁":"1f4c1","🕖":"1f556","🕗":"1f557","📂":"1f4c2","🕘":"1f558","🕙":"1f559","📃":"1f4c3","🕚":"1f55a","🕛":"1f55b","📄":"1f4c4","🗻":"1f5fb","🗼":"1f5fc","📅":"1f4c5","🗽":"1f5fd","🗾":"1f5fe","📆":"1f4c6","🗿":"1f5ff","😁":"1f601","😂":"1f602","😃":"1f603","📈":"1f4c8","😄":"1f604","😅":"1f605","📉":"1f4c9","😆":"1f606","😉":"1f609","📊":"1f4ca","😊":"1f60a","😋":"1f60b","📋":"1f4cb","😌":"1f60c","😍":"1f60d","📌":"1f4cc","😏":"1f60f","😒":"1f612","📍":"1f4cd","😓":"1f613","😔":"1f614","📎":"1f4ce","😖":"1f616","😘":"1f618","📏":"1f4cf","😚":"1f61a","😜":"1f61c","📐":"1f4d0","😝":"1f61d","😞":"1f61e","📑":"1f4d1","😠":"1f620","😡":"1f621","😢":"1f622","😣":"1f623","😤":"1f624","😥":"1f625","😨":"1f628","😩":"1f629","😪":"1f62a","😫":"1f62b","😭":"1f62d","😰":"1f630","😱":"1f631","😲":"1f632","😳":"1f633","😵":"1f635","😷":"1f637","😸":"1f638","😹":"1f639","😺":"1f63a","😻":"1f63b","😼":"1f63c","😽":"1f63d","😾":"1f63e","😿":"1f63f","🙀":"1f640","🙅":"1f645","🙆":"1f646","🙇":"1f647","🙈":"1f648","🙉":"1f649","🙊":"1f64a","🙋":"1f64b","🙌":"1f64c","🙍":"1f64d","🙎":"1f64e","🙏":"1f64f","🚀":"1f680","🚃":"1f683","🚄":"1f684","🚅":"1f685","🚇":"1f687","🚉":"1f689","🚌":"1f68c","🚏":"1f68f","🚑":"1f691","🚒":"1f692","🚓":"1f693","🚕":"1f695","🚗":"1f697","🚙":"1f699","🚚":"1f69a","🚢":"1f6a2","🚤":"1f6a4","🚥":"1f6a5","🚧":"1f6a7","🚨":"1f6a8","🚩":"1f6a9","🚪":"1f6aa","🚫":"1f6ab","🚬":"1f6ac","🚭":"1f6ad","🚲":"1f6b2","🚶":"1f6b6","🚹":"1f6b9","🚺":"1f6ba","🚻":"1f6bb","🚼":"1f6bc","🚽":"1f6bd","🚾":"1f6be","🛀":"1f6c0","🤘":"1f918","😀":"1f600","😇":"1f607","😈":"1f608","😎":"1f60e","😐":"1f610","😑":"1f611","😕":"1f615","😗":"1f617","😙":"1f619","😛":"1f61b","😟":"1f61f","😦":"1f626","😧":"1f627","😬":"1f62c","😮":"1f62e","😯":"1f62f","😴":"1f634","😶":"1f636","🚁":"1f681","🚂":"1f682","🚆":"1f686","🚈":"1f688","🚊":"1f68a","🚍":"1f68d","🚎":"1f68e","🚐":"1f690","🚔":"1f694","🚖":"1f696","🚘":"1f698","🚛":"1f69b","🚜":"1f69c","🚝":"1f69d","🚞":"1f69e","🚟":"1f69f","🚠":"1f6a0","🚡":"1f6a1","🚣":"1f6a3","🚦":"1f6a6","🚮":"1f6ae","🚯":"1f6af","🚰":"1f6b0","🚱":"1f6b1","🚳":"1f6b3","🚴":"1f6b4","🚵":"1f6b5","🚷":"1f6b7","🚸":"1f6b8","🚿":"1f6bf","🛁":"1f6c1","🛂":"1f6c2","🛃":"1f6c3","🛄":"1f6c4","🛅":"1f6c5","🌍":"1f30d","🌎":"1f30e","🌐":"1f310","🌒":"1f312","🌖":"1f316","🌗":"1f317","🌘":"1f318","🌚":"1f31a","🌜":"1f31c","🌝":"1f31d","🌞":"1f31e","🌲":"1f332","🌳":"1f333","🍋":"1f34b","🍐":"1f350","🍼":"1f37c","🏇":"1f3c7","🏉":"1f3c9","🏤":"1f3e4","🐀":"1f400","🐁":"1f401","🐂":"1f402","🐃":"1f403","🐄":"1f404","🐅":"1f405","🐆":"1f406","🐇":"1f407","🐈":"1f408","🐉":"1f409","🐊":"1f40a","🐋":"1f40b","🐏":"1f40f","🐐":"1f410","🐓":"1f413","🐕":"1f415","🐖":"1f416","🐪":"1f42a","👥":"1f465","👬":"1f46c","👭":"1f46d","💭":"1f4ad","💶":"1f4b6","💷":"1f4b7","📬":"1f4ec","📭":"1f4ed","📯":"1f4ef","📵":"1f4f5","🔀":"1f500","🔁":"1f501","🔂":"1f502","🔄":"1f504","🔅":"1f505","🔆":"1f506","🔇":"1f507","🔉":"1f509","🔕":"1f515","🔬":"1f52c","🔭":"1f52d","🕜":"1f55c","🕝":"1f55d","🕞":"1f55e","🕟":"1f55f","🕠":"1f560","🕡":"1f561","🕢":"1f562","🕣":"1f563","🕤":"1f564","🕥":"1f565","🕦":"1f566","🕧":"1f567","🔈":"1f508","🚋":"1f68b","🏅":"1f3c5","🏴":"1f3f4","📸":"1f4f8","🛌":"1f6cc","🖕":"1f595","🖖":"1f596","🙁":"1f641","🙂":"1f642","🛫":"1f6eb","🛬":"1f6ec","🏻":"1f3fb","🏼":"1f3fc","🏽":"1f3fd","🏾":"1f3fe","🏿":"1f3ff","🙃":"1f643","🤑":"1f911","🤓":"1f913","🤗":"1f917","🙄":"1f644","🤔":"1f914","🤐":"1f910","🤒":"1f912","🤕":"1f915","🤖":"1f916","🦁":"1f981","🦄":"1f984","🦂":"1f982","🦀":"1f980","🦃":"1f983","🧀":"1f9c0","🌭":"1f32d","🌮":"1f32e","🌯":"1f32f","🍿":"1f37f","🍾":"1f37e","🏹":"1f3f9","🏺":"1f3fa","🛐":"1f6d0","🕋":"1f54b","🕌":"1f54c","🕍":"1f54d","🕎":"1f54e","📿":"1f4ff","🏏":"1f3cf","🏐":"1f3d0","🏑":"1f3d1","🏒":"1f3d2","🏓":"1f3d3","🏸":"1f3f8","🥁":"1f941","🦐":"1f990","🦑":"1f991","🥚":"1f95a","🥛":"1f95b","🥜":"1f95c","🥝":"1f95d","🥞":"1f95e","🇼":"1f1fc","🇻":"1f1fb","🇺":"1f1fa","🇹":"1f1f9","🇸":"1f1f8","🇷":"1f1f7","🇶":"1f1f6","🇵":"1f1f5","🇴":"1f1f4","🇳":"1f1f3","🇲":"1f1f2","🇱":"1f1f1","🇰":"1f1f0","🇯":"1f1ef","🇮":"1f1ee","🇭":"1f1ed","🇬":"1f1ec","🇫":"1f1eb","🇪":"1f1ea","🇩":"1f1e9","🇨":"1f1e8","🇧":"1f1e7","🇦":"1f1e6","⏩":"23e9","⏪":"23ea","⏫":"23eb","⏬":"23ec","⏰":"23f0","⏳":"23f3","*":"002a","⛎":"26ce","✅":"2705","✊":"270a","✋":"270b","✨":"2728","❌":"274c","❎":"274e","❓":"2753","❔":"2754","❕":"2755","➕":"2795","➖":"2796","➗":"2797","➰":"27b0","#":"0023","➿":"27bf",9:"0039",8:"0038",7:"0037",6:"0036",5:"0035",4:"0034",3:"0033",2:"0032",1:"0031",0:"0030","©":"00a9","®":"00ae","‼":"203c","⁉":"2049","™":"2122","ℹ":"2139","↔":"2194","↕":"2195","↖":"2196","↗":"2197","↘":"2198","↙":"2199","↩":"21a9","↪":"21aa","⌚":"231a","⌛":"231b","Ⓜ":"24c2","▪":"25aa","▫":"25ab","▶":"25b6","◀":"25c0","◻":"25fb","◼":"25fc","◽":"25fd","◾":"25fe","☀":"2600","☁":"2601","☎":"260e","☑":"2611","☔":"2614","☕":"2615","☝":"261d","☺":"263a","♈":"2648","♉":"2649","♊":"264a","♋":"264b","♌":"264c","♍":"264d","♎":"264e","♏":"264f","♐":"2650","♑":"2651","♒":"2652","♓":"2653","♠":"2660","♣":"2663","♥":"2665","♦":"2666","♨":"2668","♻":"267b","♿":"267f","⚓":"2693","⚠":"26a0","⚡":"26a1","⚪":"26aa","⚫":"26ab","⚽":"26bd","⚾":"26be","⛄":"26c4","⛅":"26c5","⛔":"26d4","⛪":"26ea","⛲":"26f2","⛳":"26f3","⛵":"26f5","⛺":"26fa","⛽":"26fd","✂":"2702","✈":"2708","✉":"2709","✌":"270c","✏":"270f","✒":"2712","✔":"2714","✖":"2716","✳":"2733","✴":"2734","❄":"2744","❇":"2747","❗":"2757","❤":"2764","➡":"27a1","⤴":"2934","⤵":"2935","⬅":"2b05","⬆":"2b06","⬇":"2b07","⬛":"2b1b","⬜":"2b1c","⭐":"2b50","⭕":"2b55","〰":"3030","〽":"303d","㊗":"3297","㊙":"3299","🀄":"1f004","🅿":"1f17f","🈂":"1f202","🈚":"1f21a","🈯":"1f22f","🈷":"1f237","🎞":"1f39e","🎟":"1f39f","🏋":"1f3cb","🏌":"1f3cc","🏍":"1f3cd","🏎":"1f3ce","🎖":"1f396","🎗":"1f397","🌶":"1f336","🌧":"1f327","🌨":"1f328","🌩":"1f329","🌪":"1f32a","🌫":"1f32b","🌬":"1f32c","🐿":"1f43f","🕷":"1f577","🕸":"1f578","🌡":"1f321","🎙":"1f399","🎚":"1f39a","🎛":"1f39b","🏳":"1f3f3","🏵":"1f3f5","🏷":"1f3f7","📽":"1f4fd","✝":"271d","🕉":"1f549","🕊":"1f54a","🕯":"1f56f","🕰":"1f570","🕳":"1f573","🕶":"1f576","🕹":"1f579","🖇":"1f587","🖊":"1f58a","🖋":"1f58b","🖌":"1f58c","🖍":"1f58d","🖥":"1f5a5","🖨":"1f5a8","⌨":"2328","🖲":"1f5b2","🖼":"1f5bc","🗂":"1f5c2","🗃":"1f5c3","🗄":"1f5c4","🗑":"1f5d1","🗒":"1f5d2","🗓":"1f5d3","🗜":"1f5dc","🗝":"1f5dd","🗞":"1f5de","🗡":"1f5e1","🗣":"1f5e3","🗨":"1f5e8","🗯":"1f5ef","🗳":"1f5f3","🗺":"1f5fa","🛠":"1f6e0","🛡":"1f6e1","🛢":"1f6e2","🛰":"1f6f0","🍽":"1f37d","👁":"1f441","🕴":"1f574","🕵":"1f575","✍":"270d","🖐":"1f590","🏔":"1f3d4","🏕":"1f3d5","🏖":"1f3d6","🏗":"1f3d7","🏘":"1f3d8","🏙":"1f3d9","🏚":"1f3da","🏛":"1f3db","🏜":"1f3dc","🏝":"1f3dd","🏞":"1f3de","🏟":"1f3df","🛋":"1f6cb","🛍":"1f6cd","🛎":"1f6ce","🛏":"1f6cf","🛣":"1f6e3","🛤":"1f6e4","🛥":"1f6e5","🛩":"1f6e9","🛳":"1f6f3","⏏":"23cf","⏭":"23ed","⏮":"23ee","⏯":"23ef","⏱":"23f1","⏲":"23f2","⏸":"23f8","⏹":"23f9","⏺":"23fa","☂":"2602","☃":"2603","☄":"2604","☘":"2618","☠":"2620","☢":"2622","☣":"2623","☦":"2626","☪":"262a","☮":"262e","☯":"262f","☸":"2638","☹":"2639","⚒":"2692","⚔":"2694","⚖":"2696","⚗":"2697","⚙":"2699","⚛":"269b","⚜":"269c","⚰":"26b0","⚱":"26b1","⛈":"26c8","⛏":"26cf","⛑":"26d1","⛓":"26d3","⛩":"26e9","⛰":"26f0","⛱":"26f1","⛴":"26f4","⛷":"26f7","⛸":"26f8","⛹":"26f9",
        "✡":"2721","❣":"2763","🌤":"1f324","🌥":"1f325","🌦":"1f326","🖱":"1f5b1"},a.imagePathPNG="//cdn.jsdelivr.net/emojione/assets/png/",a.imagePathSVG="//cdn.jsdelivr.net/emojione/assets/svg/",a.imagePathSVGSprites="./../assets/sprites/emojione.sprites.svg",a.imageType="png",a.sprites=!1,a.unicodeAlt=!0,a.ascii=!1,a.cacheBustParam="?v=2.2.6",a.regShortNames=new RegExp("<object[^>]*>.*?</object>|<span[^>]*>.*?</span>|<(?:object|embed|svg|img|div|span|p|a)[^>]*>|("+a.shortnames+")","gi"),a.regAscii=new RegExp("<object[^>]*>.*?</object>|<span[^>]*>.*?</span>|<(?:object|embed|svg|img|div|span|p|a)[^>]*>|((\\s|^)"+a.asciiRegexp+"(?=\\s|$|[!,.?]))","g"),a.regUnicode=new RegExp("<object[^>]*>.*?</object>|<span[^>]*>.*?</span>|<(?:object|embed|svg|img|div|span|p|a)[^>]*>|("+a.unicodeRegexp+")","gi"),a.toImage=function(b){return b=a.unicodeToImage(b),b=a.shortnameToImage(b)},a.unifyUnicode=function(b){return b=a.toShort(b),b=a.shortnameToUnicode(b)},a.shortnameToAscii=function(b){var c,d=a.objectFlip(a.asciiList);return b=b.replace(a.regShortNames,function(b){return"undefined"!=typeof b&&""!==b&&b in a.emojioneList?(c=a.emojioneList[b].unicode[a.emojioneList[b].unicode.length-1],"undefined"!=typeof d[c]?d[c]:b):b})},a.shortnameToUnicode=function(b){var c;return b=b.replace(a.regShortNames,function(b){return"undefined"!=typeof b&&""!==b&&b in a.emojioneList?(c=a.emojioneList[b].unicode[0].toUpperCase(),a.convert(c)):b}),a.ascii&&(b=b.replace(a.regAscii,function(b,d,e,f){return"undefined"!=typeof f&&""!==f&&a.unescapeHTML(f)in a.asciiList?(f=a.unescapeHTML(f),c=a.asciiList[f].toUpperCase(),e+a.convert(c)):b})),b},a.shortnameToImage=function(b){var c,d,e;return b=b.replace(a.regShortNames,function(b){return"undefined"!=typeof b&&""!==b&&b in a.emojioneList?(d=a.emojioneList[b].unicode[a.emojioneList[b].unicode.length-1],e=a.unicodeAlt?a.convert(d.toUpperCase()):b,c="png"===a.imageType?a.sprites?'<span class="emojione emojione-'+d+'" title="'+b+'">'+e+"</span>":'<img class="emojione" alt="'+e+'" src="'+a.imagePathPNG+d+".png"+a.cacheBustParam+'"/>':a.sprites?'<svg class="emojione"><description>'+e+'</description><use xlink:href="'+a.imagePathSVGSprites+"#emoji-"+d+'"></use></svg>':'<object class="emojione" data="'+a.imagePathSVG+d+".svg"+a.cacheBustParam+'" type="image/svg+xml" standby="'+e+'">'+e+"</object>"):b}),a.ascii&&(b=b.replace(a.regAscii,function(b,f,g,h){return"undefined"!=typeof h&&""!==h&&a.unescapeHTML(h)in a.asciiList?(h=a.unescapeHTML(h),d=a.asciiList[h],e=a.unicodeAlt?a.convert(d.toUpperCase()):a.escapeHTML(h),c="png"===a.imageType?a.sprites?g+'<span class="emojione emojione-'+d+'" title="'+a.escapeHTML(h)+'">'+e+"</span>":g+'<img class="emojione" alt="'+e+'" src="'+a.imagePathPNG+d+".png"+a.cacheBustParam+'"/>':a.sprites?'<svg class="emojione"><description>'+e+'</description><use xlink:href="'+a.imagePathSVGSprites+"#emoji-"+d+'"></use></svg>':g+'<object class="emojione" data="'+a.imagePathSVG+d+".svg"+a.cacheBustParam+'" type="image/svg+xml" standby="'+e+'">'+e+"</object>"):b})),b},a.unicodeToImage=function(b){var c,d,e;if(!a.unicodeAlt||a.sprites)var f=a.mapUnicodeToShort();return b=b.replace(a.regUnicode,function(b){return"undefined"!=typeof b&&""!==b&&b in a.jsEscapeMap?(d=a.jsEscapeMap[b],e=a.unicodeAlt?a.convert(d.toUpperCase()):f[d],c="png"===a.imageType?a.sprites?'<span class="emojione emojione-'+d+'" title="'+f[d]+'">'+e+"</span>":'<img class="emojione" alt="'+e+'" src="'+a.imagePathPNG+d+".png"+a.cacheBustParam+'"/>':a.sprites?'<svg class="emojione"><description>'+e+'</description><use xlink:href="'+a.imagePathSVGSprites+"#emoji-"+d+'"></use></svg>':'<img class="emojione" alt="'+e+'" src="'+a.imagePathSVG+d+".svg"+a.cacheBustParam+'"/>'):b})},a.toShort=function(b){var c=a.getUnicodeReplacementRegEx(),d=a.mapUnicodeCharactersToShort();return a.replaceAll(b,c,d)},a.convert=function(a){if(a.indexOf("-")>-1){for(var b=[],c=a.split("-"),d=0;d<c.length;d++){var e=parseInt(c[d],16);if(e>=65536&&1114111>=e){var f=Math.floor((e-65536)/1024)+55296,g=(e-65536)%1024+56320;e=String.fromCharCode(f)+String.fromCharCode(g)}else e=String.fromCharCode(e);b.push(e)}return b.join("")}var c=parseInt(a,16);if(c>=65536&&1114111>=c){var f=Math.floor((c-65536)/1024)+55296,g=(c-65536)%1024+56320;return String.fromCharCode(f)+String.fromCharCode(g)}return String.fromCharCode(c)},a.escapeHTML=function(a){var b={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"};return a.replace(/[&<>"']/g,function(a){return b[a]})},a.unescapeHTML=function(a){var b={"&amp;":"&","&#38;":"&","&#x26;":"&","&lt;":"<","&#60;":"<","&#x3C;":"<","&gt;":">","&#62;":">","&#x3E;":">","&quot;":'"',"&#34;":'"',"&#x22;":'"',"&apos;":"'","&#39;":"'","&#x27;":"'"};return a.replace(/&(?:amp|#38|#x26|lt|#60|#x3C|gt|#62|#x3E|apos|#39|#x27|quot|#34|#x22);/gi,function(a){return b[a]})},a.mapEmojioneList=function(b){for(var c in a.emojioneList)if(a.emojioneList.hasOwnProperty(c))for(var d=0,e=a.emojioneList[c].unicode.length;e>d;d++){var f=a.emojioneList[c].unicode[d];b(f,c)}},a.mapUnicodeToShort=function(){return a.memMapShortToUnicode||(a.memMapShortToUnicode={},a.mapEmojioneList(function(b,c){a.memMapShortToUnicode[b]=c})),a.memMapShortToUnicode},a.memoizeReplacement=function(){if(!a.unicodeReplacementRegEx||!a.memMapShortToUnicodeCharacters){var b=[];a.memMapShortToUnicodeCharacters={},a.mapEmojioneList(function(c,d){var e=a.convert(c);a.emojioneList[d].isCanonical&&(a.memMapShortToUnicodeCharacters[e]=d),b.push(e)}),a.unicodeReplacementRegEx=b.join("|")}},a.mapUnicodeCharactersToShort=function(){return a.memoizeReplacement(),a.memMapShortToUnicodeCharacters},a.getUnicodeReplacementRegEx=function(){return a.memoizeReplacement(),a.unicodeReplacementRegEx},a.objectFlip=function(a){var b,c={};for(b in a)a.hasOwnProperty(b)&&(c[a[b]]=b);return c},a.escapeRegExp=function(a){return a.replace(/[-[\]{}()*+?.,;:&\\^$#\s]/g,"\\$&")},a.replaceAll=function(b,c,d){var e=a.escapeRegExp(c),f=new RegExp("<object[^>]*>.*?</object>|<span[^>]*>.*?</span>|<(?:object|embed|svg|img|div|span|p|a)[^>]*>|("+e+")","gi"),g=function(a,b){return"undefined"==typeof b||""===b?a:d[b]};return b.replace(f,g)}}(this.emojione=this.emojione||{}),"object"==typeof module&&(module.exports=this.emojione);

/*!
 * EmojioneArea v3.1.5
 * https://github.com/mervick/emojionearea
 * Copyright Andrey Izman and other contributors
 * Released under the MIT license
 * Date: 2016-09-27T09:32Z
 */
(function(document, window, $) {
    'use strict';

    var unique = 0;
    var eventStorage = {};
    var possibleEvents = {};
    var emojione = window.emojione;
    var readyCallbacks = [];
    function emojioneReady (fn) {
        if (emojione) {
            fn();
        } else {
            readyCallbacks.push(fn);
        }
    };
    var blankImg = 'data:image/gif;base64,R0lGODlhAQABAJH/AP///wAAAMDAwAAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw==';
    var slice = [].slice;
    var css_class = "emojionearea";
    var emojioneSupportMode = 0;
    var invisibleChar = '&#8203;';
    function trigger(self, event, args) {
        var result = true, j = 1;
        if (event) {
            event = event.toLowerCase();
            do {
                var _event = j==1 ? '@' + event : event;
                if (eventStorage[self.id][_event] && eventStorage[self.id][_event].length) {
                    $.each(eventStorage[self.id][_event], function (i, fn) {
                        return result = fn.apply(self, args|| []) !== false;
                    });
                }
            } while (result && !!j--);
        }
        return result;
    }
    function attach(self, element, events, target) {
        target = target || function (event, callerEvent) { return $(callerEvent.currentTarget) };
        $.each(events, function(event, link) {
            event = $.isArray(events) ? link : event;
            (possibleEvents[self.id][link] || (possibleEvents[self.id][link] = []))
                .push([element, event, target]);
        });
    }
    function getTemplate(template, unicode, shortname) {
        var imageType = emojione.imageType, imagePath;
        if (imageType=='svg'){
            imagePath = emojione.imagePathSVG;
        } else {
            imagePath = emojione.imagePathPNG;
        }
        return template
            .replace('{name}', shortname || '')
            .replace('{img}', imagePath + (emojioneSupportMode < 2 ? unicode.toUpperCase() : unicode) + '.' + imageType)
            .replace('{uni}', unicode)
            .replace('{alt}', emojione.convert(unicode));
    };
    function shortnameTo(str, template, clear) {
        return str.replace(/:?\+?[\w_\-]+:?/g, function(shortname) {
            shortname = ":" + shortname.replace(/:$/,'').replace(/^:/,'') + ":";
            var unicode = emojione.emojioneList[shortname];
            if (unicode) {
                if (emojioneSupportMode > 3) unicode = unicode.unicode;
                return getTemplate(template, unicode[unicode.length-1], shortname);
            }
            return clear ? '' : shortname;
        });
    };
    function pasteHtmlAtCaret(html) {
        var sel, range;
        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                range = sel.getRangeAt(0);
                range.deleteContents();
                var el = document.createElement("div");
                el.innerHTML = html;
                var frag = document.createDocumentFragment(), node, lastNode;
                while ( (node = el.firstChild) ) {
                    lastNode = frag.appendChild(node);
                }
                range.insertNode(frag);
                if (lastNode) {
                    range = range.cloneRange();
                    range.setStartAfter(lastNode);
                    range.collapse(true);
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
            }
        } else if (document.selection && document.selection.type != "Control") {
            document.selection.createRange().pasteHTML(html);
        }
    }
    var getDefaultOptions = function () {
        return $.fn.emojioneArea && $.fn.emojioneArea.defaults ? $.fn.emojioneArea.defaults : {
            attributes: {
                dir               : "ltr",
                spellcheck        : false,
                autocomplete      : "off",
                autocorrect       : "off",
                autocapitalize    : "off",
            },
            placeholder       : null,
            emojiPlaceholder  : ":smiley:",
            container         : null,
            hideSource        : true,
            shortnames        : true,
            sprite            : true,
            pickerPosition    : "top", // top | bottom | right
            filtersPosition   : "top", // top | bottom
            hidePickerOnBlur  : true,
            buttonTitle       : "Use the TAB key to insert emoji faster",
            tones             : true,
            tonesStyle        : "bullet", // bullet | radio | square | checkbox
            inline            : null, // null - auto
            saveEmojisAs      : "unicode", // unicode | shortname | image
            shortcuts         : true,
            autocomplete      : true,
            autocompleteTones : false,
            standalone        : false,
            useInternalCDN    : true, // Use the self loading mechanism
            imageType         : "png", // Default image type used by internal CDN
            recentEmojis      : true,
            textcomplete: {
                maxCount      : 15,
                placement     : null // null - default | top | absleft | absright
            },

            filters: {
                tones: {
                    title: "Diversity",
                    emoji: "santa runner surfer swimmer lifter ear nose point_up_2 point_down point_left point_right punch " +
                    "wave ok_hand thumbsup thumbsdown clap open_hands boy girl man woman cop bride_with_veil person_with_blond_hair " +
                    "man_with_gua_pi_mao man_with_turban older_man grandma baby construction_worker princess angel " +
                    "information_desk_person guardsman dancer nail_care massage haircut muscle spy hand_splayed middle_finger " +
                    "vulcan no_good ok_woman bow raising_hand raised_hands person_frowning person_with_pouting_face pray rowboat " +
                    "bicyclist mountain_bicyclist walking bath metal point_up basketball_player fist raised_hand v writing_hand"
                },

                recent: {
                    icon: "clock3",
                    title: "Recent",
                    emoji: ""
                },

                smileys_people: {
                    icon: "yum",
                    title: "Smileys & People",
                    emoji: "grinning grimacing grin joy smiley smile sweat_smile laughing innocent wink blush slight_smile " +
                    "upside_down relaxed yum relieved heart_eyes kissing_heart kissing kissing_smiling_eyes " +
                    "kissing_closed_eyes stuck_out_tongue_winking_eye stuck_out_tongue_closed_eyes stuck_out_tongue " +
                    "money_mouth nerd sunglasses hugging smirk no_mouth neutral_face expressionless unamused rolling_eyes " +
                    "thinking flushed disappointed worried angry rage pensive confused slight_frown frowning2 persevere " +
                    "confounded tired_face weary triumph open_mouth scream fearful cold_sweat hushed frowning anguished " +
                    "cry disappointed_relieved sleepy sweat sob dizzy_face astonished zipper_mouth mask thermometer_face " +
                    "head_bandage sleeping zzz poop smiling_imp imp japanese_ogre japanese_goblin skull ghost alien robot " +
                    "smiley_cat smile_cat joy_cat heart_eyes_cat smirk_cat kissing_cat scream_cat crying_cat_face " +
                    "pouting_cat raised_hands clap wave thumbsup thumbsdown punch fist v ok_hand raised_hand open_hands " +
                    "muscle pray point_up point_up_2 point_down point_left point_right middle_finger hand_splayed metal " +
                    "vulcan writing_hand nail_care lips tongue ear nose eye eyes bust_in_silhouette busts_in_silhouette " +
                    "speaking_head baby boy girl man woman person_with_blond_hair older_man older_woman man_with_gua_pi_mao " +
                    "man_with_turban cop construction_worker guardsman spy santa angel princess bride_with_veil walking " +
                    "runner dancer dancers couple two_men_holding_hands two_women_holding_hands bow information_desk_person " +
                    "no_good ok_woman raising_hand person_with_pouting_face person_frowning haircut massage couple_with_heart " +
                    "couple_ww couple_mm couplekiss kiss_ww kiss_mm family family_mwg family_mwgb family_mwbb family_mwgg " +
                    "family_wwb family_wwg family_wwgb family_wwbb family_wwgg family_mmb family_mmg family_mmgb family_mmbb " +
                    "family_mmgg womans_clothes shirt jeans necktie dress bikini kimono lipstick kiss footprints high_heel " +
                    "sandal boot mans_shoe athletic_shoe womans_hat tophat helmet_with_cross mortar_board crown school_satchel " +
                    "pouch purse handbag briefcase eyeglasses dark_sunglasses ring closed_umbrella"
                },

                animals_nature: {
                    icon: "hamster",
                    title: "Animals & Nature",
                    emoji: "dog cat mouse hamster rabbit bear panda_face koala tiger lion_face cow pig pig_nose frog " +
                    "octopus monkey_face see_no_evil hear_no_evil speak_no_evil monkey chicken penguin bird baby_chick " +
                    "hatching_chick hatched_chick wolf boar horse unicorn bee bug snail beetle ant spider scorpion crab " +
                    "snake turtle tropical_fish fish blowfish dolphin whale whale2 crocodile leopard tiger2 water_buffalo " +
                    "ox cow2 dromedary_camel camel elephant goat ram sheep racehorse pig2 rat mouse2 rooster turkey dove " +
                    "dog2 poodle cat2 rabbit2 chipmunk feet dragon dragon_face cactus christmas_tree evergreen_tree " +
                    "deciduous_tree palm_tree seedling herb shamrock four_leaf_clover bamboo tanabata_tree leaves " +
                    "fallen_leaf maple_leaf ear_of_rice hibiscus sunflower rose tulip blossom cherry_blossom bouquet " +
                    "mushroom chestnut jack_o_lantern shell spider_web earth_americas earth_africa earth_asia full_moon " +
                    "waning_gibbous_moon last_quarter_moon waning_crescent_moon new_moon waxing_crescent_moon " +
                    "first_quarter_moon waxing_gibbous_moon new_moon_with_face full_moon_with_face first_quarter_moon_with_face " +
                    "last_quarter_moon_with_face sun_with_face crescent_moon star star2 dizzy sparkles comet sunny " +
                    "white_sun_small_cloud partly_sunny white_sun_cloud white_sun_rain_cloud cloud cloud_rain " +
                    "thunder_cloud_rain cloud_lightning zap fire boom snowflake cloud_snow snowman2 snowman wind_blowing_face " +
                    "dash cloud_tornado fog umbrella2 umbrella droplet sweat_drops ocean"
                },

                food_drink: {
                    icon: "pizza",
                    title: "Food & Drink",
                    emoji: "green_apple apple pear tangerine lemon banana watermelon grapes strawberry melon cherries peach " +
                    "pineapple tomato eggplant hot_pepper corn sweet_potato honey_pot bread cheese poultry_leg meat_on_bone " +
                    "fried_shrimp egg hamburger fries hotdog pizza spaghetti taco burrito ramen stew fish_cake sushi bento " +
                    "curry rice_ball rice rice_cracker oden dango shaved_ice ice_cream icecream cake birthday custard candy " +
                    "lollipop chocolate_bar popcorn doughnut cookie beer beers wine_glass cocktail tropical_drink champagne " +
                    "sake tea coffee baby_bottle fork_and_knife fork_knife_plate"
                },

                activity: {
                    icon: "basketball",
                    title: "Activity",
                    emoji: "soccer basketball football baseball tennis volleyball rugby_football 8ball golf golfer ping_pong " +
                    "badminton hockey field_hockey cricket ski skier snowboarder ice_skate bow_and_arrow fishing_pole_and_fish " +
                    "rowboat swimmer surfer bath basketball_player lifter bicyclist mountain_bicyclist horse_racing levitate " +
                    "trophy running_shirt_with_sash medal military_medal reminder_ribbon rosette ticket tickets performing_arts " +
                    "art circus_tent microphone headphones musical_score musical_keyboard saxophone trumpet guitar violin " +
                    "clapper video_game space_invader dart game_die slot_machine bowling"
                },

                travel_places: {
                    icon: "rocket",
                    title: "Travel & Places",
                    emoji: "red_car taxi blue_car bus trolleybus race_car police_car ambulance fire_engine minibus truck " +
                    "articulated_lorry tractor motorcycle bike rotating_light oncoming_police_car oncoming_bus " +
                    "oncoming_automobile oncoming_taxi aerial_tramway mountain_cableway suspension_railway railway_car " +
                    "train monorail bullettrain_side bullettrain_front light_rail mountain_railway steam_locomotive train2 " +
                    "metro tram station helicopter airplane_small airplane airplane_departure airplane_arriving sailboat " +
                    "motorboat speedboat ferry cruise_ship rocket satellite_orbital seat anchor construction fuelpump busstop " +
                    "vertical_traffic_light traffic_light checkered_flag ship ferris_wheel roller_coaster carousel_horse " +
                    "construction_site foggy tokyo_tower factory fountain rice_scene mountain mountain_snow mount_fuji volcano " +
                    "japan camping tent park motorway railway_track sunrise sunrise_over_mountains desert beach island " +
                    "city_sunset city_dusk cityscape night_with_stars bridge_at_night milky_way stars sparkler fireworks " +
                    "rainbow homes european_castle japanese_castle stadium statue_of_liberty house house_with_garden " +
                    "house_abandoned office department_store post_office european_post_office hospital bank hotel " +
                    "convenience_store school love_hotel wedding classical_building church mosque synagogue kaaba shinto_shrine"
                },

                objects: {
                    icon: "bulb",
                    title: "Objects",
                    emoji: "watch iphone calling computer keyboard desktop printer mouse_three_button trackball joystick " +
                    "compression minidisc floppy_disk cd dvd vhs camera camera_with_flash video_camera movie_camera projector " +
                    "film_frames telephone_receiver telephone pager fax tv radio microphone2 level_slider control_knobs " +
                    "stopwatch timer alarm_clock clock hourglass_flowing_sand hourglass satellite battery electric_plug bulb " +
                    "flashlight candle wastebasket oil money_with_wings dollar yen euro pound moneybag credit_card gem scales " +
                    "wrench hammer hammer_pick tools pick nut_and_bolt gear chains gun bomb knife dagger crossed_swords shield " +
                    "smoking skull_crossbones coffin urn amphora crystal_ball prayer_beads barber alembic telescope microscope " +
                    "hole pill syringe thermometer label bookmark toilet shower bathtub key key2 couch sleeping_accommodation " +
                    "bed door bellhop frame_photo map beach_umbrella moyai shopping_bags balloon flags ribbon gift confetti_ball " +
                    "tada dolls wind_chime crossed_flags izakaya_lantern envelope envelope_with_arrow incoming_envelope e-mail " +
                    "love_letter postbox mailbox_closed mailbox mailbox_with_mail mailbox_with_no_mail package postal_horn " +
                    "inbox_tray outbox_tray scroll page_with_curl bookmark_tabs bar_chart chart_with_upwards_trend " +
                    "chart_with_downwards_trend page_facing_up date calendar calendar_spiral card_index card_box ballot_box " +
                    "file_cabinet clipboard notepad_spiral file_folder open_file_folder dividers newspaper2 newspaper notebook " +
                    "closed_book green_book blue_book orange_book notebook_with_decorative_cover ledger books book link " +
                    "paperclip paperclips scissors triangular_ruler straight_ruler pushpin round_pushpin triangular_flag_on_post " +
                    "flag_white flag_black closed_lock_with_key lock unlock lock_with_ink_pen pen_ballpoint pen_fountain " +
                    "black_nib pencil pencil2 crayon paintbrush mag mag_right"
                },

                symbols: {
                    icon: "heartpulse",
                    title: "Symbols",
                    emoji: "heart yellow_heart green_heart blue_heart purple_heart broken_heart heart_exclamation two_hearts " +
                    "revolving_hearts heartbeat heartpulse sparkling_heart cupid gift_heart heart_decoration peace cross " +
                    "star_and_crescent om_symbol wheel_of_dharma star_of_david six_pointed_star menorah yin_yang orthodox_cross " +
                    "place_of_worship ophiuchus aries taurus gemini cancer leo virgo libra scorpius sagittarius capricorn " +
                    "aquarius pisces id atom u7a7a u5272 radioactive biohazard mobile_phone_off vibration_mode u6709 u7121 " +
                    "u7533 u55b6 u6708 eight_pointed_black_star vs accept white_flower ideograph_advantage secret congratulations " +
                    "u5408 u6e80 u7981 a b ab cl o2 sos no_entry name_badge no_entry_sign x o anger hotsprings no_pedestrians " +
                    "do_not_litter no_bicycles non-potable_water underage no_mobile_phones exclamation grey_exclamation question " +
                    "grey_question bangbang interrobang 100 low_brightness high_brightness trident fleur-de-lis part_alternation_mark " +
                    "warning children_crossing beginner recycle u6307 chart sparkle eight_spoked_asterisk negative_squared_cross_mark " +
                    "white_check_mark diamond_shape_with_a_dot_inside cyclone loop globe_with_meridians m atm sa passport_control " +
                    "customs baggage_claim left_luggage wheelchair no_smoking wc parking potable_water mens womens baby_symbol " +
                    "restroom put_litter_in_its_place cinema signal_strength koko ng ok up cool new free zero one two three four " +
                    "five six seven eight nine ten 1234 arrow_forward pause_button play_pause stop_button record_button track_next " +
                    "track_previous fast_forward rewind twisted_rightwards_arrows repeat repeat_one arrow_backward arrow_up_small " +
                    "arrow_down_small arrow_double_up arrow_double_down arrow_right arrow_left arrow_up arrow_down arrow_upper_right " +
                    "arrow_lower_right arrow_lower_left arrow_upper_left arrow_up_down left_right_arrow arrows_counterclockwise " +
                    "arrow_right_hook leftwards_arrow_with_hook arrow_heading_up arrow_heading_down hash asterisk information_source " +
                    "abc abcd capital_abcd symbols musical_note notes wavy_dash curly_loop heavy_check_mark arrows_clockwise " +
                    "heavy_plus_sign heavy_minus_sign heavy_division_sign heavy_multiplication_x heavy_dollar_sign currency_exchange " +
                    "copyright registered tm end back on top soon ballot_box_with_check radio_button white_circle black_circle " +
                    "red_circle large_blue_circle small_orange_diamond small_blue_diamond large_orange_diamond large_blue_diamond " +
                    "small_red_triangle black_small_square white_small_square black_large_square white_large_square small_red_triangle_down " +
                    "black_medium_square white_medium_square black_medium_small_square white_medium_small_square black_square_button " +
                    "white_square_button speaker sound loud_sound mute mega loudspeaker bell no_bell black_joker mahjong spades " +
                    "clubs hearts diamonds flower_playing_cards thought_balloon anger_right speech_balloon clock1 clock2 clock3 " +
                    "clock4 clock5 clock6 clock7 clock8 clock9 clock10 clock11 clock12 clock130 clock230 clock330 clock430 " +
                    "clock530 clock630 clock730 clock830 clock930 clock1030 clock1130 clock1230 eye_in_speech_bubble"
                },

                flags: {
                    icon: "flag_gb",
                    title: "Flags",
                    emoji: "ac af al dz ad ao ai ag ar am aw au at az bs bh bd bb by be bz bj bm bt bo ba bw br bn bg bf bi " +
                    "cv kh cm ca ky cf td flag_cl cn co km cg flag_cd cr hr cu cy cz dk dj dm do ec eg sv gq er ee et fk fo " +
                    "fj fi fr pf ga gm ge de gh gi gr gl gd gu gt gn gw gy ht hn hk hu is in flag_id ir iq ie il it ci jm jp " +
                    "je jo kz ke ki xk kw kg la lv lb ls lr ly li lt lu mo mk mg mw my mv ml mt mh mr mu mx fm md mc mn me " +
                    "ms ma mz mm na nr np nl nc nz ni ne flag_ng nu kp no om pk pw ps pa pg py pe ph pl pt pr qa ro ru rw " +
                    "sh kn lc vc ws sm st flag_sa sn rs sc sl sg sk si sb so za kr es lk sd sr sz se ch sy tw tj tz th tl " +
                    "tg to tt tn tr flag_tm flag_tm ug ua ae gb us vi uy uz vu va ve vn wf eh ye zm zw re ax ta io bq cx " +
                    "cc gg im yt nf pn bl pm gs tk bv hm sj um ic ea cp dg as aq vg ck cw eu gf tf gp mq mp sx ss tc "
                }
            }
        };
    };
    function isObject(variable) {
        return typeof variable === 'object';
    };
    function getOptions(options) {
        var default_options = getDefaultOptions();
        if (options && options['filters']) {
            var filters = default_options.filters;
            $.each(options['filters'], function(filter, data) {
                if (!isObject(data) || $.isEmptyObject(data)) {
                    delete filters[filter];
                    return;
                }
                $.each(data, function(key, val) {
                    filters[filter][key] = val;
                });
            });
            options['filters'] = filters;
        }
        return $.extend({}, default_options, options);
    };

    var saveSelection, restoreSelection;
    if (window.getSelection && document.createRange) {
        saveSelection = function(el) {
            var sel = window.getSelection && window.getSelection();
            if (sel && sel.rangeCount > 0) {
                var range = sel.getRangeAt(0);
                var preSelectionRange = range.cloneRange();
                preSelectionRange.selectNodeContents(el);
                preSelectionRange.setEnd(range.startContainer, range.startOffset);
                return preSelectionRange.toString().length;
            }
        };

        restoreSelection = function(el, sel) {
            var charIndex = 0, range = document.createRange();
            range.setStart(el, 0);
            range.collapse(true);
            var nodeStack = [el], node, foundStart = false, stop = false;

            while (!stop && (node = nodeStack.pop())) {
                if (node.nodeType == 3) {
                    var nextCharIndex = charIndex + node.length;
                    if (!foundStart && sel >= charIndex && sel <= nextCharIndex) {
                        range.setStart(node, sel - charIndex);
                        range.setEnd(node, sel - charIndex);
                        stop = true;
                    }
                    charIndex = nextCharIndex;
                } else {
                    var i = node.childNodes.length;
                    while (i--) {
                        nodeStack.push(node.childNodes[i]);
                    }
                }
            }

            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }
    } else if (document.selection && document.body.createTextRange) {
        saveSelection = function(el) {
            var selectedTextRange = document.selection.createRange(),
                preSelectionTextRange = document.body.createTextRange();
            preSelectionTextRange.moveToElementText(el);
            preSelectionTextRange.setEndPoint("EndToStart", selectedTextRange);
            var start = preSelectionTextRange.text.length;
            return start + selectedTextRange.text.length;
        };

        restoreSelection = function(el, sel) {
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(el);
            textRange.collapse(true);
            textRange.moveEnd("character", sel);
            textRange.moveStart("character", sel);
            textRange.select();
        };
    }


    var uniRegexp;
    function unicodeTo(str, template) {
        return str.replace(uniRegexp, function(unicodeChar) {
            var map = emojione[(emojioneSupportMode === 0 ? 'jsecapeMap' : 'jsEscapeMap')];
            if (typeof unicodeChar !== 'undefined' && unicodeChar in map) {
                return getTemplate(template, map[unicodeChar]);
            }
            return unicodeChar;
        });
    }
    function htmlFromText(str, self) {
        str = str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;')
            .replace(/`/g, '&#x60;')
            .replace(/(?:\r\n|\r|\n)/g, '\n')
            .replace(/(\n+)/g, '<div>$1</div>')
            .replace(/\n/g, '<br/>')
            .replace(/<br\/><\/div>/g, '</div>');
        if (self.shortnames) {
            str = emojione.shortnameToUnicode(str);
        }
        return unicodeTo(str, self.emojiTemplate)
            .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;')
            .replace(/  /g, '&nbsp;&nbsp;');
    }
    function textFromHtml(str, self) {
        str = str
            .replace(/<img[^>]*alt="([^"]+)"[^>]*>/ig, '$1')
            .replace(/\n|\r/g, '')
            .replace(/<br[^>]*>/ig, '\n')
            .replace(/(?:<(?:div|p|ol|ul|li|pre|code|object)[^>]*>)+/ig, '<div>')
            .replace(/(?:<\/(?:div|p|ol|ul|li|pre|code|object)>)+/ig, '</div>')
            .replace(/\n<div><\/div>/ig, '\n')
            .replace(/<div><\/div>\n/ig, '\n')
            .replace(/(?:<div>)+<\/div>/ig, '\n')
            .replace(/([^\n])<\/div><div>/ig, '$1\n')
            .replace(/(?:<\/div>)+/ig, '</div>')
            .replace(/([^\n])<\/div>([^\n])/ig, '$1\n$2')
            .replace(/<\/div>/ig, '')
            .replace(/([^\n])<div>/ig, '$1\n')
            .replace(/\n<div>/ig, '\n')
            .replace(/<div>\n/ig, '\n\n')
            .replace(/<(?:[^>]+)?>/g, '')
            .replace(new RegExp(invisibleChar, 'g'), '')
            .replace(/&nbsp;/g, ' ')
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
            .replace(/&quot;/g, '"')
            .replace(/&#x27;/g, "'")
            .replace(/&#x60;/g, '`')
            .replace(/&amp;/g, '&');

        switch (self.saveEmojisAs) {
            case 'image':
                str = unicodeTo(str, self.emojiTemplate);
                break;
            case 'shortname':
                str = emojione.toShort(str);
        }
        return str;
    }
    function calcButtonPosition() {
        var self = this,
            offset = self.editor[0].offsetWidth - self.editor[0].clientWidth,
            current = parseInt(self.button.css('marginRight'));
        if (current !== offset) {
            self.button.css({marginRight: offset});
            if (self.floatingPicker) {
                self.picker.css({right: parseInt(self.picker.css('right')) - current + offset});
            }
        }
    }
    function lazyLoading() {
        var self = this;
        if (!self.sprite && self.lasyEmoji[0]) {
            var pickerTop = self.picker.offset().top,
                pickerBottom = pickerTop + self.picker.height() + 20;
            self.lasyEmoji.each(function() {
                var e = $(this), top = e.offset().top;
                if (top > pickerTop && top < pickerBottom) {
                    e.attr("src", e.data("src")).removeClass("lazy-emoji");
                }
            })
            self.lasyEmoji = self.lasyEmoji.filter(".lazy-emoji");
        }
    }
    function selector (prefix, skip_dot) {
        return (skip_dot ? '' : '.') + css_class + (prefix ? ("-" + prefix) : "");
    }
    function div(prefix) {
        var parent = $('<div/>', isObject(prefix) ? prefix : {"class" : selector(prefix, true)});
        $.each(slice.call(arguments).slice(1), function(i, child) {
            if ($.isFunction(child)) {
                child = child.call(parent);
            }
            if (child) {
                $(child).appendTo(parent);
            }
        });
        return parent;
    }
    function getRecent () {
        return localStorage.getItem("recent_emojis") || "";
    }
    function updateRecent(self) {
        var emojis = getRecent();
        if (!self.recent || self.recent !== emojis) {
            if (emojis.length) {
                var skinnable = self.scrollArea.is(".skinnable"),
                    scrollTop, height;

                if (!skinnable) {
                    scrollTop = self.scrollArea.scrollTop();
                    height = self.recentCategory.is(":visible") ? self.recentCategory.height() : 0;
                }

                var items = shortnameTo(emojis, self.emojiBtnTemplate, true).split('|').join('');
                self.recentCategory.children(".emojibtn").remove();
                $(items).insertAfter(self.recentCategory.children("h1"));


                self.recentCategory.children(".emojibtn").on("click", function() {
                    self.trigger("emojibtn.click", $(this));
                });

                self.recentFilter.show();

                if (!skinnable) {
                    self.recentCategory.show();

                    var height2 = self.recentCategory.height();

                    if (height !== height2) {
                        self.scrollArea.scrollTop(scrollTop + height2 - height);
                    }
                }
            } else {
                if (self.recentFilter.hasClass("active")) {
                    self.recentFilter.removeClass("active").next().addClass("active");
                }
                self.recentCategory.hide();
                self.recentFilter.hide();
            }
            self.recent = emojis;
        }
    };
    function setRecent(self, emoji) {
        var recent = getRecent();
        var emojis = recent.split("|");

        var index = emojis.indexOf(emoji);
        if (index !== -1) {
            emojis.splice(index, 1);
        }
        emojis.unshift(emoji);

        if (emojis.length > 9) {
            emojis.pop();
        }

        localStorage.setItem("recent_emojis", emojis.join("|"));

        updateRecent(self);
    };
// see https://github.com/Modernizr/Modernizr/blob/master/feature-detects/storage/localstorage.js
    function supportsLocalStorage () {
        var test = 'test';
        try {
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch(e) {
            return false;
        }
    }
    function init(self, source, options) {
        //calcElapsedTime('init', function() {
        options = getOptions(options);
        self.sprite = options.sprite && emojioneSupportMode < 3;
        self.inline = options.inline === null ? source.is("INPUT") : options.inline;
        self.shortnames = options.shortnames;
        self.saveEmojisAs = options.saveEmojisAs;
        self.standalone = options.standalone;
        self.emojiTemplate = '<img alt="{alt}" class="emojione' + (self.sprite ? '-{uni}" src="' + blankImg + '"/>' : 'emoji" src="{img}"/>');
        self.emojiTemplateAlt = self.sprite ? '<i class="emojione-{uni}"/>' : '<img class="emojioneemoji" src="{img}"/>';
        self.emojiBtnTemplate = '<i class="emojibtn" role="button" data-name="{name}">' + self.emojiTemplateAlt + '</i>';
        self.recentEmojis = options.recentEmojis && supportsLocalStorage();

        var pickerPosition = options.pickerPosition;
        self.floatingPicker = pickerPosition === 'top' || pickerPosition === 'bottom';

        var sourceValFunc = source.is("TEXTAREA") || source.is("INPUT") ? "val" : "text",
            editor, button, picker, tones, filters, filtersBtns, emojisList, categories, scrollArea,
            app = div({
                "class" : css_class + ((self.standalone) ? " " + css_class + "-standalone " : " ") + (source.attr("class") || ""),
                role: "application"
            },
            editor = self.editor = div("editor").attr({
                contenteditable: (self.standalone) ? false : true,
                placeholder: options["placeholder"] || source.data("placeholder") || source.attr("placeholder") || "",
                tabindex: 0
            }),
            button = self.button = div('button',
                div('button-open'),
                div('button-close')
            ).attr('title', options.buttonTitle),
            picker = self.picker = div('picker',
                div('wrapper',
                    filters = div('filters'),
                    scrollArea = div('scroll-area',
                        emojisList = div('emojis-list'),
                        tones = div('tones',
                            function() {
                                if (options.tones) {
                                    this.addClass(selector('tones-' + options.tonesStyle, true));
                                    for (var i = 0; i <= 5; i++) {
                                        this.append($("<i/>", {
                                            "class": "btn-tone btn-tone-" + i + (!i ? " active" : ""),
                                            "data-skin": i,
                                            role: "button"
                                        }));
                                    }
                                }
                            }
                        )
                    )
                )
            ).addClass(selector('picker-position-' + options.pickerPosition, true))
             .addClass(selector('filters-position-' + options.filtersPosition, true))
             .addClass('hidden')
        );

        editor.data(source.data());

        $.each(options.attributes, function(attr, value) {
            editor.attr(attr, value);
        });

        $.each(options.filters, function(filter, params) {
            var skin = 0;
            if (filter === 'recent' && !self.recentEmojis) {
                return;
            }
            if (filter !== 'tones') {
                $("<i/>", {
                    "class": selector("filter", true) + " " + selector("filter-" + filter, true),
                    "data-filter": filter,
                    title: params.title
                })
                .wrapInner(shortnameTo(params.icon, self.emojiTemplateAlt))
                .appendTo(filters);
            } else if (options.tones) {
                skin = 5;
            } else {
                return;
            }
            do {
                var category = div('category').attr({name: filter, "data-tone": skin}).appendTo(emojisList),
                    items = params.emoji.replace(/[\s,;]+/g, '|');
                if (skin > 0) {
                    category.hide();
                    items = items.split('|').join('_tone' + skin + '|') + '_tone' + skin;
                }

                if (filter === 'recent') {
                    items = getRecent();
                }

                items = shortnameTo(items,
                    self.sprite ?
                        '<i class="emojibtn" role="button" data-name="{name}"><i class="emojione-{uni}"></i></i>' :
                        '<i class="emojibtn" role="button" data-name="{name}"><img class="emojioneemoji lazy-emoji" data-src="{img}"/></i>',
                    true).split('|').join('');

                category.html(items);
                $('<h1/>').text(params.title).prependTo(category);
            } while (--skin > 0);
        });

        options.filters = null;
        if (!self.sprite) {
            self.lasyEmoji = emojisList.find(".lazy-emoji");
        }

        filtersBtns = filters.find(selector("filter"));
        filtersBtns.eq(0).addClass("active");
        categories = emojisList.find(selector("category"));

        self.recentFilter = filtersBtns.filter('[data-filter="recent"]');
        self.recentCategory = categories.filter("[name=recent]");

        self.scrollArea = scrollArea;

        if (options.container) {
            $(options.container).wrapInner(app);
        } else {
            app.insertAfter(source);
        }

        if (options.hideSource) {
            source.hide();
        }

        self.setText(source[sourceValFunc]());
        source[sourceValFunc](self.getText());
        calcButtonPosition.apply(self);

        // if in standalone mode and no value is set, initialise with a placeholder
        if (self.standalone && !self.getText().length) {
            var placeholder = $(source).data("emoji-placeholder") || options.emojiPlaceholder;
            self.setText(placeholder);
            editor.addClass("has-placeholder");
        }

        // attach() must be called before any .on() methods !!!
        // 1) attach() stores events into possibleEvents{},
        // 2) .on() calls bindEvent() and stores handlers into eventStorage{},
        // 3) bindEvent() finds events in possibleEvents{} and bind founded via jQuery.on()
        // 4) attached events via jQuery.on() calls trigger()
        // 5) trigger() calls handlers stored into eventStorage{}

        attach(self, emojisList.find(".emojibtn"), {click: "emojibtn.click"});
        attach(self, window, {resize: "!resize"});
        attach(self, tones.children(), {click: "tone.click"});
        attach(self, [picker, button], {mousedown: "!mousedown"}, editor);
        attach(self, button, {click: "button.click"});
        attach(self, editor, {paste :"!paste"}, editor);
        attach(self, editor, ["focus", "blur"], function() { return self.stayFocused ? false : editor; });
        attach(self, picker, {mousedown: "picker.mousedown", mouseup: "picker.mouseup", click: "picker.click",
            keyup: "picker.keyup", keydown: "picker.keydown", keypress: "picker.keypress"});
        attach(self, editor, ["mousedown", "mouseup", "click", "keyup", "keydown", "keypress"]);
        attach(self, picker.find(".emojionearea-filter"), {click: "filter.click"});

        var noListenScroll = false;
        scrollArea.on('scroll', function () {
            if (!noListenScroll) {
                lazyLoading.call(self);
                if (scrollArea.is(":not(.skinnable)")) {
                    var item = categories.eq(0), scrollTop = scrollArea.offset().top;
                    categories.each(function (i, e) {
                        if ($(e).offset().top - scrollTop >= 10) {
                            return false;
                        }
                        item = $(e);
                    });
                    var filter = filtersBtns.filter('[data-filter="' + item.attr("name") + '"]');
                    if (filter[0] && !filter.is(".active")) {
                        filtersBtns.removeClass("active");
                        filter.addClass("active");
                    }
                }
            }
        });

        self.on("@filter.click", function(filter) {
            var isActive = filter.is(".active");
            if (scrollArea.is(".skinnable")) {
                if (isActive) return;
                tones.children().eq(0).click();
            }
            noListenScroll = true;
            if (!isActive) {
                filtersBtns.filter(".active").removeClass("active");
                filter.addClass("active");
            }
            var headerOffset = categories.filter('[name="' + filter.data('filter') + '"]').offset().top,
                scroll = scrollArea.scrollTop(),
                offsetTop = scrollArea.offset().top;
            scrollArea.stop().animate({
                scrollTop: headerOffset + scroll - offsetTop - 2
            }, 200, 'swing', function () {
                lazyLoading.call(self);
                noListenScroll = false;
            });
        })

        .on("@picker.show", function() {
            if (self.recentEmojis) {
                updateRecent(self);
            }
            lazyLoading.call(this);
        })

        .on("@tone.click", function(tone) {
            tones.children().removeClass("active");
            var skin = tone.addClass("active").data("skin");
            if (skin) {
                scrollArea.addClass("skinnable");
                categories.hide().filter("[data-tone=" + skin + "]").show();
                if (filtersBtns.eq(0).is('.active[data-filter="recent"]')) {
                    filtersBtns.eq(0).removeClass("active").next().addClass("active");
                }
            } else {
                scrollArea.removeClass("skinnable");
                categories.hide().filter("[data-tone=0]").show();
                filtersBtns.eq(0).click();
            }
            lazyLoading.call(self);
        })

        .on("@button.click", function(button) {
            if (button.is(".active")) {
                self.hidePicker();
            } else {
                self.showPicker();
            }
        })

        .on("@!paste", function(editor, event) {

            var pasteText = function(text) {
                var caretID = "caret-" + (new Date()).getTime();
                var html = htmlFromText(text, self);
                pasteHtmlAtCaret(html);
                pasteHtmlAtCaret('<i id="' + caretID +'"></i>');
                editor.scrollTop(editorScrollTop);
                var caret = $("#" + caretID),
                    top = caret.offset().top - editor.offset().top,
                    height = editor.height();
                if (editorScrollTop + top >= height || editorScrollTop > top) {
                    editor.scrollTop(editorScrollTop + top - 2 * height/3);
                }
                caret.remove();
                self.stayFocused = false;
                calcButtonPosition.apply(self);
                trigger(self, 'paste', [editor, text, html]);
            }

            if (event.originalEvent.clipboardData) {
                var text = event.originalEvent.clipboardData.getData('text/plain');
                pasteText(text);

                if (event.preventDefault){
                    event.preventDefault();
                } else {
                    event.stop();
                };

                event.returnValue = false;
                event.stopPropagation();
                return false;
            }

            self.stayFocused = true;
            // insert invisible character for fix caret position
            pasteHtmlAtCaret('<span>' + invisibleChar + '</span>');

            var sel = saveSelection(editor[0]),
                editorScrollTop = editor.scrollTop(),
                clipboard = $("<div/>", {contenteditable: true})
                    .css({position: "fixed", left: "-999px", width: "1px", height: "1px", top: "20px", overflow: "hidden"})
                    .appendTo($("BODY"))
                    .focus();

            window.setTimeout(function() {
                editor.focus();
                restoreSelection(editor[0], sel);
                var text = textFromHtml(clipboard.html().replace(/\r\n|\n|\r/g, '<br>'), self);
                clipboard.remove();
                pasteText(text);
            }, 200);
        })

        .on("@emojibtn.click", function(emojibtn) {
            editor.removeClass("has-placeholder");
            if (!app.is(".focused")) {
                editor.focus();
            }
            if (self.standalone) {
                editor.html(shortnameTo(emojibtn.data("name"), self.emojiTemplate));
                self.trigger("blur");
            } else {
                saveSelection(editor[0]);
                pasteHtmlAtCaret(shortnameTo(emojibtn.data("name"), self.emojiTemplate));
            }

            if (self.recentEmojis) {
                setRecent(self, emojibtn.data("name"));
            }
        })

        .on("@!resize @keyup @emojibtn.click", calcButtonPosition)

        .on("@!mousedown", function(editor, event) {
            if (!app.is(".focused")) {
                editor.focus();
            }
            event.preventDefault();
            return false;
        })

        .on("@change", function() {
            var html = self.editor.html().replace(/<\/?(?:div|span|p)[^>]*>/ig, '');
            // clear input: chrome adds <br> when contenteditable is empty
            if (!html.length || /^<br[^>]*>$/i.test(html)) {
                self.editor.html(self.content = '');
            }
            source[sourceValFunc](self.getText());
        })

        .on("@focus", function() {
            app.addClass("focused");
        })

        .on("@blur", function() {
            app.removeClass("focused");

            if (options.hidePickerOnBlur) {
                self.hidePicker();
            }

            var content = self.editor.html();
            if (self.content !== content) {
                self.content = content;
                trigger(self, 'change', [self.editor]);
                source.blur().trigger("change");
            } else {
                source.blur();
            }
        });

        if (options.shortcuts) {
            self.on("@keydown", function(_, e) {
                if (!e.ctrlKey) {
                    if (e.which == 9) {
                        e.preventDefault();
                        button.click();
                    }
                    else if (e.which == 27) {
                        e.preventDefault();
                        if (button.is(".active")) {
                            self.hidePicker();
                        }
                    }
                }
            });
        }

        if (isObject(options.events) && !$.isEmptyObject(options.events)) {
            $.each(options.events, function(event, handler) {
                self.on(event.replace(/_/g, '.'), handler);
            });
        }

        if (options.autocomplete) {
            var autocomplete = function() {
                var textcompleteOptions = {
                    maxCount: options.textcomplete.maxCount,
                    placement: options.textcomplete.placement
                };

                if (options.shortcuts) {
                    textcompleteOptions.onKeydown = function (e, commands) {
                        if (!e.ctrlKey && e.which == 13) {
                            return commands.KEY_ENTER;
                        }
                    };
                }

                var map = $.map(emojione.emojioneList, function (_, emoji) {
                    return !options.autocompleteTones ? /_tone[12345]/.test(emoji) ? null : emoji : emoji;
                });
                map.sort();
                editor.textcomplete([
                    {
                        id: css_class,
                        match: /\B(:[\-+\w]*)$/,
                        search: function (term, callback) {
                            callback($.map(map, function (emoji) {
                                return emoji.indexOf(term) === 0 ? emoji : null;
                            }));
                        },
                        template: function (value) {
                            return shortnameTo(value, self.emojiTemplate) + " " + value.replace(/:/g, '');
                        },
                        replace: function (value) {
                            return shortnameTo(value, self.emojiTemplate);
                        },
                        cache: true,
                        index: 1
                    }
                ], textcompleteOptions);

                if (options.textcomplete.placement) {
                    // Enable correct positioning for textcomplete
                    if ($(editor.data('textComplete').option.appendTo).css("position") == "static") {
                        $(editor.data('textComplete').option.appendTo).css("position", "relative");
                    }
                }
            };
            if ($.fn.textcomplete) {
                autocomplete();
            } else {
                $.getScript("https://cdn.rawgit.com/yuku-t/jquery-textcomplete/v1.3.4/dist/jquery.textcomplete.js",
                    autocomplete);
            }
        }

        if (self.inline) {
            app.addClass(selector('inline', true));
            self.on("@keydown", function(_, e) {
                if (e.which == 13) {
                    e.preventDefault();
                }
            });
        }

        if (/firefox/i.test(navigator.userAgent)) {
            // disabling resize images on Firefox
            document.execCommand("enableObjectResizing", false, false);
        }

        //}, self.id === 1); // calcElapsedTime()
    };
    var emojioneVersion = window.emojioneVersion || '2.1.4';
    var cdn = { 
        defaultBase: "https://cdnjs.cloudflare.com/ajax/libs/emojione/",
        base: null,
        isLoading: false
    };
    function loadEmojione(options) {

        function detectVersion(emojione) {
            var version = emojione.cacheBustParam;
            if (!isObject(emojione['jsEscapeMap'])) return '1.5.2';
            if (version === "?v=1.2.4") return '2.0.0';
            if (version === "?v=2.0.1") return '2.1.0'; // v2.0.1 || v2.1.0
            if (version === "?v=2.1.1") return '2.1.1';
            if (version === "?v=2.1.2") return '2.1.2';
            if (version === "?v=2.1.3") return '2.1.3';
            if (version === "?v=2.1.4") return '2.1.4';
            return '2.1.4';
        }

        function getSupportMode(version) {
            switch (version) {
                case '1.5.2': return 0;
                case '2.0.0': return 1;
                case '2.1.0':
                case '2.1.1': return 2;
                case '2.1.2': return 3;
                case '2.1.3':
                case '2.1.4':
                default: return 4;
            }
        }
        options = getOptions(options);

        if (!cdn.isLoading) {
            if (!emojione || getSupportMode(detectVersion(emojione)) < 2) {
                cdn.isLoading = true;
                $.getScript(cdn.defaultBase + emojioneVersion + "/lib/js/emojione.min.js", function () {
                    emojione = window.emojione;
                    emojioneVersion = detectVersion(emojione);
                    emojioneSupportMode = getSupportMode(emojioneVersion);
                    cdn.base = cdn.defaultBase + emojioneVersion + "/assets";
                    if (options.sprite) {
                        var sprite = cdn.base + "/sprites/emojione.sprites.css";
                        if (document.createStyleSheet) {
                            document.createStyleSheet(sprite);
                        } else {
                            $('<link/>', {rel: 'stylesheet', href: sprite}).appendTo('head');
                        }
                    }
                    while (readyCallbacks.length) {
                        readyCallbacks.shift().call();
                    }
                    cdn.isLoading = false;
                });
            } else {
                emojioneVersion = detectVersion(emojione);
                emojioneSupportMode = getSupportMode(emojioneVersion);
                cdn.base = cdn.defaultBase + emojioneVersion + "/assets";
            }
        }

        emojioneReady(function() {
            if (options.useInternalCDN) {
                emojione.imagePathPNG = cdn.base + "/png/";
                emojione.imagePathSVG = cdn.base + "/svg/";
                emojione.imagePathSVGSprites = cdn.base + "/sprites/emojione.sprites.svg";
                emojione.imageType = options.imageType;
            }

            uniRegexp = new RegExp("<object[^>]*>.*?<\/object>|<span[^>]*>.*?<\/span>|<(?:object|embed|svg|img|div|span|p|a)[^>]*>|(" + emojione.unicodeRegexp + ")", "gi");
        });
    };
    var EmojioneArea = function(element, options) {
        var self = this;
        loadEmojione(options);
        eventStorage[self.id = ++unique] = {};
        possibleEvents[self.id] = {};
        emojioneReady(function() {
            init(self, element, options);
        });
    };
    function bindEvent(self, event) {
        event = event.replace(/^@/, '');
        var id = self.id;
        if (possibleEvents[id][event]) {
            $.each(possibleEvents[id][event], function(i, ev) {
                // ev[0] = element
                // ev[1] = event
                // ev[2] = target
                $.each($.isArray(ev[0]) ? ev[0] : [ev[0]], function(i, el) {
                    $(el).on(ev[1], function() {
                        var args = slice.call(arguments),
                            target = $.isFunction(ev[2]) ? ev[2].apply(self, [event].concat(args)) : ev[2];
                        if (target) {
                            trigger(self, event, [target].concat(args));
                        }
                    });
                });
            });
            possibleEvents[id][event] = null;
        }
    }

    EmojioneArea.prototype.on = function(events, handler) {
        if (events && $.isFunction(handler)) {
            var self = this;
            $.each(events.toLowerCase().split(' '), function(i, event) {
                bindEvent(self, event);
                (eventStorage[self.id][event] || (eventStorage[self.id][event] = [])).push(handler);
            });
        }
        return this;
    };

    EmojioneArea.prototype.off = function(events, handler) {
        if (events) {
            var id = this.id;
            $.each(events.toLowerCase().replace(/_/g, '.').split(' '), function(i, event) {
                if (eventStorage[id][event] && !/^@/.test(event)) {
                    if (handler) {
                        $.each(eventStorage[id][event], function(j, fn) {
                            if (fn === handler) {
                                eventStorage[id][event] = eventStorage[id][event].splice(j, 1);
                            }
                        });
                    } else {
                        eventStorage[id][event] = [];
                    }
                }
            });
        }
        return this;
    };

    EmojioneArea.prototype.trigger = function() {
        var args = slice.call(arguments),
            call_args = [this].concat(args.slice(0,1));
        call_args.push(args.slice(1));
        return trigger.apply(this, call_args);
    };

    EmojioneArea.prototype.setFocus = function () {
        var self = this;
        emojioneReady(function () {
            self.editor.focus();
        });
        return self;
    };

    EmojioneArea.prototype.setText = function (str) {
        var self = this;
        emojioneReady(function () {
            self.editor.html(htmlFromText(str, self));
            self.content = self.editor.html();
            trigger(self, 'change', [self.editor]);
            calcButtonPosition.apply(self);
        });
        return self;
    }

    EmojioneArea.prototype.getText = function() {
        return textFromHtml(this.editor.html(), this);
    }

    EmojioneArea.prototype.showPicker = function () {
        var self = this;
        if (self._sh_timer) {
            window.clearTimeout(self._sh_timer);
        }
        self.picker.removeClass("hidden");
        self._sh_timer =  window.setTimeout(function() {
            self.button.addClass("active");
        }, 50);
        trigger(self, "picker.show", [self.picker]);
        return self;
    }

    EmojioneArea.prototype.hidePicker = function () {
        var self = this;
        if (self._sh_timer) {
            window.clearTimeout(self._sh_timer);
        }
        self.button.removeClass("active");
        self._sh_timer =  window.setTimeout(function() {
            self.picker.addClass("hidden");
        }, 500);
        trigger(self, "picker.hide", [self.picker]);
        return self;
    }

    $.fn.emojioneArea = function(options) {
        return this.each(function() {
            if (!!this.emojioneArea) return this.emojioneArea;
            $.data(this, 'emojioneArea', this.emojioneArea = new EmojioneArea($(this), options));
            return this.emojioneArea;
        });
    };

    $.fn.emojioneArea.defaults = getDefaultOptions();

}) (document, window, jQuery);

//# sourceMappingURL=merchant.js.map
