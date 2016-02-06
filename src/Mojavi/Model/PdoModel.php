<?php
namespace Mojavi\Model;

use Mojavi\Ps\PreparedStatement as PreparedStatement;
use Mojavi\Controller\Controller as Controller;
use Mojavi\Core\MojaviObject as MojaviObject;
use Mojavi\Logging\LoggerManager as LoggerManager;
use Mojavi\Exception\MojaviException as MojaviException;
use Mojavi\Error\Error;
use Exception;
use PDOException;

/**
 * Model provides a convention for separating business logic from application
 * logic. When using a model you're providing a globally accessible API for
 * other modules to access, which will boost interoperability among modules in
 * your web application.
 */
abstract class PdoModel extends MojaviObject
{
	const DEBUG = MO_DEBUG;
	const CRITERIA_RETVAL_TYPE_ITERATOR	= 1;
	const CRITERIA_RETVAL_TYPE_FORM		= 2;

	// +-----------------------------------------------------------------------+
	// | PRIVATE VARIABLES													 |
	// +-----------------------------------------------------------------------+

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Retrieve the current application context.
	 *
	 * @return Context The current Context instance.
	 */
	public function getContext ()
	{
		return Controller::getInstance()->getContext();
	}

	// -------------------------------------------------------------------------

	/**
	 * Initialize this model.
	 *
	 * @param Context The current application context.
	 *
	 * @return bool true, if initialization completes successfully, otherwise
	 *			  false.
	 */
	public function initialize ($context)
	{
		return true;
	}

	// -------------------------------------------------------------------------

	/**
	 * Execute an SQL Statement and return result to be handled by calling function.
	 *
	 * @param	mixed PreparedStatement or KeyBasedPreparedStatement
	 * @param	string Name of connection to be used
	 * @param	resource $connection Connection resource handler
	 * @return	mixed Resource if query executed successfully, otherwise false
	 */
	public function executeQuery (PreparedStatement $ps, $name = 'default', $con = NULL, $debug = self::DEBUG)
	{
		$retval = false;
		try {

			// Connect to database
			if (is_null($con)) {
				$con = $this->getContext()->getDatabaseConnection($name);
			}

			// Get the prepared query
			/* @var $sth PDOStatement */
			$sth = $ps->getPreparedStatement($con);

			// Debug the query to the log
			if ($debug) { LoggerManager::error(__METHOD__ . " :: " . $ps->getDebugQueryString()); }
			
			// Execute the query
			$sth->execute();

			// Set the retval to the statement because everything worked
			$retval = $sth;

		} catch (PDOException $e) {
			// If the MySQL server has gone away, try reconnecting, otherwise throw an exception
			if ($e->getMessage() == 'SQLSTATE[HY000]: General error: 2006 MySQL server has gone away') {
				try {
					// If the server went away, then close the connection and try again
					$this->getContext()->getDatabaseManager()->getDatabase($name)->shutdown();
					
					// Connect to database
					$con = $this->getContext()->getDatabaseConnection($name);
					
					// Get the prepared query
					/* @var $sth PDOStatement */
					$sth = $ps->getPreparedStatement($con);
		
					if ($debug) {
						LoggerManager::error(__METHOD__ . " :: " . $ps->getDebugQueryString());
					}
					// Execute the query
					$sth->execute();
		
					$retval = $sth;
					
				} catch (Exception $e) {
					ob_start();
					$sth->debugDumpParams();
					$stmt = ob_get_clean();
					
					$this->getErrors ()->addError ('error', new Error ($e->getMessage() . ": " . $sth->queryString));
					
					$e = new MojaviException ($e->getMessage());
					LoggerManager::fatal ($sth->queryString);
					LoggerManager::fatal ($stmt);
					LoggerManager::fatal ($e->printStackTrace(''));
					throw $e;
				}
			} else if (strpos($e->getMessage(), 'Lock wait timeout exceeded; try restarting transaction') !== false) {
				// If there was a lock on the transaction, then try it again before failing
				try {
					$this->getContext()->getDatabaseManager()->getDatabase($name)->shutdown();
					
					// Connect to database
					$con = $this->getContext()->getDatabaseConnection($name);
					
					// Get the prepared query
					/* @var $sth PDOStatement */
					$sth = $ps->getPreparedStatement($con);
		
					if ($debug) {
						LoggerManager::error(__METHOD__ . " :: " . $ps->getDebugQueryString());
					}
					// Execute the query
					$sth->execute();
		
					$retval = $sth;
					
				} catch (Exception $e) {
					ob_start();
					$sth->debugDumpParams();
					$stmt = ob_get_clean();
					
					$this->getErrors ()->addError ('error', new Error ($e->getMessage() . ": " . $sth->queryString));
					
					$e = new MojaviException ($e->getMessage());
					LoggerManager::fatal ($sth->queryString);
					LoggerManager::fatal ($stmt);
					LoggerManager::fatal ($e->printStackTrace(''));
					throw $e;
				}
				
			} else {
				ob_start();
				$sth->debugDumpParams();
				$stmt = ob_get_clean();
				
				$this->getErrors ()->addError ('error', new Error ($e->getMessage() . ": " . $sth->queryString));
				
				$e = new MojaviException ($e->getMessage());
				LoggerManager::fatal ($sth->queryString);
				LoggerManager::fatal ($stmt);
				LoggerManager::fatal ($e->printStackTrace(''));
				throw $e;
			}
		} catch (MojaviException $e) {
			// Output Mojavi Exceptions to the log, and continue
			$this->getErrors ()->addError ('error', new Error ($e->getMessage ()));
			LoggerManager::fatal ($e->printStackTrace (''));
		} catch (Exception $e) {
			// Output Normal Exceptions to the log, and continue
			$this->getErrors ()->addError ('error', new Error ($e->getMessage ()));
			$e = new MojaviException ($e->getMessage());
			LoggerManager::fatal ($e->printStackTrace (''));
		}
		return $retval;
	}
	
