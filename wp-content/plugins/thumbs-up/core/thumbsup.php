<?php
/**
 * Plugin Name: Front Users
 * Plugin URI: http://www.geertdedeckere.be/
 * Description: Thumbs Up or Down a Post
 * Version: 0.9
 * Author: Geert De Deckere
 * Author URI: http://www.geertdedeckere.be/
 * ThumbsUp
 *
 * @author     Geert De Deckere <http://www.geertdedeckere.be/>
 * @copyright  (c) 2009 Geert De Deckere
 *
 * A word of explanation on the architecture of the ThumbsUp class may be helpful, I guess. Its
 * structure had to be thought-through seriously because of the mix of ingredients it needs to
 * be able to handle. It needs to handle new votes in POST requests. It must be able to send
 * cookies before any output has been sent. Also it needs to be designed for multiple setups
 * of ThumbsUp integrated in a single page.
 *
 * The first thing to note is that no output must have been sent to the browser yet, if we want
 * to be able to set cookies. That is why we need to check POST data for new votes immediately
 * when this file is included at the top of the webpage. This is done by catch_vote(), called in
 * the constructor. If a new vote is successfully registered we update the cookie with the IDs
 * of the items the user already voted for.
 *
 * If a new vote is registered, valid or invalid, we need to show the feedback on the correspon-
 * ding item, and only on that item. Since multiple ThumbsUp items can be setup in one webpage,
 * we store the new vote feedback into the new_vote property. It will only be loaded, by
 * load_vote(), at the point when an item with the same ID is setup.
 *
 * There is more going on in the ThumbsUp class, of course, but it is good to have an idea of
 * the mechanics explained above. All the methods have been amply commented in the class itself.
 * Hope you learn something if you are going over the code!
 */

// The absolute path to the thumbsup folder, which is located one level up;
// that is why the "core" folder is chopped off by substr().
// We need to define this constant here because this file can be included from any subdirectory
// of the website, hence we can't rely on relative paths to load config.php in the constructor.
define('THUMBSUP_DOCROOT', substr(realpath(dirname(__FILE__)), 0, -4));

// Load other components
//require THUMBSUP_DOCROOT.'core/thumbsup_database.php';
require THUMBSUP_DOCROOT.'core/thumbsup_template.php';

// Start your engines!
$thumbsup = new ThumbsUp;

// Where the magic happens...
class ThumbsUp {

	/**
	 * @var  array  configuration settings from config.php, loaded only once
	 */
	protected $default_config;

	/**
	 * @var  array  merged configuration settings
	 */
	protected $config;

	/**
	 * @var  array  currently loaded item, including the results and vote from the current user
	 */
	protected $item;

	/**
	 * @var  array  if a new vote was cast, its data is stored in here
	 */
	protected $new_vote;

	/**
	 * @var  array  list of item ids the user already voted on, items ids are stored as array keys
	 */
	protected $cookie;

	/**
	 * @var  string  user's IP address
	 */
	protected $ip;

	/**
	 * @var  boolean  AJAX request or not?
	 */
	protected $is_ajax;

	/**
	 * Constructor. Creates and sets up a new ThumbsUp object.
	 * Triggers setup() to load, or create, the item with the provided name.
	 *
	 * @param   string  item name, a unique identifier for the item to load/create; or NULL
	 * @param   array   configuration settings that overwrite the defaults from config.php
	 * @return  void
	 */
	public function __construct($name = NULL, array $config = array())
	{
		// Load in the default settings from the config.php file
		$this->default_config = $this->config = include THUMBSUP_DOCROOT.'config.php';

		// Determine whether this is an AJAX request or not
		$this->is_ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

		// Load the user's IP address
		$this->ip = $this->get_ip();

		// Load the thumbsup cookie
		$this->cookie = $this->get_cookie();

		// Register new vote
		$this->catch_vote();

		// If this is an AJAX request
		if ($this->is_ajax)
		{
			// Send an AJAX response
			$this->ajax();
		}
		else
		{
			// Load or create the requested item
			$this->setup($name, NULL, $config);
		}
	}

