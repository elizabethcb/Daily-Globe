
	<script type="text/javascript">document.documentElement.className = 'js';</script>

	<div id="tu-header">
		<h1 class="tu">ThumbsUp Admin</h1>

	</div>

	<div id="tu-content">
		<?php if ( ! empty($error)) { ?>
			<p class="tu-alert"><strong>
				<?php echo htmlspecialchars($error) ?>
				<a class="tu-cancel" href="#">cancel</a>
			</strong></p>
		<?php } ?>

		<?php echo $content ?>
	</div>

	<div id="tu-footer">
		<p id="tu-copyright">
			<a href="http://themeforest.net/?ref=GeertDD">ThumbsUp</a> • Copyright ©2009<br />
			By <a href="http://themeforest.net/user/GeertDD?ref=GeertDD">Geert De Deckere</a>
		</p>

	</div>
