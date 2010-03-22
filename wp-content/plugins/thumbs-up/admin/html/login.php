<?php defined('THUMBSUP_DOCROOT') or exit('No direct script access.') ?>

<form id="login" method="post" action="<?php echo THUMBSUP_WEBROOT.'thumbsup/admin/index.php' ?>">

	<p class="center">
		<label for="username">Username</label>
		<input id="username" name="username" type="text" size="25" value="<?php if (isset($username)) echo htmlspecialchars($username) ?>" maxlength="100" title="Username" />
		+
		<label for="password">Password</label>
		<input id="password" name="password" type="password" size="25" value="" maxlength="100" title="Password" />
		=
		<input type="submit" value="Login" />
	</p>

</form>