	/**
	 * Execute an SQL Statement and return result to be handled by calling function.
	 *
	 * @param	mixed PreparedStatement or KeyBasedPreparedStatement
	 * @param	string Name of connection to be used
	 * @param	resource $connection Connection resource handler
	 * @return	mixed Resource if query executed successfully, otherwise false
	 */
	public function executeUpdate (PreparedStatement $ps, $name = 'default', $con = NULL, $debug = self::DEBUG)
	{
		$retval = $this->executeQuery($ps, $name, $con, $debug);
		return $retval->rowCount();
	}
	
	/**
	 * Execute an SQL Statement and return result to be handled by calling function.
	 *
	 * @param	mixed PreparedStatement or KeyBasedPreparedStatement
	 * @param	string Name of connection to be used
	 * @param	resource $connection Connection resource handler
	 * @return	mixed Resource if query executed successfully, otherwise false
	 */
	public function executeInsert (PreparedStatement $ps, $name = 'default', $con = NULL, $debug = self::DEBUG)
	{
		$retval = false;
		try {

			// Connect to database
			if (is_null($con)) {
				if (self::DEBUG) { LoggerManager::debug(__METHOD__  . ":: Retrieving New DB Connection for '" . $name . "'..."); }
				$con = $this->getContext()->getDatabaseConnection($name);
			}

			// Get the prepared query
			/* @var $sth PDOStatement */
			$sth = $ps->getPreparedStatement($con);

			// Debug the query to the log
			if ($debug) { LoggerManager::error(__METHOD__ . " :: " . $ps->getDebugQueryString()); }
			
			// Execute the query
			$sth->execute();
			
			// Return the last insert id
			$retval = $con->lastInsertId();
		} catch (PDOException $e) {
			// If the MySQL server has gone away, try reconnecting, otherwise throw an exception
			if ($e->getMessage() == 'SQLSTATE[HY000]: General error: 2006 MySQL server has gone away') {
				try {
					$this->getContext()->getDatabaseManager()->getDatabase($name)->shutdown();
					
					// Connect to database
					$con = $this->getContext()->getDatabaseConnection($name);
					
					// Get the prepared query
					/* @var $sth PDOStatement */
					$sth = $ps->getPreparedStatement($con);
		
					if ($debug) {
						LoggerManager::error(__METHOD__ . " :: " . $ps->getDebugQueryString());
					}
					// Execute the query
					$sth->execute();
		
					$retval = $sth;
					
				} catch (Exception $e) {
					ob_start();
					$sth->debugDumpParams();
					$stmt = ob_get_clean();
					
					$this->getErrors ()->addError ('error', new Error ($e->getMessage() . ": " . $sth->queryString));
					
					$e = new MojaviException ($e->getMessage());
					LoggerManager::fatal ($sth->queryString);
					LoggerManager::fatal ($stmt);
					LoggerManager::fatal ($e->printStackTrace(''));
					throw $e;
				}
			} else if (strpos($e->getMessage(), 'Lock wait timeout exceeded; try restarting transaction') !== false) {
				// If there was a lock on the transaction, then try it again before failing
				try {
					$this->getContext()->getDatabaseManager()->getDatabase($name)->shutdown();
					
					// Connect to database
					$con = $this->getContext()->getDatabaseConnection($name);
					
					// Get the prepared query
					/* @var $sth PDOStatement */
					$sth = $ps->getPreparedStatement($con);
		
					if ($debug) {
						LoggerManager::error(__METHOD__ . " :: " . $ps->getDebugQueryString());
					}
					// Execute the query
					$sth->execute();
		
					$retval = $sth;
					
				} catch (Exception $e) {
					ob_start();
					$sth->debugDumpParams();
					$stmt = ob_get_clean();
					
					$this->getErrors ()->addError ('error', new Error ($e->getMessage() . ": " . $sth->queryString));
					
					$e = new MojaviException ($e->getMessage());
					LoggerManager::fatal ($sth->queryString);
					LoggerManager::fatal ($stmt);
					LoggerManager::fatal ($e->printStackTrace(''));
					throw $e;
				}
				
			} else {
				ob_start();
				$sth->debugDumpParams();
				$stmt = ob_get_clean();
				
				$this->getErrors ()->addError ('error', new Error ($e->getMessage() . ": " . $sth->queryString));
				
				$e = new MojaviException ($e->getMessage());
				LoggerManager::fatal ($sth->queryString);
				LoggerManager::fatal ($stmt);
				LoggerManager::fatal ($e->printStackTrace(''));
				throw $e;
			}
		} catch (MojaviException $e) {
			// Output Mojavi Exceptions to the log and throw the Exception
			$this->getErrors ()->addError ('error', new Error ($e->getMessage ()));
			LoggerManager::fatal ($e->printStackTrace (''));
			throw $e;
		} catch (Exception $e) {
			// Output Normal Exceptions to the log and throw the Exception
			$this->getErrors ()->addError ('error', new Error ($e->getMessage ()));
			$e = new MojaviException ($e->getMessage());
			LoggerManager::fatal ($e->printStackTrace (''));
			throw $e;
		}
		return $retval;
	}

	// -------------------------------------------------------------------------
}

