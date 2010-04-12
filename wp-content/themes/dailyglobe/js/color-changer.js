
$(document).ready(function() {

	// If the user has chosen a header color, remember that color in a cookie
	if(readCookie('header_color')){
		$('#header').css({background:readCookie('header_color')});
	}

   
   // When you click on an li in #color-changer, change //
   // the background of #header to #color-changer li's  //
   // background color.                                 //
   
   $('#fancy_div #color-changer li').live("click", function () {
   	  color = $(this).css("background-color");
      $('#header, #fdbk_tab').css("background", color ); 
      createCookie('header_color',color,365);
      $.fn.fancybox.close()
    });

 });
