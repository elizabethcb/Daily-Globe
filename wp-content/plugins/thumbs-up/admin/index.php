<?php
/**
 * ThumbsUp Admin
 *
 * @author     Geert De Deckere <http://www.geertdedeckere.be/>
 * @copyright  (c) 2009 Geert De Deckere
 */

// Start your engines!
new ThumbsUp_Admin;

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
		// Start the session (before any output)
		session_start();

		// Load the settings from the config.php file
		$this->config = include '../config.php';

		// Load other components
		require THUMBSUP_DOCROOT.'core/thumbsup_database.php';
		require THUMBSUP_DOCROOT.'core/thumbsup_template.php';

		// Determine whether this is an AJAX request or not
		$this->is_ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

		// Determine whether the user is logged in
		$this->is_logged_in = self::is_logged_in();

		// Send the correct HTTP Content-Type header
		if ( ! $this->is_ajax)
		{
			header('Content-Type: text/html;charset=utf-8');
		}

		// Initialize the template
		$this->template = new ThumbsUp_Template(THUMBSUP_DOCROOT.'admin/html/layout.php');
		$this->template->pagetitle = 'ThumbsUp Admin';
		$this->template->content = '';

		// First of all, make sure the user is logged in!
		// If not, show login form and stop the script.
		if ( ! $this->is_logged_in)
			return $this->action_login();

		// Grab the action from the URL, and prefix it with "action_"
		$action = 'action_'.((empty($_GET['action'])) ? 'dashboard' : $_GET['action']);

		// Look for a corresponding action_method
		if (in_array($action, get_class_methods($this)))
			return $this->$action();

		// Show an error for invalid actions
		header('HTTP/1.0 404 Not Found');
		exit('Page not found');
	}

	/**
	 * Checks whether the current user is logged in or not.
	 *
	 * @return  boolean  logged in or not
	 */
	public static function is_logged_in()
	{
		return isset($_SESSION['thumbsup_admin']);
	}

	/**
	 * Logs a user in into the admin.
	 *
	 * @return  void
	 */
	public function action_login()
	{
		// If the user is already logged in, show the dashboard
		if ($this->is_logged_in)
			return $this->action_dashboard();

		// Setup content template
		$content = new ThumbsUp_Template(THUMBSUP_DOCROOT.'admin/html/login.php');

		// The login form was submitted
		if (isset($_POST['username']) AND isset($_POST['password']))
		{
			// Basic user/pass preparation
			$username = (string) $_POST['username'];
			$password = sha1((string) $_POST['password']);

			// Do username and password match?
			if (array_key_exists($username, $this->config['admins']) AND $this->config['admins'][$username] === $password)
			{
				// Update session id for security if possible
				if ( ! headers_sent())
				{
					session_regenerate_id();
				}

				// Set the thumbsup_admin session
				$_SESSION['thumbsup_admin'] = $username;

				// Update the login status
				$this->is_logged_in = TRUE;

				// The user has successfully logged in, show the dashboard
				return $this->action_dashboard();
			}

			// Used to show error message in HTML
			$content->username = $username;
			$this->template->error = 'Invalid username and/or password';
		}

		// Render the template
		$this->template->content = $content;
		$this->template->render();
	}

	/**
	 * Logs a user out of the admin.
	 *
	 * @return  void
	 */
	public function action_logout()
	{
		// Update session id for security if possible
		if ( ! headers_sent())
		{
			if (version_compare(PHP_VERSION, '5.1.0', '>='))
			{
				// Delete the old session file
				session_regenerate_id(TRUE);
			}
			else
			{
				session_regenerate_id();
			}
		}

		// Delete the admin session
		unset($_SESSION['thumbsup_admin']);

		// Redirect to the login form and exit
		header('Location: '.THUMBSUP_WEBROOT.'thumbsup/admin/');
		exit('You are logged out now.');
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
		$filter_sql = ($filter === '') ? '' : "WHERE LOWER(i.name) LIKE '%".sqlite_escape_string((string) $_GET['filter'])."%'";

		// Pagination
		$per_page = ( ! empty($this->config['admin_items_per_page'])) ? max(1, (int) $this->config['admin_items_per_page']) : 100;
		$total_items = ($filter === '') ? ThumbsUp_Database::get_total_items() : (int) ThumbsUp_Database::db()->singleQuery("SELECT COUNT(1) FROM items i $filter_sql");
		$total_pages = (int) ceil($total_items / $per_page);
		$page = (isset($_GET['page']) && ctype_digit($_GET['page'])) ? min($total_pages, max(1, (int) $_GET['page'])) : 1;
		$limit_sql = 'LIMIT '.$per_page.' OFFSET '.(($page - 1) * $per_page);

		// List of all items
		$content->items = (array) ThumbsUp_Database::db()->arrayQuery("
			SELECT
				i.id AS id,
				i.name AS name,
				i.closed AS closed,
				i.date AS date,
				COUNT(v.id) AS total_votes,
				SUM(v.rating) AS positive_votes
			FROM items i
			LEFT JOIN votes v ON i.id = v.item_id
			$filter_sql
			GROUP BY i.id
			ORDER BY LOWER(i.name) ASC
			$limit_sql", SQLITE_ASSOC);

		// Item counts
		$content->total_items_shown = count($content->items);

		// Render the template
		$content->filter = $filter;
		$content->total_items = $total_items;
		$content->total_pages = $total_pages;
		$content->page = $page;
		$this->template->content = $content;
		$this->template->render();
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

}
