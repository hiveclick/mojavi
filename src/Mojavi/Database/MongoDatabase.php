<?php
namespace Mojavi\Database;

use Mojavi\Exception\DatabaseException;

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.								   |
// | Copyright (c) 2005-2010 the Agavi Project.								|
// |																		   |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the	|
// | LICENSE file online at http://www.agavi.org/LICENSE.txt				   |
// |   vi: set noexpandtab:													|
// |   Local Variables:														|
// |   indent-tabs-mode: t													 |
// |   End:																	|
// +---------------------------------------------------------------------------+

/**
 * MongoDatabase provides connectivity for the Mongo database API layer.
 */
class MongoDatabase extends Database
{
	/**
	 * Connect to the database.
	 *
	 * @throws	 <b>DatabaseException</b> If a connection could not be
	 *										   created.
	 */
	function connect()
	{
		
		// determine how to get our parameters
		$method = $this->getParameter('method', 'dsn');
		$database = $this->getParameter('database');
		// get parameters
		switch($method) {
			case 'normal' :
				// get parameters normally
				$host	 = $this->getParameter('host', 'localhost');
				$port	 = $this->getParameter('port', '');
				$dsn = 'mongodb://' . $host;
				if ($port != '') {
					$dsn .= ':' . $port;
				}
				break;
			case 'dsn' :
				$dsn = $this->getParameter('dsn');
				if($dsn == null) {
					// missing required dsn parameter
					$error = 'Database configuration specifies method "dsn", but is missing dsn parameter';
					throw new DatabaseException($error);
				}
				break;
		}

		try {
			$options = array();

			if ($this->hasParameter('user')) {
				$options['user'] = $this->getParameter('user', '');
			}
			if ($this->hasParameter('password')) {
				$options['password'] = $this->getParameter('password', '');
			}
			
			$this->mongo = new \MongoClient($dsn, $options);
			// make sure the connection went through
			if ($this->mongo === false)
			{
				// the connection's foobar'
				$error = 'Failed to create a Mongo connection';
				throw new DatabaseException($error);
			}
			
			// select our database
			if ($database != null) {
				$this->connection = $this->mongo->selectDB($database);
			}
			// since we're not an abstraction layer, we copy the connection
			// to the resource
			$this->resource = $this->connection;
		} catch(\MongoException $e) {
			throw new DatabaseException($e->getMessage());
		}
	}

	/**
	 * Execute the shutdown procedure.
	 *
	 * @throws	 <b>DatabaseException</b> If an error occurs while shutting
	 *										   down this database.
	 */
	public function shutdown()
	{
		// assigning null to a previously open connection object causes a disconnect
		$this->connection = null;
	}
}
