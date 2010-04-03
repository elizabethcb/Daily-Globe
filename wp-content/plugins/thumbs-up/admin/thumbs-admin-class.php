<?php
/**
 * ThumbsUp Admin
 *
 * @author     Geert De Deckere <http://www.geertdedeckere.be/>
 * @copyright  (c) 2009 Geert De Deckere
SELECT SUM( total_votes ) AS tvotes, SUM( positive_votes ) AS pvotes
FROM (

SELECT pm.post_id AS pid, COUNT( tu.id ) AS total_votes, SUM( tu.rating ) AS positive_votes
FROM wp_7_tu_votes tu
NATURAL JOIN wp_7_postmeta pm
WHERE pm.meta_key = 'syndication_feed_id'
AND pm.meta_value =4
GROUP BY pm.post_id
) AS totot

SELECT p.ID AS pid, p.post_author, COUNT( tu.id ) AS total_votes, SUM( tu.rating ) AS positive_votes
FROM wp_7_posts p
JOIN wp_7_tu_votes tu ON p.ID = tu.post_id
WHERE p.post_author =8
GROUP BY tu.post_id
 */



class ThumbsUp_Admin {

	/**
	 * @var  array  configuration settings
	 */
	protected $config;

	/**
	 * @var  object  ThumbsUp_Template for layout wrapper
	 */
	public $template;

	/**
	 * @var  boolean  AJAX request or not?
	 */
	protected $is_ajax;

	/**
	 * @var  boolean  logged in or not?
	 */
	protected $is_logged_in;

