<?php defined('THUMBSUP_DOCROOT') or exit('No direct script access.') ?>

<?php if ($total_items === 0 && $filter === '') { ?>
	<h2>You didn't create any ThumbsUp items yet.</h2>
	<p>As soon as you created some, come back here to manage them.</p>
<?php } else { ?>

	<table summary="ThumbsUp item list" class="thumbsup">
		<col width="20" />
		<col width="" />
		<col width="120" />
		<col width="140" />
		<thead>
			<tr class="column-labels">
				<th>1</th>
				<th>Name</th>
				<th>Votes</th>
				<th>Since</th>
				<th>1</th>
			</tr>
			<tr class="tu-filter">
				<td></td>
				<td colspan="2">
					<form id="filter" method="get" action="/wp-admin/admin.php?page=admin/thumbs-admin.php">
						<input type="hidden" name="page" value="admin/thumbs-admin.php" />
						<input class="input" name="filter" type="text" value="<?php echo htmlspecialchars($filter) ?>" />
						<input type="submit" value="filter" title="Filter on item name" />
					</form>
				</td>
				<td class="count tu-right" colspan="2">
					<?php if ($total_items_shown === $total_items) { ?>
						Showing all <strong id="total_items"><?php echo $total_items ?></strong> items
					<?php } else { ?>
						Showing <strong>
							<span id="total_items_shown"><?php echo $total_items_shown ?></span> of
							<span id="total_items"> <?php echo $total_items ?></span>
						</strong> items
						<?php if ($filter !== '') { ?>
							• <a href="/wp-admin/admin.php?page=admin/thumbs-admin.php">Show all</a>
						<?php } ?>
					<?php } ?>
				</td>
			</tr>
		</thead>

		<tfoot>
			<tr class="pages">
				<td colspan="5">
					Pages:
					<?php if ($total_pages == 0) echo '0' ?>
					<?php for ($i = 1; $i <= $total_pages; ++$i) { ?>
						<?php if ($i == $page) { echo '<strong>['.$page.']</strong>'; continue; } ?>
						<a href="<?php echo THUMBSUP_WEBROOT ?>admin/?page=<?php echo $i ?>&amp;filter=<?php echo htmlspecialchars($filter) ?>"><?php echo $i ?></a>
					<?php } ?>
				</td>
			</tr>
		</tfoot>

		<tbody>
		<?php if ($total_items == 0) { ?>
			<tr>
				<td></td>
				<td colspan="4"><p>No items found that match “<?php echo htmlspecialchars($filter) ?>”.</p></td>
			</tr>
		<?php } else { ?>
			<?php foreach ($items as $item) { ?>

			<tr id="item_<?php echo $item->id ?>">

				<td class="tu-center">
					<form method="post" action="<?php echo THUMBSUP_WEBROOT ?>admin/admin-ajax-response.php?action=toggle_closed">
						<input name="post_id" type="hidden" value="<?php echo $item->id ?>" />
						<input class="lock <?php echo ('0' == $item->closed) ? 'closed' : '' ?>" type="submit" title="Close/open voting" />
					</form>
				</td>

				<td>
					<?php echo htmlspecialchars(trim($item->title)) ?>
				</td>

				<td class="tu-right">
					<form method="post" action="<?php echo THUMBSUP_WEBROOT ?>admin/admin-ajax-response.php?action=reset_votes">
						<span class="votes" title="Positive votes/Total votes"><?php echo $item->positive_votes.'/'.$item->total_votes ?></span>
						<input name="post_id" type="hidden" value="<?php echo $item->id ?>" />
						<input type="submit" value="reset" title="Are you sure you want to delete all votes for “<?php echo htmlspecialchars($item->title) ?>”?" />
					</form>
				</td>

				<td class="date tu-right" title="Voting start date">
					<?php echo date('j M Y \a\t  H:i', strtotime($item->date)) ?>
				</td>

				<td class="delete tu-tu-center">
					<form method="post" action="?action=delete">
						<input name="post_id" type="hidden" value="<?php echo $item->id ?>" />
						<input class="delete" type="submit" value="delete" title="Are you sure you want to completely delete “<?php echo htmlspecialchars($item->title) ?>”?" />
					</form>
				</td>

			</tr>
			<?php } ?>
		<?php } ?>
		</tbody>
	</table>

<?php } ?>

<h2>So, how do I create a new ThumbsUp item? • <a id="show-help" href="#">Show</a></h2>
<div id="help">
	<p>
		<strong>Step 1</strong> •
		At the top of your webpage (before any output) add the following line of PHP.
		Note: this is a relative path, you may need to change it to reflect the directory structure of your webpage.<br />
		<code>&lt;?php include './thumbsup/core/thumbsup.php' ?&gt;</code><br />
	</p>
	<p>
		<strong>Step 2</strong> •
		Load <a href="http://jquery.com/">jQuery</a> (&gt;=1.3) and the ThumbsUp jQuery file in the &lt;head&gt; of your HTML.<br />
		<code>
			&lt;script type="text/javascript" src="http://yoursite.com/path/to/jquery-1.3.2.min.js"&gt;&lt;/script&gt;<br />
			&lt;script type="text/javascript" src="&lt;?php echo THUMBSUP_WEBROOT ?&gt;thumbsup/core/thumbsup.js.php"&gt;&lt;/script&gt;
		</code>
	</p>
	<p>
		<strong>Step 3</strong> •
		Create a ThumbsUp item anywhere you want on your webpage. Give it a name and pick a template for it.
		Voilà, as soon as that page is loaded the item will be created and show up in the admin.<br />
		<code>&lt;?php $thumbsup->setup('unique-item-name', 'template-to-use')->render() ?&gt;</code>
	</p>

	<h2>A note about renaming and deleting</h2>
	<p>
		Whenever you rename or delete a ThumbsUp item here in the admin, be sure to update your webpages (step&nbsp;3) accordingly.
		Items will be automatically (re)created whenever <code>$thumbsup->setup()</code> is called in a webpage.
	</p>
</div>