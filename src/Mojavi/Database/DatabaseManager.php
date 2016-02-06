<?php
namespace Mojavi\Database;

use Mojavi\Core\MojaviObject as MojaviObject;
use Mojavi\Exception\DatabaseException as DatabaseException;
use Mojavi\Config\ConfigCache as ConfigCache;

/**
 * DatabaseManager allows you to setup your database connectivity before the
 * request is handled. This eliminates the need for a filter to manage database
 * connections.
 */

class DatabaseManager extends MojaviObject
{

	// +-----------------------------------------------------------------------+
	// | PRIVATE DATA														  |
	// +-----------------------------------------------------------------------+

	private
		$databases = array();

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Retrieve the database connection associated with this Database
	 * implementation.
	 *
	 * @param string A database name.
	 *
	 * @return mixed A Database instance.
	 *
	 * @throws <b>DatabaseException</b> If the requested database name does
	 *								  not exist.
	 */
	public function getDatabase ($name = 'default')
	{

		if (isset($this->databases[$name]))
		{

			return $this->databases[$name];

		}

		// nonexistent database name
		$error = 'Database "%s" does not exist';
		$error = sprintf($error, $name);

		throw new DatabaseException($error);

	}
	
	/**
	 * Return the array of all database connection names
	 *
	 * @return array Database instances
	 */
	
	public function getAllDatabases() {
		return array_keys($this->databases);
	}

	// -------------------------------------------------------------------------

	/**
	 * Initialize this DatabaseManager.
	 *
	 * @return bool true, if initialization completes successfully, otherwise
	 *			  false.
	 *
	 * @throws <b>InitializationException</b> If an error occurs while
	 *										initializing this DatabaseManager.
	 */
	public function initialize ()
	{

		// load database configuration
		require_once(ConfigCache::checkConfig('config/databases.ini'));

	}

	// -------------------------------------------------------------------------

	/**
	 * Execute the shutdown procedure.
	 *
	 * @return void
	 *
	 * @throws <b>DatabaseException</b> If an error occurs while shutting down
	 *								 this DatabaseManager.
	 */
	public function shutdown ()
	{

		// loop through databases and shutdown connections
		foreach ($this->databases as $database)
		{

			$database->shutdown();

		}

	}

}
