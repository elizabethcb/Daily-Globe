<?php defined('THUMBSUP_DOCROOT') or exit('No direct script access.');
/**
 * ThumbsUp Database
 *
 * @author     Geert De Deckere <http://www.geertdedeckere.be/>
 * @copyright  (c) 2009 Geert De Deckere
 */

class ThumbsUp_Database {

	/**
	 * @var  object  SQLiteDatabase object
	 */
	protected static $db;

	/**
	 * Opens a SQLite database; automatically creates the database if it does not exist yet.
	 * Also automatically installs the tables needed by ThumbsUp: items and votes.
	 * Returns an SQLiteDatabase object, which represents an open SQLite database.
	 * Note: using a method like this allows you to lazy load the database, the database will
	 *       only be touched if/when this method is called for the first time.
	 *
	 * @return  object  SQLiteDatabase
	 */
	public static function db()
	{
		// Database not opened yet
		if (self::$db === NULL)
		{
			// Open or create the database if it does not exist yet
			self::$db = new SQLiteDatabase(THUMBSUP_DATABASE, 0666, $error) or exit($error);

			// Retrieve the names of all tables in the database
			$tables = (array) self::$db->singleQuery('SELECT name FROM sqlite_master WHERE type = \'table\'');

			// Create the items table if it does not exist
			if ( ! in_array('items', $tables))
			{
				self::$db->queryExec("
					CREATE TABLE items (
						id INTEGER AUTOINCREMENT,
						name TEXT,
						closed INTEGER,
						date INTEGER,
						PRIMARY KEY (id),
						UNIQUE (name) )");
			}

			// Create the votes table if it does not exist
			if ( ! in_array('votes', $tables))
			{
				self::$db->queryExec("
					CREATE TABLE votes (
						id INTEGER AUTOINCREMENT,
						item_id INTEGER,
						rating INTEGER,
						ip TEXT,
						date INTEGER,
						PRIMARY KEY (id) )");
			}
		}

		// Return SQLiteDatabase object
		return self::$db;
	}

	/**
	 * Counts how many items are in the database.
	 *
	 * @return  integer  total item count
	 */
	public static function get_total_items()
	{
		return (int) self::db()->singleQuery("
			SELECT COUNT(1)
			FROM items");
	}

	/**
	 * Checks whether a certain item name already exists.
	 *
	 * @param   string   item name
	 * @return  boolean  TRUE if the item exists; FALSE otherwise
	 */
	public static function item_name_exists($name)
	{
		return (bool) self::db()->singleQuery("
			SELECT 1
			FROM items
			WHERE name = '".sqlite_escape_string((string) $name)."'");
	}

}
