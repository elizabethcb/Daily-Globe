
$(document).ready(function() {
   // do stuff when DOM is ready
   //hide the button.
   $('#chartbtn-off').toggle();
   $('#chartbtn-off').css("color","white");


	// If the user has chosen a header color, remember that color in a cookie
	if(readCookie('header_color')){
		$('#header').css({background:readCookie('header_color')});
	}

   
   // When you click on an li in #color-changer, change //
   // the background of #header to #color-changer li's  //
   // background color.                                 //
   
   $('#color-changer li').click(function () {
   	  color = $(this).css("background-color");
      $('#header, #fdbk_tab').css("background", color ); 
      createCookie('header_color',color,365);
    });
    
    // When button is clicked the chart is maximized.  When clicked again it's minimized
    // On button is hidden.  Off button is visible.

    $('#chartbtn-on').click(function () {
    	$('#chartbtn-on').css('display','none');
    	$('#chartbtn-off').css('display','block');
    	//if(toggleright == 1) {
    	$('#customize').animate({ width: "360px" }, 200 );
    	//	toggleright = 0;
    	//} else {
    	//	$('#colorchart').css("right", "-163px");
    	//	toggleright = 1;
    	//}
    });
    
    $('#chartbtn-off').click(function () {
		$('#chartbtn-on').css('display','block');
    	$('#chartbtn-off').css('display','none');;
    	$('#customize').animate({ width: "180px" }, 200 );
    });

 });
