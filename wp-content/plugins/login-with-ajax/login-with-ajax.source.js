jQuery(document).ready( function($) {
 	//Oh well... I guess we have to use jQuery ... if you are a javascript developer, consider MooTools if you have a choice, it's great!
	//I'm biased, but form functionality already comes prepacked with MooTools :) 	
 	$('#LoginWithAjax_Form').submit(function(event){
		//Stop event, add loading pic...
		event.preventDefault();
		if( $('#LoginWithAjax').length > 0 ){
			$('<div class="LoginWithAjax_Loading" id="LoginWithAjax_Loading"></div>').prependTo('#LoginWithAjax');
		}else{
			$('<div class="LoginWithAjax_Loading" id="LoginWithAjax_Loading"></div>').prependTo('#login-with-ajax');
		}
		//Sort out url
		url = $('#LoginWithAjax_Form').attr('action');
		url += (url.match(/\?/) != null) ? '&callback=?' : '?callback=?' ;
		url += "&log="+encodeURIComponent($("#lwa_user_login").attr('value'));
		url += "&pwd="+encodeURIComponent($("#lwa_user_pass").attr('value'));
		url += "&login-with-ajax=login";
		$.getJSON( url , function(data, status){
			$('#LoginWithAjax_Loading').remove();
			if( data.result === true || data.result === false ){
				if(data.result === true){
					//Login Successful
					if( $('#LoginWithAjax_Status').length > 0 ){
						$('#LoginWithAjax_Status').attr('class','confirm').html(data.message);
					}else{
						$('<span id="LoginWithAjax_Status" class="confirm">'+data.message+'</span>').prependTo('#login-with-ajax');
					}
					if(data.redirect == null){
						window.location.reload();
					}else{
						window.location = data.redirect;
					}
				}else{
					//Login Failed
					//If there already is an error element, replace text contents, otherwise create a new one and insert it
					if( $('#LoginWithAjax_Status').length > 0 ){
						$('#LoginWithAjax_Status').attr('class','invalid').html(data.error);
					}else{
						$('<span id="LoginWithAjax_Status" class="invalid">'+data.error+'</span>').prependTo('#login-with-ajax');
					}
					//We assume a link in the status message is for a forgotten password
					$('#LoginWithAjax_Status').click(function(event){
						event.preventDefault();
						$('#LoginWithAjax_Remember').show('slow');
					});
				}
			}else{	
				//If there already is an error element, replace text contents, otherwise create a new one and insert it
				if( $('#LoginWithAjax_Status').length > 0 ){
					$('#LoginWithAjax_Status').attr('class','invalid').html('An error has occured. Please try again.'+status);
				}else{
					$('<span id="LoginWithAjax_Status" class="invalid">An error has occured. Please try again.</span>').prependTo('#login-with-ajax');
				}
			}
		});
	});	
	
 	$('#LoginWithAjax_Remember').submit(function(event){
		//Stop event, add loading pic...
		event.preventDefault();
		$('<div id="LoginWithAjax_Loading"></div>').prependTo('#LoginWithAjax');
		//Sort out url
		url = $('#LoginWithAjax_Remember').attr('action');
		url += (url.match(/\?/) != null) ? '&callback=?' : '?callback=?' ;
		url += "&user_login="+$("#lwa_user_remember").attr('value');
		url += "&login-with-ajax=remember";
		$.getJSON( url , function(data, status){
			$('#LoginWithAjax_Loading').remove();
			if( data.result === true || data.result === false ){
				if(data.result == '1'){
					//Successful
					if( $('#LoginWithAjax_Status').length > 0 ){
						$('#LoginWithAjax_Status').attr('class','confirm').html(data.message);
					}else{
						$('<span id="LoginWithAjax_Status" class="confirm">'+data.message+'</span>').prependTo('#login-with-ajax');
					}
				}else{
					//Failed
					//If there already is an error element, replace text contents, otherwise create a new one and insert it
					if( $('#LoginWithAjax_Status').length > 0 ){
						$('#LoginWithAjax_Status').attr('class','invalid').html(data.error);
					}else{
						$('<span id="LoginWithAjax_Status" class="invalid">'+data.error+'</span>').prependTo('#login-with-ajax');
					}
				}
			}else{	
				//If there already is an error element, replace text contents, otherwise create a new one and insert it
				if( $('#LoginWithAjax_Status').length > 0 ){
					$('#LoginWithAjax_Status').attr('class','invalid').html('An error has occured. Please try again.'+status);
				}else{
					$('<span id="LoginWithAjax_Status" class="invalid">An error has occured. Please try again.</span>').prependTo('#login-with-ajax');
				}
			}
		});
	});		
	$('#LoginWithAjax_Remember').hide();
	$('#LoginWithAjax_Links_Remember').click(function(event){
		event.preventDefault();
		$('#LoginWithAjax_Remember').show('slow');
	});
	$('#LoginWithAjax_Links_Remember_Cancel').click(function(event){
		event.preventDefault();
		$('#LoginWithAjax_Remember').hide('slow');
	});
});