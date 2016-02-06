<?php
namespace Mojavi\Database;

use Mojavi\Util\ParameterHolder as ParameterHolder;

/**
 * Database is a base abstraction class that allows you to setup any type of
 * database connection via a configuration file.
 */
abstract class Database extends ParameterHolder
{
	// +-----------------------------------------------------------------------+
	// | PROTECTED VARIABLES												   |
	// +-----------------------------------------------------------------------+

	protected
		$connection = null,
		$resource   = null;



	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+


	/**
	 * Connect to the database.
	 *
	 * @throws <b>DatabaseException</b> If a connection could not be created.
	 */

	abstract function connect ();

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the database connection associated with this Database
	 * implementation.
	 *
	 * When this is executed on a Database implementation that isn't an
	 * abstraction layer, a copy of the resource will be returned.
	 *
	 * @return mixed A database connection.
	 *
	 * @throws <b>DatabaseException</b> If a connection could not be retrieved.
	 */

	public function getConnection ()
	{
		if ($this->connection == null)
		{
			$this->connect();
		}
		return $this->connection;
	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve a raw database resource associated with this Database
	 * implementation.
	 *
	 * @return mixed A database resource.
	 *
	 * @throws <b>DatabaseException</b> If a resource could not be retrieved.
	 */

	public function getResource ()
	{
		if ($this->resource == null)
		{
			$this->connect();
		}
		return $this->resource;
	}

	// -------------------------------------------------------------------------

	/**
	 * Initialize this Database.
	 *
	 * @param array An associative array of initialization parameters.
	 *
	 * @return bool true, if initialization completes successfully, otherwise
	 *			  false.
	 *
	 * @throws <b>InitializationException</b> If an error occurs while
	 *										initializing this Database.
	 */

	public function initialize ($parameters = null)
	{
		if ($parameters != null)
		{
			$this->parameters = array_merge($this->parameters, $parameters);
		}
	}

	// -------------------------------------------------------------------------

	/**
	 * Execute the shutdown procedure.
	 *
	 * @return void
	 *
	 * @throws <b>DatabaseException</b> If an error occurs while shutting down
	 *								 this database.
	 */
	abstract function shutdown ();

}



