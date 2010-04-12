$(document).ready(function() {
	
	$('#subnav ul li ul').wrap('<div class="submenu closed" style="display: none;"></div>');
	$('#subnav ul li .submenu').prepend('<div class="carrottop"></div>');
	$('.submenu').parent().append('<div class="down_arrow"></div>');
	
	$('.submenu').parent().hover(
		function() { $('.submenu', this).stop(false,true).fadeIn(500); },
		function() { $('.submenu', this).stop(false,true).fadeOut(500); 
	});
	
	$('#search-events').click(function() {
		$('#events-search-page').toggle();
		$('#events-page').toggle();
		return false;
	});
	
	$('#search-events').hover(
		function() {
			$(this).addClass('event-nav-link-hover');
		},
		function() {
			$(this).removeClass('event-nav-link-hover');
		}
	);
	
	$('div#twitter-form input#search').keypress(function(event) {
 		 if (event.keyCode == '13') {
 		 var searchVal = $(this).serialize();
					  
					  
		 $("#searchresults").fadeOut().load("<?php bloginfo('stylesheet_directory'); ?>/json/twitter-search.php?" + searchVal).fadeIn();
		}
	});
	
	$('.category_container .post_list li:last-child, .home_post_wrapper:last-child').css("border", "none");

});

	
	   // Create a cookie
	function createCookie(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires;
	}
	// Read the cookie
	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}
	
	// Erase the cookie
	function eraseCookie(name) {
		createCookie(name,"",-1);
	}
