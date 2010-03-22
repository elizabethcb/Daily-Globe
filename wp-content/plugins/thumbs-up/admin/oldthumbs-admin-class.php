<?php
/**
 * ThumbsUp Admin
 *
 * @author     Geert De Deckere <http://www.geertdedeckere.be/>
 * @copyright  (c) 2009 Geert De Deckere
 */



class ThumbsUp_Admin {

	/**
	 * @var  array  configuration settings
	 */
	protected $config;

	/**
	 * @var  object  ThumbsUp_Template for layout wrapper
	 */
	protected $template;

	/**
	 * @var  boolean  AJAX request or not?
	 */
	protected $is_ajax;

	/**
	 * @var  boolean  logged in or not?
	 */
	protected $is_logged_in;

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

		// Load other components
//		require THUMBSUP_DOCROOT.'core/thumbsup_database.php';
		require THUMBSUP_DOCROOT.'core/thumbsup_template.php';

		// Determine whether this is an AJAX request or not
		$this->is_ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

		// Determine whether the user is logged in
//		$this->is_logged_in = self::is_logged_in();


		// Initialize the template
		$this->template = new ThumbsUp_Template(THUMBSUP_DOCROOT.'admin/html/layout.php');
		$this->template->pagetitle = 'ThumbsUp Admin';
		$this->template->content = '';
		$pattern = '/thumbsup-admin.php/';
		if ('admin/thumbs-admin.php' == $_GET['page']) { // || preg_match($pattern, $_SERVER['REQUEST_URI']) ) {
			// Grab the action from the URL, and prefix it with "action_"
			$action = 'action_'.((empty($_GET['action'])) ? 'dashboard' : $_GET['action']);

			// Look for a corresponding action_method
			if (in_array($action, get_class_methods($this))) {
				return $this->$action();
			}

		}
		// Show an error for invalid actions
		return('<pre>Action: '. print_r($action).'<br />'.print_r(get_class_methods($this)).'</pre>');
		//return print_r($_SERVER['REQUEST_URI']);
		//return $this;
	}
	
	/**
	 * The function run using the admin_head hook
	 */
	public function admin_head() {
		wp_enqueue_script('jquery-admin', THUMBSUP_PLUGIN_URL.'javascript/jquery-admin.js');
		wp_enqueue_style('tu-admin-style', THUMBSUP_PLUGIN_URL.'css/admin.css');
	}
	
	/**
	 * The function run using the admin_menu hook
	 */
	 public function admin_menu() {
	 	$admin_page = 'admin/thumbs-admin.php';
		$tab_title = 'Thumbs Up';
		$func = 'admin_loader';
		$access_level = 'manage_options';

		$sub_pages = array(
			'Neat Stuff'=>'neat_stuff'
		);
	
		add_menu_page($tab_title, $tab_title, $access_level, $admin_page, $func);
	
		foreach ($sub_pages as $title=>$page) {
			add_submenu_page($admin_page, $title, $title, $access_level, $page, $func);
		}
	 }
	 
	function admin_loader() {
		$page = trim($_GET['page']);
	
		if ('admin/thumbs-admin.php' == $page) {
			require_once(THUMBSUP_DOCROOT . 'admin/thumbs-admin-class.php');
		} else if (file_exists(THUMBSUP_DOCROOT . 'admin/' . $page . '.php')) {
			require_once(THUMBSUP_DOCROOT . 'admin/' . $page . '.php');
		}
	}


	/**
	 * Shows the admin dashboard: an overview of all ThumbsUp items.
	 *
	 * @return  void
	 */
	public function action_dashboard()
	{

		// Setup content template
		$content = new ThumbsUp_Template(THUMBSUP_DOCROOT.'admin/html/dashboard.php');
		
		// Filter by name
		$filter = (isset($_GET['filter'])) ? trim($_GET['filter']) : '';
		$filter_sql = '';//($filter === '') ? '' : "WHERE LOWER(i.name) LIKE '%".sqlite_escape_string((string) $_GET['filter'])."%'";

		// Pagination
		$per_page = ( ! empty($this->config['admin_items_per_page'])) ? max(1, (int) $this->config['admin_items_per_page']) : 100;
		$total_items = 20; //($filter === '') ? ThumbsUp_Database::get_total_items() : (int) ThumbsUp_Database::db()->singleQuery("SELECT COUNT(1) FROM items i $filter_sql");
		$total_pages = (int) ceil($total_items / $per_page);
		// TODO change page to paginate or p
		$page = (isset($_GET['page']) && ctype_digit($_GET['page'])) ? min($total_pages, max(1, (int) $_GET['page'])) : 1;
		$limit_sql = 'LIMIT '.$per_page.' OFFSET '.(($page - 1) * $per_page);

		global $wpdb;
		// List of all items
		$content->items = $wpdb->get_results("
			SELECT
				i.id AS id,
				i.name AS name,
				i.date AS date,
				COUNT(v.id) AS total_votes,
				SUM(v.rating) AS positive_votes
			FROM ". $wpdb->prefix . "posts i
			LEFT JOIN " . $wpdb->prefix . "tu_votes v ON i.id = v.post_id
			$filter_sql
			GROUP BY i.id
			ORDER BY LOWER(i.name) ASC
			$limit_sql");

		// Item counts
		$content->total_items_shown = count($content->items);

		// Render the template
		$content->filter = $filter;
		$content->total_items = $total_items;
		$content->total_pages = $total_pages;
		$content->page = $page;
		$this->template->content = $content;
		return $this->template->render(TRUE);
		// Setup content template
	}

	/**
	 * Closes or opens an item.
	 *
	 * @return  void
	 */
	public function action_toggle_closed()
	{
		// We need an item id
		if (empty($_POST['item_id']))
		{
			$error = 'No item ID posted.';
		}
		else
		{
			// Clean item id
			$item_id = (int) $_POST['item_id'];

			// Toggle the closed value in the database
			ThumbsUp_Database::db()->queryExec("
				UPDATE items
				SET closed = ABS(closed - 1)
				WHERE id = $item_id");

			// Nothing changed, this means the item id is invalid
			if (ThumbsUp_Database::db()->changes() !== 1)
			{
				$error = 'Invalid item ID.';
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
	 * Renames an item.
	 *
	 * @return  void
	 */
	public function action_rename()
	{
		// We need an item id, and a new name
		if (empty($_POST['item_id']) OR ! isset($_POST['item_name']))
		{
			$error = 'No item ID or new name posted.';
		}
		else
		{
			// Clean item id and name
			$item_id = (int) $_POST['item_id'];
			$name = (string) $_POST['item_name'];

			// Look up new name
			if (ThumbsUp_Database::item_name_exists($name))
			{
				$error = 'This new name is already in use. It needs to be unique.';
			}
			else
			{
				// Update new name
				ThumbsUp_Database::db()->queryExec("
					UPDATE items
					SET name = '".sqlite_escape_string($name)."'
					WHERE id = $item_id");

				// Count changes to verify id
				if (ThumbsUp_Database::db()->changes() !== 1)
				{
					$error = 'Non-existing item ID.';
				}
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
	 * Deletes all votes for an item.
	 *
	 * @return  void
	 */
	public function action_reset_votes()
	{
		// We need an item id
		if (empty($_POST['item_id']))
		{
			$error = 'No item ID posted.';
		}
		else
		{
			// Clean item id
			$item_id = (int) $_POST['item_id'];

			// Reset the date
			ThumbsUp_Database::db()->queryExec("
				UPDATE items
				SET date = ".time()."
				WHERE id = $item_id");

			// Nothing changed, this means the item id is invalid
			if (ThumbsUp_Database::db()->changes() !== 1)
			{
				$error = 'Invalid item ID.';
			}
			else
			{
				// Delete all votes for the item
				ThumbsUp_Database::db()->queryExec("
					DELETE FROM votes
					WHERE item_id = $item_id");
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
	 *
	 * @return  void
	 */
	public function action_delete()
	{
		// We need an item id
		if (empty($_POST['item_id']))
		{
			$error = 'No item ID posted.';
		}
		else
		{
			// Clean item id
			$item_id = (int) $_POST['item_id'];

			// Delete both the item and the votes for it
			ThumbsUp_Database::db()->queryExec("
				DELETE FROM items
				WHERE id = $item_id;
				DELETE FROM votes
				WHERE item_id = $item_id");
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
	
	/**
	 * Activate: Only used once to create the database table and insert options into options table
	 */
	 public function activate() {
	 	global $wpdb;
	 	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$tblname = $wpdb->prefix . "tu_votes";
		$tbl = "CREATE TABLE " . $tblname . " (
						id mediumint(9) NOT NULL AUTOINCREMENT,
						post_id mediumint(9),
						rating tinyint,
						ip varchar(128),
						date timestamp DEFAULT NOW(),
						UNIQUE KEY id (id) )";
		if($wpdb->get_var("SHOW TABLES LIKE '$tblname'") != $tblname) {
			dbDelta($tbl);
		}
		// If all goes well here, we'll insert data into options.
		// Which might be most of $this->config
		update_option('tu_config', $this->config);
		
	 }

}
