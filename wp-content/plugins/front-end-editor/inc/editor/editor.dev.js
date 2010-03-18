jQuery(document).ready(function($){
	if ( frontEditorData._loaded )
		return;
	frontEditorData._loaded = true;

	// http://ejohn.org/blog/simple-javascript-inheritance/
	// Inspired by base2 and Prototype
	(function(){
	  var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;

	  // The base Class implementation (does nothing)
	  this.Class = function(){};

	  // Create a new Class that inherits from this class
	  Class.extend = function(prop) {
		var _super = this.prototype;

		// Instantiate a base class (but only create the instance,
		// don't run the init constructor)
		initializing = true;
		var prototype = new this();
		initializing = false;

		// Copy the properties over onto the new prototype
		for (var name in prop) {
		  // Check if we're overwriting an existing function
		  prototype[name] = ( typeof prop[name] == "function" &&
		    typeof _super[name] == "function" && fnTest.test(prop[name]) ) ?
		    (function(name, fn){
		      return function() {
		        var tmp = this._super;

		        // Add a new ._super() method that is the same method
		        // but on the super-class
		        this._super = _super[name];

		        // The method only need to be bound temporarily, so we
		        // remove it when we're done executing
		        var ret = fn.apply(this, arguments);
		        this._super = tmp;

		        return ret;
		      };
		    })(name, prop[name]) :
		    prop[name];
		}

		// The dummy class constructor
		function Class() {
		  // All construction is actually done in the init method
		  if ( !initializing && this.init )
		    this.init.apply(this, arguments);
		}

		// Populate our constructed prototype object
		Class.prototype = prototype;

		// Enforce the constructor to be what we expect
		Class.constructor = Class;

		// And make this class extendable
		Class.extend = arguments.callee;

		return Class;
	  };
	})();


	var spinner = $('<img>').attr({
		'src': frontEditorData.spinner,
		'class': 'front-editor-spinner'
	});

	var is_overlay = function($el) {
		var attr = $el.attr('id') + ' ' + $el.attr("class");
		attr = $.trim(attr).split(' ');

		var tokens = ['lightbox', 'shutter', 'thickbox', 'awppost_link'];

		for ( i in tokens )
			for ( j in attr )
				if ( attr[j].indexOf(tokens[i]) != -1 )
					return true;

		return false;
	};

	var resume = function() {
		if ( frontEditorData._trap )
			return;

		var $link = frontEditorData._to_click;

		if ( typeof $link == 'undefined' )
			return;

/*
		var ev_reference;
		var ev_capture = function(ev) {	ev_reference = ev; }

		var onClick = $link.attr('onclick');

		$link.bind('click', ev_capture);

		if ( typeof onClick == 'function' )
			$link.bind('click', onClick);

		$link.click();

		$link.unbind('click', ev_capture);

		if ( typeof onClick == 'function' )
			$link.unbind('click', onClick);

		if ( ev_reference.isDefaultPrevented() )
			return;
*/

		if ( typeof $link.attr('href') != 'undefined' && $link.attr('href') != '#' ) {
			if ( $link.attr('target') == '_blank' )
				window.open($link.attr('href'));
			else
				window.location.href = $link.attr('href');
		}

		delete frontEditorData._to_click;
	};

	var classes = {};

	classes['base'] = Class.extend({
		init: function($el, type, name, id) {
			var self = this;

			self.set_el($el);
			self.type = type;
			self.name = name;
			self.id = id;

			self.bind(self.el, 'click', self.click);
			self.bind(self.el, 'dblclick', self.dblclick);
		},

		set_el: function($el) {
			var self = this;

			self.el = $el;

			// From a > .front-ed > content
			// To .front-ed > a > content
			var $parent = self.el.parents('a');

			if ( ! $parent.length )
				return;

			var $link = $parent.clone(true)
				.html(self.el.html());

			var $wrap = self.el.clone(true)
				.html($link);

			$parent.replaceWith($wrap);

			self.el = $wrap;
		},

		click: function(ev) {
//			if ( typeof frontEditorData._to_click != 'undefined' )
//				return;

			var $el = $(ev.target).closest('a');

			if ( ! $el.length )
				return;

			if ( is_overlay($el) )
				return;

			ev.stopImmediatePropagation();
			ev.preventDefault();

			frontEditorData._to_click = $el;

			setTimeout(resume, 300);
		},

		dblclick: function(ev) {
			var self = this;

			ev.stopPropagation();
			ev.preventDefault();

			frontEditorData._trap = true;
		},

		get_content: null /* function() */,
		set_content: null /* function(content) */,

		ajax_get_handler: null /* function(content) */,
		ajax_set_handler: null /* function(content) */,

		ajax_get: function() {
			var self = this;

			var data = {
				'nonce': frontEditorData.nonce,
				'action': 'front-editor',
				'callback': 'get',
				'name': self.name,
				'type': self.type,
				'item_id': self.id
			};

			$.post(frontEditorData.ajax_url, data, function(response){
				self.ajax_get_handler(response);
			});
		},

		ajax_set: function(content) {
			var self = this;

			content = content || self.get_content();

			var data = {
				'nonce': frontEditorData.nonce,
				'action': 'front-editor',
				'callback': 'save',
				'name': self.name,
				'type': self.type,
				'item_id': self.id,
				'content': content
			};

			$.post(frontEditorData.ajax_url, data, function(response){
				self.ajax_set_handler(response);
			});
		},

		// Event utility: this = self
		bind: function(element, event, callback) {
			var self = this;

			element.bind(event, function(ev) {
				callback.call(self, ev);
			});
		}
	});

	classes['image'] = classes['base'].extend({
		dblclick: function(ev) {
			var self = this;

			self._super(ev);

			self.open_box();
		},

		open_box: function() {
			var self = this;

			tb_show(frontEditorData.caption, frontEditorData.admin_url +
				'/media-upload.php?type=image&TB_iframe=true&width=640&editable_image=1');

			var $revert = $('<a id="fee_img_revert" href="#">').text(frontEditorData.img_revert);
			
			$revert.click(function(ev){
				self.ajax_set(-1);
			});

			$('#TB_ajaxWindowTitle').after($revert);
			$('#TB_closeWindowButton img').attr('src', frontEditorData.tb_close);

			self.bind($('#TB_iframeContent'), 'load', self.replace_button);
		},

		replace_button: function(ev) {
			var self = this;

			var $frame  = $(ev.target).contents();

			$('.describe', $frame).livequery(function(){
				var $item = $(this);
				var $button = $('<a href="#" class="button">').text(frontEditorData.caption);

				$button.click(function(ev){
					self.ajax_set(self.get_content($item));
				});

				$(this).find(':submit, #go_button').replaceWith($button);
			});
		},

		get_content: function($item) {
			var $field;

			// Media library
			$field = $item.find('.urlfile');
			if ( $field.length )
				return $field.attr('title');

			// From URL (embed)
			$field = $item.find('#embed-src');
			if ( $field.length )
				return $field.val();

			// From URL
			$field = $item.find('#src');
			if ( $field.length )
				return $field.val();

			return false;
		},

		ajax_set_handler: function(url) {
			var self = this;

			if ( url == -1 ) {
				window.location.reload(true);
			} else {
				self.el.find('img').attr('src', url);
				tb_remove();
			}
		}
	});

	classes['input'] = classes['base'].extend({
		init: function($el, type, name, id) {
			var self = this;

			self.spinner = spinner.clone();

			self._super($el, type, name, id);
		},

		input_tag: '<input type="text">',

		create_input: function() {
			var self = this;

			self.input = $(self.input_tag);

			self.input
				.attr('id', 'edit_' + self.el.attr('id'))
				.addClass('front-editor-content')
				.prependTo(self.form);
		},

		set_input: function(content) {
			var self = this;

			try {
				self.input.val(content);
			} catch(e) {
				// stupid IE
				alert('failed to set textarea content');

				var get_html_translation_table = function(table, quote_style) {
					// http://kevin.vanzonneveld.net
					// +   original by: Philip Peterson
					// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
					// +   bugfixed by: noname
					// +   bugfixed by: Alex
					// +   bugfixed by: Marco
					// +   bugfixed by: madipta
					// +   improved by: KELAN
					// +   improved by: Brett Zamir (http://brett-zamir.me)
					// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
					// +      input by: Frank Forte
					// +   bugfixed by: T.Wild
					// +      input by: Ratheous
					// %          note: It has been decided that we're not going to add global
					// %          note: dependencies to php.js, meaning the constants are not
					// %          note: real constants, but strings instead. Integers are also supported if someone
					// %          note: chooses to create the constants themselves.
					// *     example 1: get_html_translation_table('HTML_SPECIALCHARS');
					// *     returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}
		
					var entities = {}, hash_map = {}, decimal = 0, symbol = '';
					var constMappingTable = {}, constMappingQuoteStyle = {};
					var useTable = {}, useQuoteStyle = {};
		
					// Translate arguments
					constMappingTable[0]      = 'HTML_SPECIALCHARS';
					constMappingTable[1]      = 'HTML_ENTITIES';
					constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
					constMappingQuoteStyle[2] = 'ENT_COMPAT';
					constMappingQuoteStyle[3] = 'ENT_QUOTES';

					useTable       = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
					useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

					if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
						throw new Error("Table: "+useTable+' not supported');
						// return false;
					}

					entities['38'] = '&amp;';
					if (useTable === 'HTML_ENTITIES') {
						entities['160'] = '&nbsp;';
						entities['161'] = '&iexcl;';
						entities['162'] = '&cent;';
						entities['163'] = '&pound;';
						entities['164'] = '&curren;';
						entities['165'] = '&yen;';
						entities['166'] = '&brvbar;';
						entities['167'] = '&sect;';
						entities['168'] = '&uml;';
						entities['169'] = '&copy;';
						entities['170'] = '&ordf;';
						entities['171'] = '&laquo;';
						entities['172'] = '&not;';
						entities['173'] = '&shy;';
						entities['174'] = '&reg;';
						entities['175'] = '&macr;';
						entities['176'] = '&deg;';
						entities['177'] = '&plusmn;';
						entities['178'] = '&sup2;';
						entities['179'] = '&sup3;';
						entities['180'] = '&acute;';
						entities['181'] = '&micro;';
						entities['182'] = '&para;';
						entities['183'] = '&middot;';
						entities['184'] = '&cedil;';
						entities['185'] = '&sup1;';
						entities['186'] = '&ordm;';
						entities['187'] = '&raquo;';
						entities['188'] = '&frac14;';
						entities['189'] = '&frac12;';
						entities['190'] = '&frac34;';
						entities['191'] = '&iquest;';
						entities['192'] = '&Agrave;';
						entities['193'] = '&Aacute;';
						entities['194'] = '&Acirc;';
						entities['195'] = '&Atilde;';
						entities['196'] = '&Auml;';
						entities['197'] = '&Aring;';
						entities['198'] = '&AElig;';
						entities['199'] = '&Ccedil;';
						entities['200'] = '&Egrave;';
						entities['201'] = '&Eacute;';
						entities['202'] = '&Ecirc;';
						entities['203'] = '&Euml;';
						entities['204'] = '&Igrave;';
						entities['205'] = '&Iacute;';
						entities['206'] = '&Icirc;';
						entities['207'] = '&Iuml;';
						entities['208'] = '&ETH;';
						entities['209'] = '&Ntilde;';
						entities['210'] = '&Ograve;';
						entities['211'] = '&Oacute;';
						entities['212'] = '&Ocirc;';
						entities['213'] = '&Otilde;';
						entities['214'] = '&Ouml;';
						entities['215'] = '&times;';
						entities['216'] = '&Oslash;';
						entities['217'] = '&Ugrave;';
						entities['218'] = '&Uacute;';
						entities['219'] = '&Ucirc;';
						entities['220'] = '&Uuml;';
						entities['221'] = '&Yacute;';
						entities['222'] = '&THORN;';
						entities['223'] = '&szlig;';
						entities['224'] = '&agrave;';
						entities['225'] = '&aacute;';
						entities['226'] = '&acirc;';
						entities['227'] = '&atilde;';
						entities['228'] = '&auml;';
						entities['229'] = '&aring;';
						entities['230'] = '&aelig;';
						entities['231'] = '&ccedil;';
						entities['232'] = '&egrave;';
						entities['233'] = '&eacute;';
						entities['234'] = '&ecirc;';
						entities['235'] = '&euml;';
						entities['236'] = '&igrave;';
						entities['237'] = '&iacute;';
						entities['238'] = '&icirc;';
						entities['239'] = '&iuml;';
						entities['240'] = '&eth;';
						entities['241'] = '&ntilde;';
						entities['242'] = '&ograve;';
						entities['243'] = '&oacute;';
						entities['244'] = '&ocirc;';
						entities['245'] = '&otilde;';
						entities['246'] = '&ouml;';
						entities['247'] = '&divide;';
						entities['248'] = '&oslash;';
						entities['249'] = '&ugrave;';
						entities['250'] = '&uacute;';
						entities['251'] = '&ucirc;';
						entities['252'] = '&uuml;';
						entities['253'] = '&yacute;';
						entities['254'] = '&thorn;';
						entities['255'] = '&yuml;';
					}

					if (useQuoteStyle !== 'ENT_NOQUOTES') {
						entities['34'] = '&quot;';
					}
					if (useQuoteStyle === 'ENT_QUOTES') {
						entities['39'] = '&#39;';
					}
					entities['60'] = '&lt;';
					entities['62'] = '&gt;';


					// ascii decimals to real symbols
					for (decimal in entities) {
						symbol = String.fromCharCode(decimal);
						hash_map[symbol] = entities[decimal];
					}
		
					return hash_map;
				}

				var htmlentities = function(string, quote_style) {
					// http://kevin.vanzonneveld.net
					// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
					// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
					// +   improved by: nobbler
					// +    tweaked by: Jack
					// +   bugfixed by: Onno Marsman
					// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
					// +    bugfixed by: Brett Zamir (http://brett-zamir.me)
					// +      input by: Ratheous
					// -    depends on: get_html_translation_table
					// *     example 1: htmlentities('Kevin & van Zonneveld');
					// *     returns 1: 'Kevin &amp; van Zonneveld'
					// *     example 2: htmlentities("foo'bar","ENT_QUOTES");
					// *     returns 2: 'foo&#039;bar'

					var hash_map = {}, symbol = '', tmp_str = '', entity = '';
					tmp_str = string.toString();
		
					if (false === (hash_map = get_html_translation_table('HTML_ENTITIES', quote_style))) {
						return false;
					}
					hash_map["'"] = '&#039;';
					for (symbol in hash_map) {
						entity = hash_map[symbol];
						tmp_str = tmp_str.split(symbol).join(entity);
					}

					return tmp_str;
				}

				self.input.html(htmlentities(content));
			}
		},

		get_content: function() {
			var self = this;
			return self.input.val();
		},

		set_content: function(content) {
			var self = this;
			self.el.html(content);
		},

		ajax_get: function() {
			var self = this;

			self.el.hide().after(self.spinner.show());

			self.create_input();

			self._super();
		},

		ajax_set: function() {
			var self = this;

			self.el.before(self.spinner.show());

			self._super();
		},

		ajax_get_handler: function(content) {
			var self = this;

			self.spinner.hide().replaceWith(self.form);

			self.set_input(content);

			self.input.focus();
		},

		ajax_set_handler: function(content) {
			var self = this;

			self.set_content(content);

			self.spinner.hide();
			self.el.show();
		},

		dblclick: function(ev) {
			var self = this;

			self._super(ev);

			self.form_handler();
		},

		form_handler: function() {
			var self = this;

			// Button actions
			var form_remove = function(with_spinner) {
				frontEditorData._trap = false;

				self.form.remove();

				if ( with_spinner === true )
					self.el.before(self.spinner.show());
				else
					self.el.show();

				self.el.trigger('fee_remove_form');
			};

			var form_submit = function() {
				self.ajax_set();
				form_remove(true);
			};

			// Button markup
			self.save_button = $('<button>')
				.attr({'class': 'front-editor-save', 'title': frontEditorData.save_text})
				.text(frontEditorData.save_text)
				.click(form_submit);

			self.cancel_button = $('<button>')
				.attr({'class': 'front-editor-cancel', 'title': frontEditorData.cancel_text})
				.text('X')
				.click(form_remove);

			// Create form
			self.form = ( self.type == 'input' ) ? $('<span>') : $('<div>');

			self.form
				.addClass('front-editor-container')
				.append(self.save_button)
				.append(self.cancel_button);

			self.bind(self.form, 'keypress', self.keypress);
			
			self.ajax_get();
		},

		keypress: function(ev) {
			var self = this;

			var keys = {ENTER: 13, ESCAPE: 27};
			var code = (ev.keyCode || ev.which || ev.charCode || 0);

			if ( code == keys.ENTER && self.type == 'input' )
				self.save_button.click();

			if ( code == keys.ESCAPE )
				self.cancel_button.click();
		}
	});

	classes['terminput'] = classes['input'].extend({
		set_input: function(content) {
			var self = this;

			self._super(content);

			self.input.suggest(frontEditorData.ajax_url + '?action=ajax-tag-search&tax=' + self.id.split('#')[1], {
				multiple: true,
				resultsClass: 'fee_suggest_results',
				selectClass: 'fee_suggest_over',
				matchClass: 'fee_suggest_match'
			});
		}
	});

	classes['textarea'] = classes['input'].extend({
		input_tag: '<textarea>',

		set_input: function(content) {
			var self = this;
			
			self._super(content);

			if ( self.type == 'textarea' )
				self.input.growfield();
		}
	});

	classes['rich'] = classes['textarea'].extend({
		set_input: function(content) {
			var self = this;

			self._super(content);

			self.editor = new nicEditor(self.panel_options).panelInstance(self.input.attr('id'));

			self.form.find('.nicEdit-main').focus();
		},

		get_content: function() {
			var self = this;
			return self.pre_wpautop(self.input.val());
		},

		// Copied from wp-admin/js/editor.dev.js
		pre_wpautop: function(content) {
			var blocklist1, blocklist2;

			// Protect pre|script tags
			content = content.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
				a = a.replace(/<br ?\/?>[\r\n]*/g, '<wp_temp>');
				return a.replace(/<\/?p( [^>]*)?>[\r\n]*/g, '<wp_temp>');
			});

			// Pretty it up for the source editor
			blocklist1 = 'blockquote|ul|ol|li|table|thead|tbody|tfoot|tr|th|td|div|h[1-6]|p|fieldset';
			content = content.replace(new RegExp('\\s*</('+blocklist1+')>\\s*', 'g'), '</$1>\n');
			content = content.replace(new RegExp('\\s*<(('+blocklist1+')[^>]*)>', 'g'), '\n<$1>');

			// Mark </p> if it has any attributes.
			content = content.replace(/(<p [^>]+>.*?)<\/p>/g, '$1</p#>');

			// Sepatate <div> containing <p>
			content = content.replace(/<div([^>]*)>\s*<p>/gi, '<div$1>\n\n');

			// Remove <p> and <br />
			content = content.replace(/\s*<p>/gi, '');
			content = content.replace(/\s*<\/p>\s*/gi, '\n\n');
			content = content.replace(/\n[\s\u00a0]+\n/g, '\n\n');
			content = content.replace(/\s*<br ?\/?>\s*/gi, '\n');

			// Fix some block element newline issues
			content = content.replace(/\s*<div/g, '\n<div');
			content = content.replace(/<\/div>\s*/g, '</div>\n');
			content = content.replace(/\s*\[caption([^\[]+)\[\/caption\]\s*/gi, '\n\n[caption$1[/caption]\n\n');
			content = content.replace(/caption\]\n\n+\[caption/g, 'caption]\n\n[caption');

			blocklist2 = 'blockquote|ul|ol|li|table|thead|tbody|tfoot|tr|th|td|h[1-6]|pre|fieldset';
			content = content.replace(new RegExp('\\s*<(('+blocklist2+') ?[^>]*)\\s*>', 'g'), '\n<$1>');
			content = content.replace(new RegExp('\\s*</('+blocklist2+')>\\s*', 'g'), '</$1>\n');
			content = content.replace(/<li([^>]*)>/g, '\t<li$1>');

			if ( content.indexOf('<object') != -1 ) {
				content = content.replace(/<object[\s\S]+?<\/object>/g, function(a){
					return a.replace(/[\r\n]+/g, '');
				});
			}

			// Unmark special paragraph closing tags
			content = content.replace(/<\/p#>/g, '</p>\n');
			content = content.replace(/\s*(<p [^>]+>[\s\S]*?<\/p>)/g, '\n$1');

			// Trim whitespace
			content = content.replace(/^\s+/, '');
			content = content.replace(/[\s\u00a0]+$/, '');

			// put back the line breaks in pre|script
			content = content.replace(/<wp_temp>/g, '\n');

			return content;
		},

		panel_options: {
			iconsPath: frontEditorData.nicedit_icons,
			buttonList: [
				'bold', 'italic', 'strikethrough',
				'left','center', 'right',
				'ul', 'ol',
				'fontFormat',
				'link', 'unlink', 'image',
				'xhtml'
			]
		},

		ajax_set: function() {
			var self = this;

			self.editor.nicInstances[0].saveContent();

			self._super();
		}
	});

	// Widget fields hack: Add id attr to each element
	$('.front-ed-widget_title, .front-ed-widget_text').each(function() {
		var $el = $(this);
		var id = $el.parents('.widget').attr('id');

		if ( id )
			$el.attr('id', 'fee_' + id);
		else
			// undo wrap; can't find widget id
			$el.replaceWith($el.html());
	});

	// Create field instances
	$.each(frontEditorData.fields, function(name, type) {
		$('.front-ed-' + name).each(function() {
			var $el = $(this);

			var id = $el.attr('id').substr(4);

			var parts = id.split('#');

			if ( parts.length == 4 )	// hack for frontEd_meta
				type = parts[2];

			new classes[type]($el, type, name, id);
		});
	});
});