	/**
	 * Updates configuration settings. Loads an item into the class if an item name
	 * is provided. Also loads the vote results and the current user's vote.
	 * For non-existing item names, a new item will be created.
	 * This method is chainable.
	 *
	 * @param   string  item name, a unique identifier for the item to load/create; or NULL
	 * @param   string  template name; or NULL for default template
	 * @param   array   array with extra configuration settings
	 * @return  object  ThumbsUp
	 */
	public function setup($name = NULL, $template = NULL, $config = array())
	{
		// Template name supplied as second argument
		if (is_string($template))
		{
			$config['template'] = $template;
		}

		// Reset the configuration options, then overwrite them for this setup
		$this->config = $config + $this->default_config;

		// An item name was provided
		if ($name !== NULL)
		{
			// Load the item into the class
			if ( ! $this->load_item($name))
			{
				// If it does not exist yet, create it now
				$this->create_item($name);
			}

			// Load results and vote feedback
			$this->load_results();
			$this->load_vote();
		}

		// Chainable method
		return $this;
	}

	/**
	 * Looks at the POST data to catch a possible new vote. If one, the vote is
	 * completely validated first before being registered. The new vote data is
	 * then stored in the new_vote property, waiting to be picked up by load_vote()
	 * when the same item is set up.
	 *
	 * @return  boolean  TRUE if a new vote was registered; FALSE otherwise
	 */
	protected function catch_vote()
	{
		// Immediately get out of here if no vote was cast
		if ( ! isset($_POST['thumbsup_id']) OR ! isset($_POST['thumbsup_rating']))
			return FALSE;

		// Ignore votes for invalid item ids
		if ( ! ctype_digit((string) $_POST['thumbsup_id']) OR ! $this->load_item((int) $_POST['thumbsup_id']))
			return FALSE;

		// A new vote is cast
		$this->new_vote['item_id'] = $this->item['id'];

		// Check if one is still allowed to vote on this item
		if ($this->item['closed'])
		{
			$this->new_vote['error'] = 'Voting is closed for this item.';
			return FALSE;
		}

		// Already voted for this item? Check cookie first.
		if (isset($this->cookie[$this->item['id']]))
		{
			$this->new_vote['error'] = 'You have already voted for this item.';
			return FALSE;
		}

		// Check IP, if we found one for this user
		if ($this->config['ip_check'] AND ! empty($this->ip))
		{
			if (ThumbsUp_Database::db()->singleQuery("
				SELECT 1
				FROM votes
				WHERE item_id = {$this->item['id']} AND ip = '$this->ip'
				LIMIT 1"))
			{
				$this->new_vote['error'] = 'You have already voted for this item.';
				return FALSE;
			}
		}

		// Store the vote data into the new_vote property
		$this->new_vote += array(
			'rating'  => min(1, max(0, (int) $_POST['thumbsup_rating'])), // Must be either 0 or 1
			'ip'      => $this->ip,
			'date'    => time(),
		);

		// Finally, it is time to cast the vote
		ThumbsUp_Database::db()->queryExec("
			INSERT INTO votes (id, item_id, rating, ip, date)
			VALUES (NULL, {$this->new_vote['item_id']}, {$this->new_vote['rating']}, '{$this->new_vote['ip']}', {$this->new_vote['date']})");

		// Add the current item id to the cookie list
		$this->update_cookie($this->item['id']);

		// Vote successfully registered
		return TRUE;
	}

	/**
	 * Sends a JSON-encoded item back in case of an AJAX request.
	 *
	 * @return  void
	 */
	protected function ajax()
	{
		// Ignore invalid AJAX requests, AJAX requests should always contain a vote
		if ( ! $this->is_ajax OR empty($this->new_vote))
			return;

		// Load results and vote feedback
		$this->load_results();
		$this->load_vote();

		// Send the item back in JSON format
		echo json_encode($this->item);
	}

	/**
	 * Attempts to load an existing item into the item property.
	 * If the item name does not exist, FALSE will be returned.
	 *
	 * @param   mixed    string: item name; integer: item id
	 * @return  boolean  TRUE on success, FALSE on failure
	 */
	protected function load_item($name)
	{
		// An item name has been provided
		if (is_string($name))
		{
			// No need to reload the same item
			if (isset($this->item['name']) AND $this->item['name'] === $name)
				return TRUE;

			// Query condition
			$where = "name = '".sqlite_escape_string((string) $name)."'";
		}
		// An item id has been provided
		else
		{
			// Quick id cleanup
			$id = (int) $name;

			// No need to reload the same item
			if (isset($this->item['id']) AND (int) $this->item['id'] === $id)
				return TRUE;

			// Query condition
			$where = "id = $id";
		}

		// Select the item by name
		$item = ThumbsUp_Database::db()->arrayQuery("
			SELECT id, name, closed, date
			FROM items
			WHERE $where", SQLITE_ASSOC);

		// The item does not exist
		if (empty($item))
		{
			// Reset the item property
			$this->item = NULL;
			return FALSE;
		}

		// Load the item row into the class
		$this->item = current($item);
		return TRUE;
	}

	/**
	 * Creates a new item in the database, and loads it into the item property.
	 *
	 * @param   string   unique item name
	 * @return  boolean  TRUE on success, FALSE on failure
	 */
	protected function create_item($name)
	{
		// Check whether the item name is still available
		if (ThumbsUp_Database::item_name_exists($name))
			return FALSE;

		// Initialize the item fields
		$this->item = array(
			'name'   => (string) $name,
			'closed' => 0,
			'date'   => time(),
		);

		// Insert the item into the database
		ThumbsUp_Database::db()->queryExec("
			INSERT INTO items (id, name, closed, date)
			VALUES (NULL, '".sqlite_escape_string($this->item['name'])."', {$this->item['closed']}, {$this->item['date']})");

		// Load the item id
		$this->item['id'] = ThumbsUp_Database::db()->lastInsertRowid();

		// All done successfully
		return TRUE;
	}

	/**
	 * Loads a previous vote from the user for the current item; stores it in item[vote].
	 * item[vote] = array with vote data (rating, date) if a previous vote was found
	 *            = TRUE if the user previously voted, but we couldn't retrieve the rating
	 *            = FALSE if the user has note voted on the item yet, for as far as we know
	 *
	 * @return  boolean  TRUE if a new/previous vote is found; FALSE otherwise
	 */
	protected function load_vote()
	{
		// No item loaded yet
		if (empty($this->item['id']))
			return FALSE;

		// A new vote was cast for the current item
		if (isset($this->new_vote['item_id']) AND $this->new_vote['item_id'] == $this->item['id'])
		{
			// Transfer new vote data to the item property
			$this->item['vote'] = $this->new_vote;

			// This vote is only new, if it was really registered
			$this->item['vote']['new'] = empty($this->new_vote['error']);

			// The item id is stored in item[id] already
			unset($this->item['vote']['item_id']);

			// Vote found
			return TRUE;
		}

		// We can only retrieve a previous vote via IP
		if ($this->config['ip_check'] AND ! empty($this->ip))
		{
			$vote = ThumbsUp_Database::db()->arrayQuery("
				SELECT rating, ip, date
				FROM votes
				WHERE item_id = {$this->item['id']} AND ip = '$this->ip'
				LIMIT 1", SQLITE_ASSOC);

			// Previous vote found
			if ( ! empty($vote))
			{
				$this->item['vote'] = current($vote);
				$this->item['vote']['new'] = FALSE;

				// Vote found
				return TRUE;
			}
		}

		// If no previous vote was found via IP, we can still look at the cookie just to
		// determine whether the user already voted on this item. We won't we able to look up
		// the rating, though, so we set item[vote] to a boolean.
		return $this->item['vote'] = isset($this->cookie[$this->item['id']]);
	}

	/**
	 * Loads and calculates the vote results for the current item. Stores them in item[results].
	 *
	 * @return  boolean  TRUE if results were loaded; FALSE otherwise
	 */
	protected function load_results()
	{
		// No item loaded yet
		if (empty($this->item['id']))
			return FALSE;

		// Join-query to retrieve vote results
		$results = ThumbsUp_Database::db()->arrayQuery("
			SELECT
				COUNT(1)      AS total_votes,
				SUM(v.rating) AS positive_votes,
				MAX(v.date)   AS last_vote_date
			FROM items i
			JOIN votes v ON i.id = v.item_id
			WHERE i.id = {$this->item['id']}
			GROUP BY i.id", SQLITE_ASSOC);

		// Nobody voted on this item yet
		if (empty($results))
		{
			// Initialize result array manually
			$this->item['results'] = array(
				'total_votes'         => 0,
				'positive_votes'      => 0,
				'votes_balance'       => 0,
				'negative_votes'      => 0,
				'positive_percentage' => 0,
				'negative_percentage' => 0,
				'last_vote_date'      => NULL,
			);

			// We're done
			return TRUE;
		}

		// Load the results into the class
		$this->item['results'] = current($results);

		// Add our own extra result data, calculated by PHP instead of SQL
		$this->item['results']['negative_votes'] = $this->item['results']['total_votes'] - $this->item['results']['positive_votes'];
		$this->item['results']['votes_balance'] = $this->item['results']['positive_votes'] - $this->item['results']['negative_votes'];
		$this->item['results']['positive_percentage'] = $this->item['results']['positive_votes'] / $this->item['results']['total_votes'] * 100;
		$this->item['results']['negative_percentage'] = 100 - $this->item['results']['positive_percentage'];

		return TRUE;
	}

	/**
	 * Attemps to retrieve the IP address from the current user. This method will only
	 * return clean IPs.
	 *
	 * @return  mixed  string if IP found; void otherwise
	 */
	protected function get_ip()
	{
		// Loop over server keys that could contain the client IP address,
		// and return the first valid one.
		foreach (array('REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR') as $key)
		{
			if (isset($_SERVER[$key]) AND preg_match('~^(?:\d{1,3}+\.){3}\d{1,3}+$~D', $_SERVER[$key]))
				return $_SERVER[$key];
		}
	}

	/**
	 * Looks for the thumbsup cookie and parses it into an array of item ids for
	 * which the current user has voted already. Attempts to delete invalid cookies.
	 *
	 * @return  array  list of item ids (as array keys) the user has already voted for
	 */
	protected function get_cookie()
	{
		// If no cookie exists, we're out of here
		if ( ! isset($_COOKIE[$this->config['cookie_name']]))
			return array();

		// Make sure our cookie value is a string
		$cookie = (string) $_COOKIE[$this->config['cookie_name']];

		// Validate the cookie contents; should be numeric ids separated by dashes:
		// for example: 1-5-87-54
		if ( ! preg_match('~^(?:\d++-)*\d*$~D', $cookie))
		{
			// Delete the invalid cookie, if possible
			if ( ! headers_sent())
			{
				// Setting a cookie with a value of FALSE will try to delete it
				setcookie($this->config['cookie_name'], FALSE);
			}
			unset($_COOKIE[$this->config['cookie_name']]);

			// No valid cookie found
			return array();
		}

		// Return an array with item ids as keys
		return array_flip(explode('-', $cookie));
	}

	/**
	 * Adds an additional item id for which the user has voted to the cookie.
	 * Attempts to send the updated cookie to the user.
	 *
	 * @param   integer  additional item id for which the user has voted
	 * @return  boolean  TRUE if the cookie was updated successfully; FALSE otherwise
	 */
	protected function update_cookie($item_id)
	{
		// Add the new item id
		$this->cookie[(int) $item_id] = NULL;

		// Too late to send cookies
		if (headers_sent())
			return FALSE;

		// A cookie lifetime of 0 will keep the cookie until the session ends
		$expire = (empty($this->config['cookie_lifetime'])) ? 0 : time() + (int) $this->config['cookie_lifetime'];

		// Should return TRUE; does not necessarily mean the user accepted the cookie, though
		return setcookie($this->config['cookie_name'], implode('-', array_keys($this->cookie)), $expire, THUMBSUP_WEBROOT);
	}

	/**
	 * Renders the HTML template, and outputs it (by default).
	 *
	 * @param   boolean  TRUE to return the HTML as a string instead of echoing it
	 * @return  mixed    void by default; string if $return has been set to TRUE
	 */
	public function render($return = FALSE)
	{
		// Setup the desired template
		$template = new ThumbsUp_Template(THUMBSUP_DOCROOT.'templates/'.$this->config['template'].'/html.php');

		// Pass on selected data to the template
		$template->config = $this->config;
		$template->item   = $this->item;

		// Output or return the HTML
		return $template->render($return);
	}

}