	protected $tblname = 'wp_tu_votes';
	/**
	 * Constructor. Starts session. Loads database and template classes.
	 * Sets up the layout template wrapper. Routes the request to the correct
	 * action_xxx method, while making sure the user is logged in.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// Load the settings from the config.php file
		$this->config = include THUMBSUP_DOCROOT. 'config.php';
		//$this->error = false;
		// Load other components
//		require THUMBSUP_DOCROOT.'core/thumbsup_database.php';
		require_once THUMBSUP_DOCROOT.'core/thumbsup_template.php';

		// Determine whether this is an AJAX request or not
		$this->is_ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

		// Grab the action from the URL, and prefix it with "action_"
		$action = 'action_'.((empty($_GET['action'])) ? 'dashboard' : $_GET['action']);

		// TODO Restructure this
		// The dashboard should be retrieved if $_GET['page'] exists
		if ('admin/thumbs-admin.php' == $_GET['page']) { 
				
			// Initialize the template
			$this->template = new ThumbsUp_Template(THUMBSUP_DOCROOT.'admin/html/layout.php');
			$this->template->pagetitle = 'ThumbsUp Admin';
			$this->template->content = '';
			$pattern = '/thumbsup-admin.php/';
			// Look for a corresponding action_method
			if (in_array($action, get_class_methods($this))) {
				return $this->$action();
			}

		} elseif ($this->is_ajax AND $action != 'action_dashboard') {
			//echo $action;

			if(in_array($action, get_class_methods($this))) {
				return $this->$action();
			}
		} else {
			// Show an error for invalid actions
			//return print_r($_SERVER['REQUEST_URI']);
			//$this->error = "No valid action found";
			return $this;
		}
	}
	



	/**
	 * Shows the admin dashboard: an overview of all ThumbsUp items.
	 *
	 * @return  void
	 */
	public function action_dashboard()
	{
		global $wpdb;
		// Setup content template
		$content = new ThumbsUp_Template(THUMBSUP_DOCROOT.'admin/html/dashboard.php');
		
		// Filter by name
		$filter = (isset($_GET['filter'])) ? trim($_GET['filter']) : '';
		$filter_sql = ($filter === '') ? '' : 
			$wpdb->prepare("WHERE i.post_title LIKE '%%%s%%'", $filter);

		// Pagination
		$per_page = ( ! empty($this->config['admin_items_per_page'])) ? max(1, (int) $this->config['admin_items_per_page']) : 100;
		$counting = "SELECT COUNT(DISTINCT post_id) FROM " . $this->tblname;
		$total_items = ($filter === '') ?
			$wpdb->get_var($wpdb->prepare($counting)) :
			$wpdb->get_var($wpdb->prepare($counting. $filter_sql));
		$total_pages = (int) ceil($total_items / $per_page);
		// TODO change page to paginate or p
		$page = (isset($_GET['page']) && ctype_digit($_GET['page'])) ? min($total_pages, max(1, (int) $_GET['page'])) : 1;
		$limit_sql = 'LIMIT '.$per_page.' OFFSET '.(($page - 1) * $per_page);


		// List of all items
		$content->items = $wpdb->get_results("
			SELECT
				i.id AS id,
				i.post_title AS title,
				i.post_date AS date,
				COUNT(v.id) AS total_votes,
				SUM(v.rating) AS positive_votes
			FROM " . $this->tblname. " v
			JOIN ". $wpdb->prefix . "posts i ON i.id = v.post_id
			$filter_sql
			GROUP BY i.id
			ORDER BY i.post_title ASC
			$limit_sql");

		// TODO move this to sql, because I really really hate doing this.
		// At least there's a maximum.
		foreach ($content->items as $con) {
			$con->closed = get_post_meta($con->id, 'vote_closed', 'true');
		}
		//echo '<pre>'; print_r($content->items); echo '</pre>';
		// Item counts
		$content->total_items_shown = count($content->items);

		// Render the template
		$content->filter = $filter;
		$content->total_items = $total_items;
		$content->total_pages = $total_pages;
		$content->page = $page;
		$this->template->content = $content;

		return $this;
	}

	/**
	 * Closes or opens an item.
	 *
	 * @return  void
	 */
	public function action_toggle_closed() {
		// We need an item id
		if (empty($_POST['item_id'])) {
			$error = 'No post ID given.';
		} else {
			// Clean post id
			$post_id = (int) $_POST['item_id'];

			// Toggle the closed value in the database
			//add_post_meta( $post_id, 'vote_closed', 0, true) or
			update_post_meta( $post_id, 'vote_closed', 0, true);

			// TODO Error checking
			// Nothing changed, this means the item id is invalid
			//if (ThumbsUp_Database::db()->changes() !== 1) {
			//	$error = 'Invalid item ID.';
			//}
		}

		if ($this->is_ajax) {
			// Return an error, or null if everything went fine
			exit(json_encode((isset($error)) ? $error : NULL));
		}

		// If this is not an AJAX request, print the error
		if ( ! empty($error)) {
			$this->template->error = $error;
		}

		// Show the item overview again
		$this->action_dashboard();
	}

	
	/**
	 * Deletes all votes for an item.
	 * TODO This removes the item from the list.  Essencially the same as delete.
	 * @return  void
	 */
	public function action_reset_votes() {
		// We need an item id
		if (empty($_POST['post_id'])) {
			$error = 'No post ID posted.';
		} else {
			// Clean item id
			$post_id = (int) $_POST['post_id'];

			// Reset the date
			//ThumbsUp_Database::db()->queryExec("
			//	UPDATE items
			//	SET date = ".time()."
			//	WHERE id = $post_id");

			global $wpdb;
			
			$deleted = $wpdb->query($wpdb->prepare("DELETE FROM " . $this->tblname ."
				WHERE post_id = %d", $post_id));
			
			// Nothing changed, this means the item id is invalid
			if (0 == $deleted) {
				$error = 'Invalid post ID.';
			} elseif (!$deleted) {
				$error = 'Some MySQL error occured';
				// Delete all votes for the item
				//ThumbsUp_Database::db()->queryExec("
					//DELETE FROM votes
			//		WHERE post_id = $post_id");
			}
		}

		if ($this->is_ajax)
		{
			// Return an error, or null if everything went fine
			exit(json_encode((isset($error)) ? $error : NULL));
		}

		// If this is not an AJAX request, print the error
		if ( ! empty($error))
		{
			$this->template->error = $error;
		}

		// Show the item overview again
		$this->action_dashboard();
	}
	/**
	 * Completely deletes an item, with all votes.
	 * TODO wordpress handles.
	 * @return  void
	 */
	public function action_delete()
	{
		// Here to track down any references.
		wp_die("You don't need me anymore.  Deleting items is Wordpress's job");
		// We need an item id
		if (empty($_POST['post_id']))
		{
			$error = 'No item ID posted.';
		}
		else
		{
			// Clean item id
			$post_id = (int) $_POST['post_id'];

			// Delete both the item and the votes for it
			ThumbsUp_Database::db()->queryExec("
				DELETE FROM items
				WHERE id = $post_id;
				DELETE FROM votes
				WHERE post_id = $post_id");
		}

		if ($this->is_ajax)
		{
			// Return an error, or null if everything went fine
			exit(json_encode((isset($error)) ? $error : NULL));
		}

		// If this is not an AJAX request, print the error
		if ( ! empty($error))
		{
			$this->template->error = $error;
		}

		// Show the item overview again
		$this->action_dashboard();
	}
		/**
	 * Sets admin data.
	 *
	 * @param   string  key
	 * @param   mixed   value
	 * @return  void
	 */
	public function __set($key, $value)
	{
		$this->set($key, $value);
	}

	/**
	 * Unsets admin data (as of PHP 5.1.0).
	 *
	 * @param   string  key
	 * @return  void
	 */
	public function __unset($key)
	{
		unset($this->data[$key]);
	}

	/**
	 * Checks whether certain admin data is set (as of PHP 5.1.0).
	 *
	 * @param   string   template data key
	 * @return  boolean
	 */
	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

	/**
	 * Gets admin data.
	 *
	 * @param   string  template data key
	 * @return  mixed   template data; NULL if not found
	 */
	public function __get($key)
	{
		return (isset($this->data[$key])) ? $this->data[$key] : NULL;
	}
	


}
