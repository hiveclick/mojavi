<?php
namespace Mojavi\Model;

use Mojavi\Ps\PreparedStatement as PreparedStatement;
use Mojavi\Ps\PdoPreparedStatement as PdoPreparedStatement;
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
abstract class Model extends PdoModel
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
		if ($ps instanceof PdoPreparedStatement) {
			return parent::executeQuery($ps, $name, $con, $debug);
		}
		
		$retval = false;
		try {

			// Connect to database
			if (is_null($con)) {
				if (self::DEBUG) { LoggerManager::debug(__METHOD__  . ":: Retrieving New DB Connection for '" . $name . "'..."); }
				$con = $this->getContext()->getDatabaseConnection($name);
			}
			
			if (function_exists('mysql_ping')) {
				if (!mysql_ping($con)) {
					$this->getContext()->getDatabaseManager()->getDatabase($name)->shutdown();
					$con = $this->getContext()->getDatabaseConnection($name);
				}
			} else {
				throw new MojaviException('Missing php-mysql libraries on this server');
			}

			// Get the prepared query
			$query = $ps->getPreparedStatement($con);

			if ($debug) {
				LoggerManager::error(__METHOD__ . " :: " . $ps->getDebugQueryString());
			}
			// Execute the query
			$rs = mysql_query ($query, $con);

			if (!$rs) {
				throw new MojaviException (mysql_error ($con));
			} else {
				$retval = $rs;
			}

		} catch (MojaviException $e) {

			$this->getErrors ()->addError ('error', new Error ($e->getMessage ()));
			LoggerManager::fatal ($e->printStackTrace (''));

		} catch (PDOException $e) {
			$e = new MojaviException ($e->getMessage());
			LoggerManager::fatal ($e->printStackTrace(''));
			throw $e;
		} catch (Exception $e) {
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
		if ($ps instanceof PdoPreparedStatement) {
			return parent::executeUpdate($ps, $name, $con, $debug);
		}
		$this->executeQuery($ps, $name, $con, $debug);
		return mysql_affected_rows($con);
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
		if ($ps instanceof PdoPreparedStatement) {
			return parent::executeInsert($ps, $name, $con, $debug);
		}
		
		$retval = false;
		try {

			// Connect to database
			if (is_null($con)) {
				if (self::DEBUG) { LoggerManager::debug(__METHOD__  . ":: Retrieving New DB Connection for '" . $name . "'..."); }
				$con = $this->getContext()->getDatabaseConnection($name);
			}

			if (function_exists('mysql_ping')) {
				if (!mysql_ping($con)) {
					$this->getContext()->getDatabaseManager()->getDatabase($name)->shutdown();
					$con = $this->getContext()->getDatabaseConnection($name);
				}
			} else {
				throw new Exception('Missing php-mysql libraries on this server');
			}

			// Get the prepared query
			$query = $ps->getPreparedStatement($con);

			if ($debug) {
				LoggerManager::error(__METHOD__ . " :: " . $ps->getDebugQueryString());
			}
			// Execute the query
			$rs = mysql_query ($query, $con);

			if (!$rs) {
				throw new Exception (mysql_error ($con));
			} else {
				return mysql_insert_id($con);
			}

		} catch (MojaviException $e) {

			$this->getErrors ()->addError ('error', new Error ($e->getMessage ()));
			LoggerManager::fatal ($e->printStackTrace (''));
			throw $e;
		} catch (PDOException $e) {
			$e = new MojaviException ($e->getMessage());
			LoggerManager::fatal ($e->printStackTrace(''));
			throw $e;
		} catch (Exception $e) {

			$this->getErrors ()->addError ('error', new Error ($e->getMessage ()));
			$e = new MojaviException ($e->getMessage());
			LoggerManager::fatal ($e->printStackTrace (''));
			throw $e;
		}
	}

	// -------------------------------------------------------------------------
}

