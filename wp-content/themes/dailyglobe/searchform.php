<style type="text/css">
#hidden{
	width:40px;
	height:40px;
	border:none;
	margin-left:-35px;
	background:none;
	cursor:pointer;
}

</style>


<form id="searchform" method="get" action="<?php bloginfo('home'); ?>/"> 
	<input type="text" value="search" 
		onfocus="if (this.value == 'Search') {this.value = '';}" 
		onblur="if (this.value == '') {this.value = 'Search';}" 
		size="18" maxlength="50" name="s" id="s" /> 
	<input type="submit" value="   " id="hidden"/>
</form> 